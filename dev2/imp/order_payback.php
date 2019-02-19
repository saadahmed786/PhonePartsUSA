<?php
require_once("auth.php");
require_once("inc/functions.php");
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
/*
  if($_SESSION['login_as'] != 'admin'){
  echo  'You dont have permission to manage order payback.';
  exit;
} */

$order_id = $_GET['order_id'];
$items = rtrim($_GET['items'], ",");
$voucher_items_reasons = $_GET['reasons'];
if ($voucher_items_reasons) {
    //print_r($voucher_items_reasons);exit;
    $voucher_items_reasons = str_replace(',', ';', $voucher_items_reasons);
}
$storetype = $_GET['storetype'];
$action = $_GET['action'];
$item_remove = $_GET['itemrem'];


$order_info = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND a.order_id='" . $order_id . "'");

$query_where = ($items)? "`id` IN ('" . str_replace(',', "', '", $items) . "') AND": '';

if($action=='cancel')
{

$order_items = $db->func_query("SELECT * FROM `inv_orders_items` WHERE $query_where `order_id` = '". $order_info['order_id'] ."'");
}
else
{
	
$order_items = $db->func_query("SELECT item_sku as product_sku,item_price as product_price FROM `inv_removed_order_items` WHERE  `order_id` = '". $order_info['order_id'] ."'");
}

$_tax = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE `order_id` = "'. $order_info['order_id'] .'" AND `code` = "tax"'),2);

$amount = 0;

if (!$items) {
    $amount += $_tax;
}

$emailInfo = $_SESSION['email_info'][$order_id];
$adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );

$productNames = '<table><tbody>';

foreach ($order_items as $return_item) {

    $productNames .= '<tr><td>'. $return_item['product_sku'] .'</td></tr>';

}

$productNames .= '</tbody></table>';

if ($_POST['amount']) {
    $emailInfo['total_formatted'] = $_POST['amount'];
}

$emailInfo['products_name'] = $productNames;

