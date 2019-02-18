<div class="box">
  <div class="box-heading"><h2><?php echo $heading_title; ?></h2></div>
  <div class="category-content">
    <div class="box-category">
      <ul>
        <?php foreach ($categories as $category) { ?>
        <li>
          <?php if ($category['category_id'] == $category_id) { ?>
          	<a href="<?php echo $category['href']; ?>" class="active"><?php echo $category['name']; ?> <?php if($category['children']){ echo "<span class='dropdown'>+</span>";}?></a>
          <?php } else { ?>
          	<a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?> <?php if($category['children']){ echo "<span class='dropdown'>+</span>";}?></a>
          <?php } ?>
          <?php if ($category['children']) { ?>
          <ul>
            <?php foreach ($category['children'] as $child) { ?>
            <li>
              <?php if ($child['category_id'] == $child_id) { ?>
              <a href="<?php echo $child['href']; ?>" class="active">  <?php echo $child['name']; ?></a>
              <?php } else { ?>
              <a href="<?php echo $child['href']; ?>"> <?php echo $child['name']; ?></a>
              <?php } ?>
            </li>
            <?php } ?>
          </ul>
          <?php } ?>
        </li>
        <?php } ?>
      </ul>
    </div>
   
  </div>
</div>
