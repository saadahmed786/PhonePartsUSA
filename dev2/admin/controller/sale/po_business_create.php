<?php

require_once("auth.php");
require_once("inc/functions.php");

if($_GET['action'] == 'delete' && (int)$_GET['fileid']){
	$fileid = (int)$_GET['fileid'];
	$user_id  = $db->func_escape_string($_GET['user_id']);

	$db->db_exec("Delete from inv_po_customer_docs where id = '$fileid' and po_customer_id = '$user_id'");

	header("Location:po_business_create.php?id=$user_id&mode=edit");
	exit;
}

$mode = $_GET['mode'];
if($mode == 'edit'){
	$user_id = (int)$_GET['id'];
	$user = $db->func_query_first("select * from inv_po_customers where id = '$user_id'");
	
	list($user['first_name'] , $user['last_name']) = explode(" ", $user['contact_name']);
	$po_customer_docs = $db->func_query("select * from inv_po_customer_docs where po_customer_id = '$user_id'");
}

if($_POST['add']){
	unset($_POST['add']);
	unset($_POST['attachments']);
	
	$company_name = $db->func_escape_string($_POST['company_name']);
	$isExist = $db->func_query_first("select id from inv_po_customers where company_name = '$company_name'");
	if(!$isExist || $user_id){
		$user_arr = array();
		$user_arr = $_POST;
		
		$user_arr['contact_name'] = $db->func_escape_string($_POST['first_name']. " ". $_POST['last_name']);
		$user_arr['firstname'] = $db->func_escape_string($_POST['first_name']);
		$user_arr['lastname'] = $db->func_escape_string($_POST['last_name']);
		$user_arr['date_created'] = date('Y-m-d H:i:s');
		
		//unset($user_arr['first_name']);
		//unset($user_arr['last_name']);
		
		if($user_id){
			$db->func_array2update("inv_po_customers",$user_arr,"id = '$user_id'");
		}
		else{
			$user_id = $db->func_array2insert("inv_po_customers",$user_arr);
		}
		
		//upload return item item images
		if($_FILES['attachments']['tmp_name']){
			$imageCount = 0;
			$count    = count($_FILES['attachments']['tmp_name']);
		
			for($i=0; $i<$count; $i++){
				$uniqid = uniqid();
				$name   = explode(".",$_FILES['attachments']['name'][$i]);
				$ext    = end($name);
		
				$destination = $path."files/".$uniqid.".$ext";
				$file = $_FILES['attachments']['tmp_name'][$i];
		
				if(move_uploaded_file($file, $destination)){
					$orderDoc = array();
					$orderDoc['attachment_path'] = "files/".basename($destination);
					$orderDoc['type'] = $_FILES['attachments']['type'][$i];
					$orderDoc['size'] = $_FILES['attachments']['size'][$i];
					$orderDoc['date_added'] = date('Y-m-d H:i:s');
					$orderDoc['po_customer_id']   = $user_id;
		
					$db->func_array2insert("inv_po_customer_docs",$orderDoc);
					$imageCount++;
				}
			}
		}
		
		header("Location:po_businesses.php");
		exit;
	}
	else{
		$_SESSION['message'] = "This company name is already exist.";
		$user = $_POST;
	}
}

$business_types = array(array('id'=>'Education','value'=>'Education'),
		array('id'=>'Sole Propretor','value'=>'Sole Propretor'),
		array('id'=>'Partnership','value'=>'Partnership'),
		array('id'=>'Corporation','value'=>'Corporation'),
		array('id'=>'Corporation','value'=>'Corporation')
);

