import {synchronizeObjectToView} from "../../../../../panier/src/Resources/public/js/Panier";

const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);

import utils_display from './utils.js';

import {
  loadingStart,loadingChangeText,onProgressChange,loadingChangeProgressPercent,loadingStop
} from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/loader';

class Attributs {

    constructor() {

      this.appBodyDivHolderElement = $("#app_body");
      this.attributs = {};
      this.attributsBlocs = {};
      this.attributsClients = {};
      this.divBlocsAttributs = [];
      this.elementBlocs = $("#blocsAttributs");
      this.elementAdresse= $("#blocsAdresse");
      this.template_bloc_attributs = "bundles/maximoadresse/blocs/template_bloc_attributs.html";
      this.template_bloc_ligne_attributs = "bundles/maximoadresse/blocs/template_bloc_ligne_attributs.html";
      this.datasAttributs = {};
      this.clientCadrs = "";

      this.setDatasAttributs = function(value){
        this.datasAttributs = value;
        return this;
      }

      this.getDatasAttributs = function(){
        return this.datasAttributs;
      }

      this.setClientCadrs = function(value){
        this.clientCadrs = value;
        return this;
      }

      this.getClientCadrs = function(){
        return this.clientCadrs;
      }

      // ########################### // functions traitements

      let groupByColumn = function (tab) {

        let tabGroup = tab.reduce((group, value) => {
          const {bloc} = value;
          group[bloc] = group[bloc] ?? [];
          group[bloc].push(value);
          return group;
        }, {});

        return tabGroup;
      }
      // renvoi un tableau des id attributs restants disponibles classés par bloc
      let getAttritbutsAvailable = function (tabAll, tabClient) {

        let tabAttibutsClient = [];
        let BlocsindexDel = [];
        let TabIndexDel = [];
        let attributsAvailable = [];

        for(let all in tabAll) {
          let blocs_attributs = tabAll[all]['blocs_attributs'];
          let attributs = tabAll[all]['attributs'];

          for(let attribut in attributs) {

            let trouve = false;
            TabIndexDel = [];
            let blocs_attributs_clients = tabClient[all]['blocs_attributs']

            tabClient[all]['attributs'].forEach(function (element, i) {
              if (trouve === false) {
                if (attributs[attribut].id === element.id) {
                  let obj = {};
                  obj.value =  parseInt(attributs[attribut].id);
                  obj.bloc = blocs_attributs;
                  TabIndexDel.push( obj );
                  trouve = true;
                }
              }
            });

            if(TabIndexDel.length > 0) {
              BlocsindexDel.push(TabIndexDel);
            }

          }
        }

        let indexDel = [];
        let jsonString = {};
        for (let index in BlocsindexDel) {
          indexDel.push(BlocsindexDel[index][0]);
        }

        let indexDelgroupByBloc = groupByColumn(indexDel);
        let tabDel = [];
        for(let blocs in indexDelgroupByBloc) {

          for(let bloc in indexDelgroupByBloc[blocs]){
            let valuedel = indexDelgroupByBloc[blocs][bloc]['value'];
            let blocdel = indexDelgroupByBloc[blocs][bloc]['bloc'];
            let objdel = {
              'value' :valuedel,
              'bloc' : blocdel
            };
            tabDel.push(objdel);
          }
        }

        let ids = [];
        for(let all in tabAll) {
          let blocs_attributs = tabAll[all]['blocs_attributs'];
          let attributs = tabAll[all]['attributs'];
          let sortie = false;
          ids[all] = attributs.map(x => x.id);
          for( let temps in tabDel){
            if(blocs_attributs === tabDel[temps]['bloc']) {
              let index = ids[all].indexOf(tabDel[temps]['value']);
              if (index > -1) { // only splice array when item is found
                ids[all].splice(index, 1); // 2nd parameter means remove one item only
              }
            }
          }
          let obj = {
            'bloc' : blocs_attributs,
            'id' : ids[all]
          };
          attributsAvailable.push(obj);
        }

        return groupByColumn(attributsAvailable);
      }
      // renvoi 2 tableaux d'attributs (attributs complets et attributs client) classés par bloc
      let ordonneAttributs = function(attributs, clientAttributs){
        let tabAttrtibutsAll = [];
        let tabAttrtibutsClient = [];
        let tabDatas = [];
        let sortie = []


        for(let ligneAttributs in attributs) {
          if(clientAttributs[ligneAttributs] !== undefined){
            tabDatas = [];
            for(let attributsAll in attributs[ligneAttributs]['attributs']){
              let datas = attributs[ligneAttributs]['attributs'][attributsAll];
              tabDatas.push({'id':datas.id, 'valeur': datas.valeur});
            }
            tabAttrtibutsAll.push({
              'blocs_attributs': ligneAttributs,
              'attributs': tabDatas
            });
            tabDatas = [];
            for(let attributsClient in clientAttributs[ligneAttributs]){
              let datas = clientAttributs[ligneAttributs][attributsClient];
              tabDatas.push({'id':datas.id, 'valeur': datas.libelle});
            }
            tabAttrtibutsClient.push({
              'blocs_attributs': ligneAttributs,
              'attributs': tabDatas
            });
          }
        }

        sortie["all"] = tabAttrtibutsAll;
        sortie["client"] = tabAttrtibutsClient;

        return sortie;
      }

      let searchInAttributsByIds = function(attributs, bloc, ids){

        let AttributsBlocs = [];

        let blocResult = {
          bloc:bloc,
          attributs: []
        }

        for(let attribut in attributs[bloc]['attributs']){
          let option = {
            id:attributs[bloc]['attributs'][attribut]['id'],
            valeur:attributs[bloc]['attributs'][attribut]['valeur']
          }
          if(ids.find(e => e == attributs[bloc]['attributs'][attribut]['id']) !== undefined){
            AttributsBlocs.push(option);
          }
        }
        blocResult.attributs = AttributsBlocs

        return blocResult;

      }

      // ########################### // evenements
      this.buildEvent = async function(){
        let that = this;
        //accrochage de l'evement à l'élément qui doit etre mis à jour avec les données injectées
        return await new Promise(function(resolve,reject) {
          that.elementBlocs.trigger("buildAttributs");
          resolve("ok  addEventListener->buildAttributs in loadEventsAttributs");
        });
      }

      // construction html des blocs
      this.BuildBlocsAttributs = async function(event){

        event.preventDefault();
        event.stopPropagation();
        let that = this;
        let attributs = that.getAttributs();
        let clientAttributs = that.getAttributsClients();
        let divattributs = that.getDivBlocsAttributs();
        let template_bloc_attributs = that.template_bloc_attributs;
        let template_bloc_ligne_attributs = that.template_bloc_ligne_attributs;
        let elementBlocs = $(that.getElementBlocs());
        let ListAttributs = {};
        let ListAttributsClients = {};
        let ListAttributsTab = [];
        let ListAttributsClientsTab = [];
        let attributsOrder = ordonneAttributs(attributs,clientAttributs);
        let attributsDiponibles = getAttritbutsAvailable(attributsOrder['all'], attributsOrder['client']);
        that.elementAdresse.find("div.ligne").html("");

        $('<div>').load(template_bloc_attributs, function (response, status, xhr) {
          elementBlocs.html("");
          for(let ligneBloc in divattributs) {
            let blocDiv = $(xhr.responseText);
            blocDiv.attr('id', ligneBloc);
            blocDiv.attr('data-order', divattributs[ligneBloc].order);
            blocDiv.find('span.titre').append(divattributs[ligneBloc].titre);
            blocDiv.find("div.blocAdresse").find("input.valeur").attr('value',"");
            blocDiv.find("div.blocAdresse").find("input.valeur").attr('disabled','disabled');
            blocDiv.find("div.blocAdresse").find("input.valeur").attr('valid',0);

            //liste visuelle des attributs clients
            $('<div>').load(template_bloc_ligne_attributs, function (response, status, xhr) {
                  that.loadEventsAttributsInput(ligneBloc)
                  .then((loadEventsAttributsInput) => {
                    let blocsAttributs = $('#' + ligneBloc);
                    let el = blocsAttributs.find("div.blocAdresse");
                    el.trigger("buildAttributsInput", [ligneBloc]);
                  }).
                  catch((e) => {
                  console.error(e);
                });

                that.loadEventsAttributsList(ligneBloc)
                  .then((loadEventsAttributsList) => {
                    let blocsAttributs = $('#' + ligneBloc);
                    let el = blocsAttributs.find("select.attributs");
                    el.trigger("buildAttributsList", [ligneBloc]);
                  }).
                catch((e) => {
                  console.error(e);
                });
            });

            //controle sur les evenements SELECT, ADD
            let select = blocDiv.find("select.attributs");
            that.bindSelectAttributs(ligneBloc, select)
              .then((bindSelectAttributs) => {
                let input_add = blocDiv.find("input.valeur");
                return that.bindCheckAddAttributs(ligneBloc, input_add)
              })
              .then((bindCheckAddAttributs) => {
                let button_add = blocDiv.find("button.add");
                return that.bindAddAttribut(button_add)
              })
              .then((bindAddAttribut) => {
                elementBlocs.append(blocDiv);
              })
              .catch((e) => {
                console.error(e);
              });
          };
        });
      };

      // définition evenement sur les select
      this.loadEventsAttributsList = async function(bloc) {
        let that = this;
        let blocsAttributs = $('#' + bloc);
        let el = blocsAttributs.find("select.attributs");
        return new Promise(function(resolve,reject) {
          el.on("buildAttributsList", function(e, bloc){
            that.refreshAttributsList(e, bloc);
          });
          resolve(bloc);
        });
      }

      // définition evenement sur les input
      this.loadEventsAttributsInput = async function(bloc) {
        let that = this;
        let blocsAttributs = $('#' + bloc);
        let el = blocsAttributs.find("div.blocAdresse");
        return new Promise(function(resolve,reject) {
          el.on("buildAttributsInput", function(e, bloc){
            that.refreshAttributsInput(e, bloc);
          });
          resolve(bloc);
        });
      }

      // mise à jour des lignes recapitulatives de chaque bloc attribut
      this.refreshLignesAttributs = function(bloc){
        let that = this;
        let blocDiv = $("#" + bloc);
        let ligneEleBlocAttribut = blocDiv.find("div.ligne");
        let ligneEleBlocAdresse = that.elementAdresse.find("div.ligne");

        let attributs = that.getAttributs();
        let divattributs = that.getDivBlocsAttributs();

        ligneEleBlocAttribut.html("");

        let ligne = that.check_Abreviation(attributs[divattributs[bloc].id].attributs, divattributs[bloc].attributs).slice(0, -2);
        ligneEleBlocAttribut.html(ligne);
        ligneEleBlocAttribut.find("div.ligne").append("<div>"+ligne+"</div>");


        ligneEleBlocAdresse = that.elementAdresse.find("div.ligne");
        ligneEleBlocAdresse.html("");
        for(let attribut in divattributs){
          let blocAttributId = divattributs[attribut].id;
          let ligne = that.check_Abreviation(attributs[blocAttributId].attributs, divattributs[blocAttributId].attributs).slice(0, -2);
          that.elementAdresse.find("div.ligne").append("<div>"+ligne+"</div>");
        }

      }

      // mise à jour des input HTML
      this.refreshAttributsInput = async function(event, bloc) {
        let that = this;
        event.preventDefault();
        event.stopPropagation();

        let template_bloc_ligne_attributs = that.template_bloc_ligne_attributs;
        let blocDiv = $("#" + bloc);

        let listAttributs = blocDiv.find("div.listattributs");
        let elementBlocs = $(that.getElementBlocs());

        let attributs = that.getAttributs();
        let divattributs = that.getDivBlocsAttributs();

        that.elementAdresse.find("div.ligne").html("");

        if(divattributs[bloc].attributs.length === 0){
          blocDiv.find("div.blocAdresse").find("div.listattributs").remove();
        }

        if(divattributs[bloc]['attributs'].length !== 0) {
          //liste visuelle des attributs clients
          $('<div>').load(template_bloc_ligne_attributs, function (response, status, xhr) {
            for (let ligneAttributs in divattributs[bloc].attributs) {
              blocDiv.find("div.blocAdresse").find("div.listattributs").remove();
              let blocLigne = $(xhr.responseText);

              blocLigne.find("input.id").val(divattributs[bloc].attributs[ligneAttributs].id);
              blocLigne.find("input.attribut").attr("placeholder", divattributs[bloc].attributs[ligneAttributs].libelle);
              blocLigne.find("input.valeur").attr("placeholder", divattributs[bloc].attributs[ligneAttributs].valeur);

              let button_del = blocLigne.find("button.del");
              that.bindDelAttribut(button_del, divattributs[bloc].attributs[ligneAttributs].id)
                .then((bindDelAttribut) => {
                  blocDiv.find("div.blocAdresse").append(blocLigne);
                })
                .catch((e) => {
                  console.error(e);
                });
            }
          });
        }

        that.auxide_cadhvx_typegeo_libgeo(bloc, blocDiv);
        that.refreshLignesAttributs(bloc);

      }

      // mise à jour dds liste HTML
      this.refreshAttributsList = async function(event, bloc) {
        let that = this;
        event.preventDefault();
        event.stopPropagation();
        let blocDiv = $("#" + bloc);
        let select = blocDiv.find("select.attributs");
        select.each(function() {
          $(this).find('option').not(':first').remove();
        });

        let attributs = that.getAttributs();
        let divattributs = that.getDivBlocsAttributs();
        let clientAttributs = that.getAttributsClients();
        let attributsOrder = ordonneAttributs(attributs,clientAttributs);
        let attributsDiponibles = getAttritbutsAvailable(attributsOrder['all'], attributsOrder['client']);

        let optionsAvailbable = searchInAttributsByIds(
          attributs,
          attributsDiponibles[divattributs[bloc].id][0]['bloc'],
          attributsDiponibles[divattributs[bloc].id][0]['id']
        )
        let attributsAvailable = optionsAvailbable.attributs;
        if(divattributs[bloc].id === optionsAvailbable.bloc) {
          for(let attributAvailable in attributsAvailable){
            let option = new Option(attributsAvailable[attributAvailable].valeur, attributsAvailable[attributAvailable].id);
            $(option).html(attributsAvailable[attributAvailable].valeur);
            select.append($(option));
          }
        }
      }

    }

