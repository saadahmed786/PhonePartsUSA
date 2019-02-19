<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if (isset($success)) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1><img src="view/image/module.png"><?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $back; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
        <tr>
          <td><?php echo $categories;?></td>
          <td>
          <button type="submit" name="categories" value="categories"><?php echo $generate;?></button>
          <?php echo $warning_clear;?>
          </td>
        </tr>
        <tr>
          <td><?php echo $products;?></td>
          <td>
          <button type="submit" name="products" value="products"><?php echo $generate;?></button>
          <?php echo $warning_clear;?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php echo $footer; ?>