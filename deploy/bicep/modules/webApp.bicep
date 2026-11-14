@description('Azure region for the web app.')
param location string

@description('Web app name (globally unique).')
param webAppName string

@description('Resource ID of the App Service plan.')
param appServicePlanId string

@description('PHP stack version, e.g. PHP|8.5.')
param phpVersion string

@description('Always On; must be false on Free (F1) SKU.')
param alwaysOn bool

@description('Startup command for nginx on Linux App Service.')
param startupCommand string = 'cp /home/site/wwwroot/deploy/nginx-default /etc/nginx/sites-available/default && service nginx reload'

@description('Optional app settings (no legacy Laravel keys).')
param appSettings object = {}

var defaultSettings = {
  WEBSITE_HTTPLOGGING_RETENTION_DAYS: '7'
}

var mergedSettings = union(defaultSettings, appSettings)

resource webApp 'Microsoft.Web/sites@2024-11-01' = {
  name: webAppName
  location: location
  kind: 'app,linux'
  properties: {
    serverFarmId: appServicePlanId
    httpsOnly: true
    siteConfig: {
      linuxFxVersion: phpVersion
      appCommandLine: startupCommand
      alwaysOn: alwaysOn
      ftpsState: 'FtpsOnly'
      http20Enabled: false
    }
  }
}

resource webAppConfigWeb 'Microsoft.Web/sites/config@2024-11-01' = {
  parent: webApp
  name: 'web'
  properties: {
    linuxFxVersion: phpVersion
    appCommandLine: startupCommand
    alwaysOn: alwaysOn
    ftpsState: 'FtpsOnly'
    scmType: 'GitHubAction'
    minTlsVersion: '1.2'
  }
}

resource webAppAppSettings 'Microsoft.Web/sites/config@2024-11-01' = {
  parent: webApp
  name: 'appsettings'
  properties: mergedSettings
}

output webAppId string = webApp.id
output webAppName string = webApp.name
output defaultHostName string = webApp.properties.defaultHostName