<?php echo $header; ?>
<ul id="breadcrumbs-one">
    <?php 
    $total = count($breadcrumbs); 
    $i=0;
    foreach ($breadcrumbs as $breadcrumb) { 
        $i++;
        if($i==$total)
        {
    ?>
        <li><a class="current"><?php echo $breadcrumb['text']; ?></a></li>
    <?php 
        }else{
    ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>" rel="external"><?php echo $breadcrumb['text']; ?></a></li>
      <?php }
      } ?>
</ul>
<?php echo $content_top; ?>
<div data-role="content">
  <h2 style="margin-top:0px;"><?php echo $heading_title; ?></h2>
  <?php if ($returns) { ?>
  <ul data-role="listview">
  <?php foreach ($returns as $return) { ?>
  <li><h3>
  <b><?php echo $text_rma_number; ?></b> #<?php echo $return['rma_number']; ?><br>
    <b><?php echo $text_order_id; ?></b> <a href="<?= $return['order_href']; ?>">#<?php echo $return['order_id']; ?></a><br>
    </h3>

    <p>
    <b><?php echo $text_status; ?></b> <?php echo $return['rma_status']; ?><br/>
    <b><?php echo $text_date_added; ?></b> <?php echo $return['date_added']; ?><br />
    </p>
    </li>
    <br/>
    &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $return['href']; ?>" rel="external"><img src="catalog/view/theme/default/image/info.png" alt="<?php echo $button_view; ?>" title="<?php echo $button_view; ?>" /></a>
    <br/>
   <?php } ?>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } else { ?>
  <br/><br/>
  <?php echo $text_empty; ?>
  <br/><br/>
  <?php } ?>
<a href="<?php echo $continue; ?>" class="button" data-theme="a" data-role="button" rel="external"><?php echo $button_continue; ?></a>
</div>  
<?php echo $content_bottom; ?>
<?php echo $footer; ?> 