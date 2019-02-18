<?php if ($modules) { ?>
<div class="<?= $class; ?>">
	<div id="column-left">
		<?php foreach ($modules as $module) { ?>
		<?php echo $module; ?>
		<?php } ?>
	</div>
</div>
<?php } ?> 
