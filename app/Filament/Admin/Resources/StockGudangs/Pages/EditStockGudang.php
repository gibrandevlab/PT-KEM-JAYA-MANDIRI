<?php

namespace App\Filament\Admin\Resources\StockGudangs\Pages;

use App\Filament\Admin\Resources\StockGudangs\StockGudangResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStockGudang extends EditRecord
{
    protected static string $resource = StockGudangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
