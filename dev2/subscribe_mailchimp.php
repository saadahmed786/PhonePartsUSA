<?php
$data = [
    'email'     => $_POST['email'],
    'status'    => 'subscribed',
    'firstname' => 'john',
    'lastname'  => 'doe'
];

$type =  syncMailchimp($data);
if($type=='200')
{
    echo "<script>alert('You have successfully subscribed to mailing list.');window.close();</script>";
}
else
{
    echo "<script>alert('Oh No! There is Something wrong, please contact administrator.');window.close();</script>";   
}
function syncMailchimp($data) {
    $apiKey = 'cb763e032124eccc1f19fedbdde27ca3-us14';
    $listId = 'ceca350c2b';

    $memberId = md5(strtolower($data['email']));
    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

    $json = json_encode([
        'email_address' => $data['email'],
        'status'        => $data['status'] // "subscribed","unsubscribed","cleaned","pending"
        
    ]);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 

    $result = curl_exec($ch);
    // print_r($result);exit;
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode;
}
?>