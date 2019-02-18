/*
@author	Dmitriy Kubarev
@link	http://www.simpleopencart.com
@link	http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/ 

$(function() {
    
    if (typeof($.fancybox) == 'function') {
        $('.fancybox').fancybox({
        	width: 560,
        	height: 560,
        	autoDimensions: false
        });
    }
    
    if (typeof($.colorbox) == 'function') {
        $('.colorbox').colorbox({
            width: 560,
            height: 560
        });  
    }
    
    if (typeof($.prettyPhoto) !== 'undefined') {
        $("a[rel^='prettyPhoto']").prettyPhoto({
    	   theme: 'light_square', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
    	   opacity: 0.5,
    	   social_tools: "",
    	   deeplinking: false
        });
    }
    
    $('#simpleregister input[name=customer_type]').live('change', function(){
        if ($(this).val() == 'company') {
            $('#simplecheckout_company').show();
        } else {
            $('#simplecheckout_company').hide();
        }
    });
    
    var masked = [];
    $('.simpleregister input[type=text]').each(function(indx) {
        var mask = $(this).attr('mask');
        if (typeof mask != 'undefined' && mask != '') {
            var name = $(this).attr('name');
            masked[name] = mask;
        }
    });
    for(var i in masked) {
        $('.simpleregister input[name=' + i + ']').mask(masked[i]);
    }
    
    var simplecheckout_customer = $('.simpleregister');
    var city_autocomplete = simplecheckout_customer.find('input[name=main_city]').attr('autocomplete');
    if (typeof city_autocomplete != 'undefined') {
        simplecheckout_customer.find('input[name=main_city]').autocomplete({
    		source: function( request, response ) {
    			$.ajax({
    				url: "index.php?route=account/simpleregister/geo",
    				dataType: "json",
    				data: {
    					term: request.term
    				},
    				success: function( data ) {
                        response( $.map( data, function( item ) {
                        	return {
                                id: item.id,
    							label: item.full,
    							value: item.city,
                                postcode: item.postcode,
                                zone_id: item.zone_id,
                                country_id: item.country_id,
                                city: item.city
    						}
    					}));
    				}
    			});
    		},
    		minLength: 2,
            delay: 300,
    		select: function( event, ui ) {
                if (ui.item) {
                    if (typeof ui.item.country_id != 'undefined') {
                        if (simplecheckout_customer.find("select[name=main_country_id]").length > 0) {
                            if (simplecheckout_customer.find("select[name=main_zone_id]").length > 0) {
                                if (simplecheckout_customer.find("select[name=main_country_id]").val() != ui.item.country_id) {
                                    simplecheckout_customer.find("select[name=main_zone_id]").load('index.php?route=checkout/simplecheckout_customer/zone&country_id=' + ui.item.country_id, function() {
                                        simplecheckout_customer.find("select[name=main_zone_id]").val(ui.item.zone_id);
                                    });
                                } else {
                                    simplecheckout_customer.find("select[name=main_zone_id]").val(ui.item.zone_id);
                                }
                            }
                            simplecheckout_customer.find("select[name=main_country_id]").val(ui.item.country_id);
                        } else if (simplecheckout_customer.find("input[name=main_country_id]").length > 0) {
                            if (simplecheckout_customer.find("select[name=main_zone_id]").length > 0) {
                                if (simplecheckout_customer.find("input[name=main_country_id]").val() != ui.item.country_id) {
                                    simplecheckout_customer.find("select[name=main_zone_id]").load('index.php?route=checkout/simplecheckout_customer/zone&country_id=' + ui.item.country_id, function() {
                                        simplecheckout_customer.find("select[name=main_zone_id]").val(ui.item.zone_id);
                                    });
                                } else {
                                    simplecheckout_customer.find("select[name=main_zone_id]").val(ui.item.zone_id);
                                }
                            }
                            simplecheckout_customer.find("input[name=main_country_id]").val(ui.item.country_id);
                        } else {
                            simplecheckout_customer.append('<input type="hidden" name="main_country_id" value="'+ui.item.country_id+'">');
                        }
                    }
                    if (typeof ui.item.zone_id != 'undefined') {
                        if (simplecheckout_customer.find("select[name=main_zone_id]").length > 0) {
                            simplecheckout_customer.find("select[name=main_zone_id]").val(ui.item.zone_id);
                        } else if (simplecheckout_customer.find("input[name=main_zone_id]").length > 0) {
                            simplecheckout_customer.find("input[name=main_zone_id]").val(ui.item.zone_id);
                        } else {
                            simplecheckout_customer.append('<input type="hidden" name="main_zone_id" value="'+ui.item.zone_id+'">');
                        }
                    }
                    if (typeof ui.item.city != 'undefined') {
                        if (simplecheckout_customer.find("input[name=main_city]").length > 0) {
                            simplecheckout_customer.find("input[name=main_city]").val(ui.item.city);
                        } else {
                            simplecheckout_customer.append('<input type="hidden" name="main_city" value="'+ui.item.city+'">');
                        }
                        
                    }
                    if (typeof ui.item.postcode != 'undefined') {
                        if (simplecheckout_customer.find("input[name=main_postcode]").length > 0) {
                            simplecheckout_customer.find("input[name=main_postcode]").val(ui.item.postcode);
                        } else {
                            simplecheckout_customer.append('<input type="hidden" name="main_postcode" value="'+ui.item.postcode+'">');
                        }
                    }
                }
    		}
    	});
    } 
    
});