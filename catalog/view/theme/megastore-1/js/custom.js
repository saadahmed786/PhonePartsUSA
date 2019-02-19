$(function(){
	//Top Menu
	$('ul.links li').mouseover(function(){
		$(this).find('ul').show();
	}).mouseleave(function(){
		$('ul.links li > ul').hide();
	});
});

$(function(){
	$('.hshare').css({ opacity: 0.8 }).mouseover(function(){
		$(this).css({opacity:1});
	}).mouseleave(function(){
		$(this).css({opacity:0.8});
	});
});

$(function(){
	//Search
	var searchInput = $('.searchBox input');
	var value = searchInput.val();
	searchInput.click(function(){		
			$(this).val('');
	});
	searchInput.blur(function(){
		if($(this).val() == ""){
				$(this).val(value);
		}
	});
	
	$('#search-input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#button').trigger('click');
		}
	});

	$('#button').bind('click', function() {
		url = $('base').attr('href') + 'index.php?route=product/search';
		
		var filter_name = $('.searchBox input[name=\'filter_name\']').attr('value');
		
		if (filter_name) {
			url += '&filter_name=' + encodeURIComponent(filter_name);
		}
	
		var filter_category_id = $('.searchBox input[name=\'filter_category_id\']').attr('value');
		
		if (filter_category_id > 0) {
			url += '&filter_category_id=' + encodeURIComponent(filter_category_id);
		}	
		location = url;
	});
	

	
	$('.selected-cat').click(function(){
		$('.selectCat ul').show().mouseleave(function(){
			$(this).hide();
		});	
	});	
		
	$('.selectCat ul li > span').click(function(){
		$('.selected-cat').empty().append($(this).text());
		$('.selectCat ul').hide();		
	});
	
	$('.cat-list li').click(function(){		
		var cat_id = $(this).attr('class');
		var cat_name = $(this).text();
		
		if(cat_id){
			$('input#select-cat').attr('value',cat_id);
		}else{
			$('input#select-cat').attr('value','');	
		}
		$('.selected-cat').empty().append(cat_name);
		$('.selectCat ul').hide();		
	});
	
	
	/* Ajax Cart */
	$('#cart > .heading a').live('click', function() {
		$('#cart').addClass('active');
		
		$('#cart').load('index.php?route=module/cart #cart > *');
		
		$('#cart').live('mouseleave', function() {
			$(this).removeClass('active');
		});
	});
	
});

$(function(){
	//Image  Fade
	$('.box img,.product-grid img,.product-list img,.left img').hover(function(){
		this.check = this.check || 1;
		$(this).stop().fadeTo('slow',this.check++%2==0 ? 1 : 0.7);
	});
});

$(function(){
	//Slideshow
	var navWidth = $('.slideNav').width();
	var countLi = $('.slideNav li').size();
	
	var liWidth = navWidth/countLi;
	
	$('.slideNav li a').css('width',liWidth);
	$('.slideNav li a').mouseover(function(){
  		$(this).trigger('click');
	});
});

$(function(){
	//Left Category
	$('.box-category ul li').mouseover(function(){
		var ul = $(this).find('ul');		
		if(!$(ul).is(":visible")){
			$(ul).slideDown();
		}		
	}).mouseleave(function(){	
		if(!$(this).find('a').hasClass('active')){
			$(this).find('ul').slideUp();
		}
	});

$(function(){
	//Sidebar Modules
	$('#column-left .box-product').each(function(){
		var parent = this.parentNode;
	$(this) 
		.after('<div id="imageNav">') 
		.cycle({ 
			fx:     'scrollHorz', 
			speed:  'fast', 
			timeout: 0, 
			pager:  $('#imageNav',parent),
			after: onAfter
	});
	function onAfter(curr, next, opts, fwd) {
	 var $ht = $(this).height();
	 $(this).parent().css({height: $ht});
	}
	
	});
	
});

$(function(){
	$('.menu2 li.mainCat').click(function(){
		$(this).find('ul').toggle();
	}).mouseleave(function(){
		$(this).find('ul').hide();
	});
});

$(function(){
	//Product Page Tabs
	$('.tab-content').first().show();
	$('ul.product-tab li,a.description,a.reviews').click(function(){
		var tab = $(this).attr('class');
		$('.tab-content').hide();
		$('#' + tab).show();
		$('ul.product-tab li a').removeClass('active');
		$(this).find('a').addClass('active');
	});
	$('.product-info .right a.description').click(function(){
		$('ul.product-tab li.description').find('a').addClass('active');
	});
	$('.product-info .right a.reviews').click(function(){
		$('ul.product-tab li.reviews').find('a').addClass('active');
	});
	
});	

$(function(){
	//Sidebar Featured Categories
	$('#column-left .featured-categories > li').mouseover(function(){
		var img = $(this).find('a:first-child').html();
		var href = $(this).find('a:first-child').attr('href');
		if(!$(this).find('ul li').hasClass('cat-img')){
			$(this).find('ul').prepend('<li class="cat-img"><a href="' + href + '">' + img + '</a></li>');
		}
		$(this).find('ul').show();
	}).mouseleave(function(){
		$(this).find('ul').hide();
	});	
	
});

});