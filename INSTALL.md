### PREREQUISITES
- `>= php7`
- `symfony 5.4`

### COMPOSER
``` 
$ symfony new appli --version="5.4.*" `
```

edit `composer.json`
```
prod
{
    "require": {
        "marmits/oauth2identification": "^1.0"
    },  
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:marmits/Oauth2Identification.git"
        }
    ]
}

dev/local
{
    "require": {
        "marmits/oauth2identification": "*@dev"
    },  
    "repositories": [
        {
            "type": "path",
            "url": "../../Bundles/Marmits/Oauth2Identification",
            "options": {
                "symlink": true
            }
        }
    ]
}
```
`$ composer update`

> execute all recipes to yes

### routes.yaml
```
marmitsoauth2identificationbundle:
  resource: "@MarmitsOauth2IdentificationBundle/Resources/config/packages/routing/"
  type:     directory

#when@dev:
#  _wdt:
#    resource: "@WebProfilerBundle/Resources/config/routing/wdt.xml"
#    prefix: /_wdt

#  _profiler:
#    resource: "@WebProfilerBundle/Resources/config/routing/profiler.xml"
#    prefix: /_profiler

```

### .env
```
DATABASE_URL="mysql://user:pass@127.0.0.1:3306/testoauth?serverVersion=mariadb-10.5.12"
DECRYPT_DATAS_METHOD="AES-256-CBC"
GOOGLE_CLIENT_ID=
GOOGLE_PROJECT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URIS=
GOOGLE_ORIGINS=
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URIS=http://url/getaccesstokengithub
```

### sodium
`$ symfony console secrets:set DECRYPT_DATAS_KEY --random`

### npm
1. replace package.json by
    ```
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
            "webpack-notifier": "^1.15.0",
            "html-loader": "^3.1",
            "file-loader": "^6.2"
        }
    }
    ```
2. run
     `$ npm install` 

### jsrouting-bundle
`$ symfony console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json`

### javascript
- create & add file custom.js in `assets` folder
- custom.js
```
import '../vendor/marmits/oauth2identification/src/Resources/public/js/marmitsgoogle';

// import JS du bundle => fonctionnement indépendant route :bundle_private
import Oauth2Lib from "../vendor/marmits/oauth2identification/src/Resources/public/js/Oauth2"
let Oauth2
```
   
### WEBPACK
#### webpack.config.js
optional: (vhost alias (http://url/alias))
```
.setManifestKeyPrefix(' build/')
```

```
.addEntry('marmits', './assets/custom.js')

// enables Sass/SCSS support
.enableSassLoader()

.addLoader(
    {
        test: /\.html$/,
        loader: "html-loader",
    }
)

.autoProvidejQuery()

const config = Encore.getWebpackConfig();
config.resolve.symlinks = false;

module.exports = config;
```
#### compile
`$ npm run dev`  
or  
`$ npm run watch`

### DOCTRINE
create database testoauth
#### doctrine.yaml
    ```
    doctrine:
        orm:
        mappings: #add
            Marmits\Oauth2Identification:
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/marmits/oauth2identification/src/Entity'
                prefix: 'Marmits\Oauth2Identification\Entity'
                alias: Oauth2Identification
    ```
####  migration   
```
$ composer require symfony/maker-bundle:^1.50 --dev (php 8.0)
$ symfony console make:migration
$ symfo
```

## launch
http://url/bundle_index

### bonus dev
`$ composer require symfony/web-profiler-bundle --dev`