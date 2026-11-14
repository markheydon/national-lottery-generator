@description('Azure region for monitoring resources.')
param location string

@description('Log Analytics workspace name.')
param logAnalyticsWorkspaceName string

@description('Application Insights component name.')
param applicationInsightsName string

@description('Web app resource ID for App Insights connection.')
param webAppId string

resource logAnalytics 'Microsoft.OperationalInsights/workspaces@2023-09-01' = {
  name: logAnalyticsWorkspaceName
  location: location
  properties: {
    sku: {
      name: 'PerGB2018'
    }
    retentionInDays: 30
  }
}

resource applicationInsights 'Microsoft.Insights/components@2020-02-02' = {
  name: applicationInsightsName
  location: location
  kind: 'web'
  properties: {
    Application_Type: 'web'
    WorkspaceResourceId: logAnalytics.id
  }
}

resource webApp 'Microsoft.Web/sites@2024-11-01' existing = {
  name: last(split(webAppId, '/'))
}

resource appInsightsConnection 'Microsoft.Web/sites/siteextensions@2024-11-01' = {
  parent: webApp
  name: 'Microsoft.ApplicationInsights.AzureWebSites'
}

output applicationInsightsConnectionString string = applicationInsights.properties.ConnectionString
output logAnalyticsWorkspaceId string = logAnalytics.id