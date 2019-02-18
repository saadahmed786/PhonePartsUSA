<?php
//-----------------------------------------
// Author: 	Qphoria@gmail.com
// Web: 	http://www.OpenCartGuru.com/
// Title: 	Uber Checkout 1.5.x
//-----------------------------------------
class ControllerCheckoutCheckoutOne extends Controller {

	private $error = array();
	private $requireAccount = true;

  	public function index() {

		//if (!$this->cart->hasProducts()) {
	  	//	$this->redirect($this->url->link('checkout/cart'));
    	//}

    	if ((!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->url->link('checkout/cart'));
    	}

		if ($this->customer->isLogged()) {
	  		$this->redirect($this->url->link('checkout/checkout_two', '', 'SSL'));
    	}

		if (!empty($this->session->data['error'])) {
      		$this->error['warning'] = $this->session->data['error'];
    	}

		$this->data = array_merge($this->data, $this->language->load('account/register'));
		$this->data = array_merge($this->data, $this->language->load('account/login'));
		$this->data = array_merge($this->data, $this->language->load('checkout/checkout_one'));

		// Buttons
		if (version_compare(VERSION, '1.5.1.3', '>') == true) {
			$this->data['login_button_html'] 			= '<input type="button" onclick="$(\'#login\').submit();" class="button" value="' .$this->data['button_login']. '" />';
			$this->data['register_button_html'] 		= '<input type="button" onclick="checkRegSubmit();" class="button" value="' .$this->data['button_continue'] .'" />';
		} elseif (version_compare(VERSION, '1.5.1.3', '=') == true) {
			$this->data['login_button_html']	 		= '<a onclick="$(\'#login\').submit();" class="button151"><span>' .$this->data['button_login']. '</span></a>';
			$this->data['register_button_html'] 		= '<a onclick="checkRegSubmit();" class="button151"><span>' .$this->data['button_continue'] .'</span></a>';
		} else {
			$this->data['login_button_html']	 		= '<a onclick="$(\'#login\').submit();" class="button"><span>' .$this->data['button_login']. '</span></a>';
			$this->data['register_button_html'] 		= '<a onclick="checkRegSubmit();" class="button"><span>' .$this->data['button_continue'] .'</span></a>';
		}
		
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$this->load->model('account/customer');
		$this->load->model('account/address');

		# Check if Login Form
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['password_login'])) {

			if (isset($this->request->post['email_login']) && isset($this->request->post['password_login']) && $this->validateLogin()) {
				unset($this->session->data['guest']);

				$this->load->model('account/address');

	            $address = $this->model_account_address->getAddress($this->customer->getAddressId());

				//$this->tax->setZone($address['country_id'], $address['zone_id']);

				if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) !== false || strpos($this->request->post['redirect'], HTTPS_SERVER) !== false)) {
					$this->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
				} else {
					$this->redirect($this->url->link('checkout/checkout_two', '', 'SSL'));
				}
			}
    	}

		# Check if Create/Guest Form
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['firstname']) && $this->validate()) {
			// If no password, then guest
			if (strlen($this->request->post['password']) == 0){
				$this->makeGuest();
				if ($this->cart->hasProducts()) {
					$this->redirect($this->url->link('checkout/checkout_two', '', 'SSL'));
				} else {
					$this->redirect($this->url->link('common/home'));
				}
			} else { // Create Account if password entered
				$this->makeRegistered();
				if ($this->cart->hasProducts()) {
					$this->redirect($this->url->link('checkout/checkout_two', '', 'SSL'));
				} else {
					$this->redirect($this->url->link('account/success'));
				}
			}			
    	}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->breadcrumbs = array();

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	);

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('text_cart'),
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->url->link('checkout/checkout_one', '', 'SSL'),
        	'text'      => $this->language->get('text_register'),
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['breadcrumbs'] = $this->document->breadcrumbs;

    	$this->data['heading_title'] = $this->language->get('heading_title');

		if ($this->requireAccount) {
			$this->data['text_create_password'] = $this->language->get('text_create_password');
		} else {
			$this->data['text_create_password'] = $this->language->get('text_create_password') . ' ' . $this->language->get('text_optional');
		}

		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id'), '', 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}

		if (isset($this->session->data['agree'])) {
			$this->data['agree'] = $this->session->data['agree'];
		} else {
			$this->data['agree'] = '';
		}

		$this->data['requireAccount'] = $this->requireAccount;

		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');

		# Get Customer Groups for form
		$this->data['customer_groups'] = array();
		if (file_exists(DIR_SYSTEM . '../catalog/model/account/customer_group.php')) {
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
		}

		if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) !== false || strpos($this->request->post['redirect'], HTTPS_SERVER) !== false)) {
			$this->data['redirect'] = $this->request->post['redirect'];
		} elseif (isset($this->session->data['redirect'])) {
      		$this->data['redirect'] = $this->session->data['redirect'];
			unset($this->session->data['redirect']);
    	} else {
			$this->data['redirect'] = '';
		}

		$errors = array(
			'warning',
			'firstname',
			'lastname',
			'email',
			'telephone',
			'password',
			'confirm',
			'address_1',
			'city',
			'postcode',
			'country',
			'zone',
			'shipping_firstname',
			'shipping_lastname',
			'shipping_email',
			'shipping_telephone',
			'shipping_confirm',
			'shipping_address_1',
			'shipping_city',
			'shipping_postcode',
			'shipping_country',
			'shipping_zone',
			'message',
			'company_id',
			'tax_id',
			'captcha',
			'agree'
		);

		foreach ($errors as $error) {
			if (isset($this->error[$error])) {
				$this->data['error_' . $error] = $this->error[$error];
			} else {
				$this->data['error_' . $error] = '';
			}
		}

    	$this->data['action'] = $this->url->link('checkout/checkout_one', '', 'SSL');

		$cfields = array('firstname','lastname','email','telephone','fax','shipping_indicator','newsletter','password','confirm');
		$pfields = array('firstname','lastname','company','address_1','address_2','postcode','city','country_id','zone_id','company_id','tax_id');
		$sfields = array('firstname','lastname','company','address_1','address_2','postcode','city','country_id','zone_id');

		foreach ($cfields as $field) {
			if (isset($this->request->post[$field])) {
				$this->data[$field] = $this->request->post[$field];
			} elseif (isset($this->session->data['guest'][$field])) {
				$this->data[$field] = $this->session->data['guest'][$field];
			} else {
				$this->data[$field] = '';
			}
		}

		foreach ($pfields as $field) {
			if (isset($this->request->post[$field])) {
				$this->data[$field] = $this->request->post[$field];
			} elseif (isset($this->session->data['guest']['payment'][$field])) {
				$this->data[$field] = $this->session->data['guest']['payment'][$field];
			} else {
				$this->data[$field] = '';
			}
		}

		foreach ($sfields as $field) {
			if (isset($this->request->post['shipping_' . $field])) {
				$this->data['shipping_' . $field] = $this->request->post['shipping_' . $field];
			} elseif (isset($this->session->data['guest']['shipping'][$field])) {
				$this->data['shipping_' . $field] = $this->session->data['guest']['shipping'][$field];
			} elseif ($this->cart->hasShipping() && isset($this->session->data['guest']['payment'][$field])) {
				$this->data['shipping_' . $field] = $this->session->data['guest']['payment'][$field];
			} else {
				$this->data['shipping_' . $field] = '';
			}
		}


		// Special Overrides for some of the the generic foreach fields above
		if (!$this->data['shipping_indicator']) {
			if (isset($this->session->data['guest']['shipping_address'])) {
				$this->data['shipping_indicator'] = $this->session->data['guest']['shipping_address'];
			} else {
				$this->data['shipping_indicator'] = false;
			}
		}

		if ((!isset($this->data['captcha']) || !$this->data['captcha']) && !isset($this->session->data['guest']) && !$this->customer->isLogged()) {
      		$this->data['captcha'] = $this->config->get('uber_checkout_captcha');
    	} else {
			$this->data['captcha'] = false;
		}

		if (!$this->data['newsletter']) {
      		$this->data['newsletter'] = $this->config->get('uber_checkout_newsletter_default');
    	}

    	if (!$this->data['country_id']) {
      		$this->data['country_id'] = $this->config->get('config_country_id');
    	}

		if (!$this->data['zone_id']) {
      		$this->data['zone_id'] = false;
    	}

    	if (!$this->data['shipping_country_id']) {
      		$this->data['shipping_country_id'] = $this->config->get('config_country_id');
    	}

		if (!$this->data['shipping_zone_id']) {
      		$this->data['shipping_zone_id'] = false;
    	}

		$this->data['customer_groups'] = array();
		if (file_exists(DIR_SYSTEM . '../catalog/model/account/customer_group.php')) {
			$this->load->model('account/customer_group');

			if (is_array($this->config->get('config_customer_group_display'))) {
				$customer_groups = $this->model_account_customer_group->getCustomerGroups();

				foreach ($customer_groups as $customer_group) {
					if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
						$this->data['customer_groups'][] = $customer_group;
					}
				}
			}
		}

		if (isset($this->request->post['customer_group_id'])) {
    		$this->data['customer_group_id'] = $this->request->post['customer_group_id'];
		} else {
			$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}


		$this->data['shipping'] = $this->cart->hasShipping();

		$this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());

		$this->load->model('localisation/country');

    	$this->data['countries'] = $this->model_localisation_country->getCountries();

		$this->data['back'] = $this->url->link('checkout/cart');

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/checkout_one.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/checkout_one.tpl';
		} else {
			$this->template = 'default/template/checkout/checkout_one.tpl';
		}

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
  	}

  	// Create as registered using POST vars
  	private function makeRegistered() {
		$this->model_account_customer->addCustomer($this->request->post);

		// Set company_id and tax_id if not submitted
		if (!isset($this->request->post['company_id'])) {
			$this->request->post['company_id'] = '';
		}
		if (!isset($this->request->post['tax_id'])) {
			$this->request->post['tax_id'] = '';
		}

		// Default Shipping Address
		if ($this->config->get('config_tax_customer') == 'shipping') {
			$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
			$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
			$this->session->data['shipping_postcode'] = $this->request->post['postcode'];
		}

		// Default Payment Address
		if ($this->config->get('config_tax_customer') == 'payment') {
			$this->session->data['payment_country_id'] = $this->request->post['country_id'];
			$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
		}

		unset($this->session->data['guest']);
		$this->session->data['account'] = 'register';

		$this->customer->login($this->request->post['email'], $this->request->post['password']);

		if (isset($this->request->post['shipping_indicator'])){
			$shipping_address['customer_id'] = $this->customer->getId();
			$shipping_address['firstname'] = $this->request->post['shipping_firstname'];
			$shipping_address['lastname'] = $this->request->post['shipping_lastname'];
			$shipping_address['company'] = $this->request->post['shipping_company'];
			$shipping_address['address_1'] = $this->request->post['shipping_address_1'];
			$shipping_address['address_2'] = $this->request->post['shipping_address_2'];
			$shipping_address['city'] = $this->request->post['shipping_city'];
			$shipping_address['postcode'] = $this->request->post['shipping_postcode'];
			$shipping_address['country_id'] = $this->request->post['shipping_country_id'];
			$shipping_address['zone_id'] = $this->request->post['shipping_zone_id'];
			$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($shipping_address);
		}

		$this->language->load('mail/customer');

		$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));
		$message = sprintf($this->language->get('text_welcome'), $this->config->get('config_name')) . "\n\n";
		if (!$this->config->get('config_customer_approval')) {
			$message .= $this->language->get('text_login') . "\n";
		} else {
			$message .= $this->language->get('text_approval') . "\n";
		}
		$message .= $this->url->link('account/login', '', 'SSL') . "\n\n";
		$message .= $this->language->get('text_login_details') . "\n";
		$message .= $this->language->get('text_email') . $this->request->post['email']. "\n";
		$message .= $this->language->get('text_password') . $this->request->post['password']. "\n\n";
		$message .= $this->language->get('text_services') . "\n\n";
		$message .= $this->language->get('text_thanks') . "\n";
		$message .= $this->config->get('config_name');

		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');
		$mail->setTo($this->request->post['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject($subject);
		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		//$mail->send();

		// Send to main admin email if account email is enabled
		if ($this->config->get('config_account_mail')) {
			$mail->setTo($this->config->get('config_email'));
			$mail->send();
		}

		// Send to additional alert emails if account email is enabled
		if (!defined('EMAIL_PATTERN')) { define('EMAIL_PATTERN', '/^[^\@]+@.*\.[a-z]{2,6}$/i'); }
		$emails = explode(',', $this->config->get('config_alert_emails'));
		foreach ($emails as $email) {
			if (strlen($email) > 0 && preg_match(EMAIL_PATTERN, $email)) {
				$mail->setTo($email);
				$mail->send();
			}
		}
	}

  	// Create as guest using POST vars
  	private function makeGuest() {

		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

  		$this->session->data['guest']['customer_group_id'] = $customer_group_id;
		$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
		$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
		$this->session->data['guest']['email'] = $this->request->post['email'];
		$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
		$this->session->data['guest']['fax'] = $this->request->post['fax'];

		$this->session->data['guest']['payment']['firstname'] = $this->request->post['firstname'];
		$this->session->data['guest']['payment']['lastname'] = $this->request->post['lastname'];
		$this->session->data['guest']['payment']['company'] = $this->request->post['company'];
		if (isset($this->request->post['company_id'])) {
			$this->session->data['guest']['payment']['company_id'] = $this->request->post['company_id'];
			$this->session->data['guest']['payment']['tax_id'] = $this->request->post['tax_id'];
		}
		$this->session->data['guest']['payment']['address_1'] = $this->request->post['address_1'];
		$this->session->data['guest']['payment']['address_2'] = $this->request->post['address_2'];
		$this->session->data['guest']['payment']['postcode'] = $this->request->post['postcode'];
		$this->session->data['guest']['payment']['city'] = $this->request->post['city'];
		$this->session->data['guest']['payment']['country_id'] = $this->request->post['country_id'];
		$this->session->data['guest']['payment']['zone_id'] = $this->request->post['zone_id'];

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info) {
			$this->session->data['guest']['payment']['country'] = $country_info['name'];
			$this->session->data['guest']['payment']['iso_code_2'] = $country_info['iso_code_2'];
			$this->session->data['guest']['payment']['iso_code_3'] = $country_info['iso_code_3'];
			$this->session->data['guest']['payment']['address_format'] = $country_info['address_format'];
		} else {
			$this->session->data['guest']['payment']['country'] = '';
			$this->session->data['guest']['payment']['iso_code_2'] = '';
			$this->session->data['guest']['payment']['iso_code_3'] = '';
			$this->session->data['guest']['payment']['address_format'] = '';
		}

		$this->load->model('localisation/zone');

		$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

		if ($zone_info) {
			$this->session->data['guest']['payment']['zone'] = $zone_info['name'];
			$this->session->data['guest']['payment']['zone_code'] = $zone_info['code'];
		} else {
			$this->session->data['guest']['payment']['zone'] = '';
			$this->session->data['guest']['payment']['zone_code'] = '';
		}

		if (!empty($this->request->post['shipping_indicator'])) {
			$this->session->data['guest']['shipping_address'] = true;
		} else {
			$this->session->data['guest']['shipping_address'] = false;
		}

		// Default Payment Address
		$this->session->data['payment_country_id'] = $this->request->post['country_id'];
		$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];

		if ($this->session->data['guest']['shipping_address']) {
			$this->session->data['guest']['shipping']['firstname'] = $this->request->post['shipping_firstname'];
			$this->session->data['guest']['shipping']['lastname'] = $this->request->post['shipping_lastname'];
			$this->session->data['guest']['shipping']['company'] = $this->request->post['shipping_company'];
			$this->session->data['guest']['shipping']['address_1'] = $this->request->post['shipping_address_1'];
			$this->session->data['guest']['shipping']['address_2'] = $this->request->post['shipping_address_2'];
			$this->session->data['guest']['shipping']['postcode'] = $this->request->post['shipping_postcode'];
			$this->session->data['guest']['shipping']['city'] = $this->request->post['shipping_city'];
			$this->session->data['guest']['shipping']['country_id'] = $this->request->post['shipping_country_id'];
			$this->session->data['guest']['shipping']['zone_id'] = $this->request->post['shipping_zone_id'];

			$country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_country_id']);

			if ($country_info) {
				$this->session->data['guest']['shipping']['country'] = $country_info['name'];
				$this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
				$this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
				$this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
			} else {
				$this->session->data['guest']['shipping']['country'] = '';
				$this->session->data['guest']['shipping']['iso_code_2'] = '';
				$this->session->data['guest']['shipping']['iso_code_3'] = '';
				$this->session->data['guest']['shipping']['address_format'] = '';
			}

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping_zone_id']);

			if ($zone_info) {
				$this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
				$this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];
			} else {
				$this->session->data['guest']['shipping']['zone'] = '';
				$this->session->data['guest']['shipping']['zone_code'] = '';
			}

			// Default Shipping Address
			$this->session->data['shipping_country_id'] = $this->request->post['shipping_country_id'];
			$this->session->data['shipping_zone_id'] = $this->request->post['shipping_zone_id'];
			$this->session->data['shipping_postcode'] = $this->request->post['shipping_postcode'];
		}

		$this->session->data['account'] = 'guest';

		unset($this->session->data['shipping_method']);
		unset($this->session->data['shipping_methods']);
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
	}

  	private function validate() {

		if ($this->config->get('uber_checkout_captcha') && !isset($this->session->data['guest']) && !$this->customer->isLogged()) {
			if (empty($this->session->data['uc_captcha']) || ($this->session->data['uc_captcha'] != $this->request->post['captcha'])) {
				$this->error['captcha'] = $this->language->get('error_captcha');
			}
		}

    	if ((strlen(utf8_decode($this->request->post['firstname'])) < 1) || (strlen(utf8_decode($this->request->post['firstname'])) > 32)) {
      		$this->error['firstname'] = $this->language->get('error_firstname');
		}

    	if ((strlen(utf8_decode($this->request->post['lastname'])) < 1) || (strlen(utf8_decode($this->request->post['lastname'])) > 32)) {
      		$this->error['lastname'] = $this->language->get('error_lastname');
    	}

		// RegEx
		if (!defined('EMAIL_PATTERN')) {
			define('EMAIL_PATTERN', '/^[^\@]+@.*\.[a-z]{2,6}$/i');
		}

    	if (!preg_match(EMAIL_PATTERN, $this->request->post['email'])) {
      		$this->error['email'] = $this->language->get('error_email');
    	}

		if (!empty($this->request->post['password']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
      		$this->error['warning'] = $this->language->get('error_exists');
    	}

    	if ((strlen(utf8_decode($this->request->post['telephone'])) < 3) || (strlen(utf8_decode($this->request->post['telephone'])) > 32)) {
      		$this->error['telephone'] = $this->language->get('error_telephone');
		}

		// Customer Group
		if (file_exists(DIR_SYSTEM . '../catalog/model/account/customer_group.php')) {
			$this->load->model('account/customer_group');


			if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}

			$customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

			if ($customer_group) {
				// Company ID
				if ($customer_group['company_id_display'] && $customer_group['company_id_required'] && !$this->request->post['company_id']) {
					$this->error['company_id'] = $this->language->get('error_company_id');
				}

				// Tax ID
				if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && !$this->request->post['tax_id']) {
					$this->error['tax_id'] = $this->language->get('error_tax_id');
				}
			}
		}

    	if ((strlen(utf8_decode($this->request->post['address_1'])) < 3) || (strlen(utf8_decode($this->request->post['address_1'])) > 128)) {
      		$this->error['address_1'] = $this->language->get('error_address_1');
		}

    	if ((strlen(utf8_decode($this->request->post['city'])) < 2) || (strlen(utf8_decode($this->request->post['city'])) > 128)) {
      		$this->error['city'] = $this->language->get('error_city');
    	}

		if ((strlen(utf8_decode($this->request->post['postcode'])) < 2) || (strlen(utf8_decode($this->request->post['postcode'])) > 10 ))  {
      		$this->error['postcode'] = $this->language->get('error_postcode');
    	}

    	if ($this->request->post['country_id'] == 'FALSE') {
      		$this->error['country'] = $this->language->get('error_country');
    	}

    	if ($this->request->post['zone_id'] == 'FALSE') {
      		$this->error['zone'] = $this->language->get('error_zone');
    	}

		if (!$this->config->get('config_guest_checkout') || $this->config->get('config_customer_price') || $this->cart->hasDownload()) {
    		if ((strlen(utf8_decode($this->request->post['password'])) < 4) || (strlen(utf8_decode($this->request->post['password'])) > 20)) {
      			$this->error['password'] = $this->language->get('error_password');
    		}
		}

		if ($this->request->post['confirm'] != $this->request->post['password']) {
      		$this->error['confirm'] = $this->language->get('error_confirm');
    	}

		if (isset($this->request->post['shipping_indicator'])) {
			if ((strlen(utf8_decode($this->request->post['shipping_firstname'])) < 1) || (strlen(utf8_decode($this->request->post['shipping_firstname'])) > 32)) {
				$this->error['shipping_firstname'] = $this->language->get('error_firstname');
			}

			if ((strlen(utf8_decode($this->request->post['shipping_lastname'])) < 1) || (strlen(utf8_decode($this->request->post['shipping_lastname'])) > 32)) {
				$this->error['shipping_lastname'] = $this->language->get('error_lastname');
			}

			if ((strlen(utf8_decode($this->request->post['shipping_address_1'])) < 3) || (strlen(utf8_decode($this->request->post['shipping_address_1'])) > 64)) {
				$this->error['shipping_address_1'] = $this->language->get('error_address_1');
			}

			if ((strlen(utf8_decode($this->request->post['shipping_city'])) < 3) || (strlen(utf8_decode($this->request->post['shipping_city'])) > 32)) {
				$this->error['shipping_city'] = $this->language->get('error_city');
			}

			if ($this->request->post['shipping_country_id'] == 'FALSE') {
				$this->error['shipping_country'] = $this->language->get('error_country');
			}

			if ($this->request->post['shipping_zone_id'] == 'FALSE') {
				$this->error['shipping_zone'] = $this->language->get('error_zone');
			}
		}

		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info && !isset($this->request->post['agree'])) {
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}

    	if (!$this->error) {
      		return TRUE;
    	} else {
      		return FALSE;
    	}
  	}

  	private function validateLogin() {
    	if (!$this->customer->login($this->request->post['email_login'], $this->request->post['password_login'])) {
      		$this->error['warning'] = $this->language->get('error_login');
    	}

    	if (!$this->error) {
      		return TRUE;
    	} else {
      		return FALSE;
    	}
  	}

	public function captcha() {
		$this->load->library('captcha');

		$captcha = new Captcha();

		$this->session->data['uc_captcha'] = $captcha->getCode();

		$captcha->showImage();
	}

  	public function zone() {

		$output = '<option value="FALSE">' . $this->language->get('text_select') . '</option>';

		$this->load->model('localisation/zone');

		if (isset($this->request->get['shipping_country_id'])) {
			$country_id = $this->request->get['shipping_country_id'];
		} else {
			$country_id = $this->request->get['country_id'];
		}

    	$results = $this->model_localisation_zone->getZonesByCountryId($country_id);

      	foreach ($results as $result) {
        	$output .= '<option value="' . $result['zone_id'] . '"';

	    	if (isset($this->request->get['zone_id']) && ($this->request->get['zone_id'] == $result['zone_id'])) {
	      		$output .= ' selected="selected"';
	    	}

	    	$output .= '>' . $result['name'] . '</option>';
    	}

		if (!$results) {
			if (!$this->request->get['zone_id']) {
		  		$output .= '<option value="0" selected="selected">' . $this->language->get('text_none') . '</option>';
			} else {
				$output .= '<option value="0">' . $this->language->get('text_none') . '</option>';
			}
		}

		$this->response->setOutput($output, $this->config->get('config_compression'));
  	}

	function generatePassword($length = 8) {
		$password = "";
		$possible = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#";
		$i = 0;
		while ($i < $length) {
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

    	$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->setOutput(json_encode($json));
	}
}
?>