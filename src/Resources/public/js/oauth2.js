const routes = require('../../../../../../../public/js/fos_js_routes.json');
const Routing = require('../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);



class Oauth2 {
    constructor() {
        this.userEmail = null;
        this.template_private = "bundles/marmitsgoogleidentification/templates/template_private.html";
        this.template_social_connect = "bundles/marmitsgoogleidentification/templates/template_social_connect.html";
        this.template_bt_logout = "bundles/marmitsgoogleidentification/templates/template_bt_logout.html";
        this.bouton_connect = null;
        this.input_password = null;
        this.divprivate = $("div.private");
        this.BuildEventToPrivatDiv();

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
                    reponse.message = "Impossible de vérifier l'utilisateur";

                    this.divprivate.trigger("access_off",reponse);

                });

                // poste du code saisie
                // on concatene le code saisie avec le code commliqué en password

                // on check le password 
                // si check OK on affiche le resultat BDD 

                
            } else {
                reponse.code = 404;
                reponse.message = "Utilisateur non connecté";
                this.divprivate.trigger("access_off", reponse);
            }
        })
        .catch((e) => {
            console.error(e);
        });
    }


    getSaveAccessToken = async function(){
        let that = this;
        return new Promise(function(resolve,reject) {
            $.ajax({
                url: Routing.generate("bundlesaveaccesstoken"),
                method: "GET",
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
                success: function(datas){
                    resolve(datas);
                },
                error: function(e){

                   resolve(e);
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
            that.divprivate.find("div.message").html(data);
            that.divprivate.append(blocDiv);

        });
    }

    BuildBtLogout = function(data){
        let that = this;
        $('<div>').load(that.template_bt_logout, function (response, status, xhr) {
            let blocDiv = $(xhr.responseText);
            blocDiv.attr('id', "logout");
            that.divprivate.find("div.message").html(data);
            that.divprivate.append(blocDiv);

        });
    }

    BuildEventToPrivatDiv = function(){
        let that = this;
        that.divprivate.off("access_off");
        that.divprivate.on("access_off", function (e, data) {

            that.divprivate.find("div.message").removeClass("hidden");
            if(data.code === 403){
                that.BuildBtLogout(data.message);
            } else  if(data.code === 401) {
                that.BuildBtLogout(data.message);
            } else  if(data.code === 404) {
                that.BuildBtSocial(data.message);
            }

        });

        that.divprivate.on("access_on", function (e, data) {
            that.divprivate.find("div.message").addClass("hidden");

            let BuildFormPrivate =  that.BuildFormPrivate();

            let bindPrivateBtConnect = BuildFormPrivate.then((result) => {
                return that.bindPrivateBtConnect();
            });

            let promises = [
                BuildFormPrivate,
                bindPrivateBtConnect
            ];

            Promise.all(promises)
            .then((retour) => {
                console.log("ok");
            })
            .catch((e) => {
                console.error(e);
            });

        });
    }

    bindPrivateBtConnect = async function(){
        let that = this;
        return new Promise(function(resolve,reject) {
            that.bouton_connect.click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                let checkPrivateAccess = that.checkPrivateAccess();

                let displayPrivate = checkPrivateAccess.then((result) => {

                    return that.displayPrivate(result);
                });


                let promises = [
                    checkPrivateAccess,
                    displayPrivate
                ];

                Promise.all(promises)
                    .then((retour) => {

                    })
                    .catch((e) => {
                        console.error(e);
                    });


            });
        });
    }

    checkPrivateAccess = async function(){
        let that = this;
        let password = that.input_password.val();
        let retour = {
            error:true,
            message:""
        };

        return new Promise(function(resolve,reject) {
            if(password !== "") {
                $.ajax({
                    url: Routing.generate("checkprivateaccess"),
                    method: "POST",
                    data: {
                        password: password,
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
                retour.error = true;
                retour.message = "Empty password";
                resolve(retour);
            }
        });
    }


    displayPrivate = async function(access) {
        let that = this;
        return new Promise(function(resolve,reject) {
            console.log(access);
            resolve("display");

        });
    }





}

export default Oauth2;