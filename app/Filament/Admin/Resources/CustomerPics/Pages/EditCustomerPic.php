<?php

namespace App\Filament\Admin\Resources\CustomerPics\Pages;

use App\Filament\Admin\Resources\CustomerPics\CustomerPicResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerPic extends EditRecord
{
    protected static string $resource = CustomerPicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
