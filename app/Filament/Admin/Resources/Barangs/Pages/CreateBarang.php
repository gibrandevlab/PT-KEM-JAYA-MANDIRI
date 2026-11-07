<?php

namespace App\Filament\Admin\Resources\Barangs\Pages;

use App\Filament\Admin\Resources\Barangs\BarangResource;
use App\Models\Barang;
use App\Models\KategoriBarang;
use Filament\Resources\Pages\CreateRecord;

class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;

    public static function nextCode(int $kategoriId): string
    {
        $name = KategoriBarang::query()->find($kategoriId)?->nama_kategori ?? '';
        $words = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $prefix = count($words) >= 2
            ? implode('', array_map(fn (string $word): string => strtoupper(substr($word, 0, 1)), $words))
            : strtoupper(substr($words[0] ?? 'BRG', 0, 3));
        $last = Barang::query()->where('kode_barang', 'like', $prefix . '-%')->orderByRaw('LENGTH(kode_barang) DESC')->orderByDesc('kode_barang')->value('kode_barang');
        $lastNumber = $last ? (int) substr(strrchr($last, '-'), 1) : 0;
        return $prefix . '-' . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kode_barang'] = self::nextCode((int) $data['kategori_id']);
        $data['tahun'] = filled($data['tahun'] ?? null) ? $data['tahun'] : (string) now()->year;
        return $data;
    }
}
