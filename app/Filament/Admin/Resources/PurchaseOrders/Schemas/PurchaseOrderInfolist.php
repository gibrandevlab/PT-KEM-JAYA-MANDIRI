<?php

namespace App\Filament\Admin\Resources\PurchaseOrders\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('no_po'),
                TextEntry::make('no_po_customer')
                    ->placeholder('-'),
                TextEntry::make('jenis')
                    ->badge(),
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('penawaran_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('customer_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('po_customer_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('vendor_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('nama_perusahaan'),
                TextEntry::make('term_of_delivery')
                    ->placeholder('-'),
                TextEntry::make('delivery_time')
                    ->placeholder('-'),
                TextEntry::make('term_of_payment')
                    ->placeholder('-'),
                TextEntry::make('alamat')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('telepon')
                    ->placeholder('-'),
                TextEntry::make('tanggal_kirim')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('catatan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('subtotal')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('ppn_persen')
                    ->numeric(),
                TextEntry::make('ppn_nominal')
                    ->numeric()
                    ->placeholder('-'),
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
                TextEntry::make('kategori_po')
                    ->placeholder('-'),
                TextEntry::make('pic')
                    ->placeholder('-'),
                TextEntry::make('currency')
                    ->placeholder('-'),
                TextEntry::make('diskon_persen')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('diskon_nominal')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_history')
                    ->boolean()
                    ->placeholder('-'),
            ]);
    }
}
