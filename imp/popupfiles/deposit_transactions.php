<?php
include_once '../auth.php';
include_once '../inc/functions.php';
$deposit_id = (int)$_GET['deposit_id'];
$deposit_type = $_GET['deposit_type'];
if(!($deposit_type))
{
	$deposit_type='paypal';
}
if($deposit_type=='paypal')
{
$rows = $db->func_query("SELECT * FROM inv_transactions where deposit_id='".$deposit_id."' order by deposit_date desc");
$total_transactions = (float)$db->func_query_first_cell("SELECT SUM(net_amount) from inv_transactions where deposit_id='".$deposit_id."' ");
	
}
else if ($deposit_type=='tax')
{
	$rows = $db->func_query("

		select * from (
		SELECT deposited_tax_amount as amount,deposited_tax_amount as net_amount,0.00 as transaction_fee,tax_deposited_date as deposit_date,tax_deposited_by as deposited_by,order_id as id from inv_orders where tax_deposit_id='".$deposit_id."'

		union all
		SELECT refund_deposited_tax_amount as amount,refund_deposited_tax_amount as net_amount,0.00 as transaction_fee,refund_tax_deposited_date as deposit_date,refund_tax_deposited_by as deposited_by,order_id as id from inv_orders where refund_tax_deposit_id='".$deposit_id."'

		) a
		 order by 4 desc");

	$total_transactions = (float)$db->func_query_first_cell("SELECT SUM(net_amount) from inv_orders where deposit_id='".$deposit_id."' ");
}
else
{
	$rows = $db->func_query("SELECT gross_amount as amount,net_amount,payment_fee as transaction_fee,deposited_date as deposit_date,deposited_by,order_id as id from inv_orders where deposit_id='".$deposit_id."' order by deposited_date desc");

	$total_transactions = (float)$db->func_query_first_cell("SELECT SUM(net_amount) from inv_orders where deposit_id='".$deposit_id."' ");
}

$deposit_data = $db->func_query_first("SELECT * FROM inv_deposits where deposit_id='".(int)$deposit_id."'");
if($_POST['action'] && $_POST['action']=='toggle'){
	
$check = $db->func_query_first_cell("SELECT status from inv_deposits where deposit_id='".(int)$_POST['deposit_id']."'");
if($check=='open')
{
	$status = 'closed';
}
else
{
	$status = 'open';
}
$db->db_exec("UPDATE inv_deposits set status='".$status."' where deposit_id='".(int)$_POST['deposit_id']."'");
echo json_encode(array('success'=>$status));exit;
	
	
}
if($_POST['action'] && $_POST['action']=='unmap'){

	if($_POST['deposit_type']=='paypal')
	{


	// $data = $db->func_query_first("SELECT deposit_id,net_amount from inv_transactions where id='".(int)$_POST['id']."'");

	      $db->db_exec("UPDATE inv_transactions SET deposit_id='0',deposit_date='".date("Y-m-d H:i:s")."',deposited_by='".$_SESSION['user_id']."' where id='".$_POST['id']."'");
	  }
	  elseif($_POST['deposit_type']=='tax')
	  {

	  	$_check = $db->func_query_first_cell("SELECT refund_deposited_tax_amount from inv_orders where order_id='".$_POST['id']."'");
	  	if($_check)
	  	{

	  		 $db->db_exec("UPDATE inv_orders SET refund_tax_deposit_id='0',refund_tax_deposited_date='0000-00-00',refund_tax_deposited_by='".$_SESSION['user_id']."',refund_deposited_tax_amount=0.00 where order_id='".$_POST['id']."'");
	  	}
	  	else
	  	{
	  		
	  		 $db->db_exec("UPDATE inv_orders SET tax_deposit_id='0',tax_deposited_date='0000-00-00',tax_deposited_by='".$_SESSION['user_id']."',deposited_tax_amount=0.00 where order_id='".$_POST['id']."'");
	  	}
	  }
	  else
	  {
	  	// echo "UPDATE inv_orders SET deposit_id='0',deposited_date='0000-00-00',deposited_by='".$_SESSION['user_id']."',gross_amount=0.00,payment_fee=0.00,net_amount=0.00 where order_id='".$_POST['id']."'";exit;
	  	// $data = $db->func_query_first("SELECT deposit_id,net_amount from inv_orders where order_id='".(int)$_POST['id']."'");

	      $db->db_exec("UPDATE inv_orders SET deposit_id='0',deposited_date='0000-00-00',deposited_by='".$_SESSION['user_id']."' where order_id='".$_POST['id']."'");
	  }

	      // $db->db_exec("UPDATE inv_deposits set amount=amount-".(float)$data['net_amount']." where deposit_id='".$data['deposit_id']."'");
echo json_encode(array('success'=>1));
	      exit;
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
		<h2 align="center">Deposit History</h2>
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
			<table width="90%" class="xtable" cellpadding="0" cellspacing="0" class="xtable">
					<thead>
					<tr>
					<th>Date</th>
					<th>Deposit #</th>
					<th>Amount</th>

					<th>Deposited By</th>
					<th>Status</th>
					<th>Action</th>
					</tr>
					</thead>
					<tr>
					<td><?php echo date('m/d/Y',strtotime($deposit_data['deposit_date']));?></td>
					<td><?php echo $deposit_data['name'];?></td>
					<td><?php echo '$'.number_format($deposit_data['amount'],2);?></td>
					<td><?php echo get_username($deposit_data['deposited_by']);?></td>
					<td><span class="tag <?php echo ($deposit_data['status']=='closed'?'red':'blue');?>-bg"><?php echo $deposit_data['status'];?></span></td>
					<td><a href="javascript:void(0)" onclick="toggleStatus(this,'<?php echo $deposit_data['deposit_id'];?>','<?php echo $deposit_type;?>')"><?php echo ($deposit_data['status']=='open'?'Close':'Open');?></a></td>
					</tr>
					</table>

				<table width="90%" class="xtable" cellpadding="0" cellspacing="0" class="xtable">
					<thead>
						<tr>
							<th>Dep Date</th>
							<th>Transaction ID</th>
							<th colspan="3">Amount</th>
							<th>User ID</th>
							<th>Action</th>
							</tr>
							<tr>
							<th colspan="2"></th>
							<th>Gross</th>
							<th>Fee</th>
							<th>Net</th>
							<th colspan="2"></th>

							</tr>

						</thead>
						<tbody>
						<?php
						$total_amount=0.00;
						$total_transaction_fee=0.00;
						$total_net = 0.00;
						$i=0;
						foreach($rows as $row)
						{
							$total_amount+=$row['amount'];
							$total_transaction_fee+=$row['transaction_fee'];
							$total_net+=$row['net_amount'];
							?>
							<tr>
							<td><?php echo americanDate($row['deposit_date']);?></td>
							<td><?php echo ($row['transaction_id']?$row['transaction_id']:$row['id']);?></td>
							<td><?php echo '$'.number_format($row['amount'],2);?></td>
							<td><?php echo '$'.number_format($row['transaction_fee'],2);?></td>
							<td><?php echo '$'.number_format($row['net_amount'],2);?></td>
							<td><?php echo get_username($row['deposited_by']);?></td>
							<td>
							<?php
							if($deposit_data['status']=='open')
							{
						?>
							<a href="javascript:void(0)" onclick="unmapTransaction(this,'<?php echo $row['id'];?>','<?php echo $deposit_type;?>')">Unmap</a>
							<?php
						}
						?>
							</td>
							</tr>
							<?php
							$i++;
						}
						?>
						<tr>
						<td colspan="2"><strong>Total:</strong></td>
						<td><strong>$<?php echo number_format($total_amount,2);?></strong></td>
						<td><strong>$<?php echo number_format($total_transaction_fee,2);?></strong></td>
						<td><strong>$<?php echo number_format($total_net,2);?></strong></td>
						<td colspan="2" align="right"><strong><?php echo $i;?> Transactions</strong></td>
						
						</tr>
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
function toggleStatus(obj,deposit_id,deposit_type)
{

	$.ajax({
        url: '<?php echo $host_path;?>popupfiles/deposit_transactions.php',
        type:"POST",
        dataType:"json",
        data:{deposit_id:deposit_id,action:'toggle',deposit_type:deposit_type},
       
  success: function(json){

  	if(json['success'])
  	{
  		$(this).html(json['success']);
  		location.reload(true);
  	}
   
                    }
                  });
}

function unmapTransaction(obj,id,deposit_type)
{
$(obj).parent().parent().hide(500);
	$.ajax({
        url: '<?php echo $host_path;?>popupfiles/deposit_transactions.php',
        type:"POST",
        dataType:"json",
        data:{id:id,action:'unmap',deposit_type:deposit_type},
       
  success: function(json){

  	
   
                    }
                  });
}

</script>