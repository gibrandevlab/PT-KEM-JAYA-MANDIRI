<?php

namespace App\Support;

use App\Models\Invoice;
use Illuminate\Validation\ValidationException;

class InvoiceShipmentGuard
{
    public static function validate(?int $shipmentId, ?string $type, ?int $dpInvoiceId = null, ?int $ignoreInvoiceId = null): void
    {
        if (! $shipmentId) return;

        $existing = Invoice::query()->where('sj_id', $shipmentId)
            ->when($ignoreInvoiceId, fn ($query) => $query->whereKeyNot($ignoreInvoiceId))->get();

        if ($type === 'Langsung' && $existing->isNotEmpty()) {
            throw ValidationException::withMessages(['data.sj_id' => 'Surat Jalan ini sudah memiliki Invoice. Gunakan alur DP dan Pelunasan jika perlu tagihan bertahap.']);
        }

        if ($type === 'DP' && $existing->contains(fn (Invoice $invoice): bool => in_array($invoice->tipe_invoice, ['Langsung', 'DP'], true))) {
            throw ValidationException::withMessages(['data.sj_id' => 'Surat Jalan ini sudah memiliki Invoice langsung atau DP.']);
        }

        if ($type === 'Pelunasan') {
            $dp = $existing->first(fn (Invoice $invoice): bool => $invoice->tipe_invoice === 'DP');
            if (! $dp || (int) $dp->id !== (int) $dpInvoiceId) {
                throw ValidationException::withMessages(['data.dp_ref_invoice_id' => 'Pilih Invoice DP yang terhubung ke Surat Jalan ini.']);
            }
            if ($existing->contains(fn (Invoice $invoice): bool => $invoice->tipe_invoice === 'Pelunasan')) {
                throw ValidationException::withMessages(['data.sj_id' => 'Surat Jalan ini sudah memiliki Invoice Pelunasan.']);
            }
        }
    }
}
