<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="b">
<li data-role="list-divider"><?php echo $heading_title; ?></li>      
				<?php foreach ($categories as $category) { ?>
                <li>
                  <?php if ($category['category_id'] == $category_id) { ?>
                  <a href="<?php echo $category['href']; ?>" class="active" rel="external"><?php echo $category['name']; ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $category['href']; ?>" rel="external"><?php echo $category['name']; ?></a>
                  <?php } ?>
                  <?php if ($category['children']) { ?>
                  <ul>
                    <?php foreach ($category['children'] as $child) { ?>
                    <li>
                      <?php if ($child['category_id'] == $child_id) { ?>
                      <a href="<?php echo $child['href']; ?>" class="active" rel="external"> - <?php echo $child['name']; ?></a>
                      <?php } else { ?>
                      <a href="<?php echo $child['href']; ?>" rel="external"> - <?php echo $child['name']; ?></a>
                      <?php } ?>
                    </li>
                  <?php } ?>
               </ul>
            <?php } ?>
        </li>
    <?php } ?>
</ul>
