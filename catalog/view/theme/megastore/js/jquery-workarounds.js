$(document).ready(function () {
	 
	var position_left = $(".categories").position().left;

	$(".categories > ul > li ").hover(function () {
		position_left = $(".categories").position().left;
		var position_li = $(this).position().left;
		var roznica = position_li-position_left;
		var test1 = 960-roznica;
		var troznica = 470-test1;
		if(troznica>0) {
			$(this).find(".column-2").css("margin-left", "-"+troznica+"px");
		}
		var troznica2 = 690-test1;
		if(troznica2>0) {
			$(this).find(".column-3").css("margin-left", "-"+troznica2+"px");
		}
	});
	
		
		/* Products */
		$('.box-product > div ').hover(function() {
			$(this).find('.absolute-hover-product').show();
					
		},function() {
		
			$(this).find('.absolute-hover-product').hide();
		
		}); 
		
	 
	// Animation for the languages and currency dropdown
	$('.switcher').hover(function() {
	$(this).find('.option').stop(true, true).slideDown(300);
	},function() {
	$(this).find('.option').stop(true, true).slideUp(150);
	}); 
	
	/* Ajax Cart */
	$('#cart > .cart-heading').live('click', function() {
		
		$('#cart').addClass("active");

		$('#cart').load('index.php?route=module/cart #cart > *');
				
		$('#cart').live('mouseleave', function() {
			$('#cart').removeClass("active");
		});
	});

	$('.footer-navigation h3').click(function() {
		
		var element_index = $('.footer-navigation h3').index(this);
		var classe = $('.footer-navigation ul').eq(element_index).attr('class');
		
		if(classe == 'no-active') {
		
			$('.footer-navigation ul').eq(element_index).removeClass('no-active');
		
		} else {
		
			$('.footer-navigation ul').eq(element_index).addClass('no-active');
		
		}
	
	});
	
	/* Categories */

	$(".categories > ul > li ").hover(function () {
								
			$(this).find("div.sub-menu").eq(0).show();
								
	},function () {
		
			$(this).find("div.sub-menu").eq(0).hide();		
			
	}); 
	
		$(".categories > ul > li > .sub-menu > ul > li ").hover(function () {
			
		  		$(this).find(".sub-menu").eq(0).show();

		},function () {
					
				$(this).find(".sub-menu").eq(0).hide();

		}); 
	
	/* Search */
	$('.header .button-search').bind('click', function() {
		url = $('base').attr('href') + 'index.php?route=product/search';
				 
		var filter_name = $('.header input[name=\'filter_name\']').attr('value')
		
		if (filter_name) {
			url += '&filter_name=' + encodeURIComponent(filter_name);
		}
		
		location = url;
	});
	
	$('.header .search input[name=\'filter_name\']').keydown(function(e) {
		if (e.keyCode == 13) {
			url = $('base').attr('href') + 'index.php?route=product/search';
			 
			var filter_name = $('.header input[name=\'filter_name\']').attr('value')
			
			if (filter_name) {
				url += '&filter_name=' + encodeURIComponent(filter_name);
			}
			
			location = url;
		}
	});
	
	
$('#content input[name=\'filter_name\']').keydown(function(e) {

	if (e.keyCode == 13) {

		$('#content #button-search').trigger('click');

	}

});



$('#content #button-search').bind('click', function() {

	url = 'index.php?route=product/search';

	

	var filter_name = $('#content input[name=\'filter_name\']').attr('value');

	

	if (filter_name) {

		url += '&filter_name=' + encodeURIComponent(filter_name);

	}



	var filter_category_id = $('#content select[name=\'filter_category_id\']').attr('value');

	

	if (filter_category_id > 0) {

		url += '&filter_category_id=' + encodeURIComponent(filter_category_id);

	}

	

	var filter_sub_category = $('#content input[name=\'filter_sub_category\']:checked').attr('value');

	

	if (filter_sub_category) {

		url += '&filter_sub_category=true';

	}

		

	var filter_description = $('#content input[name=\'filter_description\']:checked').attr('value');

	

	if (filter_description) {

		url += '&filter_description=true';

	}



	location = url;

});
	
	$('.menumobile').click(function () {
	  $('.categories-mobile-links').slideToggle('slow');
	});
		 	 
	/* autoclear function for inputs */
	$('.autoclear').click(function() {
	if (this.value == this.defaultValue) {
	this.value = '';
	}
	});

	$('.autoclear').blur(function() {
	if (this.value == '') {
	this.value = this.defaultValue;
	}
	});
	
	$('.success img, .warning img, .attention img, .information img').live('click', function() {
		$(this).parent().fadeOut('slow', function() {
			$(this).remove();
		});
	});	

});


function addToCart(product_id, quantity) {
	quantity = typeof(quantity) != 'undefined' ? quantity : 1;

	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: 'product_id=' + product_id + '&quantity=' + quantity,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#cart').load('index.php?route=module/cart #cart > *');
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});
}
function addToWishList(product_id) {
	$.ajax({
		url: 'index.php?route=account/wishlist/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
						
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#wishlist-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow');
			}	
		}
	});
}

function addToCompare(product_id) { 
	$.ajax({
		url: 'index.php?route=product/compare/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
						
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#compare-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});
}
