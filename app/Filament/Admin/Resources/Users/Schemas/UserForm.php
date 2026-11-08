<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class UserForm
{
    private const ACCESS_GROUPS = [
        'Transaksi & Riwayat' => ['penawarans', 'purchase_orders', 'surat_jalans', 'invoices'],
        'Inventory' => ['barangs', 'stock_gudangs', 'kategori_barangs', 'customers', 'vendors', 'customer_addresses', 'customer_pics', 'stok_logs'],
        'Admin' => ['reports', 'bank_accounts', 'term_of_payments', 'users'],
        'Dashboard' => ['dashboard'],
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->components([
                Section::make('Data Pengguna')
                    ->columns(['default' => 1, 'md' => 2, 'xl' => 3])
                    ->schema([
                        TextInput::make('nama')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(table: 'users', ignoreRecord: true)
                            ->required(),
                        TextInput::make('username')
                            ->unique(table: 'users', ignoreRecord: true)
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText(fn (string $operation): ?string => $operation === 'edit'
                                ? 'Kosongkan bila tidak diubah.'
                                : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => bcrypt($state)),
                        FileUpload::make('foto')
                            ->label('Foto Profil')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('profiles')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(1024)
                            ->helperText('Format JPG, PNG, atau WEBP. Maksimal 1 MB.'),
                        Select::make('role')
                            ->options(['admin' => 'Admin / Finance', 'pekerja' => 'Pekerja'])
                            ->default('pekerja')
                            ->required(),
                        Toggle::make('aktif')
                            ->default(true)
                            ->inline(false)
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function accessOnly(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components(self::accessSections());
    }

    /**
     * Grid akses: 1 kolom fitur + 1 toggle 3-state untuk tiap aksi.
     * Header dan baris memakai jumlah kolom sama supaya sejajar.
     */
    private static function accessSections(): array
    {
        $actions = config('access.actions');
        $features = config('access.features');
        $actionLabels = [
            'view' => 'Lihat',
            'create' => 'Tambah',
            'update' => 'Ubah',
            'delete' => 'Hapus',
        ];
        $columns = count($actions) + 1;

        $components = [
            Section::make('Keterangan')
                ->schema([
                    ToggleButtons::make('legend')
                        ->hiddenLabel()
                        ->dehydrated(false)
                        ->grouped()
                        ->options(['inherit' => 'Default', 'allow' => 'Izinkan', 'deny' => 'Tolak'])
                        ->icons(['inherit' => 'heroicon-m-minus', 'allow' => 'heroicon-m-check', 'deny' => 'heroicon-m-x-mark'])
                        ->colors(['inherit' => 'gray', 'allow' => 'success', 'deny' => 'danger'])
                        ->inline(),
                ])
                ->secondary()
                ->columnSpanFull(),
        ];

        foreach (self::ACCESS_GROUPS as $group => $groupFeatures) {
            $rows = [];
            $rows[] = Grid::make($columns)->schema([
                Text::make('Fitur')->weight(FontWeight::SemiBold)->color('gray'),
                ...collect($actions)
                    ->map(fn (string $action): Text => Text::make($actionLabels[$action] ?? ucfirst($action))
                        ->weight(FontWeight::SemiBold)
                        ->color('gray')
                        ->extraAttributes(['class' => 'text-center']))
                    ->all(),
            ]);

            foreach ($groupFeatures as $feature) {
                if (! isset($features[$feature])) {
                    continue;
                }

                $cells = [Text::make($features[$feature])->weight(FontWeight::Medium)];

                foreach ($actions as $action) {
                    $cells[] = ToggleButtons::make("permissions.{$feature}.{$action}")
                        ->hiddenLabel()
                        ->grouped()
                        ->options(['inherit' => 'Default', 'allow' => 'Izinkan', 'deny' => 'Tolak'])
                        ->icons(['inherit' => 'heroicon-m-minus', 'allow' => 'heroicon-m-check', 'deny' => 'heroicon-m-x-mark'])
                        ->colors(['inherit' => 'gray', 'allow' => 'success', 'deny' => 'danger'])
                        ->hiddenButtonLabels()
                        ->default('inherit')
                        ->required()
                        ->dehydrateStateUsing(fn (?string $state): string => in_array($state, ['inherit', 'allow', 'deny'], true) ? $state : 'inherit');
                }

                $rows[] = Grid::make($columns)
                    ->schema($cells)
                    ->extraAttributes(['class' => 'items-center gap-2 py-1']);
            }

            if (count($rows) === 1) {
                continue;
            }

            $components[] = Section::make($group)
                ->schema($rows)
                ->collapsible()
                ->collapsed()
                ->id($group === 'Transaksi & Riwayat' ? 'hak-akses' : null)
                ->columnSpanFull();
        }

        return $components;
    }
}
