<div class="col-md-3">
	<div class="featured-product">
		<h4 class="text-center">FEATURED PRODUCTS</h4>
		<div class="listing">
		<?php foreach ($products as $product) : ?>
			<div class="listing-col text-center">
				<a href="<?php echo $product['href']; ?>"><img class="lazy" height="150" src="<?php echo $product['thumb']; ?>" width="150" data-original="<?php echo $product['thumb']; ?>" alt="" class="noborder"></a>
				<p class="listing-title text-center"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></p>
				<p class="price">
				<?php
				if($product['sale_price']!='$0.00')
				{
				?>
				<span style="font-size: 13px;margin-right:5px;text-decoration:line-through;"><?php echo $product['price'];?></span><span style="color: red;"><?php echo $product['sale_price'];?></span>
				<?php
				}
				else
				{
				?>
				<span><?php echo $product['price'];?></span>
				<?php
				}
				?>
				</p>
			</div>
		<?php endforeach; ?>
		</div>
	</div>	
</div>