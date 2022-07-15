//const assetsFolder = '../';

//import assetsFolder from "../templates";

//import test from '../templates/defaultTextModal.html';
const modalTemplate = '/build/templates/defaultTextModal.html';

const assetsFolder = '/build/';

import modal from 'bootstrap/js/src/modal';
import tooltip from 'bootstrap/js/src/tooltip';

const Handlebars = require('../../../../../interfacegraphique/src/Resources/public/themes/callcenter/js/handlebars-v4.2.0');

const utils = {
    modal : modal,
    toast : {
        queue : []
    },
    password : {},
    tooltip : tooltip,
    select : {},
    format : {},
    arrayLookup : {}
};

const Toast = class {
    constructor() {
        this.message = "";
        this.duration = 5000;
    }
};

utils.buildHandlebarsTemplate = function (templatePath, context = {}) {
//function buildHandlebarsTemplate(templatePath, context = {}) {

  return new Promise(((resolve, reject) => {

    let templateFile = getAssetFolder("templates") + templatePath + ".handlebars";

    fetch(templateFile)
      .then((response) => {
        return response.text()
      })
      .then((textContent) => {

        let fragment = document.createRange().createContextualFragment(textContent);
        let template = Handlebars.compile(fragment.getElementById("template").innerHTML);

        resolve(template(context));
      })
      .catch((err) => {
        reject(err);
      });
  }));
}

utils.getImageAsset = function ( subFolder, assetName )
{
  if(subFolder !== null)
    return getAssetFolder("img/"+subFolder) + assetName;

  return getAssetFolder("img") + assetName;
}

function getAssetFolder( subFolder ) {

    if (subFolder !== undefined)

        return assetsFolder + subFolder + "/";

    return assetsFolder;
}

function getTemplateAsset(subFolder, templateName)
{

    if(subFolder !== null)

        return getAssetFolder("templates/"+subFolder) + templateName;

    return getAssetFolder("templates") + templateName;
}



/**
 * This JavaScript function (specially meant for Google Apps Script or GAS) Get the lookup (vertical) value from a multi-dimensional array.
 *
 * @version 1.1.0
 *
 * @param {Object} searchValue The value to search for the lookup (vertical).
 * @param {Array} array The multi-dimensional array to be searched.
 * @param {Number} searchIndex The column-index of the array where to search.
 * @param {Number} returnIndex The column-index of the array from where to get the returning matching value.
 * @return {Object} Returns the matching value found else returns null.
 */
utils.arrayLookup = function(searchValue,array,searchIndex,returnIndex) // Posted on Tathyika.com (also refer for more codes there)
{
    var returnVal = null;
    var i;
    for(i=0; i<array.length; i++)
    {
        if(array[i][searchIndex]==searchValue)
        {
            returnVal = array[i][returnIndex];
            break;
        }
    }

    return returnVal;
}

/** Affiche une fenêtre modale
 parameters : {
        closable : true,
        showFooterCloseButton : false, -- if no buttons
        callback : null,
        beforeCloseCallback : null,
        afterCloseCallback : null
    }
 **/
