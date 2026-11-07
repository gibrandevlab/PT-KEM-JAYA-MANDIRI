<?php

namespace App\Filament\Admin\Resources\Barangs\Pages;

use App\Filament\Admin\Resources\Barangs\BarangResource;
use App\Filament\Admin\Resources\Barangs\Actions\ImportBarangAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportBarangAction::make(),
            CreateAction::make(),
        ];
    }
}
