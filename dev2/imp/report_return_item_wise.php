  <?php
include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
//page_permission('item_wise_return_report');
if (!$_SESSION['item_wise_return_report']) {
  echo 'You dont have permission to manage users.';
  exit;
}
$RMA_STATUS[] = array("id" => "Awaiting", "value" => "Awaiting");
$RMA_STATUS[] = array("id" => "Received", "value" => "Received");
$RMA_STATUS[] = array("id" => "In QC", "value" => "In QC");
$RMA_STATUS[] = array("id" => "Completed", "value" => "Completed");
$start_date = $db->func_escape_string($_REQUEST['start_date']);
  if (!$start_date) {
    $start_date = date('y-m-d', strtotime('-30 days'));
    $_REQUEST['submit'] = 1;
    $_REQUEST['start_date'] = $start_date;
  }
  $end_date = $db->func_escape_string($_REQUEST['end_date']);
  if (!$end_date) {
    $end_date = date('Y-m-d');
    $_REQUEST['submit'] = 1;
    $_REQUEST['end_date'] = $end_date;
  }
if(isset($_REQUEST['submit'])){
  $inv_query   = '';
  
  $parameters  = $_SERVER['QUERY_STRING'];
  
  $sku = strtolower($db->func_escape_string($_REQUEST['sku']));
  $store_type   = $db->func_escape_string($_REQUEST['store_type']);
  $source   = $db->func_escape_string($_REQUEST['source']);
  $return_code = $db->func_escape_string($_REQUEST['return_code']);
  $decision = $db->func_escape_string($_REQUEST['decision']);
  $item_condition = $db->func_escape_string($_REQUEST['item_condition']);
 /* $start_date = $db->func_escape_string($_REQUEST['start_date']);
  $end_date = $db->func_escape_string($_REQUEST['end_date']);*/
  $rma_status = $db->func_escape_string($_REQUEST['rma_status']);      
  if(@$sku){
    $conditions[] =  " LOWER(b.sku) LIKE '%".$sku."%' ";
  }
  if(@$rma_status){
    $conditions[] =  " LCASE(a.rma_status)=LCASE('".$rma_status."') ";
  }
  if(@$store_type){
    $conditions[] =  " a.store_type='$store_type' ";
  }
  if(@$source){
    $conditions[] =  " a.source='".$source."' ";
  }
  
  if(@$return_code){
    $conditions[] =  " b.return_code='$return_code' ";
  }
  
  if(@$decision){
    $conditions[] =  " b.decision='$decision' ";
  }
  
  if(@$item_condition){
    $conditions[] =  " b.item_condition='$item_condition' ";
  }
  
  if(@$start_date && $end_date)
  {
    $conditions[] =  " (a.date_added BETWEEN '$start_date' and '$end_date') ";
  }
  
  $condition_sql = implode(" AND " , $conditions);
  
  
  if(!$condition_sql){
    $condition_sql = ' 1 = 1';
  }
  
  $inv_query = "SELECT b.sku,COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id  AND $condition_sql GROUP BY b.sku ORDER BY COUNT(b.sku) DESC";
         //echo $inv_query;exit;
  
}
else{
  $inv_query = "SELECT b.sku,COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id GROUP BY b.sku ORDER BY COUNT(b.sku) DESC";
}
if(isset($_GET['page'])){
  $page = intval($_GET['page']);
}
if($page < 1){
  $page = 1;
}
$max_page_links = 10;
$num_rows = 500;
$start = ($page - 1)*$num_rows;
$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "report_return_item_wise.php",$page);
$inv_orders = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link rel="stylesheet" href="include/jquery-ui.css">
  <script src="js/jquery.min.js"></script>
  <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
  <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
  <script src="js/jquery-ui.js"></script>
  <title>Report Return Item Wise</title>
  
  
