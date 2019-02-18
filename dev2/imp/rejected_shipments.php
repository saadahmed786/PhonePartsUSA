<?php



include_once 'auth.php';

include_once 'inc/functions.php';

include_once 'inc/split_page_results.php';

// ajax call for RMA Vendor Loading SHipments by Gohar

if($_POST['action']=='load_rtv_shipments'){

    $vendor_shipments_loaded = $db->func_query("SELECT * FROM inv_rejected_shipments where status = 'Pending' AND vendor = '".$_POST['vendor_id_shipments_loader']."'");

    $json = array();

    $json['loaded_rtv_shipments'] = $vendor_shipments_loaded;

    echo json_encode($json);

    exit;

}

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

if ($_GET['action'] == 'hide') {

    $shipment_id = (int)$_GET['shipment_id'];

    $db->db_exec("update inv_rejected_shipments set is_hidden = '1' where id = '$shipment_id'");

    

    $_SESSION['message'] = "Shipment is Hidden";

    header("Location:rejected_shipments.php");

    exit;

}

if((int)$_GET['shipment_id'] and $_GET['action'] == 'delete' && $_SESSION['delete_shipment']){

	$shipment_id = (int)$_GET['shipment_id'];

	$db->db_exec("delete from inv_rejected_shipments where id = '$shipment_id'");

	

	$_SESSION['message'] = "Shipment is deleted";

	header("Location:rejected_shipments.php");

	exit;

}



$vendors = $db->func_query("select id , name as value from inv_users where group_id = 1 and status=1 order by lower(name) asc");

// if($_GET['vendor']||$_GET['status']){



//     print_r($_GET['vendor']);exit;

// }

//print_r($vendors[0]['value']);exit;



if((int)$_GET['shipment_id'] and $_GET['action'] == 'complete' && $_SESSION['edit_received_shipment']){

	$shipment_id = (int)$_GET['shipment_id'];

	

	$shipment_detail = $db->func_query_first("select * from inv_rejected_shipments where id = '$shipment_id'");

	if(!$shipment_detail['package_number']){

		$_SESSION['message'] = "Package number is required.";

		header("Location:addedit_rejectedshipments.php?shipment_id=$shipment_id");

		exit;

	}

	

	$db->db_exec("update inv_rejected_shipments SET date_completed = now(), status = 'Completed'  where id = '$shipment_id'");

	

	$_SESSION['message'] = "Shipment status is Completed";

	header("Location:rejected_shipments.php");

	exit;

}





if(isset($_GET['page'])){

    $page = intval($_GET['page']);

}

if($page < 1){

    $page = 1;

}



$max_page_links = 10;

$num_rows = 20;

$start = ($page - 1)*$num_rows;

$where =  array();

if(!isset($_GET['status']))
{
    $_GET['status']='Pending';
}

if($_GET['number'] || $_GET['status'] || $_GET['vendor']){

    $number = $db->func_escape_string($_GET['number']); 

    $statuss = $db->func_escape_string($_GET['status']);

    $vendorss =$db->func_escape_string($_GET['vendor']);

    $where[]  = "package_number like '%$number%'";
    if($statuss!='Hidden')
    {
    if($statuss){

    $where[]  = "status LIKE '$statuss' ";

    }
    }
    else
    {
        $where[] = "is_hidden = '1'";

    }
    if($vendorss){

     $where[]  = "vendor LIKE '$vendorss' ";   

    }



}



$vtx = false;

//print_r($_SESSION['group']);exit;

if ($_SESSION['group'] == 'Vendor') {

    $where[] = "vendor = '" . $_SESSION['user_id'] . "'";

// $where[] = "status = 'Completed'";

    $vtx = true;

}
if($_GET['status']!='Hidden')
{
    $where[] = "is_hidden = '0'";
    
}

if ($_SESSION['login_as'] == 'admin') {

    $vtx = true;

}



if ($where) {

    $where = 'WHERE ' . implode(' AND ', $where);

}



$inv_query  = "select * from inv_rejected_shipments $where order by date_added DESC";



$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "rejected_shipments.php",$page);

$shipments  = $db->func_query($splitPage->sql_query);



foreach($shipments as $index => $shipment){

    $st = $db->func_query_first("Select count(id) as total, max(date_added) as date_added from inv_rejected_shipment_items where deleted= '0' and rejected_shipment_id = '".$shipment['id']."'");

    $shipments[$index]['items'] = $st['total'];

    $shipments[$index]['last_added'] = $st['date_added'];

}









?>

<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Rejected Item PO</title>



	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>

	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />



	<script type="text/javascript">

		$(document).ready(function() {

			$('.fancybox').fancybox({ width: '680px' , autoCenter : true , autoSize : true });

		});

	</script>	

</head>

