import Routing from "../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js";
import {loadingStart} from "../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/loader";
import utils_display from '../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/utils.js';
import {parse} from "../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/handlebars.js";

const select2 = require('./select2/select2.full.min.js');

class CentralAdresse {

  constructor(params) {
    this.utils_display = new utils_display();
    this.list_complement_numero = {};
    this.list_type_voie = {};

    this.infosClient = {
      cedex: "",
      comp_numero: "",
      cp: "",
      lieu_dit: "",
      numero: "",
      type_voie: "",
      ville: "",
      voie: "" ,
      x:"",
      y:""
    };

    this.formElements = {
      inputsaisie: null, //input
      cpElement: null, //input
      villeElement: null, //select
      cedexElement:  null, //input
      numeroElement: null, //input
      comp_numeroElement: null, //select
      type_voieElement: null, //select
      libelle_voieElement: null, //select
      lieu_ditElement:  null, //input
      ligne_adresse: "" //span
    };

    this.setListComplementNumero(params.list_complement_numero);
    this.setListTypeVoie(params.list_type_voie);
    this.setInfosClient(params.infos_client);
    this.setFormElements(params.form_elements);

    this.inputsaisie = this.getFormElements().inputsaisie;
    this.cpElement = this.getFormElements().cpElement;
    this.villeElement = this.getFormElements().villeElement;
    this.cedexElement = this.getFormElements().cedexElement;
    this.numeroElement = this.getFormElements().numeroElement;
    this.comp_numeroElement =  this.getFormElements().comp_numeroElement;
    this.type_voieElement =  this.getFormElements().type_voieElement;
    this.libelle_voieElement =  this.getFormElements().libelle_voieElement;
    this.lieu_ditElement = this.getFormElements().lieu_ditElement;
    this.ligne_adresse = this.getFormElements().ligne_adresse;



    this.cadrs = "";
    this.cedex = this.getInfosClient().cedex;
    this.comp_numero = this.getInfosClient().comp_numero;
    this.cp = this.getInfosClient().cp;
    this.lieu_dit =this.getInfosClient().lieudit;
    this.numero = this.getInfosClient().numero;
    this.type_voie = this.getInfosClient().type_voie;
    this.ville = this.getInfosClient().ville;
    this.voie = this.getInfosClient().voie;
    this.x = this.getInfosClient().x;
    this.y = this.getInfosClient().y;

    this.error = true;

    this.cpElement.val(this.cp);
    this.cedexElement.val(this.cedex);
    this.lieu_ditElement.val(this.lieu_dit);
    this.numeroElement.val(this.numero);


    this.sortie = {
      "cadrs":"",
      "cedex":"",
      "comp_numero":"",
      "cp":"",
      "error":"",
      "lieu_dit":"",
      "numero":"",
      "type_voie":"",
      "ville":"",
      "voie":"",
      "x":"",
      "y":""
    };


  }

  /* getter setter */

  setListComplementNumero= function(value){
    this.list_complement_numero = value;
    return this;
  }
  getListComplementNumero = function(){
    return this.list_complement_numero;
  }

  setListTypeVoie= function(value){
    this.list_type_voie = value;
    return this;
  }
  getListTypeVoie = function(){
    return this.list_type_voie;
  }


  setInfosClient= function(value){
    this.infosClient = value;
    return this;
  }
  getInfosClient = function(){
    return this.infosClient;
  }

  setFormElements= function(value){
    this.formElements = value;
    return this;
  }
  getFormElements = function(){
    return this.formElements;
  }


  setCp= function(value){
    this.cp = value;
    return this;
  }
  getCp = function(){
    return this.cp;
  }
  setCadrs = function(value){
    this.cadrs = value;
    return this;
  }
  getCadrs = function(){
    return this.cadrs;
  }
  setVille = function(value){
    this.ville = value;
    return this;
  }
  getVille = function(){
    return this.ville;
  }
  setCedex = function(value){
    this.cedex = value;
    return this;
  }
  getCedex = function(){
    return this.cedex;
  }
  setCompNum = function(value){
    this.comp_numero = value;
    return this;
  }
  getCompNum = function(){
    return this.comp_numero;
  }
  setTypeVoie= function(value){
    this.type_voie = value;
    return this;
  }
  getTypeVoie = function(){
    return this.type_voie;
  }
  setLieuDit= function(value){
    this.lieu_dit = value;
    return this;
  }
  getLieuDit = function(){
    return this.lieu_dit;
  }
  setNumero = function(value){
    this.numero = value;
    return this;
  }
  getNumero = function(){
    return this.numero;
  }
  setVoie = function(value){
    this.voie = value;
    return this;
  }
  getVoie = function(){
    return this.voie;
  }
  setX = function(value){
    this.x = value;
    return this;
  }
  getX = function(){
    return this.x;
  }
  setY = function(value){
    this.y = value;
    return this;
  }
  getY = function(){
    return this.y;
  }



