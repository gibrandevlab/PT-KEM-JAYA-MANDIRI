<?php

namespace App\Filament\Admin\Resources\CustomerAddresses\Pages;

use App\Filament\Admin\Resources\CustomerAddresses\CustomerAddressResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerAddress extends CreateRecord
{
    protected static string $resource = CustomerAddressResource::class;
}
