<?php

namespace App\Filament\Admin\Resources\SuratJalans\Pages;

use App\Filament\Admin\Resources\SuratJalans\SuratJalanResource;
use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\SuratJalan;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSuratJalan extends ViewRecord
{
    protected static string $resource = SuratJalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('print')->label('Cetak PDF')->icon('heroicon-o-printer')
                ->modalHeading('Pratinjau Surat Jalan')->modalWidth('7xl')->modalSubmitAction(false)->modalCancelActionLabel('Tutup')
                ->modalContent(fn (SuratJalan $record) => view('filament.admin.print-preview', ['url' => route('surat-jalans.print', $record)])),
            Action::make('createInvoice')->label('Buat Faktur dari Surat Jalan')->icon('heroicon-o-receipt-percent')
                ->visible(fn (SuratJalan $record): bool => auth()->user()?->role === 'admin' && in_array($record->status, ['dikirim', 'diterima'], true)
                    && (! $record->invoices()->exists()
                        || ($record->invoices()->where('tipe_invoice', 'DP')->exists() && ! $record->invoices()->where('tipe_invoice', 'Pelunasan')->exists())))
                ->url(fn (SuratJalan $record): string => InvoiceResource::getUrl('create', ['from_sj' => $record->id]))
                ->openUrlInNewTab(),
            Action::make('status')->label('Ubah Status')->icon('heroicon-o-arrow-path')->visible(fn (): bool => auth()->user()?->role === 'admin')
                ->fillForm(fn (SuratJalan $record): array => ['status' => $record->status])
                ->form([\Filament\Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'dikirim' => 'Dikirim', 'diterima' => 'Diterima', 'dikembalikan' => 'Dikembalikan'])->required()])
                ->action(function (SuratJalan $record, array $data): void { $record->update(['status' => $data['status']]); $this->refreshFormData(['status']); }),
        ];
    }
}
