<div class="categories_panel">
	<h3>Categories</h3>
	<div class="panel-group" id="accordion">
		<?php foreach ($menus as $i => $menu) { ?>
		<?php foreach ($menu['options'] as $j => $title) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $i . $j; ?>">
					<?= $title['parent']['name']; ?>
				</a>
			</div>
			<?php if ($title['categories']) { ?>
			<div id="collapse<?= $i . $j; ?>" class="panel-collapse collapse <?= ($i == 0 && $j == 0)? 'in': '';?>">
				<div class="panel-body">
					<ul class="custome_acc_ul">
						<?php foreach ($title['categories'] as $k => $category) { ?>

						<li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>

						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
		<?php } ?>
	</div>	
	<!-- <p><a href="#">See More</a></p> -->
</div>