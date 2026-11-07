<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Schemas;

use App\Filament\Admin\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use App\Models\Barang;
use App\Models\Penawaran;
use App\Models\PurchaseOrder;
use App\Models\TermOfPayment;
use App\Models\Vendor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pesanan Pembelian')->columns(2)->schema([
                TextInput::make('no_po')->label('No. PO')->required()->maxLength(30)->unique(ignoreRecord: true)
                    ->default(fn (): string => CreatePurchaseOrder::nextNumber()),
                TextInput::make('no_po_customer')->label('Referensi Anda'),
                DatePicker::make('tanggal')->label('Tanggal PO')->required()->default(now())->live(),
                DatePicker::make('tanggal_kirim')->label('Tanggal Kirim'),
                TextInput::make('jenis')->hidden()->default('ke_vendor')->dehydrated(),
                Select::make('kategori_po')->label('Kategori PO')->options(['Dalam Negeri' => 'Dalam Negeri', 'Luar Negeri' => 'Luar Negeri'])
                    ->default('Dalam Negeri')->required()->live(),
                TextInput::make('currency')->label('Mata Uang')->default('IDR')->required()
                    ->visible(fn (Get $get): bool => $get('kategori_po') === 'Luar Negeri'),
                Select::make('vendor_id')->label('Pilih Pemasok')->options(fn (): array => Vendor::query()->orderBy('nama_perusahaan')->pluck('nama_perusahaan', 'id')->all())
                    ->searchable()->preload()->live()->afterStateUpdated(function ($state, Set $set): void {
                        $vendor = $state ? Vendor::query()->find($state) : null;
                        if (! $vendor) return;
                        $set('nama_perusahaan', $vendor->nama_perusahaan);
                        $set('alamat', $vendor->alamat);
                        $set('telepon', $vendor->telepon);
                        $set('pic', $vendor->pic);
                    }),
                TextInput::make('nama_perusahaan')->label('Nama Perusahaan')->required()->maxLength(150),
                Textarea::make('alamat')->label('Alamat')->rows(3),
                TextInput::make('telepon')->label('Telepon'),
                TextInput::make('pic')->label('PIC Pemasok'),
                TextInput::make('term_of_delivery')->label('Syarat Pengiriman'),
                TextInput::make('delivery_time')->label('Waktu Pengiriman'),
                Select::make('term_of_payment')->label('Syarat Pembayaran')
                    ->options(fn (): array => TermOfPayment::query()->orderBy('nama_term')->pluck('nama_term', 'nama_term')->all())->searchable(),
                Textarea::make('catatan')->label('Catatan Tambahan')->rows(3)->columnSpanFull(),
                Select::make('penawaran_id')->label('Penawaran Sumber')
                    ->options(fn (): array => Penawaran::query()->where('status', 'disetujui')->orderByDesc('tanggal')->pluck('no_penawaran', 'id')->all())
                    ->searchable()->preload()->live()->nullable()
                    ->afterStateUpdated(function ($state, Set $set): void {
                        $offer = $state ? Penawaran::query()->with('penawaran_items')->find($state) : null;
                        if (! $offer) return;
                        $set('nama_perusahaan', $offer->nama_customer);
                        $set('alamat', $offer->alamat_customer);
                        $set('telepon', $offer->telepon_customer);
                        $set('customer_id', $offer->customer_id);
                        $set('term_of_delivery', $offer->term_of_delivery);
                        $set('delivery_time', $offer->delivery_time);
                        $set('term_of_payment', $offer->term_of_payment);
                        $set('po_items', $offer->penawaran_items->map(fn ($item): array => [
                            'barang_id' => $item->barang_id, 'kode_barang' => $item->kode_barang,
                            'nama_barang' => $item->nama_barang, 'deskripsi' => $item->deskripsi,
                            'satuan' => $item->satuan, 'qty' => $item->qty,
                            'harga_satuan' => $item->harga_satuan, 'subtotal' => $item->subtotal,
                        ])->all());
                    }),
                TextInput::make('customer_id')->hidden()->numeric()->dehydrated(),
            ]),
            Section::make('Item Barang')->schema([
                Repeater::make('po_items')->relationship()->label('Rincian Pesanan Pembelian')->defaultItems(1)
                    ->minItems(1)->orderColumn('urutan')->columns(2)
                    ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::normalizeItem($data))
                    ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::normalizeItem($data))
                    ->schema([
                        Select::make('barang_id')->label('Pilih Barang')->options(fn (): array => Barang::query()->orderBy('nama_barang')->get()
                            ->mapWithKeys(fn (Barang $barang): array => [$barang->id => "{$barang->kode_barang} — {$barang->nama_barang}"])->all())
                            ->searchable()->preload()->live()->afterStateUpdated(function ($state, Set $set): void {
                                $barang = $state ? Barang::query()->find($state) : null;
                                if (! $barang) return;
                                $set('kode_barang', $barang->kode_barang);
                                $set('nama_barang', $barang->nama_barang);
                                $set('satuan', $barang->satuan ?: 'Pcs');
                                $set('harga_satuan', $barang->harga_beli ?? $barang->harga_satuan ?? 0);
                                $set('subtotal', (float) ($barang->harga_beli ?? $barang->harga_satuan ?? 0));
                            }),
                        TextInput::make('kode_barang')->label('Kode Barang'),
                        TextInput::make('nama_barang')->label('Nama Barang')->required(),
                        TextInput::make('qty')->label('Jumlah')->numeric()->minValue(0.01)->default(1)->required()->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => $set('subtotal', round((float) $state * (float) $get('harga_satuan'), 2))),
                        TextInput::make('satuan')->label('Satuan')->default('Pcs')->required(),
                        TextInput::make('harga_satuan')->label('Harga Satuan')->numeric()->minValue(0)->default(0)->required()->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => $set('subtotal', round((float) $state * (float) $get('qty'), 2))),
                        TextInput::make('deskripsi')->label('Deskripsi')->columnSpanFull(),
                        TextInput::make('subtotal')->label('Total Harga')->numeric()->minValue(0)->readOnly(),
                    ]),
            ]),
            Section::make('Ringkasan Harga')->columns(2)->schema([
                Toggle::make('enable_ppn')->label('Aktifkan PPN 11%')->dehydrated(false)
                    ->default(fn (?PurchaseOrder $record): bool => (float) ($record?->ppn_persen ?? 0) > 0)->live(),
                TextInput::make('ppn_persen')->label('PPN (%)')->numeric()->minValue(0)->maxValue(100)
                    ->default(fn (?PurchaseOrder $record): float => (float) ($record?->ppn_persen ?? 0))
                    ->visible(fn (Get $get): bool => $get('kategori_po') !== 'Luar Negeri' && (bool) $get('enable_ppn')),
                Placeholder::make('po_total_preview')->label('Ringkasan Total')->content(function (Get $get): string {
                    $subtotal = collect($get('po_items') ?? [])->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
                    $ppn = $get('kategori_po') !== 'Luar Negeri' && $get('enable_ppn') ? round($subtotal * (float) $get('ppn_persen') / 100, 2) : 0;
                    $currency = $get('kategori_po') === 'Luar Negeri' ? ($get('currency') ?: 'USD') : 'Rp';
                    return "Subtotal {$currency} " . number_format($subtotal, 0, ',', '.') . " · PPN {$currency} " . number_format($ppn, 0, ',', '.') . " · Total {$currency} " . number_format($subtotal + $ppn, 0, ',', '.');
                })->columnSpanFull(),
            ]),
            Section::make('Lampiran')->description('PDF, JPG, JPEG, PNG; maksimal 5 MB per file.')->schema([
                Repeater::make('po_attachments')->relationship()->label('Lampiran PO')->columns(1)
                    ->schema([FileUpload::make('file_path')->label('File')->disk('public')->directory('po')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->maxSize(5120)
                        ->storeFileNamesIn('file_name')->required()]),
            ]),
        ]);
    }

    private static function normalizeItem(array $data): array
    {
        $data['qty'] = (float) ($data['qty'] ?? 1);
        $data['harga_satuan'] = (float) ($data['harga_satuan'] ?? 0);
        $data['subtotal'] = round($data['qty'] * $data['harga_satuan'], 2);
        return $data;
    }
}
