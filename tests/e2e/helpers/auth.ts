import { expect, type Page } from '@playwright/test';

export async function login(page: Page, username: string, password: string): Promise<void> {
    await page.goto('/login');
    await page.getByPlaceholder('Masukkan nama pengguna atau email').fill(username);
    await page.getByPlaceholder('Masukkan kata sandi').fill(password);
    await page.getByRole('button', { name: /Masuk/ }).click();
    await expect(page).toHaveURL(/\/admin(?:\/|$)/);
}
