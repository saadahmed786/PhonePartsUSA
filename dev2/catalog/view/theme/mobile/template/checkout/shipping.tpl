<?php echo $header; ?>
<div class="box">
  <div class="top">
    <h1><?php echo $heading_title; ?></h1>
  </div>
  <div class="middle">
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="shipping">
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_shipping_address; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <table>
          <tr>
            <td valign="top"><?php echo $text_shipping_to; ?><br />
              <br />
              <div style="text-align: center;"><a onclick="location='<?php echo $change_address; ?>'" class="button"><span><?php echo $button_change_address; ?></span></a></div></td>
           </tr>
           <tr>
            <td valign="top"><b><?php echo $text_shipping_address; ?></b><br />
              <?php echo $address; ?></td>
          </tr>
        </table>
      </div>
     <?php if ($shipping_methods) { ?>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_shipping_method; ?></b>
      <div class="content">
        <p><?php echo $text_shipping_methods; ?></p>
        <table cellpadding="3">
          <?php foreach ($shipping_methods as $shipping_method) { ?>
          <tr>
            <td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
          </tr>
          <?php if (!$shipping_method['error']) { ?>
          <?php foreach ($shipping_method['quote'] as $quote) { ?>
          <tr>
            <td width="1"><label for="<?php echo $quote['id']; ?>">
                <?php if ($quote['id'] == $shipping || !$shipping) { ?>
				<?php $shipping = $quote['id']; ?>
                <input type="radio" name="shipping_method" value="<?php echo $quote['id']; ?>" id="<?php echo $quote['id']; ?>" checked="checked" style="margin: 0px;" />
                <?php } else { ?>
                <input type="radio" name="shipping_method" value="<?php echo $quote['id']; ?>" id="<?php echo $quote['id']; ?>" style="margin: 0px;" />
                <?php } ?>
              </label></td>
            <td width="534"><label for="<?php echo $quote['id']; ?>" style="cursor: pointer;"><?php echo $quote['title']; ?></label></td>
            <td width="1" align="right"><label for="<?php echo $quote['id']; ?>" style="cursor: pointer;"><?php echo $quote['text']; ?></label></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
          </tr>
          <?php } ?>
          <?php } ?>
        </table>
      </div>
      <?php } ?>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_comments; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <textarea name="comment" rows="8" style="width: 99%;"><?php echo $comment; ?></textarea>
      </div>
      <div class="buttons">
        <table>
          <tr>
            <td align="left"><a href="<?php echo $back; ?>" class="button"><span><?php echo $button_back; ?></span></a></td>
            <td align="right"><input type="submit" value="<?php echo $button_continue; ?>" /></td>
          </tr>
        </table>
      </div>
    </form>
  </div>
 </div>
<?php echo $column_left; ?><?php echo $column_right; ?>
 <div class="bottom">&nbsp;</div>
<?php echo $footer; ?> 