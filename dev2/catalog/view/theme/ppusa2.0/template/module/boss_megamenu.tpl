<?php 
if(!function_exists(showSubmenu))
{
function showSubmenu($category) {
	if (count($category['children']) > 0) {
		echo '<div class="sub_menu"><ul>';
		foreach ($category['children'] as $cat) {
			echo '<li><a href="'.$cat['href'].'">'.$cat['name'].'</a>';
			showSubmenu($cat);
			echo '</li>';
		}
		echo '</ul></div>';
	}
}
}
?>

<div id="boss_menu" class="mega-menu">
  <h3><span>Categories</span></h3>
  <ul class="display-menu">
	<?php $count=count($menus); ?>
	<?php foreach ($menus as $key => $menu) { ?>
	<li class="<?php echo ($key%2==0 ? 'even' : 'odd'); ?><?php echo ($key == ($count-1) ? ' last' : ''); ?>"  ><a href="<?php echo $menu['href']; ?>"><?php echo $menu['title'] ?></a>
		<?php if (count($menu['options']) > 0) { ?>
		<div style="width: <?php echo $menu['dropdown_width']; ?>px; color: white;" class="dropdown">
			<?php foreach ($menu['options'] as $option) { ?>
			<div style="width: <?php echo $option['width']; ?>px; float: left" class="option">
				<!-- manufacturer -->
				<?php if ($option['type'] == 'manufacturer') { ?>
					<?php for ($i = 0; $i < count($option['manufacturers']);) { ?>
						<ul style="display: table-cell; width: <?php echo $menu['column_width']; ?>px" class="column manufacturer">
						  <?php $j = $i + ceil(count($option['manufacturers']) / $option['column']); ?>
						  <?php for (; $i < $j; $i++) { ?>
							  <?php if (isset($option['manufacturers'][$i])) { ?>
								<li><a href="<?php echo $option['manufacturers'][$i]['href']; ?>">
									<?php if ($option['show_image']) { ?><img src="<?php echo $option['manufacturers'][$i]['image']; ?>" alt="<?php echo $option['manufacturers'][$i]['name']; ?>" title="<?php echo $option['manufacturers'][$i]['name']; ?>"/><?php } ?>
									<?php if ($option['show_name']) { ?><?php echo $option['manufacturers'][$i]['name']; ?><?php } ?>
								</a></li>
							  <?php } ?>
						  <?php } ?>
						</ul>
					<?php } ?>
				<?php } ?>
				<!-- category -->
				<?php if ($option['type'] == 'category') { ?>
					<?php if ($option['show_parent']) { ?>
						<a href="<?php echo $option['parent']['href']; ?>" class="parent">
							<?php if ($option['show_image'] && $option['parent']['image']) { ?><img src="<?php echo $option['parent']['image']; ?>" alt="<?php echo $option['parent']['name']; ?>" title="<?php echo $option['parent']['name']; ?>"/><?php } ?>
							<?php echo $option['parent']['name']; ?>
						</a>
					<?php } ?>
					<?php for ($i = 0; $i < count($option['categories']);) { ?>
						<ul style="display: table-cell; width: <?php echo $menu['column_width']; ?>px" class="column category">
						  <?php $j = $i + ceil(count($option['categories']) / $option['column']); ?>
						  <?php for (; $i < $j; $i++) { ?>
							  <?php if (isset($option['categories'][$i])) { ?>
								<li><a href="<?php echo $option['categories'][$i]['href']; ?>">
									<?php if ($option['show_image'] && $option['categories'][$i]['image']) { ?><img src="<?php echo $option['categories'][$i]['image']; ?>" alt="<?php echo $option['categories'][$i]['name']; ?>" title="<?php echo $option['categories'][$i]['name']; ?>"/><?php } ?>
									<span><?php echo $option['categories'][$i]['name']; ?></span>
								</a>
								<?php if ($option['show_submenu']) { showSubmenu($option['categories'][$i]); } ?>
								</li>
							  <?php } ?>
						  <?php } ?>
						</ul>
					<?php } ?>
				<?php } ?>
				<!-- information -->
				<?php if ($option['type'] == 'information') { ?>
					<?php for ($i = 0; $i < count($option['informations']);) { ?>
						<ul style="display: table-cell; width: <?php echo $menu['column_width']; ?>px" class="column informations">
						  <?php $j = $i + ceil(count($option['informations']) / $option['column']); ?>
						  <?php for (; $i < $j; $i++) { ?>
							  <?php if (isset($option['informations'][$i])) { ?>
								<li><a href="<?php echo $option['informations'][$i]['href']; ?>"><?php echo $option['informations'][$i]['title']; ?></a></li>
							  <?php } ?>
						  <?php } ?>
						</ul>
					<?php } ?>
				<?php } ?>
				<!-- static block -->
				<?php if ($option['type'] == 'static_block') { ?>
					<div class="staticblock"><?php echo $option['description']; ?></div>
				<?php } ?>
				<!-- product -->
				<?php if ($option['type'] == 'product') { ?>
					<?php for ($i = 0; $i < count($option['products']);) { ?>
						<ul style="display: table-cell; width: <?php echo $menu['column_width']; ?>px" class="column product">
						  <?php $j = $i + ceil(count($option['products']) / $option['column']); ?>
						  <?php for (; $i < $j; $i++) { ?>
							  <?php if (isset($option['products'][$i])) { ?>
								<li>
									<?php if ($option['products'][$i]['thumb']) { ?><a href="<?php echo $option['products'][$i]['href']; ?>"><img src="<?php echo $option['products'][$i]['thumb']; ?>" alt="<?php echo $option['products'][$i]['name']; ?>" title="<?php echo $option['products'][$i]['name']; ?>"/></a><?php } ?>
									<span>
										<a href="<?php echo $option['products'][$i]['href']; ?>"><?php echo $option['products'][$i]['name']; ?></a>
									</span>
									<?php echo $option['products'][$i]['price']; ?>	
								</li>
							  <?php } ?>
						  <?php } ?>
						</ul>
					<?php } ?>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</li>
  <?php } ?>
  </ul>
</div>
