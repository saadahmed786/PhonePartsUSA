<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/order.png" alt="" /> <?php echo $heading_title; ?></h1>
    </div>
    <div class="content">
      <div class="vtabs">
        <a href="#tab-history"><?php echo $tab_order_history; ?></a></div>

      <div id="tab-history" class="vtabs-content">
        <form action="<?php echo $submit_url; ?>" method="post" id="form">
        <table class="form">
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="order_status_id" id="order_status_id" onchange="$('textarea[name=\'comment\']').val(comment_text[this.options[this.selectedIndex].value]);">
                <?php foreach ($order_statuses as $order_statuses) { ?>              
                <option value="<?php echo $order_statuses['order_status_id']; ?>"><?php echo $order_statuses['name']; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_notify; ?></td>
            <td><input type="checkbox" name="notify" value="1" checked="checked" /></td>
          </tr>
          <tr>
            <td><?php echo $entry_comment; ?></td>
            <td><textarea name="comment" cols="40" rows="8" style="width: 99%"></textarea>
			<textarea name="selected" style="display:none;"><?php echo $order_selectid; ?></textarea>
              <div style="margin-top: 10px; text-align: right;"><a onclick="$('#form').submit();" id="button-history" class="button"><span><?php echo $button_add_history; ?></span></a></div></td>
          </tr>
        </table>
		</form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
var comment_text = new Array(); 
<?php foreach ($comments as $comment) { ?>
comment_text[<?php echo $comment['order_status_id']; ?>]=('<?php echo preg_replace("'([\r\n])[\s]+'", "\\r\\n",$comment['comment']); ?>');
<?php } ?>
$('textarea[name=\'comment\']').val(comment_text[$('select[name=\'order_status_id\']').val()]);
//--></script> 
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script> 
<?php echo $footer; ?>