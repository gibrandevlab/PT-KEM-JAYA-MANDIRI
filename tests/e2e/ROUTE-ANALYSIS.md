# Route and form analysis

Inventory generated from `php artisan route:list` (90 route entries; Filament 5.11.1 and Laravel 13.40.0 are installed). The test plan excludes framework internals: `_boost/*`, `livewire-*/*`, `storage/*`, Filament export/import download routes, and `up`. Filament create/edit/delete actions are Livewire UI actions rather than standalone application routes; tests must submit them through their visible controls.

## Application routes

| Route (method) | Purpose and fields | Dependencies | Browser scenarios |
|---|---|---|---|
| `GET /` | Redirect to `/admin` when signed in, otherwise `/login`. | Authentication state. | Visit signed out and signed in; assert destination. |
| `GET /login` | Username-or-email and password login form. | Active user. | Empty/invalid form, username and email login, successful redirect. |
| `POST /login` | Login form submission; `username` accepts username or email; password required. | Active user credentials. | Submit through the form only; assert error or dashboard. |
| `GET|POST /logout` | Session logout endpoint. | Signed-in user. | Use the profile menu's **Keluar** action, then verify protected redirect. |
| `GET /admin` | Dashboard and inventory/transaction widgets. | Authenticated user with dashboard access; existing rows are optional. | Check widgets and page errors. |
| `GET /admin/profile` | Current-user profile page; photo is optional PNG/JPG/WEBP, max 1 MB. **Simpan Foto** persists it. | Authenticated user; use an AUTOTEST user so existing profile data is not changed. | Upload valid image, assert success notification and rendered profile. |
| `GET /admin/reports` | Report page; query filters: `jenis` (`penjualan`, `penawaran`, `pembelian`, `stok`), `mode` (`tahun`, `bulan`, `bulan_range`, `rentang`), year, month(s), date range, and free-text `q`. | Authenticated user with `reports.view`; source rows optional. | Change filters and submit the GET form, search, assert period/table. |
| `GET /admin/reports/export` | Filtered spreadsheet download; query has the same report filters. | Authenticated user; valid `jenis`. | Click **Ekspor Excel**, assert browser download and filename. |
| `GET /admin/reports/print` | Printable report; query has the same report filters. | Authenticated user; valid `jenis`. | Click **Cetak PDF**, verify the opened tab's title and report content. |
| `GET /admin/kategori-barangs` | Category list; searchable/sortable `nama_kategori`; pagination 15/30/50. | None. | Search, sort, paginate, open create and row actions. |
| `GET /admin/kategori-barangs/create` | Create form: `nama_kategori` text, required. | None; category must precede Barang. | Empty required validation, create, assert row/toast. |
| `GET /admin/kategori-barangs/{record}` | Category detail route. | Existing category. | Open detail and verify the category name. |
| `GET /admin/kategori-barangs/{record}/edit` | Edit `nama_kategori`. | Existing category; deleting a category used by goods is rejected. | Change name, assert persistence, delete only after child goods. |
| `GET /admin/barangs` | Goods list; searchable/sortable code, name, category, unit, year, and price; pagination. | Category data. | Search, sort, paginate, inspect stock-log filter link. |
| `GET /admin/barangs/create` | `kode_barang` required/unique/generated; `nama_barang` required; `kategori_id` required searchable select; `satuan` required (default Pcs); `stok` and `stok_minimum` required nonnegative numbers (default 0); optional `tahun`, nonnegative `harga_satuan`/`harga_beli`, `deskripsi`. | Category. Selecting a category generates its code. | Empty validation, select AUTOTEST category, fill item, assert generated code and saved row. |
| `GET /admin/barangs/{record}` | Goods detail. | Existing goods. | Verify AUTOTEST name and category. |
| `GET /admin/barangs/{record}/edit` | Edit the goods form. | Existing goods/category. | Edit, assert new value; test numeric bounds. |
| `GET /admin/vendors` | Vendor list; searchable/sortable master columns, pagination, row actions. | None. | Search/sort/paginate and CRUD actions. |
| `GET /admin/vendors/create` | Generated unique `kode_vendor`; required `nama_perusahaan`; optional address, city, phone, valid email; `Daftar PIC` repeater with required PIC name and optional telephone. | None; vendor selection can prefill PO. | Create/edit/delete; validate email and duplicate code; select in PO. |
| `GET /admin/vendors/{record}` | Vendor detail. | Existing vendor. | Verify company/PIC content. |
| `GET /admin/vendors/{record}/edit` | Edit vendor and PIC repeater. | Existing vendor. | Edit, assert persistence, remove test vendor after PO. |
| `GET /admin/customers` | Customer list; searchable/sortable columns and bulk delete. | None. | Search, sort, paginate, view/edit and bulk-delete selection. |
| `GET /admin/customers/create` | Generated unique customer code; required company name; required default repeaters for company and warehouse address; optional city, telephone, valid email, NPWP, primary PIC/contact, additional customer and warehouse PICs (repeater names required). | None; customer is reused by offers, shipments, and invoices. | Create valid customer, test missing repeater address/email format, edit and check transaction pickers. |
| `GET /admin/customers/{record}` | Customer detail. | Existing customer. | Verify master data and child PIC/address values. |
| `GET /admin/customers/{record}/edit` | Edit customer, address/PIC repeaters. | Existing customer. | Edit data and confirm it persists. |
| `GET /admin/customer-addresses` | Saved-address list. | Customer. | Search, inspect, edit/delete child only after test transactions. |
| `GET /admin/customer-addresses/create` | `customer_id` numeric required, `label_alamat` required text, `alamat_lengkap` required textarea. | Existing customer ID. | Create/edit/delete; invalid customer ID and empty required fields. |
| `GET /admin/customer-addresses/{record}` | Saved-address detail. | Existing address and customer. | Assert address/customer. |
| `GET /admin/customer-addresses/{record}/edit` | Edit customer/address fields. | Existing address. | Change label/address and verify. |
| `GET /admin/customer-pics` | Customer PIC list. | Customer. | Search, edit/delete test contact. |
| `GET /admin/customer-pics/create` | `customer_id`, `tipe`, and `nama` required (customer ID numeric); optional phone and valid email. | Existing customer. | Create/edit/delete; test numeric and email validation. |
| `GET /admin/customer-pics/{record}` | PIC detail. | Existing PIC/customer. | Assert contact details. |
| `GET /admin/customer-pics/{record}/edit` | Edit PIC fields. | Existing PIC. | Change contact and verify. |
| `GET /admin/bank-accounts` | Bank-account list and row actions. | None. | Search/sort/paginate and CRUD. |
| `GET /admin/bank-accounts/create` | Required `nama_bank`, `no_rekening`, `atas_nama`; `is_aktif` toggle. | None; active accounts are invoice options. | Empty validation, create/edit/delete; assert invoice picker. |
| `GET /admin/bank-accounts/{record}` | Bank-account detail. | Existing bank account. | Assert bank fields. |
| `GET /admin/bank-accounts/{record}/edit` | Edit bank-account fields. | Existing account. | Toggle active and verify. |
| `GET /admin/term-of-payments` | Payment-term list. | None. | Search/sort/paginate and CRUD. |
| `GET /admin/term-of-payments/create` | Required `nama_term` text. | None; used by offer, PO, and invoice. | Empty validation, create/edit/delete, assert transaction option. |
| `GET /admin/term-of-payments/{record}` | Payment-term detail. | Existing term. | Verify term. |
| `GET /admin/term-of-payments/{record}/edit` | Edit `nama_term`. | Existing term. | Change and assert persistence. |
| `GET /admin/users` | User list, status, searchable/sortable columns, view/edit/access/status actions and bulk delete. | Authenticated administrator. | Search, sort, paginate; verify a test user can be managed. |
| `GET /admin/users/create` | Required name, unique valid email and username, password required on create, optional JPG/PNG/WEBP profile image max 1 MB, role (`admin`/`pekerja`), active toggle. | Authenticated administrator. | Empty/invalid/duplicate validation, create only AUTOTEST account. |
| `GET /admin/users/{record}` | User details and profile image. | Existing user. | Verify test account. |
| `GET /admin/users/{record}/edit` | Edit user; blank password retains existing password. | Existing user; cannot delete current account. | Edit, preserve blank password, verify changed value. |
| `GET /admin/users/{record}/access` | Per-feature view/create/update/delete overrides (`Default`, `Izinkan`, `Tolak`). | Existing user; administrator access. | Open, change one setting, save and verify effective access in navigation/actions. |
| `GET /admin/penawarans` | Offer list and actions: view, print, archive/restore, edit, delete; searchable/sortable and paginated. | Customer, goods, payment terms optional. | Search, sort, CRUD, history transition, print preview. |
| `GET /admin/penawarans/create` | Required unique offer number (max 30), date (default today), company name; customer/address/PIC selectors; optional address, contacts, note, SDK, delivery/payment terms, discount/PPN switches; item repeater requires name, quantity >= 0.01, unit, price; required PDF/JPG/JPEG/PNG attachment (max 5 MB). `berlaku_sampai` is read-only and one week after date. | Customer, saved address/PIC, goods, payment terms. | Empty/repeater validation, create via UI, check generated totals and customer/item pickers. |
| `GET /admin/penawarans/{record}` | Offer detail with **Cetak PDF** preview. | Existing offer. | Verify row details and embedded print page. |
| `GET /admin/penawarans/{record}/edit` | Edit offer and items; **Buat Revisi** is available only for `selesai` offers. | Existing offer. | Edit totals, verify archive/history and revision availability. |
| `GET /admin/penawarans/history` | Archived offers; restore/export/delete actions. | Archived offer. | Open history, restore through confirmation, test export for selected rows. |
| `GET /admin/penawarans/{penawaran}/print` | Printable offer. | Authenticated user and existing offer. | Open through **Cetak PDF** UI action; verify printed offer number and customer. |
| `GET /admin/purchase-orders` | PO list; view, print, status, history, edit, delete; pagination/search/sort. | Vendor, goods, payment terms; approved offer is optional source. | Create, status transition, print, history, selection controls. |
| `GET /admin/purchase-orders/create` | Required unique PO number (generated by default), date, category (`Dalam Negeri`/`Luar Negeri`), company; conditional currency; optional vendor, approved offer source, contact/address/delivery/payment terms, note; item repeater requires name, quantity >= 0.01, unit, price >= 0; required PDF/JPG/PNG attachment (max 5 MB), PPN toggle. | Vendor, goods, payment terms; source selector filters to approved offers. | Create standalone/vendor PO; test item totals, source prefill when approved, foreign currency. |
| `GET /admin/purchase-orders/{record}` | PO detail; print, admin status change, and create-invoice action. | Existing PO. | Change status through modal; open print preview and invoice action. |
| `GET /admin/purchase-orders/{record}/edit` | Edit PO and item repeater. | Existing PO; deletion guard can block records with linked documents. | Edit and assert recalculated total. |
| `GET /admin/purchase-orders/history` | Archived POs; restore/export/delete actions. | Archived PO. | Open history, restore and export using UI. |
| `GET /admin/purchase-orders/{purchaseOrder}/print` | Printable PO. | Authenticated user and existing PO. | Open via **Cetak PDF** and verify PO/company text. |
| `GET /admin/surat-jalans` | Shipment list; view, print, status, archive/restore, edit/delete; searchable and paginated. | Eligible PO or offer, customer, goods. | Create shipment, status, history, print. |
| `GET /admin/surat-jalans/create` | Required unique shipment number and date; optional PO/offer/customer references; required recipient name/address; optional city/contact/PO/vehicle/note; at least one item with required name, quantity >= 1, unit. | PO in `diproses`/`selesai`, eligible offer/customer, goods. Choosing PO/offer prefills receiver and items. | Select reference, verify prefill/relationship, submit, inspect stock and linked status behavior. |
| `GET /admin/surat-jalans/{record}` | Shipment detail; print, status, and invoice-from-shipment actions. | Existing shipment. | Change status, print and verify content; open invoice action if eligible. |
| `GET /admin/surat-jalans/{record}/edit` | Edit shipment/items. | Existing shipment. | Update and verify. |
| `GET /admin/surat-jalans/history` | Archived shipments. | Archived shipment. | Restore/delete via confirmation. |
| `GET /admin/surat-jalans/{suratJalan}/print` | Printable shipment. | Authenticated user and existing shipment. | Open through UI and assert shipment/customer/item. |
| `GET /admin/invoices` | Invoice list; view, print, status/payment inline, archive/restore, edit/delete; searchable and paginated. | Customer, goods, payment terms, active bank; PO/SJ optional. | Create, edit, status, history, print, search/sort/page. |
| `GET /admin/invoices/create` | Required unique invoice number, date, invoice type (`Langsung`, `DP`, `Pelunasan`), customer name; optional SJ/PO/customer refs, customer details, tax number, PO/shipment dates/numbers, term, active bank, notes, discount/tax/payment/date; item repeater requires name, qty >= 0.01, unit, price; required PDF/JPG/PNG attachment (max 5 MB). | Customer, goods, bank/terms, PO/SJ. A shipment can have one direct invoice or a DP and one settlement; settlement requires an existing DP. | Create from SJ/PO, verify prefill and duplicate-shipment guard, DP/settlement validation, totals. |
| `GET /admin/invoices/{record}` | Invoice detail; **Cetak PDF** and status action. | Existing invoice. | Status update, print preview, assert invoice/customer. |
| `GET /admin/invoices/{record}/edit` | Edit invoice/items/payment fields. | Existing invoice. | Edit and verify. |
| `GET /admin/invoices/history` | Archived invoice/payment dates and restore/export/delete actions. | Archived invoice. | Open history, set payment dates, restore, export. |
| `GET /admin/invoices/{invoice}/print` | Printable invoice. | Authenticated user and existing invoice. | Open via UI; assert invoice number and customer. |
| `GET /admin/stock-gudangs` | Warehouse movement list; search/sort/page, delete action. | Goods with positive stock. | Search item/movement and verify stock row. |
| `GET /admin/stock-gudangs/create` | Required searchable Barang (positive stock); auto-filled read-only name/category/unit/current stock; nonnegative quantity-out and stock-update (default 0); optional date/PIC/note. Stock update takes precedence over quantity-out. | Goods with stock > 0. | Create outbound movement, assert new stock on Barang form, restore test stock via UI, delete AUTOTEST movement rows. |
| `GET /admin/stok-logs` | Read-only stock-log list; filter by Barang, search/sort/page; row action can edit only `keterangan`. | Existing stock logs. | List/filter and inspect detail; no create/delete routes. |
| `GET /admin/stok-logs/{record}` | Stock-log detail. | Existing log. | Open a test movement log and assert before/after/quantity. |

