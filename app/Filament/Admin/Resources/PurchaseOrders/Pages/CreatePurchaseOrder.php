<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Pages;

use App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource;
use Illuminate\Support\Facades\DB;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    public static function nextNumber(?string $date = null): string
    {
        $date = $date ? \Illuminate\Support\Carbon::parse($date) : now();
        $roman = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$date->month];
        $suffix = "/{$roman}/POKJM-{$date->year}";
        $last = DB::table('purchase_order')->where('jenis', 'ke_vendor')->where('no_po', 'like', "%{$suffix}")
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(no_po, '/', 1) AS UNSIGNED)) AS last_num")->value('last_num');
        return str_pad((string) (((int) $last) + 1), 2, '0', STR_PAD_LEFT) . $suffix;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $state = $this->form->getRawState();
        $items = collect($state['po_items'] ?? []);
        $subtotal = $items->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
        $isLocal = ($data['kategori_po'] ?? 'Dalam Negeri') !== 'Luar Negeri';
        $taxRate = $isLocal && ($state['enable_ppn'] ?? false) ? 11.0 : 0.0;
        $tax = round($subtotal * $taxRate / 100);
        $data['no_po'] = filled($data['no_po'] ?? null) ? $data['no_po'] : self::nextNumber($data['tanggal'] ?? null);
        $data['jenis'] = 'ke_vendor';
        $data['status'] = 'draft';
        $data['is_history'] = false;
        $data['created_by'] = Filament::auth()->id();
        $data['currency'] = $isLocal ? 'IDR' : ($data['currency'] ?: 'USD');
        $data['subtotal'] = $subtotal;
        $data['ppn_persen'] = $taxRate;
        $data['ppn_nominal'] = $tax;
        $data['total'] = $subtotal + $tax;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return PurchaseOrderResource::getUrl('view', ['record' => $this->record]);
    }
}
