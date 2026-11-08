<?php

namespace App\Filament\Admin\Resources\Concerns;

use App\Models\Barang;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Livewire\Component as LivewireComponent;

trait HasBarangPicker
{
    protected static function barangPicker(
        string $itemsKey,
        bool $usePurchasePrice = false,
        bool $includePrice = true,
        float $minimumQuantity = 0.01,
    ): Actions
    {
        return Actions::make([
            Action::make('pilihBanyakBarang')
                ->label('Pilih Banyak Barang')
                ->icon('heroicon-o-squares-plus')
                ->color('primary')
                ->modalHeading('Pilih Barang')
                ->modalDescription('Pilih barang dan tentukan jumlahnya untuk setiap baris sebelum menambahkannya ke transaksi.')
                ->modalSubmitActionLabel('Tambahkan Barang')
                ->modalWidth('7xl')
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->schema([
                    Repeater::make('items')
                        ->label('Barang dan Jumlah')
                        ->defaultItems(1)
                        ->minItems(1)
                        ->addActionLabel('Tambah Barang')
                        ->reorderable(false)
                        ->columns(['default' => 1, 'md' => 2])
                        ->schema([
                            Select::make('barang_id')
                                ->label('Pilih Barang')
                                ->options(fn (): array => Barang::query()->orderBy('nama_barang')->get()
                                    ->mapWithKeys(fn (Barang $barang): array => [$barang->id => "{$barang->kode_barang} — {$barang->nama_barang}"])->all())
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('qty')
                                ->label('Jumlah')
                                ->numeric()
                                ->minValue($minimumQuantity)
                                ->default(1)
                                ->required(),
                        ]),
                ])
                ->action(function (array $data, LivewireComponent $livewire) use ($itemsKey, $usePurchasePrice, $includePrice): void {
                    $state = $livewire->form->getRawState();
                    $items = collect($state[$itemsKey] ?? [])
                        ->filter(fn (array $item): bool => filled($item['barang_id'] ?? null)
                            || filled($item['kode_barang'] ?? null)
                            || filled($item['nama_barang'] ?? null))
                        ->values()
                        ->all();
                    $selectedItems = $data['items'] ?? [];
                    $barangs = Barang::query()
                        ->whereKey(collect($selectedItems)->pluck('barang_id')->filter()->all())
                        ->get()
                        ->keyBy('id');

                    foreach ($selectedItems as $selectedItem) {
                        $barangId = $selectedItem['barang_id'] ?? null;
                        $barang = $barangs->get($barangId);
                        if (! $barang) {
                            continue;
                        }

                        $quantity = (float) ($selectedItem['qty'] ?? 1);
                        $price = $usePurchasePrice ? ($barang->harga_beli ?? $barang->harga_satuan ?? 0) : ($barang->harga_satuan ?? 0);
                        $item = [
                            'barang_id' => $barang->id,
                            'kode_barang' => $barang->kode_barang,
                            'nama_barang' => $barang->nama_barang,
                            'qty' => $quantity,
                            'satuan' => $barang->satuan ?: 'Pcs',
                        ];
                        if ($includePrice) {
                            $item['harga_satuan'] = $price;
                            $item['subtotal'] = round($price * $quantity, 2);
                        }
                        $items[] = $item;
                    }

                    $livewire->form->fill([...$state, $itemsKey => $items]);
                }),
        ])->fullWidth();
    }
}
