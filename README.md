## marmits/Oauth2Identification

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
[google openid-connect](https://developers.google.com/identity/protocols/oauth2/openid-connect#authenticationuriparameters)

 
