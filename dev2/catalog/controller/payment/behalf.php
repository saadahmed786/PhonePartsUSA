<?php
class ControllerPaymentBehalf extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/behalf.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/behalf.tpl';
		} else {
			$this->template = 'default/template/payment/behalf.tpl';
		}	
		
		$this->render();
	}

	public function addBuyer()
	{
		$email = urldecode($this->request->post['email']);
		$behalf_buyer_id = urldecode($this->request->post['behalfBuyerId']);
		$behalf_buyer_status = urldecode($this->request->post['behalfBuyerStatus']);

			// echo "INSERT INTO ".DB_PREFIX."behalf_buyer SET email='".strtolower($this->db->escape($email))."',behalf_buyer_id='".$this->db->escape($behalf_buyer_id)."',status='".$this->db->escape($behalf_buyer_status)."'";exit;
		// echo 'here2';exit;
		$this->db->query("DELETE FROM ".DB_PREFIX."behalf_buyer WHERE LOWER(email)='".strtolower($this->db->escape($email))."'");
		$this->db->query("INSERT INTO ".DB_PREFIX."behalf_buyer SET email='".strtolower($this->db->escape($email))."',behalf_buyer_id='".$this->db->escape($behalf_buyer_id)."',status='".$this->db->escape($behalf_buyer_status)."'");

		echo json_encode(array('success'=>1));


	}
	
	public function confirm() {
		$this->load->model('checkout/order');
		
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('terms_order_status_id'));
	}
}
?>