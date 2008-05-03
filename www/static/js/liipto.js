var E = YAHOO.util.Event;
var D = YAHOO.util.Dom;



YAHOO.namespace("liipto");

//FIXME: Warte 100ms und nur ein request aufs mal :)

YAHOO.liipto.checkCode = function() {

    var handleSuccess = function(o) {
        if (YAHOO.lang.JSON.parse(o.responseText)) {
            D.setStyle("code","background-color","red");
        } else {
            D.setStyle("code","background-color","green");
        }
    }
    
    
    var handleFailure = function(o) {
        console.log("FAILUre " + alert(o.statusText)); 
    }
    
    var request = function() {
        var value = YAHOO.lang.trim(D.get('code').value);

        if (value == '') {
            D.setStyle("code","background-color","white");
            return; 
        }
        var sUrl = "/api/chk/" + value;
    
        var callback = {
            success:handleSuccess,
            failure:handleFailure,
        };
    
        
        YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    }

    return {
      init: function() {
         E.addListener("code","keyup",request);
        }
    }
}();

 
E.onDOMReady(YAHOO.liipto.checkCode.init);