    auxide_cadhvx_typegeo_libgeo = function(ligneBloc, blocDiv){
      let that = this;
      //auxide_cadhvx
      if(ligneBloc === "compidcli")
      {
        blocDiv.find("div.blocAdresse").find("div#auxide_cadhvx").html("");
        blocDiv.find("div.blocAdresse").find("div#auxide_cadhvx").addClass("hidden");
        let auxide_cadhvx = that.get_auxide_cadhvx();
        if(auxide_cadhvx !== ""){
          blocDiv.find("div.blocAdresse").find("div#auxide_cadhvx").removeClass("hidden");
          blocDiv.find("div.blocAdresse").find("div#auxide_cadhvx").html(auxide_cadhvx);
        }
      }

      //typegeo_libgeo
      if(ligneBloc === "compadr")
      {
        let typegeo_libgeo = that.get_typegeo_libgeo();
        //blocDiv.find("div.blocAdresse").find("div#typegeo_libgeo").addClass("hidden");
        blocDiv.find("div.blocAdresse").find("div#typegeo_libgeo").find("div.maximo").addClass("hidden");
        blocDiv.find("div.blocAdresse").find("div#typegeo_libgeo").find("div.maximo p").html("");
        if(typegeo_libgeo !== ""){
          blocDiv.find("div.blocAdresse").find("div#typegeo_libgeo").removeClass("hidden");
          blocDiv.find("div.blocAdresse").find("div#typegeo_libgeo").find("div.maximo").removeClass("hidden");
          blocDiv.find("div.blocAdresse").find("div#typegeo_libgeo").find("div.maximo p").html(typegeo_libgeo);
        }

        //ajouter un evenement sur 76310
        let div_76310 = blocDiv.find("div.blocAdresse").find("div#typegeo_libgeo").find("div.76310");
        div_76310.off("active_76310");
        div_76310.on("active_76310", function (e, data) {

          if (data.active_saisie_76310 === false){
            $(this).addClass("hidden");
            $(this).removeClass("right");
            $(this).find("p").html("");
            that.setClientCadrs("");
          }
          if(that.getClientCadrs() !== "") {

              $(this).addClass("right");
              $(this).removeClass("hidden");
              $(this).find("p").html(that.getClientCadrs());

          }
        });
      }
    }

