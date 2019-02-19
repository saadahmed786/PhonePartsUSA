
			<img src="image/data/ga.png" style="display:none" onload="_gaq.push(['_trackPageview','/checkout/shippingmethod']);">
            
<?php if ($error_warning) { ?>
<div class="warning">Please provide the Essential Shipping Details.</div>
<?php } ?>
<!-- <div class="warning">We advise customers to select Fedex shipping methods to avoid any delivery delays during holidays.</div>
-->
<?php //echo $code;exit; ?>
<?php if ($shipping_methods) { ?>

<div class="ship-box-table text-sm-center">

  <div class="row">
    <div class="col-md-5">
      Shipping Method
    </div>
    <div class="col-md-5 col-xs-6">
      ETA (Business Days)
    </div>
    <div class="col-md-2 col-xs-6">
      
    </div>
  </div>

  <div class="row hidden-md hidden-lg text-sm-center">
<div class="col-xs-12 col-md-12"><h3 style="margin-top:0px;margin-bottom: 0px">Shipping Methods</h3><span class="fontsize13">Next Day Shipment Cut-Off 4:00PM PST</span></div>
</div>

  <div class="row hidden">
    <div class="col-md-5 pr0">
      <!-- <input type="checkbox" <?= ($sign_product_exist) ? 'checked="checked"': '';?> name="signProduct" id="addSign" class="css-checkbox"> 
      <label for="addSign" class="css-checkbox">Require Signature</label>

     -->

    </div>
 <!--    <div class="col-md-5 pr0">
    </div> -->
    <!-- <div class="col-md-2 col-xs-6">
      $3.00
    </div> -->
  </div>
  <?php foreach ($shipping_methods as $shipping_method) { ?>
  <?php if (!$shipping_method['error']) { ?>
  <?php foreach ($shipping_method['quote'] as $quote) { ?>

  <?php
  if($quote['code']=='multiflatrate.multiflatrate_0')

            {

              $quote['title'] ='Las Vegas Store Pick Up';
            }
           /* if($quote['code']=='multiflatrate.multiflatrate_4')
            {
              $quote['title'] ='Fedex Next Business Day';
            }*/

            if($quote['code']=='multiflatrate.multiflatrate_4')
            {
              $quote['title'] ='Fedex Next Day Saturday';
            } 
            if($quote['code']=='multiflatrate.multiflatrate_19')
            {
            $quote['title'] = 'Fedex Next Business Day';
            }
            $quote['title'] = str_replace("(Ships 4:00 pm PST)","",$quote['title']);
            $quote['title'] = trim($quote['title']);

  ?>
  <div class="row">
    <div class="col-md-5 pr0 col-xs-12">
      <?php if ($quote['code'] == $code || !$code) { ?>
      <?php $code = $quote['code']; ?>
      <input type="radio" class="css-radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
      <?php } else { ?>
      <input type="radio" class="css-radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
      <?php } ?>
      <label for="<?php echo $quote['code']; ?>" class="css-radio"><?php echo $quote['title']; ?><span class="hidden-md hidden-lg"> <?php echo $quote['text'];?></span></label>
    </div>
    <div class="col-md-5 pr0 col-xs-12 fontsize13">
      <?php echo $quote['delivery_time']; ?>
    </div>
   
    <div class="col-md-2 col-xs-6 hidden-xs">
      <?php echo $quote['text']; ?>
    </div>
  </div>
  <?php } ?>
  <?php } else { ?>
  <div class="row">
  <div class="col-md-12 error">
    <?php echo $shipping_method['error']; ?>
  </div>
  </div>
  <?php } ?>
  <?php } ?>
  <?php } ?>
  
  <div class="row">
  <div class="col-md-12">
  <p class="fontsize13" style="line-height: 18px;margin-top:10px;margin-bottom:10px">Due to winter weather, and the high volume of holiday shipping by the carriers, please be aware there may be unavoidable seasonal shipping delays. We apologize for any inconvenience, and we appreciate your understanding throughout the holiday season.</p>
  </div>
  <div class="col-md-12 error">
    <label for="comment" style="font-weight: bold">Order Comments</label>
  </div>
  <div class="col-md-12 error">
    <textarea name="comment" rows="8" style="width: 98%;color:#383838;"></textarea>
  </div>
  </div>
  <!-- <span class="line full"></span> -->
  
</div>
<script type="text/javascript">
  
  $(document).ready(function(){
   // alert('here');
updateShippingMethodx($('input[name=shipping_method]:checked'));
  });
  
</script>