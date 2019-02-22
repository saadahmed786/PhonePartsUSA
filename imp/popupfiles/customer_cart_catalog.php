<?php
require_once("../config.php");
include_once '../inc/functions.php';


if ($_POST['search']){
  $cust_email = $_POST['cust_email'];
  $customer_details = $db->func_query_first('SELECT * from oc_customer where email = "'.$cust_email.'"');
$address_detail = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".(int)$customer_details['address_id']."'");
$product_ids = array();
if (unserialize($customer_details['cart'])) {
  $items = unserialize($customer_details['cart']);
  foreach ($items as $key => $item) {
     $product_ids[] = $key;
  }
  $product_ids = implode(',', $product_ids);
  $products = $db->func_query('SELECT p.product_id,p.sku,p.quantity,pd.name as title from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where p.product_id IN ('.$product_ids.') ');
  $product_details  = array();
  foreach ($products as $key => $product) {
    $product_details[$key] = $product;
    $product_details[$key]['cart_price'] = $items[$product['product_id']] * getOCItemPrice($product['product_id']) ;
    $product_details[$key]['cart_qty'] = $items[$product['product_id']];
  }
  //testObject($product_details);
} else {
  echo "No Items Found";
  exit;
}

}







?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
      <script type="text/javascript" src="../js/jquery.min.js"></script>
      <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
      <link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
   </head>
<body>

<div align="center">
<?php if ($product_details) { ?>

    <table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
      <thead>
        <tr style="background-color:#e5e5e5;">
        <th><input type="checkbox" name="select_all" onclick="selectAll()" id="select_all" /></th>
          <th>SKU</th>
          <th>Title</th>
          <th>Quantity</th>
          <th>Cart Quantity</th>
          <th>Unit Price</th>
          <th>Cart Total</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($product_details as $p) { 
        if ($p['quantity']<=0) {?>
          <tr style="color: red;">
        <?php }else{ ?>
        <tr>
        <?php }
        ?>
        <td><input type="checkbox" class="selection" name="item_checkbox" value="<?php echo $p['product_id'];?>" /></td>
         <td align="center"><?php echo $p['sku']; ?><input type="hidden" id="sku_title<?php echo $p['product_id'];?>" value="<?php echo $p['sku']; ?> - <?php echo utf8_encode($p['title']); ?>"></td>
         <td align="center"><?php echo utf8_encode($p['title']); ?></td>
         <td align="center"><?php echo $p['quantity']; ?></td>
         <td align="center"><input type="text" id="item_quantity<?php echo $p['product_id'];?>" name="item_quantity<?php echo $p['product_id'];?>" value="<?php echo  $p['cart_qty'];?>">
         <input type="hidden" id= "stock_quantity<?php echo $p['product_id'];?>" name="stock_quantity<?php echo $p['product_id'];?>" value="<?php echo  $p['quantity'];?>" >
         </td>
         <td align="center">$<?php echo number_format(getOCItemPrice($p['product_id']),2) ;?>
           <input type="hidden" name="item_price<?php echo $p['product_id'];?>" value="<?php echo  getOCItemPrice($p['product_id']);?>" id="item_price<?php echo $p['product_id'];?>">
         </td>
         <td align="center">$<?php echo number_format($p['cart_price'],2); ?></td>
       </tr>
       <?php } ?>
       <input type="hidden" id="cust_email" name="cust_email" value="<?php echo $cust_email; ?>">
       <input type="hidden" name="cust_firstname" value="<?php echo $customer_details['firstname']; ?>">
       <input type="hidden" name="cust_lastname" value="<?php echo $customer_details['lastname']; ?>">
       <input type="hidden" name="cust_phone" value="<?php echo $customer_details['telephone']; ?>">
       <input type="hidden" name="cust_group_id" value="<?php echo $customer_details['customer_group_id']; ?>">
       <input type="hidden" name="cust_add_1" value="<?php echo $address_detail['address_1']; ?>">
       <input type="hidden" name="cust_add_2" value="<?php echo $address_detail['address_2']; ?>">
       <input type="hidden" name="cust_city" value="<?php echo $address_detail['city']; ?>">
       <input type="hidden" name="cust_state" value="<?php echo $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$address_detail['zone_id']."'"); ?>">
       <input type="hidden" name="cust_postcode" value="<?php echo $address_detail['postcode']; ?>">
       <input type="hidden" name="cust_id" value="<?php echo $customer_details['customer_id']; ?>">

       <tr>
         <td colspan="7" align="center">
           <input type="button" class="button" onclick="addToCart();" name="customer_cart" value="Complete">
         </td>
       </tr>          
     </tbody>
    </table>
<?php } else {?>
<form method="post" action="">
  <table width="30%" cellspacing="0" cellpadding="5px" border="0" align="center">
  <tr>
    <th>Customer email</th>
    <td><input type="text" required name="cust_email"></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" class= "button" value="Search" name="search"></td>
  </tr>
  </table>
</form>
<?php } ?>
</div>
<script>
function addToCart()
{
  stockTable = [];
  ooos_check = false;
  $('input[type=checkbox]').each(function () {
    
           if (this.checked) {
            var product_id = $(this).val();
            var qty = $('#item_quantity'+product_id).val();
            var stock_qty = $('#stock_quantity'+product_id).val();
            if (stock_qty <= 0) {
              stockTable.push($('#sku_title'+product_id).val());
              ooos_check = true;
            }
            var price = $('#item_price'+product_id).val();
               //parent.sendCartAjax(product_id,qty,price,0);

           }
});
  if (ooos_check) {
    var conf = '';
    var index = 1;
    for (var i = stockTable.length - 1; i >= 0; i--) {
      conf += index+' - '+stockTable[i]+'\n';
      index++;

    }
    if (!confirm(conf+'\nThese Items Are out of stock\nDo you want to continue?')) {
      return false;

    } else {
       $('input[type=checkbox]').each(function () {
    
           if (this.checked) {
            var product_id = $(this).val();
            var qty = $('#item_quantity'+product_id).val();
            var price = $('#item_price'+product_id).val();
            parent.sendCartAjax(product_id,qty,price,0,1);

           }
});
      
    }

  }else {
    $('input[type=checkbox]').each(function () {
    
           if (this.checked) {
            var product_id = $(this).val();
            var qty = $('#item_quantity'+product_id).val();
            var price = $('#item_price'+product_id).val();
            parent.sendCartAjax(product_id,qty,price,0);

           }
});
  }
$('#customerEmail',window.parent.document).val($('#cust_email').val());
parent.jQuery.fancybox.close();
}
function selectAll(){
  if($("#select_all").prop('checked') == true){
    $('input[type=checkbox]').prop("checked",true);
  } else {
    $('input[type=checkbox]').prop("checked",false);
  }
}
</script>
</body>
</html>