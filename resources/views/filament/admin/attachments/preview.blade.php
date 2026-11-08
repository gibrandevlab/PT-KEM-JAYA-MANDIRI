@php
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $path = $attachment->file_path;
    $mimeType = $disk->exists($path) ? $disk->mimeType($path) : null;
    $previewUrl = in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'], true)
        ? $disk->url($path)
        : null;
@endphp

<div class="w-full">
    @if ($previewUrl && str_starts_with($mimeType, 'image/'))
        <img
            src="{{ $previewUrl }}"
            alt="Pratinjau {{ $attachment->file_name }}"
            class="mx-auto max-h-[70vh] max-w-full rounded-lg object-contain"
            loading="lazy"
            referrerpolicy="no-referrer"
        >
    @elseif ($previewUrl && $mimeType === 'application/pdf')
        <iframe
            src="{{ $previewUrl }}"
            title="Pratinjau {{ $attachment->file_name }}"
            class="h-[70vh] w-full rounded-lg border-0"
            sandbox="allow-scripts"
            referrerpolicy="no-referrer"
        ></iframe>
    @else
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 text-center dark:border-white/10 dark:bg-white/5">
            <x-filament::icon icon="heroicon-o-shield-exclamation" class="mx-auto mb-3 size-8 text-gray-500" />
            <p class="font-medium text-gray-950 dark:text-white">Pratinjau tidak tersedia untuk tipe file ini.</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">File tidak dibuka langsung demi keamanan.</p>
        </div>
    @endif
</div>
