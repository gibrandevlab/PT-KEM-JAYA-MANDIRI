<?php

namespace App\Filament\Admin\Resources\Vendors\Pages;

use App\Filament\Admin\Resources\Vendors\VendorResource;
use App\Models\Vendor;
use App\Filament\Admin\Resources\Vendors\Schemas\VendorForm;
use Filament\Resources\Pages\CreateRecord;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    public static function nextCode(): string
    {
        $prefix = 'VND-' . now()->year;
        $last = (int) Vendor::query()->where('kode_vendor', 'like', $prefix . '%')->selectRaw("MAX(CAST(SUBSTRING(kode_vendor, -4) AS UNSIGNED)) AS last_code")->value('last_code');
        return $prefix . str_pad((string) ($last + 1), 4, '0', STR_PAD_LEFT);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kode_vendor'] = filled($data['kode_vendor'] ?? null) ? $data['kode_vendor'] : self::nextCode();
        $data['pic'] = VendorForm::combinePics($this->form->getRawState()['pic_list'] ?? []);
        return $data;
    }
}