  chargement = function(){

    let that = this;
    return new Promise(function (resolve, reject) {

      let loadComplementNumero = that.loadComplementNumero();

      let bindComplementNumero = loadComplementNumero.then((datas) => {

        return that.bindComplementNumero();
      });

      let loadTypeVoie = bindComplementNumero.then((datas) => {
        return that.loadTypeVoie();
      });

      let loadVille = loadTypeVoie.then((datas) => {
        return that.loadVille();
      });

      let loadLibelleVoie = loadVille.then((datas) => {
        return that.loadLibelleVoie();
      });

      let bindCodePostal = loadLibelleVoie.then((datas) => {
        return that.bindCodePostal();
      });

      let bindVille = bindCodePostal.then((datas) => {
        return that.bindVille();
      });

      let bindVoie = bindVille.then((datas) => {
        return that.bindVoie();
      });

      let bindNumero = bindVoie.then((datas) => {
        return that.bindNumero();
      });

      let promises = [
        loadComplementNumero,
        bindComplementNumero,
        loadTypeVoie,
        loadVille,
        loadLibelleVoie,
        bindCodePostal,
        bindVille,
        bindVoie,
        bindNumero
      ];

      Promise.all(promises)
        .then((retour) => {

          let default_ligne = {
            Adresse: "",
            cadrs: that.getCadrs(),
            cedex: that.getCedex(),
            comp_numero: that.getCompNum(),
            cp: that.getCp(),
            error: false,
            problemes:{},
            lieu_dit: that.getLieuDit(),
            numero: that.getNumero(),
            type_voie: that.getTypeVoie(),
            ville: that.getVille(),
            voie: that.getVoie()
          };

          that.bindCentralAdresseAllElement();
          resolve(default_ligne);
        })
        .catch((e) => {
          that.utils_display.utils.modal.display(e.titre, e.message);
          reject(e);
        });

    });
  }

  bindCentralAdresseAllElement = function(){
    let that = this;

    let form_elements = that.getFormElements();

    form_elements.cpElement
      .add(form_elements.numeroElement)
      .add(form_elements.cedexElement)
      .add(form_elements.lieu_ditElement)
      .keyup(function( event ) {
        that.updateAdresse(event, form_elements.ligne_adresse);
      });

    form_elements.villeElement
      .add(form_elements.libelle_voieElement)
      .add(form_elements.type_voieElement)
      .add(form_elements.comp_numeroElement)
      .change(function( event ) {
        that.updateAdresse(event, form_elements.ligne_adresse);
      });

  }

  bindNumero = async function() {
    let that = this;
    return new Promise(function(resolve, reject) {
      that.numeroElement.on("change", function (e) {
        e.preventDefault();
        e.stopPropagation();
        that.numeroOnChange(e.target.value)
          .then((result) => {
            resolve(result);
          })
          .catch((e) => {
            console.error(e);
          });
      });
      resolve();
    });
  }

  bindCodePostal = async function() {
    let that = this;
    return new Promise(function(resolve, reject) {
      that.cpElement.on("change", function (e) {
        e.preventDefault();
        e.stopPropagation();
        that.codePostalOnChange(e.target.value)
        .then((result) => {
          resolve(result);
        })
        .catch((e) => {
          console.error(e);
        });
      });
      resolve();
    });
  }

  bindComplementNumero = async function() {
    let that = this;
    return new Promise(function(resolve, reject) {
      that.comp_numeroElement.on("change", function (e) {
        e.preventDefault();
        e.stopPropagation();

      });
      resolve();
    });
  }

  bindVille = async function() {
    let that = this;
    return new Promise(function(resolve, reject) {
      that.villeElement.on("change", function (e) {
        e.preventDefault();
        e.stopPropagation();
        if(e.target.value === "") {
          that.libelle_voieElement.each(function() {
            $(this).find('option').not(':first').remove();
          });

        }
        that.villeOnChange(e.target.value)
          .then((result) => {
            resolve(result);
          })
          .catch((e) => {
            console.error(e);
          });
      });
      resolve();
    });
  }

