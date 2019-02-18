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
if($_SESSION['buyback_qc_shipments'] and in_array($detail['status'],array('In QC')))
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
	$flag_recevied_qty = false;
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
		addBBComment($detail['buyback_id'],$_SESSION['login_as'].' has changed the status to In QC.');
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
	}
	// Only when QC Done
	if($flag_change_qty)
	{
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
	
					
				
				
				
				// Shipment Box Preparation Ends
					
					
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
	</script>	
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
                                        <td> </td>
                                        <td> </td>
                                        <td> </td>
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

<table border="1" cellpadding="10" cellspacing="0" width="90%">
    <tr>
        <th><input type="checkbox" onclick="toggleCheck(this)" /></th>
        <th>Image</th>
        <th>SKU</th>
        <th>Title</th>
        <th>OEM</th>
        <th>Qty</th>
        <?php
		if($flag_change_qty)
		{
		?>
        <th>Received</th>
        <?php	
		}
		?>
        <th>Non-OEM</th>
        <th>Qty</th>
        <?php
		if($flag_change_qty)
		{
		?>
        <th>Received</th>
        <?php	
		}
		?>
        
        <?php
		if($flag_change_qty)
		{
		?>
        <th>Total Received</th>
        <th>Rejected</th>
        <?php	
		}
		?>
        
        <?php
		if($flag_received_qty)
		{
		?>
        <th>Total Received</th>
        <?php	
		}
		?>
        <th>Sub Total</th>

        
    </tr>
    <?php
	foreach($products as $product)
	{
	?>
    <tr>
    <td align="center"><input type="checkbox" /></td>
    <td align="center"><img src="<?=$product['image_path'];?>" width="100" height="100" /></td>
    <td align="center"><?=linkToProduct($product['sku'],$host_path);?></td>
    <td align="center"><?=$product['description'];?></td>
     <td align="center">$<?=number_format($product['oem_price'],2);?></td>
     <td align="center"><?=(int)$product['oem_quantity'];?></td>
     <?php
		if($flag_change_qty)
		{
			$quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
			if($quantities)
			{
				$oem_qty = (int)$quantities['oem_received'];
				$non_oem_qty = (int)$quantities['non_oem_received'];
				$rejected_qty = (int)$quantities['rejected_qty'];	
			}
			else
			{
				$oem_qty = $product['oem_quantity'];
				$non_oem_qty = $product['non_oem_quantity'];
				$rejected_qty = 0;
				
			}
		?>
        <td align="center"><input type="text" id="oem_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo $oem_qty;?>" onblur="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['oem_quantity'];?>','<?php echo $product['non_oem_quantity'];?>')" name="oem_received[<?php echo $product['buyback_product_id'];?>]" style="width:80px" /></td>
        <?php	
		}
		?>
     <td align="center">$<?=number_format($product['non_oem_price'],2);?></td>
     <td align="center"><?=(int)$product['non_oem_quantity'];?></td>
     <?php
		if($flag_change_qty)
		{
		?>
         <td align="center"><input type="text" id="non_oem_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo $non_oem_qty;?>" name="non_oem_received[<?php echo $product['buyback_product_id'];?>]" style="width:80px" onblur="updateRejectedQty('<?php echo $product['buyback_product_id'];?>','<?php echo $product['oem_quantity'];?>','<?php echo $product['non_oem_quantity'];?>')" /></td>
         
         
         <td align="center"><?php echo $product['total_received'];?></td>
         <td align="center"><input type="text" id="rejected_qty_<?php echo $product['buyback_product_id'];?>" value="<?php echo $rejected_qty;?>" name="rejected_qty[<?php echo $product['buyback_product_id'];?>]" style="width:80px" /></td>
        <?php	
		}
		?>
        
        <?php
		if($flag_received_qty)
		{
		?>
         <td align="center"><input type="text" id="total_received_<?php echo $product['buyback_product_id'];?>" value="<?php echo (int)$product['total_received'];?>" name="total_received[<?php echo $product['buyback_product_id'];?>]" style="width:80px"  /></td>
         
         
        <?php	
		}
		?>
     <td align="center">$<?=number_format($product['sub_total'],2);?></td>
    </tr>
    <?php	
	}
	?>

    
</table>



<br /><br />
<?php if ($flag_received_qty): ?>
    <input type="submit" name="received" value="Received" onclick="if (!confirm('Are you sure?')) {
    return false;
}" class="button" />
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

<?php if ($detail['status'] == 'In QC'): ?>		
    
  
        <input type="submit" name="completed" value="Complete Shipment" class="button" />
     
<?php endif; ?>	
<?php if ($detail['status'] != 'Completed'): ?>		
    
  
        <input type="submit" name="save" value="Save" class="button" />
     
<?php endif; ?>		
<br style="clear:both" /><br />
<?php
if($detail['payment_type']=='store_credit' and $detail['status']=='In QC')
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
if($detail['payment_type']=='cash' and $detail['status']=='In QC')
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
function updateRejectedQty(product_id,oem_qty,non_oem_qty)
{
	$oem = $('#oem_received_'+product_id);
	$non_oem = $('#non_oem_received_'+product_id);	
	
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
	
	var rejected = (parseInt(oem_qty)+ parseInt(non_oem_qty)) - ( parseInt($oem.val()) + parseInt($non_oem.val()) )  ;
	$('#rejected_qty_'+product_id).val(parseInt(rejected));
}

</script>
</html>