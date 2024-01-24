## INSTALLATION

### BUNDLES
[thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client)    
[thephpleague/oauth2-google](https://github.com/thephpleague/oauth2-google)  
[thephpleague/oauth2-github](https://github.com/thephpleague/oauth2-github)  
[google openid-connect](https://developers.google.com/identity/protocols/oauth2/openid-connect#authenticationuriparameters)


### SYMFONY
```
symfony new appli --version="5.4.*"
```

### doctrine
```
doctrine:
dbal:
url: '%env(resolve:DATABASE_URL)%'

        # IMPORTANT: You MUST configure your server version,
        # either here or in the DATABASE_URL env var (see .env file)
        #server_version: '13'
    orm:
        auto_generate_proxy_classes: true
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
        mappings:
            Marmits\GoogleIdentification:
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/marmits/googleidentification/src/Entity'
                prefix: 'Marmits\GoogleIdentification\Entity'
                alias: GoogleIdentification
```

### COMPOSER
```
lcoal
"require": {
    "marmits/googleidentification": "*@dev",
}

,
"repositories": [
        {
            "type": "path",
            "url": "../../Bundles/Marmits/GoogleIdentification",
            "options": {
                "symlink": true
            }

        }
]

prod

"repositories": [
   {
       "type": "vcs",
       "url": "git@github.com:marmits/googleidentification.git"
   }
]
   

```

### routes.yaml
```
#index:
#    path: /
#    controller: App\Controller\DefaultController::index

marmitsgoogleidentificationbundle:
  resource: "@MarmitsGoogleIdentificationBundle/Resources/config/packages/routing/"
  type:     directory

when@dev:
  _wdt:
    resource: "@WebProfilerBundle/Resources/config/routing/wdt.xml"
    prefix:   /_wdt

  _profiler:
    resource: "@WebProfilerBundle/Resources/config/routing/profiler.xml"
    prefix:   /_profiler
```

### jsrouting-bundle
```
symfony console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json
```

### route principale
```
http://localhost/bundle_private
```

### Clean nodes modules
```

$ rm -rf node_modules
$ rm -f package-lock.json
$ npm cache clean --force
$ npm install
```

### demarrer webpack

`npm run watch`

### package.json

``` 
{
    "devDependencies": {
        "@babel/core": "^7.17.0",
        "@babel/preset-env": "^7.19.3",
        "@hotwired/stimulus": "^3.0.0",
        "@popperjs/core": "^2.11.6",
        "@symfony/stimulus-bridge": "^3.2.0",
        "@symfony/webpack-encore": "^2.1.0"
    },
    "license": "UNLICENSED",
    "private": true,
    "scripts": {
        "dev-server": "encore dev-server",
        "dev": "encore dev",
        "watch": "encore dev --watch",
        "build": "encore production --progress"
    },
    "dependencies": {
        "bootstrap": "^5.2.2",
        "core-js": "^3.25.5",
        "jquery": "^3.6.1",
        "regenerator-runtime": "^0.13.9",
        "sass": "^1.55.0",
        "sass-loader": "12.0.0",
        "webpack": "^5.74.0",
        "webpack-cli": "^4.10.0",
        "webpack-notifier": "^1.15.0"
    }
}


TAF NEW
{
    "devDependencies": {
        "@babel/core": "^7.17.0",
        "@babel/preset-env": "^7.19.3",
        "@fortawesome/fontawesome-free": "^6.5.1",
        "@hotwired/stimulus": "^3.0.0",
        "@popperjs/core": "^2.11.6",
        "@symfony/stimulus-bridge": "^3.2.0",
        "@symfony/webpack-encore": "^2.1.0"
    },
    "license": "UNLICENSED",
    "private": true,
    "scripts": {
        "dev-server": "encore dev-server",
        "dev": "encore dev",
        "watch": "encore dev --watch",
        "build": "encore production --progress"
    },
    "dependencies": {
        "bootstrap": "^5.2.2",
        "core-js": "^3.25.5",
        "jquery": "^3.6.1",
        "regenerator-runtime": "^0.13.9",
        "sass": "^1.55.0",
        "sass-loader": "12.0.0",
        "webpack": "^5.74.0",
        "webpack-cli": "^4.10.0",
        "webpack-notifier": "^1.15.0"
    }
}



```

### assets
``` 
add marmits.js => 


import '../vendor/marmits/googleidentification/src/Resources/public/js/marmitsgoogle';

// import JS du bundle => fonctionnement indépendant route :bundle_private
import Oauth2Lib from "../vendor/marmits/googleidentification/src/Resources/public/js/Oauth2"
let Oauth2 = new Oauth2Lib();

``` 

### webpack.config.js
``` 
.addEntry('marmits', './assets/marmits.js')
.autoProvidejQuery()
.enableSassLoader()

si vhost alias
.setManifestKeyPrefix(' build/')

``` 
- yarn install

- composer update

### Commandes generate static file WEBPACK
``` 
yarn encore dev
npm run watch (live)
``` 

### Secrets
Mettre un password dans une variable SODIUM et accessible comme une variable d'environnement.  
Mais non accessible dans `$_ENV`  

LIST
``` 
symfony console secrets:list --reveal 

symfony console secret:generate-keys
``` 
 SET
``` 
symfony console secrets:set REMEMBER_ME

symfony console secrets:set REMEMBER_ME --prod
``` 

REMOVE 
``` 
symfony console secrets:remove REMEMBER_ME
``` 
To improve performance (i.e. avoid decrypting secrets at runtime), you can decrypt your secrets during deployment to the "local" vault:
``` 
APP_RUNTIME_ENV=dev php bin/console secrets:decrypt-to-local --force
``` 

il faut le composant:
``` 
composer require paragonie/sodium_compat
``` 
**framework.yaml**
par defaut OK normalement (pas besoin de l'ajouter)   
``` 
secrets:
    vault_directory: '%kernel.project_dir%/config/secrets/%kernel.environment%'
``` 

pour forcer les variables de dev en local au lieu de passer par le decryptage
``` 
APP_RUNTIME_ENV=dev php bin/console secrets:decrypt-to-local --force
``` 

ou Dumper les variables d’environnement pour plus de rapidité en développement:
``` 
composer dump-env dev
``` 


### UPDATE
à voir  
[security csrf form](https://symfony.com/doc/current/security/csrf.html)

[changelog](https://github.com/marmits/googleidentification/blob/main/CHANGELOG.md)
