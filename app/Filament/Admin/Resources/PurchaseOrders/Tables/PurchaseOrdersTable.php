<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Tables;

use App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Support\HistoryDeletionService;
use App\Support\HistoryExcelExport;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
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
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 30, 50])
            ->searchable()
            ->searchPlaceholder('Cari nomor atau perusahaan...')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('no_po')->label('No. PO')->searchable()->sortable(),
                TextColumn::make('tanggal')->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('jenis')->label('Jenis')->badge()->formatStateUsing(fn (string $state, PurchaseOrder $record): string => $state === 'dari_customer' ? 'Dari Customer' : ($record->kategori_po ?: 'Ke Vendor'))
                    ->visible(fn (): bool => ! $isHistory),
                TextColumn::make('nama_perusahaan')->label($isHistory ? 'Vendor' : 'Perusahaan')->searchable()->sortable(),
                TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.'))->sortable(),
                TextColumn::make('user.nama')->label('Dibuat oleh')->sortable(),
            ])
            ->recordAction(null)
            ->recordActions([ActionGroup::make([
                ViewAction::make()->label('Lihat')->icon('heroicon-o-eye'),
                Action::make('print')->label('Cetak PDF')->icon('heroicon-o-printer')
                    ->modalHeading('Pratinjau Purchase Order')->modalWidth('7xl')->modalSubmitAction(false)->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (PurchaseOrder $record) => view('filament.admin.print-preview', ['url' => route('purchase-orders.print', $record)])),
                Action::make('toggleHistory')->label($isHistory ? 'Kembalikan ke Aktif' : 'Pindahkan ke Riwayat')
                    ->icon($isHistory ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-archive-box')->color('warning')->requiresConfirmation()
                    ->visible(fn (): bool => auth()->user()?->hasAccess('purchase_orders', 'update') ?? false)
                    ->action(fn (PurchaseOrder $record) => $record->update(['is_history' => ! $isHistory])),
                Action::make('edit')->label('Ubah')->icon('heroicon-o-pencil-square')
                    ->url(fn (PurchaseOrder $record): string => PurchaseOrderResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (PurchaseOrder $record): bool => PurchaseOrderResource::canEdit($record) && ! $isHistory),
                DeleteAction::make()->label('Hapus')->icon('heroicon-o-trash')->visible(fn (): bool => (auth()->user()?->hasAccess('purchase_orders', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                    ->disabled(fn (PurchaseOrder $record): bool => ! HistoryDeletionService::canDelete($record) || ! HistoryDeletionService::canDeleteForUser($record))
                    ->action(fn (PurchaseOrder $record) => HistoryDeletionService::delete($record)),
            ])
                    ->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Aksi')])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('archive')->label('Pindahkan ke Riwayat')->icon('heroicon-o-archive-box')->requiresConfirmation()
                        ->visible(fn (): bool => ! $isHistory && (auth()->user()?->hasAccess('purchase_orders', 'update') ?? false))
                        ->action(fn (Collection $records) => $records->each->update(['is_history' => true])),
                    BulkAction::make('restore')->label('Kembalikan ke Aktif')->icon('heroicon-o-arrow-uturn-left')->requiresConfirmation()
                        ->visible(fn (): bool => $isHistory && (auth()->user()?->hasAccess('purchase_orders', 'update') ?? false))
                        ->action(fn (Collection $records) => $records->each->update(['is_history' => false])),
                    BulkAction::make('exportHistory')->label('Ekspor Excel')->icon('heroicon-o-document-arrow-down')->visible(fn (): bool => $isHistory)
                        ->action(function (Collection $records) {
                            $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                            $records->loadMissing('po_items');
                            $rows = [];
                            foreach ($records as $index => $record) {
                                $displayNumber = $record->no_po_customer ?: ($record->no_po ?: '-');
                                if ($record->po_items->isEmpty()) {
                                    $rows[] = [$index + 1, $monthNames[$record->tanggal?->month] ?? '', $record->tanggal?->format('d/m/Y'), $record->nama_perusahaan, $displayNumber, '-', 0, '-', 0, $record->total];
                                    continue;
                                }
                                foreach ($record->po_items as $item) {
                                    $description = $item->nama_barang . ($item->deskripsi ? ' - ' . $item->deskripsi : '');
                                    $rows[] = [$index + 1, $monthNames[$record->tanggal?->month] ?? '', $record->tanggal?->format('d/m/Y'), $record->nama_perusahaan, $displayNumber, $description, $item->qty, $item->satuan ?: 'Pcs', $item->harga_satuan, $item->subtotal];
                                }
                            }

                            return HistoryExcelExport::download('History_PO_' . now()->format('Ymd_His') . '.xls',
                                ['NO', 'BULAN', 'TANGGAL PO', 'CUSTOMER', 'NO PO', 'PRODUCT DESCRIPTION', 'QTY', 'UNIT', 'UNIT PRICE', 'NET AMOUNT'], $rows);
                        }),
                    BulkAction::make('deleteSelected')->label($isHistory ? 'Hapus Arsip Terpilih' : 'Hapus Terpilih')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->visible(fn (): bool => (auth()->user()?->hasAccess('purchase_orders', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                        ->action(function (Collection $records): void { HistoryDeletionService::notify(...HistoryDeletionService::deleteMany($records)); }),
                ]),
            ]);
    }
}
