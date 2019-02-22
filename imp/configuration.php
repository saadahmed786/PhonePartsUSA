<?php

include_once 'auth.php';

if(isset($_REQUEST['submitebay'])){
	foreach($_REQUEST['ebay'] as $config){
		$key = array_keys($config);
		$key = $key[0];
		$value =  $config[$key];
		$result = $db->func_query("Select * from configuration where config_key = '$key' ");
		if($result){
			$db->func_query("Update configuration set config_value = '$value' where config_key = '$key' ");
		}
		else{
			$db->func_array2insert("configuration", array('config_key'=>$key,'config_value'=>$value));
		}
	}

	$_REQUEST['user'] = $db->func_escape_string($_REQUEST['user']);
	$user = $_REQUEST['user'];
	
	$rst  = $db->func_query("Select * from ebay_credential where username = '$user' ");
	if(!$rst){
		$db->func_array2insert("ebay_credential", array('username'=>$_REQUEST['user'],'expire_date'=>'','generate_date'=>date('Y-m-d H:i:s'),'dateofmodifications'=>date('Y-m-d H:i:s')));
	}
	$_SESSION['message'] = "Ebay configuration keys has been saved";
}

elseif(isset($_REQUEST['submitamazon'])){
	foreach($_REQUEST['amazon'] as $config){
		$key = array_keys($config);
		$key = $key[0];
		$value =  $config[$key];
		$result = $db->func_query("Select * from configuration where config_key = '$key' ");
		if($result){
			$db->func_query("Update configuration set config_value = '$value' where config_key = '$key' ");
		}
		else{
			$db->func_array2insert("configuration", array('config_key'=>$key,'config_value'=>$value));
		}
	}

	$mer_id= $_REQUEST['mer_id'];
	$mkt_id= $_REQUEST['mkt_id'];
	$rst = $db->func_query("Select * from amazon_credential where merchant_id = '$mer_id' AND market_place_id = '$mkt_id'");
	if(!$rst){
		$db->func_array2insert("amazon_credential", array('merchant_id'=>$_REQUEST['mer_id'],'market_place_id'=>$_REQUEST['mkt_id'],'dateofmodifications'=>date('Y-m-d H:i:s')));
	}
	
	$_SESSION['message'] = "Amazon configuration keys has been saved";
}

elseif(isset($_REQUEST['submitfishbowl'])){
	foreach($_REQUEST['fishbowl'] as $config){
		$key = array_keys($config);
		$key = $key[0];
		$value =  $config[$key];
		$result = $db->func_query("Select * from configuration where config_key = '$key' ");
		if($result){
			$db->func_query("Update configuration set config_value = '$value' where config_key = '$key' ");
		}
		else{
			$db->func_array2insert("configuration", array('config_key'=>$key,'config_value'=>$value));
		}
	}

	$_REQUEST['user'] = $db->func_escape_string($_REQUEST['user']);
	$_REQUEST['password'] = $db->func_escape_string($_REQUEST['password']);
	$rst = $db->func_query("Select * from fishbowl_credential where username = '{$_REQUEST['user']}'");
	if(!$rst){
		$db->func_array2insert("fishbowl_credential", array('username'=>$_REQUEST['user'],'password'=>$_REQUEST['password'],'dateofmodifications'=>date('Y-m-d H:i:s')));
	}
	else{
		$db->func_array2update("fishbowl_credential", array('password'=>$_REQUEST['password'],'dateofmodifications'=>date('Y-m-d H:i:s')) , " username = '{$_REQUEST['user']}'");
	}
	
	
	$_SESSION['message'] = "Fishbowl configuration keys has been saved";
}


