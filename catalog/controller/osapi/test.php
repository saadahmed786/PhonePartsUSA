<?php
class ControllerOsapiTest extends Controller {
public function index()
{
		$mail = new Mail(); 
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');			
		$mail->setTo('xaman.riaz@gmail.com');
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender('PhonePartsUSA');
		$mail->setSubject(html_entity_decode('this is test subject', ENT_QUOTES, 'UTF-8'));
		$mail->setHtml('<h1>this is html</h1>');
		$mail->setText(html_entity_decode('this is text', ENT_QUOTES, 'UTF-8'));
		
		if($mail->send()){
			echo 'email sent';
		}
		else
		{
			echo 'email not sent';
		}
}
}
?>