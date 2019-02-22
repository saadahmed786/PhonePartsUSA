<?php
$data = $_POST;
// mail("xaman.riaz@gmail.com","RMA IPN",print_r($data,true));
// mail("xaman.riaz@gmail.com","RMA IPN2",print_r($_GET,true));
file_put_contents("paypal_ipn.txt",print_r($data,true),FILE_APPEND);

echo "success";
exit;