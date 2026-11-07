<?php

namespace App\Filament\Admin\Resources\StockGudangs\Pages;

use App\Filament\Admin\Resources\StockGudangs\StockGudangResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStockGudang extends ViewRecord
{
    protected static string $resource = StockGudangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
