<?php

namespace App\Filament\Admin\Resources\Penawarans\Schemas;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerPic;
use App\Models\Penawaran;
use App\Models\TermOfPayment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PenawaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(['default' => 1, 'xl' => 3])->schema([
            Section::make('Informasi Penawaran')->columns(2)->schema([
                TextInput::make('no_penawaran')->required()->maxLength(30)->unique(ignoreRecord: true)
                    ->default(fn (): string => \App\Filament\Admin\Resources\Penawarans\Pages\CreatePenawaran::nextNumber())
                    ->columnSpanFull(),
                DatePicker::make('tanggal')->required()->default(now())->live()
                    ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                        if (! filled($state)) return;
                        $date = \Illuminate\Support\Carbon::parse($state);
                        $set('berlaku_sampai', $date->copy()->addWeek()->toDateString());
                        $set('no_penawaran', \App\Filament\Admin\Resources\Penawarans\Pages\CreatePenawaran::nextNumber($state, (bool) $get('use_sdk')));
                    }),
                DatePicker::make('berlaku_sampai')->label('Berlaku Sampai (Terkunci 1 Minggu)')->default(now()->addWeek())->readOnly(),
                Select::make('customer_id')
                    ->label('Pilih Pelanggan (opsional)')
                    ->options(fn (): array => Customer::query()->orderBy('nama_perusahaan')->pluck('nama_perusahaan', 'id')->all())
                    ->searchable()->preload()->live()
                    ->afterStateUpdated(function ($state, Set $set): void {
                        $customer = $state ? Customer::query()->find($state) : null;
                        if (! $customer) return;
                        $set('nama_customer', $customer->nama_perusahaan);
                        $set('alamat_customer', $customer->alamat);
                        $set('alamat_customer_2', $customer->alamat_gudang);
                        $set('telepon_customer', $customer->telepon);
                        $set('up', $customer->pic);
                        $set('mobile', $customer->pic_phone);
                        $set('email_up', $customer->pic_email);
                        $set('npwp_customer', $customer->npwp);
                    }),
                TextInput::make('nama_customer')->label('Nama Perusahaan')->required()->maxLength(150),
                Select::make('alamat_tersimpan')->label('Pilih Alamat Tersimpan')->dehydrated(false)
                    ->options(fn (Get $get): array => CustomerAddress::query()->where('customer_id', $get('customer_id'))
                        ->orderBy('label_alamat')->pluck('label_alamat', 'alamat_lengkap')->all())
                    ->visible(fn (Get $get): bool => filled($get('customer_id')))
                    ->afterStateUpdated(fn ($state, Set $set) => $set('alamat_customer', $state)),
                Textarea::make('alamat_customer')->label('Alamat Perusahaan')->rows(3),
                Textarea::make('alamat_customer_2')->label('Alamat Gudang')->rows(3),
                TextInput::make('telepon_customer')->label('Telepon Perusahaan'),
                TextInput::make('npwp_customer')->label('NPWP'),
                TextInput::make('up')->label('Nama PIC'),
                TextInput::make('mobile')->label('Telepon PIC'),
                TextInput::make('email_up')->label('Email PIC')->email(),
                Select::make('pic_tersimpan')->label('Pilih PIC Tersimpan')->dehydrated(false)
                    ->options(fn (Get $get): array => CustomerPic::query()->where('customer_id', $get('customer_id'))
                        ->where('tipe', 'Customer')->orderBy('nama')->get()
                        ->mapWithKeys(fn (CustomerPic $pic): array => [$pic->id => $pic->nama . ($pic->telepon ? " ({$pic->telepon})" : '')])->all())
                    ->visible(fn (Get $get): bool => filled($get('customer_id')))
                    ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                        $pic = $state ? CustomerPic::query()->find($state) : null;
                        if (! $pic || (int) $pic->customer_id !== (int) $get('customer_id')) return;
                        $set('up', $pic->nama);
                        $set('mobile', $pic->telepon);
                        $set('email_up', $pic->email);
                    }),
                TextInput::make('com_contact')->label('Kontak Penjualan')->default('Yudhi Syarif'),
                Toggle::make('use_sdk')->label('Gunakan Kode SDK')->default(false)->live()
                    ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                        if (! filled($get('tanggal'))) return;
                        $set('no_penawaran', \App\Filament\Admin\Resources\Penawarans\Pages\CreatePenawaran::nextNumber($get('tanggal'), (bool) $state));
                        if ($state && blank($get('kode_sdk'))) $set('kode_sdk', 'SDK');
                        if (! $state && $get('kode_sdk') === 'SDK') $set('kode_sdk', null);
                    }),
                TextInput::make('kode_sdk')->label('Kode SDK')->placeholder('Masukkan Kode SDK manual'),
                Textarea::make('perihal')->label('Catatan')->rows(3)->columnSpanFull(),
                TextInput::make('term_of_delivery')->label('Syarat Pengiriman'),
                TextInput::make('delivery_time')->label('Waktu Pengiriman'),
                Select::make('term_of_payment')->label('Syarat Pembayaran')
                    ->options(fn (): array => TermOfPayment::query()->orderBy('nama_term')->pluck('nama_term', 'nama_term')->all())
                    ->searchable(),
                Textarea::make('catatan')->label('Catatan')->rows(3)->columnSpanFull(),
            ])->columnSpan(['default' => 1, 'xl' => 2]),

            Section::make('Ringkasan Total')->schema([
                Placeholder::make('preview_subtotal')->label('Subtotal')->content(function (Get $get): string {
                    $subtotal = collect($get('penawaran_items') ?? [])->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
                    return 'Rp ' . number_format($subtotal, 0, ',', '.');
                }),
                Placeholder::make('preview_discount')->label(fn (Get $get): string => 'Diskon (' . (float) ($get('diskon_persen') ?: 0) . '%)')
                    ->content(function (Get $get): string {
                        $subtotal = collect($get('penawaran_items') ?? [])->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
                        return '- Rp ' . number_format($get('enable_diskon') ? round($subtotal * (float) $get('diskon_persen') / 100, 2) : 0, 0, ',', '.');
                    })->visible(fn (Get $get): bool => (bool) $get('enable_diskon')),
                Placeholder::make('preview_tax')->label(fn (Get $get): string => 'PPN (' . (float) ($get('ppn_persen') ?: 0) . '%)')
                    ->content(function (Get $get): string {
                        $subtotal = collect($get('penawaran_items') ?? [])->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
                        $discount = $get('enable_diskon') ? round($subtotal * (float) $get('diskon_persen') / 100, 2) : 0;
                        $tax = $get('enable_ppn') ? round(($subtotal - $discount) * (float) $get('ppn_persen') / 100, 2) : 0;
                        return 'Rp ' . number_format($tax, 0, ',', '.');
                    })->visible(fn (Get $get): bool => (bool) $get('enable_ppn')),
                Placeholder::make('preview_total')->label('Total Penawaran')->content(function (Get $get): HtmlString {
                    $subtotal = collect($get('penawaran_items') ?? [])->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
                    $discount = $get('enable_diskon') ? round($subtotal * (float) $get('diskon_persen') / 100, 2) : 0;
                    $tax = $get('enable_ppn') ? round(($subtotal - $discount) * (float) $get('ppn_persen') / 100, 2) : 0;
                    $total = number_format($subtotal - $discount + $tax, 0, ',', '.');
                    return new HtmlString('<div style="padding:22px 12px;text-align:center;background:var(--fi-color-gray-50);border-radius:12px;font-size:28px;font-weight:800;color:var(--fi-color-primary-600)">Rp ' . $total . '</div>');
                }),
                Toggle::make('enable_diskon')->label('Aktifkan Diskon')->dehydrated(false)
                    ->default(fn (?Penawaran $record): bool => (float) ($record?->diskon_persen ?? 0) > 0)->live(),
                TextInput::make('diskon_persen')->label('Diskon (%)')->numeric()->minValue(0)->maxValue(100)->live(onBlur: true)
                    ->default(fn (?Penawaran $record): float => (float) ($record?->diskon_persen ?? 0))
                    ->visible(fn (Get $get): bool => (bool) $get('enable_diskon')),
                Toggle::make('enable_ppn')->label('Aktifkan PPN')->dehydrated(false)
                    ->default(fn (?Penawaran $record): bool => (float) ($record?->ppn_persen ?? 0) > 0)->live(),
                TextInput::make('ppn_persen')->label('PPN (%)')->numeric()->minValue(0)->maxValue(100)->live(onBlur: true)
                    ->default(fn (?Penawaran $record): float => (float) ($record?->ppn_persen ?? 11))
                    ->visible(fn (Get $get): bool => (bool) $get('enable_ppn')),
            ])->columnSpan(['default' => 1, 'xl' => 1])
                ->extraAttributes(['class' => 'xl:sticky xl:top-24 xl:self-start']),

            Section::make('Rincian Barang')->schema([
                Repeater::make('penawaran_items')->relationship()->label('Item Penawaran')
                    ->defaultItems(1)->minItems(1)->orderColumn('urutan')->compact()
                    ->table([
                        TableColumn::make('Pilih Barang')->width('220px'),
                        TableColumn::make('Kode Barang')->width('130px'),
                        TableColumn::make('Nama Barang')->width('220px'),
                        TableColumn::make('Keterangan')->width('160px'),
                        TableColumn::make('Jumlah')->width('85px'),
                        TableColumn::make('Satuan')->width('90px'),
                        TableColumn::make('Harga Satuan')->width('140px'),
                        TableColumn::make('Total Harga')->width('140px'),
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::normalizeItem($data))
                    ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::normalizeItem($data))
                    ->schema([
                        Select::make('barang_id')->label('Pilih Barang')
                            ->options(fn (): array => Barang::query()->orderBy('nama_barang')->get()->mapWithKeys(fn (Barang $barang): array => [$barang->id => "{$barang->kode_barang} — {$barang->nama_barang}"])->all())
                            ->searchable()->preload()->live()
                            ->afterStateUpdated(function ($state, Set $set): void {
                                $barang = $state ? Barang::query()->find($state) : null;
                                if (! $barang) return;
                                $set('kode_barang', $barang->kode_barang);
                                $set('nama_barang', $barang->nama_barang);
                                $set('satuan', $barang->satuan ?: 'Pcs');
                                $set('harga_satuan', $barang->harga_satuan ?? 0);
                                $set('subtotal', round((float) $barang->harga_satuan * 1, 2));
                            }),
                        TextInput::make('kode_barang')->label('Kode Barang'),
                        TextInput::make('nama_barang')->label('Nama Barang')->required(),
                        TextInput::make('deskripsi')->label('Keterangan'),
                        TextInput::make('qty')->label('Jumlah')->numeric()->minValue(0.01)->default(1)->required()->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => $set('subtotal', round((float) $state * (float) $get('harga_satuan'), 2))),
                        TextInput::make('satuan')->label('Satuan')->default('Pcs')->required(),
                        TextInput::make('harga_satuan')->label('Harga Satuan')->numeric()->minValue(0)->default(0)->required()->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => $set('subtotal', round((float) $state * (float) $get('qty'), 2))),
                        TextInput::make('subtotal')->label('Subtotal Item')->numeric()->minValue(0)->default(0)->readOnly(),
                    ]),
            ])->columnSpanFull(),

            Section::make('Lampiran')->description('PDF, JPG, JPEG, atau PNG; maksimal 5 MB per file.')
                ->schema([
                    Repeater::make('penawaran_attachments')->relationship()->label('File Lampiran')->columns(1)
                        ->schema([
                            FileUpload::make('file_path')->label('File')->disk('public')->directory('penawaran')
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->maxSize(5120)
                                ->storeFileNamesIn('file_name')->required(),
                        ]),
                ])->columnSpanFull(),
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
