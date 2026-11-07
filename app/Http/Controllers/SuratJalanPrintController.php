<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use Illuminate\View\View;

class SuratJalanPrintController
{
    public function __invoke(SuratJalan $suratJalan): View
    {
        abort_unless(auth()->user()?->hasAccess('surat_jalans', 'view'), 403);
        return view('surat-jalans.print', ['suratJalan' => $suratJalan->load(['surat_jalan_items', 'user', 'purchase_order', 'penawaran'])]);
    }
}
