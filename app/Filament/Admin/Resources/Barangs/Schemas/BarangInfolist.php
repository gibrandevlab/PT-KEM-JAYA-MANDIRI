<?php

namespace App\Filament\Admin\Resources\Barangs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BarangInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('kode_barang'),
                TextEntry::make('nama_barang')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('kategori_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('satuan'),
                TextEntry::make('stok')
                    ->numeric(),
                TextEntry::make('stok_minimum')
                    ->numeric(),
                TextEntry::make('harga_beli')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tahun')
                    ->placeholder('-'),
                TextEntry::make('harga_satuan')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('deskripsi')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
