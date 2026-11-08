<?php

namespace App\Filament\Admin\Resources\TermOfPayments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TermOfPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                TextInput::make('nama_term')
                    ->required(),
            ]);
    }
}
