<?php 
if ($_GET['config']) {
	require_once("../config.php");
} else {
	require_once("../auth.php");
}
$api_key = "y6k8Kv8iu7gzZ7rcTye";
$admin_email = 'saad@phonepartsusa.com';
$password = "ppusa12345";
$yourdomain = "phonepartsusa";
if ($_POST['action'] == 'create') {
	$group = ($_POST['group']) ? $_POST['group'] : 9000170450;
	$main_category = ($_POST['main_category']) ? $_POST['main_category'] : 'Products';
	$sub_category = ($_POST['sub_category']) ? $_POST['sub_category'] : 'Incorrect Compatibility';
	$ticket_data = json_encode(array(
		"description" => $_POST['description'],
		"subject" => $_POST['subject'],
		"email" => $_POST['email'],
		"name" => $_POST['name'],
		"priority" => 1,
		"status" => 2,
		"group_id"=>$group,
		// "custom_fields"=>array('main_category'=>$main_category,'sub_category'=>$sub_category)
		// "cc_emails" => array("ram@freshdesk.com", "diana@freshdesk.com")
		));
		
	
	$url = "https://$yourdomain.freshdesk.com/api/v2/tickets";
	// $url = "https://$yourdomain.freshdesk.com/api/v2/groups";
	$ch = curl_init($url);
	$header[] = "Content-type: application/json";
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_USERPWD, "$admin_email:$password");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $ticket_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	$info = curl_getinfo($ch);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$headers = substr($server_output, 0, $header_size);
	$response = substr($server_output, $header_size);
	
	if($info['http_code'] == 201) {
		echo json_encode(array('success'=>1));
		// echo "Ticket created successfully, the response is given below \n";
		// echo "Response Headers are \n";
		// echo $headers."\n";
		// echo "Response Body \n";
		// echo "$response \n";
	} else {
		if($info['http_code'] == 404) {
			echo "Error, Please check the end point \n";
		} else {
			echo "Error, HTTP Status Code : " . $info['http_code'] . "\n";
			echo "Headers are ".$headers;
			echo "Response are ".$response;
		}
	}
	curl_close($ch);
}

?>
<script type="text/javascript" src="<?php echo $host_path; ?>js/jquery.min.js"></script>
<script type="text/javascript">
	function getDetails(req) {
		$.ajax({
			url: "<?php echo "https://$yourdomain.freshdesk.com/api/v2/"; ?>" + req,
			beforeSend: function(xhr) { 
				xhr.setRequestHeader("Authorization", "Basic " + btoa("<?php echo $admin_email; ?>:<?php echo $password; ?>")); 
			},
			type: 'Get',
			dataType: 'json',
			contentType: 'application/json',
			processData: false,			
			success: function (data) {
				console.log(JSON.stringify(data));
			},
			error: function(){
				console.log(JSON.stringify(data));
			}
		});
	}
</script>