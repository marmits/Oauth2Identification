## INSTALLATION

### BUNDLES
[thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client)  
[thephpleague/oauth2-google](https://github.com/thephpleague/oauth2-google)  
[google openid-connect](https://developers.google.com/identity/protocols/oauth2/openid-connect#authenticationuriparameters)


### SYMFONY
```
symfony new appli --version="5.4.*"
```

### COMPOSER
```
"require": {
    "marmits/googleidentification": "*@dev",
}

,s
"repositories": [
        {
            "type": "path",
            "url": "../../Bundles/Marmits/GoogleIdentification",
            "options": {
                "symlink": true
            }

        }
]
```

### jsrouting-bundle
```
bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json
```
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


```

### assets
``` 
add marmits.js => import '../vendor/marmits/googleidentification/src/Resources/public/css/marmitsgoogle.scss';
``` 

### webpack.config.js
``` 
.addEntry('marmits', './assets/marmits.js')
.autoProvidejQuery()
.enableSassLoader()

``` 
- yarn install

- composer update

### Commandes generate static file WEBPACK
``` 
yarn encore dev
npm run watch (live)
``` 

### UPDATE

[changelog](https://github.com/marmits/googleidentification/blob/main/CHANGELOG.md)
