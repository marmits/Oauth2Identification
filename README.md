## marmits/oauth2identification 
![](https://img.shields.io/badge/symfony-5.4-blue)
![](https://img.shields.io/badge/-Webpack-blue?style=flat&logo=webpack&logoColor=%23FFF)
[![Source Code](https://img.shields.io/badge/source-marmits/Oauth2Identification-blue.svg?style=flat-square)](https://github.com/marmits/Oauth2Identification/tree/vuejs)

[![release](https://img.shields.io/badge/Oauth2Identification-v2.0.1-blue)](https://github.com/marmits/Oauth2Identification/releases/tag/v2.0.1)



[![Source Code](https://img.shields.io/badge/Oauth2-red?logo=google&logoColor=f5f5f5)]()
[![Source Code](https://img.shields.io/badge/Oauth2-red?logo=github&logoColor=f5f5f5)]()

1. Déclenche une connexion Oauth2 => Google ou Github via 2 boutons d'une page HTML
2. Récupère les informations utilisateur avec l'accestoken fourni
3. Enregistre les données dans la table oauth_user de la bdd
4. Enregistre en session l'id du oauthUser de cette table afin de pouvoir récupérer les informations plus tard
5. Fourni une méthode qui donne les champs email et api_user_id en fonction de l'id stocké en session

- [Installation](INSTALL.md)
- [Documentation](DOC.md)

### BUNDLES USED

[thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client)    
[thephpleague/oauth2-google](https://github.com/thephpleague/oauth2-google)  
[thephpleague/oauth2-github](https://github.com/thephpleague/oauth2-github)  

### google api
[google openid-connect](https://developers.google.com/identity/protocols/oauth2/openid-connect#authenticationuriparameters)

### Stack
![](https://img.shields.io/badge/Symfony-blue?logo=symfony&logoColor=f5f5f5)
![](https://img.shields.io/badge/JavaScript-blue?logo=javascript&logoColor=f5f5f5)
![](https://img.shields.io/badge/npm-blue?logo=npm&logoColor=f5f5f5)
![](https://img.shields.io/badge/vue.js-blue?logo=vue.js&logoColor=f5f5f5)
![](https://img.shields.io/badge/Bootstrap-blue?logo=bootstrap&logoColor=f5f5f5)
![](https://img.shields.io/badge/Sass-blue?logo=sass&logoColor=f5f5f5)
![](https://img.shields.io/badge/FontAwesome-blue?logo=fontawesome&logoColor=f5f5f5)
