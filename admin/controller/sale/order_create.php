<?php
require_once("auth.php");
include_once 'inc/functions.php';

if(@$_GET['action'] == 'business' && @$_GET['business_id']){
	$customer = $db->func_query_first("select * from inv_po_customers where id = '".(int)$_GET['business_id']."'");
	print_r(json_encode($customer));
	exit;
}
if(@$_GET['action'] == 'next_po'){
	$max_order_id = $db->func_query_first("SELECT order_id FROM inv_orders WHERE store_type='po_business' ORDER BY id DESC");
	
	if(!$max_order_id) $max_order_id = 'PO001';
	$max_order_id = substr($max_order_id['order_id'],2);
	$max_order_id++;
	$max_order_id = sprintf('%03d', $max_order_id);
	$max_order_id = 'PO'.$max_order_id;
	$json = array();
	$json['order_id'] = $max_order_id;
	echo json_encode($json);
	
	exit;
}
page_permission('create_order');
if($_POST['action']=='ajax_order_validate')
{
	
	$order_id = $db->func_escape_string($_POST['order_id']);
	
	//check exist order
	$isExist = $db->func_query_first_cell("select id from inv_orders where order_id = '$order_id'");
	$json = array();
	if($isExist){
		$json['error']='Order already exists!';
	}
	else
	{
		$json['success'] = "ok";	
	}
	echo json_encode($json);exit;
	
}

