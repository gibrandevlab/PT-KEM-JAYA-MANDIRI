<?php

namespace App\Filament\Admin\Resources\StockGudangs\Schemas;

use App\Models\Barang;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StockGudangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Data Stok Gudang')->columns(['default' => 1, 'md' => 2, 'xl' => 3])->schema([
                Select::make('barang_id')->label('Barang')->options(fn (): array => Barang::query()->where('stok', '>', 0)->orderBy('nama_barang')->get()
                    ->mapWithKeys(fn (Barang $barang): array => [$barang->id => "{$barang->kode_barang} — {$barang->nama_barang} (Stok: {$barang->stok} {$barang->satuan})"])->all())
                    ->searchable()->preload()->required()->live()->afterStateUpdated(function ($state, Set $set): void {
                        $barang = $state ? Barang::query()->with('kategori_barang')->find($state) : null;
                        if (! $barang) return;
                        $set('nama_barang', $barang->nama_barang);
                        $set('kategori', $barang->kategori_barang?->nama_kategori);
                        $set('uom', $barang->satuan);
                        $set('stok_masuk', (int) $barang->stok);
                    }),
                TextInput::make('nama_barang')->label('Nama Barang')->required()->readOnly(),
                TextInput::make('kategori')->label('Kategori')->readOnly(),
                TextInput::make('uom')->label('Satuan')->readOnly(),
                TextInput::make('stok_masuk')->label('Stok Barang Saat Ini')->numeric()->readOnly(),
                TextInput::make('barang_keluar')->label('Barang Keluar')->numeric()->minValue(0)->default(0),
                TextInput::make('stok_update')->label('Pembaruan Stok')->numeric()->minValue(0)->default(0),
                DatePicker::make('tanggal_keluar')->label('Tanggal Barang Keluar'),
                TextInput::make('pic')->label('PIC'),
                Textarea::make('keterangan')->label('Keterangan')->rows(3)->columnSpanFull(),
            ])->columnSpanFull(),
        ]);
    }
}