## Data dependency order

1. Kategori Barang → Barang.
2. Customer → saved customer addresses and PICs; Vendor, Bank, and Term of Payment can be created independently.
3. Penawaran uses the customer, goods, addresses/PICs, and payment terms.
4. PO uses a vendor, goods, payment terms; an offer source is limited to offers whose status is `disetujui`.
5. Surat Jalan accepts only POs with status `diproses` or `selesai` (or an eligible offer) and copies receiver/items.
6. Invoice may use the PO or Surat Jalan; shipment rules restrict duplicate full invoices and DP/settlement combinations.
7. Stock Gudang applies an outbound quantity or an absolute stock update. Invoice, offer, and shipment creation alone do not modify Barang stock.

**Workflow gap found during route/code review:** the user clarified that offer status is no longer used. New offers are still assigned `draft`, and the PO source selector still filters to `disetujui`; the offer table, detail page, edit form, and registered routes expose no approval action. Therefore an AUTOTEST offer is missing from the PO source picker, so the suite asserts the intended offer-source option and reports the current mismatch. PO → Surat Jalan → Invoice can still be tested with a standalone PO.

**Penawaran search defect reproduced in Chromium:** typing into the Penawaran table search invokes `ListPenawarans::updatedTableSearch()`, which calls the missing `resetTablePage()` method and returns HTTP 500. The suite keeps a search regression test for this behavior; cleanup avoids that table search and targets visible AUTOTEST rows directly. Archive/restore actions are not exercised because they share the affected list update path.

