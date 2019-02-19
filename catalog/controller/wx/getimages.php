<?php
use Elasticsearch\ClientBuilder;
require_once(DIR_APPLICATION . '../wx/vendor/autoload.php');

class ControllerWxGetimages extends Controller {
	
	public function index() { 

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');
		
		$result = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE imageurl <> ''");
		
		foreach ($result->rows as $product) {
		
			
			$file_part = str_replace("https://us.vwr.com/stibo/low_res/std.lang.all/", "", $product['imageurl']);
			$filename  = DIR_APPLICATION . "../image/data/" . $file_part;
			$dirname   = dirname($filename);
			
			if (!is_dir($dirname)) {
				mkdir($dirname, 0755, true);
				//echo "creating folder: " . $dirname . "\n";
			}
			
			$ch = curl_init($product['imageurl']);
			$fp = fopen($filename, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			
			
			fclose($fp);
			//echo "wrote image: " . $filename . "\n";
			
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = 'data/" . $file_part . "', imageurl = '' WHERE product_id = " . $product['product_id']);
			echo "update product: " . $product['product_id'] . "    " . $product['imageurl'] . "\n";
			//exit();
		}
	}
}