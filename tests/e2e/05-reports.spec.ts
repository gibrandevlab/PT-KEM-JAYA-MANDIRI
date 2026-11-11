import { test, expect } from '@playwright/test';
import { E2E_PREFIX, openPage } from './helpers/filament';

test('filter reports and open export and print from their UI controls', async ({ page }) => {
    await openPage(page, '/admin/reports');
    await expect(page.getByRole('heading', { name: /Laporan Penjualan/ })).toBeVisible();

    const filter = page.locator('#report-filter-form');
    await filter.locator('select[name="jenis"]').selectOption('penawaran');
    await filter.locator('select[name="mode"]').selectOption('rentang');
    await filter.locator('input[name="dari"]').fill('2020-01-01');
    await filter.locator('input[name="sampai"]').fill('2100-12-31');
    await filter.locator('input[name="q"]').fill(E2E_PREFIX);
    await filter.getByRole('button', { name: 'Tampilkan' }).click();

    await expect(page).toHaveURL(/jenis=penawaran/);
    await expect(page.getByRole('heading', { name: /Laporan Penawaran/ })).toBeVisible();
    await expect(page.locator('#report-content')).toContainText(E2E_PREFIX);

    const downloadPromise = page.waitForEvent('download');
    await page.getByRole('link', { name: /Ekspor Excel/ }).click();
    const download = await downloadPromise;
    expect(download.suggestedFilename()).toMatch(/\.(csv|xls|xlsx)$/i);

    const [printPage] = await Promise.all([
        page.context().waitForEvent('page'),
        page.getByRole('link', { name: /Cetak PDF/ }).click(),
    ]);
    await printPage.waitForLoadState('domcontentloaded');
    await expect(printPage.getByRole('heading', { name: /Laporan Penawaran/ })).toBeVisible();
    await expect(printPage.locator('body')).toContainText(E2E_PREFIX);
});
