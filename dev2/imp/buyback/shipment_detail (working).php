<?php
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
require_once("../auth.php");
require_once("../inc/functions.php");
function addBBComment($buyback_id,$comment)
{
	
	global $db;
	$data = array();
//	$data['customer_id'] = $customer_id;
	$data['comment'] = $db->func_escape_string($comment);
	$data['buyback_id'] = $buyback_id;
	$data['user_id'] = $_SESSION['user_id'];
	//$data['email'] = $oldEmail;
	$data['date_added'] = date('Y-m-d H:i:s');

	$db->func_array2insert("inv_buyback_comments",$data);	
		
}
$shipment_number = $db->func_escape_string($_REQUEST['shipment']);
if (!$shipment_number) {
    header("Location:$host_path/buyback/shipments.php");
    exit;
}
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE shipment_number='".$shipment_number."'");
if($detail['status']=='In QC')
{
	$detail['status'] = 'QC Completed';	
}
if(!$detail)
{
	 header("Location:$host_path/buyback/shipments.php");
    exit;
	
}
if($_SESSION['login_as']=='admin')
{
	$_SESSION['buyback_qc_shipments'] = 1;
	$_SESSION['buyback_receive_shipments'] = 1;
}
if($_SESSION['buyback_qc_shipments'] and in_array($detail['status'],array('Received')))
{
$flag_change_qty = true;	
}
else
{
	$flag_change_qty = false;
}


if($_SESSION['buyback_receive_shipments'] && in_array($detail['status'],array('Awaiting')))
{
$flag_received_qty = true;	
}
else
{
	$flag_received_qty = false;
}


$products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");

