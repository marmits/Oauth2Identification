const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);


import Api76310 from "./76310";
import CentralAdresse from "./CentralAdresse";
import  '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/chaines.js';

import {
  loadingStart,loadingChangeText,onProgressChange,loadingChangeProgressPercent,loadingStop
} from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/loader';

class Adresse {

    constructor() {
      this.template_bloc_adresse= "bundles/maximoadresse/blocs/template_bloc_adresse.html";
      this.divBlocAdresse = null;
      this.elementBlocs = $("#blocsAdresse");
      this.mode = null;
      this.adresse76310List = null;
      this.input = null;
      this.validAdresse = null;
      this.eventBuilderAdresse = new CustomEvent('buildAdresse', {
        detail: {
          name: 'buildAdresse'
        }
      });
      this.centralAdresse = null;
      this.paramsCentralAdresse = {};
      this.adresse_client = {};
      this.complement_numero_datas = {};
      this.type_voie_datas = {};
      this.new_adresse = {
        cadrs: "",
        cedex: "",
        comp_numero: "",
        cp: "",
        lieu_dit: "",
        numero: "",
        type_voie: "",
        ville: "",
        voie: "",
        error: true,
        init:true
      };
      this.api76310 = null;
      this.context = null;

      this.buildEvent = async function(){
        let that = this;

        return await new Promise(function(resolve,reject) {
          return that.loadEventsAdresse()
            .then((loadEventsAdresse) => {
              resolve(loadEventsAdresse);
            })
            .catch((e) => {
              console.log("erreur buildEventBlocsAdresse");
              reject(e);
            });
        });
      }

      // Chargement d'un évenement sur bloc adresse
      this.loadEventsAdresse = async function() {
        let that = this;
        let element = $(that.getElementBlocs());
        let blocsAdresse = '#' + element.attr('id');
        let el = document.querySelector(blocsAdresse);

        let select = null;
        //accrochage de l'evement à l'élément qui doit etre mis à jour avec les données injectées
        return await new Promise(function(resolve,reject) {
          el.addEventListener('buildAdresse', function (e) {
            that.refreshAdresse(e);
          }, { once: true });
          el.dispatchEvent(that.eventBuilderAdresse);
          resolve("ok  addEventListener->buildAdresse in loadEventsAdresse");
        });
      };

      this.refreshAdresse = async function(event) {
        let that = this;
        event.preventDefault();
        event.stopPropagation();
        that.input = document.getElementById('inputsaisie');
        that.adresse76310List = document.getElementById('adresse76310List');
        that.validAdresse = $("#validAdresse");
        that.setapi76310();
        that.bindClickTrouveInCentralAdresse();
      }
    }

    setApi76310(value){
      this.api76310 = value;
      return this;
    }
    getApi76310(){
      return this.api76310;
    }

    setContext(value){
      this.context = value;
      return this;
    }
    getContext(){
      return this.context;
    }

    setNewAdresse(value){
      this.new_adresse = value;
      return this;
    }
    getNewAdresse(){
      return this.new_adresse;
    }

    processNewAdresse = function(datas){

        let that = this;
        let errorMEssage = "";
        let jsonError =  {};
        let result = {};

        if(!$.isEmptyObject(datas)) {
          let res = that.checkValidAdressLigne(datas);

          if (res.valid === true) {
            result = {
              'valid': true,
              'message': errorMEssage
            }
            datas.error = false;
            datas.problemes = {};
            let cadrs_text = ""
            if(datas.cadrs !== ""){
              cadrs_text = datas.cadrs+", ";
            }
            datas.Adresse =  cadrs_text + datas.numero + " " + datas.voie + ", " + datas.cp + " " + datas.ville;
          }
          else
          {
            errorMEssage = '<i class=\'fa fa-exclamation-triangle text-danger\'></i> Pour que l\'enregistrement soit pris en compte:' +
              '<br /><br />'+res.message+
              '<br /><i class=\'fa fa-exclamation-triangle text-danger\'></i> L\'adresse saisie n\'est pas valide.';
            result = {
              'valid': false,
              'message': errorMEssage
            }
            datas.problemes = result;
          }
        }
        else
        {
          result = {
            'valid': false,
            'message': ''
          }
          datas.error = true;
          datas.problemes = result;
        }

        that.setNewAdresse(datas);
        $(document).trigger("nouvelle_adresse", [datas]);

        return result;

      }

