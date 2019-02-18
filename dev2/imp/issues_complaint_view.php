<?php

require_once("auth.php");
include_once("inc/functions.php");
page_permission('complaints');
$issue_id = (int)$_GET['id'];
$user_id = (isset($_GET['user_id'])?$_GET['user_id']:'');
$is_popup = (isset($_GET['popup'])?$_GET['popup']:'0');

if((int)$_GET['id'] && $_GET['action'] == 'resolve'){
	$db->db_exec("update inv_issues_complaints set `status`= 'Resolving' where id = '".(int)$_GET['id']."'");
	$log = 'Complaint '. linkToComplaint($issue_id) .' status changed to Resolving';
	actionLog($log);
	add_issue_history($_GET['id'],$_SESSION['login_as']. ' marked the status to Resolving.');
	header("Location:issues_complaint_view.php?id=$issue_id");
	exit;
}

if((int)$_GET['id'] && $_GET['action'] == 'fixed'){
	$db->db_exec("update inv_issues_complaints set `status` = 'Fixed' where id = '".(int)$_GET['id']."'");
	$log = 'Complaint '. linkToComplaint($issue_id) .' status changed to Fixed';
	actionLog($log);
	add_issue_history($_GET['id'],$_SESSION['login_as']. ' marked the status to Fixed.');
	header("Location:issues_complaint_view.php?id=$issue_id");
	exit;
}

if((int)$_GET['id'] && $_GET['action'] == 'completed'){
	$db->db_exec("update inv_issues_complaints set `status`= 'Completed' where id = '".(int)$_GET['id']."'");
	$log = 'Complaint '. linkToComplaint($issue_id) .' status changed to Completed';
	actionLog($log);
	add_issue_history($_GET['id'],$_SESSION['login_as']. ' marked the status to Completed.');
	header("Location:issues_complaint_view.php?id=$issue_id");
	exit;
}

if((int)$_GET['id'] && $_GET['action'] == 'notcompleted'){
	$db->db_exec("update inv_issues_complaints set `status`= 'Not Completed' where id = '".(int)$_GET['id']."'");
	$log = 'Complaint '. linkToComplaint($issue_id) .' status changed to Not Completed';
	actionLog($log);
	add_issue_history($_GET['id'],$_SESSION['login_as']. ' marked the status to Not Completed.');
	header("Location:issues_complaint_view.php?id=$issue_id");
	exit;
}


if((int)$_GET['id'] && $_GET['action'] == 'rejected'){
	$db->db_exec("update inv_issues_complaints set `status`= 'Rejected' where id = '".(int)$_GET['id']."'");
	$log = 'Complaint '. linkToComplaint($issue_id) .' status changed to Rejected';
	actionLog($log);
	add_issue_history($_GET['id'],$_SESSION['login_as']. ' rejected the task.');
	$log = 'Complaint was rejected for ' . linkToProduct($_POST['sku']);
	actionLog($log);
	header("Location:issues_complaint_view.php?id=$issue_id");
	exit;
}


if((int)$_GET['id'] && $_GET['action'] == 'restarted'){
	$db->db_exec("update inv_issues_complaints set `status`= 'Restarted' where id = '".(int)$_GET['id']."'");
	$log = 'Complaint '. linkToComplaint($issue_id) .' status changed to Restarted';
	actionLog($log);
	add_issue_history($_GET['id'],$_SESSION['login_as']. ' restarted the task.');
	header("Location:issues_complaint_view.php?id=$issue_id");
	exit;
}

