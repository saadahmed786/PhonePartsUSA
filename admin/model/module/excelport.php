<?php 
class ModelModuleExcelport extends Model {
	public $now;
	public $productSize = 26;
	public $productAttributeSeparator = '  >  ';
	public $productFilterSeparator = '  >  ';
	public $categoryFilterSeparator = '  >  ';
	public $dataArray = array(
		'Products' => array(),
		'Categories' => array(),
		'Options' => array(),
		'Attributes' => array(),
		'AttributeGroups' => array(),
		'Customers' => array(),
		'CustomerGroups' => array()
	);
	public $exportData = array(
		'Products' => array(
			'tag' => "SELECT pt.product_id, GROUP_CONCAT(DISTINCT pt.tag ORDER BY pt.tag ASC SEPARATOR ',') as value FROM {DB_PREFIX}product_tag pt WHERE pt.language_id = '{LANGUAGE_ID}' GROUP BY pt.product_id",
			'keyword' => "SELECT SUBSTRING(query, 12) as product_id, keyword as value FROM {DB_PREFIX}url_alias ua WHERE ua.query LIKE 'product_id=%'",
			'categories' => "SELECT p2c.product_id, GROUP_CONCAT(DISTINCT p2c.category_id ORDER BY p2c.category_id ASC SEPARATOR ',') as value FROM {DB_PREFIX}product_to_category p2c GROUP BY p2c.product_id",
			'stores' => "SELECT p2s.product_id, GROUP_CONCAT(DISTINCT p2s.store_id ORDER BY p2s.store_id ASC SEPARATOR ',') as value FROM {DB_PREFIX}product_to_store p2s GROUP BY p2s.product_id",
			'filters' => "SELECT pf.product_id, GROUP_CONCAT(DISTINCT pf.filter_id ORDER BY pf.filter_id ASC SEPARATOR ',') as value FROM {DB_PREFIX}product_filter pf GROUP BY pf.product_id",
			'downloads' => "SELECT p2d.product_id, GROUP_CONCAT(DISTINCT p2d.download_id ORDER BY p2d.download_id ASC SEPARATOR ',') as value FROM {DB_PREFIX}product_to_download p2d GROUP BY p2d.product_id",
			'related' => "SELECT pr.product_id, GROUP_CONCAT(DISTINCT pr.related_id ORDER BY pr.related_id ASC SEPARATOR ',') as value FROM {DB_PREFIX}product_related pr GROUP BY pr.product_id",
			'profiles' => "SELECT pp.product_id, GROUP_CONCAT(DISTINCT CONCAT_WS(':', pp.profile_id, pp.customer_group_id) ORDER BY pp.profile_id ASC SEPARATOR ',') as value  FROM {DB_PREFIX}product_profile pp GROUP BY pp.product_id"
		),
		'Categories' => array(
			'keyword' => "SELECT SUBSTRING(query, 13) as category_id, keyword as value FROM {DB_PREFIX}url_alias ua WHERE ua.query LIKE 'category_id=%'",
			'stores' => "SELECT c2s.category_id, GROUP_CONCAT(DISTINCT c2s.store_id ORDER BY c2s.store_id ASC SEPARATOR ',') as value FROM {DB_PREFIX}category_to_store c2s GROUP BY c2s.category_id",
			'category_layout' => "SELECT c2l.category_id, GROUP_CONCAT(DISTINCT (SELECT CONCAT_WS(':', c2l.store_id, c2l.layout_id)) SEPARATOR ',') as value FROM {DB_PREFIX}category_to_layout c2l GROUP BY c2l.category_id",
			'filters' => "SELECT cf.category_id, GROUP_CONCAT(DISTINCT cf.filter_id ORDER BY cf.filter_id ASC SEPARATOR ',') as value FROM {DB_PREFIX}category_filter cf GROUP BY cf.category_id"
		),
		'Options' => array(
		
		),
		'Attributes' => array(
		
		),
		'AttributeGroups' => array(
		
		),
		'Customers' => array(
			
		),
		'CustomerGroups' => array(
			
		)
	);
	public $extraGeneralFields = array(
		'Products' => array(
			/* Example: ////////////////////////////////////////////
			array(
				'title' => 'Custom Category',
				'column_full' => 'R',
				'column_light' => 'AN',
				'name' => 'custom_category',
				'select_sql' => "SELECT p2c.product_id, GROUP_CONCAT(DISTINCT p2c.category_id ORDER BY p2c.category_id ASC SEPARATOR ',') as value FROM {DB_PREFIX}product_to_category p2c GROUP BY p2c.product_id",
				'select_eval' => NULL,
				'eval_add' => 'print_r($data["custom_category"]); exit;',
				'eval_edit' => 'print_r($data["custom_category"]); exit;'
			) 
			//////////////////////////////////////////////////////*/
			
			/* {EXTRA_PRODUCT_FIELDS} */ // Important hook! Do not delete.
		),
		'Categories' => array(
			/* {EXTRA_CATEGORY_FIELDS} */ // Important hook! Do not delete.
		),
		'Options' => array(
			/* {EXTRA_OPTION_FIELDS} */ // Important hook! Do not delete.
		),
		'Attributes' => array(
			/* {EXTRA_ATTRIBUTE_FIELDS} */ // Important hook! Do not delete.
		),
		'AttributeGroups' => array(
			/* {EXTRA_ATTRIBUTE_GROUP_FIELDS} */ // Important hook! Do not delete.
		),
		'Customers' => array(
			/* {EXTRA_CUSTOMER_FIELDS} */ // Important hook! Do not delete.
		),
		'CustomerGroups' => array(
			/* {EXTRA_CUSTOMER_GROUP_FIELDS} */ // Important hook! Do not delete.
		)
	);
	
