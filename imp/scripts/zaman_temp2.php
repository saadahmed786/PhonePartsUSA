<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
// require_once("../easypost/lib/easypost.php");
// \EasyPost\EasyPost::setApiKey(EASYPOST_API);


    /*
  $rows = $db->func_query("select distinct b.product_sku from inv_shipment_items b,inv_shipments a  where a.id=b.shipment_id and a.status='Completed' and year(a.date_added)>='2017'");
  foreach($rows as $row)
  {
  	$vendor_id = $db->func_query_first_cell("select  a.vendor from inv_shipment_items b,inv_shipments a  where a.id=b.shipment_id and a.status='Completed' and b.product_sku='".$row['product_sku']."' and vendor not in (0,97,46) order by a.id desc limit 1");

  	if($vendor_id && $row['product_sku'])
  	{
  		$check = $db->func_query_first_cell("SELECT vendor from inv_product_vendors where product_sku='".$db->func_escape_string($row['product_sku'])."' and vendor='".(int)$vendor_id."'");
				if(!$check)
				{

					// echo $vendor_id.'--'.$row['product_sku']."<br>";
			$db->db_exec("DELETE FROM inv_product_vendors where product_sku='".$row['product_sku']."'");
			$db->db_exec("INSERT INTO inv_product_vendors SET vendor='".(int)$vendor_id."',product_sku='".$db->func_escape_string($row['product_sku'])."'");
			}
  	}
  }*/
  
  /*$rows = $db->func_query("select * from inv_vendor_po where date(date_added)>='2018-12-01'");
  foreach($rows as $row)
  {
    $vpo_id = (int)($row['id']);
    // $row = $db->func_query_first("SELECT * FROM inv_vendor_po WHERE id = '$vpo_id'");;
    $vendor_id = $row['vendor'];
    $vendor_po_id = $row['vendor_po_id'];


    $applied_credits = $db->func_query_first_cell("SELECT SUM(amount) from inv_vendor_credit_data where vendor_id='".$row['vendor']."' and vendor_po_id='".$row['id']."'");

    $shipment_data = $db->func_query_first("SELECT a.ex_rate, SUM(b.qty_received) as qty_received,sum(b.unit_price * b.qty_received) as unit_price,(select sum(c.shipping_cost) from inv_shipments c where c.vendor_po_id=a.vendor_po_id) as shipping_cost from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po_id."'");

   
        $payment_status_new = 'No Payment Status';
    
     if((int)$shipment_data['qty_received']==0 && $row['amount_paid']+($applied_credits*(-1))>0)
        {
            $payment_status_new = 'Pre-Paid';
        }
     if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($row['amount_paid']-$applied_credits)+$row['amount_refunded']))==0)
        {
            $payment_status_new= 'Paid';
        }

        if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($row['amount_paid']-$applied_credits)+$row['amount_refunded']))>0)
        {
            $payment_status_new= 'Not Paid';
        }

        if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($row['amount_paid']-$applied_credits)+$row['amount_refunded']))<0)
        {
            $payment_status_new= 'Over-Paid';
        }
        // if($row['payment_status_new']!=$payment_status_new)
        // {
        echo $vendor_po_id.'--'.$row['payment_status_new']."--$payment_status_new<br>";
            
       $db->db_exec("UPDATE inv_vendor_po SET payment_status_new='".$payment_status_new."' WHERE id='".$vpo_id."'");
        // }

  }*/

