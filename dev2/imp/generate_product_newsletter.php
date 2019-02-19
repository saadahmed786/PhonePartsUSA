<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

if($_SESSION['login_as'] != 'admin'){
	echo 'You dont have permission to generate news letter.';
	exit;
}

if(isset($_POST['sku']) and count($_POST['sku'])>0)
{
	$html ="";
	
	$html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email News Letter</title>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"> 
<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css"> 
<style>
h2 a {
	font-family: "Roboto", sans-serif !important;
	color:#2d2d2d !important;
}
h4{
	font-family:"Lato";
	color:#918f8f;
}

</style>
</head>

<body style="background:#EEEEEE; margin:0; padding:20px 0 0 0;">
<table cellpadding="0" cellspacing="0" align="center" width="700">
	<tr>
    	<td style="text-align:center;">
        	<img style="width:400px;" src="https://beta.phonepartsusa.com/image/logo_new.png" />
        </td>
    </tr>
	<tr>
    	<td style="text-align:center;">
		<table cellpadding="0" cellspacing="20" align="center" width="800" style="margin:0;">
		<tr>
	';
    
        	
        
    

	$i=1;
	foreach($_POST['sku'] as $sku)
	{
		$url = 'http://phonepartsusa.com/index.php?route=product/product&product_id='.getResult("SELECT product_id FROM oc_product WHERE sku='".$sku."'");
		$html.='<td style="background:#fff; padding:25px 0; border:1px solid #d0d0d0;"><a href="'.$url.'"><img width="150" height="150" src="http://cdn.phonepartsusa.com/image/'.getItemImage($sku).'"/></a>
                    	<h2 style="padding:10px 20px; margin:0;"><a style="font-size:18px; text-decoration:none;" href="'.$url.'">'.getItemName($sku).'</a></h2>
                        <h3 style="font-size:16px; color:#1AB0F3; padding:0 20px; margin:0; font-family:Arial, Helvetica, sans-serif;"></h3>
                        <h4 style="font-size:14px;padding:0 20px; margin:0; ">Model #: '.$sku.'</h4>
                        <span style="display:inline-block; margin:10px 0 0 0;">'.getLeastPrice($sku,$_POST['customer_group_id']).'</span>
                    </td>';
		
		
		if($i!=0 and $i%3==0)
		{
			$html.='
			</tr>
	</table>
	</td>
	</tr>
			<tr>
    	<td style="text-align:center;">
		<table cellpadding="0" cellspacing="20" align="center" width="800" style="margin:0;">
		<tr>';	
			
		}
		$i++;
		
		
	}
	
	$html.='
	</tr>
	</table>
	</td>
	</tr>
	</table>
	<table cellpadding="0" cellspacing="0" align="center" style="background:#363636; height:160px; width:100%; padding:20px 0;">
	<tr>
    	<td align="center" valign="top">
        	<table cellpadding="0" cellspacing="0" width="700" style="margin:0;">
        	<tr>
            	<td>
                	<p style="color:#fff; font-family:\'Roboto\'; font-size:14px;">Copyright &copy; '.date('Y').' PhonePartsUSA.com, All rights reserved.<br />You are receiving this email because you are a current account holder at our PhonePartsUSA website. </p>
                    <p style="color:#fff; font-family:\'Roboto\'; font-size:14px;">Our Mailing Address Is:<br /> PhonePartsUSA<br />344 N Ogden Ave<br />Chicago, IL 60607</p>
                    <p><a style="color:#fff; font-family:\'Roboto\'; font-size:14px; text-decoration:none;" href="#">Add Us to Your Address book</a></p>
                    <p style="color:#fff; font-family:\'Roboto\'; font-size:14px;"><a style="color:#fff; font-family:\'Roboto\'; font-size:14px;" href="#">Unsubscribe Form This List</a> | <a style="color:#fff; font-family:\'Roboto\'; font-size:14px;" href="#">Update Subscription Preferences</a></p>
                </td>
            </tr>
        </table>
        </td>
    </tr>
</table>

</body>
</html>
';

 header("Content-type: text/html");
   header("Content-Disposition: attachment; filename=newsletter.html");

   // do your Db stuff here to get the content into $content
   
echo $html;exit;
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Generate Newsletter</title>
	 <style type="text/css">
	 	table td{text-align:center;}
	 </style>
	 
	 <script type="text/javascript" src="js/jquery.min.js"></script>
     
	
	
     <link rel="stylesheet" type="text/css" href="include/jquery.autocomplete.css" media="screen" />
	 
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
		
		
		<br />
        <div class="search" align="center">
		 	Customer Group
               <select  onchange="$('#customer_group_id').val(this.value);" style="font-size: 28px; padding: 10px; border: 1px solid #CCC; display: block; margin: 20px 0;"> 
               <option value="-1">Sale Price</option>
               <option value="0">Least Price</option>
               <option value="8">Default</option>
               <option value="6">Wholesale</option>
               <option value="10">Local</option>
               <option value="1631">Silver</option>
               <option value="1632">Gold</option>
               <option value="1633">Platinum</option>
               <option value="1634">Diamond</option>

               </select> 
               <br>
		 		 SKU: 
		 		 <input type="text" id="autocomplete" value="<?php echo $keyword;?>"  /><div style="float:left" id="loader"></div>
             
                
		 	
		</div>
		<br />
		 <form action="generate_product_newsletter.php" method="post" >
		<table id="data_table"  border="1" style="border-collapse:collapse;clear:both" width="80%" align="center" cellpadding="3">
			<tr style="background:#e5e5e5;">
			    
			    <th width="450px">Item</th>
			    <th width="100px">Action</th>
			</tr>
			
		</table>
        
		<br />       
        <div align="center" >
        <input type="submit" class="button" value="Generate HTML" /> 
        
        </div>
        <input type="hidden" id="customer_group_id" name="customer_group_id" value="-1">
        </form>
    </body>
</html>
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<script>
$(document).ready(function(e) {

$('#autocomplete').autocomplete({
    serviceUrl: 'popupfiles/search_products.php',
	onSearchStart: function(){
		$('.loading').remove();
		$('#loader').after('<img src="images/loading.gif" height="42" width="42" class="loading">');
	},
	onSearchComplete: function(){
		$('.loading').remove();
	},
    onSelect: function (suggestion) {
		html="";
		html+="<tr><td>"+(suggestion.value)+"</td><td><img style='cursor:pointer' onClick='$(this).parent().parent().remove();' src='images/cross.png'><input type='hidden' name='sku[]' value='"+suggestion.data+"'></td></tr>";
       $('#data_table').append(html);
	   $('#autocomplete').val('');
	      }
});

    
});
</script>	 	
