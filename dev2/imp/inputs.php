<?php
require_once("../auth.php");
require_once("../inc/functions.php");
$table = "inv_buy_back";
$page = "inputs.php.php";

if($_POST['add']){
	$db->db_exec("DELETE FROM inv_buy_back");
	$array = array();
	$array['upper_text'] = $db->func_escape_string($_POST['upper_text']);
	$array['lower_text'] = $db->func_escape_string($_POST['lower_text']);
	$array['cash_discount'] = (float)$_POST['cash_discount'];
	
	foreach($_POST['xdata'] as $key => $data)
	{
		$image = '';
		
		if(isset($_FILES['xdata']['tmp_name'][$key]))
		{
			$ext = pathinfo($_FILES['xdata']['name'][$key]['file'], PATHINFO_EXTENSION);
			$uniqid = uniqid();
			$image =   $uniqid . ".".$ext;
			$destination = "../files/" . $image;

			//print_r($_FILES['xdata']['tmp_name'][$key]['file']);exit;
			if (move_uploaded_file($_FILES['xdata']['tmp_name'][$key]['file'], $destination)) {
                //resizeImage($destination, $image, 500, 500);

			}
			else
			{
		//echo 'error';exit;
				$image = '';	
			}

		}

		if($data['hidden_image'])
		{
			$image = $data['hidden_image'];	
		}

		$array['sku'] = $db->func_escape_string($data['sku']);
		$array['description'] = $db->func_escape_string($data['description']);
		$array['oem'] = (float)$data['oem'];
		$array['non_oem'] = (float)$data['nonoem'];
		$array['sort'] = (int)$data['sort'];
		$array['image'] = $image;

		$db->func_array2insert("inv_buy_back",$array);

	}
	$_SESSION['message']="Data Added";
	header("Location: inputs.php");
	exit;
}

$row = $db->func_query_first("SELECT * FROM inv_buy_back limit 1");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>LCD Buy Back Program</title>
</head>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script>

	function checkWhiteSpace (t) {
		if ($(t).val() == ' ') {
			$(t).val('');
		}
	}

	function allowNum (t) {
		var input = $(t).val();
		var valid = input.substring(0, input.length - 1);
		if (isNaN(input)) {
			$(t).val(valid);
		}
	}

	function checkFile(e) {
		var file = $(e).val().split(".");
		var ext = file.pop();
		var allowed = ['png', 'jpeg', 'jpg', 'gif'];
		if ($.inArray(ext, allowed) >= 0) {

		} else {
			alert('This File is not Allowed');
			$(e).val('');
		}
	}

	function addRow(){
		var current_row = $('#variants tr').length+1;	
		var row = "<tr>"+

		" <td align='center'><div><img id='img_"+current_row+"' src='https://phonepartsusa.com/dev2/image/cache/no_image-100x100.jpg' style='cursor:pointer;'></div><input style='display:none' type='file' name='xdata["+current_row+"][file]' id='image_path_"+current_row+"' onchange='checkFile(this);' /><br /><a href='javascript:void(0);' onClick='$(\"#image_path_"+current_row+"\").click();'>Browse</a> | <a href='javascript:void(0);' onClick='$(\"#image_path_"+current_row+"\").val(\"\");$(\"#img_"+current_row+"\").attr(\"src\",\"https://phonepartsusa.com/dev2/image/cache/no_image-100x100.jpg\");'>Remove</a></td>"+
		" <td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata["+current_row+"][sku]' required=\"\" /></td>"+
		" <td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata["+current_row+"][description]' required=\"\" /></td>"+
		" <td align='center'><input type='text' onkeyup='allowNum(this);' name='xdata["+current_row+"][oem]' required=\"\" /></td>"+
		" <td align='center'><input type='text' onkeyup='allowNum(this);' name='xdata["+current_row+"][nonoem]' required=\"\" /></td>"+
		" <td align='center'><input style='width:50px' onkeyup='allowNum(this);' type='text' name='xdata["+current_row+"][sort]' value='0' required=\"\" /></td>"+

		"<td align='center'><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>"+
		"</tr>";
		$("#variants").append(row);		
		current_row++;	 
	}
