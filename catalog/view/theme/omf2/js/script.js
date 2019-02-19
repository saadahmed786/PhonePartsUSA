if(document.readyState !== "complete") {  
	document.addEventListener("DOMContentLoaded", function () {         
		if(topNavAnchors = document.querySelectorAll("#primary .nav > li > a")) {
			var subMenus = document.querySelectorAll("#primary .nav > li > ul");

            document.body.onclick = function() {
                for (var s = 0; s < subMenus.length; s++) {
                    if (subMenus[s].classList.contains("visible")) {
                      subMenus[s].classList.remove("visible");
                    }
                }
            };
      
            var topNavLi = document.querySelectorAll("#primary .nav > li ");

            var j = 0;  
            
            for (var i = 0; i < topNavAnchors.length; i++) {        
            	if(topNavLi[i].getElementsByTagName("ul").length != 0) {
            		j++;
            		topNavAnchors[i].onclick = function(event) {
            			event.preventDefault();
            			event.stopPropagation();
            			for (var s = 0; s < subMenus.length; s++) {
            				if (subMenus[s] !== this.nextElementSibling && subMenus[s].classList.contains("visible")) {
            					subMenus[s].classList.remove("visible");
            				}
            			}
            			if (!this.nextElementSibling.classList.contains("visible")) {
            				this.nextElementSibling.classList.add("visible");
            			}
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

    if (document.documentElement.getAttribute("dir") === "rtl") {
      document.getElementsByTagName("body")[0].className += " rtl";
    }

		if ($.colorbox && window.matchMedia) {
	        // Establishing media check
	        widthCheck = window.matchMedia("(max-width: 768px)");
	        if (widthCheck.matches){
	        	$.colorbox.remove();
	        }
	    }

	    if((tabsAnchors = document.querySelectorAll("#tabs > a")) && tabsAnchors.length) {
	    	for (var i = 1; i <= tabsAnchors.length; i++) {
	    		var id = tabsAnchors[i-1].getAttribute('href').substring(1);
	    		var p = tabsAnchors[0].parentNode;
	    		p.insertBefore(document.getElementById(id), tabsAnchors[i]);
	    		tabsAnchors[i-1].onclick = function() {
	    			if (this.nextElementSibling.classList.contains('selected')) {
	    				this.nextElementSibling.classList.remove('selected');
	    				this.classList.remove('selected');
	    			} else {
	    				if (s = document.querySelector('.tab-content.selected')) {
	    					s.classList.remove('selected');
	    				}
	    				this.nextElementSibling.classList.add('selected');
	    			}
	    		};
	    	}

	    	document.getElementById(tabsAnchors[0].getAttribute('href').substring(1)).classList.add('selected');
	    }

	    if (addToCartButtons = document.querySelectorAll(".cart .button, #button-add-to-cart")) {
	    	for (var i = 0; i < addToCartButtons.length; i++) {
          if (addToCartButtons[i].id !== 'button-cart') {
      			var re = /addToCart\('(.*)'\);/;
      			var quantity;
      			addToCartButtons[i].onclick = function() {        
      				quantity = typeof(quantity) != 'undefined' ? quantity : 1;

      				$.ajax({
      					url: 'index.php?route=checkout/cart/add',
      					type: 'post',
      					data: 'product_id=' + this.getAttribute('onclick').match(re)[1] + '&quantity=' + quantity,
      					dataType: 'json',
      					success: function(json) {
      						$('.success, .warning, .attention, .information, .error').remove();

      						if (json['redirect']) {
      							location = json['redirect'];
      						}

      						if (json['success']) {                  
      							window.location.href = 'index.php?route=checkout/cart';  
      						} 
      					}
      				});
      			}
          }
	    	}
	    }

	}, false);

	if ($.colorbox ) {
		var mql = window.matchMedia("(max-width: 768px)");

	    // Add a media query change listener
	    mql.addListener(function(m) {
	    	if(m.matches) {
		        // Changed to portrait
		        $.colorbox.remove();
		    } else {
		        // Changed to landscape
		        $('.colorbox').colorbox({
		        	overlayClose: true,
		        	opacity: 0.5,
		        	rel: "colorbox"
		        });
		    }
		});
	}

}

function supportsSVG() {
	return !!document.createElementNS && !!document.createElementNS('http://www.w3.org/2000/svg', "svg").createSVGRect;
}

/* ----------------------------------
 * SLIDER v1.0.0
 * Licensed under The MIT License
 * Adapted from Brad Birdsall's swipe
 * http://opensource.org/licenses/MIT
 * ---------------------------------- */

!function () {

  var pageX;
  var pageY;
  var slider;
  var deltaX;
  var deltaY;
  var offsetX;
  var lastSlide;
  var startTime;
  var resistance;
  var sliderWidth;
  var slideNumber;
  var isScrolling;
  var scrollableArea;

  var getSlider = function (target) {
    var i, sliders = document.querySelectorAll('.slider ul');
    for (; target && target !== document; target = target.parentNode) {
      for (i = sliders.length; i--;) { if (sliders[i] === target) return target; }
    }
  }

  var getScroll = function () {
    var translate3d = slider.style.webkitTransform.match(/translate3d\(([^,]*)/);
    return parseInt(translate3d ? translate3d[1] : 0)
  };

  var setSlideNumber = function (offset) {
    var round = offset ? (deltaX < 0 ? 'ceil' : 'floor') : 'round';
    slideNumber = Math[round](getScroll() / ( scrollableArea / slider.children.length) );
    slideNumber += offset;
    slideNumber = Math.min(slideNumber, 0);
    slideNumber = Math.max(-(slider.children.length - 1), slideNumber);
  }

  var onTouchStart = function (e) {
    slider = getSlider(e.target);

    if (!slider) return;

    var firstItem  = slider.querySelector('li');

    scrollableArea = firstItem.offsetWidth * slider.children.length;
    isScrolling    = undefined;
    sliderWidth    = slider.offsetWidth;
    resistance     = 1;
    lastSlide      = -(slider.children.length - 1);
    startTime      = +new Date;
    pageX          = e.touches[0].pageX;
    pageY          = e.touches[0].pageY;

    setSlideNumber(0);

    slider.style['-webkit-transition-duration'] = 0;
  };

  var onTouchMove = function (e) {
    if (e.touches.length > 1 || !slider) return; // Exit if a pinch || no slider

    deltaX = e.touches[0].pageX - pageX;
    deltaY = e.touches[0].pageY - pageY;
    pageX  = e.touches[0].pageX;
    pageY  = e.touches[0].pageY;

    if (typeof isScrolling == 'undefined') {
      isScrolling = Math.abs(deltaY) > Math.abs(deltaX);
    }

    if (isScrolling) return;

    offsetX = (deltaX / resistance) + getScroll();

    e.preventDefault();

    resistance = slideNumber == 0         && deltaX > 0 ? (pageX / sliderWidth) + 1.25 :
                 slideNumber == lastSlide && deltaX < 0 ? (Math.abs(pageX) / sliderWidth) + 1.25 : 1;

    slider.style.webkitTransform = 'translate3d(' + offsetX + 'px,0,0)';
  };

  var onTouchEnd = function (e) {
    if (!slider || isScrolling) return;

    setSlideNumber(
      (+new Date) - startTime < 1000 && Math.abs(deltaX) > 15 ? (deltaX < 0 ? -1 : 1) : 0
    );

    offsetX = slideNumber * sliderWidth;

    slider.style['-webkit-transition-duration'] = '.2s';
    slider.style.webkitTransform = 'translate3d(' + offsetX + 'px,0,0)';

    e = new CustomEvent('slide', {
      detail: { slideNumber: Math.abs(slideNumber) },
      bubbles: true,
      cancelable: true
    });

    slider.parentNode.dispatchEvent(e);
  };

  window.addEventListener('touchstart', onTouchStart);
  window.addEventListener('touchmove', onTouchMove);
  window.addEventListener('touchend', onTouchEnd);

}();