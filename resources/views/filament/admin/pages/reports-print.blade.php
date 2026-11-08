<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        @page{size:A4 {{ $jenis === 'stok' ? 'landscape' : 'portrait' }};margin:13mm 12mm}
        *{box-sizing:border-box}
        body{margin:0;background:#f3f4f6;color:#172033;font:10pt/1.4 Tahoma,Arial,sans-serif}
        .page{max-width:1100px;margin:24px auto;padding:26px;background:#fff;box-shadow:0 12px 35px rgb(15 23 42 / .13)}
        .toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:20px}
        .toolbar span{color:#475569;font-size:9pt}
        .print-button{border:0;border-radius:6px;background:#d97706;padding:10px 18px;color:#fff;font-weight:700;cursor:pointer}
        .company-header{width:100%;border-collapse:collapse;border-bottom:4px solid #d97706;margin-bottom:18px}
        .company-header td{padding:0 0 12px;vertical-align:middle}
        .company-header img{display:block;max-width:112px;max-height:76px;width:auto;height:auto}
        .company-details{text-align:right;color:#334155;font-size:9pt;line-height:1.45}
        .company-details strong{display:block;margin-bottom:4px;color:#12314a;font-size:17pt}
        .report-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin:0 0 16px}
        .report-heading h1{margin:0;color:#12314a;font-size:21pt;line-height:1.15}
        .report-heading p{margin:5px 0 0;color:#475569}
        .report-chip{display:inline-block;border-radius:999px;background:#fff0d5;padding:6px 11px;color:#9a4d00;font-size:8.5pt;font-weight:700;white-space:nowrap}
        .summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:0 0 16px}
        .summary-card{border:1px solid #d6e0e8;border-left:4px solid #d97706;border-radius:6px;background:#f5f8fb;padding:10px 12px}
        .summary-label{color:#526274;font-size:8pt;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
        .summary-value{margin-top:3px;color:#12314a;font-size:13pt;font-weight:800}
        .filters{margin:0 0 12px;color:#475569;font-size:8.5pt}
        .filters strong{color:#172033}
        .report-table{width:100%;border-collapse:collapse;font-size:8.4pt}
        .report-table th{background:#12314a;color:#fff;padding:8px 7px;text-align:left;font-size:7.5pt;text-transform:uppercase;letter-spacing:.035em}
        .report-table th:first-child{border-left:4px solid #d97706}
        .report-table td{border-bottom:1px solid #d8e0e7;padding:7px;vertical-align:top}
        .report-table tbody tr:nth-child(even){background:#f3f7fa}
        .report-table .right{text-align:right;white-space:nowrap}
        .report-table .center{text-align:center}
        .muted{color:#64748b}
        .badge{display:inline-block;border-radius:999px;padding:3px 7px;font-size:7.5pt;font-weight:700;white-space:nowrap}
        .badge-paid{background:#d1fae5;color:#065f46}
        .badge-unpaid{background:#fee2e2;color:#991b1b}
        .empty{text-align:center!important;padding:30px 10px!important;color:#64748b}
        .report-footer{display:flex;justify-content:space-between;gap:12px;margin-top:18px;padding-top:8px;border-top:1px solid #d6e0e8;color:#64748b;font-size:8pt}
        thead{display:table-header-group}
        tr{page-break-inside:avoid}
        @media(max-width:700px){.page{margin:0;padding:14px}.company-header,.company-header tbody,.company-header tr,.company-header td{display:block;width:100%}.company-details{text-align:left;margin-top:10px}.report-heading{display:block}.report-chip{margin-top:10px}.summary{grid-template-columns:1fr}.report-table{font-size:7.5pt}.report-table th,.report-table td{padding:5px 4px}}
        @media print{body{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}.page{max-width:none;margin:0;padding:0;box-shadow:none}.toolbar{display:none}.report-table{font-size:8pt}}
    </style>
</head>
<body>
    <main class="page">
        <div class="toolbar"><span>Periksa ringkasan laporan, lalu cetak atau pilih “Save as PDF”.</span><button type="button" class="print-button" onclick="window.print()">Cetak PDF</button></div>

        <table class="company-header"><tr>
            <td style="width:40%"><img src="{{ asset('Picture1.png') }}" alt="Logo PT. Kem Jaya Mandiri"></td>
            <td class="company-details"><strong>PT. KEM JAYA MANDIRI</strong>Graha Harapan Regency Blok I5 No.15<br>Babelan - Bekasi 17610<br>Telp. 021-56926318<br>E-mail: support@kemjaya.com</td>
        </tr></table>

        <header class="report-heading">
            <div>
                <h1>{{ $title }}</h1>
                <p>Periode {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</p>
            </div>
            <span class="report-chip">{{ number_format($count) }} data</span>
        </header>

        @if($jenis === 'penjualan')
            <section class="summary">
                <div class="summary-card"><div class="summary-label">Nilai faktur</div><div class="summary-value">Rp {{ number_format($total, 0, ',', '.') }}</div></div>
                <div class="summary-card"><div class="summary-label">Sudah dibayar</div><div class="summary-value">Rp {{ number_format($paidTotal, 0, ',', '.') }}</div></div>
                <div class="summary-card"><div class="summary-label">Jumlah faktur</div><div class="summary-value">{{ number_format($count) }}</div></div>
            </section>
        @elseif($jenis === 'penawaran')
            <section class="summary">
                <div class="summary-card"><div class="summary-label">Total penawaran</div><div class="summary-value">Rp {{ number_format($total, 0, ',', '.') }}</div></div>
                <div class="summary-card"><div class="summary-label">Jumlah penawaran</div><div class="summary-value">{{ number_format($count) }}</div></div>
            </section>
        @elseif($jenis === 'pembelian')
            <section class="summary">
                <div class="summary-card"><div class="summary-label">Total pembelian</div><div class="summary-value">Rp {{ number_format($total, 0, ',', '.') }}</div></div>
                <div class="summary-card"><div class="summary-label">Jumlah PO vendor</div><div class="summary-value">{{ number_format($count) }}</div></div>
            </section>
        @endif

        @if($search !== '')<p class="filters"><strong>Filter pencarian:</strong> {{ $search }}</p>@endif

        <table class="report-table">
            @if($jenis === 'penjualan')
                <thead><tr><th>Tanggal</th><th>Nomor Faktur</th><th>Pelanggan</th><th>Nomor SJ</th><th>Status Pembayaran</th><th class="right">Total</th></tr></thead>
                <tbody>
                    @forelse($rows as $invoice)
                        <tr><td>{{ $invoice->tanggal?->format('d/m/Y') }}</td><td><strong>{{ $invoice->no_invoice }}</strong></td><td>{{ $invoice->nama_customer }}</td><td>{{ $invoice->surat_jalan?->no_sj ?: '-' }}</td><td><span class="badge {{ $invoice->status_payment === 'Paid' ? 'badge-paid' : 'badge-unpaid' }}">{{ $invoice->status_payment === 'Paid' ? 'Lunas' : 'Belum Dibayar' }}</span></td><td class="right">Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="empty">Tidak ada faktur pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            @elseif($jenis === 'penawaran')
                <thead><tr><th>Tanggal</th><th>Nomor Penawaran</th><th>Customer</th><th>Perihal</th><th>Status</th><th class="right">Total</th></tr></thead>
                <tbody>
                    @forelse($rows as $offer)
                        <tr><td>{{ $offer->tanggal?->format('d/m/Y') }}</td><td><strong>{{ $offer->no_penawaran }}</strong></td><td>{{ $offer->nama_customer }}</td><td>{{ $offer->perihal ?: '-' }}</td><td>{{ ucfirst($offer->status) }}</td><td class="right">Rp {{ number_format((float) $offer->total, 0, ',', '.') }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="empty">Tidak ada penawaran pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            @elseif($jenis === 'pembelian')
                <thead><tr><th>Tanggal</th><th>Nomor PO</th><th>Pemasok</th><th class="right">Total</th></tr></thead>
                <tbody>
                    @forelse($rows as $purchaseOrder)
                        <tr><td>{{ $purchaseOrder->tanggal?->format('d/m/Y') }}</td><td><strong>{{ $purchaseOrder->no_po }}</strong></td><td>{{ $purchaseOrder->nama_perusahaan }}</td><td class="right">Rp {{ number_format((float) $purchaseOrder->total, 0, ',', '.') }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="empty">Tidak ada PO vendor pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            @else
                <thead><tr><th>Tanggal & Waktu</th><th>Kode</th><th>Nama Barang</th><th>Jenis</th><th class="right">Jumlah</th><th class="right">Stok Sebelum</th><th class="right">Stok Sesudah</th><th>Keterangan</th><th>Pengguna</th></tr></thead>
                <tbody>
                    @forelse($rows as $log)
                        <tr><td>{{ $log->created_at?->format('d/m/Y H:i') }}</td><td>{{ $log->barang?->kode_barang ?: '-' }}</td><td>{{ $log->barang?->nama_barang ?: '-' }}</td><td>{{ ucfirst($log->jenis) }}</td><td class="right">{{ number_format((float) $log->qty, 2, ',', '.') }}</td><td class="right">{{ number_format((float) $log->stok_sebelum, 2, ',', '.') }}</td><td class="right">{{ number_format((float) $log->stok_sesudah, 2, ',', '.') }}</td><td>{{ $log->keterangan ?: '-' }}</td><td>{{ $log->user?->nama ?: 'Sistem' }}</td></tr>
                    @empty
                        <tr><td colspan="9" class="empty">Tidak ada pergerakan stok pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            @endif
        </table>

        <footer class="report-footer"><span>PT. KEM JAYA MANDIRI · {{ $title }}</span><span>Dicetak {{ now()->format('d/m/Y H:i') }}</span></footer>
    </main>
</body>
</html>
