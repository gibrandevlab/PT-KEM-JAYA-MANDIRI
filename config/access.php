<?php

return [
    'overrides_enabled' => env('RBAC_OVERRIDES_ENABLED', true),
    'features' => [
        'dashboard' => 'Dashboard',
        'penawarans' => 'Penawaran',
        'purchase_orders' => 'Purchase Order',
        'surat_jalans' => 'Surat Jalan',
        'invoices' => 'Invoice',
        'barangs' => 'Data Barang',
        'stock_gudangs' => 'Stock Gudang',
        'kategori_barangs' => 'Kategori Barang',
        'customers' => 'Customer',
        'vendors' => 'Vendor',
        'bank_accounts' => 'Data Bank',
        'term_of_payments' => 'Term of Payment',
        'users' => 'Kelola User',
        'customer_addresses' => 'Alamat Customer',
        'customer_pics' => 'PIC Customer',
        'stok_logs' => 'Log Stok',
        'reports' => 'Laporan',
    ],

    'actions' => ['view', 'create', 'update', 'delete'],

    // Native defaults: admin has full access; pekerja handles operations and reads inventory.
    'defaults' => [
        'admin' => ['*' => ['*']],
        'pekerja' => [
            'dashboard' => ['view'],
            'penawarans' => ['view', 'create'],
            'purchase_orders' => ['view', 'create'],
            'surat_jalans' => ['view', 'create'],
            'barangs' => ['view'],
            'stock_gudangs' => ['view', 'create', 'delete'],
            'kategori_barangs' => ['view', 'create', 'update', 'delete'],
            'customers' => ['view'],
            'vendors' => ['view'],
            'customer_addresses' => ['view'],
            'customer_pics' => ['view'],
            'stok_logs' => ['view'],
        ],
    ],
];
