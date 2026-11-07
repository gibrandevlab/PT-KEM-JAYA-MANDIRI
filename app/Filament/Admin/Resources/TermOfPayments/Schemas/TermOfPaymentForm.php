<?php

namespace App\Filament\Admin\Resources\TermOfPayments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TermOfPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_term')
                    ->required(),
            ]);
    }
}
