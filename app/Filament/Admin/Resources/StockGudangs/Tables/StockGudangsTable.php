<?php

namespace App\Filament\Admin\Resources\StockGudangs\Tables;

use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockGudangsTable
{
    public static function configure(Table $table): Table
    {
        return $table->searchable()->searchPlaceholder('Cari nama barang, kategori, PIC...')->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('nama_barang')->label('Nama Barang')->searchable()->sortable(),
                TextColumn::make('kategori')->label('Kategori')->badge()->searchable(),
                TextColumn::make('uom')->label('UOM')->searchable(),
                TextColumn::make('stok_masuk')->label('Stok Barang')->numeric()->sortable(),
                TextColumn::make('barang_keluar')->label('Barang Keluar')->numeric()->sortable(),
                TextColumn::make('stok_update')->label('Stock Update')->numeric()->sortable(),
                TextColumn::make('tanggal_keluar')->label('Tanggal Keluar')->date('d M Y')->sortable(),
                TextColumn::make('pic')->label('PIC')->searchable(),
            ])
            ->recordActions([
                DeleteAction::make()->icon('heroicon-o-trash')
                    ->iconButton()
                    ->visible(fn (): bool => auth()->user()?->hasAccess('stock_gudangs', 'delete') ?? false),
            ])
            ->toolbarActions([]);
    }
}
