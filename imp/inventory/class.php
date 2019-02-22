<?php
class Inventory extends Database  {
	private $shipengine_api_key='bgAGrx/lhRabpio+NZ7mVIJZSUDvCMTy2stwALBlsAQ';
	private $shipengine_url ='https://api.shipengine.com';
	private function generic_query()
	{
		return "SELECT a.*,b.*,c.tracking_number as my_tracking_number,c.combined_orders FROM inv_orders a INNER JOIN inv_orders_details b ON  (a.order_id=b.order_id) LEFT JOIN inv_label_data c ON(a.order_id=c.order_id) where 1=1  and a.order_id<>'' and";
	}
	public function getProcessedOrders($limit=40)
	{
		// $query = "select * from inv_users";
		$rows = $this->func_query("SELECT a.order_id FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status) in ('processed','unshipped') and a.is_picked=0 order by a.order_date desc limit $limit");
		return $rows;
	}

	public function getPackedOrders($filter,$limit=40)
	{
		// $query = "select * from inv_users";
		$query = $this->generic_query()."  lower(a.order_status) in ('processed','unshipped','on hold') ";
		if($filter!='')
		{
			$query.=" and (a.email like '%".$this->func_escape_string($filter)."%' or concat(b.first_name,' ',b.last_name) like '%".$this->func_escape_string($filter)."%' or a.order_id like '%".$this->func_escape_string($filter)."%') ";
		}

		$query.=" and a.is_picked=1 and a.is_packed=1 and a.is_adjusted=0 group by a.order_id order by a.order_date desc limit $limit";

		$rows = $this->func_query($query);
		$array = array();
		foreach($rows as $row)
		{
			$__temp = $this->getOrder($row['order_id'],false,$row);
			
			if($__temp['shipping_method']=='Local Las Vegas Store Pickup - 9:30am-4:30pm')
			{
				continue;
			}
			$array[] = $__temp;

		}
		return $array;
	}
	public function getShippedOrders($filter='',$date='',$carrier='',$limit=40)
	{
		
		// $rows = $this->func_query("SELECT a.order_id FROM inv_orders a WHERE  lower(a.order_status) in ('shipped') and a.is_picked=1 and a.is_packed=1 and a.is_adjusted=0 order by a.order_date desc limit $limit");
		$query = $this->generic_query()." lower(a.order_status) in ('shipped') ";
		if($filter!='')
		{
			$query.=" and (a.email like '%".$this->func_escape_string($filter)."%' or concat(b.first_name,' ',b.last_name) like '%".$this->func_escape_string($filter)."%' or a.order_id like '%".$this->func_escape_string($filter)."%' or c.tracking_number like '%".$this->func_escape_string($filter)."%' ) ";
		}
		if($date!='')
		{
			$query.=" AND date(c.created_at)='".date('Y-m-d',strtotime($date))."' ";
		}

		if($carrier!='')
		{
			$query.=" AND c.service_code='".$carrier."'";
		}
		
		// $query.=" and a.is_picked=1 and a.is_packed=1 and a.is_adjusted=0 group by a.order_id";
		$query.=" and a.is_picked=1 and a.is_packed=1 and c.voided=0  group by c.tracking_number";
		
		$query.=" order by a.order_date desc limit $limit";
		// echo $query;exit;
		$rows = $this->func_query($query);
		$array = array();
		foreach($rows as $row)
		{
			$array[] = $this->getOrder($row['order_id'],false,$row);
		}
		return $array;
	}

	public function getAdjustmentOrders($filter='',$limit=40)
	{
		$query = $this->generic_query()."  lower(a.order_status) in ('processed','unshipped','shipped')";
		if($filter!='')
		{
			$query.=" and (a.email like '%".$this->func_escape_string($filter)."%' or concat(b.first_name,' ',b.last_name) like '%".$this->func_escape_string($filter)."%' or a.order_id like '%".$this->func_escape_string($filter)."%') ";
		}
		$query.=" and a.is_picked=1 and a.is_adjusted=1 order by a.order_date desc limit $limit";
			// echo $query;exit;
		$rows = $this->func_query($query);
		$array = array();
		foreach($rows as $row)
		{
			$data = $this->getOrder($row['order_id'],true,$row); // get the removed items
			if($data['items'])
			{
				$array[] = $data;
			}
		}
		return $array;
	}


