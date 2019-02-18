<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
page_permission('customers');
if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
}



if ($page < 1) {
    $page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1) * $num_rows;

$order_id = (int) trim($_REQUEST['order_id']);
// $keyword = $db->func_escape_string((trim($_REQUEST['keyword'])));
$filter_city = $db->func_escape_string((trim($_REQUEST['filter_city'])));
$filter_state = $db->func_escape_string((trim($_REQUEST['filter_state'])));
$filter_zip = $db->func_escape_string((trim($_REQUEST['filter_zip'])));

$filter_order_select = ($_REQUEST['filter_order_select']);
$filter_order_range1 = $db->func_escape_string((trim($_REQUEST['filter_order_range1'])));
$filter_order_range2 = $db->func_escape_string((trim($_REQUEST['filter_order_range2'])));

$filter_total_select = ($_REQUEST['filter_total_select']);
$filter_total_range1 = $db->func_escape_string((trim($_REQUEST['filter_total_range1'])));
$filter_total_range2 = $db->func_escape_string((trim($_REQUEST['filter_total_range2'])));

// $firstname = $db->func_escape_string((trim($_REQUEST['firstname'])));
// $lastname = $db->func_escape_string((trim($_REQUEST['lastname'])));
$keyword = $db->func_escape_string((trim($_REQUEST['keyword'])));
$email = $db->func_escape_string((trim($_REQUEST['email'])));
$phone = $db->func_escape_string((trim($_REQUEST['phone'])));
$address_1 = $db->func_escape_string((trim($_REQUEST['address_1'])));
$address_2 = $db->func_escape_string((trim($_REQUEST['address_2'])));
$address_all = $db->func_escape_string((trim($_REQUEST['address_all'])));





$where = array();
$having = array();

if ($order_id) {
    $where[] = " customer_id in (select customer_id from oc_order where order_id  = '$order_id' ) ";
    $parameters[] = "order_id=$order_id";
    $parameters2[] = "order_id=$order_id";
}

if ($filter_city) {
    $where[] = " LOWER(city) LIKE '%" . strtolower($filter_city) . "%' ";
    $parameters[] = "filter_city=$filter_city";
    $parameters2[] = "filter_city=$filter_city";
}

// if ($firstname) {
//     $where[] = " LOWER(firstname) LIKE '%" . strtolower($firstname) . "%' ";
//     $parameters[] = "firstname=$firstname";
//     $parameters2[] = "firstname=$firstname";
// }
// if ($lastname) {
//     $where[] = " LOWER(lastname) LIKE '%" . strtolower($lastname) . "%' ";
//     $parameters[] = "lastname=$lastname";
//     $parameters2[] = "lastname=$lastname";
// }

if ($keyword) {
    $where[] = " (LOWER(concat(firstname,' ',lastname)) LIKE '%" . strtolower($keyword) . "%' or lower(company) like '%".strtolower($keyword)."%' or lower(email) like '%".strtolower($keyword)."%') ";
    $parameters[] = "keyword=$keyword";
    $parameters2[] = "keyword=$keyword";
}


if ($email) {
    $where[] = " LOWER(email) LIKE '%" . strtolower($email) . "%' ";
    $parameters[] = "email=$email";
    $parameters2[] = "email=$email";
}
if ($phone) {
    $where[] = " telephone LIKE '%" . strtolower($phone) . "%' ";
    $parameters[] = "phone=$phone";
    $parameters2[] = "phone=$phone";
}
if ($address_1) {
    $where[] = " LOWER(address1) LIKE '%" . strtolower($address_1) . "%' ";
    $parameters[] = "address_1=$address_1";
    $parameters2[] = "address_1=$address_1";
}
if ($address_2) {
    $where[] = " LOWER(address2) LIKE '%" . strtolower($address_2) . "%' ";
    $parameters[] = "address_2=$address_2";
    $parameters2[] = "address_2=$address_2";
}
if ($address_all) {
    $where[] = " LOWER(address2) LIKE '%" . strtolower($address_all) . "%' OR LOWER(address1) LIKE '%" . strtolower($address_all) . "%' ";
    $parameters[] = "address_all=$address_all";
    $parameters2[] = "address_all=$address_all";
}
if ($filter_state) {
    $where[] = " LOWER(zone_id) = '" . strtolower($filter_state) . "' ";
    $parameters[] = "filter_state=$filter_state";
    $parameters2[] = "filter_state=$filter_state";
}
if ($filter_zip) {
    $where[] = " zip LIKE '%" . strtolower($filter_zip) . "%' ";
    $parameters[] = "filter_zip=$filter_zip";
    $parameters2[] = "filter_zip=$filter_zip";
}
// if ($keyword) {
//     $where[] = " (lower(email) like '%" . strtolower($keyword) . "%' OR lower(firstname) like '%" . strtolower($keyword) . "%' OR lower(lastname) like '%" . $keyword . "%') ";
//     $parameters[] = "keyword=$keyword";
//     $parameters2[] = "keyword=$keyword";
// }

