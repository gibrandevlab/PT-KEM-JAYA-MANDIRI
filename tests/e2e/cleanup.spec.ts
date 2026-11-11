import { test, expect } from '@playwright/test';
import {
    deleteRowFromTable,
    openPage,
    waitLivewire,
} from './helpers/filament';

const AUTOTEST_PREFIX = 'AUTOTEST-';

async function removePrefixedRows(
    page: import('@playwright/test').Page,
    path: string,
    searchRows = true,
): Promise<void> {
    await openPage(page, path);

    const search = page.getByRole('searchbox').first();

    if (searchRows && await search.count() > 0) {
        await waitLivewire(page, () => search.fill(AUTOTEST_PREFIX));
    }

    const rows = page.getByRole('row').filter({ hasText: AUTOTEST_PREFIX });
    let remaining = await rows.count();

    while (remaining > 0) {
        await deleteRowFromTable(page, AUTOTEST_PREFIX);
        remaining = await rows.count();
    }
}

async function removePrefixedCustomers(page: import('@playwright/test').Page): Promise<void> {
    await removePrefixedRowsViaBulk(page, '/admin/customers');
}

async function removePrefixedRowsViaBulk(
    page: import('@playwright/test').Page,
    path: string,
): Promise<void> {
    await openPage(page, path);

    const search = page.getByRole('searchbox').first();

    if (await search.count() > 0) {
        await waitLivewire(page, () => search.fill(AUTOTEST_PREFIX));
    }

    const rows = page.getByRole('row').filter({ hasText: AUTOTEST_PREFIX });

    while (await rows.count() > 0) {
        const selectedCheckboxNames: string[] = [];

        for (const row of await rows.all()) {
            const rowCheckbox = row.getByRole('checkbox');
            const checkboxName = await rowCheckbox.getAttribute('aria-label');

            if (!checkboxName) {
                throw new Error('Expected each selected row checkbox to have an accessible label.');
            }

            selectedCheckboxNames.push(checkboxName);
            await rowCheckbox.check();
        }

        const bulkActionsButton = page.locator('.fi-ta-header-toolbar')
            .getByRole('button', { name: 'Tindakan', exact: true });
        await expect(bulkActionsButton).toBeVisible();
        await bulkActionsButton.click();

        const deleteAction = page.getByRole('menuitem', {
            name: /Hapus yang dipilih|Hapus Terpilih|Delete selected/i,
        }).or(page.getByRole('button', {
            name: /Hapus yang dipilih|Hapus Terpilih|Delete selected/i,
        })).filter({ visible: true }).last();
        await expect(deleteAction).toBeVisible();
        await deleteAction.click();
        const confirmationDialog = page.getByRole('alertdialog').last();
        const confirmationButton = confirmationDialog
            .getByRole('button', { name: /^(Hapus|Delete)$/i }).last();
        await expect(confirmationButton).toBeVisible();
        await waitLivewire(page, () =>
            confirmationButton.click({ force: true }),
        );

        for (const checkboxName of selectedCheckboxNames) {
            await expect(page.getByRole('checkbox', { name: checkboxName, exact: true })).toHaveCount(0);
        }
    }
}

test('delete only AUTOTEST business data through table actions', async ({ page }) => {
    test.setTimeout(180_000);

    await removePrefixedRows(page, '/admin/invoices/history');
    await removePrefixedRows(page, '/admin/invoices');
    await removePrefixedRows(page, '/admin/surat-jalans/history');
    await removePrefixedRows(page, '/admin/surat-jalans');
    await removePrefixedRows(page, '/admin/purchase-orders/history');
    await removePrefixedRows(page, '/admin/purchase-orders');
    await removePrefixedRows(page, '/admin/penawarans/history', false);
    await removePrefixedRows(page, '/admin/penawarans', false);
    await removePrefixedRows(page, '/admin/stock-gudangs');
    await removePrefixedRowsViaBulk(page, '/admin/customer-pics');
    await removePrefixedRowsViaBulk(page, '/admin/customer-addresses');
    await removePrefixedRowsViaBulk(page, '/admin/vendors');
    await removePrefixedRowsViaBulk(page, '/admin/bank-accounts');
    await removePrefixedRowsViaBulk(page, '/admin/term-of-payments');
    await removePrefixedRowsViaBulk(page, '/admin/barangs');
    await removePrefixedRows(page, '/admin/kategori-barangs');
    await removePrefixedRowsViaBulk(page, '/admin/users');
    await removePrefixedCustomers(page);
});
