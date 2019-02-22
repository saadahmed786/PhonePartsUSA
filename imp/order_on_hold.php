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

$order_info = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND a.order_id='" . $order_id . "'");

$query_where = ($items)? "`id` IN ('" . str_replace(',', "', '", $items) . "') AND": '';

$order_items = $db->func_query("SELECT * FROM `inv_orders_items` WHERE $query_where `order_id` = '". $order_info['order_id'] ."'");

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

    $order_id = $_POST['order_id'];
    $order_details = $db->func_query_first('SELECT * FROM inv_orders WHERE order_id = "'. $order_id .'"');

    // Adding Voucher
    

    $emailInfo['customer_name'] = $order_details['customer_name'];
    $emailInfo['order_id'] = $_POST['order_id'];
    $emailInfo['email'] = $order_details['email'];
    $post = [
            "description" => 'Order on Hold',
            "subject" => 'Order #'. $_POST['order_id'] .' is set to On Hold',
            "email" => $order_details['email'],
            "name" => $order_details['customer_name'],
            "priority" => 1,
            "status" => 2,
            "action"=>'create'
            ];

            $ch = curl_init($host_path . 'freshdesk/create_ticket.php?config=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        // execute!
            $response = curl_exec($ch);

        // close the connection, release resources used
            curl_close($ch);
    if ($_POST['canned_id']) {

        $email = array();

        $src = $path .'files/canned_' . $_POST['canned_id'] . ".png";

        if (file_exists($src)) {
            $email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
        }

        $email['title'] = $_POST['title'];
        $email['subject'] = $_POST['subject'];
        $email['message'] = shortCodeReplace($emailInfo, $_POST['comment']);

        if(sendEmailDetails($emailInfo, $email))
        {
            $json['success'] ='Email sent successfully';

            // $json['success'] = "Order is put On Hold";
            // $comment = 'Order #. '. linkToOrder($_POST['order_id']) .' is put On Hold.';
            // $addcomment = array();
            // $addcomment['date_added'] = date('Y-m-d H:i:s');
            // $addcomment['user_id'] = $_SESSION['user_id'];
            // $addcomment['comment'] = $db->func_escape_string($comment);
            // $addcomment['order_id'] = $_POST['order_id'];
            // $db->func_array2insert("inv_order_history", $addcomment);
            // actionLog($comment);
        }
        else
        {
            $json['error'] = 'Email not sent, please contact admin';
        }

    } else {
        $json['error'] = 'Please fill in with email body to proceed';
    }
    

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
                <h2>Order On Hold</h2>
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
                            ?>
                        </td>
                    </tr>

                    
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
                                <table width="70%"  cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
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

                           <input type="button" name="add" value="Send Email" onclick="submitForm()" />
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

                if (!$('#reason').val()) {
                    alert('Select Reason');
                    return false;
                }
                var reimbursement = 0;



                if (!confirm('Are you sure?'))
                {
                    return false;

                }


                $.ajax({

                    url: 'order_on_hold.php',
                    type: "POST",
                    data: {order_id: '<?php echo $order_info['order_id']; ?>', reason: $('#reason').val(), items: '<?php echo $items; ?>', amount: $('input[name=price]').val(), title: $('input[name="title"]').val(), subject: $('input[name="subject"]').val(), canned_id: $('select[name="canned_id"]').val(), comment: CKEDITOR.instances.comment.getData()},
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
                                // var description = 'Order on Hold';
                                // var fsubject = 'Order #<?php echo $order_info['order_id']; ?> is set to On Hold';
                                // var useremail = '<?php echo $order_info['email']; ?>';
                                // var username = '<?php echo $order_info['customer_name']; ?>';
                                // var description = 'Order on Hold'
                                // $.ajax({
                                //     url: 'freshdesk/create_ticket.php',
                                //     type: 'POST',
                                //     dataType: 'json',
                                //     data: {description: description, subject: fsubject, email: useremail, name: username, group: 9000086662},
                                // })
                                // .always(function() {
                                    window.parent.changeOrderStatus('On Hold', 'true');
                                // });
                            }
                        }
                    });


            }
        </script>
    </div>		


</div>		     
</body>
</html>