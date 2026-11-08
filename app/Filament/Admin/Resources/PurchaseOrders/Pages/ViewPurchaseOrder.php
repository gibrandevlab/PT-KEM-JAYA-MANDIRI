<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Pages;

use App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\PurchaseOrder;
use App\Support\PurchaseOrderStatusService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('print')->label('Cetak PDF')->icon('heroicon-o-printer')
                ->modalHeading('Pratinjau Purchase Order')->modalWidth('7xl')->modalSubmitAction(false)->modalCancelActionLabel('Tutup')
                ->modalContent(fn (PurchaseOrder $record) => view('filament.admin.print-preview', ['url' => route('purchase-orders.print', $record)])),
            Action::make('ubahStatus')->label('Ubah Status')->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => auth()->user()?->role === 'admin' && (auth()->user()?->hasAccess('purchase_orders', 'update') ?? false))
                ->fillForm(fn (PurchaseOrder $record): array => ['status' => $record->status])
                ->form([\Filament\Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'])->required()])
                ->action(function (PurchaseOrder $record, array $data): void { PurchaseOrderStatusService::change($record, $data['status']); $this->refreshFormData(['status']); }),
            Action::make('createInvoice')->label('Buat Faktur dari PO')->icon('heroicon-o-receipt-percent')
                ->visible(fn (PurchaseOrder $record): bool => auth()->user()?->role === 'admin' && in_array($record->status, ['diproses', 'selesai'], true))
                ->url(fn (PurchaseOrder $record): string => InvoiceResource::getUrl('create', ['from_po' => $record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
