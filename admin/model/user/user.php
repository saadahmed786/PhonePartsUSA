<?php
class ModelUserUser extends Model {
	public function addUser($data) {
		
		if(isset($data['view_returned_items'])) $view_retuend_items = 1; else $view_retuend_items = 0;
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', password = '" . $this->db->escape(md5($data['password'])) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', user_group_id = '" . (int)$data['user_group_id'] . "', status = '" . (int)$data['status'] . "', date_added = NOW(),view_returned_items='".$view_retuend_items."'");
	}
	
	public function editUser($user_id, $data) {
		if(isset($data['view_returned_items'])) $view_retuend_items = 1; else $view_retuend_items = 0;
		if(isset($data['can_edit_order'])) $can_edit_order = 1; else $can_edit_order = 0;
		if(isset($data['update_b_order'])) $update_b_order = 1; else $update_b_order = 0;
		if(isset($data['pos_user'])) $pos_user = 1; else $pos_user = 0;
		if(isset($data['can_create_order'])) $can_create_order = 1; else $can_create_order = 0;
		if(isset($data['can_process_rma'])) $can_process_rma = 1; else $can_process_rma = 0;
		if(isset($data['can_issue_store_credit'])) $can_issue_store_credit = 1; else $can_issue_store_credit = 0;
		
		
		$this->db->query("UPDATE `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "',view_returned_items='".$view_retuend_items."',can_edit_order='".$can_edit_order."',update_b_order='".$update_b_order."',pos_user='".$pos_user."', user_group_id = '" . (int)$data['user_group_id'] . "', status = '" . (int)$data['status'] . "',can_create_order='".(int)$can_create_order."',can_process_rma='".(int)$can_process_rma."',can_issue_store_credit='".(int)$can_issue_store_credit."' WHERE user_id = '" . (int)$user_id . "'");
		
		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "user` SET password = '" . $this->db->escape(md5($data['password'])) . "' WHERE user_id = '" . (int)$user_id . "'");
		}
	}

	public function editPassword($user_id, $password) {
		$this->db->query("UPDATE `" . DB_PREFIX . "user` SET password = '" . $this->db->escape(md5($password)) . "' WHERE user_id = '" . (int)$user_id . "'");
	}

	public function editCode($email, $code) {
		$this->db->query("UPDATE `" . DB_PREFIX . "user` SET code = '" . $this->db->escape($code) . "' WHERE email = '" . $this->db->escape($email) . "'");
	}
			
	public function deleteUser($user_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
	}
	
	public function getUser($user_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
	
		return $query->row;
	}
	
	public function getUserByUsername($username) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . $this->db->escape($username) . "'");
	
		return $query->row;
	}
		
	public function getUserByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");
	
		return $query->row;
	}
		
	public function getUsers($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "user`";
			
		$sort_data = array(
			'username',
			'status',
			'date_added'
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY username";	
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

	public function getTotalUsers() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user`");
		
		return $query->row['total'];
	}

	public function getTotalUsersByGroupId($user_group_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . (int)$user_group_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalUsersByEmail($email) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE email = '" . $this->db->escape($email) . "'");
		
		return $query->row['total'];
	}	
	 public function getPOSUsers() {
		$query = $this->db->query("SELECT user_id, username FROM `" . DB_PREFIX . "user` WHERE pos_user = '1'");
        	return $query->rows;
        }
}
?>