<?php

namespace App\Filament\Admin\Resources\StokLogs\Pages;

use App\Filament\Admin\Resources\StokLogs\StokLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStokLog extends ViewRecord
{
    protected static string $resource = StokLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