**Customer creation bug reproduced in Chromium:** submitting a customer with non-empty inline address repeaters returns HTTP 500 in `CustomerInventorySync::sync()` (`app/Support/CustomerInventorySync.php:34`): Filament repeater keys are strings, but the code adds `1` to the key. The E2E customer fixture leaves those inline repeater values blank and exercises saved addresses through the standalone Customer Address UI instead; the application defect remains unmodified and should be fixed separately.

## E2E verification snapshot

- The latest full run executed 23 tests: 16 passed, 6 failed, and 1 was skipped (4.1 minutes). The failing cases included the unavailable offer source, PO status modal selection, downstream shipment/invoice flows, stock-movement deletion, and cleanup. The PO modal locator has since been narrowed to its named dialog, but the full suite has not been rerun with that change.
- A targeted Penawaran search run now waits for the Livewire response and confirms the HTTP 500 described above; the earlier full run had a false-positive search assertion.
- Targeted cleanup still fails while deleting AUTOTEST purchase orders through the UI. Cleanup is not verified, so AUTOTEST records from these runs may remain. Do not treat the current suite as clean or green.
- Empty/invalid/duplicate validation, sorting, pagination, and full edit/delete coverage are not yet exercised for every resource. The suite currently focuses on UI create flows, selected edit/view flows, transaction print/history, report controls, dashboard/profile, and stock effects.

## Coverage caveats

- `StockGudang` has only list/create registered pages despite edit/view classes being present. `StokLog` has list/detail routes and no create/delete route. Stock-log audit data cannot be removed through the current UI; do not generate permanent log rows during routine runs unless that persistence is accepted.
- `Customer` exposes bulk deletion rather than an individual delete action; related addresses/PICs should be removed before the customer.
- Print and report export are GET destinations, but the suite opens them only by clicking visible Filament/report UI controls.
- Generated identifiers (vendor/customer/goods codes and document numbers) are assigned by the app; prefix user-entered names/notes and keep generated IDs as-is.
