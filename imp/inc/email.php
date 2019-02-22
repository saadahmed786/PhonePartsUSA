<?php

include 'phpmailer/class.phpmailer.php';

class email{
	
	public $phpMailer;
	
	public $template_path = '';
	
	public function __construct(){
		global $path;
		
		$this->template_path = $path."/emails/";
		
		$this->phpMailer = new PHPMailer();
		$this->phpMailer->From = 'no-reply@phonepartsusa.com';
		$this->phpMailer->FromName = 'PhonePartsUSA';
	}
	
	public function sendReturnDenied($email , $customer_name , $order_id){
		$this->phpMailer->ClearAddresses();
		$this->phpMailer->Subject = 'Return request is denied - PhonepartsUSA';
		
		$template = $this->template_path. "return_denied.html";
		$body = file_get_contents($template);
		
		$body = str_ireplace(array("{customer}","{order}"), array($customer_name,$order_id), $body);
		
		$this->phpMailer->MsgHTML($body);
		$this->phpMailer->AddAddress($email , $customer_name);
		
		$this->phpMailer->Send();
	}
	
	public function sendReturnReceived($customer_name , $order_id){
		$this->phpMailer->ClearAddresses();
		$this->phpMailer->Subject = 'Return request is received - PhonepartsUSA';
		
		$template = $this->template_path. "return_received.html";
		$body = file_get_contents($template);
		
		$body = str_ireplace(array("{customer}","{order}"), array($customer_name,$order_id), $body);
		
		$this->phpMailer->MsgHTML($body);
		$this->phpMailer->AddAddress($email , $customer_name);
		
		$this->phpMailer->Send();
	}
}