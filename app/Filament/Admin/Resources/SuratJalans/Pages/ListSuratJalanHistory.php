<?php

namespace App\Filament\Admin\Resources\SuratJalans\Pages;

use App\Filament\Admin\Resources\SuratJalans\SuratJalanResource;
use App\Filament\Admin\Resources\Concerns\DeletesHistoryRecords;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSuratJalanHistory extends ListRecords
{
    use DeletesHistoryRecords;
    protected static string $resource = SuratJalanResource::class;
    protected static ?string $title = 'History Surat Jalan';
    protected static ?string $breadcrumb = 'History';

    protected function authorizeAccess(): void { abort_unless(SuratJalanResource::canViewAny(), 403); }

    protected function getHeaderActions(): array
    {
        return [Action::make('back')->label('Kembali ke Surat Jalan')->icon('heroicon-o-arrow-left')->url(SuratJalanResource::getUrl('index')), $this->deleteAllHistoryAction('surat_jalans')];
    }

    protected function getTableQuery(): Builder
    {
        return SuratJalanResource::getEloquentQuery()->with('user')->where('is_history', true);
    }
}
