<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="NOINDEX, NOFOLLOW" />
	<title>404 Page Not Found</title>
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
    <h1>Not Found</h1>
    <p>The requested URL /admin was not found on this server.</p>
    <p>Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.</p>
<hr />
<address>Apache Server at <?php echo $_SERVER['HTTP_HOST']; ?> Port 80</address>
</body></html>