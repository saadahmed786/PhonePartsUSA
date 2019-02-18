<?php

require_once("auth.php");
require_once("inc/functions.php");
page_permission("add_device");
$table = "inv_devices";
$page = 'devices_detail.php';
$title = "Add Device";
$mode = $_GET['mode'];
if($_POST['action']=='get_sku')
{
	$type_id = $db->func_query_first_cell("SELECT device_type_id FROM inv_d_model WHERE id='".$_POST['model_id']."'");
	$code = $db->func_query_first_cell("SELECT code FROM inv_d_type WHERE id='".(int)$type_id."'");
	$max = $db->func_query_first_cell("select max(replace(sku,'$code','')) FROM $table");
	if(!$max)
	{
		$max=0;	
	}
	$max = $max+1;
	$max = str_pad($max,"6","0",STR_PAD_LEFT);
	$json = array();
	$json['sku'] = $code.$max;
	echo json_encode($json);
	exit;
}
if($_GET['action']=='remove')
{
$db->db_exec("DELETE FROM inv_devices_images WHERE id='".(int)$_GET['image_id']."'");
	$db->db_exec("INSERT INTO inv_devices_history SET device_id='".$id."',text='An image is deleted',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");
$_SESSION['message'] = 'Image Deleted';	
header("Location: $page?mode=edit&id=".$_GET['id']);
exit;
}
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$result = $db->func_query_first("select * from $table where id = '$id'");
	$_devices_issues = $db->func_query("SELECT issue_id FROM inv_devices_issues WHERE device_id='".$result['id']."'");
	$_devices_accessories = $db->func_query("SELECT accessories_id FROM inv_devices_accessories WHERE device_id='".$result['id']."'");
	$devices_issues = array();
	$devices_accessories = array();
	foreach($_devices_issues as $row)
	{
		$devices_issues[] = $row['issue_id'];
		
	}
	
	foreach($_devices_accessories as $row)
	{
		$devices_accessories[] = $row['accessories_id'];
		
	}
}
if($mode=='delete')
{
	$id = (int)$_GET['id'];
	$db->db_exec("DELETE FROM $table WHERE id='".$id."'");	
	$_SESSION['message'] = "Record Deleted";
	header("Location: $page");
	exit;
}

