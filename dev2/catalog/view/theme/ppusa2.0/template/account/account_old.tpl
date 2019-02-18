<style type="text/css">
	.tabs-nav-display{
		display: none;
		background: #fff;
    	padding-top: 40px;
	}
</style>
<?php echo $header;?>
<!-- @End of header -->
<head>
<script type="text/javascript" src="catalog/view/javascript/ppusa2.0/jquery.canvasjs.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/bossthemes/bossthemes.js"></script>
</head>
	<main class="main">
		<div class="container repair-parts-page">
			<div class="row">
				<div class="col-md-12 intro-head">
					<h1 class="blue blue-title uppercase">account control center</h1>
					<h3><?php echo $customer_name?>, welcome to your PhonePartUSA.com account!</h3>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs small five-tabs">
					    <li class="active"><a id="" href="javascript:void(0)" onclick="showTab('Dashboard')" >Dashboard</a></li>
					    <li><a id="" href="javascript:void(0)" onclick="showTab('Settings')" >Settings</a></li>
					    <li><a id="" href="javascript:void(0)" onclick="showTab('History')" >History</a></li>
					    <li><a id="" href="javascript:void(0)" onclick="showTab('Communication')" >Communications</a></li>
					    <li><a id="" href="javascript:void(0)" onclick="showTab('List')" >Lists</a></li>
					</ul>
					<div class="tab-content">
					    <div id="Dashboard" class="tab-pane active tabs-nav-display">
					    	<div class="tab-inner">
						    	<div class="row service-row">
						    		<div class="col-md-9">
						    			<h3 class="uppercase">Contact Information</h3>
								    	<ul class="list-inline service-links">
								    		<li><?php echo $customer_name?></li>
								    		<li><?php echo $customer_business?></li>
								    		<li><?php echo $telephone?></li>
								    		<li><?php echo $customer_email?></li>
								    	</ul>
								    	<div class="text-center mt20">
								    		<a href="<?php echo $_SERVER['REQUEST_URI']; ?>#Settings" id="setting-button" class="btn btn-primary">Edit</a>
								    	</div>
								    	<div>
								    		$lbb;
								    	</div>
						    		</div>
						    		<div class="col-md-3 pull-right">
						    			<h3 class="uppercase text-center">Special Orders</h3>
						    			<div class="text-right">
						    				<a href="" class="btn btn-primary">Request a product</a>
						    			</div>
						    		</div>
						    	</div>
					    	</div>
					    	<div class="border"></div>
					    	<div class="tab-inner pd60">
					    		<h3 class="uppercase">open orders</h3>
					    		<div class="parent"></div>
					    		<div class="h-317">
						    		<div class="scroll">
						    		<?php echo $orders;?>
						    		</div>
					    		</div>
					    	</div>
					    	<div class="border"></div>
					    	<div class="tab-inner pd60">
					    		<div class="row">
					    			<div class="col-md-8 col-sm-7 ">
					    				<h3 class="uppercase">current credit  vouchers</h3>
					    			</div>
					    			<div class="col-md-4  col-sm-5 text-right all-voucher text-xs-left">
					    				<a href="<?php echo $viewvouchers; ?>" class="btn btn-primary mb-xs-20">view all vouchers</a>
					    			</div>
					    		</div>
					    		<div class="parent"></div>
					    		<div class="h-180">
						    		<div class="scroll2">
						    		<?php echo $vouchers;?>
						    		</div>
					    		</div>
					    	</div>
					    	<div class="border"></div>
					    	<div class="tab-inner pd60 pb60 pdr0">
					    			<div id="chartContainer" style="height: 350px; "></div>
					    			
					    		</div>
					    	</div>
					    </div>
					    <div id="Settings" class="tab-pane tabs-nav-display" >
					    <div class="tab-inner">
					    <?php echo $settings;?>
					    </div>
					    </div>
					    <div id="History" class="tab-pane tabs-nav-display" >
					    <div class="tab-inner pd60">
					    	<div class="row">
					    		<div class="col-md-8 col-sm-7 ">
					    		<h3 class="uppercase">order history</h3>
					    	</div>
					   	</div>
					    		<div class="parent"></div>
					    		<div class="h-317">
						    		<div class="scroll">
						    		<?php echo $orders;?>
						    		</div>
					    		</div>
					    </div>
					    <div class="border"></div>
					    	<div class="tab-inner pd60">
					    		<div class="row">
					    			<div class="col-md-8 col-sm-7 ">
					    				<h3 class="uppercase">credit  vouchers history</h3>
					    			</div>
					    		</div>
					    		<div class="parent"></div>
					    		<div class="h-180">
						    		<div class="scroll2">
						    		<?php echo $vouchers;?>
						    		</div>
					    		</div>
					    	</div>
					    <div class="border"></div>
					    	<div class="tab-inner pd60">
					    		<div class="row">
					    			<div class="col-md-8 col-sm-7 ">
					    				<h3 class="uppercase">return history</h3>
					    			</div>
					    		</div>
					    		<div class="parent"></div>
					    		<div class="h-180">
						    		<div class="scroll2">
						    		<?php echo $template_returns;?>
						    		</div>
					    		</div>
					    	</div>
					    	<div class="border"></div>
					    	<div class="tab-inner pd60">
					    		<div class="row">
					    			<div class="col-md-8 col-sm-7 ">
					    				<h3 class="uppercase">LCD Buy back History</h3>
					    			</div>
					    			<div class="col-md-4  col-sm-5 text-right all-voucher text-xs-left">
					    				<a href="<?php echo $lbb; ?>" class="btn btn-primary mb-xs-20">view all LBB</a>
					    			</div>
					    		</div>
					    		<div class="parent"></div>
					    		<div class="h-180">
						    		<div class="scroll2">
						    		<?php echo $buyback;?>
						    		</div>
					    		</div>
					    	</div>
					    </div>
					    <div id="Communication" class="tab-pane tabs-nav-display" >
					    	<?php echo $communications;?>
					    </div>
					    <div id="List" class="tab-pane tabs-nav-display" >
					    <input type="hidden" name="theme" value="2">
					    <?php echo $lists;?>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</main><!-- @End of main -->
	<script type="text/javascript">


window.onload = function () {
	$.ajax({
		url: 'index.php?route=account/order/getOrder',
		dataType: 'json',			
		success: function(json) {

			points = [];
			 if (json != '') {
			 	for (i = 0; i < json.length; i++) {
			 		value = parseInt(json[i]['total'].split('$')[1]);
					points.push({label: json[i]['date_added'], y: value });
			 	}
			 }
			 var chart = new CanvasJS.Chart("chartContainer", { 
					title: {
						text: "Recent Order History"
					},
					data: [
						{
							type: "area",
							dataPoints: points
						}
					],
					axisY:{
			      prefix: "$"
			    },
			    axisX:{
			    	labelFontSize: 10
			    }
				});
			 chart.render();
		},
		error: function(xhr, ajaxOptions, thrownError) {
		}
	});



}

function showTab(tab)
{
	$('.tabs-nav-display').hide();
	$('#'+tab).show();
}

</script>
<?php echo $footer; ?>
<!-- @End of footer -->