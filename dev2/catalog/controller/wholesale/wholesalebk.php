<?php 
class ControllerWholesaleWholesale extends Controller {

	private $error = array();

	public function index() {
		$this->language->load('wholesale/wholesale');
		
		$this->load->model('catalog/wholesale');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_catalog_wholesale->addWholeSaleAccount($this->request->post);
			
			unset($this->request->post['emailVerify'], $this->request->post['submit']);
			$this->request->post['business_license'] = '<a href="'. HTTP_IMAGE . $this->request->post['business_license'] .'">Business License</a>';

			$message = "<p>Thank you for your submission! We will respond to your inquiry as soon as possible. A copy of your responses is included below. Thanks again!</p>";
			$message .= "<table><tbody>";
			foreach ($this->request->post as $key => $value) {
				if ($value == '1') {
					$value = 'Yes';
				}
				if ($value) {
					$message .= '<tr>';
					$message .= '<td>' . str_replace('_', ' ', $key) . '</td>';
					$message .= '<td>' . $value . '</td>';
					$message .= '</tr>';
				}
			}
			$message .= "</tbody></table>";
			$message .= '<p>PhonepartsUSA.com</p>';
			
			$mail = new Mail();

			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');            
			$mail->setTo($this->request->post['email']);
			$mail->setFrom("noreply@phonepartsusa.com");
			$mail->setSender("noreply@phonepartsusa.com");
			$mail->setSubject("Thank you submitting your PhonepartsUSA.com Wholesale Application");
			$mail->setHtml($message);

			$mail->send();

			$mail = new Mail();

			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');            
			$mail->setTo("sales@phonepartsusa.com");
			$mail->setFrom("noreply@phonepartsusa.com");
			$mail->setSender("noreply@phonepartsusa.com");
			$mail->setSubject("Thank you submitting your PhonepartsUSA.com Wholesale Application");
			$mail->setHtml($message);

			$mail->send();

			$this->redirect($this->url->link('wholesale/wholesale/thanks'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$wholesaleContent = $this->model_catalog_wholesale->gettext();

		$this->data['text_1'] = $wholesaleContent['text_1'];
		$this->data['text_2'] = $wholesaleContent['text_2'];
		$this->data['text_3'] = $wholesaleContent['text_3'];

		$this->data['breadcrumbs'] = array();
		
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
			'href'      => $this->url->link('wholesale/wholesale', '', 'SSL'),      	
			'separator' => $this->language->get('text_separator')
			);

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if ($this->error) {
			$this->data['error_form'] = 'Please complete the fields highlighted in red!';
		} else {
			$this->data['error_form'] = '';
		}		

		if (isset($this->error['first_name'])) {
			$this->data['error_first_name'] = $this->error['first_name'];
		} else {
			$this->data['error_first_name'] = '';
		}

		if (isset($this->error['last_name'])) {
			$this->data['error_last_name'] = $this->error['last_name'];
		} else {
			$this->data['error_last_name'] = '';
		}

		if (isset($this->error['office'])) {
			$this->data['error_office'] = $this->error['office'];
		} else {
			$this->data['error_office'] = '';
		}

		if (isset($this->error['mobile'])) {
			$this->data['error_mobile'] = $this->error['mobile'];
		} else {
			$this->data['error_mobile'] = '';
		}

		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}

		if (isset($this->error['personal_email'])) {
			$this->data['error_personal_email'] = $this->error['personal_email'];
		} else {
			$this->data['error_personal_email'] = '';
		}

		if (isset($this->error['position'])) {
			$this->data['error_position'] = $this->error['position'];
		} else {
			$this->data['error_position'] = '';
		}

		if (isset($this->error['company_name'])) {
			$this->data['error_company_name'] = $this->error['company_name'];
		} else {
			$this->data['error_company_name'] = '';
		}

		if (isset($this->error['address'])) {
			$this->data['error_address'] = $this->error['address'];
		} else {
			$this->data['error_address'] = '';
		}

		if (isset($this->error['suite'])) {
			$this->data['error_suite'] = $this->error['suite'];
		} else {
			$this->data['error_suite'] = '';
		}

		if (isset($this->error['zip_code'])) {
			$this->data['error_zip_code'] = $this->error['zip_code'];
		} else {
			$this->data['error_zip_code'] = '';
		}

		if (isset($this->error['city'])) {
			$this->data['error_city'] = $this->error['city'];
		} else {
			$this->data['error_city'] = '';
		}

		if (isset($this->error['state'])) {
			$this->data['error_state'] = $this->error['state'];
		} else {
			$this->data['error_state'] = '';
		}

		if (isset($this->error['retail_point'])) {
			$this->data['error_retail_point'] = $this->error['retail_point'];
		} else {
			$this->data['error_retail_point'] = '';
		}

		if (isset($this->error['repairs'])) {
			$this->data['error_repairs'] = $this->error['repairs'];
		} else {
			$this->data['error_repairs'] = '';
		}

		if (isset($this->error['intrested'])) {
			$this->data['error_intrested'] = $this->error['intrested'];
		} else {
			$this->data['error_intrested'] = '';
		}

		if (isset($this->error['business_license'])) {
			$this->data['error_business_license'] = $this->error['business_license'];
		} else {
			$this->data['error_business_license'] = '';
		}

		if (isset($this->error['license_no'])) {
			$this->data['error_license_no'] = $this->error['license_no'];
		} else {
			$this->data['error_license_no'] = '';
		}

		if (isset($this->error['emailVerify'])) {
			$this->data['error_emailVerify'] = $this->error['emailVerify'];
		} else {
			$this->data['error_emailVerify'] = '';
		}

		//Action
		$this->data['action'] = $this->url->link('wholesale/wholesale', '', 'SSL');



		//Setting POST Data back

		if (isset($this->request->post['first_name'])) {
			$this->data['first_name'] = $this->request->post['first_name'];
		} else {
			$this->data['first_name'] = ($this->customer->isLogged()) ? $this->customer->getFirstName(): '';
		}

		if (isset($this->request->post['last_name'])) {
			$this->data['last_name'] = $this->request->post['last_name'];
		} else {
			$this->data['last_name'] = ($this->customer->isLogged()) ? $this->customer->getLastName(): '';
		}

		if (isset($this->request->post['office'])) {
			$this->data['office'] = $this->request->post['office'];
		} else {
			$this->data['office'] = '';
		}

		if (isset($this->request->post['mobile'])) {
			$this->data['mobile'] = $this->request->post['mobile'];
		} else {
			$this->data['mobile'] = '';
		}

		if (isset($this->request->post['email'])) {
			$this->data['email'] = ($this->customer->isLogged()) ? $this->customer->getEmail(): $this->request->post['email'];
		} else {
			$this->data['email'] = ($this->customer->isLogged()) ? $this->customer->getEmail(): '';
		}

		if (isset($this->request->post['position'])) {
			$this->data['position'] = $this->request->post['position'];
		} else {
			$this->data['position'] = '';
		}

		if (isset($this->request->post['company_name'])) {
			$this->data['company_name'] = $this->request->post['company_name'];
		} else {
			$this->data['company_name'] = '';
		}

		if (isset($this->request->post['address'])) {
			$this->data['address'] = $this->request->post['address'];
		} else {
			$this->data['address'] = '';
		}

		if (isset($this->request->post['suite'])) {
			$this->data['suite'] = $this->request->post['suite'];
		} else {
			$this->data['suite'] = '';
		}

		if (isset($this->request->post['zip_code'])) {
			$this->data['zip_code'] = $this->request->post['zip_code'];
		} else {
			$this->data['zip_code'] = '';
		}

		if (isset($this->request->post['city'])) {
			$this->data['city'] = $this->request->post['city'];
		} else {
			$this->data['city'] = '';
		}

		if (isset($this->request->post['state'])) {
			$this->data['state'] = $this->request->post['state'];
		} else {
			$this->data['state'] = '';
		}

		if (isset($this->request->post['retail_point'])) {
			$this->data['retail_point'] = $this->request->post['retail_point'];
		} else {
			$this->data['retail_point'] = '';
		}

		if (isset($this->request->post['repairs'])) {
			$this->data['repairs'] = $this->request->post['repairs'];
		} else {
			$this->data['repairs'] = '';
		}

		if (isset($this->request->post['intrested'])) {
			$this->data['intrested'] = $this->request->post['intrested'];
		} else {
			$this->data['intrested'] = array('');
		}

		if (isset($this->request->post['business_license'])) {
			$this->data['business_license'] = $this->request->post['business_license'];
		} else {
			$this->data['business_license'] = '';
		}

		if (isset($this->request->post['license_no'])) {
			$this->data['license_no'] = $this->request->post['license_no'];
		} else {
			$this->data['license_no'] = '';
		}

		if (isset($this->request->post['emailVerify'])) {
			$this->data['emailVerify'] = $this->request->post['emailVerify'];
		} else {
			$this->data['emailVerify'] = '';
		}


		//loged
		$this->data['logged'] = $this->customer->isLogged();

      	//States

		$this->data['states'] = $this->model_catalog_wholesale->getState('USA');


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/wholesale/wholesale.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/wholesale/wholesale.tpl';
		} else {
			$this->template = 'default/template/wholesale/wholesale.tpl';
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

	private function validate() {
		
		if ((utf8_strlen($this->request->post['first_name']) < 1) || (utf8_strlen($this->request->post['first_name']) > 32)) {
			$this->error['first_name'] = $this->language->get('error_first_name');
		}

		if ((utf8_strlen($this->request->post['last_name']) < 1) || (utf8_strlen($this->request->post['last_name']) > 32)) {
			$this->error['last_name'] = $this->language->get('error_last_name');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ($this->model_catalog_wholesale->getTotalRequestByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		if ((utf8_strlen($this->request->post['personal_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['personal_email'])) {
			$this->error['personal_email'] = $this->language->get('error_personal_email');
		}
		
		if ((utf8_strlen($this->request->post['office']) < 6) || (utf8_strlen($this->request->post['office']) > 32)) {
			$this->error['office'] = $this->language->get('error_office');
		}

		// if ((utf8_strlen($this->request->post['mobile']) < 6) || (utf8_strlen($this->request->post['mobile']) > 32)) {
		// 	$this->error['mobile'] = $this->language->get('error_mobile');
		// }
		
		if ((utf8_strlen($this->request->post['position']) < 3) || (utf8_strlen($this->request->post['position']) > 50)) {
			$this->error['position'] = $this->language->get('error_position');
		}

		if ((utf8_strlen($this->request->post['company_name']) < 5) || (utf8_strlen($this->request->post['company_name']) > 96)) {
			$this->error['company_name'] = $this->language->get('error_company_name');
		}
		
		if ((utf8_strlen($this->request->post['address']) < 3) || (utf8_strlen($this->request->post['address']) > 255)) {
			$this->error['address'] = $this->language->get('error_address');
		}

		if ((utf8_strlen($this->request->post['suite']) < 1) || (utf8_strlen($this->request->post['suite']) > 20)) {
			$this->error['suite'] = $this->language->get('error_suite');
		}

		if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
			$this->error['city'] = $this->language->get('error_city');
		}

		if ($this->request->post['zip_code'] == '') {
			$this->error['zip_code'] = $this->language->get('error_zip_code');
		}

		if ($this->request->post['state'] == '') {
			$this->error['state'] = $this->language->get('error_state');
		}

		if (!isset($this->request->post['intrested'])) {
			$this->error['intrested'] = $this->language->get('error_intrested');
		}

		if ($this->request->post['business_license'] != '' && !file_exists(DIR_IMAGE . $this->request->post['business_license'])) {
			$this->error['business_license'] = $this->language->get('error_business_license');
		}

		if (!isset($this->request->post['retail_point'])) {
			$this->error['retail_point'] = $this->language->get('error_retail_point');
		}

		if ((utf8_strlen($this->request->post['city']) != '') && ((utf8_strlen($this->request->post['license_no']) < 6) || (utf8_strlen($this->request->post['license_no']) > 100))) {
			$this->error['license_no'] = $this->language->get('error_license_no');
		}

		if ($this->request->post['emailVerify'] != 'success' || $this->request->post['emailVerify'] == '') {
			$this->error['emailVerify'] = $this->language->get('error_emailVerify');
		}

		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function uplaodFile() {
		$allowed = array('png', 'tiff', 'tif', 'jpeg', 'jpg', 'doc', 'docx', 'xls', 'xlsx', 'pdf');
		if ($_FILES['file']['tmp_name']) {
			$uniqid = uniqid();
			$name = explode(".", $_FILES['file']['name']);
			$ext = end($name);
			$fileName = $uniqid . '.' . $ext;
			$destination = DIR_IMAGE . $uniqid . ".$ext";
			$file = $_FILES['file']['tmp_name'];
			if (in_array($ext, $allowed)) {
				if (move_uploaded_file($file, $destination)) {
					$array = array('success'=> 1,'msg' => HTTP_IMAGE . $fileName, 'file' => $fileName);
					echo json_encode($array);
					exit;
				}
			} else {
				$array = array('error'=> 1, 'msg' => 'This file is now allowed');
				echo json_encode($array);
				exit;
			}
		} else {
			$array = array('error'=> 1, 'msg' => 'Please Select File');
			echo json_encode($array);
			exit;
		}
	}

	public function removeFile () {
		if ($_POST['file']) {
			unlink(DIR_IMAGE . $_POST['file']);
			$array = array('success'=> 1,'msg' => 'File Removed');
			echo json_encode($array);
			exit;
		} else {
			$array = array('success'=> 1,'msg' => 'File Removed');
			echo json_encode($array);
		}
	}

	public function verifyEmail(){
		$this->load->model('account/customer');
		if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$array = array('success'=> 1, 'msg' => '<span style="color: green;">Verified</span>');
			echo json_encode($array);
			exit;
		} else {
			$array = array('error'=> 1,'msg' => 'Please create an account by pressing the "Create Account" button above.');
			echo json_encode($array);
			exit;
		}
	}

	public function thanks() {

		$this->data['heading_title'] = 'Thank You!';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/wholesale/thanks.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/wholesale/thanks.tpl';
		} else {
			$this->template = 'default/template/wholesale/thanks.tpl';
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
	
}
?>