$configExist = $db->func_query("Select config_key, config_value from configuration","config_key");
$amazon_details = $db->func_query_first("select * from amazon_credential limit 1");
$ebay_details   = $db->func_query_first("select * from  ebay_credential limit 1");
$fish_details   = $db->func_query_first("select * from  fishbowl_credential limit 1");

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Configuration</title>
		<script  type="text/javascript" src="include/jquery-latest.js"></script>
		<script  type="text/javascript" src="include/jquery.validate.js"></script>
		<script type="text/javascript">
    			jQuery(document).ready(function(){
        		jQuery("#ebay").validate();
        		jQuery("#amazon").validate();
        		jQuery("#fishbowl").validate();
    		});
		</script>		
	</head>
    <body>
		<?php include_once 'inc/header.php';?>
	
		<?php if(@$_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
		<?php endif;?>
	
 	  <form name="ebay" action="" method="post" id="ebay">
  		<table width="80%" cellpadding="10" style="border:1px solid #585858;" align="center">
		    <tbody><tr>
		       <td align="center" style="padding-right:20%">
		           Ebay Configuration 
		       </td>
		    </tr>
		    <tr>
		       <td>
		         <table cellspacing="2px" cellpadding="2px" border="0" style="margin-left:10%">
		            <tr>
				       <td style="padding-right:155px">
					      <label>EBAY_APP_ID :</label>
					   </td> 
					  <td>
	                      <input id="EBAY_APP_ID" type="text" name="ebay[0][EBAY_APP_ID]" value="<?php echo  @$configExist['EBAY_APP_ID']['config_value']?>" , class="required" style="width:400px;">
	                      <br />
	                      <label class="error" for="EBAY_APP_ID" generated="true" style="color:red;"> </label> 
	                  </td>	
			  		</tr>
			  		
				  	<tr>
					   <td>
					 	   <label>EBAY_DEV_ID :</label>
					   </td>
					   <td>
	                       <input id="EBAY_DEV_ID" type="text" value="<?php echo  @$configExist['EBAY_DEV_ID']['config_value']?>" name="ebay[1][EBAY_DEV_ID]" class="required" style="width:400px;"><br />
	                       <label class="error" for="EBAY_DEV_ID" generated="true" style="color:red;"> </label>
	                   </td>
				  	</tr>
				  	
			  		<tr>
				      <td>
					    <label>EBAY_CERT_ID :</label>
					  </td>
					  <td>
	                      <input id="EBAY_CERT_ID" type="text" value="<?php echo  @$configExist['EBAY_CERT_ID']['config_value']?>" name="ebay[2][EBAY_CERT_ID]" class="required" style="width:400px;">
	                      <br />
	                      <label class="error" for="EBAY_CERT_ID" generated="true" style="color:red;"> </label> 
	                  </td>
				  	</tr>
				  	
			  	   <tr>
			           <td>
				          <label>USER_TOKEN :</label>
				       </td>
				       <td>
                           <textarea id="USER_TOKEN" rows="2" cols="16" value="<?php echo  @$configExist['USER_TOKEN']['config_value']?>" name="ebay[3][USER_TOKEN]" class="required" style="width:400px;"><?php echo  @$configExist['USER_TOKEN']['config_value']?></textarea> <br />
                           <label class="error" for="USER_TOKEN" generated="true" style="color:red;"> </label> 
                       </td>
			      </tr>
			      
			      <tr>
			           <td>
				          <label>eBay Max Qty :</label>
				       </td>
				       <td>
				       	   <input id="EBAY_MAX_QTY" type="text" value="<?php echo  @$configExist['EBAY_MAX_QTY']['config_value']?>" name="ebay[4][EBAY_MAX_QTY]" class="required" style="width:400px;"><br />
                           <label class="error" for="EBAY_MAX_QTY" generated="true" style="color:red;"> </label> 
                       </td>
			      </tr>
			      
			      <tr>
				     <td>
					    <label>User Name :</label>
					  </td>
					  <td>
	                      <input id="user" type="text" value="<?php echo $ebay_details['username']?>"  name="user" style="width:400px;">
	                  </td>	
			  	 </tr>
				 <tr>
		  		     <td align="center" colspan="2">
		  		       <input type="submit" value="Submit"  name="submitebay">
		  		     </td>
		  		 </tr>
	  		 </table>
	      </td>	  
	   </tr>
  </table> 
 </form>
 
 
 <form name="amazon" action="" method="post" id="amazon" >
   <table width="80%" cellpadding="10" style="border:1px solid #585858;" align="center">
	    <tbody><tr>
	       <td align="center" style="padding-right:20%">
	           Amazon Configuration 
	       </td>
	    </tr>
	    <tr>
	       <td>
	        <table cellspacing="2px" cellpadding="2px" border="0" style="margin-left:10%">
	            <tr>
			     <td style="padding-right:18px">
				    <label>AWS_ACCESS_KEY_ID :</label>
				  </td>
				  <td>
                      <input id="AWS_ACCESS_KEY_ID" type="text" value="<?php echo  @$configExist['AWS_ACCESS_KEY_ID']['config_value']?>" name="amazon[0][AWS_ACCESS_KEY_ID]" class="required" style="width:400px;"> <br />
                      <label class="error" for="AWS_ACCESS_KEY_ID" generated="true" style="color:red;"> </label> 
                  </td>
				</tr>
				
			    <tr>
			      <td style="padding-right:50px">
				    <label>AWS_SECRET_ACCESS_KEY :</label>
				  </td>
				  <td>
                      <input id="AWS_SECRET_ACCESS_KEY" type="text" value="<?php echo  @$configExist['AWS_SECRET_ACCESS_KEY']['config_value']?>" name="amazon[1][AWS_SECRET_ACCESS_KEY]" class="required" style="width:400px;"> <br />
                      <label class="error" for="AWS_SECRET_ACCESS_KEY" generated="true" style="color:red;"> </label> 
                  </td>
			  	</tr>
			  	
			    <tr>
			      <td style="padding-right:50px">
				    <label>Merchant ID :</label>
				  </td>
				  <td>
                      <input id="mer_id" value="<?php echo $amazon_details['merchant_id']?>" type="text"  name="mer_id" class="required" style="width:400px;"> <br />
                      <label class="error" for="mer_id" generated="true" style="color:red;"> </label> 
                  </td>
			  	</tr>
			  	
			    <tr>
	  			  <td>
				    <label>MarketPlace ID:</label>
				  </td>
				  <td>
                      <input id="mkt_id" type="text" value="<?php echo $amazon_details['market_place_id']?>" name="mkt_id" class="required" style="width:400px;">
                  </td>		       
                  <td> <label class="error" for="mkt_id" generated="true" style="color:red;"> </label> </td> 
	  		   </tr>
	  		   
	  		   <tr>
	  		     <td align="center" colspan="2">
	  		       <input type="submit" value="Submit"  name="submitamazon">
	  		     </td>
	  		   </tr>
	  		 </table>
	      </td>	  
	   </tr>
  </table> 
 </form>
 
 
 <form name="fishbowl" action="" method="post" id="fishbowl">
    <table width="80%" cellpadding="10" style="border:1px solid #585858;" align="center">
	    <tbody>
		    <tr>
		       <td align="center" style="padding-right:20%">
		           Fishbowl Configuration 
		       </td>
		    </tr>
		    <tr>
	      	 <td>
	         	<table cellspacing="2px" cellpadding="2px" border="0" style="margin-left:10%">
		            <tr>
				      <td style="padding-right:155px;" >
					    FB_APP_KEY :
					  </td>
					  <td>
	                      <input id="FB_APP_KEY" type="text" value="<?php echo  @$configExist['FB_APP_KEY']['config_value']?>" name="fishbowl[0][FB_APP_KEY]" class="required" style="width:400px;"> <br />
	                      <label class="error" for="FB_APP_KEY" generated="true" style="color:red;"> </label> 
	                  </td>
				  	</tr>
				  	
				  	<tr>
					     <td>
						    <label>User Name :</label>
						 </td>
						 <td>
		                     <input id="user" type="text" value="<?php echo $fish_details['username']?>"  name="user" class="required" style="width:400px;">
		                 </td>
		                 <td> <br /><label class="error" for="user" generated="true" style="color:red;"> </label> </td>	
				  	</tr>
				  	
				  	<tr>
					     <td>
						    <label>Password :</label>
						 </td>
						 <td>
		                     <input id="password" type="text" value="<?php echo $fish_details['password']?>"  name="password" class="required" style="width:400px;">
		                 </td>
		                 <td> <br /><label class="error" for="password" generated="true" style="color:red;"> </label> </td>	
				  	</tr>
				  	
		  		    <tr>
		  		       <td align="center" colspan="2">
		  		       <input type="submit" value="Submit"  name="submitfishbowl" class="required">	  		       
		  		     </td>
		  		   </tr>
	  		 </table>
	      </td>	  
	   </tr>
  </table> 
 </form>
 
 </body>
</html>                                                                      