    checkValidAdressLigne = function (new_adresse) {

      let valid = false;
      let message = "";

      if (new_adresse !== undefined) {

        if (new_adresse.cp === "" || new_adresse.cp === null) {
            message = message + "Code postal obligatoire.<br />";
        }
        if (new_adresse.numero === "" || new_adresse.numero === null) {
            message = message + "Numéro de voie obligatoire.<br />";
        }
        if (new_adresse.voie === "") {
            message = message + "Libellé de voie obligatoire.<br />";
        }
        if (new_adresse.ville === "") {
            message = message +  "Ville obligatoire.<br />";
        }

      } else {
        message = "nouvelle adresse indéfinie";
      }

      if(message === ""){
        valid = true;
      }

      let retour = {
        valid:valid,
        message:message
      }

      return retour;
    }

    setAdresseClient(value){
      this.adresse_client = value;
      return this;
    }
    getAdresseClient(){
      return this.adresse_client;
    }

    setComplementNumeroDatas(value){
      this.complement_numero_datas = value;
      return this;
    }
    getComplementNumeroDatas(){
      return this.complement_numero_datas;
    }

    setTypeVoieDatas(value){
      this.type_voie_datas = value;
      return this;
    }
    getTypeVoieDatas(){
      return this.type_voie_datas;
    }

    setDivBlocAdresse(value){
      this.divBlocAdresse = value;
      return this;
    }

    getDivBlocAdresse(){
      return this.divBlocAdresse;
    }

    getElementBlocs(){
    return this.elementBlocs;
  }

    chargeBlocsAdresse = async function() {
        let that = this;
        return await new Promise(function (resolve, reject) {
          that.loadTemplateFileAdresse()
            .then((divBlocs) => {
              that.setDivBlocAdresse(divBlocs);
              return that.displayBlocsAdresse()
                .then((resDisplay) => {
                  resolve("chargeBlocsAdresse ok");
                })
                .catch((e) => {
                  reject(e);
                });
            });
        });
      }

    loadTemplateFileAdresse = async function(){
      let that = this;
      let blocsDiv = [];
      return await new Promise(function(resolve,reject) {
        let blocDiv = null;
        $('<div>').load(that.template_bloc_adresse,function( response, status, xhr ) {

          blocDiv = $(xhr.responseText);
          blocDiv.attr('id', 'adresse');
          blocDiv.find('span.titre').html('Adresse');
          blocsDiv.push(blocDiv);

          resolve(blocsDiv);
        });
      });
    }

    displayBlocsAdresse = async function(){
      let that = this;
      return await new Promise(function(resolve,reject) {
        let bloc = that.getDivBlocAdresse();
        that.elementBlocs.append(bloc);
        resolve("ok displayBlocsAdresse");
      });
    };

    /* *******************************************************
    76310
    ******************************************************* */
    api76310SetForm = function(etat)
    {

      let that = this;
      that.mode.classList.remove("btn-secondary");
      that.mode.classList.add("btn-success");
      if(etat.valid === true) {
        that.mode.classList.remove("btn-success");
        that.mode.classList.add("btn-secondary");
        that.centralAdresseSetForm("api76310");
      } else {
        that.resetCentralAdresse();
      }
    }

    setapi76310(){
      let that = this;

      that.mode = document.getElementById('mode');
      let params = {
        elementDisplay:that.adresse76310List,
        inputElement:that.input,
        validElement: that.validAdresse
      };

      that.api76310Chargement(params);
      that.validAdresse.on("changeInputSaisie", function(e, value){
        e.target.value = value;
        that.api76310SetForm(value);
      });
    }

    api76310Chargement(params) {
      let api = new Api76310(params);

      let that = this;
      that.setApi76310(api);
      // Init timeout
      let timeout = null;
      // Ecouter le KeyUp
      that.input.addEventListener('keyup', function (e) {

        // reinitialiser le timeout.
        clearTimeout(timeout);

        // Nouveau timeout 1000ms (1 second)
        timeout = setTimeout(function () {
          // Envoi à géoconcept si longueur input > 2
          let inputText = that.input.value;
          api.setValidAdresse({valid: false, message: ''});
          that.processNewAdresse({});
          if (inputText.length > 2)
          {
            // Nettoyage du résultat
            api.clearContent(that.adresse76310List);
            //- Envoi
            api.query76310(inputText);
          }
        }, 1000);
      });
    }

