<?php
class ModelPosSlip extends Model {
	public function createSlip ($orderIds) {
		$this->load->model('sale/order');
		$this->load->model('pos/extension');
		
		$orderIds = explode(",", $orderIds);
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$link = HTTPS_CATALOG;
			$server = HTTPS_IMAGE;
		} else {	        		
			$link = HTTP_CATALOG;
			$server = HTTP_IMAGE;
		}
		/*Setting Temp Data Variables*/
		$orders = '';
		$pageHeight = 378;
		$subTotal = 0;
		$shipTotal = 0;
		$taxTotal = 0;
		$totalTotal = 0;
		$vouchers = array ();
		foreach ($orderIds as $order_id) {
			$order_info = $this->model_sale_order->getOrder($order_id);
			$orders .= '<div class="order-info top-line">';
			$orders .= '<h4>Order ' . (($order_info['ref_order_id'])? $order_info['ref_order_id'] : $order_id) . '</h4>';
			$orders .= '<img src="' . $link . 'barcode.php?text='. $order_id .'&size=50" alt="barcode">';
			$orders .= '<table class="products" cellspacing="0" cellpadding="0">';
			$orders .= '<tbody>';
			$name = $this->user->userHavePermission('firstname');
			if(strlen($this->user->userHavePermission('lastname'))>0)
			{
				$name = $name.' '.substr($this->user->userHavePermission('lastname'),0,1).'.';
			}
			$pageHeight += 145;
			foreach ($this->cart->getProducts($order_id) as $product) {
				$pageHeight += 10;
				$nameArray = str_split($product['name'], 20);
				$lines = count($nameArray);
				foreach ($nameArray as $key => $nameChunk) {
					if ((substr($nameChunk, -1, 1) != ' ' || substr($nameArray[($key + 1)], 0, 1) != ' ') && $lines != ($key + 1)) {
						$nameArray[$key] .= '-<br>';
					}
				}
				$pageHeight += ($lines + 1) * 12;
				$orders .= '<tr><td class="first-child">'. $product['quantity'] .'</td><td>' . implode('', $nameArray) . '<br>'. $product['model'] .' <span>($'. number_format($product['price'], 2) .')</span></td><td class="last-child">$'. number_format($product['price'] * $product['quantity'], 2) .'</td></tr>';
			}
			/*Getting Order Totals*/
			$sort_order = array();
			$results = $this->model_pos_extension->getExtensions('total');
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			array_multisort($sort_order, SORT_ASC, $results);
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('pos/' . $result['code']);
					$this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
				$sort_order = array();
				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
				array_multisort($sort_order, SORT_ASC, $total_data);
			}
			/*Getting Order Totals for Admin*/
			if ($order_info['admin_view_only'] == 1) {
				$order_total = $this->model_sale_order->getOrderTotals($order_id);
				$total_data = array();
				$m = 0;
				foreach ($order_total as $o_total) {
					$total_data[$m]['code'] = $o_total['code'];
					$total_data[$m]['title'] = $o_total['title'];
					$total_data[$m]['text'] = $o_total['text'];
					$total_data[$m]['value'] = $o_total['value'];
					$total_data[$m]['sort_order'] = $o_total['sort_order'];
					$m++;
				}
			}
			$order_totalx = $this->model_sale_order->getOrderTotals($order_id);
			foreach ($order_totalx as $o_total) {
				if ($o_total['code'] == 'sub_total') {
					$subTotal += $o_total['value'];
				}
				if ($o_total['code'] == 'shipping') {
					$shipTotal += $o_total['value'];
				}
				if ($o_total['code'] == 'tax') {
					$taxTotal += $o_total['value'];
				}
				if ($o_total['code'] == 'voucher') {
					$vouchers[] = array('name' => $o_total['title'], 'value' => $o_total['value']);	          				
				}
				if ($o_total['code'] == 'total') {
					$totalTotal += $o_total['value'];
				}				
			}
			$orders .= '</tbody>';
			$orders .= '</table>';
			$orders .= '</div>';
		}
		
		
		$totals = array();
		if ($subTotal) {
			$totals[] = array (
				'code' => 'subtotal',
				'name' => 'SubTotal',
				'value' => '$' . number_format($subTotal, 2),
				);
		}
		if ($shipTotal) {
			$totals[] = array (
				'code' => 'shipping',
				'name' => 'Shipping',
				'value' => '$' . number_format($shipTotal, 2),
				);
		}
		if ($taxTotal) {
			$totals[] = array (
				'code' => 'tax',
				'name' => 'Sales Tax 8.1%',
				'value' => '$' . number_format($taxTotal, 2),
				);
		}
		if ($vouchers) {
			foreach ($vouchers as $voucher) {
				$totals[] = array (
					'code' => 'voucher',
					'name' => $voucher['name'],
					'value' => '$' . number_format($voucher['value'], 2),
					);
			}
		}
		if ($totalTotal) {
			$totals[] = array (
				'code' => 'total',
				'class' => 'huge',
				'name' => 'Total',
				'value' => '$' . number_format($totalTotal, 2),
				);
		}
		$totals = $this->getTotal($totals, $order_info['order_id']);
		$totalHtml = '';
		$totalHtml .= '<div class="total-info top-line">';
		$totalHtml .= '<table class="totals" cellspacing="0" cellpadding="0">';
		$totalHtml .= '<tbody>';
		foreach ($totals as $totalx) {
			$pageHeight += 21;
			$totalHtml .= '<tr'. (($totalx['class'] == 'huge')? ' class="huge"' : '' ) .'><td class="first-child">'. $totalx['name'] .'</td><td class="last-child">' . $totalx['value'] . '</td></tr>';
		}
		$totalHtml .= '</tbody>';
		$totalHtml .= '</table>';
		$totalHtml .= '</div>';
		$fileLogo = str_replace('phonepartsusalogo1-1.png', 'logo-black.png', $this->config->get('config_logo'));
		if ($fileLogo && file_exists(DIR_IMAGE . $fileLogo)) {
			$logo = $server . $fileLogo;
		} else if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$logo = $server . $this->config->get('config_logo');
		} else {
			$logo = '';
		}
		$style = '<style>'.
		'h4 {margin: 0; padding:0; font-size: 20px; font-weight: bold;}'.
		'h6 {margin: 0; padding:0; font-size: 14px;}'.
		'h5 {margin: 10px; padding:0; font-size: 12px;}'.
		'.container {width: 288px; box-sizing: border-box; padding: 20px; text-transform: uppercase; font-family: Courier;}'.
		'.time-info, .address, .order-info, .totals, .footer {font-family: arial;}'.
		'.time-info, .logo, .logo img, .address, .order-info, .total-info, .footer {width: 248px;}'.
		'.logo {margin-bottom: 10px; }'.
		'.address {text-align: center; font-size: 12px; line-height: 1.5; margin-bottom: 10px;}'.
		'.top-line {border-top: 1px solid #000;}'.
		'.time-info {font-size: 12px; padding: 10px 0px; text-align: center;}'.
		'.order-info {padding: 20px 0px; text-align: center;}'.
		'.order-info img {margin: 10px 0px; height: 40px;width:150px;}'.
		'.order-info .products {font-size: 11px; text-align: left;}'.
		'.order-info .products tr td {padding-bottom: 10px; width: 150px;}'.
		'.order-info .products tr td.first-child {width: 28px;}'.
		'.order-info .products tr td.last-child {width: 58px; text-align: right;}'.
		'.totals {margin-top: 10px; font-size: 15px;}'.
		'.totals tr td {padding: 3px 0;}'.
		'.totals tr td.first-child {text-align: left; width: 155px;}'.
		'.totals tr td.last-child {text-align: right; width: 93px;}'.
		'.totals tr.huge td {font-weight: bold; font-size: 18px; padding: 10px 0;}'.
		'.footer {text-align: center; margin-top: 30px;}'.
		'.footer span.small {margin: 20px 0; font-size:10px; text-transform: Auto;}'.
		'</style>';
		$header = '<page backtop="0mm" backleft="0mm" backbottom="0mm">';
		$header .= '<html>';
		$header .= '<head>';
		$header .= $style;
		$header .= '</head>';
		$header .= '<body>';
		$header .= '<div class="container">';
		$header .= '<div class="header">';
		$header .= '<div class="logo">';
		$header .= '<img src="' . $logo . '" alt="Phonepartsusa.com">';
		$header .= '</div>';
		$header .= '<div class="address">';
		// $header .= '<br>';
		$header .= '5145 SOUTH ARVILLE STREET, SUITE A<br>';
		$header .= 'LAS VEGAS, NEVADA 89118<br>';
		$header .= '855.213.5588<br>';
		$header .= 'MONDAY - FRIDAY 9:30AM - 4:30PM';
		$header .= '</div>';
		$header .= '<div class="time-info top-line">';
		$header .= date('m/d/y h:i A') .'<br>';
		$header .= $name;
		$header .= '</div>';
		$header .= '</div>';
		$footer = '';
		$footer .= '<div class="footer top-line">';
		$footer .= '<span class="small">Customers are responsible for inspecting item(s) during order pickup. Merchandise may be returned within 60 days of purchase date. Returned item(s) must be in original purchase condition and free of damages. For full return policy, please visit our website.</span>';
		$footer .= '<h5>WWW.PHONEPARTSUSA.COM</h5>';
		$footer .= '<h6>THANK YOU FOR YOUR BUSINESS</h6>';
		$footer .= '</div>';
		$footer .= '</div>';
		$footer .= '</body>';
		$footer .= '</html>';
		$footer .= '</page>';
		$html = $header . $orders . $totalHtml . $footer;
		$pageHeight = $pageHeight / 96;
		$pageHeight = explode('.', $pageHeight);
		if ($pageHeight[1][0] > 5) {
			$pageHeight = $pageHeight[0] + 1.3;
		} else {
			$pageHeight = $pageHeight[0] + 1;
		}
		
		return $data = array('html' => $html, 'pageHeight' => $pageHeight * 25.4, 'pageWidth' => 3 * 25.4);
		
	}

	public function createRMASlip ($return_id) {
		$this->load->model('sale/rma');
		$this->load->model('pos/extension');
		
		$orderIds = explode(",", $orderIds);
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$link = HTTPS_CATALOG;
			$server = HTTPS_IMAGE;
		} else {	        		
			$link = HTTP_CATALOG;
			$server = HTTP_IMAGE;
		}
		/*Setting Temp Data Variables*/
		$return = '';
		$pageHeight = 378;
		$totalTotal = 0;
		$vouchers = array ();

		$return_info = $this->model_sale_rma->getRMADetails($return_id);
		$return .= '<div class="order-info top-line">';
		$return .= '<h3>RMA ' . $return_info['rma_number'] . '</h3>';
		$return .= '<h4>Order ' . $return_info['order_id'] . '</h4>';
		$return .= '<img src="' . $link . 'barcode.php?text='. $return_info['order_id'] .'&size=50" alt="barcode">';
		$return .= '<table class="products" cellspacing="0" cellpadding="0">';
		$return .= '<tbody>';
		$name = $this->user->userHavePermission('firstname');
		if(strlen($this->user->userHavePermission('lastname'))>0)
		{
			$name = $name.' '.substr($this->user->userHavePermission('lastname'),0,1).'.';
		}
		$pageHeight += 175;
		$total_tax_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$return_info['order_id']."' AND code='tax'");
		$is_tax_valid = $total_tax_query->row['value'];
		foreach ($this->model_sale_rma->getRMAProducts($return_id) as $product) {
			$pageHeight += 10;
			$nameArray = str_split($product['title'], 20);
			$lines = count($nameArray);
			foreach ($nameArray as $key => $nameChunk) {
				if ((substr($nameChunk, -1, 1) != ' ' || substr($nameArray[($key + 1)], 0, 1) != ' ') && $lines != ($key + 1)) {
					$nameArray[$key] .= '-<br>';
				}
			}
			$pageHeight += ($lines + 1) * 12;
			$return .= '<tr><td class="first-child">'. $product['quantity'] .'</td><td>' . implode('', $nameArray) . '<br>'. $product['sku'] .' <span>($'. number_format($product['price'], 2) .')</span></td><td class="last-child">$'. number_format($product['price'] * $product['quantity'], 2) .'</td></tr>';
			$totalTotal += number_format($product['price'] * $product['quantity'], 2);
		}
		$total_tax=0;
		if ($is_tax_valid) {
		$total_tax = $totalTotal*(0.081);
		}
		$return .= '</tbody>';
		$return .= '</table>';
		$return .= '</div>';

		$totals = array();
		if ($totalTotal) {
			$totals[] = array (
				'code' => 'subtotal',
				'name' => 'SubTotal',
				'value' => '$' . number_format($totalTotal, 2),
				);
			if ($is_tax_valid) {
			$totals[] = array (
				'code' => 'tax',
				'name' => 'Sales Tax(8.1%)',
				'value' => '$' . number_format($total_tax, 2),
				);
			}	
			$totals[] = array (
				'code' => 'total',
				'class' => 'huge',
				'name' => 'Total',
				'value' => '$' . number_format(($totalTotal+$total_tax), 2),
				);
		}
		$query = $this->db->query("SELECT sum(`price`) as `price`, `action` FROM inv_return_decision WHERE return_id='".(int)$return_id."'");

		foreach ($query->rows as $value) {
			$totals[] = array (
				'code' => strtolower(str_replace(' ', '-', $value['action'])),
				'class' => 'huge',
				'name' => $value['action'],
				'value' => '$' . number_format(($value['price']+$total_tax), 2),
				);
		}

		$query = $this->db->query("SELECT ov.`code` FROM oc_voucher ov INNER JOIN inv_voucher_details ivd ON (ov.voucher_id = ivd.voucher_id) WHERE rma_number = '". $return_info['rma_number'] ."'");

		foreach ($query->rows as $value) {
			$totals[] = array (
				'code' => strtolower(str_replace(' ', '-', $value['action'])),
				'class' => 'huge',
				'name' => 'Voucher ' . $value['code'],
				'value' => '',
				);
		}

		$totalHtml = '';
		$totalHtml .= '<div class="total-info top-line">';
		$totalHtml .= '<table class="totals" cellspacing="0" cellpadding="0">';
		$totalHtml .= '<tbody>';
		foreach ($totals as $totalx) {
			$pageHeight += 21;
			$totalHtml .= '<tr'. (($totalx['class'] == 'huge')? ' class="huge"' : '' ) .'><td class="first-child">'. $totalx['name'] .'</td><td class="last-child">' . $totalx['value'] . '</td></tr>';
		}
		$totalHtml .= '</tbody>';
		$totalHtml .= '</table>';

		$totalHtml .= '<table class="signs" cellspacing="0" cellpadding="0">';
		$totalHtml .= '<tbody>';
			$pageHeight += 42;
			$totalHtml .= '<tr><td class="first-child">Name:__________</td><td class="last-child">ID#:________</td></tr>';
			$totalHtml .= '<tr><td class="first-child">Sign:___________</td><td class="last-child"></td></tr>';
		$totalHtml .= '</tbody>';
		$totalHtml .= '</table>';
		$totalHtml .= '</div>';
		$fileLogo = str_replace('phonepartsusalogo1-1.png', 'logo-black.png', $this->config->get('config_logo'));
		if ($fileLogo && file_exists(DIR_IMAGE . $fileLogo)) {
			$logo = $server . $fileLogo;
		} else if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$logo = $server . $this->config->get('config_logo');
		} else {
			$logo = '';
		}
		$style = '<style>'.
		'h3 {margin: 0; padding:0; font-size: 26px; font-weight: bold;}'.
		'h4 {margin: 0; padding:0; font-size: 20px; font-weight: bold;}'.
		'h6 {margin: 0; padding:0; font-size: 14px;}'.
		'h5 {margin: 10px; padding:0; font-size: 12px;}'.
		'.container {width: 288px; box-sizing: border-box; padding: 20px; text-transform: uppercase; font-family: Courier;}'.
		'.time-info, .address, .order-info, .totals, .signs, .footer {font-family: arial;}'.
		'.time-info, .logo, .logo img, .address, .order-info, .total-info, .footer {width: 248px;}'.
		'.logo {margin-bottom: 10px; }'.
		'.address {text-align: center; font-size: 12px; line-height: 1.5; margin-bottom: 10px;}'.
		'.top-line {border-top: 1px solid #000;}'.
		'.time-info {font-size: 12px; padding: 10px 0px; text-align: center;}'.
		'.order-info {padding: 20px 0px; text-align: center;}'.
		'.order-info img {margin: 10px 0px; height: 40px;width:150px;}'.
		'.order-info .products {font-size: 11px; text-align: left;}'.
		'.order-info .products tr td {padding-bottom: 10px; width: 150px;}'.
		'.order-info .products tr td.first-child {width: 28px;}'.
		'.order-info .products tr td.last-child {width: 58px; text-align: right;}'.
		'.totals {margin-top: 10px; font-size: 15px;}'.
		'.totals tr td {padding: 3px 0;}'.
		'.totals tr td.first-child {text-align: left; width: 155px;}'.
		'.totals tr td.last-child {text-align: right; width: 93px;}'.
		'.totals tr.huge td {font-weight: bold; font-size: 18px; padding: 10px 0;}'.
		'.signs {margin-top: 10px; font-size: 15px;}'.
		'.signs tr td {padding: 3px 0; width: 124px;}'.
		'.signs tr td.first-child {text-align: left;}'.
		'.signs tr td.last-child {text-align: right;}'.
		'.footer {text-align: center; margin-top: 30px;}'.
		'.footer span.small {margin: 20px 0; font-size:10px; text-transform: Auto;}'.
		'</style>';
		$header = '<page backtop="0mm" backleft="0mm" backbottom="0mm">';
		$header .= '<html>';
		$header .= '<head>';
		$header .= $style;
		$header .= '</head>';
		$header .= '<body>';
		$header .= '<div class="container">';
		$header .= '<div class="header">';
		$header .= '<div class="logo">';
		$header .= '<img src="' . $logo . '" alt="Phonepartsusa.com">';
		$header .= '</div>';
		$header .= '<div class="address">';
		// $header .= '<br>';
		$header .= '5145 SOUTH ARVILLE STREET, SUITE A<br>';
		$header .= 'LAS VEGAS, NEVADA 89118<br>';
		$header .= '855.213.5588<br>';
		$header .= 'MONDAY - FRIDAY 9:30AM - 4:30PM';
		$header .= '</div>';
		$header .= '<div class="time-info top-line">';
		$header .= date('m/d/y h:i A') .'<br>';
		$header .= $name;
		$header .= '</div>';
		$header .= '</div>';
		$footer = '';
		$footer .= '<div class="footer top-line">';
		$footer .= '<span class="small">Customers are responsible for inspecting item(s) during order pickup. Merchandise may be returned within 60 days of purchase date. Returned item(s) must be in original purchase condition and free of damages. For full return policy, please visit our website.</span>';
		$footer .= '<h5>WWW.PHONEPARTSUSA.COM</h5>';
		$footer .= '<h6>THANK YOU FOR YOUR BUSINESS</h6>';
		$footer .= '</div>';
		$footer .= '</div>';
		$footer .= '</body>';
		$footer .= '</html>';
		$footer .= '</page>';
		$html = $header . $return . $totalHtml . $footer;
		$pageHeight = $pageHeight / 96;
		$pageHeight = explode('.', $pageHeight);
		if ($pageHeight[1][0] > 5) {
			$pageHeight = $pageHeight[0] + 1.3;
		} else {
			$pageHeight = $pageHeight[0] + 1;
		}
		
		return $data = array('html' => $html, 'pageHeight' => $pageHeight * 25.4, 'pageWidth' => 3 * 25.4);
		
	}
	public function createPdf ($data = array(), $dir = '') {
		
		require_once(DIR_SYSTEM . 'html2_pdf_lib/html2pdf.class.php');
		$size = array ($data['pageWidth'], $data['pageHeight']);
		try {
			$html2pdf = new HTML2PDF('P', (($size)? $size : 'A4'), 'en', true, 'UTF-8', array('0','0','0','0'));
			$html2pdf->setDefaultFont('courier');
			$html2pdf->writeHTML($data['html']);
			$filename = time();
			$file = $dir .  $filename . '.pdf';
			$filePath = DIR_IMAGE . $file;
			$html2pdf->Output($filePath, 'F');
		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
		return $file;
	}
	public function printNodeSlip ($orderIds) {
		$orderIds = explode(',', $orderIds);
		foreach ($orderIds as $order_id) {
			if (strpos($order_id, '-') == false) {
				$sql = "SELECT pdf_receipt FROM `" . DB_PREFIX . "order` WHERE order_id = $order_id AND pdf_receipt <> '' GROUP BY pdf_receipt";
			} else {
				$sql = "SELECT pdf_receipt FROM `" . DB_PREFIX . "order` WHERE ref_order_id = $order_id AND pdf_receipt <> '' GROUP BY pdf_receipt";
			}

			$row = $this->db->query($sql)->row;
			$this->printNodePDF($row['pdf_receipt'], 'POS Receipt Print - '.$this->user->userHavePermission('firstname').' '.$this->user->userHavePermission('lastname'));
		}
	}
	public function printNodePDF($pdf, $title)	{
		if (strpos($pdf, DIR_IMAGE) === false) {
			$pdf = DIR_IMAGE . $pdf;
		}
		require_once(DIR_SYSTEM . 'PrintNode-PHP-master/vendor/autoload.php');
		// $credentials = 'f9305047bdf9a187cfc02de4780b8e0c7cb3261a'; /*Dev ID*/
		$credentials = '19982dc5978951c99f98cdcfe5feb4881cc5147b';
		$request = new PrintNode\Request($credentials);
		// $computers = $request->getComputers();
		$printers = $request->getPrinters();
    	// print_r($printers);exit;
		// $printJobs = $request->getPrintJobs();
		$printJob = new PrintNode\PrintJob();
		// $printJob->printer = 130442; //$printers[1]; /*Dev id*/
		// $printJob->printer = 130444; //$printers[1]; /*Dev id*/
		$printJob->printer = 136106;
		$printJob->contentType = 'pdf_base64';
		$printJob->content = base64_encode(file_get_contents($pdf));
		$printJob->source = 'My App/1.0';
		$printJob->title = $title;
		$response = $request->post($printJob);
		$statusCode = $response->getStatusCode();
		$statusMessage = $response->getStatusMessage();
	}
	public function getTotal($total_data, $order_id, $invoice = false)	{
		$this->load->model('sale/order');
		$order_info = $this->model_sale_order->getOrder($order_id);
		$name = 'name';
		$value = 'value';
		if ($invoice) {
			$name = 'title';
			$value = 'text';
		}
		$cardPaid = (float)$order_info['card_paid'];
		$cashPaid = (float)$order_info['cash_paid'];
		$paypalPay = (float)$order_info['paypal_paid'];
		$changeDue = (float)$order_info['change_due'];
		if ($cardPaid > 0) {
			$total_data[] = array (
				'code' => 'card',
				$name => 'Tendered Card',
				$value => '$' . number_format($cardPaid, 2),
				);
		}
		if ($paypalPay > 0) {
			$total_data[] = array (
				'code' => 'payaplpay',
				$name => 'Paypal Payment',
				$value => '$' . number_format($paypalPay, 2),
				);
		}
		if ($cashPaid > 0) {
			$total_data[] = array (
				'code' => 'cash',
				$name => 'Tendered Cash',
				$value => '$' . number_format($cashPaid, 2),
				);
		}
		if ($cashPaid > 0 || $paypalPay > 0 || $cardPaid > 0) {
			$total_data[] = array (
				'code' => 'change',
				'class' => 'huge',
				$name => 'Change',
				$value => '$' . number_format($changeDue, 2),
				);
		}
		return $total_data;
	}

	public function savePdf ($orderIds, $pdf)	{
		$this->db->query("UPDATE " . DB_PREFIX . "order SET pdf_receipt = '" . $pdf . "' WHERE order_id in ('" . str_replace(',', "','", $orderIds) . "')");
	}

	public function saveRMAPdf ($return_id, $pdf)	{
		$this->db->query("UPDATE inv_returns SET pdf_receipt = '" . $pdf . "' WHERE id = '". (int) $return_id ."'");
	}

	public function emailInvoice($orderIds)	{
		$this->load->model('sale/order');
		$this->load->model('pos/extension');
		
		$orderIds = explode(",", $orderIds);
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$link = HTTPS_CATALOG;
			$server = HTTPS_IMAGE;
		} else {	        		
			$link = HTTP_CATALOG;
			$server = HTTP_IMAGE;
		}
		/*Setting Temp Data Variables*/
		$orders = '';
		$subTotal = 0;
		$shipTotal = 0;
		$taxTotal = 0;
		$totalTotal = 0;
		$vouchers = array ();
		foreach ($orderIds as $order_id) {
			$order_info = $this->model_sale_order->getOrder($order_id);
			$orders .= '<tr>';
			$orders .= '<td align="center">';
			$orders .= '<h2 style="color:#424333; font-size: 30px; margin-top: 25px; margin-bottom: 30px;">ORDER ' . (($order_info['ref_order_id'])? $order_info['ref_order_id'] : $order_id) . '</h2>';
			$orders .= '</td>';
			$orders .= '</tr>';
			$orders .= '<tr>';
			$orders .= '<td>';
			$orders .= '<table style="text-align: left; padding-left: 30px; padding-right: 30px; max-width: 600px; width: 100%;">';
			$name = $this->user->userHavePermission('firstname');
			if(strlen($this->user->userHavePermission('lastname'))>0)
			{
				$name = $name.' '.substr($this->user->userHavePermission('lastname'),0,1).'.';
			}
			$products = $this->cart->getProducts($order_id);
			$kp = 1;
			foreach ($products as $product) {
				$orders .= '<tr>';
				$orders .= '<td style="' . (($kp == count($products))? 'border-bottom: 2px dashed #86aefe; ': '') . 'padding-top: 10px; padding-bottom: 10px; vertical-align: top; padding-right: 40px;">';
				$orders .= '<p style="font-size: 17px; margin-bottom:5px; color:#424333; margin-top: 0;">'. $product['name'] .'</p>';
				$orders .= '<strong style="display: inline-block; margin-bottom: 5px; margin-top: 5px;">'. $product['model'] .'</strong>';
				$orders .= '<p style="font-size: 17px; margin-bottom:5px; color:#424333; margin-top: 0;">$'. number_format($product['price'], 2) .' x '. $product['quantity'] .'</p>';
				$orders .= '</td>';
				$orders .= '<td style="' . (($kp == count($products))? 'border-bottom: 2px dashed #86aefe; ': '') . 'padding-top: 10px; padding-bottom: 10px; vertical-align: top; padding-right: 5px; padding-left: 5px;">$'. number_format($product['price'] * $product['quantity'], 2) .'</td>';
				$orders .= '</tr>';
				$kp++;
			}
			/*Getting Order Totals*/
			$sort_order = array();
			$results = $this->model_pos_extension->getExtensions('total');
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			array_multisort($sort_order, SORT_ASC, $results);
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('pos/' . $result['code']);
					$this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
				$sort_order = array();
				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
				array_multisort($sort_order, SORT_ASC, $total_data);
			}
			/*Getting Order Totals for Admin*/
			if ($order_info['admin_view_only'] == 1) {
				$order_total = $this->model_sale_order->getOrderTotals($order_id);
				$total_data = array();
				$m = 0;
				foreach ($order_total as $o_total) {
					$total_data[$m]['code'] = $o_total['code'];
					$total_data[$m]['title'] = $o_total['title'];
					$total_data[$m]['text'] = $o_total['text'];
					$total_data[$m]['value'] = $o_total['value'];
					$total_data[$m]['sort_order'] = $o_total['sort_order'];
					$m++;
				}
			}
			$order_totalx = $this->model_sale_order->getOrderTotals($order_id);
			foreach ($order_totalx as $o_total) {
				if ($o_total['code'] == 'sub_total') {
					$subTotal += $o_total['value'];
				}
				if ($o_total['code'] == 'shipping') {
					$shipTotal += $o_total['value'];
				}
				if ($o_total['code'] == 'tax') {
					$taxTotal += $o_total['value'];
				}
				if ($o_total['code'] == 'voucher') {
					$vouchers[] = array('name' => $o_total['title'], 'value' => $o_total['value']);	          				
				}
				if ($o_total['code'] == 'total') {
					$totalTotal += $o_total['value'];
				}				
			}
			$orders .= '</table>';
			$orders .= '</td>';
			$orders .= '</tr>';
		}
		
		
		$totals = array();
		if ($subTotal) {
			$totals[] = array (
				'code' => 'subtotal',
				'class' => 'huge',
				'name' => 'SubTotal',
				'value' => '$' . number_format($subTotal, 2),
				);
		}
		if ($shipTotal) {
			$totals[] = array (
				'code' => 'shipping',
				'name' => 'Shipping',
				'value' => '$' . number_format($shipTotal, 2),
				);
		}
		if ($taxTotal) {
			$totals[] = array (
				'code' => 'tax',
				'name' => 'Sales Tax 8.1%',
				'value' => '$' . number_format($taxTotal, 2),
				);
		}
		if ($vouchers) {
			foreach ($vouchers as $voucher) {
				$totals[] = array (
					'code' => 'voucher',
					'name' => $voucher['name'],
					'value' => '$' . number_format($voucher['value'], 2),
					);
			}
		}
		if ($totalTotal) {
			$totals[] = array (
				'code' => 'total',
				'class' => 'huge',
				'name' => 'Total',
				'value' => '$' . number_format($totalTotal, 2),
				);
		}
		$totals = $this->getTotal($totals, $order_info['order_id']);
		$totalHtml = '';
		$totalHtml .= '<tr>';
		$totalHtml .= '<td style="padding-bottom: 30px;">';
		$totalHtml .= '<table style="text-align: left; width:100%; padding-left: 30px; padding-right: 30px; max-width: 600px;">';
		foreach ($totals as $totalx) {
			if ($totalx['class'] == 'huge') {
				$totalHtml .= '<tr>';
				$totalHtml .= '<td style=" padding-top: 20px; font-weight: 600; color:#424333; font-size: 25px; width:80%;">'. $totalx['name'] .'</td>';
				$totalHtml .= '<td style=" padding-top: 20px; color:#424333; font-size: 25px; width:20%;">' . $totalx['value'] . '</td>';
				$totalHtml .= '</tr>';
			} else {
				$totalHtml .= '<tr>';
				$totalHtml .= '<td style=" padding-top: 10px; color:#424333; font-size: 28px; padding-left: 15px; width:80%;">'. $totalx['name'] .'</td>';
				$totalHtml .= '<td style=" padding-top: 10px; color:#424333; font-size: 25px; width:20%;">' . $totalx['value'] . '</td>';
				$totalHtml .= '</tr>';
			}
		}
		$totalHtml .= '</table>';
		$totalHtml .= '</td>';
		$totalHtml .= '</tr>';
		$header = '';
		// $header .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		// $header .= '<html xmlns="http://www.w3.org/1999/xhtml">';
		// $header .= '<head>';
		// $header .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		// $header .= '<title>PPUSA ORDER EMAIL</title>';
		// $header .= '</head>';
		$header .= '<body style=" background-color: #777; margin: 0; padding: 0; min-width: 100%!important; font-family: helvetica, sans-serif">';
		$header .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="text-align: center;">';
		$header .= '<tr>';
		$header .= '<td>';
		$header .= '<div style="background-image:url(' . $server . 'white-curve.png); background-repeat: repeat-x; height: 15px; display: block; width: 600px; margin: 5px auto 0 auto;"></div>';
		$header .= '<div style="background-color: #fff; margin: 0 auto; max-width: 600px; box-shadow: 0px 4px 5px rgba(0, 0, 0, 0.1);"><table class="content" style="width: 100%; table-layout: fixed; max-width: 600px;" align="center" cellpadding="0" cellspacing="0" border="0">';
		$header .= '<tr>';
		$header .= '<td style="padding-top:95px; padding-bottom: 95px;">';
		$header .= '<a href="' . $link . '"><img src="'. $server .'email-logo.png" alt="" style="max-width: 500px; margin-left:50px; margin-right:50px;" /></a>';
		$header .= '</td>';
		$header .= '</tr>';
		$header .= '<tr>';
		$header .= '<td>';
		$header .= '<span style="display:block; text-align: center; max-height: 22px;"><img src="' . $server . 'caret.jpg" alt="" /></span>';
		$header .= '<table width="100%" bgcolor="#fff" border="0" cellpadding="0" cellspacing="0">';
		$header .= '<tr>';
		$header .= '<td style="background-color: #6085fa; text-align: center; padding-top: 30px; position: relative;">';
		$header .= '<h1 style="font-size: 50px; font-weight: normal; margin: 0; color:#fff; margin-bottom: 30px;"><small style="display: inline-block; vertical-align: super; font-size: 33px;">$</small>'. number_format($totalTotal, 2) .'</h1>';
		$header .= '<p style="font-size:16px; margin: 0; padding:3px; background-color: #5471cb; color:#fff;">'. date('m/d/y h:i A') .'</p>';
		$header .= '</td>';
		$header .= '</tr>';
		$header .= '</table>';
		$header .= '</td>';
		$header .= '</tr>';
		$footer = '';
		$footer .= '<tr>';
		$footer .= '<td>';
		$footer .= '<table style=" background-color: #424333; background-image:url(' . $server . 'grey-curve.jpg); background-repeat: repeat-x; max-width: 600px; width:100%; padding:25px; padding-top: 60px;">';
		$footer .= '<tr>';
		$footer .= '<td align="center">';
		$footer .= '<p style="color:#fff; line-height: 22px; font-size: 16px; margin-top: 0; margin-bottom: 5px;">';
		$footer .= 'Customers are responsible for inspecting item(s) during order pick up. ';
		$footer .= 'Merchandise may be returned within 60 days of purchase date. ';
		$footer .= 'Returned item(s) must be in original purchase ';
		$footer .= 'condition and free of damages. For full return policy, please visit <br>';
		$footer .= '<a href="'. $link .'returnpolicy" style="color:#fff;">https://phonepartsusa.com/returnpolicy</a>';
		$footer .= '</p>';
		$footer .= '<p style="color:#72726e; font-size: 12px; max-width: 340px; text-align: center; margin-bottom: 15px;">';
		$footer .= '© 2016 PhonePartsUSA, LLC. All rights reserved <br />';
		$footer .= '5145 S Arville Street, Suite A<br>';
		$footer .= 'Las Vegas, NV 89118';
		$footer .= '</p>';
		$footer .= '<p style="color:#72726e; font-size: 12px; max-width: 340px; text-align: center; margin-bottom: 15px;">';
		$footer .= '<a href="https://phonepartsusa.com/privacy-policy" style="color:#72726e; font-size: 12px; display: block;">PhonePartsUSA Privacy Policy</a>';
		// $footer .= '<a href="" style="color:#72726e;">Map data</a> © <a href="" style="color:#72726e;">OpenStreetMap</a> contributors';
		// $footer .= '<a href="" style="color:#72726e;">Not your receipt</a>';
		$footer .= '</p>';
		// $footer .= '<p style="color:#72726e; font-size: 12px; max-width: 340px; text-align: center; margin-bottom: 15px;">';
		// $footer .= '<a href="" style="color:#72726e;">Manage preferences </a> for digital receipt.';
		// $footer .= '</p>';
		$footer .= '</td>';
		$footer .= '</tr>';
		$footer .= '</table></div>';
		$footer .= '</td>';
		$footer .= '</tr>';
		$footer .= '</table>';
		$footer .= '</td>';
		$footer .= '</tr>';
		$footer .= '</table>';
		$footer .= '</body>';
		// $footer .= '</html>';
		$html = $header . $orders . $totalHtml . $footer;
		
		$mail = new Mail(); 
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');			
		$mail->setTo($order_info['email']);
		//$mail->setTo('shanklq@gmail.com');
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($order_info['store_name']);
		$mail->setSubject(html_entity_decode('Your Order is Picked Up! - PhonePartsUSA', ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($html);
		$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
		$mail->send();
		// file_put_contents('file.html', $html);
	}
	public function createDrawerSlip ($drawerID) {
		$drawer = $this->db->query("SELECT * FROM `oc_close_drawer` WHERE id = '". (int) $drawerID ."'")->row;		
		$pageHeight = 1100;
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_IMAGE;
		} else {
			$server = HTTP_IMAGE;
		}
		$fileLogo = str_replace('phonepartsusalogo1-1.png', 'logo-black.png', $this->config->get('config_logo'));
		if ($fileLogo && file_exists(DIR_IMAGE . $fileLogo)) {
			$logo = $server . $fileLogo;
		} else if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$logo = $server . $this->config->get('config_logo');
		} else {
			$logo = '';
		}
		$name = $this->user->userHavePermission('firstname');
		if(strlen($this->user->userHavePermission('lastname'))>0) {
			$name = $name.' '.substr($this->user->userHavePermission('lastname'),0,1).'.';
		}
		$style = '<style>'.
		'h4 {margin: 0; padding:0; font-size: 20px; font-weight: bold;}'.
		'h6 {margin: 0; padding:0; font-size: 14px;}'.
		'h5 {margin: 10px; padding:0; font-size: 12px;}'.
		'.container {width: 288px; box-sizing: border-box; padding: 20px; text-transform: uppercase; font-family: Courier;}'.
		'.time-info, .address, .order-info, .totals, .footer {font-family: arial;}'.
		'.time-info, .logo, .logo img, .address, .order-info, .total-info, .footer {width: 248px;}'.
		'.logo {margin-bottom: 10px; }'.
		'.address {text-align: center; font-size: 12px; line-height: 1.5; margin-bottom: 10px;}'.
		'.top-line {border-top: 1px solid #000;}'.
		'.time-info {font-size: 12px; padding: 10px 0px; text-align: center;}'.
		'.order-info {padding: 20px 0px; text-align: center;}'.
		'.order-info img {margin: 10px 0px; height: 40px;width:150px;}'.
		'.order-info .products {font-size: 11px; text-align: left;}'.
		'.order-info .products tr td.bold {font-weight: bold;}'.
		'.order-info .products tr td {padding-bottom: 10px; width: 60px; text-align: right;}'.
		'.order-info .products tr td.first-child {width: 113px;}'.
		'.order-info .products tr td.last-child {width: 60px;}'.
		'.totals {margin-top: 10px; font-size: 15px;}'.
		'.totals tr td {padding: 3px 0;}'.
		'.totals tr td.first-child {text-align: left; width: 155px;}'.
		'.totals tr td.last-child {text-align: right; width: 93px;}'.
		'.right {text-align: right;}'.
		'.left {text-align: left !important;}'.
		'.totals tr.huge td {font-weight: bold; font-size: 18px; padding: 10px 0;}'.
		'.footer {text-align: center; margin-top: 30px;}'.
		'.footer span.small {margin: 20px 0; font-size:10px; text-transform: Auto;}'.
		'</style>';
		$header = '<page backtop="0mm" backleft="0mm" backbottom="0mm">';
		$header .= '<html>';
		$header .= '<head>';
		$header .= $style;
		$header .= '</head>';
		$header .= '<body>';
		$header .= '<div class="container">';
		$header .= '<div class="header">';
		$header .= '<div class="logo">';
		$header .= '<img src="' . $logo . '" alt="Phonepartsusa.com">';
		$header .= '</div>';
		$header .= '<div class="address">';
		// $header .= '<br>';
		$header .= '5145 SOUTH ARVILLE STREET, SUITE A<br>';
		$header .= 'LAS VEGAS, NEVADA 89118<br>';
		$header .= '855.213.5588<br>';
		$header .= 'MONDAY - FRIDAY 9:30AM - 4:30PM';
		$header .= '</div>';
		$header .= '<div class="time-info top-line">';
		$header .= date('m/d/y h:i A') .'<br>';
		$header .= $name;
		$header .= '</div>';
		$header .= '</div>';
		$body = '';
		$body .= '<div class="order-info top-line">';
		$body .= '<table class="products" cellspacing="0" cellpadding="0">';
		$body .= '<tbody>';
		
		$body .= '<tr><td class="first-child">&nbsp;</td><td class="bold">Count</td><td class="last-child bold">Value</td></tr>';
		
		$body .= '<tr><td class="first-child left bold">Coins</td><td>&nbsp;</td><td class="last-child">&nbsp;</td></tr>';
		$body .= '<tr><td class="first-child">Pennies</td><td>'. (int) $drawer['pennies_count'] .'</td><td class="last-child">'. number_format($drawer['pennies_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">Nickles</td><td>'. (int) $drawer['nickles_count'] .'</td><td class="last-child">'. number_format($drawer['nickles_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">Dimes</td><td>'. (int) $drawer['dimes_count'] .'</td><td class="last-child">'. number_format($drawer['dimes_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">Quarters</td><td>'. (int) $drawer['quarters_count'] .'</td><td class="last-child">'. number_format($drawer['quarters_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">Half Dollars</td><td>'. (int) $drawer['half_dollars_count'] .'</td><td class="last-child">'. number_format($drawer['half_dollars_value'], 2) .'</td></tr>';
		
		$body .= '<tr><td class="first-child left bold">Bills</td><td>&nbsp;</td><td class="last-child">&nbsp;</td></tr>';
		$body .= '<tr><td class="first-child">1</td><td>'. (int) $drawer['ones_count'] .'</td><td class="last-child">'. number_format($drawer['one_dollar_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">2</td><td>'. (int) $drawer['twos_count'] .'</td><td class="last-child">'. number_format($drawer['two_dollar_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">5</td><td>'. (int) $drawer['fives_count'] .'</td><td class="last-child">'. number_format($drawer['five_dollar_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">10</td><td>'. (int) $drawer['tens_count'] .'</td><td class="last-child">'. number_format($drawer['ten_dollar_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">20</td><td>'. (int) $drawer['twenties_count'] .'</td><td class="last-child">'. number_format($drawer['twenty_dollar_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">50</td><td>'. (int) $drawer['fifties_count'] .'</td><td class="last-child">'. number_format($drawer['fifty_dollar_value'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child">100</td><td>'. (int) $drawer['hundreds_count'] .'</td><td class="last-child">'. number_format($drawer['hundred_dollar_value'], 2) .'</td></tr>';
		
		$body .= '</tbody>';
		$body .= '</table>';
		$body .= '</div>';
		$body .= '<div class="order-info top-line">';
		$body .= '<table class="products" cellspacing="0" cellpadding="0">';
		$body .= '<tbody>';

		$body .= '<tr><td class="first-child left bold">Cash Total</td><td>&nbsp;</td><td class="last-child">'. number_format($drawer['cash_total'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child left bold">Starting Cash</td><td>&nbsp;</td><td class="last-child">-'. number_format($drawer['starting_cash'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child left bold">Deposit Total</td><td>&nbsp;</td><td class="last-child" style="border-top: 1px solid #000;">'. number_format($drawer['deposit_total'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child left bold">Expected</td><td>&nbsp;</td><td class="last-child">'. number_format($drawer['expected'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child left bold">Over/Short</td><td>&nbsp;</td><td class="last-child" style="border-top: 1px solid #000;">'. number_format($drawer['over_short'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child left bold">&nbsp;</td><td>&nbsp;</td><td class="last-child">&nbsp;</td></tr>';
		$body .= '<tr><td class="first-child left bold">Card Total</td><td>&nbsp;</td><td class="last-child">'. number_format($drawer['credit_card_total'], 2) .'</td></tr>';
		$body .= '<tr><td class="first-child left bold">PayPal Total</td><td>&nbsp;</td><td class="last-child">'. number_format($drawer['paypal_total'], 2) .'</td></tr>';
		
		$body .= '</tbody>';
		$body .= '</table>';
		$body .= '</div>';

		$footer = '';
		$footer .= '<div class="footer top-line">';
		$footer .= '<h5 class="right">'. $drawer['msg'] .'</h5>';
		$footer .= '<h5 class="right"><br><br>Cashier: ________________</h5>';
		$footer .= '<h5 class="right"><br><br>Manager: ________________</h5>';
		$footer .= '<br>';
		$footer .= '<h5>WWW.PHONEPARTSUSA.COM</h5>';		
		$footer .= '</div>';
		$footer .= '</div>';
		$footer .= '</body>';
		$footer .= '</html>';
		$footer .= '</page>';
		$html = $header . $body . $footer;
		$pageHeight = $pageHeight / 96;
		$pdf = $this->createPdf(array('html' => $html, 'pageHeight' => $pageHeight * 25.4, 'pageWidth' => 3 * 25.4), 'drawer/');
		$this->db->query("UPDATE " . DB_PREFIX . "close_drawer SET pdf = '" . $pdf . "' WHERE id = '" . (int) $drawerID . "'");
		return $pdf;
	}
}