<?php

namespace App\Filament\Admin\Resources\TermOfPayments;

use App\Filament\Admin\Resources\TermOfPayments\Pages\CreateTermOfPayment;
use App\Filament\Admin\Resources\TermOfPayments\Pages\EditTermOfPayment;
use App\Filament\Admin\Resources\TermOfPayments\Pages\ListTermOfPayments;
use App\Filament\Admin\Resources\TermOfPayments\Pages\ViewTermOfPayment;
use App\Filament\Admin\Resources\TermOfPayments\Schemas\TermOfPaymentForm;
use App\Filament\Admin\Resources\TermOfPayments\Schemas\TermOfPaymentInfolist;
use App\Filament\Admin\Resources\TermOfPayments\Tables\TermOfPaymentsTable;
use App\Models\TermOfPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TermOfPaymentResource extends Resource
{
    protected static ?string $model = TermOfPayment::class;
    protected static ?string $modelLabel = 'syarat pembayaran';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TermOfPaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TermOfPaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TermOfPaymentsTable::configure($table);
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
            'index' => ListTermOfPayments::route('/'),
            'create' => CreateTermOfPayment::route('/create'),
            'view' => ViewTermOfPayment::route('/{record}'),
            'edit' => EditTermOfPayment::route('/{record}/edit'),
        ];
    }
}
