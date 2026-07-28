import { test, expect } from '@playwright/test';

/**
 * Collect browser console and uncaught page errors across key routes.
 */
test.describe('Smoke checks', () => {
    test('no console or page errors on primary routes', async ({ page }) => {
        const errors: string[] = [];

        page.on('console', (message) => {
            if (message.type() === 'error') {
                errors.push(message.text());
            }
        });

        page.on('pageerror', (error) => {
            errors.push(error.message);
        });

        await page.goto('/');
        await page.goto('/game/lotto/generate');
        await page.goto('/game/euromillions-hotpicks/generate');

        expect(errors).toEqual([]);
    });

    test('unknown game slug returns 404', async ({ page }) => {
        const response = await page.goto('/game/not-a-real-game/generate');
        expect(response?.status()).toBe(404);
    });
});
