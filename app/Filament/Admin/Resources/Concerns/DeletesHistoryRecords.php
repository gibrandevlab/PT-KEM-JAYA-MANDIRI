<?php

namespace App\Filament\Admin\Resources\Concerns;

use App\Support\HistoryDeletionService;
use Filament\Actions\Action;

trait DeletesHistoryRecords
{
    protected function deleteAllHistoryAction(string $feature): Action
    {
        return Action::make('deleteAllHistory')
            ->label('Hapus Semua Riwayat')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription('Dokumen yang masih direferensikan transaksi lain akan dilewati.')
            ->visible(fn (): bool => auth()->user()?->role === 'admin' && (auth()->user()?->hasAccess($feature, 'delete') ?? false))
            ->action(function (): void {
                [$deleted, $skipped] = HistoryDeletionService::deleteMany($this->getTableQuery()->get());
                HistoryDeletionService::notify($deleted, $skipped);
            });
    }
}
