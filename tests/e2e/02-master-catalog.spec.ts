import { test, expect } from '@playwright/test';
import {
    E2E_PREFIX,
    fillForm,
    openPage,
    recordIdFromLink,
    selectOption,
    submitCreate,
    submitEdit,
} from './helpers/filament';

test.describe.configure({ mode: 'serial' });

const categoryName = `${E2E_PREFIX}-Category`;
const editedCategoryName = `${categoryName}-Edited`;
const itemName = `${E2E_PREFIX}-Product`;
const editedItemName = `${itemName}-Edited`;
let categoryId: string;
let itemId: string;

test('create, inspect, edit, and search a category through the UI', async ({ page }) => {
    await openPage(page, '/admin/kategori-barangs');
    await page.getByRole('link', { name: /Buat kategori/i }).click();
    await expect(page).toHaveURL(/\/admin\/kategori-barangs\/create$/);

    await page.getByRole('button', { name: 'Buat', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/kategori-barangs\/create$/);
    await expect(page.locator('[id="form.nama_kategori"]')).toHaveJSProperty('required', true);

    await fillForm(page, { nama_kategori: categoryName });
    await submitCreate(page);
    await expect(page.getByText(categoryName, { exact: true })).toBeVisible();

    await openPage(page, '/admin/kategori-barangs');
    const search = page.getByRole('searchbox').first();
    await search.fill(categoryName);
    await expect(page.getByRole('row').filter({ hasText: categoryName })).toBeVisible();
    categoryId = await recordIdFromLink(page, 'kategori-barangs', categoryName);

    await openPage(page, `/admin/kategori-barangs/${categoryId}`);
    await expect(page.getByRole('heading', { name: /Detail Kategori Barang|Kategori Barang/ })).toBeVisible();

    await openPage(page, `/admin/kategori-barangs/${categoryId}/edit`);
    await fillForm(page, { nama_kategori: editedCategoryName });
    await submitEdit(page);
    await expect(page.locator('[id="form.nama_kategori"]')).toHaveValue(editedCategoryName);

    await openPage(page, '/admin/kategori-barangs');
    await page.getByRole('searchbox').first().fill(editedCategoryName);
    await expect(page.getByRole('row').filter({ hasText: editedCategoryName })).toBeVisible();
});

test('create, inspect, edit, and search a related product through the UI', async ({ page }) => {
    await openPage(page, '/admin/barangs/create');
    await selectOption(page, 'kategori_id', editedCategoryName, true);
    await fillForm(page, {
        nama_barang: itemName,
        satuan: 'Pcs',
        stok: '10',
        stok_minimum: '1',
        harga_satuan: '15000',
        harga_beli: '10000',
        tahun: '2026',
        deskripsi: `${E2E_PREFIX} product fixture`,
    });
    await submitCreate(page);
    await expect(page.getByText(itemName, { exact: true })).toBeVisible();

    await openPage(page, '/admin/barangs');
    const search = page.getByRole('searchbox').first();
    await search.fill(itemName);
    const row = page.getByRole('row').filter({ hasText: itemName });
    await expect(row).toBeVisible();
    itemId = await recordIdFromLink(page, 'barangs', itemName);

    await openPage(page, `/admin/barangs/${itemId}`);
    await expect(page.getByText(itemName, { exact: true })).toBeVisible();

    await openPage(page, `/admin/barangs/${itemId}/edit`);
    await fillForm(page, { nama_barang: editedItemName });
    await submitEdit(page);
    await expect(page.locator('[id="form.nama_barang"]')).toHaveValue(editedItemName);

    await openPage(page, '/admin/barangs');
    await page.getByRole('searchbox').first().fill(editedItemName);
    await expect(page.getByRole('row').filter({ hasText: editedItemName })).toBeVisible();
});
