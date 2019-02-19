<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
if($_SESSION['login_as'] != 'admin'){
	$_SESSION['message'] = 'You dont have permission to manage users.';
	header("Location:$host_path/sales.php");
	exit;
}

if(isset($_POST['add']))
{
	$sku = urldecode($_POST['sku']);
	$name = urldecode($_POST['name']);
	$manufacturers = $_POST['manufacturer'];
	$devices = $_POST['device'];
	$models = $_POST['model'];
	$attribs = $_POST['attrib'];
	
	if(!$manufacturers)
	{
	echo 'Please select manufacturer';exit;	
	}
	if(!$devices)
	{
	echo 'Please select device';exit;	
	}
	if(!$models)
	{
	echo 'Please select models';exit;	
	}
	if(!$attribs)
	{
	echo 'Please select attributes';exit;	
	}
	
	$db->db_exec("delete from inv_device_product where sku = '".$sku."'");
	
	$array = array();
	
	$array['sku'] = $sku;
	$array['name'] = $name;
	$array['date_added'] = date('Y-m-d h:i:s');
	$array['added_by'] = $_SESSION['login_as'];
 	
	$prod_id = $db->func_array2insert("inv_device_product",$array);
	
	
	foreach($manufacturers as $manufacturer)
	{
	$array = array();
	
	$array['device_product_id'] = $prod_id;
	$array['manufacturer_id'] = $manufacturer;
	
 	
	$manuf_id = $db->func_array2insert("inv_device_manufacturer",$array);
	
		foreach($devices as $device)
		{
			$xdevice = explode("-",$device);
				
				if($xdevice[1]==$manufacturer)
				{
			$array = array();
	
	$array['device_manufacturer_id'] = $manuf_id;
	$array['device_id'] = $device;
	
 	
	$device_id = $db->func_array2insert("inv_device_device",$array);
			
			foreach($models as $model)
			{
				
				$xmodel = explode("-",$model);
				
				if($xmodel[1]==$xdevice[0])
				{
					
					$array = array();
	
	$array['device_device_id'] = $device_id;
	$array['model_id'] = $xmodel[0];
	
 	
	$model_idx = $db->func_array2insert("inv_device_model",$array);
					
					
					foreach($attribs as $attrib)
			{
				
				
				
				
					
					$array = array();
	
	$array['device_model_id'] = $model_idx;
	$array['attrib_id'] = $attrib;
	
 	
	$model_id = $db->func_array2insert("inv_device_attrib",$array);
					
				
				
			}
					
				}
				
			}
				}
		}
	
	}
	
	
echo 'Record Saved';exit;	
}
if(isset($_POST['action']) && $_POST['action']=='ajax')
{
	if($_POST['type']=='device')
	{
		$i = $_POST['i'];
		$manufacturers = implode(",",$_POST['manufacturers']);
		if(empty($manufacturers)) exit;
		$rows = $db->func_query("SELECT * FROM inv_model_mt WHERE manufacturer_id IN (".$manufacturers.")");
		echo '<select name="device[]" id="device'.$i.'" multiple="multiple" onchange="populateModel('.$i.')">	';
		foreach($rows as $row)
		{
			
		echo '<option value="'.$row['model_id'].'-'.$row['manufacturer_id'].'">'.$row['device'].'</option>';	
			
		}
		echo '</select>';
	}
	
	
	if($_POST['type']=='model')
	{
		$i = $_POST['i'];
		$models = $_POST['models'];
		if(empty($models)) exit;
		
		echo '<select name="model[]" id="model'.$i.'" multiple="multiple" ">	';
		foreach($models as $model)
		{
			$xmodel = explode("-",$model);
		$rows = $db->func_query("SELECT
mc.`id`,d.`sub_model`,d.`model_id`,d.`sub_model_id`,mc.`carrier_id`,c.`name`
FROM
    `inv_model_dt` d
    INNER JOIN `inv_model_carrier` mc
        ON (d.`sub_model_id` = mc.`sub_model_id`)
    INNER JOIN `inv_carrier` c
        ON (mc.`carrier_id` = c.`id`)
		
		WHERE d.model_id =".$xmodel[0]."
		");
		foreach($rows as $row)
		{
			
		echo '<option value="'.$row['id'].'-'.$row['model_id'].'">'.$row['sub_model'].' ('.$row['name'].')'.'</option>';	
			
		}
		}
		echo '</select>';
	}
	if($_POST['type']=='attribs')
	{
		$sku_type = $_POST['sku_type'];
		$i = $_POST['i'];
	$attrib_groups = 	$db->func_query_first("SELECT attribute_group_id FROM inv_product_skus WHERE id=".(int)$sku_type);
		if($attrib_groups['attribute_group_id']=='')
		{
		echo 'No Attribute Defined for this SKU Type';	
		}
		else
		{
			$attrib_groups =  rtrim($attrib_groups['attribute_group_id'],",");
			$attrib_groups = explode(",",$attrib_groups);
			foreach($attrib_groups as $attrib_group)
			{
				$group_info = $db->func_query_first("SELECT name FROM inv_attribute_group WHERE id='".$attrib_group."'");
			$rows = 	$db->func_query("SELECT * FROM inv_attr WHERE attribute_group_id='".(int)$attrib_group."'");	
				
				echo '<strong>'.$group_info['name'].'</strong><br />';
				
				foreach($rows as $row)
				{
				echo '<input type="checkbox" name="attrib[]" value="'.$row['id'].'"> '.$row['name'].'<br />';	
					
				}
			}
		}
	}
	
	if($_POST['type']=='verify')
	{
		$device_product_id = $_POST['device_product_id'];
		$db->db_exec("UPDATE inv_device_product SET verified='1',verified_by='".$_SESSION['login_as']."' WHERE device_product_id='".(int)$device_product_id."'");
	
	echo "Updated";	
	}
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

$product_id = $_GET['product_id'];
$product_infos = "SELECT
a.`product_id`,a.`sku`,b.name as title
FROM
    `oc_product` a
    INNER JOIN `oc_product_description` b
        ON (a.`product_id` = b.`product_id`) WHERE a.status=1 ORDER BY a.sku";
		
		
		$splitPage  = new splitPageResults($db , $product_infos , $num_rows , "devices_new.php",$page);
$product_infos = $db->func_query($splitPage->sql_query);
		
		$manufacturers = $db->func_query("select * from inv_manufacturer WHERE status=1");
		$sku_types		=	$db->func_query("SELECT * from inv_product_skus");
	
		
		

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?php echo $title;?></title>
        
         <script type="text/javascript" src="js/jquery.min.js"></script>
        
<style>
.multiple{
width:	
}

</style>        
        <script>
		function populateDevice($i)
		{
			$('#div_sku_type'+$i).hide();
			$('#div_model'+$i).html('');
			var manufacturers = $('#manufacturer'+$i).val();
			      $.ajax({
                url: "devices_new.php",
				type:"POST",
                data: {manufacturers: manufacturers,action:'ajax',type:'device',i:$i},
                success: function(data) {
					
					
					$('#div_device'+$i).html(data);
					}
            });
			
			
			
		}
		
		
		function populateModel($i)
		{$('#div_sku_type'+$i).hide();
			
			var models = $('#device'+$i).val();
			      $.ajax({
                url: "devices_new.php",
				type:"POST",
                data: {models: models,action:'ajax',type:'model',i:$i},
                success: function(data) {
					
					
					$('#div_model'+$i).html(data);
					populateAttributes($i);
					$('#div_sku_type'+$i).show();
					}
            });
			
			
			
		}
		
		function populateAttributes($i)
		{
			
			var sku_type = $('#sku_type'+$i).val();
			      $.ajax({
                url: "devices_new.php",
				type:"POST",
                data: {sku_type: sku_type,action:'ajax',type:'attribs',i:$i},
                success: function(data) {
					
					
					$('#div_attribs'+$i).html(data);
					
					}
            });
			
			
			
		}
		function submitThis(i)
		{
			
			var checked1 = []
$('#tr_'+i+' input[name=attrib\\[\\]]:checked').each(function ()
{
    checked1.push(parseInt($(this).val()));
});
			
			  $.ajax({
                url: "devices_new.php",
				type:"POST",
                data: {sku:encodeURIComponent($('#sku'+i).val()),name:encodeURIComponent($('#name'+i).val()),manufacturer:$('#manufacturer'+i).val(),device:$('#device'+i).val(),model:$('#model'+i).val(),attrib:checked1,add:'save'},
                success: function(data) {
					
					alert(data);
				
					
					}
            });	
			
		}
		
		function verifyThis(device_product_id)
		{
			
			 $.ajax({
                url: "devices_new.php",
				type:"POST",
                data: {device_product_id:device_product_id,action:'ajax',type:'verify'},
                success: function(data) {
					
					alert(data);
					location.reload();
				
					
					}
            });	
			
		}
		function toggleCheck(obj)
		{
			$('.checkboxes').prop('checked',obj.checked);	
			traverseCheckboxes();
		}
		function traverseCheckboxes()
		{
			var Val = '';	
			$('.checkboxes').each(function(index, element) {
                if($(element).is(":checked"))
				{
					Val += $(element).val()+',';	
					
				}
            });
			$('#selected_items').val(Val);
		}
		</script>
	</head>
	<body>
		<div align="center">
			<div align="center"> 
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			  <form id="myForm" action="" method="post">
              <table border="1" width="98%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
                <tr style="background-color:#e7e7e7;font-weight:bold">
			 	  	 	<td width="1" style="text-align:center"><input type="checkbox" onclick="toggleCheck(this)" /></td>
                         <td>SKU</td>
			 	  	 	 <td>Product</td>
                         <td>Manufacturer</td>
			 	  	 	<td>Device</td>
			 	  	 	 <td>Model / Sub Model</td>
			 	  	 	 <td style="display:none">SKU Type</td>
			 	  	 	 <td>Attributes</td>
                         <td>Completed By</td>
			 	  	 	 <td  align="center">Action</td>
			 	  	 </tr>
                     <?php
					 $i=0;
					 foreach($product_infos as $product_info)
					 {
					 	$my_sku = substr($product_info['sku'],0,7);
					 ?>
               <tr id="tr_<?php echo $i;?>">
               <td style="text-align:center"><input type="checkbox" class="checkboxes" onclick="traverseCheckboxes()" value="<?php echo $product_info['sku'];?>" /></td>
                	 <td><?php echo $product_info['sku'];?></td>
			 	  	 	 <td><?php echo $product_info['title'];?></td>
                         <td>
						 <select name="manufacturer[]" id="manufacturer<?php echo $i;?>" multiple="multiple" class="multiple" onchange="populateDevice(<?php echo $i;?>)">
                         <?php
						 $man_query1 = $db->func_query_first("SELECT * FROM inv_device_product WHERE sku='".$product_info['sku']."'");
						 $xmanu_id ='';
						 $device_did='';
						  $device_model='';
                         foreach($manufacturers as $manufacturer)
						 {
							 
							 
							 $man_query2=$db->func_query_first("SELECT * FROM inv_device_manufacturer WHERE device_product_id='".$man_query1['device_product_id']."' AND manufacturer_id='".$manufacturer['manufacturer_id']."'");
							?>
                            <option value="<?php echo $manufacturer['manufacturer_id'];?>" <?php if($man_query2){ echo 'selected';  $xmanu_id.=$man_query2['device_manufacturer_id'].',';} ?>><?php echo $manufacturer['name'];?></option>
                            <?php 
							 
						 }
						 $xmanu_id = rtrim($xmanu_id,",");
						 ?>
                         
                         </select>
                         
						 </td>
                         <td><div id="div_device<?php echo $i;?>">
                         <?php
						 
					if($xmanu_id)
					{
					 $man_query3 = $db->func_query("SELECT * FROM inv_device_device WHERE device_manufacturer_id IN ($xmanu_id)");
					 
					 
						 foreach($man_query3 as $query)
						 {
							 $device_did.=$query['device_device_id'].',';
							echo getResult("SELECT device FROM inv_model_mt WHERE model_id='".$query['device_id']."'")."<br>"; 
							 
						 }
						 $device_did = rtrim($device_did,",");
					}
						 
						 ?>
                         
                         </div></td>
			 	  	 	<td><div id="div_model<?php echo $i;?>"><?php
                        if($device_did)
					{
					 $man_query4 = $db->func_query("SELECT * FROM inv_device_model WHERE device_device_id IN ($device_did)");
					
					 
						 foreach($man_query4 as $query)
						 {
							 $device_model.=$query['device_model_id'].',';
							 
							$resultx =  getResult("SELECT sub_model_id FROM inv_model_carrier WHERE id='".$query['model_id']."'");
							//echo $resultx;
							echo getResult("SELECT sub_model FROM inv_model_dt WHERE sub_model_id='".$resultx."'")."<br>";
							 
						 }
						 $device_model = rtrim($device_model,",");
					}
						
						?></div></td> 
			 	  	 	
			 	  	 	 <td style="display:none"><div id="div_sku_type<?php echo $i;?>" style="display:none">
                         
                          <select name="sku_type" id="sku_type<?php echo $i;?>" onchange="populateAttributes(<?php echo $i;?>)">
                          <?php
						  foreach($sku_types as $sku_type)
						  {
							?>
                            <option value="<?php echo $sku_type['id'];?>" <?php if($my_sku == $sku_type['sku']) echo 'selected'; ?>><?php echo $sku_type['sku'];?></option>
                            <?php  
							  
						  }
						  
						   ?>
                          </select>
                         </div></td>
			 	  	 	 <td><div id="div_attribs<?php echo $i;?>"><?php
                        if($device_model)
					{
					 $man_query4 = $db->func_query("SELECT DISTINCT attrib_id FROM inv_device_attrib WHERE device_model_id IN ($device_model)");
					 
					 
						 foreach($man_query4 as $query)
						 {
							 
							//echo $resultx;
							echo getResult("SELECT name FROM inv_attr WHERE id='".$query['attrib_id']."'")."<br>";
							 
						 }
						 $device_model = rtrim($device_model,",");
					}
						
						?></div></td>
                        
                        <td><?=($man_query1['added_by']?$man_query1['added_by']:'Not Mapped');?> (<?=($man_query1['verified']==1?'Verified':'Unverified');?>)</td>
			 	  	 	 <td  align="center"><input type="button" class="button" name="add" value="Update" onclick="submitThis(<?php echo $i;?>)" />  
                         <?php 
						 if($man_query1['added_by'])
						 {
							 if($man_query1['verified']==0 or $man_query1['verified_by']==$_SESSION['login_as'])
							 {
							 ?>
                         <input type="button" class="button" value="Verify" onclick="verifyThis(<?php echo $man_query1['device_product_id'];?>)" />
                         <?php
							 }
							 ?>
                         <?php
						 }
						 ?>
                          <input type="hidden" id="sku<?php echo $i;?>" value="<?php echo $product_info['sku'];?>" /><input type="hidden" id="name<?php echo $i;?>" value="<?php echo $product_info['title'];?>" />
                          
                         
                           </td>
               </tr>
               
               <?php
			   $i++;
					 }
					 ?>
                     <tr>
	                      <td  align="left" colspan="3">
	                         <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
	                      </td>
	                      
	                      <td align="center" colspan="4"  >
	                      	  <form method="get">
	                      	  	  Page: <input type="text" name="page" value="<?php echo $page;?>" size="3" maxlength="3" />
	                      	  	  <input type="submit" name="Go" value="Go" />
	                      	  </form>
	                      </td>
	                      
	                      <td align="right" colspan="2" >
	                      		<?php echo $splitPage->display_links(10,$parameters);?>
	                      </td>
	                 </tr>
               </table>
               <input type="hidden" id="selected_items" value="" />
              </form>
             
            
		 </div>
         
	</body>
</html>			 		 