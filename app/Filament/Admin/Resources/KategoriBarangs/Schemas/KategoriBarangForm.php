<?php

namespace App\Filament\Admin\Resources\KategoriBarangs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KategoriBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                TextInput::make('nama_kategori')
                    ->required()
                    ->unique(ignoreRecord: true),
            ]);
    }
}
