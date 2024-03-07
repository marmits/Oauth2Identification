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


