<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\StokLog;
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
        abort_unless(in_array($type, ['penjualan', 'pembelian', 'stok'], true), 422);
        $year = max(2000, min(2100, (int) $request->query('tahun', now()->year)));
        $mode = $request->query('mode', 'tahun');
        $from = Carbon::create($year, 1, 1)->toDateString();
        $to = Carbon::create($year, 12, 31)->toDateString();

        if ($mode === 'bulan_range') {
            $start = max(1, min(12, (int) $request->query('bulan_dari', 1)));
            $end = max(1, min(12, (int) $request->query('bulan_sampai', 12)));
            if ($start > $end) [$start, $end] = [$end, $start];
            $from = Carbon::create($year, $start, 1)->startOfMonth()->toDateString();
            $to = Carbon::create($year, $end, 1)->endOfMonth()->toDateString();
        } elseif ($mode === 'rentang') {
            $from = $this->date($request->query('dari')) ?? $from;
            $to = $this->date($request->query('sampai')) ?? $to;
            if ($from > $to) [$from, $to] = [$to, $from];
        }

        $search = trim((string) $request->query('q', ''));
        if ($type === 'penjualan') {
            $query = Invoice::query()->with('surat_jalan')->where('is_history', false)->whereBetween('tanggal', [$from, $to]);
            if ($search !== '') $query->where(fn (Builder $q) => $q->where('no_invoice', 'like', "%{$search}%")->orWhere('nama_customer', 'like', "%{$search}%")->orWhereHas('surat_jalan', fn (Builder $sj) => $sj->where('no_sj', 'like', "%{$search}%")));
            $headers = ['Tanggal', 'No. Invoice', 'Customer', 'No. SJ', 'Status Pembayaran', 'Total'];
            $rows = $query->orderBy('tanggal')->get()->map(fn (Invoice $i) => [$i->tanggal?->format('Y-m-d'), $i->no_invoice, $i->nama_customer, $i->surat_jalan?->no_sj, $i->status_payment, $i->total]);
        } elseif ($type === 'pembelian') {
            $query = PurchaseOrder::query()->where('jenis', 'ke_vendor')->where('is_history', false)->whereBetween('tanggal', [$from, $to]);
            if ($search !== '') $query->where(fn (Builder $q) => $q->where('no_po', 'like', "%{$search}%")->orWhere('nama_perusahaan', 'like', "%{$search}%"));
            $headers = ['Tanggal', 'No. PO', 'Vendor', 'Total'];
            $rows = $query->orderBy('tanggal')->get()->map(fn (PurchaseOrder $po) => [$po->tanggal?->format('Y-m-d'), $po->no_po, $po->nama_perusahaan, $po->total]);
        } else {
            $query = StokLog::query()->with(['barang', 'user'])->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            if ($search !== '') $query->where(fn (Builder $q) => $q->where('keterangan', 'like', "%{$search}%")->orWhereHas('barang', fn (Builder $b) => $b->where('kode_barang', 'like', "%{$search}%")->orWhere('nama_barang', 'like', "%{$search}%")));
            $headers = ['Tanggal', 'Kode Barang', 'Nama Barang', 'Jenis', 'Qty', 'Stok Sebelum', 'Stok Sesudah', 'Keterangan', 'User'];
            $rows = $query->orderBy('created_at')->get()->map(fn (StokLog $log) => [$log->created_at?->format('Y-m-d H:i:s'), $log->barang?->kode_barang, $log->barang?->nama_barang, $log->jenis, $log->qty, $log->stok_sebelum, $log->stok_sesudah, $log->keterangan, $log->user?->nama ?? 'Sistem']);
        }

        $filename = 'laporan_' . $type . '_' . $from . '_' . $to . '.xls';
        return response()->streamDownload(function () use ($headers, $rows): void {
            $cell = static function (mixed $value): string {
                $value = (string) ($value ?? '');
                $type = is_numeric($value) && $value !== '' ? 'Number' : 'String';
                return '<Cell><Data ss:Type="' . $type . '">' . htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</Data></Cell>';
            };
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<?mso-application progid="Excel.Sheet"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Worksheet ss:Name="Laporan"><Table><Row>';
            foreach ($headers as $header) echo $cell($header);
            echo '</Row>';
            foreach ($rows as $row) {
                echo '<Row>';
                foreach ($row as $value) echo $cell($value);
                echo '</Row>';
            }
            echo '</Table></Worksheet></Workbook>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }

    private function date(mixed $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return null;
        try { return Carbon::createFromFormat('!Y-m-d', $value)->toDateString(); } catch (\Throwable) { return null; }
    }
}
