### Fonctionnement

1. Déclenche une connection Oauth2 => Google ou Github
2. Récupère les informations utilisateur avec l'accestoken fourni
3. Enregistre les données dans la table oauth_user de la bdd
4. Enregistre en session l'id du oauthUser de cette table afin de pouvoir récupérer les informations plus tard
5. Fourni une méthode qui donne les champs email et api_user_id en fonction de l'id stocké en session


### OUTPUT
`Marmits\Oauth2Identification\Services\UserApi`

`userApi->getOauthUserIdentifiants();`  
renvoi un objet provenant de BDD de type:  
`Marmits\Oauth2Identification\Dto\IdentifiantsOutput`
