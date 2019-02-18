<?php echo $header; ?>
<!-- WholeSale Form -->
<link rel="stylesheet" type="text/css" class="ui" href="catalog/view/theme/bt_optronics/stylesheet/global_style.css">
<link rel="stylesheet" type="text/css" class="ui" href="catalog/view/theme/bt_optronics/stylesheet/jquery.fancybox.css">
<!--<link rel="stylesheet" type="text/css" href="css/docs.css">-->
<link rel="stylesheet/less" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/form.less" />
<link rel="stylesheet/less" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/popup.less" />
<link rel="stylesheet/ess" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/accordian.less" />
<style type="text/css" media="screen">
	el {
		color: red;
		font-size: 10px;
	}
	.errorInput {
		border-color: red !important;
	}
</style>
<!-- WholeSale Form -->
<!-- WholeSale Form -->
<script src="catalog/view/javascript/easing_min.js"></script>
<script src="catalog/view/javascript/highlight_min.js"></script>
<script src="catalog/view/javascript/history_min.js"></script>
<script src="catalog/view/javascript/tablesort_min.js"></script>
<script src="catalog/view/javascript/semantic_min.js"></script>
<script src="catalog/view/javascript/form_design.js"></script>
<script src="catalog/view/javascript/docs.js"></script>
<script src="catalog/view/javascript/less_min.js"></script>
<script src="catalog/view/javascript/popup.js"></script>
<script type="text/javascript" src="http://cdn.transifex.com/live.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery.fancybox.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery.SimpleMask.js"></script>
<script type="text/javascript" src="catalog/view/javascript/scripts.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#fancybox-manual-b").click(function() {
			$.fancybox.open({
				href : '#open_register_box',
				padding : 5
			});
		});
		$("#fancybox-manual-c").click(function() {
			$.fancybox.open({
				href : '#open_register_box_two',
				padding : 5
			});
		});
		$('input[name="first_name"]').focus();
	});
</script>
<!-- Wholesale Form -->
<script type="text/javascript" charset="utf-8" async defer>
	$(document).ready(function (e) {
		$('.fancybox3').fancybox({
			width: '600px',
			'height': 300,
			autoCenter: true,
			autoSize: false,
			afterClose : function(){
				$('#submit-form').trigger( "click" );
			}
		});
	});
