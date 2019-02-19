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
				<td class="left" colspan="2"><?php echo $text_return_detail; ?></td>
			</tr>
		</thead>
		 <div class="buttons">
			<div class="right"><a target="_blank" href="<?php echo $return_info['pdf_link']; ?>" class="button_pink"><span>Download PDF</span></a></div>
		</div> 
		<table style="width: 100%;">
			<tr>
				<th width="15%"><?= $text_rma_number; ?></th><td width="35%" class="left"><?= $return_info['rma_number']; ?></td>
				<th width="15%"><?= $text_order_id; ?></th><td width="35%" class="left"><?= $return_info['order_id']; ?></td>
			</tr>
			<tr>
				<th width="15%"><?= $text_date_added; ?></th><td width="35%" class="left"><?= $return_info['date_added']; ?></td>
				<th width="15%"><?= $text_status; ?></th><td width="35%" class="left"><?= $return_info['rma_status']; ?></td>
			</tr>
			<tr>
				<th>&nbsp</th>
			</tr>
		</table>
		<?php if ($products) { ?>
		<table class="list">
		<thead>
			<tr>
				<td class="center">Products</td>
			</tr>
		</thead>
		</table>
		<table class="list order_info">
			<thead>
				<tr>
					<td class="left"><?php echo $column_product; ?></td>
					<td class="left model"><?php echo $column_model; ?></td>
					<td class="right"><?php echo $column_quantity; ?></td>
					<td class="right"><?php echo $column_reason; ?></td>
					<td class="right"><?php echo $column_action; ?></td>
					<td class="right price"><?php echo $column_price; ?></td>
				</tr>
			</thead>
			<tbody>
			<?php $total = 0.00; ?>
				<?php foreach ($products as $product) { ?>
				<tr>
					<td class="left">
						<?php echo $product['title']; ?>
					</td>
					<td class="left model"><?php echo $product['sku']; ?></td>
					<td class="right"><?php echo $product['quantity']; ?></td>
					<td class="right"><?php echo $product['reason']; ?></td>
					<td class="right"><?php echo $product['decision']; ?></td>
					<td class="right price">$<?php echo $product['price']; ?></td>
					<?php $total += $product['price']; ?>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="right" colspan="5"><b>Total:</b></td>
					<td class="right price">$<?php echo number_format($total, 2); ?></td>
				</tr>
			</tfoot>
		</table>
		<?php } ?>
		<?php if ($replacements) { ?>
		<table class="list">
		<thead>
			<tr>
				<td class="center">Replacements</td>
			</tr>
		</thead>
		</table>
		<table class="list order_info">
			<thead>
				<tr>
					<td class="left model"><?php echo $column_model; ?></td>
					<!-- <td class="right"><?php echo $column_quantity; ?></td> -->
					<td class="right"><?php echo $column_action; ?></td>
					<td class="right price"><?php echo $column_price; ?></td>
				</tr>
			</thead>
			<tbody>
			<?php $total = 0.00; ?>
				<?php foreach ($replacements as $product) { ?>
				<tr>
					<td class="left model"><?php echo $product['sku']; ?></td>
					<!-- <td class="right"><?php echo $product['quantity']; ?></td> -->
					<td class="right"><?php echo $product['action']; ?></td>
					<td class="right price">$<?php echo number_format($product['price'], 2); ?></td>
					<?php $total += $product['price']; ?>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="right" colspan="2"><b>Total:</b></td>
					<td class="right price">$<?php echo number_format($total, 2); ?></td>
				</tr>
			</tfoot>
		</table>
		<?php } ?>
		<?php if ($credits) { ?>
		<table class="list">
		<thead>
			<tr>
				<td class="center">Credits</td>
			</tr>
		</thead>
		</table>
		<table class="list order_info">
			<thead>
				<tr>
					<td class="left model"><?php echo $column_model; ?></td>
					<!-- <td class="right"><?php echo $column_quantity; ?></td> -->
					<td class="right"><?php echo $column_action; ?></td>
					<td class="right price"><?php echo $column_price; ?></td>
				</tr>
			</thead>
			<tbody>
			<?php $total = 0.00; ?>
				<?php foreach ($credits as $product) { ?>
				<tr>
					<td class="left model"><?php echo $product['sku']; ?></td>
					<!-- <td class="right"><?php echo $product['quantity']; ?></td> -->
					<td class="right"><?php echo $product['action']; ?></td>
					<td class="right price">$<?php echo number_format($product['price'], 2); ?></td>
					<?php $total += $product['price']; ?>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="right" colspan="2"><b>Total:</b></td>
					<td class="right price">$<?php echo number_format($total, 2); ?></td>
				</tr>
			</tfoot>
		</table>
		<?php } ?>
		<?php if ($refunds) { ?>
		<table class="list">
		<thead>
			<tr>
				<td class="center">Refunds</td>
			</tr>
		</thead>
		</table>
		<table class="list order_info">
			<thead>
				<tr>
					<td class="left model"><?php echo $column_model; ?></td>
					<!-- <td class="right"><?php echo $column_quantity; ?></td> -->
					<td class="right"><?php echo $column_action; ?></td>
					<td class="right price"><?php echo $column_price; ?></td>
				</tr>
			</thead>
			<tbody>
			<?php $total = 0.00; ?>
				<?php foreach ($refunds as $product) { ?>
				<tr>
					<td class="left model"><?php echo $product['sku']; ?></td>
					<!-- <td class="right"><?php echo $product['quantity']; ?></td> -->
					<td class="right"><?php echo $product['action']; ?></td>
					<td class="right price">$<?php echo number_format($product['price'], 2); ?></td>
					<?php $total += $product['price']; ?>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="right" colspan="2"><b>Total:</b></td>
					<td class="right price">$<?php echo number_format($total, 2); ?></td>
				</tr>
			</tfoot>
		</table>
		<?php } ?>
		
		<?php echo $content_bottom; ?></div>
		<?php echo $footer; ?> 