<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Tables;

use App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Support\HistoryDeletionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        $isHistory = request()->routeIs('filament.admin.resources.purchase-orders.history');

        return $table
            ->searchable()
            ->searchPlaceholder('Cari nomor/perusahaan...')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('no_po')->label('No. PO')->searchable()->sortable(),
                TextColumn::make('tanggal')->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('jenis')->label('Jenis')->badge()->formatStateUsing(fn (string $state, PurchaseOrder $record): string => $state === 'dari_customer' ? 'Dari Customer' : ($record->kategori_po ?: 'Ke Vendor')),
                TextColumn::make('nama_perusahaan')->label('Perusahaan')->searchable()->sortable(),
                TextColumn::make('total')->label('Total')->money(fn (PurchaseOrder $record): string => $record->currency ?: 'IDR')->sortable(),
                TextColumn::make('user.nama')->label('Dibuat oleh')->sortable(),
            ])
            ->recordAction(null)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->icon('heroicon-o-eye'),
                    Action::make('print')->label('Cetak')->icon('heroicon-o-printer')
                        ->url(fn (PurchaseOrder $record): string => route('purchase-orders.print', $record))->openUrlInNewTab(),
                    Action::make('toggleHistory')->label($isHistory ? 'Kembalikan ke Aktif' : 'Pindah ke History')
                        ->icon($isHistory ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-archive-box')->color('warning')->requiresConfirmation()
                        ->visible(fn (): bool => auth()->user()?->hasAccess('purchase_orders', 'update') ?? false)
                        ->action(fn (PurchaseOrder $record) => $record->update([
                            'is_history' => ! $isHistory,
                            ...($isHistory && $record->status === 'selesai' ? ['status' => 'draft'] : []),
                        ])),
                    Action::make('edit')->label('Edit')->icon('heroicon-o-pencil-square')
                        ->url(fn (PurchaseOrder $record): string => PurchaseOrderResource::getUrl('edit', ['record' => $record]))
                        ->visible(fn (PurchaseOrder $record): bool => PurchaseOrderResource::canEdit($record) && ! $isHistory),
                    DeleteAction::make()->icon('heroicon-o-trash')->visible(fn (): bool => (auth()->user()?->hasAccess('purchase_orders', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                        ->disabled(fn (PurchaseOrder $record): bool => ! HistoryDeletionService::canDelete($record) || ! HistoryDeletionService::canDeleteForUser($record))
                        ->action(fn (PurchaseOrder $record) => HistoryDeletionService::delete($record)),
                ])->iconButton()->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('archive')->label('Pindahkan ke History')->icon('heroicon-o-archive-box')->requiresConfirmation()
                        ->visible(fn (): bool => ! $isHistory && (auth()->user()?->hasAccess('purchase_orders', 'update') ?? false))
                        ->action(fn (Collection $records) => $records->each->update(['is_history' => true])),
                    BulkAction::make('restore')->label('Kembalikan ke Aktif')->icon('heroicon-o-arrow-uturn-left')->requiresConfirmation()
                        ->visible(fn (): bool => $isHistory && (auth()->user()?->hasAccess('purchase_orders', 'update') ?? false))
                        ->action(fn (Collection $records) => $records->each(function (PurchaseOrder $record): void {
                            $record->update(['is_history' => false, 'status' => $record->status === 'selesai' ? 'draft' : $record->status]);
                        })),
                    BulkAction::make('exportHistory')->label('Export Excel')->icon('heroicon-o-document-arrow-down')->visible(fn (): bool => $isHistory)
                        ->action(function (Collection $records) {
                            return response()->streamDownload(function () use ($records): void {
                                $output = fopen('php://output', 'w'); fwrite($output, "\xEF\xBB\xBF");
                                fputcsv($output, ['Tanggal', 'No. PO', 'Jenis', 'Perusahaan', 'Total', 'Status']);
                                foreach ($records as $record) fputcsv($output, [$record->tanggal?->format('d/m/Y'), $record->no_po, $record->jenis, $record->nama_perusahaan, $record->total, $record->status]);
                                fclose($output);
                            }, 'History_PO_' . now()->format('Ymd_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
                        }),
                    BulkAction::make('deleteSelected')->label($isHistory ? 'Hapus Arsip Terpilih' : 'Hapus Terpilih')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->visible(fn (): bool => (auth()->user()?->hasAccess('purchase_orders', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                        ->action(function (Collection $records): void { HistoryDeletionService::notify(...HistoryDeletionService::deleteMany($records)); }),
                ]),
            ]);
    }
}
