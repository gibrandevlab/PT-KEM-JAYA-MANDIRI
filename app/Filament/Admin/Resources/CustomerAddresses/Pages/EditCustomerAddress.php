<?php

namespace App\Filament\Admin\Resources\CustomerAddresses\Pages;

use App\Filament\Admin\Resources\CustomerAddresses\CustomerAddressResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerAddress extends EditRecord
{
    protected static string $resource = CustomerAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
