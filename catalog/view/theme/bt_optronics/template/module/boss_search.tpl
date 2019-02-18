<?php
	$filter_category_id = -1; // not to select the category causing issue wth vqmod
?>
<div id="boss-search">
<div class="choose-select">
	<div class="bkg_input_search">
		<input type="text" name="filter_name2" placeholder="<?php echo $entry_search; ?>"  />
	</div>
	<div class="input_cat">
    <select name="filter_category_id">
        <option value="0"><?php echo $text_category; ?></option>
        <?php foreach ($categories as $category_1) { ?>
			<?php if ($category_1['category_id'] == $filter_category_id) { ?>
			<option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
			<?php } else { ?>
			<option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
			<?php } ?>
			<?php foreach ($category_1['children'] as $category_2) { ?>
				<?php if ($category_2['category_id'] == $filter_category_id) { ?>
				<option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
				<?php } else { ?>
				<option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
				<?php } ?>
				<?php foreach ($category_2['children'] as $category_3) { ?>
					<?php if ($category_3['category_id'] == $filter_category_id) { ?>
					<option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
					<?php } ?>
				<?php } ?>
			<?php } ?>
        <?php } ?>
	</select>
	</div>
    <input title="<?php echo $button_search; ?>" type="button" value="<?php echo $button_search; ?>" id="boss-button-search" class="button-search2" />
</div>
</div>