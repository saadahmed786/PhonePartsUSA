<?php
require_once("auth.php");
include_once 'inc/functions.php';

$results = $db->func_query("Select * from inv_daily_inventory_value order by date_added desc limit 30");
?>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="include/table_sorter.css" rel="stylesheet" type="text/css" />
        
        
    <script type="text/javascript" src="js/jquery.min.js"></script>
    
        
        <title>Daily Total Inventory Cost</title>
    </head>
<div><?php include_once 'inc/header.php';?></div><br><br>
<h2 align="center">Daily Total Inventory Cost</h2>
      
    <div>
      <table width="40%" cellspacing="0" cellpadding="5px" border="1" align="center">
      <thead>
        <tr style="background-color:#e5e5e5;">
          <th>Date</th>
          <th>Total Inventory Cost</th>
          <th>C.O.G.S</th>
          <th>C.O.G.R</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($results as $result) { ?>
        <tr>
          <td align="center"><?php echo $result['date_added']; ?></td>
          <td align="center">$<?php echo number_format($result['inventory_cost'],2); ?></td>
          <td align="center">$<?php echo number_format($result['total_sale'],2); ?></td>
          <td align="center">$<?php echo number_format($result['total_received'],2); ?></td>
        </tr>      
     <?php } ?>    
      </tbody>
    </table>
    </div>
  </body>
</html>