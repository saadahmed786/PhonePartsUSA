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
		<div class="alert alert-danger alert-dismissible" style="display:none" role="alert"></div>
		
			<div class="row">
				<div class="col-md-12 intro-head">
					<div class="col-md-3 text-right col-xs-4  col-md-offset-2"><i class="fa fa-user fa-5x" style="color:4986fe"></i></div> <div class="col-md-3"> <span class="blue blue-title uppercase" style="font-size:32px;font-weight:bold">account</span><br><span class="blue blue-title uppercase" style="font-size:24px;font-weight:bold">dashboard</span></div><div class="col-md-2"></div>
					
				</div>
			</div>
			<div class="row" style="margin-top:10px">
			<div class="col-md-12  text-center">
			<h3 style="font-size:18px;font-weight:500"><?php echo $customer_name?>, welcome to your PhonePartsUSA.com account!</h3>
			</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs small five-tabs">
					    <li class="active" ><a id="dashboard_tab" href="javascript:void(0)" onclick="showTab('Dashboard')"  >Dashboard</a></li>
					    <li><a id="setting_tab" href="javascript:void(0)" onclick="showTab('Settings')" >Settings</a></li>
					    <!-- <li><a id="" href="javascript:void(0)" onclick="showTab('History')" >History</a></li> -->
					    <li><a id="" href="javascript:void(0)" onclick="showTab('Communication')" >Communications</a></li>
					    <li><a id="" href="javascript:void(0)" class="hidden-xs" onclick="showTab('List')" >Lists</a></li>
					</ul>
					<div class="tab-content">
					    <div id="Dashboard" class="tab-pane active tabs-nav-display">
					    	
					    	
					    	
					    	</div>
					    </div>
					    <div id="Settings" class="tab-pane tabs-nav-display" >
					    <div class="tab-inner">
					    <?php echo $settings;?>
					    </div>
					    </div>
					  
					    <div id="Communication" class="tab-pane tabs-nav-display" >
					    	<?php echo $communications;?>
					    </div>
					    <div id="List" class="tab-pane tabs-nav-display hidden-xs" >
					    <input type="hidden" name="theme" value="2">
					    <?php echo $lists;?>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</main><!-- @End of main -->
	<script type="text/javascript">




function showTab(tab)
{
	$('.tabs-nav-display').hide();
	$('#'+tab).show();
	
		$('#' + tab).html('<div style="text-align:center;"><img src="catalog/view/theme/ppusa2.0/images/spinner.gif" style="width:30%"></div>');
		$.ajax({
		url: 'index.php?route=account/account/getModule',
		data:{type:tab},
		dataType: 'html',			
		success: function(html) {
			if(tab=='Settings')
			{
				$('#'+tab).html('<div class="tab-inner">'+html+'</div>');
			}
			else
			{
			$('#'+tab).html(html);
				
			}
			$('.selectpicker').selectpicker('refresh');
			$(".address-scroll").slimScroll({
        height: "138px",
        size: "10px",
        color: "#4986fe",
        railVisible: !0,
        railColor: "#f4f4f4",
        alwaysVisible: !0
    });
			$(".scroll2").slimScroll({
        height: "auto",
        size: "10px",
        color: "#4986fe",
        railVisible: !0,
        railColor: "#f4f4f4",
        alwaysVisible: !0
    })

		},
		error: function(xhr, ajaxOptions, thrownError) {
		}
	});
	
}
<?php
if(isset($this->session->data['account_error']) || isset($this->session->data['success']) || isset($this->session->data['wholesale_account_user']))
{
?>
$(document).ready(function(){
// showTab('Settings');

$('#setting_tab').trigger('click');
});

<?php
unset($this->session->data['account_error']);
unset($this->session->data['success']);
}
else
{
	?>
$(document).ready(function(){
// showTab('Settings');

$('#dashboard_tab').trigger('click');
});
	<?php
}
?>

</script>
<?php echo $footer; ?>
<!-- @End of footer -->