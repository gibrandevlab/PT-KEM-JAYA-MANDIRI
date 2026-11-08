<?php

namespace App\Filament\Admin\Resources\StokLogs\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StokLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 30, 50])->searchable()->searchPlaceholder('Cari barang, referensi, keterangan...')->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Tanggal & Waktu')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('barang.kode_barang')->label('Kode Barang')->searchable(),
                TextColumn::make('barang.nama_barang')->label('Nama Barang')->searchable(),
                TextColumn::make('jenis')->label('Jenis')->badge()->color(fn (string $state): string => $state === 'masuk' ? 'success' : ($state === 'keluar' ? 'danger' : 'warning')),
                TextColumn::make('qty')->label('Jumlah')->numeric()->sortable(),
                TextColumn::make('stok_sebelum')->label('Stok Sebelum')->numeric(),
                TextColumn::make('stok_sesudah')->label('Stok Sesudah')->numeric(),
                TextColumn::make('keterangan')->label('Keterangan / Referensi')->searchable()->description(fn ($record): string => $record->referensi_tipe && $record->referensi_id ? "{$record->referensi_tipe} #{$record->referensi_id}" : ''),
                TextColumn::make('user.nama')->label('Pengguna'),
            ])
            ->filters([
                SelectFilter::make('barang_id')->label('Barang')->relationship('barang', 'nama_barang')->searchable()->preload(),
            ])
            ->recordActions([ActionGroup::make([
                EditAction::make()->icon('heroicon-o-pencil-square')->iconButton()->schema([
                    Textarea::make('keterangan')->label('Keterangan')->rows(4)->required(),
                ])->visible(fn (): bool => auth()->user()?->hasAccess('stok_logs', 'update') ?? false),
            ])
                    ->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Aksi')])
            ->toolbarActions([]);
    }
}
