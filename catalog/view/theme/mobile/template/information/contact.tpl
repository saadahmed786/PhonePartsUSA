<?php echo $header; ?>
<div class="box">
  <div class="top">
    <h1><?php echo $heading_title; ?></h1>
  </div>
  <div class="middle">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="contact">
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
      <table style="width: 100%">
        <tr>
          <td style="width: 50%">
          <b><?php echo $text_address; ?></b><br />
          <?php echo $store; ?><br />
          <?php echo $address; ?>
          </td>
          <td>
          <?php if ($telephone) { ?>
          <b><?php echo $text_telephone; ?></b><br />
          <?php echo $telephone; ?><br />
          <br />
          <?php } ?>
          <?php if ($fax) { ?>
          <b><?php echo $text_fax; ?></b><br />
          <?php echo $fax; ?>
          <?php } ?>
          </td>
        </tr>
      </table>
      </div>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <table>
          <tr>
            <td><?php echo $entry_name; ?><br />
              <input type="text" name="name" value="<?php echo $name; ?>" />
              <?php if ($error_name) { ?>
              <span class="error"><?php echo $error_name; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_email; ?><br />
              <input type="text" name="email" value="<?php echo $email; ?>" />
              <?php if ($error_email) { ?>
              <span class="error"><?php echo $error_email; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_enquiry; ?><br />
              <textarea name="enquiry" style="width: 99%;"><?php echo $enquiry; ?></textarea>
              <?php if ($error_enquiry) { ?>
              <span class="error"><?php echo $error_enquiry; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_captcha; ?><br />
              <input type="text" name="captcha" value="<?php echo $captcha; ?>" />
              <?php if ($error_captcha) { ?>
              <span class="error"><?php echo $error_captcha; ?></span>
              <?php } ?>
              <br />
              <img src="index.php?route=information/contact/captcha" /></td>
          </tr>
          <tr>
            <td align="left"><input type="submit" value="<?php echo $button_continue; ?>" /></td>
          </tr>
        </table>
      </div>
    </form>
  </div>
  </div>
<?php echo $column_left; ?><?php echo $column_right; ?>
 <div class="bottom">&nbsp;</div>
<?php echo $footer; ?> 