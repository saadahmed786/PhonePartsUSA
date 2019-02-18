<?php
class ModelPosSubTotal extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		$this->language->load('total/sub_total');
		$this->load->model('sale/order');
		$sub_total = $this->cart->getSubTotal($this->request->get['order_idx']);
	 //print_r($this->cart->getProducts($this->request->get['order_idx']));exit;
		/*$sub_total = 0;

		foreach($this->cart->getProducts($this->request->get['order_idx']) as $product)
		{
			$product_det = $this->model_sale_order->getOrderProduct($this->request->get['order_idx'],$product['product_id']);
				
				$price =   ($product_det['price']*$product['quantity']);
				$sub_total+=$price;
		}*/
		
		if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$sub_total += $voucher['amount'];
			}
		}
		
		$total_data[] = array( 
			'code'       => 'sub_total',
			'title'      => 'Sub total',
			'text'       => $this->currency->format($sub_total),
			'value'      => $sub_total,
			'sort_order' => $this->config->get('sub_total_sort_order')
		);
		
		$total += $sub_total;
		
	}
	
	public function getTotal2(&$total_data, &$total, &$taxes) {
		$this->language->load('total/sub_total');
		$this->load->model('sale/order');
		$sub_total = $this->cart->getSubTotal2();
	 //print_r($this->cart->getProducts($this->request->get['order_idx']));exit;
		/*$sub_total = 0;

		foreach($this->cart->getProducts($this->request->get['order_idx']) as $product)
		{
			$product_det = $this->model_sale_order->getOrderProduct($this->request->get['order_idx'],$product['product_id']);
				
				$price =   ($product_det['price']*$product['quantity']);
				$sub_total+=$price;
		}*/
		
		if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$sub_total += $voucher['amount'];
			}
		}
		
		$total_data[] = array( 
			'code'       => 'sub_total',
			'title'      => 'Sub total',
			'text'       => $this->currency->format($sub_total),
			'value'      => $sub_total,
			'sort_order' => $this->config->get('sub_total_sort_order')
		);
		
		$total += $sub_total;
		
	}
}
?>