$_SESSION['email_info'][$order_id] = $emailInfo;
if ($_POST['order_id'] && $_POST['reason']) {
  //  $json['success'] = 'Action Completed Successfully';
    if (!$_POST['items']) {
        $comment = 'Order #'. linkToOrder($_POST['order_id']) .' canceld and refunded.';

        $addReport = array(
            'order_id'  =>  $_POST['order_id'],
            'reason_id' =>  $_POST['reason'],
            'order_amount'    =>  $_POST['amount'],
            'user_id'   =>  $_SESSION['user_id'],
            'date_added'=>  date('Y-m-d H:i:s')
            );
        $cancel_id = $db->func_array2insert("inv_order_cancel_report", $addReport);
        unset($addReport);

        $skus = $db->func_query("SELECT * FROM `inv_orders_items` WHERE `order_id` = '". $_POST['order_id'] ."'");
        foreach ($skus as $xsk) {
            $addReport = array(
                'cancel_id'  =>  $cancel_id,
                'sku'       =>  $xsk['product_sku'],
                'amount'    =>  $xsk['product_price'],
                'action'   =>  'Order Canceled',
                'date_added'=>  date('Y-m-d H:i:s')
                );
            $db->func_array2insert("inv_product_cancel_report", $addReport);
            unset($addReport);
        }
    } else {

        $addReport = array(
            'order_id'  =>  $_POST['order_id'],
            'reason_id' =>  $_POST['reason'],
            'order_amount'    =>  $_POST['amount'],
            'user_id'   =>  $_SESSION['user_id'],
            'date_added'=>  date('Y-m-d H:i:s')
            );
        $cancel_id = $db->func_array2insert("inv_order_cancel_report", $addReport);
        unset($addReport);

        $skus = $db->func_query("SELECT * FROM `inv_orders_items` WHERE `id` IN ('" . str_replace(',', "', '", $_POST['items']) . "')");
        foreach ($skus as $xsk) {
            $sku .= ' ' . linkToProduct($xsk['product_sku']);

            $addReport = array(
                'cancel_id'  =>  $cancel_id,
                'sku'       =>  $xsk['product_sku'],
                'amount'    =>  $xsk['product_price'],
                'action'   =>  'Item Removed',
                'date_added'=>  date('Y-m-d H:i:s')
                );
            $order_history_id = $db->func_array2insert("inv_product_cancel_report", $addReport);
            unset($addReport);
        }
        $comment = 'Product(s) '. $sku .' removed from Order #'. linkToOrder($_POST['order_id']) .' and refunded.';
    }
    $order_id = $_POST['order_id'];
    $order_details = $db->func_query_first('SELECT * FROM inv_orders WHERE order_id = "'. $order_id .'"');

    // Adding Voucher
    
    $ext = (!$_POST['items'])? 'CO': 'RP';
    $vouch['code'] = getVoucherCode($order_id, $ext);
    $emailInfo['voucher_code'] = $vouch['code'];
    $emailInfo['customer_name'] = $order_details['customer_name'];
    $emailInfo['order_id'] = $_POST['order_id'];
    $emailInfo['email'] = $order_details['email'];
    if ($_POST['canned_id']) {

        $email = array();

        $src = $path .'files/canned_' . $_POST['canned_id'] . ".png";

        if (file_exists($src)) {
            $email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
        }

        $email['title'] = $_POST['title'];
        $email['subject'] = $_POST['subject'];
        $email['message'] = shortCodeReplace($emailInfo, $_POST['comment']);

        sendEmailDetails($emailInfo, $email);

    } else {
        $_SESSION['message'] = 'Email not sent';
    }

    if (!$_POST['reimb']) {
        $vouch['voucher_theme_id'] = '8';
        $vouch['status'] = 1;
        $vouch['to_name'] = $order_details['customer_name'];
        $vouch['to_email'] = $order_details['email'];
        $vouch['amount'] = $_POST['amount'];
        $vouch['from_name'] = $_SESSION['login_as'];
        $vouch['from_email'] = $_SESSION['email'];
        $vouch['user_id'] = $_SESSION['user_id'];
        //$vouch['reason_id'] = $_POST['voucher_reason'];
        $vouch['voucher_items_reasons'] = $_POST['voucher_items_reasons'];
        $vouch['date_added'] = date('Y-m-d H:i:s');
        $id = $db->func_array2insert('oc_voucher', $vouch);

        $log = 'Voucher '. linkToVoucher($id, $host_path, $vouch['code']) .' was created of amount '. $vouch['amount'] .' for ' . linkToProfile($vouch['to_email']) . ' as ' . (($vouch['status'])? 'Enabled': 'Disabled');
        actionLog($log);


        $item_detail = '';
        foreach ($skus as $return_item) {
            $_sku = $return_item['product_sku'];
            $_title = $db->func_escape_string(getItemName($return_item['product_sku']));
            $_quantity = $return_item['product_qty'];
            $_price = $return_item['product_price'];

            $item_detail.='SKU: '.$_sku.', Title:'.$_title.', Qty: '.$_quantity.', Price: '.$_price."<br>";
        }

        $voucher_detail = array();
        $voucher_detail['voucher_id'] = $id;
        $voucher_detail['order_id'] = $order_id;

        $voucher_detail['detail'] = $item_detail;
        if ($_POST['itemrem'] == '1') {
        $voucher_detail['is_manual'] = 1;            
        } else {    
        $voucher_detail['is_order_cancellation'] = 1;
        }
        $voucher_detail['user_id'] = $_SESSION['user_id'];
        addVoucherDetail($voucher_detail);

        if ($id) {
            if ($skus) {
                $reasons = explode(";", $_POST['voucher_items_reasons']);
                //print_r($reasons);exit;
                $iterate = 0;
                foreach ($skus as $data) {
                    $product['voucher_id'] = $id;
                    $product['order_id'] = $order_id;
                    $product['sku'] = $data['product_sku'];
                    $product['price'] = $data['product_price'];
                    $product['reason'] = $reasons[$iterate];
                    $db->func_array2insert('`inv_voucher_products`', $product);
                    unset($product);
                    $iterate++;
                }
            }

            $data = array();
            $data['voucher_id'] = $id;
            $data['user_id'] = $_SESSION['user_id'];
            $data['comment'] = $_SESSION['login_as'] . ' created this Voucher';
            $data['date_added'] = date('Y-m-d H:i:s');

            $db->func_array2insert('`inv_voucher_comments`', $data);





            $json['success'] = "Voucher " . $vouch['code'] . " has been added";
        }
    } else {
        $json['success'] = "Order Canceled";
    }

    $addcomment = array();
    $addcomment['date_added'] = date('Y-m-d H:i:s');
    $addcomment['user_id'] = $_SESSION['user_id'];
    $addcomment['comment'] = $db->func_escape_string($comment);
    $addcomment['order_id'] = $_POST['order_id'];
    $order_history_id = $db->func_array2insert("inv_order_history", $addcomment);
    actionLog($comment);

    echo json_encode($json);
    exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Refund</title>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>
<body>
    <div align="center">


        <?php if ($_SESSION['message']): ?>
            <div align="center"><br />
                <font color="red"><?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?><br />
                </font>
            </div>
        <?php endif; ?>

        <br clear="all" />



        <div align="center">
            <form action="" id="myFrm" method="post">
                <h2><?php echo ($action == 'cancel')? 'Cancel Order': 'Issue Refund or Store Credit'; ?></h2>
                <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">
                    <tr>
                        <td>Order ID</td>
                        <td><?php echo $order_info['order_id']; ?></td>
                    </tr>
                    <tr>
                        <td>Item(s)</td>
                        
                        <td>
                            <?php
                            foreach ($order_items as $item) {

                                echo $item['product_sku'] . "<br />";
                                $amount+=$item['product_price'];
                            }
                            if($order_info['store_type']=='web')
                            {
                                $_voucher_query = 'cast(a.order_id as char(50)) = "'. $order_info['order_id'] .'"';
                            }
                            else
                            {
                                $_voucher_query = 'cast(a.inv_order_id as char(50)) = "'. $order_info['order_id'] .'"';
                            }
                            $vouchers = $db->func_query('SELECT *, `a`.`amount` as `used`, `b`.`amount` as `remain` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND '.$_voucher_query.' ');
                            foreach ($vouchers as $voucher) {
                                $voucher['used'] = $voucher['used']*-1;
                                $voucher_amount += $voucher['used'];
                            }
                            //print_r($voucher_amount);exit;
                            if ($amount > $voucher_amount) {
                                $amount = $amount - $voucher_amount;
                            } else {
                                $amount = 0.00;
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Refund Shipping</td>
                        <td>
                            <input type="checkbox" name="refundShipping" value="1" onchange="updatePrice(this);" />
                        </td>
                    </tr>
                    <?php if ($storetype != 'amazon') { ?>
                    <tr>
                        <td>
                            Store Credit / Refund
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="method"  value="credit" checked="checked" >
                                Issue Store Credit
                            </label>
                            <?php

                            if (preg_match("/^cash(.*)/i", strtolower($order_info['payment_method'])) > 0) {
                            }
                            else
                            {
                                ?>
                                <label>
                                    <input type="radio"  name="method" value="paypal" >
                                    Refund
                                </label>
                                <?php
                            }
                            ?>
                            <label>
                        
                                <input type="radio" name="method"  value="reimbursement">
                                No Refund or Store Credit
                            </label>
                        </td>
                    </tr>
                    <?php } ?>
                      
                <tr>
                        <td>Reason</td>
                        <td>
                            <select name="reason" required="" id="reason">
                                <option value="">Please Select</option>
                                <?php //$q = "SELECT * FROM `inv_order_reasons` WHERE `type` = 'item'"; ?>
                                <?php //if ($action == 'cancel') { 
                                    $q = "SELECT * FROM `inv_order_reasons` WHERE `type` = 'order'";
                                //} ?>
                                <?php foreach ( $db->func_query($q) as $type) { ?>
                                <option value="<?= $type['id']; ?>"><?= $type['name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td>$<span id="amount_span"><?php echo number_format($amount, 2); ?></span><input type="hidden" name="price" value="<?php echo $amount; ?>"</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div align="center">
                                <table width="70%" <?php echo (($storetype == 'amazon')? ' style="display: none;': ''); ?> cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
                                    <tr>
                                        <td>Canned Message:</td>
                                        <td>
                                            <?php $canned_messages = $db->func_query('SELECT * FROM `inv_canned_message` WHERE `catagory` = "1"'); ?>
                                            <select name="canned_id" id="canned_message">
                                                <option value=""> --- Custom --- </option>
                                                <?php foreach ($canned_messages as $canned_message) { ?>
                                                <option value="<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['name'])  ; ?></option>
                                                <?php } ?>                        
                                            </select>
                                            <input type="hidden" name="total_formatted" value="<?= $emailInfo['total_formatted']; ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Title</td>
                                        <td><input type="text" name="title" id="canned_title" value=""/></td>
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
                                    var canned_messages = [<?php echo implode(',', $messages)?>];
                                    var msgs = {};
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
</td>
</tr>

<tr>
    <td colspan="2" align="center">
     <?php if ($storetype == 'amazon') { ?>
     <input type="radio" style="display: none;" name="method" checked="checked" value="amazon" >
     <?php } ?>
     <input type="button" name="add" value="Complete" onclick="submitForm()" />
 </td>
</tr>
</table>
</form>
<script>
    /*function checkVoucherReason(){
        var check = $('input[name=method]:checked', '#myFrm').val();
        if (check != 'credit') {
            $('#voucher_reason_row').hide();
            document.getElementById("voucher_reason").required = false;
        } else {
            $('#voucher_reason_row').show();
            document.getElementById("voucher_reason").required = true;
        }
    }*/
    function updatePrice(obj) {
        var amount = 0.00
        if (obj.checked)
        {
            amount =<?php echo $amount + $order_info['shipping_cost']; ?>;

        }
        else
        {
            amount =<?php echo $amount; ?>;


        }
        $('input[name=price]').val(amount);

        $('#amount_span').html(amount.toFixed(2));

    }
    function submitForm() {

        if (!$('#reason').val()) {
            alert('Select Reason');
            return false;
        }
        /*var checkradio = $('input[name=method]:checked', '#myFrm').val();
        if (checkradio == 'credit') {
           if (!$('#voucher_reason').val()) {
            alert('Select Voucher Reason');
            return false;
         } 
        }*/

        var reimbursement = 0;

                    // if (!$('input[name=method]').val()) {
                    //     alert('Select Method');
                    //     return false;
                    // }
                   // alert($('input[name=method]').val());return false;
                    //var url = 'order_payback.php';
                    if ($('input[name=method]:checked').val() == 'reimbursement') {
                        reimbursement = 1;
                    }
                    if ($('input[name=method]:checked').val() == 'paypal') {
                        url = 'ajax_payback.php';
                    } else if ($('input[name=method]:checked').val() == 'credit' || $('input[name=method]:checked').val() == 'reimbursement') {
                        url = 'order_payback.php';
                    } else if ($('input[name=method]:checked').val() == 'amazon') {
                        url = 'ajax_amazon_payback.php';
                    };

                    if (!confirm('Are you sure?'))
                    {
                        return false;

                    }

                    refundShipping = $('input[name=refundShipping]:checked').val();



                    $.ajax({

                        url: url,
                        type: "POST",
                        data: {itemrem:'<?php echo (int)$item_remove;?>',order_id: '<?php echo $order_info['order_id']; ?>', refundShipping: refundShipping, reason: $('#reason').val(), items: '<?php echo $items; ?>', amount: $('input[name=price]').val(), reimb: reimbursement,voucher_items_reasons: '<?php echo $voucher_items_reasons; ?>', title: $('input[name="title"]').val(), subject: $('input[name="subject"]').val(), canned_id: $('select[name="canned_id"]').val(), comment: CKEDITOR.instances.comment.getData()},
                        dataType: "json",
                        beforeSend: function () {
                            $('input[name=add]').prop('disabled', 'disabled');
                        },
                        complete: function () {
                            $('input[name=add]').prop('disabled', '');
                        },
                        success: function (json) {
                            if (json['error']) {
                                alert(json['error']);
                                return false;
                            }
                            if (json['success']) {
                                //$('table').html('<h1>'+ json['response'] +'</h1>')
                                alert(json['success']);
                                <?php if ($action == 'cancel') { ?>
                                    window.parent.changeOrderStatus('Canceled', 'true');
                                    <?php } ?>
                                    <?php if ($action == 'remove') { ?>
                                        $('#refundProducts', window.parent.document).val('0');
                                        $("input[name=update]", window.parent.document).click();
                                        <?php } ?>
                                    }
                                }
                            });


}
</script>
</div>		


</div>		     
</body>
</html>