utils.modal.display = function(titre,message,boutons,parameters) {

    let that = this;

    if (typeof boutons === 'undefined') { boutons = null; }
    if (typeof parameters === 'undefined') { parameters = {}; }
    if (typeof parameters.callback === 'undefined') { parameters.callback = null; }
    if (typeof parameters.beforeCloseCallback === 'undefined') { parameters.beforeCloseCallback = null; }
    if (typeof parameters.afterCloseCallback === 'undefined') { parameters.afterCloseCallback = null; }
    if (typeof parameters.closable === 'undefined') { parameters.closable = true; }
    if (typeof parameters.showFooterCloseButton === 'undefined') { parameters.showFooterCloseButton = false; }


    let modal_container = $("#app-modal-container");
    let modal_wrapper = document.createElement("div");
    modal_container.get(0).appendChild(modal_wrapper);

    $(modal_wrapper).load(modalTemplate, function(response,status,xhr) {

        if(status === 'success') {
            let modal = $(modal_wrapper).find(".modal");
            let modal_header = modal.find(".modal-header");
            let modal_title = modal.find(".modal-title");
            let modal_content = modal.find(".modal-body");
            let modal_footer = modal.find(".modal-footer");
            let button_to_focus = null;

            modal_title.html(titre);
            modal_content.html(message);

            // Affichage des boutons dans le footer de la modal
            if(boutons !== null && boutons.length > 0) {
                boutons.forEach(function(bouton){

                    let boutonNode = document.createElement("button");

                    if(bouton.classes !== undefined && Array.isArray(bouton.classes)) {
                        bouton.classes.forEach(function(className){
                            boutonNode.classList.add(className);
                        });
                    }

                    if(bouton.id !== undefined) {
                        boutonNode.id = bouton.id;
                        boutonNode.setAttribute("data-id", bouton.id);
                    }

                    boutonNode.innerHTML = bouton.libelle;

                    if(bouton.focus === true) {
                        button_to_focus = boutonNode;
                    }

                    if(typeof bouton.callback === 'function') {
                        boutonNode.addEventListener('click', function(){
                            bouton.callback(modal);
                        });
                    }

                    modal_footer.get(0).appendChild(boutonNode);
                });
            } else {
                if(parameters.showFooterCloseButton){
                    modal_footer.find("button#defaultCloseButton").removeClass('hidden');
                } else {
                    modal_footer.remove();
                }
            }

            // Si la croix de fermeture est visible ou non
            let closableByKeyboard = false;

            if(parameters.closable === false) {
                modal_header.find("button.close").hide();
            } else {
                modal_header.find("button.close").show();
                closableByKeyboard = true;
            }

            // Callback executé avant la fermeture
            modal.on("hide.bs.modal", function(e){
                if(typeof parameters.beforeCloseCallback === 'function') {
                    parameters.beforeCloseCallback(e);
                }
            });

            // Callback executé après la fermeture
            modal.on("hidden.bs.modal", function(e){
                if(typeof parameters.afterCloseCallback === 'function') {
                    parameters.afterCloseCallback(e);
                }
                modal_wrapper.remove();
            });

            // Callback executé juste après l'appel de la modale (avant affichage)
            modal.on("show.bs.modal", function(e){
                let focused_element = document.activeElement;
                if( focused_element ) focused_element.blur();

                let modals = document.querySelectorAll("#app-modal-container .modal").length;
                this.style.setProperty('z-index',(9991 + (modals * 2)),'important');
            });

            // Callback executé une fois l'affichage de la modale effectué
            modal.on('shown.bs.modal', function(e) {

                let modals = document.querySelectorAll("#app-modal-container .modal").length;
                let newBackdrop = document.querySelector(".modal-backdrop:last-child");
                newBackdrop.style.setProperty('z-index',(9991 + (modals * 2) - 1).toString(),'important');


                if(typeof parameters.callback === 'function') {
                    parameters.callback(e);
                }

                if(button_to_focus !== null){
                    button_to_focus.focus();
                } else {
                    modal_footer.find("button#defaultCloseButton").get(0).focus();
                }

            });

            // Backdrop static (pas de fermeture par le backdrop)
            // Keyboard false (pas de fermeture par le bouton echap)
            modal.modal({
                'keyboard' : closableByKeyboard,
                'backdrop' : 'static'
            }).show();

        } else {
          console.error(xhr);
            alert("Impossible de charger le template de la modale ...");
        }
    });
};

/**
 * Affiche une fenêtre qui demande une saisie à l'utilisateur
 * @param titre
 * @param message
 * @param parameters
 */
