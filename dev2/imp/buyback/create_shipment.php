<?php
require_once("../auth.php");
require_once("../inc/functions.php");
$table = "inv_buy_back";
$page = "inputs.php.php";
page_permission("buyback_create_shipment");
function getBuyBackNo(){
	
	global $db;
	
	
	$prefix="LBB";
	
$last_number = $db->func_query_first("select max(replace(shipment_number,'$prefix','')) as shipment_number from oc_buyback where shipment_number LIKE '%$prefix%'");
	
	
	$last_number = $last_number['shipment_number'];
	
	

	if($last_number >= 999 && $last_number < 9999){
		$rma_number = $prefix."0".($last_number+1);
	}
	elseif($last_number >= 99 && $last_number < 999){
		$rma_number = $prefix."00".($last_number+1);
	}
	elseif($last_number >= 9){
		$rma_number = $prefix."000".($last_number+1);
	}
	elseif($last_number < 9){
		$rma_number = $prefix."0000".($last_number+1);
	}
	else{
		$rma_number = $prefix."".($last_number+1);
	}

	return $rma_number;
	
	}
if($_POST['add']){
	$data = array();

	$option = $_POST['main']['data_option'];
	unset($_POST['main']['data_option']);
	$data = $_POST['main'];
	$data['shipment_number']=getBuyBackNo();
	$data['address_id']='-1';
	//$data['option']=$data_option;
	$data['date_added']= date('Y-m-d H:i:s');
	$data['added_by']= $_SESSION['user_id'];
	$buyback_id = $db->func_array2insert("oc_buyback",$data);
	//unset($data);
	$total = 0.00;
	foreach($_POST['detail'] as $key => $detail)
	{
		if($detail['qty'])
		{
			// $sub_total = ((int)$detail['oem_quantity'] * $detail['oem_price']) + ((int)$detail['non_oem_quantity'] * $detail['non_oem_price']) ;
		$detail_data = array();
		
		$detail_data=$detail;
		$detail_data['buyback_id']=$buyback_id;
		// $detail_data['sub_total'] = $sub_total;
		$db->func_array2insert("oc_buyback_products",$detail_data);
	
		$total+=$sub_total;
		}
	}
	if($data['payment_type']=='cash')
	{
		$cash_discount = $db->func_query_first_cell("SELECT cash_discount FROM inv_buy_back");
		$discount = ((float)$total*(float)$cash_discount) / 100;
		$total = $total - $cash_discount;
		$total = round($total,4);
	}
	
	$db->db_exec("UPDATE oc_buyback SET total='$total',`option`='$option' WHERE buyback_id='$buyback_id' ");

	addComment('buyback', array('id'=>$buyback_id,'comment'=>'Manual BuyBack has been created'));
	$_SESSION['message'] = 'BuyBack Created Successfully';
	header("Location: ".$host_path."buyback/shipments.php");
	exit;
}

$row = $db->func_query_first("SELECT * FROM inv_buy_back limit 1");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>LCD Buy Back Program</title>
</head>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<script>
function phoneMask(e){


						var s=e.value;
						var n = (s.length)-7;
						if(n==3){var p=n;}else{var p=4;}
						var regex = new RegExp('(\\d{3})(\\d{'+p+'})(\\d{4})');
						var text = s.replace(regex, "($1) $2-$3");
						e.value=text;

		
}
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

	
</script>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once '../inc/header.php';?>
		</div>

		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<form action="" method="post" enctype="multipart/form-data">
			<h2>LCD Buy Back Program</h2>
			<table align="center" border="1" width="85%" cellpadding="5" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td><strong>Firstname</strong></td>
					<td><input type="text"  required name="main[firstname]" id="firstname" value="" />
					<input type="hidden" name="main[customer_id]" id="customer_id" value="0">

					<small style="font-size:8px">(<a class="fancybox3 fancybox.iframe" href="buyback_customer_lookup_create.php">Map Customer</a>)</small>
					</td>
				</tr>
<tr>
					<td><strong>Lastname</strong></td>
					<td><input type="text"  name="main[lastname]" id="lastname" value="" /></td>
				</tr>
<tr>
					<td><strong>Email</strong></td>
					<td><input type="text" required  name="main[email]" id="email" value="" /></td>
				</tr>
<tr>
					<td><strong>Telephone</strong></td>
					<td><input type="text" onkeyup="phoneMask(this)" required  name="main[telephone]" id="telephone" value="" /></td>
				</tr>
<tr>
					<td><strong>Address</strong></td>
					<td><input type="text" required  name="main[address_1]" id="address_1" value="" /></td>
				</tr>
