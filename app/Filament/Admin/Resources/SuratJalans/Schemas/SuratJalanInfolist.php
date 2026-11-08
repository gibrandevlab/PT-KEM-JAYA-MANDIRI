<?php

namespace App\Filament\Admin\Resources\SuratJalans\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SuratJalanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                TextEntry::make('no_sj'),
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('po_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('penawaran_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('customer_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('nama_penerima'),
                TextEntry::make('alamat_pengiriman')
                    ->columnSpanFull(),
                TextEntry::make('kota_tujuan')
                    ->placeholder('-'),
                TextEntry::make('telepon_penerima')
                    ->placeholder('-'),
                TextEntry::make('nama_pengirim')
                    ->placeholder('-'),
                TextEntry::make('pic_penerima')
                    ->placeholder('-'),
                TextEntry::make('no_po')
                    ->placeholder('-'),
                TextEntry::make('kendaraan')
                    ->placeholder('-'),
                TextEntry::make('no_polisi')
                    ->placeholder('-'),
                TextEntry::make('catatan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_history')
                    ->boolean()
                    ->placeholder('-'),
            ]);
    }
}