if($_POST['received'] || $_POST['qcdone'] || $_POST['completed'] || $_POST['save'])
{
	
		if($_POST['received'])
	{
		$db->db_exec("UPDATE oc_buyback SET status='Received',date_received='".date('Y-m-d H:i:s')."' WHERE shipment_number='".$shipment_number."'");
		
		
		addBBComment($detail['buyback_id'],$_SESSION['login_as'].' has changed the status to Received.');
		
		
	}
	else if($_POST['qcdone'])
	{
		$db->db_exec("UPDATE oc_buyback SET status='In QC',date_qc='".date('Y-m-d H:i:s')."' WHERE shipment_number='".$shipment_number."'");
		addBBComment($detail['buyback_id'],$_SESSION['login_as'].' has changed the status to QC Completed.');
	}	
	else if($_POST['completed'])
	{
		$db->db_exec("UPDATE oc_buyback SET status='Completed',date_completed='".date('Y-m-d H:i:s')."' WHERE shipment_number='".$shipment_number."'");	
		addBBComment($detail['buyback_id'],$_SESSION['login_as'].' has changed the status to Completed.');
	}
	
	
	if($flag_received_qty)
	{
		foreach($products as $product)
		{
			$db->db_exec("UPDATE oc_buyback_products SET total_received ='".(int)$_POST['total_received'][$product['buyback_product_id']]."' WHERE buyback_product_id='".$product['buyback_product_id']."'");
			
			
		}
		if($_POST['new_received_lcd'])
		{
			foreach($_POST['new_received_lcd'] as $key => $sku)
			{
				$new_detail = $db->func_query_first("SELECT id,sku,image,description,oem,non_oem FROM inv_buy_back WHERE sku='".$sku."'");
				
				$db->db_exec("INSERT INTO oc_buyback_products SET buyback_id='".$detail['buyback_id']."',sku='".$new_detail['sku']."',image_path='".$host_path."files/".$new_detail['image']."',description='".$new_detail['description']."',total_received='".$_POST['new_total_received'][$key]."',data_type='received'");
				
				addBBComment($detail['buyback_id'],$_SESSION['login_as'].' added new Item "'.$new_detail['description'].'" of Qty:'.$_POST['new_total_received'][$key].' to received.');
			}
			
		}
		
			$db->db_exec("UPDATE oc_buyback SET total_received='".(int)$_POST['received_total']."' WHERE shipment_number='".$shipment_number."'");
	}
	// Only when QC Done
	if($flag_change_qty)
	{
		
		if($_POST['new_qc_lcd'])
		{
			
			foreach($_POST['new_qc_lcd'] as $key => $sku)
			{
				$new_detail = $db->func_query_first("SELECT id,sku,image,description,oem,non_oem FROM inv_buy_back WHERE sku='".$sku."'");
				
				$total_oem_total = (int)$_POST['new_oem_received'][$key] * (float)$new_detail['oem'];
				$total_non_oem_total = (int)$_POST['new_non_oem_received'][$key] * (float)$new_detail['non_oem'];
				$total_rejected_total = 0.00;
				
				
				$xarray = array();
				$xarray['buyback_id'] = $detail['buyback_id'];
				$xarray['sku'] = $new_detail['sku'];
				if($new_detail['image'])
				{
					
				
				$xarray['image_path'] = $host_path."files/".$new_detail['image'];
				}
				$xarray['description'] = $new_detail['description'];
				$xarray['oem_price'] = $new_detail['oem'];
				$xarray['non_oem_price'] = $new_detail['non_oem'];
				$xarray['oem_quantity'] = (int)$_POST['new_oem_received'][$key];
				$xarray['non_oem_quantity'] = (int)$_POST['new_non_oem_received'][$key];
				$xarray['total_qc_received'] = (int)$_POST['new_total_qc_received'][$key];
				$xarray['data_type'] = 'qc';
				$xarray['total_oem_total'] = (float)$total_oem_total;
				$xarray['total_non_oem_total'] = (float)$total_non_oem_total;
				$xarray['total_rejected_total'] = (float)$total_rejected_total;
					
					$_id = $db->func_array2insert('oc_buyback_products',$xarray);
							
				/*$db->db_exec("INSERT INTO oc_buyback_products SET buyback_id='".$detail['buyback_id']."',sku='".$new_detail['sku']."',image_path='".$host_path."files/".$new_detail['image']."',description='".$new_detail['description']."',oem_price='".$new_detail['oem']."',non_oem_price='".$new_detail['non_oem']."',oem_quantity='".(int)$_POST['new_oem_received'][$key]."',non_oem_quantity='".(int)$_POST['new_non_oem_received'][$key]."',total_qc_received='".(int)$_POST['new_total_qc_received'][$key]."',data_type='qc',total_oem_total='".(float)$total_oem_total."',total_non_oem_total='".(float)$total_non_oem_total."',total_rejected_total='".(float)$total_rejected_total."'");*/
				
				
				addBBComment($detail['buyback_id'],$_SESSION['login_as'].' added new Item "'.$new_detail['description'].'" of Qty:'.$_POST['new_oem_received'][$key].' to qc.');
				
				$_POST['oem_received'][$_id] = (int)$_POST['new_oem_received'][$key];
				$_POST['non_oem_received'][$_id] = (int)$_POST['new_non_oem_received'][$key];
				$_POST['rejected_qty'][$_id] = (int)$_POST['new_rejected_qty'][$key] ;
				$_POST['total_qc_received'][$_id] = $_POST['new_total_qc_received'][$key];
			}
			
		}
		
$products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");

		foreach($products as $product)
		{
			
			
				 $db->db_exec("DELETE FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
				
				$data = array();
				$data['oem_received'] = (int)$_POST['oem_received'][$product['buyback_product_id']];
				$data['non_oem_received'] = (int)$_POST['non_oem_received'][$product['buyback_product_id']];
				$data['rejected_qty'] = (int)$_POST['rejected_qty'][$product['buyback_product_id']];
				$data['buyback_product_id'] = $product['buyback_product_id'];
				
					$data['date_added'] = date('Y-m-d H:i:s');
					$db->func_array2insert('inv_buyback_shipments',$data);
				
				$db->db_exec("UPDATE oc_buyback_products SET total_qc_received = '".(int)$_POST['total_qc_received'][$product['buyback_product_id']]."' WHERE buyback_product_id='".$product['buyback_product_id']."'");
				
				// Shipment Box Preparation
				
				
					$last_id = $db->func_query_first_cell ( "select id from inv_buyback_boxes where status != 'Completed'" );
	if (! $last_id) {
		$rejcetedShipment = array();
		$rejcetedShipment ['status'] = 'Pending';
		$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
		$rejcetedShipment ['user_id'] = $_SESSION ['user_id'];
		$last_id = $db->func_array2insert ( 'inv_buyback_boxes', $rejcetedShipment );
	}	
					
					$db->db_exec ( "delete from inv_buyback_box_items where buyback_product_id='".$product['buyback_product_id']."' and shipment_id = '".$last_id."'" );
					
					
					
	
		$ShipmentItems = array();
		$ShipmentItems ['shipment_id'] = $last_id;
		$ShipmentItems ['oem_received'] = $data['oem_received'];
		$ShipmentItems ['non_oem_received'] = $data['non_oem_received'];
		$ShipmentItems ['buyback_product_id'] = $data['buyback_product_id'];
				$db->func_array2insert ( 'inv_buyback_box_items', $ShipmentItems);
	
					
				
				$db->db_exec("UPDATE oc_buyback_products SET total_oem_total='".(float)$_POST['total_oem_total'][$product['buyback_product_id']]."',total_non_oem_total='".(float)$_POST['total_non_oem_total'][$product['buyback_product_id']]."',total_rejected_total='".(float)$_POST['total_rejected_total'][$product['buyback_product_id']]."' WHERE buyback_product_id='".$product['buyback_product_id']."'");
				
				// Shipment Box Preparation Ends
					
					
			}
	}
			
			if($_POST['admin_oem_qty'] or $_POST['admin_non_oem_qty'])
			{
					foreach($_POST['admin_oem_qty'] as $key => $value)
					{
						$db->db_exec("UPDATE oc_buyback_products SET admin_oem_qty='".(int)$_POST['admin_oem_qty'][$key]."',admin_non_oem_qty='".(int)$_POST['admin_non_oem_qty'][$key]."' WHERE buyback_product_id='".$key."' ");	
						
					}
				
				
			}
		
		
	
	
	$_SESSION['message'] = 'Modification Made!';
	header("Location:shipment_detail.php?shipment=$shipment_number");
	exit;
}
if($_POST['addcomment'])
{
	
	addBBComment($detail['buyback_id'],$_POST['comment']);
	
	$_SESSION['message'] = 'Comment has been added';
	header("Location:shipment_detail.php?shipment=$shipment_number");
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Shipment Details</title>
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

    	
    <script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '700px' , autoCenter : true , height : '500px'});
			$('.fancybox2').fancybox({ width: '700px' , height: 'auto' , autoCenter : true , autoSize : true });
		});
		var items = '';
				<?php
				$_items = $db->func_query("SELECT sku,description FROM inv_buy_back ORDER BY sort");
				foreach($_items as $_item)
				{
					?>
					items = items + '<option value="<?php echo $_item['sku'];?>"><?=$_item['description'];?></option>';
					<?php
				}
				?>
			function addRowReceived(){
				
		var current_row = $('#received_table tr').length+1;	
		var row = "<tr>"+
"<td><select name='new_received_lcd[]'>"+items+"</select></td>"+
"<td class='light-grey'><input type='text' id='' value='0' name='new_total_received[]' style='width:80px'  /> <a href='javascript:void(0);' onClick='$(this).parent().parent().remove();'>X</a></td>"+
		"</tr>";
		//$("#received_table").append(row);	
		
		$(row).insertBefore('#received_table tr:nth-last-child(2)');	
		current_row++;	 
	}
	function addRowQC(){
				
		var current_row = $('#qc_table tr').length+1;	
		var row = "<tr>"+
"<td><select name='new_qc_lcd[]'>"+items+"</select></td>"+
"<td align='center'><input type='text' name='new_oem_received[]' style='width:80px' value='0'></td>"+
"<td align='center'><input type='text' name='new_non_oem_received[]' style='width:80px' value='0'></td>"+
"<td align='center'><input type='text' name='new_rejected_qty[]' style='width:80px' value='0'></td>"+
"<td align='center' class='light-grey'><input type='text' name='new_total_qc_received[]' style='width:80px' value='0'> <a href='javascript:void(0);' onClick='$(this).parent().parent().remove();'>X</a></td>"+

		"</tr>";
		//$("#received_table").append(row);	
		
		$(row).insertBefore('#qc_table tr:nth-last-child(1)');	
		current_row++;	 
	}
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
                    <h2>LBB Shipment Details</h2>

                    
                   <?php
				   	if($detail['customer_id']==0)
					{
						
						$firstname = $detail['firstname'];
						$lastname = $detail['lastname'];
						$email = $detail['email'];
						$telephone = $detail['telephone'];
						$address_1 = $detail['address_1'];
						$city = $detail['city'];
						$postcode = $detail['postcode'];
						$zone_id = $detail['zone_id'];
					}
					else
					{
						
						$customer_detail = $db->func_query_first("SELECT email,telephone FROM oc_customer WHERE customer_id='".$detail['customer_id']."'");
						$address = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".$detail['address_id']."'");
						
						
						$firstname = $address['firstname'];
						$lastname = $address['lastname'];
						$email = $customer_detail['email'];
						$telephone = $customer_detail['telephone'];
						$address_1 = $address['address_1'];
						$city = $address['city'];
						$postcode = $address['postcode'];
						$zone_id = $address['zone_id'];
					}
				   $zone = $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$zone_id."'");
				   ?>
                    <table border="1" cellpadding="10" cellspacing="0" width="90%">
                        <tr>
                            <td>
                                <table cellpadding="5">
                                    <caption><b>Shipping</b></caption>
                                    <tr>	
                                        <td><b>Full Name:</b></td>
                                        <td><?php echo $firstname . " " . $lastname; ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><b>Email:</b></td>
                                        <td><?php echo linkToProfile($email,$host_path); ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><b>Address 1:</b></td>
                                        <td><?php echo $address_1['address1'] ?></td>
                                        <td></td>
                                    </tr>

                                   
                                    <tr>	
                                        <td>City: <?php echo $city; ?></td>
                                        <td>State: <?php echo $zone; ?></td>
                                        <td>Zip: <?php echo $postcode; ?></td>
                                    </tr>
                                </table>	    
                            </td>

                            <td>
                                <table cellpadding="5">
                                    <caption><b>Payment</b></caption>
                                    <tr>	
                                        <td><b>Payment Type:</b></td>
                                        <td><?php echo $detail['payment_type']; ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><b>PayPal Email:</b></td>
                                        <td><?php echo $detail['paypal_email'];?></td>
                                        <td></td>
                                    </tr>

                                   

                                    <tr>	
                                        <td><strong>Total</strong></td>
                                        <td>$<?=number_format($detail['total'],2);?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><strong>Tracking #</strong></td>
                                        <td colspan="2"><?=$detail['tracking_no'];?> </td>
                                        
                                    </tr>
                                </table>	    
                            </td>

                            <td>
                                <table cellpadding="5">
                                    <caption><b>Other Detail</b></caption>
                                    <tr>
                                        <td><b>Shipment #: <?=$detail['shipment_number'];?></a></b></td>
                                        <td></td>
                                        <td><b></b></td>	    	       
                                    </tr>

                                    <tr>
                                        <td><b>Added: </b><?php echo americanDate($detail['date_added']); ?></td>
                                        <td>|</td>
                                        <td><b>Date Received: </b><?php echo americanDate($detail['date_received']); ?></td>	    	       
