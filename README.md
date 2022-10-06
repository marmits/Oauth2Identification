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
        "@babel/preset-env": "^7.16.0",
        "@hotwired/stimulus": "^3.0.0",
        "@symfony/stimulus-bridge": "^3.2.0",
        "@symfony/webpack-encore": "^2.1.0",
        "core-js": "^3.23.0",
        "regenerator-runtime": "^0.13.9",
        "sass": "^1.55.0",
        "sass-loader": "^12.6.0",
        "webpack": "^5.74.0",
        "webpack-cli": "^4.10.0",
        "webpack-notifier": "^1.15.0"
    },
    "license": "UNLICENSED",
    "private": true,
    "scripts": {
        "dev-server": "encore dev-server",
        "dev": "encore dev",
        "watch": "encore dev --watch",
        "build": "encore production --progress"
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
