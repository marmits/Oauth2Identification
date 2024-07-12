### PREREQUISITES
- `>= php8.1`
- `symfony 5.4`

### COMPOSER
`$ symfony new appli --version="5.4.*"`

edit `composer.json`
```
prod
{
    "require": {
        "marmits/oauth2identification": "^2.0"
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


### WEBPACK 
(les npm s'ajustent avec le package.json généré par **symfony/webpack-encore-bundle**)
#### 1. package.json
`$ npm install`

#### 2. npm utlisés dans le projet
```
$ npm i sass-loader
$ npm i html-loader
$ npm i file-loader
$ npm i sass
$ npm i bootstrap
$ npm i @fortawesome/fontawesome-free
$ npm install vue
```

#### 3. webpack.config.js
optional: (vhost alias (http://url/alias))
```
.setPublicPath('/unrep/build') // si alias vhost unrep
.setManifestKeyPrefix(' build/') // si alias
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

.enableTypeScriptLoader()
.enableVueLoader(() => {}, { runtimeCompilerBuild: false })

const config = Encore.getWebpackConfig();
config.resolve.symlinks = false;

module.exports = config;
```
#### 4. webpack compile
`$ npm run dev`  
or  
`$ npm run watch`


### javascript
- create & add file custom.js in `assets` folder
- custom.js
```
import '../vendor/marmits/oauth2identification/src/Resources/public/js/marmitsgoogle';
```
   


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
$ symfony console doctrine:migrations:migrate
```

## launch
http://url/bundle_index

### bonus dev
`$ composer require symfony/web-profiler-bundle --dev`