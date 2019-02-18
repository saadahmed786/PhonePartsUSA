<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 style="background-image: url('view/image/feed.png');"><?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
	
<fieldset>
<legend><?php echo $field_pluginkey ?></legend>
<table  width="100%" cellspacing="0" cellpadding="2" border="0" class="adminlist">

  <tr>
          <td  width="230"><?php echo $entry_status; ?></td>
          <td><select name="fb_browse_status">
              <?php if ($fb_browse_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>

 <tr class="row0">
  <td width="230"><label for="BSS_PLUGIN_KEY"><?php echo $field_pluginkey_description ?></td>
  <td>
   <input type="text" name="BSS_PLUGIN_KEY" id="BSS_PLUGIN_KEY" maxlength="" value="<?php echo $BSS_PLUGIN_KEY;?>" />
  </td>
 </tr>
</table>
</fieldset>
   <br/>
<fieldset>
<legend><?php echo $text_preview_section ?></legend>

	   <a href="<?php echo $preview_link; ?>" target="_blank"><?php echo $text_preview_link ?></a>
</fieldset>
	<br/>  
	
<fieldset>  
	  <h2>Guide to installing the Facebook App</h2>

<h3>
	 How to install Facebook App:</h3>
	<ol>
		<li>
			&nbsp;Go to the App page: <a href="http://www.facebook.com/apps/application.php?id=136002413133082" target="_blank">http://www.facebook.com/apps/application.php?id=136002413133082</a></li>
		<li>
			&nbsp;Select &#39;Add to my page&#39; on the left-hand menu.</li>
		<li>
			&nbsp;On the dialog, click on the &#39;Add to page&#39; button next to the &#39;Page&#39; to which you wish to add the App.</li>
		<li>
			&nbsp;Click the &#39;Close&#39; button once you have added it to all the pages you want.</li>
		<li>
			&nbsp;Go to your &#39;Page&#39; (the one that you have installed our App on)</li>
		<li>
			&nbsp;The &#39;Online Shop&#39; app should now be visible on your page (you can change the name of this later, if you want)</li>
		<li>
			&nbsp;Click to view the &#39;Online Shop&#39; App.</li>
		<li>
			&nbsp;You will now see the sign-up page for our Catalog Browser App.</li>
		<li>
			&nbsp;Choose the price-plan that meets your page requirements, by clicking the &#39;Sign up&#39; button.</li>
		<li>
			&nbsp;&#39;Allow&#39; the permissions on the popup dialog. This will create an account with Bright Software Solutions, allowing you to customise and manage your Facebook Product Catalog Browser.</li>
	</ol>
	Now you have installed all the components required. All that&#39;s left, is to complete the setup of the Product Catalog Browser!
	<h3>
	 How to set up your Catalog Browser:</h3>
	<ol>
		<li>
			If not already on it, go to the &#39;Online Shop&#39; App on your page (this should be visible on the left hand menu on your Facebook page).</li>
		<li>
			Enter the details for your shop:
			<ul>
				<li>
					Shop Name</li>
				<li>
					Shop Website</li>
			</ul>
		</li>
		<li>
			Enter your &#39;Shop Plugin Key&#39;,(Displayed Above) and then click on &#39;Save Settings&#39;. The &#39;Shop Plugin Key&#39; is the 8-digit code that was created, when you installed our Catalog-creator onto your online shop. If you have not installed our Catalog-creator onto your online shop yet, please refer to &#39;How to install Catalog Creator&#39; before proceeding.</li>
		<li>
			Optional Settings
			<ul>
				<li>
					Banner (Banner image location, either load up, or point to a location on the internet)</li>
				<li>
					Display Special Products Category - option to display &#39;specials&#39;.</li>
				<li>
					Display Featured Products Category - option to display &#39;featured&#39; products from your store.</li>
				<li>
					Display New Products Category - option to display the newest products from your store.</li>
				<li>
					Default Page - set which page of the Catalog Browser you want to display as default (e.g. &#39;Specials&#39;).</li>
					<li>
					Share Setting - Encourage viral marketing by making it easy for your customers to share a product with friends and followers via the Facebook and Twitter share icons.</li>
	
			</ul>
		</li>
		<li>
			Click &quot;Save&quot; and then click &quot;Leave Admin Mode&quot; to see what your shop looks like!</li>
	</ol>
	
	<p>For more information and tips please check out our <a href="http://www.brightsoftwaresolutions.com/fb-shop-browser/fb-faq">F.A.Q</a> or if you need a bit of help, please feel free to get in touch at: <a href="http://support.brightsoftwaresolutions.com">support.brightsoftwaresolutions.com</a>.</p> 

	  
</fieldset>
	  
<p class="copyright"><?php echo $copyright;?></p>
    </form>
  </div>
</div>
<?php echo $footer; ?>