<?php
require_once("auth.php");

$ebayLastCronDate   = $db->func_query_first_cell("select last_cron_date  from ebay_credential ORDER BY last_cron_date DESC");
$bigcommerceLastCronDate   = $db->func_query_first_cell("select last_cron_date  from bigcommerce_credential ORDER BY last_cron_date DESC");
$caLastCronDate     = $db->func_query_first_cell("select last_cron_date  from ca_credential ORDER BY last_cron_date DESC");
$amazonLastCronDate = $db->func_query_first_cell("select last_cron_date  from  amazon_credential ORDER BY last_cron_date DESC");
$webLastCronDate    = $db->func_query_first("select config_value  from configuration where config_key = 'WEB_LAST_CRON_TIME'");


$_query = "select * from inv_issues_complaints where find_in_set('".$_SESSION['user_id']."',assigned_to) and status = 'Created'";
$issues = $db->func_query($_query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Home</title>
	</head>
	<body>
		<div align="center"> 
		   <?php include_once 'inc/header.php';?>
		</div>
		
		 <?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		 <?php endif;?>
		 
		 <h2 align="center">Cron Summary</h2>
		 <table align="center"  style="border:1px solid #585858;border-collapse:collapse;" cellpadding="10px" width="60%" border="1" cellspacing="0">
			<tr>
				<th> eBay Last Cron Date : </th>
				<?php if($ebayLastCronDate) :?>
					<td> <?php echo date('d-M-Y H:i:s' , strtotime($ebayLastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
			
			<tr>
				<th> Bigcommerce Last Cron Date : </th>
				<?php if($bigcommerceLastCronDate) :?>
					<td> <?php echo date('d-M-Y H:i:s' , strtotime($bigcommerceLastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
			
			<tr>
				<th> Channel Advisor Last Cron Date : </th>
				<?php if($caLastCronDate) :?>
					<td> <?php echo date('d-M-Y H:i:s' , strtotime($caLastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
			
			<tr>
				<th> Amazon Last Cron Date : </th>
				<?php if($amazonLastCronDate) :?>
					<td> <?php echo date('d-M-Y H:i:s' , strtotime($amazonLastCronDate)) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
			
			<tr>
				<th> Web Last Cron Date : </th>
				<?php if($webLastCronDate['config_value']) :?>
					<td> <?php echo date('d-M-Y H:i:s' , strtotime($webLastCronDate['config_value'])) ;?> </td>
				<?php else :?>
					<td> Not Found.</td>
				<?php endif ;?>
			</tr>
        </table>
        
        <?php if(count($issues) > 0):?>
	        <br /><br/>
	        <h2>Issues / Complaints</h2>
	        <div align="center">
		 	  <table border="1" width="80%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
		 	  	 <tr style="background-color:#e7e7e7;">
		 	  	 	 <td>Date</td>
		 	  	 	 <td>SKU</td>
		 	  	 	 <td>Item Title</td>
		 	  	 	 <td>Revision Issue</td>
		 	  	 	 <td>Complaint Issue</td>
		 	  	 	 <td>Notes</td>
		 	  	 	 <td>Status</td>
		 	  	 	 <td colspan="1" align="center">Action</td>
		 	  	 </tr>
		 	  	 
		 	  	 <?php foreach($issues as $result):?>
		 	  	 	<tr>
			 	  	 	 <td><?php echo $result['date_added']; ?></td>
			 	  	 	 
			 	  	 	 <td><?php echo $result['sku']; ?></td>
			 	  	 	 
			 	  	 	 <td><?php echo $result['item_title']; ?></td>
			 	  	 	 
			 	  	 	 <td><?php echo $result['revision_issue']; ?></td>
			 	  	 	
			 	  	 	 <td><?php echo $result['item_issue']; ?></td>
			 	  	 	 
			 	  	 	 <td><?php echo $result['notes']; ?></td>
			 	  	 	 
			 	  	 	 <td><?php echo $result['status']; ?></td>
			 	  	 	 
			 	  	 	 <td><a href="issues_complaint_view.php?id=<?php echo $result['id']; ?>">View</a></td>
		 	  	   </tr>
		 	  	 <?php endforeach;?>
		 	 </table>  	 
		   </div>	 
		<?php endif;?>	   
   </body>
</html>