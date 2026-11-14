@description('Azure region for the plan (e.g. uksouth).')
param location string

@description('App Service plan name.')
param appServicePlanName string

@description('Plan SKU: name, tier, size, capacity, family as needed.')
param sku object

resource appServicePlan 'Microsoft.Web/serverfarms@2024-11-01' = {
  name: appServicePlanName
  location: location
  kind: 'linux'
  sku: sku
  properties: {
    reserved: true
  }
}

output appServicePlanId string = appServicePlan.id
output appServicePlanName string = appServicePlan.name