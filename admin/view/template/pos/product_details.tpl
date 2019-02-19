<!-- receive request from main page and render the product details div -->
<?php
	$dispay_attrs_string = array('name', 'sku', 'upc', 'model', 'cost', 'description', 'price', 'quantity', 'thumb', 'manufacturer', 'location', 'minimum');
	$dispay_attrs_array = array('product_options');
?>

<div id="product_details_list">
	<div id="product_details_string">
		<table class="list">
			<thead>
			<tr>
				<td class="right"><?php echo $column_attr_name; ?></td>
				<td class="left"><?php echo $column_attr_value; ?></td>
			</tr>
			</thead>
			<tbody id="details_list">
				<?php
					foreach ($dispay_attrs_string as $display_attr) {
						if (isset(${$display_attr})) {
							$text_attr = ucfirst($display_attr) . ':';
							if (isset(${'entry_'.$display_attr})) {
								$text_attr = ${'entry_'.$display_attr};
							}
				?>
					<tr>
						<td class="right"><?php echo $text_attr; ?></td>
				<?php
						if ('thumb' == $display_attr) {
				?>
						<td class="left"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" /></div></td>
				<?php
						} else {
				?>
						<td class="left"><?php echo htmlspecialchars_decode(${$display_attr}); ?></td>
				<?php
						}
				?>
					</tr>
				<?php
						}
					}
				?>
			</tbody>
		</table>
	</div>
	<div id="product_details_array">
		<?php
			foreach($dispay_attrs_array as $dispay_attr_array) {
				if ('product_options' == $dispay_attr_array) {
		?>
        <div id="product_options">
			<table class="list">
				<thead>
				<tr>
					<td colspan="3" class="left"><?php echo $tab_option; ?></td>
				</tr>
				<tr>
					<td class="left"><?php echo $column_attr_name; ?></td>
					<td class="left"><?php echo $column_attr_value; ?></td>
					<td><?php echo $entry_required; ?></td>
				</tr>
				</thead>
			<tbody id="details_list">
          <?php $option_value = ''; ?>
          <?php
			foreach ($product_options as $product_option) { 
				if ($product_option['type'] == 'text' ||
					$product_option['type'] == 'textarea' ||
					$product_option['type'] == 'file' ||
					$product_option['type'] == 'date' ||
					$product_option['type'] == 'datetime' ||
					$product_option['type'] == 'time'
					) {
					$option_value = $product_option['option_value'];
				} elseif ($product_option['type'] == 'select' || 
					$product_option['type'] == 'radio' || 
					$product_option['type'] == 'checkbox' || 
					$product_option['type'] == 'image') {
					if (isset($option_values[$product_option['option_id']])) {
						foreach ($option_values[$product_option['option_id']] as $option_value_value) {
							foreach ($product_option['product_option_value'] as $product_option_value) {
								if ($product_option_value['option_value_id'] == $option_value_value['option_value_id']) {
								$option_value .= $option_value_value['name'];
									$option_value .= '<br/>';
								}
							}
						}
					}
				}
		  ?>
              <tr>
					<td><?php echo $product_option['name']; ?></td>
					<td><?php echo $option_value; ?></td>
					<td><?php if ($product_option['required']) { echo $text_yes; } else { echo $text_no; } ?></td>
              </tr>
          <?php 
				}
		  ?>
        </div>		
		<?php
				}
			}
		?>
	</div>
</div>