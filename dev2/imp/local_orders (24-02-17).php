<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
page_permission('local_orders');

//$rma_number = $db->func_escape_string($_REQUEST['rma_number']);

//$returns=$db->func_query_first("SELECT * FROM inv_returns WHERE rma_number='$rma_number'");










if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
}

if ($page < 1) {
    $page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1) * $num_rows;

if(!isset($_REQUEST['filter_date1']))
{
	$_REQUEST['filter_date1'] = date('Y-m-d');
}

if(!isset($_REQUEST['filter_date2']))
{
	$_REQUEST['filter_date2'] = date('Y-m-d');
}

$filter_order_id = (int) trim($_REQUEST['filter_order_id']);

$filter_email = $db->func_escape_string(strtolower(trim($_REQUEST['filter_email'])));
$filter_date1 = $db->func_escape_string(date('Y-m-d',strtotime($_REQUEST['filter_date1'])));
$filter_date2 = $db->func_escape_string(date('Y-m-d',strtotime($_REQUEST['filter_date2'])));



$filter_total_select = $db->func_escape_string($_REQUEST['filter_total_select']);
$filter_total_range1 = $db->func_escape_string(trim($_REQUEST['filter_total_range1']));
$filter_total_range2 = $db->func_escape_string(trim($_REQUEST['filter_total_range2']));




$where1 = array();
$where2 = array();
$where3 = array();
$where4 = array();
$where5 = array();


if ($filter_order_id) {
    $where1[] = " order_id LIKE '%$filter_order_id%' ";
	$where2[] = " order_id LIKE '%$filter_order_id%'  ";
	$where3[] = " a.order_id LIKE '%$filter_order_id%'  ";
	$where4[] = " a.order_id LIKE '%$filter_order_id%'  ";
	$where5[] = " a.order_id LIKE '%$filter_order_id%'  ";
    
}

if ($filter_email) {
    $where1[] = " LOWER(email) LIKE '%$filter_email%' ";
	$where2[] = " LOWER(email) LIKE '%$filter_email%'  ";
	$where3[] = " LOWER(email) LIKE '%$filter_email%'  ";
	$where4[] = " LOWER(a.email) LIKE '%$filter_email%'  ";
	$where5[] = " LOWER(a.email) LIKE '%$filter_email%'  ";
    
}
if ($filter_date1 && $filter_date2) {
    //$where1[] = " DATE(date_added) BETWEEN '".date('Y-m-d',strtotime($filter_date1))."' AND '".date('Y-m-d',strtotime($filter_date2))."' ";
	 $where2[] = " DATE(pos_date) BETWEEN '".date('Y-m-d',strtotime($filter_date1))."' AND '".date('Y-m-d',strtotime($filter_date2))."' ";
	  $where3[] = " DATE(pos_date) BETWEEN '".date('Y-m-d',strtotime($filter_date1))."' AND '".date('Y-m-d',strtotime($filter_date2))."' ";
	   $where4[] = " DATE(b.date_added) BETWEEN '".date('Y-m-d',strtotime($filter_date1))."' AND '".date('Y-m-d',strtotime($filter_date2))."' ";
	    $where5[] = " DATE(a.date_added) BETWEEN '".date('Y-m-d',strtotime($filter_date1))."' AND '".date('Y-m-d',strtotime($filter_date2))."' ";
    
}







if ($filter_total_range1) {
   
    if ($filter_total_select == 'BETWEEN') {
        if ($filter_total_range2) {
            $where1[] = ' round(total,2)>=' . (float) $filter_total_range1 . ' AND round(total,2)<=' . (float) $filter_total_range2 . ' ';
			$where2[] = ' round(total,2)>=' . (float) $filter_total_range1 . ' AND round(total,2)<=' . (float) $filter_total_range2 . ' ';
			$where3[] = ' round(total,2)>=' . (float) $filter_total_range1 . ' AND round(total,2)<=' . (float) $filter_total_range2 . ' ';
			$where4[] = ' round(b.amount,2)>=' . (float) $filter_total_range1 . ' AND round(b.amount,2)<=' . (float) $filter_total_range2 . ' ';
			
        }
    } else {
        $where1[] = ' round(total,2)' . $filter_total_select . '' . (float) $filter_total_range1 . ' ';
		$where2[] = ' round(total,2)' . $filter_total_select . '' . (float) $filter_total_range1 . ' ';
		$where3[] = ' round(total,2)' . $filter_total_select . '' . (float) $filter_total_range1 . ' ';
		$where4[] = ' round(b.amount,2)' . $filter_total_select . '' . (float) $filter_total_range1 . ' ';
    }
}


