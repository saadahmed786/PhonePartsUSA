<?php
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$rows = $db->func_query("SELECT * FROM oc_product_description WHERE description LIKE '%youtube.com%'");

foreach($rows as $row)
{
	$description = stripslashes($row['description']);
$data = scrape_between($description,'&lt;iframe','/iframe&gt;');
$embed_code = scrape_between($data,'src=&quot;','&quot;');
$code = explode("/", $embed_code);
$code = end($code);
if($code)
{
	$code = str_replace("&amp;quot;", '', $code);
	$code = str_replace("\\", '', $code);
	//echo $data;exit;
	$new_description = str_replace($data, '', $description);
	$new_description = str_replace('&lt;iframe/iframe&gt;','',$new_description);
	//echo $new_description."<br><br>";
	($db->func_query("UPDATE oc_product_description SET description = '".$db->func_escape_string(($new_description))."' WHERE product_id='".$row['product_id']."'"));
	//echo "UPDATE oc_product_description SET description = '".$db->func_escape_string(htmlentities($new_description))."' WHERE product_id='".$row['product_id']."'"."<br>";
	$db->db_exec("UPDATE oc_product SET video='".$code."' WHERE product_id='".$row['product_id']."'");
	//echo "UPDATE oc_product_description SET description = '$new_description' WHERE product_id='".$row['product_id']."'";exit;
	echo $row['product_id'].'--'.$code."<br><br>=========================<br>";;

}
}
?>