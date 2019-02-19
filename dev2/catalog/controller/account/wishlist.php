<?php 
class ControllerAccountWishList extends Controller {
	public function index() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/wishlist', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	}    	
		
		$this->language->load('account/wishlist');
		
		$this->load->model('catalog/product');
		
		$this->load->model('tool/image');
		
		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}
		
		if (isset($this->request->get['remove'])) {
			$key = array_search($this->request->get['remove'], $this->session->data['wishlist']);
			
			if ($key !== false) {
				unset($this->session->data['wishlist'][$key]);
			}
		
			$this->session->data['success'] = $this->language->get('text_remove');
			if($this->session->data['temp_theme'] == 'ppusa2.0')
			{
				$this->redirect($this->url->link('account/account'));
			}
			else
			{
			 $this->redirect($this->url->link('account/wishlist'));
			}
		}
						
		$this->document->setTitle($this->language->get('heading_title'));	
      	
		$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(       	
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->data['breadcrumbs'][] = array(       	
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/wishlist'),
        	'separator' => $this->language->get('text_separator')
      	);
								
		$this->data['heading_title'] = $this->language->get('heading_title');	
		
		$this->data['text_empty'] = $this->language->get('text_empty');
     	
		$this->data['column_image'] = $this->language->get('column_image');
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_model'] = $this->language->get('column_model');
		$this->data['column_stock'] = $this->language->get('column_stock');
		$this->data['column_price'] = $this->language->get('column_price');
		$this->data['column_action'] = $this->language->get('column_action');
		
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
							
		$this->data['products'] = array();
		$this->data['lists'] = array();

		$lists = $this->model_catalog_product->getCustomerLists($data);
		if($lists->rows)
		{
			$this->data['lists'] = $lists->rows;
		}

		foreach ($this->session->data['wishlist'] as $key => $product_id) {
			$result = $this->model_catalog_product->getProductFromList($product_id);
			$lists_product = $result->rows;
			//If the product is not already added in any other list
			if(!$lists_product)
			{
				$product_info = $this->model_catalog_product->getProduct($product_id);
				
				if ($product_info) { 
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
					} else {
						$image = false;
					}

					if ($product_info['quantity'] <= 0) {
						$stock = $product_info['stock_status'];
					} elseif ($this->config->get('config_stock_display')) {
						$stock = $product_info['quantity'];
					} else {
						$stock = $this->language->get('text_instock');
					}
								
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$price = false;
					}
					
					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$special = false;
					}
																				
					$this->data['products'][] = array(
						'product_id' => $product_info['product_id'],
						'thumb'      => $image,
						'name'       => $product_info['name'],
						'model'      => $product_info['model'],
						'stock'      => $stock,
						'price'      => $price,		
						'special'    => $special,
						'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
						'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'])
					);
				} else {
					unset($this->session->data['wishlist'][$key]);
				}
			}
		}	

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/wishlist.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/wishlist.tpl';
		} else {
			$this->template = 'default/template/account/wishlist.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);
							
		$this->response->setOutput($this->render());		
	}
	
	public function add() {
		$this->language->load('account/wishlist');
		
		$json = array();

		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}
				
		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}
		
		$this->load->model('catalog/product');
		
		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		if ($product_info) {
			if (!in_array($this->request->post['product_id'], $this->session->data['wishlist'])) {	
				$this->session->data['wishlist'][] = $this->request->post['product_id'];
			}
			 
			if ($this->customer->isLogged()) {			
				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));				
			} else {
				$json['success'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));				
			}
			
			$json['total'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}	
		
		$this->response->setOutput(json_encode($json));
	}

	public function showListProducts()
	{
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$html = '';
  		$data = $_POST['list_id'];
  		$all_products = $this->session->data['wishlist'];
  		if($data)
  		{
  			$products = $this->model_catalog_product->getListProducts($data);
  			$all_products = $products->rows;
  		}
		foreach ($all_products as $key => $val) {
			if(isset($products))
			{
				$product_id = $val['product_id'];
				$lists_product = false;
			}
			else
			{
				$product_id = $val;
				$result = $this->model_catalog_product->getProductFromList($product_id);
				$lists_product = $result->rows;
			}
			
			$product_info = $this->model_catalog_product->getProduct($product_id);

			//If the product is not already added in any other list
			if(!$lists_product)
			{
				if ($product_info) { 
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
					} else {
						$image = false;
					}

					if ($product_info['quantity'] <= 0) {
						$stock = $product_info['stock_status'];
					} elseif ($this->config->get('config_stock_display')) {
						$stock = $product_info['quantity'];
					} else {
						$stock = $this->language->get('text_instock');
					}
								
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$price = false;
					}
					
					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$special = false;
					}
																				
					
				$html .= '
	            <div id="delete-item-pop" class="popup">
	              <div class="popup-head">
	                <h2 class="blue-title uppercase">Are you sure you want to Delete this Item?</h2>
	              </div>
	              <div class="popup-body">
	                <h5 class="text-center">
	                  '.$product_info['name'].'
	                </h5>
	                <div class="text-center popup-btns">

	                  <a href="'.$this->url->link('account/wishlist', 'remove=' . $product_info['product_id']).'" class="btn btn-primary" type="submit">Yes Delete Item from List</a> &nbsp;
	                  <button class="btn btn-primary red-btn" onclick="parent.$.fancybox.close();">No, I changed my mind</button>
	                </div>
	              </div>  
	            </div>
	                <div class="tab-inner pb30">
	                  
	                    <li>
	                      <div class="product-detail pr0">
	                        <div class="account-product">
	                          <div class="drager-icon">
	                            <img src="catalog/view/theme/ppusa2.0/images/icons/drager.png" alt="">
	                          </div>
	                        <div class="product-detail-inner clearfix">
	                          <div class="row">
	                            <div class="col-md-2 product-detail-img">
	                              <div class="image"><img src="'.$product['thumb'].'" alt="'.$product_info['name'].'"></div>
	                            </div>
	                            <div class="col-md-10 product-detail-text">
	                              <h3>'.$product_info['name'].'</h3>
	                              <div class="row">
	                                <div class="col-md-2">
	                                  <div class="review-area">
	                                    <ul class="review-stars clearfix">
	                                      <li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
	                                      <li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
	                                      <li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
	                                      <li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
	                                      <li><a href="#"><i class="fa fa-star"></i></a></li>
	                                    </ul>
	                                    <p><a href="#" class="review-links underline">40 reviews</a></p>
	                                  </div>
	                                </div>
	                                <div class="col-md-3">
	                                  <div class="account-btns">
	                                    <a href="#move-list-pop" class="fancybox btn btn-primary" onclick="addTolist("'.$product_info['product_id'].'")">move</a>
	                                    <input type="hidden" name="theme" value="2">
	                                    <a href="#delete-item-pop" class="fancybox btn btn-primary red-btn">Delete</a>
	                                  </div>
	                                </div>
	                                <div class="col-md-3">
	                                  <div class="text-center">
	                                    <span class="favorite"><i class="fa fa-heart"></i><a href="#" class="underline">Favorite</a></span>
	                                  </div>
	                                  <div class="cart-quality">
	                                    <table class="table">
	                                      <thead>
	                                        <tr>
	                                          <th>Quantity</th>
	                                          <th>Our Price</th>
	                                        </tr>
	                                      </thead>
	                                      <tbody>
	                                        <tr>
	                                          <td>1</td>
	                                          <td>$105.00</td>
	                                        </tr>
	                                        <tr>
	                                          <td>3-9</td>
	                                          <td>$100.00</td>
	                                        </tr>
	                                        <tr>
	                                          <td>10+</td>
	                                          <td>$95.00</td>
	                                        </tr>
	                                      </tbody>
	                                    </table>
	                                  </div>
	                                </div>
	                                <div class="col-md-4 cart-total-wrp">
	                                  <div class="cart-total text-right">
	                                    <div class="qtyt-box">
	                                      <div class="input-group spinner">
	                                        <span class="txt">QTY</span>
	                                          <input type="text" class="form-control qty" value="1">
	                                          <div class="input-group-btn-vertical">
	                                            <button class="btn " type="button"><i class="fa fa-plus"></i></button>
	                                            <button class="btn" type="button"><i class="fa fa-plus"></i></button>
	                                          </div>

	                                       </div>
	                                    </div>
	                                    <h3>';
	                                    if ($price) {
	                                    	$html .='
	                                      <div class="price">';
	                                        if (!$special) {
	                                          $html .= $product["price"];
	                                           } else {
	                                          $html .=  '<s>'.$price.'</s> <b>'.$special.'</b>';
	                                           } 
	                                          $html .= '</div>';
	                                           }
	                                           $html .= '</h3>
	                                    
	                               <button onclick="addToCartpp2("'.$product_info['product_id'].'", $(this).parent().find(".qty").val())" class="btn btn-success addtocart">Add to cart</button>
	                                  </div>
	                                </div>
	                              </div>
	                            </div>
	                          </div>
	                          <div class="features-row">
	                            <div class="row item-features">
	                              <ul>
	                                <li class="col-xs-6">
	                                  Phone model
	                                </li>
	                                <li class="col-xs-6">
	                                  '.$product_info['model'].'
	                                </li>
	                              </ul>
	                              <ul>
	                                <li class="col-xs-6">
	                                  Compatibility
	                                </li>
	                                <li class="col-xs-6">
	                                  Type
	                                </li>
	                              </ul>
	                              <ul>
	                                <li class="col-xs-6">
	                                  Information
	                                </li>
	                                <li class="col-xs-6">
	                                  '.$stock.'
	                                </li>
	                              </ul>
	                            </div>
	                          </div>
	                        </div>
	                      </div>
	                    </div>
	                  </li>
	                
	              </div>';
				} else {
					unset($this->session->data['wishlist'][$key]);
				}
			}
		}
		echo json_encode($html);
	}
			
		
}
?>