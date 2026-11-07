<?php

namespace App\Filament\Admin\Resources\KategoriBarangs\Pages;

use App\Filament\Admin\Resources\KategoriBarangs\KategoriBarangResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKategoriBarang extends ViewRecord
{
    protected static string $resource = KategoriBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
