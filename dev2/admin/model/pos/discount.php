<?php
class ModelPosDiscount extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		
            if(array_key_exists('discount_amount',$this->session->data) && $this->session->data['discount_amount']){   
                
		$discount = $this->session->data['discount_amount'];
		$type = $this->session->data['discount_type'];
                
                if($type == 'P'){
                    $discount = ($total*$discount)/100;  
                }else{
                    //convert selected currency format to default currency format                     
                    $value_currenct = $this->currency->getValue();                    
                    $discount = $discount/$value_currenct;
                }
                
                $total_data[] = array( 
                    'code'       => 'discount',
                    'title'      => 'Discount' ,
                    'text'       => $this->currency->format(0-$discount),
                    'value'      => $discount,
                    'sort_order' => $this->config->get('discount_sort_order')
                );

                $total -= $discount;

            }
	}
}
?>