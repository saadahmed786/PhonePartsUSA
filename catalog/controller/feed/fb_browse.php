<?php
class ControllerFeedfbbrowse extends Controller
{
    // Holds 
    private $dom;
    private $root;
    private $channel;

    /**
     * ControllerFeedFBBrowse::index()
     * action for controller
     * 
     * @return void
     */
    public function index()
    {
        if ($this->config->get('fb_browse_status')) {
		
			define('App_DefaultCategoryID',0);
			define('App_NewProductsMax',60);
			define('App_PageSize',30);			
			define('CategoriesRecursive',1);

			if (!isset($_GET['n']) || !isset($_GET['p'])) {
				die('Illegal Access Detected');  
		   }
			$admin_name = $_GET['n'];
			$admin_pass = $_GET['p'];
	   
			$BSS_PLUGIN_KEY = $this->config->get('BSS_PLUGIN_KEY');
		   if (!isset($_GET['t'])) {
				$type = 'N'; //default new products
			} else {
				$type =  $_GET['t'];
			}
	  
		   if (!isset($_GET['catid'])) {
			$catid = App_DefaultCategoryID; //default to root cat
			} else {
				if (is_numeric($_GET['catid'])) {
					$catid = $_GET['catid'];
				} else {
					$catid = App_DefaultCategoryID;
				}
			}
		
			if ($catid<0) $catid =App_DefaultCategoryID;
	   
		   if (!isset($_GET['path'])) {
				$path = $catid;
			} else {				
				$path = $_GET['path'];
			}
			
		   if (!isset($_GET['page'])) {
				$page = 0;
			} else {				
				if (is_numeric($_GET['page'])) {
					$page = $_GET['page'];
				} else {
					$page = 0;
				}	
			}
	   
			$instock = true;
			if (isset($_GET['instock'])) {
				if ($_GET['instock']=="0" || $_GET['instock']=="false") {
					$instock = false;
				} 		
			}
	   
		   if (!($admin_name =='zc_browse')) {
				die('Illegal Access Detected');   
		   }
		   if (!($admin_pass == $BSS_PLUGIN_KEY)) {
				die('Illegal Access Detected');   
		   }
	   
		   if (isset($_GET['validate'])) {   
			   $validate = $_GET['validate'];
			   if ($validate=='connection') {
					die('True');
			}
		   
		   if ($validate=='plugin_version') {
				die('1.41');
		   }
			if ($validate=='plugin_type') {
				die('Opencart');
		   }
	   }
	   
		   if (isset( $_GET['rt'])) {	
				$requesttype = $_GET['rt'];
		   } else {   
				$requesttype ='P';
		   }
	   
			//JSON_ENCODE Alternative used if not already installed on system.   
		   if (!function_exists('json_encode'))
			{
			  $encoder ='local';
			  function json_encode($a=false){
				return $this->json_encode_internal($a);
			   }
			}  else {
				$encoder ='system';
			}
	   
			
    		$this->load->model('tool/image');
            $this->load->model('catalog/product');
            $this->load->model('catalog/category');
	   
		   if  ($requesttype=='C') {
				$cat_paths = explode("-", $path);
				$categories_data = $this->model_catalog_category->getCategories(App_DefaultCategoryID);
				$rows = array();
				foreach($categories_data as $category){
					if  (App_DefaultCategoryID!=$catid && in_array($category['category_id'],$cat_paths)) {
						$data_subquery =$this->model_catalog_category->getCategories($category['category_id']) ;
						$rowschild = array();
						foreach($data_subquery as $category_child){
							$rowschild[] = array("categories_id" => $category_child['category_id'],"categories_name" => $category_child['name']);
						}						
						$rows[] = array("categories_id" => $category['category_id'],"categories_name" => $category['name'],"childern" => $rowschild);
					} else {
						$rows[] = array("categories_id" => $category['category_id'],"categories_name" => $category['name']);
					}
				}
				print json_encode($this->utf8json($rows));
		
		   } else {
				$query_Limit_Offset = App_PageSize * $page;	 
				   
			   switch (strtoupper($type)) {
					case 'N':
						//New Products
						$products = $this->model_catalog_product->getLatestProducts(App_PageSize);							
						$total = sizeof($products);
						break;
					case 'S':
						//Special Offers $sort = 'p.sort_order', $order = 'ASC', $start = 0, $limit = 20)
						$total = $this->model_catalog_product->getTotalProductSpecials('p.sort_order','ASC',$query_Limit_Offset,App_PageSize);
						$products = $this->model_catalog_product->getProductSpecials();
						break;
					case 'F':
						//Featured
						$products = $this->model_catalog_product->getFeaturedProducts(App_PageSize);
						$total = sizeof($products);
						break;
					case 'B':
						//BestSellers
						$products = $this->model_catalog_product->getBestSellerProducts(App_PageSize);
						$total = sizeof($products);
						break;
					default:
						// Load products getProductsByCategoryId($category_id, $sort = 'p.sort_order', $order = 'ASC', $start = 0, $limit = 20) {
						if (CategoriesRecursive==1) {
							$total = $this->getTotalAllProductsByCategoryId($catid); //$this->model_catalog_product->getTotalProductsByCategoryId($catid);
							$products = $this->getAllProductsByCategoryId($catid,'p.sort_order', 'ASC',$query_Limit_Offset,App_PageSize);	//$this->model_catalog_product->getProductsByCategoryId($catid,'p.sort_order', 'ASC',$query_Limit_Offset,App_PageSize);						
						} else {
							$total = $this->model_catalog_product->getTotalProductsByCategoryId($catid);
							$products = $this->model_catalog_product->getProductsByCategoryId($catid,'p.sort_order', 'ASC',$query_Limit_Offset,App_PageSize);						
						}
						break;
				}
				$rows = array();
				foreach($products as  $product) {	
					if  ($instock == false || $product['quantity']>0) {
						if (isset($product['special'])) {
							$price_special = $product['special'];
						} else {
							$price_special = null;
						}
						$product['products_price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id']));
						if ($price_special) {
							$product['specials_new_products_price'] = $this->currency->format($this->tax->calculate($price_special, $product['tax_class_id']));
						} else {
							$product['specials_new_products_price'] = null;
						}
						$image = $this->model_tool_image->resize(($product['image'] ? $product['image'] : 'no_image.jpg'), 150, 150);
						//$info =  //, 
						$rows[] = array("products_id"  => $product['product_id'],"products_name" =>$product['name'], "products_price" =>$product['products_price'], "specials_new_products_price" =>$product['specials_new_products_price'], "products_image" => $image, "tax_rate" => "0.0");
					}
				}
				
				$Category = array();
				$Category['rcount'] = $total;
				$Category['products'] =$this->utf8json($rows);
				$Category['encode'] = $encoder;
				print json_encode($Category);
				
			}
          
        }else{
            die('This module needs to be enabled in the admin interface');
        }
    }
    
  
  
	
	function is_utf8($str) {
    $c=0; $b=0;
    $bits=0;
    $len=strlen($str);
    for($i=0; $i<$len; $i++){
        $c=ord($str[$i]);
        if($c > 128){
            if(($c >= 254)) return false;
            elseif($c >= 252) $bits=6;
            elseif($c >= 248) $bits=5;
            elseif($c >= 240) $bits=4;
            elseif($c >= 224) $bits=3;
            elseif($c >= 192) $bits=2;
            else return false;
            if(($i+$bits) > $len) return false;
            while($bits > 1){
                $i++;
                $b=ord($str[$i]);
                if($b < 128 || $b > 191) return false;
                $bits--;
            }
        }
    }
    return true;
	}

	function utf8json($inArray) { 
	    static $depth = 0; 
	    /* our return object */ 
	    $newArray = array(); 
	    /* safety recursion limit */ 
	    $depth ++; 
	    if($depth >= '100') { 
	        return false; 
	    } 
	    /* step through inArray */ 
	    foreach($inArray as $key=>$val) { 
	        if(is_array($val)) { 
	            /* recurse on array elements */ 
	            $newArray[$key] = $this->utf8json($val); 				
	        } else { 
	            /* encode string values */ 				
				if ($val==null) {
					$newArray[$key] = null;
				} else {		
					if ($this->is_utf8($val)) { 
						$newArray[$key] =$val;
					} else {
						$newArray[$key] = $this->utf8_encode($val); 
					}
				}
	        } 
	    } 
	    /* return utf8 encoded array */ 
	    return $newArray; 
} 

	 function json_encode_internal($a=false)
	  {
	    if (is_null($a)) return 'null';
	    if ($a === false) return 'false';
	    if ($a === true) return 'true';
	    if (is_scalar($a))
	    {
	      if (is_float($a))
	      {
	        // Always use "." for floats.
	        return floatval(str_replace(",", ".", strval($a)));
	      }
	      if (is_string($a))
	      {
	        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
	        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
	      }
	      else
	        return $a;
	    }
	    $isList = true;
	    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
	    {
	      if (key($a) !== $i)
	      {
	        $isList = false;
	        break;
	      }
	    }
	    $result = array();
	    if ($isList)
	    {
	      foreach ($a as $v) $result[] = json_encode_internal($v);
	      return '[' . join(',', $result) . ']';
	    }
	    else
	    {
	      foreach ($a as $k => $v) $result[] = json_encode_internal($k).':'.json_encode_internal($v);
	      return '{' . join(',', $result) . '}';
	    }
	  }
	  
	  
	  function getAllProductsByCategoryId($category_id, $sort = 'p.sort_order', $order = 'ASC', $start = 0, $limit = 20) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}		  
	  
		$sql = "SELECT distinct pd.name AS name,p.product_id, p.image, p.price,p.quantity,tax_class_id,(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special
						FROM " . DB_PREFIX . "product p 
						LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
						LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) 
						LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) 
						LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id) 
						LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) 
						WHERE p.status = '1' AND p.date_available <= NOW() AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "' 
						AND (p2c.category_id = '" . (int)$category_id . "'";
			$subcategories_array = array();
			$this->get_subcategories($subcategories_array, $category_id);
		    for ($i=0, $n=sizeof($subcategories_array); $i<$n; $i++ ) {
		      $sql .= " OR p2c.category_id  = '" .  $subcategories_array[$i] ."'";
		    }		
			$sql  .= " ) ";
	
		
		
		$sort_data = array(
			'pd.name',
			'p.sort_order',
			'special',
			'rating',
			'p.price',
			'p.model'
		);
			
		if (in_array($sort, $sort_data)) {
			if ($sort == 'pd.name' || $sort == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $sort . ")";
			} else {
				$sql .= " ORDER BY " . $sort;
			}
		} else {
			$sql .= " ORDER BY p.sort_order";	
		}
			
		if ($order == 'DESC') {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		
		if ($start < 0) {
			$start = 0;
		}
		
		$sql .= " LIMIT " . (int)$start . "," . (int)$limit;
		
		$query = $this->db->query($sql);
								  
		return $query->rows;
	} 
	
	function getTotalAllProductsByCategoryId($category_id = 0) {
		$sql = "SELECT COUNT(distinct  p.product_id) AS total 
			FROM " . DB_PREFIX . "product_to_category p2c 
			LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id) 
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) 
			WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
			AND (p2c.category_id = '" . (int)$category_id . "'";
			$subcategories_array = array();
			$this->get_subcategories($subcategories_array, $category_id);
		    for ($i=0, $n=sizeof($subcategories_array); $i<$n; $i++ ) {
		      $sql .= " OR p2c.category_id  = '" .  $subcategories_array[$i] ."'";
		    }		
			$sql  .= " ) ";
			
			$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	function get_subcategories(&$cat_array, $category_id){
			$data_subquery =$this->model_catalog_category->getCategories($category_id) ;
			foreach($data_subquery as $category_child){
				$cat_array[] = $category_child['category_id'];
				$this->get_subcategories($cat_array,$category_child['category_id']);
			}	
	}
}
 