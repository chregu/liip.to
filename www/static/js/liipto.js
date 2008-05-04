var E = YAHOO.util.Event;
var D = YAHOO.util.Dom;

//FIXME: Document it

YAHOO.namespace("liipto");

YAHOO.liipto.checkCode = function() {

    var keypressTimer = null;
    var codeCheckRequest = null;
    var codeCheckResults = [];
    
    var handleSuccess = function(o) {
        D.setStyle('codeOkSpinner','visibility', 'hidden');
        var result = YAHOO.lang.JSON.parse(o.responseText);
        if (result) {
            codeRed();
        } else {
            codeGreen();
        }
        
        codeCheckResults[o.argument.val] = result;
    };
    
    
    var handleFailure = function(o) {
        console.log("FAILUre " + alert(o.statusText)); 
    };
    
    var codeKeypressAsync = function() {
        YAHOO.lang.later(1,this,codeKeypress);
    };
    
    var codeKeypress = function() {
    
        var value = YAHOO.lang.trim(D.get('code').value);
        
        if (keypressTimer) {
            keypressTimer.cancel();
        }
        
        if (codeCheckRequest && YAHOO.util.Connect.isCallInProgress(codeCheckRequest)) {
            YAHOO.util.Connect.abort(codeCheckRequest); 
        }
        
        if (value === '') {
            D.setStyle("codeOk","background-color","white");
            D.setStyle('codeOkSpinner','visibility', 'hidden');
            return; 
        }
        
        
        if (YAHOO.lang.isUndefined(codeCheckResults[value])) { 
           D.setStyle('codeOkSpinner','visibility', 'visible');
           keypressTimer = YAHOO.lang.later(200,this,request);
        } else {
            D.setStyle('codeOkSpinner','visibility', 'hidden');
            if (codeCheckResults[value]) {
                codeRed();
            } else {
                codeGreen();
            }
        }
            
    };
    
    var codeRed = function() {
        D.setStyle("codeOk","background-color","red");
    };
    
    var codeGreen = function() {
        D.setStyle("codeOk","background-color","green");
    };
    
    var request = function() {
        var value = YAHOO.lang.trim(D.get('code').value);
        D.setStyle('codeOkSpinner','visibility', 'visible');
        var sUrl = "/api/chk/" + value;
        var callback = {
            success: handleSuccess,
            failure: handleFailure,
            argument: {'val':value}
        };

        if (codeCheckRequest && YAHOO.util.Connect.isCallInProgress(codeCheckRequest)) {
            YAHOO.util.Connect.abort(codeCheckRequest); 
        }

        codeCheckRequest = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    };

    return {
      init: function() {
         E.addListener("code","keyup",codeKeypressAsync);
      }
    };
}();

 
E.onDOMReady(YAHOO.liipto.checkCode.init);

