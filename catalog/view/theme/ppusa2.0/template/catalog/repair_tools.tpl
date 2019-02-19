<?php echo $header; ?>
<!-- @End of header -->
<main class="main">
	<div class="container repair-parts-page">
		<ul class="breadcrum clearfix">
			<?php foreach ($breadcrumbs as $key => $breadcrumb) : ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php if (count($breadcrumbs) != ($key + 1)) { ?>
				<li class="seprator">></li>
				<?php } ?>
			<?php endforeach; ?>
		</ul>
		<!--@End breadcrum -->
		<div class="row">
				<?php echo ($filter)? $filter: ''; ?>
				<div class="col-md-9">
					<ul class="nav nav-tabs">
					    <!-- <li class="active"><a href="<?php echo $endBC['href']; ?>#repariParts">Repair Parts</a></li> -->
					    <li class="active"><a href="<?php echo $endBC['href']; ?>#repariTools">Repair Tools</a></li>
					    <!-- <li><a href="<?php echo $endBC['href']; ?>#accessories">Accessories</a></li> -->
					</ul>
					<div class="tab-content padding-30">
					    <div id="repariParts" class="tab-pane fade">
					    </div>
					    <div id="repariTools" class="tab-pane fade in active overflow-hide">
					    <div class="row first-product">
					    		<div class="col-md-3">
					    			<img src="<?php echo $info['image']; ?>" alt="<?php echo $info['name']; ?>" class="imgborder">
					    		</div>
					    		<div class="col-md-9">
					    			<h2 class="product-title"><?php echo $info['name']; ?></h2>
					    			<div class="text">
					    				<p><?php echo $info['description']; ?></p>
					    			</div>
					    		</div>
					    	</div>
					    	<div class="row product-listing">
					    	<?php foreach ($products as $product) : ?>
					    		<div class="col-md-3 listing-col">
					    			<img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="imgborder">
					    			<p class="listing-title text-center"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></p>
					    		</div>
					    	<?php endforeach; ?>
					    	</div>
					    </div>
					    <div id="accessories" class="tab-pane fade">
					    </div>
					</div>
				</div>
				<?php echo ($featured)? $featured: ''; ?>
			</div>
	</div>
</main><!-- @End of main -->
<?php echo $footer; ?>
<!-- @End of footer -->