<?php

namespace App\Filament\Admin\Resources\CustomerAddresses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerAddressInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                TextEntry::make('customer_id')
                    ->numeric(),
                TextEntry::make('label_alamat'),
                TextEntry::make('alamat_lengkap')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
