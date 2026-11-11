import { test, expect } from '@playwright/test';
import {
    E2E_PREFIX,
    fillForm,
    openPage,
    selectOption,
    submitCreate,
    waitLivewire,
} from './helpers/filament';

const purchaseOrderNumber = `${E2E_PREFIX}-PO`;
const shipmentNumber = `${E2E_PREFIX}-SJ`;

test('create a shipment from a PO, set it as sent, and verify print and invoice action', async ({ page }) => {
    await openPage(page, '/admin/surat-jalans/create');
    await fillForm(page, { no_sj: shipmentNumber, kota_tujuan: 'Jakarta', catatan: `${E2E_PREFIX} shipment` });
    await selectOption(page, 'po_id', purchaseOrderNumber, true);
    await expect(page.locator('[id="form.nama_penerima"]')).toHaveValue(new RegExp(`${E2E_PREFIX}-Vendor-Edited`));
    await expect(page.locator('input[id^="form.surat_jalan_items."][id$=".nama_barang"]').first()).toHaveValue(
        new RegExp(`${E2E_PREFIX}-Product-Edited`),
    );
    await submitCreate(page);

    const detailUrl = page.url();
    const recordId = detailUrl.match(/\/surat-jalans\/(\d+)$/)?.[1];
    expect(recordId, `Expected a shipment detail page, got ${detailUrl}`).toBeTruthy();
    await expect(page.getByText(shipmentNumber, { exact: true })).toBeVisible();

    await page.getByRole('button', { name: 'Ubah Status' }).click();
    await page.getByLabel('Status*').selectOption({ label: 'Dikirim' });
    await waitLivewire(page, () => page.getByRole('button', { name: 'Kirim' }).last().click());
    await expect(page.getByText(/dikirim/i)).toBeVisible();
    await expect(page.getByRole('link', { name: 'Buat Faktur dari Surat Jalan' })).toBeVisible();

    await page.getByRole('button', { name: 'Cetak PDF' }).click();
    const printFrame = page.frameLocator('iframe[title="Pratinjau cetak"]');
    await expect(printFrame.locator('body')).toContainText(shipmentNumber);

    await openPage(page, '/admin/surat-jalans/history');
    await expect(page.getByRole('heading', { name: /Riwayat|History|Surat Jalan/i })).toBeVisible();
});
