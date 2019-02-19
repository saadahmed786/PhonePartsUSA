<?php
require_once("auth.php");

if($_SESSION['login_as'] != 'admin'){
	$_SESSION['message'] = 'You dont have permission to manage users.';
	echo 'You dont have permission to manage scrap.';
	exit;
}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Scrape Mobile Defender</title>
        <script type="text/javascript" src="js/jquery.min.js"></script>
    <script>
	function ScrapeThis(type)
	{
		var url = $('input[name=url]').val();
		if(url=='')
		{
			alert("Please paste the url");
			return false;
		}
	$.ajax({
					  url: "ajax_mobiledefender.php",
					  data: {url : url , type : type},
					  type:'POST',
					  beforeSend: function() {
			$('#myData').html('<img src="images/loading.gif" style="height:64px;width:64px">');
		},
				      complete: function(){
				    	 
				      },  success: function(data){
						$('#myData').html(data); 
				      }
			     });	
		
	}
	
	</script>
	</head>
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
			 	<h2>Paste URL (Any Product Detail Page URL e.g http://www.mobiledefenders.com/apple/iphone-5s.html)</h2>
			    <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;" style="width:900px">
			         <tr>
			             <td>URL</td>
			         	 <td><input type="text" name="url"  required style="width:800px" /></td>
			         </tr>
			         
			         
			         
			         
			         
			         
			         
			         
			         
			         <tr>
			             <td colspan="2">
			             	 <input type="button" id="import" value="Scrape and Import" onclick="ScrapeThis(this.id)" /> <input type="button" id="scrape" value="Scrape Only" onclick="ScrapeThis(this.id)" /> 
			             </td>
			         </tr>
			    </table><br />
                <div id="myData"></div>
			 </form>
		 </div>
	</body>
</html>			 		 