if ($filter_order_range1) {
    $parameters[] = "filter_order_select=$filter_order_select";
    $parameters[] = "filter_order_range1=$filter_order_range1";
    $parameters[] = "filter_order_range2=$filter_order_range2";

    $parameters2[] = "filter_order_select=$filter_order_select";
    $parameters2[] = "filter_order_range1=$filter_order_range1";
    $parameters2[] = "filter_order_range2=$filter_order_range2";
    if ($filter_order_select == 'BETWEEN') {
        if ($filter_order_range2) {
            $where[] = ' no_of_orders>=' . (int) $filter_order_range1 . ' AND no_of_orders<=' . (int) $filter_order_range2 . ' ';
        }
    } else {
        $where[] = ' no_of_orders' . $filter_order_select . '=' . (int) $filter_order_range1 . ' ';
    }
}

if ($filter_total_range1) {
    $parameters[] = "filter_total_select=$filter_total_select";
    $parameters[] = "filter_total_range1=$filter_total_range1";
    $parameters[] = "filter_total_range2=$filter_total_range2";

    $parameters2[] = "filter_total_select=$filter_total_select";
    $parameters2[] = "filter_total_range1=$filter_total_range1";
    $parameters2[] = "filter_total_range2=$filter_total_range2";
    if ($filter_total_select == 'BETWEEN') {
        if ($filter_total_range2) {
            $where[] = ' total_amount>=' . (int) $filter_total_range1 . ' AND total_amount<=' . (int) $filter_total_range2 . ' ';
        }
    } else {
        $where[] = ' total_amount' . $filter_total_select . '=' . (int) $filter_total_range1 . ' ';
    }
}


if ($where) {
    $where = implode(" AND ", $where);
} else {
    $where = "1 = 1";
}

$sort = $_GET['sort'];
if (!in_array($sort, array("no_of_orders", "last_order", 'date_added','city','zone_name'))) {
    $sort = "no_of_orders";
} else {
    $sort = $_GET['sort'];
}


$dir = @$_GET['dir'];
if (!$dir || !in_array($dir, array("asc", "desc"))) {
    $dir = 'desc';
}

$inv_query = "SELECT *,(select name from oc_zone WHERE inv_customers.zone_id=oc_zone.zone_id) as zone_name from inv_customers WHERE $where ".($parameters?" AND parent_id=0 ":'')." and email not like '%@marketplace.amazon%'  ORDER BY $sort $dir";

