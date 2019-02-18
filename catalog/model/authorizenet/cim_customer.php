<?php
/**
 * Contains part of the Opencart Authorize.Net CIM Payment Module code.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to memiiso license.
 * Please see the LICENSE.txt file for more information.
 * All other rights reserved.
 *
 * @author     memiiso <gel.yine.gel@hotmail.com>
 * @copyright  2013-~ memiiso
 * @license    Commercial License. Please see the LICENSE.txt file
 */

class ModelAuthorizenetCimCustomer extends Model {
	
	var $table_cim = "authorizenet_cim";
	var $table_cim_payment_profiles = "authorizenet_cim_payment_profiles";
	var $table_cim_order_response = "authorizenet_cim_order_response";
	
	
	/* CIM CUSTOMER PROFILE */ 
	public function addCimCustomer($customer_id,$cim_id) {
		$result = $this->db->query("INSERT INTO " . DB_PREFIX.$this->table_cim . " 
				SET customer_id = '" . (int)$this->db->escape($customer_id) . "'
				, cim_id = '" . (int)$this->db->escape($cim_id) . "'
				, update_date = ''");
		
		$this->cache->delete('CimCustomer');
		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}
	
	public function editCimCustomer($customer_id, $cim_id) {
		$result = $this->db->query("UPDATE " . DB_PREFIX.$this->table_cim . " 
				SET cim_id = '" . $this->db->escape($cim_id). "'
				, update_date = NOW()
				WHERE customer_id=".(int)$this->db->escape($customer_id));
		
		$this->cache->delete('CimCustomer');

		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}
	
	public function deleteCimCustomer($customer_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX.$this->table_cim . " WHERE customer_id = '" . (int)$this->db->escape($customer_id) . "'");
		$this->deletePaymentProfiles($customer_id);
		$this->cache->delete('CimCustomer');
	}
	
	public function getCimCustomer($customer_id) {
		$result = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX.$this->table_cim . " 
				WHERE customer_id=".(int)$this->db->escape($customer_id)." 
				ORDER BY insert_date DESC, update_date DESC LIMIT 1");
				
		return $result->row;
	}
	
	public function getCimCustomerCimID($customer_id) {
		$result = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX.$this->table_cim . "
				WHERE customer_id=".(int)$this->db->escape($customer_id)."
				ORDER BY insert_date DESC, update_date DESC LIMIT 1");
	
		if ($result->num_rows > 0) {
			return $result->row['cim_id'];
		} else return false;
	}	
	
	/* CIM CUSTOMER PAYMENT PROFILES */
	public function addPaymentProfile(
			$customer_id,
			$payment_profile_id,
			$cc_type='',
			$default=0
			){
		$account_type = '';
		$customer_type= '';
		$name_on_account='';
		$account_number='';
		$routing_number='';
		$bank_name='';
				
		$sql="INSERT INTO " . DB_PREFIX.$this->table_cim_payment_profiles ." 
				(`customer_id`, `payment_profile_id`, `account_type`, `customer_type`, `name_on_account`, 
						`account_number`, `routing_number`, `bank_name`, `cc_type`, `cim_sc_default`) ";
		$sql.="	VALUES ('".(int)$this->db->escape($customer_id)."'
		, '".(int)$this->db->escape($payment_profile_id)."'
		, '".$this->db->escape($account_type)."'
		, '".$this->db->escape($customer_type)."'
		, '".$this->db->escape($name_on_account)."'
		, '".$this->db->escape($account_number)."'
		, '".$this->db->escape($routing_number)."'
		, '".$this->db->escape($bank_name)."'
		, '".$this->db->escape($cc_type)."'
		, '".(int)$this->db->escape($default)."');";

		$result = $this->db->query($sql);
		$this->cache->delete('CimCustomer');
		
		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}
	
	public function deletePaymentProfile($customer_id,$payment_profile_id) {
		$result = $this->db->query("DELETE FROM " . DB_PREFIX.$this->table_cim_payment_profiles . " WHERE customer_id = '" . (int)$this->db->escape($customer_id) . "' AND payment_profile_id = '" . (int)$this->db->escape($payment_profile_id) . "';");
		$this->cache->delete('CimCustomer');
	}
	public function deletePaymentProfiles($customer_id) {
		$result = $this->db->query("DELETE FROM " . DB_PREFIX.$this->table_cim_payment_profiles . " WHERE customer_id = '" . (int)$this->db->escape($customer_id) . "' ;");
		$this->cache->delete('CimCustomer');
		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}
	
