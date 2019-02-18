<?php
			ini_set("memory_limit","1024M");
			set_time_limit( 180000 );
			
			$export_html_all_details ="<html><head>";
			$export_html_all_details .="<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
			$export_html_all_details .="</head>";
			$export_html_all_details .="<body>";
			$export_html_all_details .="<style type='text/css'>
.list_detail {
	border-collapse: collapse;
	width: 100%;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	font-family: Arial, Helvetica, sans-serif;
	margin-top: 10px;
	margin-bottom: 10px;
}
.list_detail td {
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;
}
.list_detail thead td {
	background-color: #F0F0F0;
	padding: 0px 3px;
	font-size: 11px;
	font-weight: bold;	
}
.list_detail tbody td {
	padding: 0px 3px;
	font-size: 11px;	
}
.list_detail .left {
	text-align: left;
	padding: 3px;
}
.list_detail .right {
	text-align: right;
	padding: 3px;
}
.list_detail .center {
	text-align: center;
	padding: 3px;
}
</style>";
			foreach ($results as $result) {			
				$export_html_all_details .="<table class='list_detail'>";
				$export_html_all_details .="<thead>";
				$export_html_all_details .="<tr>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_prod_order_id')."</td>";					
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_prod_date_added')."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_prod_inv_no')."</td>";									
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_prod_id')."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_prod_sku')."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_prod_name')."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_prod_option')."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_prod_model')."</td>";
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_prod_currency')."</td>";						
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_prod_price')."</td>";
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_prod_quantity')."</td>";
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_prod_total')."</td>";	
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_order_sub_total')."</td>";				
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_order_shipping')."</td>";
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_order_tax')."</td>";							
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_order_value')."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_order_shipping_method')."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_order_payment_method')."</td>";	
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_order_status')."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_order_store')."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_billing_name'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_billing_company'))."</td>";									
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_billing_address_1'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_billing_address_2'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_billing_city'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_billing_zone'))."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_billing_postcode'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_billing_country'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_customer_telephone')."</td>";	
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_order_email')."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_order_customer_group')."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_shipping_name'))."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_shipping_company'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_shipping_address_1'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_shipping_address_2'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_shipping_city'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_shipping_zone'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_shipping_postcode'))."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($this->language->get('column_shipping_country'))."</td>";					
				$export_html_all_details .="</tr>";
				$export_html_all_details .="</thead><tbody>";
				$export_html_all_details .="<tr>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['product_ord_idc']."</td>";					
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['product_order_date']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['product_inv_no']."</td>";				
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['product_pidc']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['product_sku']."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['product_name']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['product_option']."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['product_model']."</td>";
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$result['product_currency']."</td>";						
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$result['product_price']."</td>";
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$result['product_quantity']."</td>";
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$result['product_total']."</td>";
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$result['order_sub_total']."</td>";					
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$result['order_shipping']."</td>";
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$result['order_tax']."</td>";							
				$export_html_all_details .= "<td align='right' nowrap='nowrap'>".$result['order_value']."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['order_shipping_method']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".strip_tags($result['order_payment_method'], '<br>')."</td>";	
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['order_status']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['order_store']."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['billing_name']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['billing_company']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['billing_address_1']."</td>";					
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['billing_address_2']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['billing_city']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['billing_zone']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['billing_postcode']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['billing_country']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['customer_telephone']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['order_email']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['order_group']."</td>";						
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['shipping_name']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['shipping_company']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['shipping_address_1']."</td>";					
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['shipping_address_2']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['shipping_city']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['shipping_zone']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['shipping_postcode']."</td>";
				$export_html_all_details .= "<td align='left' nowrap='nowrap'>".$result['shipping_country']."</td>";						
				$export_html_all_details .="</tr>";					
				$export_html_all_details .="</table>";							
			}
			$export_html_all_details .="</body></html>";

		$filename = "sale_profit_report_all_details_".date("Y-m-d",time());
		header('Expires: 0');
		header('Cache-control: private');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');			
		header('Content-Disposition: attachment; filename='.$filename.".html");
		print $export_html_all_details;			
		exit;		
?>