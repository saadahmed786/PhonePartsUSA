<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
page_permission("device_list");
$table = "inv_devices";
$query_str = "";

$device_type_id = $_POST['device_type_id'];
$make = $_POST['make'];
$grade_id = $_POST['grade_id'];
$sku = $_POST['sku'];
$model_id = $_POST['model_id'];
$location_id = $_POST['location_id'];
$imei = $_POST['imei'];
$status_id = $_POST['status_id'];

if($device_type_id)
{
$query_str.=" AND b.device_type_id='".(int)$device_type_id."'"	;
}
if($make)
{
$query_str.=" AND b.manufacturer_id='".(int)$make."'"	;
}

if($grade_id)
{
$query_str.=" AND a.grade_id='".(int)$grade_id."'"	;
}
if($sku)
{
$query_str.=" AND a.sku LIKE '%".$sku."%'"	;
}
if($model_id)
{
$query_str.=" AND a.model_id='".(int)$model_id."'"	;
}

if($location_id)
{
$query_str.=" AND a.location_id='".(int)$location_id."'"	;
}
if($imei)
{
$query_str.=" AND a.imei LIKE '%".$imei."%'"	;
}
if($status_id)
{
$query_str.=" AND a.status_id='".(int)$status_id."'"	;
}

$inv_query = "SELECT a.* FROM inv_devices a INNER JOIN inv_d_model b ON (a.model_id=b.id) where 1 = 1 $query_str ";

//$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "device_list.php",$page);

$results  = $db->func_query($inv_query);

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="include/table_sorter.css" rel="stylesheet" type="text/css" />
     
        
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <title>Device List</title>
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div align="center">
	        <form name="order" action="" method="post">
	             <table width="60%" cellpadding="10" border="1"  align="center">
	             	  <tr>
	             	  	 <td align="center">Type</td>
	             	  	 <td align="center">
	             	  	 <select name="device_type_id">
                         <option value="">Please select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_type WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($row['id']==$_POST['device_type_id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
						 }
						 ?>
                         </select>
	             	  	 </td>
	             	  	 
	             	  	 <td align="center">Make</td>
	             	  	 <td align="center">
	             	  	 	  <select name="make">
                         <option value="">Please select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_manufacturer order by name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($row['id']==$_POST['make']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
						 }
						 ?>
                         </select>
	             	  	 </td>
                         	 <td align="center">Grade</td>
	             	  	 <td align="center">
	             	  	 	 <select name="grade_id">
                         <option value="">Please select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_grade WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($row['id']==$_POST['grade_id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
						 }
						 ?>
                         </select>
	             	  	 </td>
                         </tr>
                         <tr>
                         	 <td align="center">ID</td>
	             	  	 <td align="center">
	             	  	 	 <input style="width:100px" type="text" name="sku" value="<?php echo @$_REQUEST['sku'];?>" />
	             	  	 </td>
                         
                         
                          <td align="center">Model</td>
	             	  	 <td align="center">
	             	  	 	  <select name="model_id">
                         <option value="">Please select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_model WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($row['id']==$_POST['model_id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
						 }
						 ?>
                         </select>
                             
	             	  	 </td>
                         
                        <td align="center">Location</td>
	             	  	 <td align="center">
	             	  	 	  <select name="location_id">
                         <option value="">Please select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_location WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($row['id']==$_POST['location_id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
						 }
						 ?>
                         </select>
                             
	             	  	 </td>
	             	  	 </tr>
                         <tr>
                         	 <td align="center">IMEI</td>
	             	  	 <td align="center">
	             	  	 	 <input style="width:100px" type="text" name="imei" value="<?php echo @$_REQUEST['imei'];?>" />
	             	  	 </td>
                         
                          	 <td align="center">P/F</td>
	             	  	 <td align="center">
	             	  	 	 	  <select name="status_id">
                         <option value="">Please select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_status WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($row['id']==$_POST['status_id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
						 }
						 ?>
                         </select>
	             	  	 </td>
	             	  	 
                         
	             	  	 
	             	  	 <td align="center"><input type="submit" name="search" value="Search" class="button" /></td>
                         <td align="center"><a href="devices_settings.php" class="fancybox3 fancybox.iframe">Settings</a> </td>
	             	  </tr>   
	             </table>
	        </form>
	       <a href="devices_detail.php" class="button">Add Device</a>  <a href="device_list_export.php?action=export&device_type_id=<?=$_POST['device_type_id'];?>&make=<?=$_POST['make'];?>&grade_id=<?=$_POST['grade_id'];?>&sku=<?=$_POST['sku'];?>&model_id=<?=$_POST['model_id'];?>&location_id=<?=$_POST['location_id'];?>&status_id=<?=$_POST['status_id'];?>" class="button">Export Devices</a>
	        <table border="1" cellpadding="5" cellspacing="0" width="98%" class="tablesorter">
            <thead>
        		<tr style="background:#e5e5e5;">
        			<th>Type</th>
        			<th>ID</th>
        			<th>Make</th>
        			<th>Model</th>
                    <th>IMEI</th>
                    <th>P/F</th>
        			
        			<th>Grade</th>
        		<th>Issue(s)</th>
                    
        			<th>Location</th>
        			
        			<th>Action</th>
        	   </tr>
               </thead>
               <tbody>
		       <?php
			   
			    if($results){?>
		       		<?php foreach($results as $i => $result):?>
                    
                    <?php
					$model_info = $db->func_query_first("SELECT * FROM inv_d_model WHERE id='".$result['model_id']."'");
					?>
					       <tr>
					       		
			        			<td align="center"><?=getResult("SELECT name FROM inv_d_type WHERE id='".$model_info['device_type_id']."'")?></td>
			        			<td align="center"><?php echo $result['sku']?></td>
			        			<td align="center"><?=getResult("SELECT name FROM inv_d_manufacturer WHERE id='".$model_info['manufacturer_id']."'")?></td>
                                <td align="center"><?=$model_info['name'];?></td>
                                <td align="center"><?=$result['imei'];?></td>
			        			
			        			<td align="center"><?=getResult("SELECT name FROM inv_d_status WHERE id='".$result['status_id']."'")?></td>
			        			<td align="center"><?=getResult("SELECT name FROM inv_d_grade WHERE id='".$result['grade_id']."'")?></td>
			        			<td align="center">
                                <?php
								$issues = $db->func_query("SELECT * FROM inv_devices_issues WHERE device_id='".$result['id']."'");
								foreach($issues as $issue)
								{
								echo getResult("SELECT name FROM inv_d_issue WHERE id='".$issue['issue_id']."'")."<br />";
									
								}
								?>
                                </td>
			        			<td align="center"><?=getResult("SELECT name FROM inv_d_location WHERE id='".$result['location_id']."'")?></td>
			        			<td align="center">
			        				<a href="<?php echo $host_path;?>devices_detail.php?mode=edit&id=<?php echo $result['id']?>">Edit Device</a>
			        			</td>
					       </tr>
					<?php endforeach;?>       
		       <?php 
			   }
			   else
			   {
				?>
                <tr>
                <td align="center" colspan="10">No Result</td>
                </tr>
                <?php   
			   }
			   
			   
			   		
			   ?>
		       
		       	</tbody>
	        </table>
        </div>
    </body>
</html>
<script>
  $(document).ready(function(e) {
            $('.fancybox3').fancybox({    width: '90%',
   height: 600,
   fitToView : false,
   autoSize : false
   });
        });
</script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
         <script>
		 $(document).ready(function(e) {
             $(".tablesorter").tablesorter(); 
        });
		 </script>