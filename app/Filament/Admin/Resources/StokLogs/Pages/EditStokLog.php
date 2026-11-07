<?php

namespace App\Filament\Admin\Resources\StokLogs\Pages;

use App\Filament\Admin\Resources\StokLogs\StokLogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStokLog extends EditRecord
{
    protected static string $resource = StokLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
