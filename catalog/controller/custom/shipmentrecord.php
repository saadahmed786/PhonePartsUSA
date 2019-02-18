<?php

class ControllerCustomShipmentrecord extends Controller {
	public function index() {
            $this->document->setTitle('Private Form (Shipment Details)');
           $this->load->model('catalog/product');
		   			 
			$this->load->model('tool/image');
			
      		$this->data['products'] = array();
			
			//$products = $this->model_catalog_product->getOutStockProducts();
			$product_list = array();
			
			foreach($products as $product)
			{
				
				if ($product['image']) {
					//$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
					$image='';
				} else {
					$image = '';
				}
				$average_total = $this->model_catalog_product->getAverageSale($product['product_id'],30);
				$product_list[] = array(
				
				'product_id' => $product['product_id'],
				'name'		=> $product['name'],
				'model'		=>	$product['model'],
				'sku'		=>	$product['sku'],
				'quantity'	=>	$product['quantity'],
				'image'		=>	$image,
				'price'		=>	$product['price'],
				'average_sale'	=> $average_total['average_total']
				);
				
			}
			
				//$this->data['products'] = $product_list;
				
				if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/custom/shipmentrecord.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/custom/shipmentrecord.tpl';
			} else {
				
				$this->template = 'default/template/custom/shipmentrecord.tpl';
			}
			
			$this->children = array(
			/*	'common/column_left',
				'common/column_right',
				'common/content_bottom',
				'common/content_top',*/
				'common/footer',
				'common/header'	
			);
						
			$this->response->setOutput($this->render());					
				
			
	}
}
?>