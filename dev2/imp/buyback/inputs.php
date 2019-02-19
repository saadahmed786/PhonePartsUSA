<?php
require_once("../auth.php");
require_once("../inc/functions.php");
$table = "inv_buy_back";
$page = "inputs.php";
page_permission("buyback_inputs");
if($_POST['add']){
	$db->db_exec("DELETE FROM inv_buy_back");
	$array = array();
	$array['upper_text'] = $_POST['upper_text'];
	$array['lower_text'] = $_POST['lower_text'];
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
		$array['oem_a'] = (float)$data['oem_a'];
		$array['oem_b'] = (float)$data['oem_b'];
		$array['oem_c'] = (float)$data['oem_c'];
		$array['oem_d'] = (float)$data['oem_d'];
		$array['non_oem_a'] = (float)$data['non_oem_a'];
		$array['non_oem_b'] = (float)$data['non_oem_b'];
		$array['non_oem_c'] = (float)$data['non_oem_c'];
		$array['non_oem_d'] = (float)$data['non_oem_d'];

		$array['oem_a_desc'] = $db->func_escape_string($data['oem_a_desc']);
		$array['oem_b_desc'] = $db->func_escape_string($data['oem_b_desc']);
		$array['oem_c_desc'] = $db->func_escape_string($data['oem_c_desc']);
		$array['oem_d_desc'] = $db->func_escape_string($data['oem_d_desc']);
		$array['non_oem_a_desc'] = $db->func_escape_string($data['non_oem_a_desc']);
		$array['non_oem_b_desc'] = $db->func_escape_string($data['non_oem_b_desc']);
		$array['non_oem_c_desc'] = $db->func_escape_string($data['non_oem_c_desc']);
		$array['non_oem_d_desc'] = $db->func_escape_string($data['non_oem_d_desc']);
		$array['salvage_desc'] = $db->func_escape_string($data['salvage_desc']);
		$array['unacceptable_desc'] = $db->func_escape_string($data['unacceptable_desc']);
		$array['damaged_desc'] = $db->func_escape_string($data['damaged_desc']);

		$array['salvage'] = (float)$data['salvage'];

		$array['weight'] = (float)$data['weight'];
		$array['sort'] = (int)$data['sort'];
		$array['image'] = $image;

		$db->func_array2insert("inv_buy_back",$array);

	}

		$desc_array = array();
		$desc_array['oem_a_desc'] = $db->func_escape_string($_POST['a_desc']);
		$desc_array['oem_b_desc'] = $db->func_escape_string($_POST['b_desc']);
		$desc_array['oem_c_desc'] = $db->func_escape_string($_POST['c_desc']);
		$desc_array['oem_d_desc'] = $db->func_escape_string($_POST['d_desc']);

		$desc_array['non_oem_a_desc'] = $db->func_escape_string($_POST['n_a_desc']);
		$desc_array['non_oem_b_desc'] = $db->func_escape_string($_POST['n_b_desc']);
		$desc_array['non_oem_c_desc'] = $db->func_escape_string($_POST['n_c_desc']);
		$desc_array['non_oem_d_desc'] = $db->func_escape_string($_POST['n_d_desc']);

		$desc_array['sal_desc'] = $db->func_escape_string($_POST['sal_desc']);

		$check = $db->func_query_first("SELECT * FROM inv_buyback_desc");
		if($check){
		$db->func_array2update("inv_buyback_desc",$desc_array,"id = '".$check['id']."'");	
		} else {
			$db->func_array2insert("inv_buyback_desc",$desc_array);	
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
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
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
		" <td align='center'>"+
		"Grade A: <input style='width: 100px;' type='text' value='' onkeyup='allowNum(this);' name='xdata["+current_row+"][oem_a]' /><br>"+
		"Grade A-: <input style='width: 100px;' type='text' value='' onkeyup='allowNum(this);' name='xdata["+current_row+"][oem_b]' /><br>"+
		"Grade B: <input style='width: 100px;' type='text' value='' onkeyup='allowNum(this);' name='xdata["+current_row+"][oem_c]' /><br>"+
		"Grade C: <input style='width: 100px;' type='text' value='' onkeyup='allowNum(this);' name='xdata["+current_row+"][oem_d]' /><br>"+
		"<hr />"+
		"A: SKU Map <input style='width: 100px;' type='text' value='' name='xdata["+current_row+"][oem_a_desc]' placeholder='Mapped Sku' /><br>"+
		"A-: SKU Map <input style='width: 100px;' type='text' value='' name='xdata["+current_row+"][oem_b_desc]' placeholder='Mapped Sku' /><br>"+
		"B: SKU Map <input style='width: 100px;' type='text' value='' name='xdata["+current_row+"][oem_c_desc]' placeholder='Mapped Sku' /><br>"+
		"C: SKU Map <input style='width: 100px;' type='text' value='' name='xdata["+current_row+"][oem_d_desc]' placeholder='Mapped Sku' /><br>"+
		"</td>"+
		" <td align='center'>"+
		"Grade A: <input style='width: 100px;' type='text' onkeyup='allowNum(this);' value='' name='xdata["+current_row+"][non_oem_a]' /><br>"+
		"Grade A-: <input style='width: 100px;' type='text' onkeyup='allowNum(this);' value='' name='xdata["+current_row+"][non_oem_b]' /><br>"+
		"Grade B: <input style='width: 100px;' type='text' onkeyup='allowNum(this);' value='' name='xdata["+current_row+"][non_oem_c]' /><br>"+
		"Grade C: <input style='width: 100px;' type='text' onkeyup='allowNum(this);' value='' name='xdata["+current_row+"][non_oem_d]' /><br>"+
		"<hr />"+
		"A: SKU Map <input style='width: 100px;' type='text' name='xdata["+current_row+"][non_oem_a_desc]' value='' placeholder='Mapped Sku' /><br>"+
		"A-: SKU Map <input style='width: 100px;' type='text' name='xdata["+current_row+"][non_oem_b_desc]' value='' placeholder='Mapped Sku' /><br>"+
		"B: SKU Map <input style='width: 100px;' type='text' name='xdata["+current_row+"][non_oem_c_desc]' value='' placeholder='Mapped Sku' /><br>"+
		"C: SKU Map <input style='width: 100px;' type='text' name='xdata["+current_row+"][non_oem_d_desc]' value='' placeholder='Mapped Sku' /><br>"+
		"</td>"+
		" <td align='center'>"+
		"Salvage: <input style='width: 100px;' type='text' name='xdata["+current_row+"][salvage_desc]' placeholder='Salvage Sku' value=''/><br>"+
		"Unacceptable: <input style='width: 100px;' type='text' name='xdata["+current_row+"][unacceptable_desc]' placeholder='Unacceptable Sku' value=''/><br>"+
		"Damaged: <input style='width: 100px;' type='text' name='xdata["+current_row+"][damaged_desc]' placeholder='Damaged Sku' value=''/><br>"+
		"<hr />"+
		"Salvage Price <input style='width: 100px;' type='text' onkeyup='allowNum(this);' name='xdata["+current_row+"][salvage]' value='' /><br>"+
		"</td>"+
		" <td align='center'><input type='text' onkeyup='allowNum(this);' name='xdata["+current_row+"][weight]'  /></td>"+
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
					<td><textarea name="upper_text" required id="upper_text" style="width:500px;height:150px"><?php echo $row['upper_text'];?></textarea></td>
				</tr>

				<tr>
					<td><strong>Lower Text</strong></td>
					<td><textarea name="lower_text" required id="lower_text" style="width:500px;height:150px"><?php echo $row['lower_text'];?></textarea></td>
				</tr>

				<tr>
					<td><strong>Cash Discount %</strong></td>
					<td><input type="text" onkeyup='allowNum(this);' name="cash_discount" id="cash_discount" value="<?php echo $row['cash_discount'];?>" /></td>
				</tr>



				<tr>
					<td colspan="2">
					<h2 align="center">Item Descriptions</h2>
					<table border="1" width="90%" cellpadding="5" cellspacing="0" align="center" style="">
							<tr>	
								<th>OEM</th>
								<th>Non-OEM</th>
								<th>Salvage</th>
							</tr>
							<tr>
							<?php
							$des = $db->func_query_first("SELECT * FROM inv_buyback_desc WHERE id= '0'");?>
								<td align="center">
									OEM A Description:<input style="width: 200px;" type='text' name='a_desc' placeholder="Description" value="<?php echo $des['oem_a_desc'];?>" /><br>
									OEM A- Description:<input style="width: 200px;" type='text' name='b_desc' placeholder="Description" value="<?php echo $des['oem_b_desc'];?>" /><br>
									OEM B Description:<input style="width: 200px;" type='text' name='c_desc' placeholder="Description" value="<?php echo $des['oem_c_desc'];?>" /><br>
									OEM C Description:<input style="width: 200px;" type='text' name='d_desc' placeholder="Description" value="<?php echo $des['oem_d_desc'];?>" /><br>
								</td>
								<td align="center">
									NON OEM A Description:<input style="width: 200px;" type='text' name='n_a_desc' placeholder="Description" value="<?php echo $des['non_oem_a_desc'];?>" /><br>
									NON OEM A- Description:<input style="width: 200px;" type='text' name='n_b_desc' placeholder="Description" value="<?php echo $des['non_oem_b_desc'];?>" /><br>
									NON OEM B Description:<input style="width: 200px;" type='text' name='n_c_desc' placeholder="Description" value="<?php echo $des['non_oem_c_desc'];?>" /><br>
									NON OEM C Description:<input style="width: 200px;" type='text' name='n_d_desc' placeholder="Description" value="<?php echo $des['non_oem_d_desc'];?>" /><br>
								</td>
								<td align="center">
									Salvage Description:<input style="width: 200px;" type='text' name='sal_desc' placeholder="Description" value="<?php echo $des['sal_desc'];?>" /><br>
								</td>
							</tr>
					</table>
					<br></br>	
						<table border="1" width="90%" cellpadding="5" cellspacing="0" align="center" id="variants" style="">
							<tr>	

								<th>Image</th>
								<th>SKU</th>
								<th>Description</th>
								<th>OEM</th>
								<th>Non-OEM</th>
								<th>Reject</th>
								<th>Weight in Oz</th>
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
									<td align='center'>
										Grade A: <input style="width: 100px;" type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][oem_a]' value="<?php echo $row['oem_a'];?>" /><br>
										Grade A-: <input style="width: 100px;" type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][oem_b]' value="<?php echo $row['oem_b'];?>" /><br>
										Grade B: <input style="width: 100px;" type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][oem_c]' value="<?php echo $row['oem_c'];?>" /><br>
										Grade C: <input style="width: 100px;" type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][oem_d]' value="<?php echo $row['oem_d'];?>" /><br>
										<hr />
										A: SKU Map <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][oem_a_desc]' placeholder="Mapped Sku" value="<?php echo $row['oem_a_desc'];?>" /><br>
										A-: SKU Map <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][oem_b_desc]' placeholder="Mapped Sku" value="<?php echo $row['oem_b_desc'];?>" /><br>
										B: SKU Map <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][oem_c_desc]' placeholder="Mapped Sku" value="<?php echo $row['oem_c_desc'];?>" /><br>
										C: SKU Map <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][oem_d_desc]' placeholder="Mapped Sku" value="<?php echo $row['oem_d_desc'];?>" />
									</td>
									<td align='center'>
										Grade A: <input style="width: 100px;" type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][non_oem_a]' value="<?php echo $row['non_oem_a'];?>" /><br>
										Grade A-: <input style="width: 100px;" type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][non_oem_b]' value="<?php echo $row['non_oem_b'];?>" /><br>
										Grade B: <input style="width: 100px;" type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][non_oem_c]' value="<?php echo $row['non_oem_c'];?>" /><br>
										Grade C: <input style="width: 100px;" type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][non_oem_d]' value="<?php echo $row['non_oem_d'];?>" /><br>
										<hr />
										A: SKU Map <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][non_oem_a_desc]' placeholder="Mapped Sku" value="<?php echo $row['non_oem_a_desc'];?>" /><br>
										A-: SKU Map <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][non_oem_b_desc]' placeholder="Mapped Sku" value="<?php echo $row['non_oem_b_desc'];?>" /><br>
										B: SKU Map <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][non_oem_c_desc]' placeholder="Mapped Sku" value="<?php echo $row['non_oem_c_desc'];?>" /><br>
										C: SKU Map <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][non_oem_d_desc]' placeholder="Mapped Sku" value="<?php echo $row['non_oem_d_desc'];?>" /><br>
									</td>
									<td align='center'>
										Salvage: <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][salvage_desc]' placeholder="Salvage Sku" value="<?php echo $row['salvage_desc'];?>" /><br>
										Unacceptable: <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][unacceptable_desc]' placeholder="Unacceptable Sku" value="<?php echo $row['unacceptable_desc'];?>" /><br>
										Damaged: <input style="width: 100px;" type='text' name='xdata[<?php echo $i;?>][damaged_desc]' placeholder="Damaged Sku" value="<?php echo $row['damaged_desc'];?>" /><br>
										<hr />
										Salvage Price <input style="width: 100px;" type='text' onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][salvage]' value="<?php echo $row['salvage'];?>" /><br>
									</td>
									<td align='center'><input type='text'  style="width: 50px;" onkeyup='allowNum(this);' name='xdata[<?php echo $i;?>][weight]' value="<?php echo $row['weight'];?>" /></td>
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
<script>
	CKEDITOR.replace( 'upper_text' );
	CKEDITOR.replace( 'lower_text' );
</script>			 		 