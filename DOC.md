### Oauth2 apps
Créer les applications sur Github et Google
definit avec :  
[marmits_clientapi.yaml](src/Resources/config/packages/marmits_clientapi.yaml`)

### flux Oauth2
- La page `/privat` demande une authorisation de connexion avec github ou google.  
- Puis apres connexion et redirection, elle récupère les infos de l'utlisateur identifié.  
- L'utilisateur et soit créé ou modifié dans la table `oauth_user`.  
- L'id utilisateur de la table est enregistré dans la session.

### Packages Requirements
- PHP
    - symfony/webpack-encore-bundle (inclus dans le bundle)
- NPM (post install)
    - sass-loader
    - html-loader
    - file-loader
    - sass
    - jquery
    - bootstrap
    - @fortawesome/fontawesome-free

### OUTPUT
>Marmits\Oauth2Identification\Services\UserApi

- ``userApi->getOauthUserIdentifiants()``  
  - Renvoie un objet provenant de BDD en session de type:  
  `Marmits\Oauth2Identification\Dto\IdentifiantsOutput`


### BONUS
[symfony secrets docs](https://symfony.com/doc/5.x/configuration/secrets.html)  
Fournit un objet pour chiffrer une chaine de caractère avec SODIUM. 
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


