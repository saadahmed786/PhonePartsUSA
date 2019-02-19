/*
@author	Dmitriy Kubarev
@link	http://www.simpleopencart.com
@link	http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/

function simple_parse_json(text) {
    try {
        var pos = text.indexOf('{');
        return jQuery.parseJSON(text.substr(pos));
    } catch (exc) { 
        var arr = [];
        arr['output'] = text;
        return arr;
    }
}

function simple_move_warning_block() {
    var block = $('.simplecheckout #simplecheckout_customer .simplecheckout-warning-block').attr('block');
    if (typeof block != 'undefined') {
        $('.simplecheckout #simplecheckout_' + block + ' .simplecheckout-warning-block').remove();
        $('.simplecheckout #simplecheckout_' + block + ' .simplecheckout-block-content').prepend($('.simplecheckout #simplecheckout_customer .simplecheckout-warning-block'));
        $('.simplecheckout #simplecheckout_customer .simplecheckout-warning-block').remove();
        $('html, body').animate({ scrollTop: $('.simplecheckout #simplecheckout_' + block + ' .simplecheckout-warning-block').offset().top }, 'slow');
    }
}

function simple_masked_input() {
    var masked = [];
    $('.simplecheckout #simplecheckout_customer input[type=text]').each(function(indx) {
        var mask = $(this).attr('mask');
        if (typeof mask != 'undefined' && mask != '') {
            var name = $(this).attr('name');
            masked[name] = mask;
        }
    });
    for(var i in masked) {
        $('.simplecheckout #simplecheckout_customer input[name=' + i + ']').mask(masked[i]);
    }
}

function simple_create_columns() {
    if ($('.simplecheckout #simplecheckout_customer .simplecheckout-customer').parents('.simplecheckout-left-column,.simplecheckout-right-column').length == 0) {
        $('.simplecheckout #simplecheckout_customer .simplecheckout-customer:even').addClass('simplecheckout-customer-first');
        $('.simplecheckout #simplecheckout_customer .simplecheckout-customer:odd').addClass('simplecheckout-customer-second');
    }
}

function simple_check_system() {

}

function simple_save_data() {
    $.ajax({
		url: 'index.php?route=checkout/simplecheckout_customer/save',
		data: $('.simplecheckout #simplecheckout_customer').find('input:not(input[type=radio],input[type=checkbox]),#payment_address_same:checked,#agree:checked,input[type=radio]:checked,select,textarea'),
		type: 'POST',
        dataType: 'text',
		success: function(data) {
            load_shipping();			
		}
	});
}

function load_customer() {
    $.ajax({
		url: 'index.php?route=checkout/simplecheckout_customer',
		dataType: 'text',
        beforeSend: function() {
        },
        data: {
            address_id : $('select[name=address_id]').val(),
            payment_address_id : $('select[name=payment_address_id]').val()
        },
		success: function(text) {
            var json = simple_parse_json(text);
            
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['output']) {		
				$('.simplecheckout #simplecheckout_customer').html(json['output']);
                simple_move_warning_block();
                simple_create_columns();
                simple_check_system();
                simple_masked_input();
                simple_autocomplete();
			}
            load_shipping();
		},
        error: function(jqXHR, textStatus, errorThrown) {
            var data = '';
            if (textStatus == 'parsererror') {
                var pos = jqXHR.responseText.indexOf('{');
                data = jqXHR.responseText.substr(0, pos);
            } else {
                data = textStatus + ': See error logs' + '<br>' + jqXHR.responseText;
            }
            $('.simplecheckout #simplecheckout_customer').html(data);
            load_shipping();
        }
	});	
}

function load_customer_only() {
    $.ajax({
		type: 'GET',
		url: 'index.php?route=checkout/simplecheckout_customer',
		dataType: 'text',	
        beforeSend: function() {
            overlay_block('.simplecheckout #simplecheckout_customer');      
		},	
		success: function(text) {
            var json = simple_parse_json(text);
            
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['output']) {		
				$('.simplecheckout #simplecheckout_customer').html(json['output']);
                simple_move_warning_block();
                $('.simplecheckout #simplecheckout_customer #simplecheckout_customer_login').css('display','inline-block');
                $('.simplecheckout #simplecheckout_customer #simplecheckout_customer_cancel').hide();
                overlay_remove();
                $('#simplecheckout_button_order').parent().show();
                simple_create_columns();
                simple_masked_input();
                simple_autocomplete();
			}
		}
	});
}

function load_shipping() {
    $.ajax({
		url: 'index.php?route=checkout/simplecheckout_shipping',
		beforeSend: function() {
		},
		success: function(data) {
            if (data.length == 0) {
                $('.simplecheckout #simplecheckout_shipping').remove();
            }
            var shipping_block = $('.simplecheckout #simplecheckout_shipping');
			if (shipping_block.length > 0) {
                shipping_block.html(data);
            }
            load_payment();
		},
        error: function(jqXHR, textStatus, errorThrown) {
            var shipping_block = $('.simplecheckout #simplecheckout_shipping');
			if (shipping_block.length > 0) {
                var data = '';
                if (textStatus == 'parsererror') {
                    var pos = jqXHR.responseText.indexOf('{');
                    data = jqXHR.responseText.substr(0, pos);
                } else {
                    data = textStatus + ': See error logs' + '<br>' + jqXHR.responseText;
                }
                shipping_block.html(data);
            }
            load_payment();
        }
	});	
}

function load_payment() {
    $.ajax({
		url: 'index.php?route=checkout/simplecheckout_payment',
	    beforeSend: function() {
		},
		success: function(data) {
            var payment_block = $('.simplecheckout #simplecheckout_payment');
			if (payment_block.length > 0) {
                payment_block.html(data);
            }
            load_cart();
		},
        error: function(jqXHR, textStatus, errorThrown) {
            var payment_block = $('.simplecheckout #simplecheckout_payment');
			if (payment_block.length > 0) {
                var data = '';
                if (textStatus == 'parsererror') {
                    var pos = jqXHR.responseText.indexOf('{');
                    data = jqXHR.responseText.substr(0, pos);
                } else {
                    data = textStatus + ': See error logs' + '<br>' + jqXHR.responseText;
                }
                payment_block.html(data);
            }
            load_cart();
        }
	});	
}

function load_cart() {
    $.ajax({
		url: 'index.php?route=checkout/simplecheckout_cart',
		dataType: 'text',
        beforeSend: function() {
		},
		success: function(text) {
            var json = simple_parse_json(text);
            
			if (json['redirect']) {
				location = json['redirect'];
			}
			
            if (json['block_order']) {
                $('#simplecheckout_button_order').parent().hide();
			} else {
                $('#simplecheckout_button_order').parent().show();
            }
            
			if (json['output']) {		
				$('.simplecheckout #simplecheckout_cart').html(json['output']);
			}
            
            if (json['total']) {
				$('#cart_total').html(json['total']);
                $('#cart-total').html(json['total']);
                $('#cart_menu .s_grand_total').html(json['total']);
			}
		},
        error: function(jqXHR, textStatus, errorThrown) {
            var data = '';
            if (textStatus == 'parsererror') {
                var pos = jqXHR.responseText.indexOf('{');
                data = jqXHR.responseText.substr(0, pos);
            } else {
                data = textStatus + ': See error logs' + '<br>' + jqXHR.responseText;
            }
            $('.simplecheckout #simplecheckout_cart').html(data);
        }
	});	
}

function reload_simplecheckout() {
    overlay_block('.simplecheckout #simplecheckout_payment');
    overlay_block('.simplecheckout #simplecheckout_shipping');
    overlay_block('.simplecheckout #simplecheckout_cart');  
    overlay_block('.simplecheckout #simplecheckout_customer');      
    
    load_customer();
}

function load_simplecheckout() {
    load_customer();
}

function update_simplecheckout_cart(remove_key) {
    var cart_data = $('#simplecheckout_cart_form :input');
    
    if (typeof(remove_key) != 'undefined') {
        cart_data = { 
            remove: remove_key
        };
    }
    
    $.ajax({
		type: 'POST',
		url: 'index.php?route=checkout/simplecheckout_cart',
		data: cart_data,
		dataType: 'text',
        beforeSend: function() {
	       overlay_block('.simplecheckout #simplecheckout_cart');
    	},		
        success: function(text) {
            var json = simple_parse_json(text);
            
            if (json['redirect']) {
				location = json['redirect'];
                return;
			}
            
            if (json['block_order']) {
                $('#simplecheckout_button_order').parent().hide();
			} else {
                $('#simplecheckout_button_order').parent().show();
            }
            
			if (json['reload_simplecheckout']) {
				if (typeof(reload_simplecheckout) == 'function') {
                    reload_simplecheckout();
                    return;
                }
			}
            
			if (json['output']) {		
				$('.simplecheckout #simplecheckout_cart').html(json['output']);
			}
		}
	});
}

function overlay_block(selector, transparent) {
    var obj = $(selector);
    if (obj.length > 0) {
        var background = transparent ? '' : 'url(catalog/view/theme/default/image/loading.gif) no-repeat center center';
        var blockHeight = obj.height();
        var blockWidth =  obj.width();
        var blockOffset = obj.offset();
        obj.append("<div class='simplecheckout_overlay'></div>");
        $(selector + " .simplecheckout_overlay")
            .css({
                'background' : background,
                'opacity' : 0.4,
                'position': 'absolute',
                'width': blockWidth,
                'height': blockHeight,
                'z-index': 5000
            })
            .offset({top: blockOffset.top,left: blockOffset.left});
    }
}

function overlay_remove() {
    $(".simplecheckout_overlay").remove();
}

function simple_autocomplete() {
    var simplecheckout_customer = $('.simplecheckout #simplecheckout_customer');
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
                    simple_save_data();
                }
    		}
    	});
        simplecheckout_customer.find('input[name=payment_city]').autocomplete({
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
                        if (simplecheckout_customer.find("select[name=payment_country_id]").length > 0) {
                            if (simplecheckout_customer.find("select[name=payment_zone_id]").length > 0) {
                                if (simplecheckout_customer.find("select[name=payment_country_id]").val() != ui.item.country_id) {
                                    simplecheckout_customer.find("select[name=payment_zone_id]").load('index.php?route=checkout/simplecheckout_customer/zone&country_id=' + ui.item.country_id, function() {
                                        simplecheckout_customer.find("select[name=payment_zone_id]").val(ui.item.zone_id);
                                    });
                                } else {
                                    simplecheckout_customer.find("select[name=payment_zone_id]").val(ui.item.zone_id);
                                }
                            }
                            simplecheckout_customer.find("select[name=payment_country_id]").val(ui.item.country_id);
                        } else if (simplecheckout_customer.find("input[name=payment_country_id]").length > 0) {
                            if (simplecheckout_customer.find("select[name=payment_zone_id]").length > 0) {
                                if (simplecheckout_customer.find("input[name=payment_country_id]").val() != ui.item.country_id) {
                                    simplecheckout_customer.find("select[name=payment_zone_id]").load('index.php?route=checkout/simplecheckout_customer/zone&country_id=' + ui.item.country_id, function() {
                                        simplecheckout_customer.find("select[name=payment_zone_id]").val(ui.item.zone_id);
                                    });
                                } else {
                                    simplecheckout_customer.find("select[name=payment_zone_id]").val(ui.item.zone_id);
                                }
                            }
                            simplecheckout_customer.find("input[name=payment_country_id]").val(ui.item.country_id);
                        } else {
                            simplecheckout_customer.append('<input type="hidden" name="payment_country_id" value="'+ui.item.country_id+'">');
                        }
                    }
                    if (typeof ui.item.zone_id != 'undefined') {
                        if (simplecheckout_customer.find("select[name=payment_zone_id]").length > 0) {
                            simplecheckout_customer.find("select[name=payment_zone_id]").val(ui.item.zone_id);
                        } else if (simplecheckout_customer.find("input[name=payment_zone_id]").length > 0) {
                            simplecheckout_customer.find("input[name=payment_zone_id]").val(ui.item.zone_id);
                        } else {
                            simplecheckout_customer.append('<input type="hidden" name="payment_zone_id" value="'+ui.item.zone_id+'">');
                        }
                    }
                    if (typeof ui.item.city != 'undefined') {
                        if (simplecheckout_customer.find("input[name=payment_city]").length > 0) {
                            simplecheckout_customer.find("input[name=payment_city]").val(ui.item.city);
                        } else {
                            simplecheckout_customer.append('<input type="hidden" name="payment_city" value="'+ui.item.city+'">');
                        }
                        
                    }
                    if (typeof ui.item.postcode != 'undefined') {
                        if (simplecheckout_customer.find("input[name=payment_postcode]").length > 0) {
                            simplecheckout_customer.find("input[name=payment_postcode]").val(ui.item.postcode);
                        } else {
                            simplecheckout_customer.append('<input type="hidden" name="payment_postcode" value="'+ui.item.postcode+'">');
                        }
                    }
                    simple_save_data();
                }
    		}
    	});
        simplecheckout_customer.find('select[name=main_country_id],select[name=main_zone_id],input[name=main_postcode],input[name=main_address_1],input[name=main_address_2]').bind('change', function() {
            simple_save_data();
        });
        simplecheckout_customer.find('select[name=payment_country_id],select[name=payment_zone_id],input[name=payment_postcode],input[name=payment_address_1],input[name=payment_address_2]').bind('change', function() {
            simple_save_data();
        });
    } else {
        simplecheckout_customer.find('select[name=main_country_id],select[name=main_zone_id],input[name=main_postcode],input[name=main_city],input[name=main_address_1],input[name=main_address_2]').bind('change', function() {
            simple_save_data();
        });
        simplecheckout_customer.find('select[name=payment_country_id],select[name=payment_zone_id],input[name=payment_postcode],input[name=payment_city],input[name=payment_address_1],input[name=payment_address_2]').bind('change', function() {
            simple_save_data();
        });
    }
}

$(function() {
    
    simple_create_columns();
    simple_check_system();
    simple_masked_input();
          
    $(".simplecheckout #simplecheckout_payment input[name=payment_method]").live('change', function () {
        $('.simplecheckout #simplecheckout_payment .simplecheckout-warning-block').remove();
        $.ajax({
    		url: 'index.php?route=checkout/simplecheckout_payment/select',
            type: 'POST',
    		data: {
                code: $(this).val()
            },
            dataType: 'text',
            success: function() {
                if (typeof(load_cart) == 'function') {
                    load_cart();
                }
            }   
    	});	
    });
    
    $(".simplecheckout #simplecheckout_shipping input[name=shipping_method]").live('change', function () {
        $('.simplecheckout #simplecheckout_shipping .simplecheckout-warning-block').remove();
        var shipping_code = $(this).val();
        $.ajax({
    		type: 'POST',
    		url: 'index.php?route=checkout/simplecheckout_customer/save',
    		data: $('.simplecheckout #simplecheckout_customer').find('input:not(input[type=radio],input[type=checkbox]),#payment_address_same:checked,#agree:checked,input[type=radio]:checked,select,textarea'),
    		dataType: 'text',
            success: function() {
                $.ajax({
            		url: 'index.php?route=checkout/simplecheckout_shipping/select',
                    type: 'POST',
            		data: {
                        code: shipping_code
                    },
                    success: function(data) {
                        if (typeof(reload_simplecheckout) == 'function') {
                            reload_simplecheckout();
                        }
            		}
            	});
            }
        });
        
    });
    
    $('.simplecheckout #simplecheckout_customer #simplecheckout_customer_login').live('click', function() {
        $.ajax({
    		type: 'GET',
    		url: 'index.php?route=checkout/simplecheckout_customer/login',
    		dataType: 'text',	
            beforeSend: function() {
                overlay_block('.simplecheckout #simplecheckout_customer');      
            },	
    		success: function(text) {
                var json = simple_parse_json(text);
                
    			if (json['redirect']) {
    				location = json['redirect'];
    			}
    			
    			if (json['output']) {		
    				$('.simplecheckout #simplecheckout_customer').html(json['output']);
                    simple_move_warning_block();
                    $('.simplecheckout #simplecheckout_customer #simplecheckout_customer_login').hide();
                    $('.simplecheckout #simplecheckout_customer #simplecheckout_customer_cancel').css('display','inline-block');
                    overlay_block('.simplecheckout #simplecheckout_payment', true);
                    overlay_block('.simplecheckout #simplecheckout_shipping', true);
                    overlay_block('.simplecheckout #simplecheckout_cart', true); 
                    $('#simplecheckout_button_order').parent().hide();
                    simple_masked_input();
    			}
    		}
    	});
    });
    
    $('.simplecheckout #simplecheckout_customer #simplecheckout_customer_cancel').live('click', function() {
        load_customer_only();
    });
    
    $('.simplecheckout #simplecheckout_customer #simplecheckout_button_login').live('click',function() {
        $.ajax({
    		type: 'POST',
    		url: 'index.php?route=checkout/simplecheckout_customer/login',
    		data: $('.simplecheckout #simplecheckout_customer :input'),
    		dataType: 'text',	
            beforeSend: function() {
    			$('.simplecheckout #simplecheckout_customer .simplecheckout-warning-block').remove();
    			overlay_block('.simplecheckout #simplecheckout_customer');  
            },	
    		success: function(text) {
                var json = simple_parse_json(text);
                
    			if (json['redirect']) {
    				location = json['redirect'];
    			}
    			
    			if (json['output']) {		
    				$('.simplecheckout #simplecheckout_customer').html(json['output']);
    			}
                
                if (json['reload']) {	
                    location.reload();
    			}                        
    		}
    	});
    });
    
    $('.simplecheckout #simplecheckout_customer input[name=customer_register]').live('change', function(){
        if ($(this).val() == 1) {
            $('.simplecheckout #simplecheckout_customer #create_password').show();
        } else {
            $('.simplecheckout #simplecheckout_customer #create_password').hide();
        }
    });
    
    $('.simplecheckout #simplecheckout_customer input[name=customer_type]').live('change', function(){
        if ($(this).val() == 'company') {
            $('.simplecheckout #simplecheckout_customer #simplecheckout_company').show();
        } else {
            $('.simplecheckout #simplecheckout_customer #simplecheckout_company').hide();
        }
    });
    
    $('.simplecheckout #simplecheckout_cart #simplecheckout_cart_form td.quantity').live('keypress', function (event) {
        if (event.keyCode == 13) {
            if (typeof(update_simplecheckout_cart) == 'function') {
                update_simplecheckout_cart();
            }
            return false;
        }
    });
    
    $('.simplecheckout #simplecheckout_customer input[name=register]').live('change', function() {
        var customer_view_type = $('#simple_customer_view_email').text();
        if ($(this).val() == 0) {
            if (customer_view_type == 0) {
                $('#email_row').hide();
                $('#subscribe_row').hide();
            } else if (customer_view_type == 1) {
                $('#email_row').show();
                $('#email_row .simplecheckout-required').hide();
                $('#email_row .simplecheckout-error-text').hide();
                $('#subscribe_row').show();
            } else if (customer_view_type == 2) {
                $('#email_row').show();
                $('#email_row .simplecheckout-required').show();
                $('#email_row .simplecheckout-error-text').show();
                $('#subscribe_row').show();
            }
            $('#password_row').hide();
            $('#confirm_password_row').hide();
        } else {
            $('#email_row').show();
            $('#email_row .simplecheckout-required').show();
            $('#email_row .simplecheckout-error-text').show();
            var autogenerate = $('#password_row').attr('autogenerate');
            if (typeof autogenerate === 'undefined') {
                $('#password_row').show();
                $('#confirm_password_row').show();
            }
            $('#subscribe_row').show();
        }
    });
    $('#payment_address_same').live('change', function() {
        if ($(this).prop('checked')) {
            $('#simple_payment_address_block').hide();
        } else {
            $('#simple_payment_address_block').show();
        }
        simple_save_data();
    });
    $('#simplecheckout_button_order').live('click', function() {
        $.ajax({
    		type: 'POST',
    		url: 'index.php?route=checkout/simplecheckout_customer',
    		data: $('.simplecheckout #simplecheckout_customer input:not(input[type=radio],input[type=checkbox]),#payment_address_same:checked,#agree:checked,.simplecheckout #simplecheckout_customer input[type=radio]:checked,.simplecheckout #simplecheckout_customer select,,.simplecheckout #simplecheckout_customer textarea'),
    		dataType: 'text',		
            beforeSend: function() {
    			$('#simplecheckout_button_order').parent().hide();
                overlay_block('.simplecheckout #simplecheckout_payment');
                overlay_block('.simplecheckout #simplecheckout_shipping');
                overlay_block('.simplecheckout #simplecheckout_cart');  
                overlay_block('.simplecheckout #simplecheckout_customer');
    		},
    		complete: function() {
    			
    		},
    		success: function(text) {
                var json = simple_parse_json(text);
                
    			if (json['redirect']) {
    				location = json['redirect'];
    			}
    			
    			if (json['output']) {		
    				$('.simplecheckout #simplecheckout_customer').html(json['output']);
                    if ($('.simplecheckout #simplecheckout_customer .simplecheckout-error-text').length > 0) {
                        $('html, body').animate({ scrollTop: $('.simplecheckout #simplecheckout_customer .simplecheckout-error-text:first').offset().top }, 'slow');
                    }
                    simple_move_warning_block();
                    simple_create_columns();
                    simple_masked_input();
                    simple_autocomplete();
    			}
                
                if (json['output'] && !json['payment']) {
                    $('#simplecheckout_button_order').parent().show();
                    overlay_remove();
                }
                
                if (json['payment']) {	
                    $('#simplecheckout_button_order').parent().hide();
                    overlay_block('.simplecheckout #simplecheckout_payment');
                    overlay_block('.simplecheckout #simplecheckout_shipping');
                    overlay_block('.simplecheckout #simplecheckout_cart');  
                    overlay_block('.simplecheckout #simplecheckout_customer');
                
    				$('#simplecheckout_payment_form').html(json['payment']);
                    if ($('p,input[type=text],input[type=radio],input[type=checkbox],input[type=password],select,h1,h2,h3,#simple_auto_off', $('#simplecheckout_payment_form')).length > 0) {
                        $('#simplecheckout_payment_form').css('height','');
                        $('body').scrollTop($('#simplecheckout_payment_form').offset().top);
                        $('#simplecheckout_payment_form .checkout-content').show();
                    } else {
                        var simplecheckout_payment_form = $('#simplecheckout_payment_form');
                        var gateway_link = simplecheckout_payment_form.find('div.buttons a:last').attr('href');
                        var submit_button = simplecheckout_payment_form.find('div.buttons a:last,div.buttons input[type=button]:last,div.buttons input[type=submit]:last');
                        var last_button = simplecheckout_payment_form.find('input[type=button]:last,input[type=submit]:last');
                        var last_link = simplecheckout_payment_form.find('a:last').attr('href');
                        
                        if (typeof gateway_link != 'undefined' && gateway_link != '' && gateway_link != '#') {
                            location = gateway_link;
                            $('#simplecheckout_proceed_payment').show();
                        } else if (submit_button.length) {
                            submit_button.click();
                            $('#simplecheckout_proceed_payment').show();
                        } else if (last_button.length) {
                            last_button.click();
                            $('#simplecheckout_proceed_payment').show();
                        } else if (typeof last_link != 'undefined' && last_link != '' && last_link != '#') {
                            location = last_link;
                            $('#simplecheckout_proceed_payment').show();
                        } else {
                            $('#simplecheckout_payment_form').css('height','');
                            $('body').scrollTop($('#simplecheckout_payment_form').offset().top);
                        }
                    }
    			}
    		},
            error: function(jqXHR, textStatus, errorThrown) {
                var data = '';
                if (textStatus == 'parsererror') {
                    var pos = jqXHR.responseText.indexOf('{');
                    data = jqXHR.responseText.substr(0, pos);
                } else {
                    data = textStatus + ': See error logs' + '<br>' + jqXHR.responseText;
                }
                $('.simplecheckout #simplecheckout_customer').html(data);
                load_shipping();
            }
    	});
    });
    
    $('table.cart td.remove img,.mini-cart-info td.remove img,table.s_cart_items a.s_button_remove').live('click', function(){
        load_simplecheckout();
    });

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
    
    simple_autocomplete();
});