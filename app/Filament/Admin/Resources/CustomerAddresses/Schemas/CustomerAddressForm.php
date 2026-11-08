<?php

namespace App\Filament\Admin\Resources\CustomerAddresses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerAddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                TextInput::make('label_alamat')
                    ->required(),
                Textarea::make('alamat_lengkap')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
