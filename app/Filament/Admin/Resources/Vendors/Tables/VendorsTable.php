<?php

namespace App\Filament\Admin\Resources\Vendors\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 30, 50])
            ->columns([
                TextColumn::make('kode_vendor')
                    ->searchable()->sortable(),
                TextColumn::make('nama_perusahaan')
                    ->searchable()->sortable(),
                TextColumn::make('kota')
                    ->searchable()->sortable(),
                TextColumn::make('telepon')
                    ->searchable()->sortable(),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()->sortable(),
                TextColumn::make('pic')
                    ->searchable()->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
            ])
                    ->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Aksi')])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