if(isset($_GET['debug']))
{
    echo $inv_query;
}
if(isset($_GET['action']) && $_GET['action']=='export_csv')
{
    if(!isset($_GET['order_id']))
    {
        echo 'You need to filter your results before exporting the csv';exit;
    }
    if($_SESSION['login_as']!='admin')
    {
        echo 'You have no access to export the customers, please contact admin';exit;
    }

    $_rows = $db->func_query($inv_query);

    if(count($_rows)>5000)
    {
        //echo 'Too much data, please filter the results accordingly and try again';exit;
    }

    $filename = 'customers.csv';
$fp = fopen($filename, "w");
$headers = array("Firstname","Lastname","Email","Telephone", "City","State", "Group","# of Orders","Total Amount",'Last Ordered');
fputcsv($fp, $headers,',');


foreach($_rows as $_row)
{
    $rowData = array($_row['firstname'],$_row['lastname'],$_row['email'],$_row['telephone'],$_row['city'],$_row['zone_name'],$_row['customer_group'],$_row['no_of_orders'],$_row['total_amount'],americanDate($_row['last_order']));
    fputcsv($fp, $rowData,',');
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

exit;
}
// echo $inv_query;
if (isset($_GET['agent_dashboard'])) {
    $agent_dashboard_customer = $db->func_query("SELECT * from inv_customers WHERE $where  ORDER BY no_of_orders desc limit 50 ");
    $json = array();
    foreach ($agent_dashboard_customer as $key => $agent_dashboard_customer) {        
    $json['agent_dashboard_customer'][$key] = $agent_dashboard_customer;
    $json['agent_dashboard_customer'][$key]['email'] = linkToProfile($agent_dashboard_customer['email'],'','','_blank');
    $json['agent_dashboard_customer'][$key]['md5'] = md5($agent_dashboard_customer['email']);
    $json['agent_dashboard_customer'][$key]['total_amount'] = number_format($agent_dashboard_customer['total_amount'],2);
    $json['agent_dashboard_customer'][$key]['last_order'] = americanDate($agent_dashboard_customer['last_order']);
    $json['agent_dashboard_customer'][$key]['date_added'] = americanDate($agent_dashboard_customer['date_added']);
    }
    if ($agent_dashboard_customer) {
        $json['success'] = 1;
    } else {
        $json['error'] = 1;
    }
    echo json_encode($json); 
    exit;   
}
if (isset($_GET['lbb_clients'])) {
    $where_lbb = base64_decode($_GET['where_lbb']);
    $lbb_clients = $db->func_query("SELECT *, COUNT(email) as totallbb, MAX(date_added) as lastlbb, SUM(total) as totalamount from oc_buyback where address_id = '-1' AND $where_lbb group by email order by buyback_id DESC");
    $json = array();
    $i=0;
    foreach ($lbb_clients as $key => $lbb_client) {
        $ocuser = $db->func_query_first_cell("SELECT email FROM oc_customer WHERE LCASE(email) = LCASE('". $lbb_client['email'] ."')");
        $pouser = $db->func_query_first_cell("SELECT email FROM inv_po_customers WHERE LCASE(email) = LCASE('". $lbb_client['email'] ."')");
        $invuser = $db->func_query_first_cell("SELECT email FROM inv_customers WHERE LCASE(email) = LCASE('". $lbb_client['email'] ."')");
        if (!empty($ocuser) || !empty($pouser) || !empty($invuser)) {
            continue;
        }      
        $json['lbb_client'][$i] = $lbb_client;
        $json['lbb_client'][$i]['email'] = linkToProfile($lbb_client['email'],'','','_blank');
        $json['lbb_client'][$i]['totalamount'] = number_format($lbb_client['totalamount'],2);
        $json['lbb_client'][$i]['zone'] = $db->func_query_first_cell('SELECT name FROM oc_zone WHERE zone_id = "' . $lbb_client['zone_id'] . '"');
        $json['lbb_client'][$i]['lastlbb'] = americanDate($lbb_client['lastlbb']);
        $json['lbb_client'][$i]['view_profile'] = '<a href="'.$host_path.'customer_profile.php?id='.base64_encode($lbb_client['email']).'">View Profile</a>';
        $i++;

    }
    if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){
        $json['show_amount'] = 1;
    }
    if ($lbb_clients) {
        $json['success'] = 1;
    } else {
        $json['error'] = 1;
    }
    //print_r($json);exit;
    echo json_encode($json); 
    exit;   
}
if (isset($_GET['po_clients'])) {
    $where_po = base64_decode($_GET['where_po']);
    $po_clients = $db->func_query("Select * from inv_po_customers where $where_po order by id DESC");
    $json = array();
    $i=0;
    foreach ($po_clients as $key => $user) {
        $orders = $db->func_query('select `order_id` from `inv_orders` where `po_business_id` = "' . $user['id'] . '"'); 
        $no_of_orders = count($orders);
        $order_ids = array();
        foreach ($orders as $order) {
            $order_ids[] = $order['order_id'];
        }
        $total = $db->func_query_first_cell('select sum(`product_price`) as `total` from `inv_orders_items` where `order_id` in ("' . implode('","', $order_ids) . '")') ;
        if ($filter_order_range1) {
            if ($filter_order_select == 'BETWEEN') {
                if ($no_of_orders < $filter_order_range1 or $no_of_orders > $filter_order_range2) {
                    continue;
                }
            }
            if ($filter_order_select == '>') {
                if ($no_of_orders < $filter_order_range1) {
                    continue;
                }
            }
            if ($filter_order_select == '<') {
                if ($no_of_orders > $filter_order_range1) {
                    continue;
                }
            }
        }

        if ($filter_total_range1) {
            if ($filter_total_select == 'BETWEEN') {
                if ($total < $filter_total_range1 or $total > $filter_total_range2) {
                    continue;
                }
            }
            if ($filter_total_select == '>') {
                if ($total < $filter_total_range1) {
                    continue;
                }
            }
            if ($filter_total_select == '<') {
                if ($total > $filter_total_range1) {
                    continue;
                }
            }
        }

        $json['po_client'][$i] = $user;
        $json['po_client'][$i]['email'] = linkToProfile($user['email'],'','','_blank');
        $json['po_client'][$i]['total'] =($total) ? $total : '0.00';
        $json['po_client'][$i]['no_of_orders'] = $no_of_orders;
        $json['po_client'][$i]['view_profile'] = '<a href="'.$host_path.'customer_profile.php?id='.base64_encode($user['email']).'">View Profile</a>';
        $i++;

    }
    if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){
        $json['show_amount'] = 1;
    }
    if ($json['po_client'][0]) {
        $json['success'] = 1;
    } else {
        $json['error'] = 1;
    }
    echo json_encode($json); 
    exit;   
}
$splitPage = new splitPageResults($db, $inv_query, $num_rows, "customers.php", $page);
// echo http_build_query($_REQUEST);
$_cache = md5(http_build_query($_REQUEST));
$customers = $cache->get('customers.'.$page.'.'.$_cache);
// $customers = array();
// print_r($customers);exit;
if (!$customers) {
     
    $customers = $db->func_query($splitPage->sql_query);
    $cache->set('customers.'.$page.'.'.$_cache,$customers);
    
}
$parameters[] = "sort=" . $sort;
$parameters[] = "dir=" . $dir;
if ($parameters) {
    $parameters = implode("&", $parameters);
} else {
    $parameters = '';
}
if ($parameters2) {
    $parameters2 = implode("&", $parameters2);
} else {
    $parameters2 = '';
}

if ($dir == 'desc') {
    $dir = 'asc';
} else {
    $dir = 'desc';
}
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link href="include/calendar.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />
    <link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="include/calendar.js"></script>
    <script type="text/javascript" src="include/calendar-en.js"></script>
    <script type="text/javascript" src="include/calhelper.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script>
function customerOCLogin(customer_id,salt)
{
    if(!confirm('Are you sure want to access customer account?'))
    {
        return false;
    }
    ((this.value !== '') ? window.open('https://phonepartsusa.com/index.php?route=account/login/backdoor&customer_id='+customer_id+'&salt='+salt) : null); this.value = '';
}

