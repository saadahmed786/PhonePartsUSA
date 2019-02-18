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
		<h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
	</div>
	<div class="content">
		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
		<h2><?php echo $heading_inventory; ?></h2>
		<table class="form">
			<tr>
				<td><?php echo $entry_iom_inventory_auto_return; ?></td>
				<td><div class="scrollbox">
					<?php $class = 'odd'; ?>
					<?php foreach ($order_statuses as $order_status) { ?>
						<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
						<div class="<?php echo $class; ?>">
							<input type="checkbox" name="iom_inventory_auto_return[]" value="<?php echo $order_status['order_status_id']; ?>" <?php echo (in_array($order_status['order_status_id'], $iom_inventory_auto_return)) ? 'checked="checked"' : ''; ?> />
							<?php echo $order_status['name']; ?>
						</div>
					<?php } ?>
				</div></td>
			</tr>
			<tr>
				<td><?php echo $entry_iom_inventory_auto_reserve; ?></td>
				<td><div class="scrollbox">
					<?php $class = 'odd'; ?>
					<?php foreach ($order_statuses as $order_status) { ?>
						<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
						<div class="<?php echo $class; ?>">
							<input type="checkbox" name="iom_inventory_auto_reserve[]" value="<?php echo $order_status['order_status_id']; ?>" <?php echo (in_array($order_status['order_status_id'], $iom_inventory_auto_reserve)) ? 'checked="checked"' : ''; ?> />
							<?php echo $order_status['name']; ?>
						</div>
					<?php } ?>
				</div></td>
			</tr>
			<tr>
				<td><?php echo $entry_iom_inventory_ops_pending; ?></td>
				<td><select name="iom_inventory_ops_pending">
					<?php foreach ($order_product_statuses as $order_product_status) { ?>
						<option value="<?php echo $order_product_status['order_product_status_id']; ?>"<?php echo ($order_product_status['order_product_status_id'] == $iom_inventory_ops_pending) ? ' selected="selected"' : ''; ?>><?php echo $order_product_status['name']; ?></option>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td><?php echo $entry_iom_inventory_ops_backordered; ?></td>
				<td><select name="iom_inventory_ops_backordered">
					<?php foreach ($order_product_statuses as $order_product_status) { ?>
						<option value="<?php echo $order_product_status['order_product_status_id']; ?>"<?php echo ($order_product_status['order_product_status_id'] == $iom_inventory_ops_backordered) ? ' selected="selected"' : ''; ?>><?php echo $order_product_status['name']; ?></option>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td><?php echo $entry_iom_inventory_ops_partialship; ?></td>
				<td><select name="iom_inventory_ops_partialship">
					<?php foreach ($order_product_statuses as $order_product_status) { ?>
						<option value="<?php echo $order_product_status['order_product_status_id']; ?>"<?php echo ($order_product_status['order_product_status_id'] == $iom_inventory_ops_partialship) ? ' selected="selected"' : ''; ?>><?php echo $order_product_status['name']; ?></option>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td><?php echo $entry_iom_inventory_ops_reserved; ?></td>
				<td><select name="iom_inventory_ops_reserved">
					<?php foreach ($order_product_statuses as $order_product_status) { ?>
						<option value="<?php echo $order_product_status['order_product_status_id']; ?>"<?php echo ($order_product_status['order_product_status_id'] == $iom_inventory_ops_reserved) ? ' selected="selected"' : ''; ?>><?php echo $order_product_status['name']; ?></option>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td><?php echo $entry_iom_inventory_ops_ordered; ?></td>
				<td><select name="iom_inventory_ops_ordered">
					<?php foreach ($order_product_statuses as $order_product_status) { ?>
						<option value="<?php echo $order_product_status['order_product_status_id']; ?>"<?php echo ($order_product_status['order_product_status_id'] == $iom_inventory_ops_ordered) ? ' selected="selected"' : ''; ?>><?php echo $order_product_status['name']; ?></option>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td><?php echo $entry_iom_inventory_ops_cancelled; ?></td>
				<td><select name="iom_inventory_ops_cancelled">
					<?php foreach ($order_product_statuses as $order_product_status) { ?>
						<option value="<?php echo $order_product_status['order_product_status_id']; ?>"<?php echo ($order_product_status['order_product_status_id'] == $iom_inventory_ops_cancelled) ? ' selected="selected"' : ''; ?>><?php echo $order_product_status['name']; ?></option>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td><?php echo $entry_iom_inventory_ops_shipped; ?></td>
				<td><select name="iom_inventory_ops_shipped">
					<?php foreach ($order_product_statuses as $order_product_status) { ?>
						<option value="<?php echo $order_product_status['order_product_status_id']; ?>"<?php echo ($order_product_status['order_product_status_id'] == $iom_inventory_ops_shipped) ? ' selected="selected"' : ''; ?>><?php echo $order_product_status['name']; ?></option>
					<?php } ?>
				</select></td>
			</tr>

			<tr>
				<td><?php echo $entry_iom_inventory_os_shipready; ?></td>
				<td><select name="iom_inventory_os_shipready">
					<?php foreach ($order_statuses as $order_status) { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>"<?php echo ($order_status['order_status_id'] == $iom_inventory_os_shipready) ? ' selected="selected"' : ''; ?>><?php echo $order_status['name']; ?></option>
					<?php } ?>
				</select></td>
			</tr>
			
			<tr>
				<td><?php echo $entry_iom_inventory_os_inventoryrequired; ?></td>
				<td><select name="iom_inventory_os_inventoryrequired">
					<?php foreach ($order_statuses as $order_status) { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>"<?php echo ($order_status['order_status_id'] == $iom_inventory_os_inventoryrequired) ? ' selected="selected"' : ''; ?>><?php echo $order_status['name']; ?></option>
					<?php } ?>
				</select></td>
			</tr>
			
		</table>
    </form>
  </div>
</div>
<?php echo $footer; ?>