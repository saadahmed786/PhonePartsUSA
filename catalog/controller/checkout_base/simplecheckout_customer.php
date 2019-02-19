<?php 
/*
@author	Dmitriy Kubarev
@link	http://www.simpleopencart.com
@link	http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/  

class ControllerCheckoutSimpleCheckoutCustomer extends Controller {
    private $error = array();
    private $language_code = '';
    
    public function index() {
        $this->language->load('checkout/simplecheckout');
        
        $this->load->model('account/address');
        $this->load->model('account/customer');
            
        $json = array();
        
        $this->data['simple_customer_action_register'] = $this->config->get('simple_customer_action_register');
        $this->data['simple_customer_action_subscribe'] = $this->config->get('simple_customer_action_subscribe');
        $this->data['simple_customer_view_email'] = $this->config->get('simple_customer_view_email');
        $this->data['simple_customer_view_password_confirm'] = $this->config->get('simple_customer_view_password_confirm');
        $this->data['simple_customer_view_customer_type'] = $this->config->get('simple_customer_view_customer_type');
        $this->data['simple_customer_generate_password'] = $this->config->get('simple_customer_generate_password');
        $this->data['simple_payment_view_address_show'] = $this->config->get('simple_payment_view_address_show');
        
        $register_customer = $this->customer->isLogged() ? 0 : (isset($this->request->post['register']) ? $this->request->post['register'] : ($this->data['simple_customer_action_register'] == 1));
        
        $this->data['error_password'] = '';
        $this->data['error_password_confirm'] = '';
        
        if (isset($this->request->get['address_id'])) {
            $this->data['address_id'] = $this->request->get['address_id'];
        } elseif (isset($this->request->post['address_id'])) {
            $this->data['address_id'] = $this->request->post['address_id'];
        } elseif (isset($this->session->data['guest']['shipping']['address_id'])) {
            $this->data['address_id'] = $this->session->data['guest']['shipping']['address_id'];
        } else {
            $this->data['address_id'] = $this->customer->isLogged() ? $this->customer->getAddressId() : 0;
        }
        
        if (isset($this->request->get['payment_address_id'])) {
            $this->data['payment_address_id'] = $this->request->get['payment_address_id'];
        } elseif (isset($this->request->post['payment_address_id'])) {
            $this->data['payment_address_id'] = $this->request->post['payment_address_id'];
        } elseif (isset($this->session->data['guest']['payment']['address_id'])) {
            $this->data['payment_address_id'] = $this->session->data['guest']['payment']['address_id'];
        } else {
            $this->data['payment_address_id'] = $this->customer->isLogged() ? $this->customer->getAddressId() : 0;
        }
        
        if (isset($this->request->post['payment_address_same'])) {
      		$this->data['payment_address_same'] = true;
		} elseif (isset($this->session->data['guest']['simple']['payment_address_same'])) {
            $this->data['payment_address_same'] = $this->session->data['guest']['simple']['payment_address_same'];
        } elseif ($this->data['address_id'] != $this->data['payment_address_id']) {
            $this->data['payment_address_same'] = false;
        } else {
			$this->data['payment_address_same'] = true;
		}
        
        if (!$this->cart->hasShipping()) {
            $this->data['simple_payment_view_address_show'] = false;
            $this->data['payment_address_same'] = true;
        }
        
        if ($this->request->server['REQUEST_METHOD'] == 'GET' && $this->customer->isLogged()) {
            if ((!isset($this->session->data['guest']['shipping']['address_id']) || (isset($this->session->data['guest']['shipping']['address_id']) && $this->session->data['guest']['shipping']['address_id'] != $this->data['address_id'])) || 
                (!isset($this->session->data['guest']['payment']['address_id']) || (isset($this->session->data['guest']['payment']['address_id']) && $this->session->data['guest']['payment']['address_id'] != $this->data['payment_address_id']))) {
                
                $this->session->data['guest']['customer_id'] = $this->customer->getId();
    			$this->session->data['guest']['customer_group_id'] = $this->customer->getCustomerGroupId();
                $this->session->data['guest']['email'] = $this->customer->getEmail();
    			$this->session->data['guest']['firstname'] = $this->customer->getFirstName();
    			$this->session->data['guest']['lastname'] = $this->customer->getLastName();
    			$this->session->data['guest']['telephone'] = $this->customer->getTelephone();
                $this->session->data['guest']['fax'] = $this->customer->getFax();
    		
    			$this->load->model('account/address');
                unset($this->session->data['guest']['simple']);
                unset($this->session->data['guest']['shipping']);
                unset($this->session->data['guest']['payment']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['guest']['display_customer_fields']);
                unset($this->session->data['guest']['display_payment_address_fields']);
                
                if ($this->data['address_id']) {
        			$address = $this->model_account_address->getAddress($this->data['address_id']);
                    if ($address) {
                        $address['address_id'] = $this->data['address_id'];
                    }
                    $this->session->data['guest']['shipping'] = $address;
                }
                
                if ($this->data['payment_address_id']) {
        			$address = $this->model_account_address->getAddress($this->data['payment_address_id']);
                    if ($address) {
                        $address['address_id'] = $this->data['payment_address_id'];
                    }
                    $this->session->data['guest']['payment'] = $address;
                }
                
                if (isset($this->session->data['guest']['shipping']) && empty($this->session->data['guest']['payment']) && !$this->data['simple_payment_view_address_show']) {
                    $this->session->data['guest']['payment'] = $this->session->data['guest']['shipping'];
                }
                
                if (file_exists(DIR_APPLICATION . 'model/tool/simpledata.php')) {
    				$this->load->model('tool/simpledata');
                    if (method_exists($this->model_tool_simpledata,'loadCustomerData')) {
                        $this->session->data['guest']['simpledata_customer'] = $this->model_tool_simpledata->loadCustomerData($this->customer->getId());
                    }
                    if ($this->data['address_id'] && method_exists($this->model_tool_simpledata,'loadAddressData')) {
                        $this->session->data['guest']['simpledata_address'] = $this->model_tool_simpledata->loadAddressData($this->data['address_id']);
                    }
    			}
                
            } 
        }
        
        $this->load_fields();
        
        if ($this->request->server['REQUEST_METHOD'] == 'GET' && !$this->customer->isLogged()) {
            $address = $this->prepare_address($this->get_value('zone_id'), $this->get_value('country_id'));
            
            $address['address_id'] = 0;
            $address['firstname'] = $this->get_value('firstname');
            $address['lastname'] = $this->get_value('lastname');
            $address['company'] = $this->get_value('company');
            $address['company_id'] = $this->get_value('company_id');
            $address['tax_id'] = $this->get_value('tax_id');
            $address['address_1'] = $this->get_value('address_1');
            $address['address_2'] = $this->get_value('address_2');
            $address['postcode'] = $this->get_value('postcode');
            $address['city'] = $this->get_value('city');
            
            $payment_address = $address;
        
            if ($this->data['simple_payment_view_address_show'] && !$this->data['payment_address_same']) {
                $payment_address = $this->prepare_address($this->get_value('payment_zone_id'), $this->get_value('payment_country_id'));
                
                $payment_address['address_id'] = isset($this->request->post['payment_address_id']) ? intval($this->request->post['payment_address_id']) : 0;
                $payment_address['firstname'] = $this->get_value('payment_firstname');
                $payment_address['lastname'] = $this->get_value('payment_lastname');
                $payment_address['company'] = $this->get_value('payment_company');
                $payment_address['company_id'] = $this->get_value('payment_company_id');
                $payment_address['tax_id'] = $this->get_value('payment_tax_id');
                $payment_address['address_1'] = $this->get_value('payment_address_1');
                $payment_address['address_2'] = $this->get_value('payment_address_2');
                $payment_address['postcode'] = $this->get_value('payment_postcode');
                $payment_address['city'] = $this->get_value('payment_city');
            }
                    
            $this->session->data['guest']['shipping'] = $address;
            $this->session->data['guest']['payment'] = $payment_address;
        }
        
        $this->data['error_warning'] = '';
          
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
        
            $this->session->data['comment'] = $this->get_value('comment', true);
            
            $address = $this->prepare_address($this->get_value('zone_id'), $this->get_value('country_id'));
            
            $address['address_id'] = $this->data['address_id'];
            $address['firstname'] = $this->get_value('firstname', true);
            $address['lastname'] = $this->get_value('lastname', true);
            $address['company'] = $this->get_value('company', true);
            $address['company_id'] = $this->get_value('company_id', true);
            $address['tax_id'] = $this->get_value('tax_id', true);
            $address['address_1'] = $this->get_value('address_1', true);
            $address['address_2'] = $this->get_value('address_2', true);
            $address['postcode'] = $this->get_value('postcode', true);
            $address['city'] = $this->get_value('city', true);
            
            $payment_address = $address;
            
            if ($this->data['simple_payment_view_address_show'] && !$this->data['payment_address_same']) {
                $payment_address = $this->prepare_address($this->get_value('payment_zone_id'), $this->get_value('payment_country_id'));
                
                $payment_address['address_id'] = $this->data['payment_address_id'];;
                $payment_address['firstname'] = $this->get_value('payment_firstname', true);
                $payment_address['lastname'] = $this->get_value('payment_lastname', true);
                $payment_address['company'] = $this->get_value('payment_company', true);
                $payment_address['company_id'] = $this->get_value('payment_company_id', true);
                $payment_address['tax_id'] = $this->get_value('payment_tax_id', true);
                $payment_address['address_1'] = $this->get_value('payment_address_1', true);
                $payment_address['address_2'] = $this->get_value('payment_address_2', true);
                $payment_address['postcode'] = $this->get_value('payment_postcode', true);
                $payment_address['city'] = $this->get_value('payment_city', true);
            }
            
            $email = $this->customer->isLogged() && $this->customer->getEmail() != '' ? $this->customer->getEmail() : $this->get_value('email', true);
            $email = $email != '' ? $email : 'empty@localhost';
                
            $user_telephone = $this->get_value('telephone', true);
            $user_fax = $this->get_value('fax', true);
            
            $user = array(
                'customer_id' => $this->customer->isLogged() && $this->customer->getId() ? $this->customer->getId() : 0,
                'customer_group_id' => $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->get_value('customer_group_id'),
                'email' => $email,
                'password' => isset($this->request->post['password']) ? trim($this->request->post['password']) : '',
                'firstname' => $this->customer->isLogged() ? $this->customer->getFirstName() : $this->get_value('firstname', true),
                'lastname' => $this->customer->isLogged() ? $this->customer->getLastName() : $this->get_value('lastname', true),
                'telephone' => $this->customer->isLogged() ? (!empty($user_telephone) && $this->customer->getTelephone() != $user_telephone ? $user_telephone : $this->customer->getTelephone()) : $user_telephone,
                'fax' => $this->customer->isLogged() ? (!empty($user_fax) && $this->customer->getFax() != $user_fax ? $user_fax : $this->customer->getFax()) : $user_fax,
                'newsletter' => isset($this->request->post['subscribe']) ? $this->request->post['subscribe'] : ($this->data['simple_customer_action_subscribe'] == 1)
            );
          
            $simple = !empty($this->session->data['guest']['simple']) ? $this->session->data['guest']['simple'] : array();
            $this->session->data['guest'] = $user;
            $this->session->data['guest']['shipping'] = $address;
            $this->session->data['guest']['payment'] = $payment_address;
            $this->session->data['guest']['simple'] = $simple;
            if (isset($this->session->data['guest']['simple']['main_email'])) {
                $this->session->data['guest']['simple']['main_email'] = $this->session->data['guest']['simple']['main_email'] == 'empty@localhost' ? '' : $this->session->data['guest']['simple']['main_email'];
            }
            
            $customer_id = 0;
                        
            if ($this->customer->isLogged()) {   
                $customer_id = $this->customer->getId();
                
                if (empty($address['address_id'])) {
                    $this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($address);
                } else {
                    $this->model_account_address->editAddress($address['address_id'], $address);
                    $this->session->data['shipping_address_id'] = $address['address_id'];
                }
                
                if ($this->data['simple_payment_view_address_show'] && !$this->data['payment_address_same']) {
                    if (empty($payment_address['address_id'])) {
                        $this->session->data['payment_address_id'] = $this->model_account_address->addAddress($payment_address);
                    } else {
                        $this->model_account_address->editAddress($payment_address['address_id'], $payment_address);
                        $this->session->data['payment_address_id'] = $payment_address['address_id'];
                    }
                }
                
                if (!$this->data['simple_payment_view_address_show']) {
                    $this->session->data['payment_address_id'] = $this->session->data['shipping_address_id'];
                }
            } elseif ($register_customer && !empty($user['email']) && $user['email'] != 'empty@localhost' && !empty($user['password'])) {
            
                $data = array(
                    'firstname' => $this->get_value('firstname', true),
                    'lastname' => $this->get_value('lastname', true),
                    'email' => $this->get_value('email'),
                    'password' => trim($this->request->post['password']),
                    'telephone' => $this->get_value('telephone', true),
                    'fax' => $this->get_value('fax', true),
                    'newsletter' => isset($this->request->post['subscribe']) ? $this->request->post['subscribe'] : ($this->data['simple_customer_action_subscribe'] == 1),
                    'company' => $this->get_value('company', true),
                    'company_id' => $this->get_value('company_id', true),
                    'tax_id' => $this->get_value('tax_id', true),
                    'address_1' => $this->get_value('address_1', true),
                    'address_2' => $this->get_value('address_2', true),
                    'postcode' => $this->get_value('postcode', true),
                    'city' => $this->get_value('city', true),
                    'country_id' => $this->get_value('country_id'),
                    'zone_id' => $this->get_value('zone_id'),
                    'customer_group_id' => $this->get_value('customer_group_id')
                );
    			
    			$this->model_account_customer->addCustomer($data);
                $this->customer->login($data['email'], $data['password']);
                
                $customer_id = $this->customer->getId();
                
                if (file_exists(DIR_APPLICATION . 'model/tool/simpledata.php')) {
    				$this->load->model('tool/simpledata');
                    if (method_exists($this->model_tool_simpledata,'saveRegistrationData')) {
                        $this->model_tool_simpledata->saveRegistrationData($customer_id, $this->request->post);
                    }
    			}
                
                $this->session->data['guest']['customer_id'] = $customer_id;
            }
            
            $order_id = $this->order();
            
            if (file_exists(DIR_APPLICATION . 'model/tool/simpledata.php')) {
				$this->load->model('tool/simpledata');
                if (method_exists($this->model_tool_simpledata,'saveCheckoutData')) {
                    $this->model_tool_simpledata->saveCheckoutData($customer_id, $order_id, $this->request->post);
                }
			}
           
            $json['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);
        } 
        
        $this->data['text_checkout_customer'] = $this->language->get('text_checkout_customer');
        $this->data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
        $this->data['text_checkout_customer_login'] = $this->language->get('text_checkout_customer_login');
        $this->data['text_checkout_customer_cancel'] = $this->language->get('text_checkout_customer_cancel');
        $this->data['text_private'] = $this->language->get('text_private');
        $this->data['text_company'] = $this->language->get('text_company');
        $this->data['text_select'] = $this->language->get('text_select');
        $this->data['text_add_new'] = $this->language->get('text_add_new');
        $this->data['text_your_company'] = $this->language->get('text_your_company');
        $this->data['text_select_address'] = $this->language->get('text_select_address');
        $this->data['entry_register'] = $this->language->get('entry_register');
        $this->data['entry_newsletter'] = $this->language->get('entry_newsletter');
        $this->data['entry_password'] = $this->language->get('entry_password');
        $this->data['entry_password_confirm'] = $this->language->get('entry_password_confirm');
        $this->data['entry_payment_address'] = $this->language->get('entry_payment_address');
            
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');

        $this->data['button_login'] = $this->language->get('button_login');

        $this->data['text_forgotten'] = $this->language->get('text_forgotten');
        
        $this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
        
        $this->data['action_login'] = false;
        
        $this->data['simple_customer_view_login'] = $this->config->get('simple_customer_view_login');
        $this->data['customer_logged'] = $this->customer->isLogged();
        
        if ($this->customer->isLogged()) {
            $this->data['simple_customer_view_login'] = 0; 
        }
        
        if (isset($this->request->post['customer_type'])) {
            $this->data['customer_type'] = $this->request->post['customer_type'];
        } elseif (isset($this->session->data['guest']['simple']['customer_type'])) {
            $this->data['customer_type'] = $this->session->data['guest']['simple']['customer_type'];
        } else {
            $this->data['customer_type'] = 'private';
        }
        
        if (isset($this->request->post['register'])) {
            $this->data['register'] = $this->request->post['register'];
        } elseif (isset($this->session->data['guest']['simple']['register'])) {
            $this->data['register'] = $this->session->data['guest']['simple']['register'];
        } else {
            $this->data['register'] = $this->config->get('simple_customer_view_customer_register_init');
        }
        
        if (isset($this->request->post['subscribe'])) {
      		$this->data['subscribe'] = $this->request->post['subscribe'];
		} elseif (isset($this->session->data['guest']['simple']['subscribe'])) {
            $this->data['subscribe'] = $this->session->data['guest']['simple']['subscribe'];
        } else {
			$this->data['subscribe'] = $this->config->get('simple_customer_view_customer_subscribe_init');
		}
        
        if (isset($this->request->post['password'])) {
      		$this->data['password'] = trim($this->request->post['password']);
		} elseif (isset($this->session->data['guest']['simple']['password'])) {
            $this->data['password'] = $this->session->data['guest']['simple']['password'];
        } elseif ($this->data['simple_customer_generate_password']) {
            $eng = "qwertyuiopasdfghjklzxcvbnm1234567890";
            $this->data['password'] = $eng[rand(0,35)].$eng[rand(0,35)].$eng[rand(0,35)].$eng[rand(0,35)].$eng[rand(0,35)].$eng[rand(0,35)]; 
        } else {
			$this->data['password'] = '';
		}
        
        if (isset($this->request->post['password_confirm'])) {
      		$this->data['password_confirm'] = trim($this->request->post['password_confirm']);
		} elseif (isset($this->session->data['guest']['simple']['password_confirm'])) {
            $this->data['password_confirm'] = $this->session->data['guest']['simple']['password_confirm'];
        } elseif ($this->data['simple_customer_generate_password']) {
            $this->data['password_confirm'] = $this->data['password'];   
        } else {
			$this->data['password_confirm'] = '';
		}
        
        $this->data['simple_customer_view_address_select'] = $this->config->get('simple_customer_view_address_select');
        $this->data['simple_payment_view_address_select'] = $this->config->get('simple_payment_view_address_select');
        
        if ($this->customer->isLogged() && ($this->data['simple_customer_view_address_select'] || $this->data['simple_payment_view_address_select'])) {
            $this->data['addresses'] = $this->model_account_address->getAddresses();
        }
		
        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_customer.tpl')) {
            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_customer.tpl';
        } else {
            $this->template = 'default/template/checkout/simplecheckout_customer.tpl';
        }
                
        $json['output'] = $this->render();        
        
        $this->response->setOutput(json_encode($json));            
    }
    
    public function save() {
        foreach ($this->request->post as $key => $value) {
            $this->session->data['guest']['simple'][$key] = trim($value);
        }
        
        if (empty($this->request->post['payment_address_same'])) {
            $this->session->data['guest']['simple']['payment_address_same'] = false;
        }
        
        $this->load_fields();
        
        $address = $this->prepare_address($this->get_value('zone_id'), $this->get_value('country_id'));
            
        $address['address_id'] = isset($this->request->post['address_id']) ? intval($this->request->post['address_id']) : 0;
        $address['firstname'] = $this->get_value('firstname');
        $address['lastname'] = $this->get_value('lastname');
        $address['company'] = $this->get_value('company');
        $address['company_id'] = $this->get_value('company_id');
        $address['tax_id'] = $this->get_value('tax_id');
        $address['address_1'] = $this->get_value('address_1');
        $address['address_2'] = $this->get_value('address_2');
        $address['postcode'] = $this->get_value('postcode');
        $address['city'] = $this->get_value('city');
        
        $payment_address = $address;
        
        $simple_payment_view_address_show = $this->config->get('simple_payment_view_address_show');
        
        if (!$this->cart->hasShipping()) {
            $simple_payment_view_address_show = false;
        }
        
        if ($simple_payment_view_address_show && empty($this->request->post['payment_address_same'])) {
            $payment_address = $this->prepare_address($this->get_value('payment_zone_id'), $this->get_value('payment_country_id'));
            
            $payment_address['address_id'] = isset($this->request->post['payment_address_id']) ? intval($this->request->post['payment_address_id']) : 0;
            $payment_address['firstname'] = $this->get_value('payment_firstname');
            $payment_address['lastname'] = $this->get_value('payment_lastname');
            $payment_address['company'] = $this->get_value('payment_company');
            $payment_address['company_id'] = $this->get_value('payment_company_id');
            $payment_address['tax_id'] = $this->get_value('payment_tax_id');
            $payment_address['address_1'] = $this->get_value('payment_address_1');
            $payment_address['address_2'] = $this->get_value('payment_address_2');
            $payment_address['postcode'] = $this->get_value('payment_postcode');
            $payment_address['city'] = $this->get_value('payment_city');
        }
                
        $this->session->data['guest']['shipping'] = $address;
        $this->session->data['guest']['payment'] = $payment_address;
        
        if (!empty($this->session->data['guest']['shipping']['address_id'])) {
            $this->session->data['shipping_address_id'] = $this->session->data['guest']['shipping']['address_id'];
        }
        if (!empty($this->session->data['guest']['shipping']['country_id'])) {
            $this->session->data['shipping_country_id'] = $this->session->data['guest']['shipping']['country_id'];
		}
        if (!empty($this->session->data['guest']['shipping']['zone_id'])) {
            $this->session->data['shipping_zone_id'] = $this->session->data['guest']['shipping']['zone_id'];
        }
        if (empty($this->session->data['guest']['shipping']['country_id']) || empty($this->session->data['guest']['shipping']['zone_id'])) {
            unset($this->session->data['shipping_country_id']);
            unset($this->session->data['shipping_zone_id']);
		}
        if (!empty($this->session->data['guest']['shipping']['postcode'])) {
            $this->session->data['shipping_postcode'] = $this->session->data['guest']['shipping']['postcode'];
        }
        
        if (!empty($this->session->data['guest']['payment']['address_id'])) {
            $this->session->data['payment_address_id'] = $this->session->data['guest']['payment']['address_id'];
        }
        if (!empty($this->session->data['guest']['payment']['country_id'])) {
            $this->session->data['payment_country_id'] = $this->session->data['guest']['payment']['country_id'];
		}
        if (!empty($this->session->data['guest']['payment']['zone_id'])) {
            $this->session->data['payment_zone_id'] = $this->session->data['guest']['payment']['zone_id'];
        }
        if (empty($this->session->data['guest']['payment']['country_id']) || empty($this->session->data['guest']['payment']['zone_id'])) {
            unset($this->session->data['payment_country_id']);
            unset($this->session->data['payment_zone_id']);
		}
        if (!empty($this->session->data['guest']['payment']['postcode'])) {
            $this->session->data['payment_postcode'] = $this->session->data['guest']['payment']['postcode'];
        }
    }
        
    private function prepare_address($zone_id, $country_id = 0) {
        $this->load->model('localisation/zone');
        $this->load->model('localisation/country');
    
        if ($zone_id) {
            $zone = $this->model_localisation_zone->getZone($zone_id);
            if ($zone) {
                if ($zone['country_id'] != $country_id) {
                    $zone_id = 0;
                } else {
                    $country = $this->model_localisation_country->getCountry($zone['country_id']);
                    
                    if ($country) {
                        return array(
                            'address_id'     => 0,
            				'firstname'      => '',
            				'lastname'       => '',
            				'company'        => '',
            				'company_id'     => '',
            				'tax_id'         => '',
            				'address_1'      => '',
            				'address_2'      => '',
            				'postcode'       => '',
            				'city'           => '',
            				'zone_id'        => $zone['zone_id'],
            				'zone'           => $zone['name'],
            				'zone_code'      => $zone['code'],
            				'country_id'     => $zone['country_id'],
            				'country'        => $country['name'],	
            				'iso_code_2'     => $country['iso_code_2'],
            				'iso_code_3'     => $country['iso_code_3'],
            				'address_format' => $country['address_format']
                        );
                    }
                }
            }
        } 
        if ($country_id && !$zone_id) {
            $country = $this->model_localisation_country->getCountry($country_id);
                
            if ($country) {
                return array(
                    'address_id'     => 0,
    				'firstname'      => '',
    				'lastname'       => '',
    				'company'        => '',
    				'company_id'     => '',
    				'tax_id'         => '',
    				'address_1'      => '',
    				'address_2'      => '',
    				'postcode'       => '',
    				'city'           => '',
    				'zone_id'        => $zone_id,
    				'zone'           => '',
    				'zone_code'      => '',
    				'country_id'     => $country['country_id'],
    				'country'        => $country['name'],	
    				'iso_code_2'     => $country['iso_code_2'],
    				'iso_code_3'     => $country['iso_code_3'],
    				'address_format' => $country['address_format']
                );
            }
        }
        
        return array(
            'address_id'     => 0,
    		'firstname'      => '',
    		'lastname'       => '',
    		'company'        => '',
    		'company_id'     => '',
			'tax_id'         => '',
			'address_1'      => '',
    		'address_2'      => '',
    		'postcode'       => '',
    		'city'           => '',
    		'zone_id'        => 0,
    		'zone'           => '',
    		'zone_code'      => '',
    		'country_id'     => 0,
    		'country'        => '',	
    		'iso_code_2'     => '',
    		'iso_code_3'     => '',
    		'address_format' => ''
        );
    }
    
    private function load_fields() {
    
        $shipping_method = false;
        
        if (!empty($this->session->data['shipping_method']) && !empty($this->session->data['shipping_method']['code'])) {
            $shipping = explode('.', $this->session->data['shipping_method']['code']);
            $shipping_method = is_array($shipping) ? $shipping[0] : false;
        }
           
        if (empty($this->data['customer_fields'])) {
            $simple_customer_fields_settings = $this->config->get('simple_customer_fields_settings');
            $simple_customer_fields_settings = is_array($simple_customer_fields_settings) ? $simple_customer_fields_settings : array();
            $simple_custom_fields_settings = $this->config->get('simple_custom_fields_settings');
            $simple_custom_fields_settings = is_array($simple_custom_fields_settings) ? $simple_custom_fields_settings : array();
            
            $customer_fields_settings = $simple_customer_fields_settings + $simple_custom_fields_settings;
            
            $this->data['customer_fields'] = array();
            
            foreach ($customer_fields_settings as $field_name => $field_settings) {
                $value = $this->get_field_value($field_settings);
                $values = $this->get_field_values($field_settings);
                $this->data['customer_fields'][$field_name] = array(
                    'id' => $field_settings['id'],
                    'label' => !empty($field_settings['label'][$this->get_language_code()]) ? html_entity_decode($field_settings['label'][$this->get_language_code()]) : $field_settings['id'],
                    'required' => ($field_settings['validation_type'] > 0 ? true : false),
                    'type' => $field_settings['type'],
                    'value' => $value,
                    'values' => $values,
                    'error' => $this->validate_field($field_settings, $value, $values),
                    'save_to' => $field_settings['save_to'],
                    'system' => $this->config->get('simple_attention'),
                    'mask' => !empty($field_settings['mask']) ? $field_settings['mask'] : '',
                    'autocomplete' => !empty($field_settings['autocomplete']) ? true : false
                );
            }
            
            $this->data['display_customer_fields'] = array();
            
            $set = $this->config->get('simple_customer_fields_set');
            
            $current_set_id = 'default';
            if ($shipping_method && !empty($set[$shipping_method])) {
                $current_set_id = $shipping_method;
            }
            
            if (!empty($set[$current_set_id])) {
                $fields = explode(',',$set[$current_set_id]);
                if (is_array($fields) && count($fields) > 0) {
                    foreach ($fields as $id) {
                        if (!empty($this->data['customer_fields'][$id])) {
                            $this->data['display_customer_fields'][$id] = $this->data['customer_fields'][$id];
                        }
                    }
                }
            }
            
            $this->session->data['guest']['display_customer_fields'] = $this->data['display_customer_fields'];
        }
        
        if (empty($this->data['company_fields'])) {
            $company_fields_settings = $this->config->get('simple_company_fields_settings');
            $company_fields_settings = is_array($company_fields_settings) ? $company_fields_settings : array();
            
            $this->data['company_fields'] = array();
            
            foreach ($company_fields_settings as $field_name => $field_settings) {
                $value = $this->get_field_value($field_settings);
                $values = $this->get_field_values($field_settings);
                $this->data['company_fields'][$field_name] = array(
                    'id' => $field_settings['id'],
                    'label' => !empty($field_settings['label'][$this->get_language_code()]) ? $field_settings['label'][$this->get_language_code()] : $field_settings['id'],
                    'required' => ($field_settings['validation_type'] > 0 ? true : false),
                    'type' => $field_settings['type'],
                    'value' => $value,
                    'values' => $values,
                    'error' => $this->validate_field($field_settings, $value, $values),
                    'save_to' => $field_settings['save_to'],
                    'mask' => !empty($field_settings['mask']) ? $field_settings['mask'] : '',
                    'autocomplete' => !empty($field_settings['autocomplete']) ? true : false
                );
            }
            
            $this->data['display_company_fields'] = array();
            
            $set = $this->config->get('simple_company_fields_set');
            
            $current_set_id = 'default';
            if ($shipping_method && !empty($set[$shipping_method])) {
                $current_set_id = $shipping_method;
            }
            
            if (!empty($set[$current_set_id])) {
                $fields = explode(',',$set[$current_set_id]);
                if (is_array($fields) && count($fields) > 0) {
                    foreach ($fields as $id) {
                        if (!empty($this->data['company_fields'][$id])) {
                            $this->data['display_company_fields'][$id] = $this->data['company_fields'][$id];
                        }
                    }
                }
            }
        }
        
        if (empty($this->data['payment_address_fields'])) {
            $simple_customer_fields_settings = $this->config->get('simple_customer_fields_settings');
            $simple_customer_fields_settings = is_array($simple_customer_fields_settings) ? $simple_customer_fields_settings : array();
            
            $this->data['payment_address_fields'] = array();
            
            $payment_address_fields = array('main_firstname','main_lastname','main_company','main_company_id','main_tax_id','main_address_1','main_address_2','main_city','main_postcode','main_zone_id','main_country_id');
        
            foreach ($simple_customer_fields_settings as $field_name => $field_settings) {
                if (!in_array($field_name,$payment_address_fields)) {
                    continue;
                }
                $id = str_replace('main_', 'payment_', $field_name);
                $field_settings['id'] = $id;
                $value = $this->get_field_value($field_settings);
                $values = $this->get_field_values($field_settings);
                $this->data['payment_address_fields'][$id] = array(
                    'id' => $field_settings['id'],
                    'label' => !empty($field_settings['label'][$this->get_language_code()]) ? html_entity_decode($field_settings['label'][$this->get_language_code()]) : $field_settings['id'],
                    'required' => ($field_settings['validation_type'] > 0 ? true : false),
                    'type' => $field_settings['type'],
                    'value' => $value,
                    'values' => $values,
                    'error' => $this->validate_field($field_settings, $value, $values),
                    'save_to' => 'payment_'.$field_settings['save_to'],
                    'system' => $this->config->get('simple_attention'),
                    'mask' => !empty($field_settings['mask']) ? $field_settings['mask'] : '',
                    'autocomplete' => !empty($field_settings['autocomplete']) ? true : false
                );
            }
            
            $this->data['display_payment_address_fields'] = array();
            
            $set = $this->config->get('simple_payment_address_fields_set');
            
            if (!empty($set)) {
                $fields = explode(',',$set);
            }
            
            if (empty($fields)) {
                $fields = $payment_address_fields;
            }
            
            if (is_array($fields) && count($fields) > 0) {
                foreach ($fields as $id) {
                    $id_payment = str_replace('main_', 'payment_', $id);
                    if (!empty($this->data['payment_address_fields'][$id_payment])) {
                        $this->data['display_payment_address_fields'][$id_payment] = $this->data['payment_address_fields'][$id_payment];
                    }
                }
            }
           
            $this->session->data['guest']['display_payment_address_fields'] = $this->data['display_payment_address_fields'];
        }
    }
    
    private function get_language_code() {
        if (empty($this->language_code)) {
            $this->language_code = str_replace('-', '_', strtolower($this->config->get('config_language')));
        }
        return $this->language_code;
    }
    
    private function get_field_value($fields_settings) {
        $this->load->model('tool/simplegeo');
        $id = $fields_settings['id'];
        $from = 'shipping';
        if (substr($id, 0, 5) == 'main_') {
            $id = substr($id,5);
        } elseif (substr($id, 0, 7) == 'custom_') {
            $id = substr($id,7);
        } elseif (substr($id, 0, 8) == 'company_') {
            $id = substr($id,8);
        } elseif (substr($id, 0, 8) == 'payment_') {
            $id = substr($id,8);
            $from = 'payment';
        }
        
        $value = '';
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post[$fields_settings['id']])) {
            $value = trim($this->request->post[$fields_settings['id']]);
        } elseif (!empty($this->session->data['guest']['simple'][$fields_settings['id']])) {
            $value = trim($this->session->data['guest']['simple'][$fields_settings['id']]);
        } elseif ($from == 'shipping' && !empty($this->session->data['guest']['shipping'][$id])) {
            $value = trim($this->session->data['guest']['shipping'][$id]); 
        }  elseif ($from == 'payment' && !empty($this->session->data['guest']['payment'][$id])) {
            $value = trim($this->session->data['guest']['payment'][$id]); 
        } elseif (!empty($this->session->data['guest'][$id])) {
            $value = trim($this->session->data['guest'][$id]);
        } elseif (!empty($this->session->data['guest']['simpledata_customer'][$fields_settings['id']])) {
            $value = trim($this->session->data['guest']['simpledata_customer'][$fields_settings['id']]);
        } elseif (!empty($this->session->data['guest']['simpledata_address'][$fields_settings['id']])) {
            $value = trim($this->session->data['guest']['simpledata_address'][$fields_settings['id']]); 
        } elseif (!empty($this->session->data[$id])) {
            $value = trim($this->session->data[$id]);
        } else {
            if (!empty($fields_settings['init_geoip']) && $geo = $this->model_tool_simplegeo->getGeoIp()) {
                if (($fields_settings['id'] == 'main_country_id' || $fields_settings['id'] == 'payment_country_id') && !empty($geo['country_id'])) {
                    $value = $geo['country_id'];
                }
                
                if (($fields_settings['id'] == 'main_zone_id' || $fields_settings['id'] == 'payment_zone_id') && !empty($geo['zone_id'])) {
                    $value = $geo['zone_id'];
                }
                
                if (($fields_settings['id'] == 'main_city' || $fields_settings['id'] == 'payment_city') && !empty($geo['city'])) {
                    $value = $geo['city'];
                }
                
                if (($fields_settings['id'] == 'main_postcode' || $fields_settings['id'] == 'payment_postcode') && !empty($geo['postcode'])) {
                    $value = $geo['postcode'];
                }
            }
            
            $value = empty($value) && !empty($fields_settings['init']) ? $fields_settings['init'] : $value;
        }
        
        if ($fields_settings['id'] == 'main_email' && $value == 'empty@localhost') {
            $value = '';
        }
        
        if ($fields_settings['id'] == 'main_customer_group_id' && $value === '') {
            $value = $this->config->get('config_customer_group_id');
        }
        
        return $value;
    }
    
    private function get_field_values($fields_settings) {
        $return_values = array();
        if ($fields_settings['type'] == 'select' || $fields_settings['type'] == 'radio') {
            if ($fields_settings['values'] == 'countries') {
                $this->load->model('localisation/country');
                $values = $this->model_localisation_country->getCountries();
                foreach ($values as $value) {
                    $return_values[$value['country_id']] = $value['name'];
                }
            } elseif ($fields_settings['values'] == 'zones') {
                $this->load->model('localisation/zone');
                if ($fields_settings['id'] == 'main_zone_id') {
                    $values = $this->model_localisation_zone->getZonesByCountryId(!empty($this->data['customer_fields']['main_country_id']['value']) ? $this->data['customer_fields']['main_country_id']['value'] : 0);
                } elseif ($fields_settings['id'] == 'payment_zone_id') {
                    $values = $this->model_localisation_zone->getZonesByCountryId(!empty($this->data['payment_address_fields']['payment_country_id']['value']) ? $this->data['payment_address_fields']['payment_country_id']['value'] : 0);
                }
                foreach ($values as $value) {
                    $return_values[$value['zone_id']] = $value['name'];
                }
                if (empty($return_values)) {
        		  	$return_values[0] = $this->language->get('text_none');
        		}
            } elseif ($fields_settings['values'] == 'groups') {
                $file  = DIR_APPLICATION . 'model/account/customer_group.php';
    		
                if (file_exists($file)) {
                    $this->load->model('account/customer_group');
                
                    if (method_exists($this->model_account_customer_group,'getCustomerGroups')) {
                        $customer_groups = $this->model_account_customer_group->getCustomerGroups();
    			
            			foreach ($customer_groups as $customer_group) {
            				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
            					$return_values[$customer_group['customer_group_id']] = $customer_group['name'];
            				}
            			}
                    }
                } else {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group");
                    foreach ($query->rows as $row) {
                        $return_values[$row['customer_group_id']] = $row['name'];
                    }
                }
            } elseif (!empty($fields_settings['values'][$this->get_language_code()])) {
                $values = $fields_settings['values'][$this->get_language_code()];
                $values = explode(';', $values);
                if (!empty($values) && count($values) > 0) {
                    foreach ($values as $value) {
                        $parts = explode('=', $value, 2);
                        if (!empty($parts) && count($parts) == 2) {
                            $return_values[$parts[0]] = $parts[1];
                        }
                    }
                }
            }
        }
        
        if ($fields_settings['type'] == 'select_from_api') {
            if (file_exists(DIR_APPLICATION . 'model/tool/simpledata.php')) {
				$this->load->model('tool/simpledata');
                if (method_exists($this->model_tool_simpledata,'select_'.$fields_settings['id'])) {
                    return $this->model_tool_simpledata->{'select_'.$fields_settings['id']}();
                }
			}            
        }
        
        return $return_values;
    }
    
    private function validate_field($fields_settings, $value, $values) {
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post[$fields_settings['id']])) {
            $value = trim($this->request->post[$fields_settings['id']]);
            $error_msg = !empty($fields_settings['validation_error'][$this->get_language_code()]) ? $fields_settings['validation_error'][$this->get_language_code()] : 'Error';
            
            if ($fields_settings['validation_type'] == 0) {
                return '';
            } elseif ($fields_settings['validation_type'] == 1) {
                if (empty($value)) {
                    return $error_msg;
                }
            } elseif ($fields_settings['validation_type'] == 2) {
                if (strlen(utf8_decode($value)) < $fields_settings['validation_min'] || strlen(utf8_decode($value)) > $fields_settings['validation_max']) {
                    return $error_msg;
                }
            } elseif ($fields_settings['validation_type'] == 3) {
                if ($value == '' || !preg_match($fields_settings['validation_regexp'], $value)) {
                    return $error_msg;
                }
            } elseif ($fields_settings['validation_type'] == 4) {
                if (!array_key_exists($value, $values)) {
                    return $error_msg;
                }
            } elseif ($fields_settings['validation_type'] == 5) {
                if (file_exists(DIR_APPLICATION . 'model/tool/simpledata.php')) {
    				$this->load->model('tool/simpledata');
                    if (method_exists($this->model_tool_simpledata,'validate_'.$fields_settings['id'])) {
                        return $this->model_tool_simpledata->{'validate_'.$fields_settings['id']}($value);
                    }
    			}
            }
        }
        
        return '';
    }
    
    private function get_value($id, $all = false) {
        $this->load_fields();
        $value = null;
        
        foreach ($this->data['customer_fields'] as $field) {
            if ($field['save_to'] == $id && isset($field['value'])) {
                if (!empty($value)) {
                    $value = trim($value).' '.$field['value'];
                } else {
                    $value = $field['value'];
                }
                if (!$all) {
                    break;
                }
            }
        }
        
        foreach ($this->data['company_fields'] as $field) {
            if ($field['save_to'] == $id && isset($field['value'])) {
                if (!empty($value)) {
                    $value = trim($value).' '.$field['value'];
                } else {
                    $value = $field['value'];
                }
                if (!$all) {
                    break;
                }
            }
        }
        
        foreach ($this->data['payment_address_fields'] as $field) {
            if ($field['save_to'] == $id && isset($field['value'])) {
                if (!empty($value)) {
                    $value = trim($value).' '.$field['value'];
                } else {
                    $value = $field['value'];
                }
                if (!$all) {
                    break;
                }
            }
        }
        
        return isset($value) ? $value : '';
    }
    
    public function zone() {
		$output = '<option value="">' . $this->language->get('text_select') . '</option>';
		
		$this->load->model('localisation/zone');

    	$results = $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']);
        
      	foreach ($results as $result) {
        	$output .= '<option value="' . $result['zone_id'] . '"';
	
	    	if (isset($this->request->get['zone_id']) && ($this->request->get['zone_id'] == $result['zone_id'])) {
	      		$output .= ' selected="selected"';
	    	}
	
	    	$output .= '>' . $result['name'] . '</option>';
    	} 
		
		if (!$results) {
		  	$output .= '<option value="0">' . $this->language->get('text_none') . '</option>';
		}
	
		$this->response->setOutput($output);
  	}  
    
    public function login() {
        $this->language->load('checkout/simplecheckout');
        
        $json = array();
        
        $this->data['error_email_exists'] = '';
        $this->data['error_login'] = '';
        
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ($this->customer->login($this->request->post['email'], $this->request->post['password'])) {
                unset($this->session->data['guest']);
                $json['reload'] = true;
            } else {
                $this->data['error_login'] = $this->language->get('error_login');
            }
        }
        
        $this->data['text_checkout_customer'] = $this->language->get('text_checkout_customer');
        $this->data['text_checkout_customer_login'] = $this->language->get('text_checkout_customer_login');
        $this->data['text_checkout_customer_cancel'] = $this->language->get('text_checkout_customer_cancel');
        $this->data['text_forgotten'] = $this->language->get('text_forgotten');
        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_password'] = $this->language->get('entry_password');
        $this->data['button_login'] = $this->language->get('button_login');

        $this->data['simple_customer_view_login'] = true;
        
        $this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
        
        if (isset($this->request->post['email'])) {
            $this->data['email'] = trim($this->request->post['email']);
        } else {
            $this->data['email'] = '';
        }
        
        $this->data['action_login'] = true;
        
        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_customer.tpl')) {
            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_customer.tpl';
            $this->data['template'] = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
        } else {
            $this->template = 'default/template/checkout/simplecheckout_customer.tpl';
            $this->data['template'] = 'default';
        }
             
        $json['output'] = $this->render();    
        
        $this->response->setOutput(json_encode($json));
    }
    
    private function validate() {
        $error = false;
        
        $products = $this->cart->getProducts();
    				
		foreach ($products as $product) {
			$product_total = 0;
				
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}		
			
            if ($product['minimum'] > $product_total) {
                $error = true;
            }		
		}
        
        if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
    		$error = true;
    	}
    		
		if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
  			$error = true;
		}
        
        if ($this->cart->hasShipping() && empty($this->session->data['shipping_method'])) {
  			$error = true;
            $this->data['error_warning'] = $this->language->get('error_shipping');
            $this->data['error_warning_block'] = 'shipping';
		}
        
        if (empty($this->session->data['payment_method'])) {
  			$error = true;
            $this->data['error_warning'] = $this->language->get('error_payment');
            $this->data['error_warning_block'] = 'payment';
		}
        
        $register_customer = $this->customer->isLogged() ? 0 : (isset($this->request->post['register']) ? $this->request->post['register'] : ($this->data['simple_customer_action_register'] == 1));
        
        if (!empty($this->data['display_customer_fields']['main_email']) && ($this->data['simple_customer_action_register'] == 0 || ($this->data['simple_customer_action_register'] == 2 && !$register_customer))) {
            if ($this->data['simple_customer_view_email'] == 1 || $this->data['simple_customer_view_email'] == 0) {
                if (empty($this->request->post['main_email'])) {
                    $this->data['display_customer_fields']['main_email']['error'] = '';
                } 
            } 
        } 
        
        foreach ($this->data['display_customer_fields'] as $field) {
            if ($field['error'] != '') {
                $error = true;
                break;
            }
        }
        
        if ($this->data['simple_payment_view_address_show'] && !$this->data['payment_address_same']) {
            foreach ($this->data['display_payment_address_fields'] as $field) {
                if ($field['error'] != '') {
                    $error = true;
                    break;
                }
            }
        }
        
        if ($this->data['simple_customer_view_customer_type'] && !empty($this->request->post['customer_type']) && $this->request->post['customer_type'] == 'company') {
            foreach ($this->data['display_company_fields'] as $field) {
                if ($field['error'] != '') {
                    $error = true;
                    break;
                }
            }
        }
        
        if ($register_customer && !empty($this->request->post['main_email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['main_email'])) {
      		$this->data['display_customer_fields']['main_email']['error'] = $this->language->get('error_exists');
            $error = true;
    	}
        
        $password_length_min = $this->config->get('simple_customer_view_password_length_min');
        $password_length_min = !empty($password_length_min) ? $password_length_min : 4;
        
        $password_length_max = $this->config->get('simple_customer_view_password_length_max');
        $password_length_max = !empty($password_length_max) ? $password_length_max : 20;
        
        $password = !empty($this->request->post['password']) ? trim($this->request->post['password']) : '';
        $password_confirm = !empty($this->request->post['password_confirm']) ? trim($this->request->post['password_confirm']) : '';
        
        if ($register_customer && (strlen(utf8_decode($password)) < $password_length_min || strlen(utf8_decode($password)) > $password_length_max)) {
            $this->data['error_password'] = sprintf($this->language->get('error_password'), $password_length_min, $password_length_max);
            $error = true;     
        }
        
        if ($register_customer && $this->data['simple_customer_view_password_confirm'] && $password != $password_confirm) {
            $this->data['error_password_confirm'] = $this->language->get('error_password_confirm');
            $error = true;   
        }
        
        if ($this->config->get('simple_common_view_agreement_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('simple_common_view_agreement_id'));
			
			if ($information_info) {
                $agreement_checkbox = $this->config->get('simple_common_view_agreement_checkbox');
                if (!empty($agreement_checkbox) && empty($this->request->post['agree'])) {
                    $this->data['error_warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
                    $error = true;
                }
            } 
		}
        
    	return !$error;
    }
    
    private function order() {
        $version = explode('.', VERSION);
        $version = $version[0].$version[1].$version[2];
        $subversion = isset($version[3]) ? $version[3] : 0;
        
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
        
        $data['customer_id'] = $this->customer->isLogged() && $this->customer->getId() ? $this->customer->getId() : $this->session->data['guest']['customer_id'];
        $data['customer_group_id'] = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->session->data['guest']['customer_group_id'];
        $data['firstname'] = $this->session->data['guest']['firstname'];
        $data['lastname'] = $this->session->data['guest']['lastname'];
        $data['email'] = $this->session->data['guest']['email'];
        $data['telephone'] = $this->session->data['guest']['telephone'];
        $data['fax'] = $this->session->data['guest']['fax'];
    
        $data['payment_firstname'] = $this->session->data['guest']['payment']['firstname'];
        $data['payment_lastname'] = $this->session->data['guest']['payment']['lastname'];    
        $data['payment_company'] = $this->session->data['guest']['payment']['company'];    
        $data['payment_address_1'] = $this->session->data['guest']['payment']['address_1'];
        $data['payment_address_2'] = $this->session->data['guest']['payment']['address_2'];
        $data['payment_city'] = $this->session->data['guest']['payment']['city'];
        $data['payment_postcode'] = $this->session->data['guest']['payment']['postcode'];
        $data['payment_zone'] = $this->session->data['guest']['payment']['zone'];
        $data['payment_zone_id'] = $this->session->data['guest']['payment']['zone_id'];
        $data['payment_country'] = $this->session->data['guest']['payment']['country'];
        $data['payment_country_id'] = $this->session->data['guest']['payment']['country_id'];
        $data['payment_address_format'] = $this->session->data['guest']['payment']['address_format'];
        $data['payment_company_id'] = $this->session->data['guest']['payment']['company_id'];	
		$data['payment_tax_id'] = $this->session->data['guest']['payment']['tax_id'];	
			
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
            $data['shipping_firstname'] = $this->session->data['guest']['shipping']['firstname'];
            $data['shipping_lastname'] = $this->session->data['guest']['shipping']['lastname'];    
            $data['shipping_company'] = $this->session->data['guest']['shipping']['company'];    
            $data['shipping_address_1'] = $this->session->data['guest']['shipping']['address_1'];
            $data['shipping_address_2'] = $this->session->data['guest']['shipping']['address_2'];
            $data['shipping_city'] = $this->session->data['guest']['shipping']['city'];
            $data['shipping_postcode'] = $this->session->data['guest']['shipping']['postcode'];
            $data['shipping_zone'] = $this->session->data['guest']['shipping']['zone'];
            $data['shipping_zone_id'] = $this->session->data['guest']['shipping']['zone_id'];
            $data['shipping_country'] = $this->session->data['guest']['shipping']['country'];
            $data['shipping_country_id'] = $this->session->data['guest']['shipping']['country_id'];
            $data['shipping_address_format'] = $this->session->data['guest']['shipping']['address_format'];
                
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
		
        if ($version == '151') {
        
            if (method_exists($this->tax,'setZone')) {
                 if ($this->cart->hasShipping()) {
    				$this->tax->setZone($data['shipping_country_id'], $data['shipping_zone_id']);
    			} else {
    				$this->tax->setZone($data['shipping_country_id'], $data['shipping_zone_id']);
    			}
            }
            
            $this->load->library('encryption');
        
            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();
        
                foreach ($product['option'] as $option) {
                    if ($option['type'] != 'file') {    
                        $option_data[] = array(
                            'product_option_id'       => $option['product_option_id'],
                            'product_option_value_id' => $option['product_option_value_id'],
                            'product_option_id'       => $option['product_option_id'],
                            'product_option_value_id' => $option['product_option_value_id'],
                            'option_id'               => $option['option_id'],
                            'option_value_id'         => $option['option_value_id'],                                   
                            'name'                    => $option['name'],
                            'value'                   => $option['option_value'],
                            'type'                    => $option['type']
                        );                    
                    } else {
                        $encryption = new Encryption($this->config->get('config_encryption'));
                        
                        $option_data[] = array(
                            'product_option_id'       => $option['product_option_id'],
                            'product_option_value_id' => $option['product_option_value_id'],
                            'product_option_id'       => $option['product_option_id'],
                            'product_option_value_id' => $option['product_option_value_id'],
                            'option_id'               => $option['option_id'],
                            'option_value_id'         => $option['option_value_id'],                                   
                            'name'                    => $option['name'],
                            'value'                   => $encryption->decrypt($option['option_value']),
                            'type'                    => $option['type']
                        );                                
                    }
                }
        
                $product_data[] = array(
                    'product_id' => $product['product_id'],
                    'name'       => $product['name'],
                    'model'      => $product['model'],
                    'option'     => $option_data,
                    'download'   => $product['download'],
                    'quantity'   => $product['quantity'],
                    'subtract'   => $product['subtract'],
                    'price'      => $product['price'],
                    'total'      => $product['total'],
                    'tax'        => method_exists($this->tax,'getRate') ? $this->tax->getRate($product['tax_class_id']) : $this->tax->getTax($product['total'], $product['tax_class_id'])
                ); 
            }
            
            // Gift Voucher
            if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $product_data[] = array(
                        'product_id' => 0,
                        'name'       => $voucher['description'],
                        'model'      => '',
                        'option'     => array(),
                        'download'   => array(),
                        'quantity'   => 1,
                        'subtract'   => false,
                        'price'      => $voucher['amount'],
                        'total'      => $voucher['amount'],
                        'tax'        => 0
                    );
                }
            } 
                        
            $data['products'] = $product_data;
            $data['totals'] = $total_data;
            $data['comment'] = $this->session->data['comment'];
            $data['total'] = $total;
            $data['reward'] = $this->cart->getTotalRewardPoints();
        } elseif ($version == '152' || $version == '153' || $version == '154') {
    		foreach ($this->cart->getProducts() as $product) {
    			$option_data = array();
    
    			foreach ($product['option'] as $option) {
    				if ($option['type'] != 'file') {
    					$value = $option['option_value'];	
                	} else {
    					$value = $this->encryption->decrypt($option['option_value']);
    				}	
    				
    				$option_data[] = array(
    					'product_option_id'       => $option['product_option_id'],
    					'product_option_value_id' => $option['product_option_value_id'],
    					'option_id'               => $option['option_id'],
    					'option_value_id'         => $option['option_value_id'],								   
    					'name'                    => $option['name'],
    					'value'                   => $value,
    					'type'                    => $option['type']
    				);					
    			}
     
    			$product_data[] = array(
    				'product_id' => $product['product_id'],
    				'name'       => $product['name'],
    				'model'      => $product['model'],
    				'option'     => $option_data,
    				'download'   => $product['download'],
    				'quantity'   => $product['quantity'],
    				'subtract'   => $product['subtract'],
    				'price'      => $product['price'],
    				'total'      => $product['total'],
    				'tax'        => $this->tax->getTax($product['total'], $product['tax_class_id']),
    				'reward'     => $product['reward']
    			); 
    		}
            
            // Gift Voucher
    		$voucher_data = array();
    		
    		if (!empty($this->session->data['vouchers'])) {
    			foreach ($this->session->data['vouchers'] as $voucher) {
    				$voucher_data[] = array(
    					'description'      => $voucher['description'],
    					'code'             => substr(md5(rand()), 0, 7),
    					'to_name'          => $voucher['to_name'],
    					'to_email'         => $voucher['to_email'],
    					'from_name'        => $voucher['from_name'],
    					'from_email'       => $voucher['from_email'],
    					'voucher_theme_id' => $voucher['voucher_theme_id'],
    					'message'          => $voucher['message'],						
    					'amount'           => $voucher['amount']
    
    				);
    			}
    		}  
    					
    		$data['products'] = $product_data;
    		$data['vouchers'] = $voucher_data;
    		$data['totals'] = $total_data;
    		$data['comment'] = $this->session->data['comment'];
    		$data['total'] = $total; 
        }
        
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
		} elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
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
        
        $order_id = 0;
        
        if ($version == '151') {
            $order_id = $this->model_checkout_order->create($data);
            
            // Gift Voucher
            if (isset($this->session->data['vouchers']) && is_array($this->session->data['vouchers'])) {
                $this->load->model('checkout/voucher');
        
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $this->model_checkout_voucher->addVoucher($order_id, $voucher);
                }
            }
        } elseif ($version == '152' || $version == '153' || $version == '154') {
            $order_id = $this->model_checkout_order->addOrder($data);
        }
        
        $this->session->data['order_id'] = $order_id;
        
        return $order_id;
    }
}
?>