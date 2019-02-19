<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
$items = trim($_GET['items'],",");

$my_sku = substr($items,0,7);
$items = explode(",",$items);
foreach($items as $item)
{
	
	if(substr($item,0,7)!=$my_sku)
	{
		
		echo "Please select the items with same SKU Group";exit;	
		
	}
	
}
if(isset($_POST['add']))
{
	
	$json = array();
	$classes = $_POST['classification'];
	$manufacturers = $_POST['manufacturer'];
	$devices = $_POST['device'];
	$models = $_POST['model'];
	$attribs = $_POST['attrib'];
	
	if (!$classes) {
		
        $json['error']= 'Please select class';
    }

	if(!$manufacturers)
	{
		$json['error']= 'Please select manufacturer1';
	}
	if(!$devices)
	{
		$json['error'] =  'Please select device';
	}
	if(!$models)
	{
		$json['error'] =  'Please select models';
	}
	if(!$attribs)
	{
	//$json['error'] = 'Please select attributes';
	}
	
	if($json['error']){ echo json_encode($json);exit;}
	
	foreach($items as $item)
	{
		$sku = $item;
		$sku_name = $db->func_query_first("SELECT
			b.name
			FROM
			`oc_product` a
			INNER JOIN `oc_product_description` b
			ON (a.`product_id` = b.`product_id`) WHERE a.status=1 AND a.sku='".$item."'");
		$name = $sku_name['name'];
		$db->db_exec("delete from inv_device_product where sku = '".$sku."'");

		$array = array();

		$array['sku'] = $sku;
		$array['name'] = $name;
		$array['date_added'] = date('Y-m-d h:i:s');
		$array['added_by'] = $_SESSION['login_as'];

		$prod_id = $db->func_array2insert("inv_device_product",$array);

		$array = array();
        $array['device_product_id'] = $prod_id;
        $array['class_id'] = $classes;

        $db->func_query("UPDATE oc_product SET classification_id = $classes WHERE model = '$sku'");

        $db->db_exec("delete from inv_device_class where device_product_id = '" . $oldproductid . "'");
        $class_id = $db->func_array2insert("inv_device_class", $array);

		foreach($manufacturers as $manufacturer)
		{
			$array = array();

			$array['device_product_id'] = $prod_id;
			$array['manufacturer_id'] = $manufacturer;
			$array['class_id'] = $class_id;

			$manuf_id = $db->func_array2insert("inv_device_manufacturer",$array);

			foreach($devices as $device)
			{
				$xdevice = explode("-",$device);
				
				if($xdevice[1]==$manufacturer)
				{
					$array = array();

					$array['device_manufacturer_id'] = $manuf_id;
					$array['device_id'] = $device;


					$device_id = $db->func_array2insert("inv_device_device",$array);

					foreach($models as $model)
					{

						$xmodel = explode("-",$model);

						if($xmodel[1]==$xdevice[0])
						{

							$array = array();

							$array['device_device_id'] = $device_id;
							$array['model_id'] = $xmodel[0];


							$model_idx = $db->func_array2insert("inv_device_model",$array);


							foreach($attribs as $attrib)
							{





								$array = array();

								$array['device_model_id'] = $model_idx;
								$array['attrib_id'] = $attrib;


								$model_id = $db->func_array2insert("inv_device_attrib",$array);



							}

						}

					}
				}
			}

		}

	}
	
	
	$json['success'] = 'Successfully Mapped';
	echo json_encode($json);
	exit;
}




$classification = $db->func_query("SELECT * FROM inv_classification WHERE status=1");
$manufacturers = $db->func_query("select * from inv_manufacturer WHERE status=1");
$sku_types		=	$db->func_query("SELECT * from inv_product_skus");




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?php echo $title;?></title>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<style>
		.multiple{
			height:300px	;	
		}

	</style>        
	<script>
		$(document).ready(function(e) {
			$('.fancybox3').fancybox({ width: '980px' , autoCenter : true , autoSize : true });
		});
		function populateDevice($i)
		{
			$('#div_sku_type'+$i).hide();
			$('#div_model'+$i).html('');
			var manufacturers = $('#manufacturer'+$i).val();
			$.ajax({
				url: "devices_new.php",
				type:"POST",
				data: {manufacturers: manufacturers,action:'ajax',type:'device',i:$i},
				success: function(data) {
					
					
					$('#div_device'+$i).html(data);
				}
			});
			
			
			
		}
		
		
		function populateModel($i)
		{$('#div_sku_type'+$i).hide();

		var models = $('#device'+$i).val();
		$.ajax({
			url: "devices_new.php",
			type:"POST",
			data: {models: models,action:'ajax',type:'model',i:$i},
			success: function(data) {


				$('#div_model'+$i).html(data);
				populateAttributes($i);
				$('#div_sku_type'+$i).show();
			}
		});



	}

	function populateAttributes($i)
	{

		var sku_type = $('#sku_type'+$i).val();
		$.ajax({
			url: "devices_new.php",
			type:"POST",
			data: {classification_type: $('#classification' + $i).val(),action:'ajax',type:'attribs',i:$i, ref:'map'},
			success: function(data) {


				$('#div_attribs'+$i).html(data);

			}
		});



	}
	function submitThis(i)
	{

		var checked1 = []
		$('#tr_'+i+' input[name=attrib\\[\\]]:checked').each(function ()
		{
			checked1.push(parseInt($(this).val()));
		});

		$.ajax({
			url: "map_device.php?items=<?php echo $_GET['items'];?>",
			type:"POST",
			data: {classification: $('#classification' + i).val(),manufacturer:$('#manufacturer'+i).val(),device:$('#device'+i).val(),model:$('#model'+i).val(),attrib:checked1,add:'save'},
			dataType:"json",
			success: function(json) {
				if(json['error'])
				{
					alert(json['error']);
					return false;
				}


				if(json['success'])
				{
					alert(json['success']);
					parent.location.reload();	
				}

			}
		});	

	}


</script>
</head>
<body>
	<div align="center">
		<div align="center" style="display:none"> 
			<?php include_once 'inc/header.php';?>
		</div>

		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<form id="myForm" action="" method="post">
			<table border="1" width="98%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr style="background-color:#e7e7e7;font-weight:bold">

					<td>Item Class</td>
					<td>Manufacturer</td>
					<td>Device</td>
					<td>Model / Sub Model</td>
					<td style="display:none">SKU Type</td>
					<td>Attributes</td>


				</tr>
				<?php
				$i=0;
				?>  
				<tr id="tr_<?php echo $i;?>">
					<td>

						<select name="classification[]" id="classification<?php echo $i; ?>" onchange="populateDevice(<?php echo $i; ?>); populateModel(<?php echo $i; ?>);">
							<option>Select Class</option>
							<?php foreach ($classification as $class) { ?>
								<option value="<?php echo $class['id']; ?>">
									<?= $class['name']; ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
					<td>
						<select name="manufacturer[]" id="manufacturer<?php echo $i;?>" multiple="multiple" class="multiple" onchange="populateDevice(<?php echo $i;?>)">
							<?php

							foreach($manufacturers as $manufacturer)
							{



								?>
								<option value="<?php echo $manufacturer['manufacturer_id'];?>" ><?php echo $manufacturer['name'];?></option>
								<?php 

							}

							?>

						</select>

					</td>
					<td><div id="div_device<?php echo $i;?>"></div></td>
					<td><div id="div_model<?php echo $i;?>"></div></td> 

					<td style="display:none"><div id="div_sku_type<?php echo $i;?>" style="display:none">

						<select name="sku_type" id="sku_type<?php echo $i;?>" onchange="populateAttributes(<?php echo $i;?>)" class="multiple">
							<?php
							foreach($sku_types as $sku_type)
							{
								?>
								<option value="<?php echo $sku_type['id'];?>" <?php if($my_sku == $sku_type['sku']) echo 'selected'; ?>><?php echo $sku_type['sku'];?></option>
								<?php  

							}

							?>
						</select>
					</div></td>
					<td><div id="div_attribs<?php echo $i;?>"></div></td>



				</tr>


				<tr>
					<td colspan="9" align="center"><input type="button" class="button" name="add" value="Update" onclick="submitThis(<?php echo $i;?>)" /> 

					</tr>

				</table>

			</form>


		</div>

	</body>
	</html>			 		 