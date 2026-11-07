<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\BankAccount;
use Illuminate\View\View;

class InvoicePrintController
{
    public function __invoke(Invoice $invoice): View
    {
        abort_unless(auth()->user()?->hasAccess('invoices', 'view'), 403);
        $invoice->load(['invoice_items', 'user', 'purchase_order', 'surat_jalan']);
        $dpRefInvoice = $invoice->dp_ref_invoice_id ? Invoice::find($invoice->dp_ref_invoice_id) : null;
        $bankAccount = $invoice->bank_account_id ? BankAccount::find($invoice->bank_account_id) : null;

        return view('invoices.print', compact('invoice', 'dpRefInvoice', 'bankAccount'));
    }
}
