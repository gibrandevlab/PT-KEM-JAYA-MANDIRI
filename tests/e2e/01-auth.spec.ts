import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';

test('login accepts the configured account', async ({ page }) => {
    const username = process.env.E2E_USERNAME ?? 'shiina';
    const password = process.env.E2E_PASSWORD;

    if (!password) {
        throw new Error('Set E2E_PASSWORD in the environment before running Playwright.');
    }

    await login(page, username, password);
    await expect(page.getByRole('heading', { name: /Dasbor|Dashboard/ })).toBeVisible();
});

test('login rejects an incorrect password', async ({ page }) => {
    await page.goto('/login');
    await page.getByPlaceholder('Masukkan nama pengguna atau email').fill(process.env.E2E_USERNAME ?? 'shiina');
    await page.getByPlaceholder('Masukkan kata sandi').fill(`AUTOTEST-invalid-${Date.now()}`);
    await page.getByRole('button', { name: /Masuk/ }).click();

    await expect(page.getByText(/Username atau password salah/i)).toBeVisible();
    await expect(page).toHaveURL(/\/login$/);
});

test('logout returns to login and protected pages redirect when signed out', async ({ page }) => {
    const username = process.env.E2E_USERNAME ?? 'shiina';
    const password = process.env.E2E_PASSWORD;

    if (!password) {
        throw new Error('Set E2E_PASSWORD in the environment before running Playwright.');
    }

    await login(page, username, password);

    await page.getByRole('button', { name: 'Menu pengguna' }).click();
    await page.getByRole('menuitem', { name: 'Keluar' }).click();
    await expect(page).toHaveURL(/\/login$/);

    await page.goto('/admin');
    await expect(page).toHaveURL(/\/login$/);
});
