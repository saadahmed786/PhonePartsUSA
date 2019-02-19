<?php

class ControllerCheckoutCheckout extends Controller {

    public function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');
    	$this->data['button_continue'] = $this->language->get('button_continue');

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $this->redirect($this->url->link('checkout/cart'));
        }

        if (isset($_SESSION['warning'])) {
				$this->data['error_warning'] = $_SESSION['warning'];
			} else {
				$this->data['error_warning'] = '';
			}
        unset($_SESSION['warning']);
        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $this->redirect($this->url->link('checkout/cart'));
            }
        }

        $this->language->load('checkout/checkout');
        $this->language->load('onestepcheckout/checkout');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
	$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
        
         $dir = $this->language->get('direction');
	 if($dir=='rtl'){
	 $this->document->addStyle('catalog/view/theme/default/stylesheet/osc/css/rtlstyle.css?v=2');
	 } else{
	 $this->document->addStyle('catalog/view/theme/default/stylesheet/osc/css/style.css?v=2');
	 } 
         
        $this->document->addStyle('catalog/view/theme/default/stylesheet/osc/css/style_ch_ie.css');


        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_cart'),
            'href' => $this->url->link('checkout/cart'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('checkout/checkout', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_checkout_option'] = $this->language->get('text_checkout_option');
        $this->data['text_checkout_account'] = $this->language->get('text_checkout_account');
        $this->data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
        $this->data['text_checkout_shipping_address'] = $this->language->get('text_checkout_shipping_address');
        $this->data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
        $this->data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');
        $this->data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');
        $this->data['text_modify'] = $this->language->get('text_modify');
        $this->data['text_your_details'] = $this->language->get('text_your_details');
        $this->data['text_alredy_registred'] = $this->language->get('text_alredy_registred');
        $this->data['text_ckb_delivery'] = $this->language->get('text_ckb_delivery');
        $this->data['text_delivery_addresses'] = $this->language->get('text_delivery_addresses');
        $this->data['text_ckb_billing'] = $this->language->get('text_ckb_billing');
        $this->data['text_billing_adresses'] = $this->language->get('text_billing_adresses');
        $this->data['text_create_account'] = $this->language->get('text_create_account');
        $this->data['text_shipping_meth'] = $this->language->get('text_shipping_meth');
        $this->data['text_payment_meth'] = $this->language->get('text_payment_meth');
        $this->data['text_confirm_order'] = $this->language->get('text_confirm_order');
        $this->data['text_express_checkout'] = $this->language->get('text_express_checkout');
        $this->data['text_header_info'] =  $this->language->get('text_header_info');
         $this->data['order_now'] = $this->language->get('order_now');
        $this->data['style_div'] = "";

        $setting = $this->config->get("onestepcheckout");
        if (!empty($setting)){
        	if (!empty($setting[$this->config->get('config_language_id')]["header_title"])){
        		$this->data['text_express_checkout'] = trim($setting[$this->config->get('config_language_id')]["header_title"]);
        	}
        	if (!empty($setting[$this->config->get('config_language_id')]["header_info"])){
        		$this->data['text_header_info'] = trim($setting[$this->config->get('config_language_id')]["header_info"]);
        	}
        	if (!empty($setting[$this->config->get('config_language_id')]["background_color"])){
        		$this->data['style_div'] = "background-color: ".trim($setting[$this->config->get('config_language_id')]["background_color"]).";";
        	}
        }

        //**********************
        $this->data['entry_firstname'] = $this->language->get('entry_firstname');
        $this->data['entry_lastname'] = $this->language->get('entry_lastname');
        $this->data['entry_company'] = $this->language->get('entry_company');
        $this->data['entry_email_address'] = $this->language->get('entry_email_address');
        $this->data['entry_telephone'] = $this->language->get('entry_telephone');
        $this->data['entry_address_1'] = $this->language->get('entry_address_1');
        $this->data['entry_address_2'] = $this->language->get('entry_address_2');
        $this->data['entry_fax'] = $this->language->get('entry_fax');
        $this->data['entry_country'] = $this->language->get('entry_country');
        $this->data['entry_postcode'] = $this->language->get('entry_postcode');
        $this->data['entry_city'] = $this->language->get('entry_city');
        $this->data['entry_zone'] = $this->language->get('entry_zone');
        $this->data['entry_password'] = $this->language->get('entry_password');
        $this->data['entry_confirm'] = $this->language->get('entry_confirm');
        $this->data['entry_customer_group'] = $this->language->get('entry_customer_group');

        $this->data['entry_company_id'] = $this->language->get('entry_company_id');
        $this->data['entry_tax_id'] = $this->language->get('entry_tax_id');
        $this->data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));
        // login //
        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_password'] = $this->language->get('entry_password');
        $this->data['text_forgotten'] = $this->language->get('text_forgotten');
        $this->data['button_login'] = $this->language->get('button_login');

        $this->data['text_comments'] = $this->language->get('text_comments');

        $this->data['action'] = $this->url->link('account/forgotten', '', 'SSL');

        //**********************
        // forgotten
        $this->language->load('account/forgotten');
        $this->data['text_your_email'] = $this->language->get('text_your_email');
        $this->data['text_email'] = $this->language->get('text_email');

        $this->data['entry_email'] = $this->language->get('entry_email');

        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['button_back'] = $this->language->get('button_back');
        //**********************
        $this->language->load('checkout/checkout');

        $this->data['text_select'] = $this->language->get('text_select');
        $this->data['text_none'] = $this->language->get('text_none');


        //Agree text
        if ($this->config->get('config_checkout_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

            if ($information_info) {
                $this->data['checkout_text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
            } else {
                $this->data['checkout_text_agree'] = '';
            }
        } else {
            $this->data['checkout_text_agree'] = '';
        }

        if ($this->config->get('config_account_id')) {
        	$this->load->model('catalog/information');

        	$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

        	if ($information_info) {
        		$this->data['account_text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
        	} else {
        		$this->data['account_text_agree'] = '';
        	}
        } else {
        	$this->data['account_text_agree'] = '';
        }


        if (isset($this->session->data['checkout_agree'])) {
            $this->data['checkout_agree'] = $this->session->data['checkout_agree'];
        } else {
            $this->data['checkout_agree'] = '';
        }

        if (isset($this->session->data['account_agree'])) {
        	$this->data['account_agree'] = $this->session->data['account_agree'];
        } else {
        	$this->data['account_agree'] = '';
        }


        //Addresses
        $this->data['text_address_new'] = $this->language->get('text_address_new');

       
        $this->load->model('account/address');

        $acount_addresses = $this->model_account_address->getAddresses();
		$addresses = array();
		$form_dell = array();
		$form_bill = array();
		 $address_id = "";
               
                 foreach ($acount_addresses as $key => $address){
                     $address_id=$key;
                     break;
                 }
		 foreach ($acount_addresses as $key => $address){
			$name = $address['firstname'].", ".$address['lastname'].", ".$address['address_1'].", ".$address['city']." ".$address['zone']." ".$address['country'];
		 	$addresses[$key]['name']= $name;
		 	$form_dell[$key] = $name;
		 	$form_bill[$key] = $name;
		 }

 if (isset($this->session->data['shipping_address_id'])) {
            $this->data['address_id'] = $this->session->data['shipping_address_id'];
        } else {
            $this->data['address_id'] = $address_id;
        }
//         $shipping_addresses

      //  $this->data['addresses'] = $this->model_account_address->getAddresses();


        $this->data['shipping_addresses'] = array();
        $this->data['selected_shipping'] = "";
        $this->data['form_dell']="[]";
        if(isset($this->session->data['shipping_temp'])){

        				$this->data['shipping_addresses'] =$addresses + $this->session->data['shipping_temp'];

		            if(isset($this->session->data['shipping_temp']['active']))
		              $this->data['selected_shipping'] = $this->session->data['shipping_temp']['active'];
		    	         foreach ($this->session->data['shipping_temp'] as $key => $val){
				         	if (isset($val["name"])){
				         	$form_dell[$key]=$val["name"];
				         	}
		        	 }

          } else {
          	$this->data['shipping_addresses'] =$addresses;
          }
          $this->data['form_dell'] = json_encode($form_dell);

        $this->data['payment_addresses'] = array();
        $this->data['selected_payment'] = "";
        $this->data['form_bill'] = "[]";

        	if(isset($this->session->data['payment_temp'])){

        		  $this->data['payment_addresses'] =$addresses + $this->session->data['payment_temp'];

        		if(isset($this->session->data['payment_temp']['active']))
        		$this->data['selected_payment'] = $this->session->data['payment_temp']['active'];

        		foreach ($this->session->data['payment_temp'] as $key => $val){
        			if(isset($val['name'])){
        			$form_bill[$key]=$val['name'];
        			}
        		}

        	} else {
        		$this->data['payment_addresses'] =$addresses;
        	}
        	$this->data['form_bill'] = json_encode($form_bill);




        //var_dump($this->session->data);
        //  $this->load->model('account/customer');
            $this->data['address_1'] ="";
            $this->data['fax'] ="";

            $this->data['address_2'] ="";
            $this->data['country_id'] ="";
            $this->data['zone_id'] ="";
            $this->data['company'] ="";
            $this->data['company_id'] ="";
            $this->data['tax_id'] = "";
            $this->data['city'] ="";
            $this->data['postcode'] ="";


        if ($this->customer->isLogged()) {
        	$this->data['is_logged'] = true;
            $this->data['firstname'] = $this->customer->getFirstname();
            $this->data['lastname'] = $this->customer->getLastname();

            $this->data['email'] = $this->customer->getEmail();
            $this->data['telephone'] = $this->customer->getTelephone();
            $this->data['fax'] = $this->customer->getFax();
			$this->session->data['shipping_address_id'] = $address_id;
			$this->session->data['payment_address_id'] = $address_id;


            $this->load->model('account/address');

            $address_info = $this->model_account_address->getAddress($address_id);

            if ($address_info) {
             	if ($this->config->get('config_tax_customer') == 'shipping') {
             		$this->session->data['shipping_country_id'] = $address_info['country_id'];
             		$this->session->data['shipping_zone_id'] = $address_info['zone_id'];
             		$this->session->data['shipping_postcode'] = $address_info['postcode'];
             	}

             	if ($this->config->get('config_tax_customer') == 'payment') {
             		$this->session->data['payment_country_id'] = $address_info['country_id'];
             		$this->session->data['payment_zone_id'] = $address_info['zone_id'];
             	}
            $this->data['address_1'] =$address_info["address_1"];
            $this->data['address_2'] =$address_info["address_2"];
            $this->data['country_id'] =$address_info["country_id"];
            $this->data['zone_id'] =$address_info["zone_id"];

            $this->data['company'] =$address_info["company"];
            $this->data['company_id'] =$address_info["company_id"];
            $this->data['tax_id'] =$address_info["tax_id"];

            $this->data['city'] =$address_info["city"];
            $this->data['postcode'] =$address_info["postcode"];

            }
        } else {
        	$this->data['is_logged'] = false;
            if (isset($this->session->data['guest']['firstname'])) {
                $this->data['firstname'] = $this->session->data['guest']['firstname'];
            } else {
                $this->data['firstname'] = '';
            }

            if (isset($this->session->data['guest']['lastname'])) {
                $this->data['lastname'] = $this->session->data['guest']['lastname'];
            } else {
                $this->data['lastname'] = '';
            }

            if (isset($this->session->data['guest']['email'])) {
            	$this->data['email'] = $this->session->data['guest']['email'];
            } else {
            	$this->data['email'] = '';
            }

            if (isset($this->session->data['guest']['telephone'])) {
            	$this->data['telephone'] = $this->session->data['guest']['telephone'];
            } else {
            	$this->data['telephone'] = '';
            }

            if (isset($this->session->data['guest']['fax'])) {
            	$this->data['fax'] = $this->session->data['guest']['fax'];
            } else {
            	$this->data['fax'] = '';

            }

        }

        if (isset($this->session->data['comment'])) {
        	$this->data['comment'] = $this->session->data['comment'];
        } else {
        	$this->data['comment'] = '';
        }

        $this->load->model('localisation/country');
        $this->data['countries'] = $this->model_localisation_country->getCountries();

        $this->load->model('localisation/zone');
        $zone = $this->model_localisation_zone->getZonesByCountryId($this->data['country_id']);
        $this->data['zone_html'] = '';
        if (!empty($zone)){
        	//  var_dump($zone);
        	$html = "";
        	foreach($zone as $key => $val){
        		$html .= '<option value="' . $val['zone_id'] . '"';
        		if ($val['zone_id'] == $this->data['zone_id']) {
        			$html .= ' selected="selected"';
        		}
        		$html .= '>'. $val["name"].'</option>';
        	}
        } else {
        	$html = '<option value="0" selected="selected">'.$this->data["text_none"].'</option>';
        }
        $this->data['zone_html'] = $html;
        $this->data['logged'] = $this->customer->isLogged();
        $this->data['shipping_required'] = $this->cart->hasShipping();


    	if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
                        $this->language->load('checkout/cart');
			$points = $this->customer->getRewardPoints();

			$points_total = 0;

			foreach ($this->cart->getProducts() as $product) {
				if ($product['points']) {
					$points_total += $product['points'];
				}
			}
                        $this->data['text_use_coupon'] = $this->language->get('text_use_coupon');
                        $this->data['text_use_voucher'] = $this->language->get('text_use_voucher');
                        $this->data['text_use_reward'] = sprintf($this->language->get('text_use_reward'), $points);
                        $this->data['entry_coupon'] = $this->language->get('entry_coupon');
                        $this->data['entry_voucher'] = $this->language->get('entry_voucher');
                        $this->data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);
        }
        $this->load->model('account/customer_group');

        $this->data['customer_groups'] = array();

        if (is_array($this->config->get('config_customer_group_display'))) {
        	$customer_groups = $this->model_account_customer_group->getCustomerGroups();

        	foreach ($customer_groups as $customer_group) {
        		if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
        			$this->data['customer_groups'][] = $customer_group;
        		}
        	}
        }

        if (isset($this->session->data['guest']['customer_group_id'])) {
        	$this->data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
        } else {
        	$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
        }

        // Company ID
        if (isset($this->session->data['guest']['payment']['company_id'])) {
        	$this->data['company_id'] = $this->session->data['guest']['payment']['company_id'];
        } elseif (isset($this->session->data['payment_temp']["active"])&&isset($this->session->data['payment_temp'][$this->session->data['payment_temp']["active"]]["data"])){
        	$payment_address = $this->session->data['payment_temp'][$this->session->data['payment_temp']['active']]['data'];
        	if (isset($payment_address["country_id"])){
        		$this->data['country_id'] = $payment_address["country_id"];
        	}
        }
        // Tax ID
      
        if (isset($this->session->data['guest']['payment']['tax_id'])) {
        	$this->data['tax_id'] = $this->session->data['guest']['payment']['tax_id'];
        } elseif (isset($this->session->data['payment_temp']["active"])&&isset($this->session->data['payment_temp'][$this->session->data['payment_temp']["active"]]["data"])){
        	$payment_address = $this->session->data['payment_temp'][$this->session->data['payment_temp']['active']]['data'];
        	if (isset($payment_address["tax_id"])){
        		$this->data['tax_id'] = $payment_address["tax_id"];
        	}
        }



        if (isset($this->session->data['guest']['payment']['country_id'])) {
        	$this->data['country_id'] = $this->session->data['guest']['payment']['country_id'];
        } elseif (isset($this->session->data['shipping_country_id'])) {
        	$this->data['country_id'] = $this->session->data['shipping_country_id'];
        } else {
        	$this->data['country_id'] = $this->config->get('config_country_id');
        }



        if (isset($this->session->data['guest']['payment']['zone_id'])) {
        	$this->data['zone_id'] = $this->session->data['guest']['payment']['zone_id'];
        } elseif (isset($this->session->data['shipping_zone_id'])) {
        	$this->data['zone_id'] = $this->session->data['shipping_zone_id'];
        } else {
        	$this->data['zone_id'] = '';
        }
          $this->data['coupon_status'] = $this->config->get('coupon_status');

			if (isset($this->request->post['coupon'])) {
				$this->data['coupon'] = $this->request->post['coupon'];
			} elseif (isset($this->session->data['coupon'])) {
				$this->data['coupon'] = $this->session->data['coupon'];
			} else {
				$this->data['coupon'] = '';
			}

			$this->data['voucher_status'] = $this->config->get('voucher_status');

			if (isset($this->request->post['voucher'])) {
				$this->data['voucher'] = $this->request->post['voucher'];
			} elseif (isset($this->session->data['voucher'])) {
				$this->data['voucher'] = $this->session->data['voucher'];
			} else {
				$this->data['voucher'] = '';
			}

			$this->data['reward_status'] = ($points && $points_total && $this->config->get('reward_status'));

			if (isset($this->request->post['reward'])) {
				$this->data['reward'] = $this->request->post['reward'];
			} elseif (isset($this->session->data['reward'])) {
				$this->data['reward'] = $this->session->data['reward'];
			} else {
				$this->data['reward'] = '';
			}

        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onestepcheckout/checkout.tpl')) {
            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onestepcheckout/checkout.tpl';
        } else {
            $this->template = 'default/template/onestepcheckout/checkout.tpl';
        }

        $this->children = array(
            	'common/column_left',
		'common/column_right',
		'common/content_top',
		'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function country() {
        $json = array();

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $json = array(
                'country_id' => $country_info['country_id'],
                'name' => $country_info['name'],
                'iso_code_2' => $country_info['iso_code_2'],
                'iso_code_3' => $country_info['iso_code_3'],
                'address_format' => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone' => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status' => $country_info['status']
            );
        }

        $this->response->setOutput(json_encode($json));
    }

    public function get_methods() {
        $json = array();
//unset($this->session->data['shipping_method']);
//unset($this->session->data['payment_method']);

$this->load->model('localisation/country');
$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
$this->request->post['iso_code_2'] = $country_info['iso_code_2'];
$this->request->post['iso_code_3'] = $country_info['iso_code_3'];
$this->request->post['address_format'] = $country_info['address_format'];

           if (isset($this->request->post)) {
        	if (isset($this->request->post["address_delivery_id"])) {

        		if (isset($this->session->data['shipping_temp'][$this->request->post["address_delivery_id"]]["data"])){
        			$data = $this->session->data['shipping_temp'][$this->request->post["address_delivery_id"]]["data"];
                                if(isset($data['postcode']))  $this->session->data['shipping_postcode'] = $data['postcode'];
        			$this->session->data['shipping_temp']["active"] = $this->request->post["address_delivery_id"];
					unset($this->session->data['shipping_address_id']);
        		} else {
        			$this->load->model('account/address');
        			$address_info = $this->model_account_address->getAddress($this->request->post["address_delivery_id"]);

        			if ($address_info) {
        				$this->session->data['shipping_address_id'] = $this->request->post["address_delivery_id"];
                                        $this->session->data['shipping_postcode'] = $address_info['postcode'];
        			}

        		}

        	} else {
        		unset($this->session->data['shipping_temp']["active"]);
        		unset($this->session->data['shipping_address_id']);
				
        		$this->session->data['guest']['shipping'] = $this->request->post;
                        if(isset($this->request->post['postcode'])) $this->session->data['shipping_postcode'] = $this->request->post['postcode'];
        	}

        	if (isset($this->request->post["address_billing_id"])) {
        		if (isset($this->session->data['payment_temp'][$this->request->post["address_billing_id"]]["data"])){
        		   //  $data = $this->session->data['payment_temp'][$this->request->post["address_billing_id"]]["data"];
        		    $this->session->data['payment_temp']["active"]=$this->request->post["address_billing_id"];
        		    unset($this->session->data['payment_address_id']);
        		} else {

        			$this->load->model('account/address');
        			$address_info = $this->model_account_address->getAddress($this->request->post["address_billing_id"]);

        			if ($address_info) {
        				$this->session->data['payment_address_id'] = $this->request->post["address_billing_id"];
        			}

        		}
        		//$this->session->data['guest']['payment'] = $data;
        	}else {
        		unset($this->session->data['payment_address_id']);
        		unset($this->session->data['payment_temp']["active"]);
          		$this->session->data['guest']['payment'] = $this->request->post;
          	}

                if (isset($this->request->post['type'])&&$this->request->post['type']==2){

                    if(isset($this->session->data['shipping_methods'])){
                         $shipping = explode('.', $this->request->post['shipping_method']);
                          if(isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]))
                          $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                     }

                     unset($this->session->data['payment_method']);
                     $json['payment_method'] = $this->getChild("checkout/payment_method");
                     $json['confirm_order'] = $this->confirm();

                }elseif(isset($this->request->post['type'])&&$this->request->post['type']==1){

                 if(isset($this->session->data['shipping_methods'])){
                         $shipping = explode('.', $this->request->post['shipping_method']);
                          if(isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]))
                          $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                     }

                if(isset($this->session->data['payment_methods'])){
                        if(isset($this->session->data['payment_methods'][$this->request->post['payment_method']]))
                          $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
                     }

                    $json['confirm_order'] = $this->confirm();

                } else {
                	unset($this->session->data['payment_method']);
                	unset($this->session->data['shipping_method']);
                    $json['shipping_method'] = $this->getChild("checkout/shipping_method");
                    $json['payment_method'] = $this->getChild("checkout/payment_method");
                    $json['confirm_order'] = $this->confirm();
                }

        }

        $this->response->setOutput(json_encode($json));
    }


    private function confirm() {
        $redirect = '';

        if ($this->cart->hasShipping()) {
            // Validate if shipping address has been set.
            $this->load->model('account/address');

            if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
                $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
            } else {
		            if (isset($this->session->data['shipping_temp']["active"])&&isset($this->session->data['shipping_temp'][$this->session->data['shipping_temp']["active"]]["data"])){
			        $shipping_address = $this->session->data['shipping_temp'][$this->session->data['shipping_temp']['active']]['data'];
			        } elseif (isset($this->session->data['guest'])) {
		                $shipping_address = $this->session->data['guest']['shipping'];
		            }
            }
            if (empty($shipping_address)) {
               // $redirect = $this->url->link('checkout/checkout', '', 'SSL');
            }

            // Validate if shipping method has been set.
            if (!isset($this->session->data['shipping_method'])) {
               // $redirect = $this->url->link('checkout/checkout', '', 'SSL');
            }
        } else {
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
        }

        // Validate if payment address has been set.
        $this->load->model('account/address');

        if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } else {
	        if (isset($this->session->data['payment_temp']["active"])&&isset($this->session->data['payment_temp'][$this->session->data['payment_temp']["active"]]["data"])){
	        	$payment_address = $this->session->data['payment_temp'][$this->session->data['payment_temp']['active']]['data'];
	        } elseif (isset($this->session->data['guest'])) {
	            $payment_address = $this->session->data['guest']['payment'];
	        }
        }
       if (empty($payment_address)) {
           // $redirect = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate if payment method has been set.
        if (!isset($this->session->data['payment_method'])) {
           // $redirect = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $redirect = $this->url->link('checkout/cart');
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $redirect = $this->url->link('checkout/cart');

                break;
            }
        }

        if (!$redirect) {
            $total_data = array();
            $total = 0;
            $taxes = $this->cart->getTaxes();

            $this->load->model('setting/extension');

            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                }
            }

            $sort_order = array();

            foreach ($total_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $total_data);

            $this->language->load('checkout/cart');

            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_image'] = $this->language->get('column_image');
            $this->data['column_model'] = $this->language->get('column_model');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');

            $this->data['products'] = array();
            
            $this->load->model('tool/image');

            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();

                foreach ($product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['option_value'];
                    } else {
                        $filename = $this->encryption->decrypt($option['option_value']);

                        $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                    }

                    $option_data[] = array(
                        'name' => $option['name'],
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                    );
                }
                
                if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], 60, 60);
				} else {
					$image = '';
				}
               
                $this->data['products'][] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'thumb' => $image,
                    'quantity' => $product['quantity'],
                    'subtract' => $product['subtract'],
                    'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
                    'reward'   => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
                    'total' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']),
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
                );
            }

            // Gift Voucher
            $this->data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $this->data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'])
                    );
                }
            }

            $this->data['totals'] = $total_data;


        } else {
            $this->data['redirect'] = $redirect;
        }
         $dir = $this->language->get('direction');
	 if($dir=='rtl'){
	 $nameFile="/template/onestepcheckout/rtlconfirm.tpl";
	 } else{
	  $nameFile="/template/onestepcheckout/confirm.tpl";
	 } 

        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . $nameFile)) {
            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . $nameFile;
        } else {
            $this->template = 'default'.$nameFile;
        }

        return $this->render();
    }

    public function validate() {
        $this->language->load('onestepcheckout/checkout');

        $json = array();

        // Validate if customer is logged in.
//         if ($this->customer->isLogged()) {
//             $json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
//         }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
           
        }

        


     $this->language->load('checkout/checkout');  
        $data = array();
        $type = '';
        if (isset($this->request->post['shipping'])) {
            $data = $this->request->post['shipping'];
            $type = "shipping_temp" ;

        } elseif (isset($this->request->post['payment'])) {
            $data = $this->request->post['payment'];
            $type = "payment_temp" ;
        } else {
            $data = $this->request->post;
            $type = "personal_temp" ;
//             $this->session->data["guest"]= $data;
        }

        if (!$json) {
            if ((utf8_strlen($data['firstname']) < 1) || (utf8_strlen($data['firstname']) > 32)) {
                $json['error']['firstname'] = $this->language->get('error_firstname');
            }

            if ((utf8_strlen($data['lastname']) < 1) || (utf8_strlen($data['lastname']) > 32)) {
                $json['error']['lastname'] = $this->language->get('error_lastname');
            }

            if ((utf8_strlen($data['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $data['email'])) {
                $json['error']['email'] = $this->language->get('error_email');
            }

            if ((utf8_strlen($data['telephone']) < 3) || (utf8_strlen($data['telephone']) > 32)) {
                $json['error']['telephone'] = $this->language->get('error_telephone');
            }

            if ((!isset($data["fax"]))) {
            	$data["fax"] = "";
            }

            if (isset($data['use_ship'])&&$data['use_ship']==0){
		            if (($data['address_delivery_id'] == "---")) {
		            	$json['error']['address_delivery_id'] = "Please select address!";
		            }
            }
            if (isset($data['use_bill'])&&$data['use_bill']==0){
		            if (($data['address_billing_id'] == "---")) {
		            	$json['error']['address_billing_id'] = "Please select address!";
		            }
            }




            // Customer Group
            $this->load->model('account/customer_group');

            if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                $customer_group_id = $data['customer_group_id'];
            } else {
                $customer_group_id = $this->config->get('config_customer_group_id');
            }

            $customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
            $data["customer_group_id"]=$customer_group_id;
            if ($customer_group) {
                // Company ID
                if ($customer_group['company_id_display'] && $customer_group['company_id_required'] && empty($data['company_id'])&&(!(isset($this->request->post['payment'])||isset($this->request->post['shipping'])))) {
                    $json['error']['company_id'] = $this->language->get('error_company_id');
                } else {
                	if (empty($data["company_id"])) $data["company_id"]="";
                }

                // Tax ID
                if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && empty($data['tax_id'])&&(!(isset($this->request->post['payment'])||isset($this->request->post['shipping'])))) {
                    $json['error']['tax_id'] = $this->language->get('error_tax_id');
                }else {
                	if (empty($data["tax_id"])) $data["tax_id"]="";
                }
            }

            if ((utf8_strlen($data['address_1']) < 3) || (utf8_strlen($data['address_1']) > 128)) {
                $json['error']['address_1'] = $this->language->get('error_address_1');
            }

            if ((isset($data['city']))&&((utf8_strlen($data['city']) < 2) || (utf8_strlen($data['city']) > 128))) {
                $json['error']['city'] = $this->language->get('error_city');
            }


            if (isset($data['new_account'])&& ($data['new_account']==1)){

		            if ((utf8_strlen($data['password']) < 4) || (utf8_strlen($data['password']) > 20)) {
		            	$json['error']['password'] = $this->language->get('error_password');
		            }
		            if ($data['confirm'] != $data['password']) {
		            	$json['error']['confirm'] = $this->language->get('error_confirm');
		            }

		            $this->load->model('account/customer');

		            if ($this->model_account_customer->getTotalCustomersByEmail($data['email'])) {
		            	$json['error']['email'] = $this->language->get('error_exists');
		            }


		            if ($this->config->get('config_account_id')) {
		            	$this->load->model('catalog/information');

		            	$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

		            	if ($information_info && empty($data['account_agree'])) {
		            		$json['error']['account_agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
		            	}
		            }

             } else {
                 // Check if guest checkout is avaliable.
        if (!$this->config->get('config_guest_checkout') || $this->config->get('config_customer_price') ) {
//            $json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
//              $_SESSION['warning'] = $this->language->get('warrning_guest');
        }
             }

             if ($this->config->get('config_checkout_id')) {
             	$this->load->model('catalog/information');

             	$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

             	if ($information_info && (isset($data['checkout_agree']))&&($data['checkout_agree']==0)) {
             		$json['error']['checkout_agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
             	}
             }

             $this->load->model('localisation/country');

             $country_info = $this->model_localisation_country->getCountry($data['country_id']);

             if ($country_info) {
                if ($country_info['postcode_required'] && (utf8_strlen($data['postcode']) < 2) || (utf8_strlen($data['postcode']) > 10)) {
                    $json['error']['postcode'] = $this->language->get('error_postcode');
                }

                // VAT Validation
                $this->load->helper('vat');

                if ($this->config->get('config_vat') && $data['tax_id'] && (vat_validation($country_info['iso_code_2'], $data['tax_id']) == 'invalid')&&(!(isset($this->request->post['payment'])||isset($this->request->post['shipping'])))) {
                    $json['error']['tax_id'] = $this->language->get('error_vat');
                }

            }

            if ($data['country_id'] == '') {
                $json['error']['country_id'] = $this->language->get('error_country');
            }

            if (!isset($data['zone_id']) || $data['zone_id'] == '') {
                $json['error']['zone_id'] = $this->language->get('error_zone');
            }
         if ($country_info) {
         	$country = $country_info['name'];
         	$iso_code_2 = $country_info['iso_code_2'];
         	$iso_code_3 = $country_info['iso_code_3'];
         	$address_format = $country_info['address_format'];
         	$data["country"] = $country;
         	$data["address_format"] = $address_format;

         } else {
         	$country = '';
         	$iso_code_2 = '';
         	$iso_code_3 = '';
         	$address_format = '';
         	$data["country"] = $country;
         	$data["address_format"] = $address_format;
         }
         if (isset($data['zone_id'])){
		         $this->load->model('localisation/zone');
		        $zone_info = $this->model_localisation_zone->getZone($data['zone_id']);

		        if ($zone_info) {
		        	$zone = $zone_info['name'];
		        	$zone_code = $zone_info['code'];
		        	$data["zone"] = $zone;
		        } else {
		        	$zone = '';
		        	$zone_code = '';
		        	$data["zone"] = $zone;
		        }
         }
      }
        if (isset($data) && !$json) {
        	if ((isset($data["shipping_method"]))&&(isset($data["payment_method"]))&&($type == "personal_temp" )){
                    
        		if (isset($data['new_account'])&& ($data['new_account']==1)){
        			$this->load->model('account/customer');
        			$this->model_account_customer->addCustomer($data);

        			$this->customer->login($data['email'], $data['password']);

        		  if (isset($this->session->data['payment_temp']["active"])&&isset($this->session->data['payment_temp'][$this->session->data['payment_temp']["active"]]["data"])){
        		        $this->model_account_address->addAddress($this->session->data['payment_temp'][$this->session->data['payment_temp']['active']]['data']);
        		    }

        		  if (isset($this->session->data['shipping_temp']["active"])&&isset($this->session->data['shipping_temp'][$this->session->data['shipping_temp']["active"]]["data"])){
        		    	$this->model_account_address->addAddress($this->session->data['shipping_temp'][$this->session->data['shipping_temp']['active']]['data']);
        		    }

        		}
        		if (!empty($data["comment"])){
        			$this->session->data['comment'] = strip_tags($data["comment"]);
        		} else {
        			unset($this->session->data['comment']);
        		}

        		       $personal_data=$data;

//         		       if(isset($this->session->data['shipping_methods'])){
//         		       	$shipping = explode('.', $this->request->post['shipping_method']);
//         		       	if(isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]))
//         		       		$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
//         		       }

//         		       if(isset($this->session->data['payment_methods'])){
//         		       	if(isset($this->session->data['payment_methods'][$this->request->post['payment_method']]))
//         		       		$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
//         		       }

        		       $total_data = array();
        		       $total = 0;
        		       $taxes = $this->cart->getTaxes();

        		       $this->load->model('setting/extension');

        		       $sort_order = array();

        		       $results = $this->model_setting_extension->getExtensions('total');

        		       foreach ($results as $key => $value) {
        		       	$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        		       }

        		       array_multisort($sort_order, SORT_ASC, $results);

        		       foreach ($results as $result) {
        		       	if ($this->config->get($result['code'] . '_status')) {
        		       		$this->load->model('total/' . $result['code']);

        		       		$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
        		       	}
        		       }

        		       $sort_order = array();

        		       foreach ($total_data as $key => $value) {
        		       	$sort_order[$key] = $value['sort_order'];
        		       }

        		       array_multisort($sort_order, SORT_ASC, $total_data);


        		           $data = array();
        		           $data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
        		           $data['store_id'] = $this->config->get('config_store_id');
        		           $data['store_name'] = $this->config->get('config_name');

        		           if ($data['store_id']) {
        		               $data['store_url'] = $this->config->get('config_url');
        		           } else {
        		               $data['store_url'] = HTTP_SERVER;
        		           }

        		           if ($this->customer->isLogged()) {
        		               $data['customer_id'] = $this->customer->getId();
        		               $data['customer_group_id'] = $this->customer->getCustomerGroupId();
        		               $data['firstname'] = $this->customer->getFirstName();
        		               $data['lastname'] = $this->customer->getLastName();
        		               $data['email'] = $this->customer->getEmail();
        		               $data['telephone'] = $this->customer->getTelephone();
        		               $data['fax'] = $this->customer->getFax();


        		           }
        		              elseif (isset($personal_data)) {
        		                   		               $data['customer_id'] = 0;
        		                   		               $data['customer_group_id'] = $personal_data['customer_group_id'];
        		                   		               $data['firstname'] = $personal_data['firstname'];
        		                   		               $data['lastname'] = $personal_data['lastname'];
        		                   		               $data['email'] = $personal_data['email'];
        		                   		               $data['telephone'] = $personal_data['telephone'];
        		                   		               $data['fax'] = $personal_data['fax'];
        		                }
                        	if(isset($this->session->data['payment_address_id'])){
        		               $this->load->model('account/address');
        		               $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        		               } else {
        		               	 if (isset($this->session->data['payment_temp']["active"])&&isset($this->session->data['payment_temp'][$this->session->data['payment_temp']["active"]]["data"])){
        		               	  $payment_address = $this->session->data['payment_temp'][$this->session->data['payment_temp']['active']]['data'];
        		                } else {
        		               	$payment_address = $personal_data;
        		               	}
        		              }
        		            if(empty($payment_address['tax_id'])){
        		            	if(!empty($personal_data['tax_id'])){
        		            		$payment_address['tax_id'] = $personal_data['tax_id'];
        		            	}
        		            }
        		            if(empty($payment_address['company_id'])){
        		            	if(!empty($personal_data['company_id'])){
        		            		$payment_address['company_id'] = $personal_data['company_id'];
        		            	}
        		            }

        		           $data['payment_firstname'] = $payment_address['firstname'];
        		           $data['payment_lastname'] = $payment_address['lastname'];
        		           $data['payment_company'] = $payment_address['company'];
        		           $data['payment_company_id'] = $payment_address['company_id'];
        		           $data['payment_tax_id'] = $payment_address['tax_id'];
        		           $data['payment_address_1'] = $payment_address['address_1'];
                                   if(isset($payment_address['address_2']))
        		           $data['payment_address_2'] = $payment_address['address_2'];
        		           $data['payment_city'] = $payment_address['city'];
        		           $data['payment_postcode'] = $payment_address['postcode'];
        		           $data['payment_zone'] = $payment_address['zone'];
        		           $data['payment_zone_id'] = $payment_address['zone_id'];
        		           $data['payment_country'] = $payment_address['country'];
        		           $data['payment_country_id'] = $payment_address['country_id'];
        		           $data['payment_address_format'] = $payment_address['address_format'];

        		           if (isset($this->session->data['payment_method']['title'])) {
        		               $data['payment_method'] = $this->session->data['payment_method']['title'];
        		           } else {
        		               $data['payment_method'] = '';
        		           }

        		           if (isset($this->session->data['payment_method']['code'])) {
        		               $data['payment_code'] = $this->session->data['payment_method']['code'];
        		           } else {
        		               $data['payment_code'] = '';
        		           }

        		           if ($this->cart->hasShipping()) {
//         		               if ($this->customer->isLogged()) {
//         		                   $this->load->model('account/address');

//         		                   $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
//         		               } elseif (isset($this->session->data['guest'])) {
//         		                   $shipping_address = $this->session->data['guest']['shipping'];
//         		               }
        		               if(isset($this->session->data['shipping_address_id'])){
	        		               $this->load->model('account/address');
	        		               $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
	        		               } else {
	        		               	if (isset($this->session->data['shipping_temp']["active"])&&isset($this->session->data['shipping_temp'][$this->session->data['shipping_temp']["active"]]["data"])){
	        		               		$shipping_address = $this->session->data['shipping_temp'][$this->session->data['shipping_temp']['active']]['data'];
	        		               	} else {
	        		               		$shipping_address = $personal_data;
	        		               	}

        		               }



        		               $data['shipping_firstname'] = $shipping_address['firstname'];
        		               $data['shipping_lastname'] = $shipping_address['lastname'];
        		               $data['shipping_company'] = $shipping_address['company'];
        		               $data['shipping_address_1'] = $shipping_address['address_1'];
        		               $data['shipping_address_2'] = $shipping_address['address_2'];
        		               $data['shipping_city'] = $shipping_address['city'];
        		               $data['shipping_postcode'] = $shipping_address['postcode'];
        		               $data['shipping_zone'] = $shipping_address['zone'];
        		               $data['shipping_zone_id'] = $shipping_address['zone_id'];
        		               $data['shipping_country'] = $shipping_address['country'];
        		               $data['shipping_country_id'] = $shipping_address['country_id'];
        		               $data['shipping_address_format'] = $shipping_address['address_format'];

        		               if (isset($this->session->data['shipping_method']['title'])) {
        		                   $data['shipping_method'] = $this->session->data['shipping_method']['title'];
        		               } else {
        		                   $data['shipping_method'] = '';
        		               }

        		               if (isset($this->session->data['shipping_method']['code'])) {
        		                   $data['shipping_code'] = $this->session->data['shipping_method']['code'];
        		               } else {
        		                   $data['shipping_code'] = '';
        		               }
        		           } else {
        		               $data['shipping_firstname'] = '';
        		               $data['shipping_lastname'] = '';
        		               $data['shipping_company'] = '';
        		               $data['shipping_address_1'] = '';
        		               $data['shipping_address_2'] = '';
        		               $data['shipping_city'] = '';
        		               $data['shipping_postcode'] = '';
        		               $data['shipping_zone'] = '';
        		               $data['shipping_zone_id'] = '';
        		               $data['shipping_country'] = '';
        		               $data['shipping_country_id'] = '';
        		               $data['shipping_address_format'] = '';
        		               $data['shipping_method'] = '';
        		               $data['shipping_code'] = '';
        		           }

        		           $product_data = array();

        		           foreach ($this->cart->getProducts() as $product) {
        		               $option_data = array();

        		               foreach ($product['option'] as $option) {
        		                   if ($option['type'] != 'file') {
        		                       $value = $option['option_value'];
        		                   } else {
        		                       $value = $this->encryption->decrypt($option['option_value']);
        		                   }

        		                   $option_data[] = array(
        		                       'product_option_id' => $option['product_option_id'],
        		                       'product_option_value_id' => $option['product_option_value_id'],
        		                       'option_id' => $option['option_id'],
        		                       'option_value_id' => $option['option_value_id'],
        		                       'name' => $option['name'],
        		                       'value' => $value,
        		                       'type' => $option['type']
        		                   );
        		               }

        		               $product_data[] = array(
        		                   'product_id' => $product['product_id'],
        		                   'name' => $product['name'],
        		                   'model' => $product['model'],
        		                   'option' => $option_data,
        		                   'download' => $product['download'],
        		                   'quantity' => $product['quantity'],
        		                   'subtract' => $product['subtract'],
        		                   'price' => $product['price'],
        		                   'total' => $product['total'],
        		                   'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
        		                   'reward' => $product['reward']
        		               );
        		           }

        		           // Gift Voucher
        		           $voucher_data = array();

        		           if (!empty($this->session->data['vouchers'])) {
        		               foreach ($this->session->data['vouchers'] as $voucher) {
        		                   $voucher_data[] = array(
        		                       'description' => $voucher['description'],
        		                       'code' => substr(md5(mt_rand()), 0, 10),
        		                       'to_name' => $voucher['to_name'],
        		                       'to_email' => $voucher['to_email'],
        		                       'from_name' => $voucher['from_name'],
        		                       'from_email' => $voucher['from_email'],
        		                       'voucher_theme_id' => $voucher['voucher_theme_id'],
        		                       'message' => $voucher['message'],
        		                       'amount' => $voucher['amount']
        		                   );
        		               }
        		           }

        		           $data['products'] = $product_data;
        		           $data['vouchers'] = $voucher_data;
        		           $data['totals'] = $total_data;
        		           if(isset($this->session->data['comment'])){
        		           $data['comment'] = $this->session->data['comment'];
        		           } else {
        		           	$data['comment'] = "";
        		           }
        		           $data['total'] = $total;

        		           if (isset($this->request->cookie['tracking'])) {
        		               $this->load->model('affiliate/affiliate');

        		               $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);
        		               $subtotal = $this->cart->getSubTotal();

        		               if ($affiliate_info) {
        		                   $data['affiliate_id'] = $affiliate_info['affiliate_id'];
        		                   $data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
        		               } else {
        		                   $data['affiliate_id'] = 0;
        		                   $data['commission'] = 0;
        		               }
        		           } else {
        		               $data['affiliate_id'] = 0;
        		               $data['commission'] = 0;
        		           }

        		           $data['language_id'] = $this->config->get('config_language_id');
        		           $data['currency_id'] = $this->currency->getId();
        		           $data['currency_code'] = $this->currency->getCode();
        		           $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
        		           $data['ip'] = $this->request->server['REMOTE_ADDR'];

        		           if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
        		               $data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
        		           } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
        		               $data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
        		           } else {
        		               $data['forwarded_ip'] = '';
        		           }

        		           if (isset($this->request->server['HTTP_USER_AGENT'])) {
        		               $data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
        		           } else {
        		               $data['user_agent'] = '';
        		           }

        		           if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
        		               $data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
        		           } else {
        		               $data['accept_language'] = '';
        		           }

        		           $this->load->model('checkout/order');

        		           $this->session->data['order_id'] = $this->model_checkout_order->addOrder($data);
        		           if(isset($this->session->data['order_id'])){
        		           	$payment_html = $this->getChild('payment/' . $this->session->data['payment_method']['code']);
        		           	$json = array('status'=>"success",
        		           				 "payment_html" =>$payment_html);

        		           }

        	}else {
        	if ($type!="personal_temp"){
            $name = $data['firstname']." ".$data['lastname']." ".$data['address_1']." ".$data['city']." ".$zone." ".$country;
             if (isset($this->session->data[$type])){
            	$key = count($this->session->data[$type]);
            } else {$key=0;}
            $json['post']['key'] = $type."_".$key;
            $this->session->data[$type][$type."_".$key]["data"]=$data;
            $this->session->data[$type][$type."_".$key]["name"]=$name;
            $this->session->data[$type]["active"]=$type."_".$key;

            $json['post']=$this->session->data[$type];
            } else {
         	$this->session->data[$type]=$data;
          }
        }
      }
       $this->response->setOutput(json_encode($json));
    }
     public function validatefield(){
         $this->language->load('checkout/checkout');

        $json=array();
          // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Check if guest checkout is avaliable.
        if (!$this->config->get('config_guest_checkout') || $this->config->get('config_customer_price') || $this->cart->hasDownload()) {
            //$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
        }

        $data = array();
        $type = '';
        if (isset($this->request->post['shipping'])) {
            $data = $this->request->post['shipping'];
            $type = "shipping_temp" ;

        } elseif (isset($this->request->post['payment'])) {
            $data = $this->request->post['payment'];
            $type = "payment_temp" ;
        } else {
            $data = $this->request->post;
            $type = "personal_temp" ;
//             $this->session->data["guest"]= $data;
        }

        if (!$json) {
            if ((isset($data['firstname']))&&((utf8_strlen($data['firstname']) < 1) || (utf8_strlen($data['firstname']) > 32))) {
                $json['error']['firstname'] = $this->language->get('error_firstname');
            }

            if ((isset($data['lastname']))&&((utf8_strlen($data['lastname']) < 1) || (utf8_strlen($data['lastname']) > 32))) {
                $json['error']['lastname'] = $this->language->get('error_lastname');
            }

            if ((isset($data['email']))&&((utf8_strlen($data['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $data['email']))) {
                $json['error']['email'] = $this->language->get('error_email');
            }

            if ((isset($data['telephone']))&&((utf8_strlen($data['telephone']) < 3) || (utf8_strlen($data['telephone']) > 32))) {
                $json['error']['telephone'] = $this->language->get('error_telephone');
            }

           
            if (isset($data['use_ship'])&&$data['use_ship']==0){
		            if (($data['address_delivery_id'] == "---")) {
		            	$json['error']['address_delivery_id'] = "Please select address!";
		            }
            }
            if (isset($data['use_bill'])&&$data['use_bill']==0){
		            if (($data['address_billing_id'] == "---")) {
		            	$json['error']['address_billing_id'] = "Please select address!";
		            }
            }

            // Customer Group
            $this->load->model('account/customer_group');

            if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                $customer_group_id = $data['customer_group_id'];
            } else {
                $customer_group_id = $this->config->get('config_customer_group_id');
            }

            $customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
            $data["customer_group_id"]=$customer_group_id;
            if ($customer_group) {
                // Company ID
                if ($customer_group['company_id_display'] && $customer_group['company_id_required'] &&isset($data['company_id'])&& empty($data['company_id'])&&(!(isset($this->request->post['payment'])||isset($this->request->post['shipping'])))) {
                    $json['error']['company_id'] = $this->language->get('error_company_id');
                } 

                // Tax ID
                if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && isset($data['tax_id']) && empty($data['tax_id'])&&(!(isset($this->request->post['payment'])||isset($this->request->post['shipping'])))) {
                    $json['error']['tax_id'] = $this->language->get('error_tax_id');
                }
            }

            if ((isset($data['address_1']))&&(utf8_strlen($data['address_1']) < 3) || (utf8_strlen($data['address_1']) > 128)) {
                $json['error']['address_1'] = $this->language->get('error_address_1');
            }

            if ((isset($data['city']))&&((utf8_strlen($data['city']) < 2) || (utf8_strlen($data['city']) > 128))) {
                $json['error']['city'] = $this->language->get('error_city');
            }


            if (isset($data['new_account'])&& ($data['new_account']==1)){

		            if ((utf8_strlen($data['password']) < 4) || (utf8_strlen($data['password']) > 20)) {
		            	$json['error']['password'] = $this->language->get('error_password');
		            }
		            if ($data['confirm'] != $data['password']) {
		            	$json['error']['confirm'] = $this->language->get('error_confirm');
		            }

		            $this->load->model('account/customer');

		            if ($this->model_account_customer->getTotalCustomersByEmail($data['email'])) {
		            	$json['error']['email'] = $this->language->get('error_exists');
		            }


		            if ($this->config->get('config_account_id')) {
		            	$this->load->model('catalog/information');

		            	$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

		            	if ($information_info && empty($data['account_agree'])) {
		            		$json['error']['account_agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
		            	}
		            }

             }

             if ($this->config->get('config_checkout_id')) {
             	$this->load->model('catalog/information');

             	$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

             	if ($information_info && (isset($data['checkout_agree']))&&($data['checkout_agree']==0)) {
             		$json['error']['checkout_agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
             	}
             }

             $this->load->model('localisation/country');

             $country_info = $this->model_localisation_country->getCountry($data['country_id']);

             if ($country_info) {
                if ($country_info['postcode_required'] && (utf8_strlen($data['postcode']) < 2) || (utf8_strlen($data['postcode']) > 10)) {
                    $json['error']['postcode'] = $this->language->get('error_postcode'); 
                }

                // VAT Validation
                $this->load->helper('vat');

                if ($this->config->get('config_vat') && $data['tax_id'] && (vat_validation($country_info['iso_code_2'], $data['tax_id']) == 'invalid')&&(!(isset($this->request->post['payment'])||isset($this->request->post['shipping'])))) {
                    $json['error']['tax_id'] = $this->language->get('error_vat');
                }

            }

            if ($data['country_id'] == '') {
                $json['error']['country_id'] = $this->language->get('error_country');
            }

            if (!isset($data['zone_id']) || $data['zone_id'] == '') {
                $json['error']['zone_id'] = $this->language->get('error_zone');
            }


      }
      $this->response->setOutput(json_encode($json));
    }

    public function validateCoupon() {
    	$this->load->model('checkout/coupon');
        $this->language->load('checkout/cart');

    	$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);
    	$json = array();
    	if (!$coupon_info) {
    		$json['error']= $this->language->get('error_coupon');
                unset($this->session->data['coupon']);
    	} else {
    			$this->session->data['coupon'] = $this->request->post['coupon'];
    			$json["success"] = $this->language->get('text_coupon');
       	}
       	$this->response->setOutput(json_encode($json));
     }

     public function validateVoucher() {
    	$this->load->model('checkout/voucher');

        $this->language->load('checkout/cart');
    	$json = array();
    	$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);

    	if (!$voucher_info) {
    		$json['error'] = $this->language->get('error_voucher');
                unset($this->session->data['voucher']);
    	} else {
    			$this->session->data['voucher'] = $this->request->post['voucher'];

    			$json["success"] =  $this->language->get('text_voucher');

    	}
    	$this->response->setOutput(json_encode($json));
    }

    public function validateReward() {
    	$points = $this->customer->getRewardPoints();
         $this->language->load('checkout/cart');
    	$json = array();
    	$points_total = 0;

    	foreach ($this->cart->getProducts() as $product) {
    		if ($product['points']) {
    			$points_total += $product['points'];
    		}
    	}

    	if (empty($this->request->post['reward'])) {
    		$json['error'] = $this->language->get('error_reward');
    	}

    	if ($this->request->post['reward'] > $points) {
    		$json['error'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
    	}

    	if ($this->request->post['reward'] > $points_total) {
    		$json['error'] = sprintf($this->language->get('error_maximum'), $points_total);
    	}

    	if (!$json){
    		$this->session->data['reward'] = abs($this->request->post['reward']);

    		$json["success"] = $this->language->get('text_reward');

    	}
        else {
                unset($this->session->data['reward']);
        }

    	 	$this->response->setOutput(json_encode($json));
    }

}

?>