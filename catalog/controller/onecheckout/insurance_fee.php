<?php 
class ControllerOneCheckoutInsuranceFee extends Controller {

  	public function index() {
	
			$this->language->load('onecheckout/checkout');  
			$this->data['entry_insurance_fee'] = $this->language->get('entry_insurance_fee');	
			$this->load->model('total/insurance_fee');
			$this->data['insurance_required'] = $this->model_total_insurance_fee->getstatus();
			$this->data['insurance_selection'] = $this->config->get('insurance_fee_selection');
			if(!isset($this->session->data['insurance_fee'])){
				$this->session->data['insurance_fee'] = $this->model_total_insurance_fee->getstatus();
			}
			$this->data['insurance_fee'] = $this->session->data['insurance_fee'];
			$this->data['insurance_fee_text'] = $this->currency->format(((float)$this->config->get('insurance_fee_percentage')*$this->cart->getSubTotal())/100 + (float)$this->config->get('insurance_fee_fixed_fee'));
			
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onecheckout/insurance_fee.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onecheckout/insurance_fee.tpl';
			} else {
				$this->template = 'default/template/onecheckout/insurance_fee.tpl';
			}

		$this->render();		

  	}
	
	public function saveinsurance() {
	
		if(isset($this->request->post['insurance_fee']) && $this->request->post['insurance_fee']) {
			$this->session->data['insurance_fee'] = $this->request->post['insurance_fee'];
		} else {
			$this->session->data['insurance_fee'] = 0;
		}
	
	}

}
?>