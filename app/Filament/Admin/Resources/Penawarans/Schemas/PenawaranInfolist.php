<?php

namespace App\Filament\Admin\Resources\Penawarans\Schemas;

use App\Models\Penawaran;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class PenawaranInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Penawaran')->columns(2)->schema([
                TextEntry::make('no_penawaran')->label('No. Penawaran'),
                TextEntry::make('tanggal')->date('d F Y'),
                TextEntry::make('berlaku_sampai')->label('Berlaku Sampai')->date('d F Y')->placeholder('-'),
                TextEntry::make('status')->badge(),
                TextEntry::make('nama_customer')->label('Perusahaan'),
                TextEntry::make('customer.nama_perusahaan')->label('Data Customer')->placeholder('-'),
                TextEntry::make('alamat_customer')->label('Alamat')->placeholder('-')->columnSpanFull(),
                TextEntry::make('alamat_customer_2')->label('Alamat Gudang')->placeholder('-')->columnSpanFull(),
                TextEntry::make('telepon_customer')->label('Telepon')->placeholder('-'),
                TextEntry::make('npwp_customer')->label('NPWP')->placeholder('-'),
                TextEntry::make('up')->label('PIC')->placeholder('-'),
                TextEntry::make('mobile')->label('Telepon PIC')->placeholder('-'),
                TextEntry::make('email_up')->label('Email PIC')->placeholder('-'),
                TextEntry::make('com_contact')->label('Com Contact')->placeholder('-'),
                TextEntry::make('kode_sdk')->label('Kode SDK')->placeholder('-'),
                IconEntry::make('use_sdk')->label('Menggunakan SDK')->boolean(),
                TextEntry::make('perihal')->label('Note')->placeholder('-')->columnSpanFull(),
                TextEntry::make('term_of_delivery')->label('Term of Delivery')->placeholder('-'),
                TextEntry::make('delivery_time')->label('Delivery Time')->placeholder('-'),
                TextEntry::make('term_of_payment')->label('Term of Payment')->placeholder('-'),
                TextEntry::make('catatan')->label('Catatan')->placeholder('-')->columnSpanFull(),
            ]),
            Section::make('Rincian Barang')->schema([
                RepeatableEntry::make('penawaran_items')->label('')
                    ->schema([
                        TextEntry::make('kode_barang')->label('Kode')->placeholder('-'),
                        TextEntry::make('nama_barang')->label('Barang'),
                        TextEntry::make('deskripsi')->label('Keterangan')->placeholder('-'),
                        TextEntry::make('qty')->label('Qty'),
                        TextEntry::make('satuan')->label('Satuan'),
                        TextEntry::make('harga_satuan')->label('Harga')->money('IDR'),
                        TextEntry::make('subtotal')->label('Subtotal')->money('IDR'),
                    ])->columns(2),
            ]),
            Section::make('Ringkasan')->columns(2)->schema([
                TextEntry::make('subtotal')->money('IDR'),
                TextEntry::make('diskon_persen')->label('Diskon (%)'),
                TextEntry::make('diskon_nominal')->label('Diskon')->money('IDR'),
                TextEntry::make('ppn_persen')->label('PPN (%)'),
                TextEntry::make('ppn_nominal')->label('PPN')->money('IDR'),
                TextEntry::make('total')->money('IDR')->weight('bold'),
                TextEntry::make('user.nama')->label('Dibuat oleh'),
                TextEntry::make('created_at')->label('Dibuat')->dateTime(),
                TextEntry::make('updated_at')->label('Terakhir diperbarui')->dateTime(),
            ]),
            Section::make('Riwayat & Dokumen Terkait')->columns(2)->schema([
                RepeatableEntry::make('purchase_orders')->label('Purchase Order')
                    ->schema([
                        TextEntry::make('no_po')->label('Nomor PO'),
                        TextEntry::make('created_at')->label('Dibuat')->dateTime(),
                    ])->columns(2),
                RepeatableEntry::make('surat_jalans')->label('Surat Jalan')
                    ->schema([
                        TextEntry::make('no_sj')->label('Nomor Surat Jalan'),
                        TextEntry::make('created_at')->label('Dibuat')->dateTime(),
                    ])->columns(2),
            ])->visible(fn (Penawaran $record): bool => $record->purchase_orders()->exists() || $record->surat_jalans()->exists()),
            Section::make('Lampiran')->schema([
                RepeatableEntry::make('penawaran_attachments')->label('')
                    ->schema([
                        TextEntry::make('file_name')->label('Nama File')
                            ->url(fn ($state, $record): string => Storage::disk('public')->url($record->file_path))
                            ->openUrlInNewTab(),
                    ]),
            ])->visible(fn ($record): bool => $record?->penawaran_attachments()->exists() ?? false),
        ]);
    }
}
