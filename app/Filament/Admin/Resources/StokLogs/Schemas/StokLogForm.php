<?php

namespace App\Filament\Admin\Resources\StokLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StokLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('barang_id')
                    ->required()
                    ->numeric(),
                Select::make('jenis')
                    ->options(['masuk' => 'Masuk', 'keluar' => 'Keluar', 'adjustment' => 'Adjustment'])
                    ->required(),
                TextInput::make('qty')
                    ->required()
                    ->numeric(),
                TextInput::make('stok_sebelum')
                    ->required()
                    ->numeric(),
                TextInput::make('stok_sesudah')
                    ->required()
                    ->numeric(),
                TextInput::make('referensi_tipe'),
                TextInput::make('referensi_id')
                    ->numeric(),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->required()
                    ->numeric(),
            ]);
    }
}
