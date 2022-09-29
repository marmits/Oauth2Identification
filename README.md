# MAXIMO ADRESSE
### PRESENTATION
Le but de ce bundle est de partager les fonctionnalités de création / MAJ / contrôle des adresses clients quelle que soit l’application le nécessitant (MCLI01 / NOVERT / Centre d’appels / IntMax / …)

Compatabilité >= symfony 5.4
[maximo/apiconnector](http://gitlab.maximo.fr:81/dev/projets-web/commun/MaximoApiConnector)  
[maximo/interfacegraphique](http://gitlab.maximo.fr:81/dev/projets-web/commun/MaximoInterfaceGraphique)

## INSTALL NODES ou YARN ou les 2
1. MODULES -> Jquery et bootstrap

```
yarn add bootstrap@4.6.1
yarn add jquery@3.2.1
```

2. tools  
   `npm install dotenv-webpack --save-dev`
   `yarn add sass-loader@12.0.0 node-sass --dev`


3. yarn installed modules
```
  "dependencies": {
        "@hotwired/stimulus": "^3.0.0",
        "@symfony/stimulus-bridge": "^3.0.0",
        "@symfony/webpack-encore": "^2.0.0",
        "bootstrap": "4.6.1",
        "core-js": "^3.22.8",
        "dotenv-webpack": "^7.1.0",
        "file-loader": "^6.2.0",
        "html-loader": "^3.1.2",
        "jquery": "^3.2.1",
        "node-sass": "^7.0.1",
        "popper.js": "^1.16.1",
        "regenerator-runtime": "^0.13.2",
        "sass": "^1.52",
        "sass-loader": "12.0.0",
        "webpack-notifier": "^1.6.0"
    }
```

installer les dépendances:
`yarn install`

4. Composer
   `require  maximo/adresse`     
   avec le repository de http://gitlab.maximo.fr:81/

### PARAMETRAGES
adresse_maximo.yaml  
exemple: 
```
parameters:
  

```


###  WebPack CONFIG
- Dans `webpack.config.js`:
  Désactiver tout Stimulus (optionnel)  
  Ajouter:
```
    const Dotenv = require('dotenv-webpack')
    const Encore = require('@symfony/webpack-encore');

   .addEntry('adresse', './assets/adresse.js')

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
   
   .addLoader(
      {
        test: /\.html$/,
        loader: "html-loader",

      }
    )
   .addLoader({ test: /\.handlebars$/, loader: 'handlebars-loader' })

   // enables Sass/SCSS support
   .enableSassLoader()
   
   .autoProvidejQuery()

   .addPlugin(new Dotenv({
      path: './.env'
   }))

```

- Compiler webpack:  
  `npm run dev`  
  `npm run watch`  => en live

### Générer les routes:
- Exécuter un dump des routes à l'aide du `bundle jsrouting-bundle` avec la commande:
```
bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json
```
