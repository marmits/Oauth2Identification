# Bundle Bloc Adresse Client Maximo

- [maximo/adresse](http://gitlab.maximo.fr:81/dev/projets-web/commun/MaximoBlocAdresse/)  

## REQUIREMENTS
- yarn 1.22.15
- npm 6.12.1
- PHP 7

## INSTALLATION

### 1. symfony new adresse_projet --version="5.4.*"
### 2. composer.json (SYMFONY)
```
"minimum-stability": "dev",
"prefer-stable": false,
"require": {
    "maximo/adresse": "*@dev"
},    
"require-dev": {
    "symfony/maker-bundle": "^1.38",
    "symfony/monolog-bundle": "^3.8",
    "symfony/stopwatch": "5.4.*",
    "symfony/web-profiler-bundle": "5.4.*"
},
"repositories": [
    {
        "type": "vcs",
        "url": "git@gitlab.maximo.fr:dev/projets-web/commun/MaximoApiConnector.git"
    },
    {
        "type": "vcs",
        "url": "git@gitlab.maximo.fr:dev/projets-web/commun/MaximoBlocAdresse.git"
    },
    {
        "type": "vcs",
        "url": "git@gitlab.maximo.fr:dev/projets-web/commun/MaximoInterfaceGraphique.git"
    }
],
```

### 3. package.json (NODES)
```
{
   "devDependencies": {
       "@hotwired/stimulus": "^3.0.0",
       "@symfony/stimulus-bridge": "^3.0.0",
       "@symfony/webpack-encore": "^2.1.0",
       "bootstrap": "4.6.1",
       "core-js": "^3.22.8",
       "dotenv-webpack": "^7.1.1",
       "file-loader": "^6.2.0",
       "jquery": "^3.2.1",
       "node-sass": "^7.0.1",
       "regenerator-runtime": "^0.13.2",
       "sass": "^1.53.0",
       "sass-loader": "12.0.0",
       "webpack": "^5.74.0",
       "webpack-notifier": "^1.6.0"
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
       "@hotwired/stimulus": "^3.0.0",
       "@symfony/stimulus-bridge": "^3.0.0",
       "@symfony/webpack-encore": "^2.1.0",
       "bootstrap": "4.6.1",
       "core-js": "^3.22.8",
       "dotenv-webpack": "^7.1.1",
       "file-loader": "^6.2.0",
       "html-loader": "^3.1.2",
       "jquery": "^3.2.1",
       "node-sass": "^7.0.1",
       "popper.js": "^1.16.1",
       "regenerator-runtime": "^0.13.2",
       "sass": "^1.53.0",
       "sass-loader": "12.0.0",
       "webpack": "^5.74.0",
       "webpack-notifier": "^1.6.0"
   }
}
```

### 4. Dans la console répetoire du projet
    - yarn install
    - composer install
    - composer update
    - composer update (2ème fois pour appliquer les recettes et charger les bundles dans l'autoload)

### 5. webpack.config.js =>  les répertoire du projet

```
const Dotenv = require('dotenv-webpack')
const Encore = require('@symfony/webpack-encore');


// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */

    .addEntry('adresse', './assets/adresse.js')

    /*
    .copyFiles({
      from: './vendor/maximo/adresse/src/Resources/public/blocs',
      to: 'blocs/[path][name].[ext]'
    })

    .copyFiles({
      from: './vendor/maximo/interfacegraphique/src/Resources/public/themes/callcenter/img',
      to: 'img/[path][name].[ext]'
    })

    .copyFiles({
      from: './vendor/maximo/interfacegraphique/src/Resources/public/themes/callcenter/fonts',
      to: 'fonts/[path][name].[ext]'
    })

    .copyFiles({
      from: './vendor/maximo/interfacegraphique/src/Resources/public/themes/callcenter/css',
      to: 'css/[path][name].[ext]'
    })
    
    */

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    //.enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .addLoader(
      {
        test: /\.html$/,
        loader: "html-loader",

      }
    )
    .addLoader({ test: /\.handlebars$/, loader: 'handlebars-loader' })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    .addPlugin(new Dotenv({
      path: './.env'
    }))
;
const config = Encore.getWebpackConfig();
config.resolve.symlinks = false;

module.exports = config;

```


### 6. init JS adresse.js et panier.js dans le projet
voir documentation 
```
   create assets/adresse.js
   ex:
    /*
    * Welcome to your app's main JavaScript file!
    *
    * We recommend including the built version of this JavaScript file
    * (and its CSS file) in your base layout (base.html.twig).
    */

    // any CSS you import will output into a single css file (app.css in this case)
    'use strict';
    
    /* parametres d'entrées */
    let nameAdresse = "maximoadresse from adresse.js"
    
    let adresse = null;
    let numcli_input = $("#numcli");
    let charge_button = $("#chargeadresse");
    let type_adresse = $("#type_adresse");
    
    import maximoadresse from '../vendor/maximo/adresse/src/Resources/public/js/maximoadresse';
    
    let ApiConnectorParams = {
      "optionsFromJS" : {
        "test": true
      }
    }

    let input = {
      'input_adresseMaximo' : {
        currentClientId: '6964685',
        type_adresse: 'livraison',
        ApiConnectorParams: ApiConnectorParams
      }
    }    
    
    //par default
    numcli_input.val(input.input_adresseMaximo.currentClientId);
    
    charge_button.on( "click",  function(e) {
      e.preventDefault();
      e.stopPropagation();
      if(numcli_input.val() !== "") {
        input.input_adresseMaximo.currentClientId = numcli_input.val();
        if (adresse !== undefined) {
          adresse=new maximoadresse();
          adresse.create(nameAdresse, input);
        } else {
          console.error("impossible de créer le panier");
        }
      }
    });
    
    
    type_adresse.on( "change",  function(e) {
      e.preventDefault();
      e.stopPropagation();
      charge_button.trigger("click");
    });
    
    
    document.addEventListener('adresseLoaded', (e)=>{
      console.log("%c"+adresse.getNameAdresse(), 'color: green');
      console.log("%c"+e.detail.retour, 'color: blue');
      console.log("%cClient: "+e.detail.datas.infos.client.numeroClient, 'color: blue');
    });
    
    
    import './styles/app.css';
```

### 7. routes.yaml
```
#index:
#    path: /
#    controller: App\Controller\DefaultController::index


maximoadressebundle:
  resource: "@MaximoAdresseBundle/Resources/config/packages/routing/"
  type:     directory


_wdt:
  resource: "@WebProfilerBundle/Resources/config/routing/wdt.xml"
  prefix:   /_wdt

_profiler:
  resource: "@WebProfilerBundle/Resources/config/routing/profiler.xml"
  prefix:   /_profiler
``` 

### 8. générer les routes  
   `bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json`
### 9.env
```
   PROJECT_DIR=%kernel.project_dir%   
   CLIENT_JSON_FILE=%kernel.project_dir%/vendor/maximo/apiconnector/src/Resources/config/packages/client.json
   ATTRIBUTS_ADRESSE_JSON_FILE=%kernel.project_dir%/vendor/maximo/apiconnector/src/Resources/config/packages/attributs.json
   COMPLEMENT_NUMERO=%kernel.project_dir%/vendor/maximo/apiconnector/src/Resources/config/packages/complement_numero.json
   TYPE_VOIE=%kernel.project_dir%/vendor/maximo/apiconnector/src/Resources/config/packages/type_voie.json
```

### 10. générer les fichiers CSS, JS, ETC ...  
    - `yarn encore dev` ("pour générer les fichiers statiques")
    - `npm run watch` ("live génération")


## SURCHARGE 
### 1. controller
- TestAdresseController.php
```
<?php
declare(strict_types=1);
namespace App\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Maximo\Adresse\Controller\AdresseController;
use Maximo\Adresse\Service\ProviderService;
/**
 *
 */
class TestAdresseController extends  AdresseController
{
    /**
     * @param ContainerInterface $container
     * @param ProviderService $providerService
     */
    public function __construct(ContainerInterface $container, ProviderService $providerService)
    {
        parent::__construct($container, $providerService);
    }

    /**
     *
     * @Route("/adresse_test", name="adresse_test")
     * @param Request $request
     * @return Response
     */
    public function test(Request $request): Response
    {
        return $this->render('testadresse.html.twig', [
        ]);
    }
}
```

### 2. templates
- testadresse.html.twig
```
{% extends '@MaximoAdresse/default.html.twig' %}
```


## Route par défaut:
```
/bundle_adresse
```


