<?php
if(!isset($this->request->get['xorder_id']))
{
?>
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
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a></div>
    </div>
    <div class="content">
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              
              <td class="left">
                <a href="#"><?php echo $column_date; ?></a>
                </td>
            
              <td class="left">
                <a href="#"><?php echo $column_order_id; ?></a>
                </td>
                 <td class="left">
                <a href="#"><?php echo $column_customer; ?></a>
                </td>
                 <td class="left">
                <a href="#"><?php echo $column_item_returned; ?></a>
                </td>
                
                
                 <td class="left">
                <a href="javascript:void(0);">Resolution</a>
                </td>
        
                <td class="left">
                <a href="#">Completed By</a>
                </td>
             
            </tr>
          </thead>
          <tbody>
            <?php if ($returns) { ?>
            <?php foreach ($returns as $return) { ?>
            <tr>
            
              <td class="left"><?php echo $return['date_added']; ?></td>
          
              <td class="left"><?php echo $return['order_id']; ?></td>
               <td class="left"><?php echo $return['customer']; ?></td>
              <td class="left"><?php
              echo count($return['items_returned']).' items returned'."<br>";

              foreach($return['items_returned'] as $returned_items)
              {
              
               echo $returned_items['name']." - ".$returned_items['reason_name']."<br>";
              
              
              }
              
               ?></td>
              <!--<td class="left"><?php echo $voucher['theme']; ?></td>-->
              <td class="left"><?php echo ($return['resolution']);?></td>
              <td class="left"><?php echo $return['user']; ?></td>
            
             
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="9"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
     
    </div>
  </div>
</div>
<?php
}
?>
<script type="text/javascript"><!--
function sendVoucher(voucher_id) {
	$.ajax({
		url: 'index.php?route=sale/voucher/send&token=<?php echo $token; ?>&voucher_id=' + voucher_id,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('.success, .warning').remove();
			$('.box').before('<div class="attention"><img src="view/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('.attention').remove();
		},
		success: function(json) {
			if (json['error']) {
				$('.box').before('<div class="warning">' + json['error'] + '</div>');
			}
			
			if (json['success']) {
				$('.box').before('<div class="success">' + json['success'] + '</div>');
			}		
		}
	});
}
$(document).ready(function(e) {
    <?php
	if(isset($this->request->get['xorder_id']))
	{
	?>
	parent.location.reload();
	<?php	
	}
	?>
});
//--></script> 
<?php
if(!isset($this->request->get['xorder_id']))
{
?>
<?php echo $footer; ?>
<?php
}
?>