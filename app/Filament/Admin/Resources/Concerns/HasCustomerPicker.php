<?php

namespace App\Filament\Admin\Resources\Concerns;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Actions;
use Livewire\Component as LivewireComponent;

trait HasCustomerPicker
{
    /**
     * @param array<string, string|list<string>> $fieldMap Form field => Customer attribute(s), in fallback order.
     */
    protected static function customerPicker(array $fieldMap): Actions
    {
        return Actions::make([
            Action::make('pilihCustomer')
                ->label('Pilih Customer')
                ->icon('heroicon-o-building-office-2')
                ->modalHeading('Pilih Customer')
                ->modalDescription('Cari dan pilih Customer dari data Master Customer.')
                ->modalSubmitActionLabel('Gunakan Customer')
                ->schema([
                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(fn (): array => Customer::query()->orderBy('nama_perusahaan')->pluck('nama_perusahaan', 'id')->all())
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data, LivewireComponent $livewire) use ($fieldMap): void {
                    $customer = Customer::query()->find($data['customer_id'] ?? null);
                    if (! $customer) {
                        return;
                    }

                    $state = $livewire->form->getRawState();
                    $updates = ['customer_id' => $customer->id];
                    foreach ($fieldMap as $field => $attribute) {
                        foreach ((array) $attribute as $candidate) {
                            $value = $customer->getAttribute($candidate);
                            if (filled($value)) {
                                $updates[$field] = $value;
                                break;
                            }
                        }
                    }
                    $livewire->form->fill([...$state, ...$updates]);
                }),
        ])->fullWidth();
    }
}
