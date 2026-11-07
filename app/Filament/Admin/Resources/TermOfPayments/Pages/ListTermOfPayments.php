<?php

namespace App\Filament\Admin\Resources\TermOfPayments\Pages;

use App\Filament\Admin\Resources\TermOfPayments\TermOfPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTermOfPayments extends ListRecords
{
    protected static string $resource = TermOfPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
