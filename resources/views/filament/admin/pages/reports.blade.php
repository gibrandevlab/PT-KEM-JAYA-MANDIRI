<x-filament-panels::page>
    <style>
        .report-page{display:grid;gap:1.25rem}.report-card{padding:1.25rem!important;border-radius:.75rem;background:#fff;box-shadow:0 1px 2px #0000000d}.dark .report-card{background:#111827}.report-toolbar{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1rem}.report-field{display:grid;gap:.45rem;font-size:.875rem;font-weight:600}.report-field input,.report-field select{width:100%;min-height:2.5rem;border:1px solid #d1d5db;border-radius:.5rem;padding:.5rem .75rem;background:#fff;color:#111827}.dark .report-field input,.dark .report-field select{background:#030712;border-color:#4b5563;color:#f9fafb}.report-actions{display:flex;flex-wrap:wrap;align-items:center;gap:.65rem;margin-top:1.25rem}.report-period{color:#6b7280;font-size:.875rem}.report-stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:.75rem;margin:1rem 0}.report-stat{border:1px solid #e5e7eb;border-radius:.75rem;padding:1rem;background:#f9fafb}.dark .report-stat{border-color:#374151;background:#1f2937}.report-stat-label{font-size:.8rem;color:#6b7280}.report-stat-value{font-size:1.15rem;font-weight:700;margin-top:.3rem}.dark .report-stat-label{color:#9ca3af}.report-scroll{overflow-x:auto}.report-table{width:100%;border-collapse:collapse;font-size:.875rem}.report-table th,.report-table td{padding:.8rem .75rem;border-bottom:1px solid #e5e7eb;text-align:left;white-space:nowrap}.dark .report-table th,.dark .report-table td{border-color:#374151}.report-table th{font-size:.72rem;letter-spacing:.04em;text-transform:uppercase;color:#6b7280;background:#f9fafb}.dark .report-table th{color:#9ca3af;background:#1f2937}.report-table tbody tr:hover{background:#f9fafb}.dark .report-table tbody tr:hover{background:#1f2937}.report-table .numeric{text-align:right}.report-empty{text-align:center!important;padding:3rem 1rem!important;color:#6b7280;white-space:normal!important}.report-badge{display:inline-flex;border-radius:999px;padding:.25rem .6rem;background:#f3f4f6;color:#374151;font-size:.75rem;font-weight:600}.report-badge-paid{background:#dcfce7;color:#166534}.report-badge-unpaid{background:#fee2e2;color:#991b1b}.dark .report-badge{background:#374151;color:#e5e7eb}@media print{.report-no-print,.fi-sidebar,.fi-topbar{display:none!important}.fi-main{padding:0!important}.report-page{display:block}.fi-section{box-shadow:none!important;border:0!important}.report-stat{background:white!important}}
    </style>

    <div class="report-page">
    <form method="GET" action="{{ \App\Filament\Admin\Pages\ReportsPage::getUrl() }}" class="report-no-print report-card fi-section">
        <div class="report-toolbar">
            <label class="report-field">Jenis Laporan
                <select name="jenis">
                    <option value="penjualan" @selected($jenis === 'penjualan')>Laporan Penjualan (Faktur)</option>
                    <option value="pembelian" @selected($jenis === 'pembelian')>Laporan Pembelian (Pesanan Pembelian Pemasok)</option>
                    <option value="stok" @selected($jenis === 'stok')>Laporan Pergerakan Stok</option>
                </select>
            </label>
            <label class="report-field">Periode
                <select name="mode" id="report-mode">
                    <option value="tahun" @selected($mode === 'tahun')>Per tahun</option>
                    <option value="bulan_range" @selected(in_array($mode, ['bulan', 'bulan_range'], true))>Rentang bulan</option>
                    <option value="rentang" @selected($mode === 'rentang')>Rentang tanggal</option>
                </select>
            </label>
            <label class="report-year report-field">Tahun
                <select name="tahun">
                    @foreach(array_reverse($years) as $yearOption)<option value="{{ $yearOption }}" @selected($tahun === $yearOption)>{{ $yearOption }}</option>@endforeach
                </select>
            </label>
            <label class="report-month report-field">Dari bulan
                <select name="bulan_dari">@foreach($months as $monthNumber => $monthName)<option value="{{ $monthNumber }}" @selected((int)request('bulan_dari', 1) === $monthNumber)>{{ $monthName }}</option>@endforeach</select>
            </label>
            <label class="report-month report-field">Sampai bulan
                <select name="bulan_sampai">@foreach($months as $monthNumber => $monthName)<option value="{{ $monthNumber }}" @selected((int)request('bulan_sampai', 12) === $monthNumber)>{{ $monthName }}</option>@endforeach</select>
            </label>
            <label class="report-range report-field">Dari tanggal
                <input type="date" name="dari" value="{{ $mode === 'rentang' ? $dari : '' }}">
            </label>
            <label class="report-range report-field">Sampai tanggal
                <input type="date" name="sampai" value="{{ $mode === 'rentang' ? $sampai : '' }}">
            </label>
            <label class="report-field">Cari data
                <input type="search" name="q" value="{{ $search }}" placeholder="Nomor dokumen, nama, atau keterangan...">
            </label>
        </div>
        <div class="report-actions">
            <button type="submit" class="fi-btn fi-btn-color-primary">Tampilkan</button>
            <a href="{{ $exportUrl }}" class="fi-btn fi-btn-color-gray"><x-filament::icon icon="heroicon-o-arrow-down-tray" class="h-4 w-4" /> Ekspor Excel</a>
            <button type="button" onclick="window.print()" class="fi-btn fi-btn-color-gray"><x-filament::icon icon="heroicon-o-printer" class="h-4 w-4" /> Cetak</button>
        </div>
    </form>

    <section class="fi-section report-card rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6" id="report-content">
        <div class="mb-4 flex flex-wrap items-start justify-between gap-3 border-b border-gray-200 pb-4 dark:border-white/10">
            <div>
                <h2 class="text-xl font-bold">{{ ['penjualan' => 'Laporan Penjualan', 'pembelian' => 'Laporan Pembelian Pemasok', 'stok' => 'Laporan Pergerakan Stok'][$jenis] }}</h2>
                <p class="mt-1 text-sm text-gray-500">Periode {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
            </div>
            <div class="text-sm text-gray-500">{{ number_format($count) }} data</div>
        </div>

        @if($jenis === 'penjualan')
            <div class="report-stat-grid">
                <div class="report-stat"><div class="report-stat-label">Nilai faktur</div><div class="report-stat-value">Rp {{ number_format($total, 0, ',', '.') }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Sudah dibayar</div><div class="report-stat-value">Rp {{ number_format($paidTotal, 0, ',', '.') }}</div></div>
                <div class="report-stat"><div class="report-stat-label">Jumlah faktur</div><div class="report-stat-value">{{ number_format($count) }}</div></div>
            </div>
            <div class="report-scroll"><table class="report-table">
                <thead><tr class="border-b text-xs uppercase text-gray-500"><th class="p-3">Tanggal</th><th class="p-3">Nomor Faktur</th><th class="p-3">Pelanggan</th><th class="p-3">Nomor SJ</th><th class="p-3">Status Pembayaran</th><th class="p-3 text-right">Total</th><th class="p-3">Rincian</th></tr></thead>
                <tbody>
                @forelse($rows as $invoice)
                    <tr class="border-b border-gray-100 dark:border-white/5"><td class="p-3">{{ $invoice->tanggal?->format('d/m/Y') }}</td><td class="p-3 font-semibold">{{ $invoice->no_invoice }}</td><td class="p-3">{{ $invoice->nama_customer }}</td><td class="p-3">{{ $invoice->surat_jalan?->no_sj ?: '-' }}</td><td class="p-3"><span class="report-badge report-badge-{{ strtolower($invoice->status_payment ?: 'unpaid') }}">{{ $invoice->status_payment === 'Paid' ? 'Lunas' : ($invoice->status_payment === 'Unpaid' ? 'Belum Dibayar' : 'Belum Dibayar') }}</span></td><td class="p-3 text-right">Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}</td><td class="p-3"><a title="Lihat rincian faktur" href="{{ \App\Filament\Admin\Resources\Invoices\InvoiceResource::getUrl('view', ['record' => $invoice]) }}"><x-filament::icon icon="heroicon-o-eye" class="h-5 w-5" /></a></td></tr>
                @empty<tr><td colspan="7" class="p-10 text-center text-gray-500">Tidak ada faktur pada periode ini.</td></tr>@endforelse
                </tbody>
            </table></div>
        @elseif($jenis === 'pembelian')
            <div class="report-stat-grid"><div class="report-stat"><div class="report-stat-label">Total pembelian</div><div class="report-stat-value">Rp {{ number_format($total, 0, ',', '.') }}</div></div><div class="report-stat"><div class="report-stat-label">Jumlah PO vendor</div><div class="report-stat-value">{{ number_format($count) }}</div></div></div>
            <div class="report-scroll"><table class="report-table">
                <thead><tr class="border-b text-xs uppercase text-gray-500"><th class="p-3">Tanggal</th><th class="p-3">Nomor PO</th><th class="p-3">Pemasok</th><th class="p-3 text-right">Total</th><th class="p-3">Rincian</th></tr></thead>
                <tbody>
                @forelse($rows as $purchaseOrder)
                    <tr class="border-b border-gray-100 dark:border-white/5"><td class="p-3">{{ $purchaseOrder->tanggal?->format('d/m/Y') }}</td><td class="p-3 font-semibold">{{ $purchaseOrder->no_po }}</td><td class="p-3">{{ $purchaseOrder->nama_perusahaan }}</td><td class="p-3 text-right">Rp {{ number_format((float) $purchaseOrder->total, 0, ',', '.') }}</td><td class="p-3"><a title="Lihat detail PO" href="{{ \App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource::getUrl('view', ['record' => $purchaseOrder]) }}"><x-filament::icon icon="heroicon-o-eye" class="h-5 w-5" /></a></td></tr>
                @empty<tr><td colspan="5" class="p-10 text-center text-gray-500">Tidak ada PO vendor pada periode ini.</td></tr>@endforelse
                </tbody>
            </table></div>
        @else
            <div class="report-scroll"><table class="report-table">
                <thead><tr class="border-b text-xs uppercase text-gray-500"><th class="p-3">Tanggal & Waktu</th><th class="p-3">Kode</th><th class="p-3">Nama Barang</th><th class="p-3">Jenis</th><th class="p-3 text-right">Jumlah</th><th class="p-3 text-right">Stok Sebelum</th><th class="p-3 text-right">Stok Sesudah</th><th class="p-3">Keterangan</th><th class="p-3">Pengguna</th><th class="p-3">Rincian</th></tr></thead>
                <tbody>
                @forelse($rows as $log)
                    <tr class="border-b border-gray-100 dark:border-white/5"><td class="p-3">{{ $log->created_at?->format('d/m/Y H:i') }}</td><td class="p-3">{{ $log->barang?->kode_barang }}</td><td class="p-3">{{ $log->barang?->nama_barang }}</td><td class="p-3">{{ ucfirst($log->jenis) }}</td><td class="p-3 text-right">{{ number_format((float) $log->qty, 2, ',', '.') }}</td><td class="p-3 text-right">{{ number_format((float) $log->stok_sebelum, 2, ',', '.') }}</td><td class="p-3 text-right">{{ number_format((float) $log->stok_sesudah, 2, ',', '.') }}</td><td class="p-3">{{ $log->keterangan ?: '-' }}</td><td class="p-3">{{ $log->user?->nama ?: 'Sistem' }}</td><td class="p-3"><a title="Lihat detail log" href="{{ \App\Filament\Admin\Resources\StokLogs\StokLogResource::getUrl('view', ['record' => $log]) }}"><x-filament::icon icon="heroicon-o-eye" class="h-5 w-5" /></a></td></tr>
                @empty<tr><td colspan="10" class="p-10 text-center text-gray-500">Tidak ada pergerakan stok pada periode ini.</td></tr>@endforelse
                </tbody>
            </table></div>
        @endif

        @if(method_exists($rows, 'links'))<div class="report-no-print mt-4">{{ $rows->links() }}</div>@endif
    </section>
    </div>

    <script>
        (() => {
            const mode = document.getElementById('report-mode');
            const update = () => {
                document.querySelectorAll('.report-month').forEach(el => el.style.display = mode.value === 'bulan_range' ? '' : 'none');
                document.querySelectorAll('.report-range').forEach(el => el.style.display = mode.value === 'rentang' ? '' : 'none');
                document.querySelectorAll('.report-year').forEach(el => el.style.display = mode.value === 'rentang' ? 'none' : '');
            };
            mode.addEventListener('change', update); update();
        })();
    </script>
</x-filament-panels::page>
