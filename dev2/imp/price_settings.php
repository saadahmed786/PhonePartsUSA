<?php

include 'auth.php';

if($_POST['update']){
	$prices = $_POST;
	unset($prices['update']);
	unset($prices['markups']);
	$prices['date_modified'] = date("Y-m-d H:i:s");
	
	$db->func_array2update("inv_price_markups", $prices,"id = 1");
	header("Location:price_settings.php");
	exit;
}

if($_POST['download']){
	$markups = $_POST['markups'];
	
	$prices = $db->func_query("select * from inv_product_prices");
	$filename = "prices.csv";
	$fp = fopen($filename, "w");
	
	$headers = array_merge(array("SKU"),array_values($markups));
	fputcsv($fp, $headers,',');
	
	foreach($prices as $price){
		$data = array();
		$data[] = $price['product_sku'];
		foreach(array_keys($markups) as $market){
			$data[] = $price[$market];
		}
		fputcsv($fp, $data,',');
	}
	fclose($fp);
	
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	readfile($filename);
	@unlink($filename);
	exit;
}

$price_markups = $db->func_query_first("select * from inv_price_markups");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Price Settings</title>
	<script type="text/javascript" src="<?php echo $host_path?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path?>/fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : false });
			jQuery('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : false });
		});

		function downloadPrices(){
			url = 'crons/download_prices.php';
			markups =  $('.question_answer:checked').map(function () {
                return this.value;
            }).get().join(',');
            
            window.location = url +"?market="+markups;
		}
	</script>
</head>
<body>
	<div align="center"> 
	   <?php include_once 'inc/header.php';?>
	</div>
	
	 <?php if($_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	 <?php endif;?>
	 
	 <div align="center">
	 	<h3>Price Settings</h3>
	 	
	 	<a href="crons/calculate_prices.php" class="fancybox fancybox.iframe">Calculate Prices</a><br />
	 
	    <form method="post" action="">	
	    	<div>
			 	 <table width="60%" cellpadding="5" cellspacing="0" border="1">
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[ebay_new]" class="markups" value="eBay" /></td>
			 	 	 	 <td>eBay Markup:</td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="ebay" id="ebay" value="<?php echo $price_markups['ebay']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[amazon_new]" class="markups" value="Amazon" /></td>
			 	 	 	 <td>Amazon Markup:</td>
			 	 	 	 <td> 
			 	 	 	 	 <input type="text" name="amazon" id="amazon" value="<?php echo $price_markups['amazon']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[channel_advisor_new]" class="markups" value="ChannelAdvisor MM" /></td>
			 	 	 	 <td>Channel Advisor MM Price:</td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="channel_advisor" id="channel_advisor" value="<?php echo $price_markups['channel_advisor']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[channel_advisor1_new]" class="markups" value="ChannelAdvisor 1US" /></td>
			 	 	 	 <td>Channel Advisor US1 Price:</td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="channel_advisor1" id="channel_advisor1" value="<?php echo $price_markups['channel_advisor1']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[channel_advisor2_new]" class="markups" value="ChannelAdvisor 2US" /></td>
			 	 	 	 <td>Channel Advisor US2 Price:</td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="channel_advisor2" id="channel_advisor2" value="<?php echo $price_markups['channel_advisor2']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[bigcommerce_new]" class="markups" value="Bigcommerce" /></td>
			 	 	 	 <td>Bigcommerce Price:</td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="bigcommerce" id="bigcommerce" value="<?php echo $price_markups['bigcommerce']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[bigcommerce_retail_new]" class="markups" value="Bigcommerce Retail" /></td>
			 	 	 	 <td>Bigcommerce Retail Price:</td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="bigcommerce_retail" id="bigcommerce_retail" value="<?php echo $price_markups['bigcommerce_retail']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[bonanza_new]" class="markups" value="Bonanza" /></td>
			 	 	 	 <td>Bonanza Price:</td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="bonanza" id="bonanza" value="<?php echo $price_markups['bonanza']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[wish_new]" class="markups" value="Wish" /></td>
			 	 	 	 <td>Wish Price:</td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="wish" id="wish" value="<?php echo $price_markups['wish']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td><input type="checkbox" name="markups[open_sky_new]" class="markups" value="Open Sky" /></td>
			 	 	 	 <td>OpenSky Price:</td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="open_sky" id="open_sky" value="<?php echo $price_markups['open_sky']?>" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 </table>
			 	 <br />
			 	 <input type="submit" name="update" value="Update Markups"  />
			 	 
			 	 <input type="submit" name="download" value="Download Prices"  />
			 </div>  
	   </form>		 
	</div>
</body>
</html>	   