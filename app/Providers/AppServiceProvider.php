<?php

namespace App\Providers;

use App\Models\BankAccount;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerPic;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Models\InvoiceItem;
use App\Models\KategoriBarang;
use App\Models\Penawaran;
use App\Models\PenawaranAttachment;
use App\Models\PenawaranItem;
use App\Models\PoAttachment;
use App\Models\PoCustomer;
use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\StockGudang;
use App\Models\StokLog;
use App\Models\SuratJalan;
use App\Models\SuratJalanItem;
use App\Models\TermOfPayment;
use App\Models\User;
use App\Models\Vendor;
use App\Policies\AccessPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([BankAccount::class, Barang::class, Customer::class, CustomerAddress::class, CustomerPic::class, Invoice::class, InvoiceAttachment::class, InvoiceItem::class, KategoriBarang::class, Penawaran::class, PenawaranAttachment::class, PenawaranItem::class, PoAttachment::class, PoCustomer::class, PoItem::class, PurchaseOrder::class, StockGudang::class, StokLog::class, SuratJalan::class, SuratJalanItem::class, TermOfPayment::class, User::class, Vendor::class] as $model) {
            Gate::policy($model, AccessPolicy::class);
        }
    }
}
