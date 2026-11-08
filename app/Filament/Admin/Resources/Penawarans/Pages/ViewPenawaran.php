<?php

namespace App\Filament\Admin\Resources\Penawarans\Pages;

use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use App\Models\Penawaran;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPenawaran extends ViewRecord
{
    protected static string $resource = PenawaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak')->label('Cetak PDF')->icon('heroicon-o-printer')
                ->modalHeading('Pratinjau Penawaran')->modalWidth('7xl')->modalSubmitAction(false)->modalCancelActionLabel('Tutup')
                ->modalContent(fn (Penawaran $record) => view('filament.admin.print-preview', ['url' => route('penawarans.print', $record)])),
            EditAction::make(),
        ];
    }
}
