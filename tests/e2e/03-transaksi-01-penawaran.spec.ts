import { test, expect } from '@playwright/test';
import {
    E2E_PREFIX,
    fillForm,
    openPage,
    selectOption,
    selectOptionFromLocator,
    submitCreate,
    uploadTestPdf,
    waitLivewire,
} from './helpers/filament';

const offerNumber = `${E2E_PREFIX}-Offer`;
const customerName = `${E2E_PREFIX}-Customer-Edited`;
const itemName = `${E2E_PREFIX}-Product-Edited`;
const termName = `${E2E_PREFIX}-Payment-Term-Edited`;

test('create an offer from master data, print it, and verify history UI', async ({ page }) => {
    await openPage(page, '/admin/penawarans/create');
    await fillForm(page, {
        no_penawaran: offerNumber,
        nama_customer: customerName,
        perihal: `${E2E_PREFIX} quotation`,
    });
    await selectOption(page, 'customer_id', customerName, true);
    await selectOption(page, 'term_of_payment', termName);

    await selectOptionFromLocator(
        page.locator('button[id^="form.penawaran_items."][id$=".barang_id"]'),
        itemName,
        true,
    );
    await page.locator('input[id^="form.penawaran_items."][id$=".qty"]').first().fill('2');
    await page.locator('input[id^="form.penawaran_items."][id$=".harga_satuan"]').first().fill('1000');
    await uploadTestPdf(page, `${offerNumber}.pdf`);

    await submitCreate(page);
    await expect(page).toHaveURL(/\/admin\/penawarans\/\d+$/);
    await expect(page.getByText(offerNumber, { exact: true })).toBeVisible();
    await expect(page.getByText(customerName, { exact: true })).toBeVisible();

    await page.getByRole('button', { name: 'Cetak PDF' }).click();
    const printFrame = page.frameLocator('iframe[title="Pratinjau cetak"]');
    await expect(printFrame.locator('body')).toContainText(offerNumber);
    await expect(printFrame.locator('body')).toContainText(customerName);
    await page.getByRole('button', { name: 'Tutup' }).last().click();

    await openPage(page, '/admin/penawarans');
    const row = page.getByRole('row').filter({ hasText: offerNumber });
    await expect(row).toBeVisible();
    await openPage(page, '/admin/penawarans/history');
    await expect(page.getByRole('heading', { name: /Riwayat|History|Penawaran/i })).toBeVisible();
});

test('search offers by document number', async ({ page }) => {
    await openPage(page, '/admin/penawarans');
    await waitLivewire(page, () => page.getByRole('searchbox').first().fill(offerNumber));
    await expect(page.getByRole('row').filter({ hasText: offerNumber })).toBeVisible();
});
