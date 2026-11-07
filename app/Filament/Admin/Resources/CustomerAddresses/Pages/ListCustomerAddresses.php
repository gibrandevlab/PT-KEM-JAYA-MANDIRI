<?php

namespace App\Filament\Admin\Resources\CustomerAddresses\Pages;

use App\Filament\Admin\Resources\CustomerAddresses\CustomerAddressResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerAddresses extends ListRecords
{
    protected static string $resource = CustomerAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
