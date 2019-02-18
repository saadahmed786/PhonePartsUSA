			<footer>
				<?php if ((strrpos($route, "checkout") === false) &&
							   (strrpos($route, "account") === false)) { ?>
				<form action="index.php?route=product/search_mobile" method="post" id="search" class="inline-form module">		
					<fieldset >
						<?php if ($filter_name) { ?>
						<input type="search" name="filter_name" placeholder="<?php echo $filter_name; ?>" />
						<?php } else { ?>
						<input type="search" name="filter_name"  placeholder="<?php echo $text_search; ?>" />
						<?php } ?>				  
						<input type="submit" value="<?php echo $text_search; ?>" />						
					</fieldset>							
				</form>			
				<?php } ?>
				<?php if (isset($categories)) { ?>
				<nav id="secondary">
					<ul class="nav">
						<?php $i = 0; ?>						
						<?php foreach ($categories as $category) { ?>
						<li>
							<a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>						
						</li>
						<?php  if (++$i == 5) break; /*Limit category list to 5 items */?>
						<?php } ?>
					</ul>
					<a href="<?php echo $all_categories; ?>" class="stack"><?php echo $text_all_categories; ?></a> 
				</nav>		
				<?php } ?>																
				<nav id="primary">
					<ul class="nav">
						<li><a href="<?php echo $home; ?>" class="n-home"><?php echo $text_home; ?></a></li>
						<li><a href="<?php echo $info; ?>" class="n-info"><?php echo $text_information; ?></a>
							<?php if (!empty($informations)) { ?>
						    <ul>
								<?php foreach ($informations as $information) { ?>
								<li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
								<?php } ?>
							</ul>
							<?php } ?>
						</li>						
						<li><a href="<?php echo $account; ?>" class="n-account"><?php echo $text_account; ?></a>
							<ul>
								<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
								<li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>								
							</ul>
						</li>						
						<li><a href="<?php echo $contact; ?>" class="n-contact"><?php echo $text_contact; ?></a>
							<ul>
								<li><a href="tel:<?php echo $telephone; ?>"><?php echo $text_call; ?></a></li>
								<li><a href="<?php echo $contact; ?>"><?php echo $text_enquiry; ?></a></li>
								<li><a href="http://maps.google.com/maps?q=<?php echo $address; ?>"><?php echo $text_address; ?></a></li>								
							</ul>
						</li>						
					</ul>	
				</nav>
				<aside id="settings">
					<?php echo $language; ?>
					<?php echo $currency; ?>					
				</aside>				
				<span id="welcome">
					<?php if (!$logged) { ?>
						<?php echo $text_welcome; ?>
					<?php } else { ?>
						<?php echo $text_logged; ?>
					<?php } ?>					
				</span>
				<ul class="tools">					
					<li><a href="<?php echo $_SERVER['REQUEST_URI'] ?>#header"><?php echo $text_top; ?></a></li>										
					<li><?php echo $text_view; ?> <?php echo $text_mobile; ?> / <a href="<?php echo $_SERVER['REQUEST_URI'] . (empty($_SERVER['QUERY_STRING']) ? 'index.php?' : '&');?>view=desktop"><?php echo $text_standard; ?></a></li>
				</ul>
			</footer>
		</div>
		<?php echo $google_analytics; ?>
		<?php if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != '') { ?> 		
		<script src="catalog/view/theme/omf/js/jq.mobi.min.js" type="text/javascript" ></script>		
		<?php } else { ?>
		<script src="http://cdn.jqmobi.com/jq.mobi.min.js" type="text/javascript" ></script>				
		<?php } ?>
		<script>window.$ = window.jq;</script>
		<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jq, document)</script>		 		
		<?php if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/js/script.js')) { ?>
		<script type="text/javascript"  src="<?php echo 'catalog/view/theme/' . $this->config->get('config_template') ?>/js/script.js"></script>
		<?php } else {?>
		<script type="text/javascript" src="catalog/view/theme/omf/js/script.js"></script>
		<?php } ?>		
	</body>
</html>