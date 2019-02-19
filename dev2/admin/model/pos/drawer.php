<?php

class ModelPosDrawer extends Model {

	public function getDrawers() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "drawer");
		return $query->rows;
	}

	public function getDrawersUsers() {
		$query = $this->db->query("SELECT GROUP_CONCAT(user_id) as user_ids FROM " . DB_PREFIX . "drawer WHERE status = 'Open'");
		return $query->row['user_ids'];
	}

	public function getAvailableUsers() {
		$users = $this->getDrawersUsers();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user where user_id not in (". (($users)? $users: 0) .") AND pos_user = 1");
		return $query->rows;
	}

	public function drawerAssignUser($drawer_id, $user_id, $starting_cash) {
		$sql = "INSERT INTO oc_close_drawer (`user_id`, `drawer_id`, starting_cash, date_open, paypal_total, credit_card_total, cash_total) VALUES ($user_id, $drawer_id, $starting_cash, '". date('Y-m-d H:i:s') ."', 0.0000, 0.0000, 0.0000)";
		$this->db->query($sql);
		$drawerID = $this->db->getLastId();
		if ($drawerID) {
			$sql = "UPDATE " . DB_PREFIX . "drawer set `user_id` = " . (int) $user_id . ", close_drawer_id = ". $drawerID .", `status` = 'Open' where `drawer_id` = '". (int) $drawer_id ."'";
			$this->db->query($sql);
		}
	}

	public function getUserDrawer($user_id)	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "drawer WHERE user_id='". (int) $user_id ."' AND status = 'Open'");
		return $query->row;
	}

	public function getAssignDrawer($close_drawer_id)	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "close_drawer WHERE id='". (int) $close_drawer_id ."'");
		return $query->row;
	}

	public function updateCloseDrawerCash ($cash, $card, $paypal, $close_drawer_id) {
		$sql = "UPDATE " . DB_PREFIX . "close_drawer set `paypal_total` = paypal_total+$paypal, `credit_card_total` = credit_card_total+$card, `expected` = expected+$cash where id='". (int) $close_drawer_id ."'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function getCloseDrawerValue ($field, $close_drawer_id) {
		$query = $this->db->query("SELECT `". $field ."` FROM " . DB_PREFIX . "close_drawer WHERE id='". (int) $close_drawer_id ."'");
		return $query->row[$field];
	}
}