<?php echo $header; ?>
<style type="text/css">
	.mega-menu {display:none;}
</style>
<ul id="breadcrumbs-one">
    <?php 
    $total = count($breadcrumbs); 
    $i=0;
    foreach ($breadcrumbs as $breadcrumb) { 
        $i++;
        if($i==$total)
        {
    ?>
        <li><a class="current"><?php echo $breadcrumb['text']; ?></a></li>
    <?php 
        }else{
    ?>
      	<li><a href="<?php echo $breadcrumb['href']; ?>" rel="external"><?php echo $breadcrumb['text']; ?></a></li>
      <?php }
      } ?>
</ul>
<?php echo $content_top; ?>
<div data-role="content" id="success">
<!--  <h1><?php echo $heading_title; ?></h1>-->
  <?php echo $text_message; ?>
  <a href="<?php echo $continue; ?>" data-role="button" rel="external"><?php echo $button_continue; ?></a>
</div>
<?php echo $content_bottom; ?>
  <!-- Google Code for Conversion Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1020579853;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "JNkQCOP7jAMQjaDT5gM";
var google_conversion_value = 1;
/* ]]> */
</script>
<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/1020579853/?value=1&amp;label=JNkQCOP7jAMQjaDT5gM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<script type="text/javascript"> if (!window.mstag) mstag = {loadTag : function(){},time : (new Date()).getTime()};</script> <script id="mstag_tops" type="text/javascript" src="//flex.msn.com/mstag/site/094b815f-e894-4bb6-b24d-bea6328f5c4e/mstag.js"></script> <script type="text/javascript"> mstag.loadTag("analytics", {dedup:"1",domainId:"1325622",type:"1",revenue:"",actionid:"249384"})</script> <noscript> <iframe src="//flex.msn.com/mstag/tag/094b815f-e894-4bb6-b24d-bea6328f5c4e/analytics.html?dedup=1&domainId=1325622&type=1&revenue=&actionid=249384" frameborder="0" scrolling="no" width="1" height="1" style="visibility:hidden;display:none"> </iframe> </noscript>

<script type="text/javascript"> var sa_values = { "site":12000 }; function saLoadScript(src) { var js = window.document.createElement("script"); js.src = src; js.type = "text/javascript"; document.getElementsByTagName("head")[0].appendChild(js); } var d = new Date(); if (d.getTime() - 172800000 > 1405618272000) saLoadScript("//www.shopperapproved.com/thankyou/rate/12000.js"); else saLoadScript("//direct.shopperapproved.com/thankyou/rate/12000.js?d=" + d.getTime()); </script>
<?php echo $footer; ?>
