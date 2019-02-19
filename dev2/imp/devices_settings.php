<?php

require_once("auth.php");
require_once("inc/functions.php");
page_permission("device_list");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?=$title;?></title>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		
	
	</head>
	<body>
		<div align="center">
			<div align="center" style="display:none">
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <form action="" method="post" enctype="multipart/form-data">
			 	<h2>Settings</h2>
             
		<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
        <tr>
        <td align="center"><a href="device_type.php" class="button">Add Device Type</a></td>
        <td align="center"><a href="device_issue.php" class="button">Add Device Issues</a></td>
        <td align="center"><a href="device_storage.php" class="button">Add Storage</a></td>
        <td align="center"><a href="device_status.php" class="button">Add Status</a></td>
          
        </tr>
         <tr>
        <td align="center"><a href="device_manufacturer.php" class="button">Add Manufacturer</a></td>
        <td align="center"><a href="device_location.php" class="button">Add Location</a></td>
        <td align="center"><a href="device_accessory.php" class="button">Add Accessories</a></td>
        <td align="center"><a href="device_carrier.php" class="button">Add Carrier</a></td>
        
          
        </tr>
         <tr>
        <td align="center"><a href="device_model.php" class="button">Add Device Model</a></td>
        <td align="center"><a href="device_os.php" class="button">Add OS</a></td>
        <td align="center" colspan="2"><a href="device_grade.php" class="button">Add Grade(s)</a></td>
        
          
        </tr>
        
        
        </table>	
            
			 </form>
		 </div>
         
         
         
	</body>
</html>			 		 