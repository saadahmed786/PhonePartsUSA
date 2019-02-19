<?php
class ControllerFeedGoogleBaseImproved extends Controller
{
    // Holds 
    private $dom;
    private $root;
    private $channel;

    /**
     * ControllerFeedGoogleSitemapImproved::index()
     * action for controller
     * 
     * @return void
     */
    public function index()
    {
        if ($this->config->get('google_base_improved_status')) {
            // Load all models
    		$this->load->model('tool/image');
            $this->load->model('tool/seo_url');
            $this->load->model('catalog/product');
            $this->load->model('catalog/category');
    
            // Create new dom docuemnt and root and assign them to the private vars
            $this->dom = new DOMDocument('1.0', 'UTF-8');
            $this->root = $this->_c('rss');
            $this->root->setAttribute('version', '2.0');
            $this->root->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
            $this->root = $this->dom->appendChild($this->root);
            $channel = $this->root->appendChild($this->_c('channel'));
            
            // Add basic channel info
            $channel->appendChild($this->_c('title', $this->config->get('config_name'), false));
            $channel->appendChild($this->_c('description', $this->config->get('config_meta_description'), false));
            $channel->appendChild($this->_c('link', HTTP_SERVER));
            
            // Load products
            $products = $this->model_catalog_product->getProducts();
            
            foreach($products as  $p) {
                
                $item = $this->_c('item');
                // Add basic title, link and description
                $item->appendChild($this->_c('title', $p['name'], false));
                $item->appendChild($this->_c('link', $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id='.$p['product_id']), false));
                $item->appendChild($this->_c('description', $p['description'], false));
                
                // Add brand, condition, id, mpn, quantity and weight
                $item->appendChild($this->_c('g:brand', $p['manufacturer'], false));
                $item->appendChild($this->_c('g:condition', 'new'));
                $item->appendChild($this->_c('g:id', $p['product_id']));
                $item->appendChild($this->_c('g:mpn', $p['model']));
                $item->appendChild($this->_c('g:quantity', $p['quantity']));
                $item->appendChild($this->_c('g:weight', $this->weight->format($p['weight'], $p['weight_class'])));
                
                // To add the UPC to the feed, uncomment this line
                #$item->appendChild($this->_c('g:upc', $p['model']));
                
                
                // Add product Image
                $image = $this->model_tool_image->resize(($p['image'] ? $p['image'] : 'no_image.jpg'), 500, 500);
                $item->appendChild($this->_c('g:image_link', $image));
                
                // Add price
                $special = $this->model_catalog_product->getProductSpecial($p['product_id']);
                $price = $this->tax->calculate(($special ? $special : $p['price']), $p['tax_class_id']);
                $item->appendChild($this->_c('g:price', $price));
                
                $categories = $this->model_catalog_product->getCategories($p['product_id']);
                
                foreach($categories as $c) {
    				$path = $this->getPath($c['category_id']);
    				
    				if ($path) {
    				    $cats = array();
    					
    					foreach (explode('_', $path) as $path_id) {
    						$category_info = $this->model_catalog_category->getCategory($path_id);
    						if ($category_info) $cats[] = $category_info['name'];
    					}
                        if(count($cats)) $item->appendChild($this->_c('g:product_type', implode(' > ', $cats), false));
    				}
                }
                
                // Append the item to the channel node
                $channel->appendChild($item);
            }
    
            $this->dom->formatOutput = true;
            $this->response->addHeader('Content-Type: application/xml');
            $this->response->setOutput($this->dom->saveXML());
        }else{
            die('This module needs to be enabled in the admin interface');
        }
    }
    
    /**
     * ControllerFeedGoogleSitemapImproved::getCategories()
     * Adds the list of categories to the sitemap recursively
     * 
     * @param integer $parent_id
     * @param string $current_path
     * @return void
     */
    protected function getCategories($parent_id, $current_path = '')
    {
        // Load categories
        $results = $this->model_catalog_category->getCategories($parent_id);

        // Loop through each category, increasing the path as the structure gets deeper
        foreach ($results as $result) {
            if (!$current_path) {
                $new_path = $result['category_id'];
            } else {
                $new_path = $current_path . '_' . $result['category_id'];
            }

            // add category url
            $this->addUrl(HTTP_SERVER . 'index.php?route=product/category&path=' . $new_path,
                '0.7');

            // Recursion to load new category level
            $this->getCategories($result['category_id'], $new_path);
        }
    }
    
    protected function _c($name, $value = NULL, $already_raw = true) {
        if($value === NULL) return $this->dom->createElement($name);
        
        if(!$already_raw) $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        return $this->dom->createElement($name, htmlentities($value));
    }
	
	protected function getPath($parent_id, $current_path = '') {
		$category_info = $this->model_catalog_category->getCategory($parent_id);
	
		if ($category_info) {
			if (!$current_path) {
				$new_path = $category_info['category_id'];
			} else {
				$new_path = $category_info['category_id'] . '_' . $current_path;
			}	
		
			$path = $this->getPath($category_info['parent_id'], $new_path);
					
			if ($path) {
				return $path;
			} else {
				return $new_path;
			}
		}
	}
}
 