	public $conditions = array(
		'Products' => array(
			'general_product_name' => array(
				'label' => 'Product Name',
				'join_table' => 'product_description',
				'field_name' => 'pd.name',
				'type' => 'text'
			),
			'general_meta_tag_description' => array(
				'label' => 'Meta Tag Description',
				'join_table' => 'product_description',
				'field_name' => 'pd.meta_description',
				'type' => 'text'
			),
			'general_meta_tag_keywords' => array(
				'label' => 'Meta Tag Keywords',
				'join_table' => 'product_description',
				'field_name' => 'pd.meta_keyword',
				'type' => 'text'
			),
			'general_description' => array(
				'label' => 'Description',
				'join_table' => 'product_description',
				'field_name' => 'pd.description',
				'type' => 'text'
			),
			'general_product_tags' => array(),
			'data_model' => array(
				'label' => 'Model',
				'join_table' => NULL,
				'field_name' => 'p.model',
				'type' => 'text'
			),
			'data_sku' => array(
				'label' => 'SKU',
				'join_table' => NULL,
				'field_name' => 'p.sku',
				'type' => 'text'
			),
			'data_upc' => array(
				'label' => 'UPC',
				'join_table' => NULL,
				'field_name' => 'p.upc',
				'type' => 'text'
			),
			'data_ean' => array(
				'label' => 'EAN',
				'join_table' => NULL,
				'field_name' => 'p.ean',
				'type' => 'text'
			),
			'data_jan' => array(
				'label' => 'JAN',
				'join_table' => NULL,
				'field_name' => 'p.jan',
				'type' => 'text'
			),
			'data_isbn' => array(
				'label' => 'ISBN',
				'join_table' => NULL,
				'field_name' => 'p.isbn',
				'type' => 'text'
			),
			'data_mpn' => array(
				'label' => 'MPN',
				'join_table' => NULL,
				'field_name' => 'p.mpn',
				'type' => 'text'
			),
			'data_location' => array(
				'label' => 'Location',
				'join_table' => NULL,
				'field_name' => 'p.location',
				'type' => 'text'
			),
			'data_price' => array(
				'label' => 'Price',
				'join_table' => NULL,
				'field_name' => 'p.price',
				'type' => 'number'
			),
			'data_location' => array(
				'label' => 'Location',
				'join_table' => NULL,
				'field_name' => 'p.location',
				'type' => 'text'
			),
			'data_quantity' => array(
				'label' => 'Quantity',
				'join_table' => NULL,
				'field_name' => 'p.quantity',
				'type' => 'number'
			),
			'data_out_of_stock_status' => array(
				'label' => 'Out Of Stock Status',
				'join_table' => 'stock_status',
				'field_name' => 'ss.name',
				'type' => 'text'
			),
			'data_seo_keyword' => array(
				'label' => 'SEO Keyword',
				'join_table' => 'url_alias',
				'field_name' => 'ua.keyword',
				'type' => 'text'
			),
			'data_image' => array(
				'label' => 'Image',
				'join_table' => NULL,
				'field_name' => 'p.image',
				'type' => 'text'
			),
			'data_date_available' => array(
				'label' => 'Date Available',
				'join_table' => NULL,
				'field_name' => 'p.date_available',
				'type' => 'date'
			),
			'data_length' => array(
				'label' => 'Length',
				'join_table' => NULL,
				'field_name' => 'p.length',
				'type' => 'number'
			),
			'data_width' => array(
				'label' => 'Width',
				'join_table' => NULL,
				'field_name' => 'p.width',
				'type' => 'number'
			),
			'data_height' => array(
				'label' => 'Height',
				'join_table' => NULL,
				'field_name' => 'p.height',
				'type' => 'number'
			),
			'data_weight' => array(
				'label' => 'Weight',
				'join_table' => NULL,
				'field_name' => 'p.weight',
				'type' => 'number'
			),
			'data_status' => array(
				'label' => 'Status',
				'join_table' => NULL,
				'field_name' => 'p.status',
				'type' => 'number'
			),
			'links_manufacturer' => array(
				'label' => 'Manufacturer',
				'join_table' => 'manufacturer',
				'field_name' => 'm.name',
				'type' => 'text'
			),
			'links_categories' => array(
				'label' => 'Category',
				'join_table' => 'category',
				'field_name' => 'cd.name',
				'type' => 'text'
			),
			'links_filters' => array(
				'label' => 'Filter',
				'join_table' => 'filter',
				'field_name' => 'fd.name',
				'type' => 'text'
			),
			'links_downloads' => array(
				'label' => 'Download',
				'join_table' => 'download',
				'field_name' => 'dd.name',
				'type' => 'text'
			),
			'links_related' => array(
				'label' => 'Related Product',
				'join_table' => 'product_related',
				'field_name' => 'prd.name',
				'type' => 'text'
			),
			'attribute_name' => array(
				'label' => 'Attribute Name',
				'join_table' => 'product_attribute',
				'field_name' => 'ad.name',
				'type' => 'text'
			),
			'attribute_value' => array(
				'label' => 'Attribute Value',
				'join_table' => 'product_attribute',
				'field_name' => 'pa.text',
				'type' => 'text'
			),
			'option_name' => array(
				'label' => 'Option Name',
				'join_table' => 'product_option',
				'field_name' => 'od.name',
				'type' => 'text'
			),
			'option_value' => array(
				'label' => 'Option Value',
				'join_table' => 'product_option',
				'field_name' => array('po.option_value', 'ovd.name'),
				'type' => 'text'
			),
			'discount_price' => array(
				'label' => 'Discount Price',
				'join_table' => 'product_discount',
				'field_name' => 'pdis.price',
				'type' => 'number'
			),
			'discount_date_start' => array(
				'label' => 'Discount Date Start',
				'join_table' => 'product_discount',
				'field_name' => 'pdis.date_start',
				'type' => 'date'
			),
			'discount_date_end' => array(
				'label' => 'Discount Date End',
				'join_table' => 'product_discount',
				'field_name' => 'pdis.date_end',
				'type' => 'date'
			),
			'special_price' => array(
				'label' => 'Special Price',
				'join_table' => 'product_special',
				'field_name' => 'pspe.price',
				'type' => 'number'
			),
			'special_date_start' => array(
				'label' => 'Special Date Start',
				'join_table' => 'product_special',
				'field_name' => 'pspe.date_start',
				'type' => 'date'
			),
			'special_date_end' => array(
				'label' => 'Special Date End',
				'join_table' => 'product_special',
				'field_name' => 'pspe.date_end',
				'type' => 'date'
			),
			'image_image' => array(
				'label' => 'Additional Image',
				'join_table' => 'product_image',
				'field_name' => 'pi.image',
				'type' => 'text'
			),
			'reward_points' => array(
				'label' => 'Reward Points',
				'join_table' => 'product_reward',
				'field_name' => array('p.points', 'pr.points'),
				'type' => 'number'
			),
			'design' => array(
				'label' => 'Design',
				'join_table' => 'product_to_layout',
				'field_name' => 'l.name',
				'type' => 'text'
			)
			/* {EXTRA_PRODUCT_CONDITIONS} */ // Important hook! Do not delete.
		),
		'Categories' => array(
			'general_category_name' => array(
				'label' => 'Category Name',
				'join_table' => 'category_description',
				'field_name' => 'cd.name',
				'type' => 'text'
			),
			'general_meta_tag_description' => array(
				'label' => 'Meta Tag Description',
				'join_table' => 'category_description',
				'field_name' => 'cd.meta_description',
				'type' => 'text'
			),
			'general_meta_tag_keywords' => array(
				'label' => 'Meta Tag Keywords',
				'join_table' => 'category_description',
				'field_name' => 'cd.meta_keyword',
				'type' => 'text'
			),
			'general_description' => array(
				'label' => 'Description',
				'join_table' => 'category_description',
				'field_name' => 'cd.description',
				'type' => 'text'
			),
			'data_parent' => array(
				'label' => 'Parent',
				'join_table' => 'category_parent',
				'field_name' => 'cpard.name',
				'type' => 'text'
			),
			'data_filters' => array(
				'label' => 'Filter',
				'join_table' => 'filter',
				'field_name' => 'fd.name',
				'type' => 'text'
			),
			'data_seo_keyword' => array(
				'label' => 'SEO Keyword',
				'join_table' => 'url_alias',
				'field_name' => 'ua.keyword',
				'type' => 'text'
			),
			'data_image' => array(
				'label' => 'Image',
				'join_table' => NULL,
				'field_name' => 'c.image',
				'type' => 'text'
			),
			'data_top' => array(
				'label' => 'Top',
				'join_table' => NULL,
				'field_name' => 'c.top',
				'type' => 'number'
			),
			'data_columns' => array(
				'label' => 'Column',
				'join_table' => NULL,
				'field_name' => 'c.column',
				'type' => 'number'
			),
			'data_status' => array(
				'label' => 'Status',
				'join_table' => NULL,
				'field_name' => 'c.status',
				'type' => 'number'
			),
			'design' => array(
				'label' => 'Design',
				'join_table' => 'category_to_layout',
				'field_name' => 'l.name',
				'type' => 'text'
			)
			/* {EXTRA_CATEOGRY_CONDITIONS} */ // Important hook! Do not delete.
		),
		'Options' => array(
			'option_name' => array(
				'label' => 'Option Name',
				'join_table' => 'option_description',
				'field_name' => 'od.name',
				'type' => 'text'
			),
			'option_type' => array(
				'label' => 'Option Type',
				'join_table' => NULL,
				'field_name' => 'o.type',
				'type' => 'text'
			),
			'option_value_name' => array(
				'label' => 'Option Value Name',
				'join_table' => 'option_value',
				'field_name' => 'ovd.name',
				'type' => 'text'
			),
			'option_value_image' => array(
				'label' => 'Option Value Image',
				'join_table' => 'option_value',
				'field_name' => 'ov.image',
				'type' => 'text'
			)
			/* {EXTRA_OPTION_CONDITIONS} */ // Important hook! Do not delete.
		),
		'Attributes' => array( // Applies also to Attribute Groups
			'attribute_name' => array(
				'label' => 'Attribute Name',
				'join_table' => 'attribute_description',
				'field_name' => 'ad.name',
				'type' => 'text'
			),
			'attribute_group' => array(
				'label' => 'Attribute Group',
				'join_table' => 'attribute_group_description',
				'field_name' => 'agd.name',
				'type' => 'text'
			)
			/* {EXTRA_ATTRIBUTE_CONDITIONS} */ // Important hook! Do not delete.
		),
		'Customers' => array(
			'customer_first_name' => array(
				'label' => 'First Name',
				'join_table' => 'address',
				'field_name' => array('a.firstname', 'c.firstname'),
				'type' => 'text'
			),
			'customer_last_name' => array(
				'label' => 'Last Name',
				'join_table' => 'address',
				'field_name' => array('a.lastname', 'c.lastname'),
				'type' => 'text'
			),
			'customer_email' => array(
				'label' => 'E-mail',
				'join_table' => NULL,
				'field_name' => 'a.email',
				'type' => 'text'
			),
			'customer_telephone' => array(
				'label' => 'Telephone',
				'join_table' => NULL,
				'field_name' => 'a.telephone',
				'type' => 'text'
			),
			'customer_fax' => array(
				'label' => 'Fax',
				'join_table' => NULL,
				'field_name' => 'a.fax',
				'type' => 'text'
			)
			/* {EXTRA_CUSTOMER_CONDITIONS} */ // Important hook! Do not delete.
		),
		'CustomerGroups' => array(
			'customer_group_name' => array(
				'label' => 'Customer Group Name',
				'join_table' => 'customer_group_description',
				'field_name' => 'cgd.name',
				'type' => 'text'
			),
			'customer_group_description' => array(
				'label' => 'Description',
				'join_table' => 'customer_group_description',
				'field_name' => 'cgd.description',
				'type' => 'text'
			)
			/* {EXTRA_CUSTOMER_GROUP_CONDITIONS} */ // Important hook! Do not delete.
		)
	);
	
