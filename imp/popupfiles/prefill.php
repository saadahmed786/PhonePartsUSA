<?php
include_once '../auth.php';
include_once '../inc/functions.php';
page_permission('qc_shipment');

$shipment_id = (int)$_GET['shipment_id'];
$shipment_details = $db->func_query_first("SELECT * FROM inv_shipments WHERE id='".$shipment_id."'");
$shipment_items = $db->func_query("SELECT a.product_id,a.product_sku,a.qty_shipped,b.quantity,b.prefill FROM inv_shipment_items a,oc_product b WHERE a.product_id=b.product_id and a.shipment_id='".$shipment_id."'");
// print_r($shipment_details);exit;
if(!$shipment_id)
{
	echo 'Please reload the page';exit;
}
if($shipment_details['status']!='Recieved' && $shipment_details['status']!='Issued' )
{
	echo 'Shipment must be in valid status in order to populate the Pre-Fill Quantities';
	exit;
}

if(isset($_POST['sku']) && isset($_POST['shipment_id'])){
	// print_r($_POST);exit;
	$log = 'Prefill Qty Updated: ';
	foreach($_POST['sku'] as $sku)
	{
		$db->db_exec("UPDATE oc_product SET prefill='".(int)$_POST['prefill'][$sku]."',prefill_shipment='".(int)$_POST['shipment_id']."' where trim(lower(sku))='".trim(strtolower($sku))."'");
		if((int)$_POST['prefill'][$sku])
		{
		$log.='<br>'.$sku.' &rarr; '.(int)$_POST['prefill'][$sku];
			
		}

	}

	addComment('shipment',array('id' => $_POST['shipment_id'], 'comment' => $log));
	// exit;
	// if($comment)
	// {
		// echo 'here';exit;
		// makeLedger('',array($sku=>(int)$quantity),$_SESSION['user_id'],'','Stock Adjustment (Cycle Count).',$comment);
		// $db->db_exec("UPDATE oc_product SET quantity='".$quantity."' where trim(lower(sku))='".trim(strtolower($sku))."'");
		$_SESSION['message'] = "Prefill is updated";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	// }
	
	
}

?>
<html>
<head>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../include/bootstrap/js/bootstrap.min.js"></script>
<!-- <link rel="stylesheet" type="text/css" href="../include/xtable.css" media="screen" /> -->
<!-- <link rel="stylesheet" type="text/css" href="../include/bootstrap/css/bootstrap.min.css"> -->
	<!-- <link rel="stylesheet" type="text/css" href="../include/bootstrap/css/bootstrap-theme.min.css"> -->
	<!-- <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css"> -->
<style>
.read_class{
	background-color: #eee;
}


.range-slider {
  margin: 5px 0 0 0%;
}

.range-slider {
  width: 100%;
}

.range-slider__range {
  -webkit-appearance: none;
  width: calc(100% - (54px));
  /*width: 100%;*/
  height: 10px;
  border-radius: 5px;
  background: #d7dcdf;
  outline: none;
  padding: 0;
  margin: 0;
}
.range-slider__range::-webkit-slider-thumb {
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #2c3e50;
  cursor: pointer;
  transition: background 0.15s ease-in-out;
}
.range-slider__range::-webkit-slider-thumb:hover {
  background: #1abc9c;
}
.range-slider__range:active::-webkit-slider-thumb {
  background: #1abc9c;
}
.range-slider__range::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border: 0;
  border-radius: 50%;
  background: #2c3e50;
  cursor: pointer;
  transition: background 0.15s ease-in-out;
}
.range-slider__range::-moz-range-thumb:hover {
  /*background: #1abc9c;*/
}
.range-slider__range:active::-moz-range-thumb {
  /*background: #1abc9c;*/
}
.range-slider__range:focus::-webkit-slider-thumb {
  /*box-shadow: 0 0 0 3px #fff, 0 0 0 6px #1abc9c;*/
}

