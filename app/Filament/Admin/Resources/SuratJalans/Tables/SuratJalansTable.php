<?php

namespace App\Filament\Admin\Resources\SuratJalans\Tables;

use App\Filament\Admin\Resources\SuratJalans\SuratJalanResource;
use App\Models\SuratJalan;
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

class SuratJalansTable
{
    public static function configure(Table $table): Table
    {
        $isHistory = request()->routeIs('filament.admin.resources.surat-jalans.history');
        return $table
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 30, 50])->searchable()->searchPlaceholder('Cari nomor/penerima...')->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('no_sj')->label('No. SJ')->searchable()->sortable(),
                TextColumn::make('tanggal')->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('reference')->label('Referensi')->state(fn (SuratJalan $record): string => $record->purchase_order?->no_po ?: $record->penawaran?->no_penawaran ?: '-')
                    ->searchable(query: fn ($query, string $search) => $query->whereHas('purchase_order', fn ($q) => $q->where('no_po', 'like', "%{$search}%"))->orWhereHas('penawaran', fn ($q) => $q->where('no_penawaran', 'like', "%{$search}%"))),
                TextColumn::make('nama_penerima')->label('Penerima')->searchable()->sortable(),
                TextColumn::make('user.nama')->label('Dibuat')->sortable(),
            ])
            ->recordAction(null)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->icon('heroicon-o-eye'),
                    Action::make('print')->label('Cetak')->icon('heroicon-o-printer')->url(fn (SuratJalan $record): string => route('surat-jalans.print', $record))->openUrlInNewTab(),
                    Action::make('toggleHistory')->label($isHistory ? 'Kembalikan ke Aktif' : 'Pindahkan ke Riwayat')
                        ->icon($isHistory ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-archive-box')->color('warning')->requiresConfirmation()
                        ->visible(fn (): bool => auth()->user()?->hasAccess('surat_jalans', 'update') ?? false)
                        ->action(fn (SuratJalan $record) => $record->update(['is_history' => ! $isHistory])),
                    Action::make('edit')->label('Ubah')->icon('heroicon-o-pencil-square')
                        ->url(fn (SuratJalan $record): string => SuratJalanResource::getUrl('edit', ['record' => $record]))
                        ->visible(fn (SuratJalan $record): bool => SuratJalanResource::canEdit($record) && ! $isHistory),
                    DeleteAction::make()->icon('heroicon-o-trash')->visible(fn (): bool => (auth()->user()?->hasAccess('surat_jalans', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                        ->disabled(fn (SuratJalan $record): bool => ! HistoryDeletionService::canDelete($record) || ! HistoryDeletionService::canDeleteForUser($record))
                        ->action(fn (SuratJalan $record) => HistoryDeletionService::delete($record)),
                ])->iconButton()->tooltip('Aksi'),
            ])
            ->toolbarActions([BulkActionGroup::make([
                BulkAction::make('archive')->label('Pindahkan ke Riwayat')->icon('heroicon-o-archive-box')->requiresConfirmation()
                    ->visible(fn (): bool => ! $isHistory && (auth()->user()?->hasAccess('surat_jalans', 'update') ?? false))
                    ->action(fn (Collection $records) => $records->each->update(['is_history' => true])),
                BulkAction::make('restore')->label('Kembalikan ke Aktif')->icon('heroicon-o-arrow-uturn-left')->requiresConfirmation()
                    ->visible(fn (): bool => $isHistory && (auth()->user()?->hasAccess('surat_jalans', 'update') ?? false))
                    ->action(fn (Collection $records) => $records->each->update(['is_history' => false])),
                BulkAction::make('export')->label('Ekspor Excel')->icon('heroicon-o-document-arrow-down')->visible(fn (): bool => $isHistory)
                    ->action(function (Collection $records) {
                        return response()->streamDownload(function () use ($records): void {
                            $output = fopen('php://output', 'w'); fwrite($output, "\xEF\xBB\xBF");
                            fputcsv($output, ['Tanggal SJ', 'Pelanggan', 'Nomor PO', 'Nomor SJ']);
                            foreach ($records as $record) fputcsv($output, [$record->tanggal?->format('d/m/Y'), $record->nama_penerima, $record->no_po, $record->no_sj]);
                            fclose($output);
                        }, 'History_SJ_' . now()->format('Ymd_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
                BulkAction::make('deleteSelected')->label($isHistory ? 'Hapus Arsip Terpilih' : 'Hapus Terpilih')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                    ->visible(fn (): bool => (auth()->user()?->hasAccess('surat_jalans', 'delete') ?? false) && (! $isHistory || auth()->user()?->role === 'admin'))
                    ->action(function (Collection $records): void { HistoryDeletionService::notify(...HistoryDeletionService::deleteMany($records)); }),
            ])]);
    }
}
