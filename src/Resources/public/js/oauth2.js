const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);

import {
    loadingStart,loadingChangeText,onProgressChange,loadingChangeProgressPercent,loadingStop
} from './loader';

require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

class Oauth2 {

    constructor() {
        this.userEmail = null;
        this.template_private = "bundles/marmitsgoogleidentification/templates/template_private.html";
        this.template_social_connect = "bundles/marmitsgoogleidentification/templates/template_social_connect.html";
        this.template_bt_logout = "bundles/marmitsgoogleidentification/templates/template_bt_logout.html";
        this.template_user_api_info = "bundles/marmitsgoogleidentification/templates/template_user_api_info.html";
        this.bouton_connect = null;
        this.bouton_userapi_info = null;
        this.input_password = null;
        this.divprivate = $("div.private");
        this.divprivatemessage = this.divprivate.find("div.message");
        this.divprivatemessagetext = this.divprivatemessage.find("h4.alert-heading");
        this.bouton_userapi_info = this.divprivate.find("button#private_info");
        this.infosusersopen = false;
        this.BuildEventToPrivatDiv();
        this.bindDisplayUserInfos();
        this.getSaveAccessToken()
        .then((result) => {             
            let reponse = {
                "code":null,
                "message":""
            };
            if(result.email !== undefined){
                this.userEmail = result.email;
                // verifier que l'utilisateur existe dans la BDD
                this.getIsValidUser()
                .then((result) => {
                    if(result.code === 200) {
                        reponse.code = result.code;
                        reponse.message = "Utilisateur autorisé";
                        this.divprivate.trigger("access_on",reponse);
                    } else {
                        reponse.code = result.code;
                        reponse.message = result.message;
                        this.divprivate.trigger("access_off",reponse);
                    }
                })
                .catch((e) => {
                    reponse.code = e.status;
                    reponse.message = "Utilisateur non autorisé.";
                    this.divprivate.trigger("access_off",reponse);

                });

            } else {
                reponse.code = 404;
                reponse.message = "Sign in width";
                this.divprivate.trigger("access_off", reponse);
            }
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

    getSaveAccessToken = async function(){
        let that = this;
        return new Promise(function(resolve,reject) {
            $.ajax({
                url: Routing.generate("bundlesaveaccesstoken"),
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
                        return reject("Impossible de charger getSaveAccessToken");
                    }
                },
                complete    : function(){
                    loadingStop($('body'));
                }
            });
        });
    }

    getIsValidUser = async function(){
        let that = this;
        return new Promise(function(resolve,reject) {
            $.ajax({
                url: Routing.generate("isvaliduser"),
                method: "GET",
                beforeSend : function(){
                    loadingStop($('body'));
                    loadingStart($("body"), "Check is authorized user ...");
                },
                success: function(datas){
                    resolve(datas);
                },
                error: function(e){
                   resolve(e);
                },
                complete    : function(){
                    loadingStop($('body'));
                }
            });
        });
    }

    BuildFormPrivate = async function(event){
        let that = this;
        return new Promise(function(resolve,reject) {
            $('<div>').load(that.template_private, function (response, status, xhr) {

                let blocDiv = $(xhr.responseText);
                blocDiv.attr('id', "privateform");
                that.divprivate.append(blocDiv);
                that.bouton_connect = that.divprivate.find("#privateform").find("button#private_connect");
                that.input_password = that.divprivate.find("#privateform").find("input#inputPassword");

                resolve();
            });
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

    BuildEventToPrivatDiv = function(){
        let that = this;
        that.divprivate.off("access_off");
        that.divprivate.on("access_off", function (e, data) {
            that.divprivatemessage.removeClass("hidden");
            if(data.code === 403){
                that.BuildBtLogout(data.message);
            } else  if(data.code === 401) {
                that.BuildBtLogout(data.message);
            } else  if(data.code === 404) {
                that.BuildBtSocial(data.message);
            }

            that.bindPrivateBtUserApiInfo()
            .then((retour) => {

            })
            .catch((e) => {
                    console.error(e);
                });

        });

        that.divprivate.on("access_on", function (e, data) {
            that.divprivate.find("div.infos_user").addClass('hidden');
            that.divprivatemessage.addClass("hidden");
            that.BuildBtLogout('');
            let BuildFormPrivate =  that.BuildFormPrivate();

            let bindPrivateBtUserApiInfo = BuildFormPrivate.then((result) => {
                return that.bindPrivateBtUserApiInfo();
            });

            let bindPrivateBtConnect = bindPrivateBtUserApiInfo.then((result) => {
                return that.bindPrivateBtConnect()
            });

            let promises = [
                BuildFormPrivate,
                bindPrivateBtUserApiInfo,
                bindPrivateBtConnect
            ];

            Promise.all(promises)
            .then((retour) => {

            })
            .catch((e) => {
                console.error(e);
            });

        });
    }

    BuildEventOnPrivateDatasAccess = function(element){
        let that = this;
        let time = 1000;
        that.divprivate.off("privat");
        that.divprivate.on("privat", function (e, data) {
            let timeout = null;
            timeout = setTimeout(function () {
                $(e.target).fadeOut(time);
            }, time);
        });
    }

    bindPrivateBtConnect = async function(){
        let that = this;

        return new Promise(function(resolve,reject) {
            that.bouton_connect.click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                that.loadPrivateDatas().then((result) => {
                    resolve(result);
                })
                    .catch((e) => {
                        reject(e);
                    });

            });
        });
    }

    //hidden true OR false
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

    loadPrivateDatas = async function(){
        let that = this;
        that.setInfosUserOpen(false);
        return new Promise(function(resolve,reject) {

            let password = that.input_password.val();
            that.animLogin({"action": "open", "error": false, "message": ""});
            if (password !== "") {

                let setIdentifiantAppli = that.setIdentifiantAppli();

                let checkPrivateAccess = setIdentifiantAppli.then((result) => {
                    return that.checkPrivateAccess(result);
                });

                let displayPrivate = checkPrivateAccess.then((result) => {
                    return that.displayPrivate(result);
                });

                let promises = [
                    setIdentifiantAppli,
                    checkPrivateAccess,
                    displayPrivate,
                ];

                Promise.all(promises)
                    .then((retour) => {

                        let contenu = retour[retour.length - 1][1];
                        that.animLogin({
                            "action": "close",
                            "error": retour[retour.length - 1][0].error,
                            "message": retour[retour.length - 1][0].message,
                            "contenu": contenu
                        });
                        resolve(retour);
                    })
                    .catch((e) => {
                        that.animLogin({"action": "close", "error": true, "message": e.responseJSON.message});
                        reject(e);
                    });

            } else {
                let retour = {};
                retour.error = true;
                retour.message = "Empty password";
                that.animLogin({"action": "close", "error": retour.error, "message": retour.message, "contenu": ""});
                resolve(retour);
            }
        });
    }

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

    setIdentifiantAppli = async function(){
        let that = this;
        let retour = {};
        return new Promise(function(resolve,reject) {
            $.ajax({
                url: Routing.generate("setidentifiantappli", {

                }),
                method: "GET",
                success: function (datas) {
                    resolve(datas);
                },
                error: function (e) {
                    retour.error = true;
                    retour.message = e;
                    resolve(e);
                }
            });
        });
    }

    checkPrivateAccess = async function(datasIdentifiant){
        let that = this;
        let password = that.input_password.val();
        let retour = {
            error:true,
            message:""
        };

        return new Promise(function(resolve,reject) {
            if (password !== "") {
                if(datasIdentifiant.error === false) {
                    $.ajax({
                            url: Routing.generate("checkprivateaccess"),
                            method: "POST",
                            data: {
                                identifiant:datasIdentifiant.identifiant,
                                password: password
                            },
                            success: function (datas) {
                                retour = datas;
                                resolve(retour);
                            },
                            error: function (e) {
                                retour.error = true;
                                retour.message = e;
                                resolve(e);
                            }
                        });
                } else {
                    let message = datasIdentifiant.message;
                    reject(message);
                }
            } else {
                retour.error = true;
                retour.message = "Empty password";
                resolve(retour);
            }
        });
    }

    displayPrivate = async function(access) {
        let that = this;
        return new Promise(function(resolve,reject) {
            $.ajax({
                url: Routing.generate("privatedatasaccess"),
                method: "GET",
                success: function (datas) {
                    let private_datas = [];
                    private_datas.push(access);
                    private_datas.push(datas);
                    resolve(private_datas);
                },
                error: function (e) {
                    reject(e);
                }
            });
        });
    }

    animLogin = function(datas){
        let that = this;

        if(datas.action === "open") {
            loadingStart($("body"), "Login in progress, please wait...");
            that.divprivate.find("form#privateform").addClass("hidden");
        } else {
            that.divprivate.find("form#privateform").removeClass("hidden");
            switch(datas.error) {
                case true:
                    that.divprivate.find("form#privateform").find("div.badlogin").remove();
                    let div = document.createElement("DIV");
                    div.setAttribute("class","badlogin alert alert-danger");
                    div.innerHTML = datas.message;
                    that.divprivate.find("form#privateform").prepend(div);
                    break;
                case false:
                    that.divprivate.find("form#privateform").html("");
                    if(datas.contenu !== undefined){
                        let div = document.createElement("DIV");
                        div.setAttribute("class","goodlogin alert alert-success");
                        div.innerHTML = datas.message;
                        that.divprivate.find("form#privateform").prepend(div);

                        div = document.createElement("DIV");
                        div.setAttribute("class","goodlogincontenu");
                        div.innerHTML = datas.contenu;
                        that.divprivate.find("form#privateform").append(div);
                        let alert = that.divprivate.find("form#privateform").find("div.goodlogin");

                        that.BuildEventOnPrivateDatasAccess(alert);
                        alert.trigger("privat",[]);

                    }

                    break;
            }
            loadingStop($('body'));
        }
    }


}

export default Oauth2;