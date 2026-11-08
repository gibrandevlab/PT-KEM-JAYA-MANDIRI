<?php

namespace App\Filament\Admin\Resources\KategoriBarangs\Tables;

use App\Models\KategoriBarang;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KategoriBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 30, 50])->columns([
            TextColumn::make('nama_kategori')->label('Nama Kategori')->searchable()->sortable(),
                TextColumn::make('barangs_count')->label('Total Barang')->formatStateUsing(fn (int $state): string => "{$state} Item")->sortable()->toggleable(),
        ])->recordActions([
            ActionGroup::make([
                EditAction::make()->icon('heroicon-o-pencil-square'),
                DeleteAction::make()->icon('heroicon-o-trash')->action(function (KategoriBarang $record): void {
                    if ($record->barangs()->exists()) {
                        Notification::make()->title('Kategori masih digunakan oleh barang')->danger()->send();
                        return;
                    }
                    $record->delete();
                    Notification::make()->title('Kategori berhasil dihapus')->success()->send();
                }),
            ])->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Aksi'),
        ])->toolbarActions([]);
    }
}
