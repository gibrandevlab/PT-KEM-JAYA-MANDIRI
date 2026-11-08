<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ str_replace(['/', '\\'], ' ', $invoice->no_invoice) }}</title>
    <link rel="stylesheet" href="{{ asset('print/document.css') }}">
</head>
<body>
    <div class="no-print"><button onclick="window.print()">Cetak PDF / Print</button><span>Invoice — {{ $invoice->no_invoice }}</span></div>

    <table class="company-header"><tr>
        <td style="width:40%"><img src="{{ asset('Picture1.png') }}" alt="Logo KJM"></td>
        <td class="company-details"><strong>PT. KEM JAYA MANDIRI</strong>Graha Harapan Regency Blok I5 No.15<br>Babelan - Bekasi 17610<br>Telp. 021-56926318<br>E-mail : support@kemjaya.com</td>
    </tr></table>
    <div class="separator"></div>
    <div class="document-title">INVOICE</div>

    <table class="meta"><tr>
        <td style="width:55%">
            <strong>{{ $invoice->nama_customer }}</strong><br>
            {!! nl2br(e($invoice->alamat_customer ?: '-')) !!}<br>
            @if($invoice->telepon_customer)Telp: {{ $invoice->telepon_customer }}<br>@endif
            @if($invoice->npwp)NPWP: {{ $invoice->npwp }}@endif
        </td>
        <td>
            <div><b>Nomor Invoice:</b> {{ $invoice->no_invoice }}</div>
            <div><b>Tanggal:</b> {{ $invoice->tanggal?->format('d/m/Y') ?: '-' }}</div>
            <div><b>No. PO:</b> {{ $invoice->po_number ?: $invoice->purchase_order?->no_po ?: '-' }}</div>
            <div><b>Jatuh Tempo:</b> {{ $invoice->jatuh_tempo?->format('d/m/Y') ?: '-' }}</div>
            <div><b>Status Bayar:</b> {{ $invoice->status_payment === 'Paid' ? 'Lunas' : 'Belum Dibayar' }}</div>
            @if($invoice->tanggal_payment)<div><b>Tanggal Bayar:</b> {{ $invoice->tanggal_payment->format('d/m/Y') }}</div>@endif
        </td>
    </tr></table>

    <table class="items">
        <thead><tr><th style="width:5%">No.</th><th style="width:14%">Kode</th><th>Nama Barang</th><th>Deskripsi</th><th class="right" style="width:8%">Qty</th><th class="center" style="width:8%">Satuan</th><th class="right" style="width:15%">Harga Satuan</th><th class="right" style="width:15%">Subtotal</th></tr></thead>
        <tbody>
            @forelse($invoice->invoice_items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->deskripsi ?: '-' }}</td>
                    <td class="right">{{ number_format((float) $item->qty, 2, ',', '.') }}</td>
                    <td class="center">{{ $item->satuan }}</td>
                    <td class="right">{{ number_format((float) $item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="center">Tidak ada rincian barang.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal:</td><td>{{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</td></tr>
        @if((float) $invoice->diskon_nominal > 0)<tr><td>Diskon:</td><td>{{ number_format((float) $invoice->diskon_nominal, 0, ',', '.') }}</td></tr>@endif
        @if((float) $invoice->dpp_nominal > 0)<tr><td>DPP:</td><td>{{ number_format((float) $invoice->dpp_nominal, 0, ',', '.') }}</td></tr>@endif
        @if((float) $invoice->ppn_nominal > 0)<tr><td>PPN:</td><td>{{ number_format((float) $invoice->ppn_nominal, 0, ',', '.') }}</td></tr>@endif
        <tr><td><strong>Total:</strong></td><td><strong>{{ number_format((float) $invoice->total, 0, ',', '.') }}</strong></td></tr>
    </table>

    @if($invoice->term_of_payment)<div class="terms"><b>Term of Payment:</b> {{ $invoice->term_of_payment }}</div>@endif
    @if($invoice->catatan)<div class="notes"><b>Catatan:</b><br>{{ $invoice->catatan }}</div>@endif
    <table class="sign-row"><tr><td><b>Dibuat oleh</b><div class="sign-space"></div><span class="sign-name">{{ $invoice->user?->nama ?: '-' }}</span></td><td><b>Penerima</b><div class="sign-space"></div><span class="sign-name">Nama / Tanggal</span></td></tr></table>
</body>
</html>
