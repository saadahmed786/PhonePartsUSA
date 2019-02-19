<div class="content">
	<?php if ($title) { ?>
	<div><h1><?php echo $title; ?></h1></div>
	<?php } ?>
	<?php if ($description) { ?>
	<div><?php echo $description; ?></div>
	<?php } ?>
	<div style="margin-top: 10px;">
	    <?php if ($upsell_products[0]['thumb']) { ?>
	    <div style="float: left;">
	      <div class="image"><img src="<?php echo $upsell_products[0]['thumb']; ?>" /></div>
	    </div>
	    <?php } ?>
	    <div style="float: left;">
		<div style="margin: 10px 0 10px 0; font-size: 13px;"><strong><?php echo $upsell_products[0]['name']; ?></strong></div>
		<?php if ($upsell_products[0]['price']) { ?>
		    <div><?php echo $text_price; ?>
			<?php if (!$upsell_products[0]['special']) { ?>
				<?php echo $upsell_products[0]['price']; ?>
			<?php } else { ?>
			<span style="font-size: 11px; color: #FF0000; text-decoration: line-through;"><?php echo $upsell_products[0]['price']; ?></span> <span style="font-size: 12px;"><?php echo $upsell_products[0]['special']; ?></span>
			<?php } ?>
			<br />
			<?php if ($upsell_products[0]['tax']) { ?>
			<span style="font-size: 10px;"><?php echo $text_tax; ?> <?php echo $upsell_products[0]['tax']; ?></span><br />
			<?php } ?>
		    </div>
		<?php } ?>
		<div style="margin-top: 10px;">
			<div><?php echo $text_qty; ?>
			  <input type="text" name="quantity" size="2" value="<?php echo $upsell_products[0]['minimum']; ?>" />
			  &nbsp;
			  <input type="button" value="<?php echo $button_cart; ?>" id="button-cart" class="button" />
			</div>
			<?php if ($upsell_products[0]['minimum'] > 1) { ?>
			<div class="minimum"><?php echo $upsell_products[0]['text_minimum']; ?></div>
			<?php } ?>
		</div>
	    </div>
	</div>    
</div>