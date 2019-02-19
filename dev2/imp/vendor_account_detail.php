<?php

include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;
$account_id = (int)$_GET['account_id'];
if($_GET['number']){
	//$number = $db->func_escape_string($_GET['number']);	
//	$inv_query  = "select * from inv_shipments where package_number like '%$number%' and vendor='".$account_id."' and status<>'Issued' order by date_issued DESC";
}
else{
	$inv_query  = "SELECT 
	id,
	date_issued,
	date_received,
	package_number,
	'' AS comments,
	0 as total,
	ex_rate,
	'' as xdescription,
	shipping_cost,
	0 as line_item
	FROM
	inv_shipments 
	WHERE vendor = '$account_id' 
	
	UNION
	ALL 
	SELECT 
	0 AS id,
	voucher_date AS date_issued,
	'' AS date_received,
	'' AS package_number,
	description as comments,
	credit as total,
	0 as ex_rate,
	comments as xdescription,
	0 as shipping_cost,
	line_item
	FROM
	inv_accounts 
	WHERE account_id = 'V$account_id' 
	ORDER BY date_issued ASC";

}

$shipments  = $db->func_query($inv_query);

foreach($shipments as $index => $shipment){
	$SQL = "select sum(qty_shipped * unit_price) as shipped_total ,  sum(qty_received * unit_price) as received_total 
	from inv_shipment_items where shipment_id = '".$shipment['id']."'";
	$shipments[$index]['extra'] = $db->func_query_first($SQL); 

	$SQL = "select sum(qty_rejected) as rejects ,  sum(qty_rejected * unit_price) as reject_total 
	from inv_rejected_shipment_items rsi inner join inv_shipment_items si on 
	(rsi.shipment_id = si.shipment_id and rsi.product_sku = si.product_sku)
	where rsi.shipment_id = '".$shipment['id']."'";
	$shipments[$index]['extra2'] = $db->func_query_first($SQL); 
}
$parameters = $_SERVER['QUERY_STRING'];
//print_r($shipments); exit;
if ($_GET['downloadcsv']) {

}
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Vendor Ledger</title>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '680px' , autoCenter : true , autoSize : true });
		});
	</script>	
