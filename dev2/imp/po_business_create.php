<?php
require_once("auth.php");
require_once("inc/functions.php");

if ($_GET['action'] == 'delete' && (int) $_GET['fileid']) {
	$fileid = (int) $_GET['fileid'];
	$user_id = $db->func_escape_string($_GET['user_id']);

	$db->db_exec("Delete from inv_po_customer_docs where id = '$fileid' and po_customer_id = '$user_id'");

	header("Location:po_business_create.php?id=$user_id&mode=edit");
	exit;
}
$user['customer_group_id'] = '6';
$mode = $_GET['mode'];
if ($mode == 'edit') {
	$user_id = (int) $_GET['id'];
	$user = $db->func_query_first("select a.*, b.customer_group_id from inv_po_customers as a LEFT JOIN oc_customer as b on (a.email = b.email) where a.id = '$user_id'");

	list($user['first_name'], $user['last_name']) = explode(" ", $user['contact_name']);
	$po_customer_docs = $db->func_query("select * from inv_po_customer_docs where po_customer_id = '$user_id'");
	$orders = $db->func_query('select * from `inv_orders` where `po_business_id` = "' . $user_id . '" order by order_date DESC');

	$rma_returns = $db->func_query("select * from inv_returns where email = '" . $user['email'] . "'");
	foreach ($rma_returns as $index => $rma_return) {
		$rma_returns[$index]['extra_details'] = $db->func_query("select sku , quantity , price , decision from inv_return_items where return_id = '" . $rma_return['id'] . "'");
	}
	$ordersData = '';
	$totalDue = 0;
	$totalAmount = 0;
    //echo "<pre>"; print_r($orders); exit;
	foreach ($orders as $i => $order) {
		$subtotalTotal = $db->func_query_first_cell('SELECT SUM(`product_price`) FROM `inv_orders_items` WHERE `order_id` = "' . $order['order_id'] . '"');
		$shipment = $db->func_query_first_cell('SELECT `shipping_cost` FROM `inv_orders_details` WHERE `order_id` = "' . $order['order_id'] . '"');

		if ($order['order_status'] != 'Estimate') {
			$sTotal = number_format(($subtotalTotal + $shipment), 2);
			$due = number_format((($subtotalTotal + $shipment) - $order['paid_price']), 2);
		} else {
			$sTotal = number_format(0,2);
			$due = number_format(0, 2);
		}
		$totalDue += (float) str_replace(',', '', $due);
		$totalAmount += (float) str_replace(',', '', $sTotal);
		$ordersData .= '<tr>';
		$ordersData .= '<td>' . ($i + 1) . '</td>';
		$ordersData .= '<td>' . americanDate($order['order_date']) . '</td>';
		$ordersData .= '<td><a href="viewOrderDetail.php?order=' . $order['order_id'] . '">' . $order['order_id'] . '</a></td>';
		if ($_SESSION['display_cost']) {
			$ordersData .= '<td>$' . $sTotal . '</td>';
			$ordersData .= '<td>$' . $due . '</td>';
		}
		$ordersData .= '<td>' . $order['order_status'] . '</td>';
		$ordersData .= '<td></td>';
		$ordersData .= '<td width="250px;">' . getComments($order['order_id']) . '</td>';
		$ordersData .= '<td>' . getAttachments($order['order_id']) . '</td>';
		$ordersData .= '</tr>';        
	}
}
if ($_POST['add']) {
	unset($_POST['add']);
	unset($_POST['attachments']);

	$company_name = $db->func_escape_string($_POST['company_name']);

	$isExist = $db->func_query_first("select id from inv_po_customers where company_name = '$company_name' or LOWER(email)='".$db->func_escape_string(strtolower($_POST['email']))."'");
	if (!$isExist || $user_id) {
		$user_arr = array();
		$user_arr = $_POST;

		$user_arr['contact_name'] = $db->func_escape_string($_POST['first_name'] . " " . $_POST['last_name']);

		$user_arr['firstname'] = $db->func_escape_string($_POST['first_name']);
		$user_arr['lastname'] = $db->func_escape_string($_POST['last_name']);
		$user_arr['is_fbb'] = ($_POST['is_fbb']) ? 1 : 0;
		$user_arr['is_qty_report'] = ($_POST['is_qty_report']) ? 1 : 0;
		$user_arr['date_created'] = date('Y-m-d H:i:s');
		unset($user_arr['customer_group_id']);

		unset($user_arr['first_name']);
		unset($user_arr['last_name']);
		unset($user_arr['xdata']);
		unset($user_arr['password']);

		if ($user_id) {
			$db->func_array2update("inv_po_customers", $user_arr, "id = '$user_id'");
		} else {
			$user_id = $db->func_array2insert("inv_po_customers", $user_arr);
		}

        //upload return item item images
		if ($_FILES['attachments']['tmp_name']) {
			$imageCount = 0;
			$count = count($_FILES['attachments']['tmp_name']);

			for ($i = 0; $i < $count; $i++) {
				$uniqid = uniqid();
				$name = explode(".", $_FILES['attachments']['name'][$i]);
				$ext = end($name);

				$destination = $path . "files/" . $uniqid . ".$ext";
				$file = $_FILES['attachments']['tmp_name'][$i];

				if (move_uploaded_file($file, $destination)) {
					$orderDoc = array();
					$orderDoc['attachment_path'] = "files/" . basename($destination);
					$orderDoc['type'] = $_FILES['attachments']['type'][$i];
					$orderDoc['size'] = $_FILES['attachments']['size'][$i];
					$orderDoc['date_added'] = date('Y-m-d H:i:s');
					$orderDoc['po_customer_id'] = $user_id;

					$db->func_array2insert("inv_po_customer_docs", $orderDoc);
					$imageCount++;
				}
			}
		}

		// OC Customer Creation

		$check = $db->func_query_first("SELECT * FROM oc_customer WHERE LOWER(email)='".$db->func_escape_string(strtolower($_POST['email']))."'");

		$oc = array();
		$oc['store_id'] = 0;
		$oc['firstname'] = $db->func_escape_string($_POST['first_name']);
		$oc['lastname'] = $db->func_escape_string($_POST['last_name']);
		$oc['email'] = $db->func_escape_string(strtolower($_POST['email']));
		if($_POST['password'])
		{
			$oc['password'] = $db->func_escape_string(md5($_POST['password']));
		}
		$oc['customer_group_id'] = $db->func_escape_string($_POST['customer_group_id']);
		$oc['status'] = 1;
		$oc['approved'] = 1;
		$oc['is_termed'] = 1;
		if($check)
		{
			$db->func_array2update("oc_customer", $oc,"LOWER(email)='".$db->func_escape_string(strtolower($_POST['email']))."'");
			$oc_customer_id = $check['customer_id'];
		}
		else
		{
			$oc['date_added'] = date("Y-m-d H:i:s");
			$oc_customer_id = $db->func_array2insert("oc_customer", $oc);
		}



		$db->db_exec("DELETE FROM inv_po_address WHERE po_customer_id='".$user_id."'");
		$db->db_exec("DELETE FROM oc_address WHERE customer_id='".$oc_customer_id."' ");
		$x=1;
		$_address_1 = '';
		$_address_2 = '';
		foreach($_POST['xdata'] as $key => $data) {
			if($x==1) {
				$_address_1 =$db->func_escape_string( $data['address']);
				$_telephone = $db->func_escape_string($data['telephone']);
				$_city = $db->func_escape_string($data['city']);
				$_state = $db->func_escape_string($data['state']);
				$_zip = $db->func_escape_string($data['zip']);

			}
			else if($x==2) {
				$_address_2 = $db->func_escape_string($data['address']);
			}

			$array['address'] = $db->func_escape_string($data['address']);
			$array['city'] = $db->func_escape_string($data['city']);
			$array['state'] = $db->func_escape_string($data['state']);
			$array['zip'] = $db->func_escape_string($data['zip']);
			$array['po_customer_id'] = (int)$user_id;
			$array['telephone'] = $db->func_escape_string($data['telephone']);
			if ($data['password']) {
				$array['password'] = $db->func_escape_string(md5($data['password']));
			}

			$po_address_id = $db->func_array2insert("inv_po_address",$array);


		// OC Address Entries 
			$oc_state_id = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE LOWER(name)='".strtolower($data['state'])."'");

			$oc_address = array();
			$oc_address['customer_id'] = $oc_customer_id;
			$oc_address['firstname'] = $db->func_escape_string($_POST['first_name']);
			$oc_address['lastname'] = $db->func_escape_string($_POST['last_name']);
			$oc_address['company'] = $db->func_escape_string($_POST['company_name']);
			$oc_address['address_1'] = $db->func_escape_string($array['address']);
			$oc_address['city'] = $array['city'];
			$oc_address['postcode'] = $array['zip'];
			$oc_address['country_id'] = 223;
			$oc_address['zone_id'] = $oc_state_id;

			if ($data['password']) {
				$oc_address['password'] = $db->func_escape_string(md5($data['password']));
			}


			$oc_address_id  = $db->func_array2insert("oc_address",$oc_address);

			$db->db_exec("UPDATE inv_po_address SET oc_address_id='".$oc_address_id."' WHERE address_id='".$po_address_id."'");
			if($x==1){
				$_oc_address_id = $oc_address_id;	
			}

			$x++;
		}

		$db->db_exec("UPDATE inv_po_customers SET address1='".$_address_1."',telephone='".$_telephone."',city='".$_city."',state='".$_state."',zip='".$_zip."',address2='".$_address_2."' WHERE id='".$user_id."'");
		$db->db_exec("UPDATE oc_customer SET telephone='".$_telephone."',address_id='".$_oc_address_id."' WHERE customer_id='".$oc_customer_id."'");

		$log = 'PO Customer was '. (($mode == 'edit')? 'updated': 'created') .' ' . linkToProfile(strtolower($_POST['email']));
		actionLog($log);
		header("Location:po_businesses.php");
		exit;
	} else {
		$_SESSION['message'] = "Either company or email already exist.";
		$user = $_POST;
		unset($user['xdata']);
		$rows = $_POST['xdata'];
	}
}

