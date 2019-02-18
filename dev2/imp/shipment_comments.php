<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$permission = 'user_id';

// $pageName = $_GET['shipment_name'] . ' Shipment comments';
$pageLink = 'shipment_comments.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`inv_shipment_comments`';
$shipment_id = (int)$_GET['shipment_id'];
$comments = $db->func_query("SELECT * FROM inv_shipment_comments WHERE shipment_id='".$shipment_id."'");
$shipment_detail = $db->func_query_first("SELECT * FROM inv_shipments where id='".$shipment_id."'");
$pageName = $shipment_detail['package_number'].' Shipment Comments';

if($_POST['addcomment']) {
	
	$_SESSION['message'] = addComment('shipment',array('id' => $shipment_id, 'comment' => $_POST['comment']));

	header("Location:shipment_comments.php?shipment_id=$shipment_id");
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		$(document).ready(function(e) {
			$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
		});

		function allowFloat (t) {
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (isNaN(input)) {
				$(t).val(valid);
			}
		}
		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

	</script>

</head>
<body>
	<div align="center">
		<div align="center" style="display:none">
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if($_SESSION['message']) { ?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php } ?>
			<h2><?= $pageName; ?></h2>
	</div>
	<div>
	<table align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
	<tr>
	<td width="60%">
		<table width="100%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
					<tr>
						<th width="15%">Date</th>
						<th>Comment</th>
						<th>Added By</th>
					</tr>
					<?php
					foreach($comments as $comment){?>
						
							<td style = "width:100px"><?php echo americanDate($comment['date_added']);?></td>
							<td style = "width:80px"><?php echo $comment['comment'];?></td>
							<td style = "width:40px"><?php echo get_username($comment['user_id']);?></td>
						</tr>
						<?php 
					}
					?> 
		</table>
	</td>
	<td width="40%">
		<form method="post" action="">
					<table width="100%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
						<tr>
							<td>

								<b>Comment</b>
							</td>
							<td>
								<textarea rows="5" style="width: 100%;" name="comment" required></textarea>


							</td>
						</tr>

						<tr>
							<td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />	</td>
						</tr> 	   
					</table>
				</form>
	</td>			
	<tr>
	</table>
	</div>
</body>