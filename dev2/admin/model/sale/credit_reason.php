<?php
class ModelSaleCreditReason extends Model {
	
	public function addReason($data){
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "store_credit_reason` SET name='".$this->db->escape($data['name'])."',code='".$this->db->escape($data['code'])."',message='".$this->db->escape($data['message'])."',status='".(int)($data['status'])."',date_added=NOW(),user_id='".$this->user->getId()."'");
		
		return $this->db->getLastId();
		
		
		}
		
		
		
		public function editReason($reason_id,$data){
		
		$this->db->query("UPDATE `" . DB_PREFIX . "store_credit_reason` SET name='".$this->db->escape($data['name'])."',code='".$this->db->escape($data['code'])."',message='".$this->db->escape($data['message'])."',status='".(int)($data['status'])."',date_modified=NOW(),user_id='".$this->user->getId()."' WHERE reason_id='".(int)$reason_id."'");
		
		//return $this->db->getLastId();
		
		
		}
		
		public function getTotalReasons()
	{
		$sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "store_credit_reason` ";
		
		
		
		$query = $this->db->query($sql);
		
		return $query->row['total'];
		
	}
	
	
	public function getReason($reason_id)
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . "store_credit_reason` WHERE reason_id='".(int)$reason_id."'";
		
		
		
		$query = $this->db->query($sql);
		
		return $query->row;
		
	}
	
	
	public function getReasonByCode($code)
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . "store_credit_reason` WHERE code='".$code."'";
		
		
		
		$query = $this->db->query($sql);
		
		return $query->row;
		
	}
	
	public function getReasons($data)
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . "store_credit_reason` ";
		
		$sort_data = array(
			'code',
			'name',
			'status',
			'date_added'
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY date_added";	
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
	
	
	public function deleteReason($reason_id) {
      	$this->db->query("DELETE FROM " . DB_PREFIX . "store_credit_reason WHERE reason_id = '" . (int)$reason_id . "'");
		
	}
	
	
	
	}
?>