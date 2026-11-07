<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Invoice;
use App\Filament\Admin\Pages\ReportsPage;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FinancialTrendChart extends ChartWidget
{
    protected string $view = 'filament.widgets.financial-trend-chart';
    protected static ?int $sort = 3;

    protected ?string $heading = 'Tren Keuangan (6 Bulan Terakhir)';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '6m';

    protected function getFilters(): ?array
    {
        return [
            '6m' => '6 Bulan Terakhir',
            '12m' => '12 Bulan Terakhir',
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function getDescription(): ?string
    {
        $curMonth = Carbon::now()->month;
        $curYear = Carbon::now()->year;

        $pendapatanBln = (float) Invoice::where('status_payment', 'Paid')
            ->whereMonth('tanggal', $curMonth)
            ->whereYear('tanggal', $curYear)
            ->sum('total');

        return 'Pendapatan Bulan Ini: Rp ' . number_format($pendapatanBln, 0, ',', '.');
    }

    protected function getData(): array
    {
        $monthsCount = $this->filter === '12m' ? 12 : 6;
        $months = [];
        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $time = Carbon::now()->subMonths($i);
            $key = $time->format('Y-m');
            $months[$key] = [
                'label' => $time->format('M Y'),
                'value' => 0.0,
            ];
        }

        $startDate = Carbon::now()->subMonths($monthsCount - 1)->startOfMonth();

        // Optimized single aggregate query - No N+1
        $trendData = Invoice::where('status_payment', 'Paid')
            ->where('tanggal', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as ym, SUM(total) as sum_total")
            ->groupBy('ym')
            ->pluck('sum_total', 'ym');

        foreach ($trendData as $ym => $val) {
            if (isset($months[$ym])) {
                $months[$ym]['value'] = (float) $val;
            }
        }

        $labels = array_column($months, 'label');
        $values = array_column($months, 'value');

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan (Rp)',
                    'data' => $values,
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#f97316',
                    'pointBorderColor' => '#ffffff',
                    'pointRadius' => 5,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getReportsUrl(): string
    {
        return ReportsPage::getUrl(['jenis' => 'penjualan', 'mode' => 'bulan', 'bulan' => now()->month, 'tahun' => now()->year]);
    }
}