$states = $db->func_query("select name from oc_zone where country_id = 223");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add PO Businesses</title>
	</head>
	<body>
		<div align="center">
			<div align="center"> 
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <form action="" method="post" enctype="multipart/form-data">
			 	<h2>Add PO Businesses</h2>
			    <table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         <tr>
			             <td>Company Name:</td>
			         	 <td><input type="text" name="company_name" value="<?php echo @$user['company_name'];?>" required /></td>
			         	 
			             <td>Company Name 2:</td>
			         	 <td><input type="text" name="company_name_2" value="<?php echo @$user['company_name_2'];?>" /></td>
			         </tr>
			         
			         <tr>
			             <td>Business Type:</td>
			         	 <td>
			         	 	 <?php echo createField("business_type", "business_type", "select" , $user['business_type'], $business_types,"required");?>
			         	 </td>
			         	 
			         	 <td>Email:</td>
			         	 <td><input type="text" name="email" value="<?php echo @$user['email'];?>" autocomplete="off" required /></td>
			         </tr>
			         
			         <tr>
			             <td>First Name:</td>
			         	 <td><input type="text" name="first_name" value="<?php echo @$user['first_name'];?>" autocomplete="off" required /></td>
			         	 
			             <td>Last Name:</td>
			         	 <td><input type="text" name="last_name" value="<?php echo @$user['last_name'];?>" autocomplete="off" required /></td>
			         </tr>
			         
			         <tr>
			             <td>Address 1:</td>
			         	 <td><input type="text" name="address1" value="<?php echo @$user['address1'];?>" required /></td>
			         	 
			             <td>Address 2:</td>
			         	 <td><input type="text" name="address2" value="<?php echo @$user['address2'];?>" /></td>
			         </tr>
			         
			         <tr>
			             <td>City:</td>
			         	 <td><input type="text" name="city" value="<?php echo @$user['city'];?>" required /></td>
			         	 
			             <td>State:</td>
			         	 <td>
			         	 	 <select name="state" required>
			         	 	 	 <option value="">Select One</option>
			         	 	 	 
				         	 	 <?php foreach($states as $state):?>
				         	 	 		<option value="<?php echo $state['name']?>" <?php if($user['state'] == $state['name']):?> selected="selected"  <?php endif;?>><?php echo $state['name']?></option>
				         	 	 <?php endforeach;?>
			         	 	 </select>
			         	 </td>
			         </tr>
			         
			         <tr>
			             <td>Zip Code:</td>
			         	 <td><input type="text" name="zip" maxlength="5" value="<?php echo @$user['zip'];?>" required /></td>
			         	 
			         	  <td>Telephone:</td>
			         	 <td><input type="text" name="telephone" value="<?php echo @$user['telephone'];?>" required /></td>
			         </tr>
			         
			         <tr>
			             <td>Tax ID:</td>
			         	 <td><input type="text" name="tax_id" value="<?php echo @$user['tax_id'];?>" /></td>
			         	 
			         	  <td>Fed:</td>
			         	 <td><input type="text" name="fed" value="<?php echo @$user['fed'];?>" /></td>
			         </tr>
			         
			         <tr>
			             <td>Attachments:</td>
			         	 <td>
			         	 	<input type="file" name="attachments[]" multiple />
			         	 </td>
			         </tr>
			         
			         <tr>
			             <td colspan="4" align="center">
			             	 <input type="submit" name="add" value="Submit" class="button" />
			             </td>
			         </tr>
			    </table>
			    
			    <?php if($po_customer_docs):?>
			   		 <h2>Attachments</h2>
				  	 <table border="1" cellpadding="10" width="40%">
					  	  <tr>
					  	  	  <th>Date</th>
					  	  	  <th>File</th>
					  	  	  <th>Action</th>
					  	  </tr>
						  <?php foreach($po_customer_docs as $attachment):?>
						  		<tr>
						  	  	  <td><?php echo $attachment['date_added'];?></td>
						  	  	  <td><?php echo $attachment['type'];?></td>
						  	  	  <td>
						  	  	  	  <a href="<?php echo $host_path ."". $attachment['attachment_path'];?>">download</a>
						  	  	  	  |
						  	  	  	  
						  	  	  	  <a href="po_business_create.php?action=delete&fileid=<?php echo $attachment['id']?>&user_id=<?php echo $user_id;?>" onclick="if(!confirm('Are you sure, You want to delete this file?')){ return false; }">delete</a>
						  	  	  </td>
						  	    </tr>
						  <?php endforeach;?>
					</table>	
				<?php endif;?>	  
			 </form>
			 
			 <br /><br />
		 </div>
	</body>
</html>			 		 