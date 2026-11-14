@description('Azure region for the user-assigned managed identity.')
param location string

@description('Name of the user-assigned managed identity used for GitHub OIDC.')
param oidcIdentityName string

@description('When true, reference an existing identity (brown-field). When false, create a new identity.')
param adoptExistingOidcIdentity bool = true

@description('When false, skip Website Contributor role assignment (brown-field when RBAC already exists).')
param assignWebsiteContributor bool = true

@description('Federated credential resource name.')
param federatedCredentialName string = 'github-main'

@description('Subject claim for GitHub Actions, e.g. repo:org/repo:ref:refs/heads/main.')
param githubOidcSubject string

@description('Web app name (role assignment scope).')
param webAppName string

var websiteContributorRoleDefinitionId = 'de139f84-1756-47ae-9b75-8cf8398783db'

resource oidcIdentityNew 'Microsoft.ManagedIdentity/userAssignedIdentities@2023-01-31' = if (!adoptExistingOidcIdentity) {
  name: oidcIdentityName
  location: location
}

resource oidcIdentityExisting 'Microsoft.ManagedIdentity/userAssignedIdentities@2023-01-31' existing = if (adoptExistingOidcIdentity) {
  name: oidcIdentityName
}

var oidcPrincipalId = adoptExistingOidcIdentity ? oidcIdentityExisting!.properties.principalId : oidcIdentityNew!.properties.principalId
var oidcClientIdOut = adoptExistingOidcIdentity ? oidcIdentityExisting!.properties.clientId : oidcIdentityNew!.properties.clientId
var oidcResourceId = adoptExistingOidcIdentity ? oidcIdentityExisting!.id : oidcIdentityNew!.id

resource federatedCredentialExisting 'Microsoft.ManagedIdentity/userAssignedIdentities/federatedIdentityCredentials@2023-01-31' = if (adoptExistingOidcIdentity) {
  parent: oidcIdentityExisting
  name: federatedCredentialName
  properties: {
    audiences: [
      'api://AzureADTokenExchange'
    ]
    issuer: 'https://token.actions.githubusercontent.com'
    subject: githubOidcSubject
  }
}

resource federatedCredentialNew 'Microsoft.ManagedIdentity/userAssignedIdentities/federatedIdentityCredentials@2023-01-31' = if (!adoptExistingOidcIdentity) {
  parent: oidcIdentityNew
  name: federatedCredentialName
  properties: {
    audiences: [
      'api://AzureADTokenExchange'
    ]
    issuer: 'https://token.actions.githubusercontent.com'
    subject: githubOidcSubject
  }
}

resource webApp 'Microsoft.Web/sites@2024-11-01' existing = {
  name: webAppName
}

resource websiteContributor 'Microsoft.Authorization/roleAssignments@2022-04-01' = if (assignWebsiteContributor) {
  name: guid(webApp.id, oidcResourceId, websiteContributorRoleDefinitionId)
  scope: webApp
  properties: {
    roleDefinitionId: subscriptionResourceId('Microsoft.Authorization/roleDefinitions', websiteContributorRoleDefinitionId)
    principalId: oidcPrincipalId
    principalType: 'ServicePrincipal'
  }
}

output oidcClientId string = oidcClientIdOut
output oidcPrincipalId string = oidcPrincipalId
output oidcIdentityId string = oidcResourceId
