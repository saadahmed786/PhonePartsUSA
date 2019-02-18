<div class="module featured">
	<h2><?php echo $heading_title; ?></h2>	
	<?php switch($columns) {
			case 2:
				$columns_class = "two-columns";
				break;
			case 3:
				$columns_class = "three-columns";
				break;			
		}?>
	<ul class="<?php echo  $columns_class; ?>">    
		<?php foreach ($products as $product) { ?>
		<li>
			<?php if ($product['thumb']) { ?>
			<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
			<?php } ?>
			<a href="<?php echo $product['href']; ?>" class="name"><?php echo $product['name']; ?></a>
			<?php if ($product['price']) { ?>
			<div class="price">
			<?php if (!$product['special']) { ?>
			<?php echo $product['price']; ?>
			<?php } else { ?>
			<span class="price-new"><?php echo $product['special']; ?></span>
			<?php } ?>
			</div>
			<?php } ?>
			<?php if ($product['rating']) { ?>
			<div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
			<?php } ?>        
		</li>
		<?php } ?>    
	</ul>
</div>