if($_POST['add']){
	
	unset($_POST['add']);
	
	$array = array();
	$issues = $_POST['issue_id'];
	$accessories = $_POST['accessories_id'];
	
	$has_issues = $_POST['has_issues'];
	$has_accessories = $_POST['has_accessories'];
	$note = $_POST['note'];
	unset($_POST['note']);
	unset($_POST['issue_id']);
	unset($_POST['accessories_id']);
	unset($_POST['has_issues']);
	unset($_POST['has_accessories']);
	
	$array = $_POST;
	
	
	$array['user_id'] = $_SESSION['user_id'];
	$array['date_added'] = date('Y-m-d H:i:s');
	if($id){
		
		
		
		$db->func_array2update($table,$array,"id = '$id'");
		
		$db->db_exec("INSERT INTO inv_devices_history SET device_id='".$id."',text='Device has been modified',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");
		
	}
	else{
		
		$id = $db->func_array2insert($table,$array);
			$db->db_exec("INSERT INTO inv_devices_history SET device_id='".$id."',text='New device added',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");
	}
	
	if($has_issues)
	{
		$db->db_exec("DELETE FROM inv_devices_issues WHERE device_id='".$id."'");
		
		foreach($issues as $issue)
		{
			$data = array();
			$data['device_id'] = $id;
			$data['issue_id'] = $issue;	
			$data['sort_order'] = 1;
			
			$db->func_array2insert("inv_devices_issues",$data);
		}
		
	}
	
	if($has_accessories)
	{
		$db->db_exec("DELETE FROM inv_devices_accessories WHERE device_id='".$id."'");
		
		foreach($accessories as $row)
		{
			$data = array();
			$data['device_id'] = $id;
			$data['accessories_id'] = $row;	
			$data['sort_order'] = 1;
			
			$db->func_array2insert("inv_devices_accessories",$data);
		}
		
	}
	if($note)
	{
		$db->db_exec("insert into inv_devices_notes set device_id='".$id."', text='".$db->func_escape_string($note)."',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");	
		
			$db->db_exec("INSERT INTO inv_devices_history SET device_id='".$id."',text='A note posted',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");
		
	}
	if($_FILES['device_images']['tmp_name']){ 
	
	foreach ($_FILES['device_images']['tmp_name'] as  $files){
		
		$count = count($files);
		
		for($i=0; $i<$count; $i++){
			$uniqid = uniqid();
			$destination = "files/".$uniqid.".jpg";
			$destination_thumb = "files/".$uniqid."_thumb.jpg";
			
			if(move_uploaded_file($files, $destination)){
				resizeImage($destination , $destination_thumb , 64 , 64);
					
				
				
				
					
				$itemImage = array();
				$itemImage['image_path'] = $destination;
				$itemImage['thumb_path'] = $destination_thumb;
				$itemImage['date_added'] = date('Y-m-d H:i:s');
				$itemImage['user_id']  = $_SESSION['user_id'];
				$itemImage['device_id'] = $id;
					
				$image_id = $db->func_array2insert("inv_devices_images",$itemImage);
				$imageCount++;
				$db->db_exec("INSERT INTO inv_devices_history SET device_id='".$id."',text='Image is uploaded',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");
				

			}
		}
	}
	
		
	}
	$_SESSION['message'] = 'Record Updated';
	header("Location: device_list.php");
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?=$title;?></title>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		
	
	</head>
	<body>
		<div align="center">
			<div align="center" >
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <form action="" name="frm_detail" id="frm_detail" method="post" enctype="multipart/form-data">
			 	<h2><?=$title;?></h2>
			    <table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         
			         
			         
			        <tr>
                    
			             <th>ID:</th>
			         	 <td><input type="text" name="sku" value="<?php echo @$result['sku'];?>" <?=($id?'readOnly':'');?> required /></td>
                         
                         <th>Model:</th>
			         	 <td>
                         <select name="model_id" <?=($id?'disabled':'');?> onchange="getSKU()" required>
                         <option value="">Please Select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_model WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($result['model_id']==$row['id']?'selected':'');?>><?=$db->func_query_first_cell("SELECT name FROM inv_d_manufacturer WHERE id='".$row['manufacturer_id']."'");?> <?=$row['name'];?> (<?=$db->func_query_first_cell("SELECT name FROM inv_d_carrier WHERE id='".$row['carrier_id']."'");?>)</option>
                            <?php 
							 
						 }
						 ?>
                         </select>
                         </td>
                         <th>P/F:</th>
			         	 <td >
                         <select name="status_id" required>
                         <option value="">Please Select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_status WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($result['status_id']==$row['id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         </select>
                         </td>
                         
                         
			         </tr>
                     
                     <tr>
                    
			             <th>IMEI:</th>
			         	 <td><input type="text" name="imei" value="<?php echo @$result['imei'];?>" required /></td>
                         
                         <th>OS:</th>
			         	 <td>
                         <select name="os_id" required>
                         <option value="">Please Select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_os WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($result['os_id']==$row['id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         </select>
                         </td>
                          <th>Grade:</th>
			         	 <td>
                         <select name="grade_id" required>
                         <option value="">Please Select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_grade WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($result['grade_id']==$row['id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         </select>
                         </td>
                         
			         </tr>
                     <tr>
                    
			             <th>Internal Storage:</th>
			         	 <td>
                         <select name="storage_id" required>
                         <option value="">Please Select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_storage WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($result['storage_id']==$row['id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         </select>
                         </td>
                         
                         <th>Location:</th>
			         	 <td colspan="2">
                         <select name="location_id" required>
                         <option value="">Please Select</option>
                         <?php
						 $rows = $db->func_query("SELECT * FROM inv_d_location WHERE status=1 ORDER BY name");
						 foreach($rows as $row)
						 {
							?>
                            <option value="<?=$row['id'];?>" <?=($result['location_id']==$row['id']?'selected':'');?>><?=$row['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         </select>
                         </td>
                          
                         
			         </tr>
                     
                     
                     <tr>
                     <th>Issues</th>
                     <td colspan="5">
                     <input type="checkbox" name="has_issues" onclick="showHideIssues(this)" <?=($devices_issues?'checked':'');?> value="1" />
                     <div style="clear:both"></div>
                     <div class="scrollbox" style="width:380px;height:100px;float:left;<?=($devices_issues?'':'display:none');?>" id="category_box_1">
<?php
$issues = $db->func_query("SELECT * FROM inv_d_issue WHERE status=1 order by name");

?>
                  <?php $class = 'odd'; ?>

                  <?php foreach ($issues as $issue) { ?>

                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>

                  <div class="<?php echo $class; ?>">

                    <?php if (in_array($issue['id'], $devices_issues)) { ?>

                    <input type="checkbox" name="issue_id[]" value="<?php echo $issue['id']; ?>" checked="checked" onChange="AddThis(this)" />

                    <?php echo $issue['name']; ?>

                    <?php } else { ?>

                    <input type="checkbox" name="issue_id[]" value="<?php echo $issue['id']; ?>" onChange="AddThis(this)"  />

                    <?php echo $issue['name']; ?>

                    <?php } ?>

                  </div>

                  <?php } ?>

                </div>
                
                
                <div class="scrollbox" id="category_box_2" style="width:380px;height:100px;float:right;<?=($devices_issues?'':'display:none');?>">
      
      
      </div>
                     </td>
                     
                     </tr>
                     
                      <tr>
                     <th>Accessories</th>
                     <td colspan="5">
                     <input type="checkbox" name="has_accessories" onclick="showHideAccessories(this)" <?=($devices_accessories?'checked':'');?> value="1" />
                     <div style="clear:both"></div>
                     <div class="scrollbox" style="width:380px;height:100px;float:left;<?=($devices_accessories?'':'display:none');?>" id="category_box_3">
<?php
$rows = $db->func_query("SELECT * FROM inv_d_accessories WHERE status=1 order by name");

?>
                  <?php $class = 'odd'; ?>

                  <?php foreach ($rows as $row) { ?>

                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>

                  <div class="<?php echo $class; ?>">

                    <?php if (in_array($row['id'], $devices_accessories)) { ?>

                    <input type="checkbox" name="accessories_id[]" value="<?php echo $row['id']; ?>" checked="checked" onChange="AddThis1(this)" />

                    <?php echo $row['name']; ?>

                    <?php } else { ?>

                    <input type="checkbox" name="accessories_id[]" value="<?php echo $row['id']; ?>" onChange="AddThis1(this)"  />

                    <?php echo $row['name']; ?>

                    <?php } ?>

                  </div>

                  <?php } ?>

                </div>
                
                
                <div class="scrollbox" id="category_box_4" style="width:380px;height:100px;float:right;<?=($devices_accessories?'':'display:none');?>">
      
      
      </div>
                     </td>
                     
                     </tr>
                     <tr>
                     <th>Images</th>
                     <td colspan="5">
                     
                     <?php
					 $images = $db->func_query("SELECT * FROM inv_devices_images WHERE device_id='$id'");
					 ?>
                         <?php foreach($images as $image):?>
													
														 <a href="<?php echo str_ireplace("../", "", $image['image_path']);?>" class="fancybox2 fancybox.iframe">
														 	<img src="<?php echo str_ireplace("../", "", $image['thumb_path']);?>" width="64" height="64" />
														 </a>	
														 
														 <a onclick="if(!confirm('Are you sure?')){ return false; }" href="devices_detail.php?id=<?php echo $id?>&action=remove&image_id=<?php echo $image['id'];?>">X</a>
													
												<?php endforeach;?>
                                                <br />
                                                  <input  type="file" id="device_images" name="device_images[]" multiple="multiple"  accept="image/gif, image/jpeg, image/png" value="" />
                     
                     </td>
                     
                     </tr>
			         <tr>
                     <th>Note</th>
                     <td colspan="5">
                     <textarea name="note" style="height:100px;width:450px"></textarea>
                     </td>
                     
                     </tr>
			         <tr>
			             <td colspan="6" align="center">
			             	 <input class="submit" type="submit" name="add" value="Save" />
			             </td>
			         </tr>
                   
			    </table>
			 </form>
             <br />
            
             
             
                     <table align="center" border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;margin-bottom:30px">
                     <tr>
                     <td>
                     	<table align="center" border="1" width="90%" cellpadding="10">
                        <tr>
                        	<th colspan="3">NOTES</th>
                            
                        </tr>
                        <tr style="background:#e5e5e5;">
                        <th>Date</th>
                        <th>User</th>
                        <th>Note</th>
                        
                        
                        </tr>
                        <tr>
                        <?php
						$rows = $db->func_query("SELECT * FROM inv_devices_notes WHERE device_id='".$id."'");
						foreach($rows as $row)
						{
						?>
                        <tr>
                        <td><?=date('m/d/Y h:ia',strtotime($row['date_added']));?></td>
                        <td><?=get_username($row['user_id']);?></td>
                        <td><?=$row['text'];?></td>
                        </tr>
                        <?php	
							
						}
						
						
						
						
						?>
                        
                        </tr>
                        
                        </table>
                     
                     </td>
                     
                     <td>
                     <table align="center" border="1" width="90%" cellpadding="10">
                        <tr>
                        	<th colspan="3">HISTORY</th>
                            
                        </tr>
                        <tr style="background:#e5e5e5;">
                        <th>Date</th>
                        <th>User</th>
                        <th>Detail</th>
                        
                        
                        </tr>
                        <tr>
                        <?php
						$rows = $db->func_query("SELECT * FROM inv_devices_history WHERE device_id='".$id."'");
						foreach($rows as $row)
						{
						?>
                        <tr>
                        <td><?=date('m/d/Y h:ia',strtotime($row['date_added']));?></td>
                        <td><?=get_username($row['user_id']);?></td>
                        <td><?=$row['text'];?></td>
                        </tr>
                        <?php	
							
						}
						
						
						
						
						?>
                        
                        </tr>
                        
                        </table>
                     </td>
                     
                     </tr>
                     
                     </table>
                     	
                     
                    
             
            
		 </div>
         
         
         
         
	</body>
</html>			 		 
<script>
function showHideIssues(obj)
{
	var delay = 500;
if($(obj).is(':checked'))
{
	$('#category_box_1').show(delay);
	$('#category_box_2').show(delay);
}
else
{
	
	$('#category_box_1').hide(delay);
	$('#category_box_2').hide(delay);
}
	
}
function AddThis(Obj)
{
	
var Value = $.trim($(Obj).parent().text());
	$(Obj).closest('div').hide(500);
	
	$('#category_box_2').append('<div class="odd" style="background-color:#dbeec4"><input type="checkbox" onchange="RemoveThis(this)" value="'+Obj.value+'" checked> '+Value+'</div>');
		
	
}

function RemoveThis(Obj)
{
	
	var Value = $(Obj).val();
	$checkbox = $("#category_box_1 input[value='"+Value+"']");
	
	$checkbox.attr('checked',false);
	$checkbox.closest('div').show(500);
	$(Obj).closest('div').hide(500);
		
	
}
function LoopThrough()
	{
	$("#category_box_1 input").each(function(index, element) {
        
		if($(element).is(':checked'))
		{
			$(element).closest('div').css('display','none');
		AddThis(element);	
		}
    });
	}
	
	
	function showHideAccessories(obj)
{
	var delay = 500;
if($(obj).is(':checked'))
{
	$('#category_box_3').show(delay);
	$('#category_box_4').show(delay);
}
else
{
	
	$('#category_box_3').hide(delay);
	$('#category_box_4').hide(delay);
}
	
}
function AddThis1(Obj)
{
	
var Value = $.trim($(Obj).parent().text());
	$(Obj).closest('div').hide(500);
	
	$('#category_box_4').append('<div class="odd" style="background-color:#dbeec4"><input type="checkbox" onchange="RemoveThis1(this)" value="'+Obj.value+'" checked> '+Value+'</div>');
		
	
}

function RemoveThis1(Obj)
{
	
	var Value = $(Obj).val();
	$checkbox = $("#category_box_3 input[value='"+Value+"']");
	
	$checkbox.attr('checked',false);
	$checkbox.closest('div').show(500);
	$(Obj).closest('div').hide(500);
		
	
}
function LoopThrough1()
	{
	$("#category_box_3 input").each(function(index, element) {
        
		if($(element).is(':checked'))
		{
			$(element).closest('div').css('display','none');
		AddThis1(element);	
		}
    });
	}
	function LoopThroughOut()
	{
	$("#category_box_2 input").each(function(index, element) {
        
		if($(element).is(':checked') && $(element).closest('div').is(':visible') )
		{
			$(element).closest('div').css('display','none');
		RemoveThis(element);	
		}
    });
	}
	function getSKU()
	{
		var model_id = $('select[name=model_id]').val();
		if(model_id!='')
		{
		$.ajax({
                url: "<?=$page;?>",
				type:"POST",
                data: {model_id: model_id,action:'get_sku'},
				dataType:"json",
                success: function(json) {
					$('input[name=sku]').val(json['sku']);
				}
            });	
		}
		
	}
$(document).ready(function(e) {
LoopThrough();	
LoopThrough1();	

$('.fancybox2').fancybox({ width: '680px' , autoCenter : true , autoSize : true });
});

</script>