</tr>

<tr>
 <?php
 if($detail['date_qc']):

    ?>
<td><b>QC Date: </b><?php echo americanDate($detail['date_qc']); ?></td>
<td>|</td>
<?php
endif;
if($detail['status']=='Completed'):

    ?>
<td><b>Completed Date:  </b> <?php echo americanDate($detail['date_completed']); ?></td>	 
<?php
endif;
?>   	       
</tr>

<tr>
    <td><b>Status: </b> <?php echo  $detail['status']; ?></td>
    <td>| </td>	  
    <td><strong>Procedure:</strong> <?php echo $detail['option'];?></td>	    
</tr>	



</table>
</td>
</tr> 
<?php
$payment_detail = $db->func_query_first("SELECT * FROM inv_buyback_payments WHERE buyback_id='".$detail['buyback_id']."'");
?>
	<tr>
    <td colspan="3" align="left"> <table cellpadding="5" style="width:50%" >
        <tr>
        <td style="font-weight:bold">Payment Details: </td>
            <td style="font-weight:bold" ><?php
			if($payment_detail)
			{
			if($detail['payment_type']=='store_credit')
			{
			?>
            <b>Store Credit # <?php echo $payment_detail['credit_code'];?> of amount $<?php echo number_format($payment_detail['amount'],2);?> </b>
            <?php	
				
			}
			else
			{
			?>
             <b>PayPal Transaction ID # <?php echo $payment_detail['transaction_id'];?> of amount $<?php echo number_format($payment_detail['amount'],2);?> </b>
            <?php	
			}
			?>
            
            <?php	
				
			}
			else
			{
			echo 'Not Found';	
			}
			
			?>
