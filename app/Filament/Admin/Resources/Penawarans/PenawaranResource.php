<?php

namespace App\Filament\Admin\Resources\Penawarans;

use App\Filament\Admin\Resources\Penawarans\Pages\CreatePenawaran;
use App\Filament\Admin\Resources\Penawarans\Pages\EditPenawaran;
use App\Filament\Admin\Resources\Penawarans\Pages\ListPenawarans;
use App\Filament\Admin\Resources\Penawarans\Pages\ListPenawaranHistory;
use App\Filament\Admin\Resources\Penawarans\Pages\ViewPenawaran;
use App\Filament\Admin\Resources\Penawarans\Schemas\PenawaranForm;
use App\Filament\Admin\Resources\Penawarans\Schemas\PenawaranInfolist;
use App\Filament\Admin\Resources\Penawarans\Tables\PenawaransTable;
use App\Models\Penawaran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PenawaranResource extends Resource
{
    protected static ?string $model = Penawaran::class;
    protected static ?string $modelLabel = 'penawaran';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PenawaranForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PenawaranInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenawaransTable::configure($table);
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
            'index' => ListPenawarans::route('/'),
            'history' => ListPenawaranHistory::route('/history'),
            'create' => CreatePenawaran::route('/create'),
            'view' => ViewPenawaran::route('/{record}'),
            'edit' => EditPenawaran::route('/{record}/edit'),
        ];
    }
}
