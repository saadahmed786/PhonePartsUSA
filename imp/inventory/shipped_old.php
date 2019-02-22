<?php

require_once("../auth.php");
require_once("../inc/functions.php");
require_once("class.php");
include_once '../inc/split_page_results.php';
// error_reporting(E_ERROR | E_PARSE);
// ini_set('display_errors', 1);
$pageName = 'Label Reprinting';
$pageLink = 'shipped.php';
$pageCreateLink = false;
$pageSetting = false;

$carriers = $inventory->listCarriers();
$carriers = json_decode($carriers,true);
if(isset($_POST['type']) && $_POST['type']=='ajax')
{
	if(isset($_POST['action']) && $_POST['action']=='void_label')
	{
		$order_id = $_POST['order_id'];
		$label_id = $db->func_query_first_cell("SELECT label_id FROM inv_label_data WHERE order_id='".$order_id."' and voided=0 order by id desc limit 1");	
		// echo $label_id.'a';exit;
		$respnonse = $inventory->voidLabel($label_id);
		$respnonse = json_decode($response,true);
		// print_r($response);exit;
		$json = array();
		if($response['approved']==true){
			$json['success'] = $response['message'];

		}
		else
		{
			$json['error'] = 1;
		}
		echo json_encode(($json));

		}

		if(isset($_POST['action']) && $_POST['action']=='reprint_label')
	{
		$order_id = $_POST['order_id'];
		$href = $db->func_query_first_cell("SELECT label_download FROM inv_label_data WHERE order_id='".$order_id."' and voided=0 order by id desc limit 1");	
		// echo $label_id.'a';exit;
		
		// print_r($response);exit;
		$json = array();
		if($href){
			$json['success'] = $href;

		}
		else
		{
			$json['error'] = 1;
		}
		echo json_encode(($json));

		}
		exit;
	}

$processedOrders = $inventory->getShippedOrders();
// print_r($processedOrders);exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<link rel="stylesheet" type="text/css" href="../include/xtable.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>../catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">
	
		<style type="text/css" media="screen">
		.float{
	position:fixed;
	width:60px;
	height:60px;
	bottom:40px;
	right:40px;
	background-color:#0C9;
	color:#FFF;
	border-radius:50px;
	text-align:center;
	box-shadow: 2px 2px 3px #999;
}
.float:hover{
	color:#FFF;
}

