<?php

namespace App\Support;

use App\Models\Customer;

class CustomerInventorySync
{
    public static function mutateAddresses(array $data, array $state): array
    {
        $data['alamat'] = self::joinAddresses($state['customer_addresses_form'] ?? []);
        $data['alamat_gudang'] = self::joinAddresses($state['warehouse_addresses_form'] ?? []);
        return $data;
    }

    public static function sync(Customer $customer, array $state): void
    {
        $customer->customer_pics()->delete();
        foreach ([['Customer', 'pic', 'pic_phone', 'pic_email'], ['Gudang', 'pic_gudang', 'pic_gudang_phone', null]] as [$type, $nameKey, $phoneKey, $emailKey]) {
            if (filled($state[$nameKey] ?? null)) {
                $customer->customer_pics()->create(['tipe' => $type, 'nama' => $state[$nameKey], 'telepon' => $state[$phoneKey] ?? null, 'email' => $emailKey ? ($state[$emailKey] ?? null) : null]);
            }
        }
        foreach ($state['additional_customer_pics'] ?? [] as $pic) {
            if (filled($pic['nama'] ?? null)) $customer->customer_pics()->create(['tipe' => 'Customer', 'nama' => $pic['nama'], 'telepon' => $pic['telepon'] ?? null, 'email' => $pic['email'] ?? null]);
        }
        foreach ($state['additional_warehouse_pics'] ?? [] as $pic) {
            if (filled($pic['nama'] ?? null)) $customer->customer_pics()->create(['tipe' => 'Gudang', 'nama' => $pic['nama'], 'telepon' => $pic['telepon'] ?? null, 'email' => null]);
        }

        $customer->customer_addresses()->delete();
        foreach ([['Alamat Perusahaan', 'customer_addresses_form'], ['Gudang', 'warehouse_addresses_form']] as [$prefix, $key]) {
            foreach ($state[$key] ?? [] as $index => $address) {
                if (filled($address['alamat_lengkap'] ?? null)) $customer->customer_addresses()->create(['label_alamat' => $prefix . ' ' . ($index + 1), 'alamat_lengkap' => $address['alamat_lengkap']]);
            }
        }
    }

    private static function joinAddresses(array $addresses): ?string
    {
        $values = array_values(array_filter(array_map(fn (array $row): string => trim((string) ($row['alamat_lengkap'] ?? '')), $addresses)));
        return $values ? implode("\n\n", $values) : null;
    }
}
