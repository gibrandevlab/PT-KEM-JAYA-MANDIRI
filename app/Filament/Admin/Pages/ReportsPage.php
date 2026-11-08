<?php

namespace App\Filament\Admin\Pages;

use App\Models\Invoice;
use App\Models\Penawaran;
use App\Models\PurchaseOrder;
use App\Models\StokLog;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;

class ReportsPage extends Page
{
    protected string $view = 'filament.admin.pages.reports';
    protected static ?string $slug = 'reports';
    protected static ?string $title = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan';
    protected static bool $shouldRegisterNavigation = false;
    protected Width | string | null $maxContentWidth = Width::Full;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAccess('reports', 'view') ?? false;
    }

    public function getViewData(): array
    {
        $jenis = request()->query('jenis', 'penjualan');
        if (! in_array($jenis, ['penjualan', 'penawaran', 'pembelian', 'stok'], true)) $jenis = 'penjualan';

        $mode = request()->query('mode', 'tahun');
        if (! in_array($mode, ['tahun', 'bulan', 'rentang', 'bulan_range'], true)) $mode = 'tahun';

        $year = (int) request()->query('tahun', now()->year);
        if ($year < 2000 || $year > 2100) $year = now()->year;
        $month = (int) request()->query('bulan', now()->month);
        if ($month < 1 || $month > 12) $month = now()->month;

        [$from, $to] = $this->period($mode, $year, $month);
        $search = trim((string) request()->query('q', ''));
        $rows = collect();
        $total = 0.0;
        $paidTotal = 0.0;
        $count = 0;

        if ($jenis === 'penjualan') {
            $query = Invoice::query()->with('surat_jalan')->whereDate('tanggal', '>=', $from)->whereDate('tanggal', '<=', $to);
            if ($search !== '') {
                $query->where(function (Builder $builder) use ($search): void {
                    $builder->where('no_invoice', 'like', "%{$search}%")
                        ->orWhere('nama_customer', 'like', "%{$search}%")
                        ->orWhereHas('surat_jalan', fn (Builder $sj) => $sj->where('no_sj', 'like', "%{$search}%"));
                });
            }
            $count = (clone $query)->count();
            $total = (float) (clone $query)->sum('total');
            $paidTotal = (float) (clone $query)->where('status_payment', 'Paid')->sum('total');
            $rows = $query->orderBy('tanggal')->orderBy('id')->paginate(50)->withQueryString();
        } elseif ($jenis === 'penawaran') {
            $query = Penawaran::query()->whereDate('tanggal', '>=', $from)->whereDate('tanggal', '<=', $to);
            if ($search !== '') {
                $query->where(fn (Builder $builder) => $builder->where('no_penawaran', 'like', "%{$search}%")
                    ->orWhere('nama_customer', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%"));
            }
            $count = (clone $query)->count();
            $total = (float) (clone $query)->sum('total');
            $rows = $query->orderBy('tanggal')->orderBy('id')->paginate(50)->withQueryString();
        } elseif ($jenis === 'pembelian') {
            $query = PurchaseOrder::query()->where('jenis', 'ke_vendor')->whereDate('tanggal', '>=', $from)->whereDate('tanggal', '<=', $to);
            if ($search !== '') $query->where(fn (Builder $builder) => $builder->where('no_po', 'like', "%{$search}%")->orWhere('nama_perusahaan', 'like', "%{$search}%"));
            $count = (clone $query)->count();
            $total = (float) (clone $query)->sum('total');
            $rows = $query->orderBy('tanggal')->orderBy('id')->paginate(50)->withQueryString();
        } else {
            $query = StokLog::query()->with(['barang', 'user'])->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            if ($search !== '') {
                $query->where(function (Builder $builder) use ($search): void {
                $builder->where('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('barang', fn (Builder $barang) => $barang->where('kode_barang', 'like', "%{$search}%")->orWhere('nama_barang', 'like', "%{$search}%"));
                });
            }
            $count = (clone $query)->count();
            $rows = $query->orderByDesc('created_at')->orderByDesc('id')->paginate(50)->withQueryString();
        }

        $exportUrl = route('admin.reports.export', request()->query());
        $printUrl = route('admin.reports.print', request()->query());
        return [
            'jenis' => $jenis,
            'mode' => $mode,
            'tahun' => $year,
            'bulan' => $month,
            'dari' => $from,
            'sampai' => $to,
            'search' => $search,
            'rows' => $rows,
            'count' => $count,
            'total' => $total,
            'paidTotal' => $paidTotal,
            'exportUrl' => $exportUrl,
            'printUrl' => $printUrl,
            'years' => range(2020, now()->year + 1),
            'months' => [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'],
        ];
    }

    private function period(string $mode, int $year, int $month): array
    {
        if ($mode === 'bulan') {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            return [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()];
        }

        if ($mode === 'bulan_range') {
            $fromMonth = max(1, min(12, (int) request()->query('bulan_dari', 1)));
            $toMonth = max(1, min(12, (int) request()->query('bulan_sampai', 12)));
            if ($fromMonth > $toMonth) [$fromMonth, $toMonth] = [$toMonth, $fromMonth];
            return [Carbon::create($year, $fromMonth, 1)->startOfMonth()->toDateString(), Carbon::create($year, $toMonth, 1)->endOfMonth()->toDateString()];
        }

        if ($mode === 'rentang') {
            $from = $this->validDate(request()->query('dari')) ?: Carbon::create($year, 1, 1)->toDateString();
            $to = $this->validDate(request()->query('sampai')) ?: Carbon::create($year, 12, 31)->toDateString();
            if ($from > $to) [$from, $to] = [$to, $from];
            return [$from, $to];
        }

        return [Carbon::create($year, 1, 1)->toDateString(), Carbon::create($year, 12, 31)->toDateString()];
    }

    private function validDate(mixed $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        try {
            return Carbon::createFromFormat('!Y-m-d', $value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
