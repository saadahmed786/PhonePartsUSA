<?php
include_once '../auth.php';
include_once '../inc/functions.php';
include_once '../inc/split_page_results.php';
page_permission("buyback");
if((int)$_GET['shipment_id'] and $_GET['action'] == 'delete' && $_SESSION['delete_shipment']){
	$shipment_id = (int)$_GET['shipment_id'];
	$db->db_exec("delete from inv_buyback_boxes where id = '$shipment_id'");
	
	$_SESSION['message'] = "Shipment Box is deleted";
	header("Location:box_shipments.php");
	exit;
}
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

if((int)$_GET['shipment_id'] and $_GET['action'] == 'complete' && $_SESSION['edit_received_shipment']){
	$shipment_id = (int)$_GET['shipment_id'];
	
	$shipment_detail = $db->func_query_first("select * from inv_buyback_boxes where id = '$shipment_id'");
	if(!$shipment_detail['package_number']){
		$_SESSION['message'] = "Package number is required.";
		header("Location:addedit_boxes.php?shipment_id=$shipment_id");
		exit;
	}
	
	$db->db_exec("update inv_buyback_boxes SET status = 'Completed'  where id = '$shipment_id'");
	
	$_SESSION['message'] = "Shipment status is changed to Completed";
	header("Location:box_shipments.php");
	exit;
}

if((int)$_GET['shipment_id'] and $_GET['action'] == 'ship' && $_SESSION['box_ship']){
  $shipment_id = (int)$_GET['shipment_id'];
  
  $shipment_detail = $db->func_query_first("select * from inv_buyback_boxes where id = '$shipment_id'");
  if(!$shipment_detail['package_number']){
    $_SESSION['message'] = "Package number is required.";
    header("Location:addedit_boxes.php?shipment_id=$shipment_id");
    exit;
  }
  
  $db->db_exec("update inv_buyback_boxes SET status = 'Shipped'  where id = '$shipment_id'");
  
  $_SESSION['message'] = "Shipment status is changed to Shipped";
  header("Location:box_shipments.php");
  exit;
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
//print_r($db->func_escape_string($_GET['number']));exit;
if($_GET['number']){
	$number = $db->func_escape_string($_GET['number']);
  if ($_SESSION['group']=='Vendor' || $_SESSION['group']=='Servicer' ) {
      $inv_query  = "select * from inv_buyback_boxes where package_number like '%$number%' AND servicer = '".$_SESSION['user_id']."'";
    }	else {
	$inv_query  = "select * from inv_buyback_boxes where package_number like '%$number%'";
    }
}
else{
  if ($_SESSION['group']=='Vendor' || $_SESSION['group']=='Servicer') {
      $inv_query  = "select * from inv_buyback_boxes where servicer = '".$_SESSION['user_id']."'";
    } else {
	$inv_query  = "select * from inv_buyback_boxes order by date_added DESC";
    }
}

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "box_shipments.php",$page);
$shipments  = $db->func_query($splitPage->sql_query);

