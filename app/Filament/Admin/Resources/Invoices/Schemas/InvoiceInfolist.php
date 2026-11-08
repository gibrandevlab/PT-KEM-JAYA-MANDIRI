<?php

namespace App\Filament\Admin\Resources\Invoices\Schemas;

use App\Filament\Admin\Resources\Concerns\HasAttachmentPreview;
use App\Models\Invoice;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    use HasAttachmentPreview;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                TextEntry::make('no_invoice'),
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('jatuh_tempo')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('sj_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('po_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('penawaran_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('customer_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('nama_customer'),
                TextEntry::make('alamat_customer')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('telepon_customer')
                    ->placeholder('-'),
                TextEntry::make('npwp')
                    ->placeholder('-'),
                TextEntry::make('no_faktur_pajak')
                    ->placeholder('-'),
                TextEntry::make('term_of_payment')
                    ->placeholder('-'),
                TextEntry::make('catatan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('bank_account_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('subtotal')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('diskon_persen')
                    ->numeric(),
                TextEntry::make('diskon_nominal')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('ppn_persen')
                    ->numeric(),
                TextEntry::make('ppn_nominal')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_dpp')
                    ->boolean(),
                TextEntry::make('dpp_nominal')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_revisi')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('revisi_ke')
                    ->placeholder('-'),
                TextEntry::make('tipe_invoice')
                    ->placeholder('-'),
                TextEntry::make('po_number')
                    ->placeholder('-'),
                TextEntry::make('tanggal_po')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('shipment_number')
                    ->placeholder('-'),
                TextEntry::make('dp_persen')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('dp_nominal')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_history')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('dp_ref_invoice_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status_payment')
                    ->placeholder('-'),
                TextEntry::make('tanggal_payment')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('tanggal_kirim_dokumen')
                    ->date()
                    ->placeholder('-'),
                RepeatableEntry::make('invoice_attachments')
                    ->label('Lampiran')
                    ->schema([
                        TextEntry::make('file_name')
                            ->label('Nama File')
                            ->icon('heroicon-o-document-arrow-down')
                            ->belowContent([self::attachmentPreviewAction()]),
                    ])
                    ->columnSpanFull()
                    ->visible(fn (Invoice $record): bool => $record->invoice_attachments()->exists()),
            ]);
    }
}
