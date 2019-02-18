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
				<td class="left" colspan="2"><?php echo $text_lbb_detail; ?></td>
			</tr>
		</thead>
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
				<th width="15%">Total:</th><td width="35%" class="left">$<?= number_format($lbb_data['total'], 2); ?></td>
			</tr>
			<?php if ($payment_details) { ?>
			<tr>
				<th width="15%"><?= ($payment_details['transaction_id'])? 'Transaction ID': 'Credit Code'; ?>:</th><td width="35%" class="left"><?= ($payment_details['transaction_id']) ? $payment_details['transaction_id']: $payment_details['credit_code']; ?></td>
				<th width="15%">Amount Paid:</th><td width="35%" class="left">$<?= number_format($payment_details['amount'], 2); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<th>&nbsp</th>
			</tr>
		</table>
		<table class="list order_info">
			<thead>
				<tr>
					<td class="left"><?php echo $column_name; ?></td>
					<td class="left model"><?php echo $column_model; ?></td>
					<td class="right"><?php echo $column_quantity_oem; ?></td>
					<td class="right"><?php echo $column_quantity_non_oem; ?></td>
					<td class="right price"><?php echo $column_oem_price; ?></td>
					<td class="right price"><?php echo $column_non_oem_price; ?></td>
					<td class="right"><?php echo $column_total; ?></td>
					<?php if ($products) { ?>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php $total = 0.00; ?>
				<?php foreach ($products as $product) { ?>
				<tr>
					<td class="left">
						<?php echo $product['description']; ?>
					</td>
					<td class="left model"><?php echo $product['sku']; ?></td>
					<td class="right"><?php echo $product['oem_quantity']; ?></td>
					<td class="right"><?php echo $product['non_oem_quantity']; ?></td>
					<td class="right price">$<?php echo $product['oem_price']; ?></td>
					<td class="right price">$<?php echo $product['non_oem_price']; ?></td>
					<td class="right">$<?php echo $product['sub_total']; ?></td>
					<?php $total += $product['sub_total']; ?>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="right" colspan="6"><b>Total:</b></td>
					<td class="right">$<?php echo number_format($total, 2); ?></td>
				</tr>
			</tfoot>
		</table>
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
		<div class="buttons">
			<div class="left"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
		</div>
		<?php echo $content_bottom; ?></div>
		<?php echo $footer; ?> 