<?php

require_once("auth.php");
require_once("inc/functions.php");
page_permission("complaints");
$mode = $_GET['mode'];
if($mode == 'edit'){
	$issue_id = (int)$_GET['id'];
	$issue_result = $db->func_query_first("select * from inv_issues_complaints where id = '$issue_id'");
}

if($_POST['add']){
	unset($_POST['add']);
	
	$issue_result_arr = array();
	$assigned_to = array();
	$issue_result_arr = $_POST;
	$assigned_to = $_POST['assigned_to'];
	unset($_POST['assigned_to']);
	$issue_result_arr['complaint']   = ($issue_result_arr['complaint']) ? 1 : 0;
	$issue_result_arr['revision_request'] = ($issue_result_arr['revision_request']) ? 1 : 0;
	$issue_result_arr['assigned_by'] = $_SESSION['user_id'];
	
	$issue_result_arr['revision_issue'] = implode(",", $issue_result_arr['revision_issue']);
	$issue_result_arr['stores'] = implode(",", $issue_result_arr['stores']);
	
	if($issue_id){
		$issue_result_arr['date_modified'] = date('Y-m-d H:i:s');
		$db->func_array2update("inv_issues_complaints",$issue_result_arr,"id = '$issue_id'");
		add_issue_history($issue_id,'Issue / Complaint # '.(int)$issue_id.' modified by '.$_SESSION['login_as'] );
	}
	else{
		$issue_result_arr['date_added'] = date('Y-m-d H:i:s');
		$issue_id = $db->func_array2insert("inv_issues_complaints",$issue_result_arr);
		add_issue_history($issue_id,'Issue / Complaint # '.(int)$issue_id.' created by '.$_SESSION['login_as']);
	}
	$db->db_exec("delete from inv_issue_assigned WHERE issue_id='".(int)$issue_id."'");
	
	foreach($assigned_to as $assigned)
	{
		$array = array();
		$array['issue_id'] = $issue_id;
		$array['user_id'] = $assigned;
		$com_id = $db->func_array2insert("inv_issue_assigned",$array);
		add_issue_history($issue_id,'Task has been assigned to '.get_username($assigned)).' by '.$_SESSION['login_as'];
	}
	$log = 'Complaint '. linkToComplaint($issue_id) .' was '. (($mode == 'edit')? 'updated': 'created') .' for ' . linkToProduct($_POST['sku']);
	actionLog($log);
	header("Location:issues_complaints.php");
	exit;
}

$users = $db->func_query("select * from inv_users");
$item_issues = $db->func_query("select * from inv_reasonlist");

