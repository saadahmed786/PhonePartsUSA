<?php echo $header; ?>
<div class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
	<a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	<?php } ?>
</div>
<h1><?php echo $heading_title; ?></h1>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
	<table class="list">
		<thead>
			<tr>
				<td class="left"><?php echo $text_lbb_detail; ?></td>
			</tr>
		</thead>
	</table>
	<!-- <div class="buttons">
		<div class="right"><a target="_blank" href="<?php echo $lbb_data['pdf_link']; ?>" class="button_pink"><span>Download PDF</span></a></div>
	</div> -->
	<table style="width: 100%;">
		<tr>
			<th width="15%">Name:</th><td width="35%" class="left"><?= $lbb_data['firstname'] . ' ' . $lbb_data['lastname']; ?></td>
			<th width="15%">Address:</th><td width="35%" class="left"><?= $lbb_data['address_1'] . ', ' . $lbb_data['city'] . ', ' . $lbb_data['postcode']; ?></td>
		</tr>
		<tr>
			<th width="15%">Payment Type:</th><td width="35%" class="left"><?= ucfirst(str_replace('_', ' ', $lbb_data['payment_type'])); ?></td>
			<th width="15%">Shipment #:</th><td width="35%" class="left"><?= $lbb_data['shipment_number']; ?></td>
		</tr>
		<tr>
			<th width="15%">Added:</th><td width="35%" class="left"><?= $lbb_data['date_added']; ?></td>
			<th width="15%">Status:</th><td width="35%" class="left"><?= $lbb_data['status']; ?></td>
		</tr>
		<tr>
			<th width="15%">Procedure:</th><td width="35%" class="left"><?= $lbb_data['option']; ?></td>
		</tr>
		<?php if ($payment_details) { ?>
		<tr>
			<th width="15%"><?= ($payment_details['transaction_id'])? 'Transaction ID': 'Credit Code'; ?>:</th><td width="35%" class="left"><?= ($payment_details['transaction_id']) ? $payment_details['transaction_id']: $payment_details['credit_code']; ?></td>
			<th width="15%">Amount Paid:</th><td width="35%" class="left"><?= $this->currency->format($payment_details['amount'], 2); ?></td>
		</tr>
		<?php } ?>
		<tr>
			<th>&nbsp</th>
		</tr>
	</table>