<tr>
					<td><strong>City</strong></td>
					<td><input type="text" required  name="main[city]" id="city" value="" /></td>
				</tr>

				</tr>
<tr>
					<td><strong>Zip</strong></td>
					<td><input type="text" required  name="main[postcode]" id="postcode" value="" /></td>
				</tr>

				</tr>
<tr>
					<td><strong>State</strong></td>
					<td>
							<?php
							$states_query = $db->func_query("SELECT zone_id,name FROM oc_zone WHERE country_id='223' AND status=1 ORDER BY name");
							?>
							<select id="xstate" tabindex="9" required name="main[zone_id]"  style="width:156px;">
								<option value="">Select State</option>
								<?php
								foreach($states_query as $state)
								{
									?>
									<option value="<?php echo $state['zone_id'];?>" ><?php echo $state['name'];?></option>
									<?php 

								}
								?>
							</select>
							
						</td>
				</tr>

				
</tr>
<tr>
					<td><strong>Payment Type</strong></td>
					<td>
					<select id="payment_type"  required name="main[payment_type]"  style="width:156px;">
					<option value="">Select Payment</option>
					<option value="store_credit">Store Credit</option>
					<option value="cash">Cash</option>


					</select>
					</td>
				</tr>

				</tr>
<tr>
					<td><strong>PayPal Email</strong></td>
					<td><input type="text"  name="main[paypal_email]" id="paypal_email" value="" /></td>
				</tr>

				</tr>
<tr>
					<td><strong>How to Process?</strong></td>
					<td>
					<select id="option"  required name="main[data_option]"  style="width:156px;">
					<option value="">Please Select</option>
					<option value="return">Return</option>
					<option value="dispose">Dispose</option>
					<option value="contact_customer">Contact Customer</option>



					</select>
					</td>
				</tr>




				<tr>
					<td colspan="2">
						<table border="1" width="90%" cellpadding="5" cellspacing="0" align="center" id="variants" style="">
							<tr>	

								<th>Image</th>
								<th>SKU</th>
								<th>Description</th>
								<th>Quantity</th>
								

							</tr>	
							<?php
							$rows = $db->func_query("SELECT * FROM inv_buy_back");
							$i=1;
							foreach($rows as $row)
							{
								if($row['image'])
								{
									$image = '../files/'.$row['image'];	 
								}
								else
								{
									$image = 'https://phonepartsusa.com/dev2/image/cache/no_image-100x100.jpg'; 
								}
								?>
								<tr>
									<td align='center'><div><img height="100" width="100" id='img_<?php echo $i;?>' src='<?php echo $image;?>' style='cursor:pointer;'></div></td>

									<td align='center'><?=$row['sku'];?>

									<input type="hidden" name="detail[<?=$i;?>][sku]" value="<?=$row['sku'];?>">
									<input type="hidden" name="detail[<?=$i;?>][description]" value="<?=$row['description'];?>">
									<input type="hidden" name="detail[<?=$i;?>][image_path]" value="<?=$image;?>">
									<input type="hidden" name="detail[<?=$i;?>][oem_a_price]" value="<?=$row['oem_a'];?>">
									<input type="hidden" name="detail[<?=$i;?>][oem_b_price]" value="<?=$row['oem_b'];?>">
									<input type="hidden" name="detail[<?=$i;?>][oem_c_price]" value="<?=$row['oem_c'];?>">
									<input type="hidden" name="detail[<?=$i;?>][oem_d_price]" value="<?=$row['oem_d'];?>">
									<input type="hidden" name="detail[<?=$i;?>][non_oem_a_price]" value="<?=$row['non_oem_a'];?>">
									<input type="hidden" name="detail[<?=$i;?>][non_oem_b_price]" value="<?=$row['non_oem_b'];?>">
									<input type="hidden" name="detail[<?=$i;?>][non_oem_c_price]" value="<?=$row['non_oem_c'];?>">
									<input type="hidden" name="detail[<?=$i;?>][non_oem_d_price]" value="<?=$row['non_oem_d'];?>">
									<input type="hidden" name="detail[<?=$i;?>][salvage_price]" value="<?=$row['salvage'];?>">

									</td>
									<td align='center'><?php echo $row['description'];?>  </td>
									<td align='center'><input type='text' onkeyup='allowNum(this);' name='detail[<?php echo $i;?>][qty]' value="0" /></td>
								</tr>
								<?php 
								$i++;
							}
							?>



						</table>
					</td>

				</tr>

			</table>

			<br /><br />
			
			<br><br>

			<div style="text-align:center">  <input type="submit" name="add" value="Submit" /></div>

		</form>
	</div>
</body>
</html>		 		 