<?php

namespace App\Filament\Admin\Resources\StockGudangs;

use App\Filament\Admin\Resources\StockGudangs\Pages\CreateStockGudang;
use App\Filament\Admin\Resources\StockGudangs\Pages\ListStockGudangs;
use App\Filament\Admin\Resources\StockGudangs\Schemas\StockGudangForm;
use App\Filament\Admin\Resources\StockGudangs\Schemas\StockGudangInfolist;
use App\Filament\Admin\Resources\StockGudangs\Tables\StockGudangsTable;
use App\Models\StockGudang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockGudangResource extends Resource
{
    protected static ?string $model = StockGudang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StockGudangForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockGudangInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockGudangsTable::configure($table);
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
            'index' => ListStockGudangs::route('/'),
            'create' => CreateStockGudang::route('/create'),
        ];
    }
}