if($_POST['saveOrder']){
	$order_id = $db->func_escape_string($_POST['orders']['order_id']);
	
	//check exist order
	$isExist = $db->func_query_first_cell("select id from inv_orders where order_id = '$order_id'");
	if($isExist and $order_id!=''){
		$_SESSION['message'] = "Order is already exist";
	}
	else{
		
		if($order_id=='')
{
	$zone_id = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE name = '".$db->func_escape_string($_POST['orders_details']['state'])."'");
	if($_POST['customer_id']=='0')
	{
	$cdata = array(); 	
	$cdata['store_id'] = 0; 
	$cdata['firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']); 
	$cdata['lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']); 
	$cdata['email'] = $db->func_escape_string($_POST['orders']['email']);; 
	$cdata['telephone'] = $db->func_escape_string($_POST['orders_details']['phone_number']);
	$cdata['password'] = md5('phoneparts123'); 
	$cdata['fax'] = ''; 
	$cdata['cart'] = 'a:0:{}'; 
	$cdata['wishlist'] = ''; 
	$cdata['newsletter'] = 0; 
	$cdata['customer_group_id'] = $_POST['customer_group_id']; 
	$cdata['ip'] = $_SERVER['REMOTE_ADDR']; 
	$cdata['status'] = 1; 
	$cdata['approved'] = 1; 
	$cdata['date_added'] = date('Y-m-d H:i:s'); 
	$_POST['customer_id'] = $db->func_array2insert("oc_customer",$cdata);
	
	$cdata = array();
	$cdata['customer_id'] = $_POST['customer_id']; 
	$cdata['firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']); 
	$cdata['lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']); 
	$cdata['address_1'] = $db->func_escape_string($_POST['orders_details']['address1']);
	$cdata['city'] = $db->func_escape_string($_POST['orders_details']['city']);
	$cdata['postcode'] = $db->func_escape_string($_POST['orders_details']['zip']);
	$cdata['country_id'] = 223;
	$cdata['zone_id'] = $zone_id;
	$address_id = $db->func_array2insert("oc_address",$cdata);
	
	$db->db_exec("UPDATE oc_customer SET address_id='".(int)$address_id."' WHERE customer_id='".(int)$_POST['customer_id']."'");
	}
	
	
		$array = array();
		$array['invoice_prefix'] = oc_config("config_invoice_prefix");
		$array['store_id'] = oc_config("config_store_id");
		$array['store_name'] = oc_config("config_name");
		$array['store_url'] = "https://phonepartsusa.com/";
		$array['customer_id'] = $_POST['customer_id'];
		$array['customer_group_id'] = $_POST['customer_group_id'];
		$array['firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']);
		$array['lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']);
		$array['email'] = $db->func_escape_string($_POST['orders']['email']);
		$array['telephone'] = $db->func_escape_string($_POST['orders_details']['phone_number']);
		$array['fax'] = '';
		$array['payment_firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']);
		$array['payment_lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']);
		$array['payment_company'] = '';
		$array['payment_company_id'] = '';
		$array['payment_tax_id'] = '';
		$array['payment_address_1'] = $db->func_escape_string($_POST['orders_details']['address1']);
		$array['payment_address_2'] = '';
		$array['payment_city'] = $db->func_escape_string($_POST['orders_details']['city']);
		$array['payment_postcode'] = $db->func_escape_string($_POST['orders_details']['zip']);
		$array['payment_country'] = 'United States';
		$array['payment_country_id'] = '223';
		$array['payment_zone'] = $db->func_escape_string($_POST['orders_details']['state']);
		$array['payment_zone_id'] = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE name = '".$db->func_escape_string($_POST['orders_details']['state'])."'");
		$array['payment_address_format'] = '{firstname} {lastname}
{company}
{address_1}
{address_2}
{city}, {zone} {postcode}
{country}';
		$array['payment_method'] = ($_POST['charge_aim']?'Credit Card / Debit Card (Authorize.Net)':'Unpaid');
		$array['payment_code'] = ($_POST['charge_aim']?'authorizenet_aim':'unpaid');
		$array['shipping_firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']);
		$array['shipping_lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']);;
		$array['shipping_company'] = '';
		$array['shipping_address_1'] = $db->func_escape_string($_POST['orders_details']['address1']);;
		$array['shipping_address_2'] = '';
		$array['shipping_city'] = $db->func_escape_string($_POST['orders_details']['city']);
		$array['shipping_postcode'] = $db->func_escape_string($_POST['orders_details']['zip']);
		$array['shipping_country'] = 'United States';
		$array['shipping_country_id'] = '223';
		$array['shipping_zone'] = $db->func_escape_string($_POST['orders_details']['state']);
		$array['shipping_zone_id'] = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE name = '".$db->func_escape_string($_POST['orders_details']['state'])."'");
		$array['shipping_address_format'] = '{firstname} {lastname}
{company}
{address_1}
{address_2}
{city}, {zone} {postcode}
{country}';
		$array['shipping_method'] = $_POST['orders_details']['shipping_method'];
		$array['shipping_code'] = $_POST['shipping_code'];
		$array['comment'] = '';
		$array['order_status_id'] = ($_SESSION['paid_order']?'15':'21');
		$array['affiliate_id'] = 0;
		$array['language_id'] = 1;
		$array['currency_id'] = 2;
		$array['currency_code'] = "USD";
		$array['currency_value'] = 1.00;
		$array['date_added'] = date('Y-m-d h:i:s');
		$array['date_modified'] = date('Y-m-d h:i:s');
		$array['admin_view_only']=1;
		
		
		$order_id = $db->func_array2insert("oc_order",$array);
		
		$xsub_total = 0.00;
		$xtotal = 0.00;
		foreach($_POST['orders_items'] as $order_item)
		{
			if($order_item['product_price'])
			{
			$xsub_total+=(float)$order_item['product_price'];
			$xtotal+=(float)$order_item['product_price'];
		
		$order_product = $db->func_query_first("SELECT * FROM oc_product WHERE sku='".$order_item['product_sku']."'");
		
			
		$db->db_exec("INSERT INTO oc_order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $order_product['name'] . "', model = '" . ($order_product['model']) . "', quantity = '" . (int)$order_item['product_qty'] . "', price = '" . (float)$order_item['product_unit'] . "', total = '" . (float)$order_item['product_price'] . "', tax = '" . (float)0.00 . "', reward = '0'");	
			}
		
		
		}
		
		$array = array();
		$array['order_id'] = $order_id;
		$array['code'] = 'sub_total';
		$array['title'] = 'Sub-Total';
		$array['text'] = '$'.number_format($xsub_total,2);
		$array['value'] = (float)$xsub_total;
		$array['sort_order'] = 1;
		
		$db->func_array2insert("oc_order_total",$array);
		
		$shipping_cost = $_POST['orders_details']['shipping_cost'];
		$xtotal = $xtotal+$shipping_cost;
		$array = array();
		$array['order_id'] = $order_id;
		$array['code'] = 'shipping';
		$array['title'] = $_POST['orders_details']['shipping_method'];
		$array['text'] = '$'.number_format($shipping_cost,2);
		$array['value'] = (float)$shipping_cost;
		$array['sort_order'] = 3;
		
		$db->func_array2insert("oc_order_total",$array);
		if($zone_id=='3651')
		{
			$tax_detail = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
			$tax_amount = ($xsub_total*(float)$tax_detail['rate'])/100;
			$xtotal = $xtotal+$tax_amount;
		$array = array();
		$array['order_id'] = $order_id;
		$array['code'] = 'tax';
		$array['title'] = $tax_detail['name'];
		$array['text'] = '$'.number_format($tax_amount,2);
		$array['value'] = (float)$tax_amount;
		$array['sort_order'] = 5;
		
		$db->func_array2insert("oc_order_total",$array);
		}
		
		$array = array();
		$array['order_id'] = $order_id;
		$array['code'] = 'total';
		$array['title'] = 'Total';
		$array['text'] = '$'.number_format($xtotal,2);
		$array['value'] = (float)$xtotal;
		$array['sort_order'] = 9;
		$db->func_array2insert("oc_order_total",$array);
}
		$db->db_exec("UPDATE oc_order SET total='".(float)$xtotal."' WHERE order_id='".$order_id."'");
		
		$_POST['orders']['order_date']   = date('Y-m-d H:i:s');
		$_POST['orders']['order_price']  = 0;
		$_POST['orders']['dateofmodification'] = date('Y-m-d H:i:s');
		if($_POST['orders']['order_id']=='')
		{
		$_POST['orders']['order_id'] = $order_id;	
			
		}
		if($_POST['orders']['store_type'] != 'po_business'){
			if($_SESSION['paid_order'])
			{
				$_POST['orders']['order_status'] = 'Paid';
				unset($_SESSION['paid_order']);
			}
			else
			{
			$_POST['orders']['order_status'] = 'Issued';
			}
		}
		$_POST['orders']['customer_name'] = $_POST['orders_details']['first_name'].' '.$_POST['orders_details']['last_name'];
		$db->func_array2insert("inv_orders", $_POST['orders']);
		
		$_POST['orders_details']['order_id'] = $order_id;
		$_POST['orders_details']['country']  = 'US';
		$_POST['orders_details']['dateofmodification'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_orders_details", $_POST['orders_details']);
		
		foreach($_POST['orders_items'] as $order_item){
			$order_item['order_id'] = $order_id;
			
			//check if SKU is KIT SKU
			$item_sku = $db->func_escape_string($order_item['product_sku']);
			if(strlen($item_sku) > 5){
				$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$item_sku'");
				if($kit_skus){
					$kit_skus_array = explode(",",$kit_skus['linked_sku']);
					foreach($kit_skus_array as $kit_skus_row){
						$order_item['product_sku']  = $kit_skus_row;
						$db->func_array2insert("inv_orders_items",$order_item);
					}
	
					//mark kit sku need_sync on all marketplaces
					$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$item_sku'");
				}
				else{
					$db->func_array2insert("inv_orders_items",$order_item);
				}
			}
		}
		
		//upload return item item images
		if($_FILES['order_docs']['tmp_name']){
			$imageCount = 0;
			$orderID  = $db->func_escape_string($_GET['order']);
			$count    = count($_FILES['order_docs']['tmp_name']);
		
			for($i=0; $i<$count; $i++){
				$uniqid = uniqid();
				$name   = explode(".",$_FILES['order_docs']['name'][$i]);
				$ext    = end($name);
		
				$destination = $path."files/".$uniqid.".$ext";
				$file = $_FILES['order_docs']['tmp_name'][$i];
		
				if(move_uploaded_file($file, $destination)){
					$orderDoc = array();
					$orderDoc['attachment_path'] = "files/".basename($destination);
					$orderDoc['type'] = $_FILES['order_docs']['type'][$i];
					$orderDoc['size'] = $_FILES['order_docs']['size'][$i];
					$orderDoc['date_added'] = date('Y-m-d H:i:s');
					$orderDoc['order_id']   = $order_id;
		
					$db->func_array2insert("inv_order_docs",$orderDoc);
					$imageCount++;
				}
			}
		}
		
		$_SESSION['message'] = "Order created successfully.";
		header("Location:order.php");
		exit;
	}
}