if ($where1) {
    $where1 = implode(" AND ", $where1);
} else {
    $where1 = "1 = 1";
}

if ($where2) {
    $where2 = implode(" AND ", $where2);
} else {
    $where2 = "1 = 1";
}

if ($where3) {
    $where3 = implode(" AND ", $where3);
} else {
    $where3 = "1 = 1";
}

if ($where4) {
    $where4 = implode(" AND ", $where4);
} else {
    $where4 = "1 = 1";
}

if ($where5) {
    $where5 = implode(" AND ", $where5);
} else {
    $where5 = "1 = 1";
}



$awaitings = $db->func_query("SELECT * FROM oc_order WHERE $where1 AND shipping_code='multiflatrate.multiflatrate_0' AND order_status_id in ('24','15') and pos_total=0.0000 ORDER BY date_added DESC");


$pickup_users = $db->func_query("SELECT order_id, user_id  FROM oc_order WHERE $where2 AND shipping_code='multiflatrate.multiflatrate_0' AND order_status_id = '3' ORDER BY pos_date DESC");

$canceled = $db->func_query("SELECT * FROM oc_order as a WHERE $where3 AND shipping_code='multiflatrate.multiflatrate_0' AND order_status_id = '7' ORDER BY pos_date DESC");

$voided = $db->func_query("SELECT a.order_id,a.email,b.reason_id,b.void_type,b.product_id,b.date_added FROM oc_order a,oc_void_product b WHERE a.order_id=b.order_id AND $where3 ORDER BY b.date_added DESC");

$o_ids = array();
foreach ($pickup_users as $x) {
    $o_ids[$x['user_id']][] = $x['order_id'];
}
$user_ids = array_keys($o_ids);


// array_values($o_ids[44]);exit;
$returns = $db->func_query("SELECT a.id,a.oc_user_id FROM inv_returns a WHERE store_type='storefront' and  $where5 ORDER BY date_added DESC");
$r_ids = array();



$qc_id=$db->func_query_first("SELECT auth_qc FROM inv_returns WHERE id='".$returns[0][id]."'");

$manager_id=$db->func_query_first("SELECT auth_manager FROM inv_returns WHERE id='".$returns[0][id]."'");

$qc_name=$db->func_query_first("SELECT name FROM inv_users WHERE id='".$qc_id[auth_qc]."'");

$manager_name=$db->func_query_first("SELECT name FROM inv_users WHERE id='".$manager_id[auth_manager]."'");






foreach($returns as $x)
{
        $r_ids[$x['oc_user_id']][] = $x['id'];
}
$return_user_ids = array_keys($r_ids);
//echo "SELECT a.* FROM inv_returns a WHERE order_id IN ('". implode("', '", $o_ids) ."') and $where5 ORDER BY date_added DESC";exit;