$business_types = array(array('id' => 'Education', 'value' => 'Education'),
	array('id' => 'Sole Propretor', 'value' => 'Sole Propretor'),
	array('id' => 'Partnership', 'value' => 'Partnership'),
	array('id' => 'Corporation', 'value' => 'Corporation'),
	array('id' => 'Corporation', 'value' => 'Corporation')
	);

$states = $db->func_query("select name from oc_zone where country_id = 223");
$c_groups = $db->func_query("select * from oc_customer_group_description");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Add PO Clients</title>

	<script>
		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

		function allowNum (t) {
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (isNaN(input)) {
				$(t).val(valid);
			}
		}

		function checkFile(e) {
			var file = $(e).val().split(".");
			var ext = file.pop();
			var allowed = ['png', 'jpeg', 'jpg', 'gif'];
			if ($.inArray(ext, allowed) >= 0) {

			} else {
				alert('This File is not Allowed');
				$(e).val('');
			}
		}

		function addRow(){
			var current_row = $('#variants tr').length+1;	
			var row = "<tr>"+
			" <td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata["+current_row+"][address]' style=\"width:300px\" required=\"\" /></td>"+
			" <td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata["+current_row+"][city]' required=\"\"  /></td>"+
			" <td align='center'><select name='xdata["+current_row+"][state]' required>"+
			+"<option value=''>Select One</option>";
			var select_menu = "";
			<?php foreach ($states as $state): ?>
			select_menu= select_menu+'<option value="<?php echo $state['name'] ?>"><?php echo $state['name'] ?></option>';
		<?php endforeach; ?>
		row = row+select_menu+"</select></td>"+
		" <td align='center'><input type='text' onkeyup='allowNum(this);' name='xdata["+current_row+"][zip]' required=\"\"/></td>"+
		" <td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata["+current_row+"][telephone]'  /></td>"+
		" <td align='center'><input type='password' onkeyup='checkWhiteSpace(this);' name='xdata["+current_row+"][password]' placeholder='Password'/></td>"+


		"<td align='center'><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>"+
		"</tr>";
		$("#variants").append(row);		
		current_row++;	 
	}
