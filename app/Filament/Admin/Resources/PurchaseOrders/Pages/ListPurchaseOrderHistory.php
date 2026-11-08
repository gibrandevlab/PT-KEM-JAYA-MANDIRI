<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Pages;

use App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Admin\Resources\Concerns\DeletesHistoryRecords;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPurchaseOrderHistory extends ListRecords
{
    use DeletesHistoryRecords;

    protected static string $resource = PurchaseOrderResource::class;
    protected static ?string $title = 'History Purchase Order';
    protected static ?string $breadcrumb = 'Riwayat';

    protected function authorizeAccess(): void
    {
        abort_unless(PurchaseOrderResource::canViewAny(), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->deleteAllHistoryAction('purchase_orders'),
            Action::make('back')->label('Kembali ke Pesanan Pembelian')->icon('heroicon-o-arrow-left')->url(PurchaseOrderResource::getUrl('index')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return PurchaseOrderResource::getEloquentQuery()->with('user')->where('is_history', true);
    }
}