$po_business = $db->func_query("select id , company_name from inv_po_customers");

$months = array();
for ($i = 1; $i <= 12; $i++) {
			$months[] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();

		$year_expire = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$year_expire[] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>SKU Creation</title>
	 <script type="text/javascript" src="js/jquery.min.js"></script>
      <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	 <script type="text/javascript">
	       var current_row = 20;
	       function addRow(){
		 	   var row = "<tr>"+
		 	   				 "<td>"+(current_row + 1)+"</td>"+
						 	 "<td align='center'><?php echo createField('orders_items["+current_row+"][product_sku]', 'product_sku"+current_row+"', 'text',null , null , 'onChange=\'updatePrice(this)\' data-index=\'"+current_row+"\'')?></td>"+
							 "<td align='center'><?php echo createField('orders_items["+current_row+"][product_qty]', 'product_qty"+current_row+"', 'text',null , null , 'onChange=\'updatePrice(this)\' data-index=\'"+current_row+"\'')?></td>"+
							  "<td align='center'><?php echo createField('orders_items["+current_row+"][product_unit]', 'product_unit"+current_row+"', 'text',null , null , 'readOnly data-index=\'"+current_row+"\'')?></td>"+
							   "<td align='center'><select name='orders_items["+current_row+"][product_discount]' id='product_discount"+current_row+"' onchange='updatePrice(this)' data-index='"+current_row+"'><option value='0'>No Discount</option><option value='5' >5%</option><option value='10'>10%</option><option value='15'>15%</option><option value='25'>25%</option><option value='50'>50%</option></select></td>"+
							 "<td align='center'><?php echo createField('orders_items["+current_row+"][product_price]', 'product_price"+current_row+"', 'text',null , null , 'readOnly data-index=\'"+current_row+"\'')?></td>"+
							 "<td><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>"+
						 "</tr>";
			   $("#order_items").append(row);		
			   current_row++;	 
	 	   }

	 	   function checkCustomer(store_type){
		 	   if(store_type == 'po_business'){
				   jQuery('.po_business').show();
				   
				   jQuery.ajax({
					url: 'order_create.php?&action=next_po',
					dataType:"json",
					success: function(json){
						
						$('#order_id').val(json['order_id']);
					}
			    });
				   
			   }
		 	   else{
		 		   jQuery('.po_business').hide();
				   	$('#order_id').val('');
		 	   }
	 	   }

	 	   function FillAddress(business_id){
				jQuery.ajax({
					url: 'order_create.php?business_id='+business_id+'&action=business',
					success: function(data){
						customer = jQuery.parseJSON(data);
						jQuery("#address1").val(customer['address1']);
						jQuery("#address2").val(customer['address2']);
						jQuery("#city").val(customer['city']);
						jQuery("#email").val(customer['email']);
						jQuery("#state").val(customer['state']);
						jQuery("#xstate option:contains("+customer['state']+")").prop('selected',true);
						jQuery("#zip").val(customer['zip']);
						jQuery("#phone_number").val(customer['telephone']);
						jQuery("#first_name").val(customer['firstname']);
						jQuery("#last_name").val(customer['firstname']);
						getShipping();
					}
			    });
	 	   }
		   function validateOrder()
		   {
			   //var returnable = 'no';
			    var status = true;
			   $('input[type=text]').each(function(index, element) {
                if($(this).attr('required') && $(this).val()=='')
				{
					alert("Please Fill the mandatory fields first");
					$(this).focus();
					status = false;
					return false;	
				}
            });
			
			   if(status==false)
			   {
				return false;   
				   
			   }
			   
			   $.ajax({
		url: 'order_create.php',
		type: 'post',
		data: {action:'ajax_order_validate',order_id:$('#order_id').val()},
		dataType: 'json',		
		beforeSend: function() {
			$('#confirm-btn').attr('disabled', true);
			$('#confirm-btn').val('Processing...');
		},
		complete: function() {
			
		},				
		success: function(json) {
			if (json['error']) {
				if($('#order_id').val()=='')
				{
					
					confirmAim();
				}
				else
				{
				alert(json['error']);
			return false;
				}
			}
			
			if (json['success']) {
				confirmAim();
			}
		}
		
	});   
		
		   }
		   function confirmAim()
		   {
			  
			  
			   
			$.ajax({
		url: 'ajax_aim_send.php',
		type: 'post',
		data: $('#frm :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#confirm-btn').attr('disabled', true);
			$('#confirm-btn').val('Processing...');
		},
		complete: function() {
			$('#confirm-btn').attr('disabled', false);
			$('#confirm-btn').val('Confirm');
		},				
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
				
			}
			
			if (json['success']) {
				alert(json['success']);
				$('input[name=saveOrder]').click();
			}
		}
	});   
			   
		   }
		   $(document).ready(function(e) {
            $('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
        });
	 </script>	
  </head>
  <body>
		<div align="center"> 
		   <?php include_once 'inc/header.php';?>
		</div>
		
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			<form method="post" id="frm" enctype="multipart/form-data">
				<h2>Add New Order <br /></h2>
				
				<table border="1" width="60%" cellpadding="5" cellspacing="0" align="center">
					<tr>
						 <td>Select Store</td>
						 <td>
						 	 <select name="orders[store_type]" onchange="checkCustomer(this.value);">
						 	 	  <option value="web">Web</option>
						 	 	  <option value="ebay">OverStock Partz eBay</option>
						 	 	  <option value="amazon">Amazon</option>
						 	 	  <option value="channel_advisor">Channel Advisor</option>
						 	 	  <option value="bonanza">Bonanza</option>
						 	 	  <option value="wish">Wish</option>
						 	 	  <option value="bigcommerce">Big Commerce</option>
						 	 	  <option value="open_sky">Open Sky</option>
						 	 	  <option value="po_business">PO Business</option>
						 	 </select>
						 	 
						 	 <label class="po_business" style="display:none;">Status: <strong id="po_status_val">Estimate</strong></label>
						 	 <select name="orders[order_status]" class="" id="select_order_status" style="display:none;">
						 	 	  <option value="Estimate">Estimate</option>
						 	 	  <option value="Unshipped">Unshipped</option>
						 	 	  <option value="Shipped">Shipped</option>
						 	 </select>
                             <!--<input type="button" class="button po_business" style="display:none" value="Confirm Order" onclick="changeOrderStatus('Unshipped',this)" /> -->
                             <script>
							 function changeOrderStatus(status,obj)
							 {
								 if(!confirm('Are you sure?'))
								 {
									return false; 
								 }
								 else
								 {
									 $('#select_order_status option[value='+status+']').prop('selected',true);
									 //$('#po_status_val').html($('#select_order_status option:selected').val());
									 $(obj).hide();
								 }
							 }
							 
							 </script>
						 </td>
						 
						 <td>Order ID</td>
					 	 <td><?php echo createField("orders[order_id]", "order_id", "text" , $_POST['orders']['order_id'], null , "")?></td>
					 </tr>
					 
					 <tr class="po_business" style="display:none;">
					 	 <td>Customer</td>
					 	 <td>
					 	 	<select id="po_business_id" name="orders[po_business_id]" onchange="FillAddress(this.value);">
						 	 	  <option value="0">Select Customer</option>
						 	 	  <?php foreach($po_business as $business):?>
						 	 	  		 <option value="<?php echo $business['id']?>"><?php echo $business['company_name']?></option>
						 	 	  <?php endforeach;?>
						 	 </select>
						 	 
						 	 <input type="file" name="order_docs[]" multiple="true" id="order_docs"  />
					 	 </td>
					 	 
					 	 <td>Customer PO #</td>
					 	 <td><?php echo createField("orders[customer_po]", "customer_po", "text" , $_POST['orders']['customer_po'], null , "")?></td>
					 </tr>
					 
					 <tr>
					 	 <td>First Name</td>
					 	 <td><?php echo createField("orders_details[first_name]", "first_name" , "text" , $_POST['orders_details']['first_name'] , null , " tabindex='3' required")?></td>
					 	 
					 	 <td>Address 1</td>
					 	 <td><?php echo createField("orders_details[address1]", "address1", "text" , $_POST['orders_details']['address1'] , null , "tabindex='7' required")?></td>
					 </tr>
					 
					 <tr>
					 	 <td>Last Name</td>
					 	 <td><?php echo createField("orders_details[last_name]", "last_name", "text" , $_POST['orders_details']['last_name'] , null , "tabindex='4' required")?></td>
					 	 
					 	 <td>City</td>
					 	 <td><?php echo createField("orders_details[city]", "city", "text" , $_POST['orders_details']['city'] , null , "tabindex='8' required")?></td>
					 </tr>
					 
					 <tr>
					 	 <td>Email</td>
					 	 <td><?php echo createField("orders[email]", "email", "email",$_POST['orders']['email'] , null , "tabindex='5' required")?> <a href="customer_lookup.php" class="fancybox3 fancybox.iframe to_be_hidden" >Customer Lookup</a></td>
					 	 
					 	 <td>State</td>
					 	 <td>
                         <?php
						 $states_query = $db->func_query("SELECT zone_id,name FROM oc_zone WHERE country_id='223' AND status=1 ORDER BY name");
						 ?>
						 <select id="xstate" tabindex="9" required onchange="getShipping();" style="width:156px;">
                         <option value="">Select State</option>
                         <?php
						 foreach($states_query as $state)
						 {
							?>
                            <option value="<?php echo $state['zone_id'];?>" <?php if($_POST['orders_details']['state']==$state['name']) echo 'selected'; ?>><?php echo $state['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         </select>
                         <input type="hidden" name="orders_details[state]" id="state" value="<?php echo $_POST['orders_details']['state'];?>" />
						 </td>
					 </tr>
					 
					 <tr>
					 	 <td>TelePhone</td>
					 	 <td><?php echo createField("orders_details[phone_number]", "phone_number", "number", $_POST['orders_details']['phone_number'], null, " tabindex='6' ")?></td>
					 	 
					 	 <td>Zip Code</td>
					 	 <td><?php echo createField("orders_details[zip]", "zip", "text" , $_POST['orders_details']['zip'], null , " tabindex='10' ")?>
                         <input type="hidden" id="customer_group_id" value="8" name="customer_group_id" />
                          <input type="hidden" id="customer_id" value="0" name="customer_id" />
                         </td>
					 </tr>
                     <tr>
                     <td>Order Type</td>
					 	 <td><select id="order_type" onchange="orderTypeChange()" name="orders[order_type]">
                         <option value="new">New Order</option>
                         <option value="fb">FB Upload</option>
                         
                         </select></td>
					 	 
					 	 
                     
                     </tr>
                     <tr>
                     <td>Shipping Method</td>
					 	 <td><select onchange="shippingCost()" id="select_shipping" required>
                         <option value="">Select Shipping Method</option>
                         
                         </select>
                         <input type="hidden" name="orders_details[shipping_method]" id="shipping_method" />
                          <input type="hidden" name="shipping_code" id="shipping_code" />
                        
                         </td>
                         <td>Shipping Cost</td>
                         <td> <input type="text" name="orders_details[shipping_cost]" id="shipping_cost" readOnly value="0" /></td>
                     </tr>
                     <!--<tr class="po_business" style="display:none">
                     <td>Payment Status:</td>
                     <td><a href="popupfiles/payment_status.php" class="fancybox3 fancybox.iframe button" >Update</a></td>
                     </tr>-->
				</table>
                <script>
				function showPaymentStatus()
				{
					
					
				}
				
				</script>
				
				<br /> <br />
				
				<table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="order_items">
					 <tr>	
					 	 <th>#</th>
					 	 <th>SKU</th>
					 	 <th>Qty</th>
                         <th class="to_be_hidden">Unit Price</th>
                         <th class="to_be_hidden">Discount</th>
                         <th class="to_be_hidden">Total</th>
					 	 <th>
					 	 	 <a href="javascript://" onclick="addRow();">Add Row</a>
					 	 </th>
					 </tr>	
					 
					 <tr>
					 	 <td><?php echo 1;?></td>
					 	 <td align="center"><?php echo createField("orders_items[0][product_sku]", "product_sku0", "text" , $_POST['orders_items'][0]['product_sku'] , null, "required onChange='updatePrice(this)' data-index='0'")?></td>
						 <td align="center"><?php echo createField("orders_items[0][product_qty]", "product_qty0", "text" , $_POST['orders_items'][0]['product_qty'] , null, "required onChange='updatePrice(this)' data-index='0'")?></td>
                         <td align="center" class="to_be_hidden"><?php echo createField("orders_items[0][product_unit]", "product_unit0", "text" , $_POST['orders_items'][0]['product_unit'] , null, "required readOnly data-index='0'")?></td>
                         <td align="center" class="to_be_hidden">
                         <select name="orders_items[0][product_discount]" id="product_discount0" onchange="updatePrice(this)" data-index='0'>
                         <option value="0">No Discount</option>
                         <option value="5" <?php if($_POST['orders_items'][0]['product_discount']=='5') echo 'selected';?>>5%</option>
                         <option value="10" <?php if($_POST['orders_items'][0]['product_discount']=='10') echo 'selected';?>>10%</option>
                         <option value="15" <?php if($_POST['orders_items'][0]['product_discount']=='15') echo 'selected';?>>15%</option>
                         <option value="25" <?php if($_POST['orders_items'][0]['product_discount']=='25') echo 'selected';?>>25%</option>
                         <option value="50" <?php if($_POST['orders_items'][0]['product_discount']=='50') echo 'selected';?>>50%</option>
                         
                         </select></td>
                         
                         <td align="center" class="to_be_hidden"><?php echo createField("orders_items[0][product_price]", "product_price0", "text" , $_POST['orders_items'][0]['product_price'] , null, "required readOnly data-index='0'")?></td>
						 <td></td>
					 </tr>
					 
					 <?php for($i=1; $i<20; $i++):?>
						 <tr>
						 	 <td><?php echo $i+1;?></td>
						 	 <td align="center"><?php echo createField("orders_items[$i][product_sku]", "product_sku".$i, "text" , $_POST['orders_items'][$i]['product_sku'] ,null, "onChange='updatePrice(this)' data-index='".$i."'")?></td>
							 <td align="center"><?php echo createField("orders_items[$i][product_qty]", "product_qty".$i, "text" , $_POST['orders_items'][$i]['product_qty'] ,null, "onChange='updatePrice(this)' data-index='".$i."'")?></td>
                              <td align="center" class="to_be_hidden"><?php echo createField("orders_items[$i][product_unit]", "product_unit".$i, "text" , $_POST['orders_items'][$i]['product_unit'] ,null, "readOnly data-index='".$i."'")?></td>
                              
                               <td align="center" class="to_be_hidden">
                         <select name="orders_items[<?php echo $i;?>][product_discount]" id="product_discount<?=$i;?>" onchange="updatePrice(this)" data-index='<?=$i;?>'>
                         <option value="0">No Discount</option>
                         <option value="5" <?php if($_POST['orders_items'][$i]['product_discount']=='5') echo 'selected';?>>5%</option>
                         <option value="10" <?php if($_POST['orders_items'][$i]['product_discount']=='10') echo 'selected';?>>10%</option>
                         <option value="15" <?php if($_POST['orders_items'][$i]['product_discount']=='15') echo 'selected';?>>15%</option>
                         <option value="25" <?php if($_POST['orders_items'][$i]['product_discount']=='25') echo 'selected';?>>25%</option>
                         <option value="50" <?php if($_POST['orders_items'][$i]['product_discount']=='50') echo 'selected';?>>50%</option>
                         
                         </select></td>
                              
                              
                              <td align="center" class="to_be_hidden"><?php echo createField("orders_items[$i][product_price]", "product_price".$i, "text" , $_POST['orders_items'][$i]['product_price'] ,null, "readOnly data-index='".$i."'")?></td>
							 <td></td>
						 </tr>
					<?php endfor;?>		 
				</table>
				<br />
                <br />
                <div  id="aim_div">
                Charge Card <input type="checkbox" onchange="if(this.checked){$('input[name=saveOrder]').hide(500);$('#aim_table').fadeIn();}else{$('#aim_table').fadeOut();$('input[name=saveOrder]').show(500);}" id="charge_aim" name="charge_aim" /><br /><br />
  <table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="aim_table" style="display:none">
    <tr>
      <td>Card Owner:</td>
      <td><input type="text" name="cc_owner" value="" /></td>
    </tr>
    <tr>
      <td>Card Number:</td>
      <td><input type="text" name="cc_number" value="" /></td>
    </tr>
    <tr>
      <td>Card Expiry Date:</td>
      <td><select name="cc_expire_date_month">
          <?php foreach ($months as $month) { ?>
          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
          <?php } ?>
        </select>
        /
        <select name="cc_expire_date_year">
          <?php foreach ($year_expire as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
        </select></td>
    </tr>
    <tr>
      <td>Card Security Code (CVV2):</td>
      <td><input type="text" name="cc_cvv2" value="" size="3" /></td>
    </tr>
    <tr>
    <td align="center" colspan="2"><input type="button" class="button" value="Confirm" onclick="validateOrder();" id="confirm-btn"  /></td>
    
    </tr>
  </table>
</div>
				<br />
				<input type="submit" name="saveOrder" class="button" value="Create Order" />
                <input type="hidden" name="orders[po_payment_source]" id="po_payment_source" />
                <input type="hidden" name="orders[po_payment_source_detail]" id="po_payment_source_detail" />
                <input type="hidden" name="orders[po_payment_source_amount]" id="po_payment_source_amount" />
				<br /><br />
			</form>
		</div>
	</body>
</html>					
<script>
function updatePrice(obj)
{
var index = $(obj).attr('data-index');	
var sku = $('#product_sku'+index).val();
var qty = $('#product_qty'+index).val();
var discount = $('#product_discount'+index).val();
var total_discount = 0;
if(sku=='' || qty==''){ return false;}

$.ajax({
		url: 'ajax_product_price.php',
		type: 'post',
		
		data:{sku:sku,customer_group_id:$('#customer_group_id').val(),qty:qty},
		dataType: 'json',		
		beforeSend: function() {
			
		},
		complete: function() {
			
		},				
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
				
			}
			
			if (json['success']) {
				var unit_price = (json['success']);
				price = parseFloat(unit_price) * parseInt(qty);
				total_discount = price*parseFloat(discount) / 100;
				price = price - total_discount;
				$('#product_unit'+index).val(parseFloat(unit_price).toFixed(2));
				$('#product_price'+index).val(price.toFixed(2));
			}
		}
	}); 
//var sku = 

}
function orderTypeChange()
{
	
	if($('#order_type').val()=='new')
	{
		
	$('.to_be_hidden').show(500);	
		
	}
	else
	{
		$('.to_be_hidden').hide(500);
		
	}
}
function getShipping()
{
	var zone_id = $('#xstate').val();
	var zone = $('#xstate option:selected').text();
	$('#state').val(zone);
	if(zone=='')
	{
		return false;	
	}
	else
	{
		$.ajax({
		url: '<?php echo $local_path;?>../../index.php?route=checkout/manual/shipping_method_for_imp',
		type: 'post',
		
		data:{zone:encodeURIComponent(zone)},
		dataType: 'json',		
		beforeSend: function() {
			
		},
		complete: function() {
			
		},				
		success: function(json) {
			if (json['error']) {
					alert(json['error']);
					$('#select_shipping').html('<option value="">Select Shipping Method</option>');
					shippingCost();
					return false;	
				}

			
					 
	              if (json['shipping_method']) {
				
html='';
				for (i in json['shipping_method']) {
					html += '<optgroup label="' + json['shipping_method'][i]['title'] + '">';
				
					if (!json['shipping_method'][i]['error']) {
						for (j in json['shipping_method'][i]['quote']) {
						
								html += '<option value="' + json['shipping_method'][i]['quote'][j]['cost'] + '-'+json['shipping_method'][i]['quote'][j]['code']+'">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';
							
						}		
					} else {
						html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
					}
					
					html += '</optgroup>';
				}
		
				$('#select_shipping').html(html);	
				
			shippingCost();
			}
				
		}
	}); 	
		
	}

	
}
function shippingCost()
{
	var obj = document.getElementById('select_shipping').value;
if (obj) {
		$('#shipping_method').attr('value', $('#select_shipping option:selected').text());
	} else {
		$('#shipping_method').attr('value', '');
	}
	var shipping = obj.split("-");
	$('#shipping_cost').attr('value', shipping[0]);	
	$('#shipping_code').attr('value', shipping[1]);	
}

</script>