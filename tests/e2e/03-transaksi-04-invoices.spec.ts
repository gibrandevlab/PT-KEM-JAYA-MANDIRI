import { test, expect } from '@playwright/test';
import {
    E2E_PREFIX,
    fillForm,
    openPage,
    selectOptionFromLocator,
    submitCreate,
    uploadTestPdf,
    waitLivewire,
} from './helpers/filament';

const shipmentNumber = `${E2E_PREFIX}-SJ`;
const invoiceNumber = `${E2E_PREFIX}-INV`;

test('create an invoice from its shipment action and verify print and history', async ({ page }) => {
    await openPage(page, '/admin/surat-jalans');
    await waitLivewire(page, () => page.getByRole('searchbox').first().fill(shipmentNumber));
    const shipmentRow = page.getByRole('row').filter({ hasText: shipmentNumber });
    await expect(shipmentRow).toBeVisible();
    const shipmentHref = await shipmentRow.locator('a[href*="/admin/surat-jalans/"]').first().getAttribute('href');
    if (!shipmentHref) {
        throw new Error('Shipment row does not contain a link to its detail page.');
    }

    const shipmentId = shipmentHref.match(/\/surat-jalans\/(\d+)/)?.[1];
    expect(shipmentId).toBeTruthy();
    await openPage(page, `/admin/surat-jalans/${shipmentId}`);
    await page.getByRole('link', { name: 'Buat Faktur dari Surat Jalan' }).click();
    const invoicePage = page;

    await invoicePage.waitForLoadState('domcontentloaded');
    await expect(invoicePage).toHaveURL(/\/admin\/invoices\/create\?from_sj=/);
    await expect(invoicePage.locator('[id="form.nama_customer"]')).toHaveValue(/AUTOTEST-/);
    await fillForm(invoicePage, {
        no_invoice: invoiceNumber,
        catatan: `${E2E_PREFIX} invoice`,
    });
    await selectOptionFromLocator(
        invoicePage.locator('button[id^="form.invoice_items."][id$=".barang_id"]'),
        `${E2E_PREFIX}-Product-Edited`,
        true,
    );
    await invoicePage.locator('input[id^="form.invoice_items."][id$=".qty"]').first().fill('2');
    await invoicePage.locator('input[id^="form.invoice_items."][id$=".harga_satuan"]').first().fill('1000');
    await uploadTestPdf(invoicePage, `${invoiceNumber}.pdf`);
    await submitCreate(invoicePage);
    await expect(invoicePage).toHaveURL(/\/admin\/invoices\/\d+$/);
    await expect(invoicePage.getByText(invoiceNumber, { exact: true })).toBeVisible();

    await invoicePage.getByRole('button', { name: 'Cetak PDF' }).click();
    const printFrame = invoicePage.frameLocator('iframe[title="Pratinjau cetak"]');
    await expect(printFrame.locator('body')).toContainText(invoiceNumber);
    await expect(printFrame.locator('body')).toContainText(/AUTOTEST-/);

    await openPage(invoicePage, '/admin/invoices/history');
    await expect(invoicePage.getByRole('heading', { name: /Riwayat|History|Invoice/i })).toBeVisible();
});
