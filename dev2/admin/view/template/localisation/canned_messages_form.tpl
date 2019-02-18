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
      <h1><img src="view/image/order.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_title; ?></td>
            <td>
              <input type="text" name="title" value="<?php echo empty($title) ? '' : $title; ?>" />
<?php if ($error_title) { ?>
			  <span class="error"><?php echo $error_title; ?></span>
<?php } ?>
			</td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_message; ?></td>
            <td>
              <textarea cols="100" rows="15" name="message" id="message"><?php echo empty($message) ? '' : $message; ?></textarea>
<?php if ($error_message) { ?>
			  <span class="error"><?php echo $error_message; ?></span>
<?php } ?>
			</td>
          </tr>
<?php if($order): ?>
          <tr>
		  	<td><?php echo $entry_tags; ?></td>
		  	<td>
<?php foreach($order as $k => $v): ?>
			  <a class="tag" title="<?php echo $v; ?>" data-tag="{{<?php echo $k; ?>}}"><?php echo $k; ?></a>
<?php endforeach; ?>
			</td>
		  </tr>
<?php endif; ?>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>