	public function getOnHoldOrders($limit=40)
	{
		// $query = "select * from inv_users";
		$rows = $this->func_query("SELECT a.order_id FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status) in ('on hold') and a.is_picked=0 order by a.order_date desc limit $limit");
		return $rows;
	}
	public function getToBePickedOrders($filter='',$limit = 25)
	{
		$setting  = $this->oc_config('imp_inventory_setting');
		$query = $this->generic_query()." lower(a.order_status) in ('processed','unshipped','on hold')";
		if($filter!='')
		{
			$query.=" and (a.email like '%".$this->func_escape_string($filter)."%' or concat(b.first_name,' ',b.last_name) like '%".$this->func_escape_string($filter)."%' or a.order_id like '%".$this->func_escape_string($filter)."%') ";
		}
		$query.=" and a.is_picked=0 order by a.order_date desc limit $limit";
		// echo $query;exit;
		$rows = $this->func_query($query);	
		// $rows = $this->func_query("SELECT a.order_id FROM inv_orders a WHERE  lower(a.order_status) in ('processed','unshipped','on hold') and a.is_picked=0 order by a.order_date desc limit $limit");	
		$data = array();

		// print_r($rows);exit;
		foreach($rows as $row)
		{
			
			$query = "SELECT * FROM inv_orders_items WHERE order_id='".$row['order_id']."' and product_sku<>'SIGN'";
			$items_rows = array();
			if($setting['picking_sku_col'])
			{
				$items_rows = $this->func_query($query);
			}

		// $items_row = array();
			$items = array();
			$product_total = 0.00;
			$all_quantity = 0;
			foreach($items_rows as $i => $item)
			{
				if($item['product_sku']=='SIGN')
				{
					continue;
				}

				$product_detail = $this->getProduct($item['product_sku']);
			// $items['id'] = $item['id'];
				$items[$i]['sku'] = $item['product_sku'];
				$items[$i]['quantity'] = $item['product_qty'];
				$items[$i]['picked_quantity'] = $item['picked_quantity'];
				$items[$i]['packed_quantity'] = $item['packed_quantity'];
				$items[$i]['name'] = utf8_encode($product_detail['name']);
				$items[$i]['image'] = $this->getImage($item['product_sku']);
				$items[$i]['product_total'] = '$'.number_format($item['product_price'],2);
				$items[$i]['product_unit'] = '$'.number_format($item['product_unit'],2);

				$product_total+=$item['product_price'];
				$all_quantity+=$item['product_qty'];
			}
			if(!$setting['picking_sku_col'] && $setting['picking_qty_col'])
			{
				$all_quantity = $this->func_query_first_cell("SELECT SUM(product_qty) FROM inv_orders_items where order_id='".$row['order_id']."'");
			}
			if($row['shipping_method']=='Local Las Vegas Store Pickup - 9:30am-4:30pm')
			{
				$row['shipping_method'] = 'Local Pickup';
			}
		// print_r($row);exit;
		// $array = array();
			$array = array(
				'order_id' => $row['order_id'],
				'order_date' => date('m/d/Y h:i A',strtotime($row['order_date'])),
				'customer_name' => utf8_encode($row['first_name'].' '.$row['last_name']),
				'all_quantity' => (int)$all_quantity,
				'shipping_name' => utf8_encode($row['shipping_firstname'].' '.$row['shipping_lastname']),
				'address1' => utf8_encode($row['address1']),
				'telephone' => utf8_encode($row['phone_number']),
				'email' => utf8_encode($row['email']),
				'city' => utf8_encode($row['city']),
				'state' => $row['state'],
				'zip' => utf8_encode($row['zip']),
				'country' => $row['country'],
				'shipping_method' => $row['shipping_method'],
				'order_status' => $row['order_status'],
			// 'other_shipping_method' => $row['other_shipping_method'],
				'payment_method' => $row['payment_method'],
				'product_total' => '$'.number_format($product_total,2),
				'shipping_cost' => '$'.number_format($row['shipping_cost'],2),
				'order_price' => '$'.number_format($row['order_price'],2),
				'paid_price' => '$'.number_format($row['paid_price'],2),
				'items'=>$items

				) ;
			$data[] = $array;
			
		}
		return $data;
	}

