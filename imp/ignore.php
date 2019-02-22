<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

if($_POST['save']){
	$day = $_POST['day'];
	if($day == 'all'){
		$db->db_exec("Update inv_orders SET ignored = 0");
	}
	else{
		$day = (int)$day;
		$db->db_exec("Update inv_orders SET ignored = 0 where datediff(now(),order_date) <= $day");
	}
	
	if($_REQUEST['m'] == 1){
	    $_SESSION['message'] = "Ignore condition is removed";
	    header("Location:".$host_path."ignore.php");
	    exit;
	}
}

if($_POST['markunignore']){
	$order_id_str = array();
	
	//check ignore order is kit sku contains or not. if yes the may new kit sku mapping added now and order ignored
	foreach($_POST['order'] as $order_id){
		$order_items = $db->func_query("select * from inv_orders_items where order_id = '$order_id'");
		
		foreach($order_items as $order_item){
			$order_item_sku = $order_item['product_sku'];
			
			//check if SKU is KIT SKU
			$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$order_item_sku'");
			if($kit_skus){
				$kit_skus_array = explode(",",$kit_skus['linked_sku']);
				foreach($kit_skus_array as $kit_skus_row){
					$orderItemData = $order_item;
					unset($orderItemData['id']);
					$orderItemData['product_sku']  = $kit_skus_row;
					$db->func_array2insert("inv_orders_items",$orderItemData);
				}
				
				//mark kit sku need_sync on all marketplaces
				$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$order_item_sku'");
				$db->db_exec("delete from inv_orders_items where order_id = '$order_id' and product_sku = '$order_item_sku'");
			}
		}
		
		$order_id_str[] = "'". $order_id ."'";
	}
	
	if($order_id_str){
		$order_id_str = implode(",",$order_id_str);
		$db->db_exec("update inv_orders SET ignored = 0 where order_id in ($order_id_str)");
		
		$_SESSION['message'] = count($order_id_str). " Orders marked as ignored";
	}
	else{
		$_SESSION['message'] = "0 Orders marked as ignored";
	}
	
	header("Location:ignore.php");
	exit;
}

