<?php echo $header; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <?php if ($returns) { ?>
  <?php foreach ($returns as $return) { ?>
  <div class="order-list">
    <div class="order-id">
    <b><?php echo $text_rma_number; ?></b> #<?php echo $return['rma_number']; ?><br>
    <b><?php echo $text_order_id; ?></b> <a href="<?= $return['order_href']; ?>">#<?php echo $return['order_id']; ?></a><br>
    </div>
    <div class="order-status"><b><?php echo $text_status; ?></b> <?php echo $return['rma_status']; ?></div>
    <div class="order-content">
      <div><b><?php echo $text_date_added; ?></b> <?php echo $return['date_added']; ?><br /></div>
      <div>&nbsp</div>
      <div class="order-info">
<a taraget="_blank" href="imp/pdf_reports/rma_report.php?return_id=<?=$return['return_id'];?>">
          <img src="catalog/view/theme/default/image/pdf.gif" alt="<?php echo $button_view; ?>" title="<?php echo $button_view; ?>" />
        </a>
      <a href="<?php echo $return['href']; ?>"><img src="catalog/view/theme/default/image/info.png" alt="<?php echo $button_view; ?>" title="<?php echo $button_view; ?>" /></a></div>
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