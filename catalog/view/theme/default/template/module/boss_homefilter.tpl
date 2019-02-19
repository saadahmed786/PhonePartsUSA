<?php  function percent_price($sub, $total, $dec) {
	if ($sub) {
		$perc = $sub / $total * 100;
		$perc = round($perc, $dec);
		return $perc;
	}
	return 0; 
}
?>
<?php if(!empty($tabs)){ ?>
<div class="boss_homefilter">
<h2><?php echo $heading_title; ?></h2>
<div id="boss_filter_tabs<?php echo $module; ?>" class="boss_filter_tabs">
	<?php foreach ($tabs as $numTab => $tab) { ?>
	<a href="#filtertab-<?php echo $numTab; ?><?php echo $module; ?>"><img src="<?php echo $tab['image']; ?>" title="<?php echo $tab['title']; ?>" alt="<?php echo $tab['title']; ?>" /><?php echo $tab['title']; ?></a>
	<?php } ?>
</div>

<div id="boss_filter_content<?php echo $module; ?>" class="boss_home_filter">
<?php foreach ($tabs as $numTab => $tab) { ?>
	<div class="box-heading" style="display: none">
		<img src="<?php echo $tab['image']; ?>" title="<?php echo $tab['title']; ?>" alt="<?php echo $tab['title']; ?>" /><?php echo $tab['title']; ?>
	</div>
	<div class="box" id="filtertab-<?php echo $numTab; ?><?php echo $module; ?>">
		<div class="box-content hf-es-carousel-wrapper">
		  <div class="hf-box-product hf-es-carousel">
			<?php if(!empty($tab['products'])){ ?>
			<ul class="skin-opencart">
				<?php foreach ($tab['products'] as $product) { ?>
				  <li>
				  <div class="one-product-filter">
					<?php if ($product['thumb']) { ?>
					<div class="image">
					<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>"/></a>		
					<?php if ($product['price']) { ?>
					<?php if ($product['special']) { ?>
					<?php $price =  preg_replace("/[^0-9]/", '', $product['price']);
						$special =  preg_replace("/[^0-9]/", '', $product['special']);
						$percent= 100-percent_price($special, $price,0); ?>
						<div class="discount color-1">
							<?php echo "-".$percent."%";?>
						</div>
					<?php }} ?>	
					</div>
					<?php } ?>
					<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
					<div class="description"><?php echo $product['description']; ?></div>
					<?php if ($product['rating']) { ?>
					<div class="rating"><img src="catalog/view/theme/<?php echo $template; ?>/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
					<?php } ?>
					<?php if ($product['price']) { ?>
					<div class="price">
					  <?php if (!$product['special']) { ?>
					  <?php echo $product['price']; ?>
					  <?php } else { ?>
					  <?php echo $product['special']; ?>
					  <?php } ?>
					</div>
					<?php } ?>
					<div class="cart"><span class="button_pink"><input type="button" value="<?php echo $button_cart; ?>" onclick="boss_addToCart('<?php echo $product['product_id']; ?>');" class="button" /></span></div>
				  </div>
				  </li>
				<?php } ?>
			</ul>
			<?php } ?>
		  </div> 
		</div><!-- end div box content -->
	</div>
	<?php if ($use_scrolling_panel) { ?>
	<script type="text/javascript">	
		if(getWidthBrowser() > 959){
			$('#filtertab-<?php echo $numTab; ?><?php echo $module; ?> .box-content').elastislide({
				imageW 		: 184,
				border		: 0,
				current		: 0,
				margin		: 10,
				onClick 	: true,
				minItems	: 1,
				disable_touch		: false
			});	
		}else if(getWidthBrowser() > 766 && getWidthBrowser() < 960) {
			$('#filtertab-<?php echo $numTab; ?><?php echo $module; ?> .box-content').elastislide({
				imageW 		: 180,
				border		: 0,
				current		: 0,
				margin		: 7,
				onClick 	: true,
				minItems	: 1,
				disable_touch		: false
			});	
		}
		//474 200 767
	</script>
	<?php } ?>
<?php } ?>
</div>
<script type="text/javascript"><!--
$('#boss_filter_content<?php echo $module; ?> div.box-heading').click(function() {
	if($(this).next().css('display') == 'none'){
		$(this).next().show();
		$(this).addClass('active');
	}else{
		$(this).next().hide();
		$(this).removeClass('active');
	}	
	return false;
}).next().hide();
	
$(document).ready(function() {
	boss_homefilter_resize<?php echo $module; ?>();
});
$(window).resize(function() {
	boss_homefilter_resize<?php echo $module; ?>();
});
function boss_homefilter_resize<?php echo $module; ?>()	{
	if(getWidthBrowser() < 767){
		$('#boss_filter_tabs<?php echo $module; ?>').hide();
		$('#boss_filter_content<?php echo $module; ?> div.box-heading').show();
		//$('.boss_home_filter .box').hide();
	} else {
		$('#boss_filter_tabs<?php echo $module; ?>').show();
		$('#boss_filter_content<?php echo $module; ?> div.box-heading').hide();
	}
}

//--></script>
<script type="text/javascript"><!--
$('#boss_filter_tabs<?php echo $module; ?> a').tabs();
//--></script>
</div>
<?php } ?>
