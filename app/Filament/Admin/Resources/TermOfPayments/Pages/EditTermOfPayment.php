<?php

namespace App\Filament\Admin\Resources\TermOfPayments\Pages;

use App\Filament\Admin\Resources\TermOfPayments\TermOfPaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTermOfPayment extends EditRecord
{
    protected static string $resource = TermOfPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
