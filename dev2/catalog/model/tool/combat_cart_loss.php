<?php
class ModelToolCombatCartLoss extends Model {

        private $current_recipient = 0;

	public function sendUnconfirmedOrderAlert($order_id, $mail_send = '0') {


            if ($this->config->get('ccl_enable_admin_emails') && (!isset($this->session->data['unconfirmed_alert_sent']) || ($mail_send == '1'))) {

                    $this->language->load('checkout/checkout');

                    $this->load->model('checkout/order');

                    $order_info = $this->model_checkout_order->getOrder($order_id);

                    $this->load->model('localisation/language');


                    $language_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$order_info['language_id'] . "'");

                    $language_info = $language_info->row;


                    if ($language_info) {
                            $language_code = $language_info['code'];
                            $language_filename = $language_info['filename'];
                            $language_directory = $language_info['directory'];
                    } else {
                            $language_code = '';
                            $language_filename = '';
                            $language_directory = '';
                    }

                    // Send out order confirmation mail
                    $language = new Language($language_directory);
                    $language->load($language_filename);
                    $language->load('mail/order');


                    $subject = $this->config->get('ccl_admin_email_subject');//, html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $order_id);
                    //$subject = str_replace('[order]',$order_id,$subject);
                    //$subject = str_replace('[store]', html_entity_decode($this->config->get('config_name')), $subject);

                    $message = htmlspecialchars_decode($this->config->get('ccl_admin_email_message'));

                    $recipients = $this->db->query('SELECT IFNULL(c.email,o.email) as email,
                            concat(ifnull(c.firstname,o.firstname), \' \', ifnull(c.lastname,o.lastname)) as customer_name ,
                            ifnull(c.firstname,o.firstname) firstname,
                            ifnull(c.lastname,o.lastname) lastname, o.shipping_firstname,o.shipping_lastname,o.shipping_address_1,o.shipping_address_2,o.shipping_city,o.shipping_postcode,o.shipping_country,o.shipping_zone, order_id,o.store_id,o.store_name,o.store_url, o.currency_code, o.currency_value
                                          FROM `'.DB_PREFIX.'order` o
                                          LEFT JOIN '.DB_PREFIX.'customer c ON c.customer_id = o.customer_id
                                          WHERE order_id = '.(int)$order_id);

                    /*content short codes here*/



                    $settings = $this->getSetting('config', $recipients->row['store_id']);

                    $from = $settings['config_email'];
                    $sender = $settings['config_name'];

                    $from = (!empty($from)?$from:$this->config->get('config_email'));
                    $store = (!empty($sender)?$sender:$this->config->get('config_name'));

                    $recipients->row['store'] = $store;

                    $this->current_recipient = $recipients->row;

                    $shortcodes_subject = new Shortcodes();
                    $shortcodes_subject->add_shortcode('customername',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('order',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('customerfirstname',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('customerlastname',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('cost',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('store',array(&$this,'template_variables_subject'));

                    $subject = $shortcodes_subject->do_shortcode($subject);

                    $shortcodes = new Shortcodes();
                    $shortcodes->add_shortcode('customername',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('order',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('customerfirstname',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('customerlastname',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('deliveryaddress',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('cost',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('products',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('store',array(&$this,'template_variables'));

                    $message = $shortcodes->do_shortcode($message);

                    if($mail_send == '1') {
                        $mail = new Mail();
                        $mail->protocol = $this->config->get('config_mail_protocol');
                        $mail->parameter = $this->config->get('config_mail_parameter');
                        $mail->hostname = $this->config->get('config_smtp_host');
                        $mail->username = $this->config->get('config_smtp_username');
                        $mail->password = $this->config->get('config_smtp_password');
                        $mail->port = $this->config->get('config_smtp_port');
                        $mail->timeout = $this->config->get('config_smtp_timeout');
                        $mail->setTo($order_info['email']);
                        $mail->setFrom($from);
                        $mail->setSender($store);
                        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                        $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                        $mail->setHtml($message);
                        $mail->send();
                    } else {
                        $this->save_order_email($order_id, $subject, $message,'1');
                    }
                    $this->session->data['unconfirmed_alert_sent'] = $order_id;

            }
	}
        public function autoSendUnconfirmedOrder($order_id) {

            if ($this->config->get('ccl_enable_auto_emails')) {

                    $this->language->load('checkout/checkout');

                    $this->load->model('checkout/order');

                    $order_info = $this->model_checkout_order->getOrder($order_id);

                    $this->load->model('localisation/language');


                    $language_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$order_info['language_id'] . "'");

                    $language_info = $language_info->row;


                    if ($language_info) {
                            $language_code = $language_info['code'];
                            $language_filename = $language_info['filename'];
                            $language_directory = $language_info['directory'];
                    } else {
                            $language_code = '';
                            $language_filename = '';
                            $language_directory = '';
                    }

                    // Send out order confirmation mail
                    $language = new Language($language_directory);
                    $language->load($language_filename);
                    $language->load('mail/order');
                    
                    $recipients = $this->db->query('SELECT IFNULL(c.email,o.email) as email,
                            concat(ifnull(c.firstname,o.firstname), \' \', ifnull(c.lastname,o.lastname)) as customer_name ,
                            ifnull(c.firstname,o.firstname) firstname,
                            ifnull(c.lastname,o.lastname) lastname, o.shipping_firstname,o.shipping_lastname,o.shipping_address_1,o.shipping_address_2,o.shipping_city,o.shipping_postcode,o.shipping_country,o.shipping_zone, order_id,o.store_id, o.store_name, o.store_url, o.currency_code, o.currency_value
                                          FROM `'.DB_PREFIX.'order` o
                                          LEFT JOIN '.DB_PREFIX.'customer c ON c.customer_id = o.customer_id
                                          WHERE order_id = '.(int)$order_id);

                    /*content short codes here*/

                    $settings = $this->getSetting('cclauto', $recipients->row['store_id']);
                    
                    $subject = $settings['ccl_auto_email_subject'];

                    $message = htmlspecialchars_decode($settings['ccl_auto_email_message']);
                    $from = $settings['ccl_auto_email_from'];
                    
                    $sender = $this->config->get('config_name');

                    $from = (!empty($from)?$from:$this->config->get('config_email'));
                    $store = (!empty($sender)?$sender:$this->config->get('config_name'));

                    $recipients->row['store'] = $store;

                    $this->current_recipient = $recipients->row;

                    $shortcodes_subject = new Shortcodes();
                    $shortcodes_subject->add_shortcode('customername',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('order',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('customerfirstname',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('customerlastname',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('cost',array(&$this,'template_variables_subject'));
                    $shortcodes_subject->add_shortcode('store',array(&$this,'template_variables_subject'));

                    $subject = $shortcodes_subject->do_shortcode($subject);

                    $shortcodes = new Shortcodes();
                    $shortcodes->add_shortcode('customername',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('order',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('customerfirstname',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('customerlastname',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('deliveryaddress',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('cost',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('products',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('store',array(&$this,'template_variables'));
                    $shortcodes->add_shortcode('couponcode',array(&$this,'template_variables'));

                    $message = $shortcodes->do_shortcode($message);

                    $mail = new Mail();
                    $mail->protocol = $this->config->get('config_mail_protocol');
                    $mail->parameter = $this->config->get('config_mail_parameter');
                    $mail->hostname = $this->config->get('config_smtp_host');
                    $mail->username = $this->config->get('config_smtp_username');
                    $mail->password = $this->config->get('config_smtp_password');
                    $mail->port = $this->config->get('config_smtp_port');
                    $mail->timeout = $this->config->get('config_smtp_timeout');
                    //$mail->setTo($this->config->get('config_email'));
                    $mail->setTo($order_info['email']);
                    $mail->setFrom($from);
                    $mail->setSender($store);
                    $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                    $mail->setHtml($message); 

                    $this->save_order_email($order_id, $subject, $message);
                    $mail->send();

            }
	}
        function save_order_email($order_id,$email_subject,$email_message, $admin_notify = '0'){
            $this->db->query("insert into `".DB_PREFIX."order_emails` set order_id='".(int)$order_id."', date_added=now(),email_subject='".$this->db->escape($email_subject)."', email_message='".$this->db->escape($email_message)."', admin_notify='".$admin_notify."'");
        }
        function send_admin_notification() {
            $query = $this->db->query('SELECT * FROM `'.DB_PREFIX.'order_emails` WHERE admin_notify = 1');
            if ($query->num_rows) {
                foreach ($query->rows as $result) {        
                    $this->sendUnconfirmedOrderAlert($result['order_id'], '1');
                    $this->db->query("DELETE from " . DB_PREFIX . "order_emails WHERE order_id='" . (int)$result['order_id'] . "'");
		}
            }
            
        }
        function get_orders($data = array())
        {

            return $this->db->query('SELECT o.order_id, CONCAT(o.firstname, " ", o.lastname) AS customer, o.store_name, o.total,o.date_added, o.date_modified, (select count(*) from `'.DB_PREFIX.'order_emails` oe where o.order_id=oe.order_id AND oe.admin_notify = 0) as total_emails
                                    FROM `'.DB_PREFIX.'order` o
                                    WHERE o.order_status_id <=0 AND o.date_added > NOW() - INTERVAL 60 MINUTE 
   AND o.`date_added` < NOW() - INTERVAL 15 MINUTE
                                    ORDER BY o.date_modified desc');
        }
        public function addUnconfirmedOrder(){
                $total_data = array();
                $total = 0;
                $taxes = $this->cart->getTaxes();

                $this->load->model('setting/extension');

                $sort_order = array();

                $results = $this->model_setting_extension->getExtensions('total');

                foreach ($results as $key => $value) {
                        $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                }

                array_multisort($sort_order, SORT_ASC, $results);

                foreach ($results as $result) {
                        if ($this->config->get($result['code'] . '_status')) {
                                $this->load->model('total/' . $result['code']);

                                $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                        }
                }

                $sort_order = array();

                foreach ($total_data as $key => $value) {
                        $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $total_data);

                $this->language->load('checkout/checkout');

                $data = array();

                $data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
                $data['store_id'] = $this->config->get('config_store_id');
                $data['store_name'] = $this->config->get('config_name');

                if ($data['store_id']) {
                        $data['store_url'] = $this->config->get('config_url');
                } else {
                        $data['store_url'] = HTTP_SERVER;
                }

                if ($this->customer->isLogged()) {
                        $data['customer_id'] = $this->customer->getId();
                        $data['customer_group_id'] = $this->customer->getCustomerGroupId();
                        $data['firstname'] = $this->session->data['logged_in']['firstname'];
                        $data['lastname'] = $this->session->data['logged_in']['lastname'];
                        $data['email'] = $this->customer->getEmail();
                        $data['telephone'] = $this->session->data['logged_in']['telephone'];
                        $data['fax'] = $this->session->data['logged_in']['fax'];
                        $data['payment_company'] = $this->session->data['logged_in']['company'];  


                        $this->load->model('account/address');

                        $payment_address = $this->model_account_address->getAddress(isset($this->session->data['payment_address_id'])?$this->session->data['payment_address_id']:$this->customer->getAddressId());
                } elseif (isset($this->session->data['guest'])) {
                        $data['customer_id'] = 0;
                        $data['customer_group_id'] = (isset($this->session->data['guest']['customer_group_id'])?$this->session->data['guest']['customer_group_id']:(int)$this->config->get('config_customer_group_id'));
                        $data['firstname'] = $this->session->data['guest']['firstname'];
                        $data['lastname'] = $this->session->data['guest']['lastname'];
                        $data['email'] = $this->session->data['guest']['email'];
                        $data['telephone'] = $this->session->data['guest']['telephone'];
                        $data['fax'] = $this->session->data['guest']['fax'];
                        $data['payment_company'] = $this->session->data['guest']['payment']['company'];
                        $payment_address = $this->session->data['guest']['payment'];
                }

                $data['payment_firstname'] = $payment_address['firstname'];
                $data['payment_lastname'] = $payment_address['lastname'];
                
                $data['payment_company_id'] = isset($payment_address['company_id'])?$payment_address['company_id']:'';
                $data['payment_tax_id'] = isset($payment_address['tax_id'])?$payment_address['tax_id']:'';
                $data['payment_address_1'] = $payment_address['address_1'];
                $data['payment_address_2'] = $payment_address['address_2'];
                $data['payment_city'] = $payment_address['city'];
                $data['payment_postcode'] = $payment_address['postcode'];
                $data['payment_zone'] = $payment_address['zone'];
                $data['payment_zone_id'] = $payment_address['zone_id'];
                $data['payment_country'] = $payment_address['country'];
                $data['payment_country_id'] = $payment_address['country_id'];
                $data['payment_address_format'] = $payment_address['address_format'];

                //No Payment method selected as of yet
                $data['payment_method'] = '';
                $data['payment_code'] = '';

                if ($this->cart->hasShipping()) {
                        if ($this->customer->isLogged()) {
                                $this->load->model('account/address');

                                $shipping_address = $this->model_account_address->getAddress(isset($this->session->data['shipping_address_id'])?$this->session->data['shipping_address_id']:$this->customer->getAddressId());
                        } elseif (isset($this->session->data['guest'])) {
                                $shipping_address = $this->session->data['guest']['shipping'];
                        }

                        if($this->customer->isLogged())
                        {
                            $this->load->model('account/address');

                                $shipping_address = $this->model_account_address->getAddress(isset($this->session->data['shipping_address_id'])?$this->session->data['shipping_address_id']:$this->customer->getAddressId());
                                
                             $data['shipping_firstname'] = $shipping_address['firstname'];
                        $data['shipping_lastname'] = $shipping_address['lastname'];
                        $data['shipping_company'] = $shipping_address['company'];
                        $data['shipping_address_1'] = $shipping_address['address_1'];
                        $data['shipping_address_2'] = $shipping_address['address_2'];
                        $data['shipping_city'] = $shipping_address['city'];
                        $data['shipping_postcode'] = $shipping_address['postcode'];
                        $data['shipping_zone'] = $shipping_address['zone'];
                        $data['shipping_zone_id'] = $shipping_address['zone_id'];
                        $data['shipping_country'] = $shipping_address['country'];
                        $data['shipping_country_id'] = $shipping_address['country_id'];
                        $data['shipping_address_format'] = $shipping_address['address_format'];

                        }
                        else
                        {
                            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$this->session->data['guest']['shipping']['zone_id'] . "'");
            
            if ($zone_query->num_rows) {
                $zone = $zone_query->row['name'];
                // $zone_code = $zone_query->row['code'];
            } else {
                $zone = '';
                // $zone_code = '';
            }

                             $data['shipping_firstname'] = $this->session->data['guest']['shipping']['firstname'];
                        $data['shipping_lastname'] = $this->session->data['guest']['shipping']['firstname'];
                        $data['shipping_company'] = $this->session->data['guest']['payment']['company'];;
                        $data['shipping_address_1'] = $this->session->data['guest']['shipping']['address_1'];;
                        $data['shipping_address_2'] = $this->session->data['guest']['shipping']['address_2'];;
                        $data['shipping_city'] = $this->session->data['guest']['shipping']['city'];;
                        $data['shipping_postcode'] = $this->session->data['guest']['shipping']['postcode'];
                        $data['shipping_zone'] = $zone;
                        $data['shipping_zone_id'] = $this->session->data['guest']['shipping']['zone_id'];;
                        $data['shipping_country'] = $this->session->data['guest']['shipping']['country'];;
                        $data['shipping_country_id'] = $this->session->data['guest']['shipping']['country_id'];;
                        $data['shipping_address_format'] = $this->session->data['guest']['shipping']['address_format'];;
                        }

                       

                        if (isset($this->session->data['shipping_method']['title'])) {
                                $data['shipping_method'] = $this->session->data['shipping_method']['title'];
                        } else {
                                $data['shipping_method'] = '';
                        }

                        if (isset($this->session->data['shipping_method']['code'])) {
                                $data['shipping_code'] = $this->session->data['shipping_method']['code'];
                        } else {
                                $data['shipping_code'] = '';
                        }
                } else {
                        $data['shipping_firstname'] = '';
                        $data['shipping_lastname'] = '';
                        $data['shipping_company'] = '';
                        $data['shipping_address_1'] = '';
                        $data['shipping_address_2'] = '';
                        $data['shipping_city'] = '';
                        $data['shipping_postcode'] = '';
                        $data['shipping_zone'] = '';
                        $data['shipping_zone_id'] = '';
                        $data['shipping_country'] = '';
                        $data['shipping_country_id'] = '';
                        $data['shipping_address_format'] = '';
                        $data['shipping_method'] = '';
                        $data['shipping_code'] = '';
                }

                        $product_data = array();

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];
					} else {
						$value = $this->encryption->decrypt($option['option_value']);
					}

					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $value,
						'type'                    => $option['type']
					);
				}

				$product_data[] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['total'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
			}

			// Gift Voucher
			$voucher_data = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$voucher_data[] = array(
						'description'      => $voucher['description'],
						'code'             => substr(md5(rand()), 0, 7),
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $voucher['amount']
					);
				}
			}

			$data['products'] = $product_data;
			$data['vouchers'] = $voucher_data;



                $data['totals'] = $total_data;
                $data['comment'] = ''; //No comments added yet
                $data['total'] = $total;

                if (isset($this->request->cookie['tracking'])) {
                        $this->load->model('affiliate/affiliate');

                        $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);
                        $subtotal = $this->cart->getSubTotal();

                        if ($affiliate_info) {
                                $data['affiliate_id'] = $affiliate_info['affiliate_id'];
                                $data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                        } else {
                                $data['affiliate_id'] = 0;
                                $data['commission'] = 0;
                        }
                } else {
                        $data['affiliate_id'] = 0;
                        $data['commission'] = 0;
                }

                $data['language_id'] = $this->config->get('config_language_id');
                $data['currency_id'] = $this->currency->getId();
                $data['currency_code'] = $this->currency->getCode();
                $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
                $data['ip'] = $this->request->server['REMOTE_ADDR'];

                if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                        $data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
                } elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
                        $data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
                } else {
                        $data['forwarded_ip'] = '';
                }

                if (isset($this->request->server['HTTP_USER_AGENT'])) {
                        $data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
                } else {
                        $data['user_agent'] = '';
                }

                if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                        $data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
                } else {
                        $data['accept_language'] = '';
                }
                $data['currency_id'] = $this->currency->getId();
		$data['currency'] = $this->currency->getCode();
                $data['value'] = $this->currency->getValue($this->currency->getCode());



                $product_data = array();

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];
					} else {
						$value = $this->encryption->decrypt($option['option_value']);
					}

					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $value,
						'type'                    => $option['type']
					);
				}

				$product_data[] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['total'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
			}


                $this->load->model('checkout/order');

                $this->session->data['order_id'] = method_exists($this->model_checkout_order,'addOrder')?$this->model_checkout_order->addOrder($data):$this->model_checkout_order->create($data);
        }

        /**
        * Combine user attributes with known attributes and fill in defaults when needed.
        *
        * The pairs should be considered to be all of the attributes which are
        * supported by the caller and given as a list. The returned attributes will
        * only contain the attributes in the $pairs list.
        *
        * If the $atts list has unsupported attributes, then they will be ignored and
        * removed from the final returned list.
        *
        * @since 2.5
        *
        * @param array $pairs Entire list of supported attributes and their defaults.
        * @param array $atts User defined attributes in shortcode tag.
        * @return array Combined and filtered attribute list.
        */
        public function shortcode_atts($pairs, $atts) {
                $atts = (array)$atts;
                $out = array();
                foreach($pairs as $name => $default) {
                        if ( array_key_exists($name, $atts) )
                                $out[$name] = $atts[$name];
                        else
                                $out[$name] = $default;
                }
                return $out;
        }

        /*ADD New functions */

        function template_variables($atts,$content,$variable){

            extract($this->shortcode_atts(array(
                    'class' => '',
                    'style' => '',
                    'href' => ''
            ), $atts));


            switch($variable){
                case 'customername':
                    if($this->current_recipient){
                        return sprintf('<span class="%s" style="%s">%s</span>',$class,$style,$this->current_recipient['customer_name']);
                    }
                    break;
                case 'order':
                    if($this->current_recipient){
                        return sprintf('<span class="%s" style="%s">%s</span>',$class,$style,$this->current_recipient['order_id']);
                    }
                    break;
                case 'customerfirstname':
                    if($this->current_recipient){
                        return sprintf('<span class="%s" style="%s">%s</span>',$class,$style,$this->current_recipient['firstname']);
                    }
                    break;
                case 'customerlastname':
                    if($this->current_recipient){
                        return sprintf('<span class="%s" style="%s">%s</span>',$class,$style,$this->current_recipient['lastname']);
                    }
                    break;
                case 'deliveryaddress':
                    if($this->current_recipient){
                        return sprintf('<div class="%s" style="%s">%s %s<br/>%s<br/>%s<br/>%s %s %s<br/>%s</div>',$class,$style,
                                        $this->current_recipient['shipping_firstname'],$this->current_recipient['shipping_lastname'],
                                        $this->current_recipient['shipping_address_1'],
                                        $this->current_recipient['shipping_address_2'],
                                        $this->current_recipient['shipping_city'],$this->current_recipient['shipping_zone'],$this->current_recipient['shipping_postcode'],
                                        $this->current_recipient['shipping_country']);
                    }
                    break;
                case 'cost':
                    $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$this->current_recipient['order_id'] . "' ORDER BY sort_order ASC");
                    $text='';
                    foreach ($order_total_query->rows as $total) {
                            $text .= sprintf('<p class="%s" style="%s"><strong>%s</strong>:%s</p>',$class,$style,$total['title'],html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8')) . "\n";
                    }
                    return $text;
                    break;
                case 'products':
                    $products = $this->db->query("SELECT name,model,quantity,price,total from `".DB_PREFIX."order_product` op where order_id='".(int)$this->current_recipient['order_id']."'");
                    $html = '';
                    if($products->num_rows){
                        $html = "<table class='cart_contents $class' style='$style'><thead><tr><th>".$this->language->get('Name').'</th><th>'.$this->language->get('Model').'</th><th>'.$this->language->get('Quantity').'</th><th>'.$this->language->get('Unit Price').'</th><th>'.$this->language->get('Total').'</th></tr></thead>';
                        foreach($products->rows as $product){
                            $html .= '<tr><td>'.$product['name'].'</td><td>'.$product['model'].'</td><td>'.$product['quantity'].'</td><td>'.$this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $this->current_recipient['currency_code'], $this->current_recipient['currency_value']).'</td><td>'.$this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $this->current_recipient['currency_code'], $this->current_recipient['currency_value']).'</td></tr>';
                        }

                        $totals = $this->db->query("SELECT title,text from `".DB_PREFIX."order_total` ot where order_id='".(int)$this->current_recipient['order_id']."' order by sort_order asc");
                        if($totals->num_rows){
                            $html .= '<tfoot>';
                            foreach($totals->rows as $total){
                                $html .= '<tr><th colspan="4">'.$total['title'].'</th><th>'.$total['text'].'</th></tr>';
                            }
                            $html .= '</tfoot>';
                        }

                        $html .= '</table>';
                    }
                    return $html;
                    break;
                 case 'store':
                    if($this->current_recipient){
                        return sprintf('<span class="%s" style="%s"><a href="%s">%s</a></span>',$class,$style,$this->current_recipient['store_url'],$this->current_recipient['store_name']);
                    }
                    break;
                case 'couponcode':
                    if($this->current_recipient){
                        $coupon_info = $this->get_auto_coupon();
                        $this->addCoupon($coupon_info);
                        return $coupon_info['code'];
                    }
                    break;
            }

        }
        /* Start coupon added for automated customer reminder */
        function get_auto_coupon() {
            $coupon = array();
            $duration = $this->config->get('ccl_auto_coupon_duration');
            $d = "+".$duration;
            $end_date = date('Y-m-d', strtotime($d." day"));

            $coupon['name'] = "Coupon for ".$this->current_recipient['customer_name'];
            $coupon['code'] = $this->generate_auto_coupon();
            $coupon['type'] = "P";
            $coupon['discount'] = $this->config->get('ccl_auto_coupon_value');
            $coupon['total'] = $this->config->get('ccl_auto_coupon_total');
            $coupon['logged'] = 0;
            $coupon['shipping'] = 0;
            $coupon['date_start'] = date('Y-m-d');
            $coupon['date_end'] = $end_date;
            $coupon['uses_total'] = 1;
            $coupon['uses_customer'] = 1;
            $coupon['status'] = 1;
            
            return $coupon;
        }
        public function generate_auto_coupon() {
            $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $res = "";
            for ($i = 0; $i < 6; $i++) {
                $res .= $chars[mt_rand(0, strlen($chars)-1)];
            }
            return $res;
        }
        public function addCoupon($data) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', discount = '" . (float)$data['discount'] . "', type = '" . $this->db->escape($data['type']) . "', total = '" . (float)$data['total'] . "', logged = '" . (int)$data['logged'] . "', shipping = '" . (int)$data['shipping'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "', uses_total = '" . (int)$data['uses_total'] . "', uses_customer = '" . (int)$data['uses_customer'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

            $coupon_id = $this->db->getLastId();
		
	}
        /* End coupon added for automated customer reminder */
         function template_variables_subject($atts,$content,$variable){


            switch($variable){
                case 'customername':
                    if($this->current_recipient){
                        return $this->current_recipient['customer_name'];
                    }
                    break;
                case 'order':
                    if($this->current_recipient){
                        return $this->current_recipient['order_id'];
                    }
                    break;
                case 'customerfirstname':
                    if($this->current_recipient){
                        return $this->current_recipient['firstname'];
                    }
                    break;
                case 'customerlastname':
                    if($this->current_recipient){
                        return $this->current_recipient['lastname'];
                    }
                    break;

                case 'cost':
                    $cost = $this->db->query("SELECT text from `".DB_PREFIX."order_total` ot where (title='Total:' or title='Total') and order_id='".(int)$this->current_recipient['order_id']."'");
                    if($cost->num_rows){
                        return $cost->row['text'];
                    }
                    break;
                 case 'store':
                    if($this->current_recipient){
                        return $this->current_recipient['store_name'];
                    }
                    break;
            }

        }

		function getSetting($group, $store_id = 0) {
		$data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($group) . "'");

		foreach ($query->rows as $result) {
			//if (!$result['serialized']) {
				$data[$result['key']] = $result['value'];
			//} else {
				//$data[$result['key']] = unserialize($result['value']);
			//}
		}

		return $data;
	}
}

//added new class
class Shortcodes {
    /**
    * WordPress API for creating bbcode like tags or what WordPress calls
    * "shortcodes." The tag and attribute parsing or regular expression code is
    * based on the Textpattern tag parser.
    *
    * A few examples are below:
    *
    * [shortcode /]
    * [shortcode foo="bar" baz="bing" /]
    * [shortcode foo="bar"]content[/shortcode]
    *
    * Shortcode tags support attributes and enclosed content, but does not entirely
    * support inline shortcodes in other shortcodes. You will have to call the
    * shortcode parser in your function to account for that.
    *
    * {@internal
    * Please be aware that the above note was made during the beta of WordPress 2.6
    * and in the future may not be accurate. Please update the note when it is no
    * longer the case.}}
    *
    * To apply shortcode tags to content:
    *
    * <code>
    * $out = do_shortcode($content);
    * </code>
    *
    * @link http://codex.wordpress.org/Shortcode_API
    *
    * @package WordPress
    * @subpackage Shortcodes
    * @since 2.5
    */

    /**
    * Container for storing shortcode tags and their hook to call for the shortcode
    *
    * @since 2.5
    * @name $shortcode_tags
    * @var array
    * @global array $shortcode_tags
    */
    public $shortcode_tags = array();


    /**
    * Add hook for shortcode tag.
    *
    * There can only be one hook for each shortcode. Which means that if another
    * plugin has a similar shortcode, it will override yours or yours will override
    * theirs depending on which order the plugins are included and/or ran.
    *
    * Simplest example of a shortcode tag using the API:
    *
    * <code>
    * // [footag foo="bar"]
    * function footag_func($atts) {
    * 	return "foo = {$atts[foo]}";
    * }
    * add_shortcode('footag', 'footag_func');
    * </code>
    *
    * Example with nice attribute defaults:
    *
    * <code>
    * // [bartag foo="bar"]
    * function bartag_func($atts) {
    * 	extract(shortcode_atts(array(
    * 		'foo' => 'no foo',
    * 		'baz' => 'default baz',
    * 	), $atts));
    *
    * 	return "foo = {$foo}";
    * }
    * add_shortcode('bartag', 'bartag_func');
    * </code>
    *
    * Example with enclosed content:
    *
    * <code>
    * // [baztag]content[/baztag]
    * function baztag_func($atts, $content='') {
    * 	return "content = $content";
    * }
    * add_shortcode('baztag', 'baztag_func');
    * </code>
    *
    * @since 2.5
    * @uses $shortcode_tags
    *
    * @param string $tag Shortcode tag to be searched in post content.
    * @param callable $func Hook to run when shortcode is found.
    */
    public function add_shortcode($tag, $func) {

            if ( is_callable($func) )
                    $this->shortcode_tags[$tag] = $func;
    }

    /**
    * Removes hook for shortcode.
    *
    * @since 2.5
    * @uses $shortcode_tags
    *
    * @param string $tag shortcode tag to remove hook for.
    */
    public function remove_shortcode($tag) {

            unset($this->shortcode_tags[$tag]);
    }

    /**
    * Clear all shortcodes.
    *
    * This function is simple, it clears all of the shortcode tags by replacing the
    * shortcodes global by a empty array. This is actually a very efficient method
    * for removing all shortcodes.
    *
    * @since 2.5
    * @uses $shortcode_tags
    */
    public function remove_all_shortcodes() {


            $this->shortcode_tags = array();
    }

    /**
    * Search content for shortcodes and filter shortcodes through their hooks.
    *
    * If there are no shortcode tags defined, then the content will be returned
    * without any filtering. This might cause issues when plugins are disabled but
    * the shortcode will still show up in the post or content.
    *
    * @since 2.5
    * @uses $shortcode_tags
    * @uses get_shortcode_regex() Gets the search pattern for searching shortcodes.
    *
    * @param string $content Content to search for shortcodes
    * @return string Content with shortcodes filtered out.
    */
    public function do_shortcode($content) {

            if (empty($this->shortcode_tags) || !is_array($this->shortcode_tags))
                    return $content;

            $pattern = $this->get_shortcode_regex();
            return preg_replace_callback( "/$pattern/s", array( &$this, 'do_shortcode_tag'), $content );
    }

    /**
    * Retrieve the shortcode regular expression for searching.
    *
    * The regular expression combines the shortcode tags in the regular expression
    * in a regex class.
    *
    * The regular expression contains 6 different sub matches to help with parsing.
    *
    * 1 - An extra [ to allow for escaping shortcodes with double [[]]
    * 2 - The shortcode name
    * 3 - The shortcode argument list
    * 4 - The self closing /
    * 5 - The content of a shortcode when it wraps some content.
    * 6 - An extra ] to allow for escaping shortcodes with double [[]]
    *
    * @since 2.5
    * @uses $shortcode_tags
    *
    * @return string The shortcode search regular expression
    */
    public function get_shortcode_regex() {
            $tagnames = array_keys($this->shortcode_tags);
            $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

            // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
            return
                    '\\['                              // Opening bracket
                    . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
                    . "($tagregexp)"                     // 2: Shortcode name
                    . '\\b'                              // Word boundary
                    . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
                    .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
                    .     '(?:'
                    .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
                    .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
                    .     ')*?'
                    . ')'
                    . '(?:'
                    .     '(\\/)'                        // 4: Self closing tag ...
                    .     '\\]'                          // ... and closing bracket
                    . '|'
                    .     '\\]'                          // Closing bracket
                    .     '(?:'
                    .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                    .             '[^\\[]*+'             // Not an opening bracket
                    .             '(?:'
                    .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
                    .                 '[^\\[]*+'         // Not an opening bracket
                    .             ')*+'
                    .         ')'
                    .         '\\[\\/\\2\\]'             // Closing shortcode tag
                    .     ')?'
                    . ')'
                    . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }

    /**
    * Regular Expression callable for do_shortcode() for calling shortcode hook.
    * @see get_shortcode_regex for details of the match array contents.
    *
    * @since 2.5
    * @access private
    * @uses $shortcode_tags
    *
    * @param array $m Regular expression match array
    * @return mixed False on failure.
    */
    public function do_shortcode_tag( $m ) {

            // allow [[foo]] syntax for escaping a tag
            if ( $m[1] == '[' && $m[6] == ']' ) {
                    return substr($m[0], 1, -1);
            }

            $tag = $m[2];
            $attr = $this->shortcode_parse_atts( $m[3] );

            if ( isset( $m[5] ) ) {
                    // enclosing tag - extra parameter
                    return $m[1] . call_user_func( $this->shortcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
            } else {
                    // self-closing tag
                    return $m[1] . call_user_func( $this->shortcode_tags[$tag], $attr, NULL,  $tag ) . $m[6];
            }
    }

    /**
    * Retrieve all attributes from the shortcodes tag.
    *
    * The attributes list has the attribute name as the key and the value of the
    * attribute as the value in the key/value pair. This allows for easier
    * retrieval of the attributes, since all attributes have to be known.
    *
    * @since 2.5
    *
    * @param string $text
    * @return array List of attributes and their value.
    */
    public function shortcode_parse_atts($text) {
            $atts = array();
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
            $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
            if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
                    foreach ($match as $m) {
                            if (!empty($m[1]))
                                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                            elseif (!empty($m[3]))
                                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                            elseif (!empty($m[5]))
                                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                            elseif (isset($m[7]) and strlen($m[7]))
                                    $atts[] = stripcslashes($m[7]);
                            elseif (isset($m[8]))
                                    $atts[] = stripcslashes($m[8]);
                    }
            } else {
                    $atts = ltrim($text);
            }
            return $atts;
    }

    /**
    * Combine user attributes with known attributes and fill in defaults when needed.
    *
    * The pairs should be considered to be all of the attributes which are
    * supported by the caller and given as a list. The returned attributes will
    * only contain the attributes in the $pairs list.
    *
    * If the $atts list has unsupported attributes, then they will be ignored and
    * removed from the final returned list.
    *
    * @since 2.5
    *
    * @param array $pairs Entire list of supported attributes and their defaults.
    * @param array $atts User defined attributes in shortcode tag.
    * @return array Combined and filtered attribute list.
    */
    public function shortcode_atts($pairs, $atts) {
            $atts = (array)$atts;
            $out = array();
            foreach($pairs as $name => $default) {
                    if ( array_key_exists($name, $atts) )
                            $out[$name] = $atts[$name];
                    else
                            $out[$name] = $default;
            }
            return $out;
    }

    /**
    * Remove all shortcode tags from the given content.
    *
    * @since 2.5
    * @uses $shortcode_tags
    *
    * @param string $content Content to remove shortcode tags.
    * @return string Content without shortcode tags.
    */
    public function strip_shortcodes( $content ) {


            if (empty($this->shortcode_tags) || !is_array($this->shortcode_tags))
                    return $content;

            $pattern = $this->get_shortcode_regex();

            return preg_replace_callback( "/$pattern/s", array( &$this, 'strip_shortcode_tag'), $content );
    }

    public function strip_shortcode_tag( $m ) {
            // allow [[foo]] syntax for escaping a tag
            if ( $m[1] == '[' && $m[6] == ']' ) {
                    return substr($m[0], 1, -1);
            }

            return $m[1] . $m[6];
    }


}

?>
