<?php
class ModelSaleRma extends Model {
	
	public function getReasons()
	{
		$query = $this->db->query("SELECT * FROM inv_reasons ORDER BY id");	
		return $query->rows;
	}
	
	public function isRMAGenerated($order_id,$sku)
	{
		$query = $this->db->query("SELECT
			a.rma_number
			FROM
			`inv_returns` a
			INNER JOIN `inv_return_items` b
			ON (a.`id` = b.`return_id`)
			WHERE a.`order_id`='".$order_id."' AND b.sku='".$this->db->escape($sku)."'");
		
		
		$row = $query->row;
		return $row['rma_number'];
		
	}
	
	public function addReturnMain($data)
	{
		$rma_number = $this->getRMANumber('web');
		$this->db->query("INSERT INTO inv_returns SET rma_number='".$this->db->escape($rma_number)."',email='".$this->db->escape($data['email'])."',order_id='".$data['order_id']."',store_type='".$this->db->escape($data['store_type'])."',rma_status='".$this->db->escape($data['rma_status'])."',date_added=NOW(),source='storefront'");

		return	$this->db->getLastId();
		
		
	}
	
	public function addReturnDetail($data)
	{
		
		$this->db->query("INSERT INTO inv_return_items SET return_id='".(int)$data['return_id']."',sku='".$data['sku']."',title='".$this->db->escape($data['product_title'])."',quantity='".(int)$data['quantity']."',price='".(float)$data['price']."',return_code='".$this->db->escape($data['reason'])."',how_to_process='".$this->db->escape($data['process'])."',item_condition='".$this->db->escape($data['item_condition'])."',item_issue='".$this->db->escape($data['item_issue'])."',decision='".$this->db->escape($data['decision'])."'");
		
	}
	
	public function getRMANumber($store_type='web'){
	//global $db;


		if($store_type == 'bigcommerce'){
			$prefix = "RL";
		}
		elseif($store_type == 'bonanza'){
			$prefix = "BO";
		}
		elseif($store_type == 'web'){
			$prefix = "PP";
		}
		elseif($store_type == 'channel_advisor'){
			$prefix = "MM";
		}
		elseif($store_type == 'wish'){
			$prefix = "WL";
		}
		elseif($store_type == 'amazon'){
			$prefix = "AM";
		}
		else{
			$prefix = "PP";
		}

		$last_number = $this->db->query("select max(replace(rma_number,'$prefix','')) as rma_number from inv_returns where rma_number LIKE '%$prefix%'");

		$last_number = $last_number->row;
		$last_number = $last_number['rma_number'];



		if($last_number >= 999 && $last_number < 9999){
			$rma_number = $prefix."0".($last_number+1);
		}
		elseif($last_number >= 99 && $last_number < 999){
			$rma_number = $prefix."00".($last_number+1);
		}
		elseif($last_number >= 9){
			$rma_number = $prefix."000".($last_number+1);
		}
		elseif($last_number < 9){
			$rma_number = $prefix."0000".($last_number+1);
		}
		else{
			$rma_number = $prefix."".($last_number+1);
		}

		return $rma_number;
	}	
	public function getRMA($return_id)
	{
		$query = $this->db->query("SELECT rma_number FROM inv_returns WHERE id='".(int)$return_id."'");	
		$row = $query->row;
		return $row['rma_number'];

	}
	
}
?>