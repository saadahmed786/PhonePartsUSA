<?php
/*
  osapi.tpl

  OneSaas Connect API 2.0.6.35 for OpenCart v1.5.4.1
  http://www.onesaas.com

  Copyright (c) 2012 oneSaas

  1.0.6.2	- Show version in admin UI
  		  
*/
?>

<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title . ' (v. 2.0.6.35)'; ?></h1>
    </div>
    <div class="content">
      <p>Please copy the following Configuration Key into <a href="" title="OneSaas">OneSaas</a> configuration to get connected</p>
      <textarea cols="200" rows="4" onclick="this.focus();this.select()" readonly><?php echo $configkey ?></textarea>
    </div>
  </div>
</div>
<?php echo $footer; ?>
