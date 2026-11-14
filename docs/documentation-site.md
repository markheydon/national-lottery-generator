# Public documentation site (`website/`)

The user-facing site is built with [Hugo](https://gohugo.io/) and the [Blowfish](https://blowfish.page/) theme. Content lives in `website/content/`.

## Edit content

| Page | Source |
|------|--------|
| Home | `website/content/_index.md` |
| Guides | `website/content/guides/` |

Preserve entertainment-only messaging and responsible gambling links on every user-facing page. Align game lists and behaviour claims with `config/games.php` and `src/`.

Configuration: `website/hugo.yaml`. Custom domain: `website/static/CNAME`.

## Local preview

From the repository root (Docker or Podman required unless Hugo Extended is installed locally):

```bash
./scripts/invoke-hugo-site.sh preview
./scripts/invoke-hugo-site.sh serve
./scripts/invoke-hugo-site.sh build
```

See [`website/README.md`](../website/README.md) and [`scripts/invoke-hugo-site.sh`](../scripts/invoke-hugo-site.sh).

## Deployment

GitHub Actions workflow [`.github/workflows/hugo.yml`](../.github/workflows/hugo.yml) builds on pull requests and deploys to GitHub Pages on every push to `main` that touches `website/`.

Repository **Settings → Pages** must use **GitHub Actions** as the source (not `/docs` on a branch).