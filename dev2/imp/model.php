<?php  
require_once("auth.php");
require_once("inc/functions.php");
$_POST = escapeArrayDB($_POST);
if ($_POST['action'] == 'delete_subModel') {
	$db->db_exec("DELETE FROM inv_model_dt WHERE sub_model_id='".$_POST['sub_model_id']."'");
	$db->db_exec("DELETE FROM inv_model_carrier WHERE sub_model_id = '".$_POST['sub_model_id']."'");
	exit;
}
if ($_POST['action'] == 'delete_carrier') {
	$db->db_exec("DELETE FROM inv_model_carrier WHERE sub_model_id = '".$_POST['sub_model_id']."' AND carrier_id = '".$_POST['carrier_id']."'");
	exit;
}
$mode = $_GET['mode'];
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$row = $db->func_query_first("select * from inv_model_mt where model_id = '$id'");
	$row['keyword'] = $db->func_query_first_cell("SELECT keyword FROM oc_url_alias WHERE query='catalog_model_id=".$id."'");
}
if($_POST['add']){
	unset($_POST['add']);
	
	$allowed = array('png', 'jpeg', 'jpg');
	$uploaded = 0;
	if ($_FILES['upFile']['tmp_name']) {
		$uniqid = uniqid();
		$name = explode(".", $_FILES['upFile']['name']);
		$ext = end($name);
		$fileName = $uniqid . '-' . $_POST['email'] . '.' . $ext;
		$dir = 'images/models/';
		$destination = $path . $dir . $fileName;
		$file = $_FILES['upFile']['tmp_name'];
		if (in_array($ext, $allowed)) {
			if (move_uploaded_file($file, $destination)) {
				$message .= 'Logo Uploaded <br>';
				$uploaded = 1;
			}
		} else {
			$message .= 'Logo Not Uploaded <br>';
		}
	}
	$_POST['image'] = ($uploaded)? $dir . $fileName : '';
	//echo "<pre>";
	//print_r($_POST);exit;
	$array = array();
		//$array = $_POST;
	$array['manufacturer_id']= $_POST['manufacturer_id'];
	$array['device']= $_POST['device'];
	$array['variant_exists']= $_POST['variant_exist'];
	$array['model_type_id'] = $_POST['model_type'];
	$array['model_connection_id'] = $_POST['model_connection'];
	$array['length'] = $_POST['length'];
	$array['height'] = $_POST['height'];
	$array['width'] = $_POST['width'];
	if($_POST['image']!='')
	{
	$array['image'] = $_POST['image'];
		
	}
	$array['date_added'] = date('Y-m-d h:i:s');
	if ($id) {
		$db->func_array2update("inv_model_mt",$array,"model_id = '$id'");
	} else{
		$id=	$db->func_array2insert("inv_model_mt",$array);
	}
		//$db->db_exec("DELETE FROM inv_model_dt WHERE model_id='".$id."'");
	$db->db_exec("DELETE FROM inv_model_screw WHERE model_id='".$id."'");
	foreach($_POST['screw_driver'] as $screw_driver) {
		$array = array();
		$array['model_id'] = $id;
		$array['screw_driver_id'] = $screw_driver;
		$db->func_array2insert("inv_model_screw",$array);	
	}
	if ($_POST['variant_exist']==0) {
		$array = array();
		$array['sub_model']=$_POST['model_dt1'][0]['sub_model'];
		$array['model_id']=$id;
		$array['order_no']=1;
		$sub_model_id = $db->func_array2insert("inv_model_dt",$array);
		$data = array();
		$data['sub_model_id']= $sub_model_id;
		$data['carrier_id']= $_POST['model_dt1'][0]['carrier'];
		$data['order_no']= 1;
		$db->func_array2insert("inv_model_carrier",$data);
	} else {
		$i=1;
		foreach($_POST['model_dt'] as $model) {
			$array = array();
			$array['sub_model']=$model['sub_model'];
			$array['model_id']=$id;
			
			$array['order_no']=$i;
			
			if ($model['sub_model_id']) {
				$sub_model_id = $model['sub_model_id'];
				$db->func_array2update("inv_model_dt",$array,"sub_model_id = '$sub_model_id'");
			} else {
				$sub_model_id = $db->func_array2insert("inv_model_dt",$array);
			}
			
			
			$j=1;
			foreach($model['carrier'] as $model_carrier) {
				
				if (!$db->func_query_first_cell("SELECT sub_model_id FROM inv_model_carrier WHERE sub_model_id = '$sub_model_id' AND carrier_id = '$model_carrier'")) {
					$data = array();
					$data['sub_model_id']= $sub_model_id;
					$data['carrier_id']= $model_carrier;
					$data['order_no']= $j;
					$db->func_array2insert("inv_model_carrier",$data);
					$j++;
				}
			}
			$i++;
			
		}
	}
	$db->db_exec("DELETE FROM oc_url_alias WHERE query='catalog_model_id=".$id."'");
	$check_query = $db->func_query_first("SELECT * FROM oc_url_alias where keyword='".strtolower($db->func_escape_string($_POST['keyword']))."'");
	if($check_query)
	{
	    $_POST['keyword']  = $_POST['keyword'].'-';
	}
	$db->db_exec("INSERT INTO oc_url_alias  SET query='catalog_model_id=".$id."',keyword='".strtolower($db->func_escape_string($_POST['keyword']))."'");
	$_SESSION['message'] = $message . "Model Updated";
	header("Location:model_list.php");
	exit;
	
}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Add/Edit Model</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<style type="text/css" media="all">
		.upMain {
			position: relative;
		}
		.ui.blue.button.upMain {
			display: inline-block;
		}
	</style>
