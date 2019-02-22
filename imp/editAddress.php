<?php
require_once("auth.php");
require_once("inc/functions.php");
$perission = 'inv_customer_contact';
$pageName = 'Address';
$pageLink = 'editAddress.php';
$table = '`inv_customer_contacts`';
$table2 = '`inv_contacts_ph`';
$customer_id = $_GET['customer_id'];
$contact_id = (int) $_GET['contact_id'];
$positions = array ('', 'Owner', 'Director', 'Manager', 'Purchasing', 'Sales');
$phTypes = array ('', 'Office Mobile', 'Personal Mobile', 'Landline', 'Office');
page_permission($perission);
if ($contact_id) {
	$data = $db->func_query_first("SELECT * FROM $table WHERE id = '$contact_id'");
	$customer_id = $data['customer_id'];
	$customer_contact_id = $data['id'];
	$dataContacts = $db->func_query("SELECT * FROM $table2 WHERE customer_contact_id = '$customer_contact_id'");
}

if ($_POST['action'] == 'deleteContact') {
	$db->db_exec("DELETE FROM $table2 WHERE id = '".$_POST['id']."'");
	exit;
}

if ($_POST['add']) {
	$contactArray = $_POST['contact'];
	$updateContact = $_POST['contactExt'];
	unset($_POST['contact'], $_POST['contactExt'], $_POST['add']);
	$array = $_POST;
	$array['customer_id'] = $customer_id;
	if ($contact_id) {
		$db->func_array2update($table ,$array, "id = '$contact_id'");
	} else {
		$contact_id = $db->func_array2insert($table, $array);
	}
	foreach ($updateContact as $key => $contact) {
		$contact['customer_contact_id'] = $contact_id;
		$db->func_array2update($table2, $contact, "id = '$key'");
	}
	foreach ($contactArray as $contact) {
		$contact['customer_contact_id'] = $contact_id;
		$db->func_array2insert($table2, $contact);
	}
	$_SESSION['message'] = 'Contact Updated';
	header("Location:$pageLink?customer_id=$customer_id" . "&" . "contact_id=$contact_id");
	exit;
}

$cus_id=$_GET['customer_id'];
$cus1_id=$_GET['c_id'];
//print_r($cus1_id);
//exit;
if($cus_id){
$customer_id=$db->func_query_first("select * from oc_address where address_id= $cus_id");
}
if($cus1_id){
$customer1_id=$db->func_query_first("select * from oc_address where address_id= $cus1_id");
}
//print_r($customer1_id);
//exit;
$id=$customer_id['customer_id'];
$id1=$customer1_id['customer_id'];
//print_r($id);
//exit;


if($_POST['saveChanges']){

	unset($_POST['add']);

	$firstname=$_POST['firstName'];
	$lastname=$_POST['lastName'];
	$company=$_POST['company'];
	$address1=$_POST['addres_1'];	
	$address2=$_POST['addres_2'];
	$city=$_POST['city'];
	$postalcode=$_POST['postalcode'];
	$country=$_POST['country'];

	$db->db_exec("UPDATE oc_address SET firstname='". $firstname ."',lastname='". $lastname ."',company='". $company ."',address_1='". $address1 ."',address_2='". $address2 ."',city='". $city ."',postcode='". $postalcode ."' WHERE address_id='".$cus_id."'");


	}

	if($_POST['saveChanges1']){

	unset($_POST['add']);

	$firstname=$_POST['firstName1'];
	$lastname=$_POST['lastName1'];
	$company=$_POST['company1'];
	$address1=$_POST['addres_11'];	
	$address2=$_POST['addres_21'];
	$city=$_POST['city1'];
	$postalcode=$_POST['postalcode1'];
	$country=$_POST['country1'];

	$db->db_exec("UPDATE oc_address SET firstname='". $firstname ."',lastname='". $lastname ."',company='". $company ."',address_1='". $address ."',address_2='". $address2 ."',city='". $city ."',postcode='". $postalcode ."' WHERE address_id='".$cus1_id."'");
		echo "<script>parent.$.fancybox.close();</script>";
		

	}


	$user_id = (int)$_GET['id'];
	//print_r($user_id);
	//exit;

	$user = $db->func_query_first("select * from inv_users where id = '$user_id'");
	//print_r($user);
	//exit;
	$user['country'] =$customer_id['country_id'];
	$user1['country'] =$customer1_id['country_id'];
	 //$db->func_query_first_cell("SELECT country_id FROM `oc_country` WHERE name = '". $user['country'] ."'");

$user['providence'] =$customer_id['zone_id'];
$user1['providence'] =$customer1_id['zone_id'];
 //$db->func_query_first_cell("SELECT zone_id FROM `oc_zone` WHERE name = '". $user['providence'] ."' AND country_id = '". $user['country'] ."'");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