    /* *******************************************************
    CentralAdresse
    ******************************************************* */
    resetCentralAdresse = function(){

      let that = this;

      let ligne = that.elementBlocs.find("div.bloc").find("div.ligneAdresse");
      that.adresse76310List.classList.add("hidden");
      that.centralAdresse.cpElement.val("");
      that.centralAdresse.cedexElement.val("");
      that.centralAdresse.lieu_ditElement.val("");
      that.centralAdresse.numeroElement.val("");
      that.centralAdresse.villeElement.each(function() {
        $(this).find('option').not(':first').remove();
      });
      that.centralAdresse.libelle_voieElement.each(function () {$(this).find('option').not(':first').remove() });
      that.centralAdresse.villeElement.prop('selectedIndex', 0);
      that.centralAdresse.libelle_voieElement.prop('selectedIndex', 0);
      that.centralAdresse.type_voieElement.prop('selectedIndex', 0);
      that.centralAdresse.comp_numeroElement.prop('selectedIndex', 0);

      that.centralAdresse.lieu_dit = "";
      that.centralAdresse.cp = "";
      that.centralAdresse.cadrs = "";
      that.centralAdresse.cedex = "";
      that.centralAdresse.numero = "";

      that.centralAdresse.sortie.Adresse = "";
      that.centralAdresse.sortie.problemes = {};
      that.centralAdresse.sortie.cadrs = "";
      that.centralAdresse.sortie.cedex = "";
      that.centralAdresse.sortie.comp_numero = "";
      that.centralAdresse.sortie.cp = null;
      that.centralAdresse.sortie.lieu_dit = "";
      that.centralAdresse.sortie.numero = null;
      that.centralAdresse.sortie.type_voie = "";
      that.centralAdresse.sortie.ville = "";
      that.centralAdresse.sortie.voie = "";
      that.centralAdresse.sortie.x = "";
      that.centralAdresse.sortie.y = "";
      that.centralAdresse.sortie.error = false;
      that.setNewAdresse({});
      ligne.trigger("SaisieFormCentralAdresse", {sortie: that.centralAdresse.sortie, context:"reset"});
      let BlocCompadr = $("#compadr");
      let bloc_76310 = BlocCompadr.find("div.blocAdresse").find("div#typegeo_libgeo").find("div.76310");
      let data = {
        active_saisie_76310:false,
        from:"resetCentralAdresse"
      };

      bloc_76310.trigger("active_76310", data);
      $(document).trigger("nouvelle_adresse", []);

    }

