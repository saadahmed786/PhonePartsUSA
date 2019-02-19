<?php echo $header; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php //echo $content_top; ?>
  <h1><?php echo $heading_title; ?></h1>
  <?php echo $text_message; ?>
  <div class="buttons">
    <div class="left"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
  </div>
  <?php echo $content_bottom; ?></div>
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

<span id="_GUARANTEE_GuaranteeSpan"></span>
<script type="text/javascript">
if(window._GUARANTEE && _GUARANTEE.Loaded) {

_GUARANTEE.Guarantee.order = "<?php echo $Norton_Order;?>";
_GUARANTEE.Guarantee.subtotal = "<?php echo $Norton_Total;?>";
_GUARANTEE.Guarantee.email = "<?php echo $Norton_Email;?>";
_GUARANTEE.WriteGuarantee("JavaScript", "_GUARANTEE_GuaranteeSpan");
}
</script>
<img src='https://www.bizrate.com/roi/index.xpml?mid=293739&cust_type=<?php echo $Customer_Status;?>&order_id=<?php echo $Norton_Order;?>&order_value=<?php echo $Norton_Total;?>&units_ordered=<?php echo $Total_Units;?>' />

 <?php if(isset($orderDetails)) { ?>            
            <!-- START Google Trusted Stores Order -->
			<div id="gts-order" style="display:none;" translate="no">
			<!-- start order and merchant information -->
			  <span id="gts-o-id"><?php echo $orderDetails['order_id']; ?></span>
			  <span id="gts-o-domain"><?php echo $this->config->get('config_domain');?></span>
			  <span id="gts-o-email"><?php echo html_entity_decode($orderDetails['email'], ENT_QUOTES, 'UTF-8'); ?></span>
			  <span id="gts-o-country"><?php echo $orderDetails['payment_iso_code_2']; ?></span>
			  <span id="gts-o-currency"><?php echo $orderDetails['currency_code']; ?></span>
			  <span id="gts-o-total"><?php echo number_format($orderDetails['total'],2); ?></span>
			  <span id="gts-o-discounts">0</span>
			  <span id="gts-o-shipping-total"><?php echo $orderDetails['shipping_total']; ?></span>
			  <span id="gts-o-tax-total"><?php
              if($orderDetails['order_tax']=='') $orderDetails['order_tax']=0.00;
              
               echo number_format($orderDetails['order_tax'],2); ?></span>
			  <span id="gts-o-est-ship-date"><?php echo $ship_date; ?></span>
			  <span id="gts-o-est-delivery-date"><?php echo $deliv_date; ?></span>
			  <span id="gts-o-has-preorder">N</span>
			  <span id="gts-o-has-digital"><?php echo $this->config->get('config_has_digital_goods');?></span>
			  <!-- end order and merchant information -->

			<!-- start repeated item specific information -->
			<!-- item example: this area repeated for each item in the order -->
			<?php if(isset($orderProduct)) { ?> 
            <?php foreach($orderProduct as $product) { ?>
			<span class="gts-item">
				<span class="gts-i-name"><?php echo html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8'); ?></span>
				<span class="gts-i-price"><?php echo number_format($product['price'],2); ?></span>
				<span class="gts-i-quantity"><?php echo $product['quantity']; ?></span>
				<span class="gts-i-prodsearch-id"><?php echo $product['product_id']; ?></span>
				<?php if (utf8_strlen($this->config->get('config_google_shopping_account_id')) > 0) { ?>
				<span class="gts-i-prodsearch-store-id"><?php echo $this->config->get('config_google_shopping_account_id');?></span>
				<span class="gts-i-prodsearch-country"><?php echo $this->config->get('config_google_shopping_country');?></span>
				<span class="gts-i-prodsearch-language"><?php echo $this->config->get('config_google_shopping_language');?></span>
				<?php } ?>
			</span>
			<?php } ?>
 			<?php } ?>  
			<!-- end item 1 example -->
			<!-- end repeated item specific information -->

			</div>
			<!-- END Google Trusted Stores Order -->
            <?php } ?>  

<?php echo $footer; ?>