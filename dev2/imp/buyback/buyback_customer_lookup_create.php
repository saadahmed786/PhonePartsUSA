<?php
require_once("../config.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';

$page = (int)$_GET['page'];
if(!$page){
	$page = 1;
}
$buyback_detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE shipment_number='".$_GET['shipment']."'");

if(isset($_GET['action']) && $_GET['action']=='map_buyback' )
{
	
		$data = array();
		$data['firstname'] = $db->func_escape_string($_POST['firstname']);
		$data['lastname'] = $db->func_escape_string($_POST['lastname']);
		$data['email'] = $db->func_escape_string($_POST['email']);
		$data['telephone'] = $db->func_escape_string($_POST['telephone']);
		$data['address_1'] = $db->func_escape_string($_POST['address_1']);
		$data['city'] = $db->func_escape_string($_POST['city']);
		$data['postcode'] = $db->func_escape_string($_POST['postcode']);
		$data['zone_id'] = $db->func_escape_string($_POST['state']);
		$data['address_id'] = '-1';
		$data['customer_id'] = 0;
$db->func_array2update('oc_buyback', $data, 'buyback_id = "'. $buyback_detail['buyback_id'] .'"');
//print_r($buyback_detail);exit;
actionLog(linkToLbbShipment($_GET['shipment']). ' details are changed through mapping.');

		echo json_encode(array('success'=>1));
		exit;
	
}

$where = array();
if($_GET['email']){
	$email = $db->func_escape_string($_GET['email']);
	$where[] = " a.email = '$email' ";
	$parameters[] = "email=$email";
}

if($_GET['firstname']){
	$firstname = $db->func_escape_string($_GET['firstname']);
	$where[] = " a.firstname = '$firstname' ";
	$parameters[] = "firstname=$firstname";
}

if($_GET['lastname']){
	$lastname = $db->func_escape_string($_GET['lastname']);
	$where[] = " a.lastname = '$lastname' ";
	$parameters[] = "lastname=$lastname";
}

// if($_GET['customer_group']){
// 	$customer_group = $db->func_escape_string($_GET['customer_group']);
// 	$where[] = " customer_group_id = '$customer_group' ";
// 	$parameters[] = "customer_group=$customer_group";
// }
if($_GET['postcode']){
	$postcode = $db->func_escape_string($_GET['postcode']);
	$where[] = " b.postcode = '$postcode' ";
	$parameters[] = "postcode=$postcode";
}


if($where){
	$where = implode(" AND ",$where);
}
else{
	$where = ' 1 = 1';
}

$_query = "SELECT a.* FROM oc_customer a,oc_address b WHERE a.address_id=b.address_id and a.`status`=1 AND a.approved=1 and $where order by a.firstname,a.lastname";
$splitPage   = new splitPageResults($db , $_query , 25 , "buyback_customer_lookup_create.php",$page ,  $count_query);
$rows = $db->func_query($splitPage->sql_query);


if($parameters){
	$parameters = implode("&",$parameters);
}
else{
	$parameters = '';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Customer Lookup</title>
	 <script type="text/javascript" src="../js/jquery.min.js"></script>
     <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	 
	 
	
	 <style type="text/css">
	 	.data td,.data th{
	 		 border: 1px solid #e8e8e8;
             text-align:center;
             width: 150px;
         }
         .div-fixed{
			 position:fixed;
			 top:0px;
			 left:8px;
			 background:#fff;
			 width:98.8%; 
		 }
		 .red td{ box-shadow:1px 2px 5px #990000}
	 </style>
  </head>
  <body>
		<div align="center" style="display:none"> 
		   <?php include_once '../inc/header.php';?>
		</div>
		 
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php else:?>
			<br /><br /> 
		<?php endif;?>
		
		<div align="center">
			<form action="" method="get">
				 <table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
				    <tr>
				        <td>
							Email: <?php echo createField("email","email","text",$_GET['email']);?>				        
				        </td>
				        
				        <td>
							Firstname: <?php echo createField("firstname","firstname","text",$_GET['firstname']);?>				        
				        </td>
				        
				        <td>
							Lastname: <?php echo createField("lastname","lastname","text",$_GET['lastname']);?>				        
				        </td>
				        
				        <td>
                        <?php
						$customer_groups = $db->func_query("SELECT customer_group_id as id, name as value FROM oc_customer_group_description");
						
						?>
							Zip Code: <?php echo createField("postcode","postcode","txt",$_GET['postcode']);?>			
							<input type="hidden" name="shipment" value="<?=$_GET['shipment'];?>">	        
				        </td>
				    </tr>
				 </table>
				 <br />
				 <input type="submit" name="search" value="Search" class="button" />
			</form>
	   </div>			
	   <br />
	
	   <div>	
		   <table class="data" border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="5">
		   	   <tr style="background:#e5e5e5;">
					<th style="width:50px;">#</th>
					<th>Firstname</th>
					<th>Lastname</th>
					<th>Email</th>
					
					<th>Address 1</th>
				    <th>City</th>
                    <th>State</th>
                    <th>Zip Code</th>
                    <th>Action</th>
                    
			   </tr>
               
			   <?php foreach($rows as $k => $row):?>
               <?php
			   $address_detail = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".(int)$row['address_id']."'");
			   ?>
			   		<tr>
					   <td style="width:50px;"><?php echo $k+1;?></td>			   		
			   		   <td><?php echo $row['firstname'];?></td>
                       <td><?php echo $row['lastname'];?></td>
                       <td><?php echo $row['email'];?></td>
                       
			   		  <td><?php echo $address_detail['address_1'];?></td>
                      <td><?php echo $address_detail['city'];?></td>
                      <td><?php echo $state =  $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$address_detail['zone_id']."'");?> </td>
                      <td><?php echo $address_detail['postcode'];?></td>
                      <td><a href="javascript:void(0)" onclick="selectThis('<?php echo $db->func_escape_string($row['firstname']);?>','<?php echo $db->func_escape_string($row['lastname']);?>','<?php echo $db->func_escape_string($row['email']);?>','<?php echo $db->func_escape_string($row['telephone']);?>','<?php echo $db->func_escape_string($row['customer_group_id']);?>','<?php echo $db->func_escape_string($address_detail['address_1']);?>','<?php echo $db->func_escape_string($address_detail['city']);?>','<?php echo $db->func_escape_string($address_detail['zone_id']);?>','<?php echo $db->func_escape_string($address_detail['postcode']);?>','<?php echo $row['customer_id'];?>')">Select</a></td>
                     
                      
			   		 
			   		</tr>
			   <?php endforeach;?>
		   </table>
		   
		   <br /><br />
		   <table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
				 <tr>
		                 <td colspan="7" align="left">
		                       <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
		                 </td>
		                      
		                 <td colspan="6" align="right">
		                     <?php echo $splitPage->display_links(10,$parameters);?>
		                 </td>
		           </tr>
			</table>
			<br />
      </div>		
  </body>
</html>  
<script>
function selectThis(firstname,lastname,email,phone,customer_group_id,address_1,city,state,postcode,customer_id)
{
	if(!confirm('Are you sure want to map this customer to buyback?'))
	{
		return false;
	}
	
	// $.ajax({
	// 									url: 'buyback_customer_lookup.php?action=map_buyback&shipment=<?=$_GET['shipment'];?>',
	// 									type:"POST",
	// 									dataType:"json",
	// 									data:{customer_id:customer_id,firstname:firstname,lastname:lastname,email:email,phone:phone,address_1:address_1,city:city,state:state,postcode:postcode},
	// 									success: function(json){
	// 										if (json['success']) {
	// 											alert('Customer mapped successfully!');
	// 											 parent.location.reload(true);
	// 										}
	// 									}
	// 								});
									// alert(state);
$('#firstname',window.parent.document).val(firstname);	
$('#lastname',window.parent.document).val(lastname);	
$('#email',window.parent.document).val(email);	
$('#telephone',window.parent.document).val(phone);	
$('#address_1',window.parent.document).val(address_1);	
$('#city',window.parent.document).val(city);
$('#xstate',window.parent.document).val(state);

$('#postcode',window.parent.document).val(postcode);	
// $('#customer_group_id',window.parent.document).val(customer_group_id);	
$('#customer_id',window.parent.document).val(customer_id);	

parent.jQuery.fancybox.close();

}
</script>          			   