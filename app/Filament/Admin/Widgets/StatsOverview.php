<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Barangs\BarangResource;
use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Admin\Resources\SuratJalans\SuratJalanResource;
use App\Models\Barang;
use App\Models\Invoice;
use App\Models\Penawaran;
use App\Models\PurchaseOrder;
use App\Models\SuratJalan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $curMonth = $now->month;
        $curYear = $now->year;
        $isAdmin = Auth::user()?->role === 'admin';

        $totalPenawaran = Penawaran::count();
        $totalPO = PurchaseOrder::whereMonth('tanggal', $curMonth)->whereYear('tanggal', $curYear)->count();
        $totalSJ = SuratJalan::whereMonth('tanggal', $curMonth)->whereYear('tanggal', $curYear)->count();
        $totalBarang = Barang::count();

        $stats = [
            Stat::make('Penawaran Bulan Ini', number_format($totalPenawaran))
                ->description('Total katalog penawaran')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([3, 5, 2, 8, 6, 9, max(1, $totalPenawaran)])
                ->url(PenawaranResource::getUrl()),

            Stat::make('Pesanan Pembelian Bulan Ini', number_format($totalPO))
                ->description('Pesanan pembelian masuk')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning')
                ->chart([2, 4, 3, 6, 5, 7, max(1, $totalPO)])
                ->url(PurchaseOrderResource::getUrl()),

            Stat::make('Surat Jalan Bulan Ini', number_format($totalSJ))
                ->description('Pengiriman aktif')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success')
                ->chart([1, 3, 4, 2, 5, 4, max(1, $totalSJ)])
                ->url(SuratJalanResource::getUrl()),
        ];

        if ($isAdmin) {
            $totalInvoice = Invoice::whereMonth('tanggal', $curMonth)->whereYear('tanggal', $curYear)->count();
            $invoiceBelumBayar = Invoice::where('status_payment', 'Unpaid')->count();

            $stats[] = Stat::make('Faktur Bulan Ini', number_format($totalInvoice))
                ->description('Tagihan terbit')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('primary')
                ->chart([2, 5, 3, 6, 7, 5, max(1, $totalInvoice)])
                ->url(InvoiceResource::getUrl());

            $stats[] = Stat::make('Faktur Belum Dibayar', number_format($invoiceBelumBayar))
                ->description('Status pembayaran belum lunas')
                ->descriptionIcon('heroicon-m-clock')
                ->color($invoiceBelumBayar > 0 ? 'danger' : 'success')
                ->chart([1, 2, 4, 3, 5, 2, max(1, $invoiceBelumBayar)])
                ->url(InvoiceResource::getUrl());
        }

        $stats[] = Stat::make('Total Item Barang', number_format($totalBarang))
            ->description('Data induk barang')
            ->descriptionIcon('heroicon-m-archive-box')
            ->color('gray')
            ->chart([50, 80, 120, 200, 500, 1000, $totalBarang])
            ->url(BarangResource::getUrl());

        return $stats;
    }
}