$issue_result = $db->func_query_first("select * from inv_issues_complaints where id = '$issue_id'");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>View Customer Issue / Complaint</title>
</head>
<body>
	<div align="center">
		<div align="center" style="<?php echo ($is_popup?'display:none':'');?>"> 
			<?php include_once 'inc/header.php';?>
		</div>

		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<form action="" method="post" enctype="multipart/form-data">
			<h2>View Customer Issue / Complaint</h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>SKU</td>
					<td><?php echo @$issue_result['sku'];?></td>
				</tr>

				<tr>
					<td>Item Title</td>
					<td><?php echo @$issue_result['item_title'];?></td>
				</tr>
				<tr>
					<td>Category</td>
					<td><?php echo $db->func_query_first_cell( "SELECT name FROM inv_issues_category WHERE issue_category_id='".(int)$issue_result['issue_category_id']."'" );?></td>
				</tr>


				<tr>
					<td>Customer Complaint : <?php if($issue_result['complaint'] == 1):?> Yes <?php endif;?></td>
					<td>Revision Request   : <?php if($issue_result['revision_request'] == 1):?> Yes <?php endif;?></td>
				</tr>

				<tr>
					<td>
						Item Issue: 
					</td>

					<td>
						<?php echo $issue_result['item_issue'];?>
					</td>
				</tr>

				<tr>
					<td>
						Revision Request:
					</td>

					<td>
						<?php echo $issue_result['revision_issue'];?>
					</td>
				</tr>

				<tr>
					<td>
						Locations:
					</td>

					<td>
						<?php echo $issue_result['stores'];?>
					</td>
				</tr>

				<tr>
					<td>Notes</td>
					<td>
						<?php echo @$issue_result['notes']?>
					</td>
				</tr>

				<tr>
					<td>Priority</td>
					<td>
						<?php  switch($issue_result['priority'])
						{
							case 3:
							$priority = "<span class='tag red-bg'>High</span>";
							break; 
							case 2:
							$priority = "<span class='tag blue-bg'>Medium</span>";
							break; 
							case 1:
							$priority = "<span class='tag orange-bg'>Low</span>";
							break; 
							default:
							$priority = "Not Defined";
							break;


						}
						echo $priority;

						?>
					</td>
				</tr>
				<tr>
					<td>Status</td>
					<td>
						<?php echo get_issue_status_tag($issue_result['status']);

						?> 
					</td>
				</tr>
				<?php
				if($_SESSION['login_as']=='admin')
				{
					$histories = $db->func_query("SELECT * from inv_issue_history WHERE issue_id='".(int)$issue_id."' ORDER BY date_added");
					?>
					<tr>
						<td>History:</td>
						<td style="font-weight:bold">
							<?php
							if($histories){
								foreach($histories as $history)
								{
									echo date('d M Y h:iA',strtotime($history['date_added'])) .' - '.$history['description']. ' ( '.get_username($history['user_id']).')'."<br>";


								}
							}
							else
							{
								echo "Not Viewed by any user yet"; 
							}
							?>
						</td>

					</tr>
					<?php
				}
				?>
				<tr>
					<td colspan="2" align="center">
						<?php if($issue_result['status'] == 'Created' or $issue_result['status'] == 'Restarted' ):?>
							<a class="button" href="issues_complaint_view.php?action=resolve&id=<?php echo $issue_id?>" onclick="if(!confirm('Are you sure, You want to mark as resolving?')){ return false;}">Mark Resolving</a>
						<?php endif;?>	 		

						<?php if($issue_result['status'] == 'Resolving'):?>
							<a class="button" href="issues_complaint_view.php?action=completed&id=<?php echo $issue_id?>" onclick="if(!confirm('Are you sure, You want to mark as complete?')){ return false;}">Mark Complete</a> <a class="button" href="issues_complaint_view.php?action=notcompleted&id=<?php echo $issue_id?>" onclick="if(!confirm('Are you sure, You want to mark as not completed?')){ return false;}">Mark Not Complete</a>
						<?php endif;?> 	

						<?php if($issue_result['status'] == 'Completed'):?>
							<a class="button" href="issues_complaint_view.php?action=fixed&id=<?php echo $issue_id?>" onclick="if(!confirm('Are you sure, You want to mark as fixed?')){ return false;}">Mark Fixed</a> <a class="button" href="issues_complaint_view.php?action=rejected&id=<?php echo $issue_id?>" onclick="if(!confirm('Are you sure, You want to mark as rejected?')){ return false;}">Mark Rejected</a>
						<?php endif;?>

						<?php if($issue_result['status'] == 'Not Completed'):?>
							<a class="button" href="issues_complaint_view.php?action=restarted&id=<?php echo $issue_id?>" onclick="if(!confirm('Are you sure, You want to restart this task?')){ return false;}">Restart Task</a> 
						<?php endif;?> 

						<?php if($issue_result['status'] == 'Fixed'):?>
							<a class="button" href="issues_complaint_view.php?action=rejected&id=<?php echo $issue_id?>" onclick="if(!confirm('Are you sure, You want to mark as rejected?')){ return false;}">Mark Rejected</a>
						<?php endif;?> 	

						<?php if($issue_result['status'] == 'Rejected'):?>
							<a class="button" href="issues_complaint_view.php?action=restarted&id=<?php echo $issue_id?>" onclick="if(!confirm('Are you sure, You want to restart this task?')){ return false;}">Restart Task</a> 
						<?php endif;?> 		
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>		

<?php
if($is_popup)
{
	$q = $db->func_query_first("select * from inv_issue_assigned WHERE user_id='".$user_id."' AND issue_id='".$issue_result['id']."' AND seen=0");

	if($q)
	{

		add_issue_history($issue_result['id'],get_username($user_id). ' viewed the task.');
	}

	$db->db_exec("UPDATE inv_issue_assigned SET seen=1,seen_date='".date("Y-m-d H:i:s")."' WHERE user_id='".$user_id."' AND issue_id='".$issue_result['id']."' AND seen=0");	

	//echo 'done';
}

?>	 		 