<?php
class Inventory extends Database  {

	public function getProcessedOrders($limit=70)
	{
		// $query = "select * from inv_users";
		$rows = $this->func_query("SELECT a.order_id FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status) in ('processed','unshipped') and a.is_picked=0 order by a.order_date desc limit $limit");
		return $rows;
	}

	public function getOnHoldOrders($limit=70)
	{
		// $query = "select * from inv_users";
		$rows = $this->func_query("SELECT a.order_id FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status) in ('on hold') and a.is_picked=0 order by a.order_date desc limit $limit");
		return $rows;
	}

	public function getPickedOrders($limit=100)
	{
		// $query = "select * from inv_users";
		$rows = $this->func_query("SELECT order_id FROM inv_orders WHERE lower(order_status) in ('processed','unshipped') and is_picked=1 and is_packed=0 order by order_date desc limit $limit");
		return $rows;
	}
	public function getOrder($order_id)
	{
		$query = "SELECT * FROM inv_orders a, inv_orders_details b where a.order_id=b.order_id and lower(a.order_status) in ('unshipped','processed','on hold')  and concat(a.prefix,a.order_id)='".$this->func_escape_string($order_id)."'";
		// echo $query;exit;
		$row = $this->func_query_first($query);
		if($row)
		{		
		$query = "SELECT * FROM inv_orders_items WHERE order_id='".$row['order_id']."'";
		$items_rows = $this->func_query($query);
		
		$items = array();
		foreach($items_rows as $i => $item)
		{
			if($item['product_sku']=='SIGN') continue;
			$product_detail = $this->getProduct($item['product_sku']);
			// $items['id'] = $item['id'];
			$items[$i]['sku'] = $item['product_sku'];
			$items[$i]['quantity'] = $item['product_qty'];
			$items[$i]['picked_quantity'] = $item['picked_quantity'];
			$items[$i]['packed_quantity'] = $item['packed_quantity'];
			$items[$i]['name'] = utf8_encode($product_detail['name']);

		}

		// $array = array();
		$array = array(
			'order_id' => $row['order_id'],
			'customer_name' => $row['first_name'].' '.$row['last_name'],
			'address1' => $row['address1'],
			'city' => $row['city'],
			'state' => $row['state'],
			'zip' => $row['zip'],
			'shipping_method' => $row['shipping_method'],
			'other_shipping_method' => $row['other_shipping_method'],
			'payment_method' => $row['payment_method'],
			'items'=>$items

			) ;
	}
	else
	{
		$array = false;
	}
		// print_r($array);exit;
		return $array;
		
	}
	public function getProduct($sku)
	{
		$query = "SELECT a.*,b.name FROM oc_product a,oc_product_description b where a.product_id=b.product_id and a.model='".$this->func_escape_string($sku)."'";

		return $this->func_query_first($query);
	}

	public function markPicked($order_id,$skus)
	{
		$this->db_exec("START TRANSACTION");
		foreach($skus as $sku => $quantity)
		{
			$picked_quantity = $this->func_query_first_cell("select picked_quantity from inv_orders_items  where order_id='".$this->func_escape_string($order_id)."' and product_sku='".$this->func_escape_string($sku)."'");
			
					
			$_query = $this->db_exec("UPDATE oc_product SET not_picked=not_picked+".(int)$picked_quantity.",picked=picked-".(int)$picked_quantity." WHERE trim(lower(model))='".trim(strtolower($sku))."'");
				

			$query1=$this->db_exec("UPDATE inv_orders_items SET picked_quantity='".(int)$quantity."', is_picked=1 where order_id='".$this->func_escape_string($order_id)."' and product_sku='".$this->func_escape_string($sku)."'");


			$query2 = $this->db_exec("UPDATE oc_product SET not_picked=not_picked-".(int)$quantity.",picked=picked+".(int)$quantity." WHERE trim(lower(model))='".trim(strtolower($sku))."'");
		}

		$query = $this->db_exec("UPDATE inv_orders SET is_picked=1 where order_id='".$this->func_escape_string($order_id)."'");

		if($query1 && $query && $query2)
		{
			$this->db_exec("COMMIT");
			return true;
		}
		else
		{
			$this->db_exec("ROLLBACK");	
			return false;
		}
		
	}

