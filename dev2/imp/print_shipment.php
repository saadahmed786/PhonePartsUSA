<?php

require_once("auth.php");
include_once 'inc/Barcode.php';

if(isset($_GET['ids'])){
	$ids = $_GET['ids'];
	$ids_array = explode(",", $ids);
}
else{
	header("Location:rejected_shipments.php");
	exit;
}

$Barcode = new Barcode();
$Barcode->setType('C128');
$Barcode->setSize(60,140);
$Barcode->hideCodeType();
?>
<html>
<head>
	<style type="text/css">
	   body{font-family:'Arial'}	
	   table{width:144px;height:80px;overflow:hidden;margin:10px 0px;padding:0}
	   table tr td{margin:0 auto;}
	   td.img{height:50px;}
	   td.item_name{font-size:8px;line-height:8px;height:15px;}
	   .last{margin-bottom:0px !important;height:78px;}
	</style>
</head>
<body>
<center>
<?php $count = count($ids_array);?>
<?php $i=1; foreach($ids_array as $id):?>
	<table align="center" cellspacing="0" <?php if($count == $i):?> class="last" <?php endif;?>>
	    <?php 
	    	$code = $id;
	    	$Barcode->setCode($code); 
	    	$file = 'images/barcode/'.$code.'.png';
	    	$Barcode->writeBarcodeFile($file);
	    ?>
	    <tr>
			<td align="center" class="item_name"><?php echo $code;?></td>
		</tr>
		<tr>
			<td align="center"><!-- <img width="160" height="50" src="<?php echo $file;?>" /> -->
			<div id="barcode"></div></td>
		</tr>
	</table>	
<?php $i++; endforeach;?>
</center>
<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="js/jquery-barcode.min.js"></script>  
<script>
$("#barcode").barcode("<?php echo $code;?>", "code128");    

</script>
</body>
</html>