</head>
<body>
	<div align="center">
		<div align="center" style="display: none;"> 
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
		
		<?php if($_GET['customer_id']){?>
			<h2>Edit <?= $pageName; ?></h2>
			

			<div id="new_address" >
				
				<form id="formEditAddress" method="post" enctype="multipart/form-data" >
					<label>First Name</label><br/>
					<input type="text" name="firstName" value="<?= $customer_id['firstname']; ?>">
					<br/>
					<label>Last Name</label><br/>
					<input type="text" name="lastName" value="<?= $customer_id['lastname']; ?>">
					<br/>
					<label>Company</label><br/>
					<input type="text" name="company" value="<?= $customer_id['company']; ?>">
					<br/>
					<label>Address 1</label><br/>
					<input type="text" name="addres_1" value="<?= $customer_id['address_1']; ?>">
					<br/>
					<label>Address 2</label><br/>
					<input type="text" name="addres_2" value="<?= $customer_id['address_2']; ?>">
					<br/>
					<label>City</label><br/>
					<input type="text" name="city" value="<?= $customer_id['city']; ?>">
					<br/>
					<label>Postal Code</label><br/>
					<input type="text" name="postalCode" value="<?= $customer_id['postcode']; ?>">
					<br/>
					<label>Country</label><br/>
					<select name="country">

							<option value="">--Select--</option>

							<?php foreach($db->func_query("SELECT * FROM `oc_country`") as $country):?>

								<option value="<?php echo $country['country_id'];?>" <?php if($country['country_id'] == $user['country']):?> selected="selected" <?php endif;?>><?php echo $country['name'];?></option>

							<?php endforeach;?>

						</select>
					<br/>

					<label>Region/State</label><br/>
					<select name="providence" required>

							<option value="">--Select--</option>

							<?php foreach($db->func_query("SELECT * FROM `oc_zone` WHERE `country_id` = '". $user['country'] ."'") as $zone):?>

								<option value="<?php echo $zone['name'];?>" <?php if($zone['zone_id'] == $user['providence']):?> selected="selected" <?php endif;?>><?php echo $zone['name'];?></option>

							<?php endforeach;?>

						</select>
					

						<br/>
					<input name="saveChanges" type="submit" value="Save changes"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					<input type="button" value="Cancel" onclick='parent.$.fancybox.close();'/>


				</form>
					

				</div>
				<?php }?>


		
		<?php if($_GET['c_id']){?>
			<h2>Edit <?= $pageName; ?></h2>
			

			<div id="new_address" >
				
				<form id="formEditAddress" method="post" enctype="multipart/form-data" >
					<label>Name</label><br/>
					<input type="text" name="firstName1" value="<?= $customer1_id['firstname']; ?>">
					<br/>
					<label>Last Name</label><br/>
					<input type="text" name="lastName1" value="<?= $customer1_id['lastname']; ?>">
					<br/>
					<label>Company</label><br/>
					<input type="text" name="company1" value="<?= $customer1_id['company']; ?>">
					<br/>
					<label>Address 1</label><br/>
					<input type="text" name="addres_11" value="<?= $customer_id['address_1']; ?>">
					<br/>
					<label>Address 2</label><br/>
					<input type="text" name="addres_21" value="<?= $customer_id['address_2']; ?>">
					<br/>
					<label>City</label><br/>
					<input type="text" name="city1" value="<?= $customer1_id['city']; ?>">
					<br/>
					<label>Postal Code</label><br/>
					<input type="text" name="postalCode1" value="<?= $customer1_id['postcode']; ?>">
					<br/>
					<label>Country</label><br/>
					<select name="country1">

							<option value="">--Select--</option>

							<?php foreach($db->func_query("SELECT * FROM `oc_country`") as $country):?>

								<option value="<?php echo $country['country_id'];?>" <?php if($country['country_id'] == $user1['country']):?> selected="selected" <?php endif;?>><?php echo $country['name'];?></option>

							<?php endforeach;?>

						</select>
					<br/>

					<label>Region/State</label><br/>
					<select name="providence" required>

							<option value="">--Select--</option>

							<?php foreach($db->func_query("SELECT * FROM `oc_zone` WHERE `country_id` = '". $user1['country'] ."'") as $zone):?>

								<option value="<?php echo $zone['name'];?>" <?php if($zone['zone_id'] == $user1['providence']):?> selected="selected" <?php endif;?>><?php echo $zone['name'];?></option>

							<?php endforeach;?>

						</select>
					

						<br/>
					<input name="saveChanges1" type="submit" value="Save changes"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					<input type="button" value="Cancel" onclick='parent.$.fancybox.close();'/>


				</form>
					

				</div>
				<?php }?>

		
	</div>
	<script>
		var i = 1;
		$(document).ready(function(e) {
			$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
			window.parent.loadContacts();
			});

		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

		function verifyEmail (t) {
			var email = $(t).val();
			var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
			if (!re.test(email)) {
				$(t).parent().find('.error').remove();
				$(t).parent().append('<span class="error">Enter Correct Email!<span>');
			} else {
				$(t).parent().find('.error').remove();
			}
		}
		function addRow () {
			$clone = $('#dataContacts tr:first-child').clone();
			$clone.find('select').attr('name', 'contact['+ i +'][type]').val('');
			$clone.find('input').attr('name', 'contact['+ i +'][contact]').val('');
			$clone.find('td:last-child').remove();
			$clone.append('<td><a href="javascript:void(0);" onclick="removeContact(this);">x</a></td>');
			$('#dataContacts').append($clone);
			i++;
		}
		function removeContact(t, contact_id) {
			if (contact_id) {
				$.ajax({
					url: '<?php echo $pageLink; ?>',
					type: 'POST',
					dataType: 'json',
					data: {id: contact_id, action: 'deleteContact'},
				}).always(function() {
					$(t).parent().parent().remove();
					window.parent.loadContacts();
				});
			} else {
				$(t).parent().parent().remove();
			}

		}
	</script>
</body>