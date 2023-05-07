# National Lottery Generator App

## Overview

Just for fun, makes an attempt at 'guessing' the Lotto numbers using a half-arsed bit of logic.

## Deployment on Azure App Services

I have this web app deployed on Azure App Services. The following sections describe the configuration required to get it working.

### Nginx Configuration

Supplied [`nginx-default`](nginx-default) file can be used by setting the following in the Configuration->General settings->Startup Command

```
cp /home/site/wwwroot/nginx-default /etc/nginx/sites-available/default && service nginx reload
```

### Database Configuration (Azure MySQL)

The following environment variables need to be set in the Configuration->Application settings section to use Azure MySQL:

```
[
    {
        "name": "DB_CONNECTION",
        "value": "azure-mysql",
        "slotSetting": false
    },
    {
        "name": "MYSQL_ATTR_SSL_CA",
        "value": "/home/site/wwwroot/ssl/DigiCertGlobalRootCA.crt.pem",
        "slotSetting": false
    }
]
```

The `DB_CONNECTION` value is used in the `config/database.php` file to determine which database connection to use, in this case, the `azure-mysql` connection. The `azure-mysql` connection is defined in the `config/database.php` file to use the various AZURE_MYSQL_* environment variables that get created when using the Azure Marketplace Web App + Database offer.

The SSL certificate is required to connect to Azure MySQL. The certificate is in the `ssl` folder in the root of the application.

### Laravel Configuration

As with all Laravel apps, the `APP_KEY` environment variable needs to be set in the Configuration->Application settings section.

Additionally, once the database is setup, the following command need to be run from SSH in the `wwwroot` folder of the application to create the tables and seed the database with the pre-set list of games.

```
php artisan migrate:fresh --seed
```