</script>
<script type="text/javascript">
    function fetchLBBClients(where){
        //console.log(where);return false;
        $.ajax({
            url: 'customers.php?lbb_clients=1&where_lbb='+where,
            type: 'get',
            dataType: 'json',
            beforeSend: function () {
                $('#lbb_clients').html('');
                $('#lbb_loader').show();
            },
            complete: function () {
                $('#lbb_loader').hide();
            },
            success: function (json) {

                if (json['success']) {
                    var html = '';
                    if(json['show_amount']){
                    //alert('here');return false;
                        for (var i = 0; i < json['lbb_client'].length; i++) {
                        html+='<tr>';
                        html+='<td>'+json['lbb_client'][i]['firstname']+' '+json['lbb_client'][i]['lastname']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['telephone']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['email']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['city']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['zone']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['totallbb']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['lastlbb']+'</td>';
                        html+='<td>$'+json['lbb_client'][i]['totalamount']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['view_profile']+'</td>';
                        html+='</tr>';
                     }
                 } else {
                        for (var i = 0; i < json['lbb_client'].length; i++) {
                        html+='<tr>';
                        html+='<td>'+json['lbb_client'][i]['firstname']+' '+json['lbb_client'][i]['lastname']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['telephone']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['email']+'</td>';
                        html+='<td>$'+json['lbb_client'][i]['city']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['zone']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['totallbb']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['lastlbb']+'</td>';
                        html+='<td>'+json['lbb_client'][i]['view_profile']+'</td>';
                        html+='</tr>';
                     }

                    }
                    $('#lbb_clients').html(html);

                } else {
                    var html = '';
                    html+='<tr>';
                    html+='<td align="center" colspan="9"><b> No LBB Clients Found</b></td>';
                    html+='</tr>';
                    $('#lbb_clients').html(html);


                }


            }
        });

    }
    function fetchPOClients(where){
        //console.log(where);return false;
        $.ajax({
            url: 'customers.php?po_clients=1&where_po='+where+'&filter_order_select='+$('#fos').val()+'&filter_order_range1='+$('#for1').val()+'&filter_order_range2='+$('#for2').val()+'&filter_total_select='+$('#fts').val()+'&filter_total_range1='+$('#ftr1').val()+'&filter_total_range2='+$('#ftr2').val(),
            type: 'get',
            dataType: 'json',
            beforeSend: function () {
                $('#po_clients').html('');
                $('#po_loader').show();
            },
            complete: function () {
                $('#po_loader').hide();
            },
            success: function (json) {

                if (json['success']) {
                    var html = '';
                    if(json['show_amount']){
                    //alert('here');return false;
                        for (var i = 0; i < json['po_client'].length; i++) {
                        html+='<tr>';
                        html+='<td>'+json['po_client'][i]['company_name']+'</td>';
                        html+='<td>'+json['po_client'][i]['contact_name']+'</td>';
                        html+='<td>'+json['po_client'][i]['telephone']+'</td>';
                        html+='<td>'+json['po_client'][i]['email']+'</td>';
                        html+='<td>'+json['po_client'][i]['tax_id']+'</td>';
                        html+='<td>'+json['po_client'][i]['city']+'</td>';
                        html+='<td>'+json['po_client'][i]['state']+'</td>';
                        html+='<td>'+json['po_client'][i]['no_of_orders']+'</td>';
                        html+='<td>$'+json['po_client'][i]['total']+'</td>';
                        html+='<td>'+json['po_client'][i]['view_profile']+'</td>';
                        html+='</tr>';
                     }
                    
                 } else {
                        for (var i = 0; i < json['po_client'].length; i++) {
                        html+='<tr>';
                        html+='<td>'+json['po_client'][i]['company_name']+'</td>';
                        html+='<td>'+json['po_client'][i]['contact_name']+'</td>';
                        html+='<td>'+json['po_client'][i]['telephone']+'</td>';
                        html+='<td>'+json['po_client'][i]['email']+'</td>';
                        html+='<td>'+json['po_client'][i]['tax_id']+'</td>';
                        html+='<td>'+json['po_client'][i]['city']+'</td>';
                        html+='<td>'+json['po_client'][i]['state']+'</td>';
                        html+='<td>'+json['po_client'][i]['no_of_orders']+'</td>';
                        html+='<td>'+json['po_client'][i]['view_profile']+'</td>';
                        html+='</tr>';
                     }

                    }
                    $('#po_clients').html(html);

                } else {
                    var html = '';
                    html+='<tr>';
                    html+='<td align="center" colspan="9"><b> No PO Clients Found</b></td>';
                    html+='</tr>';
                    $('#po_clients').html(html);


                }


            }
        });

    }
    
</script>
    <title>Customers</title>
</head>
<body>
    <?php include_once 'inc/header.php'; ?>

    <?php if (@$_SESSION['message']): ?>
        <div align="center"><br />
            <font color="red"><?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?><br /></font>
            </div>
        <?php endif; ?>

        <table width="96%">             <tr><td style="vertical-align: top;"
align="center">             <br><br>             <font style="font-size:
x-large;">Search Filters</font><form style="width:260px !important;" name="order" action="" method="get">
            
                <table width="90%" cellpadding="3" cellspacing="3" border="0"   align="center">
                    <tr>
                    <tr>
    <td>Keyword<br><input type="text" style="width:100%" name="keyword" value="<?php echo @$_REQUEST['keyword']; ?>" placeholder="Email, First Name, Last Name or Company" /></td></tr>
    <!-- <td>Last Name</td>
    <td>
    <input type="text" name="lastname" value="<?php echo @$_REQUEST['lastname']; ?>" />
    </td> -->
    <tr><td>Phone<br><input type="text" style="width:100%" name="phone" value="<?php echo @$_REQUEST['phone']; ?>" /></td></tr>
    
    <!-- <td>Address 1</td>
    <td>
    <input type="text" name="address_1" value="<?php echo @$_REQUEST['address_1']; ?>" />
    </td>
    <td>Address 2</td>
    <td>
    <input type="text" name="address_2" value="<?php echo @$_REQUEST['address_2']; ?>" />
    </td> -->
    <tr>
    <td>Address<br><input type="text" style="width:100%" name="address_all" value="<?php echo @$_REQUEST['address_all']; ?>" /></td>
