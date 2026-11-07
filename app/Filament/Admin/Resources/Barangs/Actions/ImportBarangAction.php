<?php

namespace App\Filament\Admin\Resources\Barangs\Actions;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class ImportBarangAction
{
    public static function make(): Action
    {
        return Action::make('importExcel')->label('Smart Import Excel')->icon('heroicon-o-arrow-up-tray')->color('success')
            ->visible(fn (): bool => auth()->user()?->hasAccess('barangs', 'create') ?? false)
            ->form([
                FileUpload::make('file')->label('File Excel (.xlsx)')->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->maxSize(20480)->storeFiles(false)->required(),
            ])
            ->action(function (array $data): void {
                require_once app_path('Support/SimpleXLSX.php');
                $filePath = $data['file']?->getRealPath();
                $xlsx = $filePath ? SimpleXLSX::parse($filePath) : false;
                if (! $xlsx) {
                    Notification::make()->title('File Excel tidak bisa dibaca')->body(SimpleXLSX::parseError())->danger()->send();
                    return;
                }

                $counts = ['barang' => 0, 'vendor' => 0, 'po' => 0];
                $categoryCache = $vendorCache = $purchaseOrderCache = [];
                DB::transaction(function () use ($xlsx, &$counts, &$categoryCache, &$vendorCache, &$purchaseOrderCache): void {
                    foreach ($xlsx->sheetNames() as $sheetIndex => $sheetName) {
                        $year = preg_replace('/[^0-9]/', '', $sheetName) ?: (string) now()->year;
                        $columns = ['desc' => -1, 'qty' => -1, 'price' => -1, 'uom' => -1, 'po' => -1, 'vendor' => -1, 'date' => -1, 'category' => -1];
                        $headerFound = false;

                        foreach ($xlsx->rows($sheetIndex) as $row) {
                            if (! $headerFound) {
                                foreach ($row as $index => $header) {
                                    $header = strtolower(trim((string) $header));
                                    if (str_contains($header, 'item description') || str_contains($header, 'nama barang')) $columns['desc'] = $index;
                                    if (str_contains($header, 'uom') || str_contains($header, 'satuan')) $columns['uom'] = $index;
                                    if (str_contains($header, 'qty') || str_contains($header, 'stok')) $columns['qty'] = $index;
                                    if (str_contains($header, 'price') || str_contains($header, 'harga beli')) $columns['price'] = $index;
                                    if (str_contains($header, 'po no') || str_contains($header, 'no po')) $columns['po'] = $index;
                                    if (str_contains($header, 'vendor') || str_contains($header, 'supplier')) $columns['vendor'] = $index;
                                    if (str_contains($header, 'date') || str_contains($header, 'tanggal')) $columns['date'] = $index;
                                    if (str_contains($header, 'kategori') || str_contains($header, 'category')) $columns['category'] = $index;
                                }
                                if ($columns['desc'] >= 0) $headerFound = true;
                                continue;
                            }

                            $name = trim((string) ($row[$columns['desc']] ?? ''));
                            if ($name === '') continue;
                            $qty = self::number($row[$columns['qty']] ?? 0);
                            $price = self::number($row[$columns['price']] ?? 0);
                            $unit = trim((string) ($row[$columns['uom']] ?? 'Pcs')) ?: 'Pcs';
                            $categoryName = trim((string) ($row[$columns['category']] ?? ''));
                            $categoryId = null;
                            if ($categoryName !== '') {
                                $categoryCache[$categoryName] ??= KategoriBarang::query()->firstOrCreate(['nama_kategori' => $categoryName])->id;
                                $categoryId = $categoryCache[$categoryName];
                            }

                            $barang = Barang::query()->create([
                                'kode_barang' => self::nextCode('BRG-' . now()->year), 'nama_barang' => $name,
                                'kategori_id' => $categoryId, 'satuan' => $unit, 'stok' => $qty,
                                'stok_minimum' => 0, 'harga_satuan' => $price, 'tahun' => $year,
                            ]);
                            $counts['barang']++;

                            $poNumber = trim((string) ($row[$columns['po']] ?? ''));
                            if ($poNumber === '') continue;
                            $vendorName = trim((string) ($row[$columns['vendor']] ?? '')) ?: 'Unknown';
                            if (! isset($vendorCache[$vendorName])) {
                                $vendorCache[$vendorName] = Vendor::query()->firstOrCreate(['nama_perusahaan' => $vendorName], ['kode_vendor' => self::nextCode('VND-' . now()->year)]);
                                if ($vendorCache[$vendorName]->wasRecentlyCreated) $counts['vendor']++;
                            }
                            $vendor = $vendorCache[$vendorName];
                            if (! isset($purchaseOrderCache[$poNumber])) {
                                $purchaseOrderCache[$poNumber] = PurchaseOrder::query()->firstOrCreate(['no_po' => $poNumber], [
                                    'jenis' => 'ke_vendor', 'tanggal' => self::date($row[$columns['date']] ?? null),
                                    'vendor_id' => $vendor->id, 'nama_perusahaan' => $vendorName,
                                    'status' => 'selesai', 'created_by' => auth()->id(), 'subtotal' => 0, 'ppn_persen' => 0,
                                    'ppn_nominal' => 0, 'total' => 0, 'is_history' => false,
                                ]);
                                if ($purchaseOrderCache[$poNumber]->wasRecentlyCreated) $counts['po']++;
                            }
                            $purchaseOrder = $purchaseOrderCache[$poNumber];
                            PoItem::query()->create([
                                'po_id' => $purchaseOrder->id, 'barang_id' => $barang->id, 'kode_barang' => $barang->kode_barang,
                                'nama_barang' => $name, 'satuan' => $unit, 'qty' => $qty,
                                'harga_satuan' => $price, 'subtotal' => $qty * $price,
                            ]);
                            $purchaseOrder->increment('subtotal', $qty * $price);
                            $purchaseOrder->increment('total', $qty * $price);
                        }
                    }
                });

                Notification::make()->title('Import selesai')->body("{$counts['barang']} barang, {$counts['vendor']} vendor, {$counts['po']} PO diproses.")->success()->send();
            });
    }

    private static function number(mixed $value): float
    {
        $value = trim((string) $value);
        if (str_contains($value, ',')) $value = str_replace(',', '.', str_replace('.', '', $value));
        else $value = str_replace('.', '', $value);
        return (float) preg_replace('/[^0-9.]/', '', $value);
    }

    private static function date(mixed $value): string
    {
        try { return Carbon::parse($value)->toDateString(); } catch (\Throwable) { return now()->toDateString(); }
    }

    private static function nextCode(string $prefix): string
    {
        $max = (int) Barang::query()->where('kode_barang', 'like', $prefix . '%')->selectRaw("MAX(CAST(SUBSTRING(kode_barang, -4) AS UNSIGNED)) AS max_code")->value('max_code');
        if (str_starts_with($prefix, 'VND-')) {
            $max = (int) Vendor::query()->where('kode_vendor', 'like', $prefix . '%')->selectRaw("MAX(CAST(SUBSTRING(kode_vendor, -4) AS UNSIGNED)) AS max_code")->value('max_code');
        }
        return $prefix . str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }
}
