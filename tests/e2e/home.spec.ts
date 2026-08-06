import { test, expect } from '@playwright/test';

import { GAMES } from './helpers';

test.describe('Home page', () => {
    test('loads with branding and all game cards', async ({ page }) => {
        const response = await page.goto('/');
        expect(response?.status()).toBe(200);

        await expect(page).toHaveTitle(/Lottery Generator/);
        await expect(
            page.getByRole('heading', { name: 'Lottery Generator' }),
        ).toBeVisible();
        await expect(
            page.getByText(
                /Just for fun, makes an attempt at 'guessing' the Lotto numbers/,
            ),
        ).toBeVisible();

        const generateLinks = page.locator('a[href*="/generate"]');
        await expect(generateLinks).toHaveCount(GAMES.length);

        for (const game of GAMES) {
            await expect(page.getByAltText(game.name, { exact: true })).toBeVisible();
            await expect(
                page.locator(`a[href="/game/${game.slug}/generate"]`),
            ).toBeVisible();
        }
    });

    test('static assets load without error', async ({ page, request }) => {
        await page.goto('/');

        const cssHref = await page
            .locator('link[href*="css/app.css"]')
            .getAttribute('href');
        expect(cssHref).toBeTruthy();

        const cssResponse = await request.get(cssHref!);
        expect(cssResponse.status()).toBe(200);

        const jsSrc = await page
            .locator('script[src*="js/app.js"]')
            .getAttribute('src');
        expect(jsSrc).toBeTruthy();

        const jsResponse = await request.get(jsSrc!);
        expect(jsResponse.status()).toBe(200);
    });

    test('navigates to a game from the home page', async ({ page }) => {
        await page.goto('/');

        await page.locator('a[href="/game/lotto/generate"]').click();
        await expect(page).toHaveURL('/game/lotto/generate');
        await expect(page.getByAltText('Lotto')).toBeVisible();
    });
});
