<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Surat Jalan {{ str_replace(['/', '\\'], ' ', $suratJalan->no_sj) }}</title><link rel="stylesheet" href="{{ asset('print/document.css') }}"></head>
<body><div class="no-print"><button onclick="window.print()">Cetak PDF / Print</button><span>Surat Jalan — {{ $suratJalan->no_sj }}</span></div>
<table class="company-header"><tr><td style="width:40%"><img src="{{ asset('Picture1.png') }}" alt="Logo KJM"></td><td class="company-details"><strong>PT. KEM JAYA MANDIRI</strong>Graha Harapan Regency Blok I5 No.15<br>Babelan - Bekasi 17610<br>Telp. 021-56926318<br>E-mail : support@kemjaya.com</td></tr></table><div class="separator"></div>
<div class="document-title">SURAT JALAN</div>
@php($referenceNumber = $suratJalan->no_po ?: $suratJalan->purchase_order?->no_po ?: $suratJalan->penawaran?->no_penawaran ?: '-')
@php($referenceLabel = $suratJalan->no_po || $suratJalan->purchase_order?->no_po ? 'Purchase Order Number' : 'Quotation Reference')
<table class="meta"><tr><td style="width:55%"><b>To:</b><br><strong>{{ $suratJalan->nama_penerima }}</strong><br>{{ $suratJalan->alamat_pengiriman }}<br>Phone: {{ $suratJalan->telepon_penerima ?: '-' }}</td><td><b>Date:</b> {{ $suratJalan->tanggal?->format('d M Y') }}<br><b>SJ No.:</b> {{ $suratJalan->no_sj }}<br><b>{{ $referenceLabel }}:</b> {{ $referenceNumber }}<br><b>Page:</b> 1</td></tr></table>
<table class="items"><thead><tr><th style="width:7%">No.</th><th style="width:17%">Product Code</th><th style="width:38%">Product Name</th><th style="width:22%">Description</th><th style="width:9%">Qty</th><th style="width:9%">Unit</th></tr></thead><tbody>
@foreach($suratJalan->surat_jalan_items as $index => $item)<tr><td class="center">{{ $index + 1 }}</td><td class="center">{{ $item->kode_barang }}</td><td>{{ $item->nama_barang }}</td><td>{{ $item->keterangan ?: '-' }}</td><td class="center">{{ number_format($item->qty, 0, ',', '.') }}</td><td class="center">{{ $item->satuan }}</td></tr>@endforeach
</tbody></table>
@if($suratJalan->catatan)<div class="notes"><b>Note:</b><br>{{ $suratJalan->catatan }}</div>@endif
<table class="sign-row"><tr><td><b>Prepared By</b><div class="sign-space"></div><span class="sign-name">{{ $suratJalan->user?->nama ?: '-' }}</span></td><td><b>Received By</b><div class="sign-space"></div><span class="sign-name">Nama / Tanggal</span></td></tr></table>
</body></html>
