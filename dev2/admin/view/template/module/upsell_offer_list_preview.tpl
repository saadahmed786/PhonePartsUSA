<div class="content">
	<?php if ($title) { ?>
	<div><h1><?php echo $title; ?></h1></div>
	<?php } ?>
	<?php if ($description) { ?>
	<div><?php echo $description; ?></div>
	<?php } ?>
	<div style="margin-top: 10px;">
	    <?php foreach ($upsell_products as $key=>$upsell_product) { ?>
		    <?php if ($key % 3 == 0) { ?><div style="float: left; margin-top: 20px; width: 100%;"><?php } ?>
		    <div style="float: left; margin-right: 20px;">
		        <?php if ($upsell_product['thumb']) { ?>
		        <div class="image"><img src="<?php echo $upsell_product['thumb']; ?>" /></div>
		        <?php } ?>
		        <div><strong><?php echo $upsell_product['name']; ?></strong></div>
			<?php if ($upsell_product['price']) { ?>
			    <div><?php echo $text_price; ?>
				<?php if (!$upsell_product['special']) { ?>
					<?php echo $upsell_products[0]['price']; ?>
				<?php } else { ?>
				<span style="font-size: 11px; color: #FF0000; text-decoration: line-through;"><?php echo $upsell_product['price']; ?></span> <span style="font-size: 12px;"><?php echo $upsell_product['special']; ?></span>
				<?php } ?>
				<br />
				<?php if ($upsell_product['tax']) { ?>
				<span style="font-size: 10px;"><?php echo $text_tax; ?> <?php echo $upsell_product['tax']; ?></span><br />
				<?php } ?>
			    </div>
			<?php } ?>
			<div style="margin-top:3px;">
				<div><?php echo $text_qty; ?>
				  <input type="text" name="quantity" size="2" value="<?php echo $upsell_product['minimum']; ?>" />
				  <br />
				  <input type="button" value="<?php echo $button_cart; ?>" />
				</div>
			</div>
		    </div>
		    <?php if ($key % 3 + 3 == 0) { ?></div><?php } ?>
	    <?php } ?>
	</div>    
</div>