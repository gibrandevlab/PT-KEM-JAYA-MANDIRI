<?php

namespace App\Filament\Admin\Resources\StokLogs;

use App\Filament\Admin\Resources\StokLogs\Pages\ListStokLogs;
use App\Filament\Admin\Resources\StokLogs\Pages\ViewStokLog;
use App\Filament\Admin\Resources\StokLogs\Schemas\StokLogForm;
use App\Filament\Admin\Resources\StokLogs\Schemas\StokLogInfolist;
use App\Filament\Admin\Resources\StokLogs\Tables\StokLogsTable;
use App\Models\StokLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StokLogResource extends Resource
{
    protected static ?string $model = StokLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StokLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StokLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StokLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStokLogs::route('/'),
            'view' => ViewStokLog::route('/{record}'),
        ];
    }
}
