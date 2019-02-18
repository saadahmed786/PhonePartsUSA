<?php
require_once("auth.php");
require_once("inc/functions.php");
$page = "whitelist_ip.php";



if($_POST['add']){	
	$array = array();
	foreach($_POST['ip'] as $ips)
	{
		if($ips)
		{
		$array[] = $ips;
		}
	}
	$serialize = serialize($array);
	$db->db_exec("UPDATE oc_setting SET value='$serialize',serialized=1 WHERE `key`='config_security_ipgrants'");
	$_SESSION['message']='Whitelist IP Updated';
	header("Location: whitelist_ip.php");
	exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Admin Security Whitelist</title>
	</head>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script>
	 
	
	
	function addRow(){
	var current_row = $('#variants tr').length+1;	
		 	   var row = "<tr>"+
		 	   				 "<td align='center'>"+(current_row)+"</td>"+
						 	 " <td align='center'><input type='text' name='ip["+current_row+"]'  /></td>"+
							 
							 "<td align='center'><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>"+
						 "</tr>";
			   $("#variants").append(row);		
			   current_row++;	 
	 	   }
	
	</script>
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
			 
			 <form action="" method="post">
			   	<h2>Add Whitelist IP</h2>
			   
                
             
                
                
                
                <table border="1" width="50%" cellpadding="5" cellspacing="0" align="center" id="variants" style="">
					 <tr>	
					 	 <th>#</th>
					 	 <th>IPv4 Address</th>
					 	 
					 	 <th>
					 	 	 <a href="javascript://" onclick="addRow();">Add Row</a>
					 	 </th>
					 </tr>	
					 
						<?php
						
							$ips= oc_config("config_security_ipgrants");
							$ips = unserialize($ips);

							
							$i=0;
							foreach($ips as $xrow)
							{
								
								
							?>
                            
                             <tr>
						 	 <td align="center"><?php echo $i+1;?></td>
						 	 <td align="center"><input type="text" name="ip[<?php echo $i;?>]" value="<?php echo $xrow;?>" /><input type="hidden" name="attributes[<?php echo $i;?>][id]" value="<?php echo $xrow['id'];?>" /></td>
							  
							 <td align="center"><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>
						 </tr>
                            <?php	
								$i++;
							}
							
						
						
						?>
					
				</table>
                <br /><br />
              <div style="text-align:center">  <input type="submit" name="add" value="Submit" /></div>
                
			 </form>
		 </div>
	</body>
</html>			 		 