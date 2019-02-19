<?php
//==============================================================================
// Form Builder v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

class ControllerModuleFormBuilder extends Controller {
	private $type = 'module';
	private $name = 'form_builder';
	
    public function __call($method, $args) {
        $module = array(
			'form_id'	=> str_replace('form_', '', $method),
			'box'		=> '',
			'heading'	=> '',
			'content'	=> '',
			'css'		=> '',
			'stores'	=> array($this->config->get('config_store_id'))
		);
		$this->index($module);
    }
	
	protected function index($module) {
		$this->data['type'] = $this->type;
		$this->data['name'] = $this->name;
		
		$this->data['module'] = $module;
		if (!in_array($this->config->get('config_store_id'), $module['stores'])) return;
		
		$form_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "form WHERE form_id = " . (int)$module['form_id']);
		$this->data['form'] = $form_query->row;
		foreach ($this->data['form'] as &$data) {
			if (is_string($data) && strpos($data, 'a:') === 0) $data = unserialize($data);
		}
		if (empty($this->data['form']['status'])) return;
		
		$this->data['language'] = $this->session->data['language'];
		
		$template = (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/' . $this->type . '/' . $this->name . '.tpl')) ? (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) : 'default';
		$this->template = $template . '/template/' . $this->type . '/' . $this->name . '.tpl';
		
		$this->render();
	}
	
	public function replaceShortcodes($text) {
		foreach ($this->request->get as $key => $value) {
			$text = str_replace('[' . $key . ']', $value, $text);
			if ($key == 'product_id') {
				$this->load->model('catalog/product');
				$product_info = $this->model_catalog_product->getProduct($value);
				foreach ($product_info as $k => $v) {
					$text = str_replace('[product_' . $k . ']', $v, $text);
				}
			}
		}
		$text = preg_replace('/\[.*?\]/', '', $text);
		return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	}
	
	public function validatePassword() {
		if (empty($this->request->get['form_id']) || empty($this->request->get['password'])) return;
		
		$form_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "form WHERE form_id = " . (int)$this->request->get['form_id']);
		$form = $form_query->row;
		foreach ($form as &$data) {
			if (is_string($data) && strpos($data, 'a:') === 0) $data = unserialize($data);
		}
		
