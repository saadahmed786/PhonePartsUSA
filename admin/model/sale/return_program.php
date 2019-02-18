<?php
class ModelSaleReturnProgram extends Model {
	public function getReturns()
	{
		$sql = "SELECT DISTINCT a.* FROM `".DB_PREFIX."return_program_mt` a INNER JOIN `".DB_PREFIX."return_program_dt` b WHERE a.return_id=b.return_id ORDER BY date_added DESC";
		
		$result = $this->db->query($sql);
		return $result->rows;	
		
	}
	
	public function getReturnItems($return_id)
	{
		$sql = "SELECT a.*,b.name,c.name as reason_name FROM `".DB_PREFIX."return_program_dt` a,".DB_PREFIX."product_description b,".DB_PREFIX."return_reason c WHERE a.product_id=b.product_id AND a.reason_id=c.return_reason_id AND return_id='".(int)$return_id."'";
		
		$result = $this->db->query($sql);
		
		return $result->rows;
	}
	
	public function addReturn($data)
	{
		
	$this->load->model('sale/order');
	
	$order_info = $this->model_sale_order->getOrder($data['order_id']);
	
	$sql="INSERT INTO ".DB_PREFIX."return_program_mt SET order_id='".(int)$data['order_id']."',resolution='".$this->db->escape($data['resolution_code'])."',date_added=NOW(),user_id='".(int)$this->user->getId()."'";
	$this->db->query($sql);
	$return_id = $this->db->getLastId();
	
	foreach($data['product_items'] as $key=> $product)
	{
		
		$tmp = explode("-",$product);
		$product_id=$tmp[0];
		$amount = $tmp[1];
		
	$sql="INSERT INTO ".DB_PREFIX."return_program_dt SET reason_id='".$data['reason'][$product_id]."', return_id='".(int)$return_id."',product_id='".(int)$product_id."',amount='".(float)$amount."'";
		
		$this->db->query($sql);
	}
	}
}
?>