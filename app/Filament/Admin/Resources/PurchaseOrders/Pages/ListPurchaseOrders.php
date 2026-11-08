<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Pages;

use App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;

class ListPurchaseOrders extends ListRecords
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Buat PO Baru')->icon('heroicon-o-plus'),
            Action::make('history')->label('Riwayat')->icon('heroicon-o-clock')->url(PurchaseOrderResource::getUrl('history')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('is_history', false)->where('status', '!=', 'selesai');
    }
}
