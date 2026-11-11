import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';
import {
    E2E_PREFIX,
    deleteRowFromTable,
    fillForm,
    openPage,
    selectOption,
    submitCreate,
    waitLivewire,
} from './helpers/filament';

test('dashboard renders transaction and stock widgets', async ({ page }) => {
    await openPage(page, '/admin');
    await expect(page.getByText('Penawaran Bulan Ini')).toBeVisible();
    await expect(page.getByText('Total Item Barang')).toBeVisible();
    await expect(page.getByText('Total Item Gudang')).toBeVisible();
});

test('update the profile photo of a newly created AUTOTEST account', async ({ page, browser }) => {
    const username = `${E2E_PREFIX.toLowerCase()}-profile`;
    const password = `${E2E_PREFIX}-Profile-Pass1`;
    const name = `${E2E_PREFIX}-Profile`;

    await openPage(page, '/admin/users/create');
    await fillForm(page, {
        nama: name,
        email: `${username}@example.test`,
        username,
        password,
    });
    await selectOption(page, 'role', 'Pekerja');
    await submitCreate(page);
    let userCreated = true;

    const profileContext = await browser.newContext({
        baseURL: new URL(page.url()).origin,
        storageState: { cookies: [], origins: [] },
    });

    try {
        const profilePage = await profileContext.newPage();
        await profilePage.setViewportSize({ width: 390, height: 844 });
        await login(profilePage, username, password);

        await openPage(profilePage, '/admin/profile');
        await expect(profilePage.getByRole('heading', { name: 'Profil Saya' })).toBeVisible();
        await expect(profilePage.getByRole('heading', { name: 'Informasi Akun' })).toBeVisible();
        await expect(profilePage.getByRole('button', { name: 'Simpan Foto' })).toBeVisible();
        await expect.poll(() =>
            profilePage.evaluate(() => document.documentElement.scrollWidth <= document.documentElement.clientWidth),
        ).toBe(true);

        await profilePage.locator('input[type="file"]').setInputFiles({
            name: 'autotest-profile.png',
            mimeType: 'image/png',
            buffer: Buffer.from(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/Jx8AAAAASUVORK5CYII=',
                'base64',
            ),
        });

        await waitLivewire(profilePage, () => profilePage.getByRole('button', { name: 'Simpan Foto' }).click());
        await expect(profilePage.getByText('Profil berhasil diperbarui')).toBeVisible();
        await expect(profilePage.getByRole('heading', { name })).toBeVisible();
    } finally {
        await profileContext.close();

        if (userCreated) {
            await openPage(page, '/admin/users');
            await waitLivewire(page, () => page.getByRole('searchbox').first().fill(name));
            await deleteRowFromTable(page, name);
        }
    }
});
