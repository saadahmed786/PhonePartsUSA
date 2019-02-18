<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 style="background-image: url('view/image/module.png');"><?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
		<table class="form">
		  <tr>
			<td width="40%"><?php echo $autodetect; ?></td>
			<td><select name="autodetect">
				<?php if ($mobile_autodetect == 'true') { ?>
				<option value="true" selected="selected"><?php echo $text_true; ?></option>
				<?php } else { ?>
				<option value="true"><?php echo $text_true; ?></option>
				<?php } ?>
				<?php if ($mobile_autodetect == 'false') { ?>
				<option value="false" selected="selected"><?php echo $text_false; ?></option>
				<?php } else { ?>
				<option value="false"><?php echo $text_false; ?></option>
				<?php } ?>
			  </select></td>
		  </tr>
		  <tr>
			<td><?php echo $generate_link; ?></td>
			<td><select name="generate_link">
				<?php if ($mobile_generate_link== 'true') { ?>
				<option value="true" selected="selected"><?php echo $text_true; ?></option>
				<?php } else { ?>
				<option value="true"><?php echo $text_true; ?></option>
				<?php } ?>
				<?php if ($mobile_generate_link == 'false') { ?>
				<option value="false" selected="selected"><?php echo $text_false; ?></option>
				<?php } else { ?>
				<option value="false"><?php echo $text_false; ?></option>
				<?php } ?>
			  </select></td>
		  </tr>
		  <tr>
			<td><?php echo $entry_status; ?></td>
			<td><select name="mobile_status">
				<?php if ($mobile_status) { ?>
				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
				<option value="0"><?php echo $text_disabled; ?></option>
				<?php } else { ?>
				<option value="1"><?php echo $text_enabled; ?></option>
				<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
				<?php } ?>
			  </select></td>
		  </tr>
		  <tr>
			<td><?php echo $template_name; ?></td>
			<td><input type="text" name="mobile_template_name" value="<?php if ($mobile_template_name == '') {echo 'mobile';} else {echo $mobile_template_name;} ?>" />
				<br />
				<?php if ($error_template) { ?><span class="error"><?php echo $error_template; ?></span> <?php } ?>		
			</td>
		  </tr>
		</table>
    </form>
  </div>
</div>
<?php echo $footer; ?>