.my-float{
	margin-top:22px;
}

		.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th
		{
			padding:4px;
		}

		.form-control{
			font-size:11px;
			height:25px;
			padding:2px 5px;
		}
		.form-group
		{
			margin-bottom:0px;
		}
		.panel-group .panel {
			border-radius: 0;
			box-shadow: none;
			border-color: #EEEEEE;
		}

		.panel-default > .panel-heading {
			padding: 0;
			border-radius: 0;
			color: #212121;
			background-color: #FAFAFA;
			border-color: #EEEEEE;
		}

		.panel-title {
			font-size: 12px;
			font-weight: bold;
		}

		.panel-title > a {
			display: block;
			padding: 10px;
			text-decoration: none;
			text-align: left;
		}

		.more-less {
			float: right;
			color: #212121;
		}

		.panel-default > .panel-heading + .panel-collapse > .panel-body {
			border-top-color: #EEEEEE;
			padding:4px;
		}

		body{
			/*background-color: #bdc3c7;*/
		}
		.table tr td{
			border-top: none !important;
		}
		.table-fixed{
			width: 100%;
			/*background-color: #f3f3f3;*/
		}
		.table-fixed tbody{
			height:200px;
			overflow-y:auto;
			width: 100%;
		}
		.table-fixed thead,tbody,tr,td,th{
			/*display:block;*/
		}
		.table tbody td{
			float:left;  
		}
		.table-fixed thead tr th {
			float:left;
			background-color: #f39c12;
			border-color:#e67e22;
		}
		.grade {font-size: 12px;}
		.grade input[type=checkbox] {margin-top: 0;}
		#cart {display: block; position: absolute; z-index: 100; background: rgb(255, 255, 255) none repeat scroll 0% 0%; padding: 10px; border: 1px solid rgb(221, 221, 221); border-radius: 10px; width: 100%;}
		#cart .cart-item{font-size: 10px; min-height: 60px;}
		.table-bordered.customColor > tbody > tr > td {border: 1px solid #999; border-left: 0; border-right: 0; padding:0;}
		.list-group-item {padding: 2px 2px 2px 18px; border-width: 0; border-bottom-width: 2px;}
		.well {background: rgb(250, 250, 250) none repeat scroll 0% 0%; min-height: 250px;}
		#MainMenu {max-height: 250px; overflow:hidden;}
		#loadManufacturers {max-height: 331px; min-height: 331px; overflow-x:hidden;}
		#loadOnHold {max-height: 331px; min-height: 331px; overflow-x:hidden;}
		#loadModels {max-height: 210px; min-height: 210px;  overflow-x:hidden;}
		#loadSubModels {max-height: 210px; min-height: 210px; overflow-x:hidden;}
		#MainMenu .list-group { border-left: 1px solid #ccc; border-radius: 0;}
		#MainMenu .list-group:first-child {border-left: 0;}
		.col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 { padding-left: 5px; padding-right: 5px;}
		.row {margin-right: -5px; margin-left: -5px;}
		.containProduct {border: 1px solid #ccc; box-shadow: 0px 0px 1px 1px #ccc; border-radius: 10px; padding: 10px; margin-bottom: 10px;}
		.product h4 { min-height: 60px;}
		.disableDiv {position: absolute; top: 0; left: 0; width: 100%; height: 100%; text-align: center; background: rgba(0, 0, 0, 0.5);}
		.disableDiv .editLayer {padding: 10px; line-height: 30px; width: 50px; height: 50px; position: relative; top: 50%; transform: translate(0, -50%); color: #000; font-size: 20px; border-radius: 100%; cursor: pointer; background-color: rgba(255, 255, 255, 1); display: inline-block;}
		.disableDiv .editLayer:hover {color: #286090;}
		.disableDiv .sign {color: #286090; position: absolute; right: 20px; top: 50%; line-height: 12px; font-size: 12px; padding: 5px; width: 25px; height: 25px; transform: translate(0px, -50%); background-color: rgba(255, 255, 255, 1); border-radius: 100%;}
		/*.highlight td{background-color:#000;color:#FFF;}*/
		#xcontent{width: 100%;
			height: 100%;
			top: 0px;
			left: 0px;
			position: fixed;
			display: block;
			opacity: 0.8;
			background-color: #000;
			z-index: 99;}
		</style>
		<style>
			#interactive.viewport {position: relative; width: 100%; height: auto; overflow: hidden; text-align: center;}
			#interactive.viewport > canvas, #interactive.viewport > video {max-width: 100%;width: 100%;}
			canvas.drawing, canvas.drawingBuffer {position: absolute; left: 0; top: 0;}
		</style>

</head>
<body>
<div id="xcontent" style="display:none"><div style="color:#fff;
			top:40%;
			position:fixed;
			left:40%;
			font-weight:bold;font-size:25px"><img src="https://phonepartsusa.com/catalog/view/theme/default/image/loader_white.gif" /><span style="margin-left: 11%;
			margin-top: 33%;
			position: absolute;

			width: 201px;">Please wait...</span></div></div>  
	<div align="center">
		<div align="center"> 
			<?php include_once '../inc/header.php';?>
		</div>
		<?php if ($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red">
				<?php
				echo $_SESSION['message'];
				unset($_SESSION['message']);
				?>
				<br />
			</font>
		</div>
		<?php } ?>
		<h2 style="font-size:15px"> <?= $pageName; ?>:: All Orders <a href="#" id="reload">(reload)</a></h2>
		<div class="row" style="margin-bottom:10px;text-align:left;">
		<div class="col-md-12" style="margin-left:7px">
		<button class="btn btn-danger btn-sm disabled" id="void_label">Void</button>
		<button class="btn btn-success btn-sm disabled" id="reprint_label">Re-Print Label</button>
		</div>
		</div>
			<div class="row">
			<div class="col-md-8" style="height:550px;overflow: scroll">
			<table  class="xtable"   align="center" style="width:98%;margin-top: 0px;">
				<thead>
					<tr>
						<th  width="13%"  align="center">Order Date</th>
						<th width="11%" align="center">Order #</th>

						<th width="14%" align="center">Recipient</th>

						<th width="10%" align="center">Item SKU</th>
						<th width="20%" align="center">Item Name</th>
						<th width="11%" align="center">Service</th>
						<th width="11%" align="center">Order Total</th>
						
						<th width="6%" align="center">Qty</th>
						<th width="6%" align="center">Shipping Paid</th>

					</tr>
				</thead>
				<tbody>
					<?php
						foreach($processedOrders as $row)
						{
								// print_r($row);exit;

					?>
					<tr data-order="<?php echo $row['order_id'];?>">
							<td><?php echo ($row['order_date']);?></td>
							<td><?php echo linkToOrder($row['order_id'],$host_path,'target="_blank"');?></td>
							<td><?php echo $row['customer_name'];?></td>
							<td><?php echo (count($row['items'])>1?'(Multiple Items)':linkToProduct($row['items'][0]['sku'],$host_path,'target="_blank"',''));?></td>
							<td><?php echo (count($row['items'])>1?'(Multiple Items)':$row['items'][0]['name']);?></td>
							<td><?php echo $row['shipping_method'];?></td>
							<td><?php echo $row['order_price']; ?></td>
							<td style="<?php echo ($row['all_quantity']>1?'font-size:13px;font-weight:bold':'');?>"><?php echo $row['all_quantity'];?></td>
							<td><?php echo $row['shipping_cost'];?></td>
					</tr>

					<?php
				}
				?>
				</tbody>
				
			</table>
			</div>
			<div class="col-md-4 panel list-group" style="height:550px;overflow: scroll">
						<div class="row">
							<div class="col-md-12" style="">
									<div style="text-align: left;font-weight: bold; margin-bottom: 5px; background-color: #FAFAFA; border-color: #EEEEEE; padding: 3px;">ORDER # <span data-map="order_id">N/A</span></div> <div class="panel-group" id="accordion"  style="font-size:12px">

									<div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingOne">
											<h4 class="panel-title">
												<a role="button" data-toggle="collapse"  href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
													<i class="more-less glyphicon glyphicon-minus"></i>
													SHIPPING
												</a>
											</h4>
										</div>
										<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
											<div class="panel-body">
												<table class="table" >
													<tr>
														<td class="col-md-2 text-right">Requested</td>
														<td style="color:green" class="col-md-10" data-map="shipping_method" >N/A</td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Ship From</td>
														<td class="col-md-10" style="color:green">Las Vegas, NV.</td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Weight</td>
														<td class="col-md-10" >
															<div class="col-md-3" style="float:left">
																<input type="number" class="form-control form-control-md col-md-3" id="weight_lb" onkeyup="changeWeight(this);" data-attr="lb"   placeholder="(lb)" > 
															</div>
															<div class="col-md-3" style="float:left">
																<input type="number" class="form-control form-control-md col-md-3" id="weight_oz" onkeyup="changeWeight(this);" data-attr="oz"  placeholder="(oz)" > 
															</div>
															<div class="col-md-6"></div>
														</td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Service</td>
														<td class="col-md-10"><div class="form-group">

															<select class="form-control" id="service" >
																<option value="">Please Select</option>
																<?php
																foreach($carriers['carriers'] as $carrier)
																{
																	?>
																	<optgroup label="<?php echo $carrier['friendly_name'];?>">
																	<?php
																	foreach($carrier['services'] as $service)
																	{
																	?>
																	<option value="<?php echo $carrier['carrier_id'];?>~<?php echo $service['service_code'];?>"><?php echo utf8_decode($service['name']);?></option>

																	<?php
																}
																?>
																</optgroup>
																<?php
																}
																?>
															</select>
														</div></td>
													</tr>

													<tr>
														<td class="col-md-2 text-right">Package</td>
														<td class="col-md-10"><div class="form-group">

															<select class="form-control" id="package" >
																<option value="">Please Select</option>
															</select>
														</div></td>
													</tr>

													<tr>
														<td class="col-md-2 text-right">Size</td>
														<td class="col-md-10" >

															<input type="number" class="form-control form-control-md col-md-3" id="size_length"  placeholder="L" style="width:25% !important" > 





															<input type="number" class="form-control form-control-md col-md-2" id="size_width" placeholder="W" style="width:25% !important;margin-left:5px;"  > 


															<input type="number" class="form-control form-control-md col-md-2" id="size_height" placeholder="H" style="width:25% !important;margin-left:5px"  > 


														</td>

													</tr>

													<tr>
														<td class="col-md-2 text-right">Confirm</td>
														<td class="col-md-10"><div class="form-group">

															<select class="form-control" id="confimration" >
																<option value="none">Service Default</option>
																<option value="delivery">No Signature Required</option>
																<option value="signature">Indirect Signature Required</option>
																<option value="adult_signature">Adult Signature Required</option>
																<option value="direct_signature">Direct Signature Required</option>
															</select>
														</div></td>
													</tr>

													<tr>
														<td class="col-md-2 text-right">Insurance</td>
														<td class="col-md-10">
															<input type="number" class="form-control form-control-md col-md-3" id="insured_amount" placeholder="Leave Blank if not Insured" style="width:25% !important" value="0.00" > 

														</td>
													</tr>

													<tr>
														<td class="col-md-2 text-right">Delivery Time</td>
														<td style="color:green" class="col-md-10" id="delivery_time">N/A</td>
													</tr>
													<tr>
														<td class="col-md-2 text-right">Rate</td>
														<td style="color:green" class="col-md-10"><span style="font-size:14px;font-weight: bold;color:green" id="estimate_rate">$0.00</span><i class="fa fa-refresh" id="get_rate" style="margin-left: 5px; border: 1px solid #DCDCDC; padding: 4px; border-radius: 5px;cursor: pointer;"></i></td> </tr>



												</table>
											</div>
										</div>

										<div class="panel-heading" role="tab" id="headingTwo">
											<h4 class="panel-title">
												<a role="button" data-toggle="collapse"  href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
													<i class="more-less glyphicon glyphicon-plus"></i>
													ITEM(S) ORDERED
												</a>
											</h4>
										</div>
										<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
											<div class="panel-body">
												


											</div>
										</div>

										<div class="panel-heading" role="tab" id="headingThree">
											<h4 class="panel-title">
												<a role="button" data-toggle="collapse"  href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
													<i class="more-less glyphicon glyphicon-plus"></i>
													CUSTOMER
												</a>
											</h4>
										</div>
										<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
											<div class="panel-body">
												<div class="row" style="">
													<div class="col-md-2" style="font-weight: bold;padding-top:2.55%">Sold To</div>
													<div class="col-md-4"><span data-map="customer_name"></span><br>
														<span data-map="telephone"></span><br>
														<span data-map="email"></span></div>
														<div class="col-md-2" style="font-weight: bold;padding-top:2.55%">Ship To</div>
														<div class="col-md-4"><span data-map="shipping_name"></span><br>
															<span data-map="address1"></span><br>
															<span data-map="city"></span>, <span data-map="state"></span> <span data-map="zip"></span> <span data-map="country"></span><br> 
															</div>
														</div>
													</div>
												</div>

												<div class="panel-heading" role="tab" id="headingFour">
													<h4 class="panel-title">
														<a role="button" data-toggle="collapse"  href="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
															<i class="more-less glyphicon glyphicon-plus"></i>
															SUMMARY
														</a>
													</h4>
												</div>
												<div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
													<div class="panel-body">
														<div class="row">
															<div class="col-md-2" style="font-weight:bold">Order Date</div>
															<div class="col-md-4" data-map="order_date"></div>
															<div class="col-md-2" style="font-weight:bold">Product Total</div>
															<div class="col-md-4" data-map="product_total">$0.00</div>

														</div>
														<div class="row">
															<div class="col-md-2" style="font-weight:bold">Ship By</div>
															<div class="col-md-4"></div>
															<div class="col-md-2" style="font-weight:bold">Shipping Paid</div>
															<div class="col-md-4" data-map="shipping_cost">$0.00</div>
														</div>
														
														<div class="row">
														<div class="col-md-2" style="font-weight:bold"></div>
															<div class="col-md-4"></div>
														
															<div class="col-md-2" style="font-weight:bold">Total Order</div>
															<div class="col-md-4" data-map="order_price">$0.00</div>
														</div>
														<div class="row">
															<div class="col-md-2" style="font-weight:bold"></div>
															<div class="col-md-4"></div>
															<div class="col-md-2" style="font-weight:bold">Total Paid</div>
															<div class="col-md-4" data-map="paid_price">$0.00</div>
														</div>
													</div>

												</div>
												<button class="btn btn-primary" id="create_label">Regenerate Label</button>
											</div>
										</div>
									</div>
								</div>

							</div>

			</div>


			<br />
		</div>
	</body>
	<script>
		$('.xtable tr td').on('click',function(e){
				$('.highlight').removeClass('highlight');
				$(this).parent().addClass('highlight');
				// alert($(this).parent().attr('data-order'));
				loadOrder($(this).parent().attr('data-order'));
		});
		
		$('#get_rate').on('click', function(e) {
			if($('#service').val()=='')
			{
				alert('Please select the Service');
				return false;
			}
			else if(parseFloat($('#weight_oz').val())=='0.00' || $('#weight_oz').val()=='' )
			{
				alert('Please provide weight');
				return false;
			}
			else if($('#package').val()=='' )
			{
				alert('Please select the package type');
				return false;
			}

			$.ajax({
				url: 'label.php',
				type: 'post',
				data: {type:'ajax',action:'get_rate',service:$('#service').val(),order_id:$('[data-map=order_id]').html(),weight:$('#weight_oz').val(),package:$('#package').val()},
				dataType: 'json',
				beforeSend: function() {
					$('#get_rate').addClass('disabled');
					$('#estimate_rate').html('Loading...');
					// $('#xcontent').show();
				// $('#button-guest').attr('disabled', true);
				// $('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				// $('#button-guest').attr('disabled', false); 
				// $('.wait').remove();
				// $('#xcontent').hide();
				$('#get_rate').removeClass('disabled');
			},			
			success: function(json) {
				if(json['rate']=='$0.00')
				{
						alert("Alert: Problem getting the Rate, please check your inputs and try again");
						$('#estimate_rate').html('#Error');

						return false;
				}
				else
				{
						$('#estimate_rate').html(json['rate']);
				$('#delivery_time').html(json['estimate_delivery_time']);	
				}
				
				
				
			}
		});			



		});
			$('#service').on('change',function(e){
					if($(this).val()=='')
					{
						alert('Please select the Service');
						return false;
					}

					$.ajax({
				url: 'label.php',
				type: 'post',
				data: {type:'ajax',action:'get_packages',service:$('#service').val()},
				dataType: 'json',
				beforeSend: function() {
					$('#create_label').addClass('disabled');
					// $('#estimate_rate').html('Loading...');
					// $('#xcontent').show();
				// $('#button-guest').attr('disabled', true);
				// $('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				// $('#button-guest').attr('disabled', false); 
				// $('.wait').remove();
				// $('#xcontent').hide();
				$('#create_label').removeClass('disabled');
			},			
			success: function(json) {
				if(json['errors'])
				{
					alert(json['errors']['message']);
					return false;
				}
				var html='';
				for(i = 0; i <  json['packages'].length; ++i)
				{
	
					html+='<option value="'+json['packages'][i]['package_code']+'" '+(json['packages'][i]['package_code']=='package'?' selected':'')+'>'+json['packages'][i]['name']+'</option>';
				
				

				}
				// $('#collapseTwo .panel-body').html(html);  
				$('#package').html(html);
				
				
			}
		});		
			});
		$('#create_label').on('click', function(e) {
			if($('#service').val()=='')
			{
				alert('Please select the Service');
				return false;
			}
			else if(parseFloat($('#weight_oz').val())=='0.00' || $('#weight_oz').val()=='' )
			{
				alert('Please provide weight');
				return false;
			}
			else if($('#package').val()=='')
			{
				alert('Please select the Package type');
				return false;
			}

			$.ajax({
				url: 'label.php',
				type: 'post',
				data: {type:'ajax',action:'create_label',service:$('#service').val(),order_id:$('[data-map=order_id]').html(),weight:$('#weight_oz').val(),confimration:$('#confimration').val(),insured_amount:$('#insured_amount').val(),package:$('#package').val()},
				dataType: 'json',
				beforeSend: function() {
					$('#create_label').addClass('disabled');
					// $('#estimate_rate').html('Loading...');
					$('#xcontent').show();
				// $('#button-guest').attr('disabled', true);
				// $('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				// $('#button-guest').attr('disabled', false); 
				// $('.wait').remove();
				//$('#xcontent').hide();
				$('#create_label').removeClass('disabled');
			},			
			success: function(json) {
				
				if(json['success'])
				{
					// $('#xcontent').show();
					window.location=json['success'];

					setTimeout(
    					function() {
     								$('#reload').trigger('click');
    								}, 3000);
					
					// location.reload(true);
					//var win = window.open(json['success'], '_blank');
  						// win.focus();
				}
				else
				{
					alert(json['error']);
					$('#xcontent').hide();
					return false;
				}
				
				
			}
		});			



		});
		$('#reload').on('click',function(e){
				location.reload(true);
		});




		function loadOrders()
		{
			$.ajax({
				url: 'label.php',
				type: 'post',
				data: {type:'ajax',action:'load_orders'},
				dataType: 'json',
				beforeSend: function() {
					// $('#xcontent').show();
				// $('#button-guest').attr('disabled', true);
				// $('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				// $('#button-guest').attr('disabled', false); 
				// $('.wait').remove();
				// $('#xcontent').hide();
			},			
			success: function(json) {
				$('#loadManufacturers .row').html(json['processedOrders']);
				
				
			}
		});


		}

		function loadOrder(order_id)
		{
			var disableDiv = '<div class="disableDiv"><span class="editLayer" onclick="$(this).parent().remove();"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></span><span class="sign"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></span></div>';
			$('#loadManufacturers').append(disableDiv);
			

			$.ajax({
				url: 'label.php',
				type: 'post',
				data: {type:'ajax',action:'load_order',order_id:order_id},
				dataType: 'json',
				beforeSend: function() {
					$('#xcontent').show();

				// $('#button-guest').attr('disabled', true);
				// $('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				// $('#button-guest').attr('disabled', false); 
				// $('.wait').remove();
				$('#xcontent').hide();
				$('#void_label').removeClass('disabled');
				$('#reprint_label').removeClass('disabled');
			},			
			success: function(json) {
				
				$.each(json, function(key, value) {  
					var ctrl = $('[data-map='+key+']');  
					switch(ctrl.prop("type")) { 
						case "text": case "select":   
						ctrl.val(value);

						break;  
						default:
						ctrl.html(value); 
					}  
				});
				var html='';
				for(i = 0; i <  json['items'].length; ++i)
				{
	
					html+='<div class="row" style="height:80px">';
					html+='<div class="col-md-2">  <img src="'+json['items'][i]['image']+'" style="height:100%"></div>';
					html+='<div class="col-md-6" style="text-align:left">';
					html+='<a href="#">'+json['items'][i]['name']+'</a><br><span style="color:#909090">SKU: '+json['items'][i]['sku']+'</span><br><span>Total: <span style="color:green">'+json['items'][i]['product_total']+'</span></span></div>';
					html+='<div class="col-md-3 text-center" style="font-size:21px;font-weight:bold;padding-top:28px;'+(json['items'][i]['quantity']>1?'color:white;background-color:red;':'color:black;background-color:white;')+'">'+json['items'][i]['quantity']+'</div></div><hr />';

				

				}
				$('#collapseTwo .panel-body').html(html);  
			}

		});
		}

		function changeWeight(obj)
		{
			var conversion = 'oz';
			var weight_oz = 0.0000;
			var weight_lb = 0.0000;
			if($(obj).attr('data-attr')=='oz')
			{
				var conversion = 'lb';
			}


			if(conversion =='oz')
			{
				var weight_lb = $('#weight_lb').val();

				if(weight_lb=='') weight_lb = 0.0000;


				weight_oz = parseFloat(weight_lb) * 16;
				weight_oz = weight_oz.toFixed(4);
				$('#weight_oz').val(weight_oz);
			}
			else
			{
				var weight_oz = $('#weight_oz').val();
				if(weight_oz=='') weight_oz = 0.0000;
				weight_lb = parseFloat(weight_oz) / 16;
				weight_lb = weight_lb.toFixed(4);
				$('#weight_lb').val(weight_lb);
			}
		}
		$('#void_label').on('click',function(e){
			$.ajax({
				url: 'shipped.php',
				type: 'post',
				data: {type:'ajax',action:'void_label',order_id:$('[data-map=order_id]').html()},
				dataType: 'json',
				beforeSend: function() {
					// $('#get_rate').addClass('disabled');
					// $('#estimate_rate').html('Loading...');
					$('#xcontent').show();
				// $('#button-guest').attr('disabled', true);
				// $('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				// $('#button-guest').attr('disabled', false); 
				// $('.wait').remove();
				$('#xcontent').hide();
				// $('#get_rate').removeClass('disabled');
			},			
			success: function(json) {
				if(json['error'])
				{
						alert("Alert: Problem voiding the label, please try again");
						// $('#estimate_rate').html('#Error');

						return false;
				}
				else
				{
						alert(json['success']);
						location.reload(true);
						return false;
				}
				
				
				
			}
		});		
		});

		$('#reprint_label').on('click',function(e){
			$.ajax({
				url: 'shipped.php',
				type: 'post',
				data: {type:'ajax',action:'reprint_label',order_id:$('[data-map=order_id]').html()},
				dataType: 'json',
				beforeSend: function() {
					// $('#get_rate').addClass('disabled');
					// $('#estimate_rate').html('Loading...');
					$('#xcontent').show();
				// $('#button-guest').attr('disabled', true);
				// $('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				// $('#button-guest').attr('disabled', false); 
				// $('.wait').remove();
				$('#xcontent').hide();
				// $('#get_rate').removeClass('disabled');
			},			
			success: function(json) {
				if(json['error'])
				{
						alert("Alert: Problem re-printing the label, please try again");
						// $('#estimate_rate').html('#Error');

						return false;
				}
				else
				{
						window.location = json['success'];
				}
				
				
				
			}
		});		
		});

		
		function toggleIcon(e) {
			$(e.target)
			.prev('.panel-heading')
			.find(".more-less")
			.toggleClass('glyphicon-plus glyphicon-minus');
		}
		$('.panel-group').on('hidden.bs.collapse', toggleIcon);
		$('.panel-group').on('shown.bs.collapse', toggleIcon);

	</script>