    centralAdresseSetForm = function(statut){
      let that= this;

      let allField =  that.elementBlocs.find("form :input");
      let ligne = that.elementBlocs.find("div.bloc").find("div.ligneAdresse");
      allField.prop("disabled", true);

      if(statut !== "api76310") {
        that.setTypegeo_libgeo76310();
        if(statut === "on"){
          allField.prop("disabled", false);
         // that.resetCentralAdresse();
        }
      }
      else {
        if (that.getNewAdresse() !== undefined) {
          // FROM API76310 avec adresse valide
          let NewAdresse = that.getNewAdresse();


          /* CAdrs */
          if (NewAdresse.cadrs !== undefined) {
            that.centralAdresse.sortie.cadrs = NewAdresse.cadrs;
          }

          /* Complement Numéro */
          // si NewAdresse.Numero contient un element dans that.centralAdresse.comp_numeroElement
          // on ne l'affiche pas dans that.centralAdresse.numeroElement et on place that.centralAdresse.comp_numeroElement sur le bon index

          let numero_infos = that.centralAdresse.processComplementNumero(NewAdresse.numero);
          that.centralAdresse.sortie.comp_numero = numero_infos.cpl_num;

          /* Type de voie */
          // si NewAdresse.Voie contient un element dans that.centralAdresse.type_voieElement
          // on place that.centralAdresse.type_voieElement sur le bon index
          let voie_infos = that.centralAdresse.processTypeVoie(NewAdresse.voie);
          that.centralAdresse.sortie.type_voie = voie_infos.type_voie;

          /* CP */
          that.centralAdresse.cpElement.val(NewAdresse.cp);
          that.centralAdresse.sortie.cp = NewAdresse.cp;

          /* NUMERO */
          that.centralAdresse.numeroElement.val(numero_infos.numero);
          that.centralAdresse.sortie.numero = numero_infos.numero;
          if (numero_infos.cpl_num !== "") {
            that.centralAdresse.sortie.numero = numero_infos.numero + " " + numero_infos.cpl_num;
          }

          /* CEDEX */
          that.centralAdresse.sortie.cedex = "";
          that.centralAdresse.cedexElement.val(NewAdresse.cedex);
          that.centralAdresse.cedexElement.prop("disabled", false);
          if (NewAdresse.cedex !== undefined) {
            if (NewAdresse.cedex !== "") {
              that.centralAdresse.cedex = NewAdresse.cedex;
              that.centralAdresse.cedexElement.prop("disabled", true);
            }
            that.centralAdresse.sortie.cedex = NewAdresse.cedex;
          }

          /* LIEU DIT */
          that.centralAdresse.sortie.lieu_dit = "";
          that.centralAdresse.lieu_ditElement.val(NewAdresse.lieu_dit);
          that.centralAdresse.lieu_ditElement.prop("disabled", false);
          if (NewAdresse.lieu_dit !== undefined) {
            if (NewAdresse.lieu_dit !== "") {
              that.centralAdresse.lieu_ditElement.prop("disabled", true);
            }
            that.centralAdresse.sortie.lieu_dit = NewAdresse.lieu_dit;
          }

          /* VILLE */
          let optionVille = document.createElement("option");
          optionVille.innerHTML = NewAdresse.ville;
          optionVille.value = NewAdresse.ville;
          that.elementBlocs.find("form select#ville").each(function () {
            $(this).find('option').not(':first').remove();
          });
          that.elementBlocs.find("form select#ville").append(optionVille);
          optionVille.selected = optionVille.value;
          that.centralAdresse.sortie.ville = NewAdresse.ville;


          /* LIBELLE VOIE */
          let optionVoie = document.createElement("option");
          optionVoie.innerHTML = NewAdresse.voie;
          optionVoie.value = NewAdresse.voie;
          that.elementBlocs.find("form select#libelle_voie").each(function () {
            $(this).find('option').not(':first').remove();
          });
          that.elementBlocs.find("form select#libelle_voie").append(optionVoie);
          optionVoie.selected = optionVoie.value;
          that.centralAdresse.sortie.voie = NewAdresse.voie;
          that.centralAdresse.sortie.error = false;

          if (NewAdresse.x !== undefined) {
            that.centralAdresse.sortie.x = NewAdresse.x;
          }

          if (NewAdresse.y !== undefined) {
            that.centralAdresse.sortie.y= NewAdresse.y;
          }

          ligne.trigger("SaisieFormCentralAdresse", {sortie: that.centralAdresse.sortie, context:"api76310"});
        }
      }

      return this;

    }

