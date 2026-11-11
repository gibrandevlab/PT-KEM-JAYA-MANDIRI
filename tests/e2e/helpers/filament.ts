import { expect, type Locator, type Page } from '@playwright/test';

export const E2E_PREFIX = `AUTOTEST-${process.env.E2E_RUN_ID ?? 'RUN'}`;

export async function openPage(page: Page, path: string): Promise<void> {
    const response = await page.goto(path);

    expect(response?.ok(), `GET ${path} should succeed`).toBeTruthy();
    await expect(page.locator('main')).toBeVisible();
}

export function formField(page: Page, name: string): Locator {
    return page.locator(`[id="form.${name}"]`);
}

export async function fillForm(page: Page, fields: Record<string, string>): Promise<void> {
    for (const [name, value] of Object.entries(fields)) {
        await formField(page, name).fill(value);
    }
}

export async function uploadTestPdf(page: Page, filename: string): Promise<void> {
    await page.locator('input[type="file"]').first().setInputFiles({
        name: filename,
        mimeType: 'application/pdf',
        buffer: Buffer.from(
            '%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [] /Count 0 >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF\n',
        ),
    });

    await expect(page.locator('legend').filter({ hasText: filename })).toBeVisible();
}

export async function selectOption(
    page: Page,
    name: string,
    value: string,
    waitForUpdate = false,
): Promise<void> {
    await selectOptionFromLocator(formField(page, name), value, waitForUpdate);
}

export async function selectOptionFromLocator(
    field: Locator,
    value: string,
    waitForUpdate = false,
): Promise<void> {
    const page = field.page();

    if (await field.evaluate((element) => element.tagName === 'SELECT')) {
        if (waitForUpdate) {
            await waitLivewire(page, () => field.selectOption({ label: value }));
        } else {
            await field.selectOption({ label: value });
        }
        return;
    }

    await field.click();

    const listbox = page.getByRole('listbox').last();
    await expect(listbox).toBeVisible();

    const search = page.locator('input[role="combobox"]:visible').last();

    if (await search.count() > 0) {
        await search.first().fill(value);
    }

    const option = listbox.getByRole('option', { name: value, exact: false }).first();

    if (waitForUpdate) {
        await waitLivewire(page, () => option.click());
    } else {
        await option.click();
    }
}

export async function waitLivewire(page: Page, action: () => Promise<unknown>): Promise<void> {
    const responsePromise = page.waitForResponse((response) => {
        const pathname = new URL(response.url()).pathname;

        return response.request().method() === 'POST'
            && /\/livewire(?:-[^/]+)?\/update$/.test(pathname);
    });

    await action();
    const response = await responsePromise;

    expect(response.ok(), 'The Livewire request should succeed').toBeTruthy();
    await expect(page.locator('main')).toBeVisible();
}

export async function submitCreate(page: Page): Promise<void> {
    await waitLivewire(page, () => page.getByRole('button', { name: 'Buat', exact: true }).click());
}

export async function submitEdit(page: Page): Promise<void> {
    await waitLivewire(page, () => page.getByRole('button', { name: /Simpan/i }).first().click());
}

export async function recordIdFromLink(page: Page, resource: string, text: string): Promise<string> {
    const row = page.getByRole('row').filter({ hasText: text });
    await expect(row).toBeVisible();

    const href = await row.locator(`a[href*="/admin/${resource}/"]`).first().getAttribute('href');
    const match = href?.match(new RegExp(`/admin/${resource}/(\\d+)`));

    if (!match) {
        throw new Error(`The ${resource} record link did not include its ID: ${href ?? '(missing href)'}`);
    }

    return match[1];
}

export async function deleteRowFromTable(page: Page, text: string): Promise<void> {
    const matchingRows = page.getByRole('row').filter({ hasText: text });
    const row = matchingRows.last();

    await expect(row).toBeVisible();
    const rowText = await row.innerText();
    await row.getByRole('button', { name: /Aksi|Tindakan|Actions/i }).click();

    const deleteAction = page.getByRole('menuitem', { name: /^(Hapus|Delete)$/i })
        .or(page.getByRole('button', { name: /^(Hapus|Delete)$/i }))
        .filter({ visible: true })
        .last();
    await expect(deleteAction).toBeVisible();
    await deleteAction.click();
    const confirmationDialog = page.getByRole('alertdialog').last();
    const confirmationButton = confirmationDialog.getByRole('button', { name: /^(Hapus|Delete)$/i }).last();
    await expect(confirmationButton).toBeVisible();
    await waitLivewire(page, () => confirmationButton.click({ force: true }));
    await expect(page.getByRole('row').filter({ hasText: rowText })).toHaveCount(0);
}
