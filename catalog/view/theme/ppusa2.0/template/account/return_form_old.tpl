<?php echo $header; ?>
<div id="xcontent2" style="display:none"><div style="color:#fff;
top:40%;
position:fixed;
left:40%;
font-weight:bold;font-size:25px"><img src="catalog/view/theme/default/image/loader_white.gif" /><span style="margin-left: 11%;
    margin-top: 33%;
    position: absolute;
    
    width: 201px;">Please wait...</span></div></div> 
<!-- @End of header -->
<main class="main">
		<div class="container confirm-return-page">
			<div class="row row-centered">
				<div class="col-md-10 intro-head col-centered">
					<div class="text-center">
						<h1 class="blue blue-title uppercase mb20"> &amp; exchanges</h1>
					</div>
				</div>
			</div>
			<div class="white-box overflow-hide">
				<div class="row">
					<div class="col-md-12 table-cell">
						<div class="row inline-block">
							<div class="col-md-3 white-box-left pr0 inline-block">
								<div class="white-box-inner panel-trigger-parent">
									<h4 class="blue-title">Return Policy </h4>
								</div>
								<div class="white-box-inner panel-triggered">
									<p>OEM Screens 100% Original Screens removed from devices. Never Refurbished or Resurfaced.
									Non-OEM Screens: (contain any of the following Aftermarket LCD, Touch Screen, Polarizor, Glass, Touch Screen Flex Cable, LCD Flex Cable, or Frame OEM Screens 100% Original Screens removed from devices. Never Refurbished or Resurfaced.</p>
									<p>Non-OEM Screens: (contain any of the following Aftermarket LCD, Touch Screen, Polarizor, Glass, Touch Screen Flex Cable, LCD Flex Cable, or Frame OEM Screens 100% Original Screens removed from devices. Never Refurbished or Resurfaced.</p>
									<p>Non-OEM Screens: (contain any of the following Aftermarket LCD, Touch Screen, Polarizor, Glass, Touch Screen Flex Cable, LCD Flex Cable, or Frame OEM Screens 100% Original Screens removed from devices. Never Refurbished or Resurfaced.</p>
								</div>
							</div>
							<div class="col-md-9 tabs-onfull white-box-right inline-block overflow-hide">
								<ul class="nav nav-tabs small">
								    <li class="active"><a id="lookup-button" href="#lookup" data-toggle="tab">1. Order Lookup</a></li>
								    <li ><a id="select-button" href="#selectItems" data-toggle="tab">2. Select Items</a></li>
								    <li ><a id="confirm-button" href="#confirmRetrun" data-toggle="tab">3. Confirm Return</a></li>
								    <li ><a id="print-button" href="#printRam" data-toggle="tab">4. Print RMA Label</a></li>
								</ul>
								<div class="tab-content">
								    <div id="lookup" class="tab-pane fade in active">
								    	<div class="tab-inner">
								    		<div class="return-head text-center">
									    		<p>Use your original order number and either your e-mail or zip code to proceed to the next step.
												Need to return from multiple orders? That’s fine just return to step 1 after selecting your items, and look up another Order ID.</p>
											</div>	
											<form role="form" action="" class="form-horizontal v-form field-space-40" onsubmit="return false;">
												<div class="form-group">
											    	<div class="col-md-4">
											    		<label for="orderId" class="control-label">Order ID</label>
											    		<input type="text" class="form-control" name="order_id" placeholder="Order ID...">
											    	</div>
											    	<div class="field-box col-md-7">
											    		<div class="row">
													    	<div class="col-sm-6">
													    		<label for="email" class="control-label">Email</label>
													    		<input type="text" class="form-control" name="email" placeholder="Email...">
													    	</div>
													    	<div class="col-sm-6 has-or">
													    		<span class="or-text">or</span>
													    		<label for="zcode" class="control-label">Zip Code</label>
													    		<input type="text" class="form-control" name="postcode" placeholder="Zip Code...">
													    	</div>
												    	</div>
											    	</div>
											 	</div>
											 	<div class="text-center">
												 	<button class="btn btn-primary" id="begin_return" onclick="BeginReturn()">
									    				begin return process
									    			</button>
								    			</div>
											</form>
										</div>	
										<div class="border"></div>
										<div class="tab-inner pb60">	
											<h3 class="uppercase">order history</h3>
								    		<?php 
								    		if($is_logged)
								    		{
								    		$i = 0;
								    			?>
								    			<div class="h-300">
									    		<div class="scroll">
										    			<?php foreach ($user_orders as $user_order){
										    			?>
										    			<div class="order-box small row mr0">
										    			<div class="col-lg-4 order-col table-cell">
										    				<ul class="track-list list-inline">
										    					<li>Order date: </li>
										    					<!-- <li>2/30/2016</li -->
										    					<li><?php echo date('m/d/y', strtotime($user_order['date_added']));?></li>										    				
										    				</ul>
										    				<ul class="track-list list-inline">
										    					<li>Order #:  </li>
										    					<li><?php echo $user_order['order_id'];?></li>
										    				</ul>
										    				<ul class="track-list list-inline">
										    					<li>Order Total:</li>
										    					<li>$<?php echo $user_order['total'];?></li>
										    				</ul>
										    			</div>
										    			<div class="col-lg-4 order-col table-cell">
										    				<ul class="track-list list-inline">
										    					<li>Shipped to:</li>
										    					<li><?php echo $user_order['shipping_address_1'];?></li>
										    				</ul>
										    				<ul class="track-list"></ul>
										    				<ul class="track-list list-inline">
										    					<li>Status:</li>
										    					<li><?php echo $user_order['order_status'];?></li>
										    				</ul>
										    			</div>
										    			<div class="col-lg-4 order-col table-cell text-center v-middle">
										    				<button class="btn btn-primary" onclick="BeginReturnOrder(<?php echo $user_order['order_id'];?>);" id="begin_return_order">Begin return</button>
										    			</div>
										    		</div>
										    		<?php $i++;	}?>


									    		</div>
								    		</div>
								    			<?php
								    		}
								    		else
								    		{
								    			?>
								    			<form method="post" action="<?php echo $v2_signin_link;?>">
								    			<p class="montserat16">Do you have a PhonePartsUSA account? Sign in to view your order history</p>
								    			<input type="hidden" name="redirect" value="<?=$this->url->link('account/return/insert','','SSL');?>">
								    		<button class="btn btn-primary">sign in</button>
								    		</form>
								    			<?php
								    		}
								    		?>
								    		
										</div>
								    </div>
								   <div id="selectItems" class="tab-pane fade">
								    	<div class="tab-inner">
								    		<div class="return-head text-center">
								    			<p>Select the item you wish to return, followed by the return reason and how you would like for us to process the return upson reception</p>
								    			<button class="btn btn-primary mr30">
								    				Add items from another oreder id
								    			</button>
								    			<button class="btn btn-primary" onclick="confirmation();">
								    				Continue to return confirmation
								    			</button>
								    		</div>
								    	</div>
								    	<div class="border"></div>
								    	<div class="tab-inner pb60" id="selectItemsDiv">
								    			
								    	</div>
								    </div>
								    <div id="confirmRetrun" class="tab-pane fade ">
								    	<div class="tab-inner">
								    		<div class="return-head text-center">
								    			<p>Review your return request, when everything looks good submit <br> the request and print the RMA lable.</p>
								    			<button class="btn btn-primary mr30">
								    				Add items from another oreder id
								    			</button>
								    			<button onclick="print_confirmed();generateRMA();" class="btn btn-primary">
								    				Submit request
								    			</button>
								    		</div>
								    	</div>
								    	<div class="border"></div>
								    	<div id="confirmation" class="tab-inner pb60">

								    	</div>
								    </div>
								    <div id="printRam" class="tab-pane fade">
								    	<div class="tab-inner">
								    		<div class="return-head text-center" id="print_btn_show">

								    		</div>
								    	</div>
								    	<div class="border"></div>
								    	<div id="print_confirmed"  class="tab-inner pb60">
								    		
								    	</div>	
								    </div>
								    	
								    </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main><!-- @End of main -->
	<script>
// $('#begin_return').on(document,'click',function(e) {
// 	// alert('here');
// BeginReturn();
// });
function BeginReturnOrder(order_id){
	// alert('here');
	$.ajax({
		url: 'index.php?route=account/return/beginReturnOrder',
		data:{order_id: order_id},
		dataType:"json",
		beforeSend: function() {
		// console.log('before send')
		},
		complete: function() {
			// console.log('complete')
		},
		success: function(json) {

			
			if (json['error']) {
				alert(json['error']);
			}
			 else {	
			 	$('.nav-tabs li a[href="#lookup"]').removeAttr('data-toggle','tab').parent().removeClass('active').addClass('disabled');
			 	$('.nav-tabs li a[href="#lookup"]').attr('href','javascript:void(0);');
			 $('.nav-tabs li a[href="#selectItems"]').attr('data-toggle','tab').tab('show').parent().removeClass('disabled').addClass('active');
			 	var html;
			 	var len = Object.keys(json).length
			 for(j=0;j<len;j++){
				/*alert(json[j]['model']);*/
				html += '<div class="return-list-box">';
				html += '<input type="hidden" id="price_'+j+'" value="'+json[j]['price']+'">';
				html += '<div class="return-list">';
				html += '<div class="return-list-head row">';
				html += '<input type="hidden" value="'+json[j]['model']+'" id="sku_'+j+'">';
				html += '<h4 class="col-md-3">'+json[j]['model']+'</h4>';
				html += '<input type="hidden" value="'+json[j]['name']+'" id="sku_desc_'+j+'">';
				html += '<p class="col-md-9">'+json[j]['name']+'</p>';
				html += '</div>';
				html += '<div class="return-items row">';
				html += '<div class="col-lg-4 row select-full">';
				html += '<p class="col-lg-8 return-lbl pr0">';
				html += '<input type="checkbox" value="'+json[j]['product_id']+'" class="css-checkbox" onChange="returnSelect();" id="return_select'+j+'">';
				html += '<label for="return_select'+j+'" class="css-label2">Return Quantity</label>';
				html += '</p><div class="col-lg-4">';
				html += '<select  class="selectpicker">';
				html += '<option value="1">1</option>';
				html += '</select>';
				html += '</div>';
				html += '</div>';
				html += '<div class="col-lg-5 row select-full">';
				html += '<p class="col-lg-4 text-right return-lbl">Reason:</p>';
				html += '<div class="col-lg-8 pl0 pr0">';
				html += '<select id="select_return'+j+'" onChange="returnSelect();" class="selectpicker">';
				var reason_len = Object.keys(json[j]['reasons']).length;
				for(k=0;k<reason_len;k++){	
				html += '<option value="'+json[j]['reasons'][k]['return_reason_id']+'">'+json[j]['reasons'][k]['name']+'</option>';
				}
				html += '</select>';
				html += '</div>';
				html += '</div>';
				html += '<div class="col-lg-4 row select-full">';
				html += '<p class="col-lg-8 text-right return-lbl">Preference:</p>';
				html += '<div class="col-lg-4 pl0 pr0">';
				html += '<select id="return_processing'+j+'" onChange="returnSelect()" class="selectpicker">';
				html += '<option value="exchange">Exchange</option>';
				html += '<option value="refund">Refund</option>';
				html += '</select>';
				html += '</div>';
				html += '</div>';
				html += '</div>';
				html += '<div  class="return-items return-items-comment">';
				html += '<p>Comments <small class="require">(required)</small></p>';
				html += '<textarea name=\'comment[]["'+json[j]['model']+'"]\' id="comment_'+j+'" placeholder="Describe your issue here..." class="mt5 form-control" >';
				html += '</textarea>';
				html += '</div>';
				html += '</div>';
				html += '</div>';
				
				// i++;
				}
				html += '<input type="hidden" id="total_records" value="'+len+'"><input type="hidden" id="ord_id" name="order_id" value="'+order_id+'"><input type="hidden" id="checked_records" name="checked_records">';
				$('#selectItemsDiv').html(html);
				 $('.selectpicker').selectpicker({
        width: '270px',
        style: 'btn btn-xs btn-default'
    });
			}	
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
function BeginReturn()
{
	$.ajax({
		url: 'index.php?route=account/return/validateNewReturn',
		data:{version: '2',order_id:$('input[name=order_id]').val(),email:$('input[name=email]').val(),postcode:$('input[name=postcode]').val()},
		dataType:"json",
		beforeSend: function() {
			// $('.warning').remove();
			// $('#begin_return').before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			$('#begin_return').attr('disabled','disabled');
			//$('#xcontent2').show();
		},
		complete: function() {
			// $('.wait').remove();
			$('#begin_return').removeAttr('disabled');
			// $('#lookup').attr('disabled','disabled');
			// $('#selectItems').removeAttr('disabled');
			// $('#selectItems').click();
			
			 // $('.nav-tabs li a[href="#selectItems"]').removeClass('disabled').addClass('active').attr('data-toggle','tab').tab('show');

		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}
			 else {	
			 	$('.nav-tabs li a[href="#lookup"]').removeAttr('data-toggle','tab').parent().removeClass('active').addClass('disabled');
			 	$('.nav-tabs li a[href="#lookup"]').attr('href','javascript:void(0);');
			 $('.nav-tabs li a[href="#selectItems"]').attr('data-toggle','tab').tab('show').parent().removeClass('disabled').addClass('active');
				showProducts(json['success']);
			}	
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	
}

function showProducts(order_id)
{
	$.ajax({
		url: '<?php echo HTTPS_SERVER; ?>index.php?route=account/return/showProductsNew&version=2&order_id=' + order_id,
		dataType:"json",
		
		beforeSend: function() {
			//$('.warning').remove();
			$('#begin_return').attr('disabled','disabled');
			
		},
		complete: function() {
			//$('#xcontent2').hide();
			$('#begin_return').removeAttr('disabled');
		},			
				success: function(json) {

			
			if (json['error']) {
				alert(json['error']);
			}
			 else {	
			 	var html;
			 	var len = Object.keys(json).length
			 for(j=0;j<len;j++){
				/*alert(json[j]['model']);*/
				html += '<div class="return-list-box">';
				html += '<input type="hidden" id="price_'+j+'" value="'+json[j]['price']+'">';
				html += '<div class="return-list">';
				html += '<div class="return-list-head row">';
				html += '<input type="hidden" value="'+json[j]['model']+'" id="sku_'+j+'">';
				html += '<h4 class="col-md-3">'+json[j]['model']+'</h4>';
				html += '<input type="hidden" value="'+json[j]['name']+'" id="sku_desc_'+j+'">';
				html += '<p class="col-md-9">'+json[j]['name']+'</p>';
				html += '</div>';
				html += '<div class="return-items row">';
				html += '<div class="col-lg-4 row select-full">';
				html += '<p class="col-lg-8 return-lbl pr0">';
				html += '<input type="checkbox" value="'+json[j]['product_id']+'" class="css-checkbox" onChange="returnSelect();" id="return_select'+j+'">';
				html += '<label for="return_select'+j+'" class="css-label2">Return Quantity</label>';
				html += '</p><div class="col-lg-4">';
				html += '<select  class="selectpicker">';
				html += '<option value="1">1</option>';
				html += '</select>';
				html += '</div>';
				html += '</div>';
				html += '<div class="col-lg-5 row select-full">';
				html += '<p class="col-lg-4 text-right return-lbl">Reason:</p>';
				html += '<div class="col-lg-8 pl0 pr0">';
				html += '<select id="select_return'+j+'" onChange="returnSelect();" class="selectpicker">';
				var reason_len = Object.keys(json[j]['reasons']).length;
				for(k=0;k<reason_len;k++){	
				html += '<option value="'+json[j]['reasons'][k]['return_reason_id']+'">'+json[j]['reasons'][k]['name']+'</option>';
				}
				html += '</select>';
				html += '</div>';
				html += '</div>';
				html += '<div class="col-lg-4 row select-full">';
				html += '<p class="col-lg-8 text-right return-lbl">Preference:</p>';
				html += '<div class="col-lg-4 pl0 pr0">';
				html += '<select id="return_processing'+j+'" onChange="returnSelect()" class="selectpicker">';
				html += '<option value="exchange">Exchange</option>';
				html += '<option value="refund">Refund</option>';
				html += '</select>';
				html += '</div>';
				html += '</div>';
				html += '</div>';
				html += '<div class="return-items return-items-comment">';
				html += '<p>Comments <small class="require">(required)</small></p>';
				html += '<textarea name=\'comment[]["'+json[j]['model']+'"]\' id="comment_'+j+'" placeholder="Describe your issue here..." class="mt5 form-control" >';
				html += '</textarea>';
				html += '</div>';
				html += '</div>';
				html += '</div>';
				}
				html += '<input type="hidden" id="total_records" value="'+len+'"><input type="hidden" id="ord_id" name="order_id" value="'+order_id+'"><input type="hidden" id="checked_records" name="checked_records">';
				$('#selectItemsDiv').html(html);
				 $('.selectpicker').selectpicker({
        width: '270px',
        style: 'btn btn-xs btn-default'
    });
			}	
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	
	
}
function returnSelect()
{
    var total_records = $("#total_records").val();	
    var str ="";
    for(var i=0;i<=total_records;i++)
    {
      if($("#return_select"+i).is(":checked"))
      {
       if(i>0)
       {
           str+="~";	
       }
       str+=$("#return_select"+i).val()+","+$("#select_return"+i).val()+","+$("#return_processing"+i).val();
   }
}
$("#checked_records").val(str);
// console.log($("#checked_records").val());
}
function confirmation()
{
    var total_records = $("#total_records").val();	
    var html;
    for(var i=0;i<=total_records;i++)
    {
      if($("#return_select"+i).is(":checked"))
      {
      	html+= '<div id="conf_item'+i+'" class="return-list">';
				html+= '<div class="return-list-head row">';
				html+= '<h4 class="col-md-3">'+$("#sku_"+i).val()+'</h4>';
				html+= '<p class="col-md-9">'+$("#sku_desc_"+i).val()+'</p>';
				html+= '</div>';
				html+= '<div class="return-items row">';
				html+= '<div class="col-sm-3">';
				html+= '<p><span class="blue">Order Number:</span> '+$("#ord_id").val()+'</p>';
				html+= '</div>';
				html+= '<div class="col-sm-5">';
				html+= '<p><span class="blue">Reason:</span>'+$("#select_return"+i).val()+'</p>';
				html+= '</div>';
				html+= '<div class="col-sm-4">';
				html+= '<p><span class="blue">Preference: </span>'+$("#return_processing"+i).val()+'</p>';
				html+= '</div>';
				html+= '</div>';
				html+= '<div class="return-items row">';
				html+= '<div class="col-sm-3">';
				html+= '<p><span class="blue">Return Quantity: </span> 1</p>';
				html+= '</div>';
				html+= '<div class="col-sm-5">';
				html+= '<p><span class="blue">AT </span> $'+$("#price_"+i).val()+' <span class="blue">Each </span></p>';
				html+= '</div>';
				html+= '<div class="col-sm-3">';
				html+= '<p><span class="blue">Return Value</span> $'+$("#price_"+i).val()+'</p>';
				html+= '</div>';
				html+= '</div>';
				html+= '<div class="return-items row">';
				html+= '<div class="col-sm-12">';
				html+= '<p><span class="blue">Comments: </span>'+$("#comment_"+i).val()+'</p>';
				html+= '</div>';
				html+= '</div>';
				html+= '<div class="return-btns">';
				html+= '<button onclick="editItemSwitcher()" class="btn btn-primary mr20">Edit return item</button>';
				html+= '<button onclick="deleteConfirmedItem('+i+')" class="btn btn-primary red-btn">rEMOVE from return</button>';
				html+= '</div>';
				html+= '</div>';
   }
}
$("#confirmation").html(html);
$('.nav-tabs li a[href="#selectItems"]').removeAttr('data-toggle','tab').parent().removeClass('active').addClass('disabled');
$('.nav-tabs li a[href="#selectItems"]').attr('href','javascript:void(0);');
$('.nav-tabs li a[href="#confirmRetrun"]').attr('data-toggle','tab').tab('show').parent().removeClass('disabled').addClass('active');
}
function deleteConfirmedItem(i){
	$("#conf_item"+i).remove();
	$("#return_select"+i).prop('checked', false);
	returnSelect();
}
function editItemSwitcher(){
$('#confirm-button').attr('href','#confirmRetrun');
$('#select-button').attr('href','#selectItems');
$('.nav-tabs li a[href="#confirmRetrun"]').removeAttr('data-toggle','tab').parent().removeClass('active');
$('.nav-tabs li a[href="#selectItems"]').attr('data-toggle','tab').tab('show').parent().removeClass('disabled').addClass('active');
}
function print_confirmed()
{
    var total_records = $("#total_records").val();	
    var html;
    for(var i=0;i<=total_records;i++)
    {
      if($("#return_select"+i).is(":checked"))
      {
      	html+= '<div class="return-list">';
				html+= '<div class="return-list-head row">';
				html+= '<h4 class="col-md-3">'+$("#sku_"+i).val()+'</h4>';
				html+= '<p class="col-md-9">'+$("#sku_desc_"+i).val()+'</p>';
				html+= '</div>';
				html+= '<div class="return-items row">';
				html+= '<div class="col-sm-3">';
				html+= '<p><span class="blue">Order Number:</span> '+$("#ord_id").val()+'</p>';
				html+= '</div>';
				html+= '<div class="col-sm-5">';
				html+= '<p><span class="blue">Reason:</span>'+$("#select_return"+i).val()+'</p>';
				html+= '</div>';
				html+= '<div class="col-sm-4">';
				html+= '<p><span class="blue">Preference: </span>'+$("#return_processing"+i).val()+'</p>';
				html+= '</div>';
				html+= '</div>';
				html+= '<div class="return-items row">';
				html+= '<div class="col-sm-3">';
				html+= '<p><span class="blue">Return Quantity: </span> 1</p>';
				html+= '</div>';
				html+= '<div class="col-sm-5">';
				html+= '<p><span class="blue">AT </span> $'+$("#price_"+i).val()+' <span class="blue">Each </span></p>';
				html+= '</div>';
				html+= '<div class="col-sm-3">';
				html+= '<p><span class="blue">Return Value</span> $'+$("#price_"+i).val()+'</p>';
				html+= '</div>';
				html+= '</div>';
				html+= '<div class="return-items row">';
				html+= '<div class="col-sm-12">';
				html+= '<p><span class="blue">Comments: </span>'+$("#comment_"+i).val()+'</p>';
				html+= '</div>';
				html+= '</div>';
				html+= '</div>';
   }
}
$("#print_confirmed").html(html);
$('.nav-tabs li a[href="#confirmRetrun"]').removeAttr('data-toggle','tab').parent().removeClass('active').addClass('disabled');
$('.nav-tabs li a[href="#confirmRetrun"]').attr('href','javascript:void(0);');
$('.nav-tabs li a[href="#printRam"]').attr('data-toggle','tab').tab('show').parent().removeClass('disabled').addClass('active');
}
function itemIssueCheck(){
	var r = true;
	var total_records = $("#total_records").val();	
    var str ="";
    for(var i=0;i<=total_records;i++)
    {
    	if($("#return_select"+i).is(":checked"))
      {
      		if($("#comment_"+i).val()=="")
      		{
      			r = false; 
   				}
		  }
		}
		return r;
}
function generateRMA()
{
	if(!itemIssueCheck())
	{
		alert("Please provide us with every detail of the item issue.");
		$('#confirm-button').attr('href','#confirmRetrun');
		$('#select-button').attr('href','#selectItems');
		$('.nav-tabs li a[href="#confirmRetrun"]').removeAttr('data-toggle','tab').parent().removeClass('active').removeClass('disabled');
		$('.nav-tabs li a[href="#selectItems"]').attr('data-toggle','tab').tab('show').parent().removeClass('disabled').addClass('active');
		return false;
	}

	$.ajax({
		url: "index.php?route=account/return/generateRMA",
		data:$("#selectItemsDiv input ,#selectItemsDiv select,#selectItemsDiv textarea"),
		type:"post",
		dataType: "json",
		beforeSend: function() {
			$("#xcontent2").show();
      },
      complete: function() {
   		},			
   		success: function(json) {
   	 if(json["success"])
   	 {		
					//window.location=json["success"];
					var htmm = '<p>RMA #'+json["rma_num"]+' has been created! Print the label and ship your returns back to us.</p><button  id="gen_rma_button" onclick="printThis()" class="btn btn-primary disabled"> Generating RMA </button><div style="display:none"  id="print_slip"></div>';
					$("#print_btn_show").html(htmm);
    	    ThirdStep();
   	 }
    	else{
    		$("#xcontent2").hide();
     		alert(json["error"]);
   		  return false;	
 			}
		},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(json["error"]);
			}
		});
}
function ThirdStep()
{
	
	$.ajax({
		url: "index.php?route=account/return/successNew",
		type:"post",
		dataType: "json",
		
		beforeSend: function() {
		},
		complete: function() {

		},			
		success: function(json) {
						//alert(json['image']);
						$("#xcontent2").hide();
            $("#print_slip").html(json['image']);
            $("#gen_rma_button").html('<i class="fa fa-print"></i> Print RMA Label ');
            $("#gen_rma_button").removeClass('disabled');
        },
        error: function(xhr, ajaxOptions, thrownError) {
        }
    });
}
function printThis()
    {
        var mywindow = window.open("", "LBB Print", "height=400,width=600");
        mywindow.document.write("<html><head><title>LBB Print</title>");
        
        mywindow.document.write("</head><body style=\"width: 8.27in;height: 11in;\" >");
        mywindow.document.write($("#print_slip").html());
        mywindow.document.write("</body></html>");
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        setTimeout(function () {
           mywindow.print();
           mywindow.close();
           return true;    
       }, 2000);
}
	</script>

	<script>
document.getElementById("#lookup-button").addEventListener("click", function(event){
    event.preventDefault()
});
</script>
<script>
document.getElementById("#select-button").addEventListener("click", function(event){
    event.preventDefault()
});
</script>
<script>
document.getElementById("#confirm_button").addEventListener("click", function(event){
    event.preventDefault()
});
</script>
<script>
document.getElementById("#print_button").addEventListener("click", function(event){
    event.preventDefault()
});
</script>
<?php echo $footer; ?>
<!-- @End of footer -->