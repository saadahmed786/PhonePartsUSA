<?php
class ControllerPaymentCod extends Controller {

      // Start Fix for payment method swap
      private $payment_code;

      public function __construct($registry) {
        parent::__construct($registry);

        $this->payment_code = preg_replace('/vq2-catalog_controller_payment_/', '', pathinfo(__FILE__, PATHINFO_FILENAME));

      }
      // End Fix for payment method swap
      
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['continue'] = ($this->config->get('config_gts_status')) ? $this->url->link('checkout/success', '', 'SSL') : $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/cod.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/cod.tpl';
		} else {
			$this->template = 'default/template/payment/cod.tpl';
		}	
		
		$this->render();
	}
	
	public function confirm() {

      // Start Fix for payment method swap
      
      if (empty($this->session->data['order_id']) || !$this->config->get($this->payment_code . '_status') || empty($this->session->data['payment_method']['code']) || $this->session->data['payment_method']['code'] != $this->payment_code) {
        $this->response->setOutput(json_encode(array(
          'error' => 'There was an issue processing your payment, please try again. If the issue persists, please contact us.'
        )));
        return;
      }
      // End Fix for payment method swap
      
		$this->load->model('checkout/order');
		
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
	}
}
?>