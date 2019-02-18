/**
Vertigo Tip by www.vertigo-project.com
Requires jQuery
*/

this.vtip = function() {    
    this.xOffset = -10; // x distance from mouse
    this.yOffset = 10; // y distance from mouse       
    	
    $(".vtip").unbind().hover(    
        function(e) {
            this.t = this.title;
            this.title = ''; 
            this.top = (e.pageY + yOffset); this.left = (e.pageX + xOffset);
            
            $('body').append( '<p id="vtip">' + this.t + '</p>' );
            
			$('p#vtip').css("display", "none");
			$('p#vtip').css("position", "absolute");
			$('p#vtip').css("padding", "10px");
			$('p#vtip').css("left", "5px");	
			$('p#vtip').css("font-size", "11px");
			$('p#vtip').css("background-color", "white");
			$('p#vtip').css("border", "1px solid #DDDDDD");
			$('p#vtip').css("background-color", "white");
			$('p#vtip').css("-moz-border-radius", "5px");
			$('p#vtip').css("-webkit-border-radius", "5px");
			$('p#vtip').css("z-index", "9999");
			
            $('p#vtip').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");

			
        },
        function() {
            this.title = this.t;
            $("p#vtip").fadeOut("fast").remove();
        }
    ).mousemove(
        function(e) {
            this.top = (e.pageY + yOffset);
            this.left = (e.pageX + xOffset);
                         
            $("p#vtip").css("top", this.top+"px").css("left", this.left+"px");
        }
    );            
    
};

jQuery(document).ready(function($){vtip();}) 