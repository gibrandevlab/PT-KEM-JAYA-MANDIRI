<?php

namespace App\Filament\Admin\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                TextInput::make('nama_bank')
                    ->required(),
                TextInput::make('no_rekening')
                    ->required(),
                TextInput::make('atas_nama')
                    ->required(),
                Toggle::make('is_aktif'),
            ]);
    }
}
