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
$keyword = $db->func_escape_string(trim($_REQUEST['keyword']));
$filter_city = $db->func_escape_string(trim($_REQUEST['filter_city']));
$filter_state = $db->func_escape_string(trim($_REQUEST['filter_state']));
$filter_zip = $db->func_escape_string(trim($_REQUEST['filter_zip']));

$filter_order_select = $db->func_escape_string($_REQUEST['filter_order_select']);
$filter_order_range1 = $db->func_escape_string(trim($_REQUEST['filter_order_range1']));
$filter_order_range2 = $db->func_escape_string(trim($_REQUEST['filter_order_range2']));

$filter_total_select = $db->func_escape_string($_REQUEST['filter_total_select']);
$filter_total_range1 = $db->func_escape_string(trim($_REQUEST['filter_total_range1']));
$filter_total_range2 = $db->func_escape_string(trim($_REQUEST['filter_total_range2']));

$firstname = $db->func_escape_string(trim($_REQUEST['firstname']));
$lastname = $db->func_escape_string(trim($_REQUEST['lastname']));
$email = $db->func_escape_string(trim($_REQUEST['email']));
$phone = $db->func_escape_string(trim($_REQUEST['phone']));
$address_1 = $db->func_escape_string(trim($_REQUEST['address_1']));
$address_2 = $db->func_escape_string(trim($_REQUEST['address_2']));





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
if ($filter_state) {
    $where[] = " LOWER(state) LIKE '%" . strtolower($filter_state) . "%' ";
    $parameters[] = "filter_state=$filter_state";
    $parameters2[] = "filter_state=$filter_state";
}
if ($filter_zip) {
    $where[] = " zip LIKE '%" . strtolower($filter_zip) . "%' ";
    $parameters[] = "filter_zip=$filter_zip";
    $parameters2[] = "filter_zip=$filter_zip";
}
if ($keyword) {
    $where[] = " (lower(email) like '%" . strtolower($keyword) . "%' OR lower(firstname) like '%" . strtolower($keyword) . "%' OR lower(lastname) like '%" . $keyword . "%') ";
    $parameters[] = "keyword=$keyword";
    $parameters2[] = "keyword=$keyword";
}

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
if (!in_array($sort, array("no_of_orders", "last_order", 'date_added'))) {
    $sort = "no_of_orders";
} else {
    $sort = $_GET['sort'];
}


$dir = @$_GET['dir'];
if (!$dir || !in_array($dir, array("asc", "desc"))) {
    $dir = 'desc';
}

$inv_query = "SELECT * from inv_customers WHERE $where  ORDER BY $sort $dir";

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
$splitPage = new splitPageResults($db, $inv_query, $num_rows, "customers.php", $page);
$_cache = md5(http_build_query($_REQUEST));
$customers = $cache->get('customers.'.$page.'.'.$_cache);
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

        <div align="center">
            <form name="order" action="" method="get">
                <table width="90%" cellpadding="10" border="1"  align="center">
                    <tr>
                    <tr>
    <td>First Name</td>
    <td>
    <input type="text" name="firstname" value="<?php echo @$_REQUEST['firstname']; ?>" />
    </td>
    <td>Last Name</td>
    <td>
    <input type="text" name="lastname" value="<?php echo @$_REQUEST['lastname']; ?>" />
    </td>
    <td>Phone</td>
    <td>
    <input type="text" name="phone" value="<?php echo @$_REQUEST['phone']; ?>" />
    </td>
    <td>Address 1</td>
    <td>
    <input type="text" name="address_1" value="<?php echo @$_REQUEST['address_1']; ?>" />
    </td>
    <td>Address 2</td>
    <td>
    <input type="text" name="address_2" value="<?php echo @$_REQUEST['address_2']; ?>" />
    </td>
</tr>
                    <tr>

                        <td style="display:none">Order ID</td>
                        <td style="display:none">
                            <input type="text" style="width:70px" name="order_id" value="<?php echo @$_REQUEST['order_id']; ?>" />
                        </td>
                        <td>City</td>
                        <td>
                            <input type="text" style="width:100px" name="filter_city" value="<?php echo @$_REQUEST['filter_city']; ?>" />
                        </td>

                        <td>State</td>
                        <td>
                            <input style="width:100px" type="text" name="filter_state" value="<?php echo @$_REQUEST['filter_state']; ?>" />
                        </td>


                        <td># Of Orders</td>
                        <td align="center">
                            <select name="filter_order_select" style="margin-bottom:5px" onChange="if ($(this).val() == 'BETWEEN') {
                            $('input[name=filter_order_range2]').show();
                        } else {
                        $('input[name=filter_order_range2]').hide();
                    }">

                    <option value=">" <?php if ($_GET['filter_order_select'] == ">") echo 'selected'; ?>>Above</option>
                    <option value="<" <?php if ($_GET['filter_order_select'] == "<") echo 'selected'; ?>>Below</option>
                    <option value="BETWEEN" <?php if ($_GET['filter_order_select'] == "BETWEEN") echo 'selected'; ?>>Between</option>
                </select><br />
                <input type="text" style="width:80px" name="filter_order_range1"  value="<?php echo @$_GET['filter_order_range1']; ?>">
                <input type="text" style="width:80px;<?php
                if ($_GET['filter_order_select'] != 'BETWEEN') {
                    echo 'display:none';
                }
                ?>" name="filter_order_range2"  value="<?php echo @$_GET['filter_order_range2']; ?>" >


            </td>

            <td>Total Amount</td>
            <td align="center">
                <select name="filter_total_select" style="margin-bottom:5px" onChange="if ($(this).val() == 'BETWEEN') {
                $('input[name=filter_total_range2]').show();
            } else {
            $('input[name=filter_total_range2]').hide();
        }">

        <option value=">" <?php if ($_GET['filter_total_select'] == ">") echo 'selected'; ?>>Above</option>
        <option value="<" <?php if ($_GET['filter_total_select'] == "<") echo 'selected'; ?>>Below</option>
        <option value="BETWEEN" <?php if ($_GET['filter_total_select'] == "BETWEEN") echo 'selected'; ?>>Between</option>
    </select><br />
    <input type="text" style="width:80px" name="filter_total_range1"  value="<?php echo @$_GET['filter_total_range1']; ?>">
    <input type="text" style="width:80px;<?php
    if ($_GET['filter_total_select'] != 'BETWEEN') {
        echo 'display:none';
    }
    ?>" name="filter_total_range2"  value="<?php echo @$_GET['filter_total_range2']; ?>" >


