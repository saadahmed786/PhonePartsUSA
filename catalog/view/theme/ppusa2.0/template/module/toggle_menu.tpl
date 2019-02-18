<style type="text/css">
.dropdown-backdrop{
	display:none !important;
}

.overlay{
	cursor: pointer;
}
</style>
<div >
        <div class="overlay"></div>
        <div class="overlay-right"></div>

    
        <!-- Sidebar -->
        <nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
            <ul class="nav sidebar-nav">
            <?php foreach ($menu as $key=> $nav) : ?>
							<li id="aa<?php echo $key ?>" <?php echo ($nav['subMenu'])? 'class=""': ''; ?> >
							<?php if($nav['subMenu']) { ?>
								<a  <?php echo ($nav['subMenu'])? 'onClick="mainToggle(this,event)" ': ''; ?>  ><?php echo $nav['name']?> <?php echo ($nav['subMenu'])? '<span class="caret"></span>': ''; ?></a>
								<?php }else{ ?>
								<a href="<?php echo $nav['href']; ?>"   ><?php echo $nav['name']?></a>
								<?php } ?>
								<?php if ($nav['subMenu']) : ?>
									<ul class="dropdown-menu "  role="menu" >

										<?php foreach ($nav['subMenu'] as $k => $subNav) : ?>
											<?php if (($k + 1) == count($nav['subMenu'])) { ?>
											<!-- <li role="separator" class="divider"></li> -->
											<?php } ?>
											<li class="dropdown" <?php echo ($subNav['subMenu'])? '': ''; ?> >
												<?php if ($subNav['subMenu']) : ?>
												<a onClick="toggleList(this,event)"  > <?php echo $subNav['name']; ?> <span class="caret"></span></a>
												<?php
												else:
													?>
												<a  href="<?php echo $subNav['href']; ?>" > <?php echo $subNav['name']; ?></a>
												<?php
												endif;
												?>
												<?php if ($subNav['subMenu']) : ?>
													<ul id='<?php echo $k  ?>al' class="dropdown-menu " role="menu">
														<?php foreach ($subNav['subMenu'] as $ki => $subNavki) : ?>
															<?php if (($ki + 1) == count($subNav['subMenu'])) { ?>
															<!-- <li role="separator" class="divider"></li> -->
															<?php } ?>
															<li><a href="<?php echo $subNavki['href']; ?>"><?php echo $subNavki['name']; ?></a></li>
														<?php endforeach; ?>
													</ul>
												<?php endif; ?>
											</li>
							<?php endforeach; ?>
									</ul>
								<?php endif; ?>

							</li>
						<?php endforeach; ?>
						<li role="separator" class="divider" style="height: 1px; margin: 9px 0; overflow: hidden; background-color: #e5e5e5;"></li>
                <!-- <li ><a href="<?php echo $this->url->link('misc/comingsoon'); ?>" >Repair Guides </a></li> -->
								<li ><a style="color:#ddd" href="<?php echo $this->url->link('account/account'); ?>" ><?php echo ($this->customer->isLogged()?'Account':'Login');?> </a></li>
								<li ><a style="color:#ddd" href="<?php echo $this->url->link('buyback/buyback'); ?>" >LCD Buy Back</a></li>
								<li ><a style="color:#ddd" href="<?php echo $this->url->link('information/information','information_id=3'); ?>" >Return Policy</a></li>
								<li ><a style="color:#ddd" href="<?php echo $this->url->link('information/information','information_id=6'); ?>" >Shipping Rates</a></li>
								<li ><a style="color:#ddd" href="<?php echo $this->url->link('information/contact'); ?>" >Support</a></li>
            </ul>
        </nav>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <button type="button" class="hamburger is-closed <?php echo ($page_class == 'checkout_checkout'?'hidden':'');?>" data-toggle="offcanvas">
                <span class="hamb-top"></span>
    			<span class="hamb-middle"></span>
				<span class="hamb-bottom"></span>
            </button>
            <!-- <i class="fa fa-bars fa-2x" style="color:#9a9a9a;font-size:1.6666em" aria-hidden="true" data-toggle="offcanvas"></i> -->
            <div>
                <!-- Logo & Menus -->
						  	<div class="mob-logo-menus">
								<div class="container" style="    margin-top: -65px;">
									
							

									<div class="row">
										<!-- Logo -->
										<div class="<?php echo ($page_class == 'checkout_checkout'?'col-xs-8':'col-xs-6');?>">
											<a href="<?php echo $home; ?>" class="logo"><img src="image/logo_new.png" title="<?php echo $name; ?>" alt="<?php echo $name; ?>"></a>
										</div>
										<!-- End Logo -->
										
										<!-- Info & Cart -->
										<div class="<?php echo ($page_class == 'checkout_checkout'?'col-xs-4':'col-xs-6');?>">
											<div class="row">
												<!-- Call & Account -->
												<div class="col-xs-8 mob-top-info">
													<ul>
														<li><a href="tel:855.213.5588" class="<?php echo ($page_class == 'checkout_checkout'?'hidden':'');?>"><img src="catalog/view/theme/ppusa2.0/images/icons/mob-call-ico.png" alt=""></a></li>
														<li>
															<a href="<?php echo (!$this->customer->isLogged()?$this->url->link('account/account'):'javascript:void(0);');?>" class="top-inquery top-dropdown-icon <?php echo ($page_class == 'checkout_checkout'?'hidden':'');?>"><img src="catalog/view/theme/ppusa2.0/images/icons/<?php echo ($this->customer->isLogged() ?'mob-account-ico-blue.png':'mob-account-ico.png');?>" alt=""></a>
															<?php
															if($this->customer->isLogged())
															{

															?>
															<div class="top-dropdowns account-login text-left">
																<div class="caret-up"></div>
																<div class="top-dropdown-inner">
															<p>
																<button class="btn btn-info btn-full light btn-big" onclick="javascript:window.location='<?php echo $this->url->link('account/account');?>'"><img src="catalog/view/theme/ppusa2.0/images/icons/person-edit.png" alt="">Account Control Center</button>
															</p>
															
															<br>
															<button class="btn btn-green btn-full btn-big" onclick="javascript:window.location='<?php echo $this->url->link('account/logout');?>'"><i class="fa fa-power-off"></i> Sign Out of <?php echo $this->customer->getFirstName();?>'s Account</button>
															
														</div>
															</div>
															<?php
														}
														?>
														</li>
													</ul>
												</div>
												<!-- End Call & Account -->
												
												<!-- Cart -->
												<div class="col-xs-4">
													<div class="cart-nav">
													   <div class="cart-icon">
				                              <a href="<?php echo $this->url->link('checkout/cart');?>">
				                                 
				                                 <span class="icon icon-cart"></span>
				                              </a>
				                           </div>
													   <div class="header_cart_icon">
														  <?php echo $cart; ?>
													   </div>
													</div>
												</div>
												<!-- End Cart -->
											</div>
										</div>
										<!-- End Info & Cart -->
									</div>

											<?php

				$oc_today = strtotime(date('Y-m-d'));
				$oc_start = strtotime($notice['start_date']);
				$oc_end = strtotime($notice['finish_date']);

				if($oc_today>=$oc_start && $oc_today<=$oc_end)
				{
					if($notice['show']=='on' && $notice['notification'])
					{
				?>
				<div class="row" style="font-size:11px">
					
					<div class="col-xs-12" style="text-align:center;color:red;font-weight:bold"><?php echo $notice['notification'];?></div>
					

				</div>
				<?php
					}
				}
				?>



								</div>
								
								<!-- Menus -->
								<?php //echo $toggle_menu;?>
								<!-- End Menus -->
							</div>
							<!-- End Logo & Menus -->

							<!-- Track & Search -->
							<div class="mob-track-search <?php echo ($page_class == 'checkout_checkout'?'hidden':'');?>">
								<div class="container">
									<div class="row">
								
										<div class="col-xs-12 mob-srch-area">
											<div class="site-search input-group">
												<?php $filtering_name_mob = $_GET['filter_name']; ?>
												 <input type="text" id="search_inp_mob" class="input" value="<?php echo $filtering_name_mob; ?>" placeholder="Search Catalog">
                           <a href="javascript:void();" id="search_inp_button_mob" onclick="searcher_mob();"><i class="fa fa-search"></i></a>
											</div>
										</div>
										
									</div>
								</div>
							</div>
							<!-- End Track & Search -->
            </div>
            </div>
        </div>
             <script type="text/javascript">
             	$('#search_inp_mob').keypress(function (e) {
             		var key = e.which;
					 if(key == 13)
						{
						$('#search_inp_button_mob').click();
						return false;  
					 }
					});
             	function mainToggle(el,e)
             	{
             		e.stopPropagation();
             		if($(el).next().hasClass('dropdown-menu'))
             		{
             			$(el).next().toggle('normal')
             		}
             	}

             	function toggleList(el,e) {
             		e.stopPropagation();
             		$(el).parent().find('ul').toggle('normal');
             		if($(el).hasClass('color-style'))
             		{
             			$(el).removeClass('color-style');
             			$(el).addClass('color-style-none');

             		}
             		else
             		{
             			$(el).removeClass('color-style-none');
             			$(el).addClass('color-style');
             		}
             	}

             	function searcher_mob() {
					url = 'index.php?route=product/search';

					var filter_name = $('#search_inp_mob').val();

					if (filter_name) {
						url += '&filter_name=' + encodeURIComponent(filter_name);
					}

					location = url;
				}

				// $('body').on('click touch ', '.overlay',function(evt){   
			 //    	alert("hello");
			 //        $('[data-toggle="offcanvas"]').click();
				// });

				$('.overlay').click(function()
				{
					$('[data-toggle="offcanvas"]').click();
				})
             </script>