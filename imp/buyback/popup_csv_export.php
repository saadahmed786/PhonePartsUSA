<?php
require_once("../auth.php");

require_once("../inc/functions.php");

page_permission("buyback");

if(isset($_POST['submit']))
{
    if($_POST['status']=='')
    {
        $where = ' 1 = 1 ';
    }
    else
    {
        $where = ' status = "'.$db->func_escape_string($_POST['status']).'"';
    }
   switch ($_POST['status']) {
       case 'Received':
           $date_filter='date_received';
           break;
           case 'In QC':
           $date_filter='date_qc';
           break;
           case 'Completed':
           $date_filter='date_completed';
           break;
       
       default:
            $date_filter='date_added';
           break;
   }

    $query = $db->func_query("SELECT * from oc_buyback WHERE MONTH(".$date_filter.")='".(int)$_POST['month']."' and YEAR(".$date_filter.")='".(int)$_POST['year']."' and $where ");

$filename = 'BuyBack-CSV-'.$_POST['month'].'-'.$_POST['year'].'--'.uniqid().'.csv';
$fp = fopen($filename, "w");

    $headers = array("Date", "Shipment #", "Full Name", "Email","Address 1", "City", "State", "Zip","Payment Type", "PayPal Email", "Total Estimate","Total Admin","Sent","Received","Accepted","Rejected","QC Total");
fputcsv($fp, $headers,',');
$rowData=array();

foreach($query as $detail)
{
    if($detail['customer_id']==0)
                    {
                        $email = $detail['email'];
                        $telephone = $detail['telephone'];
                        $firstname = $detail['firstname'];
                        $lastname = $detail['lastname'];
                        
                        $address_1 = $detail['address_1'];
                        $city = $detail['city'];
                        $postcode = $detail['postcode'];
                        $zone_id = $detail['zone_id'];
                    }
                    else
                    {
                        
                        $customer_detail = $db->func_query_first("SELECT email,telephone FROM oc_customer WHERE customer_id='".$detail['customer_id']."'");
                        $address = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".$detail['address_id']."'");
                        
                        $email = $customer_detail['email'];
                        $telephone = $customer_detail['telephone'];

                        if($detail['address_id']!='-1')
                        {

                        $firstname = $address['firstname'];
                        $lastname = $address['lastname'];
                        
                        $address_1 = $address['address_1'];
                        $city = $address['city'];
                        $postcode = $address['postcode'];
                        $zone_id = $address['zone_id'];
                    }
                    else
                    {
                        $firstname = $detail['firstname'];
                        $lastname = $detail['lastname'];
                        
                        $address_1 = $detail['address_1'];

                        $city = $detail['city'];
                        $postcode = $detail['postcode'];
                        $zone_id = $detail['zone_id'];

                    }
                    }
                    $zone = $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$zone_id."'");
                    $products = $db->func_query("SELECT * FROM oc_buyback_products WHERE buyback_id='".$detail['buyback_id']."'");
                    $admin_combine_total = 0.00;
                    $sent_qty = 0;
                    $received_qty = 0;
                    $accepted_qty = 0;
                    $rejected_qty = 0;
                    $qc_qty = 0;
                    foreach($products as $product)
                    {
                        $sent_qty+= $product['oem_quantity'] + $product['non_oem_quantity'];

                       
                        $received_qty+=$product['total_received'];
                        

                        $_quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");
                                        if($_quantities)
                                        {
                                            $_oem_qty = (int)$_quantities['oem_received'];
                                            $_non_oem_qty = (int)$_quantities['non_oem_received'];
                                            $_rejected_qty = (int)$_quantities['rejected_qty'];   
                                        }
                                        $qc_qty+=$product['total_qc_received'];
                                        $qc_oem_total+=$_oem_qty;
                                        $qc_non_oem_total+=$_non_oem_qty;
                                        $rejected_qty+=$_rejected_qty; 

                                        $accepted_qty+=$_oem_qty+$_non_oem_qty;
                                       // $qc_qty+=$_oem_qty+$_non_oem_qty+$_rejected_qty; 

                        if($product['data_type']!='customer' and $product['data_type']!='qc' and $product['data_type']!='admin') continue;


                        $quantities = $db->func_query_first("SELECT * FROM inv_buyback_shipments WHERE buyback_product_id='".$product['buyback_product_id']."'");

                        if($quantities)
                        {
                            $oem_qty = (int)$quantities['oem_received'];
                            $non_oem_qty = (int)$quantities['non_oem_received'];
                        }
                        if($product['admin_updated']=='1')
                        {
                            $oem_qty = $product['admin_oem_qty'];

                            $non_oem_qty = $product['admin_non_oem_qty'];
                        }

                        $admin_oem_total+=(int)$oem_qty * (float)$product['oem_price'];
                        $admin_non_oem_total+=(int)$non_oem_qty * (float)$product['non_oem_price'];

                        $admin_total = ($oem_qty * $product['oem_price']) + ($non_oem_qty * $product['non_oem_price']);;

                        $admin_combine_total+=(float)$admin_total;
                    }
                    $rowData = array(
                        americanDate($detail['date_added']),
                        $detail['shipment_number'],
                        $firstname.' '.$lastname,
                        $email,
                        $address_1,
                        $city,
                        $zone,
                        $postcode,
                        $detail['payment_type'],
                        $detail['paypal_email'],
                        round($detail['total'],2),
                        round($admin_combine_total,2),
                        $sent_qty,
                        $received_qty,
                        $accepted_qty,
                        $rejected_qty,
                        $qc_qty

                        );
fputcsv($fp, $rowData,',');
}


fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);
exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

    <title>CSV Export</title>

    <script type="text/javascript" src="../js/jquery.min.js"></script>

    

</head>

<?php



?>

<body>

    <div align="center">

<div style="display:none">  <?php include_once '../inc/header.php'; ?></div>



    


        <br clear="all" />







        <div align="center">

            <form action="" id="myFrm" method="post">

                <h2>CSV Export</h2>

                <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">

                    <tr>

                        <td>Month / Year</td>

                        <td>
                            <select name="month" id="month">
                            <?php
                            for($i=1;$i<=12;$i++)
                            {
                                ?>
                                <option value="<?=$i;?>"><?=date('F', mktime(0,0,0,$i));?></option>
                                <?php
                            }
                            ?>
                            </select>
                                /
                                 <select name="year" id="year">
                            <?php
                            for($i=2015;$i<=date('Y');$i++)
                            {
                                ?>
                                <option value="<?=$i;?>"><?=$i;?></option>
                                <?php
                            }
                            ?>
                            </select>
                        </td>

                    </tr>
                    <tr>
 <td>Status</td>

                        <td>
                            <select name="status" id="status">
                            <option value="">All</option>
                            <option value="Awaiting">Awaiting</option>
                            <option value="Received">Received</option>
                            <option value="In QC">QC Received</option>
                            <option value="Completed">Completed</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    <td colspan="2" align="center"><input type="submit" name="submit" value="Generate"></td>
                    </tr>



                    
                                </table>

                             

</form>

</div>      


</div>           

</body>

</html>