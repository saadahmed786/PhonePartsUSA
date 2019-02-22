<?php
   include_once 'auth.php';
   include_once 'inc/functions.php';
   include("phpmailer/class.smtp.php");
   include("phpmailer/class.phpmailer.php");
   include_once 'inc/split_page_results.php';
   /*Form Data*/
   $vendors = $db->func_query("select id , name as value from inv_users where group_id = 1 and status=1 order by lower(name)");
   $printers = array(
      array('id' => QC1_PRINTER, 'value' => 'QC1'),
      array('id' => QC2_PRINTER, 'value' => 'QC2'),
      array('id' => REC_PRINTER, 'value' => 'Receiving'),
      array('id' => STOREFRONT_PRINTER, 'value' => 'Storefront')
      );
   $carriers = array(
      array('id'=>'USPS','value'=>'USPS'),
      array('id'=>'UPS','value'=>'UPS'),
      array('id'=>'FedEx','value'=>'FedEx'),
      array('id'=>'DHL Express','value'=>'DHL Express'),
      array('id'=>'EMS','value'=>'EMS'),
      array('id'=>'HK Post','value'=>'HK Post'),
      array('id'=>'TNT','value'=>'TNT')
      );
   /*Form Data End*/
   $shipment_id = (int)$_GET['shipment_id'];
   if(!$shipment_id){
      $shipment_id = $db->func_query_first_cell("select id from inv_rejected_shipments where status != 'Completed'");
   }
   if(!$shipment_id){
      $_SESSION['message'] = "No new sku added in rejected list";
      header("Location:rejected_shipments.php");
      exit;
   }
   if($_POST['Transfer']){
      if(count($_POST['reject_ids']) > 0){
         foreach($_POST['reject_ids'] as $reject_id){
            transferRJInhouse ($_POST['new_shipment_id'], $reject_id, $shipment_id);
         }
         $_SESSION['message'] = "Reject Item is moved to another box.";
         header("Location:addedit_rejectedshipment.php?shipment_id=$shipment_id");
         exit;
      }  
      else{
         $_SESSION['message'] = "Select at least one sku to move to delete.";
         header("Location:addedit_rejectedshipment.php?shipment_id=$shipment_id");
         exit;
      }  
   }
   if($_POST['print']){ 
      foreach ($_POST['reject_ids'] as $reject_id) {
         $shipment_name = $db->func_query_first_cell("select package_number from inv_rejected_shipments where id = '$shipment_id'");
         $text = $db->func_escape_string($_POST['reason'][$reject_id]);
         $ntrItemReason = $db->func_query_first_cell("select name from inv_rj_reasons where id = '".$text."'");
          $source = $db->func_query_first_cell("select source from inv_returns where rma_number = '".$_POST['package'][$reject_id]."'");
         if ($source == 'storefront') {
            $source = 'SF';
         } else {
            $source = 'RC';
         }
         printLabel($reject_id,$_POST['sku'][$reject_id],$shipment_name,$ntrItemReason,$_POST['package'][$reject_id],$_POST['printer_id'],$source,24,'','');
         //printLabel($value, $returns_po_item_insert['product_sku'], $inv_return_shipment_box_number, $returns_po_item_insert['reason'], $returns_po_item_insert['order_id'], $returns_po_item['printer'], $source);
      }
   }
   elseif($_POST['delete']){
      if(count($_POST['reject_ids']) > 0){
         foreach($_POST['reject_ids'] as $reject_id){
            $db->db_exec ( "update inv_rejected_shipment_items set deleted=1 where reject_item_id = '$reject_id' and rejected_shipment_id = '$shipment_id' " );
            $from = $db->func_escape_string($_POST['package_number']);
            $to = "Delete";
            logRejectItem($reject_id, 'Deleted by ',$from, $to);
            // $displayLogger = $db->func_query("SELECT * FROM inv_rj_shipment_items_log WHERE reject_item_id = '$reject_id'");
            // $i++;
            // print_r($displayLogger);exit;
         }
         $_SESSION['message'] = "Items deleted successfully.";
         header("Location:addedit_rejectedshipment.php?shipment_id=$shipment_id");
         exit;
      }  
      else{
         $_SESSION['message'] = "Select at least one sku to move to delete.";
         header("Location:addedit_rejectedshipment.php?shipment_id=$shipment_id");
         exit;
      }  
   }
   elseif($_POST['MoveNTR']){
      if(count($_POST['reject_ids']) > 0){
         foreach($_POST['reject_ids'] as $reject_id){
            $text = $db->func_escape_string($_POST['reason'][$reject_id]);
            $ntrItemReason = $db->func_query_first_cell("select name from inv_rj_reasons where id = '".$text."'");
            $_shipment_id = $db->func_escape_string($_POST['shipment_number'][$reject_id]);
            $item_cost = (float)$_POST['product_cost'][$reject_id];
            $from = $db->func_escape_string($_POST['package_number']);
            addItemToBox($reject_id , $_POST[$reject_id] , $_shipment_id , 'NTRBox' , $ntrItemReason,false,false,$item_cost,$from);
            $db->db_exec("update inv_rejected_shipment_items set deleted =1 where reject_item_id = '".$reject_id."' and rejected_shipment_id = '$shipment_id' ");
   
   
         }
         $_SESSION['message'] = 'Selected Item(s) has been successfully moved to NTR';
      }  
      else{
         $_SESSION['message'] = "Select at least one sku to move to NTR.";
      }  
      header("Location:addedit_rejectedshipment.php?shipment_id=$shipment_id");
      exit;
   }
   elseif($_POST['MoveGFS']){
      if(count($_POST['reject_ids']) > 0){
         foreach($_POST['reject_ids'] as $reject_id){
            $reason_id = $db->func_escape_string($_POST['reason'][$reject_id]);
            $reason=$db->func_query_first_cell("SELECT name FROM inv_rj_reasons WHERE id = '". $reason_id ."'");
            $_shipment_id = $db->func_escape_string($_POST['shipment_number'][$reject_id]);
            $item_cost = (float)$_POST['product_cost'][$reject_id];
            $from = $db->func_escape_string($_POST['package_number']);
            addItemToBox($reject_id , $_POST[$reject_id] , $_shipment_id , 'GFSBox' , $reason,false,false,$item_cost,$from);
            $db->db_exec("update inv_rejected_shipment_items set deleted =1 where reject_item_id = '".$reject_id."' and rejected_shipment_id = '$shipment_id' ");
         }
         $_SESSION['message'] = 'Selected Item(s) has been successfully moved to GFS';
      }  
      else{
         $_SESSION['message'] = "Select at least one sku to move to GFS.";
         
      }  
      header("Location:addedit_rejectedshipment.php?shipment_id=$shipment_id");
      exit;
   }
   //save shipment
   elseif($_POST['save'] || $_POST['RejectComplete']){
      //die($_POST['status']);
      //die(here);
      $shipment = array();
      $shipment['package_number'] = $db->func_escape_string($_POST['package_number']);
      $shipment['vendor'] = $db->func_escape_string($_POST['vendor']);
      $shipment['carrier'] = $db->func_escape_string($_POST['carrier']);
      $shipment['shipping_cost'] = $db->func_escape_string($_POST['shipping_cost']);
      $shipment['amount_credited'] = $db->func_escape_string($_POST['amount_credited']);
      $shipment['tracking_number'] = $db->func_escape_string($_POST['tracking_number']);
      $shipment['status'] = $db->func_escape_string($_POST['status']);
      $shipment['date_issued'] = $_POST['date_issued'];
      // echo "select id from inv_rejected_shipments where id != '$shipment_id' and package_number = '".$shipment['package_number']."'";exit;
      // $checkExist = $db->func_query_first_cell("select id from inv_rejected_shipments where id != '$shipment_id' and package_number = '".$shipment['package_number']."'");
      if($checkExist){
   
         $_SESSION['message'] = "This package number is assigned to another shipment.";
         header("Location:addedit_rejectedshipment.php?shipment_id=$shipment_id");
         exit;
      }  else{
         $db->func_array2update("inv_rejected_shipments",$shipment,"id = '$shipment_id'");
         $_SESSION['message'] = "Shipment is updated";
      }
      
      $reject_item_ids = $_POST['hidden_reject_ids'];
   
      foreach($reject_item_ids as $id => $reject_id){
         $text = $db->func_escape_string($_POST['reason'][$reject_id]);
         $receivedStatus = (int)$_POST['received'][$reject_id];
         $reject_id = $db->func_escape_string($reject_id);
         $item_cost = (float)$_POST['product_cost'][$reject_id];
         $production_date = $_POST['production_date'][$reject_id];
         $receivedIn = '';
         if ($shipment['status'] == 'Shipped') {
            $receivedIn = " received = '$receivedStatus',";
         }
         $db->db_exec("update inv_rejected_shipment_items SET reject_reason = '$text',$receivedIn date_updated = now() where id = '$id'");

         if(($_SESSION['display_cost']))
         {
            $db->db_exec("update inv_rejected_shipment_items SET cost = '$item_cost' where id = '$id'");
         }

         if($_SESSION['edit_production_date'])
         {
         	$db->db_exec("update inv_rejected_shipment_items SET production_date = '$production_date' where id = '$id'");	
         }

      }
      
      if($_POST['RejectComplete'] && $_SESSION['edit_received_shipment']){
         //die(2);
         if(!$shipment['package_number']){
            $_SESSION['message'] = "Package number is required.";
            header("Location:addedit_rejectedshipments.php?shipment_id=$shipment_id");
            exit;
         }
         
         $db->db_exec("update inv_rejected_shipments SET status = '". $db->func_escape_string($_POST['RejectComplete']) ."'  where id = '$shipment_id'");
         $_SESSION['message'] = "Shipment status is Updated";
      }
      if($_POST['RejectComplete'] && $_SESSION['login_as']=="employee"){
         //die(2);
         if(!$shipment['package_number']){
            $_SESSION['message'] = "Package number is required.";
            header("Location:addedit_rejectedshipments.php?shipment_id=$shipment_id");
            exit;
         }
         
         $db->db_exec("update inv_rejected_shipments SET status = '". $db->func_escape_string($_POST['RejectComplete']) ."'  where id = '$shipment_id'");
         $_SESSION['message'] = "Shipment status is Completed";
      }
      header("Location: addedit_rejectedshipment.php?shipment_id=$shipment_id");
      exit;
   }
   $shipment_detail = $db->func_query_first("select * from inv_rejected_shipments where id = '$shipment_id'");
   $statuses = array(
      array('id'=>'Pending','value'=>'Pending'),
      array('id'=>'Issued','value'=>'Issued'),
      array('id'=>'Shipped','value'=>'Shipped'),
      array('id'=>'Received','value'=>'Received'),
      // array('id'=>'QCd','value'=>'QCd'),
      array('id'=>'Completed','value'=>'Completed'),
      );
   
   	if ($shipment_detail['status'] == 'Pending' && ($_SESSION['login_as'] != 'admin' || $_SESSION['login_as'] != 'manager')){
      $statuses = array(
         array('id'=>'Pending','value'=>'Pending'),
         array('id'=>'Issued','value'=>'Issued'),
         array('id'=>'Shipped','value'=>'Shipped'),
         array('id'=>'Received','value'=>'Received'),
         // array('id'=>'QCd','value'=>'QCd'),
         array('id'=>'Completed','value'=>'Completed'),
         );
      $buttonName = "Issue";
      $buttonValue = "Issued";
   }

   if ($shipment_detail['status'] == 'Issued' && ($_SESSION['login_as'] != 'admin' || $_SESSION['login_as'] != 'manager')){
      $statuses = array(
         array('id'=>'Pending','value'=>'Pending'),
         array('id'=>'Issued','value'=>'Issued'),
         array('id'=>'Shipped','value'=>'Shipped'),
         array('id'=>'Received','value'=>'Received'),
         // array('id'=>'QCd','value'=>'QCd'),
         array('id'=>'Completed','value'=>'Completed'),
         );
      $buttonName = "Ship";
      $buttonValue = "Shipped";
   }
   else if ($shipment_detail['status'] == 'Shipped' && ($_SESSION['login_as'] != 'admin' || $_SESSION['login_as'] != 'manager')){
      $statuses = array(
         array('id'=>'Shipped','value'=>'Shipped'),
         array('id'=>'Received','value'=>'Received'),
         // array('id'=>'QCd','value'=>'QCd'),
         array('id'=>'Completed','value'=>'Completed'),
         );
      $buttonName = "Receive";
      $buttonValue = "Received";
   }
   else if ($shipment_detail['status'] == 'Received' && ($_SESSION['login_as'] != 'admin' || $_SESSION['login_as'] != 'manager')){
      $statuses = array(
         array('id'=>'Received','value'=>'Received'),
         // array('id'=>'QCd','value'=>'QCd'),
         array('id'=>'Completed','value'=>'Completed'),
         );
      $buttonName = "Complete";
      $buttonValue = "Completed";
      $received = true;
   }
   else if ($shipment_detail['status'] == 'QCd' && ($_SESSION['login_as'] != 'admin' || $_SESSION['login_as'] != 'manager')){
      $statuses = array(
         // array('id'=>'QCd','value'=>'QCd'),
         array('id'=>'Completed','value'=>'Completed'),
         );
      $buttonName = "Complete";
      $buttonValue = "Completed";
      $received = true;
   }
   else if ($shipment_detail['status'] == 'Completed' && ($_SESSION['login_as'] != 'admin' || $_SESSION['login_as'] != 'manager')){
      $statuses = array(
         array('id'=>'Completed','value'=>'Completed'),
         );
      $buttonName = false;
      $received = true;
   }
   if($_SESSION['login_as'] == 'admin' || $_SESSION['login_as'] == 'manager')
   {
      $statuses = array(
         array('id'=>'Pending','value'=>'Pending'),
         array('id'=>'Issued','value'=>'Issued'),
         array('id'=>'Shipped','value'=>'Shipped'),
         array('id'=>'Received','value'=>'Received'),
         // array('id'=>'QCd','value'=>'QCd'),
         array('id'=>'Completed','value'=>'Completed'),
         );
      // $buttonName = "Ship";
      // $buttonValue = "Shipped";
   }
   $url = $host_path . 'easypost/tracker_api.php';
   $data = array(
      'tracking_number=' . $shipment_detail['tracking_number'],
      'carrier=' . $shipment_detail['carrier']
      );
   $data_string = implode('&', $data);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch,CURLOPT_POST, 1);
   curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
   curl_exec($ch);
   curl_close($ch);
   if(isset($_POST['addcomment']))
   {
      $data = array();
      $data['id'] = $shipment_id;
      $data['comment'] = $_POST['comment'];
      $msg = addComment('rejected_shipment',$data);
      $_SESSION['message'] = $msg;
      header("Location: addedit_rejectedshipment.php?shipment_id=".$shipment_id);
   
   }
   if($_POST['addToShip']){
      if(count($_POST['reject_ids']) > 0){
         $xxshipment_id = $_POST['shipment_id'];
         if (!$xxshipment_id) {
            $shipment = array(
               'package_number' => 'RJ' . rand(),
               'status' => 'Pending',
               'vendor' => $shipment_detail['vendor'],
               'ex_rate' => '0.00',
               'date_added' => date('Y-m-d H:i:s')
               );
            $xxshipment_id = $db->func_array2insert("inv_shipments", $shipment);
            $log = 'Shipment #: ' . linkToShipment($xxshipment_id, $host_path, $shipment['package_number']) . ' is Created From Rejected Shipment ' . $shipment_detail['shipment_number'];
            actionLog($log);
         }
         $package_number = $db->func_query_first_cell("SELECT package_number FROM `inv_shipments` WHERE id = '$xxshipment_id'");
         foreach($_POST['reject_ids'] as $reject_id) {
            $rejected_item = $db->func_query_first("SELECT * FROM inv_rejected_shipment_items WHERE reject_item_id = '$reject_id'");
            if (!$rejected_item['added_to_ship']) {
               $product = $db->func_query_first("SELECT * FROM oc_product a INNER JOIN oc_product_description b on (a.product_id = b.product_id) WHERE model = '". $rejected_item['product_sku'] ."'");
               $xproduct = $db->func_query_first("SELECT * FROM inv_shipment_items WHERE shipment_id = '$xxshipment_id' AND rejected_product = '1' AND product_sku = '" . $product['model'] . "'");
               if ($xproduct) {
                  $xproduct['cu_po'] = $xproduct['cu_po'] . ',' . $reject_id;
                  $xproduct['qty_shipped']++;
                  $db->func_array2update("inv_shipment_items", $xproduct, "id = '". $xproduct['id'] ."'");
               } else {
                  $item = array(
                     'shipment_id' => $xxshipment_id,
                     'product_id' => $product['product_id'],
                     'product_sku' => $product['model'],
                     'qty_shipped' => 1,
                     'product_name' => $product['name'],
                     'rejected_product' => 1,
                     'cu_po' => $reject_id
                     );
                  $db->func_array2insert("inv_shipment_items", $item);
               }
               $db->db_exec("update inv_rejected_shipment_items set added_to_ship = '$xxshipment_id' where reject_item_id = '".$reject_id."'");
               logRejectItem($reject_id, 'Added to Shipment ' . $package_number);
            }
         }
      } else {
         $_SESSION['message'] = "Select at least one sku to Add to Shipment.";
         header("Location:addedit_rejectedshipment.php?shipment_id=$shipment_id");
         exit;
      }  
   }
   $vtx = false;
   if ($_SESSION['group'] == 'Vendor') {
      if ($_SESSION['user_id'] == $shipment_detail['vendor']) {
         $vtx = true;
      } else {
         exit;
      }
   }
   if(isset($_GET['page'])){
      $page = intval($_GET['page']);
   }
   if($page < 1){
      $page = 1;
   }
   $parameters = "shipment_id=$shipment_id";
   $max_page_links = 10;
   $num_rows = 1000;
   $start = ($page - 1)*$num_rows;
   $inv_query  = "select si.* , s.package_number,s.id as ShipmentId from inv_rejected_shipment_items si left join inv_shipments s on (si.shipment_id = s.id)
   where rejected_shipment_id = '$shipment_id' and si.deleted=0 order by received DESC";
   $splitPage  = new splitPageResults($db , $inv_query , $num_rows , "addedit_rejectedshipment.php",$page);
   $products   = $db->func_query($splitPage->sql_query);
   $boxes = $db->func_query("select id , package_number from inv_rejected_shipments where id != '$shipment_id'  and status='Pending' and is_hidden=0 order by date_added DESC");
   $shipments = $db->func_query('SELECT * FROM `inv_shipments` WHERE vendor = "'. $shipment_detail['vendor'] .'" AND status = "Pending"');
   if ($_POST['status'] == 'Issued' && $shipment_detail['status'] != 'Issued') {
      $message = '<p>User this tracking number to track shipment</p>';
      $message .= '<p>Shipped Date: '. americanDate(date('Y-m-d H:i:s')) .'</p>';
      $message .= '<p>Tracking Date: '. $shipment_detail['tracking_number'] .'</p>';
      $message .= '<table><thead><tr>';
      $message .= '<th>Shipment Number</th><th>SKU</th><th>Reason</th>';
      foreach ($products as $product) {
         $message .= '<td>'. $product['package_number'] .'</td><td>'. $product['product_sku'] .'</td><td>'. $db->func_query_first_cell("SELECT name FROM inv_rj_reasons WHERE id = '". $product['reject_reason'] ."'") .'</td>';
      }
      sendEmail(get_username($shipment_detail['vendor']), get_userdetail($shipment_detail['vendor'], 'email'), 'Tracking No for Rejected Shipment '. $shipment_detail['package_number'], $message, array('email' => 'vendors@phonepartsusa.com'));
   }
   //print($shipment_detail['status']);exit;
   //print($_SESSION['login_as']);exit;
   //echo get_username($shipment_detail['carrier']);print_r($shipment_detail['carrier']);exit;
   //print_r($_SESSION['edit_received_shipment']);exit;
   ?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
      <title>Add / Edit Rejected Shipment</title>
      <script type="text/javascript" src="js/jquery.min.js"></script>
      <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
      <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
      <script type="text/javascript">
         $(document).ready(function() {
            $('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : true });
            $('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : true });
         });
         function allowFloat (t) {
            var input = $(t).val();
            var valid = input.substring(0, input.length - 1);
            if (isNaN(input)) {
               $(t).val(valid);
            }
         }
      </script>   
   </head>
   <body>
      <?php include_once 'inc/header.php';?>
      <?php 
         $total_cost = 0;
         $total_received_cost = 0;
         $shipped_cost = 0;
          ?>
      <?php if(@$_SESSION['message']):?>
      <div align="center"><br />
         <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
      </div>
      <?php endif;?>
      <div align="center">
         <?php if ($_SESSION['login_as'] == 'employee' && ($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Issued' ) ) { ?>
         <?php } else {?>
         <?php if($shipment_detail['status'] != 'Completed') { ?>
         <a class="fancyboxX4 fancybox.iframe" href="<?php echo $host_path?>/popupfiles/rejected_newbox_item.php?shipment_id=<?php echo $shipment_id?>">Add New Item</a> <br />
         <?php }?>
         <?php }?>
         <form method="post" action="">
            <br />
            <div>
               <table>
                  <tr>
                      <?php if(($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued') && $_SESSION['display_cost'] ){ ?>
                  <td>Amount Credited:</td>
                  <td><input type="text" name="amount_credited" value=<?php echo $shipment_detail['amount_credited']; ?>></td>
                  <?php } ?>
                     <?php if (($_SESSION['login_as'] == 'employee' || $_SESSION['login_as'] == 'admin') && $shipment_detail['status'] == 'Pending' || $shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued' ) { ?>
                     <?php if($shipment_detail['status'] == 'Pending'){ ?>
                     <td>Vendor:</td>
                     <td readonly>
                        <?php
                           if ($vtx) {
                              echo get_username($shipment_detail['vendor']);
                              echo createField("vendor", "vendor" , "select" , $shipment_detail['vendor'], $vendors, 'onclick="$(\'.vendor\').val($(this).val());" class="vendor" style="display:none;" ');
                           } else {
                              echo createField("vendor", "vendor" , "select" , $shipment_detail['vendor'], $vendors, 'onclick="$(\'.vendor\').val($(this).val());" class="vendor" ');
                              //echo get_username($shipment_detail['vendor']);
                           //echo createField("vendor", "vendor" , "select" , $shipment_detail['vendor'], $vendors, 'onclick="$(\'.vendor\').val($(this).val());" class="vendor" readonly');
                           }
                           ?>
                     </td>
                     <?php }?>
                     <?php if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued'){ ?>
                     <td>Vendor:</td>
                     <td readonly>
                        <?php
                           if ($vtx) {
                              echo get_username($shipment_detail['vendor']);
                              echo createField("vendor", "vendor" , "select" ,$shipment_detail['vendor'], $vendors, 'onclick="$(\'.vendor\').val($(this).val());" class="vendor" style="display: none;" readonly=""');
                           } else {
                              
                              echo get_username($shipment_detail['vendor']);
                           echo createField("vendor", "vendor" , "select" ,$shipment_detail['vendor'], $vendors, 'onclick="$(\'.vendor\').val($(this).val());" class="vendor" style="display: none;" readonly=""');
                           }
                           ?>
                     </td>
                     <?php }?>
                     <?php if($shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed'){ ?>
                     <td style="display:none;">Vendor:</td>
                     <td style="display:none;" readonly>
                        <?php
                           if ($vtx) {
                              echo get_username($shipment_detail['vendor']);
                              echo createField("vendor", "vendor" , "select" ,$shipment_detail['vendor'], $vendors, 'onclick="$(\'.vendor\').val($(this).val());" class="vendor" style="display: none;" readonly=""');
                           } else {
                              
                              //echo get_username($shipment_detail['vendor']);
                           echo createField("vendor", "vendor" , "select" , $shipment_detail['vendor'], $vendors, 'onclick="$(\'.vendor\').val($(this).val());" class="vendor" readonly');
                           }
                           ?>
                     </td>
                     <?php }?>
                     <?php if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued'){ ?>
                     <td>Carrier:</td>
                     <td required readonly>
                        <?php
                           if ($vtx) {
                              echo $shipment_detail['carrier'];
                              echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier" style="display: none;" readonly');
                           } else {
                           
                              echo ($shipment_detail['carrier']);
                           echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier" style="display: none;" readonly');
                           }
                           ?>
                     </td>
                     <?php }?>
                     <?php if($shipment_detail['status'] == 'Pending'){ ?>
                     <td>Carrier:</td>
                     <td required>
                        <?php
                           if ($vtx) {
                              echo $shipment_detail['carrier'];
                              echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier" style="display: none;" readonly');
                           } else {
                           //echo ($shipment_detail['carrier']);
                              echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier"');
                           }
                           ?>
                     </td>
                     <?php }?>
                     <?php if($shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed'){ ?>
                     <td style="display:none;">Carrier:</td>
                     <td style="display:none;" required readonly>
                        <?php
                           if ($vtx) {
                              echo $shipment_detail['carrier'];
                              echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier" style="display: none;" readonly');
                           } else {
                           
                           // echo ($shipment_detail['carrier']);
                           echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier" readonly');
                           }
                           ?>
                     </td>
                     <?php }?>
                     <?php if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued'){ ?>
                     <td>Tracking #:</td>
                     <td required>
                        <?php
                           if ($vtx) {
                              echo $shipment_detail['tracking_number'];
                              echo '<input readonly type="text" required class="tracking_number" style="display: none;" name="tracking_number" onkeyup="$(\'.tracking_number\').val($(this).val());" value="' . $shipment_detail['tracking_number'] . '"/>';
                           } else {
                                 if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Issued'){
                              echo '<input type="text" required class="tracking_number" name="tracking_number" onkeyup="$(\'.tracking_number\').val($(this).val());" value="' . $shipment_detail['tracking_number'] . '"/>';
                              }else{
                                 echo '<input readonly type="text" required class="tracking_number" name="tracking_number" onkeyup="$(\'.tracking_number\').val($(this).val());" value="' . $shipment_detail['tracking_number'] . '"/>';
                              }
                           }
                           ?>
                     </td>
                     <?php }?>
                     <?php if($shipment_detail['status'] == 'Pending'){ ?>
                     <td>Tracking #:</td>
                     <td required>
                        <?php
                           if ($vtx) {
                              echo $shipment_detail['tracking_number'];
                              echo '<input readonly type="text" class="tracking_number" style="display: none;" name="tracking_number" onkeyup="$(\'.tracking_number\').val($(this).val());" value="' . $shipment_detail['tracking_number'] . '"/>';
                           } else {
                              echo '<input type="text" class="tracking_number" name="tracking_number" onkeyup="$(\'.tracking_number\').val($(this).val());" value="' . $shipment_detail['tracking_number'] . '"/>';
                           }
                           ?>
                     </td>
                     <?php }?>
                     <?php if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued'){ ?>
                     <?php if($_SESSION['display_cost']):?>
                     <td>Shipping Cost:
                        <?php if (!$vtx) { ?>
                        <?php if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Issued'){ ?>
                        <input required onkeyup="allowFloat(this); $('.shipping_cost').val($(this).val());" class="shipping_cost" type="text" name="shipping_cost" value="<?php echo $shipment_detail['shipping_cost'];?>"  />
                        <?php }  else{ ?>
                        <input readonly required onkeyup="allowFloat(this); $('.shipping_cost').val($(this).val());" class="shipping_cost" type="text" name="shipping_cost" value="<?php echo $shipment_detail['shipping_cost'];?>"  />
                        <?php } } else { ?>
                        <?php echo $shipment_detail['shipping_cost'];?>
                        <input required onkeyup="allowFloat(this); $('.shipping_cost').val($(this).val());" class="shipping_cost" style="display:none;" type="text" name="shipping_cost" value="<?php echo $shipment_detail['shipping_cost'];?>"  />
                        <?php } ?>
                     </td>
                     <?php endif;?>
                     <?php }?>
                     <?php if($shipment_detail['status'] == 'Pending'){ ?>
                     <?php if($_SESSION['display_cost']):?>
                     <td>Shipping Cost:
                        <?php if (!$vtx) { ?>
                        <input onkeyup="allowFloat(this); $('.shipping_cost').val($(this).val());" class="shipping_cost" type="text" name="shipping_cost" value="<?php echo $shipment_detail['shipping_cost'];?>"  />
                        <?php } else { ?>
                        <?php echo $shipment_detail['shipping_cost'];?>
                        <input onkeyup="allowFloat(this); $('.shipping_cost').val($(this).val());" class="shipping_cost" style="display:none;" type="text" name="shipping_cost" value="<?php echo $shipment_detail['shipping_cost'];?>"  />
                        <?php } ?>
                     </td>
                     <?php endif;?>
                     <?php }?>
                  </tr>
                  <tr>
                     <td>Shipment Number:</td>
                     <td>
                        <?php
                           if ($vtx) {
                              echo $shipment_detail['package_number'];
                              echo '<input readonly type="text" onkeyup="$(\'.package_number\').val($(this).val());" name="package_number" class="package_number" style="display:none;" value="' . $shipment_detail['package_number'] . '" required />';
                           } else {
                              echo '<input type="text" onkeyup="$(\'.package_number\').val($(this).val());" name="package_number" class="package_number" value="' . $shipment_detail['package_number'] . '" required />';
                           }
                           ?>
                     </td>
                     <?php }?>
                     <td>
                        Status:
                     </td>
                     <?php if ($_SESSION['login_as'] == 'employee'){ //&& $shipment_detail['status'] == 'Shipped' ) { ?>
                     <td>
                        <?php echo $shipment_detail['status']; ?>
                     <td style="display:none;">
                        <select id="status" onchange="funcBtnChanger();" name="status" style="width:150px;">
                           <option value="">Select One</option>
                           <?php foreach($statuses as $status):?>
                           <option value="<?php echo $status['id']; ?>" <?php echo ($status['id'] == $shipment_detail['status'])? 'selected=""':''; ?>><?php echo $status['value']; ?></option>
                           <?php endforeach;?>
                        </select>
                     </td>
                     </td>
                     <?php } else{?>
                     <td>
                        <select id="status" onchange="funcBtnChanger();" name="status" style="width:150px;">
                           <option value="">Select One</option>
                           <?php foreach($statuses as $status):?>
                           <option value="<?php echo $status['id']; ?>" <?php echo ($status['id'] == $shipment_detail['status'])? 'selected=""':''; ?>><?php echo $status['value']; ?></option>
                           <?php endforeach;?>
                        </select>
                     </td>
                     <?php }?>
                     <td>
                        <?php if(($_SESSION['login_as'] != 'employee') && $shipment_detail['status'] != 'Completed'):?>
                        <input type="submit" name="save" value="Save " />
                        <?php endif;?>
                     </td>
                     <?php if (!$vtx) { ?>
                    
                     
                     <td>
                        Shipment Date:
                        <input type="text" data-type="date" value="<?php echo $shipment_detail['date_issued']; ?>" name="date_issued" />
                     </td>
                     <?php }else{ if($_SESSION['login_as'] != 'employee'){ ?>
                     <td>Vendor</td>
                     <td>
                        <?php
                           if ($vtx) {
                              echo get_username($shipment_detail['vendor']);
                           } else {
                           //echo get_username($shipment_detail['vendor']);
                           //echo '<input type="text" value= "' . get_username($shipment_detail['vendor'] . '"/>';
                              echo createField("vendor", "vendor" , "select" , $shipment_detail['vendor'], $vendors, 'onclick="$(\'.vendor\').val($(this).val());" class="vendor"');
                           }
                           ?>
                     </td>
                     <td>Carrier:</td>
                     <td required>
                        <?php
                           if ($vtx) {
                              echo $shipment_detail['carrier'];
                           } else {
                           //echo ($shipment_detail['carrier']);
                              echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier"');
                           }
                           ?>
                     </td>
                     <td>Tracking #:</td>
                     <td required>
                        <?php
                           if ($vtx) {
                              echo $shipment_detail['tracking_number'];
                           } else {
                              echo '<input type="text" required class="tracking_number" name="tracking_number" onkeyup="$(\'.tracking_number\').val($(this).val());" value="' . $shipment_detail['tracking_number'] . '"/>';
                           }
                           ?>
                     </td>
                     <?php if($_SESSION['display_cost']):?>
                     <td>Shipping Cost:
                        <?php if (!$vtx) { ?>
                        <input required onkeyup="allowFloat(this); $('.shipping_cost').val($(this).val());" class="shipping_cost" type="text" name="shipping_cost" value="<?php echo $shipment_detail['shipping_cost'];?>"  />
                        <?php } else { ?>
                        <?php echo $shipment_detail['shipping_cost'];?>
                        <?php } ?>
                     </td>
                     <?php endif;?>
                  </tr>
                  <tr>
                     <td>Shipment Number:</td>
                     <td>
                        <?php
                           if ($vtx) {
                              echo $shipment_detail['package_number'];
                           } else {
                              echo '<input type="text" onkeyup="$(\'.package_number\').val($(this).val());" name="package_number" class="package_number" value="' . $shipment_detail['package_number'] . '" required />';
                           }
                           ?>
                     </td>
                     <?php }} ?>
                  </tr>
               </table>
            </div>
            <div align="center">
               <?php
                  $image_path = getVendorPic($shipment_detail['vendor']);
                  
                  ?>
               <img src="<?php echo $image_path;?>" height="100" width="100">
               <br />
               <input type="button" value="Print" onclick="$('.printer').show();" /> &nbsp&nbsp&nbsp&nbsp
               <?php if($_SESSION['login_as'] == 'admin'):?>
               <input type="submit" name="delete" value="Delete" onclick="if(!confirm('Are you sure?')){ return false; }" />&nbsp&nbsp&nbsp&nbsp
               <?php endif;?>
               <?php if (!$vtx) { ?>
               <?php if($shipment_detail['status'] == 'Pending'){?>
               <input type="submit" name="MoveNTR" value="Move to NTR" onclick="if(!confirm('Are you sure?')){ return false; }" />&nbsp&nbsp&nbsp&nbsp
               <input type="submit" name="MoveGFS" value="Move to GFS" onclick="if(!confirm('Are you sure?')){ return false; }" />&nbsp&nbsp&nbsp&nbsp
               <?php }?>
               <?php } ?>
               <?php if (($_SESSION['login_as'] == 'admin' || ($vtx && $shipment_detail['status'] != 'Shipped')) && $shipment_detail['status'] != 'Pending' && $shipment_detail['status'] != 'Completed' && $shipment_detail['status'] != 'Issued') { ?>
               <input type="button" onclick="$('.blackPage').show();" value="Add to Shippment" onclick="if(!confirm('Are you sure?')){ return false; }" />&nbsp&nbsp&nbsp&nbsp
               <?php } ?>
               <?php if(( $_SESSION['edit_received_shipment']||($_SESSION['login_as'] == 'employee' && $shipment_detail['status'] != 'Pending' && $shipment_detail['status'] != 'Completed')) ):?>
               <input type="submit" name="save" value="Save" /> &nbsp&nbsp&nbsp&nbsp
               <?php if ($buttonName && $vtx || $_SESSION['login_as'] == 'admin' || ($_SESSION['login_as'] == 'employee' && !$vtx && $shipment_detail['status'] == 'Pending') ) { ?>
               <button class="buttonChanger" type="submit" name="RejectComplete" value="<?php echo $buttonValue; ?>" onclick="if(!confirm('Are you sure?')){ return false; }">
               Save And <?php echo $buttonName ?> Shipment
               </button>



               <?php

               if($_SESSION['create_vendor_credit_rtv']==1 && $shipment_detail['status']=='Received' && $shipment_detail['is_credit_added']==0){
                  ?>
                  <a href="<?php echo $host_path;?>/popupfiles/vendor_credit_rtv.php?vendor_id=<?php echo $shipment_detail['vendor'];?>&shipment_id=<?php echo $_GET['shipment_id'];?>" style="display:inline-block;margin-top:5px;margin-bottom:5px" class="button fancybox2 fancybox.iframe">Generate Vendor Credit</a>
                  <?php
               }
               ?>
               <?php } ?>
               <?php endif;?>

                 <?php
               if($shipment_detail['status']=='Pending' && !$vtx)
               {
                  ?>
                  <br><br><br>
                   
                     
                        Transfer to RTV Box:
                        <select name="new_shipment_id" class="new_shipment_id" onchange="$('.new_shipment_id').val($(this).val());" id="new_shipment_id" style="width:150px;margin-right:5px">
                           <option value="">Select One</option>
                           <?php foreach($boxes as $box):?>
                           <option value="<?php echo $box['id']; ?>"><?php echo $box['id'] . " -- ". $box['package_number']; ?></option>
                           <?php endforeach;?>
                        </select>

                     
                     

                  <input type="submit" class="button button-danger" style="" name="Transfer" value="Transfer" onclick="if(!$('#new_shipment_id').val()){ alert('Please select one PO.'); return false;}if(!confirm('Are you sure want to Transfer the selected item(s) in the New RTV?')){ return false;}" />
                  <?php
               }
               ?>


               <br /><br />
            </div>
            <br /><br />
            <?php if($_SESSION['display_cost']):?>
            <table width="20%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
               <tr>
                  <th align="center">Total</th>
                  <th align="center">Received</th>
                  <th align="center">Replaced</th>
               </tr>
               <tr>
                  <td align="center" id="stt"></td>
                  <td align="center" id="stt1"></td>
                  <td align="center" id="stt2"></td>
               </tr>
            </table>
            <br /><br />
            <?php
            endif;?>
            <div>
               <?php if($products):?>
               <table id="table1" class="tablesorter" width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse; border-left: 0px; border-right:0px;">
                  <thead>
                     <tr data-no="1">
                        <td style="border-left: 0px; border-right:0px;" colspan="11" align="center">
                           <h2 style="padding-top: 10px;">RECEIVED ITEM(s)</h2>
                        </td>
                     </tr>
                     <tr>
                        
                        <th><input type="checkbox" name="allselector" id="allselector" > </th>
                        <th>#</th>
                        <th>Shipment Number</th>
                        <th>RTV ID</th>
                        <th>SKU</th>
                        <th>Production Date<?php if($_SESSION['edit_production_date']):?><br><small><a href="javascript:void(0);" onclick="editProductionDate();">(Edit Date)</a></small><?php endif; ?></th>
                        <th>Reason</th>
                        <?php if($_SESSION['display_cost']):?>
                        <th>Cost<?php if($_SESSION['login_as'] == 'admin'):?><br><small><a href="javascript:void(0);" onclick="editCost();">(Edit Cost)</a></small><?php endif; ?></th>
                        <?php endif; ?>
                        <th>Date Added</th>
                        <th>Date Updated</th>
                        <?php if (($vtx || $_SESSION['login_as'] == 'admin') && ($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Issued') ) { ?>
                        <th>Received <input type="checkbox" name="allselector1" id="allselector1" ></th>
                        <?php } ?>
                        <?php if (!$vtx) { ?>
                        <?php if($shipment_detail['status'] == 'Pending'){?>
                        <th>Action</th>
                        <?php } ?>
                        <?php } ?>
                        <?php if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued'):?>
                        <th>Added to Shipment</th>
                        <?php endif; ?>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $i = $splitPage->display_i_count();
                        $count = 1; 
                        
                        foreach($products as $pkey => $product):?>
                     <?php if ($product['received'] != $products[($pkey - 1 )]['received'] && $pkey) { ?>
                  </tbody>
               </table>
               <table id="table2" width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse; border-left: 0px; border-right:0px;">
                  <thead>
                     <tr>
                        <td style="border-left: 0px; border-right:0px;" colspan="11" align="center">
                           <h2 style="padding-top: 10px;">NOT RECEIVED ITEM(s)</h2>
                        </td>
                     </tr>
                     <tr>
                        <th>#</th>
                        <th>Shipment Number</th>
                        <th>RTV ID</th>
                        <th>SKU</th>
                        <th>Production Date</th>
                        <th>Reason</th>
                        <?php if($_SESSION['display_cost']):?>
                        <th>Cost<?php if($_SESSION['login_as'] == 'admin'):?><br><small><a href="javascript:void(0);" onclick="editCost();">(Edit Cost)</a></small><?php endif; ?></th>
                        <?php endif; ?>
                        <th>Date Added</th>
                        <th>Date Updated</th>
                        <?php if ($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Issued') { ?>
                        <th>Received</th>
                        <?php } ?>
                        <?php if (!$vtx) { ?>
                        <?php if($shipment_detail['status'] == 'Pending'){?>
                        <th>Action</th>
                        <?php } ?>
                        <?php } ?>
                        <?php if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued'):?>
                        <th>Added to Shipment</th>
                        <?php endif; ?>
                     </tr>
                  </thead>
                  <tbody>
                     <?php } ?>
                     <tr class="list_items">

                        <?php if (($_SESSION['login_as'] == 'employee' && $shipment_detail['status'] == 'Completed' )|| ($_SESSION['login_as'] == 'employee' && ($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Issued' ))) { ?>
                        <td>
                           <input disabled="disabled" class="selection checkboxes" type="checkbox" name="reject_ids[<?php echo $product['reject_item_id'];?>]" value="<?php echo $product['reject_item_id'];?>" />
                           <input type="hidden" name="hidden_reject_ids[<?php echo $product['id'];?>]" value="<?php echo $product['reject_item_id'];?>">
                        </td>
                        <?php } else{ ?>
                        <td>
                           <input class="selection checkboxes" type="checkbox" name="reject_ids[<?php echo $product['reject_item_id'];?>]" value="<?php echo $product['reject_item_id'];?>" />
                           <input type="hidden" name="hidden_reject_ids[<?php echo $product['id'];?>]" value="<?php echo $product['reject_item_id'];?>">
                        </td>
                        <?php } ?>
                        <td align="center"><?php echo $i;?></td>
                        <td align="center">
                           <?php echo ($product['package_number'])? $product['package_number']: 'N/A';?>
                        </td>
                        <input type="hidden" value="<?php echo $product['package_number'];?>" name="package[<?php echo $product['reject_item_id'];?>]" />
                        <input type="hidden" value="<?php echo $product['product_sku'];?>" name="sku[<?php echo $product['reject_item_id'];?>]" />
                        <input type="hidden" value="<?php echo $product['reject_reason']?>" name="reason[<?php echo $product['reject_item_id']?>]" />
                        <input type="hidden" value="<?php echo $product['ShipmentId'];?>" name="shipment_number[<?php echo $product['reject_item_id'];?>]" /> 
                        <td align="center">
                           <a class="fancyboxX3 fancybox.iframe" href="reject_item_log.php?reject_item_id=<?php echo $product['reject_item_id'];?>"><?php echo $product['reject_item_id'];?></a>
                           <br>
                           (<?php echo $product['id'];?>)
                        </td>
                        <td align="center">
                           <?php echo linkToProduct($product['product_sku'], $host_path, 'target="_blank"');?>
                        </td>
                        <td align="center">
                        <?php //echo ($product['production_date']=='0000-00'?'-':$product['production_date']);?>

                        <input type="text" style="width: 100px; text-align:center;background-color: #DCDCDC" class="production_date_input" readonly="" value="<?php echo $product['production_date'];?>" data-type="monthyear" name="production_date[<?php echo $product['reject_item_id'];?>]" />

                        </td>
                        <?php if (($_SESSION['login_as'] == 'employee' || $_SESSION['login_as'] == 'admin') && ($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued') ) { ?>
                        <td align="center">
                           <input readonly type="hidden" value="<?php echo $product['product_sku'];?>" name="[<?php echo $product['reject_item_id'];?>]" />
                           <input readonly type="hidden" value="<?php echo $product['ShipmentId'];?>" name="shipment_number[<?php echo $product['reject_item_id'];?>]" />
                           <input readonly type="hidden" value="<?php echo $product['ShipmentId'];?>" name="shipment_number[<?php echo $product['reject_item_id'];?>]" />
                           <input readonly type="hidden" value="<?php echo $product['reject_reason']?>" name="reason[<?php echo $product['reject_item_id']?>]" /> 
                           <?php $reasons = $db->func_query("SELECT * FROM inv_rj_reasons WHERE classification_id = (SELECT classification_id FROM oc_product where model = '". $product['product_sku'] ."' limit 1)"); ?>
                           <?php if (!$vtx || $vtx) { ?>
                           <?php echo $db->func_query_first_cell("SELECT name FROM inv_rj_reasons WHERE id = '". $product['reject_reason'] ."'") ?>
                           <?php } ?>
                        </td>
                        <?php } else { ?>
                        <td align="center">
                           <input type="hidden" value="<?php echo $product['product_sku'];?>" name="<?php echo $product['reject_item_id'];?>" />
                           <input type="hidden" value="<?php echo $product['ShipmentId'];?>" name="shipment_number[<?php echo $product['reject_item_id'];?>]" />
                           <input type="hidden" value="<?php echo $product['ShipmentId'];?>" name="shipment_number[<?php echo $product['reject_item_id'];?>]" />
                           <?php if (($_SESSION['login_as'] != 'employee')){ ?>
                           <input type="hidden" name="reason[<?php echo $product['reject_item_id']?>]" value="<?php echo $product['reject_reason']?>" /> 
                           <?php }?>
                           <?php if (!$vtx && $_SESSION['edit_rj_reasons']) { ?>
                           <?php $reasons = $db->func_query("SELECT * FROM inv_rj_reasons WHERE classification_id = (SELECT classification_id FROM oc_product where model = '". $product['product_sku'] ."' limit 1)"); ?>
                           <select name="reason[<?php echo $product['reject_item_id']?>]">
                              <option value=''>Select</option>
                              <?php foreach ($reasons as $reason) { ?>
                              <option <?php echo ($reason['id'] == $product['reject_reason'])? 'selected="selected"': ''; ?> value="<?php echo $reason['id']; ?>"><?php echo $reason['name'] ?></option>
                              <?php } ?>
                           </select>
                           <?php } else { ?>
                           <?php echo $db->func_query_first_cell("SELECT name FROM inv_rj_reasons WHERE id = '". $product['reject_reason'] ."'") ?>
                           <?php } ?>
                        </td>
                        <?php }?>
                        <?php if($_SESSION['display_cost']):?>
                        <td align="center">
                           <!-- $<?php echo $product['cost'];?> -->
                           <input type="text" style="width: 50px; text-align:center;background-color: #DCDCDC" class="cost_input" readonly="" value="<?php echo $product['cost'];?>" name="product_cost[<?php echo $product['reject_item_id'];?>]" />
                           <?php $total_cost = $total_cost + $product['cost'];
                              if ($product['added_to_ship']) {
                              $shipped_cost = $shipped_cost + $product['cost'];
                              }
                              if($product['received'])
                              {
                                 $total_received_cost = $total_received_cost + $product['cost'];
                              }
                              ?>
                        </td>
                        <?php endif; ?>
                        <td align="center">
                           <?php echo americanDate($product['date_added']); ?>
                        </td>
                        <td align="center">
                           <?php echo americanDate($product['date_updated']); ?>
                        </td>
                        <?php if (($vtx || $_SESSION['login_as'] == 'admin') && ($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Issued')) { ?>
                        <td align="center">
                           <input class="selectors"  type="checkbox" <?php echo (($product['received'])? 'checked="checked"': '');?> <?php echo ((!$_SESSION['login_as'] == 'employee')? 'disabled=""': ''); ?> name="received[<?php echo $product['reject_item_id'];?>]" value="1" />
                        </td>
                        <?php } ?>
                        <?php if (!$vtx) { ?>
                        <?php if($shipment_detail['status'] == 'Pending'){?>
                        <td align="center">
                           <?php if ($received) { ?>
                           <a href="javascript:void(0);" onclick="$(this).parents('tr').find('.selection').trigger('click'); $('.blackPage').show();">Add to Shipment</a>
                           <?php } else { ?>
                           <a class="fancybox2 fancybox.iframe" href="<?php echo $host_path;?>popupfiles/move_rejectpo_item.php?id=<?php echo $product['rejected_shipment_id'];?>&reject_id=<?php echo $product['reject_item_id']?>">Transfer</a>
                           <?php } ?>
                        </td>
                        <?php } ?>
                        <?php } ?>
                        <?php if($shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Received' || $shipment_detail['status'] == 'Completed' || $shipment_detail['status'] == 'Issued'):?>
                        <td align="center">  
                           <?php if ($product['added_to_ship']) { ?>
                           <img src="images/check.png" alt="" /><br>
                           <?php $xship = $db->func_query_first("SELECT * from inv_shipments WHERE id = '". $product['added_to_ship'] ."'"); ?>
                           <?php echo linkToShipment($xship['id'], $host_path, $xship['package_number']) ?>
                           <?php } else { ?>
                           <img src="images/cross.png" alt="" />
                           <?php } ?>  
                        </td>
                        <?php endif; ?>
                     </tr>
                     <?php $i++; endforeach; ?>
                     
                  </tbody>
               </table>
               <table>
                  <tr>
                  <td>
                        <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                  </td>
                  </tr>
               </table>
               <br>
               <div>
                  <table>
                     <tr>
                        <?php if ($_SESSION['login_as'] == 'employee' && $shipment_detail['status'] == 'Pending' || $shipment_detail['status'] == 'Shipped' || $shipment_detail['status'] == 'Issued' ) { ?>
                        <td>Vendor:</td>
                        <td readonly>
                           <?php
                              if ($vtx) {
                                 echo get_username($shipment_detail['vendor']);
                              } else {
                              
                                 echo get_username($shipment_detail['vendor']);
                              //echo createField("vendor", "vendor" , "select" , $shipment_detail['vendor'], $vendors, 'onclick="$(\'.vendor\').val($(this).val());" class="vendor" readonly');
                              }
                              ?>
                        </td>
                        <td>Carrier:</td>
                        <td required readonly>
                           <?php
                              if ($vtx) {
                                 echo $shipment_detail['carrier'];
                              } else {
                              
                                 echo ($shipment_detail['carrier']);
                              //echo createField("carrier", "carrier" , "select" , $shipment_detail['carrier'] , $carriers, 'onclick="$(\'.carrier\').val($(this).val());" class="carrier" readonly');
                              }
                              ?>
                        </td>
                        <td>Tracking #:</td>
                        <td required>
                           <?php
                              if ($vtx) {
                                 echo $shipment_detail['tracking_number'];
                              } else {
                                 echo '<input readonly type="text" required class="tracking_number" name="tracking_number" onkeyup="$(\'.tracking_number\').val($(this).val());" value="' . $shipment_detail['tracking_number'] . '"/>';
                              }
                              ?>
                        </td>
                        <?php if($_SESSION['display_cost']):?>
                        <td>Shipping Cost:</td>
                        <td>
                           <?php if (!$vtx) { ?>
                           <input readonly required onkeyup="allowFloat(this); $('.shipping_cost').val($(this).val());" class="shipping_cost" type="text" name="shipping_cost" value="<?php echo $shipment_detail['shipping_cost'];?>"  />
                           <?php } else { ?>
                           <?php echo $shipment_detail['shipping_cost'];?>
                           <?php } ?>
                        </td>
                        <?php endif;?>
                     </tr>
                     <tr>
                        <td>Shipment Number:</td>
                        <td>
                           <?php
                              if ($vtx) {
                                 echo $shipment_detail['package_number'];
                              } else {
                                 echo '<input readonly type="text" onkeyup="$(\'.package_number\').val($(this).val());" name="package_number" class="package_number" value="' . $shipment_detail['package_number'] . '" required />';
                              }
                              ?>
                        </td>
                        <?php }?>
                        <td>
                           <?php if($shipment_detail['status'] != 'Completed' && ($vtx && $shipment_detail['status'] != 'Pending')):?>
                           <input type="submit" name="save" value="Save" />
                           <?php endif;?>
                        </td>
                        <td>&nbsp;</td>
                        <?php if ($_SESSION['login_as'] != 'employee' && !$vtx && $shipment_detail['status'] != 'Completed') { ?>
                        <!-- <td>
                           Transfer to RTV Box :
                        </td>
                        <td>
                           <select name="new_shipment_id" class="new_shipment_id" onchange="$('.new_shipment_id').val($(this).val());" id="new_shipment_id" style="width:150px;">
                              <option value="">Select One</option>
                              <?php foreach($boxes as $box):?>
                              <option value="<?php echo $box['id']; ?>"><?php echo $box['id'] . " -- ". $box['package_number']; ?></option>
                              <?php endforeach;?>
                           </select>
                        </td>
                        <?php if($shipment_detail['status'] == 'Pending'){?>
                        <td>
                           <input type="submit" name="Transfer" value="Transfer" onclick="if(!$('#new_shipment_id').val()){ alert('Please select one PO.'); return false;}" />
                        </td> -->
                        <?php }
                           } ?>
                     </tr>
                  </table>
               </div>
               <div align="center">
                  <br />
                  <?php if (($_SESSION['login_as'] == 'employee' && $shipment_detail['status'] != 'Completed' &&  $shipment_detail['status'] != 'Pending') && $_SESSION['login_as'] != 'employee') { ?>
                  <input type="button" value="Print" onclick="$('.printer').show();" />
                  <?php } ?>
                  &nbsp&nbsp&nbsp&nbsp
                  <?php if($_SESSION['login_as'] == 'admin'):?>
                  <input type="submit" name="delete" value="Delete" onclick="if(!confirm('Are you sure?')){ return false; }" />&nbsp&nbsp&nbsp&nbsp
                  <?php endif;?>
                  <?php if($_SESSION['login_as'] == 'admin' && $shipment_detail['status'] == 'Pending'){?>
                  <input type="submit" name="MoveNTR" value="Move to NTR" onclick="if(!confirm('Are you sure?')){ return false; }" />&nbsp&nbsp&nbsp&nbsp
                  <input type="submit" name="MoveGFS" value="Move to GFS" onclick="if(!confirm('Are you sure?')){ return false; }" />&nbsp&nbsp&nbsp&nbsp
                  <?php } ?>
                  <?php if ($_SESSION['login_as'] == 'employee' && $shipment_detail['status'] != 'Completed' && $shipment_detail['status'] != 'Shipped' && $shipment_detail['status'] != 'Issued' ) { ?>
                  <?php if (($vtx || $_SESSION['login_as'] == 'admin') && ($shipment_detail['status'] != 'Pending')) { ?>
                  <input type="button" onclick="$('.blackPage').show();" value="Add to Shippment" />&nbsp&nbsp&nbsp&nbsp
                  <?php } ?>
                  <?php } ?>
                  <?php if($_SESSION['edit_received_shipment'] && ($_SESSION['login_as'] != 'employee')):?>
                  <input type="submit" name="save" value="Save" /> &nbsp&nbsp&nbsp&nbsp
                  <?php if ($vtx || ($buttonName && $_SESSION['login_as'] != 'employee')) { ?>
                  <button class="buttonChanger" type="submit" name="RejectComplete" value="<?php echo $buttonValue; ?>" onclick="if(!confirm('Are you sure?')){ return false; }">
                  Save And <?php echo $buttonName ?> Shipment
                  </button>
                  <?php } ?>
                  <?php endif;?>
                  <br /><br />
               </div>
               <br /><br />
               <script type="text/javascript">
                  function funcBtnChanger(){
                     myVal = $('#status').val();
                     var changeVal = 'Save and Complete Shipment';
                     var xval = 'Shipped';
                     switch(myVal)
                     {
                        case 'Pending':
                        changeVal='Save And Issue Shipment';
                        xval = 'Issued';
                        break;

                        case 'Issued':
                        changeVal='Save And Ship Shipment';
                        xval = 'Shipped';
                        break;


                        case 'Shipped':
                        changeVal='Save And Recieve Shipment';
                        xval = 'Received';
                        break;
                        case 'Received':
                        changeVal='Save And Complete Shipment';
                        xval = 'Complete';
                        break;
                  // case 'QCd':
                  // changeVal='Save And Complete Shipment';
                  // xval = 'Complete';
                  // break;
                  default:
                  changeVal='Save And Complete Shipment';
                  xval = 'Shipped';
                  break;
                  }
                  if (myVal == 'Completed') {
                  $('.buttonChanger').hide();
                  } else {
                  $('.buttonChanger').show();
                  }
                  $('.buttonChanger').html(changeVal);
                  $('.buttonChanger').val(xval);
                  }
               </script>
               <?php
                  $tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE tracking_code='".$shipment_detail['tracking_number']."'");
                  if($tracker)
                  {
                     ?>
               <table align="center" border="1" cellspacing="0" cellpadding="5" width="70%">
                  <tr>
                     <th colspan="2">Tracking ID: <?=$tracker['tracker_id'];?></th>
                     <th colspan="2" align="right">Code: <?=$tracker['tracking_code'];?></th>
                  </tr>
                  <tr>
                     <th>Date Time</th>
                     <th>Message</th>
                     <th align="center">Status</th>
                     <th>Location</th>
                  </tr>
                  <?php
                     $tracker_statuses = $db->func_query("SELECT * FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' order by id desc");
                     foreach($tracker_statuses as $tracker_status)
                     {
                        $tracker_status['datetime'] = str_replace(array('T','Z'), ' ', $tracker_status['datetime']);
                        $location = json_decode($tracker_status['tracking_location'],true);
                        ?>
                  <tr>
                     <td><?=americanDate($tracker_status['datetime']);?></td>
                     <td><?=$tracker_status['message'];?></td>
                     <td align="center"><?=$tracker_status['status'];?></td>
                     <td><?php echo $location['city'].', '.$location['state'].', '.$location['zip'];?></td>
                  </tr>
                  <?php
                     }?>
               </table>
               <br><br>
               <?php
                  }
                  ?>
               <?php
                  $log= $db->func_query("SELECT * FROM inv_rj_shipment_items_log WHERE `from` = '".$shipment_detail['package_number']."' OR `to` = '".$shipment_detail['package_number']."'"); 
                  
                  ?>
               <table width="80%" border="0" cellpadding="5" cellspacing="0" style="border-collapse:collapse">
                  <tr>
                     <td>
         <form method="post" action="">
         <table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
         <tr>
         <td>
         <b>Comment</b>
         </td>
         <td>
         <textarea rows="5" cols="30" name="comment" ></textarea>
         </td>
         </tr>
         <tr>
         <td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />   </td>
         </tr>       
         </table>
         </form>
         </td>
         <td valign="top">
         <table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
         <tr>
         <th>Date</th>
         <th>Comment</th>
         <th>Added By</th>
         </tr>
         <?php
            $comments = $db->func_query("SELECT * FROM inv_rejected_shipment_comments WHERE rejected_shipment_id='".$shipment_id."'");
            foreach($comments as $comment)
            {
               ?>
         <tr>
         <td><?php echo americanDate($comment['date_added']);?></td>
         <td><?php echo $comment['comment'];?></td>
         <td><?php echo get_username($comment['user_id']);?></td>
         </tr>
         <?php 
            }
            ?> 
         </table>
         </td>
         </tr>
         </table>
         <br><br>
         <div align="center">
         <table align="center" border="1" cellspacing="0" cellpadding="5" width="50%">
         <tr>
         <h1>Item Movement History</h1>
         </tr>
         <thead>
         <tr>
         <th>Date & Time</th>
         <th>Item Movement</th>
         </tr> 
         </thead>
         <tbody>
         <?php foreach ($log as $logg) { ?>
         <tr>
         <td><?php echo americanDate($logg['date_added']); ?></td>
         <td><?php echo $logg['reject_item_id']; ?> <?php echo $logg['log']; ?><?php echo get_username($logg['user_id']); ?></td>
         </tr>
         <?php }  ?> 
         </tbody>
         </table><br></br>
         </div>
         <?php endif;?>
         </div>   
         <div class="blackPage" style="display: none;">
            <div class="whitePage">
               <div class="form">
                  <select name="shipment_id" id="vendor_shipment_id">
                     <option value="">--Create New--</option>
                     <?php if ($shipments) { ?>
                     <?php foreach ($shipments as $key => $row) { ?>
                     <option value="<?php echo $row['id']; ?>"><?php echo $row['package_number']; ?></option>
                     <?php } ?>
                     <?php } ?>
                  </select>
               </div>
               <div class="form">
                  <input type="submit" name="addToShip" value="Submit" onclick="if(!confirm('Are you sure?')){ return false; }" />
                  <input class="button" type="button" value="Cancel" onclick="$('.blackPage').hide();" />
                  <!-- <input type="hidden" name="selected_items1" id="selected_items1" value=""> -->
               </div>
            </div>
         </div>
         <div class="printer" style="display: none;">
            <div class="whitePage">
               <div class="form">
                  <select name="printer_id" id="printer_id">
                     <option value="">Select</option>
                     <?php foreach ($printers as $printer): ?>
                     <option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_item['printer']): ?> selected="selected" <?php endif; ?>>
                        <?php echo $printer['value'] ?>
                     </option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="form">
                  <input type="submit" name="print" value="Submit" onclick="if(!confirm('Are you sure?')){ return false; }" />
                  <input class="button" type="button" value="Cancel" onclick="$('.printer').hide();" />
                  <!-- <input type="hidden" name="selected_items1" id="selected_items1" value=""> -->
               </div>
            </div>
         </div>
         </form>
      </div>

      <script>
         
         <?php if($_SESSION['display_cost']):?>
         
         $('#stt').text('$<?php echo number_format($total_cost, 2); ?>');
         $('#stt1').text('$<?php echo number_format($total_received_cost, 2); ?>');
         $('#stt2').text('$<?php echo number_format($shipped_cost, 2); ?>');
         <?php
         endif;
         ?>
         $(document).ready(function() {
         
         
         $("#allselector").change(function(){
            // console.log(this.checked);
         $(".selection").prop("checked",this.checked)
         });
         
         // $(".selection").click(function () {
         // if ($(this).is(":checked")){
         //  var isAllChecked = 0;
         //  $(".selection").each(function(){
         //    if(!this.checked)
         //       isAllChecked = 1;
         //  })              
         //  if(isAllChecked == 0){ $("#allselector").prop("checked", true); }     
         // }
         // else {
         //  $("#allselector").prop("checked", false);
         // }
         // });
         });
         
         
         $("#allselector1").change(function(){
         if(this.checked){
          $(".selectors").each(function(){
            this.checked=true;
          })              
         }else{
          $(".selectors").each(function(){
            this.checked=false;
          })              
         }
         });
         
         $(".selectors").click(function () {
         if ($(this).is(":checked")){
          var isAllChecked = 0;
          $(".selectors").each(function(){
            if(!this.checked)
               isAllChecked = 1;
          })              
          if(isAllChecked == 0){ $("#allselector1").prop("checked", true); }     
         }
         else {
          $("#allselector1").prop("checked", false);
         }
         });
         
         
         
         
         
         
      </script>
      <script type="text/javascript" src="js/newmultiselect.js"></script>
      <script type="text/javascript">
         function editCost(){
            $('.cost_input').removeAttr("readonly");
            $('.cost_input').css("background-color", "yellow");
         }

         function editProductionDate(){
            $('.production_date_input').removeAttr("readonly");
            $('.production_date_input').css("background-color", "yellow");
         }
      </script>
      <script type="text/javascript">
         $(function () {
            $('#table1').multiSelect({
               actcls: 'highlightx',
               selector: 'tbody .list_items',
               except: ['form'],
               callback: function (items) {
                  traverseCheckboxes('#table1', '.selection');
               }
            });
         })
      </script>
      <script type="text/javascript">
         $(function () {
            $('#table2').multiSelect({
               actcls: 'highlightx',
               selector: 'tbody .list_items',
               except: ['form'],
               callback: function (items) {
                  traverseCheckboxes();traverseCheckboxes('#table2', '.selection');
               }
            });
         })
      </script>
   </body>
   <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
   <script type="text/javascript">
      $(document).ready(function() {
         // $(".tablesorter").tablesorter();
      });
   </script>
</html>

