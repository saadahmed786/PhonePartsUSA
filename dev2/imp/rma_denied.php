<?php
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");
/*
  if($_SESSION['login_as'] != 'admin'){
  echo 'You dont have permission to denie RMA.';
  exit;
} */
$rma_number = $_GET['rma_number'];
$items = rtrim($_GET['items'], ",");
$returnTemp = 'Denied';



$return_info = $db->func_query("SELECT
    a.*,
    b.sku,b.title,b.`quantity`,b.price,b.`return_code`,b.`reason`,b.`decision`,b.return_id,b.item_condition,b.item_issue,b.id as `item_id`
    FROM
    `inv_returns` a
    INNER JOIN `inv_return_items`  b
    ON (a.`id` = b.`return_id`) 

    WHERE a.rma_number='" . $rma_number . "' AND b.id IN($items)");
$order_info = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND a.order_id='" . (int) $return_info[0]['order_id'] . "'");


$emailInfo = $_SESSION['rma_info' . $rma_number];
$adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );

$item_conditions = array(array('id' => 'Good For Stock', 'value' => 'Good For Stock'),
    array('id' => 'Item Issue', 'value' => 'Item Issue'),
    array('id' => 'Customer Damage', 'value' => 'Customer Damage'),
    array('id' => 'Not Tested', 'value' => 'Not Tested'),
    array('id' => 'Not PPUSA Part', 'value' => 'Not PPUSA Part'),
    array('id' => 'Over 60 days', 'value' => 'Over 60 days'),
    array('id' => 'Shipping Damage', 'value' => 'Shipping Damage')
    );
$productPrice = 0;
$productNames = '<table><tbody>';
$productDetails = '<table width="100%">';
$productDetails .= '<thead><tr>';
$productDetails .= '<th width="35%">Name</th>';
$productDetails .= '<th width="10%">Return Reason</th>';
$productDetails .= '<th width="10%">Condition</th>';
$productDetails .= '<th width="10%">Decision</th>';
// $productDetails .= '<th width="10%">Amount</th>';
$productDetails .= '<th width="35%">Images</th>';
$productDetails .= '</tr></thead><tbody>';
foreach ($return_info as $key => $return_item) {
    if ($key == 0) {
        $returnTemp = $return_item['item_condition'];
    }
    if ($returnTemp != $return_item['item_condition']) {
        $returnTemp = 'Denied';
    }
    $price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
    $productPrice += (float) $price;
    $productNames .= '<tr><td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td></tr>';
    $productDetails .= '<tr>';
    $productDetails .= '<td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td>';
    $productDetails .= '<td>'. $return_item['return_code'] . '</td>';
    $productDetails .= '<td>'. $return_item['item_condition'] . ' - ' . $return_item['item_issue'] .'</td>';
    $productDetails .= '<td>Denied</td>';
    // $productDetails .= '<td>'. $price .'</td>';
    $images = $db->func_query("select * from inv_return_item_images where return_item_id = '" . $return_item['item_id'] . "'");
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


if ($_POST['save']) {

    $i=0;
    foreach(explode(",",$items) as $item)
    {



        $data = array();
        $data['return_id'] = $return_info[$i]['return_id'];
        $data['return_item_id'] = $return_info[$i]['item_id'];
        $data['order_id'] = $order_info['order_id'];
        $data['sku'] = $return_info[$i]['sku'];
        $data['price'] = $return_info[$i]['price'];
        $data['action'] = 'Denied';
        $data['date_added'] = date('Y-m-d H:i:s');

        $addcomment = array();
        $addcomment['comment_date'] = date('Y-m-d H:i:s');
        $addcomment['user_id'] = $_SESSION['user_id'];
        $addcomment['comments'] = 'Denied For ' . linkToProduct($data['sku'], $host_path, 'target="_blank"');
        $addcomment['return_id'] = $return_info[$i]['return_id'];

        $db->func_array2insert("inv_return_comments", $addcomment);

        $db->func_array2insert("inv_return_decision",$data);


        $data = array();

        $data['decision'] = 'Denied';


        $db->func_array2update("inv_return_items",$data,'id="'.$item.'"');
        $i++;
    }

    if ($_POST['canned_id']) {

        $email = array();

        $src = $path .'files/canned_' . $_POST['canned_id'] . ".png";

        if (file_exists($src)) {
            $email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
        }

        $email['title'] = $_POST['title'];
        $email['subject'] = $_POST['subject'];
        $email['number'] = array('title' => 'RMA Number', 'value' => $emailInfo['rma_number']);
        $email['message'] = shortCodeReplace($emailInfo, $_POST['comment']);

        sendEmailDetails($emailInfo, $email);

        $agent_id = $db->func_query_first_cell("SELECT user_id FROM inv_customers WHERE LOWER(email)='".strtolower($emailInfo['email'])."'");
        if($agent_id!='0')
        {
            $agent_email = $db->func_query_first_cell("SELECT email FROM inv_users WHERE id='".$agent_id."'");
            $emailInfo['email'] = $agent_email;
            sendEmailDetails($emailInfo, $email);

        }

    } else {
        $_SESSION['message'] = 'Email not sent';
    }

    echo '<script> window.parent.location.reload();</script>';exit;

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
                <form action="" id="myFrm" method="post" onsubmit="submitForm();">
                    <h2>Deny Items</h2>
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

                                    echo $item['sku'] . ' - ' . $item['title'] . ' || ' . $item['item_condition'] . "<br />";
                                    $amount+=$item['price'];
                                }
                                ?>


                            </td>
                        </tr>
                        <!-- <tr>
                            <td>Condition</td>
                            <td>
                                <select class="condition" name="item_condition">
                                    <option value="">Select One</option>

                                    <?php foreach ($item_conditions as $item_condition): ?>
                                        <option value="<?php echo $item_condition['id'] ?>" <?php if ($item_condition['id'] == $return_item['item_condition']): ?> selected="selected" <?php endif; ?>>
                                            <?php echo $item_condition['value'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr> -->
                        <tr>
                            <td>Amount</td>
                            <td>$<span id="amount_span"><?php echo number_format($amount, 2); ?></span>
                            </tr>

                            <tr>
                                <td>Message:</td>
                                <td>
                                    <?php $canned_message = $db->func_query_first('SELECT * FROM `inv_canned_message` WHERE `catagory` = "2" AND `type` = "'. $returnTemp .'"'); ?>
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
                                    <input type="submit" name="save" value="Save" />
                                </td>
                            </tr>
                        </table>
                    </form>
                    <script>
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

                            if (!confirm('Are you sure?')) {
                                return false;
                            } else {
                                return true;
                            }

                            return true;

                        }
                    </script>
                </div>		


            </div>		     
        </body>
        </html>