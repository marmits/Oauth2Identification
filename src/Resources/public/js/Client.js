const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);



class Client {

    constructor()
    {
        this.numeroClient = null;

        this.identite = {
          civilite            :null,
          nom                 :null,
          prenom              :null,
          numeroClient        : this.getNumCli()
        };

        this.adresse = {};
        this.attributs = {};

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

    getAdresse()
    {
      return this.adresse;
    }

    setAttributs(value)
    {
      this.attributs = value;
      return this;
    }

    getAttributs()
    {
      return this.attributs;
    }


    // Retourne les informations générale d'un client
    async getInformations(criteres)
    {

        let numcli = criteres.currentClientId;
        let type_adresse = criteres.type_adresse;
        let that = this;
        return new Promise(function(resolve,reject) {
            $.ajax({
                url: Routing.generate("get_central_information_client",{
                    numcli: numcli,
                    type_adresse: type_adresse
                }),
                data: {
                    columns: [
                      'test'
                    ]
                },
                method: "POST",
                success: function(data){
                    if(data.error.erreur === true){
                      reject(data.error);
                    } else {
                      that.setNumCli(data.identite.numeroClient);
                      that.setIdentite(data.identite);
                      that.setAdresse(data.adresse);
                      that.setAttributs(data.attributs);
                      return resolve(data);
                    }
                    },
                error: function(){
                    return reject("Impossible de charger les informations du client.");
                }
            });
        });
    }

   
}

export default Client;
