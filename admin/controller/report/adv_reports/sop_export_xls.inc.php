<?php
			ini_set("memory_limit","1024M");
			set_time_limit( 180000 );
			
				$xls_output ="<html><head>";
				$xls_output .="<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
				$xls_output .="</head>";
				$xls_output .="<body>";					
				$xls_output .="<table border='1'>";	
				$xls_output .="<tr>";
				if ($filter_group == 'year') {				
				$xls_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_year')."</td>";
				} elseif ($filter_group == 'quarter') {
				$xls_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_year')."</td>";					
				$xls_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_quarter')."</td>";				
				} elseif ($filter_group == 'month') {
				$xls_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_year')."</td>";					
				$xls_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_month')."</td>";
				} else {
				$xls_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_date_start')."</td>";				
				$xls_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_date_end')."</td>";	
				}
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_orders')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_customers')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_products')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_sub_total')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_handling')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_loworder')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_points')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_shipping')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_coupon')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_tax')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_credit')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_voucher')."</td>";	
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_commission')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_total')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_prod_costs')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_net_profit')."</td>";	
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_profit_margin')."</td>";				
				$xls_output .="</tr>";
				foreach ($results as $result) {					
					$xls_output .="<tr>";
					if ($filter_group == 'year') {				
					$xls_output .= "<td align='left'>".$result['year']."</td>";
					} elseif ($filter_group == 'quarter') {
					$xls_output .= "<td align='left'>".$result['year']."</td>";
					$xls_output .= "<td align='left'>".'Q' . $result['quarter']."</td>";					
					} elseif ($filter_group == 'month') {
					$xls_output .= "<td align='left'>".$result['year']."</td>";
					$xls_output .= "<td align='left'>".$result['month']."</td>";	
					} else {
					$xls_output .= "<td align='left'>".date($this->language->get('date_format_short'), strtotime($result['date_start']))."</td>";
					$xls_output .= "<td align='left'>".date($this->language->get('date_format_short'), strtotime($result['date_end']))."</td>";	
					}					
					$xls_output .= "<td align='right'>".$result['orders']."</td>";					
					$xls_output .= "<td align='right'>".$result['customers']."</td>";
					$xls_output .= "<td align='right'>".$result['products']."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['sub_total'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['handling'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['low_order_fee'], $this->config->get('config_currency'))."</td>";					
					$xls_output .= "<td align='right'>".$this->currency->format($result['reward'], $this->config->get('config_currency'))."</td>";					
					$xls_output .= "<td align='right'>".$this->currency->format($result['shipping'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['coupon'], $this->config->get('config_currency'))."</td>";					
					$xls_output .= "<td align='right'>".$this->currency->format($result['tax'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['credit'], $this->config->get('config_currency'))."</td>";					
					$xls_output .= "<td align='right'>".$this->currency->format($result['voucher'], $this->config->get('config_currency'))."</td>";	
					$xls_output .= "<td align='right'>".$this->currency->format('-' . ($result['commission']), $this->config->get('config_currency'))."</td>";					
					$xls_output .= "<td align='right'>".$this->currency->format($result['total'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format('-' . ($result['prod_costs']), $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher'], $this->config->get('config_currency'))."</td>";
					if (($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']) > 0) {				
					$xls_output .= "<td align='right'>".round(100 * ($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']) / ($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']), 2) . '%'."</td>";
					} else {
					$xls_output .= "<td align='right'>".'0%'."</td>";
					}	
					$xls_output .="</tr>";				
				}
				$xls_output .="</body></html>";

			$filename = "sale_profit_report_".date("Y-m-d",time());
			header('Expires: 0');
			header('Cache-control: private');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');			
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8; encoding=UTF-8');			
			header('Content-Disposition: attachment; filename='.$filename.".xls");
			header('Content-Transfer-Encoding: UTF-8');	
			print $xls_output;			
			exit;	
?>