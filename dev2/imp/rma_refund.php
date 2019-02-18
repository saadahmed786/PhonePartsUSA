<?php
require_once("auth.php");
require_once("inc/functions.php");
/*
  if($_SESSION['login_as'] != 'admin'){
  echo 'You dont have permission to manage rma Refund.';
  exit;
} */
$rma_number = $_GET['rma_number'];
$items = rtrim($_GET['items'], ",");
$return_info = $db->func_query("SELECT
    a.*,
    b.sku,b.title,b.`quantity`,b.price,b.`return_code`,b.`reason`,b.`decision`,b.return_id, b.id as return_item_id
    FROM
    `inv_returns` a
    INNER JOIN `inv_return_items`  b
    ON (a.`id` = b.`return_id`) 
    WHERE a.rma_number='" . $rma_number . "' AND b.id IN($items)");
$order_info = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND a.order_id='" . (int) $return_info[0]['order_id'] . "'");
$emailInfo = $_SESSION['rma_info' . $rma_number];
$adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );
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
foreach ($return_info as $return_item) {
    $price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
    $productPrice += (float) $price;
    $productNames .= '<tr><td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td></tr>';
    $productDetails .= '<tr>';
    $productDetails .= '<td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td>';
    $productDetails .= '<td>'. $return_item['return_code'] . '</td>';
    $productDetails .= '<td>'. $return_item['item_condition'] . ' - ' . $return_item['item_issue'] .'</td>';
    $productDetails .= '<td>Issue Credit</td>';
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
$emailInfo['rma_products_names'] = $productNames;
$emailInfo['rma_products_Details'] = $productDetails;
$emailInfo['total_price'] = $productPrice;
if (isset($_POST['credit_code'])) {
    $data = array();
    $data['code'] = $_POST['credit_code'];
    $data['voucher_theme_id'] = 8;
    $data['message'] = $_POST['message'];
    $data['amount'] = $_POST['price'];
    $data['status'] = 1;
    $data['order_id'] = $return_info[0]['order_id'];
    $data['date_added'] = date('Y-m-d h:i:s');
    $data['from_name'] = 'PhonePartsUSA.com';
    $data['from_email'] = 'sales@phonepartsusa.com';
    $data['to_name'] = $order_info['first_name'];
    $data['to_email'] = $order_info['email'];
    $voucher_id = $db->func_array2insert("oc_voucher", $data);
    $i = 0;
    foreach (explode(",", $items) as $item) {
        $data = array();
        $data['return_id'] = $return_info[$i]['return_id'];
        $data['return_item_id'] = $return_info[$i]['return_item_id'];
        $data['order_id'] = $order_info['order_id'];
        $data['sku'] = $return_info[$i]['sku'];
        $data['price'] = $return_info[$i]['price'];
        $data['action'] = 'Store Credit';
        $data['date_added'] = date('Y-m-d h:i:s');
        $addcomment = array();
        $addcomment['comment_date'] = date('Y-m-d H:i:s');
        $addcomment['user_id'] = $_SESSION['user_id'];
        $addcomment['comments'] = linkToVoucher($voucher_id, $host_path, $_POST['credit_code']) . ' of amount $' . $data['price'] . ' Store Credit Issued For ' . linkToProduct($data['sku'], $host_path, 'target="_blank"');
        $addcomment['return_id'] = $return_info[$i]['return_id'];
        $db->func_array2insert("inv_return_comments", $addcomment);
        $db->func_array2insert("inv_return_decision", $data);
        $i++;
    }
    $data = array();
    $data['order_id'] = $return_info['order_id'];
    $data['voucher_id'] = $voucher_id;
    $data['description'] = '$' . number_format($return_info['price'], 2) . " Gift Certificate for " . $order_info['first_name'];
    $data['code'] = $_POST['credit_code'];
    $data['from_name'] = 'PhonePartsUSA.com';
    $data['from_email'] = 'sales@phonepartsusa.com';
    $data['to_name'] = $order_info['first_name'];
    $data['to_email'] = $order_info['email'];
    $data['voucher_theme_id'] = 8;
    $data['message'] = $_POST['message'];
    $data['amount'] = $_POST['price'];
    $db->func_array2insert("oc_order_voucher", $data);
    echo '<h1>Store Credit: ' . $_POST['credit_code'] . ' has been generated</h1>';
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
                    ?><br /></font>
                </div>
            <?php endif; ?>
            <br clear="all" />
            <div align="center">
                <form action="" id="myFrm" method="post">
                    <h2>Issue Refund</h2>
                    <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">
                        <tr>
                            <td>Order ID</td>
                            <td><?php echo $return_info[0]['order_id']; ?></td>
                        </tr>
                        <tr>
                            <td>Item(s)</td>
                            <td>
                                <?php
                                $amount = 0.00;
                                foreach ($return_info as $item) {
                                    echo $item['sku'] . ' - ' . $item['title'] . "<br />";
                                    $amount+=$item['price'];
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Refund Shipping</td>
                            <td>
                                <input type="checkbox" onchange="updatePrice(this);" />
                            </td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td>$<span id="amount_span"><?php echo number_format($amount, 2); ?></span><input type="hidden" name="price" value="<?php echo $amount; ?>"</td>
                        </tr>
                        <tr>
                            <td>Message:</td>
                            <td>
                                <?php $canned_message = $db->func_query_first('SELECT * FROM `inv_canned_message` WHERE `catagory` = "2" AND `type` = "Issue Refund"'); ?>
                                <?php if ($canned_message) { ?>
                                <input type="hidden" name="canned_id" value="<?= $canned_message['canned_message_id']; ?>">
                                <input type="hidden" name="title" value="<?= shortCodeReplace($emailInfo, $canned_message['title']); ?>">
                                <input type="hidden" name="subject" value="<?= shortCodeReplace($emailInfo, $canned_message['subject']); ?>">
                                <?php } else { echo 'Email Templete is not Defined'; } ?>
                                <?= (!$canned_message)? '<div style="display: none;">': ''; ?>
                                <textarea name="comment" id="comment" class="comment-box" cols="40" rows="8" style="width: 99%"><?= shortCodeReplace($emailInfo, $canned_message['message']); ?><div id="customeData"></div></textarea>
                                <?= (!$canned_message)? '</div>': ''; ?>
                            </td>
                            <script>
                                CKEDITOR.replace( 'comment' );
                            </script>
                        </tr>
                        <tr style="display: none;">
                            <td>
                                <textarea id="disclaimer"><div contenteditable="false"><?= $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `type` = 1'); ?></div></textarea>
                                <?php $src = $path .'files/sign_' . $_SESSION['user_id'] . ".png"; ?>
                                <textarea id="signature"><div contenteditable="false"><?= shortCodeReplace($adminInfo, $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `user_id` = "'. $_SESSION['user_id'] .'" AND type = 0')); ?> <?= (file_exists($src))? '<img src="'. $host_path .'files/sign_' . $_SESSION['user_id'] . '.png?'. time() .'" />': ''; ?></div></textarea>
                            </td>
                            <script type="text/javascript">
                                $(function() {
                                    $('.addsd').click(function() {
                                        var message = '';
                                        if ($('#signature_check').is(':checked')) {
                                            message = message + $('#signature').text();
                                        }
                                        if ($('#disclaimer_check').is(':checked')) {
                                            message = message + $('#disclaimer').text();
                                        }
                                        CKEDITOR.instances.comment.document.getById('customeData').setHtml(message);
                                    });
                                });
                            </script>
                        </tr>
                        <tr <?= (!$canned_message)? 'style="display: none;"': ''; ?>>
                            <td></td>
                            <td>
                                <label class="addsd" for="signature_check"><input type="checkbox" id="signature_check" /> Add Signature</label><label class="addsd" for="disclaimer_check"><input type="checkbox" id="disclaimer_check" /> Add Disclaimer</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <input type="button" name="add" value="Generate" onclick="submitForm()" />
                            </td>
                        </tr>
                    </table>
                </form>
                <script>
                    function updatePrice(obj)
                    {
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
                    function submitForm()
                    {
                        if (!confirm('Are you sure?'))
                        {
                            return false;
                        }
                        $.ajax({
                            url: "ajax_refund.php",
                            type: "POST",
                            data: {return_id:<?php echo $return_info[0]['id']; ?>, order_id: '<?php echo $return_info[0]['order_id']; ?>', items: '<?php echo $items; ?>', amount: $('input[name=price]').val(), title: $('input[name="title"]').val(), subject: $('input[name="subject"]').val(), canned_id: $('input[name="canned_id"]').val(), comment: CKEDITOR.instances.comment.getData()},
                            dataType: "json",
                            beforeSend: function () {
                                $('input[name=add]').prop('disabled', 'disabled');
                            },
                            complete: function () {
                                $('input[name=add]').prop('disabled', '');
                            },
                            success: function (json) {
                                if (json['error'])
                                {
                                    alert(json['error']);
                                    return false;
                                }
                                if (json['success'])
                                {
                                    alert(json['success']);
                                   $("input[name=save]", window.parent.document).click();
                                }
                            }
                        });
}
</script>
</div>      
</div>           
</body>
</html>