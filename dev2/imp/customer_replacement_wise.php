<?php
include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
page_permission('customer_replacement_report');
$start_date = date('Y-m-d');
$end_date = date('Y-m-d');

if(isset($_REQUEST['submit'])){
	$inv_query   = '';


	$parameters  = $_SERVER['QUERY_STRING'];

	$email = $db->func_escape_string($_REQUEST['email']);
	$start_date = $db->func_escape_string($_REQUEST['start_date']);
	$end_date = $db->func_escape_string($_REQUEST['end_date']);


	if($email) {
		$conditions[] =  " LCASE(b.email)=LCASE('".$email."') ";
	}

	if($start_date && $end_date) {
		$conditions[] =  " (date(b.order_date) BETWEEN '$start_date' and '$end_date') ";
	}


	$condition_sql = implode(" AND " , $conditions);



}
if(!$condition_sql){
	$condition_sql = " (b.order_date BETWEEN '$start_date' and '$end_date') ";

}
$_REQUEST['start_date'] = $start_date;
$_REQUEST['end_date'] = $end_date;
//$inv_query = "SELECT a.firstname, a.lastname, a.email, SUM(`c` . product_qty ) count_sku, SUM(`c` . product_price ) product_price, SUM(`c` . product_true_cost ) cost FROM inv_customers AS `a` INNER JOIN inv_orders AS b ON a.email = b.email INNER JOIN inv_orders_items AS `c` ON b.order_id = c.order_id WHERE b.payment_source = 'Replacement' AND $condition_sql GROUP BY a.email ORDER BY COUNT(c.product_sku) DESC";
$inv_query = "SELECT b.email , SUM(`c` . product_qty ) count_sku, SUM(`c` . product_price ) product_price, SUM(`c` . product_true_cost ) cost FROM  inv_orders AS b  INNER JOIN inv_orders_items AS `c` ON b.order_id = c.order_id WHERE b.payment_source = 'Replacement' AND $condition_sql GROUP BY b.email ORDER BY COUNT(c.product_sku) DESC";
//die($inv_query);
// echo $inv_query;exit;
if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "report_replacement_wise.php",$page);
$inv_orders = $db->func_query($splitPage->sql_query);

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="include/jquery-ui.css">
	<script src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<script src="js/jquery-ui.js"></script>
	<title>Report Return Item Wise</title>


</head>
<body>
	<?php include_once 'inc/header.php';?>

	<?php if(@$_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php endif;?>

	<br />

	<br />

	<h2 align="center">Replacement Report</h2>

	<h3 align="center">
		<?php
		if($_SESSION['login_as']=='admin') { ?>
		<a href="pdf_c_replacement_wise.php?<?=$parameters;?>" target="_blank">Print PDF Report</a>
		<?php } ?>
	</h3>

	<form name="order" action="" method="get">
		<table width="90%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
			<tbody>
				<tr>
					<td colspan="3" align="center">
						<label for="start_date">Email:</label>
						<input type="text" name="email" value="<?php echo @$_REQUEST['email'];?>" />
						<label for="start_date">Start / End Date</label>
						<input style="width:140px" type="text" placeholder="Start Date" name="start_date" value="<?php echo $_REQUEST['start_date'];?>" class="datepicker" readOnly>
						<input style="width:140px" type="text" placeholder="End Date" name="end_date" value="<?php echo $_REQUEST['end_date'];?>" class="datepicker" readOnly>
						<input type="submit" value="Search" name="submit" style="margin: 10px 0 0 60px"></td>
					</tr>

					<tr>
						<?php if($inv_orders):?>
							<td colspan=8>
								<table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
									<thead>
										<tr style="background-color:#e5e5e5;">
											<th>SN</th>
											<th>Name</th>
											<th>Email</th>
											<th># Of Replacements</th>
											<th>Amt Replacement</th>
											<th>Cost</th>
										</tr>
									</thead>
									<?php $i = $splitPage->display_i_count();
									?>
									<?php foreach($inv_orders as $return) { ?>
									<?php
									$__a = $db->func_query_first("SELECT b.first_name,b.last_name FROM inv_orders a, inv_orders_details b WHERE a.order_id=b.order_id and a.email='".$return['email']."' order by a.id desc");
									$return['firstname'] = $__a['first_name'];
									$return['lastname']	= $__a['last_name'];
									?>
									<tr id="<?php echo $return['email'];?>">
										<td align="center"><?php echo $i; ?></td>

										<td align="center"><?php echo $return['firstname'] . ' ' . $return['lastname'];?></td>
										<td ><?php echo linkToProfile($return['email'], $hostpath);?></td>


										<td align="center">
											<?php echo $return['count_sku'];?>
										</td>

										<td align="center">
											$<?=number_format($return['product_price'],2);?>
										</td>


										<td align="center">$<?php echo number_format(@$return['cost'],2);?></td>
									</tr>
									<?php $i++;  ?>
									<?php } ?>

								</table>
							</td>  

						<?php else : ?> 

							<td colspan=4><label style="color: red; margin-left: 600px;">No Record Found</label></td>

						<?php endif;?>
					</tr>

					<tr>
						<td colspan="5" align="left">
							<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
						</td>

						<td colspan="6" align="right">
							<?php echo $splitPage->display_links(10,$parameters);?>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</body>
	</html>