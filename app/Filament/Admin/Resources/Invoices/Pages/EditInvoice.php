<?php

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Support\InvoiceShipmentGuard;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        InvoiceShipmentGuard::validate(isset($data['sj_id']) ? (int) $data['sj_id'] : null, $data['tipe_invoice'] ?? 'Langsung', isset($data['dp_ref_invoice_id']) ? (int) $data['dp_ref_invoice_id'] : null, (int) $this->record->getKey());
        $state = $this->form->getRawState();
        $subtotal = collect($state['invoice_items'] ?? [])->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
        $discountRate = ($state['enable_diskon'] ?? false) ? (float) ($data['diskon_persen'] ?? 0) : 0.0;
        $discount = round($subtotal * $discountRate / 100, 2);
        $taxRate = ($state['enable_ppn'] ?? false) ? 11.0 : 0.0;
        $tax = round(($subtotal - $discount) * $taxRate / 100, 2);
        $data['subtotal'] = $subtotal;
        $data['diskon_persen'] = $discountRate;
        $data['diskon_nominal'] = $discount;
        $data['ppn_persen'] = $taxRate;
        $data['ppn_nominal'] = $tax;
        $data['total'] = max(0, $subtotal - $discount + $tax);
        $data['tanggal_payment'] = ($data['status_payment'] ?? 'Unpaid') === 'Paid' ? ($data['tanggal_payment'] ?? now()->toDateString()) : null;
        unset($data['enable_diskon'], $data['enable_ppn']);
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
