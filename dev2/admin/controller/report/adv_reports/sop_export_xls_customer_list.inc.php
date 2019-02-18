<?php
			ini_set("memory_limit","1024M");
			set_time_limit( 180000 );
			
				$export_xls_customer_list ="<html><head>";
				$export_xls_customer_list .="<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
				$export_xls_customer_list .="</head>";
				$export_xls_customer_list .="<body>";
				foreach ($results as $result) {					
				$export_xls_customer_list .="<table border='1'>";		
				$export_xls_customer_list .="<tr>";
				if ($filter_group == 'year') {				
				$export_xls_customer_list .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_year')."</td>";
				} elseif ($filter_group == 'quarter') {
				$export_xls_customer_list .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_year')."</td>";					
				$export_xls_customer_list .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_quarter')."</td>";				
				} elseif ($filter_group == 'month') {
				$export_xls_customer_list .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_year')."</td>";					
				$export_xls_customer_list .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_month')."</td>";
				} else {
				$export_xls_customer_list .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_date_start')."</td>";				
				$export_xls_customer_list .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_date_end')."</td>";	
				}	
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_orders')."</td>";				
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_customers')."</td>";
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_products')."</td>";
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_sub_total')."</td>";
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_handling')."</td>";
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_loworder')."</td>";					
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_points')."</td>";				
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_shipping')."</td>";
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_coupon')."</td>";				
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_tax')."</td>";
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_credit')."</td>";				
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_voucher')."</td>";
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_commission')."</td>";				
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_total')."</td>";				
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_prod_costs')."</td>";
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_net_profit')."</td>";	
				$export_xls_customer_list .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_profit_margin')."</td>";					
				$export_xls_customer_list .="</tr>";				
					$export_xls_customer_list .="<tr>";
					if ($filter_group == 'year') {				
					$export_xls_customer_list .= "<td align='left'>".$result['year']."</td>";
					} elseif ($filter_group == 'quarter') {
					$export_xls_customer_list .= "<td align='left'>".$result['year']."</td>";
					$export_xls_customer_list .= "<td align='left'>".'Q' . $result['quarter']."</td>";						
					} elseif ($filter_group == 'month') {
					$export_xls_customer_list .= "<td align='left'>".$result['year']."</td>";
					$export_xls_customer_list .= "<td align='left'>".$result['month']."</td>";	
					} else {
					$export_xls_customer_list .= "<td align='left'>".date($this->language->get('date_format_short'), strtotime($result['date_start']))."</td>";
					$export_xls_customer_list .= "<td align='left'>".date($this->language->get('date_format_short'), strtotime($result['date_end']))."</td>";	
					}
					$export_xls_customer_list .= "<td align='right'>".$result['orders']."</td>";					
					$export_xls_customer_list .= "<td align='right'>".$result['customers']."</td>";
					$export_xls_customer_list .= "<td align='right'>".$result['products']."</td>";
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['sub_total'], $this->config->get('config_currency'))."</td>";
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['handling'], $this->config->get('config_currency'))."</td>";
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['low_order_fee'], $this->config->get('config_currency'))."</td>";						
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['reward'], $this->config->get('config_currency'))."</td>";					
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['shipping'], $this->config->get('config_currency'))."</td>";
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['coupon'], $this->config->get('config_currency'))."</td>";					
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['tax'], $this->config->get('config_currency'))."</td>";
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['credit'], $this->config->get('config_currency'))."</td>";					
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['voucher'], $this->config->get('config_currency'))."</td>";
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format('-' . ($result['commission']), $this->config->get('config_currency'))."</td>";				
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['total'], $this->config->get('config_currency'))."</td>";
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format('-' . ($result['prod_costs']), $this->config->get('config_currency'))."</td>";
					$export_xls_customer_list .= "<td align='right'>".$this->currency->format($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher'], $this->config->get('config_currency'))."</td>";
					if (($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']) > 0) {				
					$export_xls_customer_list .= "<td align='right'>".round(100 * ($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']) / ($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']), 2) . '%'."</td>";
					} else {
					$export_xls_customer_list .= "<td align='right'>".'0%'."</td>";
					}						
					$export_xls_customer_list .="</tr>";
					$export_xls_customer_list .="<tr>";
					$export_xls_customer_list .= "<td colspan='2'></td>";
					$export_xls_customer_list .= "<td colspan='17' align='center'>";
						$export_xls_customer_list .="<table border='1'>";
						$export_xls_customer_list .="<tr>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_customer_order_id')."</td>";					
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_customer_date_added')."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_customer_inv_no')."</td>";								
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_customer_cust_id')."</td>";				
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_billing_name'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_billing_company'))."</td>";									
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_billing_address_1'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_billing_address_2'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_billing_city'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_billing_zone'))."</td>";						
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_billing_postcode'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_billing_country'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_customer_telephone')."</td>";	
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_shipping_name'))."</td>";						
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_shipping_company'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_shipping_address_1'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_shipping_address_2'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_shipping_city'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_shipping_zone'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_shipping_postcode'))."</td>";
						$export_xls_customer_list .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".strip_tags($this->language->get('column_shipping_country'))."</td>";						
						$export_xls_customer_list .="</tr>";
						$export_xls_customer_list .="<tr>";
						$export_xls_customer_list .= "<td align='left'>".$result['customer_ord_idc']."</td>";					
						$export_xls_customer_list .= "<td align='left'>".$result['customer_order_date']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['customer_inv_no']."</td>";						
						$export_xls_customer_list .= "<td align='left'>".$result['customer_cust_idc']."</td>";						
						$export_xls_customer_list .= "<td align='left'>".$result['billing_name']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['billing_company']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['billing_address_1']."</td>";					
						$export_xls_customer_list .= "<td align='left'>".$result['billing_address_2']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['billing_city']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['billing_zone']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['billing_postcode']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['billing_country']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['customer_telephone']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['shipping_name']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['shipping_company']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['shipping_address_1']."</td>";					
						$export_xls_customer_list .= "<td align='left'>".$result['shipping_address_2']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['shipping_city']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['shipping_zone']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['shipping_postcode']."</td>";
						$export_xls_customer_list .= "<td align='left'>".$result['shipping_country']."</td>";
						$export_xls_customer_list .="</tr>";					
						$export_xls_customer_list .="</table>";
				$export_xls_customer_list .="</td>";
				$export_xls_customer_list .="</tr></table>";				
				}
				$export_xls_customer_list .="</body></html>";

			$filename = "sale_profit_report_customer_list_".date("Y-m-d",time());
			header('Expires: 0');
			header('Cache-control: private');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');			
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8; encoding=UTF-8');			
			header('Content-Disposition: attachment; filename='.$filename.".xls");
			header('Content-Transfer-Encoding: UTF-8');
			print $export_xls_customer_list;			
			exit;
?>