</head>
<body>
	<div align="center">
		<div align="center" style="display:none"> 
			<?php  include_once 'inc/header.php'; ?>
		</div>
		<?php  if($_SESSION['message']): ?>
			<div align="center"><br />
				<font color="red"><?php  echo $_SESSION['message']; unset($_SESSION['message']); ?><br /></font>
			</div>
		<?php  endif; ?>
		<form action="" method="post" enctype="multipart/form-data">
			<a href="devices_new_settings.php" title="back">Back</a>	<h2>Add Model</h2>
			<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Manufacturer</td>
					<td>
						<?php 
						$manufacturers = $db->func_query("SELECT * FROM inv_manufacturer WHERE status=1");
						 ?>
						<select id="manufacturer_id" name="manufacturer_id" required>
							<option value="">Please Select</option>
							<?php 
							foreach($manufacturers as $rec)
							{
								 ?>
								<option value="<?php  echo $rec['manufacturer_id']; ?>" <?php   if($row['manufacturer_id']==$rec['manufacturer_id']) echo 'selected'; ?>><?php  echo $rec['name']; ?></option>
								<?php  
							}
							 ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Model Type</td>
					<td>
						<?php 
						$model_type = $db->func_query("SELECT * FROM inv_model_type WHERE status=1");
						 ?>
						<select id="model_type" name="model_type" required>
							<option value="">Please Select</option>
							<?php 
							foreach($model_type as $rec)
							{
								 ?>
								<option value="<?php  echo $rec['model_type_id']; ?>" <?php   if($row['model_type_id']==$rec['model_type_id']) echo 'selected'; ?>><?php  echo $rec['name']; ?></option>
								<?php  
							}
							 ?>
						</select> <a href="javascript:void(0);" onclick="OpenPopup('model_type.php?mode=new')">Add New</a>
					</td>
				</tr>
				<tr>
					<td>Device Name</td>
					<td><input type="text" name="device" value="<?php  echo @$row['device']; ?>" required /></td>
				</tr>
				<tr>
					<td>SEO URL <small>(No spaces, only dashes)</small></td>
					<td><input type="text" name="keyword" value="<?php  echo @$row['keyword']; ?>" required /></td>
				</tr>
				<tr>
					<td>Logo</td>
					<td>
						<label class="ui blue button upMain" style="color: #fff;" for="mainimageup">
							<input onchange="validateFileUp(this);" type="file" style="opacity: 0; position: absolute; width: 100%; height: 23px; top: 0; left: 0;" name="upFile" accept="image/jpeg,image/png">
							Upload New
						</label>
						<div id="imagePrv" style="display: inline-block;">
							<?php  if ($row['image']) {  ?>
							<a class="fancybox2 fancybox.iframe" href="<?php  echo $host_path . $row['image'];  ?>" target="_blank">
								<img width="100px" src="<?php  echo $host_path . $row['image'];  ?>" alt="" />
							</a>
							<?php  }  ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>Variant Exist?</td>
					<td><input type="checkbox" name="variant_exist" onchange="variant(this)" value="1" <?php  if($row['variant_exists']): ?> checked="checked" <?php  endif; ?> /></td>
				</tr>
			</table>
			<br />
			<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td colspan="2" style="font-weight:bold;text-align:center">About this Model</td>
				</tr>
				<tr>
					<td>USB Connection</td>
					<td>
						<?php 
						$model_connection = $db->func_query("SELECT * FROM inv_model_connection WHERE status=1");
						 ?>
						<select id="model_connection" name="model_connection" required>
							<option value="">Please Select</option>
							<?php 
							foreach($model_connection as $rec)
							{
								 ?>
								<option value="<?php  echo $rec['model_connection_id']; ?>" <?php   if($row['model_connection_id']==$rec['model_connection_id']) echo 'selected'; ?>><?php  echo $rec['name']; ?></option>
								<?php  
							}
							 ?>
						</select> <a href="javascript:void(0);" onclick="OpenPopup('model_connection.php?mode=new')">Add New</a>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-weight:bold;text-align:center">Phone Dimensions</td>
				</tr>
				<tr>
					<td>Length</td>
					<td><input type="text" name="length" value="<?php  echo @$row['length']; ?>"  /></td>
				</tr>
				<tr>
					<td>Height</td>
					<td><input type="text" name="height" value="<?php  echo @$row['height']; ?>"  /></td>
				</tr>
				<tr>
					<td>Width</td>
					<td><input type="text" name="width" value="<?php  echo @$row['width']; ?>"  /></td>
				</tr>
				<tr>
					<td colspan="2" style="font-weight:bold;text-align:center">Screw Drivers</td>
				</tr>
				<tr>
					<td colspan="2"><?php 
						$screw_drivers = $db->func_query("SELECT * FROM inv_screw_driver ORDER BY name");
						foreach($screw_drivers as $screw_driver)
						{
							$check_screw = $db->func_query_first("SELECT screw_driver_id From inv_model_screw WHERE model_id='".$row['model_id']."' AND screw_driver_id='".$screw_driver['id']."'");
							 ?>
							<input type="checkbox" name="screw_driver[]" value="<?php  echo $screw_driver['id']; ?>" <?php  if(in_array($screw_driver['id'],$check_screw)) { echo 'checked';}  ?> /> <?php  echo $screw_driver['name']; ?><br />
							<?php 	
						}
						 ?></td>
					</tr>
				</table>
				<br /><br />
				<?php 
				
				$row2 = $db->func_query_first("select * from inv_model_dt where model_id = '$id'");
				 ?>
				<table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="non_variants" style="<?php  if($row['variant_exists']==1) {  ?>display:none <?php  }  ?>">
					<tr>	
						<th>#</th>
						<th>Model</th>
						<th>Carrier</th>
					 	 <!--<th>
					 	 	 <a href="javascript://" onclick="addRow();">Add Row</a>
					 	 	</th>-->
					 	 </tr>	
					 	 <tr>
					 	 	<td>1</td>
					 	 	<td align="center"><input type="text" name="model_dt1[0][sub_model]" value="<?php  echo $row2['sub_model']; ?>" /></td>
					 	 	<td align="center"><select name="model_dt1[0][carrier]">
					 	 		<option value="">Please select</option>
					 	 		<?php 
					 	 		$carriers = $db->func_query("SELECT * FROM inv_carrier ORDER BY name");
					 	 		$carrier_row = $db->func_query_first("SELECT * FROM inv_model_carrier WHERE sub_model_id='".$row2['sub_model_id']."'");
					 	 		foreach($carriers as $carrier)
					 	 		{
					 	 			 ?>
					 	 			<option value="<?php  echo $carrier['id']; ?>" <?php  if($carrier_row['carrier_id']==$carrier['id']) echo 'selected';  ?>><?php  echo $carrier['name']; ?></option>
					 	 			<?php   
					 	 		}
					 	 		 ?>
					 	 	</select>
					 	 </td>
					 	</tr>
					 </table>
					 <table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="variants" style="<?php  if($row['variant_exists']==0) {  ?>display:none <?php  }  ?>">
					 	<tr>	
					 		<th>#</th>
					 		<th>Sub Model</th>
					 		<th>Carrier</th>
					 		<th>
					 			<a href="javascript://" onclick="addRow();">Add Row</a>
					 		</th>
					 	</tr>	
					 	<?php 
					 	if($row)
					 	{
					 		$rows_variant = $db->func_query("SELECT * FROM inv_model_dt WHERE model_id='".$id."' ORDER BY order_no");
					 		$i=0;
					 		foreach($rows_variant as $row_variant)
					 		{
					 			$carrier_rows = $db->func_query("SELECT * FROM inv_model_carrier WHERE sub_model_id='".$row_variant['sub_model_id']."' ORDER BY order_no");
					 			 ?>
					 			<tr class="subModelRow subModelRow<?php  echo $row_variant['sub_model_id'];  ?>">
					 				<td><?php  echo $i+1; ?></td>
					 				<td align="center"><input type="text" name="model_dt[<?php  echo $i; ?>][sub_model]" value="<?php  echo $row_variant['sub_model']; ?>" /><input type="hidden" name="model_dt[<?php  echo $i; ?>][sub_model_id]" value="<?php  echo $row_variant['sub_model_id']; ?>" /></td>
					 				<td align="center">
					 					<?php 
					 					foreach($carrier_rows as $kx => $carrier_row)
					 					{
					 						 ?>
					 						<div class="carrierRow<?php  echo $row_variant['sub_model_id'] . $carrier_row['carrier_id'];  ?>">
					 							<?php  if ($kx > 0) {  ?><a class="remove" href='javascript://' onClick='removeCarrier(<?php  echo $carrier_row['carrier_id'];  ?>, <?php  echo $row_variant['sub_model_id'];  ?>)'>x</a> <?php  }  ?>
					 								<select name="model_dt[<?php  echo $i; ?>][carrier][]">
					 									<option value="">Please select</option>
					 									<?php 
					 									$carriers = $db->func_query("select * from inv_carrier ORDER BY name");
					 									foreach($carriers as $carrier)
					 									{
					 										 ?>
					 										<option value="<?php  echo $carrier['id']; ?>" <?php  if($carrier_row['carrier_id']==$carrier['id']) echo 'selected';  ?>><?php  echo $carrier['name']; ?></option>
					 										<?php   
					 									}
					 									 ?>
					 								</select> <a href='javascript://' onClick='addCarrier(this)'>+</a>
					 							</div>
					 							<?php  
					 						}
					 						 ?>
					 					</td>
					 					<td><a href='javascript://' onclick='removeSubModel(<?php  echo $row_variant['sub_model_id'];  ?>);'>X</a></td>
					 				</tr>
					 				<?php 	
					 				$i++;
					 			}
					 		}
					 		 ?>
					 	</table>
					 	<br /><br />
					 	<div style="text-align:center">  <input type="submit" name="add" value="Submit" /></div>
					 </form>
					</div>
				</body>
				<script>
					function removeCarrier(carrier_id, sub_model_id) {
						$.ajax({
							url: 'model.php',
							type: 'POST',
							dataType: 'json',
							data: {carrier_id: carrier_id, action: 'delete_carrier'},
						}).always(function() {
							$('.carrierRow'+ sub_model_id +carrier_id).remove();
						});
					}
					function removeSubModel(sub_model_id) {
						$.ajax({
							url: 'model.php',
							type: 'POST',
							dataType: 'json',
							data: {sub_model_id: sub_model_id, action: 'delete_subModel'},
						}).always(function() {
							$('.subModelRow'+sub_model_id).remove();
						});
					}
					var current_row = <?php  echo (count($rows_variant) + 1) ?>;
					function addRow(){
						var row = "<tr>"+
						"<td>"+(current_row)+"</td>"+
						" <td align='center'><input type='text' name='model_dt["+current_row+"][sub_model]'  /></td>"+
						"<td align='center'><select name='model_dt["+current_row+"][carrier][]'><option value=''>Please select</option><?php 
						$carriers = $db->func_query("SELECT * FROM inv_carrier ORDER BY name");
						foreach($carriers as $carrier)
						{
							 ?><option value='<?php  echo $carrier['id']; ?>' ><?php  echo addslashes($carrier['name']); ?></option><?php   
						}
						 ?></select> <a href='javascript://' onClick='addCarrier(this)'>+</a></td>"+
						"<td><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>"+
						"</tr>";
						$("#variants").append(row);		
						current_row++;	 
					}
					function addCarrier(obj)
					{
						$parent = ($(obj).parent());   
						$clone = $parent.clone();
						$clone.removeAttr('class');
						if ($clone.find('.remove').length) {
							$clone.find('.remove').attr('onclick', '$(this).parent().remove();');
						} else {
							$clone.prepend('<a class="remove" href="javascript:void(0)" onClick="$(this).parent().remove();">x</a>');
						}
						$clone.find('select').val('');
						$parent.parent().append($clone);
					}
					function OpenPopup(url)
					{
						window.open (url+'&window=1', "mywindow","location=1,status=1,scrollbars=1, width=600,height=1000");	
					}
				</script>
				<script>
					function variant(obj)
					{
						if(obj.checked==true)
						{
							$('#non_variants').fadeOut();
							$('#variants').fadeIn();	
						}
						else
						{
							$('#non_variants').fadeIn();
							$('#variants').fadeOut();	
						}
					}
				</script>
				<script>
					function validateFileUp (t) {
						var file = $(t).val().split(".");
						var ext = file.pop();
						var allowed = ['png', 'jpeg', 'jpg'];
						if ($.inArray(ext, allowed) >= 0) {
							if ($(t)[0].files[0]) {
								var reader = new FileReader();
								var src = '';
								reader.onload = function (e) {
									var error = false;
									var image = new Image();
									image.src = e.target.result;
									image.onload = function () {
										if (image.height != image.width) {
											error = true;
										}
									}
									console.log(error);
									if (error) {
										alert('This File width and height is not same');
										return false;
									}
									src = e.target.result;
									var data = '<a class="fancybox2 fancybox.iframe" href="'+ src +'" target="_blank">'
									+ '<img width="100px" src="'+ src +'" alt="" />'
									+ '</a>';
									$('#imagePrv').find('a').remove();
									$('#imagePrv').prepend(data);
								}
								reader.readAsDataURL($(t)[0].files[0]);
							}
						} else {
							alert('This File is not Allowed');
						}
					}
				</script>
				</html>