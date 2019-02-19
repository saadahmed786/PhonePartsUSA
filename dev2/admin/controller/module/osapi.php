<?php
/*
  osapi.php

  OneSaas Connect API 2.0.6.35 for OpenCart v1.5.4.1
  http://www.onesaas.com

  Copyright (c) 2012 oneSaas

  1.0.6.2	- Standardized plugin version to 1.0.6.x
  			- Plugin version stored in db (table settings with key='OSAPI_VERSION')
  			- Added plugin version to Iframe url (parameter 'c_ApiVersion')
  			- Show version in admin UI
  			- iframe url stored in db (table settings with key='OSAPI_IFRAME_URL') - default production env.
  1.0.6.6	- Added support for version update in db

*/

class ControllerModuleOsapi extends Controller {
	private $error = array();
	private $os_version = '2.0.6.35';
	private $os_prod_url = "https://secure.onesaas.com/signin/start";


	public function index() {
		$this->load->language('module/osapi');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		// Check if plugin is initialised
		$ak_query = $this->db->query("select s.key from " . DB_PREFIX . "setting s where s.key = 'OSAPI_ACCESS_KEY' and s.group='OSAPI'");
		if ($ak_query->num_rows == 0) {
			// Inizialise AccessKey
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting VALUES (NULL, 0, 'OSAPI', 'OSAPI_ACCESS_KEY', CONCAT(MD5(NOW()), MD5(CURTIME())), 0)");
			// Inizialise Version
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting VALUES (NULL, 0, 'OSAPI', 'OSAPI_VERSION', '" . $this->os_version . "', 0)");
			// Inizialise OneSaas Url
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting VALUES (NULL, 0, 'OSAPI', 'OSAPI_IFRAME_URL', '" . $this->os_prod_url . "', 0)");
			// Create table osapi_last_modified if it does not exist
			$this->db->query("create table if not exists " . DB_PREFIX . "osapi_last_modified (object_type ENUM('product','customer') NOT NULL, id INT(11) NOT NULL, hash VARCHAR(255) not null, last_modified_before DATETIME NOT NULL, PRIMARY KEY(object_type, id)) Engine=MyISAM DEFAULT CHARSET UTF8");
		}
		// Check Version
		$version_query = $this->db->query("select s.value from " . DB_PREFIX . "setting s where s.key = 'OSAPI_VERSION' and s.group='OSAPI'");
			if ($version_query->num_rows == 0) {
				// Initialise OneSaas Connect Plugin Version
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting VALUES (NULL, 0, 'OSAPI', 'OSAPI_VERSION', '" . $this->os_version . "', 0)");
			} else {
				// Check Version is current or we need to update it (in case of upgrade)
				$version = $version_query->row['value'];
				if ($version != $this->os_version) {
					$this->db->query("UPDATE " . DB_PREFIX . "setting s SET s.value='" . $this->os_version . "' where s.key = 'OSAPI_VERSION' and s.group='OSAPI'");
				}
		}
		// Read AccessKey
		$ak = '';
		$ak_query = $this->db->query("select s.value from " . DB_PREFIX . "setting s where s.key = 'OSAPI_ACCESS_KEY' and s.group='OSAPI'");
		if ($ak_query->num_rows == 1) {
			$ak = $ak_query->row['value'];
		}
		// Read OneSaas Url
		$os_url_query = $this->db->query("select s.value from " . DB_PREFIX . "setting s where s.key = 'OSAPI_IFRAME_URL' and s.group='OSAPI'");
		if ($os_url_query->num_rows == 1) {
			$os_url = $os_url_query->row['value'];
		}
		// OneSaas Configuration Page Link
		$CompanyName = $this->config->get('config_name');
		$ContactName = $this->config->get('config_owner');
		$ContactEmail = $this->config->get('config_email');
		$ContactPhone = $this->config->get('config_telephone');
		$os_link = $os_url . "?servicetype=opencart&c_ApiUrl=" . urlencode(HTTP_CATALOG) . "&c_ApiVersion=" . $this->os_version . "&c_ApiToken=" . urlencode($ak) . "&CompanyName=" . urlencode($CompanyName) . "&ContactName=" . urlencode($ContactName) . "&ContactEmail=" . urlencode($ContactEmail) . "&ContactPhone=" . urlencode($ContactPhone);

		$this->data['configkey'] = base64_encode(json_encode(array('ApiUrl' => HTTP_CATALOG, 'ApiToken' => $ak)));
		$this->data['os_link'] = $os_link;
		$this->data['os_version'] = $this->os_version;

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['breadcrumbs'] = array();
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/osapi', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		$this->children = array(
			'common/header',
			'common/footer'
		);
		$this->template = 'module/osapi.tpl';
		$this->response->setOutput($this->render());
	}
}
?>
