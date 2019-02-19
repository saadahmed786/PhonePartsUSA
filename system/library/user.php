<?php
class User {
	private $user_id;
	private $username;
	private $user;
	private $permission = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		
		if (isset($this->session->data['user_id'])) {
			$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "' AND status = '1'");
			
			if ($user_query->num_rows) {
				$this->user_id = $user_query->row['user_id'];
				$this->username = $user_query->row['username'];
				$this->user = $user_query->row;
				$this->user_group_id = $user_query->row['user_group_id'];
				
				$this->db->query("UPDATE " . DB_PREFIX . "user SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");

				$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");
				
				$permissions = unserialize($user_group_query->row['permission']);

				if (is_array($permissions)) {
					foreach ($permissions as $key => $value) {
						$this->permission[$key] = $value;
					}
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($username, $password) {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->db->escape($username) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1'");

		if ($user_query->num_rows) {
			$this->session->data['user_id'] = $user_query->row['user_id'];
			
			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];			

			$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

			$permissions = unserialize($user_group_query->row['permission']);

			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['user_id']);

		$this->user_id = '';
		$this->username = '';
		
		// session_destroy();
	}

	public function hasPermission($key, $value) {
		if (isset($this->permission[$key])) {
			return in_array($value, $this->permission[$key]);
		} else {
			return false;
		}
	}

	public function isLogged() {
		return $this->user_id;
	}
	public function getUserGroupId(){
		return $this->user_group_id;
	}

	public function getId() {
		return $this->user_id;
	}
	
	public function getReturnedPermission() {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->user_id . "'");
		
		$result = $user_query->row;
		
		return $result['view_returned_items'];
		
		
		
	}
	
	public function canEditOrder() {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->user_id . "'");
		
		$result = $user_query->row;
		
		return $result['can_edit_order'];
		
		
		
	}

	public function canCreateOrder() {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->user_id . "'");
		
		$result = $user_query->row;
		
		return $result['can_create_order'];
		
		
		
	}
	public function canProcessRMA() {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->user_id . "'");
		
		$result = $user_query->row;
		
		return $result['can_process_rma'];
		
		
		
	}
	public function canIssueStoreCredit() {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->user_id . "'");
		
		$result = $user_query->row;
		
		return $result['can_issue_store_credit'];
		
		
		
	}

	public function updateBOrder() {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->user_id . "'");
		
		$result = $user_query->row;
		
		return $result['update_b_order'];
		
	}

	public function userHavePermission ($key) {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->user_id . "'");
		
		$result = $user_query->row;
		
		return $result[$key];
	}

	
	
	public function getUserName() {
		return $this->username;
	}

	public function getUserInfo($key) {
		if (isset($this->user[$key])) {
			return $this->user[$key];
		}
		return false;
	}	
}
?>