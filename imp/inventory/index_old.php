<?php
include_once '../auth.php';
include_once '../inc/functions.php';
include_once 'class.php';
// print_r($_POST);EXIT;
if(isset($_POST['type']) && $_POST['type']=='ajax')
{
	if(isset($_POST['action']) && $_POST['action']=='load_order')
	{
		$order_id = $db->func_escape_string($_POST['order_id']);
		$ajaxOrderDetail = $inventory->getOrder($order_id);
		// print_r($ajaxOrderDetail);exit;
		if($ajaxOrderDetail)
		{
		echo json_encode($ajaxOrderDetail);
			
		}
		else
		{
			$json = array();
			$json['error'] = 1;
			echo json_encode($json);
		}
	}
	if(isset($_POST['action']) && $_POST['action']=='load_orders')
	{
		$processedOrders = $inventory->getProcessedOrders();
		$onHoldOrders = $inventory->getOnHoldOrders();

		$html1 = '<div class="col-md-6">';
				foreach ($processedOrders as $i => $row)
				{ 
					if ($i == round((count($processedOrders) / 2))) 
					{
						$html1.='</div> <div class="col-md-6">';
					} 
								$html1.='<a href="" class="list-group-item text-left" data-toggle="collapse" onclick="loadOrder(\''.$row['order_id'].'\')">'.$row['order_id'].'</a>';
				}
							
					$html1.='</div>';

					$html2 = '<div class="col-md-6">';
				foreach ($onHoldOrders as $i => $row)
				{ 
					if ($i == round((count($onHoldOrders) / 2))) 
					{
						$html2.='</div> <div class="col-md-6">';
					} 
								$html2.='<a href="" class="list-group-item text-left" data-toggle="collapse" onclick="loadOrder(\''.$row['order_id'].'\')">'.$row['order_id'].'</a>';
				}
							
					$html2.='</div>';

					$json = array();
					$json['processedOrders'] = ($html1);
					$json['onHoldOrders'] = ($html2);
					echo json_encode($json);
	}
	if(isset($_POST['action'])=='mark_picked' && $_POST['action']=='mark_picked')
	{
		// print_r($_POST['sku']);
		$order_id  = $_POST['order_id'];
		$skus = $_POST['sku'];
		$sort_array = array();
		foreach($skus as $sku)
		{
			if(isset($sort_array[$sku]))
			{
				$sort_array[$sku]+=1;
			}
			else
			{
				$sort_array[$sku]=1;
				
			}
		}
		$json = array();
		if($inventory->markPicked($order_id,$sort_array))
		{
			$json['success'] = 1;
			if($inventory->makeLedger($order_id,$sort_array,$_SESSION['user_id'],'picked'))
			{
				$json['success'] = 1;
			}
			else
			{
				$json['success'] = 0;
			}
		}
		else
		{
			$json['success'] = 0;
		}
		echo json_encode($json);
	}
	if(isset($_POST['action'])=='save_picked' && $_POST['action']=='save_picked')
	{
		// print_r($_POST['sku']);
		$order_id  = $_POST['order_id'];
		$skus = $_POST['sku'];
		$sort_array = array();
		foreach($skus as $sku)
		{
			if(isset($sort_array[$sku]))
			{
				$sort_array[$sku]+=1;
			}
			else
			{
				$sort_array[$sku]=1;
				
			}
		}
		$json = array();
		if($inventory->savePicked($order_id,$sort_array))
		{
			$json['success']=1;
			
			
		}
		else
		{
			$json['success'] = 0;
		}
		echo json_encode($json);
	}
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
	<title>Order Scanning</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>../catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">
	
	<style type="text/css" media="screen">

		body{
			/*background-color: #bdc3c7;*/
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
			display:block;
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
		.highlight td{background-color:#000;color:#FFF;}

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
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Help Info</h4>
      </div>
      <div class="modal-body">
        <p>This page is used to Manually Pick the orders from Inventory.</p>
        <p> <strong>Short Codes:</strong></p>
        <ul>
        <li>Help Window: F2</li>
        <li>Save Record: Alt + S</li>
        <li>Processed Orders: Alt + 1</li>
        <li>On Hold Orders: Alt + 2</li>

        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<div class="modal" id="livestream_scanner">
    	<div class="modal-dialog">
    		<div class="modal-content">
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    					<span aria-hidden="true">&times;</span>
    				</button>
    				<h4 class="modal-title">Barcode Scanner</h4>
    			</div>
    			<div class="modal-body" style="position: static">
    				<div id="interactive" class="viewport"></div>
    				<div class="error"></div>
    			</div>
    			<div class="modal-footer">
    				<label class="btn btn-default pull-left">
    					<i class="fa fa-camera"></i> Use camera app
    					<input type="file" accept="image/*;capture=camera" capture="camera" class="hidden" />
    				</label>
    				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
    			</div>
    		</div><!-- /.modal-content -->
    	</div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<!-- End Modal -->
	<div id="xcontent" style="display:none"><div style="color:#fff;
top:40%;
position:fixed;
left:40%;
font-weight:bold;font-size:25px"><img src="https://phonepartsusa.com/catalog/view/theme/default/image/loader_white.gif" /><span style="margin-left: 11%;
    margin-top: 33%;
    position: absolute;
    
    width: 201px;">Please wait...</span></div></div>  
	<?php if (isset($_SESSION['login_as'])) { ?>
	<div class="row">
		<div class="col-md-12">
			<?php //include_once '../inc/header.php';?>
		</div>
	</div>
	<?php } ?>
	<div class="container theme-showcase" role="main">
		<div class="row">
			<div class="col-md-12 text-center">
				<h2>Picking Mode <a href="#" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-info-sign" style="font-size:16px" data-toggle="tooltip" title="Help Info (F2)"></i></a></h2>
				<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4 text-center"><input type="text" class="form-control form-control-lg" placeholder="Scan Order ID Manually" id="order_id_manual"></div><div class="col-md-4"></div>
				</div>

				<h4 id="order_id">N/A</h4>
			</div>
			
		</div>
		

		<br>
		<div class="row">
			
			<div class="list-group panel col-md-3 text-center" style="border:1px solid grey">
				<div>
				<h4 style="background-color:#808080;padding:8px;margin:0px"><a id="processed_accordion" style="color:#FFF" data-toggle="collapse" data-target="#loadManufacturers">Processed Orders</a></h4>
				<div id="loadManufacturers" class="collapse in">
					<div class="row">
						
					</div>
				</div>
				</div>

				<div>
				<h4 style="background-color:#808080;padding:8px;margin:0px"><a style="color:#FFF" id="on_hold_accordion" data-toggle="collapse" data-target="#loadOnHold">On Hold</a></h4>
				<div id="loadOnHold" class="collapse">
					<div class="row">
						<div class="col-md-6">
							<?php foreach ($onHoldOrders as $i => $row) : ?>
								<?php if ($i == round((count($onHoldOrders) / 2))) { ?>
							</div>
							<div class="col-md-6">
								<?php  } ?>
								<a href="" class="list-group-item text-left" data-toggle="collapse" onclick="loadOrder('<?php echo $row['order_id'] ;?>')"><?php echo $row['order_id']; ?></a>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				</div>
			</div>
			<div class="col-md-9 panel list-group" style="border:1px solid grey;">
				<div class="row">
					<div class="col-md-12" style="">
						<table class="table" style="font-weight:bold">
						<tr>
						<td class="col-md-3">Customer Name:</td>
						<td class="col-md-3" ><span id="customer_name"></span></td>
						<td class="col-md-3">Shipping:</td>
						<td class="col-md-3"><span id="customer_shipping_method">N/A</span></td>
						</tr>
						<tr>
						<td class="col-md-3">Address:</td>
						<td class="col-md-9"><span id="customer_address"> </span><br>
						<span id="customer_city"></span><span id="customer_state"> </span><span id="customer_zip"></span></td>
						
						</tr>
						


						</table>
						
						
					</div>
				
				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						
						<div class="form-group row">
							<div class="col-md-3"></div>
							<div class="col-sm-6 ">
								<input type="text" class="form-control form-control-lg" id="scan_sku"  placeholder="SKU">
							</div>
							<div class="col-md-3"></div>
						</div>

					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<table class="table table-stripped table-fixed">
							<thead>
								<tr>
									<th class="col-xs-3">SKU</th>
									<th class="col-xs-7">Item Name</th>
									<th class="col-xs-2 text-center">Added?</th>
								</tr>
							</thead>
							<tbody>
								

							</tbody>
						</table>

					</div>

				</div>

			</div>
			<div class="row text-center">
<!-- <button type="button" class="btn btn-primary btn-lg btn-save" disabled="">Save Only</button> -->
<!-- <button type="button" class="btn btn-success btn-lg btn-pick" disabled>Mark Picked</button> -->
<button type="button" class="btn btn-danger btn-lg" onClick="window.location='packing.php'">Packing Window</button>
<button type="button" class="btn btn-success btn-lg disabled save_record_btn" title="Hotkey: Alt + S">Save Record</button>
				</div>
		</div>

	</div>
	<script type="text/javascript" src="<?php echo $host_path; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path ?>/fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path ?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<script type="text/javascript" src="js/quagga.js"></script>
	<script>
	$(document).ready(function(){
		$('#order_id_manual').focus();
		loadOrders();
	});

	$('#order_id_manual').on('keypress', function(e) {
		if (e.which == 13 && $(this).val()!='') {
			$('#xcontent').show();
			loadOrder($(this).val());
		}
	});
	$(document).on('keyup', function(e) {
				// alert(e.which);
				if ((e.altKey) && (e.which == 83))
				{
					markSaved();
				}
				if(e.which==113)
				{
					$('#myModal').modal('show');
				}

				if ((e.altKey) && (e.which == 49))
				{
					$('#processed_accordion').trigger('click');
				}
				if ((e.altKey) && (e.which == 50))
				{
					$('#on_hold_accordion').trigger('click');
				}
			});
	$(document).on('click','.save_record_btn',function(){
		markSaved();
	})

	$(document).on('click','#on_hold_accordion',function(){
		$('#loadManufacturers').removeClass('in');
	})

	$(document).on('click','#processed_accordion',function(){
		$('#loadOnHold').removeClass('in');
	})

	$('#scan_sku').on('keypress', function(e) {
    var sku = $(this).val().toLowerCase();;
    var found_sku = false;
    if (e.which == 13) {
		
		$("input[name='sku_checked[]']:not(:checked)").each(function(){
			// alert(sku+'--'+$(this).val().toLowerCase());
    	if(sku==$(this).val().toLowerCase())
    	{
    		$(this).attr('checked','checked');
    		found_sku = true;
    			// console.log($(this).parent().parent());
		$(this).parent().parent().find('.checkbox_val').html('<img src="../images/check.png">');
		scrollToHere(this);
    		return false;
    	}
    	
	});
		if($("input[name='sku_checked[]']").length == $("input[name='sku_checked[]']:checked").length)
		{
			$('#scan_sku').val('');
			
				markItPicked();
						return false;
		}
		var audio_file = '../sounds/success.mp3';
		if(!found_sku)
		{
			audio_file = '../sounds/error.mp3';
			// alert('No Sku found or all picked, please try retyping again');
		}
		else
		{
			audio_file = '../sounds/success.mp3';
			$('#scan_sku').val('');
		}
		var audio = new Audio(audio_file);
		audio.play();
    	
        e.preventDefault();
    }
});
	function markItPicked()
	{
		// if(!confirm('Are you sure want to continue?'))
		// {
		// 	return false;
		// }

		skus =[];
		$("input[name='sku_checked[]']:checked").each(function(){
			skus.push($(this).val());


		});
		// alert(JSON.stringify(skus));
		// return false;

		$.ajax({
				url: 'index.php',
				type: 'post',
				data: {type:'ajax',action:'mark_picked',order_id:$('#order_id').html(),sku:skus},
				dataType: 'json',
				beforeSend: function() {
					$('#xcontent').show();
				// $('#button-guest').attr('disabled', true);
				// $('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				$('#xcontent').hide();
				// $('#button-guest').attr('disabled', false); 
				// $('.wait').remove();
			},			
			success: function(json) {
				if(json['success']==1)
				{
					// alert('Success: Order marked as picked!');
					var audio = new Audio('../sounds/success2.mp3');
						audio.play();
					location.reload(true);
				}
				else
				{
					alert("Fail: Something is wrong, please try again or contact technical resource.");
				}
			}
		});
	}
	
	function loadOrders()
	{
		$.ajax({
				url: 'index.php',
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
				$('#loadOnHold .row').html(json['onHoldOrders']);
				
			}
		});
	}
	setInterval(function(){
	 loadOrders();
	}, 30 * 1000);

	function markSaved()
	{
		if($('.save_record_btn').hasClass('disabled'))
		{
			return false;
		}
		// if(!confirm('Are you sure want to continue?'))
		// {
		// 	return false;
		// }

		skus =[];
		$("input[name='sku_checked[]']:checked").each(function(){
			skus.push($(this).val());


		});
		// alert(JSON.stringify(skus));
		// return false;

		$.ajax({
				url: 'index.php',
				type: 'post',
				data: {type:'ajax',action:'save_picked',order_id:$('#order_id').html(),sku:skus},
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
			},			
			success: function(json) {
				if(json['success']==1)
				{
					// alert('Success: Order marked as picked!');
					var audio = new Audio('../sounds/success2.mp3');
						audio.play();
					location.reload(true);
				}
				else
				{
					alert("Fail: Something is wrong, please try again or contact technical resource.");
				}
			}
		});
	}
	function scrollToHere(obj)
	{
		 
		var row = $(obj).parent().parent();
		
	row.addClass('highlight');  
    setTimeout(function(){
      row.removeClass('highlight')
    },333);
    setTimeout(function(){
      row.addClass('highlight')
    },666);  

    setTimeout(function(){
      row.removeClass('highlight')
    },999);  

	}

		function loadOrder(order_id)
		{
			var disableDiv = '<div class="disableDiv"><span class="editLayer" onclick="$(this).parent().remove();"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></span><span class="sign"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></span></div>';
			// $('#loadManufacturers').append(disableDiv);
			$('#scan_sku').val('');
			$('.table-fixed tbody').html('')

			$.ajax({
				url: 'index.php',
				type: 'post',
				data: {type:'ajax',action:'load_order',order_id:order_id},
				dataType: 'json',
				beforeSend: function() {
				// $('#button-guest').attr('disabled', true);
				// $('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				// $('#button-guest').attr('disabled', false); 
				// $('.wait').remove();
				$('#xcontent').hide();
			},			
			success: function(json) {
				if(json['error'])
				{
					var audio = new Audio('../sounds/error.mp3');
					audio.play();
					alert("Unable to find the order id, please try again");
					return false;
				}

				$('.save_record_btn').removeClass('disabled');
				$('#order_id').html(json['order_id']);
				$('#order_id_manual').val('');
				$('#customer_name').html(json['customer_name']);
				$('#customer_address').html(json['address1']);
				$('#customer_city').html(json['city']+' ');
				$('#customer_state').html(json['state']+' ');
				$('#customer_zip').html(json['zip']);
				$('#customer_shipping_method').html(json['shipping_method']);

				html = '';
				var picked_quantity =0;
				var packed_quantity =0;
				var is_picked = 0;
				var is_packed = 0;

				var my_pick = 0;
				for(i = 0; i <  json['items'].length; ++i)
				{
					picked_quantity = json['items'][i]['picked_quantity'];
					my_pick = 0;
					for(j = 0;j<json['items'][i]['quantity'];++j)
					{
						// my_pick = 0;
						if(picked_quantity>my_pick)
						{
							// console.log(my_pick);
							is_picked = 1;
							my_pick++;
						}
						else
						{
							is_picked = 0;
						}

						
					html+='<tr>';
					html+='<td class="col-xs-3"><input type="checkbox" style="display:none" '+(is_picked?'checked':'')+' name="sku_checked[]" value="'+json['items'][i]['sku']+'" >'+json['items'][i]['sku']+'</td>';
					html+='<td class="col-xs-7">'+json['items'][i]['name']+'</td>';
					html+='<td class="col-xs-2 text-center checkbox_val"><img src="../images/'+(is_picked?'check':'cross')+'.png"></td>';
					html+='</tr>';

				}

				}
				$('.table-fixed tbody').html(html);
				$('.btn-save').removeAttr('disabled');
				$('#scan_sku').focus();
			}

});
		}

			
		


	</script>
	