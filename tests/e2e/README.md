# Browser E2E

Playwright tests exercise the local application through Chromium and its UI. They use the `APP_URL` value in the repository `.env` unless `E2E_BASE_URL` is set. Start the Docker application and database before running the suite.

## Install and run

```powershell
npm install
npx playwright install chromium
$env:E2E_USERNAME = "shiina"
$env:E2E_PASSWORD = "<local test account password>"
npm run test:e2e
```

For interactive debugging, run `npm run test:e2e -- --headed`. Playwright saves login state under the ignored `tests/e2e/.auth/` folder and reuses it for authenticated specs. Screenshots, video, traces, and the HTML report are written to `test-results/` and `playwright-report/`.

The application UI must be reachable at `APP_URL`; this suite does not start Docker or run database seeders. All create, edit, delete, export, and print actions are driven through page controls. Test-created business records use the `AUTOTEST-` prefix.

The full suite runs `cleanup.spec.ts` last. To rerun cleanup separately, remove eligible prefixed test records through the UI:

```powershell
npm run test:e2e:cleanup
```

Cleanup uses the authenticated browser state and deletes only records matching `AUTOTEST-`. Stock logs are an application audit trail and have no delete UI action; the cleanup test leaves them untouched.

## Specs

- `01-auth.spec.ts` — login, failed login, logout, and protected-page redirect.
- `02-master-*.spec.ts` — catalog, customer/vendor, bank/payment, and user/access workflows.
- `03-transaksi-*.spec.ts` — offers, purchase orders, shipments, invoices, history, and print previews.
- `04-stok.spec.ts` — warehouse movement, stock verification/restore, and stock-log viewing.
- `05-reports.spec.ts` — filters, browser download, and print view.
- `06-dashboard-profile.spec.ts` — dashboard widgets and profile upload on an AUTOTEST user.

The route inventory and known workflow constraints are documented in [ROUTE-ANALYSIS.md](./ROUTE-ANALYSIS.md).
