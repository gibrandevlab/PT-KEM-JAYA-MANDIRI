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
const purchaseOrderNumber = `${E2E_PREFIX}-PO`;
const vendorName = `${E2E_PREFIX}-Vendor-Edited`;
const productName = `${E2E_PREFIX}-Product-Edited`;
const termName = `${E2E_PREFIX}-Payment-Term-Edited`;

test('a current offer is available as a PO source when offer status is no longer used', async ({ page }) => {
    await openPage(page, '/admin/purchase-orders/create');
    const source = page.locator('[id="form.penawaran_id"]');
    await source.click();
    await expect(page.getByRole('option', { name: offerNumber, exact: false })).toBeVisible();
});

test('create a vendor PO, change its status through the UI, print it, and inspect history', async ({ page }) => {
    await openPage(page, '/admin/purchase-orders/create');
    await fillForm(page, {
        no_po: purchaseOrderNumber,
        no_po_customer: `${E2E_PREFIX}-Supplier-Reference`,
        catatan: `${E2E_PREFIX} purchase order`,
    });
    await selectOption(page, 'kategori_po', 'Dalam Negeri', true);
    await selectOption(page, 'vendor_id', vendorName, true);
    await selectOption(page, 'term_of_payment', termName);
    await selectOptionFromLocator(
        page.locator('button[id^="form.po_items."][id$=".barang_id"]'),
        productName,
        true,
    );
    await page.locator('input[id^="form.po_items."][id$=".qty"]').first().fill('2');
    await uploadTestPdf(page, `${purchaseOrderNumber}.pdf`);

    await submitCreate(page);
    await expect(page).toHaveURL(/\/admin\/purchase-orders\/\d+$/);
    await expect(page.getByText(purchaseOrderNumber, { exact: true })).toBeVisible();
    await expect(page.getByText(vendorName, { exact: true })).toBeVisible();

    const detailUrl = page.url();
    const recordId = detailUrl.match(/\/purchase-orders\/(\d+)$/)?.[1];
    expect(recordId, `Expected a PO detail page, got ${detailUrl}`).toBeTruthy();

    await page.getByRole('button', { name: 'Ubah Status' }).click();
    await page.getByLabel('Status*').selectOption({ label: 'Diproses' });
    await waitLivewire(page, () => page.getByRole('button', { name: 'Kirim' }).last().click());
    await expect(page.getByText(/diproses/i)).toBeVisible();

    await page.getByRole('button', { name: 'Cetak PDF' }).click();
    const printFrame = page.frameLocator('iframe[title="Pratinjau cetak"]');
    await expect(printFrame.locator('body')).toContainText(purchaseOrderNumber);
    await expect(printFrame.locator('body')).toContainText(vendorName);

    await openPage(page, '/admin/purchase-orders');
    await page.getByRole('searchbox').first().fill(purchaseOrderNumber);
    await expect(page.getByRole('row').filter({ hasText: purchaseOrderNumber })).toBeVisible();
    await openPage(page, '/admin/purchase-orders/history');
    await expect(page.getByRole('heading', { name: /Riwayat|History|Purchase Order/i })).toBeVisible();
});
