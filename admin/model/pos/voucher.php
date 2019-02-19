<?php
class ModelPosVoucher extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if(isset($this->session->data['voucher2'])) {
			$this->load->model('sale/order');
			$kk = 0;
			foreach ($this->session->data['order_idsx'] as $order_idss) {
				$_order_totals = $this->model_sale_order->getOrderTotals($order_idss);
				foreach($_order_totals as $_order_total) {
					if($_order_total['code']=='voucher') {


						$amount = $_order_total['value'];
							//break;

						$total_data[] = array(
							'code'       => 'voucher',
							'title'      => 'Voucher('.$this->session->data['voucher2'][$kk].')',
							'text'       => $this->currency->format(-$amount),
							'value'      => $amount,
							'sort_order' => $this->config->get('voucher_sort_order')
							);

						$total += $amount;
						$kk++;
					}

				}
			}
			
		}


		if (isset($this->session->data['voucher'])) {
			$this->session->data['xvoucher'] = array();
			foreach ($this->session->data['voucher'] as $voucherN) {

				$this->language->load('total/voucher');


				$voucher_info = $this->getVoucher($voucherN);

				if ($voucher_info) {
					if ((float) $voucher_info['amount'] >= (float) $total) {
						$amount = $total;
					} else {
						$amount = $voucher_info['amount'];	
					}

					$this->session->data['xvoucher'][] = array(
						'description' => '$'.number_format($voucher_info['amount'],2).' Gift Certificate for '.$voucher_info['from_name'],
						'code'=>$voucher_info['code'],
						'from_name'        => $voucher_info['from_name'],
						'from_email'       => $voucher_info['from_email'],
						'to_name'          => $voucher_info['to_name'],
						'to_email'         => $voucher_info['to_email'],
						'voucher_theme_id' => $voucher_info['voucher_theme_id'],

						'message'          => $voucher_info['message'],

						'amount'           => $amount,
						'voucher_id'		=> $voucher_info['voucher_id']
					);
					$total_data[] = array(
						'code'       => 'voucher',
						'title'      => 'Voucher(' . $voucherN . ')',
						'text'       => $this->currency->format($amount),
						'value'      => -$amount,
						'sort_order' => $this->config->get('voucher_sort_order')
						);

					$total -= $amount;
				}
			}
		}

	}
	
	

	public function confirm($order_info, $order_total) {
		$code = '';

		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');

		if ($start && $end) {  
			$code = substr($order_total['title'], $start, $end - $start);
		}	

		$this->load->model('checkout/voucher');

		$voucher_info = $this->model_checkout_voucher->getVoucher($code);

		if ($voucher_info) {
			$this->model_checkout_voucher->redeem($voucher_info['voucher_id'], $order_info['order_id'], $order_total['value']);	
		}						
	}	

	public function getVoucher($code) {
		$status = true;
		
		$voucher_query = $this->db->query("SELECT *, vtd.name AS theme FROM " . DB_PREFIX . "voucher v LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (v.voucher_theme_id = vt.voucher_theme_id) LEFT JOIN " . DB_PREFIX . "voucher_theme_description vtd ON (vt.voucher_theme_id = vtd.voucher_theme_id) WHERE v.code = '" . $this->db->escape($code) . "' AND vtd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND v.status = '1'");
		
		if ($voucher_query->num_rows) {
			if ($voucher_query->row['order_id']) {
				
				$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$voucher_query->row['order_id'] . "' AND order_status_id IN( '" . (int)$this->config->get('config_complete_status_id') . "','22','7','11')");

				if (!$order_query->num_rows) {
					$status = false;
				}

				$order_voucher_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$voucher_query->row['order_id'] . "' AND voucher_id = '" . (int)$voucher_query->row['voucher_id'] . "'");

				if (!$order_voucher_query->num_rows) {
					$status = false;
				}				
			}
			
			$voucher_history_query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "voucher_history` vh WHERE vh.voucher_id = '" . (int)$voucher_query->row['voucher_id'] . "' GROUP BY vh.voucher_id");

			if ($voucher_history_query->num_rows) {
				$amount = $voucher_query->row['amount'] + $voucher_history_query->row['total'];
			} else {
				$amount = $voucher_query->row['amount'];
			}
			
			if ($amount <= 0) {
				$status = false;
			}	
		} else {
			$status = false;
		}
		
		if ($status) {
			return array(
				'voucher_id'       => $voucher_query->row['voucher_id'],
				'code'             => $voucher_query->row['code'],
				'from_name'        => $voucher_query->row['from_name'],
				'from_email'       => $voucher_query->row['from_email'],
				'to_name'          => $voucher_query->row['to_name'],
				'to_email'         => $voucher_query->row['to_email'],
				'voucher_theme_id' => $voucher_query->row['voucher_theme_id'],
				'theme'            => $voucher_query->row['theme'],
				'message'          => $voucher_query->row['message'],
				'image'            => $voucher_query->row['image'],
				'amount'           => $amount,
				'status'           => $voucher_query->row['status'],
				'date_added'       => $voucher_query->row['date_added']
				);
		}
	}	
}
?>