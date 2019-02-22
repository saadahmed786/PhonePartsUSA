
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="NOINDEX, NOFOLLOW" />
	<title>404 Page Not Found</title>
    	<link type="text/css" href="../admin/view/javascript/jquery/msgbox/jquery.msgbox.css" rel="stylesheet" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type="text/javascript" src="../admin/view/javascript/jquery/hotkeys/jquery.hotkeys.js"></script>
	<script type="text/javascript" src="../admin/view/javascript/jquery/msgbox/jquery.msgbox.min.js"></script>
		<script>

$(function() { $(document).bind("keydown", "ctrl+shift+s", doOverride);});
	function doOverride() {
		$.msgbox("<p>Please enter security key to access login screen:</p>", {
			type    : "prompt",
			inputs  : [ {type: "text", label: "Security Key:", value: "", required: true} ],
			buttons : [ {type: "submit", value: "OK"}, {type: "cancel", value: "Cancel"}]
			}, function(result) {
	    		if (result) {
					var myForm = document.createElement("form");
					myForm.method = "post";
					myForm.action = "<?php echo $host_path;?>index.php<?php echo ($_GET['browser']?'?browser=1':'');?>";
					var myInput = document.createElement("input");
					myInput.setAttribute("name", "security_passkey");
					myInput.setAttribute("value", result);

var myAction = document.createElement("input");
					myAction.setAttribute("name", "security_action");
					myAction.setAttribute("value", 'error_check');

					myForm.appendChild(myInput);
					myForm.appendChild(myAction);
					document.body.appendChild(myForm);
					myForm.submit();
					document.body.removeChild(myForm);
				}
			}
		);
	}

		</script>
	</head>
<body>
    <h1>Not Found</h1>
    <p>The requested URL /imp was not found on this server.</p>
    <p>Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.</p>
<hr />
<address>Apache Server at phonepartsusa.com Port 80</address>
</body></html>