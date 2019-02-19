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
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
          <table class="form">
          	<tr>
          		<td>
          			<?php echo $entry_enable_on_views; ?>
          		</td>
          		<td>
          			<table>
          				<?php foreach ($views as $view) { ?>
          				<tr>
          					<td> 
          						<?php if (!empty($modules[1]['enable_on_views']) && $modules[1]['enable_on_views'][$view['key']]==1) {?>
          						<input type="hidden" name="category_quantity_module[1][enable_on_views][<?php echo $view['key']?>]" value="0" />
          						<input type="checkbox" checked="checked" name="category_quantity_module[1][enable_on_views][<?php echo $view['key']?>]" value="1" />
          						<?php } else { ?>
          						<input type="hidden" name="category_quantity_module[1][enable_on_views][<?php echo $view['key']?>]" value="0" />
          						<input type="checkbox" name="category_quantity_module[1][enable_on_views][<?php echo $view['key']?>]" value="1" />
          						<?php } ?>
          					</td>
          					<td>
          						<?php echo $view['name']; ?>
          					</td>
          				</tr>
          				<?php } ?>
          			</table>
          		</td>
          	</tr>
          	<tr>
          		<td>
          			<?php echo $entry_enable_on_modules; ?>
          		</td>
          		<td>
          			<table>
          				<?php foreach ($show_on_modules as $module) { ?>
          				<tr>
          					<td> 
          						<?php if (!empty($modules[1]['enable_on_modules']) && $modules[1]['enable_on_modules'][$module['key']]==1) {?>
          						<input type="hidden" name="category_quantity_module[1][enable_on_modules][<?php echo $module['key']?>]" value="0" />
          						<input type="checkbox" checked="checked" name="category_quantity_module[1][enable_on_modules][<?php echo $module['key']?>]" value="1" />
          						<?php } else { ?>
          						<input type="hidden" name="category_quantity_module[1][enable_on_modules][<?php echo $module['key']?>]" value="0" />
          						<input type="checkbox" name="category_quantity_module[1][enable_on_modules][<?php echo $module['key']?>]" value="1" />
          						<?php } ?>
          					</td>
          					<td>
          						<?php echo $module['name']; ?>
          					</td>
          				</tr>
          				<?php } ?>
          			</table>
          		</td>
          	</tr>
          </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>