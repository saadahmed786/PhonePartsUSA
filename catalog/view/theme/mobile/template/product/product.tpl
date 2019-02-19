<?php echo $header; ?>
<div class="box">
  <div class="top">
    <h1><?php echo $heading_title; ?></h1>
  </div>
  <div class="middle">
    <div style="width: 100%; margin-bottom: 2px;">
      <table style="width: 100%; border-collapse: collapse;">
        <tr>
          <td style="text-align: center; vertical-align: top;">
          <a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="thickbox">
          <img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image" style="margin-bottom: 3px;" /></a><br />
           </td>
          </tr>
          <tr>
          <td style="padding-left: 15px; vertical-align: top;">
          <table width="100%">
              <?php if ($display_price) { ?>
              <tr>
                <td><b><?php echo $text_price; ?></b></td>
                <td><?php if (!$special) { ?>
                  <?php echo $price; ?>
                  <?php } else { ?>
                  <span style="text-decoration: line-through;"><?php echo $price; ?></span> <span style="color: #F00;"><?php echo $special; ?></span>
                  <?php } ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td><b><?php echo $text_availability; ?></b></td>
                <td><?php echo $stock; ?></td>
              </tr>
              <tr>
                <td><b><?php echo $text_model; ?></b></td>
                <td><?php echo $model; ?></td>
              </tr>
              <?php if ($manufacturer) { ?>
              <tr>
                <td><b><?php echo $text_manufacturer; ?></b></td>
                <td><a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></td>
              </tr>
              <?php } ?>
              <tr>
                <td><b><?php echo $text_average; ?></b></td>
                <td><?php if ($average) { ?>
                  <img src="catalog/view/theme/default/image/stars_<?php echo $average . '.png'; ?>" alt="<?php echo $text_stars; ?>" style="margin-top: 2px;" />
                  <?php } else { ?>
                  <?php echo $text_no_rating; ?>
                  <?php } ?></td>
              </tr>
            </table>
            <br />
            <?php if ($display_price) { ?>
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="product">
              <?php if ($options) { ?>
              <b><?php echo $text_options; ?></b><br />
              <div style="background: #FFFFCC; border: 1px solid #FFCC33; padding: 10px; margin-top: 2px; margin-bottom: 15px;">
                <table style="width: 100%;">
                  <?php foreach ($options as $option) { ?>
                  <tr>
                    <td><?php echo $option['name']; ?>:<br />
                      <select name="option[<?php echo $option['option_id']; ?>]">
                        <?php foreach ($option['option_value'] as $option_value) { ?>
                        <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?>
                        <?php if ($option_value['price']) { ?>
                        <?php echo $option_value['prefix']; ?><?php echo $option_value['price']; ?>
                        <?php } ?>
                        </option>
                        <?php } ?>
                      </select></td>
                  </tr>
                  <?php } ?>
                </table>
                <?php } ?>
              </div>
              <?php if ($display_price) { ?>
              <?php if ($discounts) { ?>
              <b><?php echo $text_discount; ?></b><br />
              <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-top: 2px; margin-bottom: 15px;">
                <table style="width: 100%;">
                  <tr>
                    <td style="text-align: right;"><b><?php echo $text_order_quantity; ?></b></td>
                    <td style="text-align: right;"><b><?php echo $text_price_per_item; ?></b></td>
                  </tr>
                  <?php foreach ($discounts as $discount) { ?>
                  <tr>
                    <td style="text-align: right;"><?php echo $discount['quantity']; ?></td>
                    <td style="text-align: right;"><?php echo $discount['price']; ?></td>
                  </tr>
                  <?php } ?>
                </table>
              </div>
              <?php } ?>
              <?php } ?>
              <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px;"><?php echo $text_qty; ?>
                <input type="text" name="quantity" size="3" value="1" />
                <input type="submit" name="add_to_cart" value="Add to cart" />
              </div>
              <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
              <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
            </form>
            <?php } ?></td>
        </tr>
      </table>
    </div>
    <div id="description" class="tab_page">
      <h4>Description</h4>
      <?php echo $description; ?>
    </div>
    <div id="image" class="tab_page">
      <?php if ($images) { ?>
       <h4>Images</h4>
        <div style="display: inline-block;">
        <?php foreach ($images as $image) { ?>
        <div style="display: inline-block; float: left; text-align: center; margin-left: 5px; margin-right: 5px; margin-bottom: 10px;"><a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="thickbox"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" style="border: 1px solid #DDDDDD; margin-bottom: 3px;" /></a><br />
          <span style="font-size: 11px;"><?php echo $text_enlarge; ?></span></div>
        <?php } ?>
      </div>
      <?php } else { ?>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;"><?php echo $text_no_images; ?></div>
      <?php } ?>
    </div>
    <div id="related" class="tab_page">
      <?php if ($products) { ?>
      <h4>Related products</h4>
       <table class="list">
        <?php for ($i = 0; $i < sizeof($products); $i = $i + 2) { ?>
        <tr>
          <?php for ($j = $i; $j < ($i + 2); $j++) { ?>
          <td width="25%"><?php if (isset($products[$j])) { ?>
            <a href="<?php echo $products[$j]['href']; ?>"><img src="<?php echo $products[$j]['thumb']; ?>" title="<?php echo $products[$j]['name']; ?>" alt="<?php echo $products[$j]['name']; ?>" /></a><br />
            <a href="<?php echo $products[$j]['href']; ?>"><?php echo $products[$j]['name']; ?></a><br />
            <span style="color: #999; font-size: 11px;"><?php echo $products[$j]['model']; ?></span><br />
            <?php if ($display_price) { ?>
            <?php if (!$products[$j]['special']) { ?>
            <span style="color: #900; font-weight: bold;"><?php echo $products[$j]['price']; ?></span><br />
            <?php } else { ?>
            <span style="color: #900; font-weight: bold; text-decoration: line-through;"><?php echo $products[$j]['price']; ?></span> <span style="color: #F00;"><?php echo $products[$j]['special']; ?></span>
            <?php } ?>
            <?php } ?>
            <?php if ($products[$j]['rating']) { ?>
            <img src="catalog/view/theme/default/image/stars_<?php echo $products[$j]['rating'] . '.png'; ?>" alt="<?php echo $products[$j]['stars']; ?>" />
            <?php } ?>
            <?php } ?></td>
          <?php } ?>
        </tr>
        <?php } ?>
      </table>
      <?php } else { ?>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;"><?php echo $text_no_related; ?></div>
      <?php } ?>
    </div>
  </div>
  <div class="bottom">&nbsp;</div>
</div>
  <?php echo $column_left; ?><?php echo $column_right; ?>

  <div class="bottom">&nbsp;</div>
<?php echo $footer; ?> 