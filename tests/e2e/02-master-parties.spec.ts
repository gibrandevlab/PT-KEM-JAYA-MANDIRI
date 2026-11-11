import { test, expect } from '@playwright/test';
import {
    E2E_PREFIX,
    fillForm,
    openPage,
    recordIdFromLink,
    submitCreate,
    submitEdit,
    waitLivewire,
} from './helpers/filament';

test.describe.configure({ mode: 'serial' });

const customerName = `${E2E_PREFIX}-Customer`;
const vendorName = `${E2E_PREFIX}-Vendor`;
let customerId: string;

test('create and edit a customer, saved address, and contact', async ({ page }) => {
    await openPage(page, '/admin/customers/create');
    await fillForm(page, {
        nama_perusahaan: customerName,
        kota: 'Jakarta',
        telepon: '0211234567',
        email: `${E2E_PREFIX.toLowerCase()}-customer@example.test`,
        npwp: 'AUTOTEST-01',
        pic: `${E2E_PREFIX}-PIC`,
        pic_phone: '08123456789',
        pic_email: `${E2E_PREFIX.toLowerCase()}-pic@example.test`,
        pic_gudang: `${E2E_PREFIX}-Warehouse-PIC`,
        pic_gudang_phone: '08123456780',
    });
    await page.locator('[id^="form.customer_addresses_form."][id$=".alamat_lengkap"]').first()
        .fill(`${E2E_PREFIX} company address`);
    await page.locator('[id^="form.warehouse_addresses_form."][id$=".alamat_lengkap"]').first()
        .fill(`${E2E_PREFIX} warehouse address`);
    await submitCreate(page);
    await expect(page.getByText(customerName, { exact: true })).toBeVisible();

    await openPage(page, '/admin/customers');
    await page.getByRole('searchbox').first().fill(customerName);
    customerId = await recordIdFromLink(page, 'customers', customerName);

    await openPage(page, `/admin/customer-addresses/create`);
    await fillForm(page, {
        customer_id: customerId,
        label_alamat: `${E2E_PREFIX}-Address`,
        alamat_lengkap: `${E2E_PREFIX} saved address`,
    });
    await submitCreate(page);
    await expect(page.getByText(`${E2E_PREFIX}-Address`, { exact: true })).toBeVisible();

    await openPage(page, '/admin/customer-pics/create');
    await fillForm(page, {
        customer_id: customerId,
        tipe: 'Customer',
        nama: `${E2E_PREFIX}-Contact`,
        telepon: '08123456789',
        email: `${E2E_PREFIX.toLowerCase()}-contact@example.test`,
    });
    await submitCreate(page);
    await expect(page.getByText(`${E2E_PREFIX}-Contact`, { exact: true })).toBeVisible();

    await openPage(page, `/admin/customers/${customerId}`);
    await expect(page.getByText(customerName, { exact: true })).toBeVisible();

    await openPage(page, `/admin/customers/${customerId}/edit`);
    await fillForm(page, { nama_perusahaan: `${customerName}-Edited` });
    await submitEdit(page);
    await expect(page.locator('[id="form.nama_perusahaan"]')).toHaveValue(`${customerName}-Edited`);

    await openPage(page, '/admin/customers');
    await page.getByRole('searchbox').first().fill(`${customerName}-Edited`);
    await expect(page.getByRole('row').filter({ hasText: `${customerName}-Edited` })).toBeVisible();
});

test('create, view, and edit a vendor master record', async ({ page }) => {
    await openPage(page, '/admin/vendors/create');
    await fillForm(page, {
        nama_perusahaan: vendorName,
        alamat: `${E2E_PREFIX} vendor address`,
        kota: 'Jakarta',
        telepon: '0211234567',
        email: `${E2E_PREFIX.toLowerCase()}-vendor@example.test`,
    });
    await page.locator('input[id^="form.pic_list."][id$=".nama"]').first().fill(`${E2E_PREFIX}-Vendor-PIC`);
    await submitCreate(page);
    await expect(page.getByText(vendorName, { exact: true })).toBeVisible();

    await openPage(page, '/admin/vendors');
    await page.getByRole('searchbox').first().fill(vendorName);
    const vendorId = await recordIdFromLink(page, 'vendors', vendorName);
    await openPage(page, `/admin/vendors/${vendorId}`);
    await expect(page.getByText(vendorName, { exact: true })).toBeVisible();

    await openPage(page, `/admin/vendors/${vendorId}/edit`);
    await fillForm(page, { nama_perusahaan: `${vendorName}-Edited` });
    await submitEdit(page);
    await expect(page.locator('[id="form.nama_perusahaan"]')).toHaveValue(`${vendorName}-Edited`);

    await openPage(page, '/admin/vendors');
    await page.getByRole('searchbox').first().fill(`${vendorName}-Edited`);
    await expect(page.getByRole('row').filter({ hasText: `${vendorName}-Edited` })).toBeVisible();
});
