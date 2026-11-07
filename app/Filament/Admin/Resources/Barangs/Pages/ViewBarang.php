<?php

namespace App\Filament\Admin\Resources\Barangs\Pages;

use App\Filament\Admin\Resources\Barangs\BarangResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBarang extends ViewRecord
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
