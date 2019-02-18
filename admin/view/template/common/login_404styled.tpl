<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="NOINDEX, NOFOLLOW" />
	<title>404 Page Not Found</title>
	<style type="text/css">

		body { background: #ffffff; position: relative; }
		.clear { clear: both; }
		h1, h2, h3, h4, h5, h6 { font-weight: normal; }
		p { padding-bottom: 10px; }

		#wrapper { text-align: left; width: 960px; margin: 0 auto; position: relative;  }
		img#error-img { margin: 20px 0px 0px 200px; }
		#info { width: 637px; margin: 35px auto 0px; }
		p#error span { color: #ce1919; }
		div#hr { width: 635px; height: 22px; background: url('<?php echo HTTP_CATALOG;?>image/data/404/hr.png') no-repeat; margin-top: 20px; }
	</style>
    <?php if ($security_override) { ?>
	<link type="text/css" href="view/javascript/jquery/msgbox/jquery.msgbox.css" rel="stylesheet" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type="text/javascript" src="view/javascript/jquery/hotkeys/jquery.hotkeys.js"></script>
	<script type="text/javascript" src="view/javascript/jquery/msgbox/jquery.msgbox.min.js"></script>
	<?php 
	$javascriptCode = '$(function() { $(document).bind("keydown", "' . $security_sequence . '", doOverride);});
	function doOverride() {
		$.msgbox("<p>Please enter security key to access login screen:</p>", {
			type    : "prompt",
			inputs  : [ {type: "text", label: "Security Key:", value: "", required: true} ],
			buttons : [ {type: "submit", value: "OK"}, {type: "cancel", value: "Cancel"}]
			}, function(result) {
	    		if (result) {
					var myForm = document.createElement("form");
					myForm.method = "post";
					myForm.action = "' . str_replace('&amp;', '&', $this->url->link('common/login', '', 'SSL')) . '";
					var myInput = document.createElement("input");
					myInput.setAttribute("name", "security_passkey");
					myInput.setAttribute("value", result);
					myForm.appendChild(myInput);
					document.body.appendChild(myForm);
					myForm.submit();
					document.body.removeChild(myForm);
				}
			}
		);
	}';
	?>
	<script type="text/javascript" src="data:text/javascript;base64,<?php echo base64_encode($javascriptCode); ?>"></script>
	<?php } ?>
</head>
<body>
	<div id="wrapper">
		<img src="<?php echo HTTP_CATALOG;?>image/data/404/404.jpg" alt="Requested page not found" id="error-img" style="padding-left:90px;" />
		<div id="info">
			<div id="hr"></div>
			<p id="error" style="text-align: center; text-align: center; line-height: 30px; font-family: 'Lucida Grande','Lucida Sans Unicode', Tahoma, Arial, sans-serif; font-size: 22px; color: #5b5a5a; "><span>Uh-Ohh...</span> We couldn't find what you are looking for.</p>
		</div> <!-- end info div -->
	</div> <!-- end wrapper div -->
</body>
</html>