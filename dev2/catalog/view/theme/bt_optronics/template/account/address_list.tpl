<?php echo $header; ?>
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<h1><?php echo $heading_title; ?></h1>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <h2><?php echo $text_address_book; ?></h2>
  <?php foreach ($addresses as $result) { ?>
  <div class="content">
    <table style="width: 100%;">
      <tr>
        <td style="line-height:21px"><?php echo $result['address']; ?></td>
        <td style="text-align: right;">
          <?php if (!$this->session->data['warehouse']) { ?>
          <a href="<?php echo $result['update']; ?>" class="button_black"><span><?php echo $button_edit; ?></span></a>
          &nbsp; 
          <a href="<?php echo $result['delete']; ?>" class="button_pink"><span><?php echo $button_delete; ?></span></a>
          <?php } ?>
        </td>
      </tr>
    </table>
  </div>
  <?php } ?>
  <div class="buttons">
    <div class="left"><a href="<?php echo $back; ?>" class="button_pink"><span><?php echo $button_back; ?></span></a></div>
    <?php if (!$this->session->data['warehouse']) { ?><div class="right"><a href="<?php echo $insert; ?>" class="button_pink"><span><?php echo $button_new_address; ?></span></a></div><?php } ?>
  </div>
  <?php echo $content_bottom; ?></div>
  <?php echo $footer; ?>