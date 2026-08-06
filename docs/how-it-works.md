---
layout: default
title: How It Works
---

# How It Works

The app follows a simple workflow. A visitor chooses a game, the app checks whether fresh draw history is available, and then it generates a set of number suggestions for that game.

## What the app uses

The public interface is a small PHP front controller with plain templates. The supported games are defined in configuration and are rendered as cards on the home page.

## How data is refreshed

When a generator page is requested, the app checks whether the relevant draw-history file is present and fresh enough to use. If it is missing or older than 24 hours, the app downloads a new copy from the National Lottery website and stores it locally under `storage/app/lottery/`.

The app uses file-based storage rather than a database.

## Why the suggestions are playful

The generation logic is designed to be light-hearted rather than predictive. It uses historical draw information and some randomness to build suggestion lines, but it cannot change the fact that each lottery draw is random and independent.

In practice, that means:

- the app can be a fun way to explore patterns in past draws
- the results are not a reliable way to predict future outcomes
- the suggestions are no more likely to win than any other selection

## In short

The app combines historical data, local caching, and simple presentation to create lottery number suggestions for entertainment purposes.
