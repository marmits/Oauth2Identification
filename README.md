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


### COMPOSER
```
lcoal
"require": {
    "marmits/oauth2identification": "*@dev",
}

,
"repositories": [
        {
            "type": "path",
            "url": "../../Bundles/Marmits/Oauth2Identification",
            "options": {
                "symlink": true
            }

        }
]

prod

"repositories": [
   {
       "type": "vcs",
       "url": "git@github.com:marmits/Oauth2Identification.git"
   }
]
   

```

### routes.yaml
```
#index:
#    path: /
#    controller: App\Controller\DefaultController::index

marmitsoauth2identificationbundle:
  resource: "@MarmitsOauth2IdentificationBundle/Resources/config/packages/routing/"
  type:     directory

#surcharge de la route logout du bundle
logout:
  path: /logout
  controller: App\Controller\HomeController::logoutAppli

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
        "webpack-notifier": "^1.15.0",
        "html-loader": "^3.1",
        "file-loader": "^6.2"
    }
}

```

### assets
``` 
add marmits.js => 

import '../vendor/marmits/oauth2identification/src/Resources/public/js/marmitsgoogle';

// import JS du bundle => fonctionnement indépendant route :bundle_private
import Oauth2Lib from "../vendor/marmits/oauth2identification/src/Resources/public/js/Oauth2"
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


