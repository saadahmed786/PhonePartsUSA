<?php echo $header; ?>
<!--<div style=" width: 100%;
  height: 100%;
  top: 0px;
  left: 0px;
  position: fixed;
  display: block;
  opacity: 0.98;
  background-color: #000;
  z-index: 99;">
       
       </div>-->
  
<div id="xcontent2" style="display:none"><div style="color:#fff;
top:40%;
position:fixed;
left:40%;
font-weight:bold;font-size:25px"><img src="catalog/view/theme/default/image/loader_white.gif" /><span style="margin-left: 11%;
    margin-top: 33%;
    position: absolute;
    
    width: 201px;">Please wait...</span></div></div>  
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php //echo $column_left; ?><?php //echo $column_right; ?>

<div id="content" style="text-align:center"><?php echo $content_top; ?>
  <style>

/* @ Order Look
********************************************************************************************
********************************************************************************************/
.cart-holder{ width:1000px; margin:0 auto; overflow:hidden;font-family: 'Ubuntu', sans-serif !important;}
.aside{ float:left; width:241px; background:#fff; height:631px; padding:20px 0;}
.logo-holder{ padding:0 10px; margin:0 0 92px;}
.logo-holder h1{ margin:0;}

.order-detailx{ margin:0; padding:0 32px 0 0; list-style:none;}
.order-detailx li{ margin:0 0 5px; position:relative;text-align:left !important;}
.order-detailx li a{ padding:0 10px; display:block; color:#1076be; cursor:default;}
.number-style{ font-size:40px; font-weight:bold;}
.num{left:35px;position: absolute;top:14px;}
.ez-return-active{ background:#ffe400;}
.order-detailx li a:hover{ text-decoration:none; /*background:#ffe400;*/}
.wrapperx{z-index:100;}
.contentx{ width:733px; float:left; background:#c30c37; padding:45px 13px 60px 13px;}
.content-header{ margin:0 0 40px; text-align:center; color:#fff !important;}
.content-header h2{ margin:0 0 12px; font-size:48px; color:#FFF !important;}
.content-header p{ margin:0; font-size:14px;} 

.content-inner{ width:445px; margin:0 auto; background:#fff; padding:33px 15px; border-radius:6px; text-align:center;}
.content-inner form{ margin:0 0 40px; display:table;}
.content-inner input{ background:#ebebeb; border:1px solid #cfcfcf; padding:9px 10px; width:220px; border-radius:4px; margin:0 0 21px;}
.content-inner input:last-child{ margin:0;}
.or-sapret{ height:1px; background:#d2d0d0; width:100%; display:block; text-align:center; margin:0 0 40px;}
.or{ font-size:34px; height:40px; width:53px; text-align:center; display:inline-block; color:#1076be; margin:-22px 0 0; background:#fff;}
.content-inner em{ font-size:14px; color:#1c1c1c; margin:5px 0 16px; display:block;}
.return-btn{ width:341px; display:inline-block; color:#fff !important; text-align:center; padding:15px 0; background:#dd5555; border-radius:4px;font-size:16px;}
.return-btn:hover{ text-decoration:none; color:#fff; background:#E7676A;}

.return-btn-small{ width:130px; display:inline-block; color:#fff !important; text-align:center; padding:6px 0; background:#dd5555; border-radius:4px;font-size:12px;}
.return-btn-small:hover{ text-decoration:none; color:#fff; background:#E7676A;}


/* @ select item
********************************************************************************************
********************************************************************************************/
.selet-item-holder{ width:733px; float:left; background:#c30c37; padding:45px 13px 60px 13px;}
.selet-item-inner{ width:700px; margin:0 auto; background:#fff; padding:10px 15px; border-radius:6px; overflow:hidden;}
.selet-item-header{ overflow:hidden; color:#1076be; font-size:18px; margin:0 0 35px;}
.pull-left{ float:left; font-weight:bold;}
.pull-right{ float:right;}
#xcontent2{width: 100%;
  height: 100%;
  top: 0px;
  left: 0px;
  position: fixed;
  display: block;
  opacity: 0.8;
  background-color: #000;
  z-index: 99;}
.dropdown-holder{ position:relative;}
.check-hadding{ position:absolute; top:-2px; left:23px;width:325px;} 
.apl{ display:block; font-size:12px; color:#949494;}
.screen{ display:block; font-size:12px; color:#adadad; font-weight:100;color:#000;}

.check-list{ margin:0; padding:0; list-style:none;}
.check-list li{ position:relative;  margin:0 0 20px;text-align:left;} 
.check-list li:after {
     visibility: hidden;
     display: block;
     font-size: 0;
     content: " ";
     clear: both;
     height: 0;
     }
.selet-item-center{ margin:0 0 41px; height:255px; overflow-y:scroll;}

.dropdown-c-list{ padding:0; margin:0; list-style:none; float:right;}
.dropdown-c-list li{ float:left; margin:0 0 0 10px;}
.dropdown-c-list li:last-child{ margin:0 30px 0 10px;}
.dropdown-c-list li select{ width:131px; padding:5px 0; background:#f4f2f2; border:1px solid #cfcfcf;}

.footer{ text-align:center; width:344px; margin:0 auto;}
/* @ print rma
********************************************************************************************
********************************************************************************************/
.print-inner{ width:690px; overflow:hidden;}
.product-img{ margin:0 30px 0 0; height:141px; background:#e9e9e9; float:left; border:1px solid #cfcfcf; width:114px;}
.product-detail{ width:544px; float:left;text-align:left}
.print-inner .return-btn{ width:237px; font-size:18px; position:relative; margin:0 0 9px;}
.print-inner .return-btn img{ position:absolute; left:16px; top:12px;}
.print-inner strong{ display:block; margin:0 0 9px;}

.follwing-note{ padding:7px; background:#e9e9e9; margin:0 0 13px;}
.follwing-note ul{ margin:0; padding:0; list-style:none;}
.follwing-note ul li{ margin:0 0 5px; font-size:14px;text-align:left;}
.follwing-note ul li img{ margin:0 5px 0 0;}

.sipmle-box{ /*width:579px;*/ margin:0 auto; /*height:115px;*/ background:#e9e9e9; text-align:center;}
.sipmle-box img{height:100% !important;width:100% !important;}
.select-active{ background-color:#fff !important;color:#000;}
  </style>
        <noscript>
  This page needs JavaScript activated to work. 
  <style>.wrapperx { display:none; }</style>
</noscript>
  <section class="wrapperx"> 
	<div class="cart-holder">
		<div class="aside">
        	<div class="logo-holder">
            	<h1><a href="#"><img src="image/ez-logo.png" alt="logo"></a></h1>
            </div>
            <ul class="order-detailx">
            	<li><a href="javascript:void(0);" class="ez-return-active"><strong class="number-style">1</strong><span class="num">Enter Order Details</span></a></li>
                <li><a href="javascript:void(0);"><strong class="number-style">2</strong><span class="num">Select Items</span></a></li>
                <li><a href="javascript:void(0);"><strong class="number-style">3</strong><span class="num">Print Return Label</span></a></li>
            </ul>
        </div>
        <div id="xcontent">
        
    	<div class="selet-item-holder">
        	<div class="content-header">
            	<h2>ORDER LOOKUP</h2>
                <p>Enter a combination of either your original Order ID, Email Address<br> 
or Zipcode to proceed to the next step.</p>
            </div>
            <div class="content-inner">
            
            	<form>
                     <input type="text" class="form-control" name="order_id"  placeholder="Order ID">
                     <input type="text" class="form-control" name="email"  placeholder="Enter email">
            	</form>
                <span class="or-sapret"><strong class="or">OR</strong></span>
                <form>
                     <input type="text" class="form-control" name="order_id2"  placeholder="Order ID">
                     <input type="text" class="form-control" name="postcode"  placeholder="Zip Code">
            	</form>
				<a class="return-btn" id="begin_return" href="javascript:void(0);">BEGIN RETURN PROCESS</a>
            </div>
        </div>
        </div>
    </div>
</section>
  <?php echo $content_bottom; ?></div>

<script type="text/javascript"><!--
<?php if ($rxOrder && $rxProduct) { ?>
	$(document).ready(function() {
		showProducts('<?php echo $rxOrder; ?>', '<?php echo $rxProduct; ?>');
	});
<?php } ?>

$('#begin_return').click(function(e) {
BeginReturn();
});
function BeginReturn()
{
	
	$.ajax({
		url: '<?php echo HTTPS_SERVER; ?>index.php?route=account/return/validateNewReturn',
		data:$('#xcontent :input'),
		dataType:"json",
		beforeSend: function() {
			$('.warning').remove();
			$('#begin_return').before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			$('#begin_return').attr('disabled','disabled');
			$('#xcontent2').show();
		},
		complete: function() {
			$('.wait').remove();
			
		},
		success: function(json) {
			if (json['error']) {
			$('#xcontent2').hide();	
				alert(json['error']);
			} else {
				
				
				showProducts(json['success']);
				
			}
			
		
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	
}




function showProducts(order_id, product_id)
{
	var product = '';
	if (product_id) {
		product = '&product_id='+product_id;
	}
	$.ajax({
		url: '<?php echo HTTPS_SERVER; ?>index.php?route=account/return/showProducts&order_id=' + order_id + product,
		
		
		beforeSend: function() {
			$('.warning').remove();
			
		},
		complete: function() {
			$('#xcontent2').hide();
		},			
		success: function(data) {
			
			$('.order-detailx li:first a').removeClass('ez-return-active');
			$('.order-detailx li:eq(1) a').addClass('ez-return-active');
			
			//$('#xcontent').hide('fadeOut');
			$('#xcontent').html(data);
			//$('#xcontent').show('fadeIn');
		
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	
	
}

//--></script>  
<?php echo $footer; ?>