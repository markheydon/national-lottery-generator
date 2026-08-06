import { test, expect } from '@playwright/test';

import {
    GAMES,
    assertDrawDateIsRecent,
    NUMBER_LINE_PATTERN,
} from './helpers';

test.describe('Game generate pages', () => {
    for (const game of GAMES) {
        test(`${game.name} generates numbers with a recent latest draw date`, async ({
            page,
        }) => {
            const response = await page.goto(`/game/${game.slug}/generate`);
            expect(response?.status()).toBe(200);

            await expect(page).toHaveTitle(
                new RegExp(`Lottery Generator - ${game.pageTitle}`),
            );

            await expect(
                page.getByText(/not random.*highly unlikely to actually win/i),
            ).toBeVisible();

            await expect(
                page.getByAltText(game.pageTitle, { exact: true }),
            ).toBeVisible();

            const latestDrawCard = page
                .locator('.card-text')
                .filter({ hasText: 'Latest Draw' });
            await expect(latestDrawCard).toBeVisible();

            const latestDrawText = await latestDrawCard.textContent();
            expect(latestDrawText).toBeTruthy();
            assertDrawDateIsRecent(latestDrawText!);

            const suggestedSection = page
                .locator('h3')
                .filter({ hasText: 'Suggested Lines' })
                .locator('xpath=..');
            const suggestedRows = suggestedSection.locator('table tbody tr');
            const suggestedCount = await suggestedRows.count();
            expect(suggestedCount).toBeGreaterThan(0);

            for (let index = 0; index < suggestedCount; index += 1) {
                const lineText = await suggestedRows
                    .nth(index)
                    .locator('td')
                    .textContent();
                expect(lineText).toMatch(NUMBER_LINE_PATTERN);
            }

            const otherSection = page
                .locator('h3')
                .filter({ hasText: 'Other Suggestions' })
                .locator('xpath=..');
            const otherRows = otherSection.locator('table tbody tr');
            const otherCount = await otherRows.count();
            expect(otherCount).toBeGreaterThan(0);

            for (let index = 0; index < otherCount; index += 1) {
                const lineText = await otherRows
                    .nth(index)
                    .locator('td')
                    .textContent();
                expect(lineText).toMatch(NUMBER_LINE_PATTERN);
            }
        });
    }
});

test.describe('Game navigation', () => {
    test('other games dropdown links work', async ({ page }) => {
        await page.goto('/game/lotto/generate');

        await page.getByRole('button', { name: 'Other Games' }).click();
        await page
            .getByRole('link', { name: 'EuroMillions', exact: true })
            .click();

        await expect(page).toHaveURL('/game/euromillions/generate');
        await expect(page.getByAltText('EuroMillions', { exact: true })).toBeVisible();
    });

    test('all games link returns to the home page', async ({ page }) => {
        await page.goto('/game/thunderball/generate');

        await page.getByRole('button', { name: 'Other Games' }).click();
        await page.getByRole('link', { name: '← All Games' }).click();

        await expect(page).toHaveURL('/');
        await expect(
            page.getByRole('heading', { name: 'Lottery Generator' }),
        ).toBeVisible();
    });
});
