<?php

namespace App\Http\Controllers;

use App\Models\Penawaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenawaranPrintController
{
    public function __invoke(Request $request, Penawaran $penawaran): View
    {
        abort_unless($request->user()?->hasAccess('penawarans', 'view'), 403);

        $penawaran->load(['user', 'penawaran_items', 'penawaran_attachments']);

        return view('penawaran.print', compact('penawaran'));
    }
}
