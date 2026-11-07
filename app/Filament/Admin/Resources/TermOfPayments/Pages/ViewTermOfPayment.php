<?php

namespace App\Filament\Admin\Resources\TermOfPayments\Pages;

use App\Filament\Admin\Resources\TermOfPayments\TermOfPaymentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTermOfPayment extends ViewRecord
{
    protected static string $resource = TermOfPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
