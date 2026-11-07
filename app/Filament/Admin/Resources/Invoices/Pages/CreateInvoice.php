<?php

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Penawaran;
use App\Models\PurchaseOrder;
use App\Models\SuratJalan;
use App\Support\InvoiceShipmentGuard;
use Illuminate\Support\Facades\DB;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    public function mount(): void
    {
        parent::mount();
        $sjId = request()->integer('from_sj');
        $poId = request()->integer('from_po');
        if ($sjId) {
            $sj = SuratJalan::query()->with('surat_jalan_items')->find($sjId);
            if (! $sj) return;
            $po = $sj->po_id ? PurchaseOrder::query()->with('po_items')->find($sj->po_id) : null;
            $offer = $sj->penawaran_id ? Penawaran::query()->with('penawaran_items')->find($sj->penawaran_id) : null;
            $existingInvoices = Invoice::query()->where('sj_id', $sj->id)->get();
            $dpInvoice = $existingInvoices->firstWhere('tipe_invoice', 'DP');
            if ($existingInvoices->contains(fn (Invoice $invoice): bool => $invoice->tipe_invoice === 'Langsung')
                || ($dpInvoice && $existingInvoices->contains(fn (Invoice $invoice): bool => $invoice->tipe_invoice === 'Pelunasan'))) return;
            $invoiceType = $dpInvoice ? 'Pelunasan' : 'Langsung';
            $customerId = $sj->customer_id ?: $offer?->customer_id;
            $customer = $customerId ? Customer::query()->find($customerId) : null;
            $this->form->fill([
                'no_invoice' => self::nextNumber(), 'tanggal' => now()->toDateString(), 'sj_id' => $sj->id,
                'po_id' => $sj->po_id, 'penawaran_id' => $sj->penawaran_id, 'customer_id' => $customerId,
                'nama_customer' => $sj->nama_penerima, 'alamat_customer' => $sj->alamat_pengiriman,
                'telepon_customer' => $sj->telepon_penerima, 'npwp' => $offer?->npwp_customer ?: $customer?->npwp,
                'po_number' => $sj->no_po ?: $po?->no_po, 'tanggal_po' => $po?->tanggal?->toDateString(),
                'shipment_number' => $sj->no_sj, 'tipe_invoice' => $invoiceType,
                'dp_ref_invoice_id' => $dpInvoice?->id,
                'invoice_items' => $sj->surat_jalan_items->map(fn ($item): array => $this->invoiceItemFromShipment($item, $po, $offer))->all(),
            ]);
            return;
        }
        if ($poId) {
            $po = PurchaseOrder::query()->with('po_items')->find($poId);
            if (! $po) return;
            $customer = $po->customer_id ? Customer::query()->find($po->customer_id) : null;
            $offer = $po->penawaran_id ? Penawaran::query()->find($po->penawaran_id) : null;
            $this->form->fill([
                'no_invoice' => self::nextNumber(), 'tanggal' => now()->toDateString(), 'po_id' => $po->id,
                'penawaran_id' => $po->penawaran_id, 'customer_id' => $po->customer_id, 'nama_customer' => $po->nama_perusahaan,
                'alamat_customer' => $po->alamat, 'telepon_customer' => $po->telepon,
                'npwp' => $offer?->npwp_customer ?: $customer?->npwp, 'po_number' => $po->no_po,
                'tanggal_po' => $po->tanggal?->toDateString(), 'tipe_invoice' => 'Langsung',
                'invoice_items' => $po->po_items->map(fn ($item): array => $this->invoiceItemFromPo($item))->all(),
            ]);
        }
    }

    public static function nextNumber(?string $date = null): string
    {
        $date = $date ? \Illuminate\Support\Carbon::parse($date) : now();
        $roman = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$date->month];
        $suffix = "/{$roman}-INVKJM/{$date->year}";
        $last = DB::table('invoice')->where('no_invoice', 'like', "%{$suffix}")
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(no_invoice, '/', 1) AS UNSIGNED)) AS last_num")->value('last_num');
        return str_pad((string) (((int) $last) + 1), 2, '0', STR_PAD_LEFT) . $suffix;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        InvoiceShipmentGuard::validate(isset($data['sj_id']) ? (int) $data['sj_id'] : null, $data['tipe_invoice'] ?? 'Langsung', isset($data['dp_ref_invoice_id']) ? (int) $data['dp_ref_invoice_id'] : null);
        $state = $this->form->getRawState();
        $items = collect($state['invoice_items'] ?? []);
        $subtotal = $items->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
        $discountRate = ($state['enable_diskon'] ?? false) ? (float) ($data['diskon_persen'] ?? 0) : 0.0;
        $discount = round($subtotal * $discountRate / 100, 2);
        $taxRate = ($state['enable_ppn'] ?? false) ? (float) ($data['ppn_persen'] ?? 0) : 0.0;
        $tax = round(($subtotal - $discount) * $taxRate / 100, 2);
        $data['no_invoice'] = filled($data['no_invoice'] ?? null) ? $data['no_invoice'] : self::nextNumber($data['tanggal'] ?? null);
        $data['subtotal'] = $subtotal;
        $data['diskon_persen'] = $discountRate;
        $data['diskon_nominal'] = $discount;
        $data['ppn_persen'] = $taxRate;
        $data['ppn_nominal'] = $tax;
        $data['total'] = max(0, $subtotal - $discount + $tax);
        $data['status'] = 'draft';
        $data['created_by'] = Filament::auth()->id();
        $data['is_history'] = false;
        $data['tanggal_payment'] = ($data['status_payment'] ?? 'Unpaid') === 'Paid' ? ($data['tanggal_payment'] ?? now()->toDateString()) : null;
        $data['jatuh_tempo'] = self::dueDate($data['tanggal_kirim_dokumen'] ?? null, $data['term_of_payment'] ?? '', $data['tanggal'] ?? null);
        if (! $data['penawaran_id'] && ! empty($data['sj_id'])) {
            $data['penawaran_id'] = SuratJalan::query()->whereKey($data['sj_id'])->value('penawaran_id');
        }
        if (! $data['penawaran_id'] && ! empty($data['po_id'])) {
            $data['penawaran_id'] = PurchaseOrder::query()->whereKey($data['po_id'])->value('penawaran_id');
        }
        unset($data['enable_diskon'], $data['enable_ppn']);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return InvoiceResource::getUrl('view', ['record' => $this->record]);
    }

    private static function dueDate(?string $documentDate, string $term, ?string $invoiceDate): ?string
    {
        $base = $documentDate ?: $invoiceDate;
        if (! $base) return null;
        preg_match('/(\d+)/', $term, $matches);
        $days = isset($matches[1]) ? (int) $matches[1] : (preg_match('/cod|cbd|cash/i', $term) ? 0 : 30);
        return \Illuminate\Support\Carbon::parse($base)->addDays($days)->toDateString();
    }

    private function invoiceItemFromShipment(object $shipmentItem, ?PurchaseOrder $po, ?Penawaran $offer): array
    {
        $source = $po?->po_items->firstWhere('barang_id', $shipmentItem->barang_id)
            ?: $offer?->penawaran_items->firstWhere('barang_id', $shipmentItem->barang_id);
        $barang = $shipmentItem->barang_id ? Barang::query()->find($shipmentItem->barang_id) : null;
        $qty = (float) $shipmentItem->qty;
        $price = (float) ($source?->harga_satuan ?? $barang?->harga_satuan ?? 0);
        return ['barang_id' => $shipmentItem->barang_id, 'kode_barang' => $shipmentItem->kode_barang,
            'nama_barang' => $shipmentItem->nama_barang, 'deskripsi' => $shipmentItem->keterangan,
            'satuan' => $shipmentItem->satuan, 'qty' => $qty, 'harga_satuan' => $price, 'subtotal' => $qty * $price];
    }

    private function invoiceItemFromPo(object $item): array
    {
        $qty = (float) $item->qty; $price = (float) $item->harga_satuan;
        return ['barang_id' => $item->barang_id, 'kode_barang' => $item->kode_barang,
            'nama_barang' => $item->nama_barang, 'deskripsi' => $item->deskripsi,
            'satuan' => $item->satuan, 'qty' => $qty, 'harga_satuan' => $price, 'subtotal' => $qty * $price];
    }
}
