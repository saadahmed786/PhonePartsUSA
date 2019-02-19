<?php
class Customer {
	private $customer_id;
	private $firstname;
	private $lastname;
	private $email;
	private $telephone;
	private $fax;
	private $newsletter;
	private $customer_group_id;
	private $address_id;
	private $is_po;
	private $source;
	
	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['customer_id'])) { 
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");
			$address_query = $this->db->query("select company from ".DB_PREFIX."address WHERE customer_id='".(int)$this->session->data['customer_id']."' limit 1");
			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['customer_id'];
				// $this->business_name = $customer_query->row['business_name'];
				$this->business_name = $address_query->row['business_name'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				$this->email = $customer_query->row['email'];
				$this->telephone = $customer_query->row['telephone'];
				$this->fax = $customer_query->row['fax'];
				$this->newsletter = $customer_query->row['newsletter'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->address_id = $customer_query->row['address_id'];
				$this->is_po = $customer_query->row['is_termed'];
				$this->source = $customer_query->row['source'];

				$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");
				
				if (!$query->num_rows) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$this->session->data['customer_id'] . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password, $override = false) {
		if ($override) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer where LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND status = '1'");
		} else {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1' AND approved = '1'");
			$this->session->data['warehouse'] = false;
			// If Customer is a PO Customer than go to Address Password for Warehouse.
			if (!$customer_query->num_rows) {
				// Get Email Data to confrim PO client
				$customer = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND status = '1' AND approved = '1'");

				if ($customer->row['is_termed']) {
					// Matching password with Address
					$address = $this->db->query('SELECT * FROM '. DB_PREFIX .'address WHERE customer_id = "'. $customer->row['customer_id'] .'" AND password = "'. $this->db->escape(md5($password)) .'"');
					// If Address Match than set the login with Defautl Address
					if ($address) {

						$customer_query = $customer;
						$customer_query->row['address_id'] == $address->row['address_id'];
						$this->session->data['warehouse'] = true;
					} else {
						$this->session->data['warehouse'] = false;
					}

				}

			}

		}
		
		if ($customer_query->num_rows) {
			$this->session->data['customer_id'] = $customer_query->row['customer_id'];	


				$cartCount = 0;
				foreach ($this->session->data['cart'] as $key => $quantity) {
					$cartCount++;
				}
				
			if ($cartCount == 0 && $customer_query->row['cart'] && is_string($customer_query->row['cart'])) {
				$cart = unserialize($customer_query->row['cart']);
				
				foreach ($cart as $key => $value) {
					if (!array_key_exists($key, $this->session->data['cart'])) {
						$this->session->data['cart'][$key] = $value;
					} else {
						$this->session->data['cart'][$key] += $value;
					}
				}			
			}

			if ($customer_query->row['wishlist'] && is_string($customer_query->row['wishlist'])) {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				$wishlist = unserialize($customer_query->row['wishlist']);

				foreach ($wishlist as $product_id) {
					if (!in_array($product_id, $this->session->data['wishlist'])) {
						$this->session->data['wishlist'][] = $product_id;
					}
				}			
			}

			$this->customer_id = $customer_query->row['customer_id'];
			$this->business_name = $customer_query->row['business_name'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->fax = $customer_query->row['fax'];
			$this->newsletter = $customer_query->row['newsletter'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->address_id = $customer_query->row['address_id'];
			$this->is_po = $customer_query->row['is_termed'];

			$this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
			
			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
		
		unset($this->session->data['customer_id'], $this->session->data['warehouse']);

		$this->customer_id = '';
		$this->business_name = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->telephone = '';
		$this->fax = '';
		$this->newsletter = '';
		$this->customer_group_id = '';
		$this->address_id = '';
		$this->is_po = 0;
	}

	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
	}

	public function getBusinessName() {
		return $this->business_name;
	}

	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function getFax() {
		return $this->fax;
	}
	
	public function getNewsletter() {
		return $this->newsletter;	
	}

	public function getCustomerGroupId() {
		return $this->customer_group_id;	
	}
	
	public function getAddressId() {
		return $this->address_id;	
	}
	
	public function getBalance() {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}	

	public function getRewardPoints() {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];	
	}

	public function getTotalOrders() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order WHERE LOWER(TRIM(email)) = lower(trim('" . $this->db->escape($this->email) . "')) AND order_status_id in (3,15)");

		return $query->row['total'];	
	}



	public function getPO() {
		return $this->is_po;
	}

	public function getSource() {
		$query = $this->db->query("SELECT source  FROM " . DB_PREFIX . "customer_source WHERE LOWER(TRIM(email)) = lower(trim('" . $this->db->escape($this->email) . "')) and `type`='hear'");

		return $query->row['source'];	
	}

	public function getBusinessType() {
		$query = $this->db->query("SELECT source  FROM " . DB_PREFIX . "customer_source WHERE LOWER(TRIM(email)) = lower(trim('" . $this->db->escape($this->email) . "')) and `type`='business_type'");

		return $query->row['source'];	
	}

}
?>