</tr>
                    <tr>

                        <td style="display:none">Order ID</td>
                        <td style="display:none">
                            <input type="text" style="width:70px" name="order_id" value="<?php echo @$_REQUEST['order_id']; ?>" />
                        </td>
                        <td>City<br><input type="text" style="width:100%" name="filter_city" value="<?php echo @$_REQUEST['filter_city']; ?>" /></td>
                        </tr>
                        <tr><td>State<br><select style="width:100%" name="filter_state">
                        <option value="">Please Select</option>
                        <?php
                        $zones = $db->func_query("SELECT * FROM oc_zone WHERE country_id in (223,38) order by name asc");
                        foreach($zones as $zone)
                        {
                            ?>
                            <option value="<?php echo $zone['zone_id'];?>" <?php echo ($_REQUEST['filter_state']==$zone['zone_id']?'selected':'');?>><?php echo $zone['name'];?></option>
                            <?php
                        }
                        ?>

                        </select></td></tr>
                            <tr>
                                <td># Of Orders<br>
                                <select name="filter_order_select" style="margin-bottom:5px;width: 100%" onChange="if ($(this).val() == 'BETWEEN') {
                            $('input[name=filter_order_range2]').show();
                        } else {
                        $('input[name=filter_order_range2]').hide();
                    }">

                    <option value=">" <?php if ($_GET['filter_order_select'] == ">") echo 'selected'; ?>>Above</option>
                    <option value="<" <?php if ($_GET['filter_order_select'] == "<") echo 'selected'; ?>>Below</option>
                    <option value="BETWEEN" <?php if ($_GET['filter_order_select'] == "BETWEEN") echo 'selected'; ?>>Between</option>
                </select>
                <br />
                <input type="text" style="width:100%" name="filter_order_range1"  value="<?php echo @$_GET['filter_order_range1']; ?>">
                <input type="text" style="width:100%;<?php
                if ($_GET['filter_order_select'] != 'BETWEEN') {
                    echo 'display:none';
                }
                ?>" name="filter_order_range2"  value="<?php echo @$_GET['filter_order_range2']; ?>" >
                </td>
                            </tr>
                 <tr>
                     <td>Total Amount<br>
                     <select name="filter_total_select" style="margin-bottom:5px;width: 100%" onChange="if ($(this).val() == 'BETWEEN') {
                $('input[name=filter_total_range2]').show();
            } else {
            $('input[name=filter_total_range2]').hide();
        }">

        <option value=">" <?php if ($_GET['filter_total_select'] == ">") echo 'selected'; ?>>Above</option>
        <option value="<" <?php if ($_GET['filter_total_select'] == "<") echo 'selected'; ?>>Below</option>
        <option value="BETWEEN" <?php if ($_GET['filter_total_select'] == "BETWEEN") echo 'selected'; ?>>Between</option>
    </select><br />
    <input type="text" style="width:100%" name="filter_total_range1"  value="<?php echo @$_GET['filter_total_range1']; ?>">
    <input type="text" style="width:100%;<?php
    if ($_GET['filter_total_select'] != 'BETWEEN') {
        echo 'display:none';
    }
    ?>" name="filter_total_range2"  value="<?php echo @$_GET['filter_total_range2']; ?>" >
                     </td>
                 </tr>           
<!-- <td>Email</td>
    <td>
    <input type="text" name="email" value="<?php echo @$_REQUEST['email']; ?>" />
    </td> -->







</tr>   
</table><br>
<input type="submit" name="search" style="width:130px" value="Search" class="button" />  &nbsp&nbsp&nbsp&nbsp

<?php
if($_SESSION['login_as']=='admin')
{
?>
<input type="button" name="export_csv" value="Export CSV"  class="button button-danger" onClick="window.location='customers.php?action=export_csv&<?php echo http_build_query($_GET);  ?>'" />

<?php
}
?>
</form></td>
    <td style="vertical-align: top;">
    <?php
    if($_SESSION['login_as']=='admin' || $_SESSION['update_sales_agent'])
    {
    ?>
    <div style="float:right"><a class="button fancybox fancybox.iframe button-info" href="<?php echo $host_path;?>popupfiles/update_sales_agent.php">Upload CSV to Assign Agent</a></div>
    <?php
}
?>
    <table  cellpadding="0" class="xtable" cellspacing="0" style="border:1px solid #ddd;width:1000px ;clear:both">
