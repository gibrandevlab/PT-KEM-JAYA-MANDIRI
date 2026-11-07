<?php

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Filament\Admin\Resources\Concerns\DeletesHistoryRecords;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListInvoiceHistory extends ListRecords
{
    use DeletesHistoryRecords;
    protected static string $resource = InvoiceResource::class;
    protected static ?string $title = 'Riwayat Faktur';
    protected static ?string $breadcrumb = 'Riwayat';

    protected function authorizeAccess(): void { abort_unless(InvoiceResource::canViewAny(), 403); }

    protected function getHeaderActions(): array
    {
        return [Action::make('back')->label('Kembali ke Faktur')->icon('heroicon-o-arrow-left')->url(InvoiceResource::getUrl('index')), $this->deleteAllHistoryAction('invoices')];
    }

    protected function getTableQuery(): Builder
    {
        return InvoiceResource::getEloquentQuery()->with('user')->where('is_history', true);
    }
}
