using 'main.bicep'

// Production-aligned names for incremental adoption on mhcg-shared-001.
// For a green-field fork, copy main.bicepparam.example and choose unique names.

param location = 'uksouth'
param appServicePlanName = 'ASP-rgmheydon73lotterygenerator-b7d8'
param appServicePlanSku = {
  name: 'F1'
  tier: 'Free'
  size: 'F1'
  capacity: 1
}
param webAppName = 'mheydon73-lottery-generator'
param phpVersion = 'PHP|8.5'
param oidcIdentityName = 'oidc-msi-8d9c'
param federatedCredentialName = 'oidc-credential-8468'
param githubOidcSubject = 'repo:markheydon/national-lottery-generator:ref:refs/heads/main'
param enableMonitoring = false
param appSettings = {}