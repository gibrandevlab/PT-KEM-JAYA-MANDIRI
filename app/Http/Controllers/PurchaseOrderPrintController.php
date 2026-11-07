<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\View\View;

class PurchaseOrderPrintController
{
    public function __invoke(PurchaseOrder $purchaseOrder): View
    {
        abort_unless(auth()->user()?->hasAccess('purchase_orders', 'view'), 403);

        return view('purchase-orders.print', [
            'purchaseOrder' => $purchaseOrder->load(['po_items', 'user', 'customer', 'vendor']),
        ]);
    }
}