utils.modal.prompt = function(titre,message,parameters) {

    let that = this;

    if (typeof parameters === 'undefined') { parameters = {}; }
    if (typeof parameters.callback === 'undefined') { parameters.callback = null; }
    if (typeof parameters.beforeCloseCallback === 'undefined') { parameters.beforeCloseCallback = null; }
    if (typeof parameters.afterCloseCallback === 'undefined') { parameters.afterCloseCallback = null; }
    if (typeof parameters.inputType === 'undefined') { parameters.inputType = "text"; }
    if (typeof parameters.placeholder === 'undefined') { parameters.placeholder = null; }

    return new Promise(function(resolve, reject) {

        let modal_container = $("#app-modal-container");
        let modal_wrapper = document.createElement("div");
        modal_container.get(0).appendChild(modal_wrapper);

        let modaltemplate = getTemplateAsset(null,"defaultTextModal.html");

        $(modal_wrapper).load(modaltemplate, function(response,status,xhr) {

            if(status === 'success') {

                let modal = $(modal_wrapper).find(".modal");
                let modal_header = modal.find(".modal-header");
                let modal_title = modal.find(".modal-title");
                let modal_content = modal.find(".modal-body");
                let modal_footer = modal.find(".modal-footer");

                modal_title.html(titre);
                modal_content.html(message);

                let prompt_input = document.createElement("input");

                prompt_input.setAttribute("type",parameters.inputType);

                if(parameters.placeholder !== null) {
                    prompt_input.setAttribute("placeholder", parameters.placeholder);
                }

                prompt_input.id = "prompt_input";
                prompt_input.style.setProperty("display","block");
                prompt_input.classList.add("form-control");
                prompt_input.classList.add("mb-2");
                prompt_input.classList.add("mt-2");

                modal_content.get(0).append(prompt_input);

                let validate_button = document.createElement("button");
                validate_button.innerText = "Valider";
                validate_button.classList.add("btn");
                validate_button.classList.add("btn-success");

                let resolveCallback = function(){
                    modal.modal('hide');
                    return resolve(prompt_input.value);
                };

                validate_button.addEventListener('click',function(){
                    resolveCallback();
                });

                prompt_input.addEventListener('keydown',function(e) {
                    if(e.keyCode === 13) {
                        resolveCallback();
                    }
                });

                let cancel_button = document.createElement("button");
                cancel_button.innerText = "Annuler";
                cancel_button.classList.add("btn");
                cancel_button.classList.add("btn-danger");
                cancel_button.addEventListener('click', function() {
                    modal.modal('hide');
                    return reject("Prompt : annuler");
                });

                modal_footer.get(0).appendChild(cancel_button);
                modal_footer.get(0).appendChild(validate_button);

                // Callback executé avant la fermeture
                modal.on("hide.bs.modal", function(){
                    if(typeof parameters.beforeCloseCallback === 'function') {
                        parameters.beforeCloseCallback();
                    }
                });

                // Callback executé après la fermeture
                modal.on("hidden.bs.modal", function(){
                    if(typeof parameters.afterCloseCallback === 'function') {
                        parameters.afterCloseCallback();
                    }
                    modal_wrapper.remove();
                });

                // Callback executé juste après l'appel de la modale (avant affichage)
                modal.on("show.bs.modal", function(){
                    let focused_element = document.activeElement;
                    if( focused_element ) focused_element.blur();

                    let modals = document.querySelectorAll("#app-modal-container .modal").length;
                    this.style.setProperty('z-index',(9991 + (modals * 2)),'important');
                });

                // Callback executé une fois l'affichage de la modale effectué
                modal.on('shown.bs.modal', function(){

                    let modals = document.querySelectorAll("#app-modal-container .modal").length;
                    let newBackdrop = document.querySelector(".modal-backdrop:last-child");
                    newBackdrop.style.setProperty('z-index',(9991 + (modals * 2) - 1).toString(),'important');

                    if(typeof parameters.callback === 'function') {
                        parameters.callback();
                    }

                    prompt_input.focus();

                });

                // Backdrop static ( pas de fermeture par le backdrop)
                // Keyboard false (pas de fermeture par le bouton echap)
                modal.modal({
                    'keyboard' : false,
                    'backdrop' : 'static'
                }).show();

            } else {
                alert("Impossible de charger le template de la modale ...");
            }
        });

    });
};

