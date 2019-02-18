<?php
include_once 'auth.php';
include_once 'inc/functions.php';
unset($_SESSION['paid_order']);
if($_GET['action']=='cancelOrder')
{
	$order_id = $_GET['order'];
	$db->db_exec("UPDATE inv_orders SET order_status='Canceled' WHERE order_id='".$order_id."' and amazon_cancel_order=1");
	header("Location:manage_returns.php");
    exit;	
	
	
}
if ($_POST['action'] == 'save_item') {
    $item_id = (int) $_POST['item_id'];
    $sku = $_POST['sku'];
    $qty = (int) $_POST['qty'];
    $unit = (float) $_POST['item_unit'];
    $discount = (int) $_POST['discount'];
    $price = (float) $_POST['price'];

    $json = array();
    $record = $db->db_exec("UPDATE inv_orders_items SET product_sku='$sku',product_unit='$unit',product_discount='$discount',product_price='$price',product_qty='$qty',dateofmodification='" . date('Y-m-d H:i:s') . "' WHERE id='$item_id'");
    if ($record) {
        $hdata = array();
        $hdata['order_id'] = $_POST['order_id'];
        $hdata['comment'] = $sku . ' price has been changed.';
        $hdata['user_id'] = $_SESSION['user_id'];
        $hdata['date_added'] = date('Y-m-d H:i:s');
        $db->func_array2insert("inv_order_history", $hdata);

        $json['success'] = 'Record modified!';
    } else {
        $json['error'] = 'Warning: Record not modified, please try again or contact your administrator.';
    }
    echo json_encode($json);
    exit;
}

if (isset($_POST['update'])) {
    $orderID = $db->func_escape_string($_GET['order']);
    $first_name = $db->func_escape_string($_POST['first_name']);
    $po_term = $db->func_escape_string($_POST['po_term']);
    $last_name = $db->func_escape_string($_POST['last_name']);
    $order_status = $db->func_escape_string($_POST['order_status']);
    $shipping_date = date('Y-m-d', strtotime($db->func_escape_string($_POST['shipping_date'])));
    //$po_payment_source = $db->func_escape_string($_POST['po_payment_source']);
    //$po_payment_source_detail = $db->func_escape_string($_POST['po_payment_source_detail']);
    //$po_payment_source_amount = (float)$db->func_escape_string($_POST['po_payment_source_amount']);
    $payment_method = $db->func_escape_string($_POST['payment_method']);
    $customer_po = $db->func_escape_string($_POST['customer_po']);
    $payment_detail_1 = $db->func_escape_string($_POST['payment_detail_1']);
    $payment_detail_2 = $db->func_escape_string($_POST['payment_detail_2']);
    $paid_price = (float) $db->func_escape_string($_POST['paid_price']);
    $db->db_exec("update inv_orders_details SET first_name = '$first_name' , last_name = '$last_name',po_term='$po_term',payment_method='$payment_method',shipping_date='" . $shipping_date . "' where order_id = '$orderID'");

    $db->db_exec("update inv_orders SET order_status='" . $order_status . "', customer_po='". $customer_po ."', payment_detail_1='$payment_detail_1',payment_detail_2='$payment_detail_2' WHERE order_id='" . $orderID . "'");

    if ($paid_price) {
        $checkOld = $db->func_query_first_cell("SELECT  paid_price FROM inv_orders WHERE order_id='$orderID'");

        if ($checkOld) {
            $db->db_exec("UPDATE inv_orders SET paid_price=paid_price+$paid_price WHERE order_id='$orderID'");

            $hdata = array();
            $hdata['order_id'] = $orderID;
            $hdata['comment'] = 'Payment has been made for the amount of $' . number_format($paid_price, 2) . ' via "' . $payment_method . '" method';
            $hdata['user_id'] = $_SESSION['user_id'];
            $hdata['date_added'] = date('Y-m-d H:i:s');
            $db->func_array2insert("inv_order_history", $hdata);
        } else {
            $db->db_exec("UPDATE inv_orders SET paid_price=$paid_price WHERE order_id='$orderID'");

            $hdata = array();
            $hdata['order_id'] = $orderID;
            $hdata['comment'] = 'Payment has been made for the amount of $' . number_format($paid_price, 2) . ' via "' . $payment_method . '" method';
            $hdata['user_id'] = $_SESSION['user_id'];
            $hdata['date_added'] = date('Y-m-d H:i:s');
            $db->func_array2insert("inv_order_history", $hdata);
        }
    }

    header("Location:viewOrderDetail.php?order=$orderID");
    exit;
}

