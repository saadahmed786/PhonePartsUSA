<?php
class ModelWgiOosNotify extends Model {
    public function CheckInstall() {
        $sql = "SELECT count(*) as count FROM `" . DB_PREFIX . "setting` WHERE `group` = 'notify_out_stock'"; 
        $query = $this->db->query($sql);
        if ($query->row['count'] > 0){
			return true;
		}else{
			return false;
		}
    }   
	
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "out_of_stock_notify` (
			  `oosn_id` int(11) NOT NULL AUTO_INCREMENT,
			  `product_id` int(11) NOT NULL,
			  `email` varchar(50) NOT NULL,
			  `language_code` VARCHAR(10) NOT NULL,
			  `enquiry_date` datetime NOT NULL,
			  `notified_date` datetime DEFAULT NULL,
			  PRIMARY KEY (`oosn_id`)
			)");
		
		 $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`setting_id`, `store_id`, `group`, `key`, `value`, `serialized`) VALUES (NULL, '0', 'notify_out_stock', 'oosn_store_mail_sub', 'Customer is looking for a product', '0')"); 
		 $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`setting_id`, `store_id`, `group`, `key`, `value`, `serialized`) VALUES (NULL, '0', 'notify_out_stock', 'oosn_store_mail_body', 'Customer is looking for a product id - {product_id}. Reply to {customer_email}', '0'); ");
		 $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`setting_id`, `store_id`, `group`, `key`, `value`, `serialized`) VALUES (NULL, '0', 'notify_out_stock', 'oosn_customer_mail_sub1', 'Your Requested Product Is Now Available', '0'); ");
		 $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`, `group`, `key`, `value`, `serialized`) VALUES
(0, 'notify_out_stock', 'oosn_customer_mail_body1', '&lt;table align=&quot;center&quot; bgcolor=&quot;#f9f9f9&quot; cellpadding=&quot;10px&quot; width=&quot;90%&quot;&gt;\r\n	&lt;tbody&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;\r\n			&lt;p&gt;Hi,&lt;/p&gt;\r\n\r\n			&lt;p&gt;The product {product_name} Model: {model}, you requested for is now available for ordering.&lt;/p&gt;\r\n\r\n			&lt;table cellpadding=&quot;10&quot;&gt;\r\n				&lt;tbody&gt;\r\n					&lt;tr&gt;\r\n						&lt;td&gt;&lt;img height=&quot;150px&quot; src=&quot;{image}&quot; width=&quot;100px&quot; /&gt;&lt;/td&gt;\r\n						&lt;td&gt;&lt;b&gt;{product_name}&lt;/b&gt;&lt;br /&gt;\r\n						Model: {model}&lt;br /&gt;\r\n						&lt;br /&gt;\r\n						&lt;a class=&quot;button&quot; href=&quot;{link}&quot;&gt;BUY NOW ! Limited Stock !&lt;/a&gt;&lt;/td&gt;\r\n					&lt;/tr&gt;\r\n				&lt;/tbody&gt;\r\n			&lt;/table&gt;\r\n\r\n			&lt;p&gt;Regards,&lt;/p&gt;\r\n\r\n			&lt;p&gt;Your Store Name&lt;/p&gt;\r\n			&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n	&lt;/tbody&gt;\r\n&lt;/table&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n', 0); ");
	}
	
	public function getReports($data = array()) {
		$sql = "SELECT a.product_id, b.name, a.email, a.language_code, a.enquiry_date, a.notified_date FROM `" . DB_PREFIX . "out_of_stock_notify` a, `" . DB_PREFIX . "product_description` b where a.product_id = b.product_id and b.language_id = (SELECT language_id FROM `" . DB_PREFIX . "language` WHERE code = (SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_admin_language')) ORDER BY a.enquiry_date DESC";

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
	
	public function getTotalReports() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "out_of_stock_notify");

		return $query->row['total'];
	}
	
	public function updateSettings($key,$value){
		$query = $this->db->query("SELECT count(*) as count FROM " . DB_PREFIX . "setting WHERE `key` = '".$key."'");
		$count = $query->row['count'];
		
		if ($count <> 0){
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET value = '".$value."' WHERE `key` = '".$key."'");
		}else {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`setting_id`, `store_id`, `group`, `key`, `value`, `serialized`) VALUES (NULL, '0', 'notify_out_stock', '".$key."', '".$value."', '0'); ");
		}
	}
	
	public function countStoreUrl(){
		$query = $this->db->query("SELECT count(*) as count FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_url_oosn' and store_id = 0");
		return $query->row['count'];
		
	}
	
	public function getUniqueId() {
		$query = $this->db->query("SELECT distinct(product_id) AS pid FROM " . DB_PREFIX . "out_of_stock_notify");
		return $query->rows;
	}
	
	public function getStockStatus($product_id) {
		$query = $this->db->query("SELECT quantity, stock_status_id FROM " . DB_PREFIX . "product WHERE status = 1 and product_id = $product_id");
		return $query->row;
	}
	
	public function getProductStore($product_id) {
		$query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "product_to_store WHERE product_id = '".$product_id."'");
		return $query->row['store_id'];
	}
	
	public function getStoreUrl($store_id) {
		$value = '';
		$query = $this->db->query("SELECT `value` as url FROM " . DB_PREFIX . "setting WHERE store_id = '".$store_id."' and `key`='config_url'");
		$value = $query->row['url'];
		if (!isset($value)){
			$value = '';
		}
		return $value;
	}
	
	public function getProductDetails($product_id,$language_id) {
		$query = $this->db->query("SELECT b.name, a.model, a.image FROM `".DB_PREFIX."product` a, `" . DB_PREFIX . "product_description` b WHERE a.product_id = b.product_id and a.status = 1 and b.product_id = '".$product_id."' and b.language_id = '".$language_id."'");
		return $query->row;
	}
	
	public function getemail($product_id) {
		$query = $this->db->query("SELECT a.oosn_id, a.email, (SELECT language_id FROM `" . DB_PREFIX . "language` WHERE BINARY code = BINARY a.language_code) as language_id FROM " . DB_PREFIX . "out_of_stock_notify a WHERE a.notified_date IS NULL and a.product_id = $product_id");
		return $query->rows;
	}
	
	public function updatenotifieddate($oosn_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "out_of_stock_notify SET notified_date = now() WHERE oosn_id = $oosn_id");
	}
	
	public function deleteRecords() {
		$this->db->query("DELETE FROM " . DB_PREFIX . "out_of_stock_notify");
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "out_of_stock_notify`");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `group` = 'notify_out_stock'");
	}
	
	public function getLanguages() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language`");
		return $query->rows;
	}
}
?>