utils.password.getTodayPassword = function() {

    let date    = new Date();

    let day     = date.getDate();
    let month   = date.getMonth() + 1;
    let year    = date.getFullYear();
    let password;

    password  = 3 * (day % 2);
    password += 5 * (day % 3);
    password += 7 * (day % 5);
    password += 11 * (day % 7);
    password += 13 * (day % 11);
    password += 17 * (day % 13);
    password -= (month * 17);
    password += (year - 1515);

    return password;
};

utils.tooltip.displayError = function (element,message,scrollTo) {

    if (typeof scrollTo === 'undefined') scrollTo = false;

    let errorContainer = document.createElement("div");
    errorContainer.classList.add('error-line-container');

    let errorTooltip = document.createElement("div");
    errorTooltip.classList.add('error-line');
    errorTooltip.classList.add('error-pulse');
    errorTooltip.innerHTML = message;

    errorContainer.appendChild(errorTooltip);

    this.clearErrors(element);

    const errorElement = element.parentElement.insertBefore(errorContainer,element);

    if(scrollTo) {

        const view = document.getElementById("app_side_script");
        const y = errorElement.getBoundingClientRect().top + view.scrollTop - document.getElementById('app_header').offsetHeight;

        view.scroll({
            top: y,
            behavior: 'smooth'
        });
    }
};

utils.tooltip.clearErrors = function(element) {
    let existing_errors = element.parentElement.querySelector(".error-line-container");

    if(existing_errors)
        existing_errors.remove();
};

/**
 * Affiche un toast à l'utilisateur pendant une période donnée.
 * @param message message à afficher dans le toast
 * @param duration temps en MS
 */
utils.toast.display = function(message,duration) {

    if(message.trim().length === 0) {
        console.error("Le message du toast ne peut pas être vide");
        return false;
    }

    if(duration < 0 || duration > 10000) {
        console.error("Un toast ne peut être afficher pendant plus d'une durée de 10 secondes (10000 ms)");
        return false;
    }

    let toast = new Toast();
    toast.message = message;
    toast.duration = duration;

    this.queue.push(toast);

    if(this.queue.length === 1) {
        this.dequeue();
    }
};

utils.toast.dequeue = function() {
    if(this.queue.length > 0) {

        let toast = this.queue[0];

        let toastElement = document.createElement('div');
        toastElement.classList.add('toast');
        toastElement.innerHTML = toast.message;

        let elementAppended = document.body.appendChild(toastElement);

        setTimeout(() => {
            let deleteDomNodeAfter = 10000;
            elementAppended.classList.add('toast-disappear');
            utils.toast.queue.shift();
            utils.toast.dequeue();
            setTimeout(() => {
                elementAppended.remove();
            }, deleteDomNodeAfter);
        }, toast.duration);
    }
};

/**
 * Selectionne l'option dans le select qui a pour valeur value
 * @param element
 * @param value
 */
utils.select.setSelection = function (element,value) {
    for(let i = 0; i < element.options.length; i ++) {
        let currentOption = element.options[i];
        currentOption.removeAttribute('selected');
        if(currentOption.value === value){
            currentOption.setAttribute("selected","selected");
        }
    }
};

/**
 * Retourne l'option dans le select qui a pour valeur value
 * @param element
 * @param value
 */
utils.select.getSelectedElement = function (element,value) {
    for(let i = 0; i < element.options.length; i ++) {
        let currentOption = element.options[i];
        if(currentOption.value === value){
            return currentOption;
        }
    }
};

/**
 * Supprime la class hidden de l'élément et de ses enfants directs
 * @param element
 */
utils.select.showOptions = function(element) {

    let removeHiddenClass = function(element) {
        element.classList.remove('hidden');
    };

    removeHiddenClass(element);

    for(let child of element.children) {
        removeHiddenClass(child);
    }
};

utils.format.number = (number) => {
  return new Intl.NumberFormat('fr-FR').format(number);
};

//let utils;
export default  utils;
