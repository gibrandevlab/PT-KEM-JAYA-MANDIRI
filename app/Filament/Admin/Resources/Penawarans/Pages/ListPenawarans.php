<?php

namespace App\Filament\Admin\Resources\Penawarans\Pages;

use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPenawarans extends ListRecords
{
    protected static string $resource = PenawaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Buat Penawaran')->icon('heroicon-o-plus')->visible(fn (): bool => PenawaranResource::canCreate()),
            \Filament\Actions\Action::make('history')->label('Riwayat')->icon('heroicon-o-clock')->visible(fn (): bool => PenawaranResource::canViewAny())->url(PenawaranResource::getUrl('history')),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(PenawaranResource::canViewAny(), 403);
    }

    protected function getTableQuery(): Builder
    {
        return PenawaranResource::getEloquentQuery()
            ->with('user')
            ->where('is_history', false);
    }
}