</td>
        </tr>
        
    </table>
</td>
</tr>


</table>

<br />

<br />
<table border="0" cellpadding="10" cellspacing="0" width="90%">

<tr>
<td valign="top">
<h1 style="font-size:12px">Customer Data</h1>
<table border="1" cellpadding="10" cellspacing="0" width="60%">
<tr style="background-color:#999;font-weight:bold;font-size:10px">
<th>LCD Type</th>
<th>OEM</th>
<th>Non-OEM</th>

<th>Total</th>
<th>Price</th>
</tr>
<?php
$customer_total = 0;
$customer_quantity_total = 0;
foreach($products as $product)
	{
	
	if($product['data_type']!='customer') continue;
		
		$total = ($product['oem_price'] * $product['oem_quantity']) + ($product['non_oem_price'] * $product['non_oem_quantity']);
		
			
			$customer_total+=$total;
			$customer_quantity_total+= $product['oem_quantity'] + $product['non_oem_quantity'];
	
		
?>

<tr >
<td><?=$product['description'];?></td>

<td align="center">
<?php
if($product['oem_quantity']>0)
{
?>
<?php echo number_format($product['oem_price'],2);?> x <strong><?php echo $product['oem_quantity'];?></strong>
<?php
}
else
{
 echo '-';	
}
?>
</td>
<td align="center"><?php
if($product['non_oem_quantity']>0)
{
?>
<?php echo number_format($product['non_oem_price'],2);?> x <strong><?php echo $product['non_oem_quantity'];?></strong>
<?php
}
else
{
 echo '-';	
}
?></td>



<td align="center" class="light-grey"><strong><?=$product['oem_quantity']+$product['non_oem_quantity'];?></strong></td>
<td align="right" class="light-grey">$<?=number_format($total,2);?></td>

</tr>

<?php
	}
	?>
    <tr>
    
    <td colspan="3">
    
    </td>
    <td class="light-grey" align="center"><strong><?=$customer_quantity_total;?></strong></td>
    <td align="right" class="light-grey"><strong>$<?=number_format($customer_total,2);?></strong></td>
    </tr>