</script>
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php'; ?>
		</div>

		<?php if ($_SESSION['message']): ?>
			<div align="center"><br />
				<font color="red"><?php
					echo $_SESSION['message'];
					unset($_SESSION['message']);
					?><br /></font>
				</div>
			<?php endif; ?>

			<form action="" method="post" enctype="multipart/form-data">
				<h2>Add PO Clients</h2> 

				<a href="po_customer_summary.php?customer_id=<?= $_GET['id']; ?>&action=view&due_date=1" target="_blank" class="button">Download Summary 1</a> <a href="po_customer_summary.php?customer_id=<?= $_GET['id']; ?>&action=view&due_date=0" target="_blank" class="button">Download Summary 2</a><br /><br />
				<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
					<tr>
						<td>Company Name:</td>
						<td><input type="text" name="company_name" value="<?php echo @$user['company_name']; ?>" required /></td>

						<td>Company Name 2:</td>
						<td><input type="text" name="company_name_2" value="<?php echo @$user['company_name_2']; ?>" /></td>
					</tr>

					<tr>
						<td>Business Type:</td>
						<td>
							<?php echo createField("business_type", "business_type", "select", $user['business_type'], $business_types, "required"); ?>
						</td>

						<td>Email:</td>
						<td><input type="text" name="email" value="<?php echo @$user['email']; ?>" autocomplete="off" required /></td>
					</tr>

					<tr>
						<td>First Name:</td>
						<td><input type="text" name="first_name" value="<?php echo @$user['first_name']; ?>" autocomplete="off" required /></td>

						<td>Last Name:</td>
						<td><input type="text" name="last_name" value="<?php echo @$user['last_name']; ?>" autocomplete="off" required /></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type="password" name="password" value="<?php echo @$user['password'];?>" autocomplete="off" <?=($_GET['mode']=='edit'?'':'required');?> /><br />
							<?php
							if($_GET['mode']=='edit')
							{
								?>
								<small>Leave blank in case you don't want to update password.</small>
								<?php
							}
							?>
						</td>
						<td>Customer Group</td>
						<td>
							<select name="customer_group_id">
								<?php foreach ($c_groups as $group) { ?>
								<option value="<?= $group['customer_group_id']; ?>" <?= ($user['customer_group_id'] == $group['customer_group_id'])? 'selected=""': '';?>><?= $group['name']; ?></option>
								<?php }?>
							</select>
						</td>

					</tr>

					<tr style="display:none">
						<td>Address 1:</td>
						<td><input type="text" name="address1" value="<?php echo @$user['address1']; ?>" /></td>

						<td>Address 2:</td>
						<td><input type="text" name="address2" value="<?php echo @$user['address2']; ?>" /></td>
					</tr>

					<tr style="display:none">
						<td>City:</td>
						<td><input type="text" name="city" value="<?php echo @$user['city']; ?>"  /></td>

						<td>State:</td>
						<td>
							<select name="state" >
								<option value="">Select One</option>

								<?php foreach ($states as $state): ?>
									<option value="<?php echo $state['name'] ?>" <?php if ($user['state'] == $state['name']): ?> selected="selected"  <?php endif; ?>><?php echo $state['name'] ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>

					<tr style="display:none">
						<td>Zip Code:</td>
						<td><input type="text" name="zip" maxlength="5" value="<?php echo @$user['zip']; ?>"  /></td>

						<td>Telephone:</td>
						<td><input type="text" name="telephone" value="<?php echo @$user['telephone']; ?>"  /></td>
					</tr>

					<tr>
						<td>Tax ID:</td>
						<td><input type="text" name="tax_id" value="<?php echo @$user['tax_id']; ?>" /></td>

						<td>Fed:</td>
						<td><input type="text" name="fed" value="<?php echo @$user['fed']; ?>" /></td>
					</tr>

					<tr>
						<td>Attachments:</td>
						<td>
							<input type="file" name="attachments[]" multiple />
						</td>

						<td>Extra</td>
						<td>
							<input type="checkbox" name="is_fbb" value="1" <?php echo ($user['is_fbb'])? 'checked="checked"': '';?>/> Is FBA Customer? <input type="checkbox" name="is_qty_report" value="1" <?php echo ($user['is_qty_report'])? 'checked="checked"': '';?>/> QTY Report Customer 
						</td>
					</tr>

					<tr>

						<td colspan="4" align="center">
							<table border="1" width="90%" cellpadding="5" cellspacing="0" align="center" id="variants" style="">
								<tr>	

									<th>Address</th>
									<th>City</th>
									<th>State</th>
									<th>Zip</th>
									<th>Phone</th>
									<th>Password</th>


									<th>
										<a href="javascript://" onclick="addRow();">Add Row</a>
									</th>
								</tr>	
								<?php
								if(!isset($rows))
								{
									$rows = $db->func_query("SELECT * FROM inv_po_address WHERE po_customer_id='".$user_id."'");
								}
								$i=1;
								foreach($rows as $row)
								{

									?>
									<tr>
										<td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata[<?php echo $i;?>][address]' style="width:300px" value="<?php echo $row['address'];?>" required=""  />

											<td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata[<?php echo $i;?>][city]' value="<?php echo $row['city'];?>" required=""  /></td>
											<td align='center'>   <select name="xdata[<?php echo $i;?>][state]" required>
												<option value="">Select One</option>

												<?php foreach ($states as $state): ?>
													<option value="<?php echo $state['name'] ?>" <?php if ($row['state'] == $state['name']): ?> selected="selected"  <?php endif; ?>><?php echo $state['name'] ?></option>
												<?php endforeach; ?>
											</select></td>
											<td align='center'><input type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][zip]' value="<?php echo $row['zip'];?>" required="" /></td>
											<td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata[<?php echo $i;?>][telephone]' value="<?php echo $row['telephone'];?>" /></td>
											<td align='center'><input type='password' onkeyup='checkWhiteSpace(this);' name='xdata[<?php echo $i;?>][password]' value="" placeholder="Password"/></td>

											<td align='center'><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>
										</tr>
										<?php 
										$i++;
									}
									?>



								</table>

							</td>
						</tr>

						<tr>
							<td colspan="4" align="center">
								<input type="submit" name="add" value="Submit" class="button" />
							</td>
						</tr>
					</table>

					<?php if ($po_customer_docs): ?>
						<h2>Attachments</h2>
						<table border="1" cellpadding="10" width="40%">
							<tr>
								<th>Date</th>
								<th>File</th>
								<th>Action</th>
							</tr>
							<?php foreach ($po_customer_docs as $attachment): ?>
								<tr>
									<td><?php echo americanDate($attachment['date_added']); ?></td>
									<td><?php echo $attachment['type']; ?></td>
									<td>
										<a href="<?php echo $host_path . "" . $attachment['attachment_path']; ?>">download</a>
										|

										<a href="po_business_create.php?action=delete&fileid=<?php echo $attachment['id'] ?>&user_id=<?php echo $user_id; ?>" onclick="if (!confirm('Are you sure, You want to delete this file?')) {
											return false;
										}">delete</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>	
					<?php endif; ?>	  
				</form>

				<br /><br />
				<?php
				if ($orders) {
					?>
					<h2 align="center">Customer Orders History</h2>
					<div style="width:70%;">
						<?php
						if ($_SESSION['display_cost']) {
							?>
							<table style="border-collapse:collapse;" border="1" width="100%" cellpadding="10" cellspacing="0">
								<tbody>
									<tr>
										<th>Total Amount:</th>
										<td>$<?= $totalAmount; ?></td>
										<th>Total Amount Due:</th>
										<td>$<?= $totalDue; ?></td>
									</tr>
								</tbody>
							</table>
							<?php
						}
						?>
					</div>
					<br /><br />
					<div style="max-height:800px;overflow:scroll;width:70%;">
						<table style="border-collapse:collapse;" border="1" width="100%" cellpadding="10" cellspacing="0">
							<tbody>
								<tr>
									<th>#</th>
									<th>Added</th>
									<th>Order ID</th>
									<?php if ($_SESSION['display_cost']) { ?>
									<th>Total $</th>
									<th>Due $</th>
									<?php } ?>

									<th>Status</th>
									<th>Tracking No.</th>
									<th>Comments</th>
									<th>Attachments</th>
								</tr>
								<?php
								echo $ordersData;
								?>
							</tbody>
						</table>
					</div>
					<?php
				}
				?>

				<?php if ($rma_returns) { ?>
				<br /><br />
				<h2 align="center">Customer Returns</h2>

				<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
					<tr>
						<th>Added</th>
						<th>Order ID</th>
						<th>RMA #</th>
						<th>Status</th>
						<th>Items Returned</th>
						<th>Amount</th>
						<th>Comments</th>
					</tr>

					<?php foreach ($rma_returns as $k => $rma_return) { ?>
					<tr>
						<td><?php echo americanDate($rma_return['date_added']); ?></td>	

						<td><a href="viewOrderDetail.php?order=<?php echo $rma_return['order_id']; ?>"><?php echo $rma_return['order_id']; ?></a></td>

						<td>
							<a href="return_detail.php?rma_number=<?php echo $rma_return['rma_number']; ?>">
								<?php echo $rma_return['rma_number']; ?>
							</a>
						</td>
						<td><?php echo ($rma_return['rma_status'] == 'In QC') ? 'QC Completed' : $rma_return['rma_status']; ?></td>
						<td>
							<?php
							$amount = 0;
							foreach ($rma_return['extra_details'] as $item) {
								?>
								<?php echo $item['sku']; ?> / <?php echo $item['decision'] ?><br />

								<?php $amount = $amount + $item['price']; ?>
								<?php } ?>
							</td>
							<td>$<?php echo $amount; ?></td>
							<td></td>
						</tr>
						<?php } ?>
					</table>	
					<br /><br />

					<?php } ?>
				</body>
				</html>