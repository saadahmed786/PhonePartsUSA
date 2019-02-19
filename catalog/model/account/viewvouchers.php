<?php
class ModelAccountViewvouchers extends Model {
	public function getVouchers($customer_email,$start=0,$limit=10, $days = false) {
		$return = array();
		//if($customer_id>0)
		//{
			//$user_info = $this->db->query("SELECT * FROM ".DB_PREFIX."customer WHERE customer_id='".(int)$customer_id."'");	
			//if($user_info->row)
			//{
				if ($days) {
                                    $rows = $this->db->query("SELECT * FROM ".DB_PREFIX."voucher WHERE to_email='".$this->db->escape($customer_email)."' and status=1 AND `date_added` > subdate(now(), interval ". $days ." day) ORDER BY date_added DESC LIMIT $start,$limit");
                                } else {
                                    $rows = $this->db->query("SELECT * FROM ".DB_PREFIX."voucher WHERE to_email='".$this->db->escape($customer_email)."' and status=1 ORDER BY date_added DESC LIMIT $start,$limit");
                                }
				$rows = $rows->rows;
				foreach($rows as $row)
				{
					
					$order_info = $this->db->query("SELECT * FROM ".DB_PREFIX."voucher_history WHERE voucher_id='".(int)$row['voucher_id']."'");
					
					$balance = 0.00;
					$order_detail = array();
					foreach($order_info->rows as $info)
					{
						$order_detail[] = array(
						'order_id'=>$info['order_id'],
						'amount'=>$info['amount']
						);
						$balance = $balance + $info['amount'];
						
					}
					
					$balance = $row['amount']+$balance;
					
					$return[] = array(
					'code'=>$row['code'],
					'date'=>$row['date_added'],
					'status'=>$row['status'],
					'amount'=>$row['amount'],
					'balance'=>$balance,
					'order_details'=>$order_detail,
					
					)	;
					
					
				}
				
				
				
			//}
			
			
		//}
		return $return;
		}
	public function getTotalVouchers($customer_email, $days = false)
	{
		if ($days) {
                    $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "voucher WHERE to_email='".$this->db->escape($customer_email)."' AND `date_added` > subdate(now(), interval " .$days. " day)");
                } else {
                    $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "voucher WHERE to_email='".$this->db->escape($customer_email)."'");
                }
		
		return $query->row['total'];
		
	}
	
}
?>