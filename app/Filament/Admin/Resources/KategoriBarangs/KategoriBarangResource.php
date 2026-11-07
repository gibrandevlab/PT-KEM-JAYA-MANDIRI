<?php

namespace App\Filament\Admin\Resources\KategoriBarangs;

use App\Filament\Admin\Resources\KategoriBarangs\Pages\CreateKategoriBarang;
use App\Filament\Admin\Resources\KategoriBarangs\Pages\EditKategoriBarang;
use App\Filament\Admin\Resources\KategoriBarangs\Pages\ListKategoriBarangs;
use App\Filament\Admin\Resources\KategoriBarangs\Pages\ViewKategoriBarang;
use App\Filament\Admin\Resources\KategoriBarangs\Schemas\KategoriBarangForm;
use App\Filament\Admin\Resources\KategoriBarangs\Schemas\KategoriBarangInfolist;
use App\Filament\Admin\Resources\KategoriBarangs\Tables\KategoriBarangsTable;
use App\Models\KategoriBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KategoriBarangResource extends Resource
{
    protected static ?string $model = KategoriBarang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return KategoriBarangForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KategoriBarangInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategoriBarangsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withCount('barangs');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKategoriBarangs::route('/'),
            'create' => CreateKategoriBarang::route('/create'),
            'view' => ViewKategoriBarang::route('/{record}'),
            'edit' => EditKategoriBarang::route('/{record}/edit'),
        ];
    }
}
