<?php
class ModelAccountLbb extends Model {
	public function getLbb($buyback_id) {
		$query = $this->db->query("SELECT a.* FROM `" . DB_PREFIX . "buyback` a left join ".DB_PREFIX."customer b on(a.customer_id=b.customer_id) WHERE a.buyback_id = '" . (int)$buyback_id . "' AND (a.email = '" . $this->customer->getEmail() . "'  or LCASE(b.email) = LCASE('". $this->customer->getEmail() ."')) ");
		
		return $query->row;
	}

	public function getLbbs($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
	//	echo "SELECT * FROM `" . DB_PREFIX . "buyback` WHERE email = '". $this->customer->getEmail() ."' ORDER BY buyback_id DESC LIMIT " . (int)$start . "," . (int)$limit
		$query = $this->db->query("SELECT a.* FROM `" . DB_PREFIX . "buyback` a left join ".DB_PREFIX."customer b on(a.customer_id=b.customer_id) WHERE LCASE(a.email) = LCASE('". $this->customer->getEmail() ."') or LCASE(b.email) = LCASE('". $this->customer->getEmail() ."') ORDER BY buyback_id DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}
	public function getAddressDetail($address_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '$address_id'");

		return $query->row;
	}
	
	public function getLbbProducts($buyback_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "buyback_products WHERE buyback_id = '" . (int)$buyback_id . "' AND data_type = 'customer'");
		
		return $query->rows;
	}
	
	public function getLbbPayment($buyback_id) {
		$query = $this->db->query("SELECT * FROM inv_buyback_payments  WHERE buyback_id = '" . (int)$buyback_id . "'");

		return $query->row;
	}

	public function getTotalLbb() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "buyback` WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		
		return $query->row['total'];
	}
	
}
?>