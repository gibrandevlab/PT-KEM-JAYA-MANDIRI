<x-filament-panels::page>
    @php
        $isAdmin = $user->role === 'admin';
        $roleLabel = $isAdmin ? 'Admin / Finance' : 'Pekerja';
        $avatarUrl = $user->getFilamentAvatarUrl();
        $initial = mb_strtoupper(mb_substr($user->nama ?: $user->username, 0, 1));
    @endphp

    <div class="mx-auto w-full max-w-6xl space-y-6 py-2">
        {{-- Header card --}}
        <section class="overflow-hidden rounded-3xl bg-white shadow-xl shadow-amber-900/5 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:shadow-black/30 dark:ring-white/10">
            {{-- Cover --}}
            <div class="relative h-40 bg-gradient-to-br from-amber-700 via-amber-500 to-yellow-300 sm:h-48">
                <div class="absolute inset-0 opacity-30 [background-image:radial-gradient(circle_at_20%_20%,white_0,transparent_35%),radial-gradient(circle_at_80%_0%,white_0,transparent_30%)]"></div>
                <div class="absolute -right-10 -top-10 size-48 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-16 left-1/3 size-56 rounded-full bg-amber-900/20 blur-3xl"></div>
            </div>

            <div class="relative px-6 pb-8 sm:px-10">
                {{-- Avatar + name --}}
                <div class="-mt-14 flex flex-col items-center gap-4 sm:-mt-16 sm:flex-row sm:items-end sm:gap-6">
                    <div class="relative shrink-0">
                        <div class="grid size-28 place-items-center overflow-hidden rounded-full bg-gray-800 text-4xl font-extrabold text-white shadow-lg ring-4 ring-white dark:ring-gray-900 sm:size-32">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Foto profil {{ $user->nama }}" class="size-full object-cover">
                            @else
                                <span class="bg-gradient-to-br from-amber-400 to-amber-600 bg-clip-text text-transparent">{{ $initial }}</span>
                            @endif
                        </div>
                        <span @class([
                            'absolute bottom-2 right-2 size-5 rounded-full ring-4 ring-white dark:ring-gray-900',
                            'bg-emerald-500' => $user->aktif,
                            'bg-gray-400' => ! $user->aktif,
                        ])></span>
                    </div>

                    <div class="min-w-0 flex-1 text-center sm:pb-2 sm:text-left">
                        <h2 class="truncate text-2xl font-extrabold tracking-tight text-gray-950 sm:text-3xl dark:text-white">
                            {{ $user->nama }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">&#64;{{ $user->username }}</p>

                        <div class="mt-3 flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-400/20">
                                <x-filament::icon icon="heroicon-m-shield-check" class="size-4" />
                                {{ $roleLabel }}
                            </span>
                            <span @class([
                                'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset',
                                'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-400/20' => $user->aktif,
                                'bg-gray-100 text-gray-600 ring-gray-500/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-400/20' => ! $user->aktif,
                            ])>
                                <span @class(['size-1.5 rounded-full', 'bg-emerald-500' => $user->aktif, 'bg-gray-400' => ! $user->aktif])></span>
                                {{ $user->aktif ? 'Akun aktif' : 'Akun nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <div class="mt-10 grid gap-6 xl:grid-cols-2">
                    {{-- Info --}}
                    <div class="min-w-0 space-y-3">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Informasi Akun</h3>

                        @foreach ([
                            ['icon' => 'heroicon-o-user', 'label' => 'Nama pengguna', 'value' => $user->username],
                            ['icon' => 'heroicon-o-envelope', 'label' => 'Email', 'value' => $user->email],
                            ['icon' => 'heroicon-o-identification', 'label' => 'Peran', 'value' => $roleLabel],
                        ] as $row)
                            <div class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-gray-50/70 p-4 transition hover:border-amber-300 hover:bg-amber-50/60 hover:shadow-sm dark:border-white/10 dark:bg-white/5 dark:hover:border-amber-400/40 dark:hover:bg-amber-400/5">
                                <div class="grid size-11 shrink-0 place-items-center rounded-xl bg-white text-amber-600 shadow-sm ring-1 ring-gray-950/5 transition group-hover:bg-amber-500 group-hover:text-white dark:bg-gray-800 dark:text-amber-400 dark:ring-white/10 dark:group-hover:bg-amber-500 dark:group-hover:text-white">
                                    <x-filament::icon :icon="$row['icon']" class="size-5" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $row['label'] }}</div>
                                    <div class="mt-0.5 break-words font-semibold text-gray-950 dark:text-white">{{ $row['value'] ?: '-' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Upload --}}
                    <div class="min-w-0">
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-800/40">
                            <div class="mb-4 flex items-center gap-3">
                                <div class="grid size-10 place-items-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400">
                                    <x-filament::icon icon="heroicon-o-camera" class="size-5" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-950 dark:text-white">Foto Profil</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Unggah foto agar akun mudah dikenali.</p>
                                </div>
                            </div>

                            <form wire:submit="save" class="space-y-4">
                                {{ $this->form }}

                                <div class="flex justify-end">
                                    <x-filament::button type="submit" icon="heroicon-m-arrow-up-tray" color="primary">
                                        Simpan Foto
                                    </x-filament::button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