</script>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once '../inc/header.php';?>
		</div>

		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<form action="" method="post" enctype="multipart/form-data">
			<h2>LCD Buy Back Program</h2>
			<table align="center" border="1" width="85%" cellpadding="5" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td><strong>Upper Text</strong></td>
					<td><textarea name="upper_text" required id="upper_text" onkeyup='checkWhiteSpace(this);' style="width:500px;height:150px"><?php echo $row['upper_text'];?></textarea></td>
				</tr>

				<tr>
					<td><strong>Lower Text</strong></td>
					<td><textarea name="lower_text" required id="lower_text" onkeyup='checkWhiteSpace(this);' style="width:500px;height:150px"><?php echo $row['lower_text'];?></textarea></td>
				</tr>

				<tr>
					<td><strong>Cash Discount %</strong></td>
					<td><input type="text" onkeyup='allowNum(this);' name="cash_discount" id="cash_discount" value="<?php echo $row['cash_discount'];?>" /></td>
				</tr>



				<tr>
					<td colspan="2">
						<table border="1" width="90%" cellpadding="5" cellspacing="0" align="center" id="variants" style="">
							<tr>	

								<th>Image</th>
								<th>SKU</th>
								<th>Description</th>
								<th>OEM</th>
								<th>Non-OEM</th>
								<th>Sort</th>

								<th>
									<a href="javascript://" onclick="addRow();">Add Row</a>
								</th>
							</tr>	
							<?php
							$rows = $db->func_query("SELECT * FROM inv_buy_back");
							$i=1;
							foreach($rows as $row)
							{
								if($row['image'])
								{
									$image = '../files/'.$row['image'];	 
								}
								else
								{
									$image = 'https://phonepartsusa.com/dev2/image/cache/no_image-100x100.jpg'; 
								}
								?>
								<tr>
									<td align='center'><div><img height="100" width="100" id='img_<?php echo $i;?>' src='<?php echo $image;?>' style='cursor:pointer;'></div><input style='display:none' type='file' name='xdata[<?php echo $i;?>][file]' id='image_path_<?php echo $i;?>' onchange='checkFile(this);' /><br /><a href='javascript:void(0);' onClick="$('#image_path_<?php echo $i;?>').click();">Browse</a> | <a href='javascript:void(0);' onClick='$("#image_path_<?php echo $i;?>").val("");$("#hidden_image_<?php echo $i;?>").val("");$("#img_<?php echo $i;?>").attr("src","https://phonepartsusa.com/dev2/image/cache/no_image-100x100.jpg");'>Remove</a></td>

									<td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata[<?php echo $i;?>][sku]' value="<?php echo $row['sku'];?>"  /></td>
									<td align='center'><input type='text' onkeyup='checkWhiteSpace(this);' name='xdata[<?php echo $i;?>][description]' value="<?php echo $row['description'];?>"  /></td>
									<td align='center'><input type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][oem]' value="<?php echo $row['oem'];?>" /></td>
									<td align='center'><input type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][nonoem]' value="<?php echo $row['non_oem'];?>" /></td>
									<td align='center'><input style='width:50px' onkeyup='allowNum(this);' type='text' name='xdata[<?php echo $i;?>][sort]' value="<?php echo $row['sort'];?>" /></td>

									<td align='center'><input type='hidden' id="hidden_image_<?php echo $i;?>" name='xdata[<?php echo $i;?>][hidden_image]' value="<?php echo $row['image'];?>" /><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>
								</tr>
								<?php 
								$i++;
							}
							?>



						</table>
					</td>

				</tr>

			</table>

			<br /><br />
			<p><a href="javascript://" onclick="addRow();">Add Row</a></p>
			<br><br>

			<div style="text-align:center">  <input type="submit" name="add" value="Submit" /></div>

		</form>
	</div>
</body>
</html>			 		 