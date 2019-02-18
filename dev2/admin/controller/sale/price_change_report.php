<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
page_permission('customers');
if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}

if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$sku  = $db->func_escape_string($_REQUEST['sku']); 




$where = array();
$having = array();

if($sku){
	$where[] = " sku='$sku' ";
	$parameters[] = "sku=$sku";
	
}



if($where){
	$where = implode(" AND ", $where);
}
else{
	$where = "1 = 1";
}




$inv_query = "SELECT * FROM inv_price_change_history WHERE $where ORDER BY date_added";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "price_change_report.php",$page);

$prices  = $db->func_query($splitPage->sql_query);
if($parameters){
	$parameters = implode("&",$parameters);
}
else{
	$parameters = '';
}
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="include/calendar.css" rel="stylesheet" type="text/css" />
        <link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="include/calendar.js"></script>
        <script type="text/javascript" src="include/calendar-en.js"></script>
        <script type="text/javascript" src="include/calhelper.js"></script>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <title>Price Change Report</title>
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div align="center">
	        <form name="order" action="" method="get">
	             <table width="90%" cellpadding="10" border="1"  align="center">
	             	  <tr>
	             	  	 <td>SKU</td>
	             	  	 <td>
	             	  	 	 <input type="text" style="width:100px" name="sku" value="<?php echo @$_REQUEST['sku'];?>" />
	             	  	 </td>
	             	  	 
	             	  	 
                          
	             	  	 
                         
	             	  	 
	             	  	 <td><input type="submit" name="search" value="Search" class="button" /></td>
	             	  </tr>   
	             </table>
	        </form>
	        
	        <table border="1" cellpadding="10" cellspacing="0" width="80%">
        		<tr style="background:#e5e5e5;">
        			<th>S.N.</th>
        			<th>Date Completed</th>
                    <th>SKU</th>
                    <th>Item Name</th>
        			<th>Shipment No.</th>
        			<th>Raw Cost</th>
                    <th>Change</th>
                    <th>True Cost</th>
                    
                    
        			
        	   </tr>
		       <?php if($prices):?>
		       		<?php foreach($prices as $i => $price):?>
                    
                    <?php
					$previous_cost = $db->func_query_first("SELECT raw_cost FROM inv_product_costs WHERE sku='".$price['sku']."' ORDER BY id DESC limit 1,1");
					$cost_difference = (float)$previous_cost - (float)$price['raw_cost'];
					$true_cost = ($price['raw_cost']+$price['shipping_fee']) / $price['ex_rate'];
					?>
					       <tr>
					       		<td align="center"><?php echo $i+1;?></td>
			        			<td align="center"><?php echo date('m/d/Y h:ia',strtotime($price['date_added']))?></td>
			        			<td align="center"><?php echo $price['sku']?></td>
                                <td align="center"><?php echo $db->func_query_first_cell("SELECT b.name FROM oc_product a,oc_product_description b WHERE a.product_id=b.product_id AND  a.sku='".$price['sku']."'");?></td>
                                <td align="center"><a href="view_shipment.php?shipment_id=<?php echo $price['shipment_id'];?>"><?php echo $db->func_query_first_cell("SELECT package_number FROM inv_shipments WHERE id='".$price['shipment_id']."'");?></a></td>
			        			<td align="center"><?php echo number_format($price['raw_cost'],2)?></td>
                                <td align="center"><span class="tag <?php echo ($cost_difference<0?'red-bg':($cost_difference>0?'green-bg':''));?>"><?php echo number_format($cost_difference,2);?>
                                <td align="center">$<?php echo number_format((float)$true_cost,2)?></td>
                                
					       </tr>
					<?php endforeach;?>       
		       <?php endif;?>
		       
		       	<tr>
                  	  <td colspan="4" align="left">
	                      <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                      </td>
                      
                      <td colspan="7" align="right">
	                      <?php  echo $splitPage->display_links(10,$parameters); ?>
                      </td>
                </tr>
	        </table>
        </div>
    </body>
</html>