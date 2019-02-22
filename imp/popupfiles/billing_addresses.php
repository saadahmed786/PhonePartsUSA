<?php
require_once("../auth.php");

$customer_id = (int)$_GET['customer_id'];
if(!$customer_id){
	return;
}

$cols = array('payment_firstname','payment_lastname','payment_address_1','payment_address_2','payment_city','payment_zone','payment_country','payment_postcode');
$billing_addresses = $db->func_query("Select ".implode(",", $cols)." from oc_order where customer_id = '$customer_id'");
?>
<div>
   <table border="1" style="border-collapse:collapse;" cellpadding="10">
       <tr>
       	  <th>First Name</th>
		  <th>Last Name</th>
		  <th>Address 1</th>
		  <th>Address 2</th>
		  <th>City</th>
		  <th>State</th>
		  <th>Country</th>
		  <th>Zip</th>
       </tr>		   
       <?php foreach($billing_addresses as $address): if($address['payment_postcode']):?>
		   <tr>
	       	  <td><?php echo $address['payment_firstname']?></td>
			  <td><?php echo $address['payment_lastname']?></td>
			  <td><?php echo $address['payment_address_1']?></td>
			  <td><?php echo $address['payment_address_2']?></td>
			  <td><?php echo $address['payment_city']?></td>
			  <td><?php echo $address['payment_zone']?></td>
			  <td><?php echo $address['payment_country']?></td>
			  <td><?php echo $address['payment_postcode']?></td> 
	       </tr>
	  <?php endif; endforeach;?>		       
   </table>
</div>