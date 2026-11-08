<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Resources\BankAccounts\BankAccountResource;
use App\Filament\Admin\Resources\Barangs\BarangResource;
use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Filament\Admin\Resources\KategoriBarangs\KategoriBarangResource;
use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use App\Filament\Admin\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Admin\Resources\StockGudangs\StockGudangResource;
use App\Filament\Admin\Resources\SuratJalans\SuratJalanResource;
use App\Filament\Admin\Resources\TermOfPayments\TermOfPaymentResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Admin\Resources\Vendors\VendorResource;
use App\Filament\Admin\Pages\ReportsPage;
use App\Filament\Admin\Pages\Dashboard;
use App\Filament\Admin\Pages\ProfilePage;
use App\Filament\Admin\Widgets\FinancialTrendChart;
use App\Filament\Admin\Widgets\LatestPenawaransTable;
use App\Filament\Admin\Widgets\LatestSuratJalansTable;
use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\StockOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Actions\Action;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        Table::configureUsing(fn (Table $table): Table => $table->striped());
        TextColumn::configureUsing(fn (TextColumn $column): TextColumn => $column->toggleable());

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandName('KJM')
            ->brandLogo(asset('assets/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->maxContentWidth(Width::Full)
            ->favicon(asset('assets/logo.png'))
            ->font('Inter')
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                'profile' => fn (Action $action): Action => $action
                    ->label('Profil Saya')
                    ->url(ProfilePage::getUrl())
                    ->icon('heroicon-o-user-circle'),
            ])
            ->colors([
                'primary' => Color::Orange,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        NavigationItem::make('Dashboard')
                            ->icon('heroicon-o-home')
                            ->activeIcon('heroicon-s-home')
                            ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                            ->url(fn (): string => Dashboard::getUrl()),
                        NavigationItem::make('Profil Saya')
                            ->icon('heroicon-o-user-circle')
                            ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.profile'))
                            ->url(fn (): string => ProfilePage::getUrl()),
                    ])
                    ->groups([
                        NavigationGroup::make('Transaksi')
                            ->items([
                                NavigationItem::make('Penawaran')
                                    ->icon('heroicon-o-document-text')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.penawarans.*'))
                                    ->url(fn (): string => PenawaranResource::getUrl()),
                                NavigationItem::make('Purchase Order')
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.purchase-orders.*'))
                                    ->url(fn (): string => PurchaseOrderResource::getUrl()),
                                NavigationItem::make('Surat Jalan')
                                    ->icon('heroicon-o-truck')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.surat-jalans.*'))
                                    ->url(fn (): string => SuratJalanResource::getUrl()),
                                NavigationItem::make('Invoice')
                                    ->icon('heroicon-o-receipt-percent')
                                    ->badge('Finance')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.invoices.*'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('invoices', 'view') ?? false)
                                    ->url(fn (): string => InvoiceResource::getUrl()),
                            ]),
                        NavigationGroup::make('History')
                            ->items([
                                NavigationItem::make('History Penawaran')
                                    ->icon('heroicon-o-clock')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.penawarans.history'))
                                    ->url(fn (): string => PenawaranResource::getUrl('history')),
                                NavigationItem::make('History Purchase Order')
                                    ->icon('heroicon-o-clock')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.purchase-orders.history'))
                                    ->url(fn (): string => PurchaseOrderResource::getUrl('history')),
                                NavigationItem::make('History Surat Jalan')
                                    ->icon('heroicon-o-clock')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.surat-jalans.history'))
                                    ->url(fn (): string => SuratJalanResource::getUrl('history')),
                                NavigationItem::make('History Invoice')
                                    ->icon('heroicon-o-clock')
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('invoices', 'view') ?? false)
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.invoices.history'))
                                    ->url(fn (): string => InvoiceResource::getUrl('history')),
                            ]),
                        NavigationGroup::make('Inventory')
                            ->items([
                                NavigationItem::make('Data Barang')
                                    ->icon('heroicon-o-archive-box')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.barangs.*'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('barangs', 'view') ?? false)
                                    ->url(fn (): string => BarangResource::getUrl()),
                                NavigationItem::make('Stock Gudang')
                                    ->icon('heroicon-o-cube')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.stock-gudangs.*'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('stock_gudangs', 'view') ?? false)
                                    ->url(fn (): string => StockGudangResource::getUrl()),
                                NavigationItem::make('Kategori Barang')
                                    ->icon('heroicon-o-tag')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.kategori-barangs.*'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('kategori_barangs', 'view') ?? false)
                                    ->url(fn (): string => KategoriBarangResource::getUrl()),
                                NavigationItem::make('Customer')
                                    ->icon('heroicon-o-building-office-2')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.customers.*'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('customers', 'view') ?? false)
                                    ->url(fn (): string => CustomerResource::getUrl()),
                                NavigationItem::make('Vendor')
                                    ->icon('heroicon-o-building-storefront')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.vendors.*'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('vendors', 'view') ?? false)
                                    ->url(fn (): string => VendorResource::getUrl()),
                            ]),
                        NavigationGroup::make('Admin')
                            ->items([
                                NavigationItem::make('Laporan')
                                    ->icon('heroicon-o-chart-bar')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.reports'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('reports', 'view') ?? false)
                                    ->url(fn (): string => ReportsPage::getUrl()),
                                NavigationItem::make('Data Bank')
                                    ->icon('heroicon-o-building-library')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.bank-accounts.*'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('bank_accounts', 'view') ?? false)
                                    ->url(fn (): string => BankAccountResource::getUrl()),
                                NavigationItem::make('Term of Payment')
                                    ->icon('heroicon-o-credit-card')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.term-of-payments.*'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('term_of_payments', 'view') ?? false)
                                    ->url(fn (): string => TermOfPaymentResource::getUrl()),
                                NavigationItem::make('Kelola User & Akses')
                                    ->icon('heroicon-o-users')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.users.*'))
                                    ->visible(fn (): bool => auth()->user()?->hasAccess('users', 'view') ?? false)
                                    ->url(fn (): string => UserResource::getUrl()),
                            ]),
                    ]);
            })
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                StatsOverview::class,
                StockOverview::class,
                FinancialTrendChart::class,
                LatestPenawaransTable::class,
                LatestSuratJalansTable::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                    <style>
                        .barang-picker-options .fi-fo-checkbox-list-option-ctn{min-width:0;border:1px solid rgb(229 231 235 / .9);border-radius:.625rem;background:rgb(249 250 251 / .75);padding:.55rem .7rem;transition:background-color .15s,border-color .15s}
                        .barang-picker-options .fi-fo-checkbox-list-option-ctn:hover{border-color:rgb(156 163 175);background:rgb(243 244 246)}
                        .dark .barang-picker-options .fi-fo-checkbox-list-option-ctn{border-color:rgb(55 65 81);background:rgb(17 24 39 / .55)}
                        .dark .barang-picker-options .fi-fo-checkbox-list-option-ctn:hover{border-color:rgb(107 114 128);background:rgb(31 41 55)}
                        .barang-picker-options .fi-fo-checkbox-list-option{align-items:flex-start}
                        .barang-picker-options:focus-visible{outline:2px solid var(--primary-500);outline-offset:2px}
                        @media(max-width:767px){.barang-picker-options .fi-fo-checkbox-list-option-ctn{padding:.45rem .55rem}}
                    </style>
                    <script>
                        (() => {
                            const path = window.location.pathname;
                            if (!path.startsWith('/admin/')) return;

                            const draftKey = `filament-form-draft:${path}`;
                            const fieldSelector = '[wire\\:model], [wire\\:model\\.live]';
                            const fields = () => Array.from(document.querySelectorAll(fieldSelector))
                                .filter((field) => field.matches('input, select, textarea') && field.type !== 'file');
                            const fieldKey = (field) => field.getAttribute('wire:model') || field.getAttribute('wire:model.live');

                            const saveDraft = () => {
                                const draft = {};
                                fields().forEach((field) => {
                                    const key = fieldKey(field);
                                    if (!key) return;
                                    draft[key] = field.type === 'checkbox' || field.type === 'radio'
                                        ? (field.checked ? field.value : null)
                                        : field.value;
                                });
                                try { localStorage.setItem(draftKey, JSON.stringify(draft)); } catch (_) {}
                            };

                            const restoreDraft = () => {
                                let draft;
                                try { draft = JSON.parse(localStorage.getItem(draftKey) || 'null'); } catch (_) { return; }
                                if (!draft) return;
                                fields().forEach((field) => {
                                    const key = fieldKey(field);
                                    if (!key || !(key in draft)) return;
                                    if (field.type === 'checkbox' || field.type === 'radio') {
                                        field.checked = draft[key] !== null && field.value === draft[key];
                                    } else {
                                        field.value = draft[key] ?? '';
                                    }
                                    field.dispatchEvent(new Event('input', { bubbles: true }));
                                    field.dispatchEvent(new Event('change', { bubbles: true }));
                                });
                            };

                            let saveTimer;
                            document.addEventListener('input', (event) => {
                                if (event.target.matches(fieldSelector)) {
                                    clearTimeout(saveTimer);
                                    saveTimer = setTimeout(saveDraft, 200);
                                }
                            }, true);
                            document.addEventListener('change', (event) => {
                                if (event.target.matches(fieldSelector)) saveDraft();
                            }, true);

                            let livewireHookRegistered = false;
                            const clearDraftAfterSave = () => {
                                if (livewireHookRegistered) return;
                                if (!window.Livewire?.hook) return;
                                livewireHookRegistered = true;
                                window.Livewire.hook('commit', ({ succeed }) => {
                                    succeed(({ effects }) => {
                                        if (effects?.redirect) {
                                            try { localStorage.removeItem(draftKey); } catch (_) {}
                                        }
                                    });
                                });
                            };

                            const addResetButton = () => {
                                if (!/\/create\/?$/.test(path) || document.querySelector('[data-reset-form-draft]')) return;
                                const form = document.querySelector('form[wire\\:submit]');
                                if (!form) return;
                                const button = document.createElement('button');
                                button.type = 'button';
                                button.dataset.resetFormDraft = 'true';
                                button.className = 'fi-btn fi-btn-size-md fi-btn-color-gray fi-outlined';
                                button.textContent = 'Reset Isi Data';
                                button.addEventListener('click', () => {
                                    try { localStorage.removeItem(draftKey); } catch (_) {}
                                    window.location.reload();
                                });
                                const actions = form.querySelector('.fi-form-actions') || form;
                                actions.append(button);
                            };

                            document.addEventListener('DOMContentLoaded', () => {
                                restoreDraft();
                                addResetButton();
                                clearDraftAfterSave();
                            });
                            document.addEventListener('livewire:init', clearDraftAfterSave, { once: true });
                            if (document.readyState !== 'loading') {
                                restoreDraft();
                                addResetButton();
                                clearDraftAfterSave();
                            }
                            document.addEventListener('click', (event) => {
                                const link = event.target.closest('a[href]');
                                if (!link) return;
                                if (!/\/(create|edit)\/?$/.test(window.location.pathname)) return;
                                if (!link.closest('.fi-sidebar')) return;
                                if (link.target === '_blank' || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
                                const target = new URL(link.href, window.location.href);
                                if (target.origin === window.location.origin && target.pathname.startsWith('/admin/') && !/\/print\/?$/.test(target.pathname) && !link.closest('form')) {
                                    event.preventDefault();
                                    event.stopImmediatePropagation();
                                    window.open(target.href, '_blank', 'noopener');
                                }
                            }, true);
                        })();
                    </script>
                    HTML),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
