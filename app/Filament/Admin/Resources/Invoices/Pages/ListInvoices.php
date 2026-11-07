<?php

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('history')->label('Riwayat')->icon('heroicon-o-clock')->url(InvoiceResource::getUrl('history')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('is_history', false);
    }
}
