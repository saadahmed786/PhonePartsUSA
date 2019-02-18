<div class="box featured_category<?php echo $module; ?>">
  <div class="box-heading"><h2><?php echo $heading_title; ?></h2></div>
  <div class="box-content">
  		<ul class="featured-categories">
        	<?php 
				foreach($featured_category_cat as $featured):
					//Category ID
					$cat = $featured['catId'];
					//Category Thumbnail
					$catimg = $featured['catImg'];
					//Category Short Description
					$catdesc = $featured['catDesc'];
			?>
        	<li>
			<?php foreach($categories as $category_1): ?>
                    <?php if($category_1['id'] == $cat) : //Top Level ?>
                            <a href="<?php echo $category_1['href']; ?>"><img src="<?php echo $catimg ; ?>"  /></a>
                            <a href="<?php echo $category_1['href']; ?>" class="parent-cat"><?php echo $category_1['name']; ?></a>
                            <?php if($category_1['children']): ?>
                                <ul class="featured-children">
                                <?php $i=0; foreach($category_1['children'] as $subCat): $i++; //Get Sub Categories ?>
                                    <li><a href="<?php echo $subCat['href']; ?>"><?php echo $subCat['name']; ?></a></li>
                                     <?php if($i==$catdesc){ break; }?>
                                <?php endforeach; ?>            
                                </ul>                  
                            <?php endif; ?>  
                    <?php elseif($category_1['children']): ?>		
                        <?php $i=0; foreach($category_1['children'] as $child) : $i++; ?>
                                
                               <?php if($child['id'] == $cat) : ?>
                                    <a href="<?php echo $child['href']; ?>"><img src="<?php echo $catimg ; ?>"  /></a>									<a href="<?php echo $child['href']; ?>" class="parent-cat"><?php echo $child['name']; ?></a>
                                <?php if($child['children']): ?>
                                    <ul class="featured-children">
                                        <?php foreach($child['children'] as $subCat): 	//Get Sub Categories ?>
                                                <li><a href="<?php echo $subCat['href']; ?>"><?php echo $subCat['name']; ?></a></li>
                                        <?php if($i==$catdesc){ break; }?>
                                        <?php endforeach; ?>
                                    </ul>
								<?php endif; ?>  
                                
                                <?php elseif($child['children']): ?>
                                    <?php foreach($child['children'] as $c) : ?>
                                        <?php if($c['id'] == $cat): ?>
                                             <a href="<?php echo $c['href']; ?>" class="cat-img"><img src="<?php echo $catimg ; ?>"  /></a>
                                             <a href="<?php echo $c['href']; ?>" class="parent-cat"><?php echo $c['name']; ?></a>
                                        <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>                    
                    <?php endif; ?>    
                <?php endforeach; ?>            
        </li>
        <?php endforeach; ?>
        </ul>
  </div>
</div>