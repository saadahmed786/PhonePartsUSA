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
  <?php if ($lbbs) { ?>
  <ul data-role="listview">
  <?php foreach ($lbbs as $lbb) { ?>
  <li><h3>
    <b><?php echo $text_shipment_no; ?></b> #<?php echo $lbb['shipment_number']; ?>
    <b><?php echo $text_status; ?></b> <?php echo $lbb['status']; ?>
    
    </h3>
    <p>
    <b><?php echo $text_date_added; ?></b> <?php echo $lbb['date_added']; ?><br/>
    <b><?php echo $text_products; ?></b> <?php echo $lbb['total']; ?>
    </p>
    </li>
    <br/>
    &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $lbb['href']; ?>" rel="external"><img src="catalog/view/theme/default/image/info.png" alt="<?php echo $button_view; ?>" title="<?php echo $button_view; ?>" /></a>
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