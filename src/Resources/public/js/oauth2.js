const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);
class Oauth2 {
    constructor() {
        this.userEmail = null;
        this.template_private = "bundles/marmitsgoogleidentification/templates/template_private.html";
        this.divprivate = $("div.private");
        this.getSaveAccessToken()
        .then((result) => {
            if(result.email !== undefined){
                this.userEmail = result.email;

                //chargement des users bdd
                // chargement du code compliqué
                // verifier que l'utilisateur existe dans la BDD

                // s' il existe
                // construction du formulaire de saisie code
                this.BuildFormPrivate();
                // poste du code saisie
                // on concatene le code saisie avec le code commliqué en password

                // on check le password 
                // si check OK on affiche le resultat BDD 

                
            }
        })
        .catch((e) => {
        console.error(e);
    });
    }


    getSaveAccessToken = async function(){
        let that = this;
        return new Promise(function(resolve,reject) {
            $.ajax({
                url: Routing.generate("bundlesaveaccesstoken"),
                method: "GET",
                success: function(datas){
                    resolve(datas);
                },
                error: function(e){
                    if(e.responseJSON.code === 401) {
                        let result = {
                            "code": e.responseJSON.code,
                            "message": e.responseJSON.message
                        };

                        resolve(result);
                    } else {
                        console.error(e);
                        return reject("Impossible de charger getSaveAccessToken");
                    }
                }
            });
        });
    }

    BuildFormPrivate = function(event){
        let that = this;
        $('<div>').load(that.template_private, function (response, status, xhr) {
            let blocDiv = $(xhr.responseText);
            blocDiv.attr('id', "privateform");
            that.divprivate.append(blocDiv);

        });
    }

}

export default Oauth2;