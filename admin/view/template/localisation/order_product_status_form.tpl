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
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/order.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
		<table class="form">
			<tr>
				<td><span class="required">*</span> <?php echo $entry_name; ?></td>
				<td><?php foreach ($languages as $language) { ?>
					<input type="text" name="order_product_status[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($order_product_status[$language['language_id']]) ? $order_product_status[$language['language_id']]['name'] : ''; ?>" />
					<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
					<?php if (isset($error_name[$language['language_id']])) { ?>
						<span class="error"><?php echo $error_name[$language['language_id']]; ?></span><br />
					<?php } ?>
				<?php } ?></td>
			</tr> 
			<tr>
				<td><?php echo $entry_order_status; ?></td>
				<td>
					<select name="order_status_id">
					<?php foreach ($order_statuses as $order_status) { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>"<?php echo ($order_status['order_status_id'] == $order_status_id) ? ' selected="selected"' : ''; ?>><?php echo $order_status['name']; ?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo $entry_order_status_internal; ?></td>
				<td>
					<select name="int_order_status_id">
					<?php foreach ($order_statuses as $order_status) { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>"<?php echo ($order_status['order_status_id'] == $int_order_status_id) ? ' selected="selected"' : ''; ?>><?php echo $order_status['name']; ?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo $entry_days_delay; ?></td>
				<td><input type="text" name="days_delay" value="<?php echo $days_delay; ?>" size="2" /></td>
            </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>