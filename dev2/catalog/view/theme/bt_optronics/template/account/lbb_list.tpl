
<?php echo $header; ?>
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<h1><?php echo $heading_title; ?></h1>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <?php if ($lbbs) { ?>
  <?php foreach ($lbbs as $lbb) { ?>
  <div class="order-list">
    <div class="order-id"><b><?php echo $text_shipment_no; ?></b> #<?php echo $lbb['shipment_number']; ?></div>
    <div class="order-status"><b><?php echo $text_status; ?></b> <?php echo $lbb['status']; ?></div>
    <div class="order-content">
      <div><b><?php echo $text_date_added; ?></b> <?php echo $lbb['date_added']; ?><br /></div>
      <div><b><?php echo $text_total; ?></b> <?php echo $lbb['total']; ?></div>
      <div class="order-info">
        
        <a href="<?php echo $lbb['href']; ?>">
          <img src="catalog/view/theme/default/image/info.png" alt="<?php echo $button_view; ?>" title="<?php echo $button_view; ?>" />
        </a>
      </div>
    </div>
  </div>
  <?php } ?>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <?php } ?>
  <div class="buttons">
    <div class="left"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
  </div>
  <?php echo $content_bottom; ?></div>
  <?php echo $footer; ?>