</table>
</td>
<td valign="top">
<h1 style="font-size:12px">Receiving Data</h1>
<table border="1" id="received_table" cellpadding="5" cellspacing="0" width="70%">
<tr style="background-color:#999;font-weight:bold;font-size:10px">
<th>LCD Type</th>


<th>Total</th>

</tr>
<?php
$_total_received = 0;
foreach($products as $product)
	{
		if($product['data_type']!='customer' and $product['data_type']!='received') continue;
		
		
		$_total_received+=$product['total_received'];
		?>
        <tr>
        <td><?php echo $product['description'];?></td>
        <?php
		if($flag_received_qty)
		{
		?>
        <td align="center" class="light-grey"><input type="text" id="total_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$product['total_received'];?>" name="total_received[<?php echo $product['buyback_product_id'];?>]" style="width:80px"  /></td>
        <?php
		}
		else
		{
		?>
        <td align="center" class="light-grey"><?php echo $product['total_received'];?></td>
        <?php	
			
		}
		?>
        </tr>
        <?php
		}
	?>
    <tr>
    <td> </td>
    <?php
	if($flag_received_qty)
	{
	?>
    <td align="center" class="light-grey" ><input type="text" name="received_total" value="<?=$detail['total_received'];?>"  style="width:80px"/></td>
    <?php
	}
	else
	{
	?>
    <td align="center" class="light-grey" ><?=($detail['total_received']?$detail['total_received']:$_total_received);?></td>
    <?php	
	}
	?>

    </tr>
    <?php
	if($flag_received_qty)
	{
	?>
    <tr>
    <td colspan="2" align="center"><input type="button" class="button" value="Add LCD" onclick="addRowReceived();" /></td>
    </tr>
    <?php	
	}
	?>


