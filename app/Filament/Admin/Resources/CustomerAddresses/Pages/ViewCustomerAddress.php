<?php

namespace App\Filament\Admin\Resources\CustomerAddresses\Pages;

use App\Filament\Admin\Resources\CustomerAddresses\CustomerAddressResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomerAddress extends ViewRecord
{
    protected static string $resource = CustomerAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
