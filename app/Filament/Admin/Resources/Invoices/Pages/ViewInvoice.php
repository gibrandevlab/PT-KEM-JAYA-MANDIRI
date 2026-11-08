<?php

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('print')->label('Cetak PDF')->icon('heroicon-o-printer')
                ->modalHeading('Pratinjau Invoice')->modalWidth('7xl')->modalSubmitAction(false)->modalCancelActionLabel('Tutup')
                ->modalContent(fn (Invoice $record) => view('filament.admin.print-preview', ['url' => route('invoices.print', $record)])),
            Action::make('status')->label('Ubah Status')->icon('heroicon-o-arrow-path')->visible(fn (): bool => auth()->user()?->role === 'admin')
                ->fillForm(fn (Invoice $record): array => ['status' => $record->status])
                ->form([\Filament\Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'terkirim' => 'Terkirim', 'dibayar' => 'Dibayar', 'jatuh_tempo' => 'Jatuh Tempo'])->required()])
                ->action(function (Invoice $record, array $data): void { $record->update(['status' => $data['status']]); $this->refreshFormData(['status']); }),
        ];
    }
}
