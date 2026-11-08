<?php

namespace App\Filament\Admin\Resources\Concerns;

use App\Models\Barang;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Actions;
use Livewire\Component as LivewireComponent;

trait HasBarangPicker
{
    protected static function barangPicker(string $itemsKey, bool $usePurchasePrice = false, bool $includePrice = true): Actions
    {
        return Actions::make([
            Action::make('pilihBanyakBarang')
                ->label('Pilih Banyak Barang')
                ->icon('heroicon-o-squares-plus')
                ->color('primary')
                ->modalHeading('Pilih Barang')
                ->modalDescription('Cari nama atau kode barang, centang yang dipilih, lalu tekan Tambahkan Barang. Daftar bisa digulir; tombol tetap tersedia di bawah.')
                ->modalSubmitActionLabel('Tambahkan Barang')
                ->modalWidth('7xl')
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->schema([
                    CheckboxList::make('barang_ids')
                        ->label('Daftar Barang')
                        ->options(fn (): array => Barang::query()->orderBy('nama_barang')->get()
                            ->mapWithKeys(fn (Barang $barang): array => [$barang->id => "{$barang->kode_barang} — {$barang->nama_barang}"])->all())
                        ->searchable()
                        ->bulkToggleable()
                        ->gridDirection('row')
                        ->columns(['default' => 1, 'md' => 2, 'xl' => 3])
                        ->extraAttributes([
                            'class' => 'barang-picker-options',
                            'style' => 'max-height: min(52vh, 32rem); overflow-y: auto; overscroll-behavior: contain; padding-inline-end: .5rem;',
                            'tabindex' => '0',
                            'aria-label' => 'Daftar barang yang bisa digulir',
                        ])
                        ->required()
                        ->minItems(1),
                ])
                ->action(function (array $data, LivewireComponent $livewire) use ($itemsKey, $usePurchasePrice, $includePrice): void {
                    $state = $livewire->form->getRawState();
                    $items = collect($state[$itemsKey] ?? [])
                        ->filter(fn (array $item): bool => filled($item['barang_id'] ?? null)
                            || filled($item['kode_barang'] ?? null)
                            || filled($item['nama_barang'] ?? null))
                        ->values()
                        ->all();
                    $barangs = Barang::query()->whereKey($data['barang_ids'] ?? [])->get()->keyBy('id');

                    foreach ($data['barang_ids'] ?? [] as $barangId) {
                        $barang = $barangs->get($barangId);
                        if (! $barang) {
                            continue;
                        }

                        $price = $usePurchasePrice ? ($barang->harga_beli ?? $barang->harga_satuan ?? 0) : ($barang->harga_satuan ?? 0);
                        $item = [
                            'barang_id' => $barang->id,
                            'kode_barang' => $barang->kode_barang,
                            'nama_barang' => $barang->nama_barang,
                            'qty' => 1,
                            'satuan' => $barang->satuan ?: 'Pcs',
                        ];
                        if ($includePrice) {
                            $item['harga_satuan'] = $price;
                            $item['subtotal'] = $price;
                        }
                        $items[] = $item;
                    }

                    $livewire->form->fill([...$state, $itemsKey => $items]);
                }),
        ])->fullWidth();
    }
}
