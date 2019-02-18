<?php
require_once("../auth.php");
require_once("../inc/functions.php");
include_once '../inc/split_page_results.php';
$pageName='Shipment Sku Mapping';
$pageLink ="shipments_sku_mapping.php";
$pageCreateLink = false;
$pageSetting = false;
if ($_POST['getAjax']) {
	$json['lbb_data'] = $db->func_query("SELECT * FROM inv_lbb_sku_mapping WHERE lbb_sku = '". $_POST['fetching_lbb']['0'] ."'");
	echo json_encode($json);
	exit;
}
//Saving Record
if ($_POST['Add']) {
	$db->db_exec ( "delete from inv_lbb_sku_mapping where lbb_sku = '".$_POST['lbbsku']."'" );
	foreach($_POST['xdata'] as $key => $data)
	{
		$array = array();
		$array['product_sku'] = $db->func_escape_string($data['sku']);
		$array['lbb_sku'] = $_POST['lbbsku'];
		$db->func_array2insert("inv_lbb_sku_mapping",$array);
	}
	$_SESSION['message'] =' Skus Mapped ';
	header("Location:" . $pageLink);
	exit;
}
$lbb_skus = $db->func_query("SELECT * FROM inv_buy_back");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<link rel="stylesheet" type="text/css" href="include/jquery.autocomplete.css" media="screen" />
</head>
<body>
	<div align="center">
		<div align="center" style="display:none">
			<?php include_once '../inc/header.php';?>
		</div>
		<?php if($_SESSION['message']) { ?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php } ?>
		<form action="" method="post" enctype="multipart/form-data">
		<table>
		<tr>
		<td>
		<h2>LBB SKU </h2>
			<div align="left" border="1" width="60%" style="border:1px solid #585858;border-collapse:collapse;">
			<select multiple onchange="populateRow();"
			 required style="height:150px;" name="lbbsku" id="lbbsku">
				<?php if ($lbb_skus) { ?>
					<?php foreach ($lbb_skus as $key => $row) { ?>
						<option value="<?php echo $row['sku']; ?>"><?php echo $row['sku']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			</div>
		</td>
		<td>
			<table border="0" width="90%" cellpadding="5" cellspacing="0" align="center" id="variants" style="">
				<thead>
					<tr>	
						<th>Available SKU</th>
					</tr>	
				</thead>
				<tbody id="container">
					<?php $i=1;?>
						<tr>
							<td align='center'><input type="text" required class="autocomplete" name='xdata[<?php echo $i;?>][sku]' /><div style="float:left" id="loader"></div></td>
							<td align='center'><input type="button" href='javascript://' value="Remove" onclick='$(this).parent().parent().remove();'></td>
						</tr>
						<?php $i++; ?>
				</tbody>
				<tr>
					<td>
						<input align="center" type="button" value="Add Row" onclick="addRow();" name="Add Row">
						<input type="submit" value="Submit" class = "button" name="Add">
					</td>
				</tr>
			</table>
		</td>
		</tr>
		</table>
		</form>
	</div>
	<br>
	<script type="text/javascript">
		function addRow(){
		var current_row = $('#container tr').length+1;	
		var row = "<tr>"+
		"<td align='center'><input class='autocomplete' required type='text' name='xdata["+current_row+"][sku]'/><div style='float:left' id='loader'></div></td>"+
		"<td align='center'><input type='button' href='javascript://' value='Remove' onclick='$(this).parent().parent().remove();'></td>"+
		"</tr>";
		$("#container").append(row);		
		current_row++;	 
	}
	function populateRow(){
		var lbb_sku = $('#lbbsku').val();
		var current_row = $('#container tr').length+1;
		$.ajax({
				url: '',
				type: 'POST',
				dataType: 'json',
				data: {fetching_lbb: lbb_sku, getAjax: 'yes'},
			})
			.always(function(json) {
				var html = "<tr>"+
										"<td align='center'></td>"+
										"<td align='center'></td>"+
										"</tr>";
				$("#container").html(html);
				if (json['lbb_data']){
					var row = "<tr>"+
					"<td align='center'><input required type='text' name='xdata["+current_row+"][sku]' /><div style='float:left' id='loader'></td>"+
					"<td align='center'><input type='button' href='javascript://' value='Remove' onclick='$(this).parent().parent().remove();'></td>"+
					"</tr>";
					$("#container").append(row);
					current_row++;
				}
				if (lbb_sku) {
					for (var i = json['lbb_data'].length - 1; i >= 0; i--) {
					var row = "<tr>"+
										"<td align='center'><input type='text' name='xdata["+current_row+"][sku]' value="+json['lbb_data'][i]['product_sku']+" /><div style='float:left' id='loader'></td>"+
										"<td align='center'><input type='button' href='javascript://' value='Remove' onclick='$(this).parent().parent().remove();'></td>"+
										"</tr>";
					$("#container").prepend(row);		
					current_row++;					
					}
				}
			});	 
	}
	</script>
<!-- <script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
<script>
$(document).ready(function(e) {
var current_row = $('#container tr').length+1;
$('.autocomplete').autocomplete({
    serviceUrl: '../popupfiles/search_products.php',
	onSearchStart: function(){
		$('.loading').remove();
		$('#loader').after('<img src="../images/loading.gif" height="10" width="10" class="loading">');
	},
	onSearchComplete: function(){
		$('.loading').remove();
	},
    onSelect: function (suggestion) {
		html="";
		html+="<tr><td><input type='text' name='xdata["+current_row+"][sku]' value='"+suggestion.data+"'></td><td align='center'><input type='button' href='javascript://' value='Remove' onclick='$(this).parent().parent().remove();'></td></tr>";
       $('#container').html(html);
       $('#autocomplete').val('');
	      }
});

    
});
</script>	 --> 	
</body>