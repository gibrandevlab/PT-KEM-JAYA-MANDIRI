<?php

namespace App\Filament\Admin\Resources\Invoices\Tables;

use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Support\HistoryDeletionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
            TextColumn::make('no_invoice')->label('No. Invoice')->searchable()->sortable(),
            TextColumn::make('tanggal')->label('Tanggal')->date('d/m/Y')->sortable(),
            TextColumn::make('nama_customer')->label('Customer')->searchable()->sortable(),
            TextColumn::make('tipe_invoice')->label('Tipe Invoice')->formatStateUsing(fn (?string $state, Invoice $record): string => ($state ?: 'Langsung') === 'Langsung' ? 'Full Payment' : (($state === 'DP' && $record->dp_persen) ? "DP {$record->dp_persen}%" : ($state === 'Pelunasan' ? 'Balance' : (string) $state))),
            TextColumn::make('total')->label('Total')->money('IDR')->sortable(),
            SelectColumn::make('status_payment')->label('Payment')->options(['Unpaid' => 'Unpaid', 'Paid' => 'Paid'])->selectablePlaceholder(false)
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
            $columns[] = TextColumn::make('jatuh_tempo')->label('Due Date')->date('d/m/Y');
            $columns[] = TextColumn::make('tanggal_payment')->label('Tgl Bayar')->date('d/m/Y');
        }
        return $table->searchable()->searchPlaceholder('Cari nomor/customer...')->defaultSort('created_at', 'desc')
            ->columns($columns)
            ->recordAction(null)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->icon('heroicon-o-eye'),
                    Action::make('print')->label('Cetak')->icon('heroicon-o-printer')->url(fn (Invoice $record): string => route('invoices.print', $record))->openUrlInNewTab(),
                    Action::make('toggleHistory')->label($isHistory ? 'Kembalikan ke Aktif' : 'Pindah ke History')
                        ->icon($isHistory ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-archive-box')->color('warning')->requiresConfirmation()
                        ->visible(fn (): bool => auth()->user()?->hasAccess('invoices', 'update') ?? false)
                        ->action(fn (Invoice $record) => $record->update(['is_history' => ! $isHistory])),
                    Action::make('edit')->label('Edit')->icon('heroicon-o-pencil-square')
                        ->url(fn (Invoice $record): string => InvoiceResource::getUrl('edit', ['record' => $record]))
                        ->visible(fn (Invoice $record): bool => InvoiceResource::canEdit($record) && ! $isHistory),
                    DeleteAction::make()->icon('heroicon-o-trash')->visible(fn (): bool => (auth()->user()?->hasAccess('invoices', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                        ->disabled(fn (Invoice $record): bool => ! HistoryDeletionService::canDeleteForUser($record))
                        ->action(fn (Invoice $record) => HistoryDeletionService::delete($record)),
                ])->iconButton()->tooltip('Aksi'),
            ])
            ->toolbarActions([BulkActionGroup::make([
                BulkAction::make('archive')->label('Pindahkan ke History')->icon('heroicon-o-archive-box')->requiresConfirmation()
                    ->visible(fn (): bool => ! $isHistory && (auth()->user()?->hasAccess('invoices', 'update') ?? false))
                    ->action(fn (Collection $records) => $records->each->update(['is_history' => true])),
                BulkAction::make('restore')->label('Kembalikan ke Aktif')->icon('heroicon-o-arrow-uturn-left')->requiresConfirmation()
                    ->visible(fn (): bool => $isHistory && (auth()->user()?->hasAccess('invoices', 'update') ?? false))
                    ->action(fn (Collection $records) => $records->each->update(['is_history' => false])),
                BulkAction::make('export')->label('Export Excel')->icon('heroicon-o-document-arrow-down')->visible(fn (): bool => $isHistory)
                    ->action(function (Collection $records) {
                        return response()->streamDownload(function () use ($records): void {
                            $output = fopen('php://output', 'w'); fwrite($output, "\xEF\xBB\xBF");
                            fputcsv($output, ['No. Invoice', 'Tanggal', 'Customer', 'No. PO', 'Tipe', 'Total', 'Status Pembayaran', 'Tanggal Pembayaran']);
                            foreach ($records as $record) fputcsv($output, [$record->no_invoice, $record->tanggal?->format('d/m/Y'), $record->nama_customer, $record->po_number, $record->tipe_invoice, $record->total, $record->status_payment, $record->tanggal_payment?->format('d/m/Y')]);
                            fclose($output);
                        }, 'History_Invoice_' . now()->format('Ymd_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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
