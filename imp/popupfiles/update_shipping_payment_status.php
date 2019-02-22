<?php
include_once '../auth.php';
include_once '../inc/functions.php';
//echo 'here';exit;
$tracking_numbers = $_GET['tracking_numbers'];
if(!$tracking_numbers)
{
	echo 'No Tracking Selected';
	exit;
}
$tracking_numbers = explode(',', $tracking_numbers);
if(isset($_POST['id']))
{
	$ids = $_POST['id'];
	$i=0;
	foreach($ids as $id)
	{


		$update = array();
		$update['payment_status'] = $db->func_escape_string($_POST['payment_status'][$i]);
		$update['claim_voided_amount'] = (float)$_POST['claim_amount'][$i];
		$update['rma_number'] = $db->func_escape_string($_POST['rma_number'][$i]);
		if($_POST['date_filed'][$i]!='0000-00-00')
		{
		$update['date_filed'] = date('Y-m-d',strtotime($_POST['date_filed'][$i]));
			
		}
		else
		{

			$update['date_filed'] = '0000-00-00';
		}

		if($_POST['date_filed'][$i]!='0000-00-00')
		{
		$update['date_claimed'] = date('Y-m-d',strtotime($_POST['date_completed'][$i]));
			
		}
		else
		{
			$update['date_claimed'] = '0000-00-00';
		}
		$update['reason'] = $db->func_escape_string($_POST['reason'][$i]);
		// print_r($update);exit;
		// $db->func_array2update("inv_shipstation_transactions",$update,"id = '$id'");
		// echo "UPDATE inv_shipstation_transactions SET 
		// 	payment_status='".$update['payment_status']."',
		// 	claim_voided_amount='".$update['claim_voided_amount']."',
		// 	rma_number='".$update['rma_number']."',
		// 	date_filed='".$update['date_filed']."',
		// 	date_claimed='".$update['date_claimed']."',
		// 	reason='".$update['reason']."'
		// 	where id='".(int)$id."'

		// 	";exit;
		$db->db_exec("UPDATE inv_shipstation_transactions SET 
			payment_status='".$update['payment_status']."',
			claim_voided_amount='".$update['claim_voided_amount']."',
			rma_number='".$update['rma_number']."',
			date_filed='".$update['date_filed']."',
			date_claimed='".$update['date_claimed']."',
			reason='".$update['reason']."'
			where id='".(int)$id."'

			");
		$i++;
	}

	echo "<script>alert('Tracking Updated');parent.location.reload(true);</script>";
}
?>
<html>
<head>

</head>

<body>
<link rel="stylesheet" type="text/css" href="<?php echo $host_path;?>include/xtable.css" media="screen" />
	<div align="center" style="display:none"> 
		<?php include_once '../inc/header.php';?>
	</div>
	<div align="center">
		<h2 align="center">Update Tracking</h2>
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
			<form method="post">
			<div style="text-align: center"><input type="submit" class="button button-info" value="Update"></div>
			<table width="90%" class="xtable" cellpadding="0" cellspacing="0" class="xtable">
					<thead>
					<tr>
					<th>Order ID</th>
					<th>Tracking #</th>
					<th>Amount</th>

					<th>Shipping Paid</th>
					<th>Status</th>
					<th>Claim/Voided</th>
					<th>RMA #</th>
					<th>Date Filed</th>
					<th>Date Completed</th>
					<th>Reason</th>
					</tr>
					</thead>
					<tbody>
					<?php
					$i=0;
					$payment_statuses = oc_config('shipping_management_report');
					
					$payment_statuses = unserialize($payment_statuses);
					
					foreach($tracking_numbers as $tracking_number)
					{

						$row = $db->func_query_first("SELECT * FROM inv_shipstation_transactions WHERE confirmation='delivery'  and tracking_number='".$tracking_number."'");
						if(!$row) continue;
						$order_amount = 0.00;
						if($row['order_id'])
						{
						$order_amount = $db->func_query_first_cell("SELECT (a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(b.amount),0) from oc_voucher_history b where a.order_id=b.order_id) from inv_orders a where a.order_id='".$row['order_id']."'"); 
							
						}
					?>
					<tr>
					<td><?php echo linkToOrder($row['order_id'],$host_path);?></td>
					<td><?php echo $row['tracking_number'];?></td>
					<td><?php echo '$'.number_format($order_amount,2);?></td>
					<td><?php echo '$'.number_format($row['shipping_cost']+$row['insurance_cost'],2);?></td>
					<td><select id="payment_status_<?php echo $i;?>" name="payment_status[]">
					
					<?php
					foreach($payment_statuses as $payment_status)
					{
						?>
						<option value="<?php echo $payment_status;?>" <?php echo (strtolower($payment_status)==strtolower($row['payment_status'])?'selected':'');?>><?php echo $payment_status;?></option>
						<?php
					}
					?>

					</select>
					</td>
					<td><input style="width:80px" type="number" step=".01" id="claim_amount_<?php echo $i;?>" name="claim_amount[]" value="<?php echo $row['claim_voided_amount'];?>" ></td>
					<td><input type="text" id="rma_number_<?php echo $i;?>" value="<?php echo $row['rma_number'];?>" name="rma_number[]" ></td>
					<td><input type="text" class="datepicker"  name="date_filed[]" id="date_filed_<?php echo $i;?>" value="<?php echo $row['date_filed'];?>"></td>
					<td><input type="text" class="datepicker"  name="date_completed[]" id="date_completed_<?php echo $i;?>" value="<?php echo $row['date_claimed'];?>"></td>
					<td><input type="text"  name="reason[]" id="reason_<?php echo $i;?>" value="<?php echo $row['reason'];?>">
					<input type="hidden" name="id[]" value="<?php echo $row['id'];?>">
					</td>

					</tr>
					<?php
				$i++;
				}
				?>
					</tbody>
					</table>

					
				</form>

			</div>

		</form>
	</div>


</div>	
</body>
</html>
<script>

</script>