<?php
require_once("auth.php");
require_once("inc/functions.php");
// if (!$_SESSION['vouchers_update']) {
// 	echo "You dont have the permssion to access this page.";
// 	exit;
// }
$page = 'chart_of_accounts.php';
$table = "`inv_charts`";

function get_max_code($code,$part_no=1)
{
	global $db;
	switch($part_no)
	{
		case 1:
			$substr_start = '4';
			$substr_end = '2';

			$main_code_start ='1';
			$main_code_end ='2';

			$padding = 2;



		break;

		default:
			$substr_start = '7';
			$substr_end = '4';

			$main_code_start ='1';
			$main_code_end ='5';

			$padding = 4;
		break;
	}
	// echo "select max(cast(SUBSTRING(main_code,$substr_start,$substr_end) as unsigned)) from inv_charts where substring(main_code,$main_code_start,$main_code_end)=substr('$code',$main_code_start,$main_code_end)";exit;
	$max_rec = $db->func_query_first_cell("select max(cast(SUBSTRING(main_code,$substr_start,$substr_end) as unsigned)) from inv_charts where substring(main_code,$main_code_start,$main_code_end)=substr('$code',$main_code_start,$main_code_end)");
	$max_rec = $max_rec+1;
	$id = str_pad($max_rec, $padding,"0",STR_PAD_LEFT);


	return $id;


}
if(isset($_POST['action']) && $_POST['action']=='getCode')
{
	$main_code = $db->func_escape_string($_POST['main_code']);
	$parts = explode("-", $main_code);
	$json =array();
	if($parts[1]=='00')
	{
		$max_code = get_max_code($main_code,1);
		$json['success']= $parts[0].'-'.$max_code.'-0000';
	}
	else
	{
	$max_code = get_max_code($main_code,2);	

	$json['success']= $parts[0].'-'.$parts[1].'-'.$max_code;	
	}
	echo json_encode($json);
	exit;
}

if ($_POST['submit']) {
	$insert = array();
	$insert['main_code'] = $db->func_escape_string($_POST['main_code']);
	$insert['name'] = $db->func_escape_string($_POST['name']);
	$insert['description'] = $db->func_escape_string($_POST['description']);
	$insert['type'] = $db->func_escape_string($_POST['type']);
	$insert['user_id'] = $_SESSION['user_id'];

	$insert['date_added'] = date('Y-m-d H:i:s');



	
	if ($_POST['update'] == '1') {
		$db->func_array2update($table,$insert,"id = '".$_POST['id']."'");
		$_SESSION['message'] = "Account Updated successfully.";
		header("Location:$page");
		exit;
	} else {
		$id = $db->func_array2insert($table, $insert);
		$_SESSION['message'] = "Account Added successfully.";
   		header("Location:$page");
   		exit;
	}
}
$detail = array();
if(isset($_GET['action']) && $_GET['action']=='edit')
{
	$detail = $db->func_query_first("SELECT * FROM $table WHERE id='".(int)$_GET['id']."'");
}
if((int)$_GET['id'] and $_GET['action'] == 'delete'){
	
	$db->db_exec("delete from $table where id = '".$_GET['id']."'");
	$_SESSION['message'] = "Account Deleted successfully.";
   		header("Location:$page");
   		exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Chart of Accounts</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />
</head>
<body>
	<div align="center">
		<div align="center" > 
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if ($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red">
				<?php
				echo $_SESSION['message'];
				unset($_SESSION['message']);
				?>
				<br />
			</font>
		</div>
		<?php } ?>
		<form action="" method="post" enctype="multipart/form-data">
			<h2>Chart of Accounts</h2>
			<table align="center" border="1" width="40%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 
				

				<tr>
					<th>Group</th>
					<td>
						<?php
						$accounts = $db->func_query("SELECT * FROM $table where type='G' order by main_code asc ");
						?>
						<select name="chart_group" id="chart_group" onchange="chartAccounts(this.value)" <?php echo ($detail?'disabled':'');?>>
						<option value="">Please Select</option>
						<?php
							foreach($accounts as $account)
							{
								?>
								<option value="<?php echo $account['main_code'];?>"><?php echo $account['name'];?></option>
								<?php
							}
						?>

						</select>
						<input type="hidden" id="update" name="update" value="<?php echo ($detail['location_id']?'1':'0');?>">
						<input type="hidden" id="location_id" name="location_id" value="<?php echo (int)$detail['location_id'];?>">
					</td>
				</tr>

				<tr>
					<th>Code</th>
					<td>
						<input required id="main_code" style="width: 300px ;" readonly type="text" name="main_code"  value="<?php echo $detail['main_code'];?>">
						
					</td>
				</tr>

				<tr>
					<th>Name</th>
					<td>
						<input required id="name" style="width: 300px ;" type="text" name="name" value="<?php echo $detail['name'];?>">
						
					</td>
				</tr>

				<tr>
					<th>Group / Detail</th>
					<td>
						<input type="radio" name="type" value="G" <?php echo (!$detail?'checked':'');?> <?php echo ($detail['type']=='G'?'checked':'');?> > Group
						<input type="radio" name="type" value="D" <?php echo ($detail['type']=='D'?'checked':'');?>> Detail
						
					</td>
				</tr>
				<tr>
					<th>Decsription</th>
					<td>
						<textarea  cols="40" rows="3" name="description" id="description" placeholder="Please enter description" ><?php echo $detail['description'];?></textarea>
					</td>
				</tr>
				<tr >
					<td align="center" colspan="2"><input class="button" type="submit" name="submit" value="Submit" /> <input type="button" class="button button-danger" onclick="window.location='<?php echo $page;?>'" value="New Entry"></td>
				</tr>
			</table>
		</form>
		<br><br>
		
		<table align="center" width="100%" >
		
		
			<tr>
			
			<td align="center"> <h2>Available Accounts</h2></td>
		</tr>
			<tr>
				
				
				<td>
				
				<table align="center" class="xtable" border="1" width="80%" cellpadding="5" cellspacing="0" >
					<thead>
				<tr>
				
					<th>Code</th>
					<th>Name</th>
					<th>Type</th>
					
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $i =1;
				$rows = $db->func_query("SELECT * FROM $table order by main_code ");
				 foreach ($rows as $row) { ?>
				<tr>
				<td align="center"><?php echo $row['main_code']; ?></td>
					<td align="center">
					
					 <?php echo $row['name'] ?> </td>
					 <td align="center">
					 	<?php echo $row['type']; ?>
					 </td>
					 
					<td align="center">
<a href="<?php echo $host_path;?><?php echo $page;?>?action=edit&id=<?php echo $row['id'];?>" >Edit</a>
|
					<a href="<?php echo $page;?>?action=delete&id=<?php echo $row['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
					
					
					</td>
				</tr>
			<?php $i++;
			 } ?>
			</tbody>
				</table>
				</td>
			</tr>
		</table>
		<br>
	</div>
	<script type="text/javascript">
		function chartAccounts(objVal){
			console.log(objVal);
			if(objVal=='')
			{
				return false;
			}


				$.ajax({
		url: '<?php echo $page;?>',
		type: 'post',
		data:{main_code:objVal,action:'getCode'},
		dataType: 'json',       
		beforeSend: function() {
			
		},
		complete: function() {
		},              
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}
			if (json['success']) {
				$('#main_code').val(json['success']);

			}
		}
	});
		}
	</script>
</body>
