<?php
require_once("../auth.php");
$email_id = (int)$_GET['email_id'];
$row = $db->func_query_first("SELECT * FROM inv_email_report WHERE email_report_id='".$email_id."'");

echo html_entity_decode($row['email_body']);

?>