    check_Abreviation = function(attributs, lignesAttributs){
      let tabAbrev = [];
      let ligne = "";
      for (let index in attributs){
        let attrAbrev = {};

        if(attributs[index].abrev !== undefined) {
          attrAbrev.id = attributs[index].id;
          attrAbrev.valeur = attributs[index].abrev;
          tabAbrev.push(attrAbrev);
        }
      }

      let libelle = "";

      for (let ligneAttributs in lignesAttributs) {
        libelle = lignesAttributs[ligneAttributs].libelle;
        lignesAttributs[ligneAttributs].libelleOk = libelle;
        if(tabAbrev.length > 0) {

          let trouve = false;
          for(let abrev in tabAbrev){
            if((lignesAttributs[ligneAttributs].id === tabAbrev[abrev].id) && trouve === false) {
              libelle = tabAbrev[abrev].valeur;
              trouve = true;
            }
          }
          lignesAttributs[ligneAttributs].libelleOk = libelle;

        }

        ligne = ligne + lignesAttributs[ligneAttributs].libelleOk + " " + lignesAttributs[ligneAttributs].valeur + ", ";

      }

      return ligne;
    }

    /* auxide cadhvx
     */
    get_auxide_cadhvx = function()
    {
      let that = this;
      let clientAttributs = that.getAttributsClients();
         let auxide_cadhvx = "";
         if (clientAttributs['compidcli'].length === 0) {
           auxide_cadhvx = that.getClient().adresse.auxide + " " + that.getClient().adresse.cadhvx;
         }
      return auxide_cadhvx;
    }

