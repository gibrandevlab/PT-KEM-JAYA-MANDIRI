<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Pages;

use App\Models\PurchaseOrder;
use App\Support\PurchaseOrderStatusService;
use App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->status === 'dibatalkan') {
            throw ValidationException::withMessages(['data.status' => 'PO yang dibatalkan tidak bisa diedit. Buat PO baru.']);
        }

        $state = $this->form->getRawState();
        $subtotal = collect($state['po_items'] ?? [])->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
        $isLocal = ($data['kategori_po'] ?? $this->record->kategori_po) !== 'Luar Negeri';
        $taxRate = $isLocal && ($state['enable_ppn'] ?? false) ? (float) ($state['ppn_persen'] ?? 11) : 0.0;
        $tax = round($subtotal * $taxRate / 100, 2);
        $data['jenis'] = $this->record->jenis;
        $data['currency'] = $isLocal ? 'IDR' : ($data['currency'] ?: $this->record->currency ?: 'USD');
        $data['subtotal'] = $subtotal;
        $data['ppn_persen'] = $taxRate;
        $data['ppn_nominal'] = $tax;
        $data['total'] = $subtotal + $tax;
        unset($data['status'], $data['created_by'], $data['is_history']);
        return $data;
    }

    protected function afterSave(): void
    {
        $this->form->model($this->record)->saveRelationships();
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            Action::make('ubahStatus')->label('Ubah Status')->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => auth()->user()?->role === 'admin')
                ->fillForm(fn (PurchaseOrder $record): array => ['status' => $record->status])
                ->form([\Filament\Forms\Components\Select::make('status')->options([
                    'draft' => 'Draft', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan',
                ])->required()])
                ->action(fn (PurchaseOrder $record, array $data) => $this->changeStatus($record, $data['status'])),
            DeleteAction::make()->visible(fn (): bool => auth()->user()?->role === 'admin'),
        ];
    }

    private function changeStatus(PurchaseOrder $record, string $newStatus): void
    {
        PurchaseOrderStatusService::change($record, $newStatus);
        $this->refreshFormData(['status']);
    }
}
