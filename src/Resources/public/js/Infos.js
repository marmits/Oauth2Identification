const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);

import utils_display from './utils.js';

import {
  loadingStart,loadingChangeText,onProgressChange,loadingChangeProgressPercent,loadingStop
} from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/loader';



class Infos {

    constructor() {
      this.template_bloc_infos= "build/blocs/template_bloc_infos.html";
      this.divBlocInfos = null;
      this.enabled = false;
      this.enabled_infos = false;
      this.elementBlocs = $("#blocsInfos");
      this.datas_infos = {
        "type_adresse" : "",
        "etablissement" : "",
        "type_etablissement": "",
        "numtou": "",
        "numrep": ""
      };

      this.titre = "dd";

      this.eventBuilderInfos = new CustomEvent('buildInfos', {
        detail: {
          name: 'buildInfos'
        }
      });

      // Chargement de l évenement
      this.loadEventsInfos = async function() {
        let that = this;
        let element = $(that.getElementBlocs());
        let blocsInfos = '#' + element.attr('id');
        let el = document.querySelector(blocsInfos);

        ;
        //accrochage de l'evement à l'élément qui doit etre mis à jour avec les données injectées
        return await new Promise(function(resolve,reject) {

          el.addEventListener('buildInfos', function (e) {
            that.refreshInfos(e);
          }, { once: true });
          el.dispatchEvent(that.eventBuilderInfos);
          resolve("ok  addEventListener->buildInfos in loadEventsInfos");
        });
      };

      this.refreshInfos = async function(event) {
        event.preventDefault();
        event.stopPropagation();
        let that = this;
        let titreEle =  that.getElementBlocs().find("h1.titre").find("span");
        titreEle.html(that.getTitre());
      }


    }

    setTitre(value){
      this.titre = value;
      return this;
    }

    getTitre(){
      return this.titre;
    }

    setEnabled(value){
      this.enabled = value;
      return this;
    }

    getEnabled(){
      return this.enabled;
    }

    setEnableInfos(value){
      this.enabled_infos = value;
      return this;
    }

    getEnableInfos(){
      return this.enabled_infos;
    }

    setDatasInfos(value){
      this.datas_infos = value;
      return this;
    }

    getDatasInfos(){
      return this.datas_infos;
    }



    setDivBlocInfos(value){
      this.divBlocInfos = value;
      return this;
    }

    getDivBlocInfos(){
      return this.divBlocInfos;
    }

    getElementBlocs(){
    return this.elementBlocs;

  }

    chargeBlocsInfos = async function() {
      let that = this;
      return await new Promise(function (resolve, reject) {
        if(that.getEnabled() === true) {
          that.loadTemplateFileInfos()
            .then((divBlocs) => {
              that.setDivBlocInfos(divBlocs);
              return that.displayBlocsInfos()
                .then((resDisplay) => {
                  resolve("chargeBlocsInfos ok enabled true");
                })
                .catch((e) => {
                  reject(e);
                });
            });
        } else {
          resolve("chargeBlocsInfos ok enabled false");
        }
      });
    }

    loadTemplateFileInfos = async function(){
      let that = this;
      let blocsDiv = [];
      let datas = that.getDatasInfos();

      return await new Promise(function(resolve,reject) {
        let blocDiv = null;
        $('<div>').load(that.template_bloc_infos,function( response, status, xhr ) {
          blocDiv = $(xhr.responseText);
          let libelle_type_adresse = datas.type_adresse;
          let index_type_adresse = 0;

          switch(datas.type_adresse) {
            case 'livraison':
              index_type_adresse = 0;
              libelle_type_adresse = "Livraison";
              break;
            case 'facturation':
              index_type_adresse = 1;
              libelle_type_adresse = "Facturation";
              break;
          }

          blocDiv.find("div.type_adresse").find("h4").find('span.val').html(libelle_type_adresse.toUpperCase());
          blocDiv.find("h4.etablissement").find('span').addClass(datas.type_etablissement).html(datas.etablissement.toUpperCase());
          blocDiv.find("h4.numtou").find('span.val').addClass(datas.numtou).html(datas.numtou.toUpperCase());
          blocDiv.find("h4.numrep").find('span.val').addClass(datas.numrep).html(datas.numrep.toUpperCase());

          blocDiv.find("div.infos").removeClass("hidden");
          if(that.getEnableInfos() === false){
            blocDiv.find("div.infos").addClass("hidden");
          }

          $("select#type_adresse").prop('selectedIndex',  parseInt(index_type_adresse));

          blocsDiv.push(blocDiv);

          resolve(blocsDiv);
        });
      });
    }

    displayBlocsInfos = async function(){
      let that = this;
      return await new Promise(function(resolve,reject) {
        let bloc = that.getDivBlocInfos();
        that.elementBlocs.append(bloc);
        resolve("ok displayBlocsInfos");
      });
    };

}

export default Infos;
