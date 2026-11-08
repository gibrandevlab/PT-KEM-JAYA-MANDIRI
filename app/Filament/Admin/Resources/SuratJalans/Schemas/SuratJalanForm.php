<?php

namespace App\Filament\Admin\Resources\SuratJalans\Schemas;

use App\Filament\Admin\Resources\SuratJalans\Pages\CreateSuratJalan;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\Penawaran;
use App\Models\PurchaseOrder;
use App\Models\SuratJalan;
use App\Filament\Admin\Resources\Concerns\HasBarangPicker;
use App\Filament\Admin\Resources\Concerns\HasCustomerPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class SuratJalanForm
{
    use HasBarangPicker;
    use HasCustomerPicker;

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pengiriman')->description('Pilih referensi, pelanggan, alamat tujuan, dan PIC penerima.')->columns(['default' => 1, 'md' => 2, 'xl' => 3])->schema([
                TextInput::make('no_sj')->label('No. SJ')->required()->unique(ignoreRecord: true)->default(fn (): string => CreateSuratJalan::nextNumber()),
                DatePicker::make('tanggal')->label('Tanggal SJ')->required()->default(now()),
                Select::make('po_id')->label('Referensi Pesanan Pembelian')->options(fn (): array => PurchaseOrder::query()->whereIn('status', ['diproses', 'selesai'])->orderByDesc('created_at')->pluck('no_po', 'id')->all())
                    ->searchable()->preload()->live()->afterStateUpdated(function ($state, Set $set): void {
                        if (! $state) return;
                        $po = PurchaseOrder::query()->with('po_items')->find($state);
                        if (! $po) return;
                        $customer = $po->customer_id ? Customer::query()->find($po->customer_id) : null;
                        $set('penawaran_id', $po->penawaran_id);
                        $set('customer_id', $po->customer_id);
                        $set('nama_penerima', $po->nama_perusahaan);
                        $set('alamat_pengiriman', $customer?->alamat_gudang ?: $po->alamat);
                        $set('telepon_penerima', $customer?->pic_gudang_phone ?: $po->telepon);
                        $set('pic_penerima', $customer?->pic_gudang ?: $po->pic);
                        $set('no_po', $po->no_po);
                        $set('surat_jalan_items', $po->po_items->map(fn ($item): array => [
                            'barang_id' => $item->barang_id, 'kode_barang' => $item->kode_barang,
                            'nama_barang' => $item->nama_barang, 'satuan' => $item->satuan, 'qty' => $item->qty,
                        ])->all());
                    }),
                Select::make('penawaran_id')->label('Referensi Penawaran')->options(fn (): array => Penawaran::query()->whereNotIn('status', ['dibatalkan', 'selesai'])->orderByDesc('created_at')->pluck('no_penawaran', 'id')->all())
                    ->searchable()->preload()->live()->afterStateUpdated(function ($state, Set $set): void {
                        $offer = $state ? Penawaran::query()->with('penawaran_items')->find($state) : null;
                        if (! $offer) return;
                        $customer = $offer->customer_id ? Customer::query()->find($offer->customer_id) : null;
                        $set('customer_id', $offer->customer_id);
                        $set('nama_penerima', $offer->nama_customer);
                        $set('alamat_pengiriman', $customer?->alamat_gudang ?: ($offer->alamat_customer_2 ?: $offer->alamat_customer));
                        $set('telepon_penerima', $customer?->pic_gudang_phone ?: $offer->telepon_customer);
                        $set('pic_penerima', $customer?->pic_gudang ?: $offer->up);
                        $set('surat_jalan_items', $offer->penawaran_items->map(fn ($item): array => [
                            'barang_id' => $item->barang_id, 'kode_barang' => $item->kode_barang,
                            'nama_barang' => $item->nama_barang, 'satuan' => $item->satuan, 'qty' => $item->qty,
                        ])->all());
                    }),
                self::customerPicker(['nama_penerima' => 'nama_perusahaan', 'alamat_pengiriman' => ['alamat_gudang', 'alamat'], 'telepon_penerima' => ['pic_gudang_phone', 'telepon'], 'pic_penerima' => ['pic_gudang', 'pic']]),
                Hidden::make('customer_id'),
                TextInput::make('nama_penerima')->label('Nama Penerima')->required(),
                Textarea::make('alamat_pengiriman')->label('Alamat Pengiriman')->required()->rows(3),
                TextInput::make('kota_tujuan')->label('Kota Tujuan'),
                TextInput::make('telepon_penerima')->label('Telepon Penerima')->tel(),
                TextInput::make('pic_penerima')->label('PIC Penerima'),
                TextInput::make('no_po')->label('Nomor PO Pelanggan'),
                TextInput::make('kendaraan')->label('Kendaraan'),
                TextInput::make('no_polisi')->label('Nomor Polisi'),
                Textarea::make('catatan')->label('Catatan')->rows(3)->columnSpanFull(),
            ])->columnSpanFull(),
            Section::make('Rincian Barang')->schema([
                self::barangPicker('surat_jalan_items', includePrice: false),
                Repeater::make('surat_jalan_items')->relationship()->label('Item Surat Jalan')->defaultItems(1)->minItems(1)->orderColumn('urutan')->columns(['default' => 1, 'md' => 2])
                    ->schema([
                        Select::make('barang_id')->label('Pilih Barang')->options(fn (): array => Barang::query()->orderBy('nama_barang')->get()->mapWithKeys(fn (Barang $item): array => [$item->id => "{$item->kode_barang} — {$item->nama_barang}"])->all())
                            ->searchable()->preload()->live()->afterStateUpdated(function ($state, Set $set): void {
                                $barang = $state ? Barang::query()->find($state) : null;
                                if (! $barang) return;
                                $set('kode_barang', $barang->kode_barang); $set('nama_barang', $barang->nama_barang); $set('satuan', $barang->satuan ?: 'Pcs');
                            }),
                        TextInput::make('kode_barang')->label('Kode Barang'),
                        TextInput::make('nama_barang')->label('Nama Barang')->required(),
                        TextInput::make('qty')->label('Jumlah')->numeric()->minValue(1)->default(1)->required(),
                        TextInput::make('satuan')->label('Satuan')->default('Pcs')->required(),
                        TextInput::make('keterangan')->label('Keterangan'),
                    ]),
            ])->columnSpanFull(),
        ]);
    }
}
