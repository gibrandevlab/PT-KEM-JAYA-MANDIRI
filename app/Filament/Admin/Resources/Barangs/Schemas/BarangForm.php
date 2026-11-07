<?php

namespace App\Filament\Admin\Resources\Barangs\Schemas;

use App\Models\KategoriBarang;
use App\Models\Barang;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Barang')->columns(2)->schema([
                TextInput::make('kode_barang')->label('Kode Barang')->required()->unique(ignoreRecord: true)->readOnly(fn (?Barang $record): bool => $record === null),
                TextInput::make('nama_barang')->label('Nama Barang')->required(),
                Select::make('kategori_id')->label('Kategori')->options(fn (): array => KategoriBarang::query()->orderBy('nama_kategori')->pluck('nama_kategori', 'id')->all())
                    ->searchable()->preload()->required()->live()->afterStateUpdated(fn ($state, \Filament\Schemas\Components\Utilities\Set $set) => $set('kode_barang', \App\Filament\Admin\Resources\Barangs\Pages\CreateBarang::nextCode((int) $state))),
                TextInput::make('satuan')->label('Satuan')->required()->default('Pcs'),
                TextInput::make('tahun')->label('Tahun')->default((string) now()->year),
                TextInput::make('harga_satuan')->label('Harga Satuan')->numeric()->minValue(0)->default(0),
                TextInput::make('harga_beli')->label('Harga Beli')->numeric()->minValue(0)->default(0),
                TextInput::make('stok')->label('Stok')->numeric()->minValue(0)->required()->default(0),
                TextInput::make('stok_minimum')->label('Stok Minimum')->numeric()->minValue(0)->required()->default(0),
                Textarea::make('deskripsi')->label('Deskripsi')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }
}
