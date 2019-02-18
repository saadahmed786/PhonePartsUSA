<?php echo $header; ?>
<?php $grid = 12; if($column_left != '') { $grid = $grid-3; } if($column_right != '') { $grid = $grid-3; } $grid_left = 3; $grid_right = 9; if($this->config->get('column_position') == '1' && $this->config->get('general_status') == '1') { $grid_left = 9; $grid_right = 3; }  ?>

<div id="content" class="set-size">

	<?php echo $content_top; ?>
	<?php if($column_left != '' && $column_right != '') { echo '<div class="grid-'.$grid_left.' float-left">'.$column_left.'</div>'; } elseif($column_left != '') { echo $column_left; } ?>
	<?php if($column_left != '' && $column_right != '') { echo '<div class="grid-'.$grid_right.' float-left">'.$column_right.'</div>'; } elseif($column_right != '') { echo $column_right; } ?>
	<?php echo $content_bottom; ?>
	
	<p class="clear"></p>
	
</div>

<?php echo $footer; ?>