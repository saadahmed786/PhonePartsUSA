<?php
class ModelAccountCustomer extends Model {
	public function addCustomer($data,$is_contact=0) {
		if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $data['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
		//$customer_group_id=$data['customer_group_id'];
		
		$this->load->model('account/customer_group');
		
		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET store_id = '" . (int)$this->config->get('config_store_id') . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', password = '" . $this->db->escape(md5($data['password'])) . "', newsletter = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', customer_group_id = '" . (int)$customer_group_id . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '1', approved = '" . (int)!$customer_group_info['approval'] . "', date_added = NOW(),business_name='".$this->db->escape($data['business_name'])."',repairdesk_token='".$this->getRandomToken()."'");

		$customer_id = $this->db->getLastId();
		if($is_contact)
		{
			$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', company_id = '" . $this->db->escape($data['company_id']) . "', tax_id = '" . $this->db->escape($data['tax_id']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "',contact_telephone_1='".$this->db->escape($data['telephone'])."',contact_telephone_2='".$this->db->escape($data['telephone_2'])."',is_contact=1");
		}
		else
		{
		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', company_id = '" . $this->db->escape($data['company_id']) . "', tax_id = '" . $this->db->escape($data['tax_id']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "'");
			
		}
		
		$address_id = $this->db->getLastId();

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
		
		

		$this->language->load('mail/customer');
		/*
		$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));
		
		$message = sprintf($this->language->get('text_welcome'), $this->config->get('config_name')) . "\n\n";
		
		if (!$customer_group_info['approval']) {
			$message .= $this->language->get('text_login') . "\n";
		} else {
			$message .= $this->language->get('text_approval') . "\n";
		}
		
		$message .= $this->url->link('account/login', '', 'SSL') . "\n\n";
		$message .= $this->language->get('text_services') . "\n\n";
		$message .= $this->language->get('text_thanks') . "\n";
		$message .= $this->config->get('config_name');
		
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');				
		$mail->setTo($data['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		$mail->send();*/


		$message = '<table  data-module="noti-3" data-bgcolor="Main BG" width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="currentTable">
    <tbody><tr>
      <td align="center">
        <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody><tr>
            <td align="center" bgcolor="#414a51" background="" style="background-size:auto; background-repeat:repeat-x; background-position:top;">
              <table align="center" border="0" cellpadding="0" cellspacing="0">
                <tbody><tr>
                  <td width="600" align="center">
                    <table class="table-inner" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                      <tbody>
                      <tr>
                        <td align="center">
                          <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                              <td width="30" valign="bottom">
                                <table width="100%" border="0" align="right" cellpadding="0" cellspacing="0">
                                  <tbody><tr>
                                    <td height="35"></td>
                                  </tr>
                                  <tr>
                                    <td data-bgcolor="Container" height="25" bgcolor="#FFFFFF" style="border-top-left-radius:6px;font-size:0px;">&nbsp;</td>
                                  </tr>
                                </tbody></table>
                              </td>
                              <!-- headline -->
                              <td align="center" valign="bottom" background="http://www.stampready.net/dashboard/editor/user_uploads/zip_uploads/2016/09/11/lu1j6Qf5WrdymkiMsFHO7zqR/Notification-03/images/title-bg.png" style="background-size: auto; background-position: center bottom; background-repeat: repeat-x;">
                                <table data-bgcolor="Headline" width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#447BE8" style="border-radius:6px;">
                                  <tbody><tr>
                                    <td align="center">
                                      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tbody><tr>
                                          <td height="15"></td>
                                        </tr>
                                        <tr>
                                          <td data-color="Headline" data-size="Headline" mc:edit="noti-3-1" align="center" style="font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;" contenteditable="true" class="editable">
                                            <singleline label="headline">Your account is ready to use</singleline>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td height="15"></td>
                                        </tr>
                                      </tbody></table>
                                    </td>
                                  </tr>
                                </tbody></table>
                              </td>
                              <!-- end headline -->
                              <td width="30" valign="bottom">
                                <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
                                  <tbody><tr>
                                    <td height="35"></td>
                                  </tr>
                                  <tr>
                                    <td data-bgcolor="Container" height="25" bgcolor="#FFFFFF" style="border-top-right-radius:6px;font-size:0px;">&nbsp;</td>
                                  </tr>
                                </tbody></table>
                              </td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>
                    <table data-bgcolor="Container" bgcolor="#FFFFFF" class="table-inner" width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tbody><tr>
                        <td align="center">
                          <table align="center" width="90%" border="0" cellspacing="0" cellpadding="0">
                            <tbody><tr>
                              <td height="40"></td>
                            </tr>
                            <!--logo-->
                            <tr>
                              <td align="center">
                                <a href="#" class="">
                                  <img src="https://phonepartsusa.com/image/logo_new.png" alt="img" width="" style="display:block; line-height:0px; font-size:0px; border:0px;" editable="" label="logo" data-crop="false" mc:edit="noti-3-2" class="">
                                </a>
                              </td>
                            </tr>
                            <!--end logo-->
                            <tr>
                              <td height="5"></td>
                            </tr>
                            
                            <tr>
                              <td height="40"></td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>
                    <table data-bgcolor="Container" bgcolor="#FFFFFF" class="table-inner" width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tbody><tr>
                        <td align="center">
                          <table align="center" width="90%" border="0" cellspacing="0" cellpadding="0">
                            <!--image-->
                            <tbody><tr>
                              <td align="center">
                                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
                                  <tbody><tr>
                                    <td align="center" style="line-height: 0px;">
                                      <img editable="" label="image" data-crop="false" src="http://imp.phonepartsusa.com/images/passwordreset.png" alt="img" width="" class="img1" style="display:block; line-height:0px; font-size:0px; border:0px;" mc:edit="noti-3-4">
                                    </td>
                                  </tr>
                                </tbody></table>
                              </td>
                            </tr>
                            <!--end image-->
                            <tr>
                              <td height="40"></td>
                            </tr>
                            <!--headline-->
                            <tr>
                              <td data-link-style="text-decoration:none; color:#3b3b3b;" data-link-color="Title Link" data-color="Title" data-size="Title" mc:edit="noti-3-5" align="center" style="font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;line-height: 28px;">
                                <singleline label="Headline">Password: '.$data['password'].'</singleline>
                              </td>
                            </tr>
                            <!--end headline-->	
                            <tr>
                              <td height="20"></td>
                            </tr>
                            <!--dotted-->
                            <tr>
                              <td align="center">
                                <table border="0" align="center" cellpadding="0" cellspacing="0">
                                  <tbody><tr>
                                    <td align="center">
                                      <table data-bgcolor="Dotted" bgcolor="#447BE8" style="border-radius:5px;" align="center" border="0" cellpadding="0" cellspacing="0">
                                        <tbody><tr>
                                          <td style="font-size:0px; line-height:0px;" height="5" width="5">&nbsp;</td>
                                        </tr>
                                      </tbody></table>
                                    </td>
                                    <td width="15"></td>
                                    <td align="center">
                                      <table data-bgcolor="Dotted" bgcolor="#447BE8" style="border-radius:5px;" align="center" border="0" cellpadding="0" cellspacing="0">
                                        <tbody><tr>
                                          <td style="font-size:0px; line-height:0px;" height="5" width="5">&nbsp;</td>
                                        </tr>
                                      </tbody></table>
                                    </td>
                                    <td width="15"></td>
                                    <td align="center">
                                      <table data-bgcolor="Dotted" bgcolor="#447BE8" style="border-radius:5px;" align="center" border="0" cellpadding="0" cellspacing="0">
                                        <tbody><tr>
                                          <td style="font-size:0px; line-height:0px;" height="5" width="5">&nbsp;</td>
                                        </tr>
                                      </tbody></table>
                                    </td>
                                    <td width="15"></td>
                                    <td align="center">
                                      <table data-bgcolor="Dotted" bgcolor="#447BE8" style="border-radius:5px;" align="center" border="0" cellpadding="0" cellspacing="0">
                                        <tbody><tr>
                                          <td style="font-size:0px; line-height:0px;" height="5" width="5">&nbsp;</td>
                                        </tr>
                                      </tbody></table>
                                    </td>
                                  </tr>
                                </tbody></table>
                              </td>
                            </tr>
                            <!--end dotted-->
                            <tr>
                              <td height="20"></td>
                            </tr>
                            <!--content-->
                            <tr>
                              <td data-link-style="text-decoration:none; color:#447BE8;" data-link-color="Content Link" data-color="Content" data-size="Content" mc:edit="noti-3-6" align="left" style="font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:14px; line-height: 14px;">
                                <multiline label="content">'.str_replace('\\', '', str_replace(PHP_EOL, '', '<span contenteditable="false">'.$data['firstname'].' '.$data['lastname'].'</span>,<br><br>

You PhonePartsUSA account has been created for '.$data['email'].'<br><br>')).'</multiline>
                              </td>
                            </tr>
                            <!--end content-->
                            <tr>
                              <td height="40"></td>
                            </tr>
                            <!--button-->
                            <tr>
                              <td align="center">
                                <table data-bgcolor="Button" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#39CB59" class="textbutton" style="border-radius:4px;">
                                  <tbody><tr>
                                    <td data-link-style="text-decoration:none; color:#ffffff;" data-link-color="Button Link" data-size="Button" mc:edit="noti-3-7" height="40" align="center" style="font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:14px;padding-left: 25px;padding-right: 25px;font-weight: bold; ">
                                      <a href="https://phonepartsusa.com/index.php?route=account/login" style="text-decoration: none; color:#FFFFFF;" data-color="Button Link">
                                        <singleline label="button">Visit PhonePartsUSA</singleline>
                                      </a>
                                    </td>
                                  </tr>
                                </tbody></table>
                              </td>
                            </tr>
                            <!--end button-->
                            <tr>
                              <td height="5"></td>
                            </tr>
                            
                            <tr>
                              <td height="40"></td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>
                    <!--footer-->
                    <table bgcolor="#FFFFFF" class="table-inner" style=" box-shadow:0px 3px 0px #ccd5dc; border-bottom-left-radius:6px; border-bottom-right-radius:6px;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                      <tbody><tr>
                        <td data-bgcolor="Footer" height="45" align="center" bgcolor="#f4f4f4" style="border-bottom-left-radius:6px;border-bottom-right-radius:6px;">
                          <table align="center" width="90%" border="0" cellspacing="0" cellpadding="0">
                            <tbody><tr>
                              <td height="10"></td>
                            </tr>
                            <!--preference-->
                            <tr>
                              <td data-link-style="text-decoration:none; color:#447BE8;" data-link-color="Preference Link" data-color="Preference" data-size="Preference" mc:edit="noti-3-9" class="preference-link" align="center" style="font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:12px; line-height: auto;font-style: italic;">
                               &copy; '.date('Y').' PhonePartsUSA.com. All Rights Reserved
                              </td>
                            </tr>
                            <!--end preference-->
                            <tr>
                              <td height="10"></td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>
                    <!--end footer-->
                    <!--social-->
                    <table align="center" width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tbody><tr>
                        <td height="20"></td>
                      </tr>
                      <tr>
                        <td align="center">
                          <table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                               <td data-link-style="text-decoration:none; color:#447BE8;" data-link-color="Preference Link" data-color="Preference" data-size="Preference" mc:edit="noti-3-9" class="preference-link" align="center" style="font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:12px; line-height: auto;font-style: italic;">
                                5145 South Arville St. Suite A &bull; Las Vegas &bull; NV 89118 USA
                              </td>
                              
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                      <tr>
                        <td height="40"></td>
                      </tr>
                    </tbody></table>
                    <!--end social-->
                  </td>
                </tr>
              </tbody></table>
            </td>
          </tr>
        </tbody></table>
      </td>
    </tr>
  </tbody></table>';

  $subject = $data['firstname'].' '.$data['lastname'].', your account has been created!';
  $mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');				
		$mail->setTo($data['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($message);
		$mail->send();
		
		// Send to main admin email if new account email is enabled
		if ($this->config->get('config_account_mail')) {
			$mail->setTo($this->config->get('config_email'));
			$mail->send();
			
			// Send to additional alert emails if new account email is enabled
			$emails = explode(',', $this->config->get('config_alert_emails'));
			
			foreach ($emails as $email) {
				if (strlen($email) > 0 && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}
	}
	public function addSocialMediaCustomer($fname,$lname,$email,$pw){
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($fname) . "', lastname = '" . $this->db->escape($lname) . "', email = '" . $this->db->escape($email) . "', password = '" . $this->db->escape(md5($pw)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '1',approved = '1', date_added = NOW()");
	}
	public function editCustomer($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "',repairdesk_token='".$this->db->escape($data['repairdesk_token'])."' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editCustomerNew($data) {
		$phonedata = array();
		foreach ($data['phoneselector'] as $i => $type){
					$phonedata[$i]['type']= $type[$i];
					$phonedata[$i]['number']= $data['phonenumber'][$i];
		}
		$data['phones']=serialize($phonedata);
		
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', phones = '" . $this->db->escape($data['phones']) . "', business_name = '" . $this->db->escape($data['business_name']) . "',repairdesk_token='".$this->db->escape($data['repairdesk_token'])."' WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		if($data['password'])
		{
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET password='".$this->db->escape(md5($data['password']))."' WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		if($data['password']!='12345')
		{
			unset($this->session->data['wholesale_account_user']);
		}			
		}
	}

	//Updateding Email Address in IMP
	public function updateEmailImp($data) {
		$tables = array("inv_customer_comments", "inv_customer_files", "inv_customer_return_orders", "inv_customers", "inv_orders", "inv_po_customers", "inv_return_orders", "inv_returns", "oc_order", "oc_return");
		$email = $data['email'];
		$oldEmail = $data['oldEmail'];

		// adding A commment when user change his email address.
		$comment = array();
		$comment['customer_id'] = $data['customer_id'];
		$comment['comments'] = "User changed his email $oldEmail to $email";
		$comment['comment_type'] = "Sales Call";
		$comment['user_id'] = $data['customer_id'];
		$comment['email'] = $oldEmail;
//		$comment['date_added'] = date('Y-m-d H:i:s');


		foreach ($comment as $key => $value) {
			$sq[] = $key . ' = "' . $this->db->escape($value) . '"';
		}

		$this->db->query('INSERT INTO `inv_customer_comments` SET '. implode(', ', $sq) .', date_added = NOW()');

		foreach ($tables as $table) {
			$this->db->query("UPDATE `$table` SET `email`='$email' WHERE `email`='$oldEmail'");
		}
	}

	public function editPassword($email, $password) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET password = '" . $this->db->escape(md5($password)) . "' WHERE email = '" . $this->db->escape($email) . "'");
	}

	public function editNewsletter($newsletter) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		
		return $query->row;
	}
	
	public function getCustomerByEmail($email) {
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
		
		return $query->row;
	}

	public function getCustomerByToken($token) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE token = '" . $this->db->escape($token) . "' AND token != ''");
		
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = ''");
		
		return $query->row;
	}

	public function getCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cg.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group cg ON (c.customer_group_id = cg.customer_group_id) ";

		$implode = array();
		
		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$implode[] = "LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}
		
		if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
			$implode[] = "c.email = '" . $this->db->escape($data['filter_email']) . "'";
		}
		
		if (isset($data['filter_customer_group_id']) && !is_null($data['filter_customer_group_id'])) {
			$implode[] = "cg.customer_group_id = '" . $this->db->escape($data['filter_customer_group_id']) . "'";
		}	
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}	
		
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}	

		if (isset($data['filter_ip']) && !is_null($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}	

		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.ip',
			'c.date_added'
			);	

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY name";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}		
		
		$query = $this->db->query($sql);
		
		return $query->rows;	
	}

	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "'");
		
		return $query->row['total'];
	}
	
	public function getIps($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");
		
		return $query->rows;
	}	
	
	public function isBlacklisted($ip) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip_blacklist` WHERE ip = '" . $this->db->escape($ip) . "'");
		
		return $query->num_rows;
	}

	public function getCustomerPermissions($group_id) {
		$query = $this->db->query("SELECT group_concat(b.privilege) as pri from inv_customer_group_privilege a inner join inv_privilege as b on a.privilege_id = b.privilege_id where group_id = '" . (int)$group_id . "'");
		
		return explode(',', $query->row['pri']);
	}
  public function addExtra($email,$source,$type)
  {
    $this->db->query("INSERT INTO `".DB_PREFIX."customer_source` SET email='".$this->db->escape(trim($email))."', source='".$this->db->escape($source)."',`type`='".$this->db->escape($type)."'");
  }	
  public function addFSLead($data,$test=false)
  {
      $data = json_encode($data);
     
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, "https://phonepartsusa.freshsales.io/api/leads");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_POST, 1);

      $headers = array();
      $headers[] = "Authorization: Token token=RMKYt6rcgwHUAw3-wcSo7A";
      $headers[] = "Content-Type: application/json";
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($ch);
      // $this->log->write(json_encode($result));
      if($test)
      {
        
      print_r($result);exit;
      }
      if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);exit;
      }
      curl_close ($ch);
  }

  public function getRepairDeskCustomer($email,$customer_token)
  {

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "' AND repairdesk_token='".$this->db->escape($customer_token)."'");
    
    return $query->row;

  }

  public function update_repairdesk_po($email)
  {
    $this->load->model('checkout/order');
    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
    $message = 'There is something error parsing data to RepairDesk';
    $items_list = array();
    foreach($this->cart->getProducts() as $cart)
    {
      $items_list[] = array(
        'item_sku'=>$cart['model'],
        'item_qty'=>$cart['quantity'],
        'item_price'=>$cart['price'],
        'item_gst'=>0.00
        );
    }
    $posted = array(
      'ppusa_order_id' => $this->session->data['order_id'],
      'order_status' =>$order_info['order_status'],
      'repairdesk_po' =>$this->session->data['newcheckout']['repairdesk_po'],
      'shipping_method' =>$order_info['shipping_method'],
      'shipping_amount' =>$order_info['shipping_total'],
      'item_list' =>$items_list
      );
    $posted = json_encode($posted);
    $posted = array('request' =>$posted);
    $crul = curl_init();
    curl_setopt($crul, CURLOPT_HEADER, false);
    $headers = array("Host:www.repairdesk.co");
    curl_setopt($crul, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($crul, CURLOPT_URL,"https://www.repairdesk.co/index.php?r=site/updatePurchaseOrder");

    curl_setopt($crul, CURLOPT_RETURNTRANSFER,
      true); curl_setopt($crul, CURLOPT_POST, true);
    curl_setopt($crul, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($crul, CURLOPT_POSTFIELDS, $posted);
    $response = curl_exec($crul);
    if (curl_errno($crul) != CURLE_OK) {
      $status = 1;
      $message = 'Data Parsed Successfully to RepairDesk';
    }
    else{
      $status = 0;
    }
    echo json_encode(array('status'=>$status,'message'=>$message));
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

  
}
?>