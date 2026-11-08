<?php

namespace App\Filament\Admin\Resources\BankAccounts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BankAccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2])
            ->components([
                Section::make('Informasi Rekening')
                    ->description('Informasi rekening pembayaran.')
                    ->icon('heroicon-o-building-library')
                    ->columns(['default' => 1, 'md' => 2])
                    ->schema([
                        TextEntry::make('nama_bank')
                            ->label('Nama Bank')
                            ->icon('heroicon-o-building-library')
                            ->weight('bold'),
                        TextEntry::make('no_rekening')
                            ->label('No. Rekening')
                            ->icon('heroicon-o-credit-card')
                            ->copyable(),
                        TextEntry::make('atas_nama')
                            ->label('Atas Nama')
                            ->icon('heroicon-o-user'),
                        IconEntry::make('is_aktif')
                            ->label('Status')
                            ->boolean()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
