<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Schemas;

use App\Models\PurchaseOrder;
use App\Filament\Admin\Resources\Concerns\HasAttachmentPreview;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PurchaseOrderInfolist
{
    use HasAttachmentPreview;

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Purchase Order')->contained()->columns(['default' => 1, 'md' => 2])->schema([
                TextEntry::make('jenis')->label('Jenis PO')->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'dari_customer' ? 'PO Dari Customer' : 'PO Ke Vendor'),
                TextEntry::make('status')->badge(),
                TextEntry::make('nama_perusahaan')->label(fn (PurchaseOrder $record): string => $record->jenis === 'dari_customer' ? 'Customer' : 'Vendor')->weight('bold'),
                TextEntry::make('no_po')->label('Nomor PO')->color('primary'),
                TextEntry::make('no_po_customer')->label('No. PO Customer')->placeholder('-')
                    ->visible(fn (PurchaseOrder $record): bool => $record->jenis === 'dari_customer'),
                TextEntry::make('tanggal')->label('Tanggal PO')->date('d F Y'),
                TextEntry::make('tanggal_kirim')->label('Tanggal Kirim (Estimasi)')->date('d F Y')->placeholder('-'),
                TextEntry::make('kategori_po')->label('Kategori PO')->placeholder('-'),
                TextEntry::make('currency')->label('Mata Uang')->placeholder('-'),
                TextEntry::make('alamat')->label('Alamat')->placeholder('-')->columnSpanFull(),
                TextEntry::make('pic')->label('PIC')->placeholder('-'),
                TextEntry::make('telepon')->label('Telepon')->placeholder('-'),
                TextEntry::make('term_of_payment')->label('Term of Payment')->placeholder('-'),
                TextEntry::make('term_of_delivery')->label('Term of Delivery')->placeholder('-'),
                TextEntry::make('delivery_time')->label('Delivery Time')->placeholder('-'),
                TextEntry::make('penawaran.no_penawaran')->label('Penawaran Sumber')->placeholder('-'),
                TextEntry::make('user.nama')->label('Dibuat oleh')->placeholder('-'),
                TextEntry::make('catatan')->label('Catatan')->placeholder('-')->columnSpanFull(),
            ])->columnSpanFull(),
            Section::make('Detail Item')->contained()->schema([
                RepeatableEntry::make('po_items')->label('')
                    ->schema([
                        TextEntry::make('kode_barang')->label('Kode')->placeholder('-'),
                        TextEntry::make('nama_barang')->label('Nama Barang'),
                        TextEntry::make('deskripsi')->label('Deskripsi')->placeholder('-'),
                        TextEntry::make('satuan')->label('Sat'),
                        TextEntry::make('qty')->label('Qty')->formatStateUsing(fn ($state): string => number_format((float) $state, 0, ',', '.')),
                        TextEntry::make('harga_satuan')->label('Harga')->formatStateUsing(fn ($state, $record): string => ($record->purchase_order?->currency ?: 'Rp') . ' ' . number_format((float) $state, 0, ',', '.')),
                        TextEntry::make('subtotal')->label('Subtotal')->formatStateUsing(fn ($state, $record): string => ($record->purchase_order?->currency ?: 'Rp') . ' ' . number_format((float) $state, 0, ',', '.')),
                    ])->columns(2),
            ])->columnSpanFull(),
            Section::make('Ringkasan Harga')->contained()->columns(['default' => 1, 'sm' => 2])->schema([
                TextEntry::make('subtotal')->label('Subtotal')->formatStateUsing(fn ($state, PurchaseOrder $record): string => ($record->currency ?: 'Rp') . ' ' . number_format((float) $state, 0, ',', '.')),
                TextEntry::make('ppn_persen')->label('PPN (%)')->suffix('%')
                    ->visible(fn (PurchaseOrder $record): bool => $record->kategori_po !== 'Luar Negeri' && (float) $record->ppn_persen > 0),
                TextEntry::make('ppn_nominal')->label('PPN')->formatStateUsing(fn ($state, PurchaseOrder $record): string => ($record->currency ?: 'Rp') . ' ' . number_format((float) $state, 0, ',', '.'))
                    ->visible(fn (PurchaseOrder $record): bool => $record->kategori_po !== 'Luar Negeri' && (float) $record->ppn_nominal > 0),
                TextEntry::make('total')->label('Total')->formatStateUsing(fn ($state, PurchaseOrder $record): string => ($record->currency ?: 'Rp') . ' ' . number_format((float) $state, 0, ',', '.'))->weight('bold'),
            ])->columnSpanFull(),
            Section::make('Lampiran Dokumen')->contained()->schema([
                RepeatableEntry::make('po_attachments')->label('')
                    ->schema([
                        TextEntry::make('file_name')->label('Nama File')
                            ->icon('heroicon-o-document-arrow-down')
                            ->belowContent([self::attachmentPreviewAction()]),
                    ]),
            ])->columnSpanFull()->visible(fn (PurchaseOrder $record): bool => $record->po_attachments()->exists()),
        ]);
    }
}
