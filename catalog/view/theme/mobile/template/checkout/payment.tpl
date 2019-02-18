<?php echo $header; ?>
<div class="box">
  <div class="top">
    <h1><?php echo $heading_title; ?></h1>
  </div>
  <div class="middle">
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="payment">
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_payment_address; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px; display: inline-block;">
        <table>
          <tr>
            <td valign="top"><?php echo $text_payment_to; ?><br />
              <br />
              <div style="text-align: center;"><a href="<?php echo $change_address; ?>" class="button"><span><?php echo $button_change_address; ?></span></a></div></td>
           </tr>
           <tr>
            <td valign="top"><b><?php echo $text_payment_address; ?></b><br />
              <?php echo $address; ?></td>
          </tr>
        </table>
      </div>
      <?php if ($payment_methods) { ?>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_payment_method; ?></b>
      <div class="content">
        <p><?php echo $text_payment_methods; ?></p>
        <table width="100%" cellpadding="3">
          <?php foreach ($payment_methods as $payment_method) { ?>
          <tr>
            <td width="1">
              <?php if ($payment_method['id'] == $payment || !$payment) { ?>
			  <?php $payment = $payment_method['id']; ?>
              <input type="radio" name="payment_method" value="<?php echo $payment_method['id']; ?>" id="<?php echo $payment_method['id']; ?>" checked="checked" style="margin: 0px;" />
              <?php } else { ?>
              <input type="radio" name="payment_method" value="<?php echo $payment_method['id']; ?>" id="<?php echo $payment_method['id']; ?>" style="margin: 0px;" />
              <?php } ?></td>
            <td><label for="<?php echo $payment_method['id']; ?>" style="cursor: pointer;"><?php echo $payment_method['title']; ?></label></td>
          </tr>
          <?php } ?>
        </table>
      </div>
      <?php } ?>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_comments; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <textarea name="comment" rows="8" style="width: 99%;"><?php echo $comment; ?></textarea>
      </div>
      <?php if ($text_agree) { ?>
      <div class="buttons">
        <table>
          <tr>
             <td align="left" style="padding-right: 5px;"><?php echo $text_agree; ?></td>
             <td style="padding-right: 10px;"><?php if ($agree) { ?>
              <input type="checkbox" name="agree" value="1" checked="checked" />
              <?php } else { ?>
              <input type="checkbox" name="agree" value="1" />
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td align="left"><a href="<?php echo $back; ?>" class="button"><span><?php echo $button_back; ?></span></a></td>
            <td align="right" width="5">
             <input type="submit" value="<?php echo $button_continue; ?>" />
            </td>
          </tr>
        </table>
      </div>
      <?php } else { ?>
      <div class="buttons">
        <table>
          <tr>
            <td align="left"><a href="<?php echo $back; ?>" class="button"><span><?php echo $button_back; ?></span></a></td>
            <td align="right"><input type="submit" value="<?php echo $button_continue; ?>" /></td>
          </tr>
        </table>
      </div>
      <?php } ?>
    </form>
  </div>
 </div>
<?php echo $column_left; ?><?php echo $column_right; ?>
 <div class="bottom">&nbsp;</div>
<?php echo $footer; ?> 