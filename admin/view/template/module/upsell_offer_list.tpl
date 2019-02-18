<table class="list">
<thead>
	<tr>
	   <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
	   <td class="left"><?php if ($sort == 'name') { ?>
                <a id="sort-name" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                <?php } else { ?>
                <a id="sort-name"><?php echo $column_name; ?></a>
                <?php } ?></td>
	   <td class="left"><?php if ($sort == 'total_price_min') { ?>
                <a id="sort-total-price-min" class="<?php echo strtolower($order); ?>"><?php echo $column_total_price_min; ?></a>
                <?php } else { ?>
                <a id="sort-total-price-min"><?php echo $column_total_price_min; ?></a>
                <?php } ?></td>
	   <td class="left"><?php if ($sort == 'total_price_max') { ?>
                <a id="sort-total-price-max" class="<?php echo strtolower($order); ?>"><?php echo $column_total_price_max; ?></a>
                <?php } else { ?>
                <a id="sort-total-price-max"><?php echo $column_total_price_max; ?></a>
                <?php } ?></td>	
	   <td class="left"><?php if ($sort == 'date_start') { ?>
                <a id="sort-date-start" class="<?php echo strtolower($order); ?>"><?php echo $column_date_start; ?></a>
                <?php } else { ?>
                <a id="sort-date-start"><?php echo $column_date_start; ?></a>
                <?php } ?></td>
	  <td class="left"><?php if ($sort == 'date_end') { ?>
                <a id="sort-date-end" class="<?php echo strtolower($order); ?>"><?php echo $column_date_end; ?></a>
                <?php } else { ?>
                <a id="sort-date-end"><?php echo $column_date_end; ?></a>
                <?php } ?></td>	
	   <td class="right"><?php echo $column_action; ?></td>
	</tr>   
</thead>
<tbody>
	<tr class="filter">
		<td></td>
		<td class="left"><input type="text" name="filter_name" value="<?php echo $filter_name; ?>" /></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="right"><a onclick="filter();" class="button"><span><?php echo $button_filter; ?></span></a></td>
	</tr>
	<?php if ($upsell_offers) { ?>
		<?php foreach($upsell_offers as $upsell_offer){ ?>
			<tr>
				<td style="text-align: center;"><?php if ($upsell_offer['selected']) { ?>
					<input type="checkbox" name="selected[]" value="<?php echo $upsell_offer['upsell_offer_id']; ?>" checked="checked" />
					<?php } else { ?>
					<input type="checkbox" name="selected[]" value="<?php echo $upsell_offer['upsell_offer_id']; ?>" />
					<?php } ?></td>
				<td class="left"><?php echo $upsell_offer['name'];?></td>
				<td class="left"><?php echo $upsell_offer['total_price_min'];?></td>
				<td class="left"><?php echo $upsell_offer['total_price_max'];?></td>
				<td class="left"><?php echo $upsell_offer['date_start'];?></td>
				<td class="left"><?php echo $upsell_offer['date_end'];?></td>
				<td class="right">
				<?php foreach ($upsell_offer['action'] as $action) { ?>
					<?php if ($action['onclick']){ ?>
							[ <a onclick="<?php echo $action['onclick']; ?>"><?php echo $action['text']; ?></a> ]
					<?php } else { ?>
							[ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
					<?php } ?>
                <?php } ?></td></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="7" class="center"><?php echo $text_no_results; ?></td></tr>
	<?php } ?>
</tbody>
</table>

<div class="pagination"><?php echo $pagination; ?></div>

<script type="text/javascript">
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
	$('#sort-name').click(function() {
		$('#upsell-offers-list').load('index.php?route=module/upsell_offer/getList&token=<?php echo $token; ?>&sort=name<?php echo $url; ?>');	
	});
	$('#sort-total-price-min').click(function() {
		$('#upsell-offers-list').load('index.php?route=module/upsell_offer/getList&token=<?php echo $token; ?>&sort=total_price_min<?php echo $url; ?>');	
	});
	$('#sort-total-price-max').click(function() {
		$('#upsell-offers-list').load('index.php?route=module/upsell_offer/getList&token=<?php echo $token; ?>&sort=total_price_max<?php echo $url; ?>');	
	});
	$('#sort-date-start').click(function() {
		$('#upsell-offers-list').load('index.php?route=module/upsell_offer/getList&token=<?php echo $token; ?>&sort=date_start<?php echo $url; ?>');	
	});
	$('#sort-date-end').click(function() {
		$('#upsell-offers-list').load('index.php?route=module/upsell_offer/getList&token=<?php echo $token; ?>&sort=date_end<?php echo $url; ?>');	
	});
});	
</script>
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});
//--></script> 
<script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=module/upsell_offer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'filter_name\']').val(ui.item.label);
						
		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});
//--></script> 