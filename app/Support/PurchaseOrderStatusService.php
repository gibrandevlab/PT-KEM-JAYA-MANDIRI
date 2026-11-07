<?php

namespace App\Support;

use App\Models\Barang;
use App\Models\PurchaseOrder;
use App\Models\StokLog;
use Illuminate\Support\Facades\DB;

class PurchaseOrderStatusService
{
    public static function change(PurchaseOrder $record, string $status): void
    {
        abort_unless(in_array($status, ['draft', 'diproses', 'selesai', 'dibatalkan'], true), 422);

        DB::transaction(function () use ($record, $status): void {
            $record->refresh();
            if ($record->status === $status) return;

            if ($record->jenis === 'dari_customer' && $status === 'dibatalkan' && $record->status !== 'dibatalkan') {
                foreach ($record->po_items as $item) {
                    if (! $item->barang_id) continue;
                    $barang = Barang::query()->lockForUpdate()->find($item->barang_id);
                    if (! $barang) continue;
                    $before = (float) $barang->stok;
                    $after = $before + (float) $item->qty;
                    $barang->update(['stok' => $after]);
                    StokLog::query()->create([
                        'barang_id' => $barang->id, 'jenis' => 'masuk', 'qty' => $item->qty,
                        'stok_sebelum' => $before, 'stok_sesudah' => $after,
                        'referensi_tipe' => 'purchase_order', 'referensi_id' => $record->id,
                        'keterangan' => 'PO Dibatalkan: ' . $record->no_po, 'created_by' => auth()->id(),
                    ]);
                }
            }

            $record->update(['status' => $status]);
        });
    }
}
