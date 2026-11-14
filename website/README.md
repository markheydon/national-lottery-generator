# Public documentation site

User-facing documentation for the [National Lottery Number Generator](https://github.com/markheydon/national-lottery-generator), built with [Hugo](https://gohugo.io/) and the [Hextra](https://github.com/imfing/hextra) theme.

Published at [lottery.markheydon.me.uk](https://lottery.markheydon.me.uk/).

## Prerequisites

- Docker or Podman (for containerised builds), or Hugo Extended installed locally.
- Go (for Hugo module theme resolution on first build).

## Local preview

From the repository root:

```shell
./scripts/invoke-hugo-site.sh serve
./scripts/invoke-hugo-site.sh preview   # static build + nginx (default port 1314)
./scripts/invoke-hugo-site.sh build     # production-parity output to website/public/
```

**PowerShell** (Windows):

```powershell
.\scripts\Invoke-HugoSite.ps1 serve
.\scripts\Invoke-HugoSite.ps1 preview
.\scripts\Invoke-HugoSite.ps1 build
```

Use `serve` or `preview` for browser checks. A plain `build` followed by serving `website/public/` at the site root is fine for this site because `baseURL` is the domain root.

With a local Hugo Extended install:

```shell
cd website
hugo server -D
```

## GitHub Pages

The site deploys via GitHub Actions on every push to `main` that touches `website/` (and on `workflow_dispatch`). Pull requests build only, without deploying.

Repository **Settings → Pages** should use **GitHub Actions** as the source.