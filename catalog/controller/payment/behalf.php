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

	public function addPayment()
	{
		$payment_token = urldecode($this->request->post['paymentToken']);
		$payment_status = urldecode($this->request->post['paymentStatus']);
		$behalf_buyer_id = urldecode($this->request->post['behalfBuyerId']);


		$this->db->query("DELETE FROM ".DB_PREFIX."behalf_payment WHERE order_id='".(int)($this->session->data['order_id'])."'");
		$this->db->query("INSERT INTO ".DB_PREFIX."behalf_payment SET payment_token='".$this->db->escape($payment_token)."',payment_status='".$this->db->escape($payment_status)."',behalf_buyer_id='".$this->db->escape($behalf_buyer_id)."',order_id='".(int)$this->session->data['order_id']."',date_added='".date('Y-m-d H:i:s')."'");

		echo json_encode(array('success'=>1));


	}

	private function getBehalfData()
	{
		$data = array();
		if ($this->config->get('behalf_status')) {
			
			$data['email']= $this->config->get('behalf_server_email');
			$data['password']= $this->config->get('behalf_server_password');

			if($this->config->get('behalf_account')=='production')
			{
				$data['url'] = 'https://api.behalf.com';
				
			}
			
			elseif($this->config->get('behalf_account')=='sandbox')
			{
				$data['url'] = 'https://api.demo.behalf.com';
			}
			else
			{
				$data['url'] = 'https://api.demo.behalf.com';	
			}
		}
		return $data;
	}

	private function getAccessToken()
	{

		$server = $this->getBehalfData();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $server['url']."/v4/auth/token",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array(
				"authorization: Basic ".base64_encode($server['email'].':'.$server['password']),
				"content-type: application/json",
				),
			));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return false;
		} else {
			$data =  json_decode($response,true);
			if($data['accessToken'])
			{
				return $data['accessToken'];
				
			}
			else
			{
				return false;
			}
		}
	}

	private function authorizeBehalf($access_token)
	{
		
		$server = $this->getBehalfData();
		// echo "SELECT * FROM ".DB_PREFIX."behalf_payment WHERE order_id='".(int)$this->session->data['order_id']."'";exit;
		$behalf_data = $this->db->query("SELECT * FROM ".DB_PREFIX."behalf_payment WHERE order_id='".(int)$this->session->data['order_id']."'");
		$order_data = $this->db->query("SELECT total FROM ".DB_PREFIX."order WHERE order_id='".(int)$this->session->data['order_id']."'");

		if($behalf_data)
		{
			// print_r($behalf_data);exit;
			$curl = curl_init();
		// echo $server['url']."/v4/payments/".$behalf_data->row['payment_token']."/authorizations";exit;
			curl_setopt_array($curl, array(
				CURLOPT_URL => $server['url']."/v4/payments/".$behalf_data->row['payment_token']."/authorizations",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => '{"amount": "'.$order_data->row['total'].'", "behalfBuyerId": "'.$behalf_data->row['behalf_buyer_id'].'","allowInReviewAuthorization":"true"}',
				CURLOPT_HTTPHEADER => array(
					"content-type: application/json",
					"x-behalf-accesstoken: ".$access_token
					
					),
				));

			$response = curl_exec($curl);
			$err = curl_error($curl);
			$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);

			if (!$response) {
				return false;
			} else {
			// echo $response;exit;
				$data =  json_decode($response,true);


				if($data['authorizationInfo']['authorizationToken'])
				{
					$this->db->query("UPDATE ".DB_PREFIX."behalf_payment SET authorization_token='".$data['authorizationInfo']['authorizationToken']."',authorization_status='".$data['authorizationInfo']['paymentStatus']."' WHERE payment_token='".$behalf_data->row['payment_token']."'");
					if($data['authorizationInfo']['paymentStatus']=='authorized')
					{

						$this->capturePayment($access_token,$behalf_data->row['payment_token'],$data['authorizationInfo']['authorizationToken'],$order_data->row['total']);
					}
					return $data['authorizationInfo']['authorizationToken'];
				}
				else
				{
					print_r($data);exit;
					return false;
				}
			}

		}
		else
		{
			return false;
		}
	}
	private function capturePayment($access_token,$payment_token,$authorization_token,$order_total)
	{
		$server = $this->getBehalfData();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $server['url']."/v4/payments/".$payment_token."/captures",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => '{"amount": "'.$order_total.'", "authorizationToken": "'.$authorization_token.'"}',
			CURLOPT_HTTPHEADER => array(
				"content-type: application/json",
				"x-behalf-accesstoken: ".$access_token
				),
			));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {

		} else {
			$data = json_decode($response,true);
			if($data['captureInfo'])
			{
				
				$this->db->query("UPDATE ".DB_PREFIX."behalf_payment SET 
					capture_date='".$data['captureInfo']['created']."',
					capture_status='".$data['captureInfo']['paymentStatus']."',
					captured_amount='".(float)$data['captureInfo']['capturedAmount']."',
					net_captured_amount='".(float)$data['captureInfo']['netCapturedAmount']."',
					capture_id='".$data['captureInfo']['captureId']."'
					WHERE payment_token='".$payment_token."'");
			}
		}


	}
	public function confirm() {
		// echo 'here';exit;
		$this->load->model('checkout/order');
		$json = array();

		$access_token = $this->getAccessToken();
		if(!$access_token)
		{
			$json['error'] = 'Problem retreiving access token from the server, please try again or contact admin';
		}
		else
		{

			$authorization_token = $this->authorizeBehalf($access_token);

			if(!$authorization_token)
			{
				$json['error'] = 'Payment not authorized, please try with different payment method';
			}
			else
			{

			}
		}
		if(!$json['error'])
		{
			$json['success'] = 1;
			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('behalf_order_status_id'));
			
		}
		echo json_encode($json);
	}


}
?>