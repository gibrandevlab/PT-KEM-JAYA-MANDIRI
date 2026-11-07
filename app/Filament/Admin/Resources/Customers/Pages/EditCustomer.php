<?php

namespace App\Filament\Admin\Resources\Customers\Pages;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Support\CustomerInventorySync;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return CustomerInventorySync::mutateAddresses($data, $this->form->getRawState());
    }

    protected function afterSave(): void
    {
        \Illuminate\Support\Facades\DB::transaction(fn () => CustomerInventorySync::sync($this->record, $this->form->getRawState()));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
