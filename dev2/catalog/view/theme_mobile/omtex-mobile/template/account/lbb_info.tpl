<?php echo $header; ?>
<ul id="breadcrumbs-one">
	<?php 
	$total = count($breadcrumbs); 
	$i=0;
	foreach ($breadcrumbs as $breadcrumb) { 
		$i++;
		if($i==$total)
		{
			?>
			<li><a class="current"><?php echo $breadcrumb['text']; ?></a></li>
			<?php 
		}else{
			?>
			<li><a href="<?php echo $breadcrumb['href']; ?>" rel="external"><?php echo $breadcrumb['text']; ?></a></li>
			<?php
		}
	} ?>
</ul>
<?php echo $content_top; ?>
<div data-role="content">
	<h2><?php echo $heading_title; ?></h2>
	<!--<?php echo $text_order_detail; ?>-->
	<ul data-role="listview" style="margin-bottom:15px;" data-divider-theme="d">
		<li data-role="list-divider">
			<b>Name:</b> : <?= $lbb_data['firstname'] . ' ' . $lbb_data['lastname']; ?>
		</li>

		<li data-role="list-divider">
			<b>Payment Type</b> : <?= ucfirst(str_replace('_', ' ', $lbb_data['payment_type'])); ?>
		</li>

		<li data-role="list-divider">
			<b>Shipment #</b> : <?= $lbb_data['shipment_number']; ?>
		</li>

		<li data-role="list-divider">
			<b>Added</b> : <?= $lbb_data['date_added']; ?>
		</li>

		<li data-role="list-divider">
			<b>Status</b> : <?= $lbb_data['status']; ?>
		</li>

		<li data-role="list-divider">
			<b>Procedure</b> : <?= $lbb_data['option']; ?>
		</li>

		<?php if ($payment_details) { ?>
		<li data-role="list-divider">
			<b><?= ($payment_details['transaction_id'])? 'Transaction ID': 'Credit Code'; ?></b> : <?= ($payment_details['transaction_id']) ? $payment_details['transaction_id']: $payment_details['credit_code']; ?>
		</li>

		<li data-role="list-divider">
			<b>Amount Paid</b> : $<?= number_format($payment_details['amount'], 2); ?>
		</li>

		<?php } ?>

		<li><h3><b>Address:</b></h3>
			<p><?= $lbb_data['address_1'] . ', ' . $lbb_data['city'] . ', ' . $lbb_data['postcode']; ?></p>
		</li>

	</ul>

	<ul data-role="listview" data-divider-theme="d">
	<?php $total = 0.00; ?>
		<?php foreach ($products as $product) { ?>
		<?php
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
				$rejected_qty = (int)$quantities['rejected_qty'];
			}

			if($product['admin_updated']=='1') {
				$oem_a_qty = $product['admin_oem_a_qty'];
				$oem_b_qty = $product['admin_oem_b_qty'];
				$oem_c_qty = $product['admin_oem_c_qty'];
				$oem_d_qty = $product['admin_oem_d_qty'];
				$non_oem_a_qty = $product['admin_non_oem_a_qty'];
				$non_oem_b_qty = $product['admin_non_oem_b_qty'];
				$non_oem_c_qty = $product['admin_non_oem_c_qty'];
				$non_oem_d_qty = $product['admin_non_oem_d_qty'];
				$rejected_qty = $product['admin_rejected_qty'];
			}

			$admin_oem_a_total+=(int)$oem_a_qty * (float)$product['oem_a_price'];
			$admin_oem_b_total+=(int)$oem_b_qty * (float)$product['oem_b_price'];
			$admin_oem_c_total+=(int)$oem_c_qty * (float)$product['oem_c_price'];
			$admin_oem_d_total+=(int)$oem_d_qty * (float)$product['oem_d_price'];
			$admin_non_oem_a_total+=(int)$non_oem_a_qty * (float)$product['non_oem_a_price'];
			$admin_non_oem_b_total+=(int)$non_oem_b_qty * (float)$product['non_oem_b_price'];
			$admin_non_oem_c_total+=(int)$non_oem_c_qty * (float)$product['non_oem_c_price'];
			$admin_non_oem_d_total+=(int)$non_oem_d_qty * (float)$product['non_oem_d_price'];

			$admin_total = ($oem_a_qty * $product['oem_a_price']) + ($oem_b_qty * $product['oem_b_price']) + ($oem_c_qty * $product['oem_c_price']) + ($oem_d_qty * $product['oem_d_price']) + ($non_oem_a_qty * $product['non_oem_a_price']) + ($non_oem_b_qty * $product['non_oem_b_price']) + ($non_oem_c_qty * $product['non_oem_c_price']) + ($non_oem_d_qty * $product['non_oem_d_price']);

			$admin_combine_total+=(float)$admin_total;
		?>

		<li><h3> <?php echo $product['description']; ?></h3> 
			<p>

				<?php echo $column_model; ?>: <?php echo $product['sku']; ?><br/>

				Qty Sent: <?php echo $product['qty']; ?><br />
				Qty Recived: <?php echo $product['total_received']; ?><br />

				<?php echo $column_total; ?>: $<?php echo $admin_total; ?>
			</p>
		</li>
		<?php } ?>
	</ul>
	<ul data-role="listview" style="margin-bottom:15px;" data-divider-theme="d">
		<li data-role="list-divider" style="text-align:right; padding-top:1px; padding-bottom:2px;" ><h3><b>Total:</b>
			$<?php echo number_format($admin_combine_total, 2); ?></h3>
		</li>
	</ul>

	<?php if ($comment) { ?>
	<?php echo $text_comment; ?> <?php echo $comment; ?>
	<?php } ?>
	<?php if ($histories) { ?>
	<h2><?php echo $text_history; ?></h2>
	<ul data-role="listview" style="margin-bottom:10px;">
		<?php foreach ($histories as $history) { ?>
		<li><p><br/><?php echo $column_date_added; ?>: <?php echo $history['date_added']; ?><br/>
			<?php echo $column_status; ?>: <?php echo $history['status']; ?><br/>
			<?php echo $column_comment; ?>: <?php echo $history['comment']; ?><br/>
		</p></li>
		<?php } ?>
	</ul>
	<?php } ?>
	<a href="<?php echo $continue; ?>" class="button" data-role="button" rel="external"><?php echo $button_continue; ?></a>
</div>
<?php echo $content_bottom; ?>
<?php echo $footer; ?> 