		if ($this->request->get['password'] == $form['password']['password']) {
			$this->session->data['form' . $this->request->get['form_id'] . '_password'] = $form['password']['password'];
			echo 'success';
		}
	}
	
	public function captcha() {
		$this->load->library('captcha');
		$captcha = new Captcha();
		$this->session->data[$this->request->get['key']] = $captcha->getCode();
		$captcha->showImage();
	}
	
	public function validateCaptcha() {
		if (empty($this->request->get['key']) ||
			empty($this->request->get['value']) ||
			empty($this->session->data[$this->request->get['key']]) ||
			$this->session->data[$this->request->get['key']] != $this->request->get['value']
		) {
			echo 'error';
		}
	}
	
	public function upload() {
		$json = array();
		
		if (!empty($this->request->files['file']['name'])) {
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));
			if ((strlen($filename) < 3) || (strlen($filename) > 128)) {
				$json['error'] = 'file_name';
			}
			$allowed = explode(',', preg_replace('/\s+/', '', $this->request->post['extensions']));
			if (!in_array(strrchr($filename, '.'), $allowed)) {
				$json['error'] = 'file_ext';
       		}
			if ($this->request->files['file']['size'] > $this->request->post['filesize']*1000) {
				$json['error'] = 'file_size';
			}
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = 'file_upload';
			}
		} else {
			$json['error'] = 'file_upload';
		}
		
		if (empty($json)) {
			if (is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
				$file = basename($filename) . '.' . md5(mt_rand());
				move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $file);
				$json['file'] = $this->encryption->encrypt($file);
				$json['name'] = pathinfo($file, PATHINFO_FILENAME);
			}
			$json['success'] = 'success';
		}	
		
		$this->response->setOutput(json_encode($json));		
	}
	
	public function submit() {
		$form_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "form WHERE form_id = " . (int)$this->request->get['form_id']);
		$form = $form_query->row;
		foreach ($form as &$data) {
			if (is_string($data) && strpos($data, 'a:') === 0) $data = unserialize($data);
		}
		$language = $this->session->data['language'];
		
		if (!empty($form['password']['password']) && (empty($this->session->data['form' . $form['form_id'] . '_password']) || $form['password']['password'] != $this->session->data['form' . $form['form_id'] . '_password'])) {
			echo 'Password Error';
			return;
		}
		unset($this->session->data['form' . $form['form_id'] . '_password']);
		
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');
		
		$responses = array();
		$customer_emails = array();
		$files = array();
		$admin_response_list = '';
		$customer_response_list = '';
		
		foreach ($form['fields'] as $field) {
			if (!isset($field['key'])) continue;
			$response = (isset($this->request->post[$field['key']])) ? $this->request->post[$field['key']] : '';
			$responses[$field['key']] = $response;
			
			if ($field['type'] == 'email' && !empty($response)) {
				$customer_emails[] = trim($response);
			} elseif ($field['type'] == 'file' && !empty($response)) {
				$file = $this->encryption->decrypt($response);
				$responses[$field['key']] = $file;
				$response = pathinfo($file, PATHINFO_FILENAME);
				if (file_exists(DIR_DOWNLOAD . $file)) {
					copy(DIR_DOWNLOAD . $file, DIR_CACHE . $response);
					$mail->addAttachment(DIR_CACHE . $response);
					$files[] = DIR_CACHE . $response;
				}
			}
			
			$admin_response_list .= '<tr><td style="white-space: nowrap"><strong>' . strip_tags(html_entity_decode($field['name'][$language], ENT_QUOTES, 'UTF-8')) . '</strong></td> <td style="white-space: nowrap">' . (is_array($response) ? implode(', ', $response) : $response) . '</td></tr>' . "\n";
			if ($field['type'] != 'hidden' || !empty($field['email'])) {
				$customer_response_list .= '<tr><td style="white-space: nowrap"><strong>' . strip_tags(html_entity_decode($field['name'][$language], ENT_QUOTES, 'UTF-8')) . '</strong></td> <td style="white-space: nowrap">' . (is_array($response) ? implode(', ', $response) : $response) . '</td></tr>' . "\n";
			}
		}
		
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "form_response SET
			form_id = " . (int)$this->request->get['form_id'] . ",
			answered = 0,
			customer_id = " . (int)$this->customer->getId() . ",
			date_added = NOW(),
			ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "',
			response = '" . $this->db->escape(serialize($responses)) . "'
		");
		
		$replace = array(
			'[store_name]',
			'[store_url]',
			'[store_owner]',
			'[store_address]',
			'[store_email]',
			'[store_telephone]',
			'[store_fax]',
			'[current_date]',
			'[current_time]',
			'[form_name]'
		);
		$with = array(
			$this->config->get('config_title'),
			($this->config->get('config_url') ? $this->config->get('config_url') : HTTP_SERVER),
			$this->config->get('config_name'),
			$this->config->get('config_address'),
			$this->config->get('config_email'),
			$this->config->get('config_telephone'),
			$this->config->get('config_fax'),
			date($this->language->get('date_format_short')),
			date($this->language->get('time_format')),
			$form['name'][$language]
		);
		
		$admin_emails = array_map('trim', explode(',', $form['email']['admin_email']));
		$html = html_entity_decode($form['email']['admin_message'][$language], ENT_QUOTES, 'UTF-8');
		$html = str_replace($replace, $with, $html);
		$html = str_replace('[form_responses]', '<table style="width: 1px">' . $admin_response_list . '</table>', $html);
		
		$mail->setFrom(!empty($customer_emails) ? $customer_emails[0] : $admin_emails[0]);
		$mail->setSender(!empty($customer_emails) ? $customer_emails[0] : str_replace(array(',', '&'), array('', 'and'), html_entity_decode($this->config->get('config_title'), ENT_QUOTES, 'UTF-8')));
		$mail->setSubject(str_replace($replace, $with, $form['email']['admin_subject'][$language]));
		$mail->setHtml($html);
		$mail->setText(strip_tags($html));
		
		foreach ($admin_emails as $email) {
			$mail->setTo($email);
			$mail->send();
		}
		
		if (!empty($customer_emails) && $form['email']['customer_email']) {
			$html = html_entity_decode($form['email']['customer_message'][$language], ENT_QUOTES, 'UTF-8');
			$html = str_replace($replace, $with, $html);
			$html = str_replace('[form_responses]', $customer_response_list, $html);
			
			$mail->setFrom($admin_emails[0]);
			$mail->setSender(str_replace(array(',', '&'), array('', 'and'), html_entity_decode($this->config->get('config_title'), ENT_QUOTES, 'UTF-8')));
			$mail->setSubject(str_replace($replace, $with, $form['email']['customer_subject'][$language]));
			$mail->setHtml($html);
			$mail->setText(strip_tags($html));
			
			foreach ($customer_emails as $email) {
				$mail->setTo($email);
				$mail->send();
			}
		}
		
		foreach ($files as $file) {
			if ($file) unlink($file);
		}
		
		echo 'success';
	}
}
?>