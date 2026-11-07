<div class="space-y-4 text-xs">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 dark:bg-gray-800/60 p-3.5 rounded-xl border border-gray-200 dark:border-gray-700">
        <div>
            <div class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">Pelanggan</div>
            <div class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $record->nama_customer }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">Tanggal</div>
            <div class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $record->tanggal ? date('d/m/Y', strtotime($record->tanggal)) : '-' }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">Perihal</div>
            <div class="font-semibold text-gray-900 dark:text-white mt-0.5 truncate">{{ $record->perihal ?: '-' }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">Pembuat</div>
            <div class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $record->user?->nama ?: 'Admin' }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold uppercase tracking-wider text-[10px]">
                <tr>
                    <th class="px-3.5 py-2.5">Kode</th>
                    <th class="px-3.5 py-2.5">Nama Barang</th>
                    <th class="px-3.5 py-2.5 text-center">Jumlah</th>
                    <th class="px-3.5 py-2.5 text-right">Harga</th>
                    <th class="px-3.5 py-2.5 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                @forelse ($record->penawaran_items as $item)
                    <tr>
                        <td class="px-3.5 py-2 font-mono text-gray-500">{{ $item->kode_barang }}</td>
                        <td class="px-3.5 py-2 font-medium text-gray-900 dark:text-white">{{ $item->nama_barang }}</td>
                        <td class="px-3.5 py-2 text-center">{{ $item->qty }} {{ $item->satuan }}</td>
                        <td class="px-3.5 py-2 text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-3.5 py-2 text-right font-bold text-gray-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3.5 py-4 text-center text-gray-400 italic">Tidak ada rincian item barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="flex justify-end pt-1">
        <div class="w-64 space-y-1 text-right text-xs">
            <div class="flex justify-between text-gray-500 dark:text-gray-400">
                <span>Subtotal:</span>
                <span class="font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($record->subtotal, 0, ',', '.') }}</span>
            </div>
            @if ($record->ppn_nominal > 0)
                <div class="flex justify-between text-gray-500 dark:text-gray-400">
                    <span>PPN ({{ $record->ppn_persen }}%):</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($record->ppn_nominal, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-sm font-extrabold text-orange-500 border-t border-gray-200 dark:border-gray-700 pt-1.5">
                <span>Grand Total:</span>
                <span>Rp {{ number_format($record->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
