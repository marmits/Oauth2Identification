const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);

import utils_display from './utils.js';

import {
  loadingStart,loadingChangeText,onProgressChange,loadingChangeProgressPercent,loadingStop
} from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/loader';



class Adresse {

    constructor() {
        this.appBodyDivHolderElement = $("#app_body");
        this.attributs = {};
    }


    setAttributs(value){

      this.attributs = value;
      return this;
    }

    getAttributs(){
      return this.attributs;
    }

  // Retourne les informations générale d'un client
  async getAdresseListeAttributs()
  {
    let that = this;
    return new Promise(function(resolve,reject) {

      $.ajax({
        url: Routing.generate("get_central_adresse_attributs"),
        method: "GET",
        success: function(data){
          return resolve(data);
        },
        error: function(){
          return reject("Impossible de charger les informations du client.");
        }
      });
    });
  }


}

export default Adresse;
