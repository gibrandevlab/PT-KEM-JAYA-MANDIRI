import { test, expect } from '@playwright/test';
import {
    E2E_PREFIX,
    fillForm,
    openPage,
    selectOption,
    submitCreate,
    deleteRowFromTable,
    waitLivewire,
} from './helpers/filament';

test.describe.configure({ mode: 'serial' });

const productName = `${E2E_PREFIX}-Product-Edited`;

test('create an outbound warehouse movement, verify stock, restore it, and clean movement rows', async ({ page }) => {
    await openPage(page, `/admin/barangs`);
    await page.getByRole('searchbox').first().fill(productName);
    const productRow = page.getByRole('row').filter({ hasText: productName });
    await expect(productRow).toBeVisible();
    const productHref = await productRow.locator('a[href*="/admin/barangs/"]').first().getAttribute('href');
    const productId = productHref?.match(/\/barangs\/(\d+)/)?.[1];
    if (!productId) {
        throw new Error(`Expected a product record link, got ${productHref ?? '(missing href)'}`);
    }

    await openPage(page, `/admin/barangs/${productId}/edit`);
    const originalStock = await page.locator('[id="form.stok"]').inputValue();
    const originalStockNumber = Number(originalStock);
    expect(originalStockNumber).toBeGreaterThanOrEqual(2);

    let movementAttempted = false;

    try {
        await openPage(page, '/admin/stock-gudangs/create');
        await selectOption(page, 'barang_id', productName, true);
        await fillForm(page, {
            barang_keluar: '2',
            stok_update: '0',
            pic: `${E2E_PREFIX}-Warehouse-PIC`,
            keterangan: `${E2E_PREFIX} outbound movement`,
        });
        movementAttempted = true;
        await submitCreate(page);
        await expect(page.getByText(productName, { exact: true })).toBeVisible();

        await openPage(page, `/admin/barangs/${productId}/edit`);
        await expect.poll(async () => Number(await page.locator('[id="form.stok"]').inputValue()))
            .toBe(originalStockNumber - 2);

        await openPage(page, '/admin/stock-gudangs/create');
        await selectOption(page, 'barang_id', productName, true);
        await fillForm(page, {
            barang_keluar: '0',
            stok_update: `${originalStockNumber}`,
            pic: `${E2E_PREFIX}-Warehouse-PIC`,
            keterangan: `${E2E_PREFIX} restore movement`,
        });
        await submitCreate(page);
        movementAttempted = false;

        await openPage(page, `/admin/barangs/${productId}/edit`);
        await expect.poll(async () => Number(await page.locator('[id="form.stok"]').inputValue()))
            .toBe(originalStockNumber);
    } finally {
        if (movementAttempted) {
            await openPage(page, '/admin/stock-gudangs/create');
            await selectOption(page, 'barang_id', productName, true);
            await fillForm(page, {
                barang_keluar: '0',
                stok_update: `${originalStockNumber}`,
                pic: `${E2E_PREFIX}-Warehouse-PIC`,
                keterangan: `${E2E_PREFIX} restore movement`,
            });
            await submitCreate(page);
        }

        await openPage(page, '/admin/stock-gudangs');
        await page.getByRole('searchbox').first().fill(productName);
        const movements = page.getByRole('row').filter({ hasText: productName });

        while (await movements.count() > 0) {
            await deleteRowFromTable(page, productName);
        }
    }
});

test('inspect an existing stock log without mutating audit records', async ({ page }) => {
    await openPage(page, '/admin/stok-logs');
    await expect(page.getByRole('table')).toBeVisible();
    await waitLivewire(page, () => page.getByRole('searchbox').first().fill(E2E_PREFIX));
    const logRow = page.getByRole('row').filter({ hasText: E2E_PREFIX }).first();
    await expect(logRow).toBeVisible();
    await logRow.getByRole('button', { name: /Aksi|Tindakan|Actions/i }).click();
    await page.getByRole('menuitem', { name: 'Lihat', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/stok-logs\/\d+$/);
    await expect(page.getByRole('main')).toContainText(E2E_PREFIX);
    await expect(page.getByRole('main')).toContainText(/Stok Sebelum|Stok Sesudah|Jumlah/i);
});
