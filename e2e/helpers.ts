export const GAMES = [
    { slug: 'lotto', name: 'Lotto', pageTitle: 'Lotto' },
    { slug: 'euromillions', name: 'EuroMillions', pageTitle: 'EuroMillions' },
    { slug: 'thunderball', name: 'Thunderball', pageTitle: 'Thunderball' },
    { slug: 'set-for-life', name: 'Set For Life', pageTitle: 'Set For Life' },
    {
        slug: 'lotto-hotpicks',
        name: 'Lotto Hotpicks',
        pageTitle: 'LottoHotpicks',
    },
    {
        slug: 'euromillions-hotpicks',
        name: 'EuroMillions Hotpicks',
        pageTitle: 'EuroMillions Hotpicks',
    },
] as const;

const MONTH_NAMES = [
    'january',
    'february',
    'march',
    'april',
    'may',
    'june',
    'july',
    'august',
    'september',
    'october',
    'november',
    'december',
];

const LATEST_DRAW_PATTERN =
    /Latest Draw\D+(\w+)\s+(\d{1,2})(?:st|nd|rd|th)\s+(\w+)/i;

/** Matches formatted number lines such as `01 - 02 - 03` or `01 - 02 ** 03`. */
export const NUMBER_LINE_PATTERN = /\d{2}(?:\s*-\s*\d{2}|(?:\s*\*\*\s*\d{2})+)/;

/**
 * Parse the "Latest Draw" card text rendered by GameController (`l jS F` format, no year).
 */
export function parseLatestDrawDate(cardText: string): Date {
    const match = cardText.match(LATEST_DRAW_PATTERN);
    if (!match) {
        throw new Error(`Could not parse latest draw date from: ${cardText}`);
    }

    const day = Number.parseInt(match[2], 10);
    const monthIndex = MONTH_NAMES.indexOf(match[3].toLowerCase());
    if (monthIndex === -1) {
        throw new Error(`Unknown month in latest draw date: ${match[3]}`);
    }

    const now = new Date();
    let year = now.getFullYear();
    let candidate = new Date(year, monthIndex, day);

    // Draw history never labels the year; roll back when we have crossed into a new calendar year.
    if (candidate.getTime() > now.getTime() + 7 * 24 * 60 * 60 * 1000) {
        year -= 1;
        candidate = new Date(year, monthIndex, day);
    }

    return candidate;
}

/**
 * Assert the latest draw date is recent enough to indicate fresh CSV data.
 */
export function assertDrawDateIsRecent(
    cardText: string,
    maxDaysAgo = 21,
): void {
    const drawDate = parseLatestDrawDate(cardText);
    const now = new Date();
    const diffDays =
        (now.getTime() - drawDate.getTime()) / (1000 * 60 * 60 * 24);

    if (diffDays < 0) {
        throw new Error(
            `Latest draw date is in the future: ${drawDate.toISOString()}`,
        );
    }

    if (diffDays > maxDaysAgo) {
        throw new Error(
            `Latest draw date ${drawDate.toISOString()} is older than ${maxDaysAgo} days`,
        );
    }
}