    /* typegeo libgeo
     */
    get_typegeo_libgeo= function()
    {
      let that = this;
      let clientAttributs = that.getAttributsClients();
      let typegeo_libgeo = "";
      if (clientAttributs['compadr'].length === 0) {
        typegeo_libgeo = that.getClient().adresse.typegeo + " " + that.getClient().adresse.libgeo;
      }
      return typegeo_libgeo;
    }

    /* ###########################################################  */
    getElementBlocs(){
     return this.elementBlocs;
   }

    setClient(value){
      this.client = value;
      return this;
    }

    getClient(){
      return this.client;
    }

    setAttributs(value){
        this.attributs = value;
        return this;
      }

    getAttributs(){
      return this.attributs;
    }

    setAttributsClients(value){
       this.attributsClients = value;
       return this;
    }

    getAttributsClients(value){
      return this.attributsClients;
    }

    // Retourne les attributs d'adresse
    getAdresseListeAttributs = async function()
    {
      let that = this;
      let exeption = {
        "erreur":"",
        "titre":"",
        "message":""
      };
      return new Promise(function(resolve,reject) {
  
        $.ajax({
          url: Routing.generate("get_central_adresse_attributs"),
          method: "GET",
          success: function(data){
            if(data.erreur === true){
              exeption = {
                "erreur":data.erreur,
                "titre":data.titre,
                "message":data.message
              };
              reject(exeption);
            } else {
              resolve(data);
            }
          },
          error: function(){
            return reject("Impossible de charger la liste des attributs.");
          }
        });
      });
    }

