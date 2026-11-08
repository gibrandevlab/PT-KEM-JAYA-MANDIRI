<?php

namespace App\Filament\Admin\Resources\CustomerPics\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerPicInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                TextEntry::make('customer_id')
                    ->numeric(),
                TextEntry::make('tipe'),
                TextEntry::make('nama'),
                TextEntry::make('telepon')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Alamat Email')
                    ->placeholder('-'),
            ]);
    }
}