</head>
<body>
	<?php include_once 'inc/header.php';?>

	<?php if(@$_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php endif;?>




	<?php if($shipments):?>

		<?php
		$balance = 0.00;
		foreach($shipments as $temp)
		{

			$balance+=($temp['extra']['received_total'] / $temp['ex_rate'])+($temp['shipping_cost'] / $temp['ex_rate']);

		}


		$credit = $db->func_query_first_cell("SELECT SUM(credit) FROM inv_accounts WHERE account_id='V".$account_id."'");
		$balance = $balance - $credit;

		?>

		<div align="center">
		<input type="button" value="Make Payment" onclick="make_payment(this);">
		<input type="button" value="Add Line Item" onclick="add_line_item(this);">
		<input type="button" onclick="window.location='vendor_csv_download.php?account_id=<?= $account_id; ?>'" value="Download CSV">
		</div>
		<br>

		<br>
		<table width="70%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
			<thead>
				<tr>
					<!-- <th colspan="6" align="right">   <strong>Balance: $<?=number_format($balance,2);?></strong>  </th>-->

				</tr>
				<tr>
					<th> </th>
					<th>Invoice Date</th>
					<th>Description</th>
					<th>Credit</th>
					<th>Debit</th>

					<th>Balance</th>
					<th>Tracking</th>
					<th>Comments</th>

				</tr>
			</thead>
			<tbody>
				<?php  $shipped_total = 0; $received_total = 0; $shipping_cost = 0;
				$balance = 0.00;
				foreach($shipments as $shipment): if($shipment['ex_rate'] == 0) { $shipment['ex_rate'] = 1;}?>

				<?php
				$check_payment = $db->func_query_first("SELECT * FROM inv_account_shipments WHERE shipment_id='".$shipment['id']."'");
				$is_voucher = false;
				if($shipment['id']==0){

					$is_voucher = true;
				}
				if($is_voucher==false){
                   //$balance = round($balance,2) + round(($shipment['extra']['received_total'] / $shipment['ex_rate']) + ($shipment['shipping_cost'] / $shipment['ex_rate']),2);
				}
				else
				{

                  //echo $balance .' --- '.$shipment['total'].' --- '.($balance-$shipment['total'])."<br>";
                // if($shipment['line_item']==0)
                 //{


                 // $balance = round($balance,2) - round($shipment['total'],2);

                  /*if($shipment['line_item']==1)
                  {
                    if($shipment['total']<0)
                    {
 $balance = round($balance,2) - round($shipment['total']*(-1),2);
                    }
                    else
                    {
 $balance = round($balance,2) + round($shipment['total'],2);

                    }
                }*/


/*}
else
{
  

    $balance = round($balance,2)+round($shipment['total'],2);
 

}*/

}

?>

<tr id="<?php echo $shipment['id'];?>" >
	<td align="center">

		<input type="checkbox" name="shipment[]" value="<?=$shipment['id'];?>" <?=(($check_payment==true or $is_voucher==true)?'disabled':'');?>></td>


		<td align="left"><?php echo americanDate(($is_voucher?$shipment['date_issued']:$shipment['date_received']));?></td>



		<td align="center"><?=$shipment['xdescription'];?></td>
		<?php
		if($is_voucher==false){
			$credit = ($shipment['extra']['received_total'] / $shipment['ex_rate']) + ($shipment['shipping_cost'] / $shipment['ex_rate']);
			$debit = 0.00;
		}
		else
		{
			if($shipment['line_item']==0)
			{
				$credit = 0.00;
				$debit = $shipment['total'];
			}
			else
			{
				if($shipment['total']<0)
				{
					$credit = 0.00;
					$debit = $shipment['total']*(-1);
				}
				else
				{
					$credit = $shipment['total'];
					$debit = 0.00;
				}

			}
		}
		$balance = $balance + ($credit - $debit);
		?>
		<td align="center">$<?php echo number_format($credit,2);?></td>
		<td align="center">$<?=number_format($debit,2);?></td>

		<td>$<?=number_format($balance,2);?></td>
		<td align="center">
			<a href="view_shipment.php?shipment_id=<?php echo $shipment['id'];?>">
				<?php echo $shipment['package_number'];?>
			</a>
		</td>
		<?php
		if($is_voucher==false):
			?>
		<td align="center">Received <?php echo date('m/d/Y',strtotime($shipment['date_issued']));?></td>
		<?php
		else:
			?>
		<td align="center"><?php echo $shipment['comments'];?></td>
		<?php

		endif;
		?>

	</tr>
	<?php 
	$shipping_cost += round($shipment['shipping_cost'] / $shipment['ex_rate'],2);
	$shipped_total += round($shipment['extra']['shipped_total'] / $shipment['ex_rate'],2);
	$received_total += round($shipment['extra']['received_total'] / $shipment['ex_rate'],2);
	$rejects += $shipment['extra2']['rejects'];
	$rejects_total += round($shipment['extra2']['reject_total'] / $shipment['ex_rate'],2);
	?>     
	<?php $i++; endforeach; ?>

                 <!--  <tr>
                  	
                  	 <td align="right" colspan="6">Balance:</td>
                  	
                  	 <td align="center"><b>$<?php echo number_format($balance,2);?></b></td>
                  	 
                  
                  </tr>
              -->     

          </tbody>   
      </table>   
      <br />
  <?php else : ?> 
  	<p>
  		<label style="color: red; margin-left: 600px;">Shipments is not exist.</label>
  	</p>     
  <?php endif;?>
</body>
</html>        
<script>
	function make_payment(obj)
	{
		var output = $.map($(':checkbox[name=shipment\\[\\]]:checked'), function(n, i){
			return n.value;
		}).join(',');


		$.fancybox.open({
			padding : 0,
			href:'vendor_payment.php?shipment_ids='+output+'&vendor=<?=$account_id;?>',
			type: 'iframe'
		});

	}

	function add_line_item(obj)
	{


		$.fancybox.open({
			padding : 0,
			href:'vendor_line_item.php?&vendor=<?=$account_id;?>',
			type: 'iframe'
		});

	}

</script>