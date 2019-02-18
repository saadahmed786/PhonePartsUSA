<?php
class ModelAccountReturns extends Model {
	public function getReturn($return_id) {
		$query = $this->db->query("SELECT * FROM `inv_returns` WHERE id = '" . (int)$return_id . "'");
		
		return $query->row;
	}

	public function getReturns($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		
		$query = $this->db->query("SELECT * FROM `inv_returns` WHERE LCASE(email) = LCASE('". $this->customer->getEmail() ."') ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getReturnsReplacements($return_id) {
		$query = $this->db->query("SELECT * FROM `inv_return_decision` WHERE return_id = '" . (int)$return_id . "' AND action = 'Issue Replacement'");

		return $query->rows;
	}

	public function getReturnsCredits($return_id) {
		$query = $this->db->query("SELECT * FROM `inv_return_decision` WHERE return_id = '" . (int)$return_id . "' AND action = 'Issue Credit'");

		return $query->rows;
	}

	public function getReturnsRefunds($return_id) {
		$query = $this->db->query("SELECT * FROM `inv_return_decision` WHERE return_id = '" . (int)$return_id . "' AND action = 'Issue Refund'");

		return $query->rows;
	}
	
	public function getReturnItemDecision($return_item_id) {
		$query = $this->db->query("SELECT * FROM `inv_return_decision` WHERE return_item_id = '" . (int)$return_item_id . "'");

		return $query->row;
	}
	
	public function getReturnProducts($return_id) {
		$query = $this->db->query("SELECT * FROM `inv_return_items` WHERE return_id = '" . (int)$return_id . "' AND removed = 0");
		
		return $query->rows;
	}

	public function getTotalReturns() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `inv_returns` WHERE LCASE(email) = LCASE('". $this->customer->getEmail() ."')");
		
		return $query->row['total'];
	}
	
}
?>