/*
$rows = $db->func_query("select * from inv_shipstation_transactions where year(ship_date)='2018' and month(ship_date)='09' and tracking_number not in (select tracking_code from inv_tracker)");

foreach($rows as $row)
{

    $carrier = $row['carrier_code'];
// UnComment when funds available
switch($carrier)
{
    case 'express_1':
    case 'endicia':
    case 'stamps_com':
        $_carrier = 'USPS';
    break;
    case 'fedex':
        $_carrier = 'FedEx';
    break;

    case 'ups':
        $_carrier = 'UPS';
    break;
}


    $tracker_obj = \EasyPost\Tracker::create(array('tracking_code' => $row['tracking_number'],'carrier'=>$_carrier));
 $tracker_info = \EasyPost\Tracker::retrieve($tracker_obj->id);

 if($tracker_info->id)
 {
    $_array = array();
    $_array['tracker_id'] = $tracker_info->id;
    $_array['tracking_code'] = $tracker_info->tracking_code;
    $_array['status'] = $tracker_info->status;
    $_array['created_at'] = $tracker_info->created_at;
    $_array['updated_at'] = $tracker_info->updated_at;
    $_array['weight'] = (float)$tracker_info->weight;
    $_array['est_delivery_date'] = $tracker_info->est_delivery_date;
    
    $_array['shipment_id'] = $tracker_info->shipment_id;
        
    
    $_array['carrier'] = $tracker_info->carrier;
    
    
    $check = $db->func_query_first_cell("SELECT tracker_id FROM inv_tracker WHERE tracker_id='".$tracker_info['id']."'");
    if($check)
    {
    $db->func_array2update("inv_tracker",$_array," tracker_id = '".$tracker_info->id."' ");
    }
    else
    {
        $_array['datetime'] = date('Y-m-d H:i:s');
        $db->func_array2insert("inv_tracker", $_array);
    }
    $db->db_exec("DELETE FROM inv_tracker_status WHERE tracker_id='".$tracker_info->id."'");
    foreach($tracker_info->tracking_details as $detail)
    {
        $_array = array();
    $_array['tracker_id'] = $tracker_info->id;
    $_array['message'] = $db->func_escape_string($detail->message);
    $_array['status'] = $db->func_escape_string($detail->status);
    $_array['datetime'] = $db->func_escape_string($detail->datetime);
    $_array['tracking_location'] = $db->func_escape_string($detail->tracking_location);
    $db->func_array2insert("inv_tracker_status", $_array);

    }
 }

            
        
        
       
        
        
  
    curl_close($ch);
}*/

/*$rows = $db->func_query("SELECT a.pp_transaction_id as transaction_id,a.order_id,a.amount FROM oc_payflow_admin_tools a,inv_transactions b where a.pp_transaction_id=b.transaction_id and a.amount<>b.amount and a.order_id>540000 order by b.id desc");
foreach($rows as $row)
{
    $db->db_exec("UPDATE inv_transactions set amount='".$row['amount']."' WHERE order_id='".$row['order_id']."' and transaction_id='".$row['transaction_id']."'");
    echo $row['order_id'].'--'.$row['amount'];
}*/


