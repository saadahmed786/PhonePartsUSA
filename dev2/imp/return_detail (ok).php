<?php
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");
$rma_number = $db->func_escape_string($_REQUEST['rma_number']);
if (!$rma_number) {
    header("Location:$host_path/manage_returns.php");
    exit;
}

//upload return item item images
if ($_FILES['image_path']['tmp_name']) {
    foreach ($_POST['item_condition'] as $return_item_id => $condition) {
        if ($condition == 'Item Issue' && $_POST['item_issue'][$return_item_id]) {
            //check exist
            $isExist = $db->func_query_first("select id from  inv_product_issues where product_sku = '" . $_POST['product_sku'][$return_item_id] . "' and item_id = '$return_item_id' and issue_from = 'returns'");
            if (!$isExist) {
                $itemIssue = array();
                $itemIssue['username'] = $_SESSION['login_as'];
                $itemIssue['product_sku'] = $_POST['product_sku'][$return_item_id];
                $itemIssue['item_issue'] = $_POST['item_issue'][$return_item_id];
                $itemIssue['issue_from'] = 'returns';
                $itemIssue['shipment_id'] = $_POST['return_id'];
                $itemIssue['item_id'] = $return_item_id;
                $itemIssue['date_added'] = date('Y-m-d H:i:s');

                $product_issue_id = $db->func_array2insert("inv_product_issues", $itemIssue);
            } else {
                $product_issue_id = $isExist['id'];
                //$db->db_exec("");
                $db->db_exec("update inv_product_issues SET item_issue = '" . $_POST['item_issue'][$return_item_id] . "' where product_sku = '" . $_POST['product_sku'][$return_item_id] . "' and item_id = '$return_item_id' and issue_from = 'returns'");
            }
        }
    }

    $imageCount = 0;
    $isDenied = false;
    foreach ($_FILES['image_path']['tmp_name'] as $return_item_id => $files) {
        $count = count($files);

        for ($i = 0; $i < $count; $i++) {
            $uniqid = uniqid();
            $destination = "images/returns/" . $uniqid . ".jpg";
            $destination_thumb = "images/returns/" . $uniqid . "_thumb.jpg";

            if (move_uploaded_file($files[$i], $destination)) {
                resizeImage($destination, $destination_thumb, 50, 50);

                if ($_POST['decision'][$return_item_id] != 'Denied') {
                    resizeImage($destination, $destination, 500, 500);
                    $isDenied = false;
                }

                if ($_POST['decision'][$return_item_id] == 'Denied') {
                    $isDenied = true;
                }

                $itemImage = array();
                $itemImage['image_path'] = $destination;
                $itemImage['thumb_path'] = $destination_thumb;
                $itemImage['date_added'] = date('Y-m-d H:i:s');
                $itemImage['user_id'] = $_SESSION['user_id'];
                $itemImage['return_item_id'] = $return_item_id;

                $image_id = $db->func_array2insert("inv_return_item_images", $itemImage);
                $imageCount++;

                if ($_POST['item_condition'][$return_item_id] == 'Item Issue') {
                    //insert into product issue images
                    $itemImage = array();
                    $itemImage['image_path'] = $destination;
                    $itemImage['thumb_path'] = $destination_thumb;
                    $itemImage['image_id'] = $image_id;
                    $itemImage['product_issue_id'] = $product_issue_id;

                    $db->func_array2insert("inv_product_issue_images", $itemImage);
                }
            }
        }
    }

    if ($imageCount > 0) {
        $rma_return = $db->func_query_first("select r.* ,o.email, o.order_date, od.first_name,od.last_name,od.address1,od.address2,od.payment_method,
          od.city,od.state,od.zip, o.store_type,o.order_status
          from inv_returns r 
          inner join inv_orders o on (r.order_id = o.order_id) 
          inner join inv_orders_details od on (r.order_id = od.order_id)
          where rma_number  = '$rma_number'");

        $images = $db->func_query("SELECT c.* FROM  inv_returns a INNER JOIN `inv_return_items` b ON (a.`id` = b.`return_id`)
         INNER JOIN `inv_return_item_images` c
         ON (b.`id` = c.`return_item_id`) WHERE a.id='" . $rma_return['id'] . "'");
        header("Location:return_detail.php?rma_number=$rma_number");
        exit;
    }
}

//delete item images
if ($_GET['action'] == 'remove' && $_GET['image_id']) {
    $return_item_id = (int) $_GET['return_item_id'];
    $db->db_exec("delete from inv_return_item_images where return_item_id = '$return_item_id' and id = '" . (int) $_GET['image_id'] . "'");
    $db->db_exec("delete from inv_product_issue_images where image_id = '" . (int) $_GET['image_id'] . "'");

    header("Location:return_detail.php?rma_number=$rma_number");
    exit;
}

//add comments
if (isset($_POST['addcomment'])) {
    $addcomment = array();
    $addcomment['comment_date'] = date('Y-m-d H:i:s');
    $addcomment['user_id'] = $_SESSION['user_id'];
    $addcomment['comments'] = $db->func_escape_string($_POST['comments']);
    $addcomment['return_id'] = $_POST['return_id'];

    $db->func_array2insert("inv_return_comments", $addcomment);

    $_SESSION['message'] = "New comment is added.";
    header("Location:$host_path/return_detail.php?rma_number=$rma_number");
    exit;
}

if (isset($_POST['save']) || isset($_POST['completed']) || isset($_POST['qcdone'])) {
    //update sku if changed

    foreach ($_POST['product_sku'] as $return_item_id => $product_sku) {
        $return_items = array();

        if ($product_sku != $_POST['new_sku'][$return_item_id]) {
            $return_items['sku'] = $_POST['new_sku'][$return_item_id];
            $return_items['title'] = $db->func_query_first_cell("select name from oc_product_description where product_id = (select product_id from oc_product where sku = '" . $_POST['new_sku'][$return_item_id] . "' limit 1)");
        }
        if($_POST['restocking'][$return_item_id])
        {
           $return_items['restocking'] = 1;
           $return_items['restocking_grade'] = $_POST['restocking_grade'][$return_item_id];
           $return_items['discount_amount'] = (float)$_POST['discount_amount'][$return_item_id];
           $return_items['discount_per'] = (int)$_POST['discount_per'][$return_item_id];

           $db->db_exec("INSERT INTO inv_return_comments SET comment_date='".date('Y-m-d H:i:s')."',user_id='".$_SESSION['user_id']."',comments='".$product_sku." - ($".number_format($return_items['discount_amount'],2).") ".$return_items['discount_per']."% restocking fee assessed',return_id='".$_POST['return_id']."',sku='".$product_sku."'");		
       }
       else
       {
           $return_items['restocking'] = 0;
           $return_items['restocking_grade'] = '';	
           $return_items['discount_amount'] = 0.00;
           $return_items['discount_per'] = 0;
       }


        //$return_items['returnable'] = $_POST['returnable'][$return_item_id];
       $return_items['item_condition'] = $_POST['item_condition'][$return_item_id];
       $return_items['how_to_process'] = $_POST['how_to_process'][$return_item_id];
       $return_items['item_issue'] = $_POST['item_issue'][$return_item_id];
       $return_items['price'] = $_POST['product_price'][$return_item_id];

       if ($_SESSION['return_decision'] and $_POST['decision_save'] == 1) {
        $return_items['decision'] = $_POST['decision'][$return_item_id];
    }

    $return_id = $_POST['return_id'];

    $db->func_array2update("inv_return_items", $return_items, "id = '$return_item_id' AND return_id = '$return_id'");
}


$source = $db->func_escape_string($_POST['source']);

if (isset($_POST['qcdone'])) {
    $db->db_exec("update inv_returns SET rma_status = 'In QC' , source = '$source' , date_qc = '" . date('Y-m-d H:i:s') . "' where rma_number = '$rma_number'");

    $db->db_exec("INSERT INTO inv_return_history SET user_id='" . $_SESSION['user_id'] . "',return_status='In QC',date_added='" . date('Y-m-d h:i:s') . "',rma_number='" . $rma_number . "'");

    $_SESSION['message'] = "Rma verified from QC";
} elseif (isset($_POST['completed'])) {
    $db->db_exec("update inv_returns SET rma_status = 'Completed' , source = '$source' , date_completed = '" . date('Y-m-d H:i:s') . "' where rma_number = '$rma_number'");

    $db->db_exec("INSERT INTO inv_return_history SET user_id='" . $_SESSION['user_id'] . "',return_status='Completed',date_added='" . date('Y-m-d h:i:s') . "',rma_number='" . $rma_number . "'");

    $_SESSION['message'] = "Rma status is completed.";
} else {
    $db->db_exec("update inv_returns SET source = '$source' where rma_number = '$rma_number'");
    $_SESSION['message'] = "Rma changes are saved.";
}

header("Location:$host_path/return_detail.php?rma_number=$rma_number");
exit;
}

$rma_return = $db->func_query_first("select r.* ,o.email, o.order_date, od.first_name,od.last_name,od.address1,od.address2,od.payment_method,
  od.city,od.state,od.zip from inv_returns r 
  left join inv_orders o on (r.order_id = o.order_id) 
  left join inv_orders_details od on (r.order_id = od.order_id)
  where rma_number  = '$rma_number'");
if (!$rma_return) {
    header("Location:$host_path/manage_returns.php");
    exit;
}

if ($_POST['deniedReturnOrder']) {
    if (count($_POST['return_item']) > 0) {
        $customer_return_order = array();
        $customer_return_order['order_number'] = $rma_return['rma_number'] . "-" . $rma_return['order_id'];
        $customer_return_order['order_id'] = $rma_return['order_id'];
        $customer_return_order['rma_number'] = $rma_return['rma_number'];
        $customer_return_order['date_added'] = date('Y-m-d H:i:s');
        $customer_return_order['order_status'] = 'Created';
        $customer_return_order['user_id'] = $_SESSION['user_id'];
        $customer_return_order['email'] = $rma_return['email'];
        $customer_return_order['first_name'] = $rma_return['first_name'];
        $customer_return_order['last_name'] = $rma_return['last_name'];
        $customer_return_order['phone_number'] = $rma_return['order_id'];
        $customer_return_order['address1'] = $rma_return['address1'];
        $customer_return_order['city'] = $rma_return['city'];
        $customer_return_order['state'] = $rma_return['state'];
        $customer_return_order['zip'] = $rma_return['zip'];
        $customer_return_order['country'] = 'US';
        $customer_return_order['date_modified'] = date('Y-m-d H:i:s');

        $customer_return_order_id = $db->func_array2insert("inv_customer_return_orders", $customer_return_order);

        foreach ($_POST['product_sku'] as $return_item_id => $product_sku) {
            $customer_return_order_item = array();
            $customer_return_order_item['customer_return_order_id'] = $customer_return_order_id;
            $customer_return_order_item['order_item_id'] = $rma_return['rma_number'] . "-" . $product_sku . "-" . $return_item_id;
            $customer_return_order_item['product_sku'] = $product_sku;
            $customer_return_order_item['product_qty'] = 1;
            $customer_return_order_item['product_price'] = $_POST['product_price'][$order_item_id];

            $db->func_array2insert(" inv_customer_return_order_items", $customer_return_order_item);
        }

        $db->db_exec("update inv_returns SET denied_order_created = 1 where rma_number = '$rma_number'");

        $_SESSION['message'] = "Customer Denied order created successfully.";
    } else {
        $_SESSION['message'] = "Please select at least 1 item to create order.";
    }

    header("Location:return_detail.php?rma_number=$rma_number");
    exit;
}

$return_items = $db->func_query("select * from inv_return_items where return_id = '" . $rma_return['id'] . "' and removed = 0");
$removed_items = $db->func_query("select * from inv_return_items where return_id = '" . $rma_return['id'] . "' and removed = 1");
$comments = $db->func_query("select * from inv_return_comments c left join inv_users u on (c.user_id = u.id) where return_id = '" . $rma_return['id'] . "'");

$productPrice = 0;
$productNames = '<table><tbody>';
$productDetails = '<table width="100%">';
$productDetails .= '<thead><tr>';
$productDetails .= '<th width="35%">Name</th>';
$productDetails .= '<th width="10%">Return Reason</th>';
$productDetails .= '<th width="10%">Condition</th>';
$productDetails .= '<th width="10%">Decision</th>';
$productDetails .= '<th width="10%">Amount</th>';
$productDetails .= '<th width="35%">Images</th>';
$productDetails .= '</tr></thead><tbody>';
foreach ($return_items as $return_item) {
    $price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
    $productPrice += (float) $price;
    $productNames .= '<tr><td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td></tr>';
    $productDetails .= '<tr>';
    $productDetails .= '<td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td>';
    $productDetails .= '<td>'. $return_item['return_code'] . '</td>';
    $productDetails .= '<td>'. $return_item['item_condition'] . ' - ' . $return_item['item_issue'] .'</td>';
    $productDetails .= '<td>'. $return_item['decision'] .'</td>';
    $productDetails .= '<td>'. $price .'</td>';
    $images = $db->func_query("select * from inv_return_item_images where return_item_id = '" . $return_item['id'] . "'");
    $productDetails .= '<td>';
    if ($images) {
        $productDetails .= '<table> <tr>';
        foreach ($images as $image) {
            $productDetails .= '<td><a href="' . $host_path . str_ireplace("../", "", $image['image_path']) . '">';
            $productDetails .= '<img src="' . $host_path . str_ireplace("../", "", $image['thumb_path']) . '" width="25" height="25" />';
            $productDetails .= '</a></td>';
        }
        $productDetails .= '</tr></table>';
    }
    
    $productDetails .= '</td></tr>';

}
$productDetails .= '</tbody></table>';
$productNames .= '</tbody></table>';

$emailInfo = array(
    'customer_name' => $rma_return['first_name'] . $rma_return['last_name'],
    'email' => $rma_return['email'],
    'rma_number' => $rma_number,
    'order_id' => $rma_return['order_id'],
    'shipping_firstname' => $rma_return['first_name'],
    'shipping_lastname' => $rma_return['last_name'],
    'shipping_address_1' => $rma_return['address1'],
    'shipping_address_2' => $rma_return['address2'],
    'shipping_city' => $rma_return['city'],
    'shipping_zone' => $rma_return['state'],
    'shipping_postcode' => $rma_return['zip'],
    'rma_status' => $rma_return['rma_status'],
    'order_date' => americanDate($rma_return['order_date']),
    'rma_recived' => americanDate($rma_return['date_completed']),
    'rma_qc' => americanDate($rma_return['date_qc'])
    // 'rma_products_names' => $productNames,
    // 'rma_products_Details' => $productDetails,
    // 'total_price' => $productPrice
    );

$_SESSION['rma_info'] = $emailInfo;

$adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );


if (isset($_POST['sendemail'])) {
    if ($_POST['selected_products'] != '') {
        $slProduct = explode(',', $_POST['selected_products']);

        $productPrice = 0;
        $productNames = '<table><tbody>';
        $productDetails = '<table width="100%">';
        $productDetails .= '<thead><tr>';
        $productDetails .= '<th width="35%">Name</th>';
        $productDetails .= '<th width="10%">Return Reason</th>';
        $productDetails .= '<th width="10%">Condition</th>';
        $productDetails .= '<th width="10%">Decision</th>';
        $productDetails .= '<th width="10%">Amount</th>';
        $productDetails .= '<th width="35%">Images</th>';
        $productDetails .= '</tr></thead><tbody>';
        foreach($slProduct as $val) {
            foreach ($return_items as $return_item) {
                if ($return_item['id'] == $val) {
                    $price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
                    $productPrice += (float) $price;
                    $productNames .= '<tr><td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td></tr>';
                    $productDetails .= '<tr>';
                    $productDetails .= '<td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td>';
                    $productDetails .= '<td>'. $return_item['return_code'] . '</td>';
                    $productDetails .= '<td>'. $return_item['item_condition'] . ' - ' . $return_item['item_issue'] .'</td>';
                    $productDetails .= '<td>'. $return_item['decision'] .'</td>';
                    $productDetails .= '<td>'. $price .'</td>';
                    $images = $db->func_query("select * from inv_return_item_images where return_item_id = '" . $return_item['id'] . "'");
                    $productDetails .= '<td>';
                    if ($images) {
                        $productDetails .= '<table> <tr>';
                        foreach ($images as $image) {
                            $productDetails .= '<td><a href="' . $host_path . str_ireplace("../", "", $image['image_path']) . '">';
                            $productDetails .= '<img src="' . $host_path . str_ireplace("../", "", $image['thumb_path']) . '" width="25" height="25" />';
                            $productDetails .= '</a></td>';
                        }
                        $productDetails .= '</tr></table>';
                    }

                    $productDetails .= '</td></tr>';
                }
            }
        }
        $productDetails .= '</tbody></table>';
        $productNames .= '</tbody></table>';

        $emailInfo['rma_products_names'] = $productNames;
        $emailInfo['rma_products_Details'] = $productDetails;
        $emailInfo['total_price'] = $productPrice;
    }

    $email = array();

    $src = $path .'files/canned_' . $_POST['canned_id'] . ".png";

    if (file_exists($src)) {
        $email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
    }

    $email['title'] = $_POST['title'];
    $email['subject'] = $_POST['subject'];
    $email['message'] = shortCodeReplace($emailInfo, $_POST['comment']);
    
    sendEmailDetails($emailInfo, $email);

    header("Location:$host_path/return_detail.php?rma_number=$rma_number");
    exit;
}

//echo '<pre>'; print_r($emailInfo['rma_products_Details']); exit;

$item_conditions = array(array('id' => 'Good For Stock', 'value' => 'Good For Stock'),
    array('id' => 'Item Issue', 'value' => 'Item Issue'),
    array('id' => 'Customer Damage', 'value' => 'Customer Damage'),
    array('id' => 'Not Tested', 'value' => 'Not Tested'),
    array('id' => 'Not PPUSA Part', 'value' => 'Not PPUSA Part'),
    array('id' => 'Over 60 days', 'value' => 'Over 60 days')
    );

$decisionsx = array(array('id' => 'Issue Credit', 'value' => 'Issue Credit'),
    array('id' => 'Issue Refund', 'value' => 'Issue Refund'),
    array('id' => 'Issue Replacement', 'value' => 'Issue Replacement')
    );

$item_issues = $db->func_query("select * from inv_reasonlist");

if (isset($_POST['received'])) {
    $db->db_exec("update inv_returns SET rma_status = 'Received' , date_completed = '" . date('Y-m-d H:i:s') . "' where rma_number = '$rma_number'");

    $db->db_exec("INSERT INTO inv_return_history SET user_id='" . $_SESSION['user_id'] . "',return_status='Received',date_added='" . date('Y-m-d h:i:s') . "',rma_number='" . $rma_number . "'");

    $templete = $db->func_query_first('SELECT * FROM inv_canned_message WHERE `catagory` = "2"  AND `type` = "Recived"');
    $email = array();
    if ($templete) {
        $src = $path .'files/canned_' . $templete['canned_message_id'] . ".png";
        if (file_exists($src)) {
            $email['image'] = $host_path .'files/canned_' . $templete['canned_message_id'] . ".png?" . time();
        }

        $email['title'] = shortCodeReplace($emailInfo, $templete['title']);
        $email['subject'] = shortCodeReplace($emailInfo, $templete['subject']);
        $email['message'] = shortCodeReplace($emailInfo, $templete['message']);
    }

    sendEmailDetails($emailInfo, $email);
    header("Location:$host_path/return_detail.php?rma_number=$rma_number");
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Return Inventory Page</title>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

    <script type="text/javascript">
        $(document).ready(function () {
            $('.fancybox').fancybox({width: '450px', autoCenter: true, autoSize: true});
            $('.fancybox2').fancybox({width: '680px', autoCenter: true, autoSize: true});
            $('.fancybox3').fancybox({width: '980px', autoCenter: true, autoSize: true});
        });
    </script>	
    <script type="text/javascript">
        function unlockBox(condition, order_product_id) {
            if (condition == 'Item Issue') {
                jQuery("#item_issue_" + order_product_id).show();
            }
            else {
                jQuery("#item_issue_" + order_product_id).hide();
            }
        }
        function updateStockingPrice(return_item_id,obj)
        {
          var grade = $(obj).val();
          var discount = $(obj).attr('data-discount');
          var price = $('#product_price_'+return_item_id).attr('data-price');	
          var discount_amount = (parseFloat(price)*parseInt(discount)) / 100;

          var return_amount = parseFloat(price) - parseFloat(discount_amount);
          $('#product_price_'+return_item_id).val(return_amount.toFixed(2));
          $('#discount_amount_'+return_item_id).val(discount_amount.toFixed(2));
          $('#discount_per_'+return_item_id).val(discount);
      }
      function populateRestocking(return_item_id,obj)
      {


          if($(obj).is(':checked')){
           $('#div_'+return_item_id).show(500);
           $('#div_'+return_item_id+' input[type=radio]:eq(0)').click();
           updateStockingPrice(return_item_id,$('#div_'+return_item_id+' input[type=radio]:eq(0)'));
       }else{
        $('#div_'+return_item_id).hide(500);
        var price = parseFloat($('#product_price_'+return_item_id).attr('data-price'));	
        $('#product_price_'+return_item_id).val(price.toFixed(2));

        $('#discount_amount_'+return_item_id).val(0.00);
        $('#discount_per_'+return_item_id).val(0);
    }	

}
</script>
</head>
<body>
    <div class="div-fixed">
        <div align="center"> 
            <?php include_once 'inc/header.php'; ?>
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
                    <h2>RMA Return Details</h2>

                    <?php if ($rma_return['rma_status'] == 'Awaiting'): ?>
                        <a href="<?php echo $host_path; ?>/popupfiles/rma_addsku.php?return_id=<?php echo $rma_return['id'] ?>" class="fancybox fancybox.iframe">Add SKU</a>
                    <?php endif; ?>		

                    <br /><br />
                    <table border="1" cellpadding="10" cellspacing="0" width="90%">
                        <tr>
                            <td>
                                <table cellpadding="5">
                                    <caption><b>Shipping</b></caption>
                                    <tr>	
                                        <td><b>Full Name:</b></td>
                                        <td><?php echo $rma_return['first_name'] . " " . $rma_return['last_name']; ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><b>Email:</b></td>
                                        <td><?php echo linkToProfile($rma_return['email'], $host_path); ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><b>Address 1:</b></td>
                                        <td><?php echo $rma_return['address1'] ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><b>Address 2:</b></td>
                                        <td><?php echo $rma_return['address2']; ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td>City: <?php echo $rma_return['city']; ?></td>
                                        <td>State: <?php echo $rma_return['state']; ?></td>
                                        <td>Zip: <?php echo $rma_return['zip']; ?></td>
                                    </tr>
                                </table>	    
                            </td>

                            <td>
                                <table cellpadding="5">
                                    <caption><b>Billing</b></caption>
                                    <tr>	
                                        <td><b>Full Name:</b></td>
                                        <td><?php echo $rma_return['first_name'] . " " . $rma_return['last_name']; ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><b>Email:</b></td>
                                        <td><?php echo linkToProfile($rma_return['email'], $host_path); ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><b>Address 1:</b></td>
                                        <td><?php echo $rma_return['address1'] ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td><b>Address 2:</b></td>
                                        <td><?php echo $rma_return['address2']; ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>	
                                        <td>City: <?php echo $rma_return['city']; ?></td>
                                        <td>State: <?php echo $rma_return['state']; ?></td>
                                        <td>Zip: <?php echo $rma_return['zip']; ?></td>
                                    </tr>
                                </table>	    
                            </td>

                            <td>
                                <table cellpadding="5">
                                    <caption><b>Other Detail</b></caption>
                                    <tr>
                                        <td><b>Order ID: <a href="viewOrderDetail.php?order=<?php echo $rma_return['order_id']; ?>"><?php echo $rma_return['order_id']; ?></a></b></td>
                                        <td>|</td>
                                        <td><b>RMA # <?php echo $rma_return['rma_number']; ?></b></td>	    	       
                                    </tr>

                                    <tr>
                                        <td><b>Order Date: <?php echo americanDate($rma_return['order_date']); ?></b></td>
                                        <td>|</td>
                                        <td>
                                            <b>Date Received: <?php echo americanDate($rma_return['date_added']); ?>
                                                <?php if ($rma_return['date_added']) {

                                                    $xuser_id = $db->func_query_first_cell("SELECT user_id FROM inv_return_history WHERE rma_number='" . $rma_return['rma_number'] . "' AND return_status='Received'");

                                                    if ($xuser_id != null) {
                                                        echo '(';
                                                            if ($xuser_id == 0) {
                                                                echo 'Admin';
                                                            } elseif ($xuser_id == '-1') {
                                                                echo 'Employee';
                                                            } else {
                                                                echo $db->func_query_first_cell("SELECT name FROM inv_users WHERE id='" . $xuser_id . "'");
                                                            }
                                                            echo ')';
                                                        } } ?>
                                                    </b>
                                                </td>	    	       
                                            </tr>

                                            <tr>
                                               <?php
                                               if($rma_return['date_qc']):

                                                ?>
                                            <td>
                                                <b>QC Date: <?php echo americanDate($rma_return['date_qc']); ?>
                                                    <?php if ($rma_return['date_qc']) {

                                                        $xuser_id = $db->func_query_first_cell("SELECT user_id FROM inv_return_history WHERE rma_number='" . $rma_return['rma_number'] . "' AND return_status='In QC'");

                                                        if ($xuser_id != null) {
                                                            echo '(';
                                                                if ($xuser_id == 0) {
                                                                    echo 'Admin';
                                                                } elseif ($xuser_id == '-1') {
                                                                    echo 'Employee';
                                                                } else {
                                                                    echo $db->func_query_first_cell("SELECT name FROM inv_users WHERE id='" . $xuser_id . "'");
                                                                }
                                                                echo ')'; 
                                                            } } ?>
                                                        </b>
                                                    </td>
                                                    <td>|</td>
                                                    <?php
                                                    endif;
                                                    if($rma_return['rma_status']=='Completed'):

                                                        ?>
                                                    <td><b>Completed Date: <?php echo americanDate($rma_return['date_completed']); ?> <?php
                                                        if ($rma_return['date_completed']) {

                                                            $xuser_id = $db->func_query_first_cell("SELECT user_id FROM inv_return_history WHERE rma_number='" . $rma_return['rma_number'] . "' AND return_status='Completed'");

                                                            if ($xuser_id != null) {
                                                                echo '(';
                                                                    if ($xuser_id == 0) {
                                                                        echo 'Admin';
                                                                    } elseif ($xuser_id == '-1') {
                                                                        echo 'Employee';
                                                                    } else {
                                                                        echo $db->func_query_first_cell("SELECT name FROM inv_users WHERE id='" . $xuser_id . "'");
                                                                    }
                                                                    echo ')';
}
}
?></b></td>	 
<?php
endif;
?>   	       
</tr>

<tr>
    <td><b>Status:</b></td>
    <td>|</td>	  
    <td><?php echo ($rma_return['rma_status'] == 'In QC') ? 'QC Completed' : $rma_return['rma_status']; ?></td>	    
</tr>	


<tr>
    <td><b>Payment Method:</b></td>
    <td>|</td>	  
    <td><?php echo $rma_return['payment_method']; ?></td>	    
</tr>
</table>
</td>
</tr> 
<tr>
    <td colspan="3"> <table cellpadding="5" style="width:100%">
        <tr>
            <td style="font-weight:bold" colspan="4">Extra Details:</td>
        </tr>
        <tr>
            <td style="font-weight:bold">Browser Details:</td>
            <td><?php echo ($rma_return['extra'] ? $rma_return['extra'] : 'Not Found'); ?></td>

            <td style="font-weight:bold">Transaction ID:</td>
            <td><?php
                if (strtolower($rma_return['payment_method']) == 'paypal' or strtolower($rma_return['payment_method']) == 'paypal express') {
                    echo 'Tran ID: (' . $db->func_query_first_cell('SELECT transaction_id FROM oc_paypal_admin_tools WHERE order_id="' . $rma_return['order_id'] . '"') . ')';
                } else if (strtolower($rma_return['payment_method']) == 'credit card / debit card (authorize.net)') {
                    echo 'Tran ID: (' . $db->func_query_first_cell('SELECT trans_id FROM oc_authnetaim_admin WHERE order_id="' . $rma_return['order_id'] . '"') . ')';
                } else {
                    echo "N/A";
                }
                ?>
            </td>
        </tr>
    </table>
</td>
</tr>	
</table>

<br />
<p>
    Return Source:
    <select name="source">
        <option value="mail">Mail</option>
        <option value="manual" <?php if ($rma_return['source'] == 'manual'): ?> selected="selected" <?php endif; ?>>Manual</option>
        <option value="storefront" <?php if ($rma_return['source'] == 'storefront'): ?> selected="selected" <?php endif; ?>>Storefront</option>
    </select>
</p>
<br />

<table border="1" cellpadding="10" cellspacing="0" width="90%">
    <tr>
        <th><input type="checkbox" onclick="toggleCheck(this)" /></th>
        <th>SKU</th>
        <th>Title</th>
        <th>QTY</th>
        <th>Return Reason</th>

        <?php if ($_SESSION['manage_returns']): ?>
            <th>How to Process</th>
            <th>Condition</th>
        <?php endif; ?>		

        <?php if ($_SESSION['return_decision'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')): ?>
            <th>Decision</th>
        <?php endif; ?>	
        <?php if ($_SESSION['complete_return'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')): ?>
            <th>Amount</th>
        <?php endif; ?>	

        <th>Images</th>	

        <?php if ($rma_return['rma_status'] == 'Awaiting'): ?>
            <th>Remove</th>
        <?php endif; ?>		
    </tr>

    <?php $decisions = array(); ?>
    <?php foreach ($return_items as $return_item): ?>
        <?php
        $images = $db->func_query("select * from inv_return_item_images where return_item_id = '" . $return_item['id'] . "'");

        $decisionCheckQuery = $db->func_query_first("SELECT * FROM inv_return_decision WHERE return_id='" . $rma_return['id'] . "' AND sku='" . $return_item['sku'] . "'");
        ?>
        <tr>
            <?php $decisions[] = $return_item['item_condition']; ?>
            <?php $den = ($return_item['decision'] == 'Denied' && $return_item['item_condition'] == 'Customer Damage') ? 1 : 0; ?>
            <td>
                <?php if ($rma_return['rma_status'] == 'Completed' && in_array($return_item['item_condition'], array('Customer Damage'))): ?>
                    <input  type="checkbox" name="return_item[<?php echo $return_item['id']; ?>]" class="item_checkbox" value="<?php echo $return_item['id']; ?>" onchange="checkSelectBoxes()" <?php echo ($decisionCheckQuery ? 'disabled' : ''); ?> />
                <?php elseif ($rma_return['rma_status'] != 'Completed'): ?>
                    <input  type="checkbox" name="return_item[<?php echo $return_item['id']; ?>]" class="item_checkbox" value="<?php echo $return_item['id']; ?>" onchange="checkSelectBoxes()" <?php echo ($decisionCheckQuery ? 'disabled' : ''); ?> />
                <?php endif; ?> 		
            </td>

            <td width="70">
                <input type="hidden" name="product_sku[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['sku'] ?>" />


                <input type="hidden" name="new_sku[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['sku']; ?>" />
                <?= linkToProduct($return_item['sku'], $host_path); ?>
            </td>

            <td width="150"><?php echo $return_item['title']; ?></td>

            <td><?php echo $return_item['quantity']; ?></td>

            <td width="150"><?php echo $return_item['return_code']; ?></td>

            <?php if ($_SESSION['manage_returns']): ?>
                <td>
                    <input type="text" name="how_to_process[<?php echo $return_item['id']; ?>]" value="<?php echo $return_item['how_to_process'] ?>"  />
                </td>

                <td>
                    <select class="condition" name="item_condition[<?php echo $return_item['id']; ?>]" onchange="unlockBox(this.value,<?php echo $return_item['id']; ?>)">
                        <option value="">Select One</option>

                        <?php foreach ($item_conditions as $item_condition): ?>
                            <option value="<?php echo $item_condition['id'] ?>" <?php if ($item_condition['id'] == $return_item['item_condition']): ?> selected="selected" <?php endif; ?>>
                                <?php echo $item_condition['value'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <br /><br />
                    <div id="item_issue_<?php echo $return_item['id']; ?>" <?php if ($return_item['item_condition'] != 'Item Issue'): ?> style="display:none;" <?php endif; ?>>
                        <select name="item_issue[<?php echo $return_item['id']; ?>]" style="width:135px;">
                            <option value="">Select One</option>

                            <?php foreach ($item_issues as $item_issue): ?>
                                <option value="<?php echo $item_issue['name'] ?>" <?php if ($item_issue['name'] == $return_item['item_issue']): ?> selected="selected" <?php endif; ?>>
                                    <?php echo $item_issue['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </td>

                <?php if ($_SESSION['return_decision'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')): ?>
                    <td>
                        <?php if ($return_item['item_condition'] != 'Customer Damage' && $return_item['item_condition'] != 'Not PPUSA Part' && $return_item['item_condition'] != 'Over 60 days'): ?>

                          <?php
if($decisionCheckQuery)
{
	echo $decisionCheckQuery['action'];
}
else
{

?>
                          <select class="decision" name="decision[<?php echo $return_item['id']; ?>]">
                            <option value="">Please Select</option>
                            <?php
                            foreach ($decisionsx as $decision) {
                                ?>
                                <option value="<?php echo $decision['id']; ?>" <?php if ($decision['id'] == $return_item['decision']) echo 'selected'; ?>><?php echo $decision['value']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <?php
}
?>

                    <?php else: ?>
                        <?php echo createField("decision[" . $return_item['id'] . "]", "decision", "select", $return_item['decision'], array(array('id' => 'Denied', 'value' => 'Denied'))); ?>

                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if ($_SESSION['complete_return'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')): ?>
                <td>
                    <input class="price"  type="text" id="product_price_<?php echo $return_item['id'];?>" name="product_price[<?php echo $return_item['id']; ?>]" value="<?= ($den) ? '0' : $return_item['price']; ?>" data-price="<?= $return_item['price']+$return_item['discount_amount']; ?>" />

                    <input type="checkbox" name="restocking[<?php echo $return_item['id']; ?>]" value="1" onchange="populateRestocking(<?php echo $return_item['id'];?>,this)" <?php echo ($return_item['restocking']==1)?'checked':''; ?>/> Re-stocking
                    <div id="div_<?php echo $return_item['id'];?>" style="background-color:#D0D0D0;<?php echo ($return_item['restocking']==1)?'':'display:none'; ?>"><input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="A" data-discount="10" data-item-id="<?php echo $return_item['id'];?>" onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" <?php echo ($return_item['restocking_grade']=='A')?'checked':''; ?> /> A<br />
                        <input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]"  value="B" data-discount="20" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" <?php echo ($return_item['restocking_grade']=='B')?'checked':''; ?> /> B<br />
                        <input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="C" data-discount="30" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" <?php echo ($return_item['restocking_grade']=='C')?'checked':''; ?> /> C<br />
                        <input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="D" data-discount="50" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" <?php echo ($return_item['restocking_grade']=='D')?'checked':''; ?> /> D<br />
                    </div>
                    <input type="hidden" name="discount_amount[<?php echo $return_item['id'];?>]" id="discount_amount_<?php echo $return_item['id'];?>" value="<?php echo $return_item['discount_amount'];?>" />
                    <input type="hidden" name="discount_per[<?php echo $return_item['id'];?>]" id="discount_per_<?php echo $return_item['id'];?>" value="<?php echo $return_item['discount_per'];?>" />
                </td>
                <?php
                else:
                    ?>
                <td style="display:none">
                   <input  type="hidden" id="product_price_<?php echo $return_item['id'];?>" name="product_price[<?php echo $return_item['id']; ?>]" value="<?=$return_item['price']; ?>" data-price="<?= $return_item['price']+$return_item['discount_amount']; ?>" />

                   <input type="checkbox" name="restocking[<?php echo $return_item['id']; ?>]" value="1" onchange="populateRestocking(<?php echo $return_item['id'];?>,this)" /> Re-stocking
                   <div id="div_<?php echo $return_item['id'];?>" style="background-color:#D0D0D0;display:none"><input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="A" data-discount="10" data-item-id="<?php echo $return_item['id'];?>" onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" /> A<br />
                    <input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]"  value="B" data-discount="20" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" /> B<br />
                    <input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="C" data-discount="30" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" /> C<br />
                    <input type="radio" name="restocking_grade[<?php echo $return_item['id'];?>]" value="D" data-discount="50" data-item-id="<?php echo $return_item['id'];?>"  onchange="updateStockingPrice('<?php echo $return_item['id'];?>',this)" /> D<br /></div>
                    <input type="hidden" name="discount_amount[<?php echo $return_item['id'];?>" id="discount_amount_<?php echo $return_item['id'];?>" value="<?php echo $return_item['discount_amount'];?>" />
                    <input type="hidden" name="discount_per[<?php echo $return_item['id'];?>" id="discount_per_<?php echo $return_item['id'];?>" value="<?php echo $return_item['discount_per'];?>" />
                </td>
                <?php
                endif;
                ?>
                <td>
                    <input style="display:none;" onchange="document.forms['returnForm'].submit();" type="file" id="image_path_<?php echo $return_item['id']; ?>" name="image_path[<?php echo $return_item['id']; ?>][]" multiple="multiple" value="" />

                    <a href="javascript://" onclick="jQuery('#image_path_<?php echo $return_item['id']; ?>').click();">Upload</a>

                    <br /><br />
                    <?php if ($images): ?>
                        <table align="left">
                            <tr>
                                <?php foreach ($images as $image): ?>
                                    <td>
                                        <a href="<?php echo str_ireplace("../", "", $image['image_path']); ?>" class="fancybox2 fancybox.iframe">
                                            <img src="<?php echo str_ireplace("../", "", $image['thumb_path']); ?>" width="25" height="25" />
                                        </a>	

                                        <a onclick="if (!confirm('Are you sure?')) {
                                        return false;
                                    }" href="return_detail.php?rma_number=<?php echo $rma_number ?>&action=remove&image_id=<?php echo $image['id'] ?>&return_item_id=<?php echo $return_item['id'] ?>">X</a>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </table>
                <?php endif; ?>
            </td>
        <?php endif; ?>     

        <?php if ($rma_return['rma_status'] == 'Awaiting'): ?>
            <td>
                <a class="fancybox fancybox.iframe" href="<?php echo $host_path; ?>/popupfiles/rma_rmsku.php?return_id=<?php echo $rma_return['id'] ?>&id=<?php echo $return_item['id'] ?>" onclick="if (!confirm('Are you sure?')) {
                    return false;
                }">X</a>
            </td>
        <?php endif; ?>
    </tr>
<?php endforeach; ?>
</table>

<a href="" id="decision-anchor" class="fancybox3 fancybox.iframe" style="display:none"></a>
<br /><br />

<?php if ($_SESSION['return_decision'] && $rma_return['rma_status'] == 'In QC'): ?>
    <?php
    if (($_SESSION['complete_storefront_return'] == 1 and $rma_return['source'] == 'storefront') or ( $_SESSION['complete_mail_return'] == 1 and $rma_return['source'] == 'mail') or ( $_SESSION['complete_return'] or $_SESSION['login_as'] == 'admin')) {

        if ($rma_return['store_type'] == 'amazon'):
            ?>	


        <input type="button" value="Amazon Refund"  class="button" id="issue_amazon_refund" /><br /> <br />
        <?php
        else:
           ?>
       <input type="button" id="issue_sc" value="Issue Store Credit" class="button" /> 
       <input type="button" id="issue_replacement" value="Issue Replacement" class="button" /> 
       <input type="button" id="issue_refund" value="Issue Refund" class="button" /><br /><br />
       <?php
       endif;
   }
   ?>
<?php endif; ?>

<input type="hidden" name="order_id" value="<?php echo $rma_return['order_id'] ?>" />
<input type="hidden" name="return_id" value="<?php echo $rma_return['id'] ?>" />
<input type="hidden" name="return_number" value="<?php echo $rma_return['return_number'] ?>" />
<input type="hidden" id="selected_items" value="" />
<input type="hidden" name="decision_save" value="1" />
<?php if (in_array($rma_return['rma_status'], array('Awaiting'))): ?>
    <input type="submit" name="received" value="Received" onclick="if (!confirm('Are you sure?')) {
    return false;
}" class="button" />
<?php endif; ?>

<?php if ($rma_return['rma_status'] != 'Completed'): ?>
    <input type="submit" name="save" value="Save" class="button" />
<?php endif; ?>

<?php if ($_SESSION['manage_returns'] && in_array($rma_return['rma_status'], array('Received'))): ?>
    <input type="submit" name="qcdone" value="Complete QC" class="button" />
<?php endif; ?>		

<?php if ($_SESSION['return_decision'] && $rma_return['rma_status'] == 'In QC'): ?>		
    <?php
                        //if($_SESSION['complete_return'] or $_SESSION['login_as'] == 'admin' or $_SESSION['complete_storefront_return'] or $_SESSION['complete_mail_return'])
                        //{
    if (($_SESSION['complete_storefront_return'] == 1 and $rma_return['source'] == 'storefront') or ( $_SESSION['complete_mail_return'] == 1 and $rma_return['source'] == 'mail') or ( $_SESSION['complete_return'] or $_SESSION['login_as'] == 'admin')) {
        ?>
        <input type="submit" name="completed" value="Complete Return" class="button" />
        <?php
    }
    ?>
<?php endif; ?>		


<?php if ($rma_return['rma_status'] == 'Completed' && $rma_return['denied_order_created'] == 0 && in_array('Customer Damage', $decisions)): ?>
    <input type="submit" name="deniedReturnOrder" value="Denied Return order"  onclick="if (!confirm('Are you sure?')) {
    return false;
}" class="button" />
<?php endif; ?>
</form>
<?php if ($rma_return['rma_status'] != 'Awaiting') { ?>
<br /><br />
<div align="center">
    <h3>Send Email</h3>
    <table width="70%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
        <form method="post" action="" id="email_form">
            <tr>
                <td>Canned Message:</td>
                <td>
                    <?php $canned_messages = $db->func_query('SELECT * FROM `inv_canned_message` WHERE `catagory` = "2" AND `type` = "Canned"'); ?>
                    <select name="canned_id" id="canned_message">
                        <option value=""> --- Custom --- </option>
                        <?php foreach ($canned_messages as $canned_message) { ?>
                        <option value="<?= $canned_message['canned_message_id']; ?>"><?= $canned_message['name']; ?></option>
                        <?php } ?>                        
                    </select>
                    <input type="hidden" name="total_formatted" value="<?= $emailInfo['total_formatted']; ?>"/>
                </td>
            </tr>
            <tr>
                <td>Title</td>
                <td>
                    <input type="text" name="title" id="canned_title" value=""/>
                    <input type="hidden" id="selected_products" value="" name="selected_products" />
                </td>
            </tr>
            <tr>
                <td>Subject</td>
                <td><input type="text" name="subject" id="canned_subject" value=""/></td>
            </tr>
            <tr>
                <td>Message:</td>
                <td><textarea name="comment" id="comment" class="comment-box" cols="40" rows="8" style="width: 99%"></textarea></td>
                <script>
                    CKEDITOR.replace( 'comment' );
                </script>
            </tr>
            <tr>
                <td></td>
                <td><label class="addsd" for="signature_check"><input type="checkbox" id="signature_check" /> Add Signature</label><label class="addsd" for="disclaimer_check"><input type="checkbox" id="disclaimer_check" /> Add Disclaimer</label></td>
            </tr>
            <tr>
                <td></td>
                <td><input class="button" name="sendemail" onclick="return sendEmailForm(this);" value="Send Email" type="submit"></td>
                <script type="text/javascript">
                    function sendEmailForm(t) {
                        if ($('#canned_subject').val() == '' || $('#canned_title').val() == '') {
                            alert('Please select canned message Or write your own');
                            return false;
                        } else {
                            return true;
                        }
                        return false;
                    }
                </script>
            </tr>
        </form>
    </table>
    <ul style="display:none;">
        <textarea id="disclaimer"><div contenteditable="false"><?= $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `type` = 1'); ?></div></textarea>
        <?php $src = $path .'files/sign_' . $_SESSION['user_id'] . ".png"; ?>
        <textarea id="signature"><div contenteditable="false"><?= shortCodeReplace($adminInfo, $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `user_id` = "'. $_SESSION['user_id'] .'" AND type = 0')); ?> <?= (file_exists($src))? '<img src="'. $host_path .'files/sign_' . $_SESSION['user_id'] . '.png?'. time() .'" />': ''; ?></div></textarea>
        <?php foreach ($canned_messages as $canned_message) { ?>
        <textarea id="canned_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['message']); ?></textarea>
        <li id="title_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['title']); ?></li>
        <li id="subject_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['subject']); ?></li>
        <?php } ?>
    </ul>
    <script type="text/javascript">
        $(function() {
            $('#email_form').submit(function () {
                if ($('#canned_title').val() == '' || $('#canned_subject').val() == '') {
                    alert('Please Enter Your Message');
                    return false;
                }
            });
            $('#canned_message').change(function() {
                var id = $(this).val();
                var message = '';
                if(id > 0) {
                    message = $('#canned_' + id).text();
                }
                message = message + '<div id="customeData">';
                if ($('#signature_check').is(':checked')) {
                    message = message + $('#signature').text();
                }
                if ($('#disclaimer_check').is(':checked')) {
                    message = message + $('#disclaimer').text();
                }
                message = message + '</div>';
                $('#canned_title').val($('#title_' + id).text());
                $('#canned_subject').val($('#subject_' + id).text());
                CKEDITOR.instances.comment.setData(message);
            });
            $('.addsd').click(function() {
                if (!CKEDITOR.instances.comment.document.getById('customeData')) {
                    message = CKEDITOR.instances.comment.getData() + '<div id="customeData"></div>';
                    CKEDITOR.instances.comment.setData(message);
                }
                //CKEDITOR.instances.comment.document.getById('customeData');
                var message = '';
                if ($('#signature_check').is(':checked')) {
                    message = message + $('#signature').text();
                }
                if ($('#disclaimer_check').is(':checked')) {
                    message = message + $('#disclaimer').text();
                }
                CKEDITOR.instances.comment.document.getById('customeData').setHtml(message);
                //CKEDITOR.instances.comment.setData(message);
            });
            $('#canned_message').keyup(function() {
                $(this).change();
            });
        });
</script>
</div>
<?php  } ?>


<br /><br />
<?php if ($removed_items): ?>
    <table border="1" cellpadding="10" width="50%">
        <tr>
            <td>SKU</td>
            <td>Remove Reason</td>
        </tr>

        <?php foreach ($removed_items as $removed_item): ?>
            <tr>
                <td><?php echo $removed_item['sku'] ?></td>
                <td><?php echo $removed_item['remove_reason'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br /><br />		
<?php endif; ?>

<form method="post" action="">
    <table border="1" cellpadding="10" width="50%">
        <tr>
            <td align="center">
                <textarea rows="5" cols="50" name="comments" required></textarea>
            </td>
        </tr>
        <tr>
            <td align="center">
                <input type="submit" class="button" name="addcomment" value="Add Comment" />	  		  	 
            </td>
        </tr>	
    </table>
    <input type="hidden" name="return_id" value="<?php echo $rma_return['id'] ?>" />
    <input type="hidden" name="return_number" value="<?php echo $rma_return['return_number'] ?>" />

</form>

<h2>Comment History</h2>
<table cellpadding="10" border="1" width="50%">
    <tr>
        <th>Date</th>
        <th>User</th>
        <th>Comment</th>
    </tr>
    <?php foreach ($comments as $comment): ?>
        <tr>
            <td><?php echo americanDate($comment['comment_date']); ?></td>
            <td><?php echo ($comment['user_id']) ? $comment['name'] : 'admin'; ?></td>
            <td><?php echo $comment['comments']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>		
<br /><br />  
</div>
</div>		
<script type="text/javascript">

    $('.decision').on('change', function () {
        var whole = $(this).parent().parent();
        var checkBox = whole.find('.item_checkbox');
        var price = whole.find('input.price');
        var condition = whole.find('.condition').val();
        var discVal = $(this).val();
        if (discVal == 'Denied' && condition == 'Customer Damage') {
            if (checkBox.is(':checked')) {
                checkBox.removeAttr('checked');
            }
            checkBox.attr('disabled', 'disabled');
            price.val('0');
        } else {
            price.val(price.attr('data-price'));
            checkBox.removeAttr('disabled');
        }
    });
    $('.conditon').on('change', function () {
        var whole = $(this).parent().parent();
        var checkBox = whole.find('.item_checkbox');
        var price = whole.find('input.price');
        var discVal = whole.find('.decision').val();
        var condition = $(this).val();
        if (discVal == 'Denied' && condition == 'Customer Damage') {
            if (checkBox.is(':checked')) {
                checkBox.removeAttr('checked');
            }
            checkBox.attr('disabled', 'disabled');
            price.val('0');
        } else {
            price.val(price.attr('data-price'));
            checkBox.removeAttr('disabled');
        }
    });
    function viewDecision(obj, return_id) {
        if (obj.value == 'Issue Credit') {
            $('a#decision-anchor').attr('href', 'rma_credit.php?return_id=' + return_id);
            $('a#decision-anchor').click();
        }
        else if (obj.value == 'Issue Replacement') {
            $('a#decision-anchor').attr('href', 'rma_replacement.php?return_id=' + return_id);
            $('a#decision-anchor').click();
        }
        else if (obj.value == 'Issue Refund') {
            if (confirm('Are you sure want to refund?')) {
                setTimeout(function () {
                    alert('Error: Problem Communicating with Server, Please try again later');
                }, 5000);
            }
        }
    }

    function toggleCheck(obj) {
        $('.item_checkbox').not(':disabled').prop('checked', obj.checked);
        checkSelectBoxes()
    }

    function checkSelectBoxes() {
        var item_value = '';
        $('.item_checkbox').each(function (index, element) {
            if ($(element).is(':checked')) {
                item_value += $(element).val() + ',';
            }
        });

        $('#selected_items').val(item_value);
        $('#selected_products').val(item_value);
    }

    $('#issue_sc').click(function (e) {
        if ($('#selected_items').val() == '') {
            alert('Please select item first');
            return false;
        }

        $('a#decision-anchor').attr('href', 'rma_credit.php?rma_number=<?php echo $_GET['rma_number']; ?>&items=' + $('#selected_items').val());
        $('a#decision-anchor').click();

                /*$.ajax({
                 url: "ajax_store_credit.php",
                 type:"POST",
                 data: {order_id: $('input[name=order_id]').val(),items:$('#selected_items').val()},
                 success: function(data) {
                 alert(data);
                 }
             });*/
});

    $('#issue_replacement').click(function (e) {
        if ($('#selected_items').val() == '') {
            alert('Please select item first');
            return false;
        }

                /*if(!confirm('Are you sure want to proceed?')){
                 return false;	
                 }
                 
                 $.ajax({
                 url: "ajax_replacement.php",
                 type:"POST",
                 data: {order_id: $('input[name=order_id]').val(),items:$('#selected_items').val()},
                 success: function(data) {
                 alert(data);
                 }
             });*/


    $('a#decision-anchor').attr('href', 'rma_replacement.php?rma_number=<?php echo $_GET['rma_number']; ?>&items=' + $('#selected_items').val());
    $('a#decision-anchor').click();

});
    $('#issue_refund').click(function (e) {

        if ($('#selected_items').val() == '') {
            alert('Please select item first');
            return false;
        }

        $('a#decision-anchor').attr('href', 'rma_refund.php?rma_number=<?php echo $_GET['rma_number']; ?>&items=' + $('#selected_items').val());
        $('a#decision-anchor').click();
    });


    $('#issue_amazon_refund').click(function (e) {

        if ($('#selected_items').val() == '') {
            alert('Please select item first');
            return false;
        }

        $('a#decision-anchor').attr('href', 'rma_amazon_refund.php?rma_number=<?php echo $_GET['rma_number']; ?>&items=' + $('#selected_items').val());
        $('a#decision-anchor').click();
    });

    function completeReturn()
    {
        var total_checkboxes = $('.item_checkbox').not(':disabled').length;

        var total_checked = $('.item_checkbox:checked').length;

        if (total_checkboxes == total_checked)
        {
            $('input[name=decision_save]').val(0);
            $('input[name=completed]').click();
        }
        else
        {
            location.reload();
        }
    }

</script>
</body>
</html>