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


import { Tooltip, Toast, Popover } from 'bootstrap';


import {
  loadingStart,loadingChangeText,onProgressChange,loadingChangeProgressPercent,loadingStop
} from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/loader';
import Adresse from "./Adresse";
import utils_display from './utils.js';
import { tools } from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/tools.js';
import ContextCommande from "../../../../../panier/src/Resources/public/js/ContexteCommande";



class Main {

  constructor() {
    this.appBodyDivHolderElement    = $("#app_body");
    this.nameAdresse = null;
    this.adresse = null;
    this.client = null;
    this.attributs = null;
    this.params_input = {};
    this.datas = {
      "infos" : {
        'params_input':{},
        'client': {
          'identite':{},
          'adresse':{}
        },
        'attributs':{}
      }
    };

    this.ApiConnectorParams ={

    };


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

    this.setAdresse = function(adresse){
      this.adresse = adresse;
      return this;
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

    this.setAttributs = function(attributs){
      this.attributs = attributs;
      return this;
    }

    this.getAttributs = function(){
      return this.attributs;
    }

    this.getParamsInput = function(){
      return  this.params_input;
    }

    this.getDatas = function() {
      return this.datas;
    }

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
          that.datas.params_input = that.getParamsInput();

          if(result.datas_client != undefined) {
            that.datas.infos.client.identite = result.datas_client.identite;
            that.datas.infos.client.adresse = result.datas_client.adresse;
          }
          if(result.datas_attributs != undefined) {
            that.datas.infos.attributs = result.datas_attributs.attributs;
            that.getAdresse().setAttributs(result.datas_attributs.attributs);
          }

          tools.setDatas(that.datas);
          window.debug  = tools;

          resolve("save to debug console OK");
        } else {
          reject("failed saveTo tools undefined");
        }
      });
      return res;
    }

    this.chargeDataFromJson = async function(parameters) {

      let res = new Promise(function (resolve, reject) {
        $.ajax({
          method: "GET",
          url: Routing.generate("context_adresse",
            {paramsInput : parameters}
          ),
          beforeSend : function() {
            loadingStart($("body"),"Chargement de l'interface ...");
          },
          success: function (data) {
            resolve(data);
          },
          error: function (e) {
            console.log(e);
            reject(" load chargeDataFromJson failed")
          }
        });
      });
      return res;
    }

    this.setInit = async function(params){
      let that = this;
      let res = new Promise(function (resolve, reject) {

        that.datas.infos = {};
        that.currentClientId = params.currentClientId;
        that.setClient(new Client());
        that.getClient().setNumCli(that.currentClientId);
        that.setAttributs(params.attributs)

        that.setApiConnectorParams(params.ApiConnectorParams);
        that.setAdresse(new Adresse());
        that.getAdresse().panierElementView = $("div#adresse");

        if (tools !== undefined) {
          let config = {
            'client':that.getClient(),
            'attributs' : that.getAttributs()
          };

          that.datas.infos.client = that.getClient();
          window.adresse  = tools;
        }

        if((that.getAdresse() !== undefined) && (that.getAdresse() !== undefined)){
          resolve("chargement OK");
        }
        else{
          reject("failed setInit")
        }

      });
      return res;
    }




    this.init = function () {

      let that = this;
      let chargeDataJson = that.chargeDataFromJson(that.getParamsInput());


      let datas_client = {
        'datas_client': {}
      };
      let datas_attributs = {
        'datas_attributs': {}
      };


      let setInit = chargeDataJson.then((params) => {

        that.setParamsInput(params);
        return that.setInit(params)
      });

      let clientInfos = setInit.then((retourInit) => {
        return that.getClient().getInformation()
      });

      let saveClientInfos = clientInfos.then((datasClient) => {
        datas_client = {
          'datas_client': datasClient
        };
        return that.saveTo(datas_client)
      });

      let attributsListe = saveClientInfos.then((message) => {
        return that.getAdresse().getAdresseListeAttributs()
      });

      let saveAtrtibutsListe = attributsListe.then((datasAttributsListe) => {
        datas_attributs = {
          'datas_attributs': datasAttributsListe
        };
        return that.saveTo(datas_attributs)
      });



      let promises = [
        chargeDataJson,
        setInit,
        clientInfos,
        saveClientInfos,
        attributsListe,
        saveAtrtibutsListe
      ];

      Promise.all(promises)
        .then((retour) => {
          const event = new CustomEvent("adresseLoaded", {
            detail: {
              retour: "INIT OK ... => adresse.debug()",
              infos : "Les éléments sont chargés et prêts à être utlisés",
              datas : that.getDatas()
            }
          });
          document.dispatchEvent(event);
          loadingStop($('body'));
        })
        .catch((e) => {
          console.log("init a échoué !");
          console.log(e);
          loadingStop($('body'));
          utils_display.modal.display('Erreur Chargement des données ', 'Erreur.</br>' + e + '</br>');

        });
    };

  };

  static getInstance() {
    return Main;
  }

};

export default Main;
