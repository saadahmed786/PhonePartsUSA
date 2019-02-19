<?php
class ControllerAccountEdit extends Controller {
	private $error = array();

	public function index() {

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');

			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->language->load('account/edit');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/customer');
		$this->load->model('account/address');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/country');
		$this->data['infos'] = $this->model_account_address->getContactInformations();
		// print_r($this->request->post);exit;
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			// Updating email address in IMP
			$updateEmail = array(
				'oldEmail' => $this->customer->getEmail(),
				'email' => $this->request->post['email'],
				'customer_id' => $_SESSION['customer_id']
				);

			$this->model_account_customer->updateEmailImp($updateEmail);
			
			// Updating Customer Details.
			if($this->request->post['email']=='zamantest22@mailinator.com')
			{
				// print_r($this->request->post);exit;
			}


				$this->model_account_customer->editCustomerNew($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('account/account', '', 'SSL'));
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
			'text'      => $this->language->get('text_edit'),
			'href'      => $this->url->link('account/edit', '', 'SSL'),       	
			'separator' => $this->language->get('text_separator')
			);
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_your_details'] = $this->language->get('text_your_details');

		$this->data['entry_firstname'] = $this->language->get('entry_firstname');
		$this->data['entry_lastname'] = $this->language->get('entry_lastname');
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_telephone'] = $this->language->get('entry_telephone');
		$this->data['entry_fax'] = $this->language->get('entry_fax');

		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_back'] = $this->language->get('button_back');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}
		
		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}	
		if (isset($this->error['repairdesk_token'])) {
			$this->data['error_repairdesk_token'] = $this->error['repairdesk_token'];
		} else {
			$this->data['error_repairdesk_token'] = '';
		}	
		
		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}	

		$this->data['action'] = $this->url->link('account/edit', '', 'SSL');

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}

		if (isset($this->request->post['firstname'])) {
			$this->data['firstname'] = $this->request->post['firstname'];
		} elseif (isset($customer_info)) {
			$this->data['firstname'] = $customer_info['firstname'];
		} else {
			$this->data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$this->data['lastname'] = $this->request->post['lastname'];
		} elseif (isset($customer_info)) {
			$this->data['lastname'] = $customer_info['lastname'];
		} else {
			$this->data['lastname'] = '';
		}

		if (isset($this->request->post['repairdesk_token'])) {
			$this->data['repairdesk_token'] = $this->request->post['repairdesk_token'];
		} elseif (isset($customer_info) && $customer_info['repairdesk_token']!='') {
			$this->data['repairdesk_token'] = $customer_info['repairdesk_token'];
		} else {
			$this->data['repairdesk_token'] = $this->getRandomToken();
		}

		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];
		} elseif (isset($customer_info)) {
			$this->data['email'] = $customer_info['email'];
		} else {
			$this->data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$this->data['telephone'] = $this->request->post['telephone'];
		} elseif (isset($customer_info)) {
			$this->data['telephone'] = $customer_info['telephone'];
		} else {
			$this->data['telephone'] = '';
		}

		if (isset($this->request->post['fax'])) {
			$this->data['fax'] = $this->request->post['fax'];
		} elseif (isset($customer_info)) {
			$this->data['fax'] = $customer_info['fax'];
		} else {
			$this->data['fax'] = '';
		}

		if (isset($this->request->post['business_name'])) {
			$this->data['business_name'] = $this->request->post['business_name'];
		} elseif (isset($customer_info)) {
			$this->data['business_name'] = $customer_info['business_name'];
		} else {
			$this->data['business_name'] = '';
		}

		if (isset($customer_info)) {
			$this->data['phones'] = unserialize($customer_info['phones']);
		} else {
			$this->data['phones'] = '';
		}
		//print_r($this->data['phones']);exit;

		if($this->session->data['temp_theme'] != 'ppusa2.0'){
			if (isset($this->request->post['phoneselector'])) {
				$this->data['phoneselector'] = $this->request->post['phoneselector'];
			} else {
				$this->data['phoneselector'] = array('');
			}
			if (isset($this->request->post['phonenumber'])) {
				$this->data['phonenumber'] = $this->request->post['phonenumber'];
			} else {
				$this->data['phonenumber'] = array('');
			}
			if (isset($this->request->post['password'])) {
				$this->data['password'] = $this->request->post['password'];
			} else {
				$this->data['password'] = '';
			}
		}
		//print_r($this->data['business_name']);exit;
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		if($this->error)
		{
			$error_msg = '';
			foreach($this->error as $key=> $error)
			{
				$error_msg.=$error."<br>"; 
			}
			// echo $error_msg;exit;
			$this->session->data['account_error']=$error_msg;

			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}


		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/edit.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/edit.tpl';
		} else {
			$this->template = 'default/template/account/edit.tpl';
		}

		$this->data['addresses'] = $this->model_account_address->getAddresses();
		$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId(223);
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		
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

	private function validate() {
		if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}
		if($this->request->post['password']!=$this->request->post['confirm_password'])
		{
			$this->error['password'] = 'Passwords do not match';
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ((utf8_strlen($this->request->post['repairdesk_token']) > 1) && (utf8_strlen($this->request->post['repairdesk_token']) < 5)) {
				// $this->error['telephone'] = $this->language->get('error_telephone');
			$this->error['repairdesk_token'] = 'RepairDesk API Token seems to be very short.';
			}

		if($this->session->data['temp_theme'] != 'ppusa2.0'){
			if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}
		if($this->session->data['temp_theme'] != 'ppusa2.0'){
			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				// $this->error['telephone'] = $this->language->get('error_telephone');
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function getRandomToken()
	{


	    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 32; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;

	}
	
	public function deleteAddress () {
		$this->load->model('account/address');
		$this->model_account_address->deleteAddress((int)$_POST['address_id']);
		echo json_encode(array('success' => 1));
		exit;
	}
	public function setDefault()
	{
		$this->load->model('account/address');
		if ($_POST['address_id']) {
			$this->model_account_address->setDefault((int)$_POST['address_id']);
		}
		echo json_encode(array('success' => 1));
		exit;
	}
	public function updateAddress()
	{
		$this->load->model('account/address');
		$this->model_account_address->editAddressNew($this->request->post);
		$address = $this->model_account_address->getAddress($this->request->post['address_id']);
		$address['success'] = 1;
		echo json_encode($address);
		exit;
	}
}
?>