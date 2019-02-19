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
			<b><?= $text_rma_number; ?></b> <?= $return_info['rma_number']; ?>
		</li>

		<li data-role="list-divider">
			<b><?= $text_order_id; ?></b> <?= $return_info['order_id']; ?>
		</li>

		<li data-role="list-divider">
			<b><?= $text_date_added; ?></b> <?= $return_info['date_added']; ?>
		</li>

		<li data-role="list-divider">
			<b><?= $text_status; ?></b> <?= $return_info['rma_status']; ?>
		</li>

	</ul>
	<?php if ($products) { ?>
	<h3>Products</h3>
	<ul data-role="listview" data-divider-theme="d">
	<?php $total = 0.00; ?>
		<?php foreach ($products as $product) { ?>

		<li><h3> <?php echo $product['title']; ?></h3> 
			<p>

				<?php echo $column_model; ?>: <?php echo $product['sku']; ?><br/>

				<?php echo $column_quantity; ?>: <?php echo $product['quantity']; ?> - 
				<?php echo $column_reason; ?> : <?php echo $product['reason']; ?><br/>

				<?php echo $column_action; ?>: <?php echo $product['decision']; ?> - 
				<?php echo $column_price; ?> : <?php echo $product['price']; ?><br/>
				<?php $total += $product['price']; ?>
			</p>
		</li>
		<?php } ?>
	</ul>
	<ul data-role="listview" style="margin-bottom:15px;" data-divider-theme="d">
		<li data-role="list-divider" style="text-align:right; padding-top:1px; padding-bottom:2px;" ><h3><b>Total:</b>
			$<?php echo number_format($total, 2); ?></h3>
		</li>
	</ul>
	<?php } ?>


	<?php if ($replacements) { ?>
	<h3>Replacements</h3>
	<ul data-role="listview" data-divider-theme="d">
	<?php $total = 0.00; ?>
		<?php foreach ($replacements as $product) { ?>

		<li>
			<p>

				<?php echo $column_model; ?>: <?php echo $product['sku']; ?><br/>

				<?php echo $column_action; ?>: <?php echo $product['action']; ?> - 
				<?php echo $column_price; ?> : $<?php echo number_format($product['price'], 2); ?><br/>

				<?php $total += $product['price']; ?>
			</p>
		</li>
		<?php } ?>
	</ul>
	<ul data-role="listview" style="margin-bottom:15px;" data-divider-theme="d">
		<li data-role="list-divider" style="text-align:right; padding-top:1px; padding-bottom:2px;" ><h3><b>Total:</b>
			$<?php echo number_format($total, 2); ?></h3>
		</li>
	</ul>
	<?php } ?>

	<?php if ($credits) { ?>
	<h3>Credits</h3>
	<ul data-role="listview" data-divider-theme="d">
	<?php $total = 0.00; ?>
		<?php foreach ($credits as $product) { ?>

		<li>
			<p>

				<?php echo $column_model; ?>: <?php echo $product['sku']; ?><br/>

				<?php echo $column_action; ?>: <?php echo $product['action']; ?> - 
				<?php echo $column_price; ?> : $<?php echo number_format($product['price'], 2); ?><br/>

				<?php $total += $product['price']; ?>
			</p>
		</li>
		<?php } ?>
	</ul>
	<ul data-role="listview" style="margin-bottom:15px;" data-divider-theme="d">
		<li data-role="list-divider" style="text-align:right; padding-top:1px; padding-bottom:2px;" ><h3><b>Total:</b>
			$<?php echo number_format($total, 2); ?></h3>
		</li>
	</ul>
	<?php } ?>

	<?php if ($refunds) { ?>
	<h3>Refunds</h3>
	<ul data-role="listview" data-divider-theme="d">
	<?php $total = 0.00; ?>
		<?php foreach ($refunds as $product) { ?>

		<li>
			<p>

				<?php echo $column_model; ?>: <?php echo $product['sku']; ?><br/>

				<?php echo $column_action; ?>: <?php echo $product['action']; ?> - 
				<?php echo $column_price; ?> : $<?php echo number_format($product['price'], 2); ?><br/>

				<?php $total += $product['price']; ?>
			</p>
		</li>
		<?php } ?>
	</ul>
	<ul data-role="listview" style="margin-bottom:15px;" data-divider-theme="d">
		<li data-role="list-divider" style="text-align:right; padding-top:1px; padding-bottom:2px;" ><h3><b>Total:</b>
			$<?php echo number_format($total, 2); ?></h3>
		</li>
	</ul>
	<?php } ?>
	
	<a href="<?php echo $continue; ?>" class="button" data-role="button" rel="external"><?php echo $button_continue; ?></a>
</div>
<?php echo $content_bottom; ?>
<?php echo $footer; ?> 











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
		<div class="buttons">
			<div class="left"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
		</div>
		<?php echo $content_bottom; ?></div>
		<?php echo $footer; ?> 