<?php echo $header; ?>

  <!-- ---------------------- -->
  <!--     I N T R O          -->
  <!-- ---------------------- -->
  <div id="intro">
    <div id="intro_wrap">
      <div class="container_12">
        <h1><?php echo $heading_title; ?></h1>
      </div>
    </div>
  </div>
  <!-- end of intro -->

  <!-- ---------------------- -->
  <!--      C O N T E N T     -->
  <!-- ---------------------- -->
  <div id="content" class="container_16">

    <div id="success_message" class="grid_16">

      <div class="s_mb_20"><?php echo $text_message; ?></div>
<?php if ($onecheckout_survey_status && $this->config->get('onecheckout_survey_position')=='1') { ?>
<div class="s_accordion s_mb_30">
  <div id="apply_coupon" class="s_box_1 grid_6 left alpha"><strong><?php echo $survey_heading_title; ?>:</strong>
    <div class="s_row_3 clearfix">
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
</div>
<?php } ?>
      <span class="clear s_mb_25"></span>

      <div class="s_submit s_mb_30 clearfix">
        <a class="s_button_1 s_main_color_bgr" href="<?php echo $continue; ?>"><span class="s_text"><?php echo $button_continue; ?></span></a>
      </div>
      
    </div>

  </div>
  <!-- end of CONTENT -->
  
<?php echo $footer; ?> 