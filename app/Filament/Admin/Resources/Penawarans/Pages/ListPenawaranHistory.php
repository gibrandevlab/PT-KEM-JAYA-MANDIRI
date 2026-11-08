<?php

namespace App\Filament\Admin\Resources\Penawarans\Pages;

use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use App\Filament\Admin\Resources\Concerns\DeletesHistoryRecords;
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
            $this->deleteAllHistoryAction('penawarans'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return PenawaranResource::getEloquentQuery()->with('user')->where('is_history', true);
    }

}
