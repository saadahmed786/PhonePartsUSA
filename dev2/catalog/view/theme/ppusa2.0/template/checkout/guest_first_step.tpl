<div class="tab-inner pd100">
	<!-- <h3 class="blue-title uppercase mb40">billing address</h3> -->
	<div class="row">
	<div class="col-md-5 text-sm-center" style="margin-bottom: 20px" >
	<h3 class="blue-title uppercase mb40 mb-xs-7">GUEST CHECKOUT</h3>
	<p style="font-size:10px;color:grey;line-height:15px;" class="hidden-md hidden-lg mb-xs-7">You will have the opportunity to create an account and track your order once you complete your checkout.</p>
	<div class="form-group has-feedback">
    <!-- <label class="control-label">Email</label> -->
    <i class="glyphicon glyphicon-user form-control-feedback"></i>
    <input type="text" class="form-control" id="guest_email_first_step" placeholder="Email Address" />
    
</div>
<p style="font-size:12px;color:grey;line-height:20px;" class="hidden-xs hidden-sm">You will have the opportunity to create an account and track your order once you complete your checkout.</p>

<a id="button-guest_first_step" style="margin-bottom:10px" class="btn btn-primary mt50 mt-xs-10">Continue <i class="fa  fa-chevron-right"></i></a>
	</div>
	<div class="col-md-1"></div>
	<div class="col-md-6 text-sm-center" >
			<h3 class="blue-title uppercase mb40 mb-xs-7">I'M A RETURNING CUSTOMER</h3>
			<form id="login_frm" action="<?php echo $this->url->link('account/login'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
			<div class="form-group has-feedback" style="margin-right:0px;margin-left:0px">
    <!-- <label class="control-label">Email</label> -->
    <i class="glyphicon glyphicon-user form-control-feedback"></i>
    <input type="text" class="form-control" name="email" placeholder="Email Address" required />
    
</div>

<div class="form-group has-feedback" style="margin-right:0px;margin-left:0px">
    <!-- <label class="control-label">Email</label> -->
    <i class="glyphicon glyphicon-lock form-control-feedback"></i>
    <input type="password" name="password" class="form-control" placeholder="Password" required />
    
</div>
<div class="col-sm-12 text-right" style="padding:0px"> <a style="font-size: 12px; color: grey; margin-top: -10px;" href="<?php echo $this->url->link('account/forgotten');?>" class="underline">Reset Password</a></div>
<a id="login_first_step" onclick="$('#login_frm').submit();"  class="btn btn-primary mt-xs-10 mt45">Login <i class="fa  fa-chevron-right"></i></a> 

<input type="hidden" name="redirect" value="<?php echo $this->url->link('checkout/checkout');?>" />
<input type="hidden" name="is_checkout" value="1" />
</form>

	</div>
	</div>
<br><br>	
</div>
<script type="text/javascript">
$(document).keypress(function(e) {
var key = e.which;
if (key == 13) {
$('#login_frm').submit(); // Submit form code
}
});
</script>

