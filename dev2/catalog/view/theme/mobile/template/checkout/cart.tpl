<?php echo $header; ?>
<div class="box">
  <div class="top">
    <h1><?php echo $heading_title; ?>
        <?php if ($weight) { ?>
        &nbsp;(<?php echo $weight; ?>)
        <?php } ?>
    </h1>
  </div>
  <div class="middle" style="padding-bottom: 1px;">
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="cart">
      <table class="cart">
        <tr>
          <th align="center"><?php echo $column_remove; ?></th>
          <th align="center"><?php echo $column_name; ?></th>
          <th align="right"><?php echo $column_quantity; ?><br /><?php echo $column_price; ?></th>
          <th align="right"><?php echo $column_total; ?></th>
        </tr>
        <?php $class = 'odd'; ?>
        <?php foreach ($products as $product) { ?>
        <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
        <tr class="<?php echo $class; ?>">
          <td align="center"><input type="checkbox" name="remove[<?php echo $product['key']; ?>]" /></td>
          <td align="center"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
          <br />
          <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
            <?php if (!$product['stock']) { ?>
            <span style="color: #FF0000; font-weight: bold;">***</span>
            <?php } ?>
            <div>
              <?php foreach ($product['option'] as $option) { ?>
              - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br />
              <?php } ?>
            </div>
            <?php echo $product['model']; ?>
          </td>
          <td align="right" valign="center"><input type="text" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="3" />
          <br /> x <?php echo $product['price']; ?>
          </td>
          <td align="right" valign="center"><?php echo $product['total']; ?></td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="7" align="right">
          <?php foreach ($totals as $total) { ?>
          <b><?php echo $total['title']; ?></b>
            <?php echo $total['text']; ?><br/>
          <?php } ?>          
          </td>
        </tr>
      </table>
      <div class="buttons">
        <table>
          <tr>
            <td align="left"><input type="submit" value="<?php echo $button_update; ?>"/></td>
            <td align="center"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_shopping; ?></span></a></td>
            <td align="right"><a href="<?php echo $checkout; ?>" class="button"><span><?php echo $button_checkout; ?></span></a></td>
          </tr>
        </table>
      </div>
    </form>
 </div>
</div>
<?php echo $column_left; ?><?php echo $column_right; ?>
 <div class="bottom">&nbsp;</div>
<?php echo $footer; ?>