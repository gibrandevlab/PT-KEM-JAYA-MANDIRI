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

test('create and edit a bank account', async ({ page }) => {
    const bankName = `${E2E_PREFIX}-Bank`;

    await openPage(page, '/admin/bank-accounts/create');
    await fillForm(page, {
        nama_bank: bankName,
        no_rekening: `${E2E_PREFIX}-${Date.now()}`,
        atas_nama: `${E2E_PREFIX}-Account-Holder`,
    });
    await submitCreate(page);
    await expect(page.getByText(bankName, { exact: true })).toBeVisible();

    await openPage(page, '/admin/bank-accounts');
    await page.getByRole('searchbox').first().fill(bankName);
    const bankId = await recordIdFromLink(page, 'bank-accounts', bankName);
    await openPage(page, `/admin/bank-accounts/${bankId}`);
    await expect(page.getByText(bankName, { exact: true })).toBeVisible();
    await openPage(page, `/admin/bank-accounts/${bankId}/edit`);
    await fillForm(page, { nama_bank: `${bankName}-Edited` });
    await submitEdit(page);
    await expect(page.locator('[id="form.nama_bank"]')).toHaveValue(`${bankName}-Edited`);

    await openPage(page, '/admin/bank-accounts');
    await page.getByRole('searchbox').first().fill(`${bankName}-Edited`);
    await expect(page.getByRole('row').filter({ hasText: `${bankName}-Edited` })).toBeVisible();
});

test('create and edit a payment term', async ({ page }) => {
    const termName = `${E2E_PREFIX}-Payment-Term`;

    await openPage(page, '/admin/term-of-payments/create');
    await fillForm(page, { nama_term: termName });
    await submitCreate(page);
    await expect(page.getByText(termName, { exact: true })).toBeVisible();

    await openPage(page, '/admin/term-of-payments');
    await page.getByRole('searchbox').first().fill(termName);
    const termId = await recordIdFromLink(page, 'term-of-payments', termName);
    await openPage(page, `/admin/term-of-payments/${termId}`);
    await expect(page.getByText(termName, { exact: true })).toBeVisible();
    await openPage(page, `/admin/term-of-payments/${termId}/edit`);
    await fillForm(page, { nama_term: `${termName}-Edited` });
    await submitEdit(page);
    await expect(page.locator('[id="form.nama_term"]')).toHaveValue(`${termName}-Edited`);

    await openPage(page, '/admin/term-of-payments');
    await page.getByRole('searchbox').first().fill(`${termName}-Edited`);
    await expect(page.getByRole('row').filter({ hasText: `${termName}-Edited` })).toBeVisible();
});

test('create a user and open access management', async ({ page }) => {
    const name = `${E2E_PREFIX}-User`;
    const username = `${E2E_PREFIX.toLowerCase()}-user`;
    const email = `${username}@example.test`;
    const password = `${E2E_PREFIX}-User-Pass1`;

    await openPage(page, '/admin/users/create');
    await fillForm(page, {
        nama: name,
        email,
        username,
        password,
    });
    await selectOption(page, 'role', 'Pekerja');
    await submitCreate(page);
    await expect(page.getByText(name, { exact: true })).toBeVisible();

    await openPage(page, '/admin/users');
    await page.getByRole('searchbox').first().fill(name);
    const userId = await recordIdFromLink(page, 'users', name);
    await openPage(page, `/admin/users/${userId}`);
    await expect(page.getByText(name, { exact: true })).toBeVisible();

    await openPage(page, `/admin/users/${userId}/edit`);
    await fillForm(page, { nama: `${name}-Edited` });
    await submitEdit(page);
    await expect(page.locator('[id="form.nama"]')).toHaveValue(`${name}-Edited`);

    await openPage(page, `/admin/users/${userId}/access`);
    await expect(page.getByRole('heading', { name: 'Atur Hak Akses' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Default' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Izinkan' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Tolak' })).toBeVisible();

    await openPage(page, '/admin/users');
    await page.getByRole('searchbox').first().fill(`${name}-Edited`);
    await expect(page.getByRole('row').filter({ hasText: `${name}-Edited` })).toBeVisible();
});
