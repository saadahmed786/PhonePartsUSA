<?php
class ModelXhelperOrder extends Model {
	public function orderTypeCheck($order_id)
	{
		
	$query = $this->db->query("SELECT total,old_total FROM `".DB_PREFIX."order` WHERE order_id='".$order_id."'");	
	
	$row = $query->row;
	
	if((round($_SESSION['order_changes']['total'],2)==round($row['total'],2)) or count($_SESSION['order_changes'])==0)
	{
		$value = '0';
		
	}
	else{
		
		if($_SESSION['order_changes']['total']>$row['total'])
		{
			$value ='1';	
		}
		else
		{
			$value='2';	
		}
		
	}
	
	return $value;
		
	}
	
	}
?>