</head>
<body>
  <?php include_once 'inc/header.php';?>
  <?php if(@$_SESSION['message']):?>
    <div align="center"><br />
      <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
    </div>
  <?php endif;?>
  
  <br />
  
  <br />
  
  <h2 align="center">Item Wise Return Report</h2>
  
  <h3 align="center">
    <?php
    if($_SESSION['login_as']=='admin' || $_SESSION['item_wise_return_report'])
    {
      ?>
      
      <a href="javascript:void(0);" onclick="generatePDF();" class="button">Generate PDF Report</a>
      <?php
    }
    ?>
  </h3>
  
  <form name="order" action="" method="get">
    <table width="90%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>
        <tr>
          
          
          
          <td>
            <label for="start_date">SKU:</label>
            <input type="text" name="sku" value="<?php echo @$_REQUEST['sku'];?>" />
          </td>
          <td>
            <label for="store_type">Store Type</label>
            <select name="store_type">
             <option value="">Please Select</option>
             <option value="web" <?php echo (@$_REQUEST['store_type']=='web'?'selected':'');?>>Web</option>
             <option value="channel_advisor" <?php echo (@$_REQUEST['store_type']=='channel_advisor'?'selected':'');?>>Channel Advisor</option>
             <option value="bigcommerce" <?php echo (@$_REQUEST['store_type']=='bigcommerce'?'selected':'');?>>BigCommerce</option>
             <option value="ebay" <?php echo (@$_REQUEST['store_type']=='ebay'?'selected':'');?>>eBay</option>
             <option value="storefront" <?php echo (@$_REQUEST['store_type']=='storefront'?'selected':'');?>>Store Front</option>
           </select>
         </td>
         <td>
          <label for="source">Source</label>
          <select name="source">
           <option value="">Please Select</option>
           <option value="mail" <?php echo (@$_REQUEST['source']=='mail'?'selected':'');?>>Mail</option>
           <option value="storefront" <?php echo (@$_REQUEST['source']=='storefront'?'selected':'');?>>Store Front</option>
           
         </select>
       </td>
       
       <td>
         <?php
         $reasons = $db->func_query("SELECT * FROM inv_reasons");
         
         ?>
         <label for="return_code">Return Reason</label>
         <select name="return_code">
           <option value="">Please Select</option>
           <?php
           foreach($reasons as $reason)
           {
            ?>
            <option value="<?php echo $reason['title'];?>" <?php echo (@$_REQUEST['return_code']==$reason['title']?'selected':'');?>><?php echo $reason['title'];?></option>
            <?php   
          }
          
          
          
          ?>
          
        </select>
      </td>
      
      <td>
       <?php
       $decisions = $db->func_query("SELECT DISTINCT decision FROM inv_return_items WHERE decision<>''");
       
       ?>
       <label for="decision">Decision</label>
       <select name="decision">
         <option value="">Please Select</option>
         <?php
         foreach($decisions as $decision)
         {
          ?>
          <option value="<?php echo $decision['decision'];?>" <?php echo (@$_REQUEST['decision']==$decision['decision']?'selected':'');?>><?php echo $decision['decision'];?></option>
          <?php   
        }
        
        
        
        ?>
        
      </select>
    </td>
    
    <td>
     <?php
     $conditions1 = $db->func_query("SELECT DISTINCT item_condition FROM inv_return_items WHERE item_condition<>''");
     
     ?>
     <label for="item_condition">Condition</label>
     <select name="item_condition">
       <option value="">Please Select</option>
       <?php
       foreach($conditions1 as $item_condition)
       {
        ?>
        <option value="<?php echo $item_condition['item_condition'];?>" <?php echo (@$_REQUEST['item_condition']==$item_condition['item_condition']?'selected':'');?>><?php echo $item_condition['item_condition'];?></option>
        <?php   
      }
      
      
      
      ?>
      
    </select>
  </td>
  <td>
   <label for="rma_status">Status</label>
   <?php echo createField("rma_status", "rma_status", "select", $_REQUEST['rma_status'], $RMA_STATUS); ?>
 </td>
 <td> <label for="start_date">Start / End Date</label>
  <input style="width:140px" type="text" placeholder="Start Date" name="start_date" value="<?php echo $_REQUEST['start_date'];?>" class="datepicker" readOnly>
  <input style="width:140px" type="text" placeholder="End Date" name="end_date" value="<?php echo $_REQUEST['end_date'];?>" class="datepicker" readOnly>
</td>


<td><input type="submit" value="Search" name="submit" style="margin: 10px 0 0 60px"></td>
</tr>
<tr>
  <?php if($inv_orders):?>
    <td colspan="9">
      <table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
        <thead>
          <tr style="background-color:#e5e5e5;">
            <th></th>
            <th>SN</th>
            <th>SKU</th>
            <th>Item Name</th>
            <th>Completed</th>
            <th>Awaiting</th>
            <th>Total Returns</th>
            <th>Weekly Average</th>
            <th>Action</th>
            
          </tr>
        </thead>
        <?php $i = $splitPage->display_i_count();
        ?>
        <?php
        foreach($inv_orders as $return)
        {
          ?>
          <tr id="<?php echo $return['sku'];?>">
            <td align="center"><input type="checkbox" class="return_checks" checked value="<?php echo $return['sku'];?>">
              <td align="center"><?php echo $i; ?></td>
              
              <td align="center"><?php echo linkToProduct($return['sku'], $host_path);?></td>
              <td ><?php   echo $db->func_query_first_cell("SELECT
                b.`name`
                FROM
                `oc_product` a
                INNER JOIN `oc_product_description` b
                ON (a.`product_id` = b.`product_id`) WHERE a.sku='".$return['sku']."'");?></td>
                
                
                <td align="center">
                  <?php echo $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id ".($condition_sql?' AND ':'')." $condition_sql AND b.sku='".$return['sku']."' AND a.rma_status='Completed'");
                  ?>
                </td>
                
                <td align="center">
                  <?php echo $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id ".($condition_sql?' AND ':'')."  $condition_sql AND b.sku='".$return['sku']."' AND a.rma_status='Awaiting'");
                  ?>
                </td>
                
                
                <td align="center"><?php echo @$return['count_sku'];?></td>
                <td align="center"><?php echo getWeeklyAverageOfReturnsBySKU($return['sku'],$condition_sql);?></td>
                
                
                <td align="center">
                  <a href="<?php echo $host_path;?>/popupfiles/view_item_wise_summary.php?sku=<?php echo $return['sku']?>&start_date=<?=$_REQUEST['start_date'];?>&end_date=<?=$_REQUEST['end_date'];?>&conditions=<?php echo base64_encode(implode(",",$conditions)); ?>" class="fancybox3 fancybox.iframe">View Summary</a>
                </td>
              </tr>
              <?php $i++;  ?>
              <?php
            }
            ?>
            
          </table>
        </td>  
        
      <?php else : ?> 
        
        <td colspan=4><label style="color: red; margin-left: 600px;">No Record Found</label></td>
        
      <?php endif;?>
    </tr>
    
    <tr>
     <td colspan="5" align="left">
       <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
     </td>
     
     <td colspan="6" align="right">
      <?php echo $splitPage->display_links(10,$parameters);?>
    </td>
  </tr>
</tbody>
</table>
</form>
</body>
</html>
<script>
  function generatePDF()
  {
    var checkedValues = $('.return_checks:checkbox:checked').map(function() {
      return this.value;
    }).get();
    var win = window.open('pdf_return_item_wise.php?items='+checkedValues+'&<?=$parameters;?>', '_blank');
    win.focus();
  }
</script>