.range-slider__value {
  display: inline-block;
  position: relative;
  width: 20px;
  color: #fff;
  line-height: 20px;
  text-align: center;
  border-radius: 3px;
  background: #2c3e50;
  padding: 5px 10px;
  margin-left: 8px;
}
.range-slider__value:after {
  position: absolute;
  top: 8px;
  left: -7px;
  width: 0;
  height: 0;
  border-top: 7px solid transparent;
  border-right: 7px solid #2c3e50;
  border-bottom: 7px solid transparent;
  content: "";
}

::-moz-range-track {
  background: #d7dcdf;
  border: 0;
}

input::-moz-focus-inner,
input::-moz-focus-outer {
  border: 0;
}
</style>
<!-- <link rel="stylesheet" type="text/css" href="<?php echo $host_path;?>/include/xtable.css" media="screen" /> -->
</head>
<body>
	<div align="center" style="display:none"> 
		<?php include_once '../inc/header.php';?>
	</div>
	<div align="center">
		<h2 align="center">Pre-Fill Quantity</h2>
		<?php
		if($_SESSION['message'])
		{
			?>
			<h5 align="center" style="color:red"><?php echo $_SESSION['message'];?></h5>
			<?php
			unset($_SESSION['message']);
		}
		?>
		
		
			<div id="" class="">
				<form method="post" id="form_prefill">
					
					<table cellpadding="5" cellspacing="0" style="margin-top:0px;width: 98%">
					<thead>
					<tr style="background-color: #eee">
					<th style="display: none"><input type="checkbox" onchange="$(this).parent().parent().parent().next().find('input[type=checkbox]').prop('checked',this.checked);"></th>
					<th>SKU</th>
					<th>Name</th>
					<th>On Shelf</th>
					<th>Shipment Qty</th>
					<th>Adj %</th>
					<th>Prefill Qty</th>

					</tr>
					</thead>
					<tbody>
					<?php
					foreach($shipment_items as $item)
					{


					?>
					<tr>
					<td align="center" style="display:none"><input type="checkbox" name="sku[]" value="<?php echo $item['product_sku'];?>" checked></td>
					<td align="center"><?php echo $item['product_sku'];?></td>
					<td align=""><?php echo getItemName($item['product_sku']);?></td>
					<td align="center"><input type="text" readonly="" class="read_class"  style="width:50px" value="<?php echo $item['quantity'];?>"></td>
					<td align="center"><input type="text" readonly="" class="read_class qty_shipped"   style="width:50px" value="<?php echo $item['qty_shipped'];?>"></td>
					<td align="center">
					
						<div class="range-slider">
  <input class="range-slider__range" type="range" value="<?php

  echo floor(($item['prefill']/$item['qty_shipped'])*100);
  ?>" min="0" max="100">
  <span class="range-slider__value">0</span>
</div>

					</td>
						<td align="center"><input type="text" readonly="" class="read_class prefill " name="prefill[<?php echo $item['product_sku'];?>]"  style="width:50px" value="<?php echo $item['prefill'];?>">

						</td>
					</tr>
					<?php
				}
				?>
				<tr>

				<td colspan="7" align="center">
				<input type="hidden" name="shipment_id" value="<?php echo $shipment_id;?>">
				<input type="button" value="Save" class="button" onclick="if(!confirm('Are you sure want to continue?')){return false;}else{$('#form_prefill').submit();}"></td>
				</tr>
					</tbody>
					</table>

					</form>

					</div>
			
			</div>
			
		
	</div>	
</body>
<script src="../include/bootstrap/js/bootstrap.min.js"></script>


<script>
var rangeSlider = function(){
  var slider = $('.range-slider'),
      range = $('.range-slider__range'),
      value = $('.range-slider__value');
    
  slider.each(function(){

    value.each(function(){
      var value = $(this).prev().attr('value');
      $(this).html(value);
    });

    range.on('input', function(){
      $(this).next(value).html(this.value);
      calculatePrefill(this);
      

    });
  });
};

rangeSlider();
function calculatePrefill(obj)
{
	var $prefill = $(obj).parent().parent().parent().find('.prefill');
	// var $this = $(obj);
	var $qty_shipped = $(obj).parent().parent().parent().find('.qty_shipped');

	$prefill.val(
		Math.floor(parseInt($qty_shipped.val())*parseInt($(obj).val()) / 100)

		);
}

</script>
</html>