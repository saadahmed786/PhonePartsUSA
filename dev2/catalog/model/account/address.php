<?php
class ModelAccountAddress extends Model {
	public function addAddress($data) {
		
		$check = $this->db->query("SELECT address_id  from ".DB_PREFIX."address where firstname='".$this->db->escape($data['firstname'])."' and lastname='".$this->db->escape($data['lastname'])."' and address_1='".$this->db->escape($data['address_1'])."' and address_2='".$this->db->escape($data['address_2'])."' and city='".$this->db->escape($data['city'])."' and postcode='".$this->db->escape($data['postcode'])."' and zone_id='".$this->db->escape($data['zone_id'])."' and customer_id='".$this->customer->getId()."' and country_id='".$this->db->escape($data['country_id'])."'   ");
		if(!$check->row['address_id'])
		{	
		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$this->customer->getId() . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', company_id = '" . $this->db->escape(isset($data['company_id']) ? $data['company_id'] : '') . "', tax_id = '" . $this->db->escape(isset($data['tax_id']) ? $data['tax_id'] : '') . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "'");
		
		$address_id = $this->db->getLastId();
		
		if (!empty($data['default'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
		
		$check_po_customer = $this->db->query("SELECT * FROM inv_po_customers WHERE LOWER(email)='".$this->db->escape($this->customer->getEmail())."'");
		$check_po_customer = $check_po_customer->row;
		if($check_po_customer)
		{
			$state = $this->db->query("SELECT name FROM oc_zone WHERE zone_id='".$data['zone_id']."'");
			$this->db->query("INSERT INTO inv_po_address SET po_customer_id='".$check_po_customer['id']."',address='".$this->db->escape($data['address_1'])."',city='".$this->db->escape($data['city'])."',state='".$state->row['name']."',zip='" . $this->db->escape($data['postcode']) . "',oc_address_id='".$address_id."'");	
			
		}
	}
	else
	{
		$address_id = $check->row['address_id'];
	}

		return $address_id;
	}
	
	public function editAddress($address_id, $data) {
		if ($data['password']) {
			$password = ", password = '" . $this->db->escape(md5($data['password'])) . "'";
		} else {
			$password = "";
		}
		$this->db->query("UPDATE " . DB_PREFIX . "address SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', company_id = '" . $this->db->escape(isset($data['company_id']) ? $data['company_id'] : '') . "', tax_id = '" . $this->db->escape(isset($data['tax_id']) ? $data['tax_id'] : '') . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "'". $password .", country_id = '" . (int)$data['country_id'] . "' WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
		if (!empty($data['default'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
		
		$check_po_customer = $this->db->query("SELECT * FROM inv_po_customers WHERE LOWER(email)='".$this->db->escape($this->customer->getEmail())."'");
		$check_po_customer = $check_po_customer->row;
		if($check_po_customer) {
			$state = $this->db->query("SELECT name FROM oc_zone WHERE zone_id='".$data['zone_id']."'");
			$this->db->query("UPDATE inv_po_address SET po_customer_id='".$check_po_customer['id']."',address='".$this->db->escape($data['address_1'])."',city='".$this->db->escape($data['city'])."',state='".$state->row['name']."'". $password .",zip='" . $this->db->escape($data['postcode']) . "' WHERE oc_address_id='".$address_id."'");	
			
		}
		
	}
	public function editAddressNew($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "address SET firstname='".$this->db->escape($data['firstname'])."',lastname='".$this->db->escape($data['lastname'])."',company='".$this->db->escape($data['business_name'])."', address_1 = '" . $this->db->escape($data['address_1']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] ."', country_id = '" . (int)$data['country_id'] . "', address_2 = '" . $this->db->escape($data['suite']) . "' WHERE address_id  = '" . (int)$data['address_id'] . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
	}
	
	public function deleteAddress($address_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		$this->db->query("DELETE FROM inv_po_address WHERE oc_address_id='".$address_id."'");
	}	
	
	public function getAddress($address_id) {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}		
			
			$address_data = array(
				'address_id'     => $address_query->row['address_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'company_id'     => $address_query->row['company_id'],
				'tax_id'         => $address_query->row['tax_id'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format,
				'suite'   		   => $address_query->row['suite']
				);
			
			return $address_data;
		} else {
			return false;	
		}
	}
	
	public function getAddresses() {
		$address_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "' and is_contact=0");
		
		foreach ($query->rows as $result) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$result['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}		
			
			$address_data[$result['address_id']] = array(
				'address_id'     => $result['address_id'],
				'firstname'      => $result['firstname'],
				'lastname'       => $result['lastname'],
				'company'        => $result['company'],
				'company_id'     => $result['company_id'],
				'tax_id'         => $result['tax_id'],				
				'address_1'      => $result['address_1'],
				'address_2'      => $result['address_2'],
				'postcode'       => $result['postcode'],
				'city'           => $result['city'],
				'zone_id'        => $result['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $result['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format,
				'suite'   		   => $result['suite']
				);
		}		
		
		return $address_data;
	}	
	
	public function getTotalAddresses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		
		return $query->row['total'];
	}
	public function getSignProduct() {
		$query = $this->db->query("SELECT `product_id` FROM `oc_product` WHERE `sku` = 'SIGN'");
		return $query->row['product_id'];
	}
	public function setDefault($address_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}
	public function getContactInformations()
	{
		$address_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "' and is_contact=1");
		foreach ($query->rows as $result) {
			
			
			$address_data[] = array(
				'address_id'     => $result['address_id'],
				'firstname'      => $result['firstname'],
				'lastname'       => $result['lastname'],
				'company'        => $result['company'],
				'telephone_1'	 => $result['contact_telephone_1'],
				'telephone_2'	 => $result['contact_telephone_2'],
				);
		}		
		
		return $address_data;		
	}
}
?>