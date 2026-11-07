<div class="space-y-4 text-xs">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 dark:bg-gray-800/60 p-3.5 rounded-xl border border-gray-200 dark:border-gray-700">
        <div>
            <div class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">Penerima</div>
            <div class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $record->nama_penerima }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">Tanggal</div>
            <div class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $record->tanggal ? date('d/m/Y', strtotime($record->tanggal)) : '-' }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">No. PO</div>
            <div class="font-semibold text-gray-900 dark:text-white mt-0.5 font-mono">{{ $record->no_po ?: '-' }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">Kendaraan</div>
            <div class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $record->kendaraan ?: '-' }} ({{ $record->no_polisi ?: '-' }})</div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold uppercase tracking-wider text-[10px]">
                <tr>
                    <th class="px-3.5 py-2.5">Kode</th>
                    <th class="px-3.5 py-2.5">Nama Barang</th>
                    <th class="px-3.5 py-2.5 text-center">Qty Dikirim</th>
                    <th class="px-3.5 py-2.5">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                @forelse ($record->surat_jalan_items as $item)
                    <tr>
                        <td class="px-3.5 py-2 font-mono text-gray-500">{{ $item->kode_barang }}</td>
                        <td class="px-3.5 py-2 font-medium text-gray-900 dark:text-white">{{ $item->nama_barang }}</td>
                        <td class="px-3.5 py-2 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $item->qty }} {{ $item->satuan }}</td>
                        <td class="px-3.5 py-2 text-gray-500 dark:text-gray-400">{{ $item->keterangan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3.5 py-4 text-center text-gray-400 italic">Tidak ada rincian item pengiriman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($record->catatan)
        <div class="bg-gray-50 dark:bg-gray-800/40 p-3 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
            <strong>Catatan:</strong> {{ $record->catatan }}
        </div>
    @endif
</div>
