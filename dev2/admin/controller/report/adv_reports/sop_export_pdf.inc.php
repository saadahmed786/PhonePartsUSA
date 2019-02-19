<?php
			ini_set("memory_limit","1024M");
			set_time_limit( 180000 );
			
			$export_pdf = "<html><head>";			
			$export_pdf .= "</head>";
			$export_pdf .= "<body>";
			$export_pdf .= "<style type='text/css'>
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
</style>";
				$export_pdf .= "<table class='list_main'>";
				$export_pdf .= "<thead>";
				$export_pdf .= "<tr>";
				if ($filter_group == 'year') {				
				$export_pdf .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_year')."</td>";
				} elseif ($filter_group == 'quarter') {
				$export_pdf .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_year')."</td>";
				$export_pdf .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_quarter')."</td>";				
				} elseif ($filter_group == 'month') {
				$export_pdf .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_year')."</td>";
				$export_pdf .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_month')."</td>";
				} else {
				$export_pdf .= "<td align='left' width='80' nowrap='nowrap'>".$this->language->get('column_date_start')."</td>";
				$export_pdf .= "<td align='left' width='80' nowrap='nowrap'>".$this->language->get('column_date_end')."</td>";	
				}
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_orders')."</td>";
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_customers')."</td>";
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_products')."</td>";
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_sub_total')."</td>";	
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_handling')."</td>";	
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_loworder')."</td>";					
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_points')."</td>";				
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_shipping')."</td>";
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_coupon')."</td>";				
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_tax')."</td>";
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_credit')."</td>";					
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_voucher')."</td>";
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_commission')."</td>";					
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_total')."</td>";
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_prod_costs')."</td>";
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_net_profit')."</td>";	
				$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_profit_margin')."</td>";					
				$export_pdf .= "</tr>";
				$export_pdf .= "</thead><tbody>";
				foreach ($results as $result) {				
					$export_pdf .= "<tr>";
					if ($filter_group == 'year') {				
					$export_pdf .= "<td align='left' nowrap='nowrap'>".$result['year']."</td>";
					} elseif ($filter_group == 'quarter') {
					$export_pdf .= "<td align='left' nowrap='nowrap'>".$result['year']."</td>";	
					$export_pdf .= "<td align='left' nowrap='nowrap'>".'Q' . $result['quarter']."</td>";						
					} elseif ($filter_group == 'month') {
					$export_pdf .= "<td align='left' nowrap='nowrap'>".$result['year']."</td>";	
					$export_pdf .= "<td align='left' nowrap='nowrap'>".$result['month']."</td>";	
					} else {
					$export_pdf .= "<td align='left' nowrap='nowrap'>".date($this->language->get('date_format_short'), strtotime($result['date_start']))."</td>";
					$export_pdf .= "<td align='left' nowrap='nowrap'>".date($this->language->get('date_format_short'), strtotime($result['date_end']))."</td>";
					}					
					$export_pdf .= "<td align='right' nowrap='nowrap'>".$result['orders']."</td>";					
					$export_pdf .= "<td align='right' nowrap='nowrap'>".$result['customers']."</td>";
					$export_pdf .= "<td align='right' nowrap='nowrap'>".$result['products']."</td>";
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9;'>".$this->currency->format($result['sub_total'], $this->config->get('config_currency'))."</td>";
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9;'>".$this->currency->format($result['handling'], $this->config->get('config_currency'))."</td>";
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9;'>".$this->currency->format($result['low_order_fee'], $this->config->get('config_currency'))."</td>";					
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['reward'], $this->config->get('config_currency'))."</td>";					
					$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['shipping'], $this->config->get('config_currency'))."</td>";
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['coupon'], $this->config->get('config_currency'))."</td>";					
					$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['tax'], $this->config->get('config_currency'))."</td>";
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['credit'], $this->config->get('config_currency'))."</td>";					
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['voucher'], $this->config->get('config_currency'))."</td>";
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format('-' . ($result['commission']), $this->config->get('config_currency'))."</td>";					
					$export_pdf .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['total'], $this->config->get('config_currency'))."</td>";
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format('-' . ($result['prod_costs']), $this->config->get('config_currency'))."</td>";
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#c4d9ee; font-weight:bold;'>".$this->currency->format($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher'], $this->config->get('config_currency'))."</td>";
					if (($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']) > 0) {				
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#c4d9ee; font-weight:bold;'>".round(100 * ($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']) / ($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']), 2) . '%'."</td>";
					} else {
					$export_pdf .= "<td align='right' nowrap='nowrap' style='background-color:#c4d9ee; font-weight:bold;'>".'0%'."</td>";
					}							
					$export_pdf .= "</tr>";				
				}
				$export_pdf .= "</tbody></table>";
				$export_pdf .= "</body></html>";

			$dompdf = new DOMPDF();
			$dompdf->load_html($export_pdf);
			$dompdf->set_paper("a3", "landscape");
			$dompdf->render();
			$dompdf->stream("sale_profit_report_".date("Y-m-d",time()).".pdf");
?>