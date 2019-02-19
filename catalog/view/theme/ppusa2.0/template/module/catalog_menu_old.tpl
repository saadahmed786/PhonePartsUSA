<nav class="navbar navbar-default">
	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	<div class="smallLogo">
	<a href="<?php echo $home; ?>"><img src="image/data/0000png/small_logo.png" alt="PPUSA Logo"></a>
	</div>
		<div class="nav-container" style="top: -65px;">
    
        <ul id="nav">
        <?php 
        $nav_i = 1;
        ?>
        <?php foreach ($menu as $nav) : ?>
            
            <?php
            $class='';
            if($nav['name']=='Repair Parts')
            {
                $class = "first";
            }
            else if($nav['name']=='Accessories')
            {
                $class="last";
            }
            ?>
            <li  class="level0 nav-1 <?php echo $class;?> level-top parent"><a href="<?php echo $nav['href']; ?>" class="level-top" <?php echo ($nav['active']=='true'?'style="background-color:#4986FE;color:#FFF"':'') ?> ><span><?php echo $nav['name'];?></span></a>
            <?php if ($nav['subMenu']) : ?>
                <ul class="level0">
                    <?php
                    $sub_nav_i = 1;
                     foreach ($nav['subMenu'] as $k => $subNav) : ?>
                    <li class="level1 nav-1-1"><a href="<?php echo $subNav['href'];?>" onclick="return false;"><span><?php echo $subNav['name'];?></span></a>
                    <?php if ($subNav['subMenu']) : ?>
                        <ul class="level1 scroll3">
                        <?php
                        $sub_sub_nav = 1;
                        foreach ($subNav['subMenu'] as $ki => $subNavki) : ?>
                            <li data-id="" class="level2 nav-<?php echo $nav_i;?>-<?php echo $sub_nav_i;?>-<?php echo $sub_sub_nav;?> <?php echo ($sub_sub_nav==1?'first':'');?> <?php echo ((($ki + 1) == count($subNav['subMenu']))?'last':'') ;?>"><a href="<?php echo $subNavki['href']; ?>"><span><?php echo $subNavki['name']; ?></span></a></li>

                            <?php
                            $sub_sub_nav++;
                            endforeach;?>
                          
                        </ul>
                        <?php
                        endif;
                        ?>
                    </li>
                    <?php
                    $sub_nav_i++;
                    endforeach;?>
                                    
                    
                    
                    
                </ul>
            <?php endif;?>
            </li>
            <?php $nav_i++;?>
        <?php endforeach;?>
          
        </ul>

    
</div>
	</div>
	<!-- /.navbar-collapse -->
</nav>