	public $operations = array(
		'text_contains' => array(
			'html' => 'contains',
			'operation' => "{FIELD_NAME} LIKE '%{WORD}%'"
		),
		'text_not_contain' => array(
			'html' => 'does not contain',
			'operation' => "{FIELD_NAME} NOT LIKE '%{WORD}%'"
		),
		'text_exactly' => array(
			'html' => 'is exactly',
			'operation' => "{FIELD_NAME} = '{WORD}'"
		),
		'number_ge' => array(
			'html' => '&ge;',
			'operation' => "{FIELD_NAME} >= '{WORD}'"
		),
		'number_le' => array(
			'html' => '&le;',
			'operation' => "{FIELD_NAME} <= '{WORD}'"
		),
		'number_gt' => array(
			'html' => '&gt;',
			'operation' => "{FIELD_NAME} > '{WORD}'"
		),
		'number_lt' => array(
			'html' => '&lt;',
			'operation' => "{FIELD_NAME} < '{WORD}'"
		),
		'number_eq' => array(
			'html' => '&equiv;',
			'operation' => "{FIELD_NAME} = '{WORD}'"
		),
		'number_dif' => array(
			'html' => '&ne;',
			'operation' => "{FIELD_NAME} != '{WORD}'"
		)
	);
	
	/* {EXTRA_FUNCTIONS} */ // Important hook! Do not delete.
	
