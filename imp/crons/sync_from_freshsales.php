<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';


    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://phonepartsusa.freshsales.io/api/leads/view/79568?per_page=100",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => '{"user":{"email":"saad@phonepartsusa.com","password":"ppusa12345"}}',
        CURLOPT_HTTPHEADER => array(
            "auth: Token token=RMKYt6rcgwHUAw3-wcSo7A",
            "cache-control: no-cache",
            "content-type: application/json"

            ),
        ));

    $response= curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    $data = json_decode($response,true);
    // echo "<pre>";
    // print_r($data);exit;
    $pages = $data['meta']['total_pages'];
    // echo $pages;exit;
    for($i=1;$i<=$pages;$i++)
    {


         $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://phonepartsusa.freshsales.io/api/leads/view/79568?page=".$i."per_page=100",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => '{"user":{"email":"saad@phonepartsusa.com","password":"ppusa12345"}}',
        CURLOPT_HTTPHEADER => array(
            "auth: Token token=RMKYt6rcgwHUAw3-wcSo7A",
            "cache-control: no-cache",
            "content-type: application/json"

            ),
        ));

    $response= curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    $rows = json_decode($response,true);
    $rows = $rows['leads'];

    foreach($rows as $row)
    {
        $check = $db->func_query_first_cell("SELECT * FROM inv_customers WHERE TRIM(LOWER(email))='".trim(strtolower($row['email']))."'");
        if(!$check)
        {
            $db->func_query("INSERT INTO inv_customers SET
            firstname='".$db->func_escape_string($row['first_name'])."',
            lastname='".$db->func_escape_string($row['last_name'])."',
            email='".$db->func_escape_string(trim(strtolower($row['email'])))."',
            city='".$db->func_escape_string($row['city'])."',
            state='".$db->func_escape_string($row['state'])."',
            customer_group='".$db->func_escape_string('Default')."',
            address1='".$db->func_escape_string($row['address'])."',
            zip='".$db->func_escape_string($row['zipcode'])."',
            company='".$db->func_escape_string($row['company']['name'])."',
            telephone='".$db->func_escape_string($row['work_number'])."',
            date_added='".date('Y-m-d H:i:s')."',
            is_synced_from_fs=1

            ");
        }
    }

    }
    echo 1;
?>