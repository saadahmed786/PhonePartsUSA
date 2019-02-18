<?php
require_once("auth.php");
include_once 'inc/functions.php';
$_SESSION['hide_header'] = 1;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Agent Dashboard</title>
	<script>
    document.onkeydown=function(evt){
        var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
        if(keyCode == 13)
        {
            if($('#customers_form').is(":visible")){
            	fetchData('customers');
            } else if ($('#orders_form').is(":visible")){
            	fetchData('orders');
            }else if ($('#returns_form').is(":visible")){
            	fetchData('returns');
            }else if ($('#buybacks_form').is(":visible")){
            	fetchData('buybacks');
            }
        }
    }
</script>
</head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="include/calendar.css" rel="stylesheet" type="text/css" />
<link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="include/calendar.js"></script>
<script type="text/javascript" src="include/calendar-en.js"></script>
<script type="text/javascript" src="include/calhelper.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<body>
<?php
	if(isset($_GET['hide_header']))
	{
		?>
		<style>
		.toogleTab{
			display: none !important;
		}

		</style>
		<?php
	}
?>

	<div class="div-fixed">
		<div align="center" <?php
		if(isset($_GET['hide_header']))
		{
			echo 'style="display:none"';
		}
		?>> 
			<?php include_once 'inc/header.php';?>
		</div>
		
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<div align="center">
			<div class="tabMenu" >
				<input type="button" class="toogleTab" data-tab="tabAgent" value="Agent Dashboard">
				<input type="button" class="toogleTab" onclick="checkCatalogTab();" data-tab="tabCatalog" value="Create Order with Catalog">
				<input type="button" class="toogleTab" onclick="checkSkuTab();" data-tab="tabSku" value="Create Order with SKU">
				<?php
				if(!isset($_GET['hide_header']))
				{
					?>

				<input type="button" class="toogleTab" onclick="salesDashboardTab();" data-tab="tabSalesDashboard" value="Sales Dashboard">
					<?php
				}
				?>

			</div>
			<div class="tabHolder">
				
				<div id="loading">
					<h2>Loading...</h2>
				</div>
				<div id="tabAgent" class="makeTabs">
					<h3>Agent Dashboard</h3><br><br>
					<div align="center" style="width: 95%">
						<table>
							<tr>
								<td>									
									<input type="checkbox" class="selection" onclick="loadCustomerOptions(this)" name="customers_checkbox"  /> 
									<b>Customers</b><small><a href="<?php echo $host_path; ?>customers.php" target = "_blank"> (View All)</a></small>
								</td>
								<td>									
									<input type="checkbox" class="selection" onclick="loadOrderOptions(this)" name="orders_checkbox"   /> 
									<b>Orders</b><small><a href="<?php echo $host_path; ?>order.php" target = "_blank"> (View All)</a></small>
								</td>
								<td>									
									<input type="checkbox" class="selection" onclick="loadReturnOptions(this)" name="return_checkbox"  /> 
									<b>Return (RMA)</b><small><a href="<?php echo $host_path; ?>manage_returns.php" target = "_blank"> (View All)</a></small>
								</td>
								<td>									
									<input type="checkbox" class="selection" onclick="loadLBBOptions(this)" name="buyback_checkbox" /> 
									<b>LCD BuyBack (LBB)</b><small><a href="<?php echo $host_path; ?>buyback/shipments.php" target = "_blank"> (View All)</a></small>
								</td>

							</tr>

						</table>
						<br>
						<!-- Customers Form -->
						<form method="get" action="customers.php" id="customers_form" class="search_form" style="display: none;">
							<table border="0" cellpadding="5">
								<tr>
									<td>Email</td>
									<td>
										<input type="text" name="email" value="<?php echo $_GET['customer_email'];?>" />
									</td>
									
									<td>First Name</td>
									<td>
										<input type="text" name="firstname" value="<?php echo $_GET['customer_first_name'];?>" />
									</td>
									

									<td>Last Name:</td>
									<td><input type="text" name="lastname" value="<?php echo $_GET['customer_last_name'];?>" /></td>
									
									 <?php
            if($_SESSION['login_as']=='admin' or $_SESSION['is_sales_manager']==1)
            {
              $agents = $db->func_query("select id,name from inv_users WHERE is_sales_agent=1 and status=1 ");
              ?>
									<td>Agent:</td>
									<td>
										
             
                
                <select id="user_id" onchange="fetchTransitOrders()">
                  <option value="">Select Agent</option>
                  <?php
                  foreach($agents as $agent)
                  {
                    ?>
                    <option value="<?=$agent['id'];?>" <?php if($_GET['user_id']==$agent['id']) echo 'selected';?>><?=$agent['name'];?></option>
                    <?php
                  } 

                  ?>
                </select>
              
              

									</td>

									<?php
            }
            else
            {
            	?>
            	<td style="display: none">
            	<input type="hidden" id="user_id" value="<?php echo $_SESSION['user_id'];?>">
            	</td>
            	<?php
            }
            ?>

									<td  align="center"><input type="button" name="search" value="Search" class="button" onclick="fetchData('customers');" /></td>
								</tr>
								<tr>
									<td align="center" colspan="8"><a href="javascript://" onclick="ShowHideAdvancedSearchCustomer();">Advanced Look up&#8595</a></td>
								</tr>
								<tr style="display: none;" id="customer_advanced_lookup_row">
									<td>Any Previous Order ID:</td>
									<td><input type="text" name="order_id" value="<?php echo $_GET['customer_previous_order_id'];?>" /></td>
									
									<td>Delivery Zip:</td>
									<td><input type="text" name="filter_zip" value="<?php echo $_GET['customer_delivery_zip'];?>" /></td>
									
									<td>City:</td>
									<td><input type="text" name="filter_city" value="<?php echo $_GET['customer_city'];?>" /></td>
									
									<td>State:</td>
									<td><input type="text" name="filter_state" value="<?php echo $_GET['customer_state'];?>" /></td>
									
									
								</tr>
							</table>
						</form>
						<!-- Orders Form -->
						<form method="get" action="order.php" id="orders_form" class="search_form" style="display: none;">
							<table border="1" cellpadding="5">
								<tr>
									<td>Order ID</td>
									<td colspan="2">
										<input type="text" name="order_id" value="<?php echo $_GET['order_id'];?>" />
									</td>	
									<td>Email</td>
									<td colspan="2">
										<input type="text" name="email" value="<?php echo $_GET['customer_email'];?>" />
									</td>								

									<td colspan="4" align="center"><input type="button" name="search" value="Search" class="button" onclick="fetchData('orders');" /></td>
								</tr>
								<tr>
									<td align="center" colspan="10"><a href="javascript://" onclick="ShowHideAdvancedSearchOrder();">Advanced Look up&#8595</a></td>
								</tr>
								<tr style="display: none;" id="orders_advanced_lookup_row">
									
									<td>First Name</td>
									<td>
										<input type="text" name="firstname" value="<?php echo $_GET['customer_first_name'];?>" />
									</td>
									

									<td>Last Name:</td>
									<td><input type="text" name="lastname" value="<?php echo $_GET['customer_last_name'];?>" /></td>

									<td>Delivery Zip:</td>
									<td><input type="text" name="filter_zip" value="<?php echo $_GET['customer_delivery_zip'];?>" /></td>
									
									<td>City:</td>
									<td><input type="text" name="filter_city" value="<?php echo $_GET['customer_city'];?>" /></td>
									
									<td>State:</td>
									<td><input type="text" name="filter_state" value="<?php echo $_GET['customer_state'];?>" /></td>
									
									
								</tr>
							</table>
						</form>
						<!-- RMA Form -->
						<form method="get" action="manage_returns.php" id="returns_form" class="search_form" style="display: none;">
							<table border="1" cellpadding="5">
								<tr>
									<td>RMA ID</td>
									<td colspan="2">
										<input type="text" name="rma_number" value="<?php echo $_GET['rma_number'];?>" />
									</td>	
									<td>Email</td>
									<td colspan="2">
										<input type="text" name="email" value="<?php echo $_GET['customer_email'];?>" />
									</td>								

									<td colspan="4" align="center"><input type="button" name="search" value="Search" class="button" onclick="fetchData('returns');" /></td>
								</tr>
								<tr>
									<td align="center" colspan="8"><a href="javascript://" onclick="ShowHideAdvancedSearchReturn();">Advanced Look up&#8595</a></td>
								</tr>
								<tr style="display: none;" id="returns_advanced_lookup_row">
									
									<td>Any Previous Order ID:</td>
									<td><input type="text" name="order_id" value="<?php echo $_GET['customer_previous_order_id'];?>" /></td>

									<td>Delivery Zip:</td>
									<td><input type="text" name="filter_zip" value="<?php echo $_GET['customer_delivery_zip'];?>" /></td>
									
									<td>City:</td>
									<td><input type="text" name="filter_city" value="<?php echo $_GET['customer_city'];?>" /></td>
									
									<td>State:</td>
									<td><input type="text" name="filter_state" value="<?php echo $_GET['customer_state'];?>" /></td>
									
									
								</tr>
							</table>
						</form>
						<!-- LBB Form -->
						<form method="get" action="buyback/shipments.php" id="buybacks_form" class="search_form" style="display: none;">
							<table border="1" cellpadding="5">
								<tr>
									<td>BuyBack Shipment ID</td>
									<td colspan="2">
										<input type="text" name="buyback_id" value="<?php echo $_GET['rma_number'];?>" />
									</td>	
									<td>Email</td>
									<td colspan="2">
										<input type="text" name="email" value="<?php echo $_GET['customer_email'];?>" />
									</td>								

									<td colspan="4" align="center"><input type="button" name="search" value="Search" class="button" onclick="fetchData('buybacks');" /></td>
								</tr>
								<tr>
									<td align="center" colspan="7"><a href="javascript://" onclick="ShowHideAdvancedSearchBuyback();">Advanced Look up&#8595</a></td>
								</tr>
								<tr style="display: none;" id="buybacks_advanced_lookup_row">
									
									<td>First Name</td>
									<td colspan="2">
										<input type="text" name="firstname" value="<?php echo $_GET['customer_first_name'];?>" />
									</td>
									<td>Last Name:</td>
									<td colspan="3"><input type="text" name="lastname" value="<?php echo $_GET['customer_last_name'];?>" /></td>
									
									
									
								</tr>
							</table>
						</form>
					</div><br><br>
					<div align="center" id="loader" style="width: 98%;display: none;">
						<img src="images/loading.gif" style="max-width: 100px;">
					</div>

					<div align="center" style="display:none;width: 98%;height:450px;overflow:auto;" id="search_details_cust" >
						<table border="1" cellpadding="5" cellspacing="0" width="90%" id="search_details_customer" style="display: none;">

							<thead>
								<tr style="background:#e5e5e5;" ><th colspan= "11">Customer Details</th></tr>
								<tr style="background:#e5e5e5;">
									<th>First Name</th>
									<th>Last Name</th>
									<th>Email</th>
									<th>City</th>
									<th>State</th>
									<th>Group</th>
									<th># Of Orders</th>
									<th>Total Amount</th>
									<th>Last Order</th>
									<th>Creation Date</th>
									<th>Action</th>
								</tr>			
							</thead>
							<tbody id="search_details_customer">

							</tbody>
						</table>
						<br><br>
					</div>
					<div align="center" style="display:none;width: 98%;height:450px;overflow:auto;" id="search_details_ord" >
						<table border="1" cellpadding="5" cellspacing="0" width="90%" id="search_details_order" style="display: none;">

							<thead>
								<tr style="background:#e5e5e5;" ><th colspan= "11">Order Details</th></tr>
								<tr style="background:#e5e5e5;">
									<th>Order ID</th>
									<th>Order Date</th>
									<th>Email</th>
									<th>Customer</th>
									<th>Order Price</th>
									<th>Store Type</th>
									<th>PPUSA</th>
									<th>Order Status</th>
									<th>Payment</th>
									<th>FB Added</th>
								</tr>			
							</thead>
							<tbody id="search_details_order">

							</tbody>
						</table>
						<br><br>
					</div>
					<div align="center" style="display:none;width: 98%;height:450px;overflow:auto;" id="search_details_ret" >
						<table border="1" cellpadding="5" cellspacing="0" width="90%" id="search_details_return" style="display: none">

							<thead>
								<tr style="background:#e5e5e5;" ><th colspan= "11">Return Details</th></tr>
								<tr style="background:#e5e5e5;">
									<th>Received</th>
									<th>QC</th>
									<th>Completed</th>
									<th>RMA Number</th>
									<th>PPUSA</th>
									<th>Source</th>
									<th>Email</th>
									<th>Order ID</th>
									<th>SKU/Decision</th>
									<th>Amount</th>
									<th>Status</th>
									
								</tr>			
							</thead>
							<tbody id="search_details_return">

							</tbody>
						</table>
						<br><br>
					</div>
					<div align="center" style="display:none;width: 98%;height:450px;overflow:auto;" id="search_details_lbb" >
						<table border="1" cellpadding="5" cellspacing="0" width="90%" id="search_details_buyback" style="display: none;">
							<thead>
								<tr style="background:#e5e5e5;" ><th colspan= "10">LBB Details</th></tr>
								<tr style="background:#e5e5e5;">
									<th>Added</th>
									<th>Received</th>
									<th>Date QC</th>
									<th>Shipment Number</th>
									<th>Name</th>
									<th>Customer</th>
									<th>Payment Type</th>
									<th>Total</th>
									<th>Status</th>

								</tr>			
							</thead>
							<tbody id="search_details_buyback">

							</tbody>
						</table>
						<br><br>
					</div>
					<hr>
					<div align="center" style="width: 100%">
						<table width="90%" border="0">
							<tr>
								<td>
									<span><h3 align="center">Assigned Customer Orders &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspFrom:<input type="text" style="width: 70px; border:none" onblur="fetchAssignedOrders();" data-type="date" value="<?php echo date('Y-m-d'); ?>" name="assigned_start_date" id="assigned_start_date" />  To:<input style="width: 70px; border:none" type="text" data-type="date" value="<?php echo date('Y-m-d'); ?>"  onblur="fetchAssignedOrders();" name="assigned_end_date" id="assigned_end_date" /></h3></span>
									<div style="height:250px;width:100%;overflow:auto;">
										<table id="assigned_customer_orders" align="center" border="1" width="90%" cellpadding="5" cellspacing="0">
											<thead>
												<tr style="background:#e5e5e5;" align="center">
													<th>Order #</th>
													<th>Account Name</th>
													<th>Customer Email</th>
													<th>Amount</th>
													<th>Shipping Method</th>
													<th>Status</th>
												</tr>
											</thead>
											<div align="center" id="assigned_loader" style="width: 98%;display: none;">
												<img src="images/loading.gif" style="max-width: 100px;">
											</div>
											<tbody id="assigned_customer_orders">
												<?php $assigned_customer_emails = $db->func_query("SELECT email from inv_customers where user_id = '".(int)$_SESSION['user_id']."' ");
												$email_string = '';
												foreach ($assigned_customer_emails as $email) {
													$email_string.="'".$email['email']."',";
												}
												$email_string.="'***'";
												$assigned_customer_orders =   $db->func_query("SELECT i.order_id,i.order_price as amount,i.email,i.customer_name,i.order_status,od.shipping_method  from inv_orders i inner join inv_orders_details od on (i.order_id = od.order_id) where email IN ($email_string) and date(i.order_date)=date(now()) order by i.order_date desc ");
												foreach($assigned_customer_orders as $order){?>
												<tr>
													<td><a target="_blank" href="viewOrderDetail.php?order=<?php echo $order['order_id']?>"><?php echo $order['order_id'];?></a></td>
													<td><?php echo $order['customer_name']; ?></td>
													<td><?php echo linkToProfile($order['email'],  '',  '', '_blank'); ?></td>
													<td>$<?php echo number_format($order['amount'],2); ?></td>
													<td><?php echo $order['shipping_method']; ?></td>
													<td><?php echo $order['order_status']; ?></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</td>
								<td>
									<span><h3 align="center"> Agent Created Orders &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspFrom:<input type="text" style="width: 70px; border:none" data-type="date" value="<?php echo date('Y-m-d'); ?>" name="created_start_date" id="created_start_date" onblur="fetchCreatedOrders();" />  To:<input style="width: 70px; border:none" type="text" data-type="date" value="<?php echo date('Y-m-d'); ?>" onblur="fetchCreatedOrders();" name="created_end_date" id="created_end_date" /></h3></span>
									<div style="height:250px;width:107%;overflow:auto;">
										<table id="agent_created_orders" align="center" border="1" width="90%" cellpadding="5" cellspacing="0">
											<thead>
												<tr style="background:#e5e5e5;" align="center">
													<th>Order #</th>
													<th>Account Name</th>
													<th>Customer Email</th>
													<th>Amount</th>
													<th>Shipping Method</th>
													<th>Status</th>
												</tr>
											</thead>
											<div align="center" id="created_loader" style="width: 98%;display: none;">
												<img src="images/loading.gif" style="max-width: 100px;">
											</div>
											<tbody id="agent_created_orders">
												<?php 
												$assigned_customer_orders =   $db->func_query("SELECT i.order_id,i.order_price as amount,i.email,i.customer_name,i.order_status,od.shipping_method  from inv_orders i inner join inv_orders_details od on (i.order_id = od.order_id) where i.order_user = '".(int)$_SESSION['user_id']."' AND i.is_manual='1' and date(i.order_date)=date(now()) order by i.order_date desc ");
												foreach($assigned_customer_orders as $order){?>
												<tr>
													<td><a target="_blank" href="viewOrderDetail.php?order=<?php echo $order['order_id']?>"><?php echo $order['order_id'];?></a></td>
													<td><?php echo $order['customer_name']; ?></td>
													<td><?php echo linkToProfile($order['email'],  '',  '', '_blank'); ?></td>
													<td>$<?php echo number_format($order['amount'],2); ?></td>
													<td><?php echo $order['shipping_method']; ?></td>
													<td><?php echo $order['order_status']; ?></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<span><h3 align="center"> IN TRANSIT - Assigned/Manually Created Orders </h3></span>
									<div style="height:250px;width:107%;overflow:auto;">
										<table id="transit_orders" align="center" border="1" width="90%" cellpadding="5" cellspacing="0">
											<thead>
												<tr style="background:#e5e5e5;" align="center">
													<th>Order #</th>
													<th>Account Name</th>
													<th>Customer Email</th>
													<th>Amount</th>
													<th>Shipping Method</th>
													<th>Days Since Shipment</th>
													<th>Last Shipment Status</th>
												</tr>
											</thead>
											<div align="center" id="transit_loader" style="width: 98%;display: none;">
												<img src="images/loading.gif" style="max-width: 100px;">
											</div>
											<tbody id="transit_orders">
												
											</tbody>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<span><h3 align="center"> DELIVERED - Assigned/Manually Created Orders &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspFrom: <input type="text" style="width: 70px; border:none" onblur="fetchDeliveredOrders();" data-type="date" value="<?php echo date('Y-m-d'); ?>" name="delivered_start_date" id="delivered_start_date" />  To: <input style="width: 70px; border:none" type="text" data-type="date" value="<?php echo date('Y-m-d'); ?>"  onblur="fetchDeliveredOrders();" name="delivered_end_date" id="delivered_end_date" /></h3></span>
									<div style="height:250px;width:107%;overflow:auto;">
										<table id="delivered_orders" align="center" border="1" width="90%" cellpadding="5" cellspacing="0">
											<thead>
												<tr style="background:#e5e5e5;" align="center">
													<th>Order #</th>
													<th>Account Name</th>
													<th>Customer Email</th>
													<th>Amount</th>
													<th>Delivery Date</th>
												</tr>
											</thead>
											<div align="center" id="delivered_loader" style="width: 98%;display: none;">
												<img src="images/loading.gif" style="max-width: 100px;">
											</div>
											<tbody id="delivered_orders">
												
											</tbody>
										</table>
									</div>
								</td>
							</tr>	
						</table>
					</div>
					<br><br>
				</div>
				<div id="tabCatalog"  class="makeTabs">
					
				</div>
				<div id="tabSku" class="makeTabs">
					

				</div>

				<div id="tabSalesDashboard">

				</div>

			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.selection:eq(0)').trigger('click');
			fetchTransitOrders();
			fetchDeliveredOrders();

		});

		function clearTables(){
		//$('#search_details_customer tbody').html('');
		$('#search_details_customer').hide();
		$('#search_details_cust').hide();

		//$('#search_details_order tbody').html('');
		$('#search_details_order').hide();
		$('#search_details_ord').hide();

		//$('#search_details_return tbody').html('');
		$('#search_details_return').hide();
		$('#search_details_ret').hide();

		//$('#search_details_buyback tbody').html('');
		$('#search_details_buyback').hide();
		$('#search_details_lbb').hide();

	}

	function ShowHideAdvancedSearchCustomer(){
		if(!$('#customer_advanced_lookup_row').is(':visible'))
		{
			$('#customer_advanced_lookup_row').show();
		} else {
			$('#customer_advanced_lookup_row').hide();
		}
	}
	function ShowHideAdvancedSearchOrder(){
		if(!$('#orders_advanced_lookup_row').is(':visible'))
		{
			$('#orders_advanced_lookup_row').show();
		} else {
			$('#orders_advanced_lookup_row').hide();
		}
	}
	function ShowHideAdvancedSearchReturn(){
		if(!$('#returns_advanced_lookup_row').is(':visible'))
		{
			$('#returns_advanced_lookup_row').show();
		} else {
			$('#returns_advanced_lookup_row').hide();
		}
	}
	function ShowHideAdvancedSearchBuyback(){
		if(!$('#buybacks_advanced_lookup_row').is(':visible'))
		{
			$('#buybacks_advanced_lookup_row').show();
		} else {
			$('#buybacks_advanced_lookup_row').hide();
		}
	}
	function loadCustomerOptions(obj){
		clearTables();
		if (obj.checked) {
			$('.search_form').hide();
			$('#customers_form').show();
			$('.selection').prop("checked",false);
			obj.checked = true;
		}else {
			$('#customers_form').hide();
		}
	}
	function loadLBBOptions(obj){
		clearTables();
		if (obj.checked) {
			$('.search_form').hide();
			$('#buybacks_form').show();
			$('.selection').prop("checked",false);
			obj.checked = true;
		}else {
			$('#buybacks_form').hide();
		}
	}
	function loadOrderOptions(obj){
		clearTables();
		if (obj.checked) {
			$('.search_form').hide();
			$('#orders_form').show();
			$('.selection').prop("checked",false);
			obj.checked = true;
		}else {
			$('#orders_form').hide();
		}
	}
	function loadReturnOptions(obj){
		clearTables();
		if (obj.checked) {
			$('.search_form').hide();
			$('#returns_form').show();
			$('.selection').prop("checked",false);
			obj.checked = true;
		}else {
			$('#returns_form').hide();
		}
	}
	function fetchData(form){
		if (form == 'customers') {
			$.ajax({
				url: $('#customers_form').attr('action')+'?agent_dashboard=1',
				type: 'GET',
				data : $('#customers_form').serialize(),
				dataType : 'json',
				beforeSend: function () {
					$('#loader').show();
				},
				complete: function () {
					$('#loader').hide();
				},
				success: function(json){
					if (json['success']) {
						var html = '';
						for (var i = 0; i < json['agent_dashboard_customer'].length; i++) {
							html+='<tr>';
							html+='<td align="center">'+json['agent_dashboard_customer'][i]['firstname']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_customer'][i]['lastname']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_customer'][i]['email']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_customer'][i]['city']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_customer'][i]['state']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_customer'][i]['customer_group']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_customer'][i]['no_of_orders']+'</td>';
							html+='<td align="center">$'+json['agent_dashboard_customer'][i]['total_amount']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_customer'][i]['last_order']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_customer'][i]['date_added']+'</td>';
							if (json['agent_dashboard_customer'][i]['customer_id'] > 0) {
								var customer_id = json['agent_dashboard_customer'][i]['customer_id'];
								var md5 = "'"+json['agent_dashboard_customer'][i]['md5']+"'";
								html+='<td align="center"><input type="button" value="Login" class="button" onclick="customerOCLogin('+customer_id+','+md5+');"></td>';
							} else {
								html+='<td align="center"></td>';
							}
							html+='</tr>';
						}
						$('#search_details_customer tbody').html(html);
						$('#search_details_customer').show();
						$('#search_details_cust').show();
					} else {
						var html = '';
						html+='<tr>';
						html+='<td align="center" colspan="11"><b> No Customer Found</b></td>';
						html+='</tr>';
						$('#search_details_customer tbody').html(html);
						$('#search_details_customer').show();
						$('#search_details_cust').show();
					}


				}
			});
		} else if (form == 'orders'){
			$.ajax({
				url: $('#orders_form').attr('action')+'?agent_dashboard=1',
				type: 'GET',
				data : $('#orders_form').serialize(),
				dataType : 'json',
				beforeSend: function () {
					$('#loader').show();
				},
				complete: function () {
					$('#loader').hide();
				},
				success: function(json){
					if (json['success']) {
						var html = '';
						for (var i = 0; i < json['agent_dashboard_order'].length; i++) {
							html+='<tr>';
							html+='<td align="center">'+json['agent_dashboard_order'][i]['order_id']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_order'][i]['order_date']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_order'][i]['email']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_order'][i]['customer_name']+'</td>';
							html+='<td align="center">$'+json['agent_dashboard_order'][i]['order_price']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_order'][i]['store_type']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_order'][i]['ppusa_sync']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_order'][i]['order_status']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_order'][i]['payment_source']+'</td>';
							html+='<td align="center">'+json['agent_dashboard_order'][i]['fishbowl_uploaded']+'</td>';

							html+='</tr>';
						}
						$('#search_details_order tbody').html(html);
						$('#search_details_order').show();
						$('#search_details_ord').show();
					} else {
						var html = '';
						html+='<tr>';
						html+='<td align="center" colspan="11"><b> No Order Found</b></td>';
						html+='</tr>';
						$('#search_details_order tbody').html(html);
						$('#search_details_order').show();
						$('#search_details_ord').show();
					}


				}
			});

		} else if (form == 'returns') {
			$.ajax({
				url: $('#returns_form').attr('action')+'?agent_dashboard=1',
				type: 'GET',
				data : $('#returns_form').serialize(),
				dataType : 'json',
				beforeSend: function () {
					$('#loader').show();
				},
				complete: function () {
					$('#loader').hide();
				},
				success: function(json){
					if (json['success']) {
						var html = '';
						for (var i =0 ; i < json['return'].length; i++) {
							html+='<tr>';
							html+='<td align="center">'+json['return'][i]['date_received']+'</td>';
							html+='<td align="center">'+json['return'][i]['date_qc']+'</td>';
							html+='<td align="center">'+json['return'][i]['date_completed']+'</td>';
							html+='<td align="center">'+json['return'][i]['rma_number']+'</td>';
							html+='<td align="center">'+json['return'][i]['ppusa']+'</td>';
							html+='<td align="center">'+json['return'][i]['source']+'</td>';
							html+='<td align="center">'+json['return'][i]['email']+'</td>';
							html+='<td align="center">'+json['return'][i]['order_id']+'</td>';
							html+='<td align="center">'+json['return'][i]['extra_details']+'</td>';
							html+='<td align="center">$'+json['return'][i]['amount']+'</td>';
							html+='<td align="center">'+json['return'][i]['rma_status']+'</td>';


							html+='</tr>';
						}
						$('#search_details_return tbody').html(html);
						$('#search_details_return').show();
						$('#search_details_ret').show();
					} else {
						var html = '';
						html+='<tr>';
						html+='<td align="center" colspan="12"><b> No Return Found</b></td>';
						html+='</tr>';
						$('#search_details_return tbody').html(html);
						$('#search_details_return').show();
						$('#search_details_ret').show();
					}


				}
			});

		} else if (form == 'buybacks'){
			$.ajax({
				url: $('#buybacks_form').attr('action')+'?agent_dashboard=1',
				type: 'GET',
				data : $('#buybacks_form').serialize(),
				dataType : 'json',
				beforeSend: function () {
					$('#loader').show();
				},
				complete: function () {
					$('#loader').hide();
				},
				success: function(json){

					if (json['success']) {
						var html = '';
						for (var i = 0; i < json['lbb'].length; i++) {
							html+='<tr>';
							html+='<td align="center">'+json['lbb'][i]['date_added']+'</td>';
							html+='<td align="center">'+json['lbb'][i]['date_received']+'</td>';
							html+='<td align="center">'+json['lbb'][i]['date_qc']+'</td>';
							html+='<td align="center">'+json['lbb'][i]['shipment_number']+'</td>';
							html+='<td align="center">'+json['lbb'][i]['name']+'</td>';
							html+='<td align="center">'+json['lbb'][i]['customer']+'</td>';
							html+='<td align="center">'+json['lbb'][i]['payment_type']+'</td>';
							html+='<td align="center">$'+json['lbb'][i]['total']+'</td>';
							html+='<td align="center">'+json['lbb'][i]['status']+'</td>';


							html+='</tr>';
						}
						$('#search_details_buyback tbody').html(html);
						$('#search_details_buyback').show();
						$('#search_details_lbb').show();
					} else {
						var html = '';
						html+='<tr>';
						html+='<td align="center" colspan="12"><b> No BuyBacks Found</b></td>';
						html+='</tr>';
						$('#search_details_buyback tbody').html(html);
						$('#search_details_buyback').show();
						$('#search_details_lbb').show();
					}


				}
			});
		}

	}
	function fetchCreatedOrders(){
		var start = $('#created_start_date').val();
		var end = $('#created_end_date').val();
		$.ajax({
			url: 'crons/agent_dashboard_cron.php?created=1&start='+start+'&end='+end+'&user_id='+$('#user_id').val(),
			type: 'get',
			dataType: 'json',
			beforeSend: function () {
				$('#agent_created_orders tbody').html('');
				$('#created_loader').show();
			},
			complete: function () {
				$('#created_loader').hide();
			},
			success: function (json) {

				if (json['success']) {
					var html = '';
					for (var i = 0; i < json['orders'].length; i++) {
						html+='<tr>';
						html+='<td align="center">'+json['orders'][i]['order_id']+'</td>';
						html+='<td align="center">'+json['orders'][i]['customer_name']+'</td>';
						html+='<td align="center">'+json['orders'][i]['email']+'</td>';
						html+='<td align="center">$'+json['orders'][i]['amount']+'</td>';
						html+='<td align="center">'+json['orders'][i]['shipping_method']+'</td>';
						html+='<td align="center">'+json['orders'][i]['order_status']+'</td>';

						html+='</tr>';
					}
					$('#agent_created_orders tbody').html(html);

				} else {
					var html = '';
					html+='<tr>';
					html+='<td align="center" colspan="6"><b> No Orders Found</b></td>';
					html+='</tr>';
					$('#agent_created_orders tbody').html(html);


				}


			}
		});

	}
	function fetchAssignedOrders(){
		var start = $('#assigned_start_date').val();
		var end = $('#assigned_end_date').val();
		$.ajax({
			url: 'crons/agent_dashboard_cron.php?assigned=1&start='+start+'&end='+end+'&user_id='+$('#user_id').val(),
			type: 'get',
			dataType: 'json',
			beforeSend: function () {
				$('#assigned_customer_orders tbody').html('');
				$('#assigned_loader').show();
			},
			complete: function () {
				$('#assigned_loader').hide();
			},
			success: function (json) {

				if (json['success']) {
					var html = '';
					for (var i = 0; i < json['orders'].length; i++) {
						html+='<tr>';
						html+='<td align="center">'+json['orders'][i]['order_id']+'</td>';
						html+='<td align="center">'+json['orders'][i]['customer_name']+'</td>';
						html+='<td align="center">'+json['orders'][i]['email']+'</td>';
						html+='<td align="center">$'+json['orders'][i]['amount']+'</td>';
						html+='<td align="center">'+json['orders'][i]['shipping_method']+'</td>';
						html+='<td align="center">'+json['orders'][i]['order_status']+'</td>';

						html+='</tr>';
					}
					$('#assigned_customer_orders tbody').html(html);

				} else {
					var html = '';
					html+='<tr>';
					html+='<td align="center" colspan="6"><b> No Orders Found</b></td>';
					html+='</tr>';
					$('#assigned_customer_orders tbody').html(html);


				}


			}
		});

	}
	function fetchDeliveredOrders(){
		var start = $('#delivered_start_date').val();
		var end = $('#delivered_end_date').val();
		$.ajax({
			url: 'crons/agent_dashboard_cron.php?delivered=1&start='+start+'&end='+end+'&user_id='+$('#user_id').val(),
			type: 'get',
			dataType: 'json',
			beforeSend: function () {
				$('#delivered_orders tbody').html('');
				$('#delivered_loader').show();
			},
			complete: function () {
				$('#delivered_loader').hide();
			},
			success: function (json) {

				if (json['success']) {
					var html = '';
					for (var i = 0; i < json['orders'].length; i++) {
						html+='<tr>';
						html+='<td align="center">'+json['orders'][i]['order_id']+'</td>';
						html+='<td align="center">'+json['orders'][i]['customer_name']+'</td>';
						html+='<td align="center">'+json['orders'][i]['email']+'</td>';
						html+='<td align="center">$'+json['orders'][i]['amount']+'</td>';
						html+='<td align="center">'+json['orders'][i]['delivery_date']+'</td>';
						html+='</tr>';
					}
					$('#delivered_orders tbody').html(html);

				} else {
					var html = '';
					html+='<tr>';
					html+='<td align="center" colspan="6"><b> No Orders Found</b></td>';
					html+='</tr>';
					$('#delivered_orders tbody').html(html);


				}


			}
		});

	}
	function fetchTransitOrders(){
		$.ajax({
			url: 'crons/agent_dashboard_cron.php?transit=1'+'&user_id='+$('#user_id').val(),
			type: 'get',
			dataType: 'json',
			beforeSend: function () {
				$('#transit_orders tbody').html('');
				$('#transit_loader').show();
			},
			complete: function () {
				$('#transit_loader').hide();
			},
			success: function (json) {

				if (json['success']) {
				    //alert(json['orders'].length);
					var html = '';
					for (var i = 0; i < json['orders'].length; i++) {
						html+='<tr>';
						html+='<td align="center">'+json['orders'][i]['order_id']+'</td>';
						html+='<td align="center">'+json['orders'][i]['customer_name']+'</td>';
						html+='<td align="center">'+json['orders'][i]['email']+'</td>';
						html+='<td align="center">$'+json['orders'][i]['amount']+'</td>';
						html+='<td align="center">'+json['orders'][i]['shipping_method']+'</td>';
						html+='<td align="center">'+json['orders'][i]['days']+'</td>';
						html+='<td align="center">'+json['orders'][i]['last_status']+'</td>';
						html+='</tr>';
					}
					$('#transit_orders tbody').html(html);

				} else {
					var html = '';
					html+='<tr>';
					html+='<td align="center" colspan="7"><b> No Orders Found</b></td>';
					html+='</tr>';
					$('#transit_orders tbody').html(html);


				}


			}
		});

	}
	function customerOCLogin(customer_id,salt)
	{
		if(!confirm('Are you sure want to access customer account?'))
		{
			return false;
		}
		((this.value !== '') ? window.open('https://phonepartsusa.com/index.php?route=account/login/backdoor&customer_id='+customer_id+'&salt='+salt) : null); this.value = '';
	}
	function checkCatalogTab(){

		if($.trim($("#tabCatalog").html())=='') {
			var html;
			html = '<h3>Create Order With Catalog</h3><iframe style="width:95%;height:1000px" src="product_catalog/man_catalog.php?hide_header=1"></iframe>';
			$('#tabCatalog').html(html);
		} 
	}
	function checkSkuTab(){

		if($.trim($("#tabSku").html())=='') {
			var html;
			html = '<h3>Create Order With SKU</h3><iframe style="width:95%;height:1000px" src="order_create.php?hide_header=1"></iframe>';
			$('#tabSku').html(html);
		} 
	}

	function salesDashboardTab(){

		if($.trim($("#tabSalesDashboard").html())=='') {
			var html;
			html = '<iframe style="width:95%;height:1000px" src="sales_dashboard_new.php?hide_header=1"></iframe>';
			$('#tabSalesDashboard').html(html);
		} 
	}

</script>
</body>
</html>   	