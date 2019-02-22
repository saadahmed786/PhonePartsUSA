<?php

require_once("../auth.php");

$customer_id = (int)$_GET['customer_id'];
if(!$customer_id){
	$_SESSION['message'] = "Customer details not found";
	header("Location:$host_path/home.php");
	exit;
}

if($_POST['Add'] && $_POST['comments']){
	$order_extras = array();
	$order_extras['customer_id'] = $customer_id;
	$order_extras['order_id']    = $_POST['order_id'];
	$order_extras['comments']    = $_POST['comments'];
	$order_extras['user_id']     = $_SESSION['user_id'];
	
	$db->func_array2insert("inv_customer_comments",$order_extras);
	$_SESSION['message'] = "Comments added successfully.";
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

$orders = $db->func_query("select order_id from oc_order where customer_id = '$customer_id' order by date_added DESC");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Add Comments</title>
	 <script type="text/javascript" src="js/jquery.min.js"></script>
	 <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
  </head>
  <body>
  	  <div class="div-fixed">
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			 <h2>Notes</h2>
			 
			 <form method="post" action="" enctype="multipart/form-data">
			     <table cellpadding="5" cellspacing="0">
			         <tr>
			             <td>Order ID</td>
			             <td>
			             	 <select name="order_id" required>
			             	    <option value="">Select One</option>
			             	 	<?php foreach($orders as $order):?>
			             	 		<option value="<?php echo (int)$order['order_id']?>"><?php echo (int)$order['order_id']?></option>
			             	 	<?php endforeach;?>
			             	 </select>
			             </td>
			         </tr>
			         
			         <tr>
			         	<td>Comments:</td>
			         	<td>
			         	    <textarea name="comments" rows="3" cols="40" required></textarea>
			         	</td>
			         </tr>
			         
			         <tr>
			             <td align="center" colspan="2">
			                 <input type="submit" name="Add" value="Add" />
			             </td>
			         </tr>
			     </table>
			 </form>
		</div>
	 </div>
  </body>
</html>  	 		 