  bindVoie = async function() {
    let that = this;
    return new Promise(function(resolve, reject) {
      that.libelle_voieElement.on("change", function (e) {
        e.preventDefault();
        e.stopPropagation();
        if(e.target.value === "") {
          that.villeElement.find("option").eq(0).prop('selected', true);
          $(this).find('option').not(':first').remove();
        }
      });
      resolve();
    });
  }

  loadVille = async function() {
    let that = this;

    that.villeElement.each(function() {
      $(this).find('option').not(':first').remove();
    });
    return new Promise(function(resolve, reject) {
      let option = document.createElement("option");
      option.innerHTML = that.getInfosClient().ville;
      option.value = that.getInfosClient().ville;
      that.villeElement.append(option);
      option.selected = 1;
      resolve();
    });
  }

  loadLibelleVoie = async function() {
    let that = this;
    that.libelle_voieElement.each(function() {
      $(this).find('option').not(':first').remove();
    });
    return new Promise(function(resolve, reject) {
      let option = document.createElement("option");
      option.innerHTML = that.getInfosClient().voie;
      option.value = that.getInfosClient().voie;
      that.libelle_voieElement.append(option);
      option.selected = 1;
      resolve();
    });
  }

  loadComplementNumero = async function() {
    let that = this;
    that.comp_numeroElement.each(function() {
      $(this).find('option').not(':first').remove();
    });
    let exeption = {
      "erreur":"",
      "titre":"",
      "message":""
    };

    return new Promise(function(resolve, reject) {
      if(that.getListComplementNumero().erreur === true){
        exeption = {
          "erreur":that.getListComplementNumero().erreur,
          "titre":that.getListComplementNumero().titre,
          "message":that.getListComplementNumero().message
        };
        reject(exeption);
      }
      else {
        that.getListComplementNumero().forEach(function (index, element) {
          let option = document.createElement("option");
          option.innerHTML = index.libelle;
          option.value = index.code;
          that.comp_numeroElement.append(option);
          if (that.comp_numero === index.code) {
            option.selected = index;
          }
        });
        resolve();
      }
    });
  }

  loadTypeVoie = async function() {

    let that = this;
    that.type_voieElement.each(function() {
      $(this).find('option').not(':first').remove();
    });
    let exeption = {
      "erreur":"",
      "titre":"",
      "message":""
    };
    return new Promise(function(resolve, reject) {
      if(that.getListTypeVoie().erreur === true){
        exeption = {
          "erreur":that.getListTypeVoie().erreur,
          "titre":that.getListTypeVoie().titre,
          "message":that.getListTypeVoie().message
        };
        reject(exeption);
      }
      else {
        that.getListTypeVoie().forEach(function (index, element) {
          let option = document.createElement("option");
          option.innerHTML = index.libelle;
          option.value = index.code;
          that.type_voieElement.append(option);
          if (that.type_voie === index.code) {
            option.selected = index;
          }
        });
        resolve();
      }
    });
  }

  numeroOnChange = async function (numero) {
    let that = this;
    let result = {
      isNumero: false,
      messageError: "",
      datas: {}
    };
    return new Promise(function(resolve, reject) {
      if($.isNumeric(numero) === false)
      {
        result.isNumero = false;
        result.messageError = "Numéro invalide, le format doit être numérique.";
      }
      else{
        result.isNumero = true;
      }

      if(result.isNumero === false) {
        that.setNumero(null);
        that.numeroElement.val("");
        that.utils_display.utils.modal.display('<i class=\"fa fa-exclamation-circle\"></i> Erreur Chargement des données', '<i class=\'fa fa-exclamation-triangle text-danger\'></i> ' + result.messageError + '</br>');
      }
      resolve(result);
    });
  }

