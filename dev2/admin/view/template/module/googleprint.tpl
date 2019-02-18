<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
   <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" ;>

	<div id="settings_tabs" class="htabs clearfix">
			<a href="#emailset"><?php echo 'Auto Print Login & Printer Select'; ?></a>
			<a href="#invset"><?php echo 'General Settings'; ?></a>
			<a href="#invset2"><?php echo 'Invoice Settings'; ?></a>
			<a href="#cache"><?php echo 'Clear Print Cache'; ?></a>
			
			
		</div>
		
		<div id="emailset" class="divtab">
     <br></br>
	<?php if($private_key2) {  ?>
	  <form method="post"  id="form_2" action="unlink2.php">
<input type="image" src="controller/icache/files/click.png" value="Sign Out" name="sub2"/>
 	<script type="text/javascript"> 

 $(function() {
  
  $('input[name=sub2]').click(function(){
    var _data= $('#form_2').serialize() + '&sub2=' + $(this).val();
   
   $.ajax({
    
   type: 'POST',
      url: "unlink2.php?",
      data:_data,
	
      success: function(html){
         $('div#1').html(html);
		 alert('Signed Out! Now remove User name & Password and Click Save!');
		  
      }
    });
    return false;
	 alert('Error!');
  });
});
 </script> 
<?php }

  ?>
	 <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_public_key; ?></td>
          <td><input type="password" input name="public_keyg" value="<?php echo $public_keyg; ?>" size="56"><br />
            <?php if ($error_public_key) { ?>
            <span class="error"><?php echo $error_public_key; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_private_key; ?></td>
          <td><input type="password" input name="private_keyg" value="<?php echo $private_keyg; ?>" size="56"><br />
            <?php if ($error_private_key) { ?>
            <span class="error"><?php echo $error_private_key; ?></span>
            <?php } ?></td>
        </tr>
		
		<tr>
          <td><span class="required">*</span> <?php echo $entry_private_key2; ?></td>
          <td><input type="password" input name="private_key2" value="<?php echo $private_key2; ?>" size="56"><br />
            <?php if ($error_private_key2) { ?>
            <span class="error"><?php echo $error_private_key2; ?></span>
            <?php } ?></td>
        </tr>

		
		<?php		
	if (empty($private_key2) && empty($public_keyg)) 
{	

if (file_exists ('../vqmod/xml/Google Cloud Auto Print Menu.xml')) { unlink ('../vqmod/xml/Google Cloud Auto Print Menu.xml');}
if (file_exists ('../vqmod/xml/Google Cloud Auto Print.xml')) { unlink ('../vqmod/xml/Google Cloud Auto Print.xml');}

?>
<img border="0" src="controller/icache/files/uberprint.jpg" alt="Uber Print" >
<br><b><font color="red">Welcome to Uber Auto Print!</font></b></br>
<br><font color="blue">For Initial Set Up, Please Follow the Instructions Carefully!!!</font></br>

<br><font color="green">1.First Create Google Oauth2 Credentials <a href="https://console.developers.google.com/ " target="_blank">Click Here First</a> </font></br>
<br><font color="green">2. Click Create Project, Name it "AutoPrint" Then Click Create. Please wait until the auth is finished processing the page will redirect automatically.</font></br>
<br><font color="green">3. Next Select APIS & AUTH then Credentials from the left menu. Click Create new Client ID button select Web Application, then click configure and complete data on this wizard.</font></br>
<br><font color="green">4. Ensure The Web Application is selected then additional JavaScript Origins must be your Root Directory  </font> <font color="red"><?php echo "http://".$_SERVER['HTTP_HOST'];?></font><font color="green">   eg. http://www.yoursite.com and not your subdirectory eg.http://www.yoursite.com/store </font></br>
<br><font color="green">5. VERY IMPORTANT!! Authorized redirect URIs must be the following:<br> </font><br> <font color="red"><?php $sefv5 = HTTP_CATALOG."admin/controller/icache/files/tokenr.php"; echo $sefv5; ?>
</font><font color="green"><br>  </font> <font color="red"><?php $sevf5 = HTTP_CATALOG."admin/controller/icache/files/tokenb2.php"; echo $sevf5; ?> </font></br>
<font color="red"><?php $sevf59 = HTTP_CATALOG."/catalog/controller/icache/files/printmachineclient.php"; echo $sevf59; ?> </font></br>
<br><font color="green">6. Next Click Create Client ID and then copy the client id and client secret and paste them into the box below, then press save, when you log back into the admin screen you will now get the option to generate the token.</font></br>

<?php
 }
   
  if(empty($private_key2)){ if($public_keyg) {
$examplezz = HTTP_CATALOG."admin/controller/icache/files/tokenr.php";

$redirect_uri = $examplezz;

$man4 = var_export($public_keyg, true);
$man5 = var_export($private_keyg, true);
$man7 = var_export($examplezz, true);
$fuckyeah1 = "<?php\n\n\$client_id = $man4;\n\n";
$fuckyeah4 = "\n\n\$client_secret = $man5;\n\n";
$fuckyeah6 = "\n\n\$redirect_uri = $man7;\n\n?>";

$fuckyeah3 = $fuckyeah1.$fuckyeah4.$fuckyeah6;
file_put_contents('controller/icache/files/refresh2.php', $fuckyeah3);?>
<img border="0" src="controller/icache/files/uberprint.jpg" alt="Uber Print" >
<br><b><font color="red">Welcome to Uber Auto Print!</font></b></br><br />

<font color="blue"><b>Please click on the link to generate the token in a new window <?php $sefv5555 = HTTP_CATALOG."admin/controller/icache/files/tokenr.php";?><a href="<?php echo $sefv5555;?>" target="_blank">Click Here</a></b></font></br><br />
<font color="blue">Once the token is generated please copy and paste the token in the box below and press save, your printers will be availabe to select after this.</font></br>
 <?php } }
  else{
  ?>
     </form>
 <br></br>
  <?php 
$serveradd = HTTP_CATALOG."catalog/controller/icache/files/printmachineclient.php";
$man4 = var_export($public_keyg, true);
$man5 = var_export($private_keyg, true);
$man6 = var_export($private_key2, true);
$man7 = var_export($serveradd, true);
$fuckyeah1 = "<?php\n\n\$client_id = $man4;\n\n";
$fuckyeah4 = "\n\n\$client_secret = $man5;\n\n";
$fuckyeah6 = "\n\n\$redirect_uri = $man7;\n\n";
$fuckyeah5 = "\n\n\$refresh_tox = $man6;\n\n?>";
$fuckyeah3 = $fuckyeah1.$fuckyeah6.$fuckyeah4.$fuckyeah5;
file_put_contents('controller/icache/files/refresh.php', $fuckyeah3);
file_put_contents('../catalog/controller/icache/files/refresh.php', $fuckyeah3);
  
  require('controller/icache/files/tokenb2.php');?>
   <?php require('controller/icache/files/printeradmin.php');?>
 
 </table>
</div>
	<div id="invset" class="divtab">
  <table class="form">
  
 
	 		   
			   <td><?php echo $entry_savegoogle_drive; ?></td>
            <td><?php if ($savegoogle_drive) { ?>
              <input type="radio" name="savegoogle_drive" value="1" checked="checked" />
              <?php echo "Yes"; ?>
              <input type="radio" name="savegoogle_drive" value="0" />
              <?php echo "No"; ?>
              <?php } else { ?>
              <input type="radio" name="savegoogle_drive" value="1" />
              <?php echo "Yes"; ?>
              <input type="radio" name="savegoogle_drive" value="0" checked="checked" />
              <?php echo "No"; ?>
              <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </tr>
			  
			  <td><?php echo $entry_savegoogle_drive2; ?></td>
            <td><?php if ($savegoogle_drive2) { ?>
              <input type="radio" name="savegoogle_drive2" value="1" checked="checked" />
              <?php echo "Yes"; ?>
              <input type="radio" name="savegoogle_drive2" value="0" />
              <?php echo "No"; ?>
              <?php } else { ?>
              <input type="radio" name="savegoogle_drive2" value="1" />
              <?php echo "Yes"; ?>
              <input type="radio" name="savegoogle_drive2" value="0" checked="checked" />
              <?php echo "No"; ?>
              <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </tr>
			  
			   <tr>
			 <tr>
		    <td><?php echo $entry_custfoot23; ?></td>
            <td><?php if ($custfoot23) { ?>
              <input type="radio" name="custfoot23" value="1" checked="checked" />
              <?php echo "Invoice 2"; ?>
              <input type="radio" name="custfoot23" value="0" />
              <?php echo "Invoice 1"; ?>
              <?php } else { ?>
              <input type="radio" name="custfoot23" value="1" />
              <?php echo "Invoice 2"; ?>
              <input type="radio" name="custfoot23" value="0" checked="checked" />
              <?php echo "Invoice 1"; ?>
              <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td></tr>
			  
			 
  
	  </table></div>  </form>
	  
	  <div id="invset2" class="divtab">
  <table class="form">
	<td colspan="2" bgcolor="#F7F7F7"><b>Invoice1 Settings:- Customise Size & Margins</b><span class="help">(Please enter Units (eg: px, cm, em or %)</span></b></td>
		
		
				<tr><td><?php echo $entry_auto_flogo; ?></td>
           <td> <?php if (empty($auto_flogo)) { $auto_flogo = '40px'; }?>
			<?php if ($auto_flogo) { ?>
            <input name="auto_flogo" value="<?php echo $auto_flogo; ?>" size="5" />
			  
			  <?php } else { ?>
			  <input name="auto_flogo" value="<?php echo $auto_flogo; ?>" size="5" />
			  <?php } ?>
			  </td></tr>
			  <tr>
	 
	 <td><?php echo $entry_auto_width; ?></td>
           <td> <?php if (empty($auto_width)) { $auto_width = '680px'; }?>
			<?php if ($auto_width) { ?>
            <input name="auto_width" value="<?php echo $auto_width; ?>" size="5" />
			  
			  <?php } else { ?>
			  <input name="auto_width" value="<?php echo $auto_width; ?>" size="5" />
			  <?php } ?>
				  
			  <tr><td><?php echo $entry_auto_tfont; ?></td>
           <td> <?php if (empty($auto_tfont)) { $auto_tfont = '12px'; }?>
			<?php if ($auto_tfont) { ?>
            <input name="auto_tfont" value="<?php echo $auto_tfont; ?>" size="5" />
			  
			  <?php } else { ?>
			  <input name="auto_tfont" value="<?php echo $auto_tfont; ?>" size="5" />
			  <?php } ?>
		 
		 <tr><td><?php echo $entry_auto_bfont; ?></td>
           <td> <?php if (empty($auto_bfont)) { $auto_bfont = '12px'; }?>
			<?php if ($auto_bfont) { ?>
            <input name="auto_bfont" value="<?php echo $auto_bfont; ?>" size="5" />
			  
			  <?php } else { ?>
			  <input name="auto_bfont" value="<?php echo $auto_bfont; ?>" size="5" />
			  <?php } ?>
			  
			  <tr><td><?php echo $entry_auto_cfont; ?></td>
           <td> <?php if (empty($auto_cfont)) { $auto_cfont = '12px'; }?>
			<?php if ($auto_cfont) { ?>
            <input name="auto_cfont" value="<?php echo $auto_cfont; ?>" size="5" />
			  
			  <?php } else { ?>
			  <input name="auto_bfont" value="<?php echo $auto_bfont; ?>" size="5" />
			  <?php } ?>
			  
			  <tr><td><?php echo $entry_auto_border; ?></td>
           <td> <?php if (empty($auto_border)) { $auto_border = '1px'; }?>
			<?php if ($auto_border) { ?>
            <input name="auto_border" value="<?php echo $auto_border; ?>" size="5" />
			  
			  <?php } else { ?>
			  <input name="auto_border" value="<?php echo $auto_border; ?>" size="5" />
			  <?php } ?>
			  
			   <tr><td><?php echo $entry_auto_pad; ?></td>
           <td> <?php if (empty($auto_pad)) { $auto_pad = '7px'; }?>
			<?php if ($auto_pad) { ?>
            <input name="auto_pad" value="<?php echo $auto_pad; ?>" size="5" />
			  
			  <?php } else { ?>
			  <input name="auto_pad" value="<?php echo $auto_pad; ?>" size="5" />
			  <?php } ?>
		   
        			  
			  <tr><td><?php echo $entry_auto_margin; ?></td>
           <td> <?php if (empty($auto_margin)) { $auto_margin = '20px'; }?>
			<?php if ($auto_margin) { ?>
            <input name="auto_margin" value="<?php echo $auto_margin; ?>" size="5" />
			  
			  <?php } else { ?>
			  <input name="auto_margin" value="<?php echo $auto_margin; ?>" size="5" />
			  <?php } ?>
			</td></tr>

		<td colspan="2" bgcolor="#F7F7F7"><b>Invoice 2 Settings</b></td>	
		<tr> 
			  <td><?php echo $entry_invname2c2; ?></td>
           <td> <?php if (empty($invname2c2)) { $invname = ''; }?>
			<?php if ($invname2c2) { ?>
            <input name="invname2c2" value="<?php echo $invname2c2; ?>" size="20" />
			  
			  <?php } else { ?>
			  <input name="invname2c2" value="<?php echo $invname2c2; ?>" size="20" />
			  <?php } ?> </td></tr>
			 
			 <tr>
			 


			 <td><?php echo $entry_inv_skuca2; ?></td>
            <td><?php if ($inv_skuca2) { ?>
              <input type="radio" name="inv_skuca2" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="inv_skuca2" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="inv_skuca2" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="inv_skuca2" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td></tr>
			  <tr>
            <td><?php echo $entry_invpicav2; ?></td>
            <td><?php if ($invpicav2) { ?>
              <input type="radio" name="invpicav2" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="invpicav2" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="invpicav2" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="invpicav2" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </td></tr> 
			  <tr>
			 <tr>
		    <td><?php echo $entry_custfoot2; ?></td>
            <td><?php if ($custfoot2) { ?>
              <input type="radio" name="custfoot2" value="1" checked="checked" />
              <?php echo "Enabled"; ?>
              <input type="radio" name="custfoot2" value="0" />
              <?php echo "Disabled"; ?>
              <?php } else { ?>
              <input type="radio" name="custfoot2" value="1" />
              <?php echo "Enabled"; ?>
              <input type="radio" name="custfoot2" value="0" checked="checked" />
              <?php echo "Disabled"; ?>
              <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td></tr>
			  <tr>
			
			  <td><?php echo $entry_custfoot221; ?></td>
               <td><?php if ($custfoot221) { ?>
              <textarea name="custfoot221" cols="52" rows="5"><?php echo $custfoot221; ?></textarea>
			  
			  <?php } else { ?>
			  <textarea name="custfoot221" cols="52" rows="5"><?php echo $custfoot221; ?></textarea>
			  <?php } ?></td></tr>
         
 
 </table></div>  </form>
 <div id="cache" class="divtab">

  <form method="post"  id="form_1" action="unlink.php">
<input type="image" src="controller/icache/files/trash.png" value="Clear Cache" name="sub"/>
 	<script type="text/javascript"> 
  $(function() {
  $('input[name=sub]').click(function(){
    var _data= $('#form_1').serialize() + '&sub=' + $(this).val();
    $.ajax({
      type: 'POST',
      url: "unlink.php?",
      data:_data,
      success: function(html){
         $('div#1').html(html);
		 alert('Print Cache Cleared!');
      }
    });
    return false;
	 alert('Cache Not Cleared!');
  });
});
 </script> 
 <br></br>
 <br> This can be set up via cron job reccomend clearing cache at least once a week</br>
 <br> Example Cron Job : /usr/bin/php -q /home/**username**/public_html/**yourwebsite.com**/admin/unlink.php >/dev/null 2>&1 </br>
  </div>
</div>
	<script type="text/javascript">

	$('#settings_tabs a').tabs();

</script>
<?php
}

?>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>

<script type="text/javascript">
	CKEDITOR.replace('custfoot221', {
		filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
		filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
		filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
		filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
		filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
	});  
</script>