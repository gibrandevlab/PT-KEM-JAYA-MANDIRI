<?php

namespace App\Filament\Admin\Resources\StockGudangs\Pages;

use App\Filament\Admin\Resources\StockGudangs\StockGudangResource;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\CreateRecord;

class CreateStockGudang extends CreateRecord
{
    protected static string $resource = StockGudangResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $barang = Barang::query()->with('kategori_barang')->findOrFail($data['barang_id']);
        abort_unless($barang->stok > 0, 422, 'Barang ini tidak memiliki stok gudang.');
        $data['nama_barang'] = $barang->nama_barang;
        $data['kategori'] = $barang->kategori_barang?->nama_kategori;
        $data['uom'] = $barang->satuan;
        $data['stok_masuk'] = (int) $barang->stok;
        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $barang = Barang::query()->lockForUpdate()->findOrFail($this->record->barang_id);
            if ((int) $this->record->stok_update > 0) {
                $barang->update(['stok' => $this->record->stok_update]);
            } elseif ((int) $this->record->barang_keluar > 0) {
                $barang->update(['stok' => max(0, $barang->stok - $this->record->barang_keluar)]);
            }
        });
    }
}
