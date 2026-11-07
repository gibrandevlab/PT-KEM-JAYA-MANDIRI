<?php

namespace App\Filament\Admin\Resources\CustomerPics\Pages;

use App\Filament\Admin\Resources\CustomerPics\CustomerPicResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomerPic extends ViewRecord
{
    protected static string $resource = CustomerPicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
