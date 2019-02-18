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