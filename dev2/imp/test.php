 <?php
 include_once 'auth.php';

include_once 'inc/functions.php';
$rows = $db->func_query("SELECT * FROM inv_customers where user_id<>0 and address1<>'' and longitude is  null and latitude is  null ");
// print_r($rows);exit;
foreach($rows as $row)
{
	$dlocation = $row['address1'].', '.$row['city'].', '.$row['state'].' '.$row['zip'];
	// echo 'https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false';exit;
     // Get lat and long by address         
        $address = $dlocation; // Google HQ
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $prepAddr = str_replace(' ','+',$address);
        // echo 'https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false&key=AIzaSyARnAGsdBJnIPbiMqyw8cypDKiFCUfYI3A';exit;
        $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false&key=AIzaSyARnAGsdBJnIPbiMqyw8cypDKiFCUfYI3A');
        // echo $geocode.'here';exit;
        $output= json_decode($geocode);
        	// echo $output->status;exit;
        if (isset($output->status) && ($output->status == 'OK')) {
        // print_r($output);exit;
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;
    
        	echo $firstname.' '.$lastname.' ('.$longitude.','.$latitude.')<br>';
        	$db->db_exec("UPDATE inv_customers SET longitude='".$longitude."',latitude='".$latitude."' WHERE id='".$row['id']."'");
        }
        else
        {
        	echo $firstname.' '.$lastname.'(Not OK)<br>';
        }


    }


?>