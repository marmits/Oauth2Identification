### Fonctionnement

1. Déclenche une connection Oauth2 => Google ou Github
2. Récupère les informations utilisateur avec l'accestoken fourni
3. Enregistre les données dans la table oauth_user de la bdd
4. Enregistre en session l'id du oauthUser de cette table afin de pouvoir récupérer les informations plus tard
5. Fourni une méthode qui donne les champs email et api_user_id en fonction de l'id stocké en session


### OUTPUT
>Marmits\Oauth2Identification\Services\UserApi

- ``userApi->getOauthUserIdentifiants()``  
  - Renvoie un objet provenant de BDD en session de type:  
  `Marmits\Oauth2Identification\Dto\IdentifiantsOutput`


### BONUS
[symfony secrets docs](https://symfony.com/doc/5.x/configuration/secrets.html)  
Fournit un objet pour chiffrer avec SODIUM, une chaine de charactere.
>Marmits\Oauth2Identification\Services\Encryption
1. Méthodes: 
   - getParms
   - decrypt
   - encrypt
2. .env 
   - DECRYPT_DATAS_METHOD="AES-256-CBC"
   - DECRYPT_DATAS_KEY `$ symfony console secrets`
3. parameters encryption
   - marmits.clientapi_yaml


