<?php

namespace App\Filament\Admin\Resources\Barangs;

use App\Filament\Admin\Resources\Barangs\Pages\CreateBarang;
use App\Filament\Admin\Resources\Barangs\Pages\EditBarang;
use App\Filament\Admin\Resources\Barangs\Pages\ListBarangs;
use App\Filament\Admin\Resources\Barangs\Pages\ViewBarang;
use App\Filament\Admin\Resources\Barangs\Schemas\BarangForm;
use App\Filament\Admin\Resources\Barangs\Schemas\BarangInfolist;
use App\Filament\Admin\Resources\Barangs\Tables\BarangsTable;
use App\Models\Barang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;
    protected static ?string $modelLabel = 'barang';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BarangForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BarangInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarangsTable::configure($table);
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
            'index' => ListBarangs::route('/'),
            'create' => CreateBarang::route('/create'),
            'view' => ViewBarang::route('/{record}'),
            'edit' => EditBarang::route('/{record}/edit'),
        ];
    }
}
