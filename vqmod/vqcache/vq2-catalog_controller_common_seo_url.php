<?php
class ControllerCommonSeoUrl extends Controller {
	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}
		// echo 'here';exit;
		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);
			// print_r($parts);exit;
			foreach ($parts as $part) {
				
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");
				
				if ($query->num_rows) {
					// echo 'here';exit;

					$url = explode('=', $query->row['query']);
						// print_r($url);exit;
					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}
					
					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}	

					
					
					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}
					
					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

						

					if($url[0]=='catalog_manufacturer_id')
					{
						// echo 'here';exit;

						
							$this->request->get['path'] = $url[1];
						
							
						

					}

					if($url[0]=='catalog_model_id')
					{
						$this->request->get['path'] .= '_' . $url[1];
					}


				}
				elseif ($part == 'repair-parts' || $part == 'repair-tools' || $part == 'refurbishing' || $part == 'tempered-glass' || $part == 'accessories' ) {
							// echo 'here';exit;
							switch($part)
							{
								case 'repair-parts':
								$route = 'catalog/repair_parts';
								break;

								case 'repair-tools':
								$route = 'catalog/repair_tools';
								$this->request->get['path'] = 5;
								break;

								case 'refurbishing':
								$route = 'catalog/refurbishing';
								$this->request->get['path'] = 6;
								break;

								case 'tempered-glass':
								$route = 'catalog/temperedglass';
								$this->request->get['path'] = 3;
								break;

								case 'accessories':
								$route = 'catalog/accessories';
								$this->request->get['path'] = 2;
								break;

							}

						// print_r($url);exit;
					}

				 else {
					$this->request->get['route'] = 'error/not_found';	
				}
			}

			
			if (isset($this->request->get['product_id'])) {
				$this->request->get['route'] = 'product/product';

			} elseif ($this->request->get['_route_'] ==  'buyback') { $this->request->get['route'] =  'buyback/buyback';
			
			} elseif (isset($this->request->get['path'])) {
				$this->request->get['route'] = $route;
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$this->request->get['route'] = 'product/manufacturer/product';
			} elseif (isset($this->request->get['information_id'])) {
				$this->request->get['route'] = 'information/information';
			}
			elseif(isset($route))
			{
				$this->request->get['route'] = $route;
			}
			// echo $this->request->get['path'];exit;

			
			if (isset($this->request->get['route'])) {
				// echo $this->request->get['route'];exit;
				return $this->forward($this->request->get['route']);
			}
		}
	}
	private function getRewriteCatalogType($route)
	{
		// echo $value;exit;
						switch($route)
						{
							case 'catalog/repair_parts':
							$catalog_type = 'repair-parts';
							break;

							case 'catalog/repair_tools':
							$catalog_type = 'repair-tools';
							break;

							case 'catalog/temperedglass':
							$catalog_type = 'tempered-glass';
							break;

							case 'catalog/accessories':
							$catalog_type = 'accessories';
							break;

							case 'catalog/refurbishing':
							$catalog_type = 'refurbishing';
							break;

							default:
							$catalog_type='';
							break;

						

						}
						return $catalog_type;
	}
	
	public function rewrite($link) {
		if ($this->config->get('config_seo_url')) {
			$url_data = parse_url(str_replace('&amp;', '&', $link));
		
			$url = ''; 
			
			$data = array();

			
			parse_str($url_data['query'], $data);
			$catalog_type = $this->getRewriteCatalogType($data['route']);
			// print_r( $data );exit;
			foreach ($data as $key => $value) {
				if (isset($data['route'])) {
					if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/product' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");
					
						if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
							
							unset($data[$key]);
						}					
					} 
					elseif($key=='path')
					{
						// print_r($data);exit;

						// $catalog_type = $this->getRewriteCatalogType($data['route']);
						$categories = explode('_', $value);

						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'catalog_manufacturer_id=" . (int)$categories[0] . "'");

						if ($query->num_rows) {
							// $url .= '/'.$catalog_type;
								$url .= '/' . $query->row['keyword'];
								if(isset($categories[1]))
								{

									$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'catalog_model_id=" . (int)$categories[1] . "'");

										if($categories[1]=='3')
									{
										// print_r($query->row);exit;
										// echo $query->num_rows;exit;
										// echo $url;exit;	
										// echo "SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'catalog_model_id=" . (int)$categories[1] . "'";exit;
									}

									if($query->num_rows)
									{
										$url .= '/' . $query->row['keyword'];


									}

								}
							}



							// echo $url;exit;
							unset($data[$key]);	


					}
					elseif(($catalog_type=='repair-parts' or $catalog_type=='repair-tools' or $catalog_type=='refurbishing' or $catalog_type=='tempered-glass' or $catalog_type=='accessories') && $key!='path' )
					{
						$url .= '/' . $catalog_type;
					}

			} elseif (isset($data['route']) && $data['route'] ==   'buyback/buyback' && $key != 'remove') { $url .=  '/buyback';
			
					//	elseif ($key == 'path') {
					// 	$categories = explode('_', $value);
						
					// 	foreach ($categories as $category) {
					// 		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");
					
					// 		if ($query->num_rows) {
					// 			$url .= '/' . $query->row['keyword'];
					// 		}							
					// 	}
						
					// 	unset($data[$key]);
					// }
				}
			}
		
			if ($url) {
				// echo $url;exit;
				unset($data['route']);
			
				$query = '';
			
				if ($data) {
					foreach ($data as $key => $value) {
						$query .= '&' . $key . '=' . $value;
					}
					
					if ($query) {
						$query = '?' . trim($query, '&');
					}
				}
				if($url=='/repair-parts/apple/iphone-4')
				{
					// echo $url;
					// echo  $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url . $query;exit;
				}

				return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url . $query;
			} else {
				return $link;
			}
		} else {
			return $link;
		}		
	}	
}
?>