?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
         <link href="include/table_sorter.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <title>Store Front Orders</title>
    </head>
    <body>
        <?php include_once 'inc/header.php'; ?>

        <?php if (@$_SESSION['message']): ?>
            <div align="center"><br />
                <font color="red"><?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?><br /></font>
            </div>
        <?php endif; ?>

        <div align="center">
            <form name="order" action="" method="get">
                <table width="90%" cellpadding="10" border="1"  align="center">
                    <tr>
                        <td>Email</td>
                        <td>
                            <input type="text" style="width:100px" name="filter_email" value="<?php echo @$_REQUEST['filter_email']; ?>" />
                        </td>

                        <td >Order ID</td>
                        <td>
                            <input type="text" style="width:70px" name="filter_order_id" value="<?php echo @$_REQUEST['filter_order_id']; ?>" />
                        </td>
                        <td>Date From</td>
                        <td>
                            <input type="date" class="datepicker" style="width:140px" name="filter_date1" value="<?php echo @$_REQUEST['filter_date1']; ?>" placeholder="yyyy-mm-dd" />
                        </td>
                        
                         <td>Date To</td>
                        <td>
                            <input type="date" class="datepicker" style="width:140px" name="filter_date2" value="<?php echo @$_REQUEST['filter_date2']; ?>" placeholder="yyyy-mm-dd" />
                        </td>

                        

                        <td>Total Amount</td>
                        <td align="center">
                            <select name="filter_total_select" style="margin-bottom:5px" onChange="if ($(this).val() == 'BETWEEN') {
                                        $('input[name=filter_total_range2]').show();
                                    } else {
                                        $('input[name=filter_total_range2]').hide();
                                    }">

                                <option value=">" <?php if ($_GET['filter_total_select'] == ">") echo 'selected'; ?>>Above</option>
                                <option value="<" <?php if ($_GET['filter_total_select'] == "<") echo 'selected'; ?>>Below</option>
                                <option value="BETWEEN" <?php if ($_GET['filter_total_select'] == "BETWEEN") echo 'selected'; ?>>Between</option>
                                 <option value="=" <?php if ($_GET['filter_total_select'] == "=") echo 'selected'; ?>>Equals</option>
                            </select><br />
                            <input type="text" style="width:80px" name="filter_total_range1"  value="<?php echo @$_GET['filter_total_range1']; ?>">
                            <input type="text" style="width:80px;<?php
                            if ($_GET['filter_total_select'] != 'BETWEEN') {
                                echo 'display:none';
                            }
                            ?>" name="filter_total_range2"  value="<?php echo @$_GET['filter_total_range2']; ?>" >


                        </td>





                        <td><input type="submit" name="search" value="Search" class="button" /></td>
                    </tr>   
                </table>
            </form>
            
            <div align="left" style="float:left;margin-left:10%;width:40%">
        <table cellpadding="10" cellspacing="0" width="50%" border="0" style="background-color:#dcdcdc">
       <tr style="font-weight:bold">
        <td>Awaiting (Paid):</td>
        <td id="awaiting_paid"></td>
        </tr>
        <tr style="font-weight:bold">
        <td>Awaiting (UnPaid):</td>
        <td id="awaiting_unpaid"></td>
        </tr>
        <tr style="font-weight:bold;border-top:1px dotted black">
        <td>Cash Total:</td>
        <td id="picked_cash"></td>
        </tr>
         <tr style="font-weight:bold">
        <td>Card Total:</td>
        <td id="picked_card"></td>
        </tr>
        <tr style="font-weight:bold">
     <td>PayPal Total:</td>
        <td id="picked_paypal"></td>
        </tr>
        
         <tr style="font-weight:bold;border-top:1px dotted black;display:none" >
        <td>Canceled / Returned:</td>
        <td id="canceled_total"></td>
        </tr>
        
        </table>
        
        </div>
        
        <div align="left" style="float:left;width:40%">
        <table cellpadding="10" cellspacing="0" width="50%" border="0" style="background-color:#dcdcdc">
       
         <tr style="text-decoration:underline;font-weight:bold">
     <td colspan="2" align="center">Returns</td>
       
        </tr>
          <tr style="font-weight:bold">
     <td>Replacement:</td>
        <td id="replacement_total"></td>
        </tr>
        
         <tr style="font-weight:bold">
     <td>Store Credit:</td>
        <td id="credit_total"></td>
        </tr>
        
         <tr style="font-weight:bold">
     <td>Refund:</td>
        <td id="refund_total"></td>
        </tr>
        <tr style="font-weight:bold;border-top:1px dotted black;" >
        <td>Total:</td>
        <td id="return_total"></td>
        </tr>
       
        
        </table>
        
        </div>
        
        <br style="clear:both">
            
            
			<h1>Awaiting Pickups</h1>
            <table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">
              <thead>
                <tr style="background:#e5e5e5;">
                   <th>#</th>
                    <th>Added</th>
                    <th>Order ID</th>
                    <th>Name</th>
                    <th>Email</th> 
             
                    <th>Type</th>
					<th>Total</th>
                    
                </tr>
                </thead>
                <tbody>
               		<?php
					$awaiting_unpaid = 0.00;
					$awaiting_paid = 0.00;
					?>
                    <?php foreach ($awaitings as $i => $row): ?>
                    
                    <?php
					$status = ($row['payment_method']=='Cash or Credit at Store Pick-Up'?'Unpaid':'Paid');
					if($status=='Unpaid')
					{
						$awaiting_unpaid+=$row['total'];
					}
					else
					{
						$awaiting_paid+=$row['total'];
					}
					
					if($row['ref_order_id'])
					{
					$order_id = $row['ref_order_id'];	
					}
					else
					{
						$order_id = $row['order_id'];	
					}
					?>
                        <tr>
                            <td><?php echo $i+1;?></td>
                            <td><?php echo americanDate($row['date_added']); ?></td>
                            <td><a href="viewOrderDetail.php?order=<?php echo $order_id?>"><?php echo $order_id ?></a></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname'] ?></td>
                            <td><?php echo linkToProfile($row['email']) ?></td>
                         
                            <td><?php echo $status;?></td>

                            <td>$<?php echo number_format($row['total'],2);?></td>
                            
                        </tr>
                    <?php endforeach; ?>       
               </tbody>

                
            </table>
            <?php
            $_picked_cash = 0.00;
            $_picked_card = 0.00;
            $_picked_paypal = 0.00;
            foreach($user_ids as $user_id)
            {

                $oc_user = $db->func_query_first_cell("SELECT firstname from oc_user where user_id='".$user_id."'");
            ?>
            <br>
            <h1><?=$oc_user;?> Picked Ups</h1>
            <table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">
               <thead>
                <tr style="background:#e5e5e5;">
                   <th>#</th>
                    <th>Date</th>
                    <th>Order ID</th>
                    <th>Name</th>
                    <th>Email</th> 
                    <th>Initial Order Amount</th>
                    <th>Cash</th>
                    <th>Card</th>
                    <th>PayPal</th>
					<th>Comment</th>
                    
                </tr>'
                </thead>
                <tbody>
                    <?php
                    $order_ids =  implode(",", array_values($o_ids[$user_id]));
                    $pickups = $db->func_query("SELECT *  FROM oc_order WHERE order_id in ($order_ids) ORDER BY pos_date DESC");
                    ?>
               		<?php
					$picked_cash = 0.00;
					$picked_card = 0.00;
					$picked_paypal = 0.00;
                    $picked_old_total = 0.00;
					?>
                    <?php foreach ($pickups as $i => $row): ?>
                    <?php
					$cash = 0.00;
					$card = 0.00;
					$paypal = 0.00;
                   // echo $row['total']."";
					if(  preg_match("/^cash(.*)/i", strtolower($row['payment_method'])) > 0)
					{
						$cash = $row['total'];
					}
					
					if($row['payment_method']=='Card')
					{
						$card = $row['total'];
					}
					if(strtolower($row['payment_method'])=='paypal' or strtolower($row['payment_method'])=='paypal express')
					{
						$paypal = $row['total'];
					}
					
					if($row['payment_method']=='Cash,Card')
					{
						$cash = $row['cash_split'];
						$card = $row['card_split'];
					}

                    if($row['ref_order_id'])
                    {
                    $order_id = $row['ref_order_id'];   
                    }
                    else
                    {
                        $order_id = $row['order_id'];   
                    }
                    if ($row['change_due']) {
                        // $cash = $cash - $row['change_due'];
                        // $cash = $row['cash_paid'] - $row['change_due'];
                    }

					$picked_cash+=$cash;
					$picked_card+=$card;
					$picked_paypal+=$paypal;
                    $picked_old_total+=$row['old_total'];
					?>
                        <tr>
                            <td><?php echo $i+1;?></td>
                            <td><?php echo americanDate($row['date_added']); ?></td>
                           <td><a href="viewOrderDetail.php?order=<?php echo $order_id; ?>"><?php echo $order_id; ?></a></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname'] ?></td>
                            <td><?php echo linkToProfile($row['email']) ?></td>
                            <td>$<?php echo number_format($row['old_total'],2) ?></td>
                            
                            <td>$<?php echo number_format($cash,2);?></td>
                            <td>$<?php echo number_format($card,2);?></td>
                            <td>$<?php echo number_format($paypal,2);?></td>
<?php 
$checkForVoucher = $db->func_query('SELECT *, `a`.`amount` as `used`, `b`.`amount` as `remain` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND a.`order_id` = "'. $row['order_id'] .'"'); 
      //$voucher_comment = json_encode($voucher);
      $voucher_comment = '';
if($checkForVoucher)
{
    foreach ($checkForVoucher as $key => $voucher) {

        $totalUsed = $db->func_query_first_cell('SELECT SUM(`amount`) from oc_voucher_history where voucher_id = "'. $voucher['voucher_id'] .'"');
        $remain = ($voucher['remain'] - str_replace('-', '', $totalUsed))? $voucher['remain'] - str_replace('-', '', $totalUsed): ''; 
       $voucher_comment .= '<a href="'.$host_path.'vouchers_create.php?edit='.$voucher['voucher_id'].'">'.$voucher['code'].'</a> applied of '.$voucher['used'].' (Bal: $'.number_format($remain,2).' )<br>';
        //$voucher_comment .= json_encode($voucher);
    }
	    
    $row['comment'] = (strlen($row['comment'])>0?$row['comment']."<br>".$voucher_comment:$voucher_comment); 
	//$row['comment'] = json_encode($checkForVoucher);
}
else
{
    $check_transaction = $db->func_query_first("SELECT transaction_id,amount FROM inv_transactions WHERE order_id='".$row['order_id']."'");
    
    if($check_transaction)
    {
        
        $transaction_comment = 'Transaction # '.$check_transaction['transaction_id'].' / Amount Paid: $'.number_format($check_transaction['amount'],2);
$row['comment'] = (strlen($row['comment'])>0?$row['comment']."<br>".$transaction_comment:$transaction_comment); 

    }
}

?>
                            <td><?=$row['comment'];?></td>
                            
                        </tr>
                    <?php endforeach; ?>       
               
</tbody>
<tr>
<td colspan="5"><strong>Total:</strong></td>
<td><strong>$<?php echo number_format($picked_old_total,2);?></strong></td>
<td><strong>$<?php echo number_format($picked_cash,2);?></strong></td>
<td><strong>$<?php echo number_format($picked_card,2);?></strong></td>
<td><strong>$<?php echo number_format($picked_paypal,2);?></strong></td>
<td><strong></strong></td>
</tr>
                
            </table>
            <?php
            $_picked_cash+=$picked_cash;
            $_picked_card+=$picked_card;
            $_picked_paypal+=$picked_paypal;
        }
        ?>
            <br>
            <h1>Canceled</h1>
            <table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">
                <thead>
                <tr style="background:#e5e5e5;">
                   <th>#</th>
                    <th>Date</th>
                    <th>Order ID</th>
                    <th>Name</th>
                    <th>Email</th> 
                  
                    <th>Type</th>
                    <th>Total</th>
                   
					<th>Comment</th>
                    
                </tr>
                </thead>
                <tbody>
               	<?php
				$canceled_total = 0.00;
				?>
                    <?php foreach ($canceled as $i => $row): ?>
                    <?php
					$type = ($row['order_status_id']==7?'Canceled':'Returned');
					$canceled_total +=$row['total'];

