<?php 
class ControllerAccountVendor extends Controller {
	private $error = array();
		
	public function index()
	{
		// print_r($this->request->get);exit;	
				// $this->data['heading_title'] = $category_info['name'];


		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$short_url = $this->request->get['short_url'];
		if(!trim($short_url))
		{
			$this->redirect($this->url->link('common/home'));
			exit;	
		}

		$detail = $this->db->query("SELECT * FROM inv_vendor_po where short_url='".$this->db->escape($short_url)."'");

		if($detail->row['short_url']!=$short_url)
		{
			echo 'The page you are trying to access is either expired or invalid';
			exit;	
		}

		$this->document->setTitle('Vendor PO # '.$detail->row['vendor_po_id']);


		$products = $this->db->query("SELECT * FROM inv_vendor_po_items WHERE vendor_po_id='".$detail->row['vendor_po_id']."' order by sku");
		$this->data['detail'] = $detail->row;
		$this->data['products'] = array();

		$i=0;
		foreach($products->rows as $product)
		{

			//$product_id = $this->model_catalog_product->getProductIDbySku($product['sku']);
			$result = $this->db->query("SELECT a.model,a.image,b.name FROM oc_product a, oc_product_description b where a.product_id=b.product_id AND a.model='".$product['sku']."'");



			$result = $result->row;
			if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = false;
				}


				$this->data['products'][$i] = array(
					'model'=>$result['model'],
					'image'=>$image,
					'name'=>$result['name'],
					'cost'=>$product['new_cost'],
					'qty'=>$product['req_qty']
					);
				$i++;
		}

			// print_r($this->data['products']);exit;
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/home_products.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/vendor_po.tpl';
			} else {
				$this->template = 'default/template/account/vendor_po.tpl';
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

    public function update()
    {
    	$vendor_po_id = $this->request->post['vendor_po_id'];
    	$new_costs = $this->request->post['new_cost'];
    	// print_r($new_costs);exit;
    	foreach($new_costs as $sku => $new_cost)
    	{
    		$this->db->query("update inv_vendor_po_items SET new_cost='".(float)$new_cost."' WHERE sku='".$sku."' and vendor_po_id='".$this->db->escape($vendor_po_id)."'");

    	}

    	$query = $this->db->query("UPDATE inv_vendor_po SET short_url='' WHERE vendor_po_id='".$this->db->escape($vendor_po_id)."'");

    
    	$json = array();
    	if($query)
    	{
    		$json['success'] = 1;
    	}
    	else
    	{
    		$json['error'] = 1;
    	}
    	echo json_encode($json);
    	exit;

    }
 }

?>