    setAttributsClientsByBlocs = async function(blocs){
      let that = this;
      let DatasClientAttributs = [];
      let DatasTitreAttributs = [];
      let result = [];
      let clientAttributs = that.getClient().adresse.attributs;
      return await new Promise(function(resolve,reject) {
          for(let bloc in blocs) {
            if (clientAttributs[bloc] !== undefined) {
                DatasClientAttributs[bloc] = {
                  "id":bloc,
                  "titre" : that.getAttributs()[bloc].titre,
                  "order" : that.getAttributs()[bloc].order,
                  "attributs" : clientAttributs[bloc]
                };
            }
          }
        resolve(DatasClientAttributs);
      });

    }

    setDivBlocsAttributs(value){
      this.divBlocsAttributs = value;
      return this;
    }

    getDivBlocsAttributs(){
      return this.divBlocsAttributs;
    }

    /* ###########################################################  */
    /*     CHARGE BLOC ATTRIBUTS
    */
    chargeBlocsAttributs = async function(blocsAttrtibuts){
      let that = this;
      return await new Promise(function(resolve,reject) {
          return that.setAttributsClientsByBlocs(blocsAttrtibuts)
          .then((attributsClient) => {
            that.setDivBlocsAttributs(attributsClient);
            that.elementBlocs.off("buildAttributs");
            that.elementBlocs.on("buildAttributs", function(e){
              that.BuildBlocsAttributs(e);
            });
            resolve();
          })
          .catch((e) => {
            reject(e);
          });
      });
    };

