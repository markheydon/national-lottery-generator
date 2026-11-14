@description('Azure region for the user-assigned managed identity.')
param location string

@description('Name of the user-assigned managed identity used for GitHub OIDC.')
param oidcIdentityName string

@description('Federated credential resource name.')
param federatedCredentialName string = 'github-main'

@description('Subject claim for GitHub Actions, e.g. repo:org/repo:ref:refs/heads/main.')
param githubOidcSubject string

@description('Web app name (role assignment scope).')
param webAppName string

var websiteContributorRoleDefinitionId = 'de139f84-1756-47ae-9b75-8cf8398783db'

resource oidcIdentity 'Microsoft.ManagedIdentity/userAssignedIdentities@2023-01-31' = {
  name: oidcIdentityName
  location: location
}

resource federatedCredential 'Microsoft.ManagedIdentity/userAssignedIdentities/federatedIdentityCredentials@2023-01-31' = {
  parent: oidcIdentity
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

resource websiteContributor 'Microsoft.Authorization/roleAssignments@2022-04-01' = {
  name: guid(webApp.id, oidcIdentity.id, websiteContributorRoleDefinitionId)
  scope: webApp
  properties: {
    roleDefinitionId: subscriptionResourceId('Microsoft.Authorization/roleDefinitions', websiteContributorRoleDefinitionId)
    principalId: oidcIdentity.properties.principalId
    principalType: 'ServicePrincipal'
  }
}

output oidcClientId string = oidcIdentity.properties.clientId
output oidcPrincipalId string = oidcIdentity.properties.principalId
output oidcIdentityId string = oidcIdentity.id