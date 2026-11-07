<?php

namespace App\Filament\Admin\Resources\Penawarans\Pages;

use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use App\Models\Penawaran;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;

class ViewPenawaran extends ViewRecord
{
    protected static string $resource = PenawaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak')->label('Cetak PDF / Print')->icon('heroicon-o-printer')
                ->url(fn (Penawaran $record): string => route('penawarans.print', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
            Action::make('ubahStatus')->label('Ubah Status')->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => auth()->user()?->role === 'admin'
                    && (auth()->user()?->hasAccess('penawarans', 'update') ?? false))
                ->fillForm(fn (Penawaran $record): array => ['status' => $record->status])
                ->form([Select::make('status')->options([
                    'draft' => 'Draft', 'terkirim' => 'Terkirim', 'disetujui' => 'Disetujui',
                    'ditolak' => 'Ditolak', 'expired' => 'Expired',
                ])->required()])
                ->action(fn (Penawaran $record, array $data) => $record->update($data)),
        ];
    }
}