<?php if ($lbb_data['status'] != 'Awaiting' && $lbb_data['status'] != 'Received' ) { ?>
	<table class=" order_info list">
		<thead>
			<tr>
				<td rowspan="2" class="left">LCD Type</td>
				<td colspan="4" class="center model">OEM</td>
				<td colspan="4" class="left">Non OEM</td>
				<td colspan="3" class="left">Rejected</td>
				<td rowspan="2" class="right price">Total</td>
			</tr>
			<tr>
				<td class="left">A</td>
				<td class="left">A-</td>
				<td class="left">B</td>
				<td class="left">C</td>

				<td class="left">A</td>
				<td class="left">A-</td>
				<td class="left">B</td>
				<td class="left">C</td>

				<td class="left">Salvage</td>
				<td class="left">Unacceptable</td>
				<td class="left">Damaged</td>
			</tr>
		</thead>
		<?php
		$qc_quantity_total = 0;
		$admin_oem_a_total = 0.00;
		$admin_oem_b_total = 0.00;
		$admin_oem_c_total = 0.00;
		$admin_oem_d_total = 0.00;
		$admin_non_oem_a_total = 0.00;
		$admin_non_oem_b_total = 0.00;
		$admin_non_oem_c_total = 0.00;
		$admin_non_oem_d_total = 0.00;
		?>
		<?php foreach ($products as $product) { ?>
		<?php 
		if($product['data_type']!='customer' and $product['data_type']!='qc' and $product['data_type']!='admin') continue;



		$quantities = $this->db->query("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'")->row;

		if($quantities) {
			$oem_a_qty = (int)$quantities['oem_qty_a'];
			$oem_b_qty = (int)$quantities['oem_qty_b'];
			$oem_c_qty = (int)$quantities['oem_qty_c'];
			$oem_d_qty = (int)$quantities['oem_qty_d'];
			$non_oem_a_qty = (int)$quantities['non_oem_qty_a'];
			$non_oem_b_qty = (int)$quantities['non_oem_qty_b'];
			$non_oem_c_qty = (int)$quantities['non_oem_qty_c'];
			$non_oem_d_qty = (int)$quantities['non_oem_qty_d'];
			$salvage_qty = (int)$quantities['salvage_qty'];
			$unacceptable_qty = (int)$quantities['unacceptable_qty'];
			$rejected_qty = (int)$quantities['rejected_qty'];
		}

		if($product['admin_updated']=='1') {
			$oem_a_qty = ($product['admin_oem_a_qty'])? (int)$product['admin_oem_a_qty']: $oem_a_qty;
			$oem_b_qty = ($product['admin_oem_b_qty'])? (int)$product['admin_oem_b_qty']: $oem_b_qty;
			$oem_c_qty = ($product['admin_oem_c_qty'])? (int)$product['admin_oem_c_qty']: $oem_c_qty;
			$oem_d_qty = ($product['admin_oem_d_qty'])? (int)$product['admin_oem_d_qty']: $oem_d_qty;
			$non_oem_a_qty = ($product['admin_non_oem_a_qty'])? (int)$product['admin_non_oem_a_qty']: $non_oem_a_qty;
			$non_oem_b_qty = ($product['admin_non_oem_b_qty'])? (int)$product['admin_non_oem_b_qty']: $non_oem_b_qty;
			$non_oem_c_qty = ($product['admin_non_oem_c_qty'])? (int)$product['admin_non_oem_c_qty']: $non_oem_c_qty;
			$non_oem_d_qty = ($product['admin_non_oem_d_qty'])? (int)$product['admin_non_oem_d_qty']: $non_oem_d_qty;
			$salvage_qty = ($product['admin_salvage_qty'])? (int)$product['admin_salvage_qty']: $salvage_qty;
			$unacceptable_qty = ($product['admin_unacceptable'])? (int)$product['admin_unacceptable']: $unacceptable_qty;
			$rejected_qty = ($product['rejected'])? (int)$product['admin_rejected']: $rejected_qty;
		}

		$admin_oem_a_total+=(int)$oem_a_qty * (float)$product['oem_a_price'];
		$admin_oem_b_total+=(int)$oem_b_qty * (float)$product['oem_b_price'];
		$admin_oem_c_total+=(int)$oem_c_qty * (float)$product['oem_c_price'];
		$admin_oem_d_total+=(int)$oem_d_qty * (float)$product['oem_d_price'];
		$admin_non_oem_a_total+=(int)$non_oem_a_qty * (float)$product['non_oem_a_price'];
		$admin_non_oem_b_total+=(int)$non_oem_b_qty * (float)$product['non_oem_b_price'];
		$admin_non_oem_c_total+=(int)$non_oem_c_qty * (float)$product['non_oem_c_price'];
		$admin_non_oem_d_total+=(int)$non_oem_d_qty * (float)$product['non_oem_d_price'];
		$admin_salvage_total+=(int)$non_oem_d_qty * (float)$product['salvage_price'];

		$admin_total = ($oem_a_qty * $product['oem_a_price']) + ($oem_b_qty * $product['oem_b_price']) + ($oem_c_qty * $product['oem_c_price']) + ($oem_d_qty * $product['oem_d_price']) + ($non_oem_a_qty * $product['non_oem_a_price']) + ($non_oem_b_qty * $product['non_oem_b_price']) + ($non_oem_c_qty * $product['non_oem_c_price']) + ($non_oem_d_qty * $product['non_oem_d_price']) + ($salvage_qty * $product['salvage_price']);

		$admin_combine_total+=(float)$admin_total;

		?>
		<tbody>
			<tr>
				<td class="left">
					<?php echo $product['description']; ?>
				</td>
				<td class="right "><?php echo ($oem_a_qty) ? (int)$oem_a_qty.' x '.$this->currency->format($product['oem_a_price']): '';	 ?></td>
				<td class="right "><?php echo ($oem_b_qty) ? (int)$oem_b_qty.' x '.$this->currency->format($product['oem_b_price']): '';	 ?></td>
				<td class="right "><?php echo ($oem_c_qty) ? (int)$oem_c_qty.' x '.$this->currency->format($product['oem_c_price']): '';	 ?></td>
				<td class="right "><?php echo ($oem_d_qty) ? (int)$oem_d_qty.' x '.$this->currency->format($product['oem_d_price']): '';	 ?></td>

				<td class="right "><?php echo ($non_oem_a_qty) ? (int)$non_oem_a_qty.' x '.$this->currency->format($product['non_oem_a_price']): '';	 ?></td>
				<td class="right "><?php echo ($non_oem_b_qty) ? (int)$non_oem_b_qty.' x '.$this->currency->format($product['non_oem_b_price']): '';	 ?></td>
				<td class="right "><?php echo ($non_oem_c_qty) ? (int)$non_oem_c_qty.' x '.$this->currency->format($product['non_oem_c_price']): '';	 ?></td>
				<td class="right "><?php echo ($non_oem_d_qty) ? (int)$non_oem_d_qty.' x '.$this->currency->format($product['non_oem_d_price']): '';	 ?></td>
				
				<td class="right "><?php echo ($salvage_qty) ? (int)$salvage_qty.' x '.$this->currency->format($product['salvage_price']): '';	 ?></td>
				<td class="right "><?php echo ($unacceptable_qty) ? (int)$unacceptable_qty: '';	 ?></td>
				<td class="right "><?php echo ($rejected_qty) ? (int)$rejected_qty: '';	 ?></td>
				
				<td class="right"><?php echo $this->currency->format($admin_total); ?></td>

			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="12" class="right">Total:</td>
				<td class="right"  ><strong><?php echo $this->currency->format($admin_combine_total);?></strong></td>
			</tr>
		</tfoot>
	</table>
	<?php } else { ?>
	<div align="center">
	<img src="<?php echo HTTPS_IMAGE; ?>waiting.png" width="200px">
	</div>
	<?php } ?>


	<?php if ($comment) { ?>
	<table class="list">
		<thead>
			<tr>
				<td class="left"><?php echo $text_comment; ?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left"><?php echo $comment; ?></td>
			</tr>
		</tbody>
	</table>
	<?php } ?>
	<!-- <div class="buttons">
		<div class="left"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
		<div class="right"><a target="_blank" href="<?php echo $lbb_data['pdf_link']; ?>" class="button_pink"><span>Download PDF</span></a></div>
	</div> -->
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?> 