<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

if ((int) $_GET['return_id'] and $_GET['action'] == 'delete' && $_SESSION['delete_shipment']) {
    $return_id = (int) $_GET['return_id'];
    $db->db_exec("delete from inv_returns where id = '$return_id'");
    $db->db_exec("delete from inv_return_comments where return_id = '$return_id'");
    $db->db_exec("delete from inv_return_items where return_id = '$return_id'");
    $log = 'RMA Deleted ' . $return_id;
    actionLog($log);
    $_SESSION['message'] = "RMA is deleted";
    header("Location:manage_returns.php");
    exit;
}

if ((int) $_GET['return_id'] and $_GET['action'] == 'complete') {
    $return_id = (int) $_GET['return_id'];
    $db->db_exec("update inv_returns SET rma_status = 'Completed' , date_completed = '" . date('Y-m-d H:i:s') . "' where id = '$return_id'");

    $log = 'RMA completed ' . linkToRma($return_id);
    actionLog($log);
    $_SESSION['message'] = "RMA is completed now.";
    header("Location:manage_returns.php");
    exit;
}

$page = (int) $_GET['page'];
if (!$page) {
    $page = 1;
}

$where = array();
if ($_GET['rma_number']) {
    $rma_number = $db->func_escape_string(trim($_GET['rma_number']));
    $where[] = " LCASE(a.rma_number) LIKE '%".strtolower($rma_number)."%' ";
    $parameters[] = "rma_number=$rma_number";
    $parameters2[] = "rma_number=$rma_number";
}

if ($_GET['order_id']) {
    $order_id = $db->func_escape_string(trim($_GET['order_id']));
    $where[] = " a.order_id like '%$order_id%' ";
    $parameters[] = "order_id=$order_id";
    $parameters2[] = "order_id=$order_id";
}

if ($_GET['rma_status']) {
    $status = $db->func_escape_string($_GET['rma_status']);
    $where[] = " a.rma_status = '$status' ";
    $parameters[] = "rma_status=$status";
    $parameters2[] = "rma_status=$status";
}

if ($_GET['source']) {
    $source = $db->func_escape_string($_GET['source']);
    $where[] = " a.source = '$source' ";
    $parameters[] = "source=$source";
    $parameters2[] = "source=$source";
}
if ($_GET['email']) {
    $email = $db->func_escape_string(trim($_GET['email']));
    $where[] = " a.email like '%" . strtolower($email) . "%' ";
    $parameters[] = "email=$email";
    $parameters2[] = "email=$email";
}

if ($_GET['date_added']) {
    $date_added = $db->func_escape_string($_GET['date_added']);
    $date_added = date('Y-m-d', strtotime($date_added));
    $where[] = " b.date_added like '%$date_added%' ";
    $parameters[] = "date_added=$date_added";
    $parameters2[] = "date_added=$date_added";
}

if ($where) {
    $where = implode(" AND ", $where);
} else {
    $where = ' 1 = 1';
}

$sort = $_GET['sort'];
if (!in_array($sort, array("date_added", "date_qc","date_completed"))) {
    $sort = "a.date_added";
}
if ($sort == 'date_qc') {
    $sort = 'a.date_qc';
}
if ($sort == 'date_added') {
    $sort = 'b.date_added';
}
if ($sort == 'date_completed') {
    $sort = 'a.date_completed';
}

$dir = @$_GET['dir'];
if (!$dir || !in_array($dir, array("asc", "desc"))) {
    $dir = 'desc';
}
$parameters[] = "sort=" . $_GET['sort'];
$parameters[] = "dir=" . $dir;
$_query = "SELECT distinct a.*,b.date_added as date_received FROM inv_returns a LEFT JOIN inv_return_history b ON (a.`rma_number`=b.`rma_number` AND b.return_status='Received') where $where group by a.rma_number order by $sort $dir ";
// echo $_query;
$splitPage = new splitPageResults($db, $_query, 25, "manage_returns.php", $page);
$rma_returns = $db->func_query($splitPage->sql_query);
if (isset($_GET['agent_dashboard'])) {
    $json = array();
    $return = $db->func_query("SELECT distinct a.*,b.date_added as date_received FROM inv_returns a LEFT JOIN inv_return_history b ON (a.`rma_number`=b.`rma_number` AND b.return_status='Received') where $where group by a.rma_number order by a.date_added desc limit 50  ");
    if ($return) {
        $json['success'] = 1;
        $json['return'] = $return;
        foreach ($return as $key => $return) {

            $return_items = $db->func_query("select sku , quantity , price , decision from inv_return_items where return_id = '" . $return['id'] . "' AND removed <> '1'");
            $amount = 0.00;
            $_temp = '';
            foreach ($return_items as $item) {
                 $_temp.=  linkToProduct($item['sku'], $host_path,'target="_blank"').' / '.$item['decision'].'<br>';
                $amount = $amount + $item['price'];      
            }
            $json['return'][$key]['extra_details'] = $_temp;
            if ($return['ppusa']) {
                $json['return'][$key]['ppusa'] = 'YES';    
            } else {
                $json['return'][$key]['ppusa'] = 'NO';   
            }
            $json['return'][$key]['amount'] = number_format($amount,2);
            $json['return'][$key]['email'] = linkToProfile($return['email'],'','','_blank');
            $json['return'][$key]['rma_number'] = '<a target="_blank" href="return_detail.php?rma_number='.$return['rma_number'].'">'.$return['rma_number'].'</a>';
            $json['return'][$key]['date_received'] = americanDate($return['date_received']);
            $json['return'][$key]['date_qc'] = americanDate($return['date_qc']);
            $json['return'][$key]['date_completed'] = americanDate($return['date_completed']);
            $json['return'][$key]['order_id'] = '<a target="_blank" href="viewOrderDetail.php?order='. $return['order_id'].'">'.$return['order_id'].'</a>';
        }
    } else {
        $json['error'] = 1;
    }
    echo json_encode($json);
    exit;
}

