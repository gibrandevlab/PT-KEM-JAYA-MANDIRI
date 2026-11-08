<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Penawaran;
use App\Models\PurchaseOrder;
use App\Models\StokLog;
use App\Support\XlsxExport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController
{
    public function __invoke(Request $request): StreamedResponse
    {
        abort_unless($request->user()?->hasAccess('reports', 'view'), 403);

        $type = $request->query('jenis', 'penjualan');
        abort_unless(in_array($type, ['penjualan', 'penawaran', 'pembelian', 'stok'], true), 422);
        $year = max(2000, min(2100, (int) $request->query('tahun', now()->year)));
        [$from, $to] = $this->period($request, $year);
        $search = trim((string) $request->query('q', ''));

        if ($type === 'penjualan') {
            $query = Invoice::query()->with('surat_jalan')->whereDate('tanggal', '>=', $from)->whereDate('tanggal', '<=', $to);
            if ($search !== '') {
                $query->where(fn (Builder $builder) => $builder->where('no_invoice', 'like', "%{$search}%")
                    ->orWhere('nama_customer', 'like', "%{$search}%")
                    ->orWhereHas('surat_jalan', fn (Builder $sj) => $sj->where('no_sj', 'like', "%{$search}%")));
            }
            $headers = ['Tanggal', 'No. Invoice', 'Customer', 'No. SJ', 'Status Pembayaran', 'Total'];
            $rows = $query->orderBy('tanggal')->get()->map(fn (Invoice $invoice): array => [$invoice->tanggal?->format('Y-m-d'), $invoice->no_invoice, $invoice->nama_customer, $invoice->surat_jalan?->no_sj, $invoice->status_payment, $invoice->total]);
        } elseif ($type === 'penawaran') {
            $query = Penawaran::query()->whereDate('tanggal', '>=', $from)->whereDate('tanggal', '<=', $to);
            if ($search !== '') {
                $query->where(fn (Builder $builder) => $builder->where('no_penawaran', 'like', "%{$search}%")
                    ->orWhere('nama_customer', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%"));
            }
            $headers = ['Tanggal', 'No. Penawaran', 'Customer', 'Perihal', 'Status', 'Total'];
            $rows = $query->orderBy('tanggal')->get()->map(fn (Penawaran $offer): array => [$offer->tanggal?->format('Y-m-d'), $offer->no_penawaran, $offer->nama_customer, $offer->perihal, $offer->status, $offer->total]);
        } elseif ($type === 'pembelian') {
            $query = PurchaseOrder::query()->where('jenis', 'ke_vendor')->whereDate('tanggal', '>=', $from)->whereDate('tanggal', '<=', $to);
            if ($search !== '') {
                $query->where(fn (Builder $builder) => $builder->where('no_po', 'like', "%{$search}%")->orWhere('nama_perusahaan', 'like', "%{$search}%"));
            }
            $headers = ['Tanggal', 'No. PO', 'Vendor', 'Total'];
            $rows = $query->orderBy('tanggal')->get()->map(fn (PurchaseOrder $po): array => [$po->tanggal?->format('Y-m-d'), $po->no_po, $po->nama_perusahaan, $po->total]);
        } else {
            $query = StokLog::query()->with(['barang', 'user'])->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            if ($search !== '') {
                $query->where(fn (Builder $builder) => $builder->where('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('barang', fn (Builder $barang) => $barang->where('kode_barang', 'like', "%{$search}%")->orWhere('nama_barang', 'like', "%{$search}%")));
            }
            $headers = ['Tanggal', 'Kode Barang', 'Nama Barang', 'Jenis', 'Qty', 'Stok Sebelum', 'Stok Sesudah', 'Keterangan', 'User'];
            $rows = $query->orderBy('created_at')->get()->map(fn (StokLog $log): array => [$log->created_at?->format('Y-m-d H:i:s'), $log->barang?->kode_barang, $log->barang?->nama_barang, $log->jenis, $log->qty, $log->stok_sebelum, $log->stok_sesudah, $log->keterangan, $log->user?->nama ?? 'Sistem']);
        }

        return XlsxExport::download('laporan_' . $type . '_' . $from . '_' . $to . '.xlsx', $headers, $rows);
    }

    /** @return array{string, string} */
    private function period(Request $request, int $year): array
    {
        $start = Carbon::create($year, 1, 1)->toDateString();
        $end = Carbon::create($year, 12, 31)->toDateString();
        $mode = $request->query('mode', 'tahun');

        if ($mode === 'bulan') {
            $month = max(1, min(12, (int) $request->query('bulan', now()->month)));
            $date = Carbon::create($year, $month, 1);
            return [$date->copy()->startOfMonth()->toDateString(), $date->copy()->endOfMonth()->toDateString()];
        }

        if ($mode === 'bulan_range') {
            $fromMonth = max(1, min(12, (int) $request->query('bulan_dari', 1)));
            $toMonth = max(1, min(12, (int) $request->query('bulan_sampai', 12)));
            if ($fromMonth > $toMonth) {
                [$fromMonth, $toMonth] = [$toMonth, $fromMonth];
            }
            return [Carbon::create($year, $fromMonth, 1)->startOfMonth()->toDateString(), Carbon::create($year, $toMonth, 1)->endOfMonth()->toDateString()];
        }

        if ($mode === 'rentang') {
            $start = $this->date($request->query('dari')) ?? $start;
            $end = $this->date($request->query('sampai')) ?? $end;
            if ($start > $end) {
                [$start, $end] = [$end, $start];
            }
        }

        return [$start, $end];
    }

    private function date(mixed $value): ?string
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