	public function getPickedOrders($local_orders=0,$limit=40,$filter)
	{
		// $query = "select * from inv_users";
		// echo $this->generic_query()." lower(a.order_status) in ('processed','unshipped') and a.is_picked=1 and a.is_packed=0 ".($local_orders==0?" and b.shipping_method<>'Local Las Vegas Store Pickup - 9:30am-4:30pm'":" and b.shipping_method='Local Las Vegas Store Pickup - 9:30am-4:30pm'")." order by a.order_date desc limit $limit";exit;
		$query = $this->generic_query()." lower(a.order_status) in ('processed','unshipped')";
		if($filter!='')
		{
			$query.=" and (a.email like '%".$this->func_escape_string($filter)."%' or concat(b.first_name,' ',b.last_name) like '%".$this->func_escape_string($filter)."%' or a.order_id like '%".$this->func_escape_string($filter)."%') ";
		}
		$query.=" and a.is_picked=1 and a.is_packed=0 ".($local_orders==0?" and b.shipping_method<>'Local Las Vegas Store Pickup - 9:30am-4:30pm'":" and b.shipping_method='Local Las Vegas Store Pickup - 9:30am-4:30pm'")." order by a.order_date desc limit $limit";
		$rows = $this->func_query($query);
		$data = array();
		foreach($rows as $row)
		{
			$__temp = $this->getOrder($row['order_id'],false,$row);
			// if($local_orders==0)
			// {
			// 	if($__temp['shipping_method']=='Local Las Vegas Store Pickup - 9:30am-4:30pm')
			// 	{
			// 		continue;
			// 	}
			// }
			// else
			// {
			// 	if($__temp['shipping_method']!='Local Las Vegas Store Pickup - 9:30am-4:30pm')
			// 	{
			// 		continue;
			// 	}
			// }
			$data[] = $__temp;
		}
		return $data;
	}
	public function getOrder($order_id,$get_removed_items=false,$row=array(),$show_items = true)
	{
		if(empty($row))
		{


			$query = $this->generic_query()." lower(a.order_status) in ('unshipped','processed','on hold','shipped')  and a.order_id='".$this->func_escape_string($order_id)."'";

			$row = $this->func_query_first($query);
		}
		if($row)
		{		

			if($get_removed_items==true)
			{
				$query ="SELECT item_sku AS product_sku,0.00 as product_price,0.00 as product_unit,0 as picked_quantity,0 as packed_quantity FROM inv_removed_order_items WHERE order_id='".$row['order_id']."' AND reason not in ('Out of Stock','Close Short') AND item_sku<>'SIGN'";
			}
			else
			{

				$query = "SELECT * FROM inv_orders_items WHERE order_id='".$row['order_id']."' AND product_sku<>'SIGN'";
			}

			$items_rows = array();
			if($show_items==true)
			{
				$items_rows = $this->func_query($query);

			}
			if($order_id=='562343')
			{
				// print_r($items_rows);exit;
			}

			$items = array();
			$product_total = 0.00;
			$all_quantity = 0;
			$has_signature = false;
			foreach($items_rows as $i => $item)
			{
			// for removed items schema
				if($get_removed_items)
				{
					$_xx = explode("*",$item['product_sku']);

					$item['product_sku'] = trim($_xx[0]);
					$item['product_qty'] = (int)trim($_xx[1]);

				}

				if($item['product_sku']=='SIGN')
				{
					$has_signature = true;
					continue;
				}

				$product_detail = $this->getProduct($item['product_sku']);
			// $items['id'] = $item['id'];
				$items[$i]['sku'] = $item['product_sku'];
				$items[$i]['quantity'] = $item['product_qty'];
				$items[$i]['picked_quantity'] = $item['picked_quantity'];
				$items[$i]['packed_quantity'] = $item['packed_quantity'];
				$items[$i]['name'] = utf8_encode($product_detail['name']);
				$items[$i]['image'] = $this->getImage($item['product_sku']);
				$items[$i]['product_total'] = '$'.number_format($item['product_price'],2);
				$items[$i]['product_unit'] = '$'.number_format($item['product_unit'],2);

				$product_total+=$item['product_price'];
				$all_quantity+=$item['product_qty'];
			}


			$state_short = '';
			if($row['zone_id'])
			{
				$state_short = $this->func_query_first_cell("SELECT code from oc_zone WHERE zone_id='".(int)$row['zone_id']."'");
			}

			if($row['country_id'])
			{
				$country_short = $this->func_query_first_cell("SELECT iso_code_2 from oc_country WHERE country_id='".(int)$row['country_id']."'");
			}
			$comment = $this->func_query_first_cell("SELECT comment FROM oc_order WHERE cast(`order_id` as char(50)) ='".$order_id."'");
			if(!$comment) $comment = '-';
			if($row['shipping_method']=='Local Las Vegas Store Pickup - 9:30am-4:30pm')
			{
				$row['shipping_method'] = 'Local Pickup';
			}
		// $array = array();
			$array = array(
				'order_id' => $row['order_id'],
				'order_date' => date('m/d/Y h:i A',strtotime($row['order_date'])),
				'customer_name' => utf8_encode($row['first_name'].' '.$row['last_name']),
				'all_quantity' => (int)$all_quantity,
				'shipping_name' => utf8_encode($row['shipping_firstname'].' '.$row['shipping_lastname']),
				'address1' => utf8_encode($row['address1']),
				'address2' => utf8_encode($row['address2']),
				'telephone' => $row['phone_number'],
				'company' => utf8_encode($row['company']),
				'email' => utf8_encode($row['email']),
				'city' => $row['city'],
				'state' => $row['state'],
				'state_short' => $state_short,
				'zip' => $row['zip'],
				'country' => $row['country'],
				'country_code' => $country_short,
				'shipping_method' => $row['shipping_method'],
				'order_status' => $row['order_status'],
				'tracking_number' => $row['my_tracking_number'],
				'combined_orders' => $row['combined_orders'],
				'has_signature' => $has_signature,
				'payment_method' => $row['payment_method'],
				'product_total' => '$'.number_format($product_total,2),
				'shipping_cost' => '$'.number_format($row['shipping_cost'],2),
				'order_price' => '$'.number_format($row['order_price'],2),
				'paid_price' => '$'.number_format($row['paid_price'],2),
				'items'=>$items,
				'comment'=> utf8_encode($comment)

				) ;
		}
		else
		{
			$array = false;
		}
		// print_r($array);exit;
		return $array;
		
	}
	public function getOrders($order_ids)
	{
		$query = $this->generic_query()." lower(a.order_status) in ('unshipped','processed','on hold','shipped')  and a.order_id in ("."'" . implode ( "', '", $order_ids ) . "'".") ORDER BY a.order_date ASC";
		

		$rows = $this->func_query($query);
		$array = array();
		foreach($rows as $row)
		{
			$array[] = $this->getOrder($row['order_id'],false,$row);
		}

		return array('data'=>$array);
	}
	public function getProduct($sku)
	{
		$query = "SELECT a.*,b.name FROM oc_product a,oc_product_description b where a.product_id=b.product_id and a.model='".$this->func_escape_string($sku)."'";

		return $this->func_query_first($query);
	}
	public function markShipped($order_ids)
	{
		$order_ids = explode(",", $order_ids);
		
		foreach($order_ids as $order_id)
		{


			$this->db_exec("UPDATE inv_orders SET order_status='Shipped',ship_date='".date('Y-m-d H:i:s')."' where order_id='".$order_id."'");
			$this->db_exec("UPDATE oc_order SET order_status_id='3' WHERE cast(`order_id` as char(50))='".$order_id."' OR ref_order_id='".$order_id."'");
		}

	}

