<?php
class ModelFeedUKSBGoogleMerchant extends Model {
	public function getTotalProductsByStore($store_id) {
		$customer_group_id = $this->config->get('config_customer_group_id');

		$sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "uksb_google_merchant_products uksbp ON (p.product_id = uksbp.product_id) WHERE p.price > 0 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pd.description != '' AND p.status = '1' AND p.date_available <= NOW() AND uksbp.g_on_google = 1 AND (uksbp.g_expiry_date > NOW() OR uksbp.g_expiry_date = '') AND p2s.store_id = '" . (int)$store_id . "' GROUP BY p.product_id";

		$query = $this->db->query($sql);

		return $query->num_rows;
	}

	public function checkInstallState() {
		$state = '';
		$query1 = $this->db->query("DESC `" . DB_PREFIX . "category` `google_category_gb`");
		$state = (!$query1->num_rows?'complete':'incomplete');
		
		$query2 = $this->db->query("DESC `" . DB_PREFIX . "product` `google_category_gb`");
		$state = (!$query2->num_rows?'complete':'incomplete');
		
		$state = ($this->db->query("SELECT count(*) FROM `" . DB_PREFIX . "uksb_google_merchant_categories`") == $this->db->query("SELECT count(*) FROM `" . DB_PREFIX . "category`")?'complete':'incomplete');
		
		$state = ($this->db->query("SELECT count(*) FROM `" . DB_PREFIX . "uksb_google_merchant_products`") == $this->db->query("SELECT count(*) FROM `" . DB_PREFIX . "product`")?'complete':'incomplete');
		return $state;
	}

