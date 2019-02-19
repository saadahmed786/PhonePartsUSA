<?php
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
require_once("../auth.php");
require_once("../inc/functions.php");

$detail = $db->func_query("SELECT DISTINCT sku, description FROM inv_buy_back ORDER BY sort");

if(isset($_POST['submit']) && $_POST['submit']=='Save')
{
	//print_r($_POST);exit;
	foreach($detail as $_row)
	{
		$sku = $_row['sku'];
		$data = array();
		$data['sku'] = $sku;
		$data['oem'] = $_POST['oem'][$sku];
		$data['non_oem'] = $_POST['non_oem'][$sku];
		$data['oem'] = $_POST['oem'][$sku];
		$data['reject_lcd_ok'] = $_POST['reject_lcd_ok'][$sku];
		$data['reject_lcd_blemish'] = $_POST['reject_lcd_blemish'][$sku];
		$data['reject_lcd_damaged'] = $_POST['reject_lcd_damaged'][$sku];
		$data['oem'] = $_POST['oem'][$sku];
		$check_query = $db->func_query("SELECT * FROM inv_buyback_skus WHERE sku='".$sku."'");
		if($check_query)
		{
		$db->func_array2update("inv_buyback_skus",$data,"sku='".$sku."'");
	}
	else
	{
		$db->func_array2insert("inv_buyback_skus",$data);	
			
	}
	createSKU($sku, $_row['description']);

	}
	$_SESSION['message'] = 'SKU mapping is done';
	header("Location: ".$host_path."buyback/lbb_sku_mapping.php");
	exit;
}

foreach($detail as $index => $_row)
{
	$sku_data = $db->func_query_first("SELECT * from inv_buyback_skus where sku='".$_row['sku']."' ");
	$detail[$index]['extra'] = $sku_data;	
}
// echo "<pre>";
// print_r($detail);exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>LBB SKU Mapping</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />


	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '700px' , autoCenter : true , height : '500px'});
			$('.fancybox2').fancybox({ width: '700px' , height: 'auto' , autoCenter : true , autoSize : true });
		});
		
		
	
	
</script>	

<style>
	.light-grey{
		background-color:#CCC;	
	}
</style>
</head>
<body>
	<div class="div-fixed">
		<div align="center"> 
			<?php include_once '../inc/header.php'; ?>
		</div>

		<?php if ($_SESSION['message']): ?>
			<div align="center"><br />
				<font color="red"><?php
					echo $_SESSION['message'];
					unset($_SESSION['message']);
					?><br /></font>
				</div>
			<?php endif; ?>

			<div align="center" style="width:90%;margin:0 auto;">
				<form method="post" action="" id="returnForm" enctype="multipart/form-data">
					<h2>LBB SKU Mapping </h2>


					<table border="1" cellpadding="10" cellspacing="0" width="90%">
						<tr style="background-color:#DCDCDC;font-weight:bold">
						<td>Image</td>
						<td>SKU</td>
						<td>Description</td>
						<td></td>
						
						
						</tr>
						<?php
						$i=0;
						foreach($detail as $row)
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
							<td align="center"><?=$row['sku'];?></td>
							<td style=""><?=$row['description'];?></td>
							<td>
							<table width="100%" cellspacing="5" cellpadding="5" style="font-weight:bold">
							<tr>
							<td width="30%">
							Good LCD OEM:</td><td> <input required  name="oem[<?=$row['sku'];?>]" id="oem_<?=$i;?>" value="<?=$row['extra']['oem'];?>"></td>
							</tr>
							<tr>
							<td>Good LCD Non-OEM:</td>

							<td><input required name="non_oem[<?=$row['sku'];?>]" id="non_oem_<?=$i;?>" value="<?=$row['extra']['non_oem'];?>"></td>
							</tr>
							<tr>
							<td>
							Reject LCD OK: </td>
							<td>
							<input required name="reject_lcd_ok[<?=$row['sku'];?>]" id="reject_lcd_ok_<?=$i;?>" value="<?=$row['extra']['reject_lcd_ok'];?>">
							</td>
							</tr>
							<tr>
							<td>
							Reject LCD Blemish:
							</td>
							<td>
							 <input required name="reject_lcd_blemish[<?=$row['sku'];?>]" id="reject_lcd_blemish_<?=$i;?>" value="<?=$row['extra']['reject_lcd_blemish'];?>">
							</td>
							</tr>
							<tr>
							<td>
							Reject LCD Damaged: </td>
							<td>
							<input required name="reject_lcd_damaged[<?=$row['sku'];?>]" id="reject_lcd_damaged_<?=$i;?>" value="<?=$row['extra']['reject_lcd_damaged'];?>">
							</td>
							</tr>
							</table>
							</td>
							</tr>
							<?php
							$i++;
						}
						?>
						<tr>
						<td colspan="6" align="center"><input type="submit" name="submit" value="Save"></td>
						</tr>
						
					</table>
					</form>

				<br />

				<br />

				
							<br /><br />
						</body>
						
</html>