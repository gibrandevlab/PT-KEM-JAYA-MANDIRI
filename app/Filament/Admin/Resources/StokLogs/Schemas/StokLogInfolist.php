<?php

namespace App\Filament\Admin\Resources\StokLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StokLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                TextEntry::make('barang_id')
                    ->numeric(),
                TextEntry::make('jenis')
                    ->badge(),
                TextEntry::make('qty')
                    ->numeric(),
                TextEntry::make('stok_sebelum')
                    ->numeric(),
                TextEntry::make('stok_sesudah')
                    ->numeric(),
                TextEntry::make('referensi_tipe')
                    ->placeholder('-'),
                TextEntry::make('referensi_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('keterangan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
