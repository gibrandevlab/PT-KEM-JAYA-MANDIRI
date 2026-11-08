<?php

namespace App\Filament\Admin\Resources\Penawarans\Schemas;

use App\Models\Penawaran;
use App\Filament\Admin\Resources\Concerns\HasAttachmentPreview;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PenawaranInfolist
{
    use HasAttachmentPreview;

    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['default' => 1, 'md' => 2, 'xl' => 4])->components([
            // ── Header ringkas ─────────────────────────────────────────────
            Section::make()
                ->columns(['default' => 2, 'md' => 4])
                ->schema([
                    TextEntry::make('no_penawaran')
                        ->label('No. Penawaran')
                        ->icon('heroicon-o-document-text')
                        ->iconColor('primary')
                        ->weight('bold')
                        ->size('lg')
                        ->copyable(),
                    TextEntry::make('tanggal')
                        ->label('Tanggal')
                        ->icon('heroicon-o-calendar-days')
                        ->date('d F Y'),
                    TextEntry::make('berlaku_sampai')
                        ->label('Berlaku Sampai')
                        ->icon('heroicon-o-clock')
                        ->date('d F Y')
                        ->badge()
                        ->color(fn ($state): string => $state && now()->greaterThan($state) ? 'danger' : 'success')
                        ->placeholder('-'),
                    TextEntry::make('total')
                        ->label('Total')
                        ->money('IDR')
                        ->weight('bold')
                        ->size('lg')
                        ->color('primary'),
                ])->columnSpanFull(),

            // ── Customer ───────────────────────────────────────────────────
            Section::make('Informasi Customer')
                ->description('Data perusahaan dan kontak yang dituju.')
                ->icon('heroicon-o-building-office-2')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    TextEntry::make('nama_customer')->label('Perusahaan')->icon('heroicon-o-building-office')->weight('bold'),
                    TextEntry::make('npwp_customer')->label('NPWP')->icon('heroicon-o-identification')->copyable()->placeholder('-'),
                    TextEntry::make('alamat_customer')->label('Alamat')->icon('heroicon-o-map-pin')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('alamat_customer_2')->label('Alamat Gudang')->icon('heroicon-o-truck')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('telepon_customer')->label('Telepon')->icon('heroicon-o-phone')->placeholder('-'),
                    Fieldset::make('PIC')
                        ->columns(1)
                        ->schema([
                            TextEntry::make('up')->label('Nama PIC')->icon('heroicon-o-user')->placeholder('-'),
                            TextEntry::make('mobile')->label('Telepon PIC')->icon('heroicon-o-device-phone-mobile')->placeholder('-'),
                            TextEntry::make('email_up')->label('Email PIC')->icon('heroicon-o-envelope')->copyable()->placeholder('-'),
                        ]),
                ])->columnSpanFull(),

            // ── Ketentuan ──────────────────────────────────────────────────
            Section::make('Ketentuan Penawaran')
                ->description('Syarat pengiriman, pembayaran, dan catatan.')
                ->icon('heroicon-o-clipboard-document-check')
                ->columns(['default' => 1, 'md' => 3])
                ->schema([
                    TextEntry::make('term_of_delivery')->label('Syarat Pengiriman')->icon('heroicon-o-truck')->badge()->color('info')->placeholder('-'),
                    TextEntry::make('delivery_time')->label('Waktu Pengiriman')->icon('heroicon-o-clock')->badge()->color('gray')->placeholder('-'),
                    TextEntry::make('term_of_payment')->label('Syarat Pembayaran')->icon('heroicon-o-banknotes')->badge()->color('warning')->placeholder('-'),
                    TextEntry::make('com_contact')->label('Kontak Penjualan')->icon('heroicon-o-user-circle')->placeholder('-'),
                    TextEntry::make('kode_sdk')->label('Kode SDK')->icon('heroicon-o-qr-code')->placeholder('-'),
                    IconEntry::make('use_sdk')->label('Menggunakan SDK')->boolean(),
                    TextEntry::make('perihal')->label('Note')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('catatan')->label('Catatan')->placeholder('-')->columnSpanFull(),
                ])->columnSpanFull(),

            // ── Rincian barang ─────────────────────────────────────────────
            Section::make('Rincian Barang')
                ->icon('heroicon-o-cube')
                ->schema([
                    RepeatableEntry::make('penawaran_items')
                        ->label('')
                        ->contained()
                        ->columns(['default' => 2, 'md' => 4, 'xl' => 6])
                        ->schema([
                            TextEntry::make('kode_barang')->label('Kode')->badge()->color('gray')->placeholder('-'),
                            TextEntry::make('nama_barang')->label('Barang')->weight('bold')->columnSpan(['default' => 2, 'md' => 2]),
                            TextEntry::make('qty')->label('Jumlah')->suffix(fn ($record) => ' ' . ($record->satuan ?? ''))->numeric(),
                            TextEntry::make('harga_satuan')->label('Harga')->money('IDR'),
                            TextEntry::make('subtotal')->label('Subtotal')->money('IDR')->weight('bold')->color('primary'),
                            TextEntry::make('deskripsi')->label('Keterangan')->placeholder('-')->columnSpanFull(),
                        ]),
                ])->columnSpanFull(),

            // ── Ringkasan ──────────────────────────────────────────────────
            Section::make('Ringkasan Biaya')
                ->icon('heroicon-o-calculator')
                ->columns(['default' => 2, 'md' => 3])
                ->schema([
                    TextEntry::make('subtotal')->money('IDR'),
                    TextEntry::make('diskon_persen')->label('Diskon (%)')->suffix('%')->badge()->color('success'),
                    TextEntry::make('diskon_nominal')->label('Diskon')->money('IDR')->color('success'),
                    TextEntry::make('ppn_persen')->label('PPN (%)')->suffix('%')->badge()->color('warning'),
                    TextEntry::make('ppn_nominal')->label('PPN')->money('IDR'),
                    TextEntry::make('total')->money('IDR')->weight('bold')->size('lg')->color('primary'),
                ])->columnSpanFull(),

            // ── Audit ──────────────────────────────────────────────────────
            Section::make('Informasi Sistem')
                ->icon('heroicon-o-information-circle')
                ->collapsible()
                ->collapsed()
                ->columns(['default' => 1, 'md' => 3])
                ->schema([
                    TextEntry::make('user.nama')->label('Dibuat oleh')->icon('heroicon-o-user'),
                    TextEntry::make('created_at')->label('Dibuat')->dateTime()->icon('heroicon-o-plus-circle'),
                    TextEntry::make('updated_at')->label('Terakhir diperbarui')->dateTime()->icon('heroicon-o-arrow-path'),
                ])->columnSpanFull(),

            // ── Dokumen terkait ────────────────────────────────────────────
            Section::make('Riwayat & Dokumen Terkait')
                ->icon('heroicon-o-link')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    RepeatableEntry::make('purchase_orders')->label('Pesanan Pembelian')
                        ->contained()
                        ->schema([
                            TextEntry::make('no_po')->label('Nomor PO')->icon('heroicon-o-shopping-cart')->badge()->color('primary'),
                            TextEntry::make('created_at')->label('Dibuat')->dateTime(),
                        ])->columns(2),
                    RepeatableEntry::make('surat_jalans')->label('Surat Jalan')
                        ->contained()
                        ->schema([
                            TextEntry::make('no_sj')->label('Nomor Surat Jalan')->icon('heroicon-o-truck')->badge()->color('info'),
                            TextEntry::make('created_at')->label('Dibuat')->dateTime(),
                        ])->columns(2),
                ])->columnSpanFull()
                ->visible(fn (Penawaran $record): bool => $record->purchase_orders()->exists() || $record->surat_jalans()->exists()),

            // ── Lampiran ───────────────────────────────────────────────────
            Section::make('Lampiran')
                ->icon('heroicon-o-paper-clip')
                ->schema([
                    RepeatableEntry::make('penawaran_attachments')->label('')
                        ->schema([
                            TextEntry::make('file_name')->label('Nama File')
                                ->icon('heroicon-o-document-arrow-down')
                                ->belowContent([self::attachmentPreviewAction()]),
                        ]),
                ])->columnSpanFull()
                ->visible(fn ($record): bool => $record?->penawaran_attachments()->exists() ?? false),
        ]);
    }
}