    bindClickTrouveInCentralAdresse = function(){
      let that = this;
      let form =  that.elementBlocs.find("form");
      form.addClass("api76310");

      let elementBloc = $(that.getElementBlocs());
      let blocsUpdate = '#' + elementBloc.attr('id');
      let el = document.querySelector(blocsUpdate).querySelector("#mode");
      let input_saisie = document.querySelector(blocsUpdate).querySelector("#inputsaisie");

      that.loadCentralAdresse()
        .then(() => {
          that.centralAdresseSetForm("off");
          el.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            that.processNewAdresse({});
            that.resetCentralAdresse();
            let statut = e.target.getAttribute("class");
            let form = document.querySelector(blocsUpdate).querySelector("form");
            if (statut === "btn btn-success") {
              if (form.classList.contains('api76310')) {
                form.classList.remove("api76310");
                input_saisie.setAttribute("disabled", "disabled");
                input_saisie.value = "";
                that.centralAdresseSetForm("on");
              } else {
                form.classList.add("api76310");
                input_saisie.removeAttribute("disabled");

                that.centralAdresseSetForm("off");
              }
            }
          });
      });
    }

    loadCentralAdresse = async function(){
      let that = this;

      return await new Promise(function(resolve,reject) {
        let ligne = that.elementBlocs.find("div.bloc").find("div.ligneAdresse");
        that.paramsCentralAdresse = {
          list_complement_numero: that.getComplementNumeroDatas(),
          list_type_voie: that.getTypeVoieDatas(),
          infos_client: {
              cedex: that.getAdresseClient().cedex,
              comp_numero: that.getAdresseClient().compvoie,
              cp: that.getAdresseClient().commune.codpos,
              lieu_dit: that.getAdresseClient().lieudit,
              numero: that.getAdresseClient().numvoie,
              type_voie: that.getAdresseClient().voies.code,
              ville: that.getAdresseClient().commune.localite,
              voie: that.getAdresseClient().voies.libelle,
              x: that.getAdresseClient().x,
              y: that.getAdresseClient().y,
            },
          form_elements : {
            inputsaisie: $('#inputsaisie'),
            cpElement: $('#cp'),
            villeElement: $('select#ville'),
            cedexElement:  $('#cedex'),
            numeroElement: $('#numero'),
            comp_numeroElement: $('select#comp_numero'),
            type_voieElement: $('select#type_voie'),
            libelle_voieElement: $('select#libelle_voie'),
            lieu_ditElement:  $('#lieu_dit'),
            ligne_adresse: ligne
          },
        }
        that.centralAdresse = new CentralAdresse(that.paramsCentralAdresse);
        that.centralAdresse.chargement().then((sortie) => {

          ligne.on("SaisieFormCentralAdresse", function(e, value){

            that.setContext(value.context);
            value.sortie.error = true;
            that.setNewAdresse(value.sortie);

            if(that.processNewAdresse(that.getNewAdresse()).valid === true){
             value.sortie.error = false;
            }

            that.setTypegeo_libgeo76310();


            e.target.innerHTML = "";
            e.target.value = that.getNewAdresse();

            let lieu_dit = that.getNewAdresse().lieu_dit;
            if(lieu_dit === undefined){
              lieu_dit = "";
            }

            let numero = "";
            if (that.getNewAdresse().numero !== null) {
              numero = that.getNewAdresse().numero;
            }

            let cp = "";
            if (that.getNewAdresse().cp !== null) {
              cp = that.getNewAdresse().cp;
              if (cp.toString().length === 4) {
                cp = "0" + value.sortie.cp;
              }
            }
            let ligne1 = "<div></div>";
            let ligne2 = "<div></div>";
            let ligne3 = "<div></div>";

            let cedex = "";
            if ((that.getNewAdresse().cedex !== "") && (that.getNewAdresse().cedex !== undefined)){
              cedex = " Cedex " + that.getNewAdresse().cedex;
            }

            let problemes = $.isEmptyObject(value.sortie.problemes);

            if (value.sortie.error === false) {
              if (value.sortie.cadrs !== "") {
                that.centralAdresse.cadrs = that.getNewAdresse().cadrs;
                ligne1 = "<div>" + that.getNewAdresse().cadrs + ", " + numero + " " + that.getNewAdresse().voie + "</div>";
              } else {
                ligne1 = "<div>" + numero + " " + that.getNewAdresse().voie + "</div>";
              }
              ligne2 = "<div>" + lieu_dit + "</div>";
              ligne3 = "<div>" + cp + " " + that.getNewAdresse().ville + " " + cedex + "</div>";

            }
            e.target.innerHTML = ligne1 + ligne2 + ligne3;
          });

          $(document).off("click76310adresse");
          $(document).on("click76310adresse", function(e, value) {
            let etat = that.processNewAdresse(value);
            that.validAdresse.trigger("changeInputSaisie", etat);
          });

          ligne.trigger("SaisieFormCentralAdresse", {sortie: sortie, context:"init"});

          resolve("loadCentralAdresse");

        })
        .catch((e) => {
          console.error(e);
        });

      });
    }

    setTypegeo_libgeo76310 = function(){

      let BlocCompadr = $("#compadr");
      let input_saisie = $("#inputsaisie");

      let data = {
        active_saisie_76310:true,
        from:"setTypegeo_libgeo76310"
      };

      if(input_saisie.attr("disabled") === "disabled") {
        data.active_saisie_76310 = false;
      }


      if(BlocCompadr.length === 1) {
        let bloc_76310 = BlocCompadr.find("div.blocAdresse").find("div#typegeo_libgeo").find("div.76310");
        bloc_76310.trigger("active_76310", data);
      }
    }

}

export default Adresse;
