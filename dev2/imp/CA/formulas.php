<?php

include '../config.php';

$ca_credentials = $db->func_query("select * from ca_credential");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>CA Keys</title>
	 <style type="text/css">
	 	table td{text-align:center;}
	 </style>
	 
	 <script type="text/javascript" src="../js/jquery.min.js"></script>
	 <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	 
	 <script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '450px' , autoCenter : true , autoSize : true });
			$('.fancybox3').fancybox({ width: '1200px', height : '800px' , autoCenter : true , autoSize : false });
		});
	 </script>	
  </head>
  <body>
		<div align="center"> 
		   <?php include_once '../inc/header.php';?>
		</div>
		
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<h3 align="center">CA API Keys</h3>
		
		<table class="footer" border="1" style="border-collapse:collapse;" width="80%" align="center" cellpadding="3">
			<tr style="background:#e5e5e5;">
			    <th>Account</th>
			    <td>Prefix</th>
			    <th>Formula</th>
			    <th>Date Updated</th>
			    <th>Action</th>
			</tr>
			<?php foreach($ca_credentials as $ca_credential):?>
				<tr>
				    <td><?php echo $ca_credential['account_id'];?></td>
				    <td><?php echo $ca_credential['prefix'];?></td>
				    <td><?php echo $ca_credential['formula'];?></td>
				    <td><?php echo $ca_credential['datemodification'];?></td>
				    <td>
				    	<a href="formula_edit.php?id=<?php echo $ca_credential['id']?>" class="fancybox fancybox.iframe">Edit</a>
				    </td>
				</tr>
			<?php endforeach;?>
		</table>
		<br />       
    </body>
</html>