    /* ###########################################################  */
    /* ACTIONS */


    bindCheckAddAttributs = async function(bloc, element) {
      let that = this;

      return new Promise(function(resolve,reject) {
        if(element === null){
          reject("pas d'élements trouvés dans bindInputAddAttributs");
        }
        element.on("keydown keyup change", function(){

          let span = $(this).parent().parent().find("span.minmax");

          let minLength = $(this).attr('minLength');
          let maxLength =  $(this).attr('maxLength');
          let text = "";

          let value = $(this).val();
          $(this).attr("valid", 0);
          $(this).addClass("red");

          if (value.length < minLength){

            $(this).attr("valid", 0);
            span.addClass("error");
            //Text is short
          }
          else if (value.length > maxLength) {
            $(this).attr("valid", 0);
            span.addClass("error");
            //Text is long
          }
          else {
            $(this).attr("valid", 1);
            span.removeClass("error");
            //Text is valid
          }
          text = value.length + " / " + maxLength;
          span.html(text);

        });

        resolve("bindInputAddAttributs");
      });


    }

    bindSelectAttributs = async function(bloc, element) {
      let that = this;

      return new Promise(function(resolve,reject) {
        let message = null;

       if(element === null){
         reject("pas d'élements trouvés dans bindSelectAttributs");
       }

        element.change(function (e) {
          let attr = [];
          let id = e.target.value;
          let parent = e.target.parentNode.parentNode;
          let span = $(this).parent().parent().find("span.minmax");
          let input = $(this).parent().parent().find("input.valeur");
          input.attr("disabled", "disabled");
          input.val("");
          input.attr("valid", 0);
          span.html("");

          if(id > 0) {

            for (let property in that.getAttributs()[bloc]['attributs']) {
              if ((parseInt(id) === parseInt(that.getAttributs()[bloc]['attributs'][property]['id']))) {
                attr.push({
                  id: id,
                  minmax: that.getAttributs()[bloc]['attributs'][property]['contrainte']['minmax']
                });
              }
            }

            if (attr.length === 1) {
              let parent = e.target.parentNode.parentNode;
              input.removeAttr("disabled");
              input.attr("minLength", attr[0].minmax.min);
              input.attr("maxLength", attr[0].minmax.max);
            }
          }
        });

        if(message === null) {
          resolve(message);
        }
      });

    };

