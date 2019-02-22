<?php
require_once("../config.php");
include_once '../inc/functions.php';

$cust_email = $_GET['cust_email'];
$customer_details = $db->func_query_first('SELECT * from oc_customer where email = "'.$cust_email.'"');
if (unserialize($customer_details['cart'])) {
  $items = unserialize($customer_details['cart']);
  // $cart_total = $db->func_query_first_cell('SELECT SUM(price) from oc_product where product_id in ('.$items.')');
  $qty = 0;
  $cart_total = 0.00;
  foreach($items as $key => $item)
  {
    $qty+=(int)$item;
    $itemPrice = getOCItemPrice($key);
    $cart_total = (float)$cart_total + ((float)$itemPrice * $item);
  }
}



?>

<div>
    <table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
      <thead>
        <tr style="background-color:#e5e5e5;">
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email Address</th>
          <th># of items</th>
          <th>Cart Total</th>
          <th>Last Activity</th>
          <?php if($customer_details['customer_id']>0) { ?>
          <th></th>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
        <tr>
         <td align="center"><?php echo $customer_details['firstname']; ?></td>
         <td align="center"><?php echo $customer_details['lastname']; ?></td>
         <td align="center"><?php echo $cust_email; ?></td>
         <td align="center"><?php echo $qty ;?></td>
         <td align="center"><?php echo $cart_total; ?></td>
         <td align="center"><?php echo ""; ?></td>
         <?php if($customer_details['customer_id']>0) { ?>
         <td align="center"> <input type="button" value="Login as Customer" class="button" onclick="customerOCLogin('<?=$customer_details['customer_id'];?>','<?=md5($customer_email);?>')"> </td>
         <?php } ?>
       </tr>          
     </tbody>
    </table>
</div>
<script type="text/javascript">
  function customerOCLogin(customer_id,salt)
  {
    if(!confirm('Are you sure want to access customer account?'))
    {
      return false;
    }
    ((this.value !== '') ? window.open('../../../index.php?route=account/login/backdoor&customer_id='+customer_id+'&salt='+salt) : null); this.value = '';
  }
</script>
</body>
</html>