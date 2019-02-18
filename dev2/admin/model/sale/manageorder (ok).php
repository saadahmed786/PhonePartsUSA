<?php

static $config = NULL;
static $log = NULL;

// Error Handler
function error_handler_for_export($errno, $errstr, $errfile, $errline) {
	global $config;
	global $log;
	
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$errors = "Notice";
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$errors = "Warning";
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$errors = "Fatal Error";
			break;
		default:
			$errors = "Unknown";
			break;
	}
		
	if (($errors=='Warning') || ($errors=='Unknown')) {
		return true;
	}

	if ($config->get('config_error_display')) {
		echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}
	
	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}


function fatal_error_shutdown_handler_for_export()
{
	$last_error = error_get_last();
	if ($last_error['type'] === E_ERROR) {
		// fatal error
		error_handler_for_export(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
	}
}


class ModelSaleManageOrder extends Model {	
	protected function clearSpreadsheetCache() {
		$files = glob(DIR_CACHE . 'Spreadsheet_Excel_Writer' . '*');
		
		if ($files) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					@unlink($file);
					clearstatcache();
				}
			}
		}
	}
	
	function populateOrdersWorksheet( &$worksheet, &$database, $orderid )
	{
		// The options headings row
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'order_id' );
		$worksheet->writeString( $i, $j++, 'invoice_no' );
		$worksheet->writeString( $i, $j++, 'invoice_prefix' );
		$worksheet->writeString( $i, $j++, 'customer' );
		$worksheet->writeString( $i, $j++, 'firstname' );
		$worksheet->writeString( $i, $j++, 'lastname' );
		$worksheet->writeString( $i, $j++, 'email' );
		$worksheet->writeString( $i, $j++, 'telephone' );
		$worksheet->writeString( $i, $j++, 'fax' );
		$worksheet->writeString( $i, $j++, 'shipping_firstname' );
		$worksheet->writeString( $i, $j++, 'shipping_lastname' );
		$worksheet->writeString( $i, $j++, 'shipping_company' );
		$worksheet->writeString( $i, $j++, 'shipping_address_1' );
		$worksheet->writeString( $i, $j++, 'shipping_address_2' );
		$worksheet->writeString( $i, $j++, 'shipping_city' );
		$worksheet->writeString( $i, $j++, 'shipping_postcode' );
		$worksheet->writeString( $i, $j++, 'shipping_country' );
		$worksheet->writeString( $i, $j++, 'shipping_zone' );
		$worksheet->writeString( $i, $j++, 'shipping_address_format' );
		$worksheet->writeString( $i, $j++, 'shipping_method' );
		$worksheet->writeString( $i, $j++, 'payment_firstname' );
		$worksheet->writeString( $i, $j++, 'payment_lastname' );
		$worksheet->writeString( $i, $j++, 'payment_company' );
		$worksheet->writeString( $i, $j++, 'payment_address_1' );
		$worksheet->writeString( $i, $j++, 'payment_address_2' );
		$worksheet->writeString( $i, $j++, 'payment_city' );
		$worksheet->writeString( $i, $j++, 'payment_postcode' );
		$worksheet->writeString( $i, $j++, 'payment_country' );
		$worksheet->writeString( $i, $j++, 'payment_zone' );
		$worksheet->writeString( $i, $j++, 'payment_address_format' );
		$worksheet->writeString( $i, $j++, 'payment_method' );
		$worksheet->writeString( $i, $j++, 'comment' );
		$worksheet->writeString( $i, $j++, 'sub_total' );
		$worksheet->writeString( $i, $j++, 'store credit' );
		$worksheet->writeString( $i, $j++, 'total' );
		$worksheet->writeString( $i, $j++, 'currency_code' );
		$worksheet->writeString( $i, $j++, 'currency_value' );
		$worksheet->writeString( $i, $j++, 'date_added' );
		$worksheet->writeString( $i, $j++, 'date_modified' );
		$worksheet->writeString( $i, $j++, 'product_name' );
		$worksheet->writeString( $i, $j++, 'product_model' );
		$worksheet->writeString( $i, $j++, 'product_option' );
		$worksheet->writeString( $i, $j++, 'product_quantity' );
		$worksheet->writeString( $i, $j++, 'product_price' );
		$worksheet->writeString( $i, $j++, 'product_total' );
		$worksheet->writeString( $i, $j++, 'product_tax' );
		
		// The actual options data
		$this->load->model('sale/order');
		$i += 1;
		$j = 0;
		//$query  = "SELECT * FROM `".DB_PREFIX."order` WHERE `order_id` IN ( ".$orderid." )";
		$query  = "SELECT o.*, op.name, op.model,op.order_product_id, op.quantity, op.price, op.total AS ptotal, op.tax, (SELECT ot.value FROM " . DB_PREFIX . "order_total ot WHERE ot.order_id = o.order_id AND ot.code = 'sub_total') AS sub_total, (SELECT ot.value FROM " . DB_PREFIX . "order_total ot WHERE ot.order_id = o.order_id AND ot.code = 'credit') AS store_credit FROM `" . DB_PREFIX . "order` o inner join `" . DB_PREFIX . "order_product` op on o.order_id=op.order_id  WHERE o.order_id IN ( ".$orderid." ) order by o.order_id";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$worksheet->writeString( $i, $j++, $row['order_id'] );
			$worksheet->writeString( $i, $j++, $row['invoice_no'] );
			$worksheet->writeString( $i, $j++, $row['invoice_prefix'] );
			$worksheet->writeString( $i, $j++, $row['customer_id'] );
			$worksheet->writeString( $i, $j++, $row['firstname'] );
			$worksheet->writeString( $i, $j++, $row['lastname'] );
			$worksheet->writeString( $i, $j++, $row['email'] );
			$worksheet->writeString( $i, $j++, $row['telephone'] );
			$worksheet->writeString( $i, $j++, $row['fax'] );
			$worksheet->writeString( $i, $j++, $row['shipping_firstname'] );
			$worksheet->writeString( $i, $j++, $row['shipping_lastname'] );
			$worksheet->writeString( $i, $j++, $row['shipping_company'] );
			$worksheet->writeString( $i, $j++, $row['shipping_address_1'] );
			$worksheet->writeString( $i, $j++, $row['shipping_address_2'] );
			$worksheet->writeString( $i, $j++, $row['shipping_city'] );
			$worksheet->writeString( $i, $j++, $row['shipping_postcode'] );
			$worksheet->writeString( $i, $j++, $row['shipping_country'] );
			$worksheet->writeString( $i, $j++, $row['shipping_zone'] );
			$worksheet->writeString( $i, $j++, $row['shipping_address_format'] );
			$worksheet->writeString( $i, $j++, $row['shipping_method'] );
			$worksheet->writeString( $i, $j++, $row['payment_firstname'] );
			$worksheet->writeString( $i, $j++, $row['payment_lastname'] );
			$worksheet->writeString( $i, $j++, $row['payment_company'] );
			$worksheet->writeString( $i, $j++, $row['payment_address_1'] );
			$worksheet->writeString( $i, $j++, $row['payment_address_2'] );
			$worksheet->writeString( $i, $j++, $row['payment_city'] );
			$worksheet->writeString( $i, $j++, $row['payment_postcode'] );
			$worksheet->writeString( $i, $j++, $row['payment_country'] );
			$worksheet->writeString( $i, $j++, $row['payment_zone'] );
			$worksheet->writeString( $i, $j++, $row['payment_address_format'] );
			$worksheet->writeString( $i, $j++, $row['payment_method'] );
			$worksheet->writeString( $i, $j++, $row['comment'] );
			$worksheet->writeString( $i, $j++, $row['sub_total'] );
			$worksheet->writeString( $i, $j++, $row['store_credit'] );
			$worksheet->writeString( $i, $j++, $row['total'] );
			$worksheet->writeString( $i, $j++, $row['currency_code'] );
			$worksheet->writeString( $i, $j++, $row['currency_value'] );
			$worksheet->writeString( $i, $j++, $row['date_added'] );
			$worksheet->writeString( $i, $j++, $row['date_modified'] );
			$worksheet->writeString( $i, $j++, $row['name'] );
			$worksheet->writeString( $i, $j++, $row['model'] );
			$order_options = array();
			$order_str = array();
			$order_options = $this->model_sale_order->getOrderOptions($row['order_id'], $row['order_product_id']);
			foreach($order_options as $order_option){
				$order_str[] = $order_option['name'].':'.$order_option['value'];
			}
			$worksheet->writeString( $i, $j++, implode('@@@' , $order_str));
			$worksheet->writeString( $i, $j++, $row['quantity'] );
			$worksheet->writeString( $i, $j++, $row['price'] );
			$worksheet->writeString( $i, $j++, $row['ptotal'] );
			$worksheet->writeString( $i, $j++, $row['tax'] );
			$i += 1;
			$j = 0;
		}
	}
	
	public function exportOrder($selectid) {
		global $config;
		global $log;
		$config = $this->config;
		$log = $this->log;
		set_error_handler('error_handler_for_export',E_ALL);
		register_shutdown_function('fatal_error_shutdown_handler_for_export');
		$database =& $this->db;

		// We use the package from http://pear.php.net/package/Spreadsheet_Excel_Writer/
		chdir( DIR_SYSTEM . 'pear' );
		require_once "Spreadsheet/Excel/Writer.php";
		chdir( DIR_APPLICATION );
		
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setTempDir(DIR_CACHE);
		$workbook->setVersion(8); // Use Excel97/2000 BIFF8 Format
		
		// sending HTTP headers
		$workbook->send('download_orders.xls');
		
		// Creating the categories worksheet
		$worksheet =& $workbook->addWorksheet('sheet');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateOrdersWorksheet( $worksheet, $database, $selectid); 
		//$worksheet->freezePanes(array(1, 1, 1, 1));
		
			
		// Let's send the file
		$workbook->close();
		
		// Clear the spreadsheet caches
		$this->clearSpreadsheetCache();
		exit;
	}

	public function getOrders($data = array()) {
		$sql = "SELECT o.order_id, CONCAT(o.invoice_prefix,o.invoice_no) AS invoice_id, CONCAT(o.shipping_firstname, ' ', o.shipping_lastname , '<br />' , o.shipping_address_1 , ' ' , o.shipping_address_2, ' ', o.shipping_city, ' ', o.shipping_zone, ' ', o.shipping_postcode , '<br />', o.shipping_country, '<br />', o.telephone) AS customer, o.email, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, o.payment_method, (SELECT ot.value FROM " . DB_PREFIX . "order_total ot WHERE ot.order_id = o.order_id AND ot.code = 'sub_total') AS sub_total, (SELECT ot.value FROM " . DB_PREFIX . "order_total ot WHERE ot.order_id = o.order_id AND ot.code = 'credit') AS store_credit, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";

		if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
		
		if (isset($data['filter_invoice_id']) && !is_null($data['filter_invoice_id'])) {
			$sql .= " AND CONCAT(o.invoice_prefix,o.invoice_no)  LIKE '%" . $this->db->escape($data['filter_invoice_id']) . "%'";
		} 

		if (isset($data['filter_order_id']) && !is_null($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (isset($data['filter_customer']) && !is_null($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.shipping_firstname, ' ', o.shipping_lastname , '<br />' , o.shipping_address_1 , ' ' , o.shipping_address_2, ' ', o.shipping_city, ' ', o.shipping_zone, ' ', o.shipping_postcode , '<br />', o.shipping_country, '<br />', o.telephone) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
			$sql .= " AND o.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (isset($data['filter_total']) && !is_null($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'invoice_id',
			'customer',
			'o.email',
			'status',
			'o.date_added',
			'o.date_modified',
			'o.total',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
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

	public function getTotalOrders($data = array()) {
      	$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o";

		if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
		
		if (isset($data['filter_invoice_id']) && !is_null($data['filter_invoice_id'])) {
			$sql .= " AND CONCAT(o.invoice_prefix,o.invoice_no)  LIKE '%" . $this->db->escape($data['filter_invoice_id']) . "%'";
		} 

		if (isset($data['filter_order_id']) && !is_null($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (isset($data['filter_customer']) && !is_null($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.shipping_firstname, ' ', o.shipping_lastname , '<br />' , o.shipping_address_1 , ' ' , o.shipping_address_2, ' ', o.shipping_city, ' ', o.shipping_zone, ' ', o.shipping_postcode , '<br />', o.shipping_country, '<br />', o.telephone) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
			$sql .= " AND o.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (isset($data['filter_total']) && !is_null($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function getOrderStatusComment() {
      	$query = $this->db->query("SELECT order_status_id,comment FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' order by order_status_id asc");
		
		return $query->rows;
	}
	
	public function versiontoint(){
		$tmpints = explode(".",VERSION);
		$tmpint = "";
		if(isset($tmpints[0])){
			$tmpint .= $tmpints[0];
		} else {
			$tmpint .= "0";
		}
		if(isset($tmpints[1])){
			$tmpint .= $tmpints[1];
		} else {
			$tmpint .= "0";
		}
		if(isset($tmpints[2])){
			$tmpint .= $tmpints[2];
		} else {
			$tmpint .= "0";
		}
		if(isset($tmpints[3])){
			$tmpint .= $tmpints[3];
		} else {
			$tmpint .= "0";
		}
		
		return (int)$tmpint;
	}
}
?>