	public function __construct($register) {
		if (!defined('IMODULE_ROOT')) define('IMODULE_ROOT', substr(DIR_APPLICATION, 0, strrpos(DIR_APPLICATION, '/', -2)) . '/');
		if (!defined('IMODULE_ADMIN_ROOT')) define('IMODULE_ADMIN_ROOT', DIR_APPLICATION);
		if (!defined('IMODULE_SERVER_NAME')) define('IMODULE_SERVER_NAME', substr((defined('HTTP_CATALOG') ? HTTP_CATALOG : HTTP_SERVER), 7, strlen((defined('HTTP_CATALOG') ? HTTP_CATALOG : HTTP_SERVER)) - 8));
		if (!defined('IMODULE_TEMP_FOLDER')) define('IMODULE_TEMP_FOLDER', 'temp_excelport');
		if (!defined('IMODULE_UPMOST_VERSION')) define('IMODULE_UPMOST_VERSION', '1.5.6.1');
		
		$this->now = time();
		
		$this->conditions['Products']['general_product_tags'] = array(
			'label' => 'Product Tags',
			'join_table' => version_compare(VERSION, '1.5.4', '<') ? 'product_tag' : 'product_description',
			'field_name' => version_compare(VERSION, '1.5.4', '<') ? 'pt.tag' : 'pd.tag',
			'type' => 'text'
		);
		
		$this->conditions['Customers']['customer_customer_group'] = array(
			'label' => 'Customer Group Name',
			'join_table' => version_compare(VERSION, '1.5.3', '<') ? 'customer_group' : 'customer_group_description',
			'field_name' => version_compare(VERSION, '1.5.3', '<') ? 'cg.name' : 'cgd.name',
			'type' => 'text'
		);
		
		$this->conditions['CustomerGroups']['customer_group_name'] = array(
			'label' => 'Customer Group Name',
			'join_table' => version_compare(VERSION, '1.5.3', '<') ? NULL : 'customer_group_description',
			'field_name' => version_compare(VERSION, '1.5.3', '<') ? 'cg.name' : 'cgd.name',
			'type' => 'text'
		);
		
		if (version_compare(VERSION, '1.5.3', '<')) {
			unset($this->conditions['CustomerGroups']['customer_group_description']);
		}
		
		if (version_compare(VERSION, '1.5.4', '<')) {
			unset($this->conditions['Products']['data_ean']);
			unset($this->conditions['Products']['data_jan']);
			unset($this->conditions['Products']['data_isbn']);
			unset($this->conditions['Products']['data_mpn']);
		}
		
		if (version_compare(VERSION, '1.5.5', '<')) {
			unset($this->conditions['Products']['links_filters']);
			unset($this->conditions['Categories']['data_filters']);
		}
		
		parent::__construct($register);
	}
	public function folderCheck($folder) {
		if (!is_dir($folder)) {
			if (!mkdir($folder, 0755)) throw new Exception('Error: Temp directory does not exist and cannot be created! Please check the root folder permissions.');	
		}
	}
	public function getSetting($group, $store_id = 0) {
		$data = array(); 
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
		
		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$data[$result['key']] = $result['value'];
			} else {
				$data[$result['key']] = unserialize($result['value']);
			}
		}

		return $data;
	}
	public function editSetting($group, $data, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");

		foreach ($data as $key => $value) {
			if (!is_array($value)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
			}
		}
	}
	public function deleteSetting($group, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
	}
	public function cleanTemp($tempDir = '../temp_excelport', $exclude = array()) {
		$files = scandir($tempDir);
		foreach ($files as $file) {
			if (!in_array($file, array_merge(array('.', '..', 'index.html'), $exclude))) {
				if (is_file($tempDir.'/'.$file)) unlink ($tempDir.'/'.$file);
				if (is_dir($tempDir.'/'.$file)) {
					$this->cleanTemp($tempDir.'/'.$file);	
					rmdir($tempDir.'/'.$file);
				}
			}
		}
	}
	public function clearInvalidEntries($folders) {
		$result = array();
		foreach	($folders as $folder) {
			if ($folder != '') {
				$result[] = trim($folder);	
			}
		}
		return $result;
	}
	protected function array_insert(&$array, $pos, $value) {
		$count = count($array);
		for ($i = $count; $i > $pos; $i--) {
			$array[$i] = $array[$i-1];	
		}
		$array[$i] = $value;
	}
	public function getStandardFile($file, $arrayName, $indexName) {
		$allowedExts = array("zip", "xlsx");
		$name = $file['name'][$arrayName][$indexName];
		$explode = explode(".", $name);
		$extension = end($explode);
		$result = false;
		if ($file['size'][$arrayName][$indexName] <= $this->returnMaxUploadSize() && in_array($extension, $allowedExts)) { //file limit = post_max_size - 512KB
			if ($file['error'][$arrayName][$indexName] > 0) throw new Exception("Upload Error Code: " . $file['error'][$arrayName][$indexName]);
			$dest = IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/' . $name;
			if (!move_uploaded_file($file['tmp_name'][$arrayName][$indexName], $dest)) throw new Exception($this->language->get('excelport_unable_upload'));
			else $result = $dest;
		} else throw new Exception($this->language->get('excelport_invalid_file'));
		
		return $dest;
	}
	public function returnMaxUploadSize($readable = false) {
		$upload = $this->return_bytes(ini_get('upload_max_filesize'));
		$post = $this->return_bytes(ini_get('post_max_size'));
		
		if ($upload >= $post) return $readable ? $this->sizeToString($post - 524288) : $post - 524288;
		else return $readable ? $this->sizeToString($upload) : $upload;
	}
	private function return_bytes($val) { //from http://php.net/manual/en/function.ini-get.php
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
	
		return $val;
	}
	private function sizeToString($size) {
		$count = 0;
		for ($i = $size; $i >= 1024; $i /= 1024) $count++;
		switch ($count) {
			case 0 : $suffix = ' B'; break;
			case 1 : $suffix = ' KB'; break;
			case 2 : $suffix = ' MB'; break;
			case 3 : $suffix = ' GB'; break;
			case ($count >= 4) : $suffix = ' TB'; break;
		}
		return round($i, 2).$suffix;
	}
	public function createDownload($file, $die = true) {
		$this->cleanTemp('../' . IMODULE_TEMP_FOLDER, array(basename($file)));
		
		if (stripos($this->request->server['REQUEST_URI'], 'com_mijoshop') !== FALSE) {
			header("Location:".dirname(HTTP_SERVER)."/components/com_mijoshop/opencart/" . IMODULE_TEMP_FOLDER . "/".basename($file));
		} else {
			header("Location:".dirname(HTTP_SERVER)."/" . IMODULE_TEMP_FOLDER . "/".basename($file));
		}
		
		exit;
	}
	public function getProgress($error = NULL) {
		$result = array(
			'error' => false,
			'message' => '',
			'percent' => 0,
			'done' => false,
			'current' => 0,
			'all' => 0,
			'finishedImport' => false,
			'importingFile' => ''
		);
		
		if (!empty($error)) {
			$result['error'] = true;
			$result['message'] = $error;
			$result['done'] = true;
			$result['percent'] = 0;
			$this->setProgress($result);
		} else {
			if (file_exists(IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/excelport_progress.txt')) {
				$data = file_get_contents(IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/excelport_progress.txt');
				$result = json_decode($data, true);
			} else {
				$result['populateAll'] = true;
				file_put_contents(IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/excelport_progress.txt', json_encode($result));
			}
		}
		clearstatcache();
		return $result;
	}
	public function setProgress($progress) {
		if ($progress['all'] !== -1) {
			$progress['percent'] = $progress['all'] != 0 ? ceil($progress['current']*100/$progress['all']) : 0;
		} else {
			$progress['percent'] = 100;	
		}
		file_put_contents(IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/excelport_progress.txt', json_encode($progress));
		clearstatcache();
	}
	public function deleteProgress() {
		if (file_exists(IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/excelport_progress.txt')) unlink(IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/excelport_progress.txt');
		clearstatcache();	
	}
	public function createZip($files = array(),$destination = '',$overwrite = false,$destinationFolder = '../temp_excelport/') {
		//FUNCTION FOUND FROM: http://davidwalsh.name/create-zip-php
		
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { return false; }
		
		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
			//make sure the file exists
				if(file_exists($destinationFolder.$file)) {
					$valid_files[] = $file;
				}
			}
		}
		
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($destinationFolder.$file,$file);
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			
			//close the zip -- done!
			$zip->close();
			//check to make sure the file exists
			if (file_exists($destination)) return $destination;
			else return false;
		}
		else return false;
	}
	public function prepareUploadedFile($file) {
		$this->language->load('module/excelport');
		if (!file_exists($file)) throw new Exception('excelport_invalid_import_file');
		
		$info = pathinfo($file);
		$ext = $info['extension'];
		
		switch ($ext) {
			case 'xlsx' : {
				return array($file);
			} break;
			case 'zip' : {
				return $this->unzip($file, IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/' . $info['filename'] . '/');
			} break;
		}
	}
	public function unzip($file, $decompressFolder) {
		$this->language->load('module/excelport');
		$zip = new ZipArchive();
		$success = array();
		if($zip->open($file, ZIPARCHIVE::CREATE) !== true) throw new Exception($this->language->get('excelport_unable_zip_file_open'));
		
		if (!file_exists($decompressFolder) || (file_exists($decompressFolder) && !is_dir($decompressFolder))) {
			if (!mkdir($decompressFolder, 0755)) throw new Exception($this->language->get('excelport_unable_create_unzip_folder'));
		}
		
		if (!$zip->extractTo($decompressFolder)) throw new Exception($this->language->get('excelport_unable_zip_file_extract'));
		
		
		//check the files
		$files = scandir($decompressFolder);
		
		foreach ($files as $tempFile) {
			if (in_array($tempFile, array('.', '..'))) continue;
			if (!file_exists($decompressFolder.$tempFile) || is_dir($decompressFolder.$tempFile)) continue;
			
			$tempInfo = pathinfo($tempFile);
			
			if ($tempInfo['extension'] == 'xlsx') $success[] = $decompressFolder . $tempFile;
		}
		return $success;	
	}
	public function setData($type, $destinationDirectory, $language_id) {
		foreach ($this->exportData[$type] as $name => $query) {
			$file = $destinationDirectory . '/' . $name . '.xlptemp';
			
			switch ($type) {
				case 'Products' : {
					if (version_compare(VERSION, '1.5.3.1', '>') && $name == 'tag') continue 2;
					if (version_compare(VERSION, '1.5.5', '<') && $name == 'filters') continue 2;
					if (version_compare(VERSION, '1.5.6', '<') && $name == 'profiles') continue 2;
				} break;
				case 'Categories' : {
					if (version_compare(VERSION, '1.5.5', '<') && $name == 'filters') continue 2;	
				} break;
			}
			
			
			$this->setDataArray($type, $file, $language_id, $name, $query);
		}
		
		foreach ($this->extraGeneralFields[$type] as $fieldData) {
			$file = $destinationDirectory . '/custom_' . $fieldData['name'] . '.xlptemp';
			if (!empty($fieldData['name']) && !empty($fieldData['select_sql']) && empty($fieldData['select_eval'])) {
				$this->setDataArray($type, $file, $language_id, $fieldData['name'], $fieldData['select_sql']);
			} else if (!empty($fieldData['name']) && !empty($fieldData['select_eval'])) {
				$this->setDataArray($type, $file, $language_id, $fieldData['name'], NULL, $fieldData['select_eval']);
			}
		}
	}
	
	private function setDataArray($type, $file, $language_id, $name, $query, $eval = NULL) {
		if (!file_exists($file)) {
			$this->db->query("SET SESSION group_concat_max_len = 1000000;");
			if (!empty($query)) {
				$query = str_replace("{LANGUAGE_ID}", $language_id, $query);
				$query = str_replace("{DB_PREFIX}", DB_PREFIX, $query);
				$result = $this->db->query($query);
				$result = $result !== false ? $result->rows : array();
			} else if (!empty($eval)) {
				$result = false;
				eval($eval);
				$result = $result !== false ? $result : array();	
			}
			
			$this->dataArray[$type][$name] = $result;
			$data = json_encode($result);
			file_put_contents($file, $data);
		} else {
			if (empty($this->dataArray[$type][$name])) {
				$data = file_get_contents($file);
				$this->dataArray[$type][$name] = json_decode($data, true);
			}
		}	
	}
	
	public function getData($type, &$row) {
		$id_key = 'id';
		switch($type) {
			case 'Products' : $id_key = 'product_id'; break;
			case 'Categories' : $id_key = 'category_id'; break;
			case 'Options' : $id_key = 'option_id'; break;
			case 'Attributes' : $id_key = 'attribute_id'; break;
			case 'AttributeGroups' : $id_key = 'attribute_group_id'; break;
			case 'Customers' : $id_key = 'customer_id'; break;
			case 'CustomerGroups' : $id_key = 'customer_group_id'; break;
		}
		foreach ($this->dataArray[$type] as $name => $data) {
			$row[$name] = '';
			foreach ($data as $array) {
				if ($array[$id_key] == $row[$id_key]) {
					$row[$name] = $array['value'];
					break;
				}
			}
		}
	}
	
	public function importXLS($type, $language, $file = '', $settings, $addAsNew = false) {
		$this->language->load('module/excelport');
		if (!file_exists($file)) throw new Exception($this->language->get('excelport_file_not_exists'));
		
		$valid = false;
		if (version_compare(VERSION, '1.5.1.3', '>=') && version_compare(VERSION, IMODULE_UPMOST_VERSION, '<=')) $valid = true;
		if (!$valid) throw new Exception(str_replace('{VERSION}', '1.5.1.3 - ' . IMODULE_UPMOST_VERSION, $this->language->get('text_feature_unsupported')));
		
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		
		switch ($type) {
			case 'Products'	: {
				$this->load->model('module/excelport_product');
				// We need to know the type of file we are importing. We are doing this based on column 4 - if it is Categories, we are in Full mode, if it is Description, we are in Light mode
				require_once(IMODULE_ROOT.'vendors/phpexcel/PHPExcel.php');
				require_once(IMODULE_ROOT.'vendors/phpexcel/CustomReadFilter.php');
				$chunkFilter = new CustomReadFilter(array('Products' => array('E', 1, 'E', 1)));
				$objReader = new PHPExcel_Reader_Excel2007();
				$objReader->setReadFilter($chunkFilter);
				$objReader->setReadDataOnly(true);
				$objReader->setLoadSheetsOnly(array("Products"));
				$objPHPExcel = $objReader->load($file);
				$productsSheet = 0;
				$productSheetObj = $objPHPExcel->setActiveSheetIndex($productsSheet);
				$columnName = trim(strval($productSheetObj->getCell("E1")->getValue()));
				unset($chunkFilter, $productSheetObj, $productsSheet, $objPHPExcel, $objReader);
				
				if ($columnName == 'Categories') { // If the fourth column is Categories, then we are in Full mode
					$this->model_module_excelport_product->importXLSProductsFull($language, $languages, $file, $settings['ImportLimit'], $addAsNew);
				} else if ($columnName == 'Description') { // If the fourth column is Description, then we are in Light mode
					$this->model_module_excelport_product->importXLSProductsLight($language, $languages, $file, $settings['ImportLimit'], $addAsNew);
				} else { // The mode is unknown
					throw new Exception($this->language->get('excelport_mode_unknown'));	
				}
			} break;
			case 'Categories' : {
				$this->load->model('module/excelport_category');
				$this->model_module_excelport_category->importXLSCategories($language, $languages, $file, $settings['ImportLimit']);
			} break;
			case 'Options' : {
				$this->load->model('module/excelport_option');
				$this->model_module_excelport_option->importXLSOptions($language, $languages, $file);
			} break;
			case 'Attributes' : {
				$this->load->model('module/excelport_attribute');
				$this->model_module_excelport_attribute->importXLSAttributes($language, $languages, $file);
			} break;
			case 'Customers' : {
				$this->load->model('module/excelport_customer');
				$this->model_module_excelport_customer->importXLSCustomers($language, $languages, $file, $settings['ImportLimit']);
			} break;
			case 'CustomerGroups' : {
				$this->load->model('module/excelport_customer_group');
				$this->model_module_excelport_customer_group->importXLSCustomerGroups($language, $languages, $file, $settings['ImportLimit']);
			} break;
		}
	}
	public function exportXLS($type, $language, $store, $destinationFolder = '', $settings, $quickExport = false, $filter = false, $filters = array()) {
		$this->language->load('module/excelport');
		
		if (!is_string($destinationFolder)) throw new Exception($this->language->get('excelport_folder_not_string'));
		
		$valid = false;
		if (version_compare(VERSION, '1.5.1.3', '>=') && version_compare(VERSION, IMODULE_UPMOST_VERSION, '<=')) $valid = true;
		if (!$valid) throw new Exception(str_replace('{VERSION}', '1.5.1.3 - ' . IMODULE_UPMOST_VERSION, $this->language->get('text_feature_unsupported')));
		
		switch ($type) {
			case 'Products'	: {
				$this->load->model('module/excelport_product');
				if (!is_numeric($settings['ExportLimit']) || $settings['ExportLimit'] < 50 || $settings['ExportLimit'] > 800) throw new Exception($this->language->get('excelport_export_limit_invalid'));
				if ($quickExport) {
					$this->model_module_excelport_product->exportXLSProductsLight($language, $store, $destinationFolder, $settings['ExportLimit'], !empty($filters['Products']) && $filter ? $filters['Products'] : array());
				} else {
					$this->model_module_excelport_product->exportXLSProductsFull($language, $store, $destinationFolder, $settings['ExportLimit'], !empty($filters['Products']) && $filter ? $filters['Products'] : array());
				}
			} break;
			case 'Categories' : {
				$this->load->model('module/excelport_category');
				$this->model_module_excelport_category->exportXLSCategories($language, $store, $destinationFolder, $settings['ExportLimit'], !empty($filters['Categories']) && $filter ? $filters['Categories'] : array());	
			} break;
			case 'Options' : {
				$this->load->model('module/excelport_option');
				$this->model_module_excelport_option->exportXLSOptions($language, $destinationFolder, $settings['ExportLimit'], !empty($filters['Options']) && $filter ? $filters['Options'] : array());	
			} break;
			case 'Attributes' : {
				$this->load->model('module/excelport_attribute');
				$this->model_module_excelport_attribute->exportXLSAttributes($language, $destinationFolder, !empty($filters['Attributes']) && $filter ? $filters['Attributes'] : array());	
			} break;
			case 'Customers' : {
				$this->load->model('module/excelport_customer');
				$this->model_module_excelport_customer->exportXLSCustomers($language, $store, $destinationFolder, $settings['ExportLimit'], !empty($filters['Customers']) && $filter ? $filters['Customers'] : array());	
			} break;
			case 'CustomerGroups' : {
				$this->load->model('module/excelport_customer_group');
				$this->model_module_excelport_customer_group->exportXLSCustomerGroups($language, $destinationFolder, $settings['ExportLimit'], !empty($filters['CustomerGroups']) && $filter ? $filters['CustomerGroups'] : array());	
			} break;
		}
	}
}
?>