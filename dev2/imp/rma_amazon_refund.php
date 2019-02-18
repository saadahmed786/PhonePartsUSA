<?php
require_once("auth.php");

/*
  if($_SESSION['login_as'] != 'admin'){
  echo 'You dont have permission to manage amazon refunds.';
  exit;
  } */
$rma_number = $_GET['rma_number'];
$items = rtrim($_GET['items'], ",");


$return_info = $db->func_query("SELECT
a.*,
b.sku,b.title,b.`quantity`,b.price,b.`return_code`,b.`reason`,b.`decision`,b.return_id
FROM
    `inv_returns` a
    INNER JOIN `inv_return_items`  b
        ON (a.`id` = b.`return_id`) 
		
		WHERE a.rma_number='" . $rma_number . "' AND b.id IN($items)");
$order_info = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND a.order_id='" . (int) $return_info[0]['order_id'] . "'");

if (isset($_POST['credit_code'])) {}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Refund</title>
        <script type="text/javascript" src="js/jquery.min.js"></script>
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
                            <td><input type="checkbox" id="refundShipping" onchange="updatePrice(this);" />

                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td>$<span id="amount_span"><?php echo number_format($amount, 2); ?></span><input type="hidden" name="price" value="<?php echo $amount; ?>"</td>
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

var isChecked = $('#refundShipping').is(':checked') ? 1:0; 
                        $.ajax({
                            url: "ajax_amazon_refund.php",
                            type: "POST",
                            data: {return_id:<?php echo $return_info[0]['id']; ?>, order_id: '<?php echo $return_info[0]['order_id']; ?>', items: '<?php echo $items; ?>', refundShipping:isChecked },
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
                                    parent.completeReturn();

                                }
                            }
                        });


                    }
                </script>
            </div>		


        </div>		     
    </body>
</html>