	public function markProcessed($order_id)
	{
		$this->db_exec("UPDATE inv_orders SET order_status='Processed',is_picked=1,is_packed=1 where order_id='".$order_id."'");
		$this->db_exec("UPDATE inv_orders_items SET ostatus='processed',opicked=1,opacked=1 where order_id='".$order_id."'");
		$this->db_exec("UPDATE oc_order SET order_status_id='15' WHERE cast(`order_id` as char(50))='".$order_id."' OR ref_order_id='".$order_id."'");

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
		$_check =$this->func_query_first_cell("SELECT id from inv_orders_items WHERE picked_quantity<>product_qty AND order_id='".$this->func_escape_string($order_id)."' ");



		if(!$_check)
		{

			$query = $this->db_exec("UPDATE inv_orders SET is_picked=1 where order_id='".$this->func_escape_string($order_id)."'");
			$query = $this->db_exec("UPDATE inv_orders_items SET opicked=1 where order_id='".$this->func_escape_string($order_id)."'");
		}
		else
		{
			$query = $this->db_exec("UPDATE inv_orders SET is_picked=0 where order_id='".$this->func_escape_string($order_id)."'");	
			$query = $this->db_exec("UPDATE inv_orders_items SET opicked=0 where order_id='".$this->func_escape_string($order_id)."'");

			// $this->mailHelpClosedShort($order_id);
		}

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
		$query = $this->db_exec("UPDATE inv_orders_items SET opacked=1 where order_id='".$this->func_escape_string($order_id)."'");

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

	public function markAdjusted($order_id,$skus)
	{
		$this->db_exec("START TRANSACTION");
		

		$query = $this->db_exec("UPDATE inv_orders SET is_adjusted=0 where order_id='".$this->func_escape_string($order_id)."'");
		$query = $this->db_exec("UPDATE inv_orders_items SET oadjusted=0 where order_id='".$this->func_escape_string($order_id)."'");
		$query2 = $this->db_exec("UPDATE inv_removed_order_items SET is_adjusted=1 WHERE order_id='".$this->func_escape_string($order_id)."'");
		
		if($query && $query2)
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
					// $reserved = $this->func_query_first_cell("SELECT sum(b.product_qty)  FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status)='on hold'  and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
					// $not_picked = $this->func_query_first_cell("SELECT sum(b.product_qty) - sum(b.picked_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','on hold')  and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
					// $picked = $this->func_query_first_cell("SELECT sum(b.picked_quantity) - sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','on hold') and b.is_picked=1 and a.is_packed=0 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
					// $packed = $this->func_query_first_cell("SELECT sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped') and b.is_picked=1 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");



			$description = $this->ledgerDescription($action);

			$query=$this->db_exec("INSERT INTO inv_product_ledger SET order_id='".$this->func_escape_string($order_id)."',sku='".$this->func_escape_string($sku)."',quantity='".(int)$quantity."',action='".$action."',description='".$this->func_escape_string($description)."',user_id='".(int)$user_id."',date_added='".date('Y-m-d H:i:s')."',on_hand='".(int)$on_hand."'");


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
			case 'close_short':
			$description = 'Close Short Adjustment is made.';
			break;

			case 'discard_removals':
			$description = 'Item has been discarded from Removals.';
			break;

			default:

			break;

		}
		return $description;
	}
	public function getImage($sku)
	{
		$image = $this->func_query_first_cell("SELECT image FROM oc_product WHERE TRIM(LOWER(model))='".trim(strtolower($this->func_escape_string($sku)))."'");
		if($image)
		{
			$image = str_replace('data/', 'cache/data/', $image);
			$image = str_replace('impskus/', 'cache/impskus/', $image);
			$image = str_replace('.jpg', '-150x150.jpg', $image);
			$image = str_replace('.png', '-150x150.png', $image);
		}
		else
		{
			$image = 'cache/data/image-coming-soon-150x150.jpg';
		}

		return $image;



	}
	// Ship Engine API
	private function curl($url,$body='',$is_put=false) {
		$options1=array();
		$options2=array();
		$options3=array();
		$options4=array();

	$ch = curl_init (); // Initialising cURL
	$options1 = Array(
	CURLOPT_RETURNTRANSFER => TRUE, // Setting cURL's option to return the webpage data
	CURLOPT_FOLLOWLOCATION => TRUE, // Setting cURL to follow 'location' HTTP headers
	CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
	CURLOPT_CONNECTTIMEOUT => 120, // Setting the amount of time (in seconds) before the request times out
	CURLOPT_TIMEOUT => 120, // Setting the maximum amount of time for cURL to execute queries
	CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
	CURLOPT_HTTPHEADER=> array(
		'Content-type: application/json',
		'api-key: '.$this->shipengine_api_key
		),
	
	CURLOPT_URL => $this->shipengine_url.$url ); // Setting cURL's URL option with the $url variable passed into the function
	// $options2 = array();
	if($is_put)
	{
		$options2 = Array(
			CURLOPT_CUSTOMREQUEST=> "PUT"
			);
		
	}

	if($body!='' && !$is_put)
	{
		$options3 = Array(
			CURLOPT_POST=> 1


			);

	}
	if($is_put || $body!='')
	{
		$options4 = Array(CURLOPT_POSTFIELDS=>$body);
	}
	
	$options = ($options1+$options2+$options3+$options4);
	// print_r($options);exit;
	// $options3 = array_merge($options,$options2);
	// print_r($options3);
	// curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt_array ( $ch, $options ); // Setting cURL's options using the previously assigned array data in $options
	$data = curl_exec ( $ch ); // Executing the cURL request and assigning the returned data to the $data variable
	$error = curl_error($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	// echo $httpCode;exit;
	// echo $this->shipengine_url.$url;;
	curl_close ( $ch ); // Closing cURL
	if($is_put)
	{
		// echo $data;exit;
	}
	
	return $data; // Returning the data from the function
}
public function listCarriers()
{
	$data = $this->curl('/v1/carriers');
	return $data;
}
public function getRates($body)
{
	$data = $this->curl('/v1/rates',$body);
	return $data;
}

public function getPackages($service)
{
	$data = $this->curl('/v1/carriers/'.$service.'/packages');
	return $data;
}

public function getLabel($body)
{
	$data = $this->curl('/v1/labels',$body);
	return $data;
}

public function getCarrierOptions($carrier_id)
{
	$data = $this->curl('/v1/carriers/'.$carrier_id.'/options');
	return $data;
}

public function createManifest($body)
{
	$data = $this->curl('/v1/manifests',$body);
	return $data;
}

public function validateAddress($body)
{
	$data = $this->curl('/v1/addresses/validate',$body);
	return $data;
}
public function voidLabel($label_id)
{
	$data = $this->curl('/v1/labels/'.$label_id.'/void','',true);
		// echo $data.'aa';exit;
	$this->db_exec("UPDATE inv_label_data set voided=1 where label_id='".$label_id."'");
	return $data;
}


public function saveLabel($order_ids,$data)
{
	$combined_orders = $order_ids;

	$order_ids = explode(",", $order_ids);
	if(count($order_ids)==1)
	{
		$combined_orders = '';
	}
	foreach($order_ids as $order_id)
	{
		
		$this->db_exec("INSERT INTO inv_label_data SET 
			order_id='".$this->func_escape_string($order_id)."',
			label_id='".$this->func_escape_string($data['label_id'])."',
			status='".$this->func_escape_string($data['status'])."',
			shipment_id='".$this->func_escape_string($data['shipment_id'])."',
			ship_date='".date('Y-m-d H:i:s',strtotime($data['ship_date']))."',
			created_at='".date('Y-m-d H:i:s',strtotime($data['created_at']))."',
			shipment_currency='".$this->func_escape_string($data['shipment_cost']['currency'])."',
			shipment_amount='".(float)$data['shipment_cost']['amount']."',
			insurance_currency='".$this->func_escape_string($data['insurance_cost']['currency'])."',
			insurance_amount='".(float)$data['insurance_cost']['amount']."',
			tracking_number='".$this->func_escape_string($data['tracking_number'])."',
			is_return_label='".(int)($data['is_return_label'])."',
			is_international='".(int)($data['is_international'])."',
			batch_id='".$this->func_escape_string($data['batch_id'])."',
			carrier_id='".$this->func_escape_string($data['carrier_id'])."',
			service_code='".$this->func_escape_string($data['service_code'])."',
			package_code='".$this->func_escape_string($data['package_code'])."',
			voided='".(int)($data['voided'])."',
			tracking_status='".$this->func_escape_string($data['tracking_status'])."',
			label_download='".$this->func_escape_string($data['label_download']['href'])."',
			package_weight='".(float)($data['packages']['weight']['value'])."',
			package_weight_unit='".($data['packages']['weight']['unit'])."',
			dimension_unit='".($data['packages']['dimensions']['unit'])."',
			length='".(float)($data['packages']['dimensions']['length'])."',
			width='".(float)($data['packages']['dimensions']['width'])."',
			height='".(float)($data['packages']['dimensions']['height'])."',
			combined_orders='".$combined_orders."'
			

			");
	}
}
public function getOrdersFromSKU($skus)
{
	$order_ids = array();
	foreach($skus as $sku)
	{
		$data = explode("~", $sku);
		$order_id = $data[0];
			// $_sku = $data[1];
		if(!in_array($order_id, $order_ids))
		{
			$order_id[] = $order_id;
		}
	}
	return $order_ids;
}

public function checkBulkOrders($order_ids)
{
	$json_encoded_order = json_encode($order_ids);

	$data = $this->func_query_first("SELECT * FROM inv_bulk_mapping WHERE serialized='".$json_encoded_order."'");
	return $data;
}

public function makeBulkOrder($order_ids)
{
	$bulk_number = $this->getBulkOrderNo();
	$json_encoded_order = json_encode($order_ids);

	$this->db_exec("INSERT INTO inv_bulk_mapping SET bulk_number='".$bulk_number."',serialized='".$json_encoded_order."',datetime='".date('Y-m-d H:i:s')."'");

	return $bulk_number;
}

public function getBulkOrder($bulk_number)
{
	$data = $this->func_query_first("SELECT * from inv_bulk_mapping WHERE bulk_number='".$bulk_number."'");
	$return = array();
	if($data)
	{
		$return = array(
			'bulk_number'=>$bulk_number,
			'order_ids' => json_decode($data['serialized'])
			);
	}

	return $return;
}

private function getBulkOrderNo(){
	
	$prefix="BPO";
	
	$last_number = $this->func_query_first("select max(replace(bulk_number,'$prefix','')) as shipment_number from inv_bulk_mapping where bulk_number LIKE '%$prefix%'");
	
	$last_number = $last_number['shipment_number'];
	
	

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

public function getLabelHeaders()
{
	$rows = $this->func_query("SELECT distinct SUBSTRING_INDEX(service_code, '_', 1) as name from inv_label_data");
	return $rows;
}

public function getLabelData($filter)
{
	if($filter['carrier']!='local orders')
	{
		$query = "SELECT count(*) as qty,service_code FROM inv_label_data WHERE 1 = 1 ";

		if($filter['date'])
		{
			$query.=" AND DATE(created_at)='".$this->func_escape_string($filter['date'])."' ";
		}

		if($filter['carrier'])
		{
			$query.=" AND SUBSTRING_INDEX(service_code, '_', 1)='".$this->func_escape_string($filter['carrier'])."' ";
		}
		$query.=" GROUP BY service_code";

	}
	else
	{

		$query="SELECT COUNT(*) as qty,'' as service_code FROM oc_order WHERE order_status_id=3 AND date(date_added)='".$this->func_escape_string($filter['date'])."' and shipping_method='Local Las Vegas Store Pickup - 9:30am-4:30pm' ";
		// echo $query;exit;
	}


		// echo $query;exit;
	$rows = $this->func_query($query);
	return $rows;

}


public function modifyString($string)
{
	$map = array(
		'Fedex'=>'FedEx',
		'Ups'=>'UPS',
		'Usps'=>'USPS'
		);

	$string = str_replace("_", " ", $string);
	$string = ucwords($string);

	$string = str_replace(array_keys($map),array_values($map),$string);
		// $string = strtr($string, $map);
		// echo $string;exit;
	return $string;
}

public function updateInventoryShipped($order_ids,$status)
{
	$_order_ids = explode(",", $order_ids);

	foreach($_order_ids as $order_id)
	{
		$items = $this->func_query("SELECT product_sku,sum(product_qty) as product_qty FROM inv_orders_items WHERE order_id='".$this->func_escape_string($order_id)."' GROUP BY product_sku");
		
		$this->db_exec("START TRANSACTION");
		$sort_array = array();
		foreach($items as $item)
		{
			// disabled it for fb
			$query = 	$this->db_exec("UPDATE oc_product SET quantity=quantity-".(int)$item['product_qty']." WHERE TRIM(LOWER(model))='".trim(strtolower($item['product_sku']))."'");
			$this->updateOutOfStock($item['product_sku']);

			$sort_array[$item['product_sku']]=$item['product_qty'];

			
		}

		if($query)
		{
			
			$this->db_exec("COMMIT");

			$this->makeLedger($order_id,$sort_array,$_SESSION['user_id'],'shipped');
		}
		else
		{
			$this->db_exec("ROLLBACK");
		}
	}






}
public function updateOutOfStock($sku)
{
	$quantity = $this->func_query_first_cell("select quantity from oc_product WHERE LOWER(model)='".trim(strtolower($sku))."'");
	if((int)$quantity<=0)
	{
		$check = $this->func_query_first("SELECT * FROM inv_product_inout_stocks where lower(product_sku)='".trim(strtolower($sku))."' order by 1 desc limit 1");
		if($check['instock_date']!='0000-00-00 00:00:00')
		{
			$this->db_exec("INSERT INTO inv_product_inout_stocks SET product_sku='".trim($sku)."',instock_date='0000-00-00 00:00:00',outstock_date='".date('Y-m-d H:i:s')."',date_modified='".date('Y-m-d H:i:s')."' ");
			
		}
	}
}

public function updateInventoryAdjustment($order_id,$status)
{

	$items = $this->func_query("SELECT  (SUBSTRING_INDEX(item_sku, '*', 1)) as product_sku,sum(SUBSTRING_INDEX(item_sku, '*', -1)) as product_qty FROM inv_removed_order_items WHERE order_id='".$this->func_escape_string($order_id)."' GROUP BY product_sku");
	$order_status = $this->func_query_first_cell("SELECT order_status FROM inv_orders WHERE order_id='".$this->func_escape_string($order_id)."'");
		//$this->db_exec("START TRANSACTION");
	$sort_array = array();
	if(strtolower($order_status)=='shipped')
	{
		foreach($items as $item)
		{
			if($item['product_qty']==0)
			{
				$item['product_qty']=1;
			}
			$query = 	$this->db_exec("UPDATE oc_product SET quantity=quantity+".(int)$item['product_qty']." WHERE TRIM(LOWER(model))='".trim(strtolower($item['product_sku']))."'");

			

			
		}
	}

		// if($query)
		// {

		// 	$this->db_exec("COMMIT");


		// }
		// else
		// {
		// 	$this->db_exec("ROLLBACK");
		// }






}



public function updateInventoryCancel($order_id,$status)
{

	$items = $this->func_query("SELECT product_sku,sum(product_qty) as product_qty FROM inv_orders_items WHERE order_id='".$this->func_escape_string($order_id)."' GROUP BY product_sku");

	$this->db_exec("START TRANSACTION");
	$sort_array = array();
	foreach($items as $item)
	{
		$query = 	$this->db_exec("UPDATE oc_product SET quantity=quantity+".(int)$item['product_qty']." WHERE TRIM(LOWER(model))='".trim(strtolower($item['product_sku']))."'");

		$sort_array[$item['product_sku']]=$item['product_qty'];


	}

	if($query)
	{

		$this->db_exec("COMMIT");

		$this->makeLedger($order_id,$sort_array,$_SESSION['user_id'],'rollback');
	}
	else
	{
		$this->db_exec("ROLLBACK");
	}






}


private function oc_config($key)
{
	// global $db;
	$value =  $this->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='".$key."' ");
	return unserialize($value);
}

public function updateTrackingInfo($order_id,$tracking_number,$service_code)
{
	$carrier_code = explode("_", $service_code);
	switch($carrier_code[0])
	{
		case 'ups':
		$carrier = 'UPS';
		break;

		case 'fedex':
		$carrier = 'FedEx';
		break;

		case 'usps':
		$carrier = 'USPS';
		break;

		default:
		$carrier = 'Unknown';
		break;
	}

	$post = array(
		"tracking_number" => $this->func_escape_string($tracking_number),

		"carrier" => $this->func_escape_string($carrier));
	$ch = curl_init('http://imp.phonepartsusa.com/easypost/tracker_api.php');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$response = curl_exec($ch);
	curl_close($ch);
	$response = json_decode($response);
}
public function unformatCurrency($value)
{
	$val  = floatval(ltrim($array_value, '$'));
	return $val;
}

public function getOrderTotal($order_ids)
{
	$order_total = 0.00;
	foreach(explode(",", $order_ids) as $order_id)
	{
		$order_total+=$this->func_query_first_cell("SELECT order_price FROM inv_orders where order_id='".$this->func_escape_string($order_id)."'");
		// echo "SELECT order_price FROM inv_orders where order_id='".$this->func_escape_string($order_id)."'<br>";
	}
	// exit;
	return $order_total;
}

public function getShippingMap($shipping_method)
{

	$data = $this->func_query_first_cell("SELECT mapping FROM inv_shipping_mapping WHERE lower(shipping_method)='".$this->func_escape_string(strtolower($shipping_method))."'");
	$data = json_decode($data,true);
	
	$array = array();
	foreach($data['service'] as $service)
	{
		$array[] = $service;
	}
	return $array;
	// if(count($data)==1)
	// {
	// 	return $data[0]['service'];
	// }
	// elseif(count($data)>1)
	// {
	// 	foreach($data as $service)
	// 	{

	// 		if((int)$service['min_total']!=0 && (int)$service['max_total']!=0)
	// 		{

	// 			if($this->unformatCurrency($order_total)>=(float)$service['min_total']  && $this->unformatCurrency($order_total)<(float)$service['max_total'] )
	// 			{
	// 				// echo $service['service'];exit;

	// 				return $service['service'];
	// 				break;
	// 			}
	// 		}	
	// 	}
	// }
	// else
	// {
	// 	return '';
	// }
}
public function closeShort($order_id)
{
	$items = $this->func_query("SELECT * FROM inv_orders_items WHERE order_id='".$this->func_escape_string($order_id)."' and product_qty<>picked_quantity and product_sku<>'SIGN'");

	foreach ($items as $item) {
		$this->makeLedger($order_id,array($item['product_sku']=>($item['product_qty']-$item['picked_quantity'])),$_SESSION['user_id'],'close_short');
		$item_name = $this->getProduct($item['product_sku']);
			$this->db_exec("UPDATE inv_orders_items SET product_qty=picked_quantity,product_price=product_unit*picked_quantity,oadjusted=1,opicked=1 where id=".$item['id']);
			$this->db_exec("INSERT INTO inv_removed_order_items SET order_id='".$order_id."',item_sku='".$item['product_sku']." * ".($item['product_qty'] - $item['picked_quantity'])."',item_name='".$item_name['name']."',item_price='".(float)$item['product_unit']*($item['product_qty'] - $item['picked_quantity'])."',date_removed='".date('Y-m-d H:i:s')."',reason='Close Short',removed_by='".$_SESSION['user_name']."'");

			// $this->db_exec("UPDATE oc_product SET quantity=0 WHERE trim(lower(model))='".trim(strtolower($item['product_sku']))."'");

		}	
			$this->db_exec("UPDATE inv_orders SET is_picked=1,is_adjusted=1 WHERE order_id='".$order_id."'");
			$this->db_exec("DELETE FROM inv_orders_items WHERE product_qty=0 and order_id='".$order_id."'");
}
public function mailHelpClosedShort($order_id,$mail_host,$mail_user,$mail_password)
{
	// error_reporting(E_ALL);
// ini_set('display_errors', 1);
	$body='';
	// $body.='Hi Helpdesk!<br>';
	// $body.='Recently the <strong>Order # <a href="https://imp.phonepartsusa.com/viewOrderDetail.php?order='.$order_id.'">'.$order_id.'</strong> has been closed short, the information regarding the items being missed are as below:<br><br>';
	$body.='<strong>Order <a href="https://imp.phonepartsusa.com/viewOrderDetail.php?order='.$order_id.'">'.$order_id.'</strong> was closed short. Review the items removed to ensure qty are truely out of stock, and issue refund or store credit as appropriate.<br> <a href="https://imp.phonepartsusa.com/viewOrderDetail.php?order='.$order_id.'">https://imp.phonepartsusa.com/viewOrderDetail.php?order='.$order_id.'</a><br><br>';
	$body.='<table width="50%">';
	$body.='<tr>';
	$body.='<th align="center"><strong>SKU</strong></th>';
	$body.='<th align="center"><strong>Qty Short</strong></th>';
	$body.='<th align="center"><strong>Unit Price</strong></th>';
	$body.='<th align="center"><strong>Line Total</strong></th>';
	$body.='</tr>';
	$items = $this->func_query("SELECT * FROM inv_removed_order_items WHERE order_id='".$this->func_escape_string($order_id)."' ");
	$i = 1;
	$send_mail = false;
	$line_total = 0.00;
	foreach($items as $item)
	{
		$_temp = explode("*",$item['item_sku']);
		$sku = trim($_temp[0]);
		$item_qty = trim($_temp[1]);
		$line_total+=$item['item_price'];
		$send_mail = true;

		$body.='<tr>';
	$body.='<td align="center">'.$sku.'</td>';
	$body.='<td align="center">'.(int)$item_qty.'</td>';
	$body.='<td align="right">$'.number_format($item['item_price']/$item_qty,2).'</td>';
	$body.='<td align="right">$'.number_format($item['item_price'],2).'</td>';
	
	$body.='</tr>';
		// $body.='<strong>'.$i.') '.$item['product_sku'].'--'.($item['product_qty']-$item['picked_quantity'])."</strong><br>";
		$i++;
	}

	$body.='<tr >';

	$body.='<th align="right"><strong>Total</strong></th>';
	$body.='<th colspan="3" align="right"><strong>$'.number_format($line_total,2).'</strong></th>';
	$body.='</tr>';

	


	$body.='</table><br>';
	$body.='<p>The following inventory adjustments were made.</p>';
	$body.='<table width="50%">';
	$body.='<tr>';
	$body.='<th align="center"><strong>SKU</strong></th>';
	$body.='<th align="center"><strong>Qty Removed from Shelf</strong></th>';
	
	$body.='</tr>';
	foreach($items as $item)
	{
		$_temp = explode("*",$item['item_sku']);
		$sku = trim($_temp[0]);
		$item_qty = $this->getProduct($sku);

		$body.='<tr>';
		$body.='<td align="center">'.$sku.'</td>';
		$body.='<td align="center">'.(int)$item_qty['quantity'].'</td>';
		$body.='</tr>';

		$this->db_exec("UPDATE oc_product SET quantity=0 WHERE trim(lower(model))='".trim(strtolower($sku))."'");
	}
	$body.='</table>';
	// if($o)

	
	if($send_mail)
	{
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->CharSet = 'UTF-8';
		$mail->Host = $mail_host; 
    	// SMTP server example
		$mail->SMTPDebug = 0;                     
	    // enables SMTP debug information (for testing)
		$mail->SMTPAuth = true;                  
	    // enable SMTP authentication
		$mail->Port = 25;                    
	    // set the SMTP port for the GMAIL server
		$mail->Username = $mail_user; 
	    // SMTP account username example
		$mail->Password = $mail_password;        
	    // SMTP account password example
		$mail->SetFrom($mail_user, 'PhonePartsUSA');

		
//...later


		$mail->addAddress('help@phonepartsusa.com', 'Helpdesk PhonePartsUSA');
		$mail->Subject = 'Order '.$order_id.' Close Short Report';
		$mail->Body = $body;

		$mail->IsHTML(true);
		if($mail->send())
		{
			// echo 'mail sent';
			return true;
		}
		else
		{
			// echo $debug;
			echo 'mail not sent';
			return false;
		}


	}

}

public function getInventoryDetail($sku)
	{
		
		
		$product_info = $this->func_query_first("SELECT quantity,prefill,prefill_shipment FROM oc_product WHERE TRIM(LOWER(model))='".strtolower(trim($sku))."'");
		$on_hand = $product_info['quantity'];
		$prefill = $product_info['prefill'];
		$prefill_shipment = $product_info['prefill_shipment'];

		// $on_hand = $this->func_query_first_cell("SELECT quantity FROM oc_product WHERE TRIM(LOWER(model))='".strtolower(trim($sku))."'");
		
		$reserved = $this->func_query_first_cell("SELECT sum(b.product_qty)  FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status)='on hold' and b.is_picked=0 and b.is_packed=0  and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
		$not_picked = $this->func_query_first_cell("SELECT sum(b.product_qty) - sum(b.picked_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','on hold')  and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
		$picked = $this->func_query_first_cell("SELECT sum(b.picked_quantity) - sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','on hold') and b.is_picked=1 and a.is_packed=0 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
		$packed = $this->func_query_first_cell("SELECT sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','on hold') and b.is_packed=1 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");

			$adjustment = $this->func_query_first_cell("SELECT sum(SUBSTRING_INDEX(b.item_sku, '*', -1)) FROM inv_removed_order_items  b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','shipped') and a.is_adjusted=1 and a.is_picked=1 and trim(lower(SUBSTRING_INDEX(b.item_sku, '*', 1)))='".strtolower(trim($sku))."' and reason not in ('Out of Stock','Close Short') and trim(lower(SUBSTRING_INDEX(b.item_sku, '*', 1)))<>'sign' and b.is_adjusted=0");
		$allocated_qty =  (int)$not_picked + (int)$picked + (int)$packed ;
		$allocated_qty = (int)$allocated_qty - (int)$reserved;

		$array = array(
			'on_hand'=>$on_hand,
			'prefill'=>$prefill,
			'prefill_shipment'=>$prefill_shipment,
			'on_hold'=>$reserved,
			'not_picked'=>$not_picked,
			'adjustment'=>$adjustment,
			'picked'=>$picked,
			'packed'=>$packed,
			'allocated'=>$allocated_qty,
			'on_shelf'=>(int)$on_hand - (int)$picked - (int)$packed,
			'available'=>(int)$on_hand+(int)$prefill+(int)$adjustment - ((int)$allocated_qty+(int)$reserved)
			);
		return $array;
	}

}







$inventory = new Inventory;


?>