<?php if ($this->config->get('paypal_express_new_status')) { ?>

<?php if ($oc_version == 'v14x') { ?>

<div id="module_paypal_express" class="box">
  <div class="top"><img src="catalog/view/theme/default/image/icon_paypal.png" alt="" /><?php echo $heading_title; ?></div>
  <div id="information" class="middle">
    <a id="ppx" href="<?php echo $href; ?>"><img src="catalog/view/theme/default/image/EC-button.gif" alt="Paypal Express" /></a>
  </div>
  <div class="bottom">&nbsp;</div>
</div>

<?php } else { //v151x ?>

<div class="box">
  <?php if ($wrapper) { ?>
  
  <div class="box-content" style="text-align:left;">
  <?php } else { ?>
  <div class="nowrap" style="text-align:center;">
  <?php } ?>
  <a id="ppx" href="<?php echo $href; ?>"><img src="catalog/view/theme/default/image/EC-button.gif" alt="Paypal Express" /></a>
  </div>
</div>
<?php } ?>


<?php } ?>