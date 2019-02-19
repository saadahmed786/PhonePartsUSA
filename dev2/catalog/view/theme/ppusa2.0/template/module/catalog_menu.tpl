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
            else if($nav['name']=='Blowout')
            {
                $class="last";
            }


            if($nav['active']=='true')
            {
                if($class=='last')
                {
                    $style = 'background-color:red;color:#FFF;';
                }
                else
                {
                $style = 'background-color:#4986FE;color:#FFF;';
                
                }
            }
            else
            {
                if($class=='last')
                {
                //$style = 'color:red;';
                $style = '';
                }
                else
                {
                 $style='';
                }
            }

            ?>
            <li  class="level0 nav-1 <?php echo $class;?> level-top parent"><a href="<?php echo $nav['href']; ?>" class="level-top" style="<?php echo $style;?>"  ><span><?php echo $nav['name'];?></span></a>
            <?php if ($nav['subMenu']) : ?>
                
                <ul class="level0">
                    <?php
                    $sub_nav_i = 1;
                     foreach ($nav['subMenu'] as $k => $subNav) : ?>
                     
                    <li class="level1 nav-1-1"><a href="javascript:void(0)" onclick="return false;"><span><?php echo $k;?></span></a>
                    <?php if ($subNav) : ?>
                        <ul class="level1 scroll3">
                        <?php
                        $sub_sub_nav = 1;
                        foreach ($subNav as $ki => $subNavki) : ?>
                        
                            <li data-id="" class="level2 nav-<?php echo $nav_i;?>-<?php echo $sub_nav_i;?>-<?php echo $sub_sub_nav;?> <?php echo ($subNavki['type']=='red_link'?'first':'');?> <?php echo ((($ki + 1) == count($subNav))?'last':'') ;?>" style="<?php echo (($subNavki['type']=='span')?'margin-top:7px;':'') ;?>"><a <?php if($subNavki['type']!='span') { echo 'href="'.$subNavki['href'].'"';} ?>><span style="<?php echo (($subNavki['type']=='span')?'color:#000;font-weight:bold;cursor:text':'') ;?>"><?php echo $subNavki['name']; ?></span></a></li>

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