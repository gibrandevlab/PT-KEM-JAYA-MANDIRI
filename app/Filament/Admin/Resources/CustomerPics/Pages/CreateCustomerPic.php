<?php

namespace App\Filament\Admin\Resources\CustomerPics\Pages;

use App\Filament\Admin\Resources\CustomerPics\CustomerPicResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerPic extends CreateRecord
{
    protected static string $resource = CustomerPicResource::class;
}