	public function setDefaultPaymentProfile($customer_id,$payment_profile_id) {
		$result = $this->db->query("
				UPDATE " . DB_PREFIX.$this->table_cim_payment_profiles . "
				SET `default` = CASE
									WHEN payment_profile_id = ".(int)$this->db->escape($payment_profile_id)." THEN 1
									ELSE 0
								END
				, update_date = NOW()
				WHERE customer_id=".(int)$this->db->escape($customer_id)."
				AND ( payment_profile_id = '" . (int)$this->db->escape($payment_profile_id) . "' OR `default` = 1 )
				 ;");
	
		$this->cache->delete('CimCustomer');
		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}
	public function getDefaultPaymentProfileId($customer_id) {
		$result = $this->db->query(" SELECT t.* FROM " . DB_PREFIX.$this->table_cim_payment_profiles ." t
				WHERE t.customer_id=".(int)$this->db->escape($customer_id)." AND t.default='1'
				ORDER BY t.insert_date DESC, t.update_date DESC LIMIT 1 ;");
		if ($result->num_rows > 0) {
			return $result->row['payment_profile_id'];
		} else return 0;
	}

	public function getCimPaymentProfiles($customer_id) {
		$result = $this->db->query(" SELECT * FROM " . DB_PREFIX.$this->table_cim_payment_profiles ."
				WHERE customer_id=".(int)$this->db->escape($customer_id)."
				");
		$data =array();
		foreach ($result->rows as $row){
			$nrow=(object)array('customer_id'=>$row['customer_id'],'payment_profile_id'=>$row['payment_profile_id'],'cc_type'=>$row['cc_type'], 'cim_sc_default'=>$row['cim_sc_default']);
			$data['pid_'.$row['payment_profile_id']] = $nrow;
		}
		return $data;
	}
	
	public function setScPaymentProfile($customer_id,$payment_profile_id) {
		$result = $this->db->query("
				UPDATE " . DB_PREFIX.$this->table_cim_payment_profiles . "
				SET `cim_sc_default` = CASE 
									WHEN payment_profile_id = ".(int)$this->db->escape($payment_profile_id)." THEN 1
									ELSE 0
								END
				, update_date = NOW()				
				WHERE customer_id=".(int)$this->db->escape($customer_id)." 
				AND ( payment_profile_id = '" . (int)$this->db->escape($payment_profile_id) . "' OR `cim_sc_default` = 1 )
				 ;");
		
		$this->cache->delete('CimCustomer');
		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}	

	public function getScPaymentProfileId($customer_id) {
		$result = $this->db->query(" SELECT t.* FROM " . DB_PREFIX.$this->table_cim_payment_profiles ." t
				WHERE t.customer_id=".(int)$this->db->escape($customer_id)." AND t.cim_sc_default='1'
				ORDER BY t.insert_date DESC, t.update_date DESC LIMIT 1 ;");
		if ($result->num_rows > 0) {
			return $result->row['payment_profile_id'];
		} else return 0;
	}
	
	/* CIM CUSTOMER SHIPPING ADDRESSES */
	public function getCustomerCimShippingAddressId($customer_id,$address_id){
		$result = $this->db->query(" SELECT * FROM " . DB_PREFIX."address
				WHERE customer_id=".(int)$this->db->escape($customer_id)." 
				AND address_id = '" . (int)$this->db->escape($address_id) . "'	
				AND cim_shipping_address_id <> ''
				AND cim_shipping_address_id <> 0
				AND cim_shipping_address_id IS NOT NULL
				AND cim_shipping_address_id > 0
				;");		
		if ($result->num_rows > 0) {
			return $result->row['cim_shipping_address_id'];
		} else return false;
	}
	
	public function addCustomerCimShippingAddressId($customer_id,$address_id,$cim_address_ID){
		$result = $this->db->query("
				UPDATE " . DB_PREFIX."address
				SET cim_shipping_address_id 	 = 	".(int)$this->db->escape($cim_address_ID)."
				, update_date = NOW()
				WHERE customer_id=".(int)$this->db->escape($customer_id)."
				AND address_id = '" . (int)$this->db->escape($address_id) . "'
				 ;");
		$this->cache->delete('CimCustomer');
		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}
	
	
	/* CIM CUSTOMER SINGLE CLICK SHIPPING ADDRESSES */
	public function setScShippingAddress($customer_id,$adress_id,$shipping_settings) {
		$result = $this->db->query("
				UPDATE " . DB_PREFIX."address 
				SET cim_sc_shipping_default 	 = 	CASE 
									WHEN address_id = ".(int)$this->db->escape($adress_id)." THEN 1
									ELSE 0
								END
				,cim_sc_shipping_settings 	 = 	CASE 
									WHEN address_id = ".(int)$this->db->escape($adress_id)." THEN ".(int)$this->db->escape($shipping_settings)."
									ELSE NULL
								END
				, update_date = NOW()
				WHERE customer_id=".(int)$this->db->escape($customer_id)." 
				AND ( address_id = '" . (int)$this->db->escape($adress_id) . "' OR cim_sc_shipping_default = 1 )
				 ;");
		
		$this->cache->delete('CimCustomer');
		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}

	public function getScShippingAddressId($customer_id) {
		$result = $this->db->query(" SELECT * FROM " . DB_PREFIX."address
				WHERE customer_id=".(int)$this->db->escape($customer_id)." AND cim_sc_shipping_default = 1 
				ORDER BY insert_date DESC, update_date DESC LIMIT 1;");
		
		if ($result->num_rows > 0) {
			return array($result->row['address_id'] , $result->row['cim_sc_shipping_settings'] );
		} else return array(false , false);
	}
	
	/* CIM CUSTOMER BILLING ADDRESSES */
	public function setScBillingAddress($customer_id,$adress_id) {
		$result = $this->db->query("
				UPDATE " . DB_PREFIX."address
				SET cim_sc_billing_default 	 = 	CASE
									WHEN address_id = ".(int)$this->db->escape($adress_id)." THEN 1
									ELSE 0
								END
				, update_date = NOW()
				WHERE customer_id=".(int)$this->db->escape($customer_id)."
				AND ( address_id = '" . (int)$this->db->escape($adress_id) . "' OR cim_sc_billing_default = 1 )
				 ;");
	
		$this->cache->delete('CimCustomer');
		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}
	
	public function getScBillingAddressId($customer_id) {
		$result = $this->db->query(" SELECT * FROM " . DB_PREFIX."address
				WHERE customer_id=".(int)$this->db->escape($customer_id)." AND cim_sc_billing_default = 1
				ORDER BY insert_date DESC, update_date DESC LIMIT 1;");
	
		if ($result->num_rows > 0) {
			return $result->row['address_id'];
		} else return false;
	}
	
	
	/* CUSTOMER ADDRESSES  */
	public function getCustomerAddresses($customer_id) {
		$result = $this->db->query(" SELECT * FROM " . DB_PREFIX."address
				WHERE customer_id=".(int)$this->db->escape($customer_id)."
				;");
		return $result->rows;
	}
	
	/* ORDER RESPONSE LOG */
	public function addCimOrderResponse($order_id,$cim_transaction_id,$response_code,$response_reason_code,$response_reason_text,$response_text,$response_json) {
		$result = $this->db->query("INSERT INTO " . DB_PREFIX.$this->table_cim_order_response . "				
				(`order_id`, `cim_transaction_id`, `response_code`, `response_reason_code`, `response_reason_text`,
				 `response_text`, `response_json`)
				VALUES ('".(int)$this->db->escape($order_id)."',
				 '".(int)$this->db->escape($cim_transaction_id)."',
				 '".(int)$this->db->escape($response_code)."',
				 '".(int)$this->db->escape($response_reason_code)."',
				 '".$this->db->escape($response_reason_text)."',
				 '".$this->db->escape($response_text)."',
				 '".$this->db->escape($response_json)."'
				 );");
	
		return isset($result->num_rows) ? $result->num_rows : 0 ;
	}	

	/* OPENCART  */
	public function getOrderStatuses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
				
			$sql .= " ORDER BY name";
				
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
		} else {
			$order_status_data = $this->cache->get('order_status.' . (int)$this->config->get('config_language_id'));
	
			if (!$order_status_data) {
				$query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
	
				$order_status_data = $query->rows;
					
				$this->cache->set('order_status.' . (int)$this->config->get('config_language_id'), $order_status_data);
			}
	
			return $order_status_data;
		}
	}
	
}
?>
