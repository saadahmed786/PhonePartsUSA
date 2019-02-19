<?php

date_default_timezone_set("America/Los_Angeles");
include_once 'auth.php';

if(isset($_GET['action'])){
	$db->db_exec("update inv_prices_cron set last_id = 0");
	
	$_SESSION['message'] = 'Cron has been reset. Now you can run again';
	header("Location:update_script.php");
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
     <script type="text/javascript" src="<?php echo $host_path?>/js/jquery.min.js"></script>
     <script type="text/javascript">
     	  function startImportNow(){
     		  marketplaces = $('.marketplaces:checked').map(function () { return this.value; }).get().join(',');
     		  if(marketplaces.length == 0){
         		  alert("Please select at least one marketplace.");
         		  return false;
         	  }

     		  jQuery("#startimport").html("Please wait...");
         	  jQuery.ajax({
             	  url : "crons/ajax_prices.php?action=startImportNow&marketplaces="+ encodeURIComponent(marketplaces),
             	  success: function(){
             	  	  jQuery("#startimport").hide();
             	  	  jQuery("#processImoort").show();
         		 	  setInterval("getProcessUpdate()",2000);
             	  }
              });
          }

          function getProcessUpdate(){
        	  jQuery.ajax({
             	  url : "crons/ajax_prices.php?action=getProcessUpdate",
             	  success: function(response){
             	  	  if(response == 400){
             	  		 jQuery("#processImoort").html("Import process completed");
                 	  }
                 	  
             	      jQuery("#process").css({
                 	      'width': response
                 	  });

             	      percent = (response / 4).toFixed(2);
                 	  jQuery("#importData").html(percent +" % Complete");
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
		
  	 	 <div id="message" align="center">
	  	 	 <?php if(isset($_SESSION['message'])):?>
	  	 	 		<?php echo $_SESSION['message']; unset($_SESSION['message']);?>
	  	 	 <?php endif;?>
	  	 </div>	 
	  	 
  	 	 <center>
  	 		<div>
  	 			<form>
  	 				<h1 align="center">Select Market Places to update</h1>
  	 				<table>
  	 					<tr>
  	 						<td>Channel Advisor MM</td>
  	 						<td>
  	 							<input type="checkbox" class="marketplaces" name="marketplaces[]" value="channel_advisor" />
  	 						</td>
  	 					</tr>
  	 					<tr>
  	 						<td>Channel Advisor 1US</td>
  	 						<td>
  	 							<input type="checkbox" class="marketplaces" name="marketplaces[]" value="channel_advisor1" />
  	 						</td>
  	 					</tr>
  	 					<tr>
  	 						<td>Channel Advisor 2US</td>
  	 						<td>
  	 							<input type="checkbox" class="marketplaces" name="marketplaces[]" value="channel_advisor2" />
  	 						</td>
  	 					</tr>
  	 					<tr>
  	 						<td>Bigcommerce</td>
  	 						<td>
  	 							<input type="checkbox" class="marketplaces" name="marketplaces[]" value="bigcommerce" />
  	 						</td>
  	 					</tr>
  	 					<tr>
  	 						<td>Bigcommerce Retail</td>
  	 						<td>
  	 							<input type="checkbox" class="marketplaces" name="marketplaces[]" value="bigcommerce_retail" />
  	 						</td>
  	 					</tr>
  	 					<tr>
  	 						<td>Bonanza</td>
  	 						<td>
  	 							<input type="checkbox" class="marketplaces" name="marketplaces[]" value="bonanza" />
  	 						</td>
  	 					</tr>
  	 				</table>
  	 				
  	 				<br />
  	 				
		  	 		<div id="startimport">
			  	 		<a href="javascript://" onclick="startImportNow();">
			  	 			Start Update Process 
			  	 		</a>
		  	 		</div>
		  	 		
		  	 		<div id="processImoort" style="display:none;">
		  	 			Update Process is Running
		  	 		</div>
		  	 		
		  	 		<br />
		  	 		
		  	 		<div id="progressBar" align="left" style="width:400px;border:1px solid #000;">
		  	 			 <div id="process" style="background-color:blue;padding:10px 0px;width:0px;"></div>
		  	 		</div>
		  	 		<div id="importData"></div>
	  	 		</form>
	  	 	</div>
	  	 	
	  	 	<br /><br />
	  	 	
	  	 	<a href="update_script.php?action=reset">Reset to run from starting</a>
	  	 	
	  	 	<br /><br />
	  	 	
	  	 	<p>If progressbar stopes, then wait 2 - 5 minutes. Refresh page again and click start button again. It will continue again from the stopped position.</p>
  	 	</center>
  	 </div>
  </body>
</html>