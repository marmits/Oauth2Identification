const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);

import {
  loadingStart,loadingChangeText,onProgressChange,loadingChangeProgressPercent,loadingStop
} from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/loader';



class Identite {

    constructor() {
      this.template_bloc_identite= "bundles/maximoadresse/blocs/template_bloc_identite.html";
      this.divBlocIdentite = null;
      this.enabled = false;
      this.elementBlocs = $("#blocsIdentite");
      this.datas_identite = {};

      this.eventBuilderIdentite = new CustomEvent('buildIdentite', {
        detail: {
          name: 'buildIdentite'
        }
      });

      this.buildEvent = async function(){
        let that = this;
        return await new Promise(function(resolve,reject) {
          return that.loadEventsIdentite()
            .then((loadEventsIdentite) => {
              resolve(loadEventsIdentite);
            })
            .catch((e) => {
              console.log("erreur buildEventBlocsIdentite");
              reject(e);
            });
        });
      }

      // Chargement de l évenement
      this.loadEventsIdentite = async function() {
        let that = this;
        let element = $(that.getElementBlocs());
        let blocsIdentite = '#' + element.attr('id');
        let el = document.querySelector(blocsIdentite);

        let select = null;
        //accrochage de l'evement à l'élément qui doit etre mis à jour avec les données injectées
        return await new Promise(function(resolve,reject) {
          el.addEventListener('buildIdentite', function (e) {
            that.refreshIdentite(e);
          }, { once: true });
          el.dispatchEvent(that.eventBuilderIdentite);
          resolve("ok  addEventListener->buildIdentite in loadEventsIdentite");
        });
      };

      this.refreshIdentite = async function(event) {
        event.preventDefault();
        event.stopPropagation();
        let that = this;
        that.getElementBlocs().find("div.blocAdresse").find("input.civilite").val(that.getDatasIdentite()["civilite"]);
        that.getElementBlocs().find("div.blocAdresse").find("input.numeroClient").val(that.getDatasIdentite()["numeroClient"]);
        that.getElementBlocs().find("div.blocAdresse").find("input.nom").val(that.getDatasIdentite()["nom"]);
        that.getElementBlocs().find("div.blocAdresse").find("input.prenom").val(that.getDatasIdentite()["prenom"]);
        let ligneEle =  that.getElementBlocs().find("div.ligne");
        let ligne = that.getDatasIdentite()["civilite"]+" "+that.getDatasIdentite()["nom"]+" " +that.getDatasIdentite()["prenom"];
        ligneEle.html(ligne);
      }

    }

    setEnabled(value){
      this.enabled = value;
      return this;
    }

    getEnabled(){
      return this.enabled;
    }

    setDatasIdentite(value){
      this.datas_identite = value;
      return this;
    }

    getDatasIdentite(){
      return this.datas_identite;
    }


    setDivBlocIdentite(value){
      this.divBlocIdentite = value;
      return this;
    }

    getDivBlocIdentite(){
      return this.divBlocIdentite;
    }

    getElementBlocs(){
    return this.elementBlocs;
  }

    chargeBlocsIdentite = async function() {
      let that = this;
      return await new Promise(function (resolve, reject) {
        if(that.getEnabled() === true) {
          that.loadTemplateFileIdentite()
            .then((divBlocs) => {
              that.setDivBlocIdentite(divBlocs);
              return that.displayBlocsIdentite()
                .then((resDisplay) => {
                  resolve("chargeBlocsIdentite ok enabled true");
                })
                .catch((e) => {
                  reject(e);
                });
            });
        } else {
          resolve("chargeBlocsIdentite ok enabled false");
        }
      });
    }

    loadTemplateFileIdentite = async function(){
      let that = this;
      let blocsDiv = [];

      return await new Promise(function(resolve,reject) {
        let blocDiv = null;
        $('<div>').load(that.template_bloc_identite,function( response, status, xhr ) {
          blocDiv = $(xhr.responseText);
          blocDiv.attr('id', 'identite');
          blocDiv.find('span.titre').html('Identité CLient');

          blocsDiv.push(blocDiv);

          resolve(blocsDiv);
        });
      });
    }

    displayBlocsIdentite = async function(){
      let that = this;
      return await new Promise(function(resolve,reject) {
        let bloc = that.getDivBlocIdentite();
        that.elementBlocs.append(bloc);
        resolve("ok displayBlocsIdentite");
      });
    };

}

export default Identite;
