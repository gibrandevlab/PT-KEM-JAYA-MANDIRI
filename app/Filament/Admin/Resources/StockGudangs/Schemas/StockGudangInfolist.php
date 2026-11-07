<?php

namespace App\Filament\Admin\Resources\StockGudangs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockGudangInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('barang_id')
                    ->numeric(),
                TextEntry::make('nama_barang'),
                TextEntry::make('kategori')
                    ->placeholder('-'),
                TextEntry::make('uom')
                    ->placeholder('-'),
                TextEntry::make('stok_masuk')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('barang_keluar')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('stok_update')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tanggal_keluar')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('pic')
                    ->placeholder('-'),
                TextEntry::make('keterangan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
