targetScope = 'resourceGroup'

@description('Azure region for all resources.')
param location string = resourceGroup().location

@description('App Service plan name.')
param appServicePlanName string

@description('App Service plan SKU.')
param appServicePlanSku object = {
  name: 'F1'
  tier: 'Free'
  size: 'F1'
  capacity: 1
}

@description('Web app name.')
param webAppName string

@description('PHP runtime stack.')
param phpVersion string = 'PHP|8.5'

@description('User-assigned identity name for GitHub OIDC deploy.')
param oidcIdentityName string

@description('Federated credential name on the OIDC identity.')
param federatedCredentialName string = 'github-main'

@description('GitHub OIDC subject for federated credential.')
param githubOidcSubject string = 'repo:markheydon/national-lottery-generator:ref:refs/heads/main'

@description('Deploy optional Log Analytics and Application Insights.')
param enableMonitoring bool = false

@description('Log Analytics workspace name when monitoring is enabled.')
param logAnalyticsWorkspaceName string = 'law-${webAppName}'

@description('Application Insights name when monitoring is enabled.')
param applicationInsightsName string = 'appi-${webAppName}'

@description('When true, Bicep manages the web app settings map (green-field). Keep false for brown-field to avoid replacing portal settings.')
param manageAppSettings bool = false

@description('Extra web app settings merged with defaults when manageAppSettings is true.')
param appSettings object = {}

@description('Reference an existing OIDC user-assigned identity instead of creating one.')
param adoptExistingOidcIdentity bool = true

var alwaysOn = appServicePlanSku.tier != 'Free'

module appServicePlan 'modules/appServicePlan.bicep' = {
  name: 'appServicePlan'
  params: {
    location: location
    appServicePlanName: appServicePlanName
    sku: appServicePlanSku
  }
}

module webApp 'modules/webApp.bicep' = {
  name: 'webApp'
  params: {
    location: location
    webAppName: webAppName
    appServicePlanId: appServicePlan.outputs.appServicePlanId
    phpVersion: phpVersion
    alwaysOn: alwaysOn
    manageAppSettings: manageAppSettings
    appSettings: appSettings
  }
}

module githubOidc 'modules/githubOidc.bicep' = {
  name: 'githubOidc'
  params: {
    location: location
    oidcIdentityName: oidcIdentityName
    federatedCredentialName: federatedCredentialName
    adoptExistingOidcIdentity: adoptExistingOidcIdentity
    githubOidcSubject: githubOidcSubject
    webAppName: webAppName
  }
}

module monitoring 'modules/monitoring.bicep' = if (enableMonitoring) {
  name: 'monitoring'
  params: {
    location: location
    logAnalyticsWorkspaceName: logAnalyticsWorkspaceName
    applicationInsightsName: applicationInsightsName
    webAppId: webApp.outputs.webAppId
  }
}

output webAppName string = webApp.outputs.webAppName
output webAppDefaultHostName string = webApp.outputs.defaultHostName
output oidcClientId string = githubOidc.outputs.oidcClientId
output appServicePlanName string = appServicePlan.outputs.appServicePlanName
