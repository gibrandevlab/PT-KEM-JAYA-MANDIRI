@php
    $noCetak = $penawaran->no_penawaran;
    if ($penawaran->use_sdk && ! str_contains($noCetak, 'SDK/')) $noCetak = preg_replace('/-/', '-SDK/', $noCetak, 1);
    if ($penawaran->is_revisi && $penawaran->revisi_ke) $noCetak .= '   ' . $penawaran->revisi_ke;
    $bulan = ['January'=>'Januari', 'February'=>'Februari', 'March'=>'Maret', 'April'=>'April', 'May'=>'Mei', 'June'=>'Juni', 'July'=>'Juli', 'August'=>'Agustus', 'September'=>'September', 'October'=>'Oktober', 'November'=>'November', 'December'=>'Desember'];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Penawaran {{ str_replace(['/', '\\'], ' ', $penawaran->no_penawaran) }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: Tahoma, sans-serif; font-size: 10pt; line-height: 1.4; color: #000; margin: 0; padding: 1cm .6cm; }
        .header-table { width: 100%; margin-bottom: -2px; }
        .header-table td { vertical-align: middle; }
        .logo-cell { vertical-align: top !important; }
        .logo-img { max-width: 100px; width: auto; height: auto; display: inline-block; }
        .company-details { text-align: right; font-size: 12px; line-height: 1.4; }
        .company-details h1 { font-size: 24px; margin: 0 0 5px; font-weight: 800; }
        .separator { border-top: 1px solid #000; border-bottom: 1px solid #000; height: 2px; margin: 0 0 10px; }
        .address-table { width: 100%; margin-bottom: 10px; font-size: 12px; }
        .address-table td { vertical-align: top; }
        .address-block strong { font-size: 14px; display: block; margin-bottom: 5px; }
        .info-table { border-collapse: collapse; }
        .info-table td { padding: 0; vertical-align: top; font-size: 10px; }
        .info-label { width: 85px; }
        .info-colon { width: 15px; text-align: center; }
        .doc-intro { margin: 10px 0; font-size: 12px; line-height: 1.4; }
        .items-table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 2px 4px; vertical-align: middle; }
        .items-table th { background: #fff; font-weight: 700; text-align: center; font-size: 11px; }
        .text-center { text-align: center; } .text-right { text-align: right; }
        .totals-table { float: right; width: 45%; border-collapse: collapse; }
        .totals-table td { padding: 1px 6px; border: none; }
        .total-label { text-align: right; font-weight: 700; width: 60%; white-space: nowrap; }
        .total-value { text-align: right; font-weight: 700; width: 40%; }
        .footer-section { margin-top: 15px; }
        .terms-table { width: 100%; border-collapse: collapse; }
        .terms-table td { padding: 0; vertical-align: top; font-size: 11px; }
        .terms-label { width: 130px; } .terms-colon { width: 15px; text-align: center; }
        .no-print { margin-bottom: 20px; display: flex; gap: 10px; align-items: center; background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #ddd; }
        .btn-print { background: #3b82f6; color: white; border: 0; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; }
        @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; padding: 0 !important; } .no-print { display: none !important; } @page { margin: 1.9cm .6cm; } }
        * { font-family: Tahoma, sans-serif !important; }
        *:not(h1):not(h1 *) { font-size: 12px !important; }
    </style>
</head>
<body>
    <div class="no-print"><button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak PDF / Print</button></div>
    <table class="header-table"><tr>
        <td class="logo-cell" style="width:40%"><img src="{{ asset('Picture1.png') }}" class="logo-img" alt="Logo"></td>
        <td class="company-details"><h1>PT. KEM JAYA MANDIRI</h1>Graha Harapan Regency Blok I5 No.15<br>Babelan - Bekasi 17610<br>Telp. 021-56926318<br>E-mail : support@kemjaya.com</td>
    </tr></table>
    <div class="separator"></div>
    <table class="address-table"><tr>
        <td style="width:60%;vertical-align:top"><div class="address-block">
            <strong>{{ $penawaran->nama_customer }}</strong>
            <div style="line-height:1.4;color:#333;max-width:350px">{!! nl2br(e($penawaran->alamat_customer)) !!}</div>
            @if($penawaran->telepon_customer)<table class="info-table" style="width:auto;margin-top:2px"><tr><td style="width:50px">Phone</td><td style="width:10px;text-align:center">:</td><td>{{ $penawaran->telepon_customer }}</td></tr></table>@endif
            <table class="info-table" style="width:auto;margin-top:15px">
                <tr><td style="width:50px">Up</td><td style="width:10px;text-align:center">:</td><td>{{ $penawaran->up ?: '-' }}</td></tr>
                <tr><td style="width:50px">Mobile</td><td style="width:10px;text-align:center">:</td><td>{{ $penawaran->mobile ?: '-' }}</td></tr>
                <tr><td style="width:50px">Email</td><td style="width:10px;text-align:center">:</td><td>{{ $penawaran->email_up ?: '-' }}</td></tr>
            </table>
        </div></td>
        <td style="width:40%;vertical-align:top;padding-left:10px"><div style="margin-bottom:5px"><strong>Sales Contact :</strong></div>
            <table class="info-table" style="width:100%">
                <tr><td style="width:80px">Offer</td><td style="width:10px;text-align:center">:</td><td>{{ $noCetak }}</td></tr>
                <tr><td>Com. Contact</td><td style="text-align:center">:</td><td>{{ $penawaran->com_contact ?: 'Yudhi Syarif' }}</td></tr>
                <tr><td>Email</td><td style="text-align:center">:</td><td><a href="mailto:support@kemjaya.com">support@kemjaya.com</a></td></tr>
                <tr><td>Telephone</td><td style="text-align:center">:</td><td>085711037510</td></tr>
                <tr><td>Date</td><td style="text-align:center">:</td><td>{{ strtr($penawaran->tanggal?->format('d F Y') ?? '', $bulan) }}</td></tr>
                <tr><td>Page</td><td style="text-align:center">:</td><td>1</td></tr>
            </table>
        </td>
    </tr></table>
    <div class="doc-intro">Dear Sir<br>As following respon to your requirement please see our offering below,</div>
    <table class="items-table"><thead><tr>
        <th rowspan="2" style="width:4%">No.</th><th rowspan="2" style="width:50%;padding:8px 4px">Product Description</th><th rowspan="2" style="width:6%">Qty</th><th rowspan="2" style="width:8%">Unit</th><th style="width:16%;border-bottom:1px solid #000">Unit Price</th><th style="width:16%;border-bottom:1px solid #000">Net Amount</th>
    </tr><tr><th style="border-top:none">IDR</th><th style="border-top:none">IDR</th></tr></thead><tbody>
        @foreach($penawaran->penawaran_items as $index => $item)
            <tr @if($index + 1 === 25) style="page-break-after:always;break-after:page" @endif><td class="text-center">{{ $index + 1 }}</td><td><div style="margin-bottom:2px;text-transform:uppercase">{{ $item->nama_barang }}</div>@if($item->deskripsi)<div style="color:#666;font-style:italic">{{ $item->deskripsi }}</div>@endif</td><td class="text-center">{{ number_format($item->qty, 0, ',', '.') }}</td><td class="text-center">{{ $item->satuan }}</td><td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td><td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>
        @endforeach
    </tbody></table>
    <div style="overflow:hidden;margin-top:10px"><div style="float:left;width:50%;margin-left:4%"><table style="width:100%;border-collapse:collapse;margin-top:4px"><tr><td style="border:0.1mm solid #000;padding:8px;vertical-align:top;height:60px"><div style="margin-bottom:5px">Note :</div><div>@foreach(preg_split('/\r\n|\r|\n/', $penawaran->perihal ?? '') as $note) @php($note = trim($note)) @if($note !== '')<div>{{ str_starts_with($note, '-') ? $note : '- '.$note }}</div>@endif @endforeach</div></td></tr></table></div>
        <table class="totals-table"><tr><td class="total-label">Gross Total :</td><td class="total-value">{{ number_format($penawaran->subtotal, 0, ',', '.') }}</td></tr>
            @if($penawaran->diskon_nominal > 0)<tr><td class="total-label">Discount {{ rtrim(rtrim(number_format($penawaran->diskon_persen, 2, '.', ''), '0'), '.') }}% :</td><td class="total-value">{{ number_format($penawaran->diskon_nominal, 0, ',', '.') }}</td></tr><tr><td class="total-label">Total After Discount :</td><td class="total-value">{{ number_format($penawaran->subtotal - $penawaran->diskon_nominal, 0, ',', '.') }}</td></tr>@endif
            @if($penawaran->ppn_nominal > 0)<tr><td class="total-label">PPn {{ rtrim(rtrim(number_format($penawaran->ppn_persen, 2, '.', ''), '0'), '.') }}% :</td><td class="total-value">{{ number_format($penawaran->ppn_nominal, 0, ',', '.') }}</td></tr>@endif
            <tr><td class="total-label" style="padding-top:2px">Net Total :</td><td class="total-value" style="padding-top:2px">{{ number_format($penawaran->total, 0, ',', '.') }}</td></tr>
        </table>
    </div>
    <div class="footer-section"><table class="terms-table">
        <tr><td colspan="3" style="font-weight:bold;padding-bottom:0">We thank you for your inquiry and offer without obligation :</td></tr>
        <tr><td class="terms-label">Your inquiry date</td><td class="terms-colon">:</td><td>-</td></tr>
        <tr><td class="terms-label">Your reference</td><td class="terms-colon">:</td><td>{{ $penawaran->reference ?? 'By Email' }}</td></tr>
        <tr><td class="terms-label">Term of delivery</td><td class="terms-colon">:</td><td>{{ $penawaran->term_of_delivery ?: 'Franco Site' }}</td></tr>
        <tr><td class="terms-label">Delivery time</td><td class="terms-colon">:</td><td>{{ $penawaran->delivery_time ?: 'Ready Stock' }}</td></tr>
        <tr><td class="terms-label">Validity</td><td class="terms-colon">:</td><td>1 Week</td></tr>
        <tr><td class="terms-label">Term of payment</td><td class="terms-colon">:</td><td>{{ $penawaran->term_of_payment ?: 'Net 30 Days' }}</td></tr>
        <tr><td class="terms-label">Sales condition</td><td class="terms-colon">:</td><td>Consumable</td></tr>
    </table></div>
    <script>window.onload = () => window.print();</script>
</body>
</html>
