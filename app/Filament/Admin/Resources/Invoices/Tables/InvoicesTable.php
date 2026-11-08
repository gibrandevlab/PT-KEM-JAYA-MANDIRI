<?php

namespace App\Filament\Admin\Resources\Invoices\Tables;

use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Support\HistoryDeletionService;
use App\Support\HistoryExcelExport;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        $isHistory = request()->routeIs('filament.admin.resources.invoices.history');
        $columns = [
            TextColumn::make('no_invoice')->label('Nomor Faktur')->searchable()->sortable(),
            TextColumn::make('tanggal')->label('Tanggal')->date('d/m/Y')->sortable(),
            TextColumn::make('nama_customer')->label('Pelanggan')->searchable()->sortable(),
            TextColumn::make('tipe_invoice')->label('Jenis Faktur')->formatStateUsing(fn (?string $state, Invoice $record): string => ($state ?: 'Langsung') === 'Langsung' ? 'Pembayaran Penuh' : (($state === 'DP' && $record->dp_persen) ? "Uang Muka {$record->dp_persen}%" : ($state === 'Pelunasan' ? 'Pelunasan' : (string) $state))),
            TextColumn::make('total')->label('Total')->money('IDR')->sortable(),
            SelectColumn::make('status_payment')->label('Pembayaran')->options(['Unpaid' => 'Belum Dibayar', 'Paid' => 'Lunas'])->selectablePlaceholder(false)
                ->disabled(fn (): bool => ! (auth()->user()?->hasAccess('invoices', 'update') ?? false))
                ->afterStateUpdated(fn (Invoice $record, ?string $state) => $record->update(['tanggal_payment' => $state === 'Paid' ? ($record->tanggal_payment ?: now()->toDateString()) : null])),
            TextColumn::make('user.nama')->label('Dibuat')->sortable(),
        ];
        if ($isHistory) {
            $columns[] = TextInputColumn::make('tanggal_kirim_dokumen')->label('Tgl Kirim Dokumen')->type('date')
                ->disabled(fn (): bool => ! (auth()->user()?->hasAccess('invoices', 'update') ?? false))
                ->afterStateUpdated(function (Invoice $record, ?string $state): void {
                    $term = $record->term_of_payment ?? '';
                    preg_match('/(\d+)/', $term, $matches);
                    $days = isset($matches[1]) ? (int) $matches[1] : (preg_match('/cod|cbd|cash/i', $term) ? 0 : 30);
                    $record->update(['jatuh_tempo' => \Illuminate\Support\Carbon::parse($state ?: $record->tanggal)->addDays($days)->toDateString()]);
                });
            $columns[] = TextColumn::make('jatuh_tempo')->label('Jatuh Tempo')->date('d/m/Y');
            $columns[] = TextInputColumn::make('tanggal_payment')->label('Tgl Bayar')->type('date')
                ->disabled(fn (Invoice $record): bool => $record->status_payment !== 'Paid' || ! (auth()->user()?->hasAccess('invoices', 'update') ?? false));
        }
        return $table
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 30, 50])->searchable()->searchPlaceholder('Cari nomor atau pelanggan...')->defaultSort('created_at', 'desc')
            ->columns($columns)
            ->recordAction(null)
            ->recordActions([ActionGroup::make([
                    ViewAction::make()->label('Lihat')->icon('heroicon-o-eye'),
                    Action::make('print')->label('Cetak PDF')->icon('heroicon-o-printer')
                        ->modalHeading('Pratinjau Invoice')->modalWidth('7xl')->modalSubmitAction(false)->modalCancelActionLabel('Tutup')
                        ->modalContent(fn (Invoice $record) => view('filament.admin.print-preview', ['url' => route('invoices.print', $record)])),
                    Action::make('toggleHistory')->label($isHistory ? 'Kembalikan ke Aktif' : 'Pindahkan ke Riwayat')
                        ->icon($isHistory ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-archive-box')->color('warning')->requiresConfirmation()
                        ->visible(fn (): bool => auth()->user()?->hasAccess('invoices', 'update') ?? false)
                        ->action(fn (Invoice $record) => $record->update(['is_history' => ! $isHistory])),
                    Action::make('edit')->label('Ubah')->icon('heroicon-o-pencil-square')
                        ->url(fn (Invoice $record): string => InvoiceResource::getUrl('edit', ['record' => $record]))
                        ->visible(fn (Invoice $record): bool => InvoiceResource::canEdit($record) && ! $isHistory),
                    DeleteAction::make()->label('Hapus')->icon('heroicon-o-trash')->visible(fn (): bool => (auth()->user()?->hasAccess('invoices', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                        ->disabled(fn (Invoice $record): bool => ! HistoryDeletionService::canDeleteForUser($record))
                        ->action(fn (Invoice $record) => HistoryDeletionService::delete($record)),
            ])
                    ->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Aksi')])
            ->toolbarActions([BulkActionGroup::make([
                BulkAction::make('archive')->label('Pindahkan ke Riwayat')->icon('heroicon-o-archive-box')->requiresConfirmation()
                    ->visible(fn (): bool => ! $isHistory && (auth()->user()?->hasAccess('invoices', 'update') ?? false))
                    ->action(fn (Collection $records) => $records->each->update(['is_history' => true])),
                BulkAction::make('restore')->label('Kembalikan ke Aktif')->icon('heroicon-o-arrow-uturn-left')->requiresConfirmation()
                    ->visible(fn (): bool => $isHistory && (auth()->user()?->hasAccess('invoices', 'update') ?? false))
                    ->action(fn (Collection $records) => $records->each->update(['is_history' => false])),
                BulkAction::make('export')->label('Ekspor Excel')->icon('heroicon-o-document-arrow-down')->visible(fn (): bool => $isHistory)
                    ->action(function (Collection $records) {
                        return HistoryExcelExport::download('History_Invoice_' . now()->format('Ymd_His') . '.xls',
                            ['Nomor Faktur', 'Tanggal', 'Pelanggan', 'Nomor PO', 'Jenis', 'Total', 'Status Pembayaran', 'Tanggal Pembayaran'],
                            $records->map(fn (Invoice $record): array => [$record->no_invoice, $record->tanggal?->format('d/m/Y'), $record->nama_customer, $record->po_number, $record->tipe_invoice, $record->total, $record->status_payment, $record->tanggal_payment?->format('d/m/Y')]));
                    }),
                BulkAction::make('delete')->label('Hapus')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                    ->visible(fn (): bool => ! $isHistory && (auth()->user()?->hasAccess('invoices', 'delete') ?? false))
                    ->action(fn (Collection $records) => DB::transaction(fn () => $records->each(fn (Invoice $record) => HistoryDeletionService::delete($record)))),
                BulkAction::make('deleteSelected')->label($isHistory ? 'Hapus Arsip Terpilih' : 'Hapus Terpilih')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                    ->visible(fn (): bool => (auth()->user()?->hasAccess('invoices', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                    ->action(function (Collection $records): void { HistoryDeletionService::notify(...HistoryDeletionService::deleteMany($records)); }),
            ])]);
    }
}
