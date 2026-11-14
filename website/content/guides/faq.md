---
title: FAQ
weight: 30
aliases:
  - /faq.html
  - /faq.md
---

## What is this app?

This is a PHP web app that generates playful number suggestions for UK National Lottery games.

## Is it free to use?

Yes. The public app can be used without signing up or paying a fee.

## Which games are supported?

The app currently supports:

- Lotto
- EuroMillions
- Thunderball
- Set For Life
- Lotto Hotpicks
- EuroMillions Hotpicks

## Will these numbers help me win?

No. The app is for entertainment only. The suggestions are based on historical data and a simple algorithm, and they do not improve the odds of a real lottery draw.

## How are the numbers generated?

The app checks whether fresh draw-history data is available, downloads it if needed, and then builds a set of suggestion lines for the selected game. For more detail, see [How It Works](/guides/how-it-works/).

## Does the app need a database?

No. The app uses local file storage for cached draw-history CSVs rather than a database.

## What if a game or page is not loading?

If a game page says draw history is not available, the National Lottery data feed may be temporarily unreachable, or the server has not yet stored a successful download for that game. Wait a few minutes and refresh the page.

For other errors, try refreshing and checking that you are visiting the current public site. If the issue continues, please open an issue on [GitHub](https://github.com/markheydon/national-lottery-generator/issues).

## Is my data safe?

The app does not ask for personal information and does not rely on user accounts. It stores cached draw data locally on the server.

## Where can I get help?

- [Getting Started](/guides/getting-started/)
- [How It Works](/guides/how-it-works/)
- [GitHub issues](https://github.com/markheydon/national-lottery-generator/issues)