<body>

	<?php include_once 'inc/header.php';?>



	<?php if(@$_SESSION['message']):?>

		<div align="center"><br />

			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>

		</div>

	<?php endif;?>



	<div align="center">

        	<!-- <a href="addedit_rejectedshipment.php">Create Rejected Shipment</a> 

        	| -->

        	<a class="fancybox fancybox.iframe" href="<?php echo $host_path;?>popupfiles/rejected_newbox.php">Create New Box</a>

        	

        	<br /><br />

        	

        	<form method="get">

            Status:

                    <select name="status">

                    <option value="">All</option>

                    <option <?php echo ($_GET['status'] == 'Pending')? 'selected="selected"': ''; ?> value="Pending">Pending</option>

                    <option <?php echo ($_GET['status'] == 'Shipped')? 'selected="selected"': ''; ?> value="Shipped">Shipped</option>
                    <option <?php echo ($_GET['status'] == 'Hidden')? 'selected="selected"': ''; ?> value="Hidden">Hidden</option>

                    <option <?php echo ($_GET['status'] == 'Received')? 'selected="selected"': ''; ?> value="Received">Received</option>

                    <option <?php echo ($_GET['status'] == 'Completed')? 'selected="selected"': ''; ?> value="Completed">Completed</option>

                    </select>

            Vendor:

                    <select name="vendor">

                        <option value="" >All</option>

                        <?php foreach ($vendors as $i => $name) { ?>

                            <option <?php echo ($_GET['vendor'] == $name['id'])? 'selected="selected"': ''; ?> value="<?php echo $name['id']; ?>"><?php echo $name['value']; ?></option>

                        <?php }?>

                    </select>



        		Package Number:

        		<input type="text" name="number" value="<?php echo $_GET['number'];?>" />

        		<input type="submit" name="search" value="Search" />

        		<?php if ($_SESSION['rj_reasons_view']) { ?>

        		<a href="rj_reasons.php" class="fancyboxX3 fancybox.iframe button">Update RTV Reasons</a>

        		<?php } ?>

        	</form>

        </div>



        <?php if($shipments):?>

        	<table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">

        		<thead>

        			<tr>

        				<th>#</th>

                        <th>Shipment Date</th>

        				<th>Shipment Number</th>

                        <th>Status</th>

        				<th>Vendor</th>

        				<th>Date Created</th>

        				<th>Last RTV Added</th>

        				<th>Date Completed</th>

        				<th># Items</th>

        				<th>Action</th>

                        <th>Package Tracking</th>

        			</tr>

        		</thead>

        		<tbody>

        			<?php $i = $splitPage->display_i_count();

        			foreach($shipments as $shipment):?>



        			<tr id="<?php echo $shipment['id'];?>">

        				<td align="center"><?php echo $i; ?></td>

                        <?php if ($shipment['status'] == 'Shipped') { ?>

                            <td align="center"><?php echo date("Y-m-d", strtotime(americanDate($shipment['date_issued'])));?></td>

                        <?php }  else { ?>

                        <td></td>

                        <?php } ?>

                        <td align="center"><?php echo $shipment['package_number'];?></td>



                        <td align="center"><?php echo $shipment['status'];?></td>

        				

                        <td align="center"><?php echo get_username($shipment['vendor']);?></td>



        				<td align="center"><?php echo americanDate($shipment['date_added']);?></td>



        				<td align="center"><?php echo americanDate($shipment['last_added']);?></td>



        				<td align="center"><?php echo americanDate($shipment['date_completed']);?></td>



        				<td align="center"><?php echo $shipment['items'];?></td>



        				<td align="center" class="showorder">

        					<?php if(($shipment['status'] == 'Pending' && $_SESSION['edit_pending_shipment']) || $_SESSION['qc_shipment']):?>	

        						<a href="addedit_rejectedshipment.php?shipment_id=<?php echo $shipment['id']?>">Edit</a>

                                |

        					<?php endif;?>

                            <?php echo ($vtx)? '<a href="addedit_rejectedshipment.php?shipment_id=' . $shipment['id'] . '">View</a>': ''; ?>



        					<?php if($shipment['status'] == 'Pending' && $_SESSION['edit_received_shipment'] && !$vtx):?>

        						<!-- | -->

                               <!--  <a href="rejected_shipments.php?action=complete&shipment_id=<?php echo $shipment['id']?>" onclick="if(!confirm('Are you sure?')){ return false; }">

                                    Complete Shipment

                                </a> -->

        					<?php endif;?>	

                            |

        					<a href="download.php?action=rejected_shipment&shipment_id=<?php echo $shipment['id']?>">Download</a>

                            <?php if ($_SESSION['login_as'] == 'admin') { ?>

                            |

                            <a href="rejected_shipments.php?action=hide&shipment_id=<?php echo $shipment['id']?>">Hide</a>

                                

                            <?php } ?>

        					<?php if($_SESSION['delete_shipment'] && !$vtx):?>

        						|

        						<a href="rejected_shipments.php?action=delete&shipment_id=<?php echo $shipment['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>

        					<?php endif;?>

        				</td>

                        <td align="center">

                            <?php

                            if(!$shipment['tracking_number']){

                              echo 'Not Synced';

                            }

                            else

                            {

                              $tracker_id = $db->func_query_first_cell("SELECT tracker_id FROM inv_tracker where shipment_id='".$shipment['id']."'");

                              $last_update = $db->func_query_first_cell("SELECT message FROM inv_tracker_status WHERE tracker_id='".$tracker_id."' order by id desc limit 1");

                              echo $last_update;

                            }

                            ?>

                          </td>

        			</tr>

        			<?php $i++; endforeach; ?>



        			<tr>

        				<td colspan="5" align="left">

        					<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>

        				</td>



        				<td colspan="4" align="right">

        					<?php  echo $splitPage->display_links(10,$parameters); ?>

        				</td>

        			</tr>

        		</tbody>   

        	</table>   

        <?php else : ?> 

        	<p>

        		<label style="color: red; margin-left: 600px;">Shipments is not exist.</label>

        	</p>     

        <?php endif;?>

    </body>

    </html>        