    bindDelAttribut = function(element, id){
    let that = this;
    return new Promise(function(resolve,reject) {
      let message = null;

      if(element === null){
        reject("pas d'élements trouvés dans bindDelAttribut");
      }

      element.click(function (e) {
        e.preventDefault();
        e.stopPropagation();

        that.deleteAttribut(e, id)

          .then((bloc) => {
            let blocsAttributs = $('#' + bloc);
            let elInput = blocsAttributs.find("div.blocAdresse");
            let elSelect = blocsAttributs.find("select.attributs");
            elInput.trigger("buildAttributsInput", [bloc]);
            elSelect.trigger("buildAttributsList", [bloc]);
            resolve(bloc);

          })
          .catch((e) => {
            reject(e);
          });
      });

      if(message === null) {
        resolve(message);
      }
    });
  }

    bindAddAttribut = function(element){
      let that = this;
      return new Promise(function(resolve,reject) {
        let message = null;

        if(element === null){
          reject("pas d'élements trouvés dans bindSelectAttributs");
        }

        element.click(function (e) {
          e.preventDefault();
          e.stopPropagation();
          let bloc = null;
          let input = $(this).parent().parent().find("input.valeur");
          let minmax = $(this).parent().parent().find("span.minmax");

          let val = input.val();
          let valid = input.attr("valid");

          if((val.length !== 0) && (parseInt(valid) === 1)){

              that.insertAttribut(e)
                .then((bloc) => {
                  input.attr('value',"");
                  input.attr("disabled", "disabled");
                  input.attr('valid',0);
                  input.val("");
                  minmax.html("");
                  let blocsAttributs = $('#' + bloc);
                  let elInput = blocsAttributs.find("div.blocAdresse");
                  let elSelect = blocsAttributs.find("select.attributs");
                  elInput.trigger("buildAttributsInput", [bloc]);
                  elSelect.trigger("buildAttributsList", [bloc]);
                  resolve(bloc);

                })
                .catch((e) => {
                  reject(e);
                });
            }
        });

        if(message === null) {
          resolve(message);
        }
      });
    }

    deleteAttribut = async function(target, id){
    let that = this;
    let bloc = target.target.offsetParent.offsetParent.offsetParent.id;
    let DatasClientAttributs = that.getClient().adresse.attributs[bloc];

    return await new Promise(function (resolve, reject){

      for(let property in DatasClientAttributs){
        if(id == DatasClientAttributs[property].id){
          DatasClientAttributs.splice(property, 1);
        }
      }

      $(document).trigger("refresh_attributs", {"del_attribut": bloc});
      resolve(bloc);

    });
  }

    insertAttribut = async function(target){
      let that = this;
      let bloc = target.target.offsetParent.offsetParent.offsetParent.id;
      let DatasClientAttributs = that.getClient().adresse.attributs[bloc];
      let select = $("#"+bloc).find('div.blocAdresse').find('select.attributs');
      let select_id = select.val();
      let select_text = $("#"+bloc).find('div.blocAdresse').find('select.attributs').find(":selected").text();
      let input_value = $("#"+bloc).find('div.blocAdresse').find('input.valeur').val();

      return await new Promise(function (resolve, reject){
        if((select_id !== "") && (input_value !== "")){

          let newValue = {
            id:parseInt(select_id),
            libelle:select_text,
            valeur:input_value
          }

          let save = false;
          if(DatasClientAttributs.length > 0) {
            for (let property in newValue) {

              var found = DatasClientAttributs.find(function (element) {
                if (property === "id") {
                  save = false;
                  if ((newValue[property] !== element[property])) {
                    save = true;
                  }
                }
              });
            }
          }
          else {
            save = true;
          }

          if(save){
            DatasClientAttributs.push(newValue);
            $(document).trigger("refresh_attributs", {"insert_attribut": bloc});
          }
        }

        resolve(bloc);
      });
    }

}



export default Attributs;
