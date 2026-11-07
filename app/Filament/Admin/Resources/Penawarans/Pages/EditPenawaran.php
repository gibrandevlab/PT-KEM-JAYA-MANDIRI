<?php

namespace App\Filament\Admin\Resources\Penawarans\Pages;

use App\Models\Penawaran;
use App\Models\PenawaranItem;
use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;

class EditPenawaran extends EditRecord
{
    protected static string $resource = PenawaranResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $state = $this->form->getRawState();
        $items = is_array($state) ? ($state['penawaran_items'] ?? []) : [];
        $subtotal = collect($items)->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
        $discountRate = ($state['enable_diskon'] ?? false) ? (float) ($state['diskon_persen'] ?? 0) : 0.0;
        $discount = round($subtotal * $discountRate / 100, 2);
        $afterDiscount = $subtotal - $discount;
        $taxRate = ($state['enable_ppn'] ?? false) ? (float) ($state['ppn_persen'] ?? 0) : 0.0;
        $tax = round($afterDiscount * $taxRate / 100, 2);

        $data['subtotal'] = $subtotal;
        $data['diskon_persen'] = $discountRate;
        $data['diskon_nominal'] = $discount;
        $data['ppn_persen'] = $taxRate;
        $data['ppn_nominal'] = $tax;
        $data['total'] = $afterDiscount + $tax;

        return $data;
    }

    protected function afterSave(): void
    {
        $this->form->model($this->record)->saveRelationships();
    }

    protected function getRedirectUrl(): string
    {
        return PenawaranResource::getUrl('view', ['record' => $this->record]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            Action::make('buatRevisi')->label('Buat Revisi')->icon('heroicon-o-document-duplicate')
                ->visible(fn (Penawaran $record): bool => $record->status === 'selesai'
                    && auth()->user()?->role === 'admin'
                    && (auth()->user()?->hasAccess('penawarans', 'update') ?? false))
                ->form([TextInput::make('revisi_ke')->label('Revisi ke')->required()->maxLength(50)])
                ->requiresConfirmation()
                ->action(fn (Penawaran $record, array $data) => $this->createRevision($record, $data['revisi_ke'])),
            DeleteAction::make(),
        ];
    }

    private function createRevision(Penawaran $record, string $revision): void
    {
        $newRecord = DB::transaction(function () use ($record, $revision): Penawaran {
            $copy = $record->replicate(['id', 'created_at', 'updated_at']);
            $copy->created_by = auth()->id();
            $copy->is_revisi = true;
            $copy->revisi_ke = $revision;
            $copy->is_history = false;
            $copy->save();

            foreach ($record->penawaran_items as $item) {
                $newItem = $item->replicate(['id']);
                $copy->penawaran_items()->save($newItem);
            }

            return $copy;
        });

        $this->redirect(PenawaranResource::getUrl('view', ['record' => $newRecord]));
    }
}
