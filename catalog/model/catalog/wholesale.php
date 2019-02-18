<?php
class ModelCatalogWholesale extends Model {
	public function addWholeSaleAccount ($data) {
		unset($data['emailVerify'], $data['submit']);
		$data['intrested'] = implode(', ', $data['intrested']);
	if($data['theme'] == '2')
	{
		$phonedata = array();
		foreach ($data['phoneselector'] as $i => $type){
					$phonedata[$i]['type']= $type[$i];
					$phonedata[$i]['number']= $data['phonenumber'][$i];
		}
		$data['phones']=base64_encode(serialize($phonedata));
		unset($data['phoneselector'], $data['phonenumber'],$data['theme'],$data['country_id']);
	}
		$sq = array();

		foreach ($data as $key => $value) {
			$sq[] = $key . ' = "' . $this->db->escape($value) . '"';
		}

		$this->db->query('INSERT INTO ' . DB_PREFIX . 'wholesale_account SET '. implode(', ', $sq) .', date_added = NOW()');
	}
	public function addWholeSaleAccountNew($data)
	{

		$this->db->query("INSERT INTO ".DB_PREFIX."wholesale_account SET first_name='".$this->db->escape(ucfirst($data['first_name']))."',last_name='".$this->db->escape(ucfirst($data['last_name']))."',mobile='".$this->db->escape($data['work_number'])."',personal_email='".$this->db->escape(strtolower($data['email']))."',company_name='".$this->db->escape($data['company']['name'])."',type_of_business='".$this->db->escape($data['custom_field']['cf_business_type'])."',no_of_locations='".$this->db->escape($data['custom_field']['cf_of_locations'])."',repairs='".$this->db->escape($data['custom_field']['cf_average_repairs_per_week'])."',how_did_you_hear='".$this->db->escape($data['custom_field']['cf_how_did_you_hear_about_us'])."',date_added=NOW()");
	}

	public function addImpAccount($data)
	{

		$customer_id = $this->db->query("SELECT customer_id FROM ".DB_PREFIX."customer WHERE TRIM(LOWER(email))='".$this->db->escape(strtolower($data['email']))."'");
		$customer_id = $customer_id->row['customer_id'];
		$this->db->query("INSERT INTO inv_customers SET firstname='".$this->db->escape($data['first_name'])."',lastname='".$this->db->escape(($data['last_name']))."',telephone='".$this->db->escape($data['work_number'])."',email='".$this->db->escape(strtolower($data['email']))."',customer_group='Wholesale Small',no_of_orders=0,total_amount=0.00,last_order='0000-00-00 00:00:00',customer_id='".(int)$customer_id."',is_synced_from_fs=1,date_added=NOW(),is_business_customer=1,company='".$this->db->escape($data['company']['name'])."'");
	}

	public function getText() {
		$sql = "SELECT * FROM `oc_wholesale_setting` WHERE `id` = '1'";
		$query = $this->db->query($sql);
		return $query->row;
	}
	public function getState ($country) {
		$sql = "SELECT * FROM `oc_zone` WHERE `country_id` = (SELECT `country_id` FROM `oc_country` WHERE `iso_code_3` = '$country')";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getTotalRequestByEmail ($email) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "wholesale_account` WHERE LCASE(`email`) = LCASE('" . $this->db->escape(strtolower($email)) . "')";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	public function getAccount ($email) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "wholesale_account` WHERE LCASE(`personal_email`) = LCASE('" . $this->db->escape(strtolower($email)) . "')";
		$query = $this->db->query($sql);
		$data = $query->row;
		if ($data['personal_email']) {
			return true;
		} else {
			return false;
		}
	}
}