/*$skus = array('APL-001-2223', 'APL-001-0006', 'APL-001-0018', 'APL-001-0030', 'APL-001-0036', 'APL-001-0042', 'APL-001-0066', 'APL-001-0072', 'APL-001-0084', 'APL-001-0090', 'APL-001-0096', 'APL-001-0102', 'APL-001-0186', 'APL-001-0192', 'APL-001-0282', 'APL-001-0408', 'APL-001-0410', 'APL-001-0411', 'APL-001-0415', 'APL-001-0418', 'APL-001-0420', 'APL-001-0483', 'APL-001-0492', 'APL-001-0504', 'APL-001-0510', 'APL-001-0512', 'APL-001-0514', 'APL-001-0516', 'APL-001-0522', 'APL-001-0534', 'APL-001-0552', 'APL-001-0558', 'APL-001-0564', 'APL-001-0570', 'APL-001-0576', 'APL-001-0582', 'APL-001-0588', 'APL-001-0594', 'APL-001-0612', 'APL-001-0700', 'APL-001-0701', 'APL-001-0702', 'APL-001-0704', 'APL-001-0712', 'APL-001-0718', 'APL-001-0728', 'APL-001-0730', 'APL-001-0732', 'APL-001-0772', 'APL-001-0776', 'APL-001-0782', 'APL-001-0800', 'APL-001-0904', 'APL-001-0908', 'APL-001-0910', 'APL-001-0914', 'APL-001-0916', 'APL-001-0922', 'APL-001-0926', 'APL-001-0928', 'APL-001-0932', 'APL-001-0934', 'APL-001-0936', 'APL-001-0948', 'APL-001-0950', 'APL-001-0956', 'APL-001-0962', 'APL-001-0964', 'APL-001-0966', 'APL-001-0968', 'APL-001-0970', 'APL-001-0972', 'APL-001-0974', 'APL-001-0980', 'APL-001-0990', 'APL-001-0996', 'APL-001-1002', 'APL-001-1004', 'APL-001-1014', 'APL-001-1018', 'APL-001-1026', 'APL-001-1028', 'APL-001-1034', 'APL-001-1036', 'APL-001-1056', 'APL-001-1094', 'APL-001-1108', 'APL-001-1114', 'APL-001-1156', 'APL-001-1180', 'APL-001-1238', 'APL-001-1240', 'APL-001-1248', 'APL-001-1250', 'APL-001-1252', 'APL-001-1254', 'APL-001-1258', 'APL-001-1638', 'APL-001-1692', 'APL-001-1714', 'APL-001-1722', 'APL-001-1736', 'APL-001-1946', 'APL-001-1986', 'APL-001-2047', 'APL-001-2048', 'APL-001-2062', 'APL-001-2063', 'APL-001-2064', 'APL-001-2065', 'APL-001-2080', 'APL-001-2081', 'APL-001-2085', 'APL-001-2086', 'APL-001-2087', 'APL-001-2089', 'APL-001-2090', 'APL-001-2091', 'APL-001-2092', 'APL-001-2093', 'APL-001-2094', 'APL-001-2096', 'APL-001-2099', 'APL-001-2107', 'APL-001-2109', 'APL-001-2111', 'APL-001-2112', 'APL-001-2113', 'APL-001-2115', 'APL-001-2116', 'APL-001-2120', 'APL-001-2121', 'APL-001-2122', 'APL-001-2134', 'APL-001-2136', 'APL-001-2138', 'APL-001-2144', 'APL-001-2147', 'APL-001-2152', 'APL-001-2155', 'APL-001-2173', 'APL-001-2177', 'APL-001-2183', 'APL-001-2185', 'APL-001-2195', 'APL-001-2196', 'APL-001-2197', 'APL-001-2198', 'APL-001-2200', 'APL-001-2202', 'APL-001-2203', 'APL-001-2204', 'APL-001-2210', 'APL-001-2211', 'APL-001-2214', 'APL-001-2215', 'APL-001-2218', 'APL-001-2219', 'APL-001-2220', 'APL-001-2225', 'APL-001-2226', 'APL-001-2227', 'APL-001-2228', 'APL-001-2229', 'APL-001-2230', 'APL-001-2231', 'APL-001-2232', 'APL-001-2245', 'APL-001-2249', 'APL-001-2250', 'APL-001-2253', 'APL-001-2254 ', 'APL-001-2256 ', 'APL-001-2258', 'APL-001-2259', 'APL-001-2260', 'APL-001-2264', 'APL-001-2265', 'APL-001-2266', 'APL-001-2267', 'APL-001-2268', 'APL-001-2269', 'APL-001-2272', 'APL-001-2273', 'APL-001-2274', 'APL-001-2275', 'APL-001-2276', 'APL-001-2277', 'APL-001-2281', 'APL-001-2285', 'APL-001-2287', 'APL-001-2288', 'APL-001-2293', 'APL-001-2294', 'APL-001-2295', 'APL-001-2298', 'APL-001-2299', 'APL-001-2301', 'APL-001-2305', 'APL-001-2308', 'APL-001-2309', 'APL-001-2310', 'APL-001-2311', 'APL-001-2321', 'APL-001-2322', 'APL-001-2324', 'APL-001-2325', 'APL-001-2326', 'APL-001-2327', 'APL-001-2333', 'APL-001-2334', 'APL-001-2337', 'APL-001-2342', 'APL-001-2346', 'APL-001-2347', 'APL-001-2350', 'APL-001-2352', 'APL-001-2353', 'APL-001-2354', 'APL-001-2357', 'APL-001-2362', 'APL-001-2363', 'APL-001-2364', 'APL-001-2365', 'APL-001-2366', 'APL-001-2367', 'APL-001-2368', 'APL-001-2369', 'APL-001-2370', 'APL-001-2371', 'APL-001-2372', 'APL-001-2376', 'APL-001-2377', 'APL-001-2378', 'APL-001-2379', 'APL-001-2380', 'APL-001-2381', 'APL-001-2382', 'APL-001-2383', 'APL-001-2395', 'APL-001-2398', 'APL-001-2399', 'APL-001-2403', 'APL-001-2408', 'APL-001-2409', 'APL-001-2410', 'APL-001-2413', 'APL-001-2414', 'APL-001-2422', 'APL-001-2423', 'APL-001-2426', 'APL-001-2428', 'APL-001-2430', 'APL-001-2432', 'APL-001-2434', 'APL-001-2436', 'APL-001-2444', 'APL-001-2445', 'APL-001-2446', 'APL-001-2447', 'APL-001-2448', 'APL-001-2449', 'APL-001-2450', 'APL-001-2451', 'APL-001-2452', 'APL-001-2453', 'APL-001-2454', 'APL-001-2455', 'APL-001-2456', 'APL-001-2457', 'APL-001-2461', 'APL-001-2462', 'APL-001-2464', 'APL-001-2465', 'APL-001-2466', 'APL-001-2467', 'APL-001-2470', 'APL-001-2471', 'APL-001-2472', 'APL-001-2475', 'APL-001-2476', 'APL-001-2477', 'APL-001-2478', 'APL-001-2480', 'APL-001-2482', 'APL-001-2491', 'APL-001-2492', 'APL-003-1591', 'APL-003-1704', 'APL-003-1721', 'APL-003-1736', 'APL-003-1743', 'APL-003-1744', 'APL-003-1745', 'APL-003-1746', 'APL-003-1749', 'APL-003-1750', 'APL-003-1751', 'APL-003-1802', 'APL-003-1808', 'APL-003-1812', 'APL-003-1824', 'APL-003-1854', 'FLX-SAM-1571', 'SRN-BLB-008', 'SRN-BLB-012', 'SRN-BLB-016', 'SRN-BLB-018', 'SRN-BLB-020', 'SRN-BLB-022', 'SRN-BLB-024', 'SRN-BLB-026', 'SRN-BLB-030', 'SRN-BLB-032', 'SRN-BLB-066', 'SRN-BLB-068', 'SRN-BLB-070', 'SRN-BLB-073', 'SRN-BLB-077', 'SRN-BLB-079', 'SRN-BLB-086', 'SRN-BLB-088', 'SRN-HT-1147', 'SRN-HTC-006', 'SRN-HTC-012', 'SRN-HTC-018', 'SRN-HTC-024', 'SRN-HTC-030', 'SRN-HTC-036', 'SRN-HTC-042', 'SRN-HTC-043', 'SRN-HTC-045', 'SRN-HTC-046', 'SRN-HTC-048', 'SRN-HTC-054', 'SRN-HTC-060', 'SRN-HTC-066', 'SRN-HTC-072', 'SRN-HTC-078', 'SRN-HTC-084', 'SRN-HTC-090', 'SRN-HTC-096', 'SRN-HTC-102', 'SRN-HTC-1024', 'SRN-HTC-1026', 'SRN-HTC-1038', 'SRN-HTC-1042', 'SRN-HTC-108', 'SRN-HTC-1086', 'SRN-HTC-1090', 'SRN-HTC-1094', 'SRN-HTC-1096', 'SRN-HTC-1098', 'SRN-HTC-1119', 'SRN-HTC-1126', 'SRN-HTC-1131', 'SRN-HTC-1132', 'SRN-HTC-1136', 'SRN-HTC-1139', 'SRN-HTC-1142', 'SRN-HTC-1145', 'SRN-HTC-1147', 'SRN-HTC-1148', 'SRN-HTC-1149', 'SRN-HTC-1150', 'SRN-HTC-1151', 'SRN-HTC-1152', 'SRN-HTC-1155', 'SRN-HTC-1156', 'SRN-HTC-1157', 'SRN-HTC-1160', 'SRN-HTC-1161', 'SRN-HTC-120', 'SRN-HTC-126', 'SRN-HTC-132', 'SRN-HTC-144', 'SRN-HTC-150', 'SRN-HTC-156', 'SRN-HTC-162', 'SRN-HTC-168', 'SRN-HTC-174', 'SRN-HTC-180', 'SRN-HTC-186', 'SRN-HTC-198', 'SRN-HTC-210', 'SRN-HTC-216', 'SRN-HTC-222', 'SRN-HTC-228', 'SRN-HTC-234', 'SRN-HTC-240', 'SRN-HTC-252', 'SRN-HTC-258', 'SRN-HTC-264', 'SRN-HTC-270', 'SRN-HTC-276 ', 'SRN-HTC-282', 'SRN-HTC-288', 'SRN-HTC-294', 'SRN-HTC-300', 'SRN-HTC-306', 'SRN-HTC-312', 'SRN-HTC-318', 'SRN-HTC-324', 'SRN-HTC-330', 'SRN-HTC-336', 'SRN-HTC-342', 'SRN-HTC-348', 'SRN-HTC-354', 'SRN-HTC-360', 'SRN-HTC-366', 'SRN-HTC-372', 'SRN-HTC-378', 'SRN-HTC-384', 'SRN-HTC-390', 'SRN-HTC-396', 'SRN-HTC-402', 'SRN-HTC-408 ', 'SRN-HTC-414', 'SRN-HTC-426', 'SRN-HTC-432', 'SRN-HTC-438', 'SRN-HTC-444', 'SRN-HTC-450', 'SRN-HTC-456', 'SRN-HTC-462', 'SRN-HTC-468', 'SRN-HTC-474', 'SRN-HTC-480', 'SRN-HTC-486', 'SRN-HTC-492', 'SRN-HTC-498', 'SRN-HTC-504', 'SRN-HTC-510', 'SRN-HTC-516', 'SRN-HTC-522', 'SRN-HTC-528', 'SRN-HTC-534', 'SRN-HTC-540', 'SRN-HTC-550', 'SRN-HTC-554', 'SRN-HTC-556', 'SRN-HTC-560', 'SRN-HTC-568', 'SRN-HTC-572', 'SRN-HTC-578', 'SRN-HTC-582', 'SRN-HTC-584', 'SRN-HTC-586', 'SRN-HTC-588', 'SRN-HTC-590', 'SRN-HTC-596', 'SRN-HTC-600', 'SRN-HTC-602', 'SRN-HTC-606', 'SRN-HTC-608', 'SRN-HTC-626', 'SRN-HTC-644', 'SRN-HTC-646', 'SRN-HTC-650', 'SRN-HTC-652', 'SRN-HTC-654', 'SRN-HTC-658', 'SRN-HTC-660', 'SRN-HTC-732', 'SRN-HTC-744', 'SRN-HTC-746', 'SRN-HTC-764', 'SRN-HTC-772', 'SRN-HTC-776', 'SRN-HTC-804', 'SRN-HTC-812', 'SRN-HTC-830', 'SRN-HTC-834', 'SRN-HTC-862', 'SRN-HTC-884', 'SRN-HTC-892', 'SRN-HTC-908', 'SRN-HTC-918', 'SRN-HTC-922', 'SRN-HTC-934', 'SRN-HTC-946', 'SRN-HTC-948', 'SRN-HTC-952', 'SRN-HTC-976', 'SRN-HUA-084', 'SRN-HUA-152', 'SRN-HUA-163', 'SRN-HUA-164', 'SRN-HUA-165', 'SRN-LGM-006 ', 'SRN-LGM-012 ', 'SRN-LGM-024 ', 'SRN-LGM-030', 'SRN-LGM-036', 'SRN-LGM-048 ', 'SRN-LGM-060', 'SRN-LGM-066', 'SRN-LGM-072', 'SRN-LGM-078', 'SRN-LGM-090', 'SRN-LGM-096', 'SRN-LGM-102', 'SRN-LGM-108', 'SRN-LGM-114', 'SRN-LGM-120', 'SRN-LGM-132', 'SRN-LGM-138', 'SRN-LGM-146', 'SRN-LGM-150', 'SRN-LGM-156', 'SRN-LGM-160', 'SRN-LGM-164', 'SRN-LGM-166', 'SRN-LGM-290', 'SRN-LGM-296', 'SRN-LGM-298', 'SRN-LGM-316', 'SRN-LGM-318', 'SRN-LGM-320', 'SRN-LGM-324', 'SRN-LGM-336', 'SRN-LGM-348', 'SRN-LGM-358', 'SRN-LGM-360', 'SRN-LGM-362', 'SRN-LGM-406', 'SRN-LGM-414', 'SRN-LGM-500', 'SRN-LGM-512', 'SRN-LGM-522', 'SRN-LGM-534', 'SRN-LGM-536', 'SRN-LGM-542', 'SRN-LGM-559', 'SRN-LGM-561', 'SRN-LGM-569', 'SRN-LGM-579', 'SRN-LGM-580', 'SRN-LGM-584', 'SRN-LGM-593', 'SRN-LGM-614', 'SRN-LGM-627', 'SRN-LGM-636', 'SRN-LGM-637', 'SRN-LGM-638', 'SRN-LGM-642', 'SRN-LGM-649', 'SRN-LGM-653', 'SRN-LGM-655', 'SRN-LGM-657', 'SRN-LGM-663', 'SRN-LGM-667', 'SRN-LGM-673', 'SRN-LGM-675', 'SRN-LGM-676', 'SRN-LGM-677', 'SRN-LGM-681', 'SRN-LGM-683', 'SRN-LGM-684', 'SRN-LGM-685', 'SRN-LGM-696', 'SRN-LGM-700', 'SRN-MOT-018', 'SRN-MOT-030', 'SRN-MOT-036', 'SRN-MOT-042', 'SRN-MOT-048', 'SRN-MOT-054', 'SRN-MOT-060', 'SRN-MOT-066', 'SRN-MOT-072', 'SRN-MOT-078', 'SRN-MOT-084', 'SRN-MOT-090', 'SRN-MOT-102', 'SRN-MOT-120', 'SRN-MOT-126', 'SRN-MOT-132', 'SRN-MOT-138', 'SRN-MOT-144', 'SRN-MOT-158', 'SRN-MOT-274', 'SRN-MOT-288', 'SRN-MOT-292', 'SRN-MOT-294', 'SRN-MOT-296', 'SRN-MOT-298', 'SRN-MOT-300', 'SRN-MOT-312', 'SRN-MOT-326', 'SRN-MOT-332', 'SRN-MOT-344', 'SRN-MOT-346', 'SRN-MOT-358', 'SRN-MOT-362', 'SRN-MOT-392', 'SRN-MOT-404', 'SRN-MOT-406', 'SRN-MOT-410', 'SRN-MOT-414', 'SRN-MOT-422', 'SRN-MOT-426', 'SRN-MOT-440', 'SRN-MOT-442', 'SRN-MOT-464', 'SRN-MOT-606', 'SRN-MOT-610', 'SRN-MOT-612', 'SRN-MOT-615', 'SRN-MOT-616', 'SRN-MOT-617', 'SRN-MOT-618', 'SRN-MOT-632', 'SRN-MOT-638', 'SRN-MOT-653', 'SRN-MOT-655', 'SRN-MOT-659', 'SRN-MOT-660', 'SRN-MOT-664', 'SRN-MOT-665', 'SRN-MOT-666', 'SRN-MOT-678', 'SRN-MOT-679', 'SRN-MOT-680', 'SRN-MOT-682', 'SRN-MOT-684', 'SRN-MOT-686', 'SRN-MOT-688', 'SRN-MOT-691', 'SRN-MOT-692', 'SRN-NOK-006', 'SRN-NOK-012', 'SRN-NOK-018', 'SRN-NOK-024', 'SRN-NOK-030', 'SRN-NOK-054', 'SRN-NOK-060', 'SRN-NOK-062', 'SRN-NOK-064', 'SRN-NOK-066', 'SRN-NOK-072', 'SRN-NOK-076', 'SRN-NOK-080', 'SRN-NOK-082', 'SRN-NOK-084', 'SRN-NOK-086', 'SRN-NOK-088', 'SRN-NOK-090', 'SRN-NOK-152', 'SRN-NOK-153', 'SRN-NOK-157', 'SRN-NOK-166', 'SRN-NOK-169', 'SRN-SAM-036', 'SRN-SAM-084', 'SRN-SAM-1162', 'SRN-SAM-1188', 'SRN-SAM-1206', 'SRN-SAM-126', 'SRN-SAM-1331', 'SRN-SAM-1389', 'SRN-SAM-1401', 'SRN-SAM-1414', 'SRN-SAM-1460', 'SRN-SAM-1488', 'SRN-SAM-1503', 'SRN-SAM-1600', 'SRN-SAM-1601', 'SRN-SAM-1602', 'SRN-SAM-1603', 'SRN-SAM-1605', 'SRN-SAM-1606', 'SRN-SAM-1607', 'SRN-SAM-1610', 'SRN-SAM-1611', 'SRN-SAM-1612', 'SRN-SAM-1613', 'SRN-SAM-1616', 'SRN-SAM-1617', 'SRN-SAM-1618', 'SRN-SAM-1619', 'SRN-SAM-1620', 'SRN-SAM-1626', 'SRN-SAM-1627', 'SRN-SAM-1629', 'SRN-SAM-1630', 'SRN-SAM-1632', 'SRN-SAM-1633', 'SRN-SAM-1634', 'SRN-SAM-1635', 'SRN-SAM-1636', 'SRN-SAM-1637', 'SRN-SAM-1642', 'SRN-SAM-1644', 'SRN-SAM-1645', 'SRN-SAM-1648', 'SRN-SAM-1654', 'SRN-SAM-1657', 'SRN-SAM-1659', 'SRN-SAM-1660', 'SRN-SAM-1661', 'SRN-SAM-1662', 'SRN-SAM-1663', 'SRN-SAM-1665', 'SRN-SAM-1669', 'SRN-SAM-1670', 'SRN-SAM-1671', 'SRN-SAM-1673', 'SRN-SAM-1674', 'SRN-SAM-1686', 'SRN-SAM-1736', 'SRN-SAM-1738', 'SRN-SAM-174', 'SRN-SAM-1740', 'SRN-SAM-1741', 'SRN-SAM-1745', 'SRN-SAM-1748', 'SRN-SAM-1751', 'SRN-SAM-1752', 'SRN-SAM-1757', 'SRN-SAM-218', 'SRN-SAM-236', 'SRN-SAM-472', 'SRN-SAM-474', 'SRN-SAM-478', 'SRN-SAM-512', 'SRN-SAM-558', 'SRN-SAM-572', 'SRN-SAM-690', 'SRN-SAM-692', 'SRN-SAM-892', 'SRN-SAM-968', 'SRN-SNY-018', 'SRN-SNY-020', 'SRN-SNY-034', 'SRN-SNY-038', 'SRN-SNY-042', 'SRN-SNY-050', 'SRN-SNY-062', 'SRN-SNY-066', 'SRN-SNY-082', 'SRN-SNY-096', 'SRN-SNY-128', 'SRN-SNY-139', 'SRN-SNY-144', 'SRN-SNY-154', 'SRN-ZTE-002', 'SRN-ZTE-004', 'SRN-ZTE-008', 'SRN-ZTE-013', 'SRN-ZTE-020', 'TAB-SRN-002', 'TAB-SRN-018', 'TAB-SRN-036', 'TAB-SRN-102', 'TAB-SRN-144', 'TAB-SRN-230', 'TAB-SRN-294', 'TAB-SRN-308', 'TAB-SRN-316', 'TAB-SRN-322', 'TAB-SRN-334', 'TAB-SRN-500', 'TAB-SRN-508', 'TAB-SRN-578', 'TAB-SRN-672', 'TAB-SRN-673');
$filename = 'product_shelf_count.csv';
$fp = fopen($filename, "w");
$headers = array("SKU","Shelf Count","Cost");
fputcsv($fp, $headers,',');


foreach($skus as $sku)
{
    $on_shelf = $db->func_query_first_cell("Select  p.quantity-( (SELECT COALESCE(sum(b.picked_quantity) - sum(b.packed_quantity),0) FROM inv_orders_items b where b.ostatus in ('processed','unshipped','on hold') and b.is_picked=1 and b.opacked=0 and b.product_sku=p.model) + (SELECT COALESCE(sum(b.packed_quantity),0) FROM inv_orders_items b where b.ostatus in ('processed','unshipped','on hold') and b.is_packed=1 and b.product_sku=p.model)) as on_shelf from oc_product p where p.sku='".$sku."'
           ");

    $rowData = array();

    $rowData = array($sku,(int)$on_shelf,getTrueCost($sku));
    fputcsv($fp, $rowData,',');     
}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);
*/

$rows = $db->func_query("select a.id,a.email,group_concat(b.email) as concat_email from inv_customers a left join inv_customers b on (a.id=b.parent_id) where a.parent_id=0 and a.is_cron=0 and trim(a.email)<>'' and a.email not like '%marketplace.amazon%' group by a.email  limit 10000");
foreach($rows as $row)
{
    // $child = $db->func_query_first_cell("SELECT  GROUP_CONCAT(distinct email) as email from inv_customers where parent_id='".$row['id']."'  ");
    if(!$row['concat_email'])
    {
        $child = $row['email'];
    }
    
    $db->db_exec("INSERT INTO inv_customer_linkage SET email='".$row['email']."',linkage='".$child."'");
    $db->db_exec("UPDATE inv_customers SET is_cron=1 where id='".$row['id']."'");

}
echo 1;
?>