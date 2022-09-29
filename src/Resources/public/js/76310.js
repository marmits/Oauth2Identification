import Routing from "../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js";
import {loadingStart} from "../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/loader";

class api76310 {

  constructor(params) {

    this.input = params.inputElement;
    this.validElement = params.validElement;
    this.adresse76310List = params.elementDisplay;
    this.adresse76310List.classList.add("hidden");
    this.param_max_reponse=50;
    this.token_76310=0;

  }
  // recherche
  query76310 = function(text) {
    let that = this;

    if (that.token_76310 == 0)   // Demande de token
    {
      that.getAccessToken76310()
        .then((datas) => {
          let jwtoken = 'Bearer ' + datas['access_token'];
          let theJsonContent = '{"Valeur": "' + text + '","Pays": "FR","Cnt": "' + that.param_max_reponse + '","AvecCAdrs": true}';
          return that.getSuggestion76310(datas['access_token'], JSON.parse(theJsonContent))
        })
        .then((datasSuggestion) => {
          let adresse76310List = that.clearContent(that.adresse76310List);
          if (datasSuggestion.length > 0)
          {

            for(let k in datasSuggestion)
            {
                let a = document.createElement('A');
                a.setAttribute("href", "#");
                a.setAttribute("adresse_id", datasSuggestion[k]['Id']);
                a.setAttribute("class","list-group-item list-group-item-action ");
                adresse76310List.appendChild(a);
                a.innerHTML = datasSuggestion[k]['Adresse'];
                that.bindAdresseList(a);

            }

            adresse76310List.style.left = that.left+'px';
            that.bindInputSaisie(that.input);

          }
        })
        .catch((e) => {
          console.log("erreur query76310: ");
          console.error(e);
        });
    }
  }

  getAccessToken76310 = async function(parameters) {

    let res = new Promise(function (resolve, reject) {
      $.ajax({
        method: "GET",
        url: Routing.generate("gettokenapi76310"),
        success: function (data) {
          resolve(data);
        },
        error: function (e) {
          console.log(e);
          reject(" getAccessToken76310 failed")
        }
      });
    });
    return res;
  }

  getSuggestion76310 = async function(access_token, values) {
    let that = this;
    let res = new Promise(function (resolve, reject) {
      $.ajax({
        url : Routing.generate("getsuggestion76310"),
        method: "POST",
        data: {
          access_token: access_token,
          values:values
        },
        success: function(data) {
          if(data.length > 0) {
            resolve(data);
          } else {
            let message = "pas de lignes dans getSuggestion76310";
            that.setValidAdresse({ valid: false, message: message });
            reject(message);
          }
        },
        error: function(e) {
          that.setValidAdresse({ valid: false, message: e });
          console.error(e);
          reject(e);
        }
      });
    });
    return res;
  }

  getDetailsAdresse76310 = async function(access_token, values) {

    let res = new Promise(function (resolve, reject) {
      $.ajax({
        url : Routing.generate("getdetailsadresse76310"),
        method: "POST",
        data: {
          access_token: access_token,
          values:values
        },
        success: function(data) {
          if(data.length > 0) {
            resolve(data);
          } else {
            let message = "pas de lignes dans getDetailsAdresse76310";
            that.setValidAdresse({ valid: false, message: message });
            console.error(message);
            reject(message);
          }
        },
        error: function(e) {
          console.error(e);
          reject(e);
        }
      });
    });
    return res;
  }

  bindAdresseList = function(element){
    let that = this;
    let errorMEssage = "";
    let jsonError =  {};
    element.addEventListener('click', function (e) {
      event.preventDefault();
      event.stopPropagation();

      that.getAccessToken76310()
      .then((datas) => {
        let jwtoken = 'Bearer ' + datas['access_token'];
        let theJsonContent = '{"Id": "' + e.target.getAttribute('adresse_id')+ '","Pays": "FR","Adresse": "' + e.target.innerHTML + '","AvecCAdrs": true}';
        return that.getDetailsAdresse76310(datas['access_token'], JSON.parse(theJsonContent))
      })
      .then((datasDetails) => {

        that.adresse76310List.classList.add("hidden");
        that.input.value = datasDetails[0]['Adresse'];

        datasDetails[0].Comple_numero = "";
        datasDetails[0].Type_voie= "";

        let json = {
          cadrs: datasDetails[0].CAdrs,
          cedex: datasDetails[0].Cedex,
          comp_numero: datasDetails[0].Comple_numero,
          cp: datasDetails[0].Cp,
          lieu_dit: datasDetails[0].Lieudit,
          numero: datasDetails[0].Numero,
          type_voie: datasDetails[0].Type_voie,
          ville: datasDetails[0].Ville,
          voie: datasDetails[0].Voie,
          x:datasDetails[0].X,
          y:datasDetails[0].Y
        }
        $(document).trigger("click76310adresse", json);

      })
      .catch((e) => {

        console.log("erreur query76310: ");
        console.error(e);
      });

    });
  }

  bindInputSaisie = function(element){
    let that = this;
    element.addEventListener('click', function (e) {
      event.preventDefault();
      event.stopPropagation();
    });
  }

  setValidAdresse = function(value){
    let that = this;
      that.validElement.trigger("changeInputSaisie", value);
    return this;
  }

  clearContent = function(divObj)
  {
    divObj.innerHTML = "";
    divObj.classList.remove("hidden");
    return divObj;
  }

}




export default api76310;
