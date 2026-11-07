<?php

namespace App\Filament\Admin\Resources\CustomerPics\Pages;

use App\Filament\Admin\Resources\CustomerPics\CustomerPicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerPics extends ListRecords
{
    protected static string $resource = CustomerPicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