<thead>
    <tr >
        <th>S.N.</th>
        <th>Account</th>
        
        
        <th>Telephone</th>
        <!-- <th><a href="customers.php?sort=city&dir=<?php echo $dir; ?>&page=<?php echo $page; ?>&<?php echo $parameters2; ?>">City</a></th>
        <th><a href="customers.php?sort=zone_name&dir=<?php echo $dir; ?>&page=<?php echo $page; ?>&<?php echo $parameters2; ?>">State</a></th> -->

        <th>Group</th>
        <th><a href="customers.php?sort=no_of_orders&dir=<?php echo $dir; ?>&page=<?php echo $page; ?>&<?php echo $parameters2; ?>"># Of Orders</a> 
            <?php
            if ($sort == 'no_of_orders') {
                if ($dir == 'desc') {
                    echo '&uarr;';
                } else {
                    echo '&darr;';
                }
            }
            ?>
        </a></th>
        <?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
        <th>Total Amount</th>
        <th>Potential</th>
        <?php } ?>
        <th><a href="customers.php?sort=last_order&dir=<?php echo $dir; ?>&page=<?php echo $page; ?>&<?php echo $parameters2; ?>">Last Order</a> 
            <?php
            if ($sort == 'last_order') {
                if ($dir == 'desc') {
                    echo '&uarr;';
                } else {
                    echo '&darr;';
                }
            }
            ?>
        </a></th>
      <!--   <th><a href="customers.php?sort=date_added&dir=<?php echo $dir; ?>&page=<?php echo $page; ?>&<?php echo $parameters2; ?>">Creation Date</a> 
            <?php
            if ($sort == 'date_added') {
                if ($dir == 'desc') {
                    echo '&uarr;';
                } else {
                    echo '&darr;';
                }
            }
            ?>
        </a></th> -->
        <th>Action</th>
        <!-- <th>Action</th> -->
    </tr>

    </thead>

    <?php if ($customers): ?>
        <?php foreach ($customers as $i => $customer): ?>
            <tr>
                <td>

                <?php echo $i + 1; ?></td>
                <td><?php echo $customer['firstname']. ' '.$customer['lastname']; ?>

                <?php
                if($customer['company'])
                {
                    ?>
                    <br><?php echo $customer['company'];?>
                    <?php
                }
                ?>
                <br><?php echo linkToProfile($customer['email']); ?><br>
                <?php echo $customer['city'] ?>,
                <?php echo $customer['zone_name'] ?></td>

                
                <td><?php echo $customer['telephone'] ?></td>
                <!-- <td><?php echo $customer['city'] ?></td>
                <td><?php echo $customer['zone_name'] ?></td> -->

                <td><?php echo $customer['customer_group'] ?></td>
                <td><?php echo $customer['no_of_orders'] ?></td>
                <?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
                <td>$<?php echo number_format($customer['total_amount'], 2); ?></td>
                <td>$<?php echo number_format($customer['account_potential'], 2); ?></td>
                <?php } ?>
                <td><?php echo americanDate($customer['last_order'],false); ?></td>
                <!-- <td><?php echo americanDate($customer['date_added']); ?></td> -->
                <td>
                <?php
                if($customer['customer_id']>0)
                {
                ?>
                <input type="button" value="Login" class="button" onclick="customerOCLogin('<?=$customer['customer_id'];?>','<?=md5($customer['email']);?>')">
                <?php
            }
            ?>
                </td>
               <!--  <td>
                    <a href="<?php echo $host_path; ?>customer_profile.php?id=<?php echo base64_encode($customer['email']); ?>">View Profile</a>
                </td> -->
            </tr>
        <?php endforeach; ?>       
    <?php endif; ?>

    <tr>
        <td colspan="4" align="left">
            <?php echo $splitPage->display_count("Displaying %s to %s of (%s)"); ?>
        </td>

        <td colspan="7" align="right">
            <?php echo $splitPage->display_links(10, $parameters); ?>
        </td>
    </tr>
</table></td>
</td></tr>
        </table>
            

<?php
$where = array();
$parameters = array();
$parameters2 = array();
if ($order_id) {
    $where[] = " customer_id in (select customer_id from oc_order where order_id  = '$order_id' ) ";
    $parameters[] = "order_id=$order_id";
    $parameters2[] = "order_id=$order_id";
}

if ($filter_city) {
    $where[] = " LOWER(city) LIKE '%" . strtolower($filter_city) . "%' ";
    $parameters[] = "filter_city=$filter_city";
    $parameters2[] = "filter_city=$filter_city";
}

if ($firstname) {
    $where[] = " LOWER(firstname) LIKE '%" . strtolower($firstname) . "%' ";
    $parameters[] = "firstname=$firstname";
    $parameters2[] = "firstname=$firstname";
}
if ($lastname) {
    $where[] = " LOWER(lastname) LIKE '%" . strtolower($lastname) . "%' ";
    $parameters[] = "lastname=$lastname";
    $parameters2[] = "lastname=$lastname";
}
if ($email) {
    $where[] = " LOWER(email) LIKE '%" . strtolower($email) . "%' ";
    $parameters[] = "email=$email";
    $parameters2[] = "email=$email";
}
if ($phone) {
    $where[] = " telephone LIKE '%" . strtolower($phone) . "%' ";
    $parameters[] = "phone=$phone";
    $parameters2[] = "phone=$phone";
}
if ($address_1) {
    $where[] = " LOWER(address1) LIKE '%" . strtolower($address_1) . "%' ";
    $parameters[] = "address_1=$address_1";
    $parameters2[] = "address_1=$address_1";
}
if ($address_2) {
    $where[] = " LOWER(address2) LIKE '%" . strtolower($address_2) . "%' ";
    $parameters[] = "address_2=$address_2";
    $parameters2[] = "address_2=$address_2";
}
if ($address_all) {
    $where[] = " LOWER(address2) LIKE '%" . strtolower($address_all) . "%' OR LOWER(address1) LIKE '%" . strtolower($address_all) . "%' ";
    $parameters[] = "address_all=$address_all";
    $parameters2[] = "address_all=$address_all";
}

