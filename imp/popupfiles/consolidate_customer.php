<?php
include '../auth.php';
if($_SESSION['login_as']!='admin' && !$_SESSION['update_customer_email'])
{
	echo 'You do not have sufficent permission to access this page';exit;
}
if(isset($_POST['action']) && $_POST['action']=='consolidate_this')
{
	$email = trim(strtolower($_POST['email']));;
	$account_email = trim(strtolower($_POST['account_email']));

	$parent_id = $db->func_query_first("SELECT id,parent_id from inv_customers WHERE trim(lower(email))='".$db->func_escape_string($account_email)."' ");

	$status = 0;

	if($parent_id)
	{
		if($parent_id['parent_id'])
		{
		$status = 1;
		
		$db->func_query("UPDATE inv_customers SET parent_id='".(int)$parent_id['parent_id']."' where trim(lower(email))='".$db->func_escape_string($email)."'");
		}
		else
		{
			// echo "UPDATE inv_customers SET parent_id='".(int)$parent_id['id']."' where trim(lower(email))='".$db->func_escape_string($email)."'";exit;
			$status = 1;
		$db->func_query("UPDATE inv_customers SET parent_id='".(int)$parent_id['id']."' where trim(lower(email))='".$db->func_escape_string($email)."'");

		$db->func_query("UPDATE inv_customers SET parent_id='0' where trim(lower(email))='".$db->func_escape_string($account_email)."'");
		}
	}

	echo json_encode(array('status'=>$status));
	exit;


}

if(isset($_POST['action']) && $_POST['action']=='remove_consolidation')
{
	$email = trim(strtolower($_POST['email']));;
	

	$parent_id = $db->func_query_first("SELECT id,parent_id from inv_customers WHERE trim(lower(email))='".$db->func_escape_string($email)."' ");

	$status = 0;

	if($parent_id)
	{
		$status = 1;
		$db->func_query("UPDATE inv_customers SET parent_id='0' where id='".(int)$parent_id['id']."'");
		if($parent_id['parent_id'])
		{
		$status = 1;
		//$db->func_query("UPDATE inv_customers SET parent_id='0' where id='".(int)$parent_id['id']."'");
		}
		else
		{
			// echo "UPDATE inv_customers SET parent_id='0' where parent_id='".(int)$parent['id']."'";exit;
			$status = 1;
		//$db->func_query("UPDATE inv_customers SET parent_id='0' where parent_id='".(int)$parent_id['id']."'");
		}
	}	

	//$ids = $db->func_query_first("SELECT id,parent_id from inv_customers WHERE trim(lower(email))='".$db->func_escape_string($email)."' limit 1 ");

	

	
		// $status = 1;
		// $db->func_query("UPDATE inv_customers SET parent_id='0' where parent_id='".$ids['id']."' or id='".$ids['id']."'");

		// if($ids['parent_id'])
		// {
		// 		// $db->func_query("UPDATE inv_customers SET parent_id='0' where parent_id='".$ids['parent_id']."' or id='".$ids['id']."'");
		// }


	echo json_encode(array('status'=>$status));
	exit;


}
 
 $customer_email = $_GET['customer_id'];
 $customer_info = $db->func_query_first("SELECT parent_id FROM inv_customers where trim(lower(email))='".trim(strtolower($customer_email))."'");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Update Account</title>
  </head>
  <body>
  <div align="center" style="display:none"> 
		<?php include_once '../inc/header.php';?>
	</div>
  	  <div class="div-fixed">
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			 <h2>Assign Contact to Account</h2>
			 <form method="post" action="" enctype="multipart/form-data">
			     <table cellpadding="10" cellspacing="0">
			         <tr>
			         	<td>Customer:</td>
			         	<td colspan="3">
			         	    <?php echo $customer_email;?>
			         	</td>
			         </tr>

			         <tr>
			         	<td>Account Email (case-insensitive):</td>
			         	<td colspan="3">
			         	    <input type="text" id="account_to_add">
			         	</td>
			         </tr>
			         
                     
                     
			         
			         <tr>
			             <td align="center" colspan="4">
			                 <input class="button" type="button"  value="Move Account" onclick="consolidateThis()" />
			                 <?php
			                // if($customer_info['parent_id']==0)
			                 //{
			                 ?>
			                  <input class="button button-danger" type="button"  value="Remove Consolidation" onclick="removeConsolidation()" />
			                  <?php
			              //}
			              ?>
			             </td>
			         </tr>
			     </table>
			 </form>
		</div>
	 </div>
  </body>
</html>  	 

<script>

function consolidateThis()
{
	if(!confirm('Are you sure want to move this into the account mentioned?'))
	{
		return false;
	}

	$.ajax({
			url: 'consolidate_customer.php',
			type: 'post',
			dataType:'json',
			data: {action: 'consolidate_this',email:'<?php echo $customer_email;?>',account_email:$('#account_to_add').val()},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (json) {
				if(json['status']==1)
				{
					alert('Contact successfully mapped to account');
					window.parent.location.reload();
				}
				else
				{
					alert('There is some problem mapping contact to the customer, please try again or contact admin');
				}
			}
		});


}

function removeConsolidation()
{
	if(!confirm('Caution: Are you sure want to remove this account Consolidation, your action is not revertable?'))
	{
		return false;
	}

	$.ajax({
			url: 'consolidate_customer.php',
			type: 'post',
			dataType:'json',
			data: {action: 'remove_consolidation',email:'<?php echo $customer_email;?>'},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (json) {
				if(json['status']==1)
				{
					alert('Consolidation Removed');
					window.parent.location.reload();
				}
				else
				{
					alert('There is some problem mapping contact to the customer, please try again or contact admin');
				}
			}
		});


}
</script>