</table>
</td>
</tr>
<tr>
<td valign="top">


<h1 style="font-size:12px">QC Data
<?php if($flag_change_qty){
	?>
    (<a href="javascript:void(0);" onclick="addRowQC();">Add LCD</a>)
    <?php
	
}?>
</h1>
<table border="1" id="qc_table" cellpadding="10" cellspacing="0" width="70%">
<tr style="background-color:#999;font-weight:bold;font-size:10px">
<th>LCD Type</th>
<th>OEM</th>
<th>Non-OEM</th>
<th>Reject</th>
<th>Total</th>

</tr>
<?php
$qc_quantity_total = 0;
foreach($products as $product)
	{
		if($product['data_type']!='customer' and $product['data_type']!='qc') continue;
	$qc_quantity_total+=$product['total_qc_received'];
	$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
			if($quantities)
			{
				$oem_qty = (int)$quantities['oem_received'];
				$non_oem_qty = (int)$quantities['non_oem_received'];
				$rejected_qty = (int)$quantities['rejected_qty'];	
			}
		
?>

<tr >
<td><?=$product['description'];?></td>

<td align="center">
<?php
if($flag_change_qty)
{
?>
<input type="text" id="oem_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$oem_qty;?>" name="oem_received[<?php echo $product['buyback_product_id'];?>]" style="width:80px" onblur="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
    <input type="hidden" id="oem_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['oem_price'];?>" />
    <input type="hidden" name="total_oem_total[<?php echo $product['buyback_product_id'];?>]" id="total_oem_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_oem_total'];?>"  />
    <?php
}
else
{
 echo $oem_qty;	
}
?>
</td>
<td align="center"><?php
if($flag_change_qty)
{
?>
<input type="text" id="non_oem_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$non_oem_qty;?>" name="non_oem_received[<?php echo $product['buyback_product_id'];?>]" style="width:80px" onblur="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')" />
    <input type="hidden" id="non_oem_price_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['non_oem_price'];?>" />
    <input type="hidden" name="total_non_oem_total[<?php echo $product['buyback_product_id'];?>]" id="total_non_oem_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo (float)$product['total_non_oem_total'];?>"  />
    <?php
}
else
{
 echo $non_oem_qty;	
}
?></td>



<td align="center" ><?php
 if($flag_change_qty)
 {
 ?>
 <input type="text" id="rejected_qty_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$rejected_qty;?>" name="rejected_qty[<?php echo $product['buyback_product_id'];?>]" style="width:80px" onblur="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['total_received'];?>')"  />
 <input type="hidden" name="total_rejected_total[<?php echo $product['buyback_product_id'];?>]" id="total_rejected_total_<?php echo $product['buyback_product_id'];?>" value="<?php echo $product['total_rejected_total'];?>" /> 
 
 <?php
 }
 else
 {
	echo $quantities['rejected_qty']; 
 }
 ?></td>
<td align="right" class="light-grey"><?php
 if($flag_change_qty)
 {
 ?>
 <input type="text" id="total_qc_received_<?php echo $product['buyback_product_id'];?>" value="<?=(int)$product['total_qc_received'];?>" name="total_qc_received[<?php echo $product['buyback_product_id'];?>]" style="width:80px" />
 <?php
 }
 else
 {
	echo $product['total_qc_received']; 
 }
 ?></td>

</tr>

<?php
	}
	?>
    <tr>
    
    <td colspan="4">
    
    </td>
        <td align="right"  class="light-grey"><strong><?php echo $qc_quantity_total;?></strong></td>
    </tr>


