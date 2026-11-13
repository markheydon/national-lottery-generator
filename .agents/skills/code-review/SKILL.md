---
name: code-review
description: >-
  Review repository changes as a principal architect would for pull request approval:
  bugs, security, architecture, complexity, and maintainability only. Use when
  the user runs a code-review command, asks for a code review, or wants findings on a
  branch, PR, or local diff before merge. Read-only unless the user explicitly
  asks to implement fixes.
---

# Code review

Review as a principal architect and senior developer. Treat the change set like a pull request from another experienced developer.

Focus only on issues that would influence your decision to **approve or reject** the pull request. Findings must be the primary focus, ordered by severity. Do not make code changes unless the user explicitly asks for them.

For automated Bugbot or security-focused subagent reviews, use `/review-bugbot` and `/review-security` unless the user wants this review only.

## Scope

Decide what to diff before reading code:

| User intent | Diff |
| --- | --- |
| Default (branch work, PR, "review my changes") | Changes since merge-base with the repository default branch (usually `main`), including committed, staged, and unstaged edits |
| "Uncommitted", "dirty", "working tree" only | Staged + unstaged diff only |
| Named PR, branch, or GitHub link | Check out that head branch locally when needed, then use the branch diff row above |

Repository path: use the git root that contains the changes (may be a subfolder of the workspace).

If there is no diff, say so in one sentence and stop.

## Focus on finding

1. Bugs, defects, or logic errors.
2. Security concerns (ground in [AGENTS.md](../../../AGENTS.md) and [CONTRIBUTING.md](../../../CONTRIBUTING.md): no secrets in repo, HTTPS for CSV fetch, safe output in templates).
3. Violations of PHP and project conventions in [AGENTS.md](../../../AGENTS.md) (PSR-12, business logic in `src/Services/Lottery/`, entertainment-only user messaging).
4. Architectural issues that will become expensive to fix later (routing, game dispatch, download/cache layers).
5. Code that is unnecessarily complex.
6. Missing or inadequate tests when **behaviour** changed (PHPUnit in `tests/`; Playwright in `tests/e2e/` when UI or routes change).

## Do not comment on

- Formatting issues CI and Pint own (unless CI would fail).
- Naming preferences unless they materially affect readability.
- Personal coding style preferences.
- Minor optimisations without measurable value.
- Features that have not yet been implemented.
- Planned future work or open issues unless the current code creates a defect.
- Stub implementations that are clearly placeholders.

## How to review

1. Establish the diff (see **Scope**).
2. Read changed code and enough surrounding context to judge behavior and integration.
3. Trace call paths for game generate/download, `GameController` dispatch, and CSV caching when those areas change.
4. Check templates and public copy when user-facing behaviour changes (entertainment disclaimer).
5. When unsure whether something is a real issue, investigate (read callers, run targeted tests) before reporting.

Meaningful PHP changes should pass `composer run test` and `composer run lint` from [AGENTS.md](../../../AGENTS.md). Consider Playwright E2E when routes or UI change (CI-only; see `tests/e2e/`).

## Output

Lead with findings. Keep summary and praise minimal.

For each issue, provide:

- **Severity** - Critical, High, Medium, or Low.
- **Location** - `path/to/file.php:line` (or code citation when reporting in chat).
- **Why it matters** - tie to approve/reject risk.
- **Suggested improvement** - concrete, actionable fix direction (do not implement unless asked).

Present issues in a compact markdown table sorted by severity (highest first):

| Severity | Location | Why it matters | Suggested improvement |
| --- | --- | --- | --- |
| … | … | … | … |

Use UK English in prose.

## Closing

End with a brief explanation of overall merge readiness (approve, approve with nits, or reject) based only on the issues above.

If no significant issues are found, say so explicitly. Mention residual risks or test gaps only when they are material to approval.

## After the review

- Do not implement fixes, commit, or push unless the user explicitly asks.
- If the user asks to fix findings, apply minimal correct diffs, match existing conventions, then run `composer run test` and `composer run lint` before claiming the review is clear.
- Respect signed-commit rules in [AGENTS.md](../../../AGENTS.md) when committing.
