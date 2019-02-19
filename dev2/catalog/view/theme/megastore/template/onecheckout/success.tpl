<?php echo $header; ?>
<?php $grid = 12; if($column_left != '') { $grid = $grid-3; } if($column_right != '') { $grid = $grid-3; } ?>

<div class="page-title">

	<div class="set-size">
	
		<div class="grid-12">
		
		  <div class="breadcrumb">
		    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
		    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		    <?php } ?>
		  </div>
		  <h3><?php echo $heading_title; ?></h3>
	  
		</div>
	
	</div>
	
	<p class="border"></p>

</div>

<div id="content" class="set-size">

	<?php echo $content_top; ?>

	  <?php if($column_left != '') { echo '<div class="grid-3 float-left">'.$column_left.'</div>'; } ?>
	  
	  <div class="grid-<?php echo $grid; ?> float-left">
	  
	  	<p class="clear" style="height:20px;"></p>
  <?php echo $text_message; ?>
<?php if ($onecheckout_survey_status && $this->config->get('onecheckout_survey_position')=='1') { ?>
<div class="cart-module">
  <div class="cart-heading active"><?php echo $survey_heading_title; ?></div>
  <div class="cart-content" style="display:block;">
    <select name="onecheckout_surver" onchange="$.post('index.php?route=onecheckout/confirm/insertsurver&order_id=<?php echo $order_id; ?>',$('select[name=\'onecheckout_surver\']'));">
	<option value=""><?php echo $text_survey; ?></option>
   	<?php foreach ($survey_options as $option) { ?>
   	<?php if ($onecheckout_survey_option == $option) { ?>
    <option value="<?php echo $option; ?>" selected="selected"><?php echo $option; ?></option>
    <?php } else { ?>
    <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
    <?php } ?>
    <?php } ?>
	</select>
  </div>
</div>
<style type="text/css">
.cart-module > div {
	display: block;
}
.cart-module .cart-heading {
	border: 1px solid #DBDEE1;
	padding: 8px 8px 8px 22px;
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 13px;
	color: #555555;
	margin-bottom: 15px;
	cursor: pointer;
	background: #F8F8F8 url('../image/cart-right.png') 10px 50% no-repeat;
}
.cart-module .active {
	background: #F8F8F8 url('../image/cart-down.png') 7px 50% no-repeat;
}
.cart-module .cart-content {
	padding: 0px 0px 15px 0px;
	display: none;
	overflow: auto;
	font-family: Arial, Helvetica, sans-serif;
	font-size:12px;
}
</style>
<script type="text/javascript"><!--
$('.cart-module .cart-heading').bind('click', function() {
	if ($(this).hasClass('active')) {
		$(this).removeClass('active');
	} else {
		$(this).addClass('active');
	}
		
	$(this).parent().find('.cart-content').slideToggle('slow');
});
//--></script>
<?php } ?>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_continue; ?></span></a></div>
  </div>
  </div>
	  
	  <?php if($column_right != '') { echo '<div class="grid-3 float-left">'.$column_right.'</div>'; } ?>

	<?php echo $content_bottom; ?>

</div>

<?php echo $footer; ?>
 