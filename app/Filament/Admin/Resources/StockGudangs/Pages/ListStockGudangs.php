<?php

namespace App\Filament\Admin\Resources\StockGudangs\Pages;

use App\Filament\Admin\Resources\StockGudangs\StockGudangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockGudangs extends ListRecords
{
    protected static string $resource = StockGudangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
