<?php

namespace App\Filament\Admin\Resources\SuratJalans;

use App\Filament\Admin\Resources\SuratJalans\Pages\CreateSuratJalan;
use App\Filament\Admin\Resources\SuratJalans\Pages\EditSuratJalan;
use App\Filament\Admin\Resources\SuratJalans\Pages\ListSuratJalans;
use App\Filament\Admin\Resources\SuratJalans\Pages\ListSuratJalanHistory;
use App\Filament\Admin\Resources\SuratJalans\Pages\ViewSuratJalan;
use App\Filament\Admin\Resources\SuratJalans\Schemas\SuratJalanForm;
use App\Filament\Admin\Resources\SuratJalans\Schemas\SuratJalanInfolist;
use App\Filament\Admin\Resources\SuratJalans\Tables\SuratJalansTable;
use App\Models\SuratJalan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SuratJalanResource extends Resource
{
    protected static ?string $model = SuratJalan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SuratJalanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SuratJalanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuratJalansTable::configure($table);
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
            'index' => ListSuratJalans::route('/'),
            'history' => ListSuratJalanHistory::route('/history'),
            'create' => CreateSuratJalan::route('/create'),
            'view' => ViewSuratJalan::route('/{record}'),
            'edit' => EditSuratJalan::route('/{record}/edit'),
        ];
    }
}
