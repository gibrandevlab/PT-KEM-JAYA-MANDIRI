import { test as setup, expect } from '@playwright/test';
import { mkdir } from 'node:fs/promises';
import { resolve } from 'node:path';
import { login } from './helpers/auth';

const authFile = resolve(process.cwd(), 'tests/e2e/.auth/user.json');

setup('login and save browser state', async ({ page }) => {
    const username = process.env.E2E_USERNAME ?? 'shiina';
    const password = process.env.E2E_PASSWORD;

    if (!password) {
        throw new Error('Set E2E_PASSWORD in the environment before running Playwright.');
    }

    await login(page, username, password);
    await expect(page.getByRole('heading', { name: /Dasbor|Dashboard/ })).toBeVisible();

    await mkdir(resolve(process.cwd(), 'tests/e2e/.auth'), { recursive: true });
    await page.context().storageState({ path: authFile });
});