</table>


</td>
<td valign="top">

<?php
if($_SESSION['login_as']=='admin' and $detail['status']=='Completed')
{

?>
<h1 style="font-size:12px">Admin Data</h1>
<table border="1" id="qc_table" cellpadding="10" cellspacing="0" width="70%">
<tr style="background-color:#999;font-weight:bold;font-size:10px">
<th>LCD Type</th>
<th>OEM</th>
<th>Non-OEM</th>

<th>Total</th>

</tr>
<?php
$qc_quantity_total = 0;
foreach($products as $product)
	{
		if($product['data_type']!='customer' and $product['data_type']!='qc' and $product['data_type']!='admin') continue;
			
			
			$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
			
			if($quantities)
			{
				$oem_qty = (int)$quantities['oem_received'];
				$non_oem_qty = (int)$quantities['non_oem_received'];
						}
			if($product['admin_oem_qty'])
			{
			$oem_qty = $product['admin_oem_qty'];
			$non_oem_qty = $product['admin_non_oem_qty'];
			
			}
			
			$admin_total = ($oem_qty * $product['oem_price']) + ($non_oem_qty * $product['non_oem_price']);;
?>

<tr >
<td><?=$product['description'];?></td>

<td align="center">
 <input type="text" style="width:80px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>','<?=$product['oem_price'];?>','<?=$product['non_oem_price'];?>')" id="admin_oem_qty_<?php echo $product['buyback_product_id'];?>" name="admin_oem_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $oem_qty;?>" />
 
 </td>
<td align="center">
 <input type="text" style="width:80px" onblur="changeAdminQty('<?=$product['buyback_product_id'];?>','<?=$product['oem_price'];?>','<?=$product['non_oem_price'];?>')" id="admin_non_oem_qty_<?php echo $product['buyback_product_id'];?>" name="admin_non_oem_qty[<?php echo $product['buyback_product_id'];?>]" value="<?php echo $non_oem_qty;?>" />
 
 </td>



<td align="center">
 <input type="text" style="width:80px" id="admin_total_total_<?php echo $product['buyback_product_id'];?>" name="admin_total_total[<?php echo $product['buyback_product_id'];?>]" value="<?php echo (float)$admin_total;?>" readonly="readonly" /></td>
 
 

</tr>

<?php
	}
	?>
    


</table>
<?php
}
?>
</td>
</tr>

</table>
<br />
<br /><br />
<?php if ($flag_received_qty): ?>
    <input type="submit" name="received" value="Received" onclick="" class="button" style="display:none" />
 <a class="fancybox2 fancybox.iframe button" href="shipment_received.php?buyback_id=<?php echo $detail['buyback_id'];?>">Received</a> 
<?php endif; ?>



<?php if ( in_array($detail['status'], array('Received'))): ?>
    <?php
	if($_SESSION['buyback_qc_shipments'])
	{
	?>
    <input type="submit" name="qcdone" value="Complete QC" class="button" />
    <?php
	}
	?>
<?php endif; ?>		

<?php if ($detail['status'] == 'QC Completed'): ?>		
    
  
        <input type="submit" name="completed" value="Complete Shipment" class="button" />
     
<?php endif; ?>	
	
    
  
        <input type="submit" name="save" value="Save" class="button" />
     

<br style="clear:both" /><br />
<?php
if($detail['payment_type']=='store_credit' and $detail['status']=='QC Completed')
{
	$checkQuery= $db->func_query("SELECT * FROM inv_buyback_payments WHERE buyback_id='".$detail['buyback_id']."'");
	if($checkQuery)
	{
		
	}
	else
	{
	?>
	<a class="fancybox2 fancybox.iframe button" href="issue_credit.php?buyback_id=<?php echo $detail['buyback_id'];?>&firstname=<?php echo base64_encode($firstname);?>&lastname=<?php echo base64_encode($lastname);?>&email=<?php echo base64_encode($email);?>">Issue Store Credit</a>  
<?php
	}
}
?>