foreach ($rma_returns as $index => $rma_return) {
    $rma_returns[$index]['extra_details'] = $db->func_query("select sku , quantity , price , decision from inv_return_items where return_id = '" . $rma_return['id'] . "' AND removed <> '1' ");
}

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

$dir = @$_GET['dir'];
if ($dir == 'desc') {
    $dir = 'asc';
} else {
    $dir = 'desc';
}

$sources[] = array("id" => "mail", "value" => "mail");
$sources[] = array("id" => "manual", "value" => "manual");
$sources[] = array("id" => "storefront", "value" => "storefront");

$RMA_STATUS[] = array("id" => "Awaiting", "value" => "Awaiting");
$RMA_STATUS[] = array("id" => "Received", "value" => "Received");
$RMA_STATUS[] = array("id" => "In QC", "value" => "QC Completed");
$RMA_STATUS[] = array("id" => "Completed", "value" => "Completed");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Manage rma returns</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
            <script src="//code.jquery.com/jquery-1.10.2.js"></script>
            <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

            <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
            <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

            <script type="text/javascript">
                $(document).ready(function () {
                    $('.fancybox').fancybox({width: '450px', autoCenter: true, autoSize: true});
                    $('.fancybox2').fancybox({width: '680px', autoCenter: true, autoSize: true});

                    $(".datepicker").datepicker();
                });
            </script>	
            <style type="text/css">
                .data td,.data th{
                    border: 1px solid #e8e8e8;
                    text-align:center;
                    width: 150px;
                }
                .div-fixed{
                    position:fixed;
                    top:0px;
                    left:8px;
                    background:#fff;
                    width:98.8%; 
                }
                .red td{ box-shadow:1px 2px 5px #990000;}
            </style>
    </head>
    <body>
        <div align="center"> 
            <?php include_once 'inc/header.php'; ?>
        </div>

        <?php if ($_SESSION['message']): ?>
            <div align="center"><br />
                <font color="red"><?php echo $_SESSION['message'];
        unset($_SESSION['message']);
            ?><br /></font>
            </div>
        <?php else: ?>
            <br /><br /> 
<?php endif; ?>

        <div align="center">
            <form action="" method="get">
                <table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                        <td>
                            Email: <?php echo createField("email", "email", "text", $_GET['email']); ?>                     
                        </td>

                        <td>
                            Date: <?php echo createField("date_added", "date_added", "text", $_GET['date_added'], null, " class='datepicker' "); ?>                     
                        </td>

                        <td>
                            RMA Number: <?php echo createField("rma_number", "rma_number", "text", $_GET['rma_number']); ?>				        
                        </td>

                        <td>
                            Order ID: <?php echo createField("order_id", "order_id", "text", $_GET['order_id']); ?>				        
                        </td>

                        <td>
                            Status: <?php echo createField("rma_status", "rma_status", "select", $_GET['rma_status'], $RMA_STATUS); ?>				        
                        </td>

                        <td>
                            Source: <?php echo createField("source", "source", "select", $_GET['rma_status'], $sources); ?>				        
                        </td>
                    </tr>	
                </table>

                <br />
                <input type="submit" name="search" value="Search" class="button" />
            </form>
        </div>			
        <br />

        <div>	
            <table class="data" border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="3">
                <tr style="background:#e5e5e5;">
                    <th style="width:50px;">#</th>
                    <th><a href="manage_returns.php?sort=date_added&dir=<?php echo $dir; ?>&page=<?php echo $page; ?>&<?php echo $parameters2; ?>">Received
                            <?php
                            if ($_GET['sort'] == 'date_added') {
                                if ($dir == 'desc') {
                                    echo '&uarr;';
                                } else {
                                    echo '&darr;';
                                }
                            }
                            ?></a></th>
                    <th><a href="manage_returns.php?sort=date_qc&dir=<?php echo $dir; ?>&page=<?php echo $page; ?>&<?php echo $parameters2; ?>">QC<?php
                            if ($_GET['sort'] == 'date_qc') {
                                if ($dir == 'desc') {
                                    echo '&uarr;';
                                } else {
                                    echo '&darr;';
                                }
                            }
                            ?>
                        </a></th>
                    <th><a href="manage_returns.php?sort=date_completed&dir=<?php echo $dir; ?>&page=<?php echo $page; ?>&<?php echo $parameters2; ?>">Completed<?php
                            if ($_GET['sort'] == 'date_completed') {
                                if ($dir == 'desc') {
                                    echo '&uarr;';
                                } else {
                                    echo '&darr;';
                                }
                            }
                            ?>
                        </a></th>
                    <th>RMA Number</th>
                    <th>PPUSA</th>
                    <th>Source</th>
                    <th>Email</th>
                    <th>Order ID</th>
                    <th>SKU / Decision</th>
                    <?php if($_SESSION['complete_return'] == '1' || $_SESSION['login_as'] == 'admin') { ?>
                    <th>Amount</th>
                    <?php } ?>
                    <th>Status</th>
                    <th style="width:300px;">Action</th>
                </tr>
<?php foreach ($rma_returns as $k => $rma_return): ?>
                <?php 
                //echo '<pre>';                print_r($rma_return); exit;
                ?>
                    <tr>
                        <td style="width:50px;"><?php echo $k + 1; ?></td>			

                        <td><?php echo americanDate($rma_return['date_received']); ?></td>

                        <td><?php echo ($rma_return['date_qc']) ? americanDate($rma_return['date_qc']): ''; ?></td>
                        <td><?php echo ($rma_return['rma_status'] == 'Completed') ? americanDate($rma_return['date_completed']): ''; ?></td>

                        <td>
                            <a href="return_detail.php?rma_number=<?php echo $rma_return['rma_number']; ?>">
    <?php echo $rma_return['rma_number']; ?>
                            </a>
                        </td>
                        <td><?php echo ($rma_return['ppusa']) ? 'YES' : 'NO';?></td>
                        <td><?php echo $rma_return['source']; ?></td>

                        <td style="width:150px;word-wrap:break-word;float:left;border:0px 1px;"><?= linkToProfile($rma_return['email'], $host_path); ?></td>

                        <td><a href="viewOrderDetail.php?order=<?php echo $rma_return['order_id']; ?>"><?php echo $rma_return['order_id']; ?></a></td>

                        <td>
                            <?php $amount = 0;
                            foreach ($rma_return['extra_details'] as $item):
                                ?>
                                <?php echo linkToProduct($item['sku'], $host_path); ?> / <?php echo $item['decision'] ?><br />

        <?php $amount = $amount + $item['price']; ?>
    <?php endforeach; ?>
                        </td>
                        <?php if($_SESSION['complete_return'] == '1' || $_SESSION['login_as'] == 'admin') { ?>
                        <td>$<?php echo $amount; ?></td>
                        <?php }?>

                        <td><?php echo ($rma_return['rma_status'] == 'In QC') ? 'QC Completed' : $rma_return['rma_status']; ?></td> 

                        <td style="width:300px;">
                            <?php if ($rma_return['rma_status'] != 'Completed' || $_SESSION['login_as'] == 'admin'): ?>
                                <a href="return_detail.php?rma_number=<?php echo $rma_return['rma_number']; ?>">Edit</a>
                            <?php endif; ?>		

                            <?php if ($rma_return['rma_status'] == 'Received' && $_SESSION['qc_shipment']): ?>
                                | <a href="return_detail.php?rma_number=<?php echo $rma_return['rma_number']; ?>">QC</a> 
                            <?php endif; ?>	

                            <?php if ($_SESSION['return_decision'] && $rma_return['rma_status'] == 'In QC'): ?>
                                | <a href="return_detail.php?rma_number=<?php echo $rma_return['rma_number']; ?>">Mark Complete</a>
                            <?php endif; ?>

                            <?php if ($_SESSION['login_as'] == 'admin'): ?>
                                | <a onclick="if (!confirm('Are you sure?')) {
                                            return false;
                                        }" href="manage_returns.php?return_id=<?php echo $rma_return['id']; ?>&action=delete">Delete</a>
                    <?php endif; ?>		
                        </td>
                    </tr>
<?php endforeach; ?>
            </table>

            <br /><br />
            <table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
                <tr>
                    <td colspan="7" align="left">
<?php echo $splitPage->display_count("Displaying %s to %s of (%s)"); ?>
                    </td>

                    <td colspan="6" align="right">
<?php echo $splitPage->display_links(10, $parameters); ?>
                    </td>
                </tr>
            </table>
            <br />
        </div>		
    </body>
</html>            			   