if(isset($_REQUEST['submit'])){
    $inv_query   = '';
    $orderType   = $_REQUEST['ordertype'];

    $parameters  = $_SERVER['QUERY_STRING'];

    if($orderType == "Completed"){
        $conditions = array();
         
        $start_date = $db->func_escape_string($_REQUEST['start_date']);
        $end_date   = $db->func_escape_string($_REQUEST['end_date']);
        $filterBy   = $db->func_escape_string($_REQUEST['order']);
        $order_number = $db->func_escape_string(trim($_REQUEST['order_number']));

        if(@$start_date){
            $conditions[] =  " order_date >= '$start_date' ";
        }

        if(@$end_date){
            $conditions[] =  " order_date <= '$end_date' ";
        }

        if(@$filterBy !='all'){
            $conditions[] =  " store_type = '$filterBy' ";
        }

        if($order_number){
            $condition_sql = " Lower(order_id) = Lower('$order_number') OR Lower(so_number) = Lower('$order_number') ";
        }
        else{
            $condition_sql = implode(" AND " , $conditions);
        }
        
        if(!$condition_sql){
            $condition_sql = " ignored = 1 and fishbowl_uploaded = 0 and order_date > '2013-09-14 00:00:00' ";
        }
        else{
        	$condition_sql .= " and ignored = 1 and fishbowl_uploaded = 0 and order_date > '2013-09-14 00:00:00' ";
        }
        
        $inv_query = "Select * from inv_orders where $condition_sql  group by order_id order by order_date DESC";
    }
    elseif($orderType == "Return"){
        $conditions = array();
         
        $start_date = $db->func_escape_string($_REQUEST['start_date']);
        $end_date   = $db->func_escape_string($_REQUEST['end_date']);
        $filterBy   = $db->func_escape_string($_REQUEST['order']);
        $order_number = $db->func_escape_string($_REQUEST['order_number']);

        if(@$start_date){
            $conditions[] =  " order_date >= '$start_date' ";
        }

        if(@$end_date){
            $conditions[] =  " order_date <= '$end_date' ";
        }

        if(@$filterBy !='all'){
            $conditions[] =  " store_type = '$filterBy' ";
        }

        $condition_sql = implode(" AND " , $conditions);
        
        if(!$condition_sql){
            $condition_sql = " ignored = 1 and fishbowl_uploaded = 0 and order_date > '2013-09-14 00:00:00' ";
        }
    	else{
        	$condition_sql .= " and ignored = 1 and fishbowl_uploaded = 0 and order_date > '2013-09-14 00:00:00' ";
        }
        
        $inv_query = "Select * from inv_return_orders where $condition_sql group by order_id order by order_date DESC";
    }
}
else{
    $inv_query = "select * from inv_orders where ignored = 1 and fishbowl_uploaded = 0 and order_date > '2013-09-14 00:00:00' group by order_id order by order_date DESC";
}

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 20;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "ignore.php",$page);
$inv_orders = $db->func_query($splitPage->sql_query);

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Ignore Orders</title>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        
        <script type="text/javascript">
        	function checkall(checked){
            	if(checked){
                	jQuery(".orders").prop("checked",true);
            	}
            	else{
            		jQuery(".orders").prop("checked",false);
            	}
        	}
        </script>
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <br /><br />
        <div align="center">
        	<h1>Select days and click to submit mark orders as unignored</h1>
	        <form method="post">
	        	 Select Day:
	        	 <select name="day" required>
	        	 	 <option value="">Select One</option>
	        	 	 <option value="1">1 Day</option>
	        	 	 <option value="3">3 Day</option>
	        	 	 <option value="7">7 Day</option>
	        	 	 <option value="15">15 Day</option>
	        	 	 <option value="30">30 Day</option>
	        	 	 <option value="all">All</option>
	        	 </select>
	        	 
	        	 <input type="submit" name="save" value="Submit" />
	        </form>
	     </div>
	     
	     <br />
	     
	     <div align="center">
	     	<form method="post">
	     		  <table>
	     		  		<tr>
	                        <td>
	                            <label for="order">Filter By Store Type :</label>
	                            <select id="order" name="order" style="width: 145px;">
	                                <option value="all">All</option>
	                                <option value="ebay" <?php if($_REQUEST['order']=='ebay'):?> selected='selected' <?php endif;?>>eBay</option>
	                                <option value="amazon" <?php if($_REQUEST['order']=='amazon'):?> selected='selected' <?php endif;?>>Amazon</option>
	                                <option value="web" <?php if($_REQUEST['order']=='web'):?> selected='selected' <?php endif;?>>Web</option>
	                                <option value="channel_advisor" <?php if($_REQUEST['order']=='channel_advisor'):?> selected='selected' <?php endif;?>>Channel Advisor</option>
	                                <option value="bigcommerce" <?php if($_REQUEST['order']=='bigcommerce'):?> selected='selected' <?php endif;?>>Bigcommerce</option>
	                            </select>
	                        </td>

	                        <td>
	                            <label for="order">Order Type :</label>
	                            <select id="ordertype" name="ordertype" style="width: 130px;">
	                                <option value="Completed" <?php if($_REQUEST['ordertype']=='Completed'):?> selected='selected' <?php endif;?>>Completed/Shipped</option>
	                                <option value="Return" <?php if($_REQUEST['ordertype']=='Return'):?> selected='selected' <?php endif;?>>Return/Refund</option>
	                            </select>
	                        </td>
	                        
	                        <td>
	                            <label for="start_date">Order Number:</label>
	                            <input type="text" name="order_number" value="<?php echo @$_REQUEST['order_number'];?>" />
	                       </td>
	                   </tr>
	                   
	                   <tr>     
	                       <td>
	                            <label for="start_date">Start Date:</label>
	                            <input type="text" class="datepicker" value="<?php echo @$_REQUEST['start_date'];?>" name="start_date" size="20" style="width: 110px;" readonly="readonly" />
	                       </td>
	
	                        <td>
	                            <label for="end_date" style="margin-left: 30px;" valign="top">End Date:</label> 
	                            <input type="text" class="datepicker" value="<?php echo @$_REQUEST['end_date'];?>" name="end_date" size="20" style="width: 110px;" readonly="readonly" />
	                        </td>
	
	                        <td><input type="submit" value="Search" name="submit" style="margin: 10px 0 0 60px"></td>
	                  </tr>
	     		  </table>
	     		  
	     		  <br />
	     		  	
	              <?php if($inv_orders):?>
	                  <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center">
	                        <thead>
	                              <tr>
	                                  <th><input type="checkbox" name="selectall" value="1" onclick="checkall(this.checked);" /></th>
	                 	              <th>Order ID</th>
	                 	              <th>Error</th>
	                                  <th>Order Date</th>
	                                  <th>Email</th>
	                                  <th>Store Type</th>
	                                  <th>Try Count</th>
	                                  <th>Fishbowl Uploaded</th>
	                               </tr>
	                         </thead>
	                         <?php $i = $splitPage->display_i_count();
	                               foreach($inv_orders as $order):?>
	                                   <tr id="<?php echo $order['id'];?>">
	                                       <td align="center">
	                                          	<input type="checkbox" class="orders" name="order[]" value="<?php echo @$order['order_id'];?>" />
	                                       </td>
	                                                
	                                       <td align="center"><a href="viewOrderDetail.php?order=<?php echo @$order['order_id'];?>"><?php echo @$order['order_id'];?></a></td>
	                                       
	                                       <td align="center"><a href="error_logs.php?order_id=<?php echo @$order['order_id'];?>">View</a></td>
	                                                
	                                       <td align="center"><?php americanDate($order['order_date']);?></td>
	                                               
	                                       <td align="center"><?php echo linkToProfile($order['email'], $host_path);?></td>
	                                                
	                                       <td align="center"><?php echo @$order['store_type'];?></td>
	                                                
	                                       <td align="center"><?php echo @$order['try_count'];?></td>
	                                                
	                                       <td align="center"><?php echo (@$order['fishbowl_uploaded']) ? 'Yes' : 'No';?></td>
	                                   </tr>
	                         <?php $i++; endforeach; ?>
	                   </table>
	                   
	                   <div align="center">
	                   		<input type="submit" name="markunignore" value="Mark as unignore" />
	                   </div>
	              <?php else : ?> 
	                            <div align="center">
	                            	Order Doesn't Exist
	                            </div>	
	             <?php endif;?>
             </form>
             
             <div align="center">       
                <br />
                <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");
                  	  print "&nbsp;";
                      echo $splitPage->display_links(10,$parameters);
                ?>
            </div>             
	     </div>   
     </body>
</html>