<?php
if($detail['payment_type']=='cash' and $detail['status']=='QC Completed')
{
	$checkQuery= $db->func_query("SELECT * FROM inv_buyback_payments WHERE buyback_id='".$detail['buyback_id']."'");
	if($checkQuery)
	{
		
	}
	else
	{
	?>
	<a class="fancybox2 fancybox.iframe button" href="issue_cash.php?buyback_id=<?php echo $detail['buyback_id'];?>&firstname=<?php echo base64_encode($firstname);?>&lastname=<?php echo base64_encode($lastname);?>&email=<?php echo base64_encode($email);?>">Issue Payment</a>  
<?php
	}
}
?>
<input type="hidden" name="shipment" value="<?php echo $shipment_number;?>" />
</form>

<br />
 <table border="1" cellpadding="10" cellspacing="0" width="90%">
 <tr>
<td valign="top" width="50%"><form method="post" action="">
									<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
										<tr>
											<td>

												<b>Comment</b>
											</td>
											<td>
												<textarea rows="5" cols="50" name="comment" required></textarea>


											</td>
										</tr>
										
										<tr>
											<td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />	</td>
										</tr> 	   
									</table>
								</form></td><td width="50%">
                                <h2 align="center">Comment History</h2>
								<table width="98%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
									<tr>
										<th>Date</th>
										<th>Comment</th>
										

										<th>Added By</th>


									</tr>
									<?php
									$comments = $db->func_query("SELECT * FROM inv_buyback_comments WHERE buyback_id='".$detail['buyback_id']."'");
									foreach($comments as $comment)
									{
										?>
										<tr>
											<td><?php echo americanDate($comment['date_added']);?></td>
											<td><?php echo $comment['comment'];?></td>
											
											<td><?php echo get_username($comment['user_id']);?></td>

										</tr>
										<?php 

									}
									?> 

								</table>
                                
                                </td> 
 </tr>


</table>
<br /><br />
</body>
<script>
function changeAdminQty(product_id,oem_price,non_oem_price)
{
	var $oem_qty = $('#admin_oem_qty_'+product_id);
	var $non_oem_qty = $('#admin_non_oem_qty_'+product_id);	
	
	var total = parseInt($oem_qty.val()) * parseFloat(oem_price) + parseInt($non_oem_qty.val()) * parseFloat(non_oem_price); 
	
	$('#admin_total_total_'+product_id).val(total.toFixed(2));
	
}
function updateRejectedQty(product_id,total_received)
{
	$oem = $('#oem_received_'+product_id);
	
	$non_oem = $('#non_oem_received_'+product_id);	
	$rejected = $('#rejected_qty_'+product_id);
	
	/*if(parseInt($oem.val()) > parseInt(oem_qty))
	{
		
		alert('You are placing higher quantity than original');
		$oem.focus();
		return false;
		
	}
	if(parseInt($non_oem.val()) > parseInt(non_oem_qty))
	{
		alert('You are placing higher quantity than original');
		$non_oem.focus();
		return false;
		
	}*/
	
	var rejected = (parseInt(total_received)) - ( parseInt($oem.val()) + parseInt($non_oem.val()) )  ;
	$('#total_qc_received_'+product_id).val(parseInt($oem.val())+parseInt($non_oem.val())+parseInt($rejected.val()));
	//$('#rejected_qty_'+product_id).val(parseInt(rejected));
	updatePricing(product_id);
	
		
		
		
}
function updatePricing(product_id)
{
$oem_qty = $('#oem_received_'+product_id);
$non_oem_qty = $('#non_oem_received_'+product_id);
$rejected_qty = $('#rejected_qty_'+product_id);

$oem_price = $('#oem_price_'+product_id);
$non_oem_price = $('#non_oem_price_'+product_id);
//alert($oem_price.val());
oem_val = parseInt($oem_qty.val()) * parseFloat($oem_price.val());
non_oem_val = parseInt($non_oem_qty.val()) * parseFloat($non_oem_price.val()); 	
	
	$('#total_oem_total_'+product_id).val(oem_val.toFixed(2));
	$('#total_non_oem_total_'+product_id).val(non_oem_val.toFixed(2));
}

</script>
</html>