<?php 
$api_key = "y6k8Kv8iu7gzZ7rcTye";
$admin_email = 'saad@phonepartsusa.com';
$password = "ppusa12345";
$yourdomain = "phonepartsusa";
if ($_POST['action'] == 'create') {
  if (isset($_POST['method']) && $_POST['method'] == 'g') {
    $ticket_payload = array(
      'email' => $_POST['email'],
      'name'=>$_POST['name'],
      'subject' => $_POST['subject'],
      'description' => $_POST['body'],
      'priority' => 1,
      'status' => 2,
  // 'attachments[]' =>  curl_file_create($_POST['attachment']),
      "group_id"=>9000170450,
  // "custom_fields"=>array('Main category'=>9000072164)
      );
  } else {
   $ticket_payload = array(
    'email' => $_POST['email'],
    'name'=>$_POST['name'],
    'subject' => $_POST['name'].' Submitted LBB#'.$_POST['lbb_number'].' containing '.$_POST['total_lcd'].' Broken Screens.',
    'description' => $_POST['body'],
    'priority' => 1,
    'status' => 2,
  // 'attachments[]' =>  curl_file_create($_POST['attachment']),
    "group_id"=>9000170450,
  // "custom_fields"=>array('Main category'=>9000072164)
    );
 }
 $url = "https://$yourdomain.freshdesk.com/api/v2/tickets";
 $ch = curl_init($url);
 curl_setopt($ch, CURLOPT_POST, true);
 curl_setopt($ch, CURLOPT_HEADER, true);
 curl_setopt($ch, CURLOPT_USERPWD, "$admin_email:$password");
 curl_setopt($ch, CURLOPT_POSTFIELDS, $ticket_payload);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 $server_output = curl_exec($ch);
 $info = curl_getinfo($ch);
 $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
 $headers = substr($server_output, 0, $header_size);
 $response = substr($server_output, $header_size);

 if($info['http_code'] == 201) {
  echo "Ticket created successfully, the response is given below \n";
  echo "Response Headers are \n";
  echo $headers."\n";
  echo "Response Body \n";
  echo "$response \n";
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