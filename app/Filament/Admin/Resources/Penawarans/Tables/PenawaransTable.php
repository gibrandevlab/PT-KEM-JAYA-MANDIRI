<?php

namespace App\Filament\Admin\Resources\Penawarans\Tables;

use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use App\Models\Penawaran;
use App\Support\HistoryDeletionService;
use App\Support\HistoryExcelExport;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PenawaransTable
{
    public static function configure(Table $table): Table
    {
        $isHistory = request()->routeIs('filament.admin.resources.penawarans.history');

        return $table
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 30, 50])
            ->searchPlaceholder('Cari nomor penawaran atau pelanggan...')
            ->searchable()
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('no_penawaran')->label('No. Penawaran')->searchable()->sortable()
                    ->formatStateUsing(fn (string $state, Penawaran $record): string => $record->use_sdk && ! str_contains($state, 'SDK/')
                        ? preg_replace('/^(\d+-)/', '$1SDK/', $state)
                        : $state),
                TextColumn::make('tanggal')->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('nama_customer')->label('Customer')->searchable()->sortable(),
                TextColumn::make('total')->label('Total')->money('IDR')->sortable(),
                TextColumn::make('user.nama')->label('Dibuat oleh')->sortable(),
            ])
            ->recordAction(null)
            ->recordActions([
                ActionGroup::make([
                ViewAction::make()->label('Lihat')->icon('heroicon-o-eye'),
                Action::make('print')->label('Cetak PDF')->icon('heroicon-o-printer')
                    ->modalHeading('Pratinjau Penawaran')->modalWidth('7xl')->modalSubmitAction(false)->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Penawaran $record) => view('filament.admin.print-preview', ['url' => route('penawarans.print', $record)])),
                Action::make('toggleHistory')
                    ->label($isHistory ? 'Kembalikan ke Aktif' : 'Pindahkan ke Riwayat')
                    ->icon($isHistory ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-archive-box')
                    ->color('warning')->requiresConfirmation()
                    ->visible(fn (): bool => auth()->user()?->hasAccess('penawarans', 'update') ?? false)
                    ->action(fn (Penawaran $record) => $record->update(['is_history' => ! $isHistory])),
                Action::make('edit')->label('Ubah')->icon('heroicon-o-pencil-square')
                    ->url(fn (Penawaran $record): string => PenawaranResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (Penawaran $record): bool => ! $isHistory && PenawaranResource::canEdit($record)),
                DeleteAction::make()->label('Hapus')->icon('heroicon-o-trash')
                    ->visible(fn (): bool => (auth()->user()?->hasAccess('penawarans', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                    ->disabled(fn (Penawaran $record): bool => ! HistoryDeletionService::canDelete($record) || ! HistoryDeletionService::canDeleteForUser($record))
                    ->action(fn (Penawaran $record) => HistoryDeletionService::delete($record)),
                ])->icon('heroicon-m-ellipsis-vertical')->label('Aksi')->tooltip('Aksi lainnya')->button()->color('gray'),
            ])
            ->recordAction(fn (): ?string => null)
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('archive')->label('Pindahkan ke Riwayat')->icon('heroicon-o-archive-box')
                        ->requiresConfirmation()->visible(fn (): bool => ! $isHistory && (auth()->user()?->hasAccess('penawarans', 'update') ?? false))
                        ->action(fn (Collection $records) => $records->each->update(['is_history' => true])),
                    BulkAction::make('restore')->label('Kembalikan ke Aktif')->icon('heroicon-o-arrow-uturn-left')
                        ->requiresConfirmation()->visible(fn (): bool => $isHistory && (auth()->user()?->hasAccess('penawarans', 'update') ?? false))
                        ->action(fn (Collection $records) => $records->each->update(['is_history' => false])),
                    BulkAction::make('exportExcel')->label('Ekspor Excel')->icon('heroicon-o-document-arrow-down')
                        ->visible(fn (): bool => $isHistory)
                        ->action(function (Collection $records) {
                            $records->loadMissing('penawaran_items');
                            $rows = [];
                            foreach ($records as $index => $record) {
                                if ($record->penawaran_items->isEmpty()) {
                                    $rows[] = [$index + 1, $record->tanggal?->format('Y'), $record->tanggal?->format('F'), $record->tanggal?->format('d/m/Y'), $record->nama_customer, $record->no_penawaran, '-', 0, '-', 0, $record->total];
                                    continue;
                                }
                                foreach ($record->penawaran_items as $item) {
                                    $rows[] = [$index + 1, $record->tanggal?->format('Y'), $record->tanggal?->format('F'), $record->tanggal?->format('d/m/Y'), $record->nama_customer, $record->no_penawaran, trim($item->nama_barang . ($item->deskripsi ? ' - ' . $item->deskripsi : '')), $item->qty, $item->satuan, $item->harga_satuan, $item->subtotal];
                                }
                            }
                            return HistoryExcelExport::download('History_Penawaran_' . now()->format('Ymd_His') . '.xls',
                                ['NO', 'TAHUN', 'BULAN', 'TANGGAL', 'CUSTOMER', 'NO PENAWARAN', 'PRODUCT DESCRIPTION', 'QTY', 'UNIT', 'UNIT PRICE', 'NET AMOUNT'], $rows);
                        }),
                    BulkAction::make('deleteSelected')->label($isHistory ? 'Hapus Arsip Terpilih' : 'Hapus Terpilih')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->visible(fn (): bool => (auth()->user()?->hasAccess('penawarans', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                        ->action(function (Collection $records): void { HistoryDeletionService::notify(...HistoryDeletionService::deleteMany($records)); }),
                ]),
            ]);
    }
}