if ($_GET['action'] == 'delete' && (int) $_GET['fileid']) {
    $fileid = (int) $_GET['fileid'];
    $orderID = $db->func_escape_string($_GET['order']);

    $db->db_exec("Delete from inv_order_docs where id = '$fileid' and order_id = '$orderID'");

    header("Location:viewOrderDetail.php?order=$orderID");
    exit;
}

//add comments
if (isset($_POST['addcomment'])) {
    $orderID = $db->func_escape_string($_GET['order']);
    $po_check = $db->func_query_first_cell("SELECT store_type FROM inv_orders WHERE order_id='$orderID'");
    if ($po_check != 'po_business') {
        $addcomment = array();
        $addcomment['date_added'] = date('Y-m-d H:i:s');
        $addcomment['user_id'] = $_SESSION['user_id'];
        $addcomment['comment'] = $db->func_escape_string($_POST['comment']);
        $addcomment['order_id'] = $orderID;

        $order_history_id = $db->func_array2insert("oc_order_history", $addcomment);

        $order_mod_logs = array();
        $order_mod_logs['order_history_id'] = $order_history_id;
        $order_mod_logs['order_id'] = $orderID;
        $order_mod_logs['user_id'] = $_SESSION['user_id'];
        $order_mod_logs['date_modified'] = date('Y-m-d H:i:s');

        $db->func_array2insert("oc_order_mod_logs", $order_mod_logs);
    } else {
        $addcomment = array();
        $addcomment['date_added'] = date('Y-m-d H:i:s');
        $addcomment['user_id'] = $_SESSION['user_id'];
        $addcomment['comment'] = $db->func_escape_string($_POST['comment']);
        $addcomment['order_id'] = $orderID;
        $order_history_id = $db->func_array2insert("inv_order_history", $addcomment);
    }
    $_SESSION['message'] = "New comment is added.";
    header("Location:$host_path/viewOrderDetail.php?order=$orderID");
    exit;
}

//upload return item item images
if ($_FILES['order_docs']['tmp_name']) {
    $imageCount = 0;
    $orderID = $db->func_escape_string($_GET['order']);

    $uniqid = uniqid();
    $name = explode(".", $_FILES['order_docs']['name']);
    $ext = end($name);

    $destination = $path . "files/" . $uniqid . ".$ext";
    $file = $_FILES['order_docs']['tmp_name'];

    if (move_uploaded_file($file, $destination)) {
        $orderDoc = array();
        $orderDoc['attachment_path'] = "files/" . basename($destination);
        $orderDoc['type'] = $_FILES['order_docs']['type'];
        $orderDoc['size'] = $_FILES['order_docs']['size'];
        $orderDoc['description'] = $_POST['description'];
        $orderDoc['date_added'] = date('Y-m-d H:i:s');
        $orderDoc['order_id'] = $orderID;

        $db->func_array2insert("inv_order_docs", $orderDoc);
        $imageCount++;
    }

    if ($imageCount > 0) {
        $_SESSION['message'] = "attachments are added successfully.";
    } else {
        $_SESSION['message'] = "attachments are not added.";
    }
    header("Location:$host_path/viewOrderDetail.php?order=$orderID");
    exit;
}

