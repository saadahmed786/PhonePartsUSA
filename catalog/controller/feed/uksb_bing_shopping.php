<?php 
class ControllerFeedUksbBingShopping extends Controller {
	public function plainText($string) {
	    $table = array(
		'“'=>'"', '”'=>'"', '‘'=>"'", '’'=>"'", '•'=>'*', '—'=>'-', '–'=>'-', '¿'=>'?', '¡'=>'!', '°'=>' deg. ',
		'÷'=>' / ', '×'=>'X', '±'=>'+/-',
		'&amp;' => '&', '&quot;'=>'"', '&nbsp;'=> ' ', "\n"=> ' ', "\r"=> ' ', "\t"=> ' '
	    );

	    $string = strip_tags(html_entity_decode($string));
	    $string = strtr($string, $table);
	    $string = preg_replace('/&#?[a-z0-9]+;/i',' ',$string);	
	    $string = preg_replace('/\s{2,}/i', ' ', $string );	
	    if($this->config->get('uksb_google_merchant_characters')){

		$table2 = array(
				'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 
				'Þ'=>'B', 'þ'=>'b', 'ß'=>'Ss',
				'ç'=>'c',
				'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 
				'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
				'ñ'=>'n',
				'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'œ'=>'o', 'ð'=>'o',
				'š'=>'s',
				'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u',
				'ý'=>'y', 'ÿ'=>'y', 
				'ž'=>'z', 'ž'=>'z',
				'©'=>'(c)', '®'=>'(R)'
		);

		$string = strtr($string, $table2);
		$string = preg_replace('/[^(\x20-\x7F)]*/','', $string ); 
	    }
	    return substr($string, 0, 5000 );	
	}

