<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($notinstalled <> 0) {?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>

<link rel="stylesheet" href="view/kickstart/css/kickstart.css" media="all" /> <!-- KICKSTART -->
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/report.png" alt="" /> <?php echo $heading_title; ?></h1> 
		<div class="right"><form action="" method="post"> <?php if ($products) { ?><input type="submit" name="sendemail" class="small green" value="<?php echo $text_notify_customers;?>"> <?php } ?> <a href="<?php echo $link_to_setting; ?>" class="button blue"><i class='icon-cogs'></i> <?php echo $button_customise; ?></a></form></div>
    </div>

    <div class="content">
		<table width="100%" cellspacing="10px">
		<tr>
		
		<td style="background-color:#FFFF99; padding: 15px;">
		<b><?php echo $text_total_alert;?><?php echo $total_alert;?></b><br>
		<b><?php echo $text_total_responded;?><?php echo $total_responded;?></b><br><br>
		<div class="clearfix"></div>
		<div class="center"><a href="<?php echo $current_page;?>&filteroption=all" class="button small blue"><?php echo $text_show_all_reports;?></a> <a href="<?php echo $current_page;?>&delete=all" class="button small red"><?php echo $text_reset_all;?></a></div>
		</td>
		
		<td style="background-color:#CCFF99; padding: 15px;">
		<b><?php echo $text_customers_awaiting_notification;?><?php echo $awaiting_notification;?></b><br>
		<b><?php echo $text_number_of_products_demanded;?><?php echo $product_requested;?></b><br><br>
		<div class="clearfix"></div>
		<div class="center"><a href="<?php echo $current_page;?>&filteroption=awaiting" class="button small blue"><?php echo $text_show_awaiting_reports;?></a> <a href="<?php echo $current_page;?>&delete=awaiting" class="button small red"><?php echo $text_reset_awaiting;?></a></div>
		</td>
		
		<td style="background-color:#66CCFF; padding: 15px;">
		<b><?php echo $text_archive_records;?><?php echo $total_responded;?></b><br>
		<b><?php echo $text_customers_notified;?><?php echo $customer_notified;?></b><br><br>
		<div class="clearfix"></div>
		<div class="center">
		<a href="<?php echo $current_page;?>&filteroption=archive" class="button small blue"><?php echo $text_show_archive_reports;?></a> <a href="<?php echo $current_page;?>&delete=archive" class="button small red"><?php echo $text_reset_archive;?></a></div>
		</td>
		
		</tr>
		</table>
	
	<hr>
<div class="col_9">
<h5><i class="icon-laptop"></i> <?php echo $current_report;?> <?php echo $text_reports;?></h5>
      <table class="list">
        <thead>
         	<tr>
            <td class="left"><?php echo $column_product_id; ?></td>
            <td class="left"><?php echo $column_product_name; ?></td>
            <td class="left"><?php echo $column_sku; ?></td>
            <td class="left"><?php echo $column_email; ?></td>
            <td class="left"><?php echo $column_language; ?></td>
            <td class="left"><?php echo $column_enquiry_date; ?></td>
            <td class="left"><?php echo $column_notify_date; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if ($products) { ?>
          <?php foreach ($products as $product) { ?>
          <tr>
            <td class="left"><?php echo $product['product_id']; ?></td>
            <td class="left"><a href="<?php echo $product['product_link']; ?>" target="_blank"><?php echo $product['name']; ?></a></td>
            <td class="left"><?php echo $product['sku']['sku']; ?></td>
            <td class="left"><?php echo $product['email']; ?></td>
            <td class="left"><?php echo $product['language_code']; ?></td>
            <td class="left"><?php echo $product['enquiry_date']; ?></td>
	   		<td class="left"><?php echo $product['notify_date']; ?></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <div class="pagination"><?php echo $pagination; ?></div>
      <?php } ?></div>

	<div class="col_3">
<h5><i class="icon-signal"></i> <?php echo $text_product_in_demand; ?></h5>
<table class="list">
<thead>
         	<tr>
            <td class="left"><?php echo $column_product_id; ?></td>
            <td class="left"><?php echo $column_product_name; ?></td>
            <td class="right"><?php echo $column_count; ?></td>
          </tr>
        </thead>
        <tbody>

<?php if ($demands) { 
foreach ($demands as $demand) {
echo '<tr>';
echo '<td class="left">'.$demand['pid'].'</td>';
echo '<td class="left">'.$demand['name'].'</td>';
echo '<td class="right">'.$demand['count'].'</td>';
echo '</tr>';
}
}else { ?>
          <tr>
            <td class="center" colspan="3"><?php echo $text_no_results; ?></td>
          </tr>
<?php } ?>
</tbody>
</table>
	</div>

      <?php echo $installed; ?>
    </div>
  </div>
</div>
<?php echo $footer; ?>