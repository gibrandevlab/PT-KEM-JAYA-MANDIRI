<x-filament-panels::page>
    @php
        $isAdmin = $user->role === 'admin';
        $roleLabel = $isAdmin ? 'Admin / Finance' : 'Pekerja';
        $avatarUrl = $user->getFilamentAvatarUrl();
        $nameParts = preg_split('/\s+/u', trim($user->nama ?: $user->username), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initial = mb_strtoupper(mb_substr($nameParts[0] ?? '', 0, 1).mb_substr($nameParts[1] ?? '', 0, 1));
    @endphp

    <div class="mx-auto w-full max-w-5xl space-y-6">
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900 sm:p-6">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                <div class="relative mx-auto shrink-0 sm:mx-0">
                    <div class="grid size-20 place-items-center overflow-hidden rounded-full bg-gray-100 text-2xl font-semibold text-primary-700 ring-1 ring-gray-950/10 dark:bg-white/10 dark:text-primary-300 dark:ring-white/10 sm:size-24">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Foto profil {{ $user->nama }}" class="size-full object-cover">
                        @else
                            {{ $initial }}
                        @endif
                    </div>
                    <span @class([
                        'absolute bottom-1 right-1 size-3.5 rounded-full ring-2 ring-white dark:ring-gray-900',
                        'bg-success-500' => $user->aktif,
                        'bg-gray-400' => ! $user->aktif,
                    ])></span>
                </div>

                <div class="min-w-0 flex-1 text-center sm:text-left">
                    <h2 class="break-words text-xl font-semibold text-gray-950 dark:text-white sm:text-2xl">
                        {{ $user->nama ?: $user->username }}
                    </h2>
                    <p class="mt-1 break-all text-sm text-gray-500 dark:text-gray-400">
                        {{ '@'.$user->username }}
                    </p>
                    <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">
                        <span class="inline-flex items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 dark:bg-primary-400/10 dark:text-primary-300">
                            {{ $roleLabel }}
                        </span>
                        <span @class([
                            'inline-flex items-center rounded-full px-3 py-1 text-xs font-medium',
                            'bg-success-50 text-success-700 dark:bg-success-400/10 dark:text-success-300' => $user->aktif,
                            'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300' => ! $user->aktif,
                        ])>
                            {{ $user->aktif ? 'Akun aktif' : 'Akun nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(18rem,0.65fr)]">
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-white/10 sm:px-6">
                    <h3 class="font-semibold text-gray-950 dark:text-white">Informasi Akun</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi akun yang digunakan untuk mengakses aplikasi.</p>
                </div>

                <dl class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ([
                        ['label' => 'Nama lengkap', 'value' => $user->nama],
                        ['label' => 'Nama pengguna', 'value' => $user->username],
                        ['label' => 'Email', 'value' => $user->email],
                        ['label' => 'Peran', 'value' => $roleLabel],
                    ] as $row)
                        <div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-[10rem_minmax(0,1fr)] sm:gap-4 sm:px-6">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $row['label'] }}</dt>
                            <dd class="break-words text-sm font-medium text-gray-950 dark:text-white">
                                {{ $row['value'] ?: '-' }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <section class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900 sm:p-6">
                <div>
                    <h3 class="font-semibold text-gray-950 dark:text-white">Foto Profil</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gunakan foto JPG, PNG, atau WEBP hingga 1 MB.</p>
                </div>

                <form
                    wire:submit="save"
                    class="mt-5 space-y-4"
                    x-data="{ uploading: false, uploadError: false, preview: null, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true; uploadError = false; progress = 0"
                    x-on:livewire-upload-progress.window="progress = $event.detail.progress"
                    x-on:livewire-upload-finish="uploading = false; progress = 100"
                    x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false; uploadError = true"
                    x-on:change="$event.target.matches('[data-profile-photo-input]') && $event.target.files[0] && (preview = URL.createObjectURL($event.target.files[0]))"
                >
                    <input
                        id="profile-photo-upload"
                        data-profile-photo-input
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        wire:model="profilePhotoUpload"
                        class="peer sr-only"
                    >
                    <label
                        for="profile-photo-upload"
                        class="flex min-h-11 w-full cursor-pointer items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-600 peer-focus-visible:ring-offset-2 dark:border-white/20 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10"
                    >
                        <x-filament::icon icon="heroicon-m-arrow-up-tray" class="size-4" />
                        Pilih Foto
                    </label>

                    <div x-cloak x-show="preview" class="flex min-w-0 items-center gap-3 rounded-lg border border-gray-200 p-3 dark:border-white/10">
                        <img :src="preview" alt="Pratinjau foto profil" class="size-12 shrink-0 rounded-full object-cover">
                        <span class="min-w-0 break-words text-sm text-gray-600 dark:text-gray-300">Pratinjau foto baru</span>
                    </div>

                    @if ($avatarUrl)
                        <div class="flex min-w-0 items-center gap-3 rounded-lg border border-gray-200 p-3 dark:border-white/10">
                            <img src="{{ $avatarUrl }}" alt="Foto profil saat ini" class="size-12 shrink-0 rounded-full object-cover">
                            <span class="min-w-0 break-words text-sm text-gray-600 dark:text-gray-300">Foto profil saat ini</span>
                        </div>
                    @endif

                    <p x-cloak x-show="uploading" role="status" class="text-sm text-primary-700 dark:text-primary-300">
                        Mengunggah foto… <span x-text="progress + '%'">0%</span>
                    </p>
                    <p x-cloak x-show="uploadError" role="alert" class="text-sm text-danger-600 dark:text-danger-400">
                        Upload gagal. Periksa format dan ukuran foto, lalu coba lagi.
                    </p>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        @if ($avatarUrl)
                            <x-filament::button
                                class="w-full justify-center sm:w-auto"
                                type="button"
                                wire:click="resetPhoto"
                                wire:confirm="Reset foto profil ke inisial nama?"
                                icon="heroicon-m-arrow-uturn-left"
                                color="gray"
                                x-bind:disabled="uploading"
                            >
                                Reset Foto
                            </x-filament::button>
                        @endif
                        <x-filament::button
                            class="w-full justify-center sm:w-auto"
                            type="submit"
                            icon="heroicon-m-check"
                            color="primary"
                            x-bind:disabled="uploading"
                        >
                            Simpan Foto
                        </x-filament::button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-filament-panels::page>
