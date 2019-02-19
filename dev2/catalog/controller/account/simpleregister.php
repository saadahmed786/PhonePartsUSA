<?php
/*
@author	Dmitriy Kubarev
@link	http://www.simpleopencart.com
@link	http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/  
 
class ControllerAccountSimpleRegister extends Controller {
	private $error = array();
    private $language_code = '';
    
  	public function index() {
		if ($this->customer->isLogged()) {
	  		$this->redirect($this->url->link('account/account', '', 'SSL'));
    	}

    	$this->language->load('account/simpleregister');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/customer');
		
        $this->data['simple_registration_password_confirm'] = $this->config->get('simple_registration_password_confirm');
        $this->data['simple_registration_captcha'] = $this->config->get('simple_registration_captcha');
        $this->data['simple_registration_subscribe'] = $this->config->get('simple_registration_subscribe');
        $this->data['simple_registration_subscribe_init'] = $this->config->get('simple_registration_subscribe_init');
        $this->data['simple_registration_view_customer_type'] = $this->config->get('simple_registration_view_customer_type');
        $this->data['simple_registration_generate_password'] = $this->config->get('simple_registration_generate_password');
        
        $this->data['error_warning'] = '';
        $this->data['error_password'] = '';
        $this->data['error_password_confirm'] = '';
        $this->data['error_captcha'] = '';
        
        $this->load_fields();
        
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
            unset($this->session->data['guest']);
            
            $data = array(
                'firstname' => $this->get_value('firstname'),
                'lastname' => $this->get_value('lastname'),
                'email' => $this->get_value('email'),
                'password' => trim($this->request->post['password']),
                'telephone' => $this->get_value('telephone'),
                'fax' => $this->get_value('fax'),
                'newsletter' => isset($this->request->post['subscribe']) ? $this->request->post['subscribe'] : ($this->data['simple_registration_subscribe'] == 1),
                'company' => $this->get_value('company'),
                'company_id' => $this->get_value('company_id'),
                'tax_id' => $this->get_value('tax_id'),
                'address_1' => $this->get_value('address_1'),
                'address_2' => $this->get_value('address_2'),
                'postcode' => $this->get_value('postcode'),
                'city' => $this->get_value('city'),
                'country_id' => $this->get_value('country_id'),
                'zone_id' => $this->get_value('zone_id'),
                'customer_group_id' => $this->get_value('customer_group_id')
            );
			
			$this->model_account_customer->addCustomer($data);
            
            $this->customer->login($data['email'], $data['password']);
	  	  
            if (file_exists(DIR_APPLICATION . 'model/tool/simpledata.php')) {
				$this->load->model('tool/simpledata');
                if (method_exists($this->model_tool_simpledata,'saveRegistrationData')) {
                    $this->model_tool_simpledata->saveRegistrationData($this->customer->getId(), $this->request->post);
                }
			}
			
	  		$this->redirect($this->url->link('account/success'));
    	} 

      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),        	
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),      	
        	'separator' => $this->language->get('text_separator')
      	);
		
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_register'),
			'href'      => $this->url->link('account/simpleregister', '', 'SSL'),      	
        	'separator' => $this->language->get('text_separator')
      	);
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', 'SSL'));
    	$this->data['text_your_details'] = $this->language->get('text_your_details');
    	$this->data['text_company_details'] = $this->language->get('text_company_details');
    	$this->data['text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_select'] = $this->language->get('text_select');
        		
    	$this->data['entry_password'] = $this->language->get('entry_password');
    	$this->data['entry_password_confirm'] = $this->language->get('entry_password_confirm');
    	$this->data['entry_newsletter'] = $this->language->get('entry_newsletter');
    	$this->data['entry_captcha'] = $this->language->get('entry_captcha');
		$this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['text_private'] = $this->language->get('text_private');
        $this->data['text_company'] = $this->language->get('text_company');
    
        $this->data['action'] = $this->url->link('account/simpleregister', '', 'SSL');

        $this->data['simple_registration_agreement_checkbox'] = false;
        $this->data['simple_registration_agreement_checkbox_init'] = 0;
                
        $this->data['text_agree'] = '';
        
		if ($this->config->get('simple_registration_agreement_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('simple_registration_agreement_id'));
			
			if ($information_info) {
                $this->data['simple_registration_agreement_checkbox'] = $this->config->get('simple_registration_agreement_checkbox');
                $this->data['simple_registration_agreement_checkbox_init'] = $this->config->get('simple_registration_agreement_checkbox_init');
                
                $current_theme = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
                
                $text = ($current_theme == 'shoppica' || $current_theme == 'shoppica2') ? 'text_agree_shoppica' : 'text_agree';
				$this->data['text_agree'] = sprintf($this->language->get($text), $this->url->link('information/information/info', 'information_id=' . $this->config->get('simple_registration_agreement_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} 
		}
        
		if (isset($this->request->post['agree'])) {
      		$this->data['agree'] = $this->request->post['agree'];
		} else {
			$this->data['agree'] = $this->data['simple_registration_agreement_checkbox_init'];
		}
        
        if (isset($this->request->post['subscribe'])) {
      		$this->data['subscribe'] = $this->request->post['subscribe'];
		} else {
			$this->data['subscribe'] = $this->data['simple_registration_subscribe_init'];
		}
        
        if (isset($this->request->post['password'])) {
      		$this->data['password'] = trim($this->request->post['password']);
		} elseif ($this->data['simple_registration_generate_password']) {
            $eng = "qwertyuiopasdfghjklzxcvbnm1234567890";
            $this->data['password'] = $eng[rand(0,35)].$eng[rand(0,35)].$eng[rand(0,35)].$eng[rand(0,35)].$eng[rand(0,35)].$eng[rand(0,35)]; 
        } else {
			$this->data['password'] = '';
		}
        
        if (isset($this->request->post['password_confirm'])) {
      		$this->data['password_confirm'] = trim($this->request->post['password_confirm']);
		} elseif ($this->data['simple_registration_generate_password']) {
            $this->data['password_confirm'] = $this->data['password']; 
        } else {
			$this->data['password_confirm'] = '';
		}
        
        if (isset($this->request->post['customer_type'])) {
      		$this->data['customer_type'] = $this->request->post['customer_type'];
		} else {
			$this->data['customer_type'] = 'private';
		}
        
        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/simpleregister.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/simpleregister.tpl';
            $this->data['template'] = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
		} else {
			$this->template = 'default/template/account/simpleregister.tpl';
            $this->data['template'] = 'default';
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
    
    private function load_fields() {
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
                    'mask' => !empty($field_settings['mask']) ? $field_settings['mask'] : '',
                    'autocomplete' => !empty($field_settings['autocomplete']) ? true : false
                );
            }
            
            $this->data['display_customer_fields'] = array();
            
            $set = $this->config->get('simple_customer_fields_set');
            if (!empty($set['registration'])) {
                $fields = explode(',',$set['registration']);
                if (count($fields) > 0) {
                    foreach ($fields as $id) {
                        if (!empty($this->data['customer_fields'][$id])) {
                            $this->data['display_customer_fields'][$id] = $this->data['customer_fields'][$id];
                        }
                    }
                }
            }
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
            
            if (!empty($set['registration'])) {
                $fields = explode(',',$set['registration']);
                if (count($fields) > 0) {
                    foreach ($fields as $id) {
                        if (!empty($this->data['company_fields'][$id])) {
                            $this->data['display_company_fields'][$id] = $this->data['company_fields'][$id];
                        }
                    }
                }
            }
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
                
        $value = '';
        
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post[$fields_settings['id']])) {
            $value = trim($this->request->post[$fields_settings['id']]);
        } else {
            if (!empty($fields_settings['init_geoip']) && $geo = $this->model_tool_simplegeo->getGeoIp()) {
                if ($fields_settings['id'] == 'main_country_id' && !empty($geo['country_id'])) {
                    $value = $geo['country_id'];
                }
                
                if ($fields_settings['id'] == 'main_zone_id' && !empty($geo['zone_id'])) {
                    $value = $geo['zone_id'];
                }
                
                if ($fields_settings['id'] == 'main_city' && !empty($geo['city'])) {
                    $value = $geo['city'];
                }
                
                if ($fields_settings['id'] == 'main_postcode' && !empty($geo['postcode'])) {
                    $value = $geo['postcode'];
                }
            }
            
            $value = empty($value) && !empty($fields_settings['init']) ? $fields_settings['init'] : $value;
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
                $values = $this->model_localisation_zone->getZonesByCountryId($this->data['customer_fields']['main_country_id']['value']);
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
    
    private function get_value($id) {
        $this->load_fields();
        
        $value = null;
        
        foreach ($this->data['customer_fields'] as $field) {
            if ($field['save_to'] == $id && isset($field['value'])) {
                if (!empty($value)) {
                    $value = trim($value).' '.$field['value'];
                } else {
                    $value = $field['value'];
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
    
    public function captcha() {
		$this->load->library('captcha');
		
		$captcha = new Captcha();
		
		$this->session->data['captcha'] = $captcha->getCode();
		
		$captcha->showImage();
	}
   
  	private function validate() {
        $error = false;
        
        foreach ($this->data['display_customer_fields'] as $field) {
            if ($field['error'] != '') {
                $error = true;
                break;
            }
        }
        
        if ($this->data['simple_registration_view_customer_type'] && !empty($this->request->post['customer_type']) && $this->request->post['customer_type'] == 'company') {
            foreach ($this->data['display_company_fields'] as $field) {
                if ($field['error'] != '') {
                    $error = true;
                    break;
                }
            }
        }
        
        if (!empty($this->request->post['main_email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['main_email'])) {
      		$this->data['display_customer_fields']['main_email']['error'] = $this->language->get('error_exists');
            $error = true;
    	}
        
        $password_length_min = $this->config->get('simple_registration_password_length_min');
        $password_length_min = !empty($password_length_min) ? $password_length_min : 4;
        
        $password_length_max = $this->config->get('simple_registration_password_length_max');
        $password_length_max = !empty($password_length_max) ? $password_length_max : 20;
        
        $password = !empty($this->request->post['password']) ? trim($this->request->post['password']) : '';
        $password_confirm = !empty($this->request->post['password_confirm']) ? trim($this->request->post['password_confirm']) : '';
        
        if (strlen(utf8_decode($password)) < $password_length_min || strlen(utf8_decode($password)) > $password_length_max) {
            $this->data['error_password'] = sprintf($this->language->get('error_password'), $password_length_min, $password_length_max);
            $error = true;     
        }
        
        if ($this->data['simple_registration_password_confirm'] && $password != $password_confirm) {
            $this->data['error_password_confirm'] = $this->language->get('error_password_confirm');
            $error = true;   
        }
        
        if ($this->config->get('simple_registration_captcha') && (empty($this->session->data['captcha']) || (isset($this->request->post['captcha']) && $this->session->data['captcha'] != $this->request->post['captcha']))) {
			$this->data['error_captcha'] = $this->language->get('error_captcha');
            $error = true;            
		}
        
        if ($this->config->get('simple_registration_agreement_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('simple_registration_agreement_id'));
			
			if ($information_info) {
                $agreement_checkbox = $this->config->get('simple_registration_agreement_checkbox');
                if (!empty($agreement_checkbox) && empty($this->request->post['agree'])) {
                    $this->data['error_warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
                    $error = true;
                }
            } 
		}
        
    	return !$error;
  	}
    
    public function geo() {
	    $this->load->model('tool/simplegeo');

        $term = $this->request->get['term'];
        
        $this->response->setOutput(json_encode($this->model_tool_simplegeo->getGeoList($term)));
  	}  
}
?>