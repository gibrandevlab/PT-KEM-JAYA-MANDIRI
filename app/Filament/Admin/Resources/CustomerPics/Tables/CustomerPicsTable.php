<?php

namespace App\Filament\Admin\Resources\CustomerPics\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerPicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 30, 50])
            ->columns([
                TextColumn::make('customer_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tipe')
                    ->searchable()->sortable(),
                TextColumn::make('nama')
                    ->searchable()->sortable(),
                TextColumn::make('telepon')
                    ->searchable()->sortable(),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()->sortable(),
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
