<?php

namespace App\Filament\Admin\Resources\CustomerPics;

use App\Filament\Admin\Resources\CustomerPics\Pages\CreateCustomerPic;
use App\Filament\Admin\Resources\CustomerPics\Pages\EditCustomerPic;
use App\Filament\Admin\Resources\CustomerPics\Pages\ListCustomerPics;
use App\Filament\Admin\Resources\CustomerPics\Pages\ViewCustomerPic;
use App\Filament\Admin\Resources\CustomerPics\Schemas\CustomerPicForm;
use App\Filament\Admin\Resources\CustomerPics\Schemas\CustomerPicInfolist;
use App\Filament\Admin\Resources\CustomerPics\Tables\CustomerPicsTable;
use App\Models\CustomerPic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerPicResource extends Resource
{
    protected static ?string $model = CustomerPic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CustomerPicForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerPicInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerPicsTable::configure($table);
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
            'index' => ListCustomerPics::route('/'),
            'create' => CreateCustomerPic::route('/create'),
            'view' => ViewCustomerPic::route('/{record}'),
            'edit' => EditCustomerPic::route('/{record}/edit'),
        ];
    }
}