if($row['ref_order_id'])
                    {
                    $order_id = $row['ref_order_id'];   
                    }
                    else
                    {
                        $order_id = $row['order_id'];   
                    }

					?>
                        <tr>
                            <td><?php echo $i+1;?></td>
                            <td><?php echo americanDate($row['date_added']); ?></td>
                            <td><a href="viewOrderDetail.php?order=<?php echo $order_id; ?>"><?php echo $order_id; ?></a></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname'] ?></td>
                            <td><?php echo linkToProfile($row['email']) ?></td>
                           
                            <td><?php echo $type;?></td>
                            <td>$<?php echo number_format($row['total'],2);?></td>
                           

                            <td><?=$row['comment'];?></td>
                            
                        </tr>
                    <?php endforeach; ?>       
               </tbody>

                
            </table>
            
            <?php
            $return_total = 0.00;
                $replacement_total = 0.00;
                $refund_total = 0.00;
                $credit_total = 0.00;
            foreach($return_user_ids as $return_user_id)
            {
                $oc_user = $db->func_query_first_cell("SELECT firstname from oc_user where user_id='".$return_user_id."'");
            ?>
            <br>
             <h1><?=$oc_user;?> Returns</h1>
            <table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">
                <thead>
                <tr style="background:#e5e5e5;">
                   <th>#</th>
                    <th>Added</th>
                    <th>Order ID</th>
                    
                    <th>Email</th>
                    <th>Voucher</th>
                    <th>Cash</th>
                    <th>Card</th>
                    <th>Paypal</th>
                    <th class="{sorter: false}">Details </th> 
                  
                   
                    
                </tr>
                </thead>
                <tbody>
               	<?php
				

                $return_total_voucher = 0.00;
                $return_total_cash = 0.00;
                $return_total_card = 0.00;
                $return_total_paypal = 0.00;
				$return_ids =  implode(",", array_values($r_ids[$return_user_id]));
                $returns = $db->func_query("SELECT * FROM inv_returns WHERE id in ($return_ids)");
                ?>

                    <?php foreach ($returns as $i => $row): ?>
                    <?php
				
					
					$return_items = $db->func_query("SELECT * FROM inv_return_items WHERE return_id='".$row['id']."'");
                    $_voucher_refund = 0.00;
                    $_cash_refund = 0.00;
                    $_card_refund = 0.00;
                    $_paypal_refund = 0.00;
					if($row['order_id'])
					{
                    $_voucher_refund = $db->func_query_first_cell("SELECT amount FROM oc_voucher WHERE code LIKE '%".$row['order_id']."%'");
                    $_cash_refund = $db->func_query_first_cell("SELECT total FROM oc_order WHERE ref_order_id LIKE '%".$row['order_id']."-%' and order_status_id=11 and payment_method='Cash'");
                    $_state=$db->func_query_first_cell("SELECT payment_zone FROM oc_order WHERE ref_order_id LIKE '%".$row['order_id']."-%' and order_status_id=11 and payment_method='Cash'");
                    //print_r($_state);exit;

                    $_card_refund = $db->func_query_first_cell("SELECT a.price FROM inv_return_decision a,oc_order b WHERE a.order_id = b.order_id  and a.action='Issue Refund' and a.order_id LIKE '%".$row['order_id']."%'  and b.payment_method LIKE '%Card%'");
                    $_paypal_refund = $db->func_query_first_cell("SELECT a.price FROM inv_return_decision a,oc_order b WHERE a.order_id = b.order_id and a.action='Issue Refund' and a.order_id LIKE '%".$row['order_id']."%'  and lower(b.payment_method) LIKE '%paypal%'");
                    $decision_check = $db->func_query_first_cell("SELECT action FROM inv_return_decision WHERE order_id='".$row['order_id']."' AND return_id='".$row['id']."' AND sku='".$return_items[0]['sku']."'");
                }
                    $_voucher_refund = (float)$_voucher_refund;
                    $_cash_refund = (float)$_cash_refund*(-1);
                   
                    if($_state=='Nevada'){
                    $_cash_refund = (float)$_cash_refund+($_cash_refund*(8.15/100));
                    }
                    $_card_refund = (float)$_card_refund;
                    $_paypal_refund = (float)$_paypal_refund;
                    if($decision_check=='Issue Replacement'){   
                        $_voucher_refund = 0;
                        $_cash_refund = 0;
                        $_card_refund = (float)$_card_refund;
                        $_paypal_refund = (float)$_paypal_refund;
                    }
                    else if ($decision_check=='Issue Credit'){
                        if($_state=='Nevada'){
                        $_voucher_refund = (float)$return_items[0]['price']+($return_items[0]['price']*(8.1/100));
                        }else{
                        $_voucher_refund = (float)$return_items[0]['price'];
                        }
                        $_cash_refund = 0;
                        $_card_refund = (float)$_card_refund;
                        $_paypal_refund = (float)$_paypal_refund;

                    } else if ($decision_check=='Issue Refund') {
                        $_voucher_refund = 0;
                        $_cash_refund =  (float)$return_items[0]['price']+($return_items[0]['price']*(8.1/100));
                        $_card_refund = (float)$_card_refund;
                        $_paypal_refund = (float)$_paypal_refund;       
                    }

                    $refund_total+=+$_card_refund+$_cash_refund+$_paypal_refund;

                    $return_total_voucher+=$_voucher_refund;
                    $return_total_cash+=$_cash_refund;
                    $return_total_card+=$_card_refund;
                    $return_total_paypal+=$_paypal_refund;
					?>
                        <tr>
                            <td><?php echo $i+1;?></td>
                            <td><?php echo americanDate($row['date_added']); ?></td>
                            <td><a href="viewOrderDetail.php?order=<?php echo $row['order_id']?>"><?php echo $row['order_id'] ?></a></td>
                            
                            <td><?php echo linkToProfile($row['email']) ?></td>
                             <td>$<?=number_format($_voucher_refund,2);?></td>

                              <td>$<?=number_format($_cash_refund,2);?></td>
                           
                              <td>$<?=number_format($_card_refund,2);?></td>
                           
                              <td>$<?=number_format($_paypal_refund,2);?></td>
                           
                           <td align="left">
                         <strong>  <a href="<?php echo $host_path;?>return_detail.php?rma_number=<?php echo $row['rma_number'];?>"><?php echo $row['rma_number'];?></a> ( <?php echo $row['rma_status'];?>)</strong><br>
                           
                           <?php

						   foreach($return_items as $return_item)
						   {
							   $return_total+=$return_item['price'];
							   
							   $decisions = $db->func_query("SELECT SUM(price) as price,action FROM inv_return_decision WHERE order_id='".$row['order_id']."' AND return_id='".$row['id']."' AND sku='".$return_item['sku']."' GROUP BY action");
							?>
                            <strong>SKU:</strong> <?=linkToProduct($return_item['sku'],$host_path);?><br>
                             <strong>Price:</strong> $<?=number_format($return_item['price']+($return_item['price']*(0.081)),2);?><br>
                             <strong>Code:</strong> <?=$return_item['return_code'];?><br>
                             <strong>Condition:</strong> <?=$return_item['item_condition'];?><br>
                             <?php
							 foreach($decisions as $decision)
							 {
								
							 ?>
                            <strong>Decision:</strong> <?=$decision['action'];?> 
                             <?php
							 if($decision['action']=='Issue Replacement')
							 {
								 //$replacement_total+=$decision['price'];
								 $replacements = array();

								 if($row['order_id'])
								 	{
								 		$replacements = $db->func_query("SELECT order_id,ref_order_id FROM oc_order WHERE ref_order_id LIKE '%".$row['order_id']."-%' and order_status_id<>11");
								 	}
								 
								 $jj = 0;
								// echo '(';
								 $_arr = array();
								 foreach($replacements as $replacement)
								 {
									 if($jj>0)
									 {
										//echo ', '; 
									 }
										$_arr[] =  '<a href="'.$host_path.'viewOrderDetail.php?order='.$replacement['ref_order_id'].'">'.$replacement['ref_order_id'].'</a>';
										
										$replacement_items = $db->func_query("SELECT * FROM oc_order_product WHERE order_id='".$replacement['order_id']."' ");
										foreach($replacement_items as $replacement_item)
										{
											//$replacement_total+=$replacement_item['price'];
										}
										
										$jj++;
								 }
								 
								 
							 }
							 elseif($decision['action']=='Issue Credit')
							 {
                                $credit_total+=$_voucher_refund;
								$credits = array();
								 if($row['order_id'])
								 {
								 $credits = $db->func_query("SELECT voucher_id,code,amount FROM oc_voucher WHERE code LIKE '%".$row['order_id']."%'");
								}
								 $jj = 0;
								  //echo '(';
								 foreach($credits as $credit)
								 {
									 if($jj>0)
									 {
									//	echo ', '; 
									 }
										$_arr[] =  linkToVoucher($credit['voucher_id'], $host_path, $credit['code']);
										 //$credit_total+=$_voucher_refund;
										$jj++;
								 } 
								   //echo ')';
								 
								 
								 /*	 $refunds_invoices = $db->func_query("SELECT order_id,ref_order_id,total FROM oc_order WHERE ref_order_id LIKE '%".$row['order_id']."-%' and order_status_id=11");
									 foreach($refunds_invoices as $refund_invoice)
									 {
										 $refund_total+=($refund_invoice['total']*(-1));
										 
									 }*/
									 if($_arr)
									 {
										//echo '('.implode(",",$_arr).')'; 
									 }
							 }
							 elseif($decision['action']=='Issue Refund')
							 {
								 //$refund_total+=$decision['price'];
							 }
							 
							 ?>
                             <br>
                            
                             <?php
							 }
							 ?>
                             <hr>
                            <?php   
						   }
						   ?>
                           Manager: <?=$manager_name['name'];?>
                           QC Lead: <?=$qc_name['name'];?>
                           </td>
                            
                            
                        </tr>
                    <?php endforeach; ?>       
               </tbody>
<tr>
<td colspan="4" align="right"><strong>Total:</strong></td>
<td><strong>$<?php echo number_format($return_total_voucher,2);?></strong></td>
<td><strong>$<?php echo number_format($return_total_cash,2);?></strong></td>
<td><strong>$<?php echo number_format($return_total_card,2);?></strong></td>
<td><strong>$<?php echo number_format($return_total_paypal,2);?></strong></td>
<td><strong></strong></td>
</tr>
                
            </table>
            <?php
        }
        ?>
            <br>
            
            <h1>Voided Items</h1>
            <table border="1" cellpadding="5" cellspacing="0" width="80%" class="tablesorter">
                <thead>
                <tr style="background:#e5e5e5;">
                   <th>#</th>
                    <th>Date</th>
                    <th>Order ID</th>
                    
                    <th>Email</th> 
                  
                    <th>SKU</th>
                    <th>Reason</th>
                   
					<th>Void Type</th>
                    <th>Amount</th>
                    
                </tr>
                </thead>
                <tbody>
               	<?php
				$voided_total = 0.00;
				?>
                    <?php foreach ($voided as $i => $row): ?>
                    <?php
					
					$voided_total +=$row['amount'];
					?>
                        <tr>
                            <td><?php echo $i+1;?></td>
                            <td><?php echo americanDate($row['date_added']); ?></td>
                            <td><a href="viewOrderDetail.php?order=<?php echo $row['order_id']?>"><?php echo $row['order_id'] ?></a></td>
                           
                            <td><?php echo linkToProfile($row['email']) ?></td>
                           
                            <td><?php echo $db->func_query_first_cell("SELECT sku FROM oc_product WHERE product_id='".$row['product_id']."'");?> * 1</td>
                            <td><?php echo $db->func_query_first_cell("SELECT name FROM oc_return_reason WHERE return_reason_id='".$row['reason_id']."'");?></td>
                            <td><?php echo $row['void_type'];?></td>
                           

                            <td>$<?=number_format($row['amount'],2);?></td>
                            
                        </tr>
                    <?php endforeach; ?>       
               </tbody>

                
            </table>
            <br><br>
        </div>
       
       
       
       
        
        
        
        
    </body>
</html>
<script>
$(document).ready(function(e) {
    $('#awaiting_paid').html('<?='$'.number_format($awaiting_paid,2);?>');
	$('#awaiting_unpaid').html('<?='$'.number_format($awaiting_unpaid,2);?>');
	
	$('#picked_cash').html('<?='$'.number_format($_picked_cash,2);?>');
	$('#picked_card').html('<?='$'.number_format($_picked_card,2);?>');
	$('#picked_paypal').html('<?='$'.number_format($_picked_paypal,2);?>');
	
	
	$("#replacement_total").html('<?='$'.number_format($replacement_total,2);?>');
	$("#refund_total").html('<?='$'.number_format($refund_total,2);?>');
	$("#credit_total").html('<?='$'.number_format($credit_total,2);?>');
	 $('#canceled_total').html('<?='$'.number_format($canceled_total,2);?>');
	 
	  $('#return_total').html('<?='$'.number_format($replacement_total+$refund_total+$credit_total,2);?>');
	 
	 
	
	
	
});

</script>
 <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
         <script>
		 $(document).ready(function(e) {
			$(".tablesorter").tablesorter();
           
			 
			 
			  
        });
		 </script>