if ($filter_state) {
    $where[] = " LOWER(state) LIKE '%" . strtolower($filter_state) . "%' ";
    $parameters[] = "filter_state=$filter_state";
    $parameters2[] = "filter_state=$filter_state";
}

// if ($keyword) {
//     $where[] = " (lower(contact_name) like '%" . strtolower($keyword) . "%' OR lower(company_name) like '%" . strtolower($keyword) . "%' OR lower(email) like '%" . strtolower($keyword) . "%' OR lower(firstname) like '%" . strtolower($keyword) . "%' OR lower(lastname) like '%" . strtolower($keyword) . "%') ";
//     $parameters[] = "keyword=$keyword";
//     $parameters2[] = "keyword=$keyword";
// }

if ($where) {
    $where = implode(" AND ", $where);
} else {
    $where = "1 = 1";
}
$where_po = base64_encode($where);
//print_r($where_po);exit;

/*$users = $cache->get('po_customers.'.$_cache);

if (!$users) {
     $users = $db->func_query("Select * from inv_po_customers where $where order by id DESC");
    $cache->set('po_customers.'.$_cache,$users);
    
}*/
?>
<br>
<br>
<div align="center" style="max-height:800px;overflow:scroll; width:80%; margin: 0px auto; display: none;">
    <h2>PO Clients</h2>
    <br>
    <table  border="1" width="100%;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
        <thead>
        <tr style="background-color:#e7e7e7;">
            <td>Company Name</td>
            <td>Contact Name</td>
            <td>Telephone</td>
            <td>Email</td>
            <td>Tax ID</td>
            <td>City</td>
            <td>State</td>
            <td># of Orders</td>
            <?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
            <td>Total Amount</td>
            <?php } ?>
            <td colspan="1" align="center">Action</td>
        </tr>
        <input type="hidden" id="where_po" value="<?php echo $where_po; ?>">
        <input type="hidden" id="fos" value="<?php echo $filter_order_select; ?>">
        <input type="hidden" id="for1" value="<?php echo $filter_order_range1; ?>">
        <input type="hidden" id="for2" value="<?php echo $filter_order_range2; ?>">
        <input type="hidden" id="fts" value="<?php echo $filter_total_select; ?>">
        <input type="hidden" id="ftr1" value="<?php echo $filter_total_range1; ?>">
        <input type="hidden" id="ftr2" value="<?php echo $filter_total_range2; ?>">
        </thead>
        <div align="center" id="po_loader" style="width: 98%;display: none;">
         <img src="images/loading.gif" style="max-width: 100px;">
       </div>
        <tbody id="po_clients">
       <!--  <?php foreach ($users as $user): ?>
            <?php $orders = $db->func_query('select `order_id` from `inv_orders` where `po_business_id` = "' . $user['id'] . '"') ?>
            <?php $no_of_orders = count($orders); ?>
            <?php
            $order_ids = array();
            foreach ($orders as $order) {
                $order_ids[] = $order['order_id'];
            }
            ?>
            <?php $total = $db->func_query_first_cell('select sum(`product_price`) as `total` from `inv_orders_items` where `order_id` in ("' . implode('","', $order_ids) . '")') ?>
            <?php
            if ($filter_order_range1) {
                if ($filter_order_select == 'BETWEEN') {
                    if ($no_of_orders < $filter_order_range1 or $no_of_orders > $filter_order_range2) {
                        continue;
                    }
                }
                if ($filter_order_select == '>') {
                    if ($no_of_orders < $filter_order_range1) {
                        continue;
                    }
                }
                if ($filter_order_select == '<') {
                    if ($no_of_orders > $filter_order_range1) {
                        continue;
                    }
                }
            }

            if ($filter_total_range1) {
                if ($filter_total_select == 'BETWEEN') {
                    if ($total < $filter_total_range1 or $total > $filter_total_range2) {
                        continue;
                    }
                }
                if ($filter_total_select == '>') {
                    if ($total < $filter_total_range1) {
                        continue;
                    }
                }
                if ($filter_total_select == '<') {
                    if ($total > $filter_total_range1) {
                        continue;
                    }
                }
            }
            ?>
            <tr>
                <td><?php echo $user['company_name']; ?></td>

                <td><?php echo $user['contact_name']; ?></td>

                <td><?php echo $user['telephone']; ?></td>

                <td><?php echo linkToProfile($user['email']); ?></td>

                <td><?php echo $user['tax_id']; ?></td>

                <td><?php echo $user['city']; ?></td>

                <td><?php echo $user['state']; ?></td>

                <td><?php echo $no_of_orders; ?></td>
                <?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
                <td>$<?php echo ($total) ? $total : '0.00'; ?></td>
                <?php } ?>

                <td><a href="<?php echo $host_path; ?>customer_profile.php?id=<?php echo base64_encode($user['email']); ?>">View Profile</a></td>
            </tr>
        <?php endforeach; ?> -->
        </tbody>
    </table>
</div>
<?php
$where = array();
$parameters = array();
$parameters2 = array();
if ($order_id) {
    $where[] = " customer_id in (select customer_id from oc_order where order_id  = '$order_id' ) ";
    $parameters[] = "order_id=$order_id";
    $parameters2[] = "order_id=$order_id";
}