  codePostalOnChange = async function (cp){
    let that = this;
    let result = {
      isCodePostal: false,
      messageError: "",
      datas: {}
    };

    return new Promise(function(resolve, reject) {
      if(cp.length === 0){
        result.isCodePostal = false;
        result.messageError = "Code postal vide";
        that.villeElement.empty();
        that.libelle_voieElement.empty();
      }
      else if($.isNumeric(cp) === false)
      {
        that.setCp(null);
        result.isCodePostal = false;
        result.messageError = "Code postal invalide, le format doit être numérique.";
      }
      else if(cp.length !== 5) {
        that.setCp(null);
        result.isCodePostal = false;
        result.messageError = "Code postal invalide. La longueur doit être égale à 5.";
      }
      else {
        $.ajax({
          method: "GET",
          url: Routing.generate("completeCityByZipCodeCentralise", {"zipcode": cp}),
          beforeSend: function () {

            that.villeElement.each(function() {
              $(this).find('option').not(':first').remove();
            });
            that.villeElement.attr("disabled", "disabled");
          },
          success: function (data) {
            $.each(data, function (index, element) {
              let option = document.createElement("option");
              let desservie = parseInt(element.desservie) === 1;
              option.innerHTML = element.nom_ville + (desservie === false ? " (Non desservie)" : "");
              option.disabled = (desservie === false);
              option.value = JSON.stringify(element);
              that.villeElement.append(option);
              that.villeElement.removeAttr("disabled");
            });
            that.cedexElement.val("");
            that.lieu_ditElement.val("");
            that.numeroElement.val("");
            that.comp_numeroElement.prop('selectedIndex', 0);
            that.type_voieElement.prop('selectedIndex', 0);
            that.villeElement.trigger('change');

          },
          error: function(data){
            reject("Une erreur s'est produit durant la demande de la liste des villes en fonction du code postal." + data.status);
          }
        });
        result.isCodePostal = true;
      }
      if(result.isCodePostal === false) {
        that.utils_display.utils.modal.display('<i class=\"fa fa-exclamation-circle\"></i> Erreur Chargement des données', '<i class=\'fa fa-exclamation-triangle text-danger\'></i> ' + result.messageError + '</br>');
      }
      resolve(result);
    });
  }

  villeOnChange = async function (ville){
    let that = this;
    return new Promise(function (resolve, reject) {
      let code_postal = that.cpElement;
      let nom_voie = that.libelle_voieElement;

      if((code_postal.val() !== null && ville !== null) && (ville !== "")){
        let city_decoded = JSON.parse(ville);
        let city_code = city_decoded.code_ville;
        let city_name = city_decoded.nom_ville;
        $.ajax({
          url: Routing.generate("completeCityRoadByZipCodeCentralise", {
            "zipcode": code_postal.val(),
            "city": city_name
          }),
          beforeSend: function () {
            nom_voie.attr("disabled", "disabled");
          },
          success: function (data) {

            nom_voie.each(function() {
              $(this).find('option').not(':first').remove();
            });
            data.forEach(function (voie) {
              let option = document.createElement("option");
              option.innerHTML = voie.libelle;
              option.value = voie.libelle;
              option.disabled = (parseInt(voie.interdite) === 1);
              nom_voie.append(option);
              nom_voie.removeAttr("disabled");
            });
          },
          error: function (data) {
            reject("Une erreur s'est produit durant la demande de la liste des voies  en fonction de la ville." + data.status);
          }
        });
      } else {
        nom_voie.each(function() {
          $(this).find('option').not(':first').remove();
        });
        nom_voie.trigger('change');
      }
      resolve(nom_voie);
    });

  }

  /* traitement complement numero et numero */
  processComplementNumero = function(numero){
    let that = this;
    let result = {
      'indexSelected':0,
      'numero' : numero,
      'cpl_num': ''
    };
    let cpl_num = "";
    let indexSelected = 0;
    let words = numero.split(' ');

    numero = words[0];
    that.comp_numeroElement.prop("disabled", false);
    if(words.length > 1) {
      cpl_num = words[1];
      that.comp_numeroElement.find("option").each(function(index, element){
        if ($(this).val() === cpl_num.toLowerCase() && ($(this).val() !== "")) {
          indexSelected = index;
        }
      });
      that.comp_numeroElement.prop("disabled", true);
    }

    result.numero = numero;
    result.indexSelected = indexSelected;
    result.cpl_num = cpl_num;

    that.comp_numeroElement.prop('selectedIndex', indexSelected);

    return result;
  }

  /* traitement TYPE VOIE */
  /* actualise la combo type voie */
  processTypeVoie = function(libelle_voie){
    libelle_voie = libelle_voie.toUpperCase();

    let that = this;
    let result = {
      'indexSelected':0,
      'libelle_voie' : libelle_voie,
      'type_voie' : ''
    };
    let type_voie = "";
    let indexSelected = 0;

    let words = libelle_voie.split(' ');
    type_voie = words[0];

    that.type_voieElement.find("option").each(function(index, element){
      if(($(this).text().sansAccent().toUpperCase() === type_voie) && ($(this).val() !== "")) {
        indexSelected = index;
      }
    });

    result.libelle_voie = libelle_voie;
    result.indexSelected = indexSelected;
    result.type_voie = type_voie;

    that.type_voieElement.prop("disabled", true);
    if(type_voie === ""){
      that.type_voieElement.prop("disabled", false);
    }

    that.type_voieElement.prop('selectedIndex', indexSelected);

    return result;
  }

