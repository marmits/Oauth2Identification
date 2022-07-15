const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);



class Client {

    constructor()
    {
        this.numeroClient = null;

        this.identite = {
          id                  :null,
          civilite            :null,
          nom                 :null,
          prenom              :null
        };
        this.adresse = {}
    }


    setNumCli(value)
    {
        this.numeroClient = value;
        return this;
    }

    getNumCli()
    {
      return this.numeroClient;
    }


    setIdentite(value)
    {
        this.identite = value;
        return this;
    }

    getIdentite()
    {
      return this.identite;
    }

    setAdresse(value)
    {
      this.adresse = value;
      return this;
    }

    getGetAdresse()
    {
      return this.adresse;
    }


    // Retourne les informations générale d'un client
    async getInformation()
    {
        let that = this;
        return new Promise(function(resolve,reject) {

            $.ajax({
                url: Routing.generate("get_central_information_client",{
                    numcli: that.numeroClient
                }),
                data: {
                    columns: [
                      'test'
                    ]
                },
                method: "POST",
                success: function(data){
                    that.identite.numeroClient = data.typeclient;
                    that.identite.civilite = data.typeclient;
                    that.identite.nom = data.typeetablissement;
                    that.identite.prenom = data.paniermoyenepi26;
                    return resolve(data);

                },
                error: function(){
                    return reject("Impossible de charger les informations du client.");
                }
            });
        });
    }
}

export default Client;
