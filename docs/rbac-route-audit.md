# RBAC route audit

Source of defaults: PHP Native pages and `db_kem.sql` role enum (`admin`, `pekerja`).

| Native feature | Laravel route/resource | Default worker access | Audit |
| --- | --- | --- | --- |
| Dashboard | `/admin` | View; finance cards must stay hidden | Present |
| Penawaran | `/admin/penawarans` | View/create; edit own records | Present; ownership scope and policy apply |
| Purchase Order | `/admin/purchase-orders` | View/create | Present |
| Surat Jalan | `/admin/surat-jalans` | View/create | Present |
| Invoice | `/admin/invoices` | Denied | Present; admin default |
| Data Barang | `/admin/barangs` | View only | Present |
| Stock Gudang | `/admin/stock-gudangs` | View only | Present |
| Kategori Barang | `/admin/kategori-barangs` | View only | Present |
| Customer and Vendor | `/admin/customers`, `/admin/vendors` | View only | Present |
| Bank and term pembayaran | `/admin/bank-accounts`, `/admin/term-of-payments` | Denied | Present; admin default |
| Kelola User and RBAC | `/admin/users`, `/admin/manage-access` | Denied | Present; admin-only |
| Customer addresses/PIC, stock logs | Discovered CRUD resources | View only | Present; stricter than Native's admin-only maintenance |
| History | No Laravel route | Denied until implemented | Missing feature |
| Laporan | No Laravel route | Denied until implemented | Missing feature |
| PDF/export routes | No Laravel route | Denied until implemented | Missing feature |

Filament exposes CRUD routes per resource. Model policies authorize view/create/update/delete on server; User resource has explicit admin-only checks. Per-user overrides live in `user_access_overrides`; absent override inherits role defaults from `config/access.php`.

Run `php artisan migrate` to create override table before using the RBAC page. No DB migration was run during this audit.
