---
title: How It Works
weight: 20
aliases:
  - /how-it-works.html
  - /how-it-works.md
---

The app follows a simple workflow. A visitor chooses a game, the app checks whether fresh draw history is available, and then it generates a set of number suggestions for that game.

## What the app uses

The public interface is a small PHP front controller with plain templates. The supported games are defined in configuration and are rendered as cards on the home page.

## How data is refreshed

When a generator page is requested, the app checks whether the relevant draw-history file is present and fresh enough to use. If it is missing or older than 24 hours, the app tries to download a new copy from the National Lottery website and stores it locally under `storage/app/lottery/`.

Before replacing an existing file, the app keeps a timestamped backup copy. That way, if a refresh fails, an earlier file can still be used so the page can keep working with slightly older data.

If no readable draw history is available after that check (for example, on a first visit while the download is failing), the game page shows a short “draw history is not available” message instead of number suggestions. Try again after a few minutes if you see that message.

The app uses file-based storage rather than a database.

## Why the suggestions are playful

The generation logic is designed to be light-hearted rather than predictive. It uses historical draw information and some randomness to build suggestion lines, but it cannot change the fact that each lottery draw is random and independent.

In practice, that means:

- the app can be a fun way to explore patterns in past draws
- the results are not a reliable way to predict future outcomes
- the suggestions are no more likely to win than any other selection

## In short

The app combines historical data, local caching, and simple presentation to create lottery number suggestions for entertainment purposes.