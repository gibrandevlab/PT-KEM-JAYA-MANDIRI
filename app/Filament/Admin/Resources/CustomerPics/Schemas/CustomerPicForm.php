<?php

namespace App\Filament\Admin\Resources\CustomerPics\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerPicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                TextInput::make('tipe')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('telepon')
                    ->tel(),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email(),
            ]);
    }
}
