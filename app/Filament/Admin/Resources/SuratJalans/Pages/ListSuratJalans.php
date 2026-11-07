<?php

namespace App\Filament\Admin\Resources\SuratJalans\Pages;

use App\Filament\Admin\Resources\SuratJalans\SuratJalanResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSuratJalans extends ListRecords
{
    protected static string $resource = SuratJalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('history')->label('Riwayat')->icon('heroicon-o-clock')->url(SuratJalanResource::getUrl('history')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('is_history', false);
    }
}
