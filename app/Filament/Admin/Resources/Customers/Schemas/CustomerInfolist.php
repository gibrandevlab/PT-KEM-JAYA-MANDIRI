<?php

namespace App\Filament\Admin\Resources\Customers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('kode_customer'),
                TextEntry::make('nama_perusahaan'),
                TextEntry::make('alamat')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('alamat_gudang')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('kota')
                    ->placeholder('-'),
                TextEntry::make('telepon')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Alamat Email')
                    ->placeholder('-'),
                TextEntry::make('npwp')
                    ->placeholder('-'),
                TextEntry::make('pic')
                    ->placeholder('-'),
                TextEntry::make('pic_gudang')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('pic_phone')
                    ->placeholder('-'),
                TextEntry::make('pic_email')
                    ->placeholder('-'),
                TextEntry::make('pic_gudang_phone')
                    ->placeholder('-'),
            ]);
    }
}
