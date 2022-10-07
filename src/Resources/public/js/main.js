import Oauth2 from "./oauth2";


class Main {
    constructor() {
        this.bt_connect_github = $("a.connect_social");
        this.Oauth2 = new Oauth2();

    }

    bindClickButton = function(){
        let that = this;
        that.bt_connect_github.on("click", function(e, value) {
            e.preventDefault();
            e.stopPropagation();
            if($(this).hasClass("connect_social")){
                window.open($(this).attr("href"), "Get authorise");

            }

        });
    }


}

export default Main;