	public function install() {
		$query = $this->db->query("DESC `" . DB_PREFIX . "product` `upc`");
		if ($query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` CHANGE `upc` `upc` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin");
		}

		$query = $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "uksb_google_merchant_categories` (
			`category_id` int(11) unsigned NOT NULL,
			`google_category_gb` varchar(255) NOT NULL,
			`google_category_us` varchar(255) NOT NULL,
			`google_category_au` varchar(255) NOT NULL,
			`google_category_fr` varchar(255) NOT NULL,
			`google_category_de` varchar(255) NOT NULL,
			`google_category_it` varchar(255) NOT NULL,
			`google_category_nl` varchar(255) NOT NULL,
			`google_category_es` varchar(255) NOT NULL,
			`google_category_pt` varchar(255) NOT NULL,
			`google_category_cz` varchar(255) NOT NULL,
			`google_category_jp` varchar(255) NOT NULL,
			`google_category_dk` varchar(255) NOT NULL,
			`google_category_no` varchar(255) NOT NULL,
			`google_category_pl` varchar(255) NOT NULL,
			`google_category_ru` varchar(255) NOT NULL,
			`google_category_sv` varchar(255) NOT NULL,
			`google_category_tr` varchar(255) NOT NULL,
			UNIQUE KEY `category_id` (`category_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$query = $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "uksb_google_merchant_products` (
			`product_id` int(11) unsigned NOT NULL,
			`g_on_google` tinyint(1) unsigned DEFAULT '1' NOT NULL,
			`google_category_gb` varchar(255) NOT NULL,
			`google_category_us` varchar(255) NOT NULL,
			`google_category_au` varchar(255) NOT NULL,
			`google_category_fr` varchar(255) NOT NULL,
			`google_category_de` varchar(255) NOT NULL,
			`google_category_it` varchar(255) NOT NULL,
			`google_category_nl` varchar(255) NOT NULL,
			`google_category_es` varchar(255) NOT NULL,
			`google_category_pt` varchar(255) NOT NULL,
			`google_category_cz` varchar(255) NOT NULL,
			`google_category_jp` varchar(255) NOT NULL,
			`google_category_dk` varchar(255) NOT NULL,
			`google_category_no` varchar(255) NOT NULL,
			`google_category_pl` varchar(255) NOT NULL,
			`google_category_ru` varchar(255) NOT NULL,
			`google_category_sv` varchar(255) NOT NULL,
			`google_category_tr` varchar(255) NOT NULL,
			`g_condition` varchar(11) DEFAULT 'new' NOT NULL,
			`g_brand` varchar(70) NOT NULL,
			`g_gtin` varchar(20) NOT NULL,
			`g_identifier_exists` tinyint(1) unsigned DEFAULT '1' NOT NULL,
			`g_gender` varchar(6) NOT NULL,
			`g_age_group` varchar(7) NOT NULL,
			`g_colour` text NOT NULL,
			`g_size` text NOT NULL,
			`g_size_type` varchar(12) NOT NULL,
			`g_size_system` varchar(10) NOT NULL,
			`g_material` text NOT NULL,
			`g_pattern` text NOT NULL,
			`g_mpn` varchar(255) NOT NULL,
			`v_mpn` text NOT NULL,
			`v_gtin` text NOT NULL,
			`v_prices` text NOT NULL,
			`v_images` text NOT NULL,
			`g_multipack` int(11) unsigned NOT NULL,
			`g_is_bundle` tinyint(1) unsigned DEFAULT '0' NOT NULL,
			`g_adult` tinyint(1) unsigned DEFAULT '0' NOT NULL,
			`g_adwords_redirect` varchar(255) NOT NULL,
			`g_custom_label_0` varchar(100) NOT NULL,
			`g_custom_label_1` varchar(100) NOT NULL,
			`g_custom_label_2` varchar(100) NOT NULL,
			`g_custom_label_3` varchar(100) NOT NULL,
			`g_custom_label_4` varchar(100) NOT NULL,
			`g_expiry_date` char(10) NOT NULL,
			`g_unit_pricing_measure` varchar(255) NOT NULL,
			`g_unit_pricing_base_measure` varchar(255) NOT NULL,
			`g_energy_efficiency_class` varchar(4) NOT NULL,
			UNIQUE KEY `product_id` (`product_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	}
	
	public function uksbInstall() {
		$query = $this->db->query("DESC `" . DB_PREFIX . "category` `google_category_gb`");
		if ($query->num_rows) {
			$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "uksb_google_merchant_categories` (
			`category_id`,
			`google_category_gb`,
			`google_category_us`,
			`google_category_au`,
			`google_category_fr`,
			`google_category_de`,
			`google_category_it`,
			`google_category_nl`,
			`google_category_es`
			) SELECT
			`category_id`,
			`google_category_gb`,
			`google_category_us`,
			`google_category_au`,
			`google_category_fr`,
			`google_category_de`,
			`google_category_it`,
			`google_category_nl`,
			`google_category_es`
			FROM `" . DB_PREFIX . "category` Order By `category_id` ASC");
			
			if($this->db->query("SELECT count(*) FROM `" . DB_PREFIX . "uksb_google_merchant_categories`") == $this->db->query("SELECT count(*) FROM `" . DB_PREFIX . "category`")){
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "category`
				DROP `google_category_gb`,
				DROP `google_category_us`,
				DROP `google_category_au`,
				DROP `google_category_fr`,
				DROP `google_category_de`,
				DROP `google_category_it`,
				DROP `google_category_nl`,
				DROP `google_category_es`");
			}
			
		} else {
			$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "uksb_google_merchant_categories` (
			`category_id`
			) SELECT
			`category_id`
			FROM `" . DB_PREFIX . "category` Order By `category_id` ASC");
		}
		

		$query = $this->db->query("DESC `" . DB_PREFIX . "product` `ongoogle`");
		if ($query->num_rows) {

			$query = $this->db->query("DESC `" . DB_PREFIX . "product` `identifier_exists`");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `identifier_exists` TINYINT ( 1 ) DEFAULT '1' NOT NULL AFTER `gtin`");
			}

			$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "uksb_google_merchant_products` (
			`product_id`,
			`g_on_google`,
			`google_category_gb`,
			`google_category_us`,
			`google_category_au`,
			`google_category_fr`,
			`google_category_de`,
			`google_category_it`,
			`google_category_nl`,
			`google_category_es`,
			`g_condition`,
			`g_brand`,
			`g_gtin`,
			`g_identifier_exists`,
			`g_gender`,
			`g_age_group`,
			`g_colour`,
			`g_size`,
			`g_material`,
			`g_pattern`,
			`g_mpn`,
			`v_mpn`,
			`v_gtin`,
			`v_prices`,
			`g_adwords_redirect`,
			`g_custom_label_0`,
			`g_custom_label_1`
			) SELECT
			`product_id`,
			`ongoogle`,
			`google_category_gb`,
			`google_category_us`,
			`google_category_au`,
			`google_category_fr`,
			`google_category_de`,
			`google_category_it`,
			`google_category_nl`,
			`google_category_es`,
			`gcondition`,
			`brand`,
			`gtin`,
			`identifier_exists`,
			`gender`,
			`age_group`,
			`colour`,
			`size`,
			`material`,
			`pattern`,
			`mpn`,
			`vmpn`,
			`vgtin`,
			`vprices`,
			`google_adwords_redirect`,
			`google_adwords_grouping`,
			`google_adwords_labels`
			FROM `" . DB_PREFIX . "product` Order By `product_id` ASC");
			
			if($this->db->query("SELECT count(*) FROM `" . DB_PREFIX . "uksb_google_merchant_products`") == $this->db->query("SELECT count(*) FROM `" . DB_PREFIX . "product`")){
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product`
				DROP `gtin`,
				DROP `mpn`,
				DROP `google_category_gb`,
				DROP `google_category_us`,
				DROP `google_category_au`,
				DROP `google_category_fr`,
				DROP `google_category_de`,
				DROP `google_category_it`,
				DROP `google_category_nl`,
				DROP `google_category_es`,
				DROP `gender`,
				DROP `age_group`,
				DROP `colour`,
				DROP `size`,
				DROP `material`,
				DROP `pattern`,
				DROP `gcondition`,
				DROP `brand`,
				DROP `vmpn`,
				DROP `vgtin`,
				DROP `google_adwords_grouping`,
				DROP `google_adwords_labels`,
				DROP `google_adwords_redirect`,
				DROP `google_adwords_queryparam`,
				DROP `identifier_exists`,
				DROP `vprices`,
				DROP `ongoogle`");
				
				$queryx = $this->db->query("DESC `" . DB_PREFIX . "product` `google_adwords_publish`");
				if ($queryx->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP `google_adwords_publish`");
				}
				
			}
		} else {
			$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "uksb_google_merchant_products` (
			`product_id`
			) SELECT
			`product_id`
			FROM `" . DB_PREFIX . "product` Order By `product_id` ASC");
		}
	}
}
?>