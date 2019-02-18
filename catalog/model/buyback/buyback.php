<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ModelBuybackBuyBack extends Model {

	public function getGeneralDetails() {
		$query = $this->db->query("SELECT * FROM inv_buy_back LIMIT 1");
		$row = $query->row;
		return $row;
	}
	public function getProducts(){
		$query = $this->db->query("SELECT * FROM inv_buy_back ORDER BY sort");
		$rows = $query->rows;
		return $rows;
	}

	public function getGradeDesc(){
		$query = $this->db->query("SELECT * FROM inv_buyback_desc");
		$rows = $query->row;
		return $rows;
	} 
	public function saveData($data){
		$shipment_number = $this->getShipmentNo();
		if($data['cash_total'])
		{
			$total = $data['cash_total'];	
		}
		else
		{
			$total = $data['credit_total'];
		}
		
		$this->db->query("INSERT INTO oc_buyback SET shipment_number='$shipment_number', customer_id='".(int)$this->customer->getId()."',address_id='".(int)$data['address_id']."',firstname='".$this->db->escape($data['firstname'])."',lastname='".$this->db->escape($data['lastname'])."',email='".$this->db->escape($data['email'])."',telephone='".$this->db->escape($data['telephone'])."',address_1='".$this->db->escape($data['address_1'])."',city='".$this->db->escape($data['city'])."',postcode='".$this->db->escape($data['postcode'])."',zone_id='".(int)($data['zone_id'])."',payment_type='".$this->db->escape($data['payment_type'])."',total='".(float)($total)."',date_added=NOW(),paypal_email='".$this->db->escape($data['paypal_email'])."',`option`='".$this->db->escape($data['option'])."'");
		$buyback_id = $this->db->getLastId();
		$count = count($data['sku']);
		for($i=0;$i<$count;$i++)
		{
			if((float)$data['qty'][$i]>0.00)
			{
				$this->db->query("INSERT INTO oc_buyback_products SET buyback_id='$buyback_id',sku='".$this->db->escape($data['sku'][$i])."',image_path='".$data['image_path'][$i]."',description='".$this->db->escape($data['description'][$i])."', qty='".(int)($data['qty'][$i])."', oem_a_price='".(float)($data['oem_a_price'][$i])."', oem_b_price='".(float)($data['oem_b_price'][$i])."', oem_c_price='".(float)($data['oem_c_price'][$i])."', oem_d_price='".(float)($data['oem_d_price'][$i])."', non_oem_a_price='".(float)($data['non_oem_a_price'][$i])."', non_oem_b_price='".(float)($data['non_oem_b_price'][$i])."', non_oem_c_price='".(float)($data['non_oem_c_price'][$i])."', non_oem_d_price='".(float)($data['non_oem_d_price'][$i])."', salvage_price='".(float)($data['salvage_price'][$i])."'");
			}
		}

		if((int)$this->customer->getId()>0 and (int)$data['address_id']>0)
		{
			$this->db->query("INSERT INTO oc_address SET customer_id='".(int)$this->customer->getId()."',firstname='".$this->db->escape($data['firstname'])."',lastname='".$this->db->escape($data['lastname'])."',address_1='".$this->db->escape($data['address_1'])."',city='".$this->db->escape($data['city'])."',postcode='".$this->db->escape($data['postcode'])."',country_id='223',zone_id='".(int)$data['zone_id']."',insert_date='".date('Y-m-d H:i:s')."'");

		}	




		return $buyback_id; 
	}
	private function getShipmentNo(){

	//global $db;


		$prefix="LBB";

		$last_number = $this->db->query("select max(replace(shipment_number,'$prefix','')) as shipment_number from oc_buyback where shipment_number LIKE '%$prefix%'");

		$last_number = $last_number->row;
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
	public function getBuyBackDetail($buyback_id)
	{
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."buyback WHERE buyback_id='".(int)$buyback_id."'");
		$row = $query->row;
		return $row;
	}
	
	public function getBuyBackProducts($buyback_id)
	{
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."buyback_products WHERE buyback_id='".(int)$buyback_id."'");
		$rows = $query->rows;
		return $rows;
	}
	public function getAddress($buyback_id){
		$this->load->model('localisation/zone');
		$this->load->model('account/customer');
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."buyback WHERE buyback_id='".(int)$buyback_id."'");
		$row = $query->row;
		$array = array();
		// if($row['customer_id']==0)
		// {
			$zone = $this->model_localisation_zone->getZone($row['zone_id']);
			$array['firstname'] = $row['firstname'];
			$array['lastname'] = $row['lastname'];
			$array['email'] = $row['email'];
			$array['telephone'] = $row['telephone'];
			$array['address_1'] = $row['address_1'];
			$array['city'] = $row['city'];
			$array['postcode'] = $row['postcode'];
			$array['zone'] = $zone['name'];
			$array['zone_id'] = $row['zone_id'];
			$array['country_id'] = 223;

			
		// }
		// else
		// {
		// 	$zone = $this->model_localisation_zone->getZone($row['zone_id']);
		// 	$customer = $this->model_account_customer->getCustomer($row['customer_id']);
		// 	$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$row['address_id'] . "' AND customer_id = '" . (int)$row['customer_id'] . "'");

		// 	$address = $address_query->row;
		// 	$array['firstname'] = $address['firstname'];
		// 	$array['lastname'] = $address['lastname'];
		// 	$array['email'] = $customer['email'];
		// 	$array['telephone'] = $customer['telephone'];
		// 	$array['address_1'] = $address['address_1'];
		// 	$array['city'] = $address['city'];
		// 	$array['postcode'] = $address['postcode'];
		// 	$array['zone'] = $zone['name'];
			
		// }
		
		return $array;
	}
}
