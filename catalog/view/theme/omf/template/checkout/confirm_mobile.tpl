	<table class="checkout-product">
		<thead>
			<tr>
				<th class="name"><?php echo $column_name; ?></th>				
				<th class="quantity"><?php echo $text_qty; ?></th>				
				<th class="total"><?php echo $column_total; ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($products as $product) { ?>
			<tr>
				<td class="name">
					<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
					<span class="model"><?php echo $product['model']; ?></span>
					<?php foreach ($product['option'] as $option) { ?>
					<br />
					&nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
					<?php } ?>
					<span class="price"><?php echo $product['price']; ?></span>
				</td>				
				<td class="quantity"><?php echo $product['quantity']; ?></td>				
				<td class="total"><?php echo $product['total']; ?></td>
			</tr>
			<?php } ?>
			<?php foreach ($vouchers as $voucher) { ?>
			<tr>
				<td class="name">
					<?php echo $voucher['description']; ?>
					<span class="price"><?php echo $voucher['amount']; ?></span>
				</td>				
				<td class="quantity">1</td>				
				<td class="total"><?php echo $voucher['amount']; ?></td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<?php foreach ($totals as $total) { ?>
			<tr>
				<th colspan="2"><?php echo $total['title']; ?>:</th>
				<td class="total"><?php echo $total['text']; ?></td>
			</tr>
			<?php } ?>
		</tfoot>
	</table>
	<div class="payment"><?php echo $payment; ?></div>