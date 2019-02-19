<?php
require_once("../config.php");
require_once("../inc/functions.php");
$id = $_POST['issue_assigned_id'];
if($id)
{
	
	$db->db_exec("UPDATE inv_issue_assigned SET seen=1 WHERE issue_assigned_id='".(int)$id."'");
	add_issue_history($db->func_query_first_cell("SELECT issue_id FROM inv_issue_assigned WHERE issue_assigned_id='".(int)$id."'"),$_SESSION['login_as']. ' viewed the task.');
	$json['succuess'] = 'ok';
echo json_encode($json);
}
?>