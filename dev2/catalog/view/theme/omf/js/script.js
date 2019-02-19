if(document.readyState !== "complete") {  
	document.addEventListener("DOMContentLoaded", function () {  				
		if(topNavAnchors = document.querySelectorAll("#primary .nav > li > a")) {
			var topNavLi = document.querySelectorAll("#primary .nav > li ");
					
			var j = 0;	
			
			for (var i = 0; i < topNavAnchors.length; i++) {   			
				if(topNavLi[i].getElementsByTagName("ul").length != 0) {
					j++;
					topNavAnchors[i].onclick = function() {	
						return false;
					}; 			
				}
			}
		}
		
		if(languageForm = document.forms["language"]) {
			var languageSelect = languageForm.getElementsByTagName("select");		
			
			languageSelect[0].onchange= function() {
				languageForm.submit();
			};
		}
		
		if(currencyForm = document.forms["currency"]) {
			var currencySelect = currencyForm.getElementsByTagName("select");				
			
			currencySelect[0].onchange= function() {
				currencyForm.submit();
			};		
		}
		
		if(!supportsSVG()) {			
			document.getElementsByTagName("body")[0].className += " no-svg";			
		}
	}, false);
}

function supportsSVG() {
    return !!document.createElementNS && !!document.createElementNS('http://www.w3.org/2000/svg', "svg").createSVGRect;
}

// jQuery Mobile compatibility  plugins
//.submit()
(function($){
    $.fn.submit=function(){
		this.get(0).submit();    	
        return this;
    };
})(jq);

//.before()
(function($){
    $.fn.before = function(opts){
    	for (var i = 0; i < this.length; i++) {
    		$(opts).insertBefore(this[i]);
        }
    	
        return this;
    };
})(jq);

//.after()
(function($){
    $.fn.after = function(opts){
    	for (var i = 0; i < this.length; i++) {
    		$(opts).insertAfter(this[i]);
        }
    	
        return this;
    };
})(jq);

//attr override
(function($){
    $.fn.original_attr = $.fn.attr;

    $.fn.attr = function(attr, value){        
        for (var i = 0; i < this.length; i++) {
            if(value === false) {
                    return $(this).removeAttr(attr);   
            } else {
            	if (value === undefined) {
                    return $(this).original_attr(attr);
                } else {
                    return $(this).original_attr(attr,value);
                }
            }
        }
        //return this;
    };
})(jq);