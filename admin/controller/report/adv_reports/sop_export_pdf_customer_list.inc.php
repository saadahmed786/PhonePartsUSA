<?php
			ini_set("memory_limit","1024M");
			set_time_limit( 180000 );
			
			$export_pdf_customer_list = "<html><head>";
			$export_pdf_customer_list .= "</head>";
			$export_pdf_customer_list .= "<body>";
			$export_pdf_customer_list .= "<style type='text/css'>
.list_main {
	width: 100%;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;	
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
}
.list_main td {
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;	
}
.list_main thead td {
	background-color: #E5E5E5;
	padding: 3px;
	font-weight: bold;
}
.list_main tbody a {
	text-decoration: none;
}
.list_main tbody td {
	vertical-align: middle;
	padding: 3px;
}
.list_main .left {
	text-align: left;
	padding: 7px;
}
.list_main .right {
	text-align: right;
	padding: 7px;
}
.list_main .center {
	text-align: center;
	padding: 3px;
}

.list_detail {
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
	font-size: 9px;
	font-weight: bold;	
}
.list_detail tbody td {
	padding: 0px 3px;
	font-size: 9px;	
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
				$export_pdf_customer_list .= "<table class='list_main'>";				
				$export_pdf_customer_list .= "<thead>";
				$export_pdf_customer_list .= "<tr>";
				if ($filter_group == 'year') {				
				$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_year')."</td>";
				} elseif ($filter_group == 'quarter') {
				$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_year')."</td>";
				$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_quarter')."</td>";				
				} elseif ($filter_group == 'month') {
				$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_year')."</td>";
				$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_month')."</td>";
				} else {
				$export_pdf_customer_list .= "<td align='left' width='80' nowrap='nowrap'>".$this->language->get('column_date_start')."</td>";
				$export_pdf_customer_list .= "<td align='left' width='80' nowrap='nowrap'>".$this->language->get('column_date_end')."</td>";	
				}				
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_orders')."</td>";				
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_customers')."</td>";
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_products')."</td>";
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_sub_total')."</td>";	
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_handling')."</td>";	
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_loworder')."</td>";					
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_points')."</td>";				
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_shipping')."</td>";
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_coupon')."</td>";				
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_tax')."</td>";
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_credit')."</td>";					
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_voucher')."</td>";
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_commission')."</td>";				
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_total')."</td>";
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_prod_costs')."</td>";
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_net_profit')."</td>";	
				$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_profit_margin')."</td>";					
				$export_pdf_customer_list .= "</tr>";
				$export_pdf_customer_list .= "</thead><tbody>";				
					$export_pdf_customer_list .= "<tr>";
					if ($filter_group == 'year') {				
					$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['year']."</td>";
					} elseif ($filter_group == 'quarter') {
					$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['year']."</td>";	
					$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".'Q' . $result['quarter']."</td>";						
					} elseif ($filter_group == 'month') {
					$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['year']."</td>";	
					$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['month']."</td>";	
					} else {
					$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".date($this->language->get('date_format_short'), strtotime($result['date_start']))."</td>";
					$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".date($this->language->get('date_format_short'), strtotime($result['date_end']))."</td>";
					}	
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$result['orders']."</td>";					
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$result['customers']."</td>";
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$result['products']."</td>";
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9;'>".$this->currency->format($result['sub_total'], $this->config->get('config_currency'))."</td>";
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9;'>".$this->currency->format($result['handling'], $this->config->get('config_currency'))."</td>";
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9;'>".$this->currency->format($result['low_order_fee'], $this->config->get('config_currency'))."</td>";						
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['reward'], $this->config->get('config_currency'))."</td>";					
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['shipping'], $this->config->get('config_currency'))."</td>";
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['coupon'], $this->config->get('config_currency'))."</td>";					
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['tax'], $this->config->get('config_currency'))."</td>";
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['credit'], $this->config->get('config_currency'))."</td>";					
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['voucher'], $this->config->get('config_currency'))."</td>";	
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format('-' . ($result['commission']), $this->config->get('config_currency'))."</td>";						
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['total'], $this->config->get('config_currency'))."</td>";
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format('-' . ($result['prod_costs']), $this->config->get('config_currency'))."</td>";
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#c4d9ee; font-weight:bold;'>".$this->currency->format($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher'], $this->config->get('config_currency'))."</td>";	
					if (($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']) > 0) {				
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#c4d9ee; font-weight:bold;'>".round(100 * ($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']) / ($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']), 2) . '%'."</td>";
					} else {
					$export_pdf_customer_list .= "<td align='right' nowrap='nowrap' style='background-color:#c4d9ee; font-weight:bold;'>".'0%'."</td>";
					}
					$export_pdf_customer_list .= "</tr>";
					$export_pdf_customer_list .= "<tr>";
					$export_pdf_customer_list .= "<td colspan='19' align='center'>";
						$export_pdf_customer_list .= "<table class='list_detail'>";
						$export_pdf_customer_list .= "<thead>";
						$export_pdf_customer_list .= "<tr>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_customer_order_id')."</td>";					
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_customer_date_added')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_customer_inv_no')."</td>";						
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_customer_cust_id')."</td>";					
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_billing_name')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_billing_company')."</td>";									
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_billing_address_1')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_billing_address_2')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_billing_city')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_billing_zone')."</td>";						
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_billing_postcode')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_billing_country')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_customer_telephone')."</td>";	
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_shipping_name')."</td>";						
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_shipping_company')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_shipping_address_1')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_shipping_address_2')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_shipping_city')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_shipping_zone')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_shipping_postcode')."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_shipping_country')."</td>";					
						$export_pdf_customer_list .= "</tr>";
						$export_pdf_customer_list .= "</thead><tbody>";
						$export_pdf_customer_list .= "<tr>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['customer_ord_idc']."</td>";					
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['customer_order_date']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['customer_inv_no']."</td>";						
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['customer_cust_idc']."</td>";						
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['billing_name']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['billing_company']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['billing_address_1']."</td>";					
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['billing_address_2']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['billing_city']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['billing_zone']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['billing_postcode']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['billing_country']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['customer_telephone']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['shipping_name']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['shipping_company']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['shipping_address_1']."</td>";					
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['shipping_address_2']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['shipping_city']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['shipping_zone']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['shipping_postcode']."</td>";
						$export_pdf_customer_list .= "<td align='left' nowrap='nowrap'>".$result['shipping_country']."</td>";						
						$export_pdf_customer_list .= "</tr>";					
						$export_pdf_customer_list .= "</tbody></table>";
						$export_pdf_customer_list .= "</td>";
						$export_pdf_customer_list .= "</tr></tbody>";
				$export_pdf_customer_list .= "</table>";						
				}
				$export_pdf_customer_list .= "</body></html>";

			$dompdf = new DOMPDF();
			$dompdf->load_html($export_pdf_customer_list);
			$dompdf->set_paper("a2", "landscape");
			$dompdf->render();
			$dompdf->stream("sale_profit_report_customer_list_".date("Y-m-d",time()).".pdf");
?>