</td>
<td>Email</td>
    <td>
    <input type="text" name="email" value="<?php echo @$_REQUEST['email']; ?>" />
    </td>
</tr>






</tr>   
</table><br>
<input type="submit" name="search" value="Search" class="button" />
</form>

<table border="1" cellpadding="5" cellspacing="0" width="80%">
    <tr style="background:#e5e5e5;">
        <th>S.N.</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>City</th>
        <th>State</th>

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
        <th><a href="customers.php?sort=date_added&dir=<?php echo $dir; ?>&page=<?php echo $page; ?>&<?php echo $parameters2; ?>">Creation Date</a> 
            <?php
            if ($sort == 'date_added') {
                if ($dir == 'desc') {
                    echo '&uarr;';
                } else {
                    echo '&darr;';
                }
            }
            ?>
        </a></th>
        <th>Action</th>
        <!-- <th>Action</th> -->
    </tr>
    <?php if ($customers): ?>
        <?php foreach ($customers as $i => $customer): ?>
            <tr>
                <td><?php echo $i + 1; ?></td>
                <td><?php echo $customer['firstname'] ?></td>
                <td><?php echo $customer['lastname'] ?></td>
                <td><?php echo linkToProfile($customer['email']) ?></td>
                <td><?php echo $customer['city'] ?></td>
                <td><?php echo $customer['state'] ?></td>

                <td><?php echo $customer['customer_group'] ?></td>
                <td><?php echo $customer['no_of_orders'] ?></td>
                <?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
                <td>$<?php echo number_format($customer['total_amount'], 2); ?></td>
                <?php } ?>
                <td><?php echo americanDate($customer['last_order']); ?></td>
                <td><?php echo americanDate($customer['date_added']); ?></td>
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
    $where[] = " LOWER(address1) LIKE '%" . strtolower($address_1) . "%' ";
    $parameters[] = "address_1=$address_1";
    $parameters2[] = "address_1=$address_1";
}
if ($address_2) {
    $where[] = " LOWER(address2) LIKE '%" . strtolower($address_2) . "%' ";
    $parameters[] = "address_2=$address_2";
    $parameters2[] = "address_2=$address_2";
}

if ($filter_state) {
    $where[] = " LOWER(state) LIKE '%" . strtolower($filter_state) . "%' ";
    $parameters[] = "filter_state=$filter_state";
    $parameters2[] = "filter_state=$filter_state";
}

if ($keyword) {
    $where[] = " (lower(contact_name) like '%" . strtolower($keyword) . "%' OR lower(company_name) like '%" . strtolower($keyword) . "%' OR lower(email) like '%" . strtolower($keyword) . "%' OR lower(firstname) like '%" . strtolower($keyword) . "%' OR lower(lastname) like '%" . strtolower($keyword) . "%') ";
    $parameters[] = "keyword=$keyword";
    $parameters2[] = "keyword=$keyword";
}

if ($where) {
    $where = implode(" AND ", $where);
} else {
    $where = "1 = 1";
}

$users = $cache->get('po_customers.'.$_cache);

if (!$users) {
     $users = $db->func_query("Select * from inv_po_customers where $where order by id DESC");
    $cache->set('po_customers.'.$_cache,$users);
    
}
?>
<br>
<br>
<div align="center" style="max-height:800px;overflow:scroll; width:80%; margin: 0px auto;">
    <h2>PO Clients</h2>
    <br>
    <table border="1" width="100%;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
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

        <?php foreach ($users as $user): ?>
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
        <?php endforeach; ?>
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

if ($keyword) {
    $where[] = " lower(email) like '%" . strtolower($keyword) . "%' OR lower(firstname) like '%" . strtolower($keyword) . "%' OR lower(lastname) like lower('%" . strtolower($keyword) . "%') ";
    $parameters[] = "keyword=$keyword";
    $parameters2[] = "keyword=$keyword";
}

if ($where) {
    $where = implode(" AND ", $where);
} else {
    $where = "1 = 1";
}

$lbbusers = $cache->get('lbb_customers.'.$_cache);
if (!$lbbusers) {
   
     $lbbusers = $db->func_query("SELECT *, COUNT(email) as totallbb, MAX(date_added) as lastlbb, SUM(total) as totalamount from oc_buyback where address_id = '-1' AND $where group by email order by buyback_id DESC");
    $cache->set('lbb_customers.'.$_cache,$lbbusers);
    
}
//print_r($lbbusers); exit;
?>
<br>
<br>
<div style="max-height:800px;overflow:scroll; width:80%; margin: 0px auto;">
    <h2>LBB Clients</h2>
    <br>
    <table border="1" width="100%;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
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

        <?php foreach ($lbbusers as $lbbuser) { ?>
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

        <?php } ?>
    </table>
</div>
</div>
</body>
</html>