if ($_GET['order']) {
    $orderID = $db->func_escape_string($_GET['order']);

    $order = $db->func_query_first("select inv_orders.* , inv_orders_details.* from  inv_orders left join inv_orders_details on (inv_orders_details.order_id = inv_orders.order_id) where inv_orders.order_id = '$orderID' ");
    $order_items = $db->func_query("Select * from inv_orders_items where order_id = '$orderID' ");

    if (!$order) {
        $order = $db->func_query_first("select inv_return_orders.* , inv_orders_details.* from  inv_return_orders inner join inv_orders_details on (inv_orders_details.order_id = inv_return_orders.order_id) where inv_orders_details.order_id = '$orderID' ");
        $order_items = $db->func_query("Select * from inv_orders_items where order_id = '$orderID' ");
    }

    $sub_total = 0;
    foreach ($order_items as $order_item) {
        $sub_total+=$order_item['product_price'];
    }
    
    $order_total = $order['shipping_cost'] + $sub_total;

    $comments = $db->func_query("SELECT oh.`date_added`,oh.`comment`,om.`user_id`,u.`name` FROM oc_order_history oh LEFT JOIN oc_order_mod_logs om ON (om.order_history_id = oh.order_history_id)
								 LEFT JOIN inv_users u ON (u.id = om.user_id)
								 WHERE oh.order_id = '$orderID'
								 UNION ALL
								 SELECT i.date_added,i.`comment`,i.user_id,iu.name FROM inv_order_history i LEFT JOIN inv_users iu ON (i.user_id = iu.id) WHERE i.order_id='$orderID'
								 ");

    $attachments = $db->func_query("select * from inv_order_docs where order_id = '$orderID' AND is_invoice=0");

    $order_fraud = $db->func_query_first("select * from oc_order_fraud where order_id = '$orderID'");
} else {
    exit;
}

$order_fees = $db->func_query("select * from inv_order_fees where order_id = '$orderID'");

$order_shipments = $db->func_query("select * from inv_order_shipments where order_id = '$orderID'");
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="include/calendar.css" rel="stylesheet" type="text/css" />
        <link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />
        <script  type="text/javascript" src="include/calendar.js"></script>
        <script  type="text/javascript" src="include/calendar-en.js"></script>
        <script  type="text/javascript" src="include/calhelper.js"></script>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
        <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
        <title>Order Detail</title>

        <style>
            .read_class{
                background-color: #eee;
                border: 1px solid #ccc;
            }
        </style>
        <script>
            $(document).ready(function (e) {
                $('.fancybox3').fancybox({width: '90%', autoCenter: true, autoSize: true});
            });
        </script>
    </head>
    <body>
	<?php
		if ($order['store_type'] == 'po_business') {
		    $is_po = true;
		} else {
		    $is_po = false;
		}
	?>
	<?php include_once 'inc/header.php'; ?>

	<?php if (@$_SESSION['message']): ?>
	            <div align="center"><br />
	                <font color="red"><?php echo $_SESSION['message'];unset($_SESSION['message']); ?><br /></font>
	            </div>
	<?php endif; ?>

	<h2 align="center" style="float:left;margin-left:40%"> Order Details - Customer's Detail </h2>
	
	<?php if ($is_po): ?>
          <div style="float:right;margin-right:15%;margin-top:9px"><a href="email_invoice.php?order_id=<?= $order['order_id']; ?>&action=email" class="button">Email Invoice</a> <a href="email_invoice.php?order_id=<?= $order['order_id']; ?>&action=view" target="_blank" class="button">Download PDF</a></div>
    <?php endif; ?>

    <div align="center" style="clear:both">
    	<?php if ($order) : ?>
                <form method="post" action="" id="xfrm">
                    <table cellpadding="10" style="border:1px solid #585858;" width="70%" border="1">
                        <tr>
                            <th>Order ID : </th>
                            <td> <?= $order['order_id']; ?> </td>

                            <th>Order Date : </th>
                            <td> <?= date('d-M-Y H:i:s', strtotime($order['order_date'])); ?> </td>
                        </tr>
            			<?php if ($is_po):?>
                            <tr>
                                <th>Customer PO #:</th>
                                <td><input name="customer_po" size="15" value="<?= $order['customer_po']; ?>" type="text"></td>
                                <th>Shipping Date:</th>
                                <td><input type="text" onclick="return showCalendar('end_date', '%Y-%m-%d', '24', true);" id="shipping_date" name="shipping_date" size="20" style="width: 110px;" readonly="readonly" value="<?php echo ($order['shipping_date'] == '' ? '' : date('Y-m-d', strtotime($order['shipping_date']))); ?>" /><a href="javascript:void();" onclick="return showCalendar('shipping_date', '%Y-%m-%d', '24', true);"><img src="include/calendar.gif" class='calender',style='margin-top: 5px; float: left'> </a></td>
                            </tr>
        			   <?php endif;?>
                       
                       <tr>
                            <th> Order Total</th>
                            <td> $<?= number_format($order_total, 2) ?> </td>

                            <th>Store Type </th>
                            <td> <?= $order['store_type'] ?> </td>
                        </tr>

                        <tr>
                            <th>Sub Store Type </th>
                            <td> <?= $order['sub_store_type'] ?> </td>

                            <th>Order Status</th>
                            <td> 
                            	<span id="order_status_span"><?= $order['order_status'] ?></span> 
                            	<?php if ($is_po and strtolower($order['order_status']) == 'estimate'):?>
                                    <input type="button" class="button"  value="Confirm Order" onclick="changeOrderStatus('Unshipped', this)" />
                                    <script type="text/javascript">
                                        function changeOrderStatus(status, obj){
                                            if (!confirm('Are you sure?'))
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                $('#order_status_span').html('Unshipped');
                                                $('#order_status').val('Unshipped');
                                                $(obj).hide();
                                                $('input[name=update]').click();
                                            }
                                        }
                                    </script>
        						<?php endif;?>
                                <input type="hidden" name="order_status" id="order_status" value="<?php echo $order['order_status']; ?>">
                            </td>
                        </tr>

                        <tr>
                            <th>Fishbowl Uploaded</th>
                            <td> <?= $order['fishbowl_uploaded'] ?> </td>

                            <th>Customer Email </th>
                            <td> <?= $order['email'] ?> (<?=$db->func_query_first_cell("SELECT ip FROM oc_order WHERE order_id='".$order['order_id']."'");?>) </td>
                        </tr>

                        <tr>
                            <th>Payment Method : </th>
                            <td> <?= $order['payment_method'] ?> </td>

                            <th>Shipping Method : </th>
                            <td> <?= $order['shipping_method'] ?>
							    <?php
								    if ($order['shipping_method'] == 'Customer FedEx' || $order['shipping_method'] == 'Customer UPS') {
								        echo '(' . $order['customer_fedex_code'] . ')';
								    }
							    ?>
                            </td>
                        </tr>

                        <tr>
    						<?php if (!$is_po):?>
                                <th>Shipping Cost : </th>
                                <td> $<?= $order['shipping_cost'] ?> </td>
        					<?php endif;?>

                            <th>Phone : </th>
                            <td> <?= $order['phone_number'] ?> </td>
                            
    						<?php if ($is_po):?>
                                <td> </td>
                                <td> </td>
                            <?php endif;?>
                        </tr>
                        
                        <?php if ($is_po):?>
                            <tr>
                                <th colspan="4" align="center">--- PO DETAILS ---</td>
                            </tr>

                            <tr>
                                <th>Payment Source :</th>
                                <td>
	                                <?php
		                                if (round($order_total - $order['paid_price'], 2) > 0) {
		                                    ?>
		                                        <a href="popupfiles/charge_card.php?order_id=<?= $orderID; ?>" class="fancybox3 fancybox.iframe button" >Charge Card</a> <a href="popupfiles/payment_status.php?order_id=<?= $orderID; ?>" class="fancybox3 fancybox.iframe button" >Other Method</a>
		                                    <?php
		                                }
	                                ?>

                                    <input type="hidden" name="po_payment_source" id="po_payment_source" value="<?php echo $order['po_payment_source']; ?>" />
                                    <input type="hidden" name="po_payment_source_detail" id="po_payment_source_detail" value="<?php echo $order['po_payment_source_detail']; ?>" />
                                    <input type="hidden" name="po_payment_source_amount" id="po_payment_source_amount" value="<?php echo $order['po_payment_source_amount']; ?>" />

        							<!-- <span id="po_payment_source_name"><?php echo ucfirst($order['po_payment_source']); ?></span> -->
        						</td>		
                                <th>Details :</th>
                                <td>
	                                <?php
	                                	echo $order['payment_detail_1'] . "<br>";
	                                	echo $order['payment_detail_2'] . "<br>";
	                                ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Terms</th>
                                <td>
        							<?php $terms = array(5, 10, 15, 30, 45); ?>
                                    <select name="po_term" id="po_term">
                                        <option value="0">No Terms</option>
	                                    <?php
		                                    foreach ($terms as $term) {
		                                        ?>
		                                            <option value="<?= $term; ?>" <?php if ($term == $order['po_term']) echo 'selected'; ?>>Net <?= $term; ?></option>
		                                        <?php
		                                    }
	                                    ?>
                                    </select>
                                </td>
                                
        						<?php if ($order['shipping_date']):?>
                                    <th>Due Date:</th>
            						<?php if ($order['po_term'] == 0):?>
                                        <td>
                                            No Terms
                                        </td>
                					<?php else: ?>
                                        <td>
                							<?php echo date('d M Y', strtotime($order['shipping_date'] . ' + ' . (int) $order['po_term'] . ' days')); ?>
                                        </td>
                					<?php endif; ?>
                                <?php endif; ?>
                            </tr>
                       <?php endif; ?>
                    </table>

                    <br />
                    <table cellpadding="5" style="border:1px solid #585858;" width="70%" border="1">
                        <tr>
                            <td width="60%">
                                <h3 align="center">Shipping</h3>
                                <table width="100%" border="0" align="left">
                                    <tr>
                                        <th width="30%">Name : </th>
                                        <td>
                                            <input type="text" name="first_name" size="15" value="<?php echo $order['first_name']; ?>" /> 
                                            <input type="text" name="last_name" size="15" value="<?php echo $order['last_name']; ?>" /> 
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Address : </th>
                                        <td> <?= $order['address1'] . " " . @$order['address2'] ?> </td>
                                    </tr>

                                    <tr>
                                        <th>City : </th>
                                        <td> <?= $order['city'] ?> </td>
                                    </tr>

                                    <tr>
                                        <th>State : </th>
                                        <td> <?= $order['state'] ?> </td>
                                    </tr>

                                    <tr>
                                        <th>Country : </th>
                                        <td> <?= $order['country'] ?> </td>
                                    </tr>

                                    <tr>
                                        <th>Zip : </th>
                                        <td> <?= $order['zip'] ?> </td>
                                    </tr>
                                </table>
                            </td>

                            <td width="40%">
                                <h3 align="center">Billing</h3>
                                <table width="100%" border="0" align="left">
                                    <tr>
                                        <th>Name : </th>
                                        <td>
   											<?= $order['first_name'] . " " . @$order['last_name'] ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Address : </th>
                                        <td> <?= $order['bill_address1'] . " " . @$order['bill_address2'] ?> </td>
                                    </tr>

                                    <tr>
                                        <th>City : </th>
                                        <td> <?= $order['bill_city'] ?> </td>
                                    </tr>

                                    <tr>
                                        <th>State : </th>
                                        <td> <?= $order['bill_state'] ?> </td>
                                    </tr>

                                    <tr>
                                        <th>Country : </th>
                                        <td> <?= $order['bill_country'] ?> </td>
                                    </tr>

                                    <tr>
                                        <th>Zip : </th>
                                        <td> <?= $order['bill_zip'] ?> </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <br />

                    <table align="center" border="1" cellspacing="0" cellpadding="5" width="70%">
                        <tr style="font-weight:bold">
                            <td>Item ID</td>
                            <td>SKU</td>
                            <td>Qty</td>
                            <td>Price</td>
                            <?php
							if ($_SESSION['login_as'] == 'admin') {
							?>
                             <td>True Cost</td>
                            <?php
							}
							?>
                            <td>% Discount</td>
                            <td>Line Total</td>
							 <?php
							if ($_SESSION['login_as'] == 'admin') {
							?>
                             <td>Line True Cost</td>
                            <?php
							}
							?>
                            <td>Action</td>
                        </tr>

    					<?php 
							$true_cost_total = 0.00;
							foreach ($order_items as $order_item): ?>
                            <tr>
                                <td title="<?php echo $order_item['product_name']; ?>"><?php echo $order_item['order_item_id']; ?></td>

                                <td><input type="text" id="order_item_sku_<?= $order_item['id']; ?>" value="<?php echo $order_item['product_sku']; ?>" class="read_class" style="width:130px" readOnly ></td>

                                <td><input type="text" id="order_item_qty_<?= $order_item['id']; ?>" value="<?php echo $order_item['product_qty']; ?>" class="read_class" readOnly style="width:70px" onChange="calculateLineTotal('<?= $order_item['id']; ?>')" ></td>
                                <td>
                                    <input type="text" id="order_item_unit_<?= $order_item['id']; ?>" value="<?php echo ($order_item['product_unit'] ? $order_item['product_unit'] : round($order_item['product_price'] / $order_item['product_qty'], 2)); ?>" class="read_class" readOnly style="width:130px" onChange="calculateLineTotal('<?= $order_item['id']; ?>')">
                                </td>
                                
                                <?php if ($_SESSION['login_as'] == 'admin'):
										$true_cost = ($order_item['product_true_cost']);
								?>
                             		<td>$<?php echo number_format($true_cost,2);?> </td>
                            	<?php endif; ?>
                                
                                <td><input type="text" id="order_item_discount_<?= $order_item['id']; ?>" value="<?php echo $order_item['product_discount']; ?>" class="read_class" readOnly style="width:70px" onChange="calculateLineTotal('<?= $order_item['id']; ?>')" ></td>
                                <td><input type="text" id="order_item_price_<?= $order_item['id']; ?>" value="<?php echo $order_item['product_price']; ?>" class="read_class" readOnly style="width:130px" ></td>
                                
                                 <?php if ($_SESSION['login_as'] == 'admin'):
										$true_cost = $true_cost*$order_item['product_qty'];
										$true_cost_total += $true_cost;
								
									?>
                             		<td>$<?php echo number_format($true_cost,2);?> </td>
                           		 <?php endif; ?>
                                
                                <td>
	        						<?php if (strtolower($order['order_status']) == 'estimate'): ?>
	                                        <a id="edit_btn_<?= $order_item['id']; ?>" href="javascript:void(0)" onClick="editThis('<?= $order_item['id']; ?>')">Edit</a> |  <a id="save_btn_<?= $order_item['id']; ?>" href="javascript:void(0);" onClick="saveThis('<?= $order_item['id']; ?>')">Save</a>
	            					<?php endif; ?>
                                </td>
                            </tr>
    					<?php endforeach; ?>
                    
                        <tr style="font-weight:bold">
                            <td align="right" colspan="<?=($_SESSION['login_as'] == 'admin'?9:7);?>">
                                
                             <?php $fee_table = '';
								   if($order_fees){
                    				 	$fee_table.='<table align="center" border="1" cellspacing="0" cellpadding="5" width="70%">';
							 			$fee_total = 0; 
							 			foreach($order_fees as $order_fee){
											$fee_table.='<tr>
												<td align="right">
													<b>'.$order_fee['fee_type'].':</b>
												</td>
												
												<td align="right">
													$'.$order_fee['fee'].'
												</td>
											</tr>';	
							 				$fee_total += $order_fee['fee']; 
							 			}
										
							 			$fee_table.='<tr>
												 <td align="right" style="color:green"><b>Total:</b></td>
												 <td align="right" style="color:green"><b>$'.$fee_total.'</b></td>
											</tr>     
				                    	</table>';
								   }
								?>
                                <table cellpadding="5" width="25%" cellspacing="0" style="font-weight:bold" border="0">
                                    <tr>
                                        <td align="right">Sub Total:</td>
                                        <td>$<?= number_format($sub_total, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td align="right">Shipping:</td>
                                        <td>$<?= number_format($order['shipping_cost'], 2) ?></td>
                                    </tr>
                                    <?php
									$shipping_cost= 0.00;
									
									if($order_shipments[0]['voided']==0):
									
									?>
                                    <tr>
                                        <td align="right">Shipping Cost:</td>
                                        <td>$<?= number_format($order_shipments[0]['shipping_cost']+$order_shipments[0]['insurance_cost'], 2) ?></td>
                                    </tr>
                                    <?php
										$shipping_cost = $order_shipments[0]['shipping_cost']+$order_shipments[0]['insurance_cost'];
										
									endif;
									
									?>
                                    <tr>
                                        <td align="right">Order Total:</td>
                                        <td>$<?= number_format($order_total, 2) ?></td>
                                    </tr>
                                    
                                    <?php if ($_SESSION['login_as'] == 'admin'): ?>
	                                     <tr>
	                                        <td align="right" style="color:blue">Order True Cost:</td>
	                                        <td style="color:blue">$<?= number_format($true_cost_total, 2) ?></td>
	                                    </tr>
                                        <?php
										if($order['transaction_fee']>0)
										{
										?>
                                        <tr>
	                                        <td align="right" style="color:blue">Transaction Fee:</td>
	                                        <td style="color:blue">-$<?= number_format($order['transaction_fee'], 2) ?></td>
	                                    </tr>
                                        <?php
										}
										?>
                                        
                                         <?php
										if($fee_total*(-1)>0)
										{
										?>
                                        <tr>
	                                        <td align="right" style="color:blue">Fee:</td>
	                                        <td style="color:blue">-$<?= number_format($fee_total*(-1), 2) ?></td>
	                                    </tr>
                                        <?php
										}
										?>
	                                    
	                                    <tr>
	                                        <td align="right" style="color:green">Profit:</td>
	                                        <td style="color:green">$<?= number_format($order_total - $true_cost_total - $order['transaction_fee'] - ($shipping_cost) + ($fee_total), 2) ?></td>
	                                    </tr>
                                    <?php endif; ?>
                                    
                                    <tr>
                                        <td colspan="2" style="border-bottom:1px dashed #000"> </td>
                                    </tr>
                                    <tr>
                                        <td align="right">
                                            Amount Paid:</td>
                                        <td>$<?= number_format($order['paid_price'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td align="right">Amount Due:</td>
                                        <td>$<?= number_format(round($order_total - $order['paid_price'], 2), 2); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <br />
                    <?php echo $fee_table;?>

                    <div align="center">
                        <input type="hidden" name="payment_method" value="<?= $order['payment_method']; ?>">
                        <input type="hidden" name="paid_price" value="">
                        <input type="hidden" name="payment_detail_1" value="<?= $order['payment_detail_1']; ?>">
                        <input type="hidden" name="payment_detail_2" value="<?= $order['payment_detail_2']; ?>">

                        <input type="submit" name="update" value="Update Order" />
                        <?php
						if($order['store_type']=='amazon' and strtolower($order['order_status']) == 'unshipped'):
						?>
                       <!-- <input type="button" value="Cancel Order" onClick="if(confirm('Are you sure want to cancel this order?')){window.location='viewOrderDetail.php?order=<?php echo $_GET['order'];?>&action=cancelOrder';}"> -->
                        
                        <?php
						
						endif;
						?>
                    </div>	
                    
                    <br />
                    
                    
                    <br />
                    <?php if($order_shipments):?>
                    	<table align="center" border="1" cellspacing="0" cellpadding="5" width="70%">
                    			<tr>
									<th>Shipping Cost</th>
									<th>Insurance Cost</th>
									<th>Shipping Date</th>
									<th>Tracking Number</th>
									<th>Service Code</th>
									<th>Carrier Code</th>
									<th>Weight</th>
									<th>Voided</th>
								</tr>	
								<?php foreach($order_shipments as $order_shipment):?>
									<tr>
										<td>$<?php echo $order_shipment['shipping_cost'];?></td>
										<td>$<?php echo $order_shipment['insurance_cost'];?></td>
										<td><?php echo $order_shipment['ship_date'];?></td>
										<td><?php echo $order_shipment['tracking_number'];?></td>
										<td><?php echo $order_shipment['service_code'];?></td>
										<td><?php echo $order_shipment['carrier_code'];?></td>
										<td><?php echo $order_shipment['weight']. " ". $order_shipment['units'];?></td>
										<td><?php echo $order_shipment['voided'];?></td>
									</tr>	
							   <?php endforeach;?>     
                    	</table>
                    <?php endif;?>
                </form>
		<?php else : ?>
                <h4> No Order Found</h4>
		<?php endif; ?>
    </div>

    <br /><br />    				
    <div align="center">
            <table width="70%">
                <tr>
                    <td width="50%" valign="top">
                        <form method="post" action="">
                            <table border="1" cellpadding="10" width="90%">
                                <tr>
                                    <td>
                                        <textarea rows="5" cols="50" name="comment" required></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input type="submit" class="button" name="addcomment" value="Add Comment" />	  		  	 
                                    </td>
                                </tr>	
                            </table>
                            <input type="hidden" name="order_id" value="<?php echo $orderID ?>" />
                        </form>

                        <h2>Comment History</h2>
                        <table border="1" cellpadding="10" width="90%">
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Comment</th>
                            </tr>
							<?php foreach ($comments as $comment): ?>
                                <tr>
                                    <td><?php echo $comment['date_added']; ?></td>
                                    <td><?php echo ($comment['user_id']) ? $comment['name'] : 'admin'; ?></td>
                                    <td>
									    <?php
									    //parse usps , ups or fedex tracking number and make them as link
									    preg_match("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", $comment['comment'], $matches);
									    if ($matches) {
									        if (stristr($comment['comment'], "USPS")) {
									            $comment['comment'] = preg_replace("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf($usps_link, $matches[1], $matches[1]), $comment['comment']);
									        } elseif (stristr($comment['comment'], "UPS")) {
									            $comment['comment'] = preg_replace("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf($ups_link, $matches[1], $matches[1]), $comment['comment']);
									        } else {
									            $comment['comment'] = preg_replace("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf($fedex_link, $matches[1], $matches[1]), $comment['comment']);
									        }
									    }
									    ?>
    									<?php echo $comment['comment']; ?>
                                    </td>
                                </tr>
							 <?php endforeach; ?>
                        </table>		
                    </td>
                    <td width="50%" valign="top">
                        <form method="post" action="" enctype="multipart/form-data">
                            <table border="1" cellpadding="10" width="100%">
                                <tr>
                                    <td>
                                        <input type="file" name="order_docs" required />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <textarea rows="2" cols="50" name="description" style="resize:none"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input type="submit" class="button" name="upload" value="Upload" />	  		  	 
                                    </td>
                                </tr>	
                            </table>
                            <input type="hidden" name="order_id" value="<?php echo $orderID ?>" />
                        </form>

                        <h2>Attachments</h2>
                        <table border="1" cellpadding="10" width="100%">
                            <tr>
                                <th>Date</th>
                                <th>File</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($attachments as $attachment): ?>
                                <tr>
                                    <td><?php echo $attachment['date_added']; ?></td>
                                    <td><?php echo $attachment['type']; ?></td>
                                    <td><?php echo $attachment['description']; ?></td>
                                    <td>
                                        <a href="<?php echo $host_path . "" . $attachment['attachment_path']; ?>">download</a>
                                        |
                                        <a href="viewOrderDetail.php?action=delete&fileid=<?php echo $attachment['id'] ?>&order=<?php echo $orderID; ?>" onclick="if (!confirm('Are you sure, You want to delete this file?')) { return false; }">delete</a>
                                    </td>
                                </tr>
							<?php endforeach; ?>
                        </table>		
                    </td>			
                </tr>
            </table>
        </div>

        <br /><br /> 
        <div align="center"> 
            <table border="1" style="border-collapse:collapse;" width="70%" cellpadding="10">
                <tr>
                    <th>Country Match</th>
                    <th>Distance</th>
                    <th>IP City</th>
                    <th>IP Region</th>
                    <th>ISP</th>
                    <th>IP Organization</th>
                    <th>IP User Type</th>
                    <th>IP Domain</th>
                    <th>IP Corporate Proxy</th>
                    <th>Anonymous Proxy</th>
                </tr>

                <tr>       	  
                    <td><?php echo $order_fraud['country_match'] ?></td>
                    <td><?php echo $order_fraud['distance'] ?></td>	 
                    <td><?php echo $order_fraud['ip_city'] ?></td> 
                    <td><?php echo $order_fraud['ip_region'] ?></td>
                    <td><?php echo $order_fraud['ip_isp'] ?></td>
                    <td><?php echo $order_fraud['ip_org'] ?></td>
                    <td><?php echo $order_fraud['ip_user_type'] ?></td>
                    <td><?php echo $order_fraud['ip_domain'] ?></td>
                    <td><?php echo $order_fraud['ip_corporate_proxy'] ?></td> 
                    <td><?php echo $order_fraud['anonymous_proxy'] ?></td> 
                </tr>
            </table>
        </div>

        <div align="center"> 
            <br />
            <a href="order.php" style="margin-left:20px;"> Back </a> 
        </div>	 
    </body>
</html>
<script type="text/javascript">
    function editThis(item_id)
    {
        $sku = $('#order_item_sku_' + item_id);
        $qty = $('#order_item_qty_' + item_id);
        $unit = $('#order_item_unit_' + item_id);
        $discount = $('#order_item_discount_' + item_id);
        $price = $('#order_item_price_' + item_id);

        $qty.removeClass('read_class').removeAttr('readOnly');
        
		<?php if ($_SESSION['login_as'] == 'admin'):?>
            //$sku.removeClass('read_class').removeAttr('readOnly');
            $unit.removeClass('read_class').removeAttr('readOnly');
            $discount.removeClass('read_class').removeAttr('readOnly');
	    <?php endif; ?>
    }

    function saveThis(item_id)
    {
        $sku = $('#order_item_sku_' + item_id);
        $qty = $('#order_item_qty_' + item_id);
        $unit = $('#order_item_unit_' + item_id);
        $discount = $('#order_item_discount_' + item_id);
        $price = $('#order_item_price_' + item_id);

        $.ajax({
            url: 'viewOrderDetail.php',
            type: 'post',
            data: {action: 'save_item', order_id: '<?= $order['order_id']; ?>', sku: $sku.val(), qty: $qty.val(), item_unit: $unit.val(), discount: $discount.val(), price: $price.val(), item_id: item_id},
            dataType: 'json',
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (json) {
                if (json['error']) {
                    alert(json['error']);
                }

                if (json['success']) {
                    $qty.addClass('read_class').attr('readOnly');
                    $unit.addClass('read_class').attr('readOnly');
                    $discount.addClass('read_class').attr('readOnly');
                    alert(json['success']);
                }
            }
        });
    }

    function calculateLineTotal(item_id)
    {
        $qty = $('#order_item_qty_' + item_id);
        $unit = $('#order_item_unit_' + item_id);
        $discount = $('#order_item_discount_' + item_id);
        $price = $('#order_item_price_' + item_id);

        var sub_total = 0.00;
        var total = 0.00;
        var discount = 0.00;

        sub_total = parseInt($qty.val()) * parseFloat($unit.val());
        discount = (parseFloat(sub_total) * parseInt($discount.val())) / 100;
        total = parseFloat(sub_total) - parseFloat(discount);
        $price.val(total.toFixed(2));
    }
</script>