foreach($shipments as $index => $shipment){
  $products = $db->func_query("select * from inv_buyback_box_items where shipment_id = '".$shipment['id']."' and is_deleted!=1 order by shipment_id");
  $manual_items = $db->func_query("SELECT * FROM inv_buyback_manual_box_items WHERE shipment_id='".$shipment['id']."' AND is_deleted = '0'");
  $total_items = 0;
  foreach ($products as $i => $product) {
  $total_items+=$product['oem_received_a']+$product['oem_received_b']+$product['oem_received_c']+$product['oem_received_d']+$product['non_oem_received_a']+$product['non_oem_received_b']+$product['non_oem_received_c']+$product['non_oem_received_d']+$product['salvage_received'];
  }
  foreach($manual_items as $manual)
  {
  $total_items+=$manual['oem_qty_a']+$manual['oem_qty_b']+$manual['oem_qty_c']+$manual['oem_qty_d']+$manual['non_oem_qty_a']+$manual['non_oem_qty_b']+$manual['non_oem_qty_c']+$manual['non_oem_qty_d']+$manual['salvage_qty'];
  }
  if($total_items){
	$shipments[$index]['items'] = $total_items;
  }else{
    $shipments[$index]['items'] = 0;
  }
}
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Box Shipments</title>
        
        <script type="text/javascript" src="../js/jquery.min.js"></script>
		<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		 
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '680px' , autoCenter : true , autoSize : true });
			});
		</script>	
    </head>
    <body>
        <?php include_once '../inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div align="center">
        	 

        	<a class="fancybox fancybox.iframe" href="<?php echo $host_path;?>buyback/newbox.php">Create New Box</a>
         
         <?php
         
         if($_SESSION['login_as']=='admin')
         {
         ?>
          |
          <a class="fancybox3 fancybox.iframe" href="<?php echo $host_path;?>buyback/shipment_reason.php">Setting</a>
          |
          <a class="fancybox3 fancybox.iframe" href="<?php echo $host_path;?>buyback/shipments_sku_mapping.php">Shipment SKU Mapping</a>
        	<?php
        }
        ?>
        	<br /><br />
        	
	        <form method="get">
	        	Package Number:
	        	<input type="text" name="number" value="<?php echo $_GET['number'];?>" />
	        	<input type="submit" name="search" value="Search" />
	        </form>
        </div>
        
        <?php if(($shipments && $_SESSION['box_view']) || $_SESSION['group']=='Vendor' || $_SESSION['group']=='Servicer'):?>
             <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Shipment Date</th>
                        <th>Shipment Number</th>
                        <th>Status</th>
                        <th>Added</th>
                        <th># Items</th>
                        <th>Action</th>
                        <th>Package Tracking</th>
                   </tr>
               </thead>
               <tbody>
                 <?php $i = $splitPage->display_i_count();
           		     foreach($shipments as $i => $shipment):?>
                                          
                       <tr id="<?php echo $shipment['id'];?>">
                          <td align="center"><?php echo $i; ?></td>

                          <td align="center"><?php echo date("m/d/Y", strtotime($shipment['date_issued']));?></td>
                                                
                          <td align="center"><?php echo $shipment['package_number'];?></td>
                          
                          <td align="center"><?php echo $shipment['status'];?></td>
                                                
						  <td align="center"><?php echo americanDate($shipment['date_added']);?></td>
						  
						  <td align="center"><?php echo $shipment['items'];?></td>                                  
                          <td align="center" class="showorder">
                          	   <?php if($_SESSION['group'] != 'Vendor' && $_SESSION['group'] != 'Servicer' && $_SESSION['group'] != 'Super Admin' &&  $_SESSION['box_view']): ?>
                                  <a href="addedit_boxes.php?shipment_id=<?php echo $shipment['id']?>">View</a> |
                                  <?php endif;?>

                              <?php if((($shipment['status'] == 'Pending' && $_SESSION['edit_pending_shipment']) || $_SESSION['login_as'] == 'admin' || $_SESSION['qc_shipment'] || $shipment['status'] == 'Shipped' || $shipment['status'] == 'Received' || (($shipment['status'] == 'Shipped' || $shipment['status'] == 'Received' || $shipment['status'] == 'Completed') && $_SESSION['login_as'] == 'employee'))&& ($_SESSION['group'] == 'Super Admin' || $_SESSION['group'] == 'Vendor' ||$_SESSION['group'] == 'Servicer')):?>	

                              		<a href="addedit_boxes.php?shipment_id=<?php echo $shipment['id']?>">Edit</a>
                              		|
                              <?php endif;?>

                              <?php if($_SESSION['boxes_edit'] && $_SESSION['group'] != 'Vendor' && $_SESSION['group'] != 'Servicer' && $_SESSION['group'] != 'Super Admin'):?>  

                                  <a href="addedit_boxes.php?shipment_id=<?php echo $shipment['id']?>">Edit</a>
                                  |
                              <?php endif;?>	

                              <?php if($_SESSION['box_ship'] && $_SESSION['group'] != 'Vendor' && $_SESSION['group'] != 'Servicer' && $_SESSION['group'] != 'Super Admin' && $shipment['status'] == 'Pending'):?>  

                                  <a href="box_shipments.php?action=ship&shipment_id=<?php echo $shipment['id']?>" onclick="if(!confirm('Are you sure?')){ return false; }">
                                    Ship Shipment
                                  </a>
                                  |
                              <?php endif;?>  
                              
                              <?php if($shipment['status'] == 'Pending' && $_SESSION['edit_received_shipment']):?>
                              		<a href="box_shipments.php?action=complete&shipment_id=<?php echo $shipment['id']?>" onclick="if(!confirm('Are you sure?')){ return false; }">
                              	 		Complete Shipment
                              		</a>
                              		|
                              <?php endif;?>	
                              
                              
                              
                              <?php if($_SESSION['delete_shipment'] && ($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'employee')):?>
                              		<a href="box_shipments.php?action=delete&shipment_id=<?php echo $shipment['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
                              <?php endif;?>
                          </td>
                          <td align="center">
                            <?php
                            if(!$shipment['tracking_number']){
                              echo 'Not Synced';
                            }
                            else
                            {
                              $tracker_id = $db->func_query_first_cell("SELECT tracker_id FROM inv_tracker where shipment_id='".$shipment['id']."'");
                              $last_update = $db->func_query_first_cell("SELECT message FROM inv_tracker_status WHERE tracker_id='".$tracker_id."' order by id desc limit 1");
                              echo $last_update;
                            }
                            ?>
                          </td>
                        </tr>
                  <?php $i++; endforeach; ?>
                      
                  <tr>
                  	  <td colspan="7" align="left">
	                      <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                      </td>
                      
                      <td  align="right">
	                      <?php  echo $splitPage->display_links(10,$parameters); ?>
                      </td>
                  </tr>
               </tbody>   
            </table>   
        <?php else : ?> 
              <p>
                 <label style="color: red; margin-left: 600px;">Shipments don't exist.</label>
              </p>     
        <?php endif;?>
   </body>
</html>        