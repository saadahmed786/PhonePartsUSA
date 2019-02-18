<?php
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
require_once("../auth.php");
require_once("../inc/functions.php");

$buyback_id = $db->func_escape_string($_GET['buyback_id']);
$buyback_id = (int)$buyback_id;
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE buyback_id='".$buyback_id."'");


	?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Store Credit</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
</head>
    <?php
	
if(isset($_POST['tracking_no']))
{
	$db->db_exec("UPDATE oc_buyback SET tracking_no='".$db->func_escape_string($_POST['tracking_no'])."' WHERE buyback_id='".$buyback_id."'");
	$log = 'LBB Shipment ' . linkToLbbShipment($db->func_query_first_cell('SELECT shipment_number FROM oc_buyback WHERE buyback_id = "' . $buyback_id . '"')) . ' Recived';
	actionLog($log);
	echo '<script> $("input[name=received]", window.parent.document).click();</script>';exit;

}
?>
<body>
	<div align="center">


		
		

		<br clear="all" />



		<div align="center">
			<form action="" id="myFrm" method="post">
				<h2>Tracking No.</h2>
				<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">
				
					

				
					

					<tr>
						<td>Tracking #</td>
						<td><input type="text" name="tracking_no" id="tracking_no" value="" placeholder="Please provide with tracking no." style="width:200px"/></td>
					</tr>
					
					
					
					<tr>
						<td colspan="2" align="center">
							<input type="button" name="add" value="Receive Shipment" onclick="submitForm()" />
						</td>
					</tr>
				</table>
			</form>
		</div>		

		<script>
			
				
				function submitForm()
				{
					if($('#tracking_no').val()=='')
					{
						alert('Please provide with Tracking Number');
						return false;	
		
					}
					
				$('#myFrm').submit();

			}
		</script> 
	</div>		     
</body>
</html>