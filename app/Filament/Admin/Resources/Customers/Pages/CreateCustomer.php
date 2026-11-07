<?php

namespace App\Filament\Admin\Resources\Customers\Pages;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Models\Customer;
use App\Support\CustomerInventorySync;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    public static function nextCode(): string
    {
        $last = (int) Customer::query()->selectRaw('MAX(CAST(kode_customer AS UNSIGNED)) AS last_code')->value('last_code');
        return (string) ($last + 1);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $state = $this->form->getRawState();
        $data = CustomerInventorySync::mutateAddresses($data, $state);
        $data['kode_customer'] = self::nextCode();
        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(fn () => CustomerInventorySync::sync($this->record, $this->form->getRawState()));
    }
}
