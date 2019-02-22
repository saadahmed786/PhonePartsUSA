<?php
require_once("../auth.php");

$customer_id = (int)$_GET['customer_id'];
if(!$customer_id){
	return;
}

$cols = array('shipping_firstname','shipping_lastname','shipping_address_1','shipping_address_2','shipping_city','shipping_zone','shipping_country','shipping_postcode');
$shipping_addresses = $db->func_query("Select ".implode(",", $cols)." from oc_order where customer_id = '$customer_id'");
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
       <?php foreach($shipping_addresses as $address): if($address['shipping_postcode']):?>
		   <tr>
	       	  <td><?php echo $address['shipping_firstname']?></td>
			  <td><?php echo $address['shipping_lastname']?></td>
			  <td><?php echo $address['shipping_address_1']?></td>
			  <td><?php echo $address['shipping_address_2']?></td>
			  <td><?php echo $address['shipping_city']?></td>
			  <td><?php echo $address['shipping_zone']?></td>
			  <td><?php echo $address['shipping_country']?></td>
			  <td><?php echo $address['shipping_postcode']?></td> 
	       </tr>
	  <?php endif; endforeach;?>		       
   </table>
</div>