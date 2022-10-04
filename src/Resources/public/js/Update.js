import utils_display from './utils.js';

class Update {

    constructor() {
      this.template_bloc_update= "bundles/maximoadresse/blocs/template_bloc_update.html";
      this.elementBlocs = $("#blocsUpdate");
      this.eventBuilderUpdate = new CustomEvent('buildUpdate', {
        detail: {
          name: 'buildUpdate'
        }
      });
      this.attributs = null;
      this.adresse= null;
      this.eventGetDatasOnUpdate = new CustomEvent('getDatasOnUpdate', {
        detail: {
          name: 'getDatasOnUpdate'
        }
      });
      this.output = {};

      this.buildEvent = async function(){
        let that = this;

        return await new Promise(function(resolve,reject) {
          return that.loadEventsUpdate()
            .then((loadEventsUpdate) => {
              resolve(loadEventsUpdate);
            })
            .catch((e) => {
              console.log("erreur buildEventBlocsUpdate");
              reject(e);
            });
        });
      }

      // Chargement de l évenement sur blocs
      this.loadEventsUpdate = async function() {
        let that = this;
        let element = $(that.getElementBlocs());
        let blocsUpdate = '#' + element.attr('id');
        let el = document.querySelector(blocsUpdate);

        let select = null;
        //accrochage de l'evement à l'élément qui doit etre mis à jour avec les données injectées
        return await new Promise(function(resolve,reject) {
          el.addEventListener('buildUpdate', function (e) {
            that.refreshUpdate(e);
          }, { once: true });
          el.dispatchEvent(that.eventBuilderUpdate);
          resolve("ok  addEventListener->buildUpdate in loadEventsUpdate");
        });
      };

      this.refreshUpdate = function(event) {
        event.preventDefault();
        event.stopPropagation();
        let that = this;
        let ligneEle =  that.getElementBlocs().find("div.ligne");
        let blocDiv = null;
        $('<div>').load(that.template_bloc_update,function( response, status, xhr ) {
          blocDiv = $(xhr.responseText);
          that.getElementBlocs().append(blocDiv);
          let elementIDButton = blocDiv.find('button#update').attr('id');
          that.bindClickUpdate(document.getElementById(elementIDButton))
          .then(() => {
            that.getElementBlocs().addClass("show");
          })
          .catch((e) => {
            console.error(e);
          });
        });
      }

      this.getDatasOnUpdate = function(event) {
        let that = this;

        let message = "Aucune nouvelle adresse.";
        let label_nouton_enregistrer = "Enregistrer les attributs";

        if(that.getOutput().new_adresse !== undefined) {
          if ($.isEmptyObject(that.getOutput().new_adresse.problemes)) {
            message = "Nouvelle ligne d'adresse: "+ that.getOutput().new_adresse.Adresse;
             label_nouton_enregistrer = "Enregistrer tout";
          } else {
            if(that.getOutput().new_adresse.problemes.valid === false) {
                message = message + "<br /> " + that.getOutput().new_adresse.problemes.message;
            }
          }
        }

        utils_display.modal.display(
          "<i class='fa fa-exclamation-circle'></i> Information",
          message,
          [
            {
              id: 1,
              libelle: '<i class="fa fa-check"></i> Annuler',
              classes: ['btn', 'btn-success'],
              callback: (modal) => {
                modal.modal('hide');
              }
            },
            {
              id: 2,
              libelle: '<i class="fa fa-arrow-right"></i> '+label_nouton_enregistrer,
              classes: ['btn', 'btn-custom'],
              callback: (modal) => {
                that.sendDatasToApi(that.getOutput())
                .then((infosapi) => {
                  console.log(infosapi);
                  modal.modal('hide');
                });

              }
            }
          ],
          {
            closable: false
          }
        );
      }
    }

    setOutput(value){
      this.output = value;
      return this;
    }

    getOutput(){
      return this.output;
    }

    setAttributs(value){
      this.attributs = value;
      return this;
    }

    getAttributs(){
      return this.attributs;
    }

    setAdresse(value){
      this.adresse = value;
      return this;
    }

    getAdresse(){
      return this.adresse;
    }

    getElementBlocs(){
    return this.elementBlocs;
  }

    getButtonUpdate(){
      return this.buttonUpdate;
  }

    chargeBlocsUpdate = async function(attributs, adresse) {

      let that = this;
      return await new Promise(function (resolve, reject) {
        that.setAttributs(attributs);
        that.setAdresse(adresse);
        $(document).off("setDatasOutput");
        $(document).on("setDatasOutput", function(e, value) {
          that.setOutput(value);

        });
        resolve("chargeBlocsUpdate ok");
      });
    }

    bindClickUpdate = async function(element){
      let that = this;
      let elementBloc = $(that.getElementBlocs());
      let blocsUpdate = '#' + elementBloc.attr('id');
      let el = document.querySelector(blocsUpdate);

      return await new Promise(function (resolve, reject) {
        element.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          el.addEventListener('getDatasOnUpdate', function (e) {
            that.getDatasOnUpdate(e);
          }, { once: true });
          el.dispatchEvent(that.eventGetDatasOnUpdate);
        })
        resolve("bindClickUpdate ok")
      });
    }

    sendDatasToApi = async function(datasClient) {
      let that = this;
      return await new Promise(function (resolve, reject) {
        resolve(datasClient);
      });
    }

}

export default Update;