  //sur chaque champs modifié
  updateAdresse = function(e, ligneElement){
    let that = this;
    e.preventDefault();
    e.stopPropagation();
    let validCp = false;
    let validNumero = false;
    let errorMEssage = "";
    let jsonError =  {};
    let datasDetails = [];
    let codepostalreg = '([0-9]{5})';
    let n = [];
    switch(e.target.id) {
      case 'cp':
        if(!isNaN((parseInt(e.target.value)))) {
          if (e.target.value.length === 5) {
            validCp = true;
            that.setCp(parseInt(e.target.value));
          }
        }

        break;
      case 'ville':
         let value = '['+e.target.value.replace(/^"(.+)"$/,'$1')+']';
          let datas = JSON.parse(value)[0];
          if(datas !== undefined)
          {
            that.setVille(datas.nom_ville);
          }

        break;
      case 'numero':
        if(Number.isInteger(parseInt(e.target.value))){
          validNumero = true;
          that.setNumero(parseInt(e.target.value));
        }
        break;
      case 'libelle_voie':
          that.setVoie(e.target.value);
        break;
      case 'cedex':
        that.setCedex(e.target.value);
        break;
      case 'comp_numero':
        that.setCompNum(e.target.value);
        break;
      case 'type_voie':
        that.setTypeVoie(e.target.value);
        break;
      case 'lieu_dit':
        that.setLieuDit(e.target.value);
        break;
      case 'x':
        that.setX(e.target.value);
        break;
      case 'y':
        that.setY(e.target.value);
        break;
      default:

    }
    that.sortie = {};



    /* CAdrs */
    that.sortie.cadrs = that.getCadrs();

    // complement numero / numero
    let numero = that.numeroElement.val();
    that.setNumero(numero);
     that.sortie.numero = that.getNumero().toString();
    let cpl_num_index = that.comp_numeroElement.prop('selectedIndex');
    let cpl_num_libelle = that.comp_numeroElement.find("option").eq(cpl_num_index).text();
    if(parseInt(cpl_num_index) !== 0){
      numero = numero + " " + cpl_num_libelle;
    }
    that.sortie.numero = numero;
    that.sortie.comp_numero = cpl_num_libelle;

    /* CP */
    let cp = that.cpElement.val();
    that.sortie.cp = cp;

    /* LIEU DIT */
    that.sortie.lieu_dit = that.lieu_ditElement.val();

    /* CEDEX */
    that.sortie.cedex = that.cedexElement.val();

    /* VILLE */
    let ville_index = that.villeElement.prop('selectedIndex');
    let ville = that.villeElement.find("option").eq(ville_index).text();
    if(ville_index === 0){
      that.setVille("");
    }
    that.sortie.ville = ville;


    /* LIBELLE VOIE */
    let voie_index = that.libelle_voieElement.prop('selectedIndex');
    let libelle_voie = that.libelle_voieElement.find("option").eq(voie_index).text();
    that.processTypeVoie(libelle_voie);
    that.sortie.voie = libelle_voie;

    /* Type de voie */
    let type_voie_index = that.type_voieElement.prop('selectedIndex');
    let type_voie_libelle = that.type_voieElement.find("option").eq(type_voie_index).text();
    that.sortie.type_voie = type_voie_libelle;

    // en mode modification clavier
    if(that.inputsaisie.attr("disabled") === "disabled") {

      if(that.getCp() !== null){
        cp = that.getCp().toString();
        if(cp.length === 4){
          cp = "0" + cp;
        }
        that.sortie.cp = cp;
      }

      that.sortie.ville = that.getVille();
      that.sortie.voie = that.getVoie();
      that.sortie.lieu_dit = that.getLieuDit();
      that.sortie.cedex = that.getCedex();
    }

    that.sortie.x = "";
    that.sortie.y = "";






    // remise a zero de la combo voie et de la veleur de la voie si on change la ville ou si aucune voie n'est sélectionnée
    if((e.target.id === "ville") || (that.libelle_voieElement.prop('selectedIndex') === 0)){
      that.type_voieElement.prop('selectedIndex', 0);
      that.sortie.voie = "";
    }

    ligneElement.trigger("SaisieFormCentralAdresse",  {sortie: that.sortie, context:"update"});
  }

}

export default CentralAdresse;
