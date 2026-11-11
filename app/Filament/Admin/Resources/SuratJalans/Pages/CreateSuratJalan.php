<?php

namespace App\Filament\Admin\Resources\SuratJalans\Pages;

use App\Filament\Admin\Resources\SuratJalans\SuratJalanResource;
use App\Models\PurchaseOrder;
use App\Models\Penawaran;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateSuratJalan extends CreateRecord
{
    protected static string $resource = SuratJalanResource::class;

    public function mount(): void
    {
        parent::mount();
        $offerId = request()->integer('from_penawaran');
        if (! $offerId) return;
        $offer = Penawaran::query()->with('penawaran_items')->find($offerId);
        if (! $offer) return;
        $customer = $offer->customer_id ? Customer::query()->find($offer->customer_id) : null;
        $this->form->fill([
            'penawaran_id' => $offer->id,
            'customer_id' => $offer->customer_id,
            'nama_penerima' => $offer->nama_customer,
            'alamat_pengiriman' => $customer?->alamat_gudang ?: ($offer->alamat_customer_2 ?: $offer->alamat_customer),
            'telepon_penerima' => $customer?->pic_gudang_phone ?: $offer->telepon_customer,
            'pic_penerima' => $customer?->pic_gudang ?: $offer->up,
            'surat_jalan_items' => $offer->penawaran_items->map(fn ($item): array => [
                'barang_id' => $item->barang_id, 'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang, 'satuan' => $item->satuan, 'qty' => $item->qty,
            ])->all(),
        ]);
    }

    public static function nextNumber(?string $date = null): string
    {
        $date = $date ? \Illuminate\Support\Carbon::parse($date) : now();
        $roman = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$date->month];
        $suffix = "/KJM/{$roman}/{$date->year}";
        $last = DB::table('surat_jalan')->where('no_sj', 'like', "%{$suffix}")
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(no_sj, '/', 1) AS UNSIGNED)) AS last_num")->value('last_num');
        return str_pad((string) (((int) $last) + 1), 2, '0', STR_PAD_LEFT) . $suffix;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['no_sj'] = filled($data['no_sj'] ?? null) ? $data['no_sj'] : self::nextNumber($data['tanggal'] ?? null);
        $data['status'] = 'dikirim';
        $data['created_by'] = Filament::auth()->id();
        $data['is_history'] = false;
        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->po_id) PurchaseOrder::query()->whereKey($this->record->po_id)->update(['status' => 'selesai']);
    }

    protected function getRedirectUrl(): string
    {
        return SuratJalanResource::getUrl('view', ['record' => $this->record]);
    }
}