</script>
<div id="content"><?php echo $content_top; ?>
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<!-- <h1><?php echo $heading_title; ?></h1> -->
	<form class="ui form" onclick="" id="upload-form" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
		<div class="wholesale_container">
			<div class="field">
			<img src="https://phonepartsusa.com/image/data/business-customer-benefits.jpg" alt="Business Customer Benefits">
			</div>
			<div class="field" style="text-align:center">
				<img src="catalog/view/theme/bt_optronics/image/wholesalelogo.jpg" alt="Phonepartsusa.com" />
			</div>
			<?php if ($error_warning) { ?>
			<div class="warning"><?php echo $error_warning; ?></div>
			<?php } ?>

			<?php if ($error_form) { ?>
			<div class="warning"><?php echo $error_form; ?></div>

			<?php if ($error_emailVerify) { ?>
			<div class="warning"><?php echo $error_emailVerify; ?></div>
			<?php } ?>

			<?php if ($error_license_no) { ?>
			<div class="warning"><?php echo $error_license_no; ?></div>
			<?php } ?>

			<?php } ?>
			<div class="left_side">
				<h2 class="ui dividing header">Personal Information</h2>
				<div class="field" style="margin:0 0 20px 0;">
					<label>Name <el>*</el></label>
					<div class="two fields">
						<div class="field">
							<input placeholder="First Name" tabindex="0"  name="first_name" <?= ($error_first_name) ? 'class="errorInput"': '';?> value="<?= $first_name; ?>" type="text">
							<span class="error">

							</span>
						</div>
						<div class="field">
							<input placeholder="Last Name" name="last_name" <?= ($error_last_name) ? 'class="errorInput"': '';?> value="<?= $last_name; ?>" type="text">
							<span class="error">
							</span>
						</div>
					</div>
				</div>
				<div class="field">
					<label>Telephone <el>*</el></label>
					<div class="three fields">
						<div class="field">
							<input placeholder="Office" name="office" value="<?= $office; ?>" type="text" class="phone <?= ($error_office) ? 'errorInput': '';?>" maxlength="15" id="frTel">
							<span class="error"></span>
						</div>
						<div class="field">
							<input placeholder="Mobile" name="mobile" value="<?= $mobile; ?>" type="text" class="phone" maxlength="15"  id="frCep">
							<span class="error">
							</span>
						</div>

					</div>
				</div>
				<div class="field">
					<label>E-mail <el>*</el></label>
					<input placeholder="Personal Email Address" id="wholeEmail" <?= ($error_personal_email) ? 'class="errorInput"': '';?>  name="personal_email" value="<?= $personal_email; ?>" type="email">
					<span class="error">
					</span>
				</div>
				<h2 class="ui dividing header">Company Information</h2>
				<div class="field">
					<!-- <label>Position</label>-->
					<input placeholder="Position" <?= ($error_position) ? 'class="errorInput"': '';?> name="position" value="<?= $position; ?>" type="text">
					<span class="error"></span>
				</div>
				<div class="field">
					<!--<label>Company Name</label>-->
					<input placeholder="Company Name" <?= ($error_company_name) ? 'class="errorInput"': '';?> name="company_name" value="<?= $company_name; ?>" type="text">
					<?php if ($error_company_name) { ?>
					<!-- <span class="error"><?php echo $error_company_name; ?></span> -->
					<?php } ?>
				</div>
				<div class="field">
					<!--<label>Address</label>-->
					<div class="fields">
						<div class="twelve wide field">
							<input placeholder="Street Address" <?= ($error_address) ? 'class="errorInput"': '';?> name="address" value="<?= $address; ?>" type="text">
							<span class="error"></span>
						</div>
						<div class="four wide field">
							<input placeholder="Suite" name="suite" <?= ($error_suite) ? 'class="errorInput"': '';?> value="<?= $suite; ?>" type="text">
							<?php if ($error_suite) { ?>
							<!-- <span class="error"><?php echo $error_suite; ?></span> -->
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="three fields">
					<div class="field">
						<!--<label>Zip Code</label>-->
						<input placeholder="Zip Code" <?= ($error_zip_code) ? 'class="errorInput"': '';?> name="zip_code" value="<?= $zip_code; ?>" type="text">
						<?php if ($error_zip_code) { ?>
						<!-- <span class="error"><?php echo $error_zip_code; ?></span> -->
						<?php } ?>
					</div>
					<div class="field">
						<!--<label>City</label>-->
						<input placeholder="City" <?= ($error_city) ? 'class="errorInput"': '';?> name="city" value="<?= $city; ?>" type="text">
						<?php if ($error_city) { ?>
						<!-- <span class="error"><?php echo $error_city; ?></span> -->
						<?php } ?>
					</div>
					<div class="field">
						<!--<label>State</label>-->
						<?php $stateClass = ($error_state) ? 'errorInput ui fluid dropdown': 'ui fluid dropdown'; ?>
						<?php $safariClass = ($error_state) ? 'errorInput': ''; ?>
						<select name="state" <?= (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari'))? 'class="'. $safariClass .'"': 'class="'. $stateClass .'"'; ?> style="color: #a9a9a9; border: 1px solid #9a9a9a;">
							<option value="">State</option>
							<?php foreach ($states as $stat) { ?>
							<option value="<?= $stat['code']; ?>" <?=($stat['code'] == $state)? 'selected="selected"': '';?> ><?= $stat['name']; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

			</div>
			<div class="right_side">
				<h2 class="ui dividing header">Additional Information</h2>
				<!-- <div class="ui form" style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; padding:15px 0;"> -->
				<div class="grouped fields">
					<h3>Do You Have a Retail Location <el>*</el></h3>
					<div class="field">
						<div class="ui radio checkbox checked">
							<input class="hidden"  tabindex="0" name="retail_point" value="1" <?= ($retail_point == 1 || $retail_point == '')? 'checked=""' : '';?> type="radio">
							<label>Yes</label>
						</div>
					</div>
					<div class="field">
						<div class="ui radio checkbox">
							<input class="hidden" tabindex="0" name="retail_point" value="0" <?= ($retail_point == '0')? 'checked=""' : '';?> type="radio">
							<label>No</label>
						</div>
					</div>
				</div>
				<h3 style="margin:0 0 10px 0">
					Number Of Weekly Repair? 
					<el>*</el> 
				</h3>
				<div class="field" style="width:50%;">
					<select name="repairs" <?= (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari'))? '': 'class="ui fluid dropdown"'; ?> style="color: #a9a9a9; border: 1px solid #9a9a9a;">
						<?php $repairsItems = array('1-10 Phones', '20-50 Phones', '50-200 Phones', '200+ Phones');?>
						<?php foreach ($repairsItems as $value) { ?>
						<option value="<?= $value; ?>" <?= ($value == $repairs) ? 'selected="selected"': '';?>><?= $value; ?></option>
						<?php } ?>
					</select>
				</div>
				<h3 style="margin:0 0 10px 0">Which Parts Interest You Most? <el>*</el></h3>
				<div class="ui form">
					<div class="inline field" style="margin:0 0 20px 0;">
						<?php $intrestedItems = array('LCD Screens', 'Touch', 'Flex Cables', 'Accessories');?>
						<?php foreach ($intrestedItems as $value) { ?>
						<div class="ui checkbox" style="margin:15px 15px 0 0;">
							<input class="hidden" name="intrested[]" value="<?= $value; ?>" <?= (in_array($value, $intrested)) ? 'checked="checked"': ''; ?> tabindex="0" type="checkbox">
							<label <?= ($error_zip_code) ? 'style="color: red;"': '';?>><?= $value; ?></label>
						</div>
						<?php } ?>
					</div>
					<span class="error">
					</span>
				</div>
				<!-- </div> -->
				<h2 class="ui dividing header">Upload Business License</h2>
				<?= $text_1; ?>
				<div class="ui form" style="margin:20px 0 0 0;">
					<div class="fields">
						<div class="twelve wide field">
							<input type="text" name="license_no" placeholder="" <?= ($error_license_no) ? 'class="errorInput"': '';?> value="<?= $license_no; ?>">
							<span class="error"></span>
						</div>
						<div class="four wide field">
							<!-- <button class="ui blue button">Upload</button> -->
							<label class="ui blue button" style="color: #fff; position: relative;" for="business_license" onclick="">
								<input type="file" style="opacity: 0; position: absolute; width: 100px; height: 37px; top: 0; left: 0;" onchange="uploadFile(this)" name="image" id="business_license" accept="image/jpeg,image/png,application/pdf,application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoin, image/tiff">
								Upload
							</label>
							<span id="imagemessage" class="error"></span>
							<style type="text/css" media="screen">
								#viewFile, #removeFile {
									margin: 0px;
									float: left;
									font-size: 14px;
									padding-left: 20px;
									background-repeat: no-repeat;
									color: #000;
								}
								#viewFile {
									background-image: url('http://phonepartsusa.com/dev2/image/google_docs.jpg');
								}
								#viewFile:hover {
									color: #red;
								}
								#removeFile {
									float: right;
									width: 15px;
									height: 15px;
									background-image: url('http://phonepartsusa.com/dev2/image/delete-iconb.png');
								}
								#removeFile:hover {
									background-image: url('http://phonepartsusa.com/dev2/image/delete-icon.png');
								}
							</style>
							<span id="imageControle" <?= ($business_license == '')? 'style="display: none;"': '';?>>
								<a id="viewFile" target="_blank" href="<?= HTTP_IMAGE . $business_license; ?>">View File</a>
								<a id="removeFile" href="javascript:void(0)"></a>
							</span>
							<input type="hidden" name="business_license" value="<?= $business_license; ?>" id="ImageName">
							<script type="text/javascript" charset="utf-8" async defer>
								function uploadFile (e) {
									var file = $(e).val().split(".");
									var ext = file.pop();
									var allowed = ['png', 'tiff', 'tif', 'jpeg', 'jpg', 'doc', 'docx', 'xls', 'xlsx', 'pdf'];
									var img = 'img';
									if (ext == 'pdf') {
										img = 'pdf';
									}
									if (ext == 'doc' || ext == 'docx') {
										img = 'doc';
									}
									if (ext == 'xls' || ext == 'xlsx') {
										img = 'xls';
									}
									if ($.inArray(ext, allowed) >= 0) {
										var formData = new FormData();
										formData.append('file', $('#business_license')[0].files[0]);
										$.ajax({
											url: "index.php?route=wholesale/wholesale/uplaodFile",
											type: "POST",
											data:  formData,
											dataType: "json",
											contentType: false,
											cache: false,
											processData:false,
											success: function(json){
												if (json['success']) {
													$(e).parent().parent().find('.error').text('');
													$('#ImageName').val(json['file']);
													$('#viewFile').attr('href', json['msg']).css('background-image', 'url("http://phonepartsusa.com/dev2/image/'+ img +'.jpg")');;
													$('#imageControle').fadeIn();
												}
												if (json['error']) {
													$('#imageControle').hide();
													$(e).parent().parent().find('.error').text('').text(json['msg']);
												}
											},
											error: function(){} 	        
										});
									} else {
										alert('This File is not Allowed');
									}
								}
								$('#removeFile').on('click', function () {
									var file = $('#ImageName').val();
									$.ajax({
										url: "index.php?route=wholesale/wholesale/removeFile",
										type:"POST",
										dataType:"json",
										data:  {'file':file},
										success: function(json){
											if (json['success']) {
												$('#ImageName').val('');
												$('#imageControle').hide();
												$('#imagemessage').text(json['msg']);
											}
										},
										error: function(){} 	        
									});
								});
							</script>
						</div>
					</div>
				</div>
				<h2 class="ui dividing header">PhonePartsUSA Registered Email</h2>
				<?= $text_2; ?>
				<div class="ui form" style="margin:20px 0 0 0;">
					<div class="fields">
						<div class="eight wide field">
							<input type="email" id="verify" <?= ($error_emailVerify) ? 'class="errorInput"': '';?> name="email" value="<?= $email; ?>" onchange="$('emailVerify').val(''); $(this).parent().find('.error').text('');" placeholder="Registered Email">
							<input type="hidden" id="emailVerify" name="emailVerify">
							<span id="emailVMessage" class="error">
							</span>
						</div>
						<div class="four wide field">
							<a class="ui button" style="width:100%;" href="javascript:void(0)" onclick="verifyEmail(this)">Verify</a>
							<script type="text/javascript" charset="utf-8" async defer>
								$('input[type="email"]').focusout(function () {
									var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
									if ($(this).val() == '' || !re.test($(this).val())) {
										$(this).parent().find('.error').text('Please Enter a Valid Email');
									} else {
										$(this).parent().find('.error').text('');
									}
								});
								function verifyEmail (e) {
									var email = $('#verify').val();
									if (email) {
										$.ajax({
											url: "index.php?route=wholesale/wholesale/verifyEmail",
											type:"POST",
											dataType:"json",
											data:  {'email':email},
											success: function(json){
												if (json['success']) {
													$('#emailVerify').val('success');
													$('#btnRegister').hide();
													$('#verify').parent().removeClass('six').addClass('eight');
													$('#btnRegister').parent().removeClass('six').addClass('four');
													$('#emailVMessage').text('').append(json['msg']);
												}
												if (json['error']) {
													$('#emailVerify').val('error');
													$('#verify').parent().removeClass('eight').addClass('six');
													$('#btnRegister').parent().removeClass('four').addClass('six');
													$('#btnRegister').attr('href', 'index.php?route=account/register/wholesale&email=' + email).show();
													$('#emailVMessage').text(json['msg']);
												}
											}
										});
									} else {
										$('#emailVMessage').text('Please Enter Email!');
									}
								}
							</script>
						</div>
						<div class="four wide field">
							<a class="ui primary button fancybox3 fancybox.iframe" id="btnRegister" style="display: none;" href="index.php?route=account/register/wholesale" style="width:100%;">Create Account</a>
						</div>
					</div>
					<div id="open_register_box" style="width:400px; padding:25px; display:none;">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent bibendum porttitor justo scelerisque rhoncus. Suspendisse in massa a ipsum consequat fringilla ac sit amet eros. Cras in felis mattis, dapibus massa eu, tristique risus. </p>
					</div>
					<div id="open_register_box_two" style="width:400px; padding:25px; display:none;">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent bibendum porttitor justo scelerisque rhoncus. Suspendisse in massa a ipsum consequat fringilla ac sit amet eros. Cras in felis mattis, dapibus massa eu, tristique risus. </p>
					</div>
				</div>
			</div>
			<div style="clear:both"></div>
			<?= $text_3; ?>
			<div style="text-align:center; padding:100px 0 0 0;">
				<input class="ui button" style="width:50%;font-size:16px !important;" type="submit" name="submit" value="Submit Application" id="submit-form">
			</div>
		</div>
	</form>
	<?php echo $content_bottom; ?></div>
	<script>
		$('select.dropdown')
		.dropdown()
		;
	</script>
	<script>
		$('.example .custom.button')
		.popup({
			popup : $('.custom.popup'),
			on    : 'click'
		})
		;
		$('.example .teal.button')
		.popup({
			on: 'hover'
		})
		;
		$('.example input')
		.popup({
			on: 'focus'
		})
		;
// Function
function phoneMask(e){
	var s=e.val();
	var s=s.replace(/[_\W]+/g,'');
	var n=s.length;
	if(n<11){var m='(00) 0000-00000';}else{var m='(00) 00000-00000';}
	//$(e).mask(m);
}

// Type
$('.three').on('keyup','.phone',function(){ 
	phoneMask($(this));
});

// On load
$('.phone').keyup();
</script>
<script>
	semantic.accordion = {};

// ready event
semantic.accordion.ready = function() {

  // selector cache
  var
  $accordion     = $('.ui.accordion'),
  $menuAccordion = $('.ui.menu.accordion'),
  $checkbox      = $('.ui.checkbox'),
  // alias
  handler
  ;
  $accordion
  .accordion()
  ;
  $menuAccordion
  .accordion({
  	exclusive: true
  })
  ;
  $checkbox
  .checkbox()
  ;

};


// attach ready event
$(document)
.ready(semantic.accordion.ready)
;


</script>
<?php echo $footer; ?>