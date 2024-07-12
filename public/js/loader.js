function loadingStart(jQueryElement, text, opaque, progressBar, blockingLoading){

    if(typeof text === "undefined")
        text = "Chargement ...";

    if(typeof opaque === "undefined")
        opaque = false;

    if(typeof progressBar === "undefined")
        progressBar = false;

    if(typeof blockingLoading === "undefined")
        blockingLoading = true;

    let html = document.createElement("div");
    html.classList.add("loading");

    if(opaque)
        html.classList.add("opaque");

    if(!blockingLoading)
        html.classList.add("noBlock");

    let loadingContent = document.createElement("div");
    loadingContent.classList.add("content");

    let loaderElement = document.createElement("i");
    loaderElement.classList.add("fa");
    loaderElement.classList.add("fa-spinner");
    loaderElement.classList.add("fa-spin");
    loaderElement.classList.add("fa-2x");
    loaderElement.classList.add("fa-fw");

    let textElement = document.createElement("span");
    textElement.classList.add("text");
    textElement.innerHTML = text;

    loadingContent.appendChild(loaderElement);
    loadingContent.appendChild(textElement);

    if(progressBar) {
        let jaugeContainer = document.createElement("div");
        let jauge = document.createElement("div");
        let jaugePercent = document.createElement("div");

        jaugeContainer.classList.add("jauge_container");
        jaugeContainer.classList.add("jauge_surgele");

        jauge.classList.add("jauge");

        jaugePercent.classList.add("jauge_percent");
        jaugePercent.innerText = "0%";

        jauge.appendChild(jaugePercent);
        jaugeContainer.appendChild(jauge);

        loadingContent.appendChild(jaugeContainer);
    }


    html.appendChild(loadingContent);


    jQueryElement.append(html);
    jQueryElement.scrollTop(0);
    jQueryElement.attr("data-old-overflow",jQueryElement.css("overflow"));
    jQueryElement.css({"overflow" : "hidden"});
}

function loadingChangeText (text) {
    $(".loading .text").text(text);
}

function onProgressChange(callback) {
    callback();
}

function loadingChangeProgressPercent(percent, callback) {
    let context = document.querySelector(".loading .jauge_container .jauge");
    if(context !== null) {
        context.querySelector(".jauge_percent").innerText = `${percent}%`;
        context.style.width = `${percent}%`;
    }
    if(typeof callback === 'function')
        callback(percent);
}

function loadingStop(jQueryElement){
    jQueryElement.find(".loading").fadeOut(function(){$(this).remove();});
    jQueryElement.css({"overflow" : jQueryElement.attr("data-old-overflow")});
    jQueryElement.removeAttr("data-old-overflow");
}


export {
  loadingStart,loadingChangeText,onProgressChange,loadingChangeProgressPercent,loadingStop
}
