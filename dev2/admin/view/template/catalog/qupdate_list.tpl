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
    <div class="buttons"><a onclick="$('#form').attr('action', '<?php echo $update; ?>'); $('#form').submit();" class="button"><span><?php echo $button_update; ?></span></a><a onclick="$('form').submit();" class="button"><span><?php echo $button_delete; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="list">
        <thead>
          <tr>
            <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
            <td class="center"><?php echo $column_image; ?></td>
            <td class="left"><?php if ($sort == 'pd.name') { ?>
              <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
              <?php } ?></td>
			<td class="left"><?php if ($sort == 'p2c.category_id') { ?>
			  <a href="<?php echo $sort_category; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_category; ?></a>
			  <?php } else { ?>
			  <a href="<?php echo $sort_category; ?>"><?php echo $column_category; ?></a>
			  <?php } ?></td> 
            <td class="left"><?php if ($sort == 'p.model') { ?>
              <a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
              <?php } ?></td>
            <td class="left"><?php if ($sort == 'p.price') { ?>
              <a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
              <?php } ?></td>
			<td class="left"><?php if ($sort == 'p.quantity') { ?>
              <a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
            <?php } ?></td>
			<td class="left"><?php if ($sort == 'p.weight') { ?>
              <a href="<?php echo $sort_weight; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_weight; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_weight; ?>"><?php echo $column_weight; ?></a>
            <?php } ?></td>
			<td class="left"><?php if ($sort == 'p.status') { ?>
              <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
            <?php } ?></td>
            <td class="right"><?php echo $column_action; ?></td>
          </tr>
        </thead>
        <tbody>
          <tr class="filter">
            <td></td>
            <td></td>
            <td><input type="text" name="filter_name" value="<?php echo $filter_name; ?>" /></td>
			<td ><select name="filter_category">
              <option value="*"></option>
              <?php foreach ($categories as $category) { ?>
                <?php if ($category['category_id']==$filter_category) { ?>
                  <option value="<?php echo $category['category_id']; ?>" selected="selected"><?php echo $category['name']; ?></option>
                <?php } else { ?>
                  <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option> 
                <?php } ?>
              <?php } ?>
            </td>
            <td><input type="text" name="filter_model" value="<?php echo $filter_model; ?>" size="12" /></td>
            <td><input type="text" name="filter_price" value="<?php echo $filter_price; ?>"  size="12" /></td>
			<td><input type="text" name="filter_quantity" value="<?php echo $filter_quantity; ?>"  size="12" /></td>
			<td><input type="text" name="filter_weight" value="<?php echo $filter_weight; ?>"  size="12" /></td>
			<td><select name="filter_status">
                <option value="*"></option>
                <?php if ($filter_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <?php } ?>
                <?php if (!is_null($filter_status) && !$filter_status) { ?>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } ?>
             </select></td>
			<td align="right"><a onclick="filter();" class="button"><span><?php echo $button_filter; ?></span></a></td>
          </tr>
          <?php if ($products) { ?>
          <?php foreach ($products as $product) { ?>
		  <tr>
            <td style="text-align: center;"><?php if ($product['selected']) { ?>
              <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
              <?php } else { ?>
              <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
              <?php } ?></td>
			<td class="center"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
            <td class="left"><?php echo $product['name']; ?></td>
			<td class="left">
              <?php foreach ($categories as $category) { ?>
              <?php if (in_array($category['category_id'], $product['category'])) { ?>
              <?php echo $category['name'];?><br>
              <?php } ?> <?php } ?>
            </td>
            <td class="left"><input type="text" name="model<?php echo $product['product_id']; ?>" value="<?php echo $product['model']; ?>" size="12" /></td>
            <td class="left"><input type="text" name="price<?php echo $product['product_id']; ?>" value="<?php echo $product['price']; ?>" size="12" /></td>
			<td class="left"><input type="text" name="quantity<?php echo $product['product_id']; ?>" value="<?php echo $product['quantity']; ?>" size="12" /></td>
			<td class="left"><input type="text" name="weight<?php echo $product['product_id']; ?>" value="<?php echo $product['weight']; ?>" size="12" /></td>
			<td class="left"><select name="status<?php echo $product['product_id']; ?>" >
			<?php if ($product['status'] == '0') { ?>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
				<option value="1"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
            <?php } ?>
			</select></td>
			<td class="right"></td>
          </tr>
		  <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="10"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </form>
    <div class="pagination"><?php echo $pagination; ?></div>
  </div>
</div>
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=catalog/qupdate&token=<?php echo $token; ?>';

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
	
	var filter_weight = $('input[name=\'filter_weight\']').attr('value');

	if (filter_weight) {
		url += '&filter_weight=' + encodeURIComponent(filter_weight);
	}
	
	var filter_status = $('select[name=\'filter_status\']').attr('value');

	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}
	
	var filter_category = $('select[name=\'filter_category\']').attr('value');
    
    if (filter_category != '*') {
		url += '&filter_category=' + encodeURIComponent(filter_category);
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