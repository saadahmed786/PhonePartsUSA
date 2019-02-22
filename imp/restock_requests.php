<?php
require_once("auth.php");
include_once 'inc/functions.php';
//echo "SELECT * FROM oc_out_of_stock_notify WHERE YEAR(notified_date) = YEAR(NOW())"; exit;

 $recent_requests = $db->func_query("SELECT * FROM oc_out_of_stock_notify WHERE YEAR(enquiry_date) = YEAR(NOW()) order by enquiry_date DESC");

 $demands = $db->func_query("SELECT distinct(a.product_id) as pid, b.name, (SELECT COUNT(distinct(email)) from oc_out_of_stock_notify WHERE product_id = pid) AS count FROM oc_out_of_stock_notify a, oc_product_description b where a.product_id = b.product_id and b.language_id = (SELECT language_id FROM `oc_language` WHERE code = (SELECT `value` FROM `oc_setting` WHERE `key` = 'config_admin_language')) and a.notified_date IS NULL ORDER BY count DESC LIMIT 100");

?>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="include/table_sorter.css" rel="stylesheet" type="text/css" />
        
        
    <script type="text/javascript" src="js/jquery.min.js"></script>
    
        
        <title>Restock Requests</title>
    </head>
<div><?php include_once 'inc/header.php';?></div><br><br>
<h2 align="center">Restock Requests</h2>
<body>
  <?php if($_SESSION['message']) { ?>
  <div align="center"><br />
    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
  </div>
  <?php } ?>
  <table width="90%">
    <tr>
      <td>
      
        <span><h3 align="center">Recent Requests</h3></span>
        <div style="height:500px;width:870px;overflow:auto;">
          <table align="center" border="1" width="90%" cellpadding="5" cellspacing="0">
            <thead>
              <th>SKU</th>
              <th >Product Name</th>
              <th >Customer Email</th>
              <th >Customer Alert Set On</th>
              <th >Notified Date</th>
            </thead>
            <tbody>
            <?php foreach($recent_requests as $req) { 
              $sku = $db->func_query_first_cell('SELECT sku FROM oc_product WHERE product_id = "'.$req['product_id'].'"');
              $name = $db->func_query_first_cell('SELECT name FROM oc_product_description WHERE product_id = "'.$req['product_id'].'"');?>
              <tr>
                <td><a href="product/<?php echo $sku; ?>"><?php echo $sku; ?></a></td>
                <td><?php echo $name; ?></td>
                <td><?php echo $req['email']; ?></td>
                <td><?php echo $req['enquiry_date']; ?></td>
                <td><?php echo $req['notified_date']; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </td>
      <td>
        <span><h3 align="center">Products in Demand</h3></span>
        <div style="height:500px;width:450px;overflow:auto;">
          <table align="center" border="1" width="90%" cellpadding="5" cellspacing="0">
            <thead>
              <th>SKU</th>
              <th >Product Name</th>
              <th >Count</th>
            </thead>
            <tbody>
            <?php foreach($demands as $demand) {
              $sku = $db->func_query_first_cell('SELECT sku FROM oc_product WHERE product_id = "'.$demand['pid'].'"');?>
              <tr>
                <td><a href="product/<?php echo $sku; ?>"><?php echo $sku; ?></a></td>
                <td><?php echo $demand['name']; ?></td>
                <td><?php echo $demand['count']; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </td>
    </tr> 
  </table>
  
</body>
</html>