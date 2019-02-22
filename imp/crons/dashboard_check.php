<?php
require_once("../config.php");
require_once("../inc/functions.php");
$check = $db->func_query_first("Select distinct a.*,b.issue_assigned_id from inv_issues_complaints a,inv_issue_assigned b WHERE a.id=b.issue_id AND b.user_id='".$_SESSION['user_id']."' and b.seen=0 and b.notified=0 ");
$json = array();


if($check)
{
$json['success']['id'] = $check['id'];	
$json['success']['issue_assigned_id'] = $check['issue_assigned_id'];	
$json['success']['description'] = (strlen($check['notes'])>35?substr($check['notes'],0,35).'...':$check['notes']);
	$db->db_exec("UPDATE inv_issue_assigned SET notified=1 WHERE issue_assigned_id='".$check['issue_assigned_id']."'");
	add_issue_history($check['id'],$_SESSION['login_as']. ' got notification of the task.');
}
else
{
$json['error'] = "not any";	
	
}
echo json_encode($json);

?>