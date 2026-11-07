<?php

namespace App\Filament\Admin\Resources\Penawarans\Pages;

use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use App\Filament\Admin\Resources\Concerns\DeletesHistoryRecords;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPenawaranHistory extends ListRecords
{
    use DeletesHistoryRecords;
    protected static string $resource = PenawaranResource::class;

    protected static ?string $title = 'Riwayat Penawaran';

    protected static ?string $breadcrumb = 'Riwayat';

    protected function authorizeAccess(): void
    {
        abort_unless(PenawaranResource::canViewAny(), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Buat Penawaran')->icon('heroicon-o-plus')->visible(fn (): bool => PenawaranResource::canCreate()),
            Action::make('back')->label('Kembali ke Penawaran')->icon('heroicon-o-arrow-left')->url(PenawaranResource::getUrl('index')),
            $this->deleteAllHistoryAction('penawarans'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return PenawaranResource::getEloquentQuery()->with('user')->where(function (Builder $query): void {
            $query->where('is_history', true)->orWhere('status', 'selesai');
        });
    }

}