$stores = array('eBay','Amazon','Bonanza','Bigcommerce','OpenSky','Wish','Channel Advisor');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Add Customer Issue / Complaint</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#complaint").click(function(){
				if(jQuery("#complaint").prop("checked")){
					jQuery("#item_issues_box").show();
				}
				else{
					jQuery("#item_issues_box").hide();
				}
			});

			jQuery("#revision_request").click(function(){
				if(jQuery("#revision_request").prop("checked")){
					jQuery("#revision_issues_box").show();
				}
				else{
					jQuery("#revision_issues_box").hide();
				}
			});

			jQuery("#location").click(function(){
				if(jQuery("#location").prop("checked")){
					jQuery("#stores_box").show();
				}
				else{
					jQuery("#stores_box").hide();
				}
			});
		})
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

		<form action="" method="post" enctype="multipart/form-data">
			<h2>Add Customer Issue / Complaint</h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>SKU</td>
					<td><input type="text" name="sku" value="<?php echo @$issue_result['sku'];?>" required /></td>
				</tr>

				<tr>
					<td>Item Title</td>
					<td><input type="text" name="item_title" value="<?php echo @$issue_result['item_title'];?>" /></td>
				</tr>

				<tr>
					<?php
					$issue_categories = $db->func_query("SELECT * FROM inv_issues_category ORDER BY name");
					?>
					<td>Category</td>
					<td><select name="issue_category_id" style="width:135px" required>
						<option value="">Please Select</option>
						<?php
						foreach($issue_categories as $list)
						{
							?>
							<option value="<?=$list['issue_category_id'];?>" <?= ($list['issue_category_id']==$issue_result['issue_category_id']?'selected':'');?>><?=$list['name'];?></option>
							<?php 

						}
						?>

					</select>
				</td>
			</tr>

			<tr>
				<td>Customer Complaint <input type="checkbox" id="complaint" name="complaint" value="1" <?php if($issue_result['complaint'] == 1):?> checked="checked" <?php endif;?> /> </td>
				<td>
					Revision Request   <input type="checkbox" id= "revision_request" name="revision_request" value="1" <?php if($issue_result['revision_request'] == 1):?> checked="checked" <?php endif;?> />

					Location  <input type="checkbox" id = "location" name="location" value="1" <?php if($issue_result['location'] == 1):?> checked="checked" <?php endif;?> />
				</td>
			</tr>

			<tr>
				<td>
					<select name="item_issue" style="width:135px;<?php if($issue_result['complaint'] == 0):?>display:none"<?php endif;?> id="item_issues_box">
						<option value="">Select One</option>

						<?php foreach($item_issues as $item_issue):?>
							<option value="<?php echo $item_issue['name']?>" <?php if($item_issue['name'] == $issue_result['item_issue']):?> selected="selected" <?php endif;?>>
								<?php echo $item_issue['name']?>
							</option>
						<?php endforeach;?>
					</select>
				</td>

				<td>
					<?php $issue_result['revision_issue'] = explode(",", $issue_result['revision_issue']);?>

					<?php $issue_result['stores'] = explode(",", $issue_result['stores']);?>

					<select name="revision_issue[]" multiple="multiple" size="4" style="width:135px;<?php if($issue_result['revision_request'] == 0):?>display:none"<?php endif;?> id="revision_issues_box">
						<option value="">Select One</option>

						<option value="Image" <?php if(in_array('Image',$issue_result['revision_issue'])):?> selected="selected" <?php endif;?>>Image</option>

						<option value="Item Title" <?php if(in_array('Item Title',$issue_result['revision_issue'])):?> selected="selected" <?php endif;?>>Item Title</option>

						<option value="Item Description" <?php if(in_array('Item Description',$issue_result['revision_issue'])):?> selected="selected" <?php endif;?>>Item Description</option>

						<option value="Item Category" <?php if(in_array('Item Category',$issue_result['revision_issue'])):?> selected="selected" <?php endif;?>>Item Category</option>
					</select>

					<select name="stores[]" multiple="multiple" size="4" style="width:135px;<?php if($issue_result['location'] == 0):?>display:none"<?php endif;?> id="stores_box">
						<option value="">Select One</option>

						<?php foreach($stores as $store):?>

							<option value="<?php echo $store;?>" <?php if(in_array($store,$issue_result['stores'])):?> selected="selected" <?php endif;?>><?php echo $store;?></option>

						<?php endforeach;?>
					</select>
				</td>
			</tr>

			<tr>
				<td>Notes</td>
				<td>
					<textarea rows="3" cols="40" name="notes"><?php echo @$issue_result['notes']?></textarea>
				</td>
			</tr>
			<tr>
				<td>Priority</td>
				<td>
					<select name="priority" required>
						<option value="">Select One</option>

						<option value="3" <?php if($issue_result['priority'] == '3'):?> selected="selected" <?php endif;?>>High</option>
						<option value="2" <?php if($issue_result['priority'] == '2'):?> selected="selected" <?php endif;?>>Medium</option>
						<option value="1" <?php if($issue_result['priority'] == '1'):?> selected="selected" <?php endif;?>>Low</option>
					</select>
				</td>
			</tr>
			<tr style="display:none">
				<td>Status</td>
				<td>
					<select name="status" required>


						<option value="Created" <?php if($issue_result['status'] == 'Created'):?> selected="selected" <?php endif;?>>Created</option>
						<option value="Resolving" <?php if($issue_result['status'] == 'Resolving'):?> selected="selected" <?php endif;?>>Resolving</option>
						<option value="Completed" <?php if($issue_result['status'] == 'Completed'):?> selected="selected" <?php endif;?>>Completed</option>
						<option value="Not Completed" <?php if($issue_result['status'] == 'Not Completed'):?> selected="selected" <?php endif;?>>Not Completed</option>
						<option value="Fixed" <?php if($issue_result['status'] == 'Fixed'):?> selected="selected" <?php endif;?>>Fixed</option>
						<option value="Rejected" <?php if($issue_result['status'] == 'Rejected'):?> selected="selected" <?php endif;?>>Rejected</option>
						<option value="Restarted" <?php if($issue_result['status'] == 'Restarted'):?> selected="selected" <?php endif;?>>Restarted</option>

					</select>

				</td>
			</tr>

			<tr>
				<td>Assign To:</td>
				<td>
					<table>
						<tr>
							<?php $user_ids =  $db->func_query("SELECT user_id FROM inv_issue_assigned WHERE issue_id='".(int)$issue_result['id']."'");		
							$ux = array();
							foreach($user_ids as $xu)
							{
								$ux[] = $xu['user_id'];	 

							}


							?>
							<?php $i=1; foreach($users as $user):?>
							<td>
								<input type="checkbox" name="assigned_to[]" <?php if(in_array($user['id'],$ux)):?> checked="checked" <?php endif;?> value="<?php echo $user['id']?>" />
								<?php echo $user['name'];?>
							</td>

							<?php if($i % 4 == 0):?>
							</tr><tr>
						<?php endif;?>
						<?php $i++; endforeach;?>
					</tr>	 	 
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="2" align="center">
				<input type="submit" name="add" value="Create Task" class="button" />
			</td>
		</tr>
	</table>
</form>
</div>
</body>
</html>			 		 