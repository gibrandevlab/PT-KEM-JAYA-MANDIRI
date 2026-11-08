<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Users\UserResource;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 30, 50])
            ->defaultSort('nama')
            ->columns([
                TextColumn::make('nama')
                    ->searchable()->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()->sortable(),
                TextColumn::make('username')
                    ->searchable()->sortable(),
                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'admin' ? 'Admin / Finance' : 'Pekerja')
                    ->color(fn (string $state): string => $state === 'admin' ? 'warning' : 'info'),
                TextColumn::make('foto')
                    ->label('Foto')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                IconColumn::make('aktif')
                    ->boolean()
                    ->label('Status')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-no-symbol'),
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->icon('heroicon-o-eye'),
                    EditAction::make()->icon('heroicon-o-pencil-square'),
                    Action::make('manageAccess')->label('Atur Akses')->icon('heroicon-o-key')
                        ->url(fn ($record): string => UserResource::getUrl('access', ['record' => $record]))
                        ->visible(fn ($record): bool => UserResource::canEdit($record)),
                    DeleteAction::make()->icon('heroicon-o-trash')
                        ->visible(fn ($record): bool => ! $record->is(auth()->user())),
                    Action::make('toggleStatus')
                        ->label(fn ($record): string => $record->aktif ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn ($record): string => $record->aktif ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                        ->color(fn ($record): string => $record->aktif ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['aktif' => ! $record->aktif]))
                        ->visible(fn ($record): bool => ! $record->is(auth()->user())),
                ])->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