	public function savePicked($order_id,$skus)
	{
		$this->db_exec("START TRANSACTION");
		foreach($skus as $sku => $quantity)
		{
			$picked_quantity = $this->func_query_first_cell("select picked_quantity from inv_orders_items  where order_id='".$this->func_escape_string($order_id)."' and product_sku='".$this->func_escape_string($sku)."'");
			

			$_query = $this->db_exec("UPDATE oc_product SET not_picked=not_picked+".(int)$picked_quantity.",picked=picked-".(int)$picked_quantity." WHERE trim(lower(model))='".trim(strtolower($sku))."'");
				

			$query1=$this->db_exec("UPDATE inv_orders_items SET picked_quantity='".(int)$quantity."', is_picked=1 where order_id='".$this->func_escape_string($order_id)."' and product_sku='".$this->func_escape_string($sku)."'");


			$query2 = $this->db_exec("UPDATE oc_product SET not_picked=not_picked-".(int)$quantity.",picked=picked+".(int)$quantity." WHERE trim(lower(model))='".trim(strtolower($sku))."'");



		$not_picked = $this->func_query_first_cell("SELECT sum(b.product_qty) - sum(b.picked_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','on hold')  and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
		$picked = $this->func_query_first_cell("SELECT sum(b.picked_quantity) - sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','on hold') and b.is_picked=1 and a.is_packed=0 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
		$packed = $this->func_query_first_cell("SELECT sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped') and b.is_picked=1 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");

		$this->db_exec("UPDATE inv_product_ledger SET not_picked='".(int)$not_picked."',picked='".(int)$picked."',packed='".(int)$packed."' WHERE order_id='".$this->func_escape_string($order_id)."' ORDER BY id DESC LIMIT 1");

		}


		//$query = $this->db_exec("UPDATE inv_orders SET is_picked=1 where order_id='".$this->func_escape_string($order_id)."'");

		if($query1 && $query2)
		{
			$this->db_exec("COMMIT");
			return true;
		}
		else
		{
			$this->db_exec("ROLLBACK");	
			return false;
		}
		
	}

	public function markPacked($order_id,$skus)
	{
		$this->db_exec("START TRANSACTION");
		foreach($skus as $sku => $quantity)
		{
			// echo $quantity;exit;

			$packed_quantity = $this->func_query_first_cell("select packed_quantity from inv_orders_items  where order_id='".$this->func_escape_string($order_id)."' and product_sku='".$this->func_escape_string($sku)."'");
			
					
			$_query = $this->db_exec("UPDATE oc_product SET picked=picked+".(int)$packed_quantity.",packed=packed-".(int)$packed_quantity." WHERE trim(lower(model))='".trim(strtolower($sku))."'");

			$query1=$this->db_exec("UPDATE inv_orders_items SET packed_quantity='".(int)$quantity."', is_packed=1 where order_id='".$this->func_escape_string($order_id)."' and product_sku='".$this->func_escape_string($sku)."'");

			$query2 = $this->db_exec("UPDATE oc_product SET picked=picked-".(int)$quantity.",packed=packed+".(int)$quantity." WHERE trim(lower(model))='".trim(strtolower($sku))."'");
		}

		$query = $this->db_exec("UPDATE inv_orders SET is_packed=1 where order_id='".$this->func_escape_string($order_id)."'");

		if($query1 && $query && $query2)
		{
			$this->db_exec("COMMIT");
			return true;
		}
		else
		{
			$this->db_exec("ROLLBACK");	
			return false;
		}
		
	}

	public function savePacked($order_id,$skus)
	{
		$this->db_exec("START TRANSACTION");
		foreach($skus as $sku => $quantity)
		{
			$packed_quantity = $this->func_query_first_cell("select packed_quantity from inv_orders_items  where order_id='".$this->func_escape_string($order_id)."' and product_sku='".$this->func_escape_string($sku)."'");
			
					
			$_query = $this->db_exec("UPDATE oc_product SET picked=picked+".(int)$packed_quantity.",packed=packed-".(int)$packed_quantity." WHERE trim(lower(model))='".trim(strtolower($sku))."'");
				 
			$query1=$this->db_exec("UPDATE inv_orders_items SET packed_quantity='".(int)$quantity."', is_packed=1 where order_id='".$this->func_escape_string($order_id)."' and product_sku='".$this->func_escape_string($sku)."'");

			$query2 = $this->db_exec("UPDATE oc_product SET picked=picked-".(int)$quantity.",packed=packed+".(int)$quantity." WHERE trim(lower(model))='".trim(strtolower($sku))."'");
		}

		// $query = $this->db_exec("UPDATE inv_orders SET is_packed=1 where order_id='".$this->func_escape_string($order_id)."'");

		if($query1  && $query2)
		{
			$this->db_exec("COMMIT");
			return true;
		}
		else
		{
			$this->db_exec("ROLLBACK");	
			return false;
		}
		
	}

	public function makeLedger($order_id,$skus,$user_id=0,$action='not_picked')
	{
		
		$this->db_exec("START TRANSACTION");
		foreach($skus as $sku => $quantity)
		{		

					$on_hand = $this->func_query_first_cell("select quantity FROM oc_product WHERE trim(lower(model))='".strtolower(trim($sku))."' ");
					$reserved = $this->func_query_first_cell("SELECT sum(b.product_qty)  FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status)='on hold'  and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
					$not_picked = $this->func_query_first_cell("SELECT sum(b.product_qty) - sum(b.picked_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','on hold')  and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
					$picked = $this->func_query_first_cell("SELECT sum(b.picked_quantity) - sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','on hold') and b.is_picked=1 and a.is_packed=0 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
					$packed = $this->func_query_first_cell("SELECT sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped') and b.is_picked=1 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");

					

			$description = $this->ledgerDescription($action);

			$query=$this->db_exec("INSERT INTO inv_product_ledger SET order_id='".$this->func_escape_string($order_id)."',sku='".$this->func_escape_string($sku)."',quantity='".(int)$quantity."',action='".$action."',description='".$this->func_escape_string($description)."',user_id='".(int)$user_id."',date_added='".date('Y-m-d H:i:s')."',on_hold='".(int)$reserved."',not_picked='".(int)$not_picked."',picked='".(int)$picked."',packed='".(int)$packed."',on_hand='".(int)$on_hand."'");


		}

		if($query)
		{
			$this->db_exec("COMMIT");
			return true;
		}
		else
		{
			$this->db_exec("ROLLBACK");	
			return false;
		}

	}

	public function ledgerDescription($action)
	{
		switch ($action) {
				case 'reserved':
				$description = 'Quantity has been Resevered (On Hold).';
				break;
				case 'not_picked':
				$description = 'Marked as Not Picked.';
				break;
				case 'reserved_not_picked':
				$description = 'Reserved &rarr; Picked.';
				break;
				case 'not_picked_reserved':
				$description = 'Not Picked &rarr; Reserved.';
				break;
				case 'picked':
				$description = 'Marked as Picked.';
				break;
				case 'packed':
				$description = 'Marked as Packed.';
				break;
				case 'shipped':
				$description = 'Order has been Shipped.';
				break;
				case 'rollback':
				$description = 'Quantity has rolled back.';
				break;
				case 'adjustment':
				$description = 'Stock Adjustment has been made.';
				break;
				default:
				
				break;

		}
		return $description;
	}
}


$inventory = new Inventory;
?>