<?php

namespace App\Filament\Admin\Resources\Penawarans\Pages;

use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use App\Models\Customer;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Filament\Facades\Filament;

class CreatePenawaran extends CreateRecord
{
    protected static string $resource = PenawaranResource::class;

    public static function nextNumber(?string $date = null, bool $useSdk = false): string
    {
        $date = $date ? \Illuminate\Support\Carbon::parse($date) : now();
        $roman = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$date->month];
        $suffix = "-KJM/{$roman}/{$date->year}";
        $last = DB::table('penawaran')->where('no_penawaran', 'like', "%{$suffix}")
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(no_penawaran, '-', 1) AS UNSIGNED)) AS last_num")
            ->value('last_num');

        $number = str_pad((string) (((int) $last) + 1), 2, '0', STR_PAD_LEFT) . $suffix;

        return $useSdk ? preg_replace('/^(\d+-)/', '$1SDK/', $number, 1) : $number;
    }

    protected function afterCreate(): void
    {
        if ($this->record->customer_id || blank($this->record->nama_customer)) return;

        DB::transaction(function (): void {
            $name = trim($this->record->nama_customer);
            $customer = Customer::query()
                ->whereRaw('LOWER(TRIM(nama_perusahaan)) = ?', [mb_strtolower($name)])
                ->first();

            if (! $customer) {
                $customer = Customer::query()->create([
                    'kode_customer' => \App\Filament\Admin\Resources\Customers\Pages\CreateCustomer::nextCode(),
                    'nama_perusahaan' => $name,
                    'alamat' => $this->record->alamat_customer,
                    'alamat_gudang' => $this->record->alamat_customer_2,
                    'telepon' => $this->record->telepon_customer,
                    'npwp' => $this->record->npwp_customer,
                    'pic' => $this->record->up,
                    'pic_phone' => $this->record->mobile,
                    'pic_email' => $this->record->email_up,
                ]);
            }

            $this->record->update(['customer_id' => $customer->getKey()]);
        });
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $state = $this->form->getRawState();
        $items = is_array($state) ? ($state['penawaran_items'] ?? []) : [];
        $subtotal = collect($items)->sum(fn (array $item): float => (float) ($item['qty'] ?? 0) * (float) ($item['harga_satuan'] ?? 0));
        $discountRate = ($state['enable_diskon'] ?? false) ? (float) ($state['diskon_persen'] ?? 0) : 0.0;
        $discount = round($subtotal * $discountRate / 100, 2);
        $afterDiscount = $subtotal - $discount;
        $taxRate = ($state['enable_ppn'] ?? false) ? (float) ($state['ppn_persen'] ?? 0) : 0.0;
        $tax = round($afterDiscount * $taxRate / 100, 2);

        $data['no_penawaran'] = filled($data['no_penawaran'] ?? null) ? $data['no_penawaran'] : self::nextNumber($data['tanggal'] ?? null, (bool) ($data['use_sdk'] ?? false));
        $data['berlaku_sampai'] = filled($data['tanggal'] ?? null) ? \Illuminate\Support\Carbon::parse($data['tanggal'])->addWeek()->toDateString() : now()->addWeek()->toDateString();
        $data['subtotal'] = $subtotal;
        $data['diskon_persen'] = $discountRate;
        $data['diskon_nominal'] = $discount;
        $data['ppn_persen'] = $taxRate;
        $data['ppn_nominal'] = $tax;
        $data['total'] = $afterDiscount + $tax;
        $data['created_by'] = Filament::auth()->id();
        $data['status'] = 'draft';
        $data['is_history'] = false;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return PenawaranResource::getUrl('view', ['record' => $this->record]);
    }
}
