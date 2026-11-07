<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Barangs\BarangResource;
use App\Models\Barang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalBarang = Barang::count();
        $totalLowStock = Barang::whereColumn('stok', '<=', 'stok_minimum')
            ->where('stok_minimum', '>', 0)
            ->count();
        $totalStokHabis = Barang::where('stok', '<=', 0)->count();

        return [
            Stat::make('Total Item Gudang', number_format($totalBarang))
                ->description('Semua jenis barang terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info')
                ->url(BarangResource::getUrl()),

            Stat::make('Stok Menipis', number_format($totalLowStock))
                ->description($totalLowStock > 0 ? 'Mendekati / di bawah minimum' : 'Semua stok dalam batas aman')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalLowStock > 0 ? 'warning' : 'success')
                ->url(BarangResource::getUrl()),

            Stat::make('Stok Habis (Kosong)', number_format($totalStokHabis))
                ->description($totalStokHabis > 0 ? 'Kritis: segera lakukan restock!' : 'Tidak ada barang kosong')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($totalStokHabis > 0 ? 'danger' : 'success')
                ->url(BarangResource::getUrl()),
        ];
    }
}
