<?php if ($error) { ?>
<div class="warning"><?php echo $error; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<table class="list">
  <thead>
    <tr>
      <td class="left"><b><?php echo $column_date_added; ?></b></td>
      <td class="left"><b><?php echo $column_comment; ?></b></td>
      <td class="left"><b><?php echo $column_status; ?></b></td>
      <td class="left"><b>Modified By</b></td>
      <td class="left"><b><?php echo $column_notify; ?></b></td>
      <td class="left"><b>Store Credit?</b></td>
    </tr>
  </thead>
  <tbody>
    <?php if ($histories) { ?>
    <?php foreach ($histories as $history) { ?>
    <tr>
      <td class="left"><?php echo $history['date_added']; ?></td>
      <td class="left"><?php echo $history['comment']; ?><?php if($history['store_credit']==1) {
      ?>
      <br />
      <small style="color:grey">Code: <?php echo $history['code'];?><br />Amount: <?php echo $history['amount'];?></small> 
      <?php
      
      }?></td>
      <td class="left"><?php echo $history['status']; ?></td>
       <td class="left"><?php echo $history['user_name'];?></td>
      <td class="left"><?php echo $history['notify']; ?></td>
      <td class="left"><?php echo (($history['store_credit']==1)?'Yes':'No'); ?></td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td class="center" colspan="4"><?php echo $text_no_results; ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<div class="pagination"><?php echo $pagination; ?></div>
