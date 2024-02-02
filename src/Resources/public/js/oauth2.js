import {loadingStart, loadingStop} from "./loader";

const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);



require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

class Oauth2 {
    constructor() {

        this.template_social_connect = "bundles/marmitsoauth2identification/templates/template_social_connect.html";
        this.template_bt_logout = "bundles/marmitsoauth2identification/templates/template_bt_logout.html";
        this.divprivate = $("div.private");
        this.divprivatemessage = this.divprivate.find("div.message");
        this.divprivatemessagetext = this.divprivatemessage.find("h4.alert-heading");
        this.bouton_userapi_info = this.divprivate.find("button#private_info");
        this.infosusersopen = false;

        // chargement tableau infos user api
        this.bindDisplayUserInfos();
        // chargement du div principal
        this.BuildEventToPrivatDiv();


        // charge les informations d'accès dans la sesssion de l'utilisateur
        // et vérifie que l'utlisateur est en BDD
        // et déclenche l'évenement acces_on ou access_off
        this.getUserOauthLogged()
            .then((result) => {
                let reponse = {
                    "code":null,
                    "message":""
                };
                if(result.code === 200){
                    reponse.code = result.code;
                    reponse.message = "Connected";
                    reponse.result = result;
                    this.divprivate.trigger("access_on", reponse);

                } else {
                    reponse.code = 404;
                    reponse.message = "Sign in width";
                    this.divprivate.trigger("access_off", reponse);
                }
            })
            .catch((e) => {
                console.error(e);
            });

        this.bindPrivateBtUserApiInfo()
            .then((retour) => {

            })
            .catch((e) => {
                console.error(e);
            });
    }

    setInfosUserOpen = function (val){
        let that = this;
        that.divprivate.find("div.infos_user").addClass('hidden');
        if(val === true){
            that.divprivate.find("div.infos_user").removeClass('hidden');
        }
        this.infosusersopen = val;
        return this;
    }

    getInfosUserOpen = function (){
        return this.infosusersopen;
    }

    /*
    User
    Gere l'affichage du contenu en fonction de l'évenement acces_on ou access_off
    */
    BuildEventToPrivatDiv = function() {
        let that = this;
        that.divprivate.off("access_off");
        that.divprivate.on("access_off", function (e, data) {
            that.divprivatemessage.removeClass("hidden");
            if (data.code === 403) {
                that.BuildBtLogout(data.message);
            } else if (data.code === 401) {
                that.BuildBtLogout(data.message);
            } else if (data.code === 404) {
                that.BuildBtSocial(data.message);
            }
        });

        that.divprivate.off("access_on");
        that.divprivate.on("access_on", function (e, data) {
            that.BuildBtLogout(data.message);
        });

    }

    // route qui récupere la session oauth_user_infos avec email, api_user_id, accesstoken
    getUserOauthLogged = async function(){
        let that = this;
        return new Promise(function(resolve,reject) {
            $.ajax({
                url: Routing.generate("getuseroauthlogged"),
                method: "GET",
                beforeSend : function(){
                    loadingStop($('body'));
                    loadingStart($("body"), "Loading  ...");
                },
                success: function(datas){
                    resolve(datas);
                },
                error: function(e){
                    if(e.responseJSON.code === 401) {
                        let result = {
                            "code": e.responseJSON.code,
                            "message": e.responseJSON.message
                        };
                        resolve(result);
                    } else {
                        console.error(e);
                        return reject("Impossible de charger getUserOauthLogged");
                    }
                },
                complete    : function(){
                    loadingStop($('body'));
                }
            });
        });
    }

    /* infos user api hidden true OR false */
    bindDisplayUserInfos = function() {
        let that = this;
        that.divprivate.off("displayUserInfos");
        that.divprivate.on("displayUserInfos", function (e, params) {
            if (that.getInfosUserOpen() === false) {
                that.setInfosUserOpen(true);
            } else {
                that.setInfosUserOpen(false);
            }
            return false;
        });
    }

    BuildBtSocial = function(data){
        let that = this;
        $('<div>').load(that.template_social_connect, function (response, status, xhr) {
            let blocDiv = $(xhr.responseText);
            blocDiv.attr('id', "socialconnect");

            that.divprivatemessagetext.html(data);
            that.divprivate.append(blocDiv);
        });
    }

    BuildBtLogout = function(data){
        let that = this;
        $('<div>').load(that.template_bt_logout, function (response, status, xhr) {
            let blocDiv = $(xhr.responseText);
            blocDiv.attr('id', "logout");

            that.divprivatemessagetext.html(data);
            that.divprivate.append(blocDiv);
        });
    }

    // click infos user api
    bindPrivateBtUserApiInfo = async function(){
        let that = this;

        return new Promise(function(resolve,reject) {

            that.bouton_userapi_info.add(that.divprivate.find('table')).click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                that.divprivate.trigger('displayUserInfos');
            });

            resolve();

        });
    }
    
}

export default Oauth2;