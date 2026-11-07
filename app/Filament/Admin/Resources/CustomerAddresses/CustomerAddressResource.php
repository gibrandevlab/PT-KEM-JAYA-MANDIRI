<?php

namespace App\Filament\Admin\Resources\CustomerAddresses;

use App\Filament\Admin\Resources\CustomerAddresses\Pages\CreateCustomerAddress;
use App\Filament\Admin\Resources\CustomerAddresses\Pages\EditCustomerAddress;
use App\Filament\Admin\Resources\CustomerAddresses\Pages\ListCustomerAddresses;
use App\Filament\Admin\Resources\CustomerAddresses\Pages\ViewCustomerAddress;
use App\Filament\Admin\Resources\CustomerAddresses\Schemas\CustomerAddressForm;
use App\Filament\Admin\Resources\CustomerAddresses\Schemas\CustomerAddressInfolist;
use App\Filament\Admin\Resources\CustomerAddresses\Tables\CustomerAddressesTable;
use App\Models\CustomerAddress;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerAddressResource extends Resource
{
    protected static ?string $model = CustomerAddress::class;
    protected static ?string $modelLabel = 'alamat pelanggan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CustomerAddressForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerAddressInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerAddressesTable::configure($table);
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
            'index' => ListCustomerAddresses::route('/'),
            'create' => CreateCustomerAddress::route('/create'),
            'view' => ViewCustomerAddress::route('/{record}'),
            'edit' => EditCustomerAddress::route('/{record}/edit'),
        ];
    }
}
