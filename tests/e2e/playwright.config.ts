import { defineConfig, devices } from '@playwright/test';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

function envFromFile(name: string): string | undefined {
    try {
        const content = readFileSync(resolve(process.cwd(), '.env'), 'utf8');
        const line = content.split(/\r?\n/).find((entry) => entry.startsWith(`${name}=`));

        return line?.slice(name.length + 1).replace(/^['"]|['"]$/g, '').trim();
    } catch (error) {
        if ((error as NodeJS.ErrnoException).code === 'ENOENT') {
            return undefined;
        }

        throw error;
    }
}

const baseURL = process.env.E2E_BASE_URL ?? envFromFile('APP_URL') ?? 'http://127.0.0.1:8000';
const authFile = resolve(process.cwd(), 'tests/e2e/.auth/user.json');

if (!process.env.E2E_RUN_ID) {
    process.env.E2E_RUN_ID = `${Date.now()}`;
}

export default defineConfig({
    testDir: '.',
    fullyParallel: false,
    workers: 1,
    timeout: 45_000,
    expect: {
        timeout: 10_000,
    },
    reporter: [
        ['list'],
        ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ],
    outputDir: 'test-results',
    use: {
        baseURL,
        ...devices['Desktop Chrome'],
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
        trace: 'retain-on-failure',
    },
    projects: [
        {
            name: 'setup',
            testMatch: '**/auth.setup.ts',
            use: {
                ...devices['Desktop Chrome'],
                baseURL,
                storageState: { cookies: [], origins: [] },
            },
        },
        {
            name: 'auth',
            testMatch: '**/01-auth.spec.ts',
            use: {
                ...devices['Desktop Chrome'],
                baseURL,
                storageState: { cookies: [], origins: [] },
            },
        },
        {
            name: 'authenticated',
            testMatch: '**/*.spec.ts',
            testIgnore: '**/01-auth.spec.ts',
            dependencies: ['setup'],
            use: {
                ...devices['Desktop Chrome'],
                baseURL,
                storageState: authFile,
            },
        },
    ],
});