if ($filter_city) {
    $where[] = " LOWER(city) LIKE '%" . strtolower($filter_city) . "%' ";
    $parameters[] = "filter_city=$filter_city";
    $parameters2[] = "filter_city=$filter_city";
}
if ($firstname) {
    $where[] = " LOWER(firstname) LIKE '%" . strtolower($firstname) . "%' ";
    $parameters[] = "firstname=$firstname";
    $parameters2[] = "firstname=$firstname";
}
if ($lastname) {
    $where[] = " LOWER(lastname) LIKE '%" . strtolower($lastname) . "%' ";
    $parameters[] = "lastname=$lastname";
    $parameters2[] = "lastname=$lastname";
}
if ($email) {
    $where[] = " LOWER(email) LIKE '%" . strtolower($email) . "%' ";
    $parameters[] = "email=$email";
    $parameters2[] = "email=$email";
}
if ($phone) {
    $where[] = " telephone LIKE '%" . strtolower($phone) . "%' ";
    $parameters[] = "phone=$phone";
    $parameters2[] = "phone=$phone";
}
if ($address_1) {
    $where[] = " LOWER(address_1) LIKE '%" . strtolower($address_1) . "%' ";
    $parameters[] = "address_1=$address_1";
    $parameters2[] = "address_1=$address_1";
}
if ($address_all) {
    $where[] = " LOWER(address_1) LIKE '%" . strtolower($address_all) . "%' ";
    $parameters[] = "address_all=$address_all";
    $parameters2[] = "address_all=$address_all";
}

// if ($keyword) {
//     $where[] = " lower(email) like '%" . strtolower($keyword) . "%' OR lower(firstname) like '%" . strtolower($keyword) . "%' OR lower(lastname) like lower('%" . strtolower($keyword) . "%') ";
//     $parameters[] = "keyword=$keyword";
//     $parameters2[] = "keyword=$keyword";
// }

if ($where) {
    $where = implode(" AND ", $where);
} else {
    $where = "1 = 1";
}
$where_lbb = base64_encode($where);

//$lbbusers = $cache->get('lbb_customers.'.$_cache);
//if (!$lbbusers) {
   
  //   $lbbusers = $db->func_query("SELECT *, COUNT(email) as totallbb, MAX(date_added) as lastlbb, SUM(total) as totalamount from oc_buyback where address_id = '-1' AND $where group by email order by buyback_id DESC");
   // $cache->set('lbb_customers.'.$_cache,$lbbusers);
    
//}
//print_r($lbbusers); exit;
?>
<br>
<br>
<div style="max-height:800px;overflow:scroll; width:80%; margin: 0px auto;display: none;">
    <h2>LBB Clients</h2>
    <br>
    <table border="1" width="100%;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
    <thead>
        <tr style="background-color:#e7e7e7;">
            <td>Contact Name</td>
            <td>Telephone</td>
            <td>Email</td>
            <td>City</td>
            <td>State</td>
            <td># of LBB</td>
            <td>Last LBB</td>
            <?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
            <td>Total Amount</td>
            <?php } ?>
            <td colspan="1" align="center">Action</td>
        </tr>
        <input type="hidden" id="where_lbb" value="<?php echo $where_lbb; ?>">
    </thead>
        <div align="center" id="lbb_loader" style="width: 98%;display: none;">
         <img src="images/loading.gif" style="max-width: 100px;">
       </div>
    <tbody id="lbb_clients">
    
        <!-- <?php foreach ($lbbusers as $lbbuser) { ?>
        <?php
        $ocuser = $db->func_query_first_cell("SELECT email FROM oc_customer WHERE LCASE(email) = LCASE('". $lbbuser['email'] ."')");
        $pouser = $db->func_query_first_cell("SELECT email FROM inv_po_customers WHERE LCASE(email) = LCASE('". $lbbuser['email'] ."')");
        $invuser = $db->func_query_first_cell("SELECT email FROM inv_customers WHERE LCASE(email) = LCASE('". $lbbuser['email'] ."')");
        if (!empty($ocuser) || !empty($pouser) || !empty($invuser)) {
            continue;
        }
        ?>
        <tr>
            <td><?php echo $lbbuser['firstname'] . ' ' . $lbbuser['lastname']; ?></td>

            <td><?php echo $lbbuser['telephone']; ?></td>

            <td><?php echo linkToProfile($lbbuser['email']); ?></td>

            <td><?php echo $lbbuser['city']; ?></td>

            <td><?php echo $db->func_query_first_cell('SELECT name FROM oc_zone WHERE zone_id = "' . $lbbuser['zone_id'] . '"'); ?></td>

            <td><?php echo $lbbuser['totallbb']; ?></td>

            <td><?php echo americanDate($lbbuser['lastlbb']); ?></td>
            <?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
            <td>$<?php echo number_format($lbbuser['totalamount'], 2); ?></td>
            <?php } ?>
            <td><a href="<?php echo $host_path; ?>customer_profile.php?id=<?php echo base64_encode($lbbuser['email']); ?>">View Profile</a></td>
        </tr>

        <?php } ?> -->
    </tbody>
</table>

</div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
           //fetchLBBClients($('#where_lbb').val());
           //fetchPOClients($('#where_po').val());
        });
</script>
</body>
</html>
