/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import Client from "./Client";

const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js');
Routing.setRoutingData(routes);

import { Tooltip, Toast, Popover} from 'bootstrap';


import {
  loadingStart,loadingChangeText,onProgressChange,loadingChangeProgressPercent,loadingStop
} from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/loader';
import Infos from "./Infos";
import Identite from "./Identite";
import Attributs from "./Attributs";
import Adresse from "./Adresse";
import Update from "./Update";
import utils_display from './utils.js';
import { tools } from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/Tools.js';
import {parse} from "../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/handlebars-v4.2.0.js";



var deepCompare = function(arg1, arg2){
  if (Object.prototype.toString.call(arg1) === Object.prototype.toString.call(arg2)){
    if (Object.prototype.toString.call(arg1) === '[object Object]' || Object.prototype.toString.call(arg1) === '[object Array]' ){
      if (Object.keys(arg1).length !== Object.keys(arg2).length ){
        return false;
      }
      return (Object.keys(arg1).every(function(key){
        return deepCompare(arg1[key],arg2[key]);
      }));
    }
    return (arg1===arg2);
  }
  return false;
}
var convertObject = function(object){
  let array = [];
  for (const [key, value] of object) {
    array.push(key);
  }
  return array;
}

class Main {

  constructor() {
    if(tools !== undefined) {
      window.adresse = tools;
      tools.setSource('adresse');
      adresse.debug();
    }
    this.appBodyDivHolderElement = $("#app_side_script");
    this.nameAdresse = null;
    this.adresse = null;
    this.blocs = null;
    this.identite = null;
    this.infos = null;
    this.update = null;
    this.eventBuilderAttrtibuts = null;
    this.client = null;
    this.attributsparam = null;
    this.params_input = {};
    this.datas = {

    };
    this.ApiConnectorParams ={

    };
    /* ###########################################################  */

    this.setDatas = function(value){
      this.datas = value;
      return this;
    }

    this.getDatas = function() {
      return this.datas;
    }

    this.setApiConnectorParams = function(params){
      this.ApiConnectorParams = params;
      return this;
    }

    this.getApiConnectorParams = function(){
      return this.ApiConnectorParams;
    }

    this.setNameAdresse = function(name){
      this.nameAdresse = name;
      return this;
    }

    this.getNameAdresse = function() {
      return this.nameAdresse;
    }

    this.setParamsInput = function(params){
      this.params_input = params;
      return this;
    }

    this.setAttributs = function(attributs){
      this.attributs = attributs;
      return this;
    }

    this.getAttributs = function(){
      return this.attributs;
    }

    this.setIdentite = function(blocs){
      this.identite= blocs;
      return this;
    }

    this.getIdentite = function(){
      return this.identite;
    }

    this.setInfos = function(blocs){
      this.infos= blocs;
      return this;
    }

    this.getInfos = function(){
      return this.infos;
    }

    this.setAdresse = function(adresse){
      this.adresse = adresse;
      return this;
    }

    this.setUpdate = function(update){
      this.update = update;
      return this;
    }

    this.getUpdate = function(){
      return this.update;
    }

    this.getAdresse = function(){
      return this.adresse;
    }

    this.setClient = function(client){
      this.client = client;
      return this;
    }

    this.getClient = function(){
      return this.client;
    }

    this.setAttributsParam = function(attributs){
      this.attributsparam = attributs;
      return this;
    }

    this.getAttributsParam = function(){
      return this.attributsparam;
    }

    this.getParamsInput = function(){
      return this.params_input;
    }


    /* ###########################################################  */

    this.create = function (nom, input) {

      let that = this;
      that.setNameAdresse(nom);
      that.setParamsInput(input);
      window.addEventListener ? addEventListener("load", that.init(), false) : window.attachEvent ? attachEvent("onload", that.init()) : (onload = that.init());
    };

    this.saveTo = async function(result){

      let that = this;
      let res = new Promise(function (resolve, reject) {
        if (tools !== undefined) {

          if(result.datas_client != undefined) {
            that.getAttributs().setClient(result.datas_client);
            that.getAttributs().setAttributsClients(result.datas_client.adresse.attributs);
            that.getIdentite().setDatasIdentite(that.getClient().getIdentite());

            that.getInfos().setEnableInfos(that.getDatas().blocs.infos.enabled_infos);
            let infosForBLoc = {
              "type_adresse" : result.datas_client.adresse.type_adresse,
              "etablissement" : result.datas_client.adresse.ruetourep.etablissement,
              "type_etablissement": result.datas_client.adresse.ruetourep.type_etablissement,
              "numtou": result.datas_client.adresse.ruetourep.numtou,
              "numrep": result.datas_client.adresse.ruetourep.numrep
            };
            that.getInfos().setTitre(that.getDatas().blocs.infos.titre);
            that.getInfos().setDatasInfos(infosForBLoc);
            that.getAdresse().setAdresseClient(result.datas_client.adresse);
          }

          if(result.datas_attributs != undefined) {
            that.getDatas().infos.blocs_attributs = result.datas_attributs.blocs;
            that.getAttributs().setAttributs(result.datas_attributs.blocs);
            that.getAdresse().setComplementNumeroDatas(that.getDatas().complement_numero);
            that.getAdresse().setTypeVoieDatas(that.getDatas().type_voie);
          }

          resolve("save to OK");
        } else {
          reject("failed tools undefined");
        }
      });
      return res;
    }

    this.chargeDataFromJson = async function(parameters) {
      let exeption = {
        "erreur":"",
        "titre":"",
        "message":""
      };
      let res = new Promise(function (resolve, reject) {
        $.ajax({
          method: "GET",
          url: Routing.generate("context_adresse",
            {paramsInput : JSON.stringify(parameters)}
          ),
          beforeSend : function() {
            loadingStart($("body"),"Chargement de l'interface ...");
          },
          success: function (data) {

            if(data.erreur === true){
              exeption = {
                "erreur":data.erreur,
                "titre":data.erreur_titre,
                "message":data.erreur_message
              };
              reject(exeption);
            } else {
              resolve(data);
            }
          },
          error: function (e) {
            console.log(e);
            reject(" load chargeDataFromJson failed")
          }
        });
      });
      return res;
    }


    this.resetForms = async function(){
      let that = this;
      return await new Promise(function(resolve,reject) {
        that.appBodyDivHolderElement.find("div").html("");
        resolve("ok resetForms");
      });
    }

    this.validFrom = async function(){
      let that = this;
      console.log(that.appBodyDivHolderElement);
      that.appBodyDivHolderElement.find("div.update").addClass("show");
    }

    this.setInit = async function(params){
      let that = this;
      let res = new Promise(function (resolve, reject) {

        that.setDatas(that.getParamsInput());
        that.getDatas().infos = {};
        that.setClient(new Client());
        that.getClient().setNumCli(that.currentClientId);
        that.setInfos(new Infos());
        that.getInfos().setEnabled(params.blocs.infos.enabled);
        that.setIdentite(new Identite);
        that.getIdentite().setEnabled(params.blocs.identite.enabled);
        that.setAttributs(new Attributs());
        that.setAdresse(new Adresse());
        that.setUpdate(new Update());

        if (tools !== undefined) {
          let config = {
            'client':that.getClient(),
            'attributs' : that.getAttributsParam()
          };

          that.getDatas().infos.client = that.getClient();

        }

        resolve("setInit OK");

      });
      return res;
    }



    this.init = function () {
        let that = this;
        let erreur_titre = "Erreur Chargement des données";

        let datas_client = {
          'datas_client': {}
        };
        let datas_attributs = {
          'datas_attributs': {}
        };

        let chargeDataJson = that.chargeDataFromJson(that.getParamsInput());

        let setInit = chargeDataJson.then((params) => {
          that.setParamsInput(params);
          return that.setInit(params);
        });

        let clientInfos = setInit.then((retourInit) => {
          return that.getClient().getInformations(that.getParamsInput())
        });

        let saveClientInfos = clientInfos.then((datasClient) => {
          datas_client = {
            'datas_client': datasClient
          };
          return that.saveTo(datas_client);
        });

        let attributsListe = saveClientInfos.then((message) => {
          return that.getAttributs().getAdresseListeAttributs()
        });

        let saveAttributsListe = attributsListe.then((datasAttributsListe) => {
          datas_attributs = {
            'datas_attributs': datasAttributsListe
          };
          return that.saveTo(datas_attributs);
        });

        let resetForms = saveAttributsListe.then((message) => {
          return that.resetForms();
        });

        let chargeBlocsInfos = resetForms.then((data) => {
          return that.getInfos().chargeBlocsInfos()
            .then(() => {
              return that.getInfos().loadEventsInfos();
            });
        });

        let chargeBlocsIdentite = chargeBlocsInfos.then((data) => {
          return that.getIdentite().chargeBlocsIdentite(that.getClient().getIdentite());
        });

        let addEventIdentite = chargeBlocsIdentite.then((data) => {
          let that = this;
          that.getIdentite().buildEvent();
        });

        let chargeBlocsAdresse = addEventIdentite.then((data) => {
          return that.getAdresse().chargeBlocsAdresse();
        });

        let addEventAdresse = chargeBlocsAdresse.then((data) => {
          let that = this;
          that.getAdresse().buildEvent();
        });

        let chargeBlocsAttributs = addEventAdresse.then((data) => {
          return that.getAttributs().chargeBlocsAttributs(that.getDatas().infos.blocs_attributs);
        });

        let addEventBlocs = chargeBlocsAttributs.then((data) => {
          let that = this;
          that.getAttributs().buildEvent();
        });

        let chargeBlocsUpdate = addEventAdresse.then((data) => {
          return that.getUpdate().chargeBlocsUpdate(that.getAttributs(), that.getAdresse());
        });

        let addEventUpdate = chargeBlocsUpdate.then((data) => {
          let that = this;
          that.getUpdate().buildEvent();
        });

        let promises = [
          chargeDataJson,
          setInit,
          clientInfos,
          saveClientInfos,
          attributsListe,
          saveAttributsListe,
          resetForms,
          chargeBlocsInfos,
          chargeBlocsIdentite,
          addEventIdentite,
          chargeBlocsAdresse,
          addEventAdresse,
          chargeBlocsAttributs,
          addEventBlocs,
          chargeBlocsUpdate,
          addEventUpdate
        ];

        Promise.all(promises)
          .then((retour) => {

            const event = new CustomEvent("adresseLoaded", {
              detail: {
                retour: "INIT OK ... => tape debuger in console",
                infos: "Les éléments sont chargés et prêts à être utlisés",
                datas: that.getDatas()
              }
            });
            document.dispatchEvent(event);


            if(tools !== undefined) {
              tools.setDatas(that.getDatas());
              window.debuger  = tools.getDatas();

            }






            $(document).off("refresh_attributs");
            $(document).on("refresh_attributs", function(e, value){
              that.getDatas().infos.client.update_attributs = true;
              $(document).trigger("setDatasOutput", that.getDatas().infos.client);
            });

            $(document).off("nouvelle_adresse");
            $(document).on("nouvelle_adresse", function(e, value) {

              if(value !== undefined) {
                  that.getAttributs().setClientCadrs(value.cadrs);
                  if (that.getAdresse().getContext() !== "init") {
                    that.getDatas().infos.client.new_adresse = value;

                  }
              }
            });

            $(document).trigger("setDatasOutput", that.getDatas().infos.client);

            loadingStop($('body'));

          })
          .catch((e) => {
            console.log("init a échoué !");
            let description = e;
            if(e.erreur !== undefined){
              erreur_titre = e.titre;
              description = e.message;
            }

            loadingStop($('body'));

            let message = '<i class=\'fa fa-exclamation-triangle text-danger\'></i> Erreur.</br>' + description + '.</br>';
            //utils_display.modal.display(erreur_titre, message);
            that.resetForms();

            utils_display.modal.display(
              "<i class=\"fa fa-unlink\"></i> " + erreur_titre,
              message,
              [
                {
                  id: 1,
                  libelle: '<i class="fa fa-power-off"></i> ',
                  classes: ['btn', 'btn-danger'],
                  callback: (modal) => {
                    modal.modal('hide');
                    console.log(e);
                  }
                }
              ],
              {
                closable: true
              }
            );
          });

    };

  };


  static getInstance() {
    return Main;
  }

};

export default Main;
