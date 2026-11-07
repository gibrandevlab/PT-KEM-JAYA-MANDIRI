<?php

namespace App\Filament\Admin\Resources\Invoices\Schemas;

use App\Filament\Admin\Resources\Invoices\Pages\CreateInvoice;
use App\Models\BankAccount;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\SuratJalan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Faktur')->columns(2)->schema([
                TextInput::make('no_invoice')->label('Nomor Faktur')->required()->unique(ignoreRecord: true)->default(fn (): string => CreateInvoice::nextNumber()),
                DatePicker::make('tanggal')->label('Tanggal Faktur')->required()->default(now())->live(),
                Select::make('tipe_invoice')->label('Jenis Faktur')->options(['Langsung' => 'Pembayaran Penuh', 'DP' => 'Uang Muka', 'Pelunasan' => 'Pelunasan'])->default('Langsung')->required()->live(),
                Select::make('sj_id')->label('Referensi Surat Jalan')->options(fn (?Invoice $record, Get $get): array => SuratJalan::query()->with('invoices')->orderByDesc('tanggal')->get()
                    ->filter(function (SuratJalan $sj) use ($record, $get): bool {
                        $others = $sj->invoices->reject(fn (Invoice $invoice): bool => $record && $invoice->is($record));
                        if ($get('tipe_invoice') === 'Pelunasan') {
                            return $others->contains(fn (Invoice $invoice): bool => $invoice->tipe_invoice === 'DP')
                                && ! $others->contains(fn (Invoice $invoice): bool => $invoice->tipe_invoice === 'Pelunasan');
                        }
                        return $others->isEmpty();
                    })->pluck('no_sj', 'id')->all())
                    ->searchable()->preload()->live()->afterStateUpdated(function ($state, Set $set): void {
                        $sj = $state ? SuratJalan::query()->with('surat_jalan_items')->find($state) : null;
                        if (! $sj) return;
                        $set('po_id', $sj->po_id); $set('customer_id', $sj->customer_id);
                        $set('nama_customer', $sj->nama_penerima); $set('alamat_customer', $sj->alamat_pengiriman);
                        $set('telepon_customer', $sj->telepon_penerima); $set('po_number', $sj->no_po);
                        $set('shipment_number', $sj->no_sj);
                        $set('invoice_items', $sj->surat_jalan_items->map(fn ($item): array => self::itemFromReference($item))->all());
                    }),
                Select::make('po_id')->label('Referensi Pesanan Pembelian')->options(fn (): array => PurchaseOrder::query()->orderByDesc('tanggal')->pluck('no_po', 'id')->all())
                    ->searchable()->preload()->live()->afterStateUpdated(function ($state, Set $set): void {
                        $po = $state ? PurchaseOrder::query()->with('po_items')->find($state) : null;
                        if (! $po) return;
                        $set('customer_id', $po->customer_id); $set('nama_customer', $po->nama_perusahaan);
                        $set('alamat_customer', $po->alamat); $set('telepon_customer', $po->telepon); $set('po_number', $po->no_po);
                        $set('invoice_items', $po->po_items->map(fn ($item): array => self::itemFromReference($item))->all());
                    }),
                Hidden::make('penawaran_id'),
                Select::make('customer_id')->label('Pelanggan')->options(fn (): array => Customer::query()->orderBy('nama_perusahaan')->pluck('nama_perusahaan', 'id')->all())
                    ->searchable()->preload()->live()->afterStateUpdated(function ($state, Set $set): void {
                        $customer = $state ? Customer::query()->find($state) : null;
                        if (! $customer) return;
                        $set('nama_customer', $customer->nama_perusahaan); $set('alamat_customer', $customer->alamat);
                        $set('telepon_customer', $customer->telepon); $set('npwp', $customer->npwp);
                    }),
                TextInput::make('nama_customer')->label('Nama Pelanggan')->required(),
                Textarea::make('alamat_customer')->label('Alamat Pelanggan')->rows(3),
                TextInput::make('telepon_customer')->label('Telepon Pelanggan')->tel(),
                TextInput::make('npwp')->label('NPWP'),
                TextInput::make('no_faktur_pajak')->label('No. Faktur Pajak'),
                TextInput::make('po_number')->label('Nomor PO Pelanggan'),
                DatePicker::make('tanggal_po')->label('Tanggal PO'),
                TextInput::make('shipment_number')->label('Nomor Pengiriman'),
                Toggle::make('is_revisi')->label('Revisi Faktur')->live(),
                TextInput::make('revisi_ke')->label('Nomor Revisi')->visible(fn (Get $get): bool => (bool) $get('is_revisi')),
                Select::make('dp_ref_invoice_id')->label('Referensi Faktur Uang Muka')->options(fn (Get $get): array => Invoice::query()->where('tipe_invoice', 'DP')
                    ->when($get('sj_id'), fn ($query, $sjId) => $query->where('sj_id', $sjId))
                    ->orderByDesc('tanggal')->pluck('no_invoice', 'id')->all())
                    ->searchable()->visible(fn (Get $get): bool => $get('tipe_invoice') === 'Pelunasan'),
                Select::make('term_of_payment')->label('Syarat Pembayaran')->options(fn (): array => \App\Models\TermOfPayment::query()->orderBy('nama_term')->pluck('nama_term', 'nama_term')->all())->searchable(),
                Select::make('bank_account_id')->label('Rekening Bank')->options(fn (): array => BankAccount::query()->where('is_aktif', true)->orderBy('nama_bank')->get()->mapWithKeys(fn (BankAccount $bank): array => [$bank->id => "{$bank->nama_bank} — {$bank->no_rekening} ({$bank->atas_nama})"])->all())->searchable(),
                Textarea::make('catatan')->label('Catatan')->rows(3)->columnSpanFull(),
            ]),
            Section::make('Rincian Barang')->schema([
                Repeater::make('invoice_items')->relationship()->label('Rincian Faktur')->defaultItems(1)->minItems(1)->orderColumn('urutan')->columns(2)
                    ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::normalizeItem($data))
                    ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::normalizeItem($data))
                    ->schema([
                        Select::make('barang_id')->label('Pilih Barang')->options(fn (): array => Barang::query()->orderBy('nama_barang')->get()->mapWithKeys(fn (Barang $item): array => [$item->id => "{$item->kode_barang} — {$item->nama_barang}"])->all())
                            ->searchable()->preload()->live()->afterStateUpdated(function ($state, Get $get, Set $set): void {
                                $barang = $state ? Barang::query()->find($state) : null;
                                if (! $barang) return;
                                $set('kode_barang', $barang->kode_barang); $set('nama_barang', $barang->nama_barang);
                                $set('satuan', $barang->satuan ?: 'Pcs'); $set('harga_satuan', $barang->harga_satuan ?? 0);
                                $set('subtotal', round((float) $barang->harga_satuan * (float) ($get('qty') ?: 1), 2));
                            }),
                        TextInput::make('kode_barang')->label('Kode Barang'),
                        TextInput::make('nama_barang')->label('Nama Barang')->required(),
                        TextInput::make('deskripsi')->label('Deskripsi'),
                        TextInput::make('qty')->label('Jumlah')->numeric()->minValue(0.01)->default(1)->required()->live(onBlur: true)->afterStateUpdated(fn ($state, Get $get, Set $set) => $set('subtotal', round((float) $state * (float) $get('harga_satuan'), 2))),
                        TextInput::make('satuan')->label('Satuan')->default('Pcs')->required(),
                        TextInput::make('harga_satuan')->label('Harga Satuan')->numeric()->minValue(0)->default(0)->required()->live(onBlur: true)->afterStateUpdated(fn ($state, Get $get, Set $set) => $set('subtotal', round((float) $state * (float) $get('qty'), 2))),
                        TextInput::make('subtotal')->label('Subtotal Item')->numeric()->readOnly(),
                    ]),
            ]),
            Section::make('Perhitungan dan Pembayaran')->columns(2)->schema([
                Toggle::make('enable_diskon')->label('Aktifkan Diskon')->dehydrated(false)->default(fn (?Invoice $record): bool => (float) ($record?->diskon_persen ?? 0) > 0)->live(),
                TextInput::make('diskon_persen')->label('Diskon (%)')->numeric()->minValue(0)->maxValue(100)->default(0)->visible(fn (Get $get): bool => (bool) $get('enable_diskon')),
                Toggle::make('enable_ppn')->label('Aktifkan PPN')->dehydrated(false)->default(fn (?Invoice $record): bool => (float) ($record?->ppn_persen ?? 0) > 0)->live(),
                TextInput::make('ppn_persen')->label('PPN (%)')->numeric()->minValue(0)->maxValue(100)->default(11)->visible(fn (Get $get): bool => (bool) $get('enable_ppn')),
                Toggle::make('is_dpp')->label('Hitung DPP'),
                TextInput::make('dpp_nominal')->label('Nominal DPP')->numeric()->default(0),
                TextInput::make('dp_persen')->label('DP (%)')->numeric()->default(0)->visible(fn (Get $get): bool => $get('tipe_invoice') === 'DP'),
                TextInput::make('dp_nominal')->label('Nominal DP')->numeric()->default(0)->visible(fn (Get $get): bool => $get('tipe_invoice') === 'DP'),
                Select::make('status_payment')->label('Status Pembayaran')->options(['Unpaid' => 'Belum Dibayar', 'Paid' => 'Lunas'])->default('Unpaid')->live(),
                DatePicker::make('tanggal_payment')->label('Tanggal Pembayaran')->visible(fn (Get $get): bool => $get('status_payment') === 'Paid'),
                DatePicker::make('tanggal_kirim_dokumen')->label('Tanggal Kirim Dokumen'),
                Placeholder::make('invoice_total_preview')->label('Ringkasan Total')->content(function (Get $get): string {
                    $subtotal = collect($get('invoice_items') ?? [])->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
                    $discount = $get('enable_diskon') ? round($subtotal * (float) $get('diskon_persen') / 100, 2) : 0;
                    $tax = $get('enable_ppn') ? round(($subtotal - $discount) * (float) $get('ppn_persen') / 100, 2) : 0;
                    return 'Subtotal Rp ' . number_format($subtotal, 0, ',', '.') . ' · Diskon Rp ' . number_format($discount, 0, ',', '.') . ' · PPN Rp ' . number_format($tax, 0, ',', '.') . ' · Total Rp ' . number_format($subtotal - $discount + $tax, 0, ',', '.');
                })->columnSpanFull(),
            ]),
            Section::make('Lampiran')->schema([
                Repeater::make('invoice_attachments')->relationship()->label('File Lampiran')->schema([
                    FileUpload::make('file_path')->label('File')->disk('public')->directory('invoice')->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->maxSize(5120)->storeFileNamesIn('file_name')->required(),
                ]),
            ]),
        ]);
    }

    private static function itemFromReference(object $item): array
    {
        $qty = (float) ($item->qty ?? 1);
        $price = (float) ($item->getAttribute('harga_satuan') ?? $item->getAttribute('harga_beli') ?? 0);
        return ['barang_id' => $item->barang_id, 'kode_barang' => $item->kode_barang, 'nama_barang' => $item->nama_barang,
            'deskripsi' => $item->deskripsi ?? $item->keterangan ?? null, 'satuan' => $item->satuan, 'qty' => $qty,
            'harga_satuan' => $price, 'subtotal' => round($qty * $price, 2)];
    }

    private static function normalizeItem(array $data): array
    {
        $data['qty'] = (float) ($data['qty'] ?? 1);
        $data['harga_satuan'] = (float) ($data['harga_satuan'] ?? 0);
        $data['subtotal'] = round($data['qty'] * $data['harga_satuan'], 2);
        return $data;
    }
}
