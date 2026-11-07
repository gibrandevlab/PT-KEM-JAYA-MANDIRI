<?php

namespace App\Filament\Admin\Resources\Vendors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('kode_vendor')
                    ->placeholder('-'),
                TextEntry::make('nama_perusahaan'),
                TextEntry::make('alamat')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('kota')
                    ->placeholder('-'),
                TextEntry::make('telepon')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Alamat Email')
                    ->placeholder('-'),
                TextEntry::make('pic')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
