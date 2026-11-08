<?php

namespace App\Filament\Admin\Resources\TermOfPayments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TermOfPaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                Section::make('Term of Payment')
                    ->description('Syarat pembayaran yang dapat dipilih pada penawaran.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(['default' => 1, 'md' => 2])
                    ->schema([
                        TextEntry::make('nama_term')
                            ->label('Nama Term')
                            ->icon('heroicon-o-document-check')
                            ->iconColor('primary')
                            ->weight('bold')
                            ->size('lg')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->icon('heroicon-o-calendar-days')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('-'),
                    ])->columnSpanFull(),
            ]);
    }
}
