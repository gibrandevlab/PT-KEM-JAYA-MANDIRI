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
use Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('KJM')
            ->brandLogo(asset('assets/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('assets/logo.png'))
            ->font('Inter')
            ->sidebarCollapsibleOnDesktop()
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
                                NavigationItem::make('Laporan Tahunan')
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
                //
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                    <script>
                        document.addEventListener('click', (event) => {
                            const link = event.target.closest('a[href]');
                            if (!link) return;

                            const target = new URL(link.href, window.location.href);
                            if (target.origin !== window.location.origin) return;

                            if (/^\/admin\/.+\/print\/?$/.test(target.pathname)) {
                                event.preventDefault();
                                event.stopImmediatePropagation();
                                window.open(target.href, 'kjm-print-preview', 'popup=yes,width=1100,height=850,scrollbars=yes,resizable=yes');
                                return;
                            }

                            const isCreateForm = /^\/admin\/.+\/create\/?$/.test(window.location.pathname);
                            const clickedSidebarLink = link.closest('aside, [class*="sidebar"]');

                            if (isCreateForm && clickedSidebarLink && target.pathname.startsWith('/admin/')) {
                                event.preventDefault();
                                event.stopImmediatePropagation();
                                window.open(target.href, '_blank', 'noopener');
                            }
                        }, true);
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