	public function index() {
		if ($this->config->get('uksb_google_merchant_status')) { 
			$output  = "MPID\tTitle\tBrand\tMPN\tUPC\tProductURL\tDealURL\tPrice\tPricewithDiscount\tDateStartDate\tDealExpirationDateTime\tAvailability\tDescription\tImageURL\tMerchantCategory\tCondition";
			
			$this->load->model('feed/uksb_google');
		
			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
				$server = $this->config->get('config_ssl');
			} else {
				$server = $this->config->get('config_url');
			}

			if(isset($this->request->get['send'])){
				$split = explode("-", $this->request->get['send']);
				$data = array('start' => ($split[0]-1), 'limit' => ($split[1]-$split[0]+1));
				$products = $this->model_feed_uksb_google->getProducts($data);
			}else{
				$products = $this->model_feed_uksb_google->getProducts();
			}
			
			foreach ($products as $product) {
				
				$sizes = explode(",", $product['g_size']); $num_sizes = count($sizes);
				$colours = explode(",", $product['g_colour']); $num_colours = count($colours);
				$materials = explode(",", $product['g_material']); $num_materials = count($materials);
				$patterns = explode(",", $product['g_pattern']); $num_patterns = count($patterns);
				$mpns = explode(",", $product['v_mpn']); $num_mpns = count($mpns);
				$gtins = explode(",", $product['v_gtin']); $num_gtins = count($gtins);
				$prices = explode(",", $product['v_prices']); $num_prices = count($prices);
				$images = explode(",", $product['v_images']); $num_images = count($images);
				
				$variant = 0;
				$variant = (count($colours)>1||count($sizes)>1||count($materials)>1||count($patterns)>1?1:0);
				$max_variants = max($num_sizes, $num_colours, $num_materials, $num_patterns, $num_mpns, $num_gtins, $num_prices);
				
				if($variant==1){
					for($i = 1; $i <= $max_variants; $i++){
						
						$j = $i-1;
						
						$output .= "\n";
						$output .= $product['product_id'] . '_' . $i . "\t";
						$output .= $this->plainText($product['name'] . (isset($colours[$j])&&trim($colours[$j])!=''?' - '.trim($colours[$j]):'') . (isset($sizes[$j])&&trim($sizes[$j])!=''?' - '.trim($sizes[$j]):'')) ."\t";
						$output .= $this->plainText(($product['g_brand'] ? $product['g_brand'] : $product['manufacturer'])) . "\t";
						
						if(isset($mpns[$j])&&trim($mpns[$j])!=''){$output .= trim($mpns[$j]) . "\t";}else{$output .= "\t";}
						if(isset($gtins[$j])){$output .= $gtins[$j] . "\t";}else{$output .= "\t";}
						
						$output .= str_replace("&amp;", "&", $this->url->link('product/product', 'product_id=' . $product['product_id'])) . "\t";
						$output .= str_replace("&amp;", "&", $this->url->link('product/product', 'product_id=' . $product['product_id'])) . "\t";

						if(isset($prices[$j])&&trim($prices[$j])!=''){
							 
							$quantifier = strval($prices[$j]);
							$pricevalue = floatval($prices[$j]);
							if($quantifier == ''){
								$quantifier = '+';
							}
							if($pricevalue==''){
								$pricevalue=0;
							}
						}else{
							$quantifier = '+';
							$pricevalue = '0';
						}
						
						$currency = 'USD';
						$price = ($quantifier=='-'?$product['price'] - $pricevalue:$product['price'] + $pricevalue);
						$output .= $this->currency->format($price, $currency, FALSE, FALSE) . "\t";
						
						if ((float)$product['special']) {
							$sprice = ($quantifier=='-'?$product['special'] - $pricevalue:$product['special'] + $pricevalue);
							$output .= $this->currency->format($sprice, $currency, FALSE, FALSE) . "\t";
							$output .= $this->model_feed_uksb_google->getFeedSpecialStartDate($product['product_id']).'T00:00:00' . "\t";
							$output .= $this->model_feed_uksb_google->getFeedSpecialEndDate($product['product_id']).'T23:59:59' ."\t";
						}else{
							$output .= "\t\t\t";
						}
						
						
						if($this->config->get('config_stock_checkout')==0){
							if ($product['quantity']>0) {
								$output .= "In Stock\t";
							} else {
								$output .= "Out of Stock\t";
							}
						}else{
							if ($product['quantity']>0) {
								$output .= "In Stock\t";
							} else {
								$output .= "Back-Order\t";
							}
						}

						$output .= $this->plainText($product['description']) . "\t";
						
						if($images[$j] != '' && $images[$j] != 'no_image.jpg' ){
							$output .= $server . 'image/' . str_replace(" ", "%20", $images[$j]) . "\t";
						}elseif ($product['image']) {
							$output .= $server . 'image/' . str_replace(" ", "%20", $product['image']) . "\t";
						} else {
							$output .= $server . 'image/no_image.jpg' . "\t";
						}
						
						$categories = $this->model_feed_uksb_google->getCategories($product['product_id']);
						
						$catno = 1;
						foreach ($categories as $category) {
							if($catno<2){
								$path = $this->getPath(array('parent_id' => $category['category_id']));
								if ($path) {
									$string = '';
									
									foreach (explode('_', $path) as $path_id) {
										$category_info = $this->model_feed_uksb_google->getCategory($path_id);
										
										if ($category_info) {
											if (!$string) {
												$string = $category_info['name'];
											} else {
												$string .= ' > ' . $category_info['name'];
											}
										}
									}
									$output .= $string . "\t";
									$catno++;
								}
							}
						}

						$output .= "New";
					}
				}else{
					$output .= "\n";
					$output .= $product['product_id'] . "\t";
					$output .= $this->plainText($product['name']) . "\t";
					$output .= $this->plainText(($product['g_brand'] ? $product['g_brand'] : $product['manufacturer'])) . "\t";
						
					if($this->config->get('uksb_google_merchant_mpn')=='mpn'){
						if($product['mpn']){$output .= $product['mpn'] . "\t";}
					}elseif($this->config->get('uksb_google_merchant_mpn')=='location'){
						if($product['location']){$output .= $product['location'] . "\t";}
					}elseif($this->config->get('uksb_google_merchant_mpn')=='sku'){
						if($product['sku']){$output .= $product['sku'] . "\t";}
					}else{
						if($product['model']){$output .= $product['model'] . "\t";}
						else{$output .= "\t";}
					}

					if($this->config->get('uksb_google_merchant_g_gtin')=='gtin'){
						if($product['g_gtin']){$output .= $product['g_gtin'] . "\t";}
					}elseif($this->config->get('uksb_google_merchant_g_gtin')=='location'){
						if($product['location']){$output .= $product['location'] . "\t";}
					}elseif($this->config->get('uksb_google_merchant_g_gtin')=='sku'){
						if($product['sku']){$output .= $product['sku'] . "\t";}
					}elseif($this->config->get('uksb_google_merchant_g_gtin')=='upc'){
						if($product['upc']){$output .= $product['upc'] . "\t";}
					}else{$output .= "\t";}

					$output .= str_replace("&amp;", "&", $this->url->link('product/product', 'product_id=' . $product['product_id'])) . "\t";
					$output .= str_replace("&amp;", "&", $this->url->link('product/product', 'product_id=' . $product['product_id'])) . "\t";

					$currency = 'USD';
					$output .= $this->currency->format($product['price'], $currency, FALSE, FALSE) . "\t";
					
					if ((float)$product['special']) {
						$output .= $this->currency->format($product['special'], $currency, FALSE, FALSE) . "\t";
						$output .= $this->model_feed_uksb_google->getFeedSpecialStartDate($product['product_id']).'T00:00:00' . "\t";
						$output .= $this->model_feed_uksb_google->getFeedSpecialEndDate($product['product_id']).'T23:59:59' ."\t";
					}else{
						$output .= "\t\t\t";
					}


					if($this->config->get('config_stock_checkout')==0){
						if ($product['quantity']>0) {
							$output .= "In Stock\t";
						} else {
							$output .= "Out of Stock\t";
						}
					}else{
						if ($product['quantity']>0) {
							$output .= "In Stock\t";
						} else {
							$output .= "Back-Order\t";
						}
					}

					$output .= $this->plainText($product['description']) . "\t";
					
					if ($product['image']) {
						$output .= $server . 'image/' . str_replace(" ", "%20", $product['image']) . "\t";
					} else {
						$output .= $server . 'image/no_image.jpg' . "\t";
					}

					$categories = $this->model_feed_uksb_google->getCategories($product['product_id']);
					
					$catno = 1;
					foreach ($categories as $category) {
						if($catno<2){
							$path = $this->getPath(array('parent_id' => $category['category_id']));
							if ($path) {
								$string = '';
								
								foreach (explode('_', $path) as $path_id) {
									$category_info = $this->model_feed_uksb_google->getCategory($path_id);
									
									if ($category_info) {
										if (!$string) {
											$string = $category_info['name'];
										} else {
											$string .= ' > ' . $category_info['name'];
										}
									}
								}
								$output .= $string . "\t";
								$catno++;
							}
						}
					}

					$output .= "New";
				}
			}
			
			$this->response->addHeader('Content-Type: text/plain; charset=utf-8');
			$this->response->setCompression(0);
			$this->response->setOutput($output);
		}
	}
	
	protected function getPath($params = array())
	{
		$defaults = array( // the defaults will be overidden if set in $params
			'parent_id' => 0,
			'current_path' => '',
		);
	
		$params = array_merge($defaults, $params);

		$category_info = $this->model_feed_uksb_google->getCategory($params['parent_id']);
	
		if ($category_info) {
			if (!$params['current_path']) {
				$new_path = $category_info['category_id'];
			} else {
				$new_path = $category_info['category_id'] . '_' . $params['current_path'];
			}	
		
			$path = $this->getPath(array('parent_id' => $category_info['parent_id'], 'current_path' => $new_path));
					
			if ($path) {
				return $path;
			} else {
				return $new_path;
			}
		}
	}		
}
?>