<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/product.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="button"><span><?php echo $button_insert; ?></span></a><a onclick="$('form').submit();" class="button"><span><?php echo $button_delete; ?></span></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="list">
        <thead>
          <tr>
            <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
            <td class="center"><?php echo $column_image; ?></td>
			<td class="left"><?php echo $column_image_position; ?></td>
            <td class="left"><?php if ($sort == 'pt.promo_text') { ?>
              <a href="<?php echo $sort_promo_text; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_promo_text; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_promo_text; ?>"><?php echo $column_promo_text; ?></a>
              <?php } ?></td>
            <td class="left"><?php echo $column_promo_link; ?></td>
            <td class="left"><?php if ($sort == 'pt.sort_order') { ?>
              <a href="<?php echo $sort_sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_sort_order; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_sort_order; ?>"><?php echo $column_sort_order; ?></a>
            <?php } ?></td>
            <td class="right"><?php echo $column_action; ?></td>
          </tr>
        </thead>
          <tbody>
            <?php if ($promotags) { ?>
          <?php foreach ($promotags as $promotag) { ?>
          <tr>
            <td style="text-align: center;"><?php if ($promotag['selected']) { ?>
              <input type="checkbox" name="selected[]" value="<?php echo $promotag['promo_tags_id']; ?>" checked="checked" />
              <?php } else { ?>
              <input type="checkbox" name="selected[]" value="<?php echo $promotag['promo_tags_id']; ?>" />
              <?php } ?></td>
			<td class="center"><img src="<?php echo $promotag['image']; ?>" alt="<?php echo $promotag['image']; ?>" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
			<td class="left">
			<?php if ($promotag['promo_direction'] == '0') { ?>
			<?php  echo "Top-Right"; } elseif ($promotag['promo_direction'] == '1') { ?>
			<?php echo "Top-Left"; } elseif ($promotag['promo_direction'] == '2') { ?>
			<?php echo "Bottom-Left"; } elseif ($promotag['promo_direction'] == '3') { ?>
			<?php echo "Bottom-Right"; }?>
			</td>
			<td class="left"><?php echo $promotag['promo_text']; ?></td>
			<td class="left"><?php echo $promotag['promo_link']; ?></td>
			<td class="left"><?php echo $promotag['sort_order']; ?></td>
            <td class="right"><?php foreach ($promotag['action'] as $action) { ?>
              [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
              <?php } ?></td>
          </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="7"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=catalog/product&token=<?php echo $token; ?>';
	
	var filter_name = $('input[name=\'filter_name\']').attr('value');
	
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
	
	var filter_model = $('input[name=\'filter_model\']').attr('value');
	
	if (filter_model) {
		url += '&filter_model=' + encodeURIComponent(filter_model);
	}
	
	var filter_price = $('input[name=\'filter_price\']').attr('value');
	
	if (filter_price) {
		url += '&filter_price=' + encodeURIComponent(filter_price);
	}
	
	var filter_quantity = $('input[name=\'filter_quantity\']').attr('value');
	
	if (filter_quantity) {
		url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
	}
	
	var filter_status = $('select[name=\'filter_status\']').attr('value');
	
	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}	

	location = url;
}
//--></script> 
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});
//--></script> 
<?php echo $footer; ?>