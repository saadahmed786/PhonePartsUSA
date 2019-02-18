<?php  
class ControllerCustomGeneral extends Controller {

	// const ALL_PRODUCTS_V2 = true;

	public function get_image() {
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$product_id = $this->request->post['product_id'];
		// echo $product_id;
		$product_info = $this->model_catalog_product->getProduct($product_id);
		// print_r($product_info);exit;
		$json = array();
		
		if ($product_info['image']) {
				$image = $this->model_tool_image->resize($product_info['image'], 278, 330);
			} else {
				$image = $this->model_tool_image->resize('data/image-coming-soon.jpg', 278, 330);
			}
			$json['success']=$image;
		
		echo json_encode($json);
	}
}
?>