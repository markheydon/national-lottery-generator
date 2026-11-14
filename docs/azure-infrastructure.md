# Azure infrastructure (Bicep)

Maintainer notes for repeatable App Service deployment. Closes the gap described in [issue #194](https://github.com/markheydon/national-lottery-generator/issues/194).

## Two GitHub workflows

| Workflow | When | Purpose |
|----------|------|---------|
| [deploy-azure-webapp.yml](../.github/workflows/deploy-azure-webapp.yml) | Every push to `main` | Build PHP and deploy application code (no Bicep) |
| [deploy-azure-infrastructure.yml](../.github/workflows/deploy-azure-infrastructure.yml) | Manual **workflow_dispatch** only | Create or update Azure resources from `deploy/bicep/` |

Pull requests that touch `deploy/bicep/**` run [bicep.yml](../.github/workflows/bicep.yml) (`az bicep build`) — compile only, no subscription access.

## Production subscription

The live web app is in subscription **mhcg-shared-001** (`d2626ad0-5fb0-45ba-8746-602220257bc6`), resource group `rg-mheydon73-lottery-generator`, UK South.

## GitHub repository configuration

Configure these before OIDC app deploy or Bicep (if not already set):

| Name | Type | Example / notes |
|------|------|------------------|
| `AZURE_CLIENT_ID` | Secret | User-assigned MI client ID (`oidc-msi-8d9c` in production) |
| `AZURE_TENANT_ID` | Secret | Entra tenant ID |
| `AZURE_SUBSCRIPTION_ID` | Secret | Target subscription ID |
| `AZURE_WEBAPP_NAME` | Variable | `mheydon73-lottery-generator` |

The deploy identity needs **Website Contributor** on the web app. The infrastructure workflow identity needs **Contributor** (or equivalent) on the target resource group to run Bicep — the deploy MI alone may be insufficient for infra deploys.

After OIDC CD succeeds, remove the legacy publish-profile secret if it is still present.

## First Bicep deploy

1. Merge the Bicep templates; CI validates with `bicep build`.
2. Run **Deploy Azure infrastructure (Bicep)** with **what-if only** enabled (default).
3. When satisfied, run again with **what-if only** disabled (`Incremental` mode — does not delete resources absent from the template).

For a **green-field** fork, copy `deploy/bicep/main.bicepparam.example` to `main.bicepparam` and use unique plan, app, and identity names.

## Production alignment (post-merge)

Apply via Bicep incremental deploy and/or portal:

1. PHP runtime **8.5** (`linuxFxVersion`).
2. Startup command using `deploy/nginx-default` (see [deploy/README.md](../deploy/README.md)).
3. Remove legacy app settings: `APP_DEBUG`, `APP_KEY`, `APP_URL`, `CACHE_DRIVER`, `FILESYSTEM_DISK`.
4. Optional: `LOTTERY_DOWNLOAD_TIMEOUT`, `LOTTERY_BACKUP_MAX` only when needed.

## Local commands

```bash
az account set --subscription "<subscription-id>"
az group create --name rg-mheydon73-lottery-generator --location uksouth
az deployment group what-if \
  --resource-group rg-mheydon73-lottery-generator \
  --template-file deploy/bicep/main.bicep \
  --parameters deploy/bicep/main.bicepparam
```

See [deploy/bicep/](../deploy/bicep/) for modules and parameters.