<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning): ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php endif ?>
  <?php if ($success): ?>
  <div class="success"><?php echo $success; ?></div>
  <?php endif; ?>

<!-- Theme Options -->

<div class="set-size" id="theme-options">

	<h3>MegaStore Theme Options</h3>
	
	<!-- Content -->
	
	<div class="content">
	
		<div>
		
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">	
				
				<!-- Tabs -->
				
				<div class="bg-tabs">
				
					<!-- General, Design, Footer TABS -->
					
					<div id="tabs" class="htabs main-tabs">
					
						<a href="#tab_general" id="general"><span>General</span></a>
						<a href="#tab_design" id="design"><span>Design</span></a>
						<a href="#tab_footer" id="tfooter"><span>Footer</span></a>
						<a href="#tab_payment" id="tpayment"><span>Payment</span></a>
					
					</div>
					
					<!-- End General, Design Footer Tabs -->
					
					<!-- General -->
					
					<div id="tab_general" class="tab-content">
					
						<!-- Status -->
						
						<?php if($general_status == 1) { echo '<div class="status status-on" title="1" rel="general_status"></div>'; } else { echo '<div class="status status-off" title="0" rel="general_status"></div>'; } ?>
						
						<input name="general_status" value="<?php echo $general_status; ?>" id="general_status" type="hidden" />
						
						<!-- Float General -->
						
						<div id="general" style="float:left;width:330px">
						
						<!-- Title -->
						
						<h4>General settings</h4>
						
						<!-- Input -->
						
						<div class="input">
						
							<p>Product per pow</p>
							<select name="product_per_pow">
							
								<option value="0"<?php if($product_per_pow =='0'){echo ' selected="selected"';} ?>>4 (6 in full width)</option>
								<option value="1"<?php if($product_per_pow =='1'){echo ' selected="selected"';} ?>>3 (4 in full width)</option>
							
							</select>
						
							<div class="clear"></div>
						
						</div>
						
						<!-- End Input -->
						
						<!-- Input -->
						
						<div class="input">
						
							<p>Column position</p>
							<select name="column_position">
							
								<option value="0"<?php if($column_position =='0'){echo ' selected="selected"';} ?>>Left</option>
								<option value="1"<?php if($column_position =='1'){echo ' selected="selected"';} ?>>Right</option>
							
							</select>
						
							<div class="clear"></div>
						
						</div>
						
						<!-- End Input -->
						
 						<!-- Input -->	
						
						<div class="input">
						
							<p>Refine search style</p>
							<select name="refine_search_style">
							
			              <option value="0" <?php if($refine_search_style =='0'){echo ' selected="selected"';} ?>>With images</option>
			              <option value="1" <?php if($refine_search_style =='1'){echo ' selected="selected"';} ?>>Text only</option>
			              <option value="2" <?php if($refine_search_style =='2'){echo ' selected="selected"';} ?>>Disable</option>
							
							</select>
						
							<div class="clear"></div>
						
						</div>
						
						<!-- End Input -->
						
						<!-- Input -->
						
						<div class="input">
						
							<p>Default list/grid</p>
							<select name="default_list_grid">
							
			              <option value="0" <?php if($default_list_grid =='0'){echo ' selected="selected"';} ?>>List</option>
			              <option value="1" <?php if($default_list_grid =='1'){echo ' selected="selected"';} ?>>Grid</option>
							
							</select>
						
							<div class="clear"></div>
						
						</div>
						
						<!-- End Input -->
						
						<!-- Input -->
						
						<div class="input">
						
							<p>Slideshow speed</p>
							<select name="slideshow_speed">
							
			              <option value="1" <?php if($slideshow_speed =='1'){echo ' selected="selected"';} ?>>1000</option>
			              <option value="2" <?php if($slideshow_speed =='2'){echo ' selected="selected"';} ?>>2000</option>
			              <option value="3" <?php if($slideshow_speed =='3'){echo ' selected="selected"';} ?>>3000</option>
			              <option value="4" <?php if($slideshow_speed =='4' || $slideshow_speed < 1){echo ' selected="selected"';} ?>>4000</option>
			              <option value="5" <?php if($slideshow_speed =='5'){echo ' selected="selected"';} ?>>5000</option>
			              <option value="6" <?php if($slideshow_speed =='6'){echo ' selected="selected"';} ?>>6000</option>
			              <option value="7" <?php if($slideshow_speed =='7'){echo ' selected="selected"';} ?>>7000</option>
			              <option value="8" <?php if($slideshow_speed =='8'){echo ' selected="selected"';} ?>>8000</option>
			              <option value="9" <?php if($slideshow_speed =='9'){echo ' selected="selected"';} ?>>9000</option>
			              <option value="10" <?php if($slideshow_speed =='10'){echo ' selected="selected"';} ?>>10000</option>
							
							</select>
						
							<div class="clear"></div>
						
						</div>
						
						<!-- End Input -->
						
						<!-- Input -->
						
						<div class="input">
						
							<p>Layout Type</p>
							<select name="layout_type">
							
			              <option value="0" <?php if($layout_type =='0'){echo ' selected="selected"';} ?>>Full width</option>
			              <option value="1" <?php if($layout_type =='1'){echo ' selected="selected"';} ?>>Fixed</option>
							
							</select>
						
							<div class="clear"></div>
						
						</div>
						
						<!-- End Input -->
						
						</div>
						
						<!-- End Float General -->
						
						<!-- Functions -->
						
						<div class="functions">
						
							<h4>Functions</h4>
						
							<!-- Input -->
							
							<div class="input">
								
								<p>Ex. Tax price</p>
								<?php if($ex_tax_price == 0 && $ex_tax_price != '') { echo '<div class="status status-off" title="0" rel="ex_tax_price"></div>'; } else { echo '<div class="status status-on" title="1" rel="ex_tax_price"></div>'; } ?>
								
								<input name="ex_tax_price" value="<?php echo $ex_tax_price; ?>" id="ex_tax_price" type="hidden" />
								
								<div class="clear"></div>
								
							</div>
							
							<!-- End Input -->
							<!-- Input -->
							
							<div class="input">
								
								<p>Reward points</p>
								<?php if($reward_points == 0 && $reward_points != '') { echo '<div class="status status-off" title="0" rel="reward_points"></div>'; } else { echo '<div class="status status-on" title="1" rel="reward_points"></div>'; } ?>
								
								<input name="reward_points" value="<?php echo $reward_points; ?>" id="reward_points" type="hidden" />
								
								<div class="clear"></div>
								
							</div>
							
							<!-- End Input -->
							<!-- Input -->
							
							<div class="input">
								
								<p>Reviews</p>
								<?php if($reviews == 0 && $reviews != '') { echo '<div class="status status-off" title="0" rel="reviews"></div>'; } else { echo '<div class="status status-on" title="1" rel="reviews"></div>'; } ?>
								
								<input name="reviews" value="<?php echo $reviews; ?>" id="reviews" type="hidden" />
								
								<div class="clear"></div>
								
							</div>
							
							<!-- End Input -->
							<!-- Input -->
							
							<div class="input">
								
								<p>Product social share</p>
								<?php if($product_social_share == 0 && $product_social_share != '') { echo '<div class="status status-off" title="0" rel="product_social_share"></div>'; } else { echo '<div class="status status-on" title="1" rel="product_social_share"></div>'; } ?>
								
								<input name="product_social_share" value="<?php echo $product_social_share; ?>" id="product_social_share" type="hidden" />
								
								<div class="clear"></div>
								
							</div>
							
							<!-- End Input -->
							<!-- Input -->
							
							<div class="input">
								
								<p>Animation hover effect</p>
								<?php if($animation_hover_effect == 0 && $animation_hover_effect != '') { echo '<div class="status status-off" title="0" rel="animation_hover_effect"></div>'; } else { echo '<div class="status status-on" title="1" rel="animation_hover_effect"></div>'; } ?>
								
								<input name="animation_hover_effect" value="<?php echo $animation_hover_effect; ?>" id="animation_hover_effect" type="hidden" />
								
								<div class="clear"></div>
								
							</div>
							
							<!-- End Input -->
						
						</div>
						
						<!-- End Functions -->
				
					</div>
					
					<!-- End General -->
					
					<!-- Design -->
					
					<div id="tab_design" class="tab-content2">
					
						<!-- Font, colors, background TABS -->
						
						<div id="tabs_design" class="htabs tabs-design">
						
							<a href="#tab_font" id="tfont"><span>Font</span></a>
							<a href="#tab_colors" id="tcolors"><span>Colors</span></a>
							<a href="#tab_background" id="tbackground"><span>Background</span></a>
						
						</div>
						
						<!-- Font, colors, background -->
						
						<!-- Font -->
					
						<div id="tab_font" class="tab-content">
						
							<!-- Status -->
							
							<?php if($font_status == 1) { echo '<div class="status status-on" title="1" rel="font_status"></div>'; } else { echo '<div class="status status-off" title="0" rel="font_status"></div>'; } ?>
							
							<input name="font_status" value="<?php echo $font_status; ?>" id="font_status" type="hidden" />
							
							<!-- Title -->
							
							<h4>Font settings</h4>
							
							<!-- Input -->
							
							<div class="input">
							
								<p>Body Font</p>
								<select name="body_font">
								
		              <?php if (isset($body_font)) {
		              $selected = "selected";
		              ?>
		              <option value="standard" <?php if($body_font=='standard'){echo ' selected="selected"';} ?>>Standard</option>
		              <option value="Arial" <?php if($body_font=='Arial'){echo ' selected="selected"';} ?>>Arial</option>
		              <option value="Verdana" <?php if($body_font=='Verdana'){echo ' selected="selected"';} ?>>Verdana</option>
		              <option value="Helvetica" <?php if($body_font=='Helvetica'){echo ' selected="selected"';} ?>>Helvetica</option>
		              
		              <option value="Lucida Grande" <?php if($body_font=='Lucida Grande'){echo ' selected="selected"';} ?>>Lucida Grande</option>
		              <option value="Trebuchet MS" <?php if($body_font=='Trebuchet MS'){echo ' selected="selected"';} ?>>Trebuchet MS</option>
		              <option value="Times New Roman" <?php if($body_font=='Times New Roman'){echo ' selected="selected"';} ?>>Times New Roman</option>
		              <option value="Tahoma" <?php if($body_font=='Tahoma'){echo ' selected="selected"';} ?>>Tahoma</option>
		              <option value="Georgia" <?php if($body_font=='Georgia'){echo ' selected="selected"';} ?>>Georgia</option>
		                           
		              <?php } else { ?>
		              <option value="standard" selected="selected">Standard</option>
		              <option value="Arial">Arial</option>
		              <option value="Verdana">Verdana</option>    
		           <option value="Helvetica">Helvetica</option>
		              <option value="Lucida Grande">Lucida Grande</option>
		             <option value="Trebuchet MS">Trebuchet MS</option>
		            <option value="Times New Roman">Times New Roman</option>
		             <option value="Tahoma">Tahoma</option>
		            <option value="Georgia">Georgia</option>
		              
		              <?php } ?>
								
								</select>
								<select name="body_font_px" style="width:80px;margin-right:25px">
								
									<?php for( $x = 9; $x <= 50; $x++ ) { ?>
					              <option value="<?php echo $x; ?>" <?php if($body_font_px==$x || ($x == 12 && $body_font_px < 6)){echo ' selected="selected"';} ?>><?php echo $x; ?> px</option>
									<?php } ?>
								
								</select>
								<p style="width:60px">Smaller</p>
								<select name="body_font_smaller_px" style="width:80px;margin-right:35px">
								
									<?php for( $x = 9; $x <= 50; $x++ ) { ?>
					              <option value="<?php echo $x; ?>" <?php if($body_font_smaller_px==$x || ($x == 11 && $body_font_smaller_px < 6)){echo ' selected="selected"';} ?>><?php echo $x; ?> px</option>
									<?php } ?>
								
								</select>
							
								<div class="clear"></div>
							
							</div>
							
							<!-- End Input -->
							
							<!-- Input -->
							
							<div class="input">
							
								<p>Categories bar</p>
								<select name="categories_bar">
								
									              <?php if (isset($categories_bar)) {
									              $selected = "selected"; }
									              ?>
									<option value="standard" <?php if($categories_bar=='standard'){echo ' selected="selected"';} ?>>Standard</option>
									<option value="Francois+One" <?php if($categories_bar=='Francois+One'){echo ' selected="selected"';} ?>>Francois One</option>
											  <option value="Arial" <?php if($categories_bar=='Arial'){echo ' selected="selected"';} ?>>Arial</option>
									<option value="Aclonica" <?php if($categories_bar=='Aclonica'){echo ' selected="selected"';} ?>>Aclonica</option>
									<option value="Allan" <?php if($categories_bar=='Allan'){echo ' selected="selected"';} ?>>Allan</option>
									<option value="Annie+Use+Your+Telescope" <?php if($categories_bar=='Annie+Use+Your+Telescope'){echo ' selected="selected"';} ?>>Annie Use Your Telescope</option>
									<option value="Anonymous+Pro" <?php if($categories_bar=='Anonymous+Pro'){echo ' selected="selected"';} ?>>Anonymous Pro</option>
									<option value="Allerta+Stencil" <?php if($categories_bar=='Allerta+Stencil'){echo ' selected="selected"';} ?>>Allerta Stencil</option>
									<option value="Allerta" <?php if($categories_bar=='Allerta'){echo ' selected="selected"';} ?>>Allerta</option>
									<option value="Amaranth" <?php if($categories_bar=='Amaranth'){echo ' selected="selected"';} ?>>Amaranth</option>
									<option value="Anton" <?php if($categories_bar=='Anton'){echo ' selected="selected"';} ?>>Anton</option>
									<option value="Architects+Daughter" <?php if($categories_bar=='Architects+Daughter'){echo ' selected="selected"';} ?>>Architects Daughter</option>
									<option value="Arimo" <?php if($categories_bar=='Arimo'){echo ' selected="selected"';} ?>>Arimo</option>
									<option value="Artifika" <?php if($categories_bar=='Artifika'){echo ' selected="selected"';} ?>>Artifika</option>
									<option value="Arvo" <?php if($categories_bar=='Arvo'){echo ' selected="selected"';} ?>>Arvo</option>
									<option value="Asset" <?php if($categories_bar=='Asset'){echo ' selected="selected"';} ?>>Asset</option>
									<option value="Astloch" <?php if($categories_bar=='Astloch'){echo ' selected="selected"';} ?>>Astloch</option>
									<option value="Bangers" <?php if($categories_bar=='Bangers'){echo ' selected="selected"';} ?>>Bangers</option>
									<option value="Bentham" <?php if($categories_bar=='Bentham'){echo ' selected="selected"';} ?>>Bentham</option>
									<option value="Bevan" <?php if($categories_bar=='Bevan'){echo ' selected="selected"';} ?>>Bevan</option>
									<option value="Bigshot+One" <?php if($categories_bar=='Bigshot+One'){echo ' selected="selected"';} ?>>Bigshot One</option>
									<option value="Bowlby+One" <?php if($categories_bar=='Bowlby+One'){echo ' selected="selected"';} ?>>Bowlby One</option>
									<option value="Bowlby+One+SC" <?php if($categories_bar=='Bowlby+One+SC'){echo ' selected="selected"';} ?>>Bowlby One SC</option>
									<option value="Brawler" <?php if($categories_bar=='Brawler'){echo ' selected="selected"';} ?>>Brawler </option>
									<option value="Buda" <?php if($categories_bar=='Buda'){echo ' selected="selected"';} ?>>Buda</option>
									<option value="Cabin" <?php if($categories_bar=='Cabin'){echo ' selected="selected"';} ?>>Cabin</option>
									<option value="Calligraffitti" <?php if($categories_bar=='Calligraffitti'){echo ' selected="selected"';} ?>>Calligraffitti</option>
									<option value="Candal" <?php if($categories_bar=='Candal'){echo ' selected="selected"';} ?>>Candal</option>
									<option value="Cantarell" <?php if($categories_bar=='Cantarell'){echo ' selected="selected"';} ?>>Cantarell</option>
									<option value="Cardo" <?php if($categories_bar=='Cardo'){echo ' selected="selected"';} ?>>Cardo</option>
									<option value="Carter One" <?php if($categories_bar=='Carter One'){echo ' selected="selected"';} ?>>Carter One</option>
									<option value="Caudex" <?php if($categories_bar=='Caudex'){echo ' selected="selected"';} ?>>Caudex</option>
									<option value="Cedarville+Cursive" <?php if($categories_bar=='Cedarville+Cursive'){echo ' selected="selected"';} ?>>Cedarville Cursive</option>
									<option value="Cherry+Cream+Soda" <?php if($categories_bar=='Cherry+Cream+Soda'){echo ' selected="selected"';} ?>>Cherry Cream Soda</option>
									<option value="Chewy" <?php if($categories_bar=='Chewy'){echo ' selected="selected"';} ?>>Chewy</option>
									<option value="Coda" <?php if($categories_bar=='Coda'){echo ' selected="selected"';} ?>>Coda</option>
									<option value="Coming+Soon" <?php if($categories_bar=='Coming+Soon'){echo ' selected="selected"';} ?>>Coming Soon</option>
									<option value="Copse" <?php if($categories_bar=='Copse'){echo ' selected="selected"';} ?>>Copse</option>
									<option value="Corben" <?php if($categories_bar=='Corben'){echo ' selected="selected"';} ?>>Corben</option>
									<option value="Cousine" <?php if($categories_bar=='Cousine'){echo ' selected="selected"';} ?>>Cousine</option>
									<option value="Covered+By+Your+Grace" <?php if($categories_bar=='Covered+By+Your+Grace'){echo ' selected="selected"';} ?>>Covered By Your Grace</option>
									<option value="Crafty+Girls" <?php if($categories_bar=='Crafty+Girls'){echo ' selected="selected"';} ?>>Crafty Girls</option>
									<option value="Crimson+Text" <?php if($categories_bar=='Crimson+Text'){echo ' selected="selected"';} ?>>Crimson Text</option>
									<option value="Crushed" <?php if($categories_bar=='Crushed'){echo ' selected="selected"';} ?>>Crushed</option>
									<option value="Cuprum" <?php if($categories_bar=='Cuprum'){echo ' selected="selected"';} ?>>Cuprum</option>
									<option value="Damion" <?php if($categories_bar=='Damion'){echo ' selected="selected"';} ?>>Damion</option>
									<option value="Dancing+Script" <?php if($categories_bar=='Dancing+Script'){echo ' selected="selected"';} ?>>Dancing Script</option>
									<option value="Dawning+of+a+New+Day" <?php if($categories_bar=='Dawning+of+a+New+Day'){echo ' selected="selected"';} ?>>Dawning of a New Day</option>
									<option value="Didact+Gothic" <?php if($categories_bar=='Didact+Gothic'){echo ' selected="selected"';} ?>>Didact Gothic</option>
									<option value="Droid+Sans" <?php if($categories_bar=='Droid+Sans'){echo ' selected="selected"';} ?>>Droid Sans</option>
									<option value="Droid+Sans+Mono" <?php if($categories_bar=='Droid+Sans+Mono'){echo ' selected="selected"';} ?>>Droid Sans Mono</option>
									<option value="Droid+Serif" <?php if($categories_bar=='Droid+Serif'){echo ' selected="selected"';} ?>>Droid Serif</option>
									<option value="EB+Garamond" <?php if($categories_bar=='EB+Garamond'){echo ' selected="selected"';} ?>>EB Garamond</option>
									<option value="Expletus+Sans" <?php if($categories_bar=='Expletus+Sans'){echo ' selected="selected"';} ?>>Expletus Sans</option>
									<option value="Fontdiner+Swanky" <?php if($categories_bar=='Fontdiner+Swanky'){echo ' selected="selected"';} ?>>Fontdiner Swanky</option>
									<option value="Forum" <?php if($categories_bar=='Forum'){echo ' selected="selected"';} ?>>Forum</option>
									<option value="Geo" <?php if($categories_bar=='Geo'){echo ' selected="selected"';} ?>>Geo</option>
									<option value="Give+You+Glory" <?php if($categories_bar=='Give+You+Glory'){echo ' selected="selected"';} ?>>Give You Glory</option>
									<option value="Goblin+One" <?php if($categories_bar=='Goblin+One'){echo ' selected="selected"';} ?>>Goblin One</option>
									<option value="Goudy+Bookletter+1911" <?php if($categories_bar=='Goudy+Bookletter+1911'){echo ' selected="selected"';} ?>>Goudy Bookletter 1911</option>
									<option value="Gravitas+One" <?php if($categories_bar=='Gravitas+One'){echo ' selected="selected"';} ?>>Gravitas One</option>
									<option value="Gruppo" <?php if($categories_bar=='Gruppo'){echo ' selected="selected"';} ?>>Gruppo</option>
									<option value="Hammersmith+One" <?php if($categories_bar=='Hammersmith+One'){echo ' selected="selected"';} ?>>Hammersmith One</option>
									<option value="Holtwood+One+SC" <?php if($categories_bar=='Holtwood+One+SC'){echo ' selected="selected"';} ?>>Holtwood One SC</option>
									<option value="Homemade+Apple" <?php if($categories_bar=='Homemade+Apple'){echo ' selected="selected"';} ?>>Homemade Apple</option>
									<option value="Inconsolata" <?php if($categories_bar=='Inconsolata'){echo ' selected="selected"';} ?>>Inconsolata</option>
									<option value="Indie+Flower" <?php if($categories_bar=='Indie+Flower'){echo ' selected="selected"';} ?>>Indie Flower</option>
									<option value="IM+Fell+DW+Pica" <?php if($categories_bar=='IM+Fell+DW+Pica'){echo ' selected="selected"';} ?>>IM Fell DW Pica</option>
									<option value="IM+Fell+DW+Pica+SC" <?php if($categories_bar=='IM+Fell+DW+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell DW Pica SC</option>
									<option value="IM+Fell+Double+Pica" <?php if($categories_bar=='IM+Fell+Double+Pica'){echo ' selected="selected"';} ?>>IM Fell Double Pica</option>
									<option value="IM+Fell+Double+Pica+SC" <?php if($categories_bar=='IM+Fell+Double+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell Double Pica SC</option>
									<option value="IM+Fell+English" <?php if($categories_bar=='IM+Fell+English'){echo ' selected="selected"';} ?>>IM Fell English</option>
									<option value="IM+Fell+English+SC" <?php if($categories_bar=='IM+Fell+English+SC'){echo ' selected="selected"';} ?>>IM Fell English SC</option>
									<option value="IM+Fell+French+Canon" <?php if($categories_bar=='IM+Fell+French+Canon'){echo ' selected="selected"';} ?>>IM Fell French Canon</option>
									<option value="IM+Fell+French+Canon+SC" <?php if($categories_bar=='IM+Fell+French+Canon+SC'){echo ' selected="selected"';} ?>>IM Fell French Canon SC</option>
									<option value="IM+Fell+Great+Primer" <?php if($categories_bar=='IM+Fell+Great+Primer'){echo ' selected="selected"';} ?>>IM Fell Great Primer</option>
									<option value="IM+Fell+Great+Primer+SC" <?php if($categories_bar=='IM+Fell+Great+Primer+SC'){echo ' selected="selected"';} ?>>IM Fell Great Primer SC</option>
									<option value="Irish+Grover" <?php if($categories_bar=='Irish+Grover'){echo ' selected="selected"';} ?>>Irish Grover</option>
									<option value="Irish+Growler" <?php if($categories_bar=='Irish+Growler'){echo ' selected="selected"';} ?>>Irish Growler</option>
									<option value="Istok+Web" <?php if($categories_bar=='Istok+Web'){echo ' selected="selected"';} ?>>Istok Web</option>
									<option value="Josefin+Sans" <?php if($categories_bar=='Josefin+Sans'){echo ' selected="selected"';} ?>>Josefin Sans Regular 400</option>
									<option value="Josefin+Slab" <?php if($categories_bar=='Josefin+Slab'){echo ' selected="selected"';} ?>>Josefin Slab Regular 400</option>
									<option value="Judson" <?php if($categories_bar=='Judson'){echo ' selected="selected"';} ?>>Judson</option>
									<option value="Jura" <?php if($categories_bar=='Jura'){echo ' selected="selected"';} ?>> Jura Regular</option>
									<option value="Just+Another+Hand" <?php if($categories_bar=='Just+Another+Hand'){echo ' selected="selected"';} ?>>Just Another Hand</option>
									<option value="Just+Me+Again+Down+Here" <?php if($categories_bar=='Just+Me+Again+Down+Here'){echo ' selected="selected"';} ?>>Just Me Again Down Here</option>
									<option value="Kameron" <?php if($categories_bar=='Kameron'){echo ' selected="selected"';} ?>>Kameron</option>
									<option value="Kenia" <?php if($categories_bar=='Kenia'){echo ' selected="selected"';} ?>>Kenia</option>
									<option value="Kranky" <?php if($categories_bar=='Kranky'){echo ' selected="selected"';} ?>>Kranky</option>
									<option value="Kreon" <?php if($categories_bar=='Kreon'){echo ' selected="selected"';} ?>>Kreon</option>
									<option value="Kristi" <?php if($categories_bar=='Kristi'){echo ' selected="selected"';} ?>>Kristi</option>
									<option value="La+Belle+Aurore" <?php if($categories_bar=='La+Belle+Aurore'){echo ' selected="selected"';} ?>>La Belle Aurore</option>
									<option value="Lato" <?php if($categories_bar=='Lato'){echo ' selected="selected"';} ?>>Lato</option>
									<option value="League+Script" <?php if($categories_bar=='League+Script'){echo ' selected="selected"';} ?>>League Script</option>
									<option value="Lekton" <?php if($categories_bar=='Lekton'){echo ' selected="selected"';} ?>> Lekton </option>
									<option value="Limelight" <?php if($categories_bar=='Limelight'){echo ' selected="selected"';} ?>> Limelight </option>
									<option value="Lobster" <?php if($categories_bar=='Lobster'){echo ' selected="selected"';} ?>>Lobster</option>
									<option value="Lobster Two" <?php if($categories_bar=='Lobster Two'){echo ' selected="selected"';} ?>>Lobster Two</option>
									<option value="Lora" <?php if($categories_bar=='Lora'){echo ' selected="selected"';} ?>>Lora</option>
									<option value="Love+Ya+Like+A+Sister" <?php if($categories_bar=='Love+Ya+Like+A+Sister'){echo ' selected="selected"';} ?>>Love Ya Like A Sister</option>
									<option value="Loved+by+the+King" <?php if($categories_bar=='Loved+by+the+King'){echo ' selected="selected"';} ?>>Loved by the King</option>
									<option value="Luckiest+Guy" <?php if($categories_bar=='Luckiest+Guy'){echo ' selected="selected"';} ?>>Luckiest Guy</option>
									<option value="Maiden+Orange" <?php if($categories_bar=='Maiden+Orange'){echo ' selected="selected"';} ?>>Maiden Orange</option>
									<option value="Mako" <?php if($categories_bar=='Mako'){echo ' selected="selected"';} ?>>Mako</option>
									<option value="Maven+Pro" <?php if($categories_bar=='Maven+Pro'){echo ' selected="selected"';} ?>> Maven Pro</option>
									<option value="Meddon" <?php if($categories_bar=='Meddon'){echo ' selected="selected"';} ?>>Meddon</option>
									<option value="MedievalSharp" <?php if($categories_bar=='MedievalSharp'){echo ' selected="selected"';} ?>>MedievalSharp</option>
									<option value="Megrim" <?php if($categories_bar=='Megrim'){echo ' selected="selected"';} ?>>Megrim</option>
									<option value="Merriweather" <?php if($categories_bar=='Merriweather'){echo ' selected="selected"';} ?>>Merriweather</option>
									<option value="Metrophobic" <?php if($categories_bar=='Metrophobic'){echo ' selected="selected"';} ?>>Metrophobic</option>
									<option value="Michroma" <?php if($categories_bar=='Michroma'){echo ' selected="selected"';} ?>>Michroma</option>
									<option value="Miltonian Tattoo" <?php if($categories_bar=='Miltonian Tattoo'){echo ' selected="selected"';} ?>>Miltonian Tattoo</option>
									<option value="Miltonian" <?php if($categories_bar=='Miltonian'){echo ' selected="selected"';} ?>>Miltonian</option>
									<option value="Modern Antiqua" <?php if($categories_bar=='Modern Antiqua'){echo ' selected="selected"';} ?>>Modern Antiqua</option>
									<option value="Monofett" <?php if($categories_bar=='Monofett'){echo ' selected="selected"';} ?>>Monofett</option>
									<option value="Molengo" <?php if($categories_bar=='Molengo'){echo ' selected="selected"';} ?>>Molengo</option>
									<option value="Mountains of Christmas" <?php if($categories_bar=='Mountains of Christmas'){echo ' selected="selected"';} ?>>Mountains of Christmas</option>
									<option value="Muli" <?php if($categories_bar=='Muli'){echo ' selected="selected"';} ?>>Muli Regular</option>
									<option value="Neucha" <?php if($categories_bar=='Neucha'){echo ' selected="selected"';} ?>>Neucha</option>
									<option value="Neuton" <?php if($categories_bar=='Neuton'){echo ' selected="selected"';} ?>>Neuton</option>
									<option value="News+Cycle" <?php if($categories_bar=='News+Cycle'){echo ' selected="selected"';} ?>>News Cycle</option>
									<option value="Nixie+One" <?php if($categories_bar=='Nixie+One'){echo ' selected="selected"';} ?>>Nixie One</option>
									<option value="Nobile" <?php if($categories_bar=='Nobile'){echo ' selected="selected"';} ?>>Nobile</option>
									<option value="Nova+Cut" <?php if($categories_bar=='Nova+Cut'){echo ' selected="selected"';} ?>>Nova Cut</option>
									<option value="Nova+Flat" <?php if($categories_bar=='Nova+Flat'){echo ' selected="selected"';} ?>>Nova Flat</option>
									<option value="Nova+Mono" <?php if($categories_bar=='Nova+Mono'){echo ' selected="selected"';} ?>>Nova Mono</option>
									<option value="Nova+Oval" <?php if($categories_bar=='Nova+Oval'){echo ' selected="selected"';} ?>>Nova Oval</option>
									<option value="Nova+Round" <?php if($categories_bar=='Nova+Round'){echo ' selected="selected"';} ?>>Nova Round</option>
									<option value="Nova+Script" <?php if($categories_bar=='Nova+Script'){echo ' selected="selected"';} ?>>Nova Script</option>
									<option value="Nova+Slim" <?php if($categories_bar=='Nova+Slim'){echo ' selected="selected"';} ?>>Nova Slim</option>
									<option value="Nova+Square" <?php if($categories_bar=='Nova+Square'){echo ' selected="selected"';} ?>>Nova Square</option>
									<option value="Nunito:light" <?php if($categories_bar=='Nunito:light'){echo ' selected="selected"';} ?>> Nunito Light</option>
									<option value="Nunito" <?php if($categories_bar=='Nunito'){echo ' selected="selected"';} ?>> Nunito Regular</option>
									<option value="OFL+Sorts+Mill+Goudy+TT" <?php if($categories_bar=='OFL+Sorts+Mill+Goudy+TT'){echo ' selected="selected"';} ?>>OFL Sorts Mill Goudy TT</option>
									<option value="Old+Standard+TT" <?php if($categories_bar=='Old+Standard+TT'){echo ' selected="selected"';} ?>>Old Standard TT</option>
									<option value="Open+Sans" <?php if($categories_bar=='Open+Sans'){echo ' selected="selected"';} ?>>Open Sans regular</option>
									<option value="Open+Sans+Condensed" <?php if($categories_bar=='Open+Sans+Condensed'){echo ' selected="selected"';} ?>>Open Sans Condensed</option>
									<option value="Orbitron" <?php if($categories_bar=='Orbitron'){echo ' selected="selected"';} ?>>Orbitron Regular (400)</option>
									<option value="Oswald" <?php if($categories_bar=='Oswald'){echo ' selected="selected"';} ?>>Oswald</option>
									<option value="Over+the+Rainbow" <?php if($categories_bar=='Over+the+Rainbow'){echo ' selected="selected"';} ?>>Over the Rainbow</option>
									<option value="Reenie+Beanie" <?php if($categories_bar=='Reenie+Beanie'){echo ' selected="selected"';} ?>>Reenie Beanie</option>
									<option value="Pacifico" <?php if($categories_bar=='Pacifico'){echo ' selected="selected"';} ?>>Pacifico</option>
									<option value="Patrick+Hand" <?php if($categories_bar=='Patrick+Hand'){echo ' selected="selected"';} ?>>Patrick Hand</option>
									<option value="Paytone+One" <?php if($categories_bar=='Paytone+One'){echo ' selected="selected"';} ?>>Paytone One</option>
									<option value="Permanent+Marker" <?php if($categories_bar=='Permanent+Marker'){echo ' selected="selected"';} ?>>Permanent Marker</option>
									<option value="Philosopher" <?php if($categories_bar=='Philosopher'){echo ' selected="selected"';} ?>>Philosopher</option>
									<option value="Play" <?php if($categories_bar=='Play'){echo ' selected="selected"';} ?>>Play</option>
									<option value="Playfair+Display" <?php if($categories_bar=='Playfair+Display'){echo ' selected="selected"';} ?>> Playfair Display </option>
									<option value="Podkova" <?php if($categories_bar=='Podkova'){echo ' selected="selected"';} ?>> Podkova </option>
									<option value="PT+Sans" <?php if($categories_bar=='PT+Sans'){echo ' selected="selected"';} ?>>PT Sans</option>
									<option value="PT+Sans+Narrow" <?php if($categories_bar=='PT+Sans+Narrow'){echo ' selected="selected"';} ?>>PT Sans Narrow</option>
									<option value="PT+Sans+Narrow:regular,bold" <?php if($categories_bar=='PT+Sans+Narrow:regular,bold'){echo ' selected="selected"';} ?>>PT Sans Narrow (plus bold)</option>
									<option value="PT+Serif" <?php if($categories_bar=='PT+Serif'){echo ' selected="selected"';} ?>>PT Serif</option>
									<option value="PT+Serif Caption" <?php if($categories_bar=='PT+Serif Caption'){echo ' selected="selected"';} ?>>PT Serif Caption</option>
									<option value="Puritan" <?php if($categories_bar=='PT+Serif Caption'){echo ' selected="selected"';} ?>>Puritan</option>
									<option value="Quattrocento" <?php if($categories_bar=='Quattrocento'){echo ' selected="selected"';} ?>>Quattrocento</option>
									<option value="Quattrocento+Sans" <?php if($categories_bar=='Quattrocento+Sans'){echo ' selected="selected"';} ?>>Quattrocento Sans</option>
									<option value="Radley" <?php if($categories_bar=='Radley'){echo ' selected="selected"';} ?>>Radley</option>
									<option value="Raleway" <?php if($categories_bar=='Raleway'){echo ' selected="selected"';} ?>>Raleway</option>
									<option value="Redressed" <?php if($categories_bar=='Redressed'){echo ' selected="selected"';} ?>>Redressed</option>
									<option value="Rock+Salt" <?php if($categories_bar=='Rock+Salt'){echo ' selected="selected"';} ?>>Rock Salt</option>
									<option value="Rokkitt" <?php if($categories_bar=='Rokkitt'){echo ' selected="selected"';} ?>>Rokkitt</option>
									<option value="Ruslan+Display" <?php if($categories_bar=='Ruslan+Display'){echo ' selected="selected"';} ?>>Ruslan Display</option>
									<option value="Schoolbell" <?php if($categories_bar=='Schoolbell'){echo ' selected="selected"';} ?>>Schoolbell</option>
									<option value="Shadows+Into+Light" <?php if($categories_bar=='Shadows+Into+Light'){echo ' selected="selected"';} ?>>Shadows Into Light</option>
									<option value="Shanti" <?php if($categories_bar=='Shanti'){echo ' selected="selected"';} ?>>Shanti</option>
									<option value="Sigmar+One" <?php if($categories_bar=='Sigmar+One'){echo ' selected="selected"';} ?>>Sigmar One</option>
									<option value="Six+Caps" <?php if($categories_bar=='Six+Caps'){echo ' selected="selected"';} ?>>Six Caps</option>
									<option value="Slackey" <?php if($categories_bar=='Slackey'){echo ' selected="selected"';} ?>>Slackey</option>
									<option value="Smythe" <?php if($categories_bar=='Smythe'){echo ' selected="selected"';} ?>>Smythe</option>
									<option value="Sniglet" <?php if($categories_bar=='Sniglet'){echo ' selected="selected"';} ?>>Sniglet</option>
									<option value="Special+Elite" <?php if($categories_bar=='Special+Elite'){echo ' selected="selected"';} ?>>Special Elite</option>
									<option value="Stardos+Stencil" <?php if($categories_bar=='Stardos+Stencil'){echo ' selected="selected"';} ?>>Stardos Stencil</option>
									<option value="Sue+Ellen+Francisco" <?php if($categories_bar=='Sue+Ellen+Francisco'){echo ' selected="selected"';} ?>>Sue Ellen Francisco</option>
									<option value="Sunshiney" <?php if($categories_bar=='Sunshiney'){echo ' selected="selected"';} ?>>Sunshiney</option>
									<option value="Swanky+and+Moo+Moo" <?php if($categories_bar=='Swanky+and+Moo+Moo'){echo ' selected="selected"';} ?>>Swanky and Moo Moo</option>
									<option value="Syncopate" <?php if($categories_bar=='Syncopate'){echo ' selected="selected"';} ?>>Syncopate</option>
									<option value="Tangerine" <?php if($categories_bar=='Tangerine'){echo ' selected="selected"';} ?>>Tangerine</option>
									<option value="Tenor+Sans" <?php if($categories_bar=='Tenor+Sans'){echo ' selected="selected"';} ?>> Tenor Sans </option>
									<option value="Terminal+Dosis+Light" <?php if($categories_bar=='Terminal+Dosis+Light'){echo ' selected="selected"';} ?>>Terminal Dosis Light</option>
									<option value="The+Girl+Next+Door" <?php if($categories_bar=='The+Girl+Next+Door'){echo ' selected="selected"';} ?>>The Girl Next Door</option>
									<option value="Tinos" <?php if($categories_bar=='Tinos'){echo ' selected="selected"';} ?>>Tinos</option>
									<option value="Ubuntu" <?php if($categories_bar=='Ubuntu'){echo ' selected="selected"';} ?>>Ubuntu</option>
									<option value="Ultra" <?php if($categories_bar=='Ultra'){echo ' selected="selected"';} ?>>Ultra</option>
									<option value="Unkempt" <?php if($categories_bar=='Unkempt'){echo ' selected="selected"';} ?>>Unkempt</option>
									<option value="UnifrakturCook:bold" <?php if($categories_bar=='UnifrakturCook:bold'){echo ' selected="selected"';} ?>>UnifrakturCook</option>
									<option value="UnifrakturMaguntia" <?php if($categories_bar=='UnifrakturMaguntia'){echo ' selected="selected"';} ?>>UnifrakturMaguntia</option>
									<option value="Varela" <?php if($categories_bar=='Varela'){echo ' selected="selected"';} ?>>Varela</option>
									<option value="Varela Round" <?php if($categories_bar=='Varela Round'){echo ' selected="selected"';} ?>>Varela Round</option>
									<option value="Vibur" <?php if($categories_bar=='Vibur'){echo ' selected="selected"';} ?>>Vibur</option>
									<option value="Vollkorn" <?php if($categories_bar=='Vollkorn'){echo ' selected="selected"';} ?>>Vollkorn</option>
									<option value="VT323" <?php if($categories_bar=='VT323'){echo ' selected="selected"';} ?>>VT323</option>
									<option value="Waiting+for+the+Sunrise" <?php if($categories_bar=='Waiting+for+the+Sunrise'){echo ' selected="selected"';} ?>>Waiting for the Sunrise</option>
									<option value="Wallpoet" <?php if($categories_bar=='Wallpoet'){echo ' selected="selected"';} ?>>Wallpoet</option>
									<option value="Walter+Turncoat" <?php if($categories_bar=='Walter+Turncoat'){echo ' selected="selected"';} ?>>Walter Turncoat</option>
									<option value="Wire+One" <?php if($categories_bar=='Wire+One'){echo ' selected="selected"';} ?>>Wire One</option>
									<option value="Yanone+Kaffeesatz" <?php if($categories_bar=='Yanone+Kaffeesatz'){echo ' selected="selected"';} ?>>Yanone Kaffeesatz</option>
									<option value="Yeseva+One" <?php if($categories_bar=='Yeseva+One'){echo ' selected="selected"';} ?>>Yeseva One</option>
									<option value="Zeyada" <?php if($categories_bar=='Zeyada'){echo ' selected="selected"';} ?>>Zeyada</option>  
								
								</select>
								<select name="categories_bar_px" style="width:80px">
								
									<?php for( $x = 9; $x <= 50; $x++ ) { ?>
					              <option value="<?php echo $x; ?>" <?php if($categories_bar_px==$x || ($x == 14 && $categories_bar_px < 6)){echo ' selected="selected"';} ?>><?php echo $x; ?> px</option>
									<?php } ?>
								
								</select>
							
								<div class="clear"></div>
							
							</div>
							
							<!-- End Input -->
							
							<!-- Input -->
							
							<div class="input">
							
								<p>Headlines</p>
								<select name="headlines">
								
		              <?php if (isset($headlines)) {
		              $selected = "selected"; }
		              ?>
		<option value="standard" <?php if($headlines=='standard'){echo ' selected="selected"';} ?>>Standard</option>
		<option value="Francois+One" <?php if($headlines=='Francois+One'){echo ' selected="selected"';} ?>>Francois One</option>
				  <option value="Arial" <?php if($headlines=='Arial'){echo ' selected="selected"';} ?>>Arial</option>
		<option value="Aclonica" <?php if($headlines=='Aclonica'){echo ' selected="selected"';} ?>>Aclonica</option>
		<option value="Allan" <?php if($headlines=='Allan'){echo ' selected="selected"';} ?>>Allan</option>
		<option value="Annie+Use+Your+Telescope" <?php if($headlines=='Annie+Use+Your+Telescope'){echo ' selected="selected"';} ?>>Annie Use Your Telescope</option>
		<option value="Anonymous+Pro" <?php if($headlines=='Anonymous+Pro'){echo ' selected="selected"';} ?>>Anonymous Pro</option>
		<option value="Allerta+Stencil" <?php if($headlines=='Allerta+Stencil'){echo ' selected="selected"';} ?>>Allerta Stencil</option>
		<option value="Allerta" <?php if($headlines=='Allerta'){echo ' selected="selected"';} ?>>Allerta</option>
		<option value="Amaranth" <?php if($headlines=='Amaranth'){echo ' selected="selected"';} ?>>Amaranth</option>
		<option value="Anton" <?php if($headlines=='Anton'){echo ' selected="selected"';} ?>>Anton</option>
		<option value="Architects+Daughter" <?php if($headlines=='Architects+Daughter'){echo ' selected="selected"';} ?>>Architects Daughter</option>
		<option value="Arimo" <?php if($headlines=='Arimo'){echo ' selected="selected"';} ?>>Arimo</option>
		<option value="Artifika" <?php if($headlines=='Artifika'){echo ' selected="selected"';} ?>>Artifika</option>
		<option value="Arvo" <?php if($headlines=='Arvo'){echo ' selected="selected"';} ?>>Arvo</option>
		<option value="Asset" <?php if($headlines=='Asset'){echo ' selected="selected"';} ?>>Asset</option>
		<option value="Astloch" <?php if($headlines=='Astloch'){echo ' selected="selected"';} ?>>Astloch</option>
		<option value="Bangers" <?php if($headlines=='Bangers'){echo ' selected="selected"';} ?>>Bangers</option>
		<option value="Bentham" <?php if($headlines=='Bentham'){echo ' selected="selected"';} ?>>Bentham</option>
		<option value="Bevan" <?php if($headlines=='Bevan'){echo ' selected="selected"';} ?>>Bevan</option>
		<option value="Bigshot+One" <?php if($headlines=='Bigshot+One'){echo ' selected="selected"';} ?>>Bigshot One</option>
		<option value="Bowlby+One" <?php if($headlines=='Bowlby+One'){echo ' selected="selected"';} ?>>Bowlby One</option>
		<option value="Bowlby+One+SC" <?php if($headlines=='Bowlby+One+SC'){echo ' selected="selected"';} ?>>Bowlby One SC</option>
		<option value="Brawler" <?php if($headlines=='Brawler'){echo ' selected="selected"';} ?>>Brawler </option>
		<option value="Buda" <?php if($headlines=='Buda'){echo ' selected="selected"';} ?>>Buda</option>
		<option value="Cabin" <?php if($headlines=='Cabin'){echo ' selected="selected"';} ?>>Cabin</option>
		<option value="Calligraffitti" <?php if($headlines=='Calligraffitti'){echo ' selected="selected"';} ?>>Calligraffitti</option>
		<option value="Candal" <?php if($headlines=='Candal'){echo ' selected="selected"';} ?>>Candal</option>
		<option value="Cantarell" <?php if($headlines=='Cantarell'){echo ' selected="selected"';} ?>>Cantarell</option>
		<option value="Cardo" <?php if($headlines=='Cardo'){echo ' selected="selected"';} ?>>Cardo</option>
		<option value="Carter One" <?php if($headlines=='Carter One'){echo ' selected="selected"';} ?>>Carter One</option>
		<option value="Caudex" <?php if($headlines=='Caudex'){echo ' selected="selected"';} ?>>Caudex</option>
		<option value="Cedarville+Cursive" <?php if($headlines=='Cedarville+Cursive'){echo ' selected="selected"';} ?>>Cedarville Cursive</option>
		<option value="Cherry+Cream+Soda" <?php if($headlines=='Cherry+Cream+Soda'){echo ' selected="selected"';} ?>>Cherry Cream Soda</option>
		<option value="Chewy" <?php if($headlines=='Chewy'){echo ' selected="selected"';} ?>>Chewy</option>
		<option value="Coda" <?php if($headlines=='Coda'){echo ' selected="selected"';} ?>>Coda</option>
		<option value="Coming+Soon" <?php if($headlines=='Coming+Soon'){echo ' selected="selected"';} ?>>Coming Soon</option>
		<option value="Copse" <?php if($headlines=='Copse'){echo ' selected="selected"';} ?>>Copse</option>
		<option value="Corben" <?php if($headlines=='Corben'){echo ' selected="selected"';} ?>>Corben</option>
		<option value="Cousine" <?php if($headlines=='Cousine'){echo ' selected="selected"';} ?>>Cousine</option>
		<option value="Covered+By+Your+Grace" <?php if($headlines=='Covered+By+Your+Grace'){echo ' selected="selected"';} ?>>Covered By Your Grace</option>
		<option value="Crafty+Girls" <?php if($headlines=='Crafty+Girls'){echo ' selected="selected"';} ?>>Crafty Girls</option>
		<option value="Crimson+Text" <?php if($headlines=='Crimson+Text'){echo ' selected="selected"';} ?>>Crimson Text</option>
		<option value="Crushed" <?php if($headlines=='Crushed'){echo ' selected="selected"';} ?>>Crushed</option>
		<option value="Cuprum" <?php if($headlines=='Cuprum'){echo ' selected="selected"';} ?>>Cuprum</option>
		<option value="Damion" <?php if($headlines=='Damion'){echo ' selected="selected"';} ?>>Damion</option>
		<option value="Dancing+Script" <?php if($headlines=='Dancing+Script'){echo ' selected="selected"';} ?>>Dancing Script</option>
		<option value="Dawning+of+a+New+Day" <?php if($headlines=='Dawning+of+a+New+Day'){echo ' selected="selected"';} ?>>Dawning of a New Day</option>
		<option value="Didact+Gothic" <?php if($headlines=='Didact+Gothic'){echo ' selected="selected"';} ?>>Didact Gothic</option>
		<option value="Droid+Sans" <?php if($headlines=='Droid+Sans'){echo ' selected="selected"';} ?>>Droid Sans</option>
		<option value="Droid+Sans+Mono" <?php if($headlines=='Droid+Sans+Mono'){echo ' selected="selected"';} ?>>Droid Sans Mono</option>
		<option value="Droid+Serif" <?php if($headlines=='Droid+Serif'){echo ' selected="selected"';} ?>>Droid Serif</option>
		<option value="EB+Garamond" <?php if($headlines=='EB+Garamond'){echo ' selected="selected"';} ?>>EB Garamond</option>
		<option value="Expletus+Sans" <?php if($headlines=='Expletus+Sans'){echo ' selected="selected"';} ?>>Expletus Sans</option>
		<option value="Fontdiner+Swanky" <?php if($headlines=='Fontdiner+Swanky'){echo ' selected="selected"';} ?>>Fontdiner Swanky</option>
		<option value="Forum" <?php if($headlines=='Forum'){echo ' selected="selected"';} ?>>Forum</option>
		<option value="Geo" <?php if($headlines=='Geo'){echo ' selected="selected"';} ?>>Geo</option>
		<option value="Give+You+Glory" <?php if($headlines=='Give+You+Glory'){echo ' selected="selected"';} ?>>Give You Glory</option>
		<option value="Goblin+One" <?php if($headlines=='Goblin+One'){echo ' selected="selected"';} ?>>Goblin One</option>
		<option value="Goudy+Bookletter+1911" <?php if($headlines=='Goudy+Bookletter+1911'){echo ' selected="selected"';} ?>>Goudy Bookletter 1911</option>
		<option value="Gravitas+One" <?php if($headlines=='Gravitas+One'){echo ' selected="selected"';} ?>>Gravitas One</option>
		<option value="Gruppo" <?php if($headlines=='Gruppo'){echo ' selected="selected"';} ?>>Gruppo</option>
		<option value="Hammersmith+One" <?php if($headlines=='Hammersmith+One'){echo ' selected="selected"';} ?>>Hammersmith One</option>
		<option value="Holtwood+One+SC" <?php if($headlines=='Holtwood+One+SC'){echo ' selected="selected"';} ?>>Holtwood One SC</option>
		<option value="Homemade+Apple" <?php if($headlines=='Homemade+Apple'){echo ' selected="selected"';} ?>>Homemade Apple</option>
		<option value="Inconsolata" <?php if($headlines=='Inconsolata'){echo ' selected="selected"';} ?>>Inconsolata</option>
		<option value="Indie+Flower" <?php if($headlines=='Indie+Flower'){echo ' selected="selected"';} ?>>Indie Flower</option>
		<option value="IM+Fell+DW+Pica" <?php if($headlines=='IM+Fell+DW+Pica'){echo ' selected="selected"';} ?>>IM Fell DW Pica</option>
		<option value="IM+Fell+DW+Pica+SC" <?php if($headlines=='IM+Fell+DW+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell DW Pica SC</option>
		<option value="IM+Fell+Double+Pica" <?php if($headlines=='IM+Fell+Double+Pica'){echo ' selected="selected"';} ?>>IM Fell Double Pica</option>
		<option value="IM+Fell+Double+Pica+SC" <?php if($headlines=='IM+Fell+Double+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell Double Pica SC</option>
		<option value="IM+Fell+English" <?php if($headlines=='IM+Fell+English'){echo ' selected="selected"';} ?>>IM Fell English</option>
		<option value="IM+Fell+English+SC" <?php if($headlines=='IM+Fell+English+SC'){echo ' selected="selected"';} ?>>IM Fell English SC</option>
		<option value="IM+Fell+French+Canon" <?php if($headlines=='IM+Fell+French+Canon'){echo ' selected="selected"';} ?>>IM Fell French Canon</option>
		<option value="IM+Fell+French+Canon+SC" <?php if($headlines=='IM+Fell+French+Canon+SC'){echo ' selected="selected"';} ?>>IM Fell French Canon SC</option>
		<option value="IM+Fell+Great+Primer" <?php if($headlines=='IM+Fell+Great+Primer'){echo ' selected="selected"';} ?>>IM Fell Great Primer</option>
		<option value="IM+Fell+Great+Primer+SC" <?php if($headlines=='IM+Fell+Great+Primer+SC'){echo ' selected="selected"';} ?>>IM Fell Great Primer SC</option>
		<option value="Irish+Grover" <?php if($headlines=='Irish+Grover'){echo ' selected="selected"';} ?>>Irish Grover</option>
		<option value="Irish+Growler" <?php if($headlines=='Irish+Growler'){echo ' selected="selected"';} ?>>Irish Growler</option>
		<option value="Istok+Web" <?php if($headlines=='Istok+Web'){echo ' selected="selected"';} ?>>Istok Web</option>
		<option value="Josefin+Sans" <?php if($headlines=='Josefin+Sans'){echo ' selected="selected"';} ?>>Josefin Sans Regular 400</option>
		<option value="Josefin+Slab" <?php if($headlines=='Josefin+Slab'){echo ' selected="selected"';} ?>>Josefin Slab Regular 400</option>
		<option value="Judson" <?php if($headlines=='Judson'){echo ' selected="selected"';} ?>>Judson</option>
		<option value="Jura" <?php if($headlines=='Jura'){echo ' selected="selected"';} ?>> Jura Regular</option>
		<option value="Just+Another+Hand" <?php if($headlines=='Just+Another+Hand'){echo ' selected="selected"';} ?>>Just Another Hand</option>
		<option value="Just+Me+Again+Down+Here" <?php if($headlines=='Just+Me+Again+Down+Here'){echo ' selected="selected"';} ?>>Just Me Again Down Here</option>
		<option value="Kameron" <?php if($headlines=='Kameron'){echo ' selected="selected"';} ?>>Kameron</option>
		<option value="Kenia" <?php if($headlines=='Kenia'){echo ' selected="selected"';} ?>>Kenia</option>
		<option value="Kranky" <?php if($headlines=='Kranky'){echo ' selected="selected"';} ?>>Kranky</option>
		<option value="Kreon" <?php if($headlines=='Kreon'){echo ' selected="selected"';} ?>>Kreon</option>
		<option value="Kristi" <?php if($headlines=='Kristi'){echo ' selected="selected"';} ?>>Kristi</option>
		<option value="La+Belle+Aurore" <?php if($headlines=='La+Belle+Aurore'){echo ' selected="selected"';} ?>>La Belle Aurore</option>
		<option value="Lato" <?php if($headlines=='Lato'){echo ' selected="selected"';} ?>>Lato</option>
		<option value="League+Script" <?php if($headlines=='League+Script'){echo ' selected="selected"';} ?>>League Script</option>
		<option value="Lekton" <?php if($headlines=='Lekton'){echo ' selected="selected"';} ?>> Lekton </option>
		<option value="Limelight" <?php if($headlines=='Limelight'){echo ' selected="selected"';} ?>> Limelight </option>
		<option value="Lobster" <?php if($headlines=='Lobster'){echo ' selected="selected"';} ?>>Lobster</option>
		<option value="Lobster Two" <?php if($headlines=='Lobster Two'){echo ' selected="selected"';} ?>>Lobster Two</option>
		<option value="Lora" <?php if($headlines=='Lora'){echo ' selected="selected"';} ?>>Lora</option>
		<option value="Love+Ya+Like+A+Sister" <?php if($headlines=='Love+Ya+Like+A+Sister'){echo ' selected="selected"';} ?>>Love Ya Like A Sister</option>
		<option value="Loved+by+the+King" <?php if($headlines=='Loved+by+the+King'){echo ' selected="selected"';} ?>>Loved by the King</option>
		<option value="Luckiest+Guy" <?php if($headlines=='Luckiest+Guy'){echo ' selected="selected"';} ?>>Luckiest Guy</option>
		<option value="Maiden+Orange" <?php if($headlines=='Maiden+Orange'){echo ' selected="selected"';} ?>>Maiden Orange</option>
		<option value="Mako" <?php if($headlines=='Mako'){echo ' selected="selected"';} ?>>Mako</option>
		<option value="Maven+Pro" <?php if($headlines=='Maven+Pro'){echo ' selected="selected"';} ?>> Maven Pro</option>
		<option value="Meddon" <?php if($headlines=='Meddon'){echo ' selected="selected"';} ?>>Meddon</option>
		<option value="MedievalSharp" <?php if($headlines=='MedievalSharp'){echo ' selected="selected"';} ?>>MedievalSharp</option>
		<option value="Megrim" <?php if($headlines=='Megrim'){echo ' selected="selected"';} ?>>Megrim</option>
		<option value="Merriweather" <?php if($headlines=='Merriweather'){echo ' selected="selected"';} ?>>Merriweather</option>
		<option value="Metrophobic" <?php if($headlines=='Metrophobic'){echo ' selected="selected"';} ?>>Metrophobic</option>
		<option value="Michroma" <?php if($headlines=='Michroma'){echo ' selected="selected"';} ?>>Michroma</option>
		<option value="Miltonian Tattoo" <?php if($headlines=='Miltonian Tattoo'){echo ' selected="selected"';} ?>>Miltonian Tattoo</option>
		<option value="Miltonian" <?php if($headlines=='Miltonian'){echo ' selected="selected"';} ?>>Miltonian</option>
		<option value="Modern Antiqua" <?php if($headlines=='Modern Antiqua'){echo ' selected="selected"';} ?>>Modern Antiqua</option>
		<option value="Monofett" <?php if($headlines=='Monofett'){echo ' selected="selected"';} ?>>Monofett</option>
		<option value="Molengo" <?php if($headlines=='Molengo'){echo ' selected="selected"';} ?>>Molengo</option>
		<option value="Mountains of Christmas" <?php if($headlines=='Mountains of Christmas'){echo ' selected="selected"';} ?>>Mountains of Christmas</option>
		<option value="Muli" <?php if($headlines=='Muli'){echo ' selected="selected"';} ?>>Muli Regular</option>
		<option value="Neucha" <?php if($headlines=='Neucha'){echo ' selected="selected"';} ?>>Neucha</option>
		<option value="Neuton" <?php if($headlines=='Neuton'){echo ' selected="selected"';} ?>>Neuton</option>
		<option value="News+Cycle" <?php if($headlines=='News+Cycle'){echo ' selected="selected"';} ?>>News Cycle</option>
		<option value="Nixie+One" <?php if($headlines=='Nixie+One'){echo ' selected="selected"';} ?>>Nixie One</option>
		<option value="Nobile" <?php if($headlines=='Nobile'){echo ' selected="selected"';} ?>>Nobile</option>
		<option value="Nova+Cut" <?php if($headlines=='Nova+Cut'){echo ' selected="selected"';} ?>>Nova Cut</option>
		<option value="Nova+Flat" <?php if($headlines=='Nova+Flat'){echo ' selected="selected"';} ?>>Nova Flat</option>
		<option value="Nova+Mono" <?php if($headlines=='Nova+Mono'){echo ' selected="selected"';} ?>>Nova Mono</option>
		<option value="Nova+Oval" <?php if($headlines=='Nova+Oval'){echo ' selected="selected"';} ?>>Nova Oval</option>
		<option value="Nova+Round" <?php if($headlines=='Nova+Round'){echo ' selected="selected"';} ?>>Nova Round</option>
		<option value="Nova+Script" <?php if($headlines=='Nova+Script'){echo ' selected="selected"';} ?>>Nova Script</option>
		<option value="Nova+Slim" <?php if($headlines=='Nova+Slim'){echo ' selected="selected"';} ?>>Nova Slim</option>
		<option value="Nova+Square" <?php if($headlines=='Nova+Square'){echo ' selected="selected"';} ?>>Nova Square</option>
		<option value="Nunito:light" <?php if($headlines=='Nunito:light'){echo ' selected="selected"';} ?>> Nunito Light</option>
		<option value="Nunito" <?php if($headlines=='Nunito'){echo ' selected="selected"';} ?>> Nunito Regular</option>
		<option value="OFL+Sorts+Mill+Goudy+TT" <?php if($headlines=='OFL+Sorts+Mill+Goudy+TT'){echo ' selected="selected"';} ?>>OFL Sorts Mill Goudy TT</option>
		<option value="Old+Standard+TT" <?php if($headlines=='Old+Standard+TT'){echo ' selected="selected"';} ?>>Old Standard TT</option>
		<option value="Open+Sans" <?php if($headlines=='Open+Sans'){echo ' selected="selected"';} ?>>Open Sans regular</option>
		<option value="Open+Sans+Condensed" <?php if($headlines=='Open+Sans+Condensed'){echo ' selected="selected"';} ?>>Open Sans Condensed</option>
		<option value="Orbitron" <?php if($headlines=='Orbitron'){echo ' selected="selected"';} ?>>Orbitron Regular (400)</option>
		<option value="Oswald" <?php if($headlines=='Oswald'){echo ' selected="selected"';} ?>>Oswald</option>
		<option value="Over+the+Rainbow" <?php if($headlines=='Over+the+Rainbow'){echo ' selected="selected"';} ?>>Over the Rainbow</option>
		<option value="Reenie+Beanie" <?php if($headlines=='Reenie+Beanie'){echo ' selected="selected"';} ?>>Reenie Beanie</option>
		<option value="Pacifico" <?php if($headlines=='Pacifico'){echo ' selected="selected"';} ?>>Pacifico</option>
		<option value="Patrick+Hand" <?php if($headlines=='Patrick+Hand'){echo ' selected="selected"';} ?>>Patrick Hand</option>
		<option value="Paytone+One" <?php if($headlines=='Paytone+One'){echo ' selected="selected"';} ?>>Paytone One</option>
		<option value="Permanent+Marker" <?php if($headlines=='Permanent+Marker'){echo ' selected="selected"';} ?>>Permanent Marker</option>
		<option value="Philosopher" <?php if($headlines=='Philosopher'){echo ' selected="selected"';} ?>>Philosopher</option>
		<option value="Play" <?php if($headlines=='Play'){echo ' selected="selected"';} ?>>Play</option>
		<option value="Playfair+Display" <?php if($headlines=='Playfair+Display'){echo ' selected="selected"';} ?>> Playfair Display </option>
		<option value="Podkova" <?php if($headlines=='Podkova'){echo ' selected="selected"';} ?>> Podkova </option>
		<option value="PT+Sans" <?php if($headlines=='PT+Sans'){echo ' selected="selected"';} ?>>PT Sans</option>
		<option value="PT+Sans+Narrow" <?php if($headlines=='PT+Sans+Narrow'){echo ' selected="selected"';} ?>>PT Sans Narrow</option>
		<option value="PT+Sans+Narrow:regular,bold" <?php if($headlines=='PT+Sans+Narrow:regular,bold'){echo ' selected="selected"';} ?>>PT Sans Narrow (plus bold)</option>
		<option value="PT+Serif" <?php if($headlines=='PT+Serif'){echo ' selected="selected"';} ?>>PT Serif</option>
		<option value="PT+Serif Caption" <?php if($headlines=='PT+Serif Caption'){echo ' selected="selected"';} ?>>PT Serif Caption</option>
		<option value="Puritan" <?php if($headlines=='PT+Serif Caption'){echo ' selected="selected"';} ?>>Puritan</option>
		<option value="Quattrocento" <?php if($headlines=='Quattrocento'){echo ' selected="selected"';} ?>>Quattrocento</option>
		<option value="Quattrocento+Sans" <?php if($headlines=='Quattrocento+Sans'){echo ' selected="selected"';} ?>>Quattrocento Sans</option>
		<option value="Radley" <?php if($headlines=='Radley'){echo ' selected="selected"';} ?>>Radley</option>
		<option value="Raleway" <?php if($headlines=='Raleway'){echo ' selected="selected"';} ?>>Raleway</option>
		<option value="Redressed" <?php if($headlines=='Redressed'){echo ' selected="selected"';} ?>>Redressed</option>
		<option value="Rock+Salt" <?php if($headlines=='Rock+Salt'){echo ' selected="selected"';} ?>>Rock Salt</option>
		<option value="Rokkitt" <?php if($headlines=='Rokkitt'){echo ' selected="selected"';} ?>>Rokkitt</option>
		<option value="Ruslan+Display" <?php if($headlines=='Ruslan+Display'){echo ' selected="selected"';} ?>>Ruslan Display</option>
		<option value="Schoolbell" <?php if($headlines=='Schoolbell'){echo ' selected="selected"';} ?>>Schoolbell</option>
		<option value="Shadows+Into+Light" <?php if($headlines=='Shadows+Into+Light'){echo ' selected="selected"';} ?>>Shadows Into Light</option>
		<option value="Shanti" <?php if($headlines=='Shanti'){echo ' selected="selected"';} ?>>Shanti</option>
		<option value="Sigmar+One" <?php if($headlines=='Sigmar+One'){echo ' selected="selected"';} ?>>Sigmar One</option>
		<option value="Six+Caps" <?php if($headlines=='Six+Caps'){echo ' selected="selected"';} ?>>Six Caps</option>
		<option value="Slackey" <?php if($headlines=='Slackey'){echo ' selected="selected"';} ?>>Slackey</option>
		<option value="Smythe" <?php if($headlines=='Smythe'){echo ' selected="selected"';} ?>>Smythe</option>
		<option value="Sniglet" <?php if($headlines=='Sniglet'){echo ' selected="selected"';} ?>>Sniglet</option>
		<option value="Special+Elite" <?php if($headlines=='Special+Elite'){echo ' selected="selected"';} ?>>Special Elite</option>
		<option value="Stardos+Stencil" <?php if($headlines=='Stardos+Stencil'){echo ' selected="selected"';} ?>>Stardos Stencil</option>
		<option value="Sue+Ellen+Francisco" <?php if($headlines=='Sue+Ellen+Francisco'){echo ' selected="selected"';} ?>>Sue Ellen Francisco</option>
		<option value="Sunshiney" <?php if($headlines=='Sunshiney'){echo ' selected="selected"';} ?>>Sunshiney</option>
		<option value="Swanky+and+Moo+Moo" <?php if($headlines=='Swanky+and+Moo+Moo'){echo ' selected="selected"';} ?>>Swanky and Moo Moo</option>
		<option value="Syncopate" <?php if($headlines=='Syncopate'){echo ' selected="selected"';} ?>>Syncopate</option>
		<option value="Tangerine" <?php if($headlines=='Tangerine'){echo ' selected="selected"';} ?>>Tangerine</option>
		<option value="Tenor+Sans" <?php if($headlines=='Tenor+Sans'){echo ' selected="selected"';} ?>> Tenor Sans </option>
		<option value="Terminal+Dosis+Light" <?php if($headlines=='Terminal+Dosis+Light'){echo ' selected="selected"';} ?>>Terminal Dosis Light</option>
		<option value="The+Girl+Next+Door" <?php if($headlines=='The+Girl+Next+Door'){echo ' selected="selected"';} ?>>The Girl Next Door</option>
		<option value="Tinos" <?php if($headlines=='Tinos'){echo ' selected="selected"';} ?>>Tinos</option>
		<option value="Ubuntu" <?php if($headlines=='Ubuntu'){echo ' selected="selected"';} ?>>Ubuntu</option>
		<option value="Ultra" <?php if($headlines=='Ultra'){echo ' selected="selected"';} ?>>Ultra</option>
		<option value="Unkempt" <?php if($headlines=='Unkempt'){echo ' selected="selected"';} ?>>Unkempt</option>
		<option value="UnifrakturCook:bold" <?php if($headlines=='UnifrakturCook:bold'){echo ' selected="selected"';} ?>>UnifrakturCook</option>
		<option value="UnifrakturMaguntia" <?php if($headlines=='UnifrakturMaguntia'){echo ' selected="selected"';} ?>>UnifrakturMaguntia</option>
		<option value="Varela" <?php if($headlines=='Varela'){echo ' selected="selected"';} ?>>Varela</option>
		<option value="Varela Round" <?php if($headlines=='Varela Round'){echo ' selected="selected"';} ?>>Varela Round</option>
		<option value="Vibur" <?php if($headlines=='Vibur'){echo ' selected="selected"';} ?>>Vibur</option>
		<option value="Vollkorn" <?php if($headlines=='Vollkorn'){echo ' selected="selected"';} ?>>Vollkorn</option>
		<option value="VT323" <?php if($headlines=='VT323'){echo ' selected="selected"';} ?>>VT323</option>
		<option value="Waiting+for+the+Sunrise" <?php if($headlines=='Waiting+for+the+Sunrise'){echo ' selected="selected"';} ?>>Waiting for the Sunrise</option>
		<option value="Wallpoet" <?php if($headlines=='Wallpoet'){echo ' selected="selected"';} ?>>Wallpoet</option>
		<option value="Walter+Turncoat" <?php if($headlines=='Walter+Turncoat'){echo ' selected="selected"';} ?>>Walter Turncoat</option>
		<option value="Wire+One" <?php if($headlines=='Wire+One'){echo ' selected="selected"';} ?>>Wire One</option>
		<option value="Yanone+Kaffeesatz" <?php if($headlines=='Yanone+Kaffeesatz'){echo ' selected="selected"';} ?>>Yanone Kaffeesatz</option>
		<option value="Yeseva+One" <?php if($headlines=='Yeseva+One'){echo ' selected="selected"';} ?>>Yeseva One</option>
		<option value="Zeyada" <?php if($headlines=='Zeyada'){echo ' selected="selected"';} ?>>Zeyada</option>  
								
								</select>
								<select name="headlines_px" style="width:80px">
								
									<?php for( $x = 9; $x <= 50; $x++ ) { ?>
					              <option value="<?php echo $x; ?>" <?php if($headlines_px==$x || ($x == 18 && $headlines_px < 6)){echo ' selected="selected"';} ?>><?php echo $x; ?> px</option>
									<?php } ?>
								
								</select>
							
								<div class="clear"></div>
							
							</div>
							
							<!-- End Input -->
							
							<!-- Input -->
							
							<div class="input">
							
								<p>Footer headlines</p>
								<select name="footer_headlines">
								
		              <?php if (isset($footer_headlines)) {
		              $selected = "selected"; }
		              ?>
		<option value="standard" <?php if($footer_headlines=='standard'){echo ' selected="selected"';} ?>>Standard</option>
		<option value="Francois+One" <?php if($footer_headlines=='Francois+One'){echo ' selected="selected"';} ?>>Francois One</option>
				  <option value="Arial" <?php if($footer_headlines=='Arial'){echo ' selected="selected"';} ?>>Arial</option>
		<option value="Aclonica" <?php if($footer_headlines=='Aclonica'){echo ' selected="selected"';} ?>>Aclonica</option>
		<option value="Allan" <?php if($footer_headlines=='Allan'){echo ' selected="selected"';} ?>>Allan</option>
		<option value="Annie+Use+Your+Telescope" <?php if($footer_headlines=='Annie+Use+Your+Telescope'){echo ' selected="selected"';} ?>>Annie Use Your Telescope</option>
		<option value="Anonymous+Pro" <?php if($footer_headlines=='Anonymous+Pro'){echo ' selected="selected"';} ?>>Anonymous Pro</option>
		<option value="Allerta+Stencil" <?php if($footer_headlines=='Allerta+Stencil'){echo ' selected="selected"';} ?>>Allerta Stencil</option>
		<option value="Allerta" <?php if($footer_headlines=='Allerta'){echo ' selected="selected"';} ?>>Allerta</option>
		<option value="Amaranth" <?php if($footer_headlines=='Amaranth'){echo ' selected="selected"';} ?>>Amaranth</option>
		<option value="Anton" <?php if($footer_headlines=='Anton'){echo ' selected="selected"';} ?>>Anton</option>
		<option value="Architects+Daughter" <?php if($footer_headlines=='Architects+Daughter'){echo ' selected="selected"';} ?>>Architects Daughter</option>
		<option value="Arimo" <?php if($footer_headlines=='Arimo'){echo ' selected="selected"';} ?>>Arimo</option>
		<option value="Artifika" <?php if($footer_headlines=='Artifika'){echo ' selected="selected"';} ?>>Artifika</option>
		<option value="Arvo" <?php if($footer_headlines=='Arvo'){echo ' selected="selected"';} ?>>Arvo</option>
		<option value="Asset" <?php if($footer_headlines=='Asset'){echo ' selected="selected"';} ?>>Asset</option>
		<option value="Astloch" <?php if($footer_headlines=='Astloch'){echo ' selected="selected"';} ?>>Astloch</option>
		<option value="Bangers" <?php if($footer_headlines=='Bangers'){echo ' selected="selected"';} ?>>Bangers</option>
		<option value="Bentham" <?php if($footer_headlines=='Bentham'){echo ' selected="selected"';} ?>>Bentham</option>
		<option value="Bevan" <?php if($footer_headlines=='Bevan'){echo ' selected="selected"';} ?>>Bevan</option>
		<option value="Bigshot+One" <?php if($footer_headlines=='Bigshot+One'){echo ' selected="selected"';} ?>>Bigshot One</option>
		<option value="Bowlby+One" <?php if($footer_headlines=='Bowlby+One'){echo ' selected="selected"';} ?>>Bowlby One</option>
		<option value="Bowlby+One+SC" <?php if($footer_headlines=='Bowlby+One+SC'){echo ' selected="selected"';} ?>>Bowlby One SC</option>
		<option value="Brawler" <?php if($footer_headlines=='Brawler'){echo ' selected="selected"';} ?>>Brawler </option>
		<option value="Buda" <?php if($footer_headlines=='Buda'){echo ' selected="selected"';} ?>>Buda</option>
		<option value="Cabin" <?php if($footer_headlines=='Cabin'){echo ' selected="selected"';} ?>>Cabin</option>
		<option value="Calligraffitti" <?php if($footer_headlines=='Calligraffitti'){echo ' selected="selected"';} ?>>Calligraffitti</option>
		<option value="Candal" <?php if($footer_headlines=='Candal'){echo ' selected="selected"';} ?>>Candal</option>
		<option value="Cantarell" <?php if($footer_headlines=='Cantarell'){echo ' selected="selected"';} ?>>Cantarell</option>
		<option value="Cardo" <?php if($footer_headlines=='Cardo'){echo ' selected="selected"';} ?>>Cardo</option>
		<option value="Carter One" <?php if($footer_headlines=='Carter One'){echo ' selected="selected"';} ?>>Carter One</option>
		<option value="Caudex" <?php if($footer_headlines=='Caudex'){echo ' selected="selected"';} ?>>Caudex</option>
		<option value="Cedarville+Cursive" <?php if($footer_headlines=='Cedarville+Cursive'){echo ' selected="selected"';} ?>>Cedarville Cursive</option>
		<option value="Cherry+Cream+Soda" <?php if($footer_headlines=='Cherry+Cream+Soda'){echo ' selected="selected"';} ?>>Cherry Cream Soda</option>
		<option value="Chewy" <?php if($footer_headlines=='Chewy'){echo ' selected="selected"';} ?>>Chewy</option>
		<option value="Coda" <?php if($footer_headlines=='Coda'){echo ' selected="selected"';} ?>>Coda</option>
		<option value="Coming+Soon" <?php if($footer_headlines=='Coming+Soon'){echo ' selected="selected"';} ?>>Coming Soon</option>
		<option value="Copse" <?php if($footer_headlines=='Copse'){echo ' selected="selected"';} ?>>Copse</option>
		<option value="Corben" <?php if($footer_headlines=='Corben'){echo ' selected="selected"';} ?>>Corben</option>
		<option value="Cousine" <?php if($footer_headlines=='Cousine'){echo ' selected="selected"';} ?>>Cousine</option>
		<option value="Covered+By+Your+Grace" <?php if($footer_headlines=='Covered+By+Your+Grace'){echo ' selected="selected"';} ?>>Covered By Your Grace</option>
		<option value="Crafty+Girls" <?php if($footer_headlines=='Crafty+Girls'){echo ' selected="selected"';} ?>>Crafty Girls</option>
		<option value="Crimson+Text" <?php if($footer_headlines=='Crimson+Text'){echo ' selected="selected"';} ?>>Crimson Text</option>
		<option value="Crushed" <?php if($footer_headlines=='Crushed'){echo ' selected="selected"';} ?>>Crushed</option>
		<option value="Cuprum" <?php if($footer_headlines=='Cuprum'){echo ' selected="selected"';} ?>>Cuprum</option>
		<option value="Damion" <?php if($footer_headlines=='Damion'){echo ' selected="selected"';} ?>>Damion</option>
		<option value="Dancing+Script" <?php if($footer_headlines=='Dancing+Script'){echo ' selected="selected"';} ?>>Dancing Script</option>
		<option value="Dawning+of+a+New+Day" <?php if($footer_headlines=='Dawning+of+a+New+Day'){echo ' selected="selected"';} ?>>Dawning of a New Day</option>
		<option value="Didact+Gothic" <?php if($footer_headlines=='Didact+Gothic'){echo ' selected="selected"';} ?>>Didact Gothic</option>
		<option value="Droid+Sans" <?php if($footer_headlines=='Droid+Sans'){echo ' selected="selected"';} ?>>Droid Sans</option>
		<option value="Droid+Sans+Mono" <?php if($footer_headlines=='Droid+Sans+Mono'){echo ' selected="selected"';} ?>>Droid Sans Mono</option>
		<option value="Droid+Serif" <?php if($footer_headlines=='Droid+Serif'){echo ' selected="selected"';} ?>>Droid Serif</option>
		<option value="EB+Garamond" <?php if($footer_headlines=='EB+Garamond'){echo ' selected="selected"';} ?>>EB Garamond</option>
		<option value="Expletus+Sans" <?php if($footer_headlines=='Expletus+Sans'){echo ' selected="selected"';} ?>>Expletus Sans</option>
		<option value="Fontdiner+Swanky" <?php if($footer_headlines=='Fontdiner+Swanky'){echo ' selected="selected"';} ?>>Fontdiner Swanky</option>
		<option value="Forum" <?php if($footer_headlines=='Forum'){echo ' selected="selected"';} ?>>Forum</option>
		<option value="Geo" <?php if($footer_headlines=='Geo'){echo ' selected="selected"';} ?>>Geo</option>
		<option value="Give+You+Glory" <?php if($footer_headlines=='Give+You+Glory'){echo ' selected="selected"';} ?>>Give You Glory</option>
		<option value="Goblin+One" <?php if($footer_headlines=='Goblin+One'){echo ' selected="selected"';} ?>>Goblin One</option>
		<option value="Goudy+Bookletter+1911" <?php if($footer_headlines=='Goudy+Bookletter+1911'){echo ' selected="selected"';} ?>>Goudy Bookletter 1911</option>
		<option value="Gravitas+One" <?php if($footer_headlines=='Gravitas+One'){echo ' selected="selected"';} ?>>Gravitas One</option>
		<option value="Gruppo" <?php if($footer_headlines=='Gruppo'){echo ' selected="selected"';} ?>>Gruppo</option>
		<option value="Hammersmith+One" <?php if($footer_headlines=='Hammersmith+One'){echo ' selected="selected"';} ?>>Hammersmith One</option>
		<option value="Holtwood+One+SC" <?php if($footer_headlines=='Holtwood+One+SC'){echo ' selected="selected"';} ?>>Holtwood One SC</option>
		<option value="Homemade+Apple" <?php if($footer_headlines=='Homemade+Apple'){echo ' selected="selected"';} ?>>Homemade Apple</option>
		<option value="Inconsolata" <?php if($footer_headlines=='Inconsolata'){echo ' selected="selected"';} ?>>Inconsolata</option>
		<option value="Indie+Flower" <?php if($footer_headlines=='Indie+Flower'){echo ' selected="selected"';} ?>>Indie Flower</option>
		<option value="IM+Fell+DW+Pica" <?php if($footer_headlines=='IM+Fell+DW+Pica'){echo ' selected="selected"';} ?>>IM Fell DW Pica</option>
		<option value="IM+Fell+DW+Pica+SC" <?php if($footer_headlines=='IM+Fell+DW+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell DW Pica SC</option>
		<option value="IM+Fell+Double+Pica" <?php if($footer_headlines=='IM+Fell+Double+Pica'){echo ' selected="selected"';} ?>>IM Fell Double Pica</option>
		<option value="IM+Fell+Double+Pica+SC" <?php if($footer_headlines=='IM+Fell+Double+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell Double Pica SC</option>
		<option value="IM+Fell+English" <?php if($footer_headlines=='IM+Fell+English'){echo ' selected="selected"';} ?>>IM Fell English</option>
		<option value="IM+Fell+English+SC" <?php if($footer_headlines=='IM+Fell+English+SC'){echo ' selected="selected"';} ?>>IM Fell English SC</option>
		<option value="IM+Fell+French+Canon" <?php if($footer_headlines=='IM+Fell+French+Canon'){echo ' selected="selected"';} ?>>IM Fell French Canon</option>
		<option value="IM+Fell+French+Canon+SC" <?php if($footer_headlines=='IM+Fell+French+Canon+SC'){echo ' selected="selected"';} ?>>IM Fell French Canon SC</option>
		<option value="IM+Fell+Great+Primer" <?php if($footer_headlines=='IM+Fell+Great+Primer'){echo ' selected="selected"';} ?>>IM Fell Great Primer</option>
		<option value="IM+Fell+Great+Primer+SC" <?php if($footer_headlines=='IM+Fell+Great+Primer+SC'){echo ' selected="selected"';} ?>>IM Fell Great Primer SC</option>
		<option value="Irish+Grover" <?php if($footer_headlines=='Irish+Grover'){echo ' selected="selected"';} ?>>Irish Grover</option>
		<option value="Irish+Growler" <?php if($footer_headlines=='Irish+Growler'){echo ' selected="selected"';} ?>>Irish Growler</option>
		<option value="Istok+Web" <?php if($footer_headlines=='Istok+Web'){echo ' selected="selected"';} ?>>Istok Web</option>
		<option value="Josefin+Sans" <?php if($footer_headlines=='Josefin+Sans'){echo ' selected="selected"';} ?>>Josefin Sans Regular 400</option>
		<option value="Josefin+Slab" <?php if($footer_headlines=='Josefin+Slab'){echo ' selected="selected"';} ?>>Josefin Slab Regular 400</option>
		<option value="Judson" <?php if($footer_headlines=='Judson'){echo ' selected="selected"';} ?>>Judson</option>
		<option value="Jura" <?php if($footer_headlines=='Jura'){echo ' selected="selected"';} ?>> Jura Regular</option>
		<option value="Just+Another+Hand" <?php if($footer_headlines=='Just+Another+Hand'){echo ' selected="selected"';} ?>>Just Another Hand</option>
		<option value="Just+Me+Again+Down+Here" <?php if($footer_headlines=='Just+Me+Again+Down+Here'){echo ' selected="selected"';} ?>>Just Me Again Down Here</option>
		<option value="Kameron" <?php if($footer_headlines=='Kameron'){echo ' selected="selected"';} ?>>Kameron</option>
		<option value="Kenia" <?php if($footer_headlines=='Kenia'){echo ' selected="selected"';} ?>>Kenia</option>
		<option value="Kranky" <?php if($footer_headlines=='Kranky'){echo ' selected="selected"';} ?>>Kranky</option>
		<option value="Kreon" <?php if($footer_headlines=='Kreon'){echo ' selected="selected"';} ?>>Kreon</option>
		<option value="Kristi" <?php if($footer_headlines=='Kristi'){echo ' selected="selected"';} ?>>Kristi</option>
		<option value="La+Belle+Aurore" <?php if($footer_headlines=='La+Belle+Aurore'){echo ' selected="selected"';} ?>>La Belle Aurore</option>
		<option value="Lato" <?php if($footer_headlines=='Lato'){echo ' selected="selected"';} ?>>Lato</option>
		<option value="League+Script" <?php if($footer_headlines=='League+Script'){echo ' selected="selected"';} ?>>League Script</option>
		<option value="Lekton" <?php if($footer_headlines=='Lekton'){echo ' selected="selected"';} ?>> Lekton </option>
		<option value="Limelight" <?php if($footer_headlines=='Limelight'){echo ' selected="selected"';} ?>> Limelight </option>
		<option value="Lobster" <?php if($footer_headlines=='Lobster'){echo ' selected="selected"';} ?>>Lobster</option>
		<option value="Lobster Two" <?php if($footer_headlines=='Lobster Two'){echo ' selected="selected"';} ?>>Lobster Two</option>
		<option value="Lora" <?php if($footer_headlines=='Lora'){echo ' selected="selected"';} ?>>Lora</option>
		<option value="Love+Ya+Like+A+Sister" <?php if($footer_headlines=='Love+Ya+Like+A+Sister'){echo ' selected="selected"';} ?>>Love Ya Like A Sister</option>
		<option value="Loved+by+the+King" <?php if($footer_headlines=='Loved+by+the+King'){echo ' selected="selected"';} ?>>Loved by the King</option>
		<option value="Luckiest+Guy" <?php if($footer_headlines=='Luckiest+Guy'){echo ' selected="selected"';} ?>>Luckiest Guy</option>
		<option value="Maiden+Orange" <?php if($footer_headlines=='Maiden+Orange'){echo ' selected="selected"';} ?>>Maiden Orange</option>
		<option value="Mako" <?php if($footer_headlines=='Mako'){echo ' selected="selected"';} ?>>Mako</option>
		<option value="Maven+Pro" <?php if($footer_headlines=='Maven+Pro'){echo ' selected="selected"';} ?>> Maven Pro</option>
		<option value="Meddon" <?php if($footer_headlines=='Meddon'){echo ' selected="selected"';} ?>>Meddon</option>
		<option value="MedievalSharp" <?php if($footer_headlines=='MedievalSharp'){echo ' selected="selected"';} ?>>MedievalSharp</option>
		<option value="Megrim" <?php if($footer_headlines=='Megrim'){echo ' selected="selected"';} ?>>Megrim</option>
		<option value="Merriweather" <?php if($footer_headlines=='Merriweather'){echo ' selected="selected"';} ?>>Merriweather</option>
		<option value="Metrophobic" <?php if($footer_headlines=='Metrophobic'){echo ' selected="selected"';} ?>>Metrophobic</option>
		<option value="Michroma" <?php if($footer_headlines=='Michroma'){echo ' selected="selected"';} ?>>Michroma</option>
		<option value="Miltonian Tattoo" <?php if($footer_headlines=='Miltonian Tattoo'){echo ' selected="selected"';} ?>>Miltonian Tattoo</option>
		<option value="Miltonian" <?php if($footer_headlines=='Miltonian'){echo ' selected="selected"';} ?>>Miltonian</option>
		<option value="Modern Antiqua" <?php if($footer_headlines=='Modern Antiqua'){echo ' selected="selected"';} ?>>Modern Antiqua</option>
		<option value="Monofett" <?php if($footer_headlines=='Monofett'){echo ' selected="selected"';} ?>>Monofett</option>
		<option value="Molengo" <?php if($footer_headlines=='Molengo'){echo ' selected="selected"';} ?>>Molengo</option>
		<option value="Mountains of Christmas" <?php if($footer_headlines=='Mountains of Christmas'){echo ' selected="selected"';} ?>>Mountains of Christmas</option>
		<option value="Muli" <?php if($footer_headlines=='Muli'){echo ' selected="selected"';} ?>>Muli Regular</option>
		<option value="Neucha" <?php if($footer_headlines=='Neucha'){echo ' selected="selected"';} ?>>Neucha</option>
		<option value="Neuton" <?php if($footer_headlines=='Neuton'){echo ' selected="selected"';} ?>>Neuton</option>
		<option value="News+Cycle" <?php if($footer_headlines=='News+Cycle'){echo ' selected="selected"';} ?>>News Cycle</option>
		<option value="Nixie+One" <?php if($footer_headlines=='Nixie+One'){echo ' selected="selected"';} ?>>Nixie One</option>
		<option value="Nobile" <?php if($footer_headlines=='Nobile'){echo ' selected="selected"';} ?>>Nobile</option>
		<option value="Nova+Cut" <?php if($footer_headlines=='Nova+Cut'){echo ' selected="selected"';} ?>>Nova Cut</option>
		<option value="Nova+Flat" <?php if($footer_headlines=='Nova+Flat'){echo ' selected="selected"';} ?>>Nova Flat</option>
		<option value="Nova+Mono" <?php if($footer_headlines=='Nova+Mono'){echo ' selected="selected"';} ?>>Nova Mono</option>
		<option value="Nova+Oval" <?php if($footer_headlines=='Nova+Oval'){echo ' selected="selected"';} ?>>Nova Oval</option>
		<option value="Nova+Round" <?php if($footer_headlines=='Nova+Round'){echo ' selected="selected"';} ?>>Nova Round</option>
		<option value="Nova+Script" <?php if($footer_headlines=='Nova+Script'){echo ' selected="selected"';} ?>>Nova Script</option>
		<option value="Nova+Slim" <?php if($footer_headlines=='Nova+Slim'){echo ' selected="selected"';} ?>>Nova Slim</option>
		<option value="Nova+Square" <?php if($footer_headlines=='Nova+Square'){echo ' selected="selected"';} ?>>Nova Square</option>
		<option value="Nunito:light" <?php if($footer_headlines=='Nunito:light'){echo ' selected="selected"';} ?>> Nunito Light</option>
		<option value="Nunito" <?php if($footer_headlines=='Nunito'){echo ' selected="selected"';} ?>> Nunito Regular</option>
		<option value="OFL+Sorts+Mill+Goudy+TT" <?php if($footer_headlines=='OFL+Sorts+Mill+Goudy+TT'){echo ' selected="selected"';} ?>>OFL Sorts Mill Goudy TT</option>
		<option value="Old+Standard+TT" <?php if($footer_headlines=='Old+Standard+TT'){echo ' selected="selected"';} ?>>Old Standard TT</option>
		<option value="Open+Sans" <?php if($footer_headlines=='Open+Sans'){echo ' selected="selected"';} ?>>Open Sans regular</option>
		<option value="Open+Sans+Condensed" <?php if($footer_headlines=='Open+Sans+Condensed'){echo ' selected="selected"';} ?>>Open Sans Condensed</option>
		<option value="Orbitron" <?php if($footer_headlines=='Orbitron'){echo ' selected="selected"';} ?>>Orbitron Regular (400)</option>
		<option value="Oswald" <?php if($footer_headlines=='Oswald'){echo ' selected="selected"';} ?>>Oswald</option>
		<option value="Over+the+Rainbow" <?php if($footer_headlines=='Over+the+Rainbow'){echo ' selected="selected"';} ?>>Over the Rainbow</option>
		<option value="Reenie+Beanie" <?php if($footer_headlines=='Reenie+Beanie'){echo ' selected="selected"';} ?>>Reenie Beanie</option>
		<option value="Pacifico" <?php if($footer_headlines=='Pacifico'){echo ' selected="selected"';} ?>>Pacifico</option>
		<option value="Patrick+Hand" <?php if($footer_headlines=='Patrick+Hand'){echo ' selected="selected"';} ?>>Patrick Hand</option>
		<option value="Paytone+One" <?php if($footer_headlines=='Paytone+One'){echo ' selected="selected"';} ?>>Paytone One</option>
		<option value="Permanent+Marker" <?php if($footer_headlines=='Permanent+Marker'){echo ' selected="selected"';} ?>>Permanent Marker</option>
		<option value="Philosopher" <?php if($footer_headlines=='Philosopher'){echo ' selected="selected"';} ?>>Philosopher</option>
		<option value="Play" <?php if($footer_headlines=='Play'){echo ' selected="selected"';} ?>>Play</option>
		<option value="Playfair+Display" <?php if($footer_headlines=='Playfair+Display'){echo ' selected="selected"';} ?>> Playfair Display </option>
		<option value="Podkova" <?php if($footer_headlines=='Podkova'){echo ' selected="selected"';} ?>> Podkova </option>
		<option value="PT+Sans" <?php if($footer_headlines=='PT+Sans'){echo ' selected="selected"';} ?>>PT Sans</option>
		<option value="PT+Sans+Narrow" <?php if($footer_headlines=='PT+Sans+Narrow'){echo ' selected="selected"';} ?>>PT Sans Narrow</option>
		<option value="PT+Sans+Narrow:regular,bold" <?php if($footer_headlines=='PT+Sans+Narrow:regular,bold'){echo ' selected="selected"';} ?>>PT Sans Narrow (plus bold)</option>
		<option value="PT+Serif" <?php if($footer_headlines=='PT+Serif'){echo ' selected="selected"';} ?>>PT Serif</option>
		<option value="PT+Serif Caption" <?php if($footer_headlines=='PT+Serif Caption'){echo ' selected="selected"';} ?>>PT Serif Caption</option>
		<option value="Puritan" <?php if($footer_headlines=='PT+Serif Caption'){echo ' selected="selected"';} ?>>Puritan</option>
		<option value="Quattrocento" <?php if($footer_headlines=='Quattrocento'){echo ' selected="selected"';} ?>>Quattrocento</option>
		<option value="Quattrocento+Sans" <?php if($footer_headlines=='Quattrocento+Sans'){echo ' selected="selected"';} ?>>Quattrocento Sans</option>
		<option value="Radley" <?php if($footer_headlines=='Radley'){echo ' selected="selected"';} ?>>Radley</option>
		<option value="Raleway" <?php if($footer_headlines=='Raleway'){echo ' selected="selected"';} ?>>Raleway</option>
		<option value="Redressed" <?php if($footer_headlines=='Redressed'){echo ' selected="selected"';} ?>>Redressed</option>
		<option value="Rock+Salt" <?php if($footer_headlines=='Rock+Salt'){echo ' selected="selected"';} ?>>Rock Salt</option>
		<option value="Rokkitt" <?php if($footer_headlines=='Rokkitt'){echo ' selected="selected"';} ?>>Rokkitt</option>
		<option value="Ruslan+Display" <?php if($footer_headlines=='Ruslan+Display'){echo ' selected="selected"';} ?>>Ruslan Display</option>
		<option value="Schoolbell" <?php if($footer_headlines=='Schoolbell'){echo ' selected="selected"';} ?>>Schoolbell</option>
		<option value="Shadows+Into+Light" <?php if($footer_headlines=='Shadows+Into+Light'){echo ' selected="selected"';} ?>>Shadows Into Light</option>
		<option value="Shanti" <?php if($footer_headlines=='Shanti'){echo ' selected="selected"';} ?>>Shanti</option>
		<option value="Sigmar+One" <?php if($footer_headlines=='Sigmar+One'){echo ' selected="selected"';} ?>>Sigmar One</option>
		<option value="Six+Caps" <?php if($footer_headlines=='Six+Caps'){echo ' selected="selected"';} ?>>Six Caps</option>
		<option value="Slackey" <?php if($footer_headlines=='Slackey'){echo ' selected="selected"';} ?>>Slackey</option>
		<option value="Smythe" <?php if($footer_headlines=='Smythe'){echo ' selected="selected"';} ?>>Smythe</option>
		<option value="Sniglet" <?php if($footer_headlines=='Sniglet'){echo ' selected="selected"';} ?>>Sniglet</option>
		<option value="Special+Elite" <?php if($footer_headlines=='Special+Elite'){echo ' selected="selected"';} ?>>Special Elite</option>
		<option value="Stardos+Stencil" <?php if($footer_headlines=='Stardos+Stencil'){echo ' selected="selected"';} ?>>Stardos Stencil</option>
		<option value="Sue+Ellen+Francisco" <?php if($footer_headlines=='Sue+Ellen+Francisco'){echo ' selected="selected"';} ?>>Sue Ellen Francisco</option>
		<option value="Sunshiney" <?php if($footer_headlines=='Sunshiney'){echo ' selected="selected"';} ?>>Sunshiney</option>
		<option value="Swanky+and+Moo+Moo" <?php if($footer_headlines=='Swanky+and+Moo+Moo'){echo ' selected="selected"';} ?>>Swanky and Moo Moo</option>
		<option value="Syncopate" <?php if($footer_headlines=='Syncopate'){echo ' selected="selected"';} ?>>Syncopate</option>
		<option value="Tangerine" <?php if($footer_headlines=='Tangerine'){echo ' selected="selected"';} ?>>Tangerine</option>
		<option value="Tenor+Sans" <?php if($footer_headlines=='Tenor+Sans'){echo ' selected="selected"';} ?>> Tenor Sans </option>
		<option value="Terminal+Dosis+Light" <?php if($footer_headlines=='Terminal+Dosis+Light'){echo ' selected="selected"';} ?>>Terminal Dosis Light</option>
		<option value="The+Girl+Next+Door" <?php if($footer_headlines=='The+Girl+Next+Door'){echo ' selected="selected"';} ?>>The Girl Next Door</option>
		<option value="Tinos" <?php if($footer_headlines=='Tinos'){echo ' selected="selected"';} ?>>Tinos</option>
		<option value="Ubuntu" <?php if($footer_headlines=='Ubuntu'){echo ' selected="selected"';} ?>>Ubuntu</option>
		<option value="Ultra" <?php if($footer_headlines=='Ultra'){echo ' selected="selected"';} ?>>Ultra</option>
		<option value="Unkempt" <?php if($footer_headlines=='Unkempt'){echo ' selected="selected"';} ?>>Unkempt</option>
		<option value="UnifrakturCook:bold" <?php if($footer_headlines=='UnifrakturCook:bold'){echo ' selected="selected"';} ?>>UnifrakturCook</option>
		<option value="UnifrakturMaguntia" <?php if($footer_headlines=='UnifrakturMaguntia'){echo ' selected="selected"';} ?>>UnifrakturMaguntia</option>
		<option value="Varela" <?php if($footer_headlines=='Varela'){echo ' selected="selected"';} ?>>Varela</option>
		<option value="Varela Round" <?php if($footer_headlines=='Varela Round'){echo ' selected="selected"';} ?>>Varela Round</option>
		<option value="Vibur" <?php if($footer_headlines=='Vibur'){echo ' selected="selected"';} ?>>Vibur</option>
		<option value="Vollkorn" <?php if($footer_headlines=='Vollkorn'){echo ' selected="selected"';} ?>>Vollkorn</option>
		<option value="VT323" <?php if($footer_headlines=='VT323'){echo ' selected="selected"';} ?>>VT323</option>
		<option value="Waiting+for+the+Sunrise" <?php if($footer_headlines=='Waiting+for+the+Sunrise'){echo ' selected="selected"';} ?>>Waiting for the Sunrise</option>
		<option value="Wallpoet" <?php if($footer_headlines=='Wallpoet'){echo ' selected="selected"';} ?>>Wallpoet</option>
		<option value="Walter+Turncoat" <?php if($footer_headlines=='Walter+Turncoat'){echo ' selected="selected"';} ?>>Walter Turncoat</option>
		<option value="Wire+One" <?php if($footer_headlines=='Wire+One'){echo ' selected="selected"';} ?>>Wire One</option>
		<option value="Yanone+Kaffeesatz" <?php if($footer_headlines=='Yanone+Kaffeesatz'){echo ' selected="selected"';} ?>>Yanone Kaffeesatz</option>
		<option value="Yeseva+One" <?php if($footer_headlines=='Yeseva+One'){echo ' selected="selected"';} ?>>Yeseva One</option>
		<option value="Zeyada" <?php if($footer_headlines=='Zeyada'){echo ' selected="selected"';} ?>>Zeyada</option>  
								
								</select>
								<select name="footer_headlines_px" style="width:80px">
								
									<?php for( $x = 9; $x <= 50; $x++ ) { ?>
					              <option value="<?php echo $x; ?>" <?php if($footer_headlines_px==$x || ($x == 18 && $footer_headlines_px < 6)){echo ' selected="selected"';} ?>><?php echo $x; ?> px</option>
									<?php } ?>
								
								</select>
							
								<div class="clear"></div>
							
							</div>
							
							<!-- End Input -->
							
							<!-- Input -->
							
							<div class="input">
							
								<p>Big headlines</p>
								<select name="big_headlines">
								
		              <?php if (isset($big_headlines)) {
		              $selected = "selected"; }
		              ?>
		<option value="standard" <?php if($big_headlines=='standard'){echo ' selected="selected"';} ?>>Standard</option>
		<option value="Francois+One" <?php if($big_headlines=='Francois+One'){echo ' selected="selected"';} ?>>Francois One</option>
				  <option value="Arial" <?php if($big_headlines=='Arial'){echo ' selected="selected"';} ?>>Arial</option>
		<option value="Aclonica" <?php if($big_headlines=='Aclonica'){echo ' selected="selected"';} ?>>Aclonica</option>
		<option value="Allan" <?php if($big_headlines=='Allan'){echo ' selected="selected"';} ?>>Allan</option>
		<option value="Annie+Use+Your+Telescope" <?php if($big_headlines=='Annie+Use+Your+Telescope'){echo ' selected="selected"';} ?>>Annie Use Your Telescope</option>
		<option value="Anonymous+Pro" <?php if($big_headlines=='Anonymous+Pro'){echo ' selected="selected"';} ?>>Anonymous Pro</option>
		<option value="Allerta+Stencil" <?php if($big_headlines=='Allerta+Stencil'){echo ' selected="selected"';} ?>>Allerta Stencil</option>
		<option value="Allerta" <?php if($big_headlines=='Allerta'){echo ' selected="selected"';} ?>>Allerta</option>
		<option value="Amaranth" <?php if($big_headlines=='Amaranth'){echo ' selected="selected"';} ?>>Amaranth</option>
		<option value="Anton" <?php if($big_headlines=='Anton'){echo ' selected="selected"';} ?>>Anton</option>
		<option value="Architects+Daughter" <?php if($big_headlines=='Architects+Daughter'){echo ' selected="selected"';} ?>>Architects Daughter</option>
		<option value="Arimo" <?php if($big_headlines=='Arimo'){echo ' selected="selected"';} ?>>Arimo</option>
		<option value="Artifika" <?php if($big_headlines=='Artifika'){echo ' selected="selected"';} ?>>Artifika</option>
		<option value="Arvo" <?php if($big_headlines=='Arvo'){echo ' selected="selected"';} ?>>Arvo</option>
		<option value="Asset" <?php if($big_headlines=='Asset'){echo ' selected="selected"';} ?>>Asset</option>
		<option value="Astloch" <?php if($big_headlines=='Astloch'){echo ' selected="selected"';} ?>>Astloch</option>
		<option value="Bangers" <?php if($big_headlines=='Bangers'){echo ' selected="selected"';} ?>>Bangers</option>
		<option value="Bentham" <?php if($big_headlines=='Bentham'){echo ' selected="selected"';} ?>>Bentham</option>
		<option value="Bevan" <?php if($big_headlines=='Bevan'){echo ' selected="selected"';} ?>>Bevan</option>
		<option value="Bigshot+One" <?php if($big_headlines=='Bigshot+One'){echo ' selected="selected"';} ?>>Bigshot One</option>
		<option value="Bowlby+One" <?php if($big_headlines=='Bowlby+One'){echo ' selected="selected"';} ?>>Bowlby One</option>
		<option value="Bowlby+One+SC" <?php if($big_headlines=='Bowlby+One+SC'){echo ' selected="selected"';} ?>>Bowlby One SC</option>
		<option value="Brawler" <?php if($big_headlines=='Brawler'){echo ' selected="selected"';} ?>>Brawler </option>
		<option value="Buda" <?php if($big_headlines=='Buda'){echo ' selected="selected"';} ?>>Buda</option>
		<option value="Cabin" <?php if($big_headlines=='Cabin'){echo ' selected="selected"';} ?>>Cabin</option>
		<option value="Calligraffitti" <?php if($big_headlines=='Calligraffitti'){echo ' selected="selected"';} ?>>Calligraffitti</option>
		<option value="Candal" <?php if($big_headlines=='Candal'){echo ' selected="selected"';} ?>>Candal</option>
		<option value="Cantarell" <?php if($big_headlines=='Cantarell'){echo ' selected="selected"';} ?>>Cantarell</option>
		<option value="Cardo" <?php if($big_headlines=='Cardo'){echo ' selected="selected"';} ?>>Cardo</option>
		<option value="Carter One" <?php if($big_headlines=='Carter One'){echo ' selected="selected"';} ?>>Carter One</option>
		<option value="Caudex" <?php if($big_headlines=='Caudex'){echo ' selected="selected"';} ?>>Caudex</option>
		<option value="Cedarville+Cursive" <?php if($big_headlines=='Cedarville+Cursive'){echo ' selected="selected"';} ?>>Cedarville Cursive</option>
		<option value="Cherry+Cream+Soda" <?php if($big_headlines=='Cherry+Cream+Soda'){echo ' selected="selected"';} ?>>Cherry Cream Soda</option>
		<option value="Chewy" <?php if($big_headlines=='Chewy'){echo ' selected="selected"';} ?>>Chewy</option>
		<option value="Coda" <?php if($big_headlines=='Coda'){echo ' selected="selected"';} ?>>Coda</option>
		<option value="Coming+Soon" <?php if($big_headlines=='Coming+Soon'){echo ' selected="selected"';} ?>>Coming Soon</option>
		<option value="Copse" <?php if($big_headlines=='Copse'){echo ' selected="selected"';} ?>>Copse</option>
		<option value="Corben" <?php if($big_headlines=='Corben'){echo ' selected="selected"';} ?>>Corben</option>
		<option value="Cousine" <?php if($big_headlines=='Cousine'){echo ' selected="selected"';} ?>>Cousine</option>
		<option value="Covered+By+Your+Grace" <?php if($big_headlines=='Covered+By+Your+Grace'){echo ' selected="selected"';} ?>>Covered By Your Grace</option>
		<option value="Crafty+Girls" <?php if($big_headlines=='Crafty+Girls'){echo ' selected="selected"';} ?>>Crafty Girls</option>
		<option value="Crimson+Text" <?php if($big_headlines=='Crimson+Text'){echo ' selected="selected"';} ?>>Crimson Text</option>
		<option value="Crushed" <?php if($big_headlines=='Crushed'){echo ' selected="selected"';} ?>>Crushed</option>
		<option value="Cuprum" <?php if($big_headlines=='Cuprum'){echo ' selected="selected"';} ?>>Cuprum</option>
		<option value="Damion" <?php if($big_headlines=='Damion'){echo ' selected="selected"';} ?>>Damion</option>
		<option value="Dancing+Script" <?php if($big_headlines=='Dancing+Script'){echo ' selected="selected"';} ?>>Dancing Script</option>
		<option value="Dawning+of+a+New+Day" <?php if($big_headlines=='Dawning+of+a+New+Day'){echo ' selected="selected"';} ?>>Dawning of a New Day</option>
		<option value="Didact+Gothic" <?php if($big_headlines=='Didact+Gothic'){echo ' selected="selected"';} ?>>Didact Gothic</option>
		<option value="Droid+Sans" <?php if($big_headlines=='Droid+Sans'){echo ' selected="selected"';} ?>>Droid Sans</option>
		<option value="Droid+Sans+Mono" <?php if($big_headlines=='Droid+Sans+Mono'){echo ' selected="selected"';} ?>>Droid Sans Mono</option>
		<option value="Droid+Serif" <?php if($big_headlines=='Droid+Serif'){echo ' selected="selected"';} ?>>Droid Serif</option>
		<option value="EB+Garamond" <?php if($big_headlines=='EB+Garamond'){echo ' selected="selected"';} ?>>EB Garamond</option>
		<option value="Expletus+Sans" <?php if($big_headlines=='Expletus+Sans'){echo ' selected="selected"';} ?>>Expletus Sans</option>
		<option value="Fontdiner+Swanky" <?php if($big_headlines=='Fontdiner+Swanky'){echo ' selected="selected"';} ?>>Fontdiner Swanky</option>
		<option value="Forum" <?php if($big_headlines=='Forum'){echo ' selected="selected"';} ?>>Forum</option>
		<option value="Geo" <?php if($big_headlines=='Geo'){echo ' selected="selected"';} ?>>Geo</option>
		<option value="Give+You+Glory" <?php if($big_headlines=='Give+You+Glory'){echo ' selected="selected"';} ?>>Give You Glory</option>
		<option value="Goblin+One" <?php if($big_headlines=='Goblin+One'){echo ' selected="selected"';} ?>>Goblin One</option>
		<option value="Goudy+Bookletter+1911" <?php if($big_headlines=='Goudy+Bookletter+1911'){echo ' selected="selected"';} ?>>Goudy Bookletter 1911</option>
		<option value="Gravitas+One" <?php if($big_headlines=='Gravitas+One'){echo ' selected="selected"';} ?>>Gravitas One</option>
		<option value="Gruppo" <?php if($big_headlines=='Gruppo'){echo ' selected="selected"';} ?>>Gruppo</option>
		<option value="Hammersmith+One" <?php if($big_headlines=='Hammersmith+One'){echo ' selected="selected"';} ?>>Hammersmith One</option>
		<option value="Holtwood+One+SC" <?php if($big_headlines=='Holtwood+One+SC'){echo ' selected="selected"';} ?>>Holtwood One SC</option>
		<option value="Homemade+Apple" <?php if($big_headlines=='Homemade+Apple'){echo ' selected="selected"';} ?>>Homemade Apple</option>
		<option value="Inconsolata" <?php if($big_headlines=='Inconsolata'){echo ' selected="selected"';} ?>>Inconsolata</option>
		<option value="Indie+Flower" <?php if($big_headlines=='Indie+Flower'){echo ' selected="selected"';} ?>>Indie Flower</option>
		<option value="IM+Fell+DW+Pica" <?php if($big_headlines=='IM+Fell+DW+Pica'){echo ' selected="selected"';} ?>>IM Fell DW Pica</option>
		<option value="IM+Fell+DW+Pica+SC" <?php if($big_headlines=='IM+Fell+DW+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell DW Pica SC</option>
		<option value="IM+Fell+Double+Pica" <?php if($big_headlines=='IM+Fell+Double+Pica'){echo ' selected="selected"';} ?>>IM Fell Double Pica</option>
		<option value="IM+Fell+Double+Pica+SC" <?php if($big_headlines=='IM+Fell+Double+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell Double Pica SC</option>
		<option value="IM+Fell+English" <?php if($big_headlines=='IM+Fell+English'){echo ' selected="selected"';} ?>>IM Fell English</option>
		<option value="IM+Fell+English+SC" <?php if($big_headlines=='IM+Fell+English+SC'){echo ' selected="selected"';} ?>>IM Fell English SC</option>
		<option value="IM+Fell+French+Canon" <?php if($big_headlines=='IM+Fell+French+Canon'){echo ' selected="selected"';} ?>>IM Fell French Canon</option>
		<option value="IM+Fell+French+Canon+SC" <?php if($big_headlines=='IM+Fell+French+Canon+SC'){echo ' selected="selected"';} ?>>IM Fell French Canon SC</option>
		<option value="IM+Fell+Great+Primer" <?php if($big_headlines=='IM+Fell+Great+Primer'){echo ' selected="selected"';} ?>>IM Fell Great Primer</option>
		<option value="IM+Fell+Great+Primer+SC" <?php if($big_headlines=='IM+Fell+Great+Primer+SC'){echo ' selected="selected"';} ?>>IM Fell Great Primer SC</option>
		<option value="Irish+Grover" <?php if($big_headlines=='Irish+Grover'){echo ' selected="selected"';} ?>>Irish Grover</option>
		<option value="Irish+Growler" <?php if($big_headlines=='Irish+Growler'){echo ' selected="selected"';} ?>>Irish Growler</option>
		<option value="Istok+Web" <?php if($big_headlines=='Istok+Web'){echo ' selected="selected"';} ?>>Istok Web</option>
		<option value="Josefin+Sans" <?php if($big_headlines=='Josefin+Sans'){echo ' selected="selected"';} ?>>Josefin Sans Regular 400</option>
		<option value="Josefin+Slab" <?php if($big_headlines=='Josefin+Slab'){echo ' selected="selected"';} ?>>Josefin Slab Regular 400</option>
		<option value="Judson" <?php if($big_headlines=='Judson'){echo ' selected="selected"';} ?>>Judson</option>
		<option value="Jura" <?php if($big_headlines=='Jura'){echo ' selected="selected"';} ?>> Jura Regular</option>
		<option value="Just+Another+Hand" <?php if($big_headlines=='Just+Another+Hand'){echo ' selected="selected"';} ?>>Just Another Hand</option>
		<option value="Just+Me+Again+Down+Here" <?php if($big_headlines=='Just+Me+Again+Down+Here'){echo ' selected="selected"';} ?>>Just Me Again Down Here</option>
		<option value="Kameron" <?php if($big_headlines=='Kameron'){echo ' selected="selected"';} ?>>Kameron</option>
		<option value="Kenia" <?php if($big_headlines=='Kenia'){echo ' selected="selected"';} ?>>Kenia</option>
		<option value="Kranky" <?php if($big_headlines=='Kranky'){echo ' selected="selected"';} ?>>Kranky</option>
		<option value="Kreon" <?php if($big_headlines=='Kreon'){echo ' selected="selected"';} ?>>Kreon</option>
		<option value="Kristi" <?php if($big_headlines=='Kristi'){echo ' selected="selected"';} ?>>Kristi</option>
		<option value="La+Belle+Aurore" <?php if($big_headlines=='La+Belle+Aurore'){echo ' selected="selected"';} ?>>La Belle Aurore</option>
		<option value="Lato" <?php if($big_headlines=='Lato'){echo ' selected="selected"';} ?>>Lato</option>
		<option value="League+Script" <?php if($big_headlines=='League+Script'){echo ' selected="selected"';} ?>>League Script</option>
		<option value="Lekton" <?php if($big_headlines=='Lekton'){echo ' selected="selected"';} ?>> Lekton </option>
		<option value="Limelight" <?php if($big_headlines=='Limelight'){echo ' selected="selected"';} ?>> Limelight </option>
		<option value="Lobster" <?php if($big_headlines=='Lobster'){echo ' selected="selected"';} ?>>Lobster</option>
		<option value="Lobster Two" <?php if($big_headlines=='Lobster Two'){echo ' selected="selected"';} ?>>Lobster Two</option>
		<option value="Lora" <?php if($big_headlines=='Lora'){echo ' selected="selected"';} ?>>Lora</option>
		<option value="Love+Ya+Like+A+Sister" <?php if($big_headlines=='Love+Ya+Like+A+Sister'){echo ' selected="selected"';} ?>>Love Ya Like A Sister</option>
		<option value="Loved+by+the+King" <?php if($big_headlines=='Loved+by+the+King'){echo ' selected="selected"';} ?>>Loved by the King</option>
		<option value="Luckiest+Guy" <?php if($big_headlines=='Luckiest+Guy'){echo ' selected="selected"';} ?>>Luckiest Guy</option>
		<option value="Maiden+Orange" <?php if($big_headlines=='Maiden+Orange'){echo ' selected="selected"';} ?>>Maiden Orange</option>
		<option value="Mako" <?php if($big_headlines=='Mako'){echo ' selected="selected"';} ?>>Mako</option>
		<option value="Maven+Pro" <?php if($big_headlines=='Maven+Pro'){echo ' selected="selected"';} ?>> Maven Pro</option>
		<option value="Meddon" <?php if($big_headlines=='Meddon'){echo ' selected="selected"';} ?>>Meddon</option>
		<option value="MedievalSharp" <?php if($big_headlines=='MedievalSharp'){echo ' selected="selected"';} ?>>MedievalSharp</option>
		<option value="Megrim" <?php if($big_headlines=='Megrim'){echo ' selected="selected"';} ?>>Megrim</option>
		<option value="Merriweather" <?php if($big_headlines=='Merriweather'){echo ' selected="selected"';} ?>>Merriweather</option>
		<option value="Metrophobic" <?php if($big_headlines=='Metrophobic'){echo ' selected="selected"';} ?>>Metrophobic</option>
		<option value="Michroma" <?php if($big_headlines=='Michroma'){echo ' selected="selected"';} ?>>Michroma</option>
		<option value="Miltonian Tattoo" <?php if($big_headlines=='Miltonian Tattoo'){echo ' selected="selected"';} ?>>Miltonian Tattoo</option>
		<option value="Miltonian" <?php if($big_headlines=='Miltonian'){echo ' selected="selected"';} ?>>Miltonian</option>
		<option value="Modern Antiqua" <?php if($big_headlines=='Modern Antiqua'){echo ' selected="selected"';} ?>>Modern Antiqua</option>
		<option value="Monofett" <?php if($big_headlines=='Monofett'){echo ' selected="selected"';} ?>>Monofett</option>
		<option value="Molengo" <?php if($big_headlines=='Molengo'){echo ' selected="selected"';} ?>>Molengo</option>
		<option value="Mountains of Christmas" <?php if($big_headlines=='Mountains of Christmas'){echo ' selected="selected"';} ?>>Mountains of Christmas</option>
		<option value="Muli" <?php if($big_headlines=='Muli'){echo ' selected="selected"';} ?>>Muli Regular</option>
		<option value="Neucha" <?php if($big_headlines=='Neucha'){echo ' selected="selected"';} ?>>Neucha</option>
		<option value="Neuton" <?php if($big_headlines=='Neuton'){echo ' selected="selected"';} ?>>Neuton</option>
		<option value="News+Cycle" <?php if($big_headlines=='News+Cycle'){echo ' selected="selected"';} ?>>News Cycle</option>
		<option value="Nixie+One" <?php if($big_headlines=='Nixie+One'){echo ' selected="selected"';} ?>>Nixie One</option>
		<option value="Nobile" <?php if($big_headlines=='Nobile'){echo ' selected="selected"';} ?>>Nobile</option>
		<option value="Nova+Cut" <?php if($big_headlines=='Nova+Cut'){echo ' selected="selected"';} ?>>Nova Cut</option>
		<option value="Nova+Flat" <?php if($big_headlines=='Nova+Flat'){echo ' selected="selected"';} ?>>Nova Flat</option>
		<option value="Nova+Mono" <?php if($big_headlines=='Nova+Mono'){echo ' selected="selected"';} ?>>Nova Mono</option>
		<option value="Nova+Oval" <?php if($big_headlines=='Nova+Oval'){echo ' selected="selected"';} ?>>Nova Oval</option>
		<option value="Nova+Round" <?php if($big_headlines=='Nova+Round'){echo ' selected="selected"';} ?>>Nova Round</option>
		<option value="Nova+Script" <?php if($big_headlines=='Nova+Script'){echo ' selected="selected"';} ?>>Nova Script</option>
		<option value="Nova+Slim" <?php if($big_headlines=='Nova+Slim'){echo ' selected="selected"';} ?>>Nova Slim</option>
		<option value="Nova+Square" <?php if($big_headlines=='Nova+Square'){echo ' selected="selected"';} ?>>Nova Square</option>
		<option value="Nunito:light" <?php if($big_headlines=='Nunito:light'){echo ' selected="selected"';} ?>> Nunito Light</option>
		<option value="Nunito" <?php if($big_headlines=='Nunito'){echo ' selected="selected"';} ?>> Nunito Regular</option>
		<option value="OFL+Sorts+Mill+Goudy+TT" <?php if($big_headlines=='OFL+Sorts+Mill+Goudy+TT'){echo ' selected="selected"';} ?>>OFL Sorts Mill Goudy TT</option>
		<option value="Old+Standard+TT" <?php if($big_headlines=='Old+Standard+TT'){echo ' selected="selected"';} ?>>Old Standard TT</option>
		<option value="Open+Sans" <?php if($big_headlines=='Open+Sans'){echo ' selected="selected"';} ?>>Open Sans regular</option>
		<option value="Open+Sans+Condensed" <?php if($big_headlines=='Open+Sans+Condensed'){echo ' selected="selected"';} ?>>Open Sans Condensed</option>
		<option value="Orbitron" <?php if($big_headlines=='Orbitron'){echo ' selected="selected"';} ?>>Orbitron Regular (400)</option>
		<option value="Oswald" <?php if($big_headlines=='Oswald'){echo ' selected="selected"';} ?>>Oswald</option>
		<option value="Over+the+Rainbow" <?php if($big_headlines=='Over+the+Rainbow'){echo ' selected="selected"';} ?>>Over the Rainbow</option>
		<option value="Reenie+Beanie" <?php if($big_headlines=='Reenie+Beanie'){echo ' selected="selected"';} ?>>Reenie Beanie</option>
		<option value="Pacifico" <?php if($big_headlines=='Pacifico'){echo ' selected="selected"';} ?>>Pacifico</option>
		<option value="Patrick+Hand" <?php if($big_headlines=='Patrick+Hand'){echo ' selected="selected"';} ?>>Patrick Hand</option>
		<option value="Paytone+One" <?php if($big_headlines=='Paytone+One'){echo ' selected="selected"';} ?>>Paytone One</option>
		<option value="Permanent+Marker" <?php if($big_headlines=='Permanent+Marker'){echo ' selected="selected"';} ?>>Permanent Marker</option>
		<option value="Philosopher" <?php if($big_headlines=='Philosopher'){echo ' selected="selected"';} ?>>Philosopher</option>
		<option value="Play" <?php if($big_headlines=='Play'){echo ' selected="selected"';} ?>>Play</option>
		<option value="Playfair+Display" <?php if($big_headlines=='Playfair+Display'){echo ' selected="selected"';} ?>> Playfair Display </option>
		<option value="Podkova" <?php if($big_headlines=='Podkova'){echo ' selected="selected"';} ?>> Podkova </option>
		<option value="PT+Sans" <?php if($big_headlines=='PT+Sans'){echo ' selected="selected"';} ?>>PT Sans</option>
		<option value="PT+Sans+Narrow" <?php if($big_headlines=='PT+Sans+Narrow'){echo ' selected="selected"';} ?>>PT Sans Narrow</option>
		<option value="PT+Sans+Narrow:regular,bold" <?php if($big_headlines=='PT+Sans+Narrow:regular,bold'){echo ' selected="selected"';} ?>>PT Sans Narrow (plus bold)</option>
		<option value="PT+Serif" <?php if($big_headlines=='PT+Serif'){echo ' selected="selected"';} ?>>PT Serif</option>
		<option value="PT+Serif Caption" <?php if($big_headlines=='PT+Serif Caption'){echo ' selected="selected"';} ?>>PT Serif Caption</option>
		<option value="Puritan" <?php if($big_headlines=='PT+Serif Caption'){echo ' selected="selected"';} ?>>Puritan</option>
		<option value="Quattrocento" <?php if($big_headlines=='Quattrocento'){echo ' selected="selected"';} ?>>Quattrocento</option>
		<option value="Quattrocento+Sans" <?php if($big_headlines=='Quattrocento+Sans'){echo ' selected="selected"';} ?>>Quattrocento Sans</option>
		<option value="Radley" <?php if($big_headlines=='Radley'){echo ' selected="selected"';} ?>>Radley</option>
		<option value="Raleway" <?php if($big_headlines=='Raleway'){echo ' selected="selected"';} ?>>Raleway</option>
		<option value="Redressed" <?php if($big_headlines=='Redressed'){echo ' selected="selected"';} ?>>Redressed</option>
		<option value="Rock+Salt" <?php if($big_headlines=='Rock+Salt'){echo ' selected="selected"';} ?>>Rock Salt</option>
		<option value="Rokkitt" <?php if($big_headlines=='Rokkitt'){echo ' selected="selected"';} ?>>Rokkitt</option>
		<option value="Ruslan+Display" <?php if($big_headlines=='Ruslan+Display'){echo ' selected="selected"';} ?>>Ruslan Display</option>
		<option value="Schoolbell" <?php if($big_headlines=='Schoolbell'){echo ' selected="selected"';} ?>>Schoolbell</option>
		<option value="Shadows+Into+Light" <?php if($big_headlines=='Shadows+Into+Light'){echo ' selected="selected"';} ?>>Shadows Into Light</option>
		<option value="Shanti" <?php if($big_headlines=='Shanti'){echo ' selected="selected"';} ?>>Shanti</option>
		<option value="Sigmar+One" <?php if($big_headlines=='Sigmar+One'){echo ' selected="selected"';} ?>>Sigmar One</option>
		<option value="Six+Caps" <?php if($big_headlines=='Six+Caps'){echo ' selected="selected"';} ?>>Six Caps</option>
		<option value="Slackey" <?php if($big_headlines=='Slackey'){echo ' selected="selected"';} ?>>Slackey</option>
		<option value="Smythe" <?php if($big_headlines=='Smythe'){echo ' selected="selected"';} ?>>Smythe</option>
		<option value="Sniglet" <?php if($big_headlines=='Sniglet'){echo ' selected="selected"';} ?>>Sniglet</option>
		<option value="Special+Elite" <?php if($big_headlines=='Special+Elite'){echo ' selected="selected"';} ?>>Special Elite</option>
		<option value="Stardos+Stencil" <?php if($big_headlines=='Stardos+Stencil'){echo ' selected="selected"';} ?>>Stardos Stencil</option>
		<option value="Sue+Ellen+Francisco" <?php if($big_headlines=='Sue+Ellen+Francisco'){echo ' selected="selected"';} ?>>Sue Ellen Francisco</option>
		<option value="Sunshiney" <?php if($big_headlines=='Sunshiney'){echo ' selected="selected"';} ?>>Sunshiney</option>
		<option value="Swanky+and+Moo+Moo" <?php if($big_headlines=='Swanky+and+Moo+Moo'){echo ' selected="selected"';} ?>>Swanky and Moo Moo</option>
		<option value="Syncopate" <?php if($big_headlines=='Syncopate'){echo ' selected="selected"';} ?>>Syncopate</option>
		<option value="Tangerine" <?php if($big_headlines=='Tangerine'){echo ' selected="selected"';} ?>>Tangerine</option>
		<option value="Tenor+Sans" <?php if($big_headlines=='Tenor+Sans'){echo ' selected="selected"';} ?>> Tenor Sans </option>
		<option value="Terminal+Dosis+Light" <?php if($big_headlines=='Terminal+Dosis+Light'){echo ' selected="selected"';} ?>>Terminal Dosis Light</option>
		<option value="The+Girl+Next+Door" <?php if($big_headlines=='The+Girl+Next+Door'){echo ' selected="selected"';} ?>>The Girl Next Door</option>
		<option value="Tinos" <?php if($big_headlines=='Tinos'){echo ' selected="selected"';} ?>>Tinos</option>
		<option value="Ubuntu" <?php if($big_headlines=='Ubuntu'){echo ' selected="selected"';} ?>>Ubuntu</option>
		<option value="Ultra" <?php if($big_headlines=='Ultra'){echo ' selected="selected"';} ?>>Ultra</option>
		<option value="Unkempt" <?php if($big_headlines=='Unkempt'){echo ' selected="selected"';} ?>>Unkempt</option>
		<option value="UnifrakturCook:bold" <?php if($big_headlines=='UnifrakturCook:bold'){echo ' selected="selected"';} ?>>UnifrakturCook</option>
		<option value="UnifrakturMaguntia" <?php if($big_headlines=='UnifrakturMaguntia'){echo ' selected="selected"';} ?>>UnifrakturMaguntia</option>
		<option value="Varela" <?php if($big_headlines=='Varela'){echo ' selected="selected"';} ?>>Varela</option>
		<option value="Varela Round" <?php if($big_headlines=='Varela Round'){echo ' selected="selected"';} ?>>Varela Round</option>
		<option value="Vibur" <?php if($big_headlines=='Vibur'){echo ' selected="selected"';} ?>>Vibur</option>
		<option value="Vollkorn" <?php if($big_headlines=='Vollkorn'){echo ' selected="selected"';} ?>>Vollkorn</option>
		<option value="VT323" <?php if($big_headlines=='VT323'){echo ' selected="selected"';} ?>>VT323</option>
		<option value="Waiting+for+the+Sunrise" <?php if($big_headlines=='Waiting+for+the+Sunrise'){echo ' selected="selected"';} ?>>Waiting for the Sunrise</option>
		<option value="Wallpoet" <?php if($big_headlines=='Wallpoet'){echo ' selected="selected"';} ?>>Wallpoet</option>
		<option value="Walter+Turncoat" <?php if($big_headlines=='Walter+Turncoat'){echo ' selected="selected"';} ?>>Walter Turncoat</option>
		<option value="Wire+One" <?php if($big_headlines=='Wire+One'){echo ' selected="selected"';} ?>>Wire One</option>
		<option value="Yanone+Kaffeesatz" <?php if($big_headlines=='Yanone+Kaffeesatz'){echo ' selected="selected"';} ?>>Yanone Kaffeesatz</option>
		<option value="Yeseva+One" <?php if($big_headlines=='Yeseva+One'){echo ' selected="selected"';} ?>>Yeseva One</option>
		<option value="Zeyada" <?php if($big_headlines=='Zeyada'){echo ' selected="selected"';} ?>>Zeyada</option>  
								
								</select>
								<select name="big_headlines_px" style="width:80px">
								
									<?php for( $x = 9; $x <= 50; $x++ ) { ?>
					              <option value="<?php echo $x; ?>" <?php if($big_headlines_px==$x || ($x == 25 && $big_headlines_px < 6)){echo ' selected="selected"';} ?>><?php echo $x; ?> px</option>
									<?php } ?>
								
								</select>
							
								<div class="clear"></div>
							
							</div>
							
							<!-- End Input -->
							
							<!-- Input -->
							
							<div class="input">
							
								<p>Price (custom_font)</p>
								<select name="custom_price">
								
		              <?php if (isset($custom_price)) {
		              $selected = "selected"; }
		              ?>
		<option value="standard" <?php if($custom_price=='standard'){echo ' selected="selected"';} ?>>Standard</option>
		<option value="Francois+One" <?php if($custom_price=='Francois+One'){echo ' selected="selected"';} ?>>Francois One</option>
				  <option value="Arial" <?php if($custom_price=='Arial'){echo ' selected="selected"';} ?>>Arial</option>
		<option value="Aclonica" <?php if($custom_price=='Aclonica'){echo ' selected="selected"';} ?>>Aclonica</option>
		<option value="Allan" <?php if($custom_price=='Allan'){echo ' selected="selected"';} ?>>Allan</option>
		<option value="Annie+Use+Your+Telescope" <?php if($custom_price=='Annie+Use+Your+Telescope'){echo ' selected="selected"';} ?>>Annie Use Your Telescope</option>
		<option value="Anonymous+Pro" <?php if($custom_price=='Anonymous+Pro'){echo ' selected="selected"';} ?>>Anonymous Pro</option>
		<option value="Allerta+Stencil" <?php if($custom_price=='Allerta+Stencil'){echo ' selected="selected"';} ?>>Allerta Stencil</option>
		<option value="Allerta" <?php if($custom_price=='Allerta'){echo ' selected="selected"';} ?>>Allerta</option>
		<option value="Amaranth" <?php if($custom_price=='Amaranth'){echo ' selected="selected"';} ?>>Amaranth</option>
		<option value="Anton" <?php if($custom_price=='Anton'){echo ' selected="selected"';} ?>>Anton</option>
		<option value="Architects+Daughter" <?php if($custom_price=='Architects+Daughter'){echo ' selected="selected"';} ?>>Architects Daughter</option>
		<option value="Arimo" <?php if($custom_price=='Arimo'){echo ' selected="selected"';} ?>>Arimo</option>
		<option value="Artifika" <?php if($custom_price=='Artifika'){echo ' selected="selected"';} ?>>Artifika</option>
		<option value="Arvo" <?php if($custom_price=='Arvo'){echo ' selected="selected"';} ?>>Arvo</option>
		<option value="Asset" <?php if($custom_price=='Asset'){echo ' selected="selected"';} ?>>Asset</option>
		<option value="Astloch" <?php if($custom_price=='Astloch'){echo ' selected="selected"';} ?>>Astloch</option>
		<option value="Bangers" <?php if($custom_price=='Bangers'){echo ' selected="selected"';} ?>>Bangers</option>
		<option value="Bentham" <?php if($custom_price=='Bentham'){echo ' selected="selected"';} ?>>Bentham</option>
		<option value="Bevan" <?php if($custom_price=='Bevan'){echo ' selected="selected"';} ?>>Bevan</option>
		<option value="Bigshot+One" <?php if($custom_price=='Bigshot+One'){echo ' selected="selected"';} ?>>Bigshot One</option>
		<option value="Bowlby+One" <?php if($custom_price=='Bowlby+One'){echo ' selected="selected"';} ?>>Bowlby One</option>
		<option value="Bowlby+One+SC" <?php if($custom_price=='Bowlby+One+SC'){echo ' selected="selected"';} ?>>Bowlby One SC</option>
		<option value="Brawler" <?php if($custom_price=='Brawler'){echo ' selected="selected"';} ?>>Brawler </option>
		<option value="Buda" <?php if($custom_price=='Buda'){echo ' selected="selected"';} ?>>Buda</option>
		<option value="Cabin" <?php if($custom_price=='Cabin'){echo ' selected="selected"';} ?>>Cabin</option>
		<option value="Calligraffitti" <?php if($custom_price=='Calligraffitti'){echo ' selected="selected"';} ?>>Calligraffitti</option>
		<option value="Candal" <?php if($custom_price=='Candal'){echo ' selected="selected"';} ?>>Candal</option>
		<option value="Cantarell" <?php if($custom_price=='Cantarell'){echo ' selected="selected"';} ?>>Cantarell</option>
		<option value="Cardo" <?php if($custom_price=='Cardo'){echo ' selected="selected"';} ?>>Cardo</option>
		<option value="Carter One" <?php if($custom_price=='Carter One'){echo ' selected="selected"';} ?>>Carter One</option>
		<option value="Caudex" <?php if($custom_price=='Caudex'){echo ' selected="selected"';} ?>>Caudex</option>
		<option value="Cedarville+Cursive" <?php if($custom_price=='Cedarville+Cursive'){echo ' selected="selected"';} ?>>Cedarville Cursive</option>
		<option value="Cherry+Cream+Soda" <?php if($custom_price=='Cherry+Cream+Soda'){echo ' selected="selected"';} ?>>Cherry Cream Soda</option>
		<option value="Chewy" <?php if($custom_price=='Chewy'){echo ' selected="selected"';} ?>>Chewy</option>
		<option value="Coda" <?php if($custom_price=='Coda'){echo ' selected="selected"';} ?>>Coda</option>
		<option value="Coming+Soon" <?php if($custom_price=='Coming+Soon'){echo ' selected="selected"';} ?>>Coming Soon</option>
		<option value="Copse" <?php if($custom_price=='Copse'){echo ' selected="selected"';} ?>>Copse</option>
		<option value="Corben" <?php if($custom_price=='Corben'){echo ' selected="selected"';} ?>>Corben</option>
		<option value="Cousine" <?php if($custom_price=='Cousine'){echo ' selected="selected"';} ?>>Cousine</option>
		<option value="Covered+By+Your+Grace" <?php if($custom_price=='Covered+By+Your+Grace'){echo ' selected="selected"';} ?>>Covered By Your Grace</option>
		<option value="Crafty+Girls" <?php if($custom_price=='Crafty+Girls'){echo ' selected="selected"';} ?>>Crafty Girls</option>
		<option value="Crimson+Text" <?php if($custom_price=='Crimson+Text'){echo ' selected="selected"';} ?>>Crimson Text</option>
		<option value="Crushed" <?php if($custom_price=='Crushed'){echo ' selected="selected"';} ?>>Crushed</option>
		<option value="Cuprum" <?php if($custom_price=='Cuprum'){echo ' selected="selected"';} ?>>Cuprum</option>
		<option value="Damion" <?php if($custom_price=='Damion'){echo ' selected="selected"';} ?>>Damion</option>
		<option value="Dancing+Script" <?php if($custom_price=='Dancing+Script'){echo ' selected="selected"';} ?>>Dancing Script</option>
		<option value="Dawning+of+a+New+Day" <?php if($custom_price=='Dawning+of+a+New+Day'){echo ' selected="selected"';} ?>>Dawning of a New Day</option>
		<option value="Didact+Gothic" <?php if($custom_price=='Didact+Gothic'){echo ' selected="selected"';} ?>>Didact Gothic</option>
		<option value="Droid+Sans" <?php if($custom_price=='Droid+Sans'){echo ' selected="selected"';} ?>>Droid Sans</option>
		<option value="Droid+Sans+Mono" <?php if($custom_price=='Droid+Sans+Mono'){echo ' selected="selected"';} ?>>Droid Sans Mono</option>
		<option value="Droid+Serif" <?php if($custom_price=='Droid+Serif'){echo ' selected="selected"';} ?>>Droid Serif</option>
		<option value="EB+Garamond" <?php if($custom_price=='EB+Garamond'){echo ' selected="selected"';} ?>>EB Garamond</option>
		<option value="Expletus+Sans" <?php if($custom_price=='Expletus+Sans'){echo ' selected="selected"';} ?>>Expletus Sans</option>
		<option value="Fontdiner+Swanky" <?php if($custom_price=='Fontdiner+Swanky'){echo ' selected="selected"';} ?>>Fontdiner Swanky</option>
		<option value="Forum" <?php if($custom_price=='Forum'){echo ' selected="selected"';} ?>>Forum</option>
		<option value="Geo" <?php if($custom_price=='Geo'){echo ' selected="selected"';} ?>>Geo</option>
		<option value="Give+You+Glory" <?php if($custom_price=='Give+You+Glory'){echo ' selected="selected"';} ?>>Give You Glory</option>
		<option value="Goblin+One" <?php if($custom_price=='Goblin+One'){echo ' selected="selected"';} ?>>Goblin One</option>
		<option value="Goudy+Bookletter+1911" <?php if($custom_price=='Goudy+Bookletter+1911'){echo ' selected="selected"';} ?>>Goudy Bookletter 1911</option>
		<option value="Gravitas+One" <?php if($custom_price=='Gravitas+One'){echo ' selected="selected"';} ?>>Gravitas One</option>
		<option value="Gruppo" <?php if($custom_price=='Gruppo'){echo ' selected="selected"';} ?>>Gruppo</option>
		<option value="Hammersmith+One" <?php if($custom_price=='Hammersmith+One'){echo ' selected="selected"';} ?>>Hammersmith One</option>
		<option value="Holtwood+One+SC" <?php if($custom_price=='Holtwood+One+SC'){echo ' selected="selected"';} ?>>Holtwood One SC</option>
		<option value="Homemade+Apple" <?php if($custom_price=='Homemade+Apple'){echo ' selected="selected"';} ?>>Homemade Apple</option>
		<option value="Inconsolata" <?php if($custom_price=='Inconsolata'){echo ' selected="selected"';} ?>>Inconsolata</option>
		<option value="Indie+Flower" <?php if($custom_price=='Indie+Flower'){echo ' selected="selected"';} ?>>Indie Flower</option>
		<option value="IM+Fell+DW+Pica" <?php if($custom_price=='IM+Fell+DW+Pica'){echo ' selected="selected"';} ?>>IM Fell DW Pica</option>
		<option value="IM+Fell+DW+Pica+SC" <?php if($custom_price=='IM+Fell+DW+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell DW Pica SC</option>
		<option value="IM+Fell+Double+Pica" <?php if($custom_price=='IM+Fell+Double+Pica'){echo ' selected="selected"';} ?>>IM Fell Double Pica</option>
		<option value="IM+Fell+Double+Pica+SC" <?php if($custom_price=='IM+Fell+Double+Pica+SC'){echo ' selected="selected"';} ?>>IM Fell Double Pica SC</option>
		<option value="IM+Fell+English" <?php if($custom_price=='IM+Fell+English'){echo ' selected="selected"';} ?>>IM Fell English</option>
		<option value="IM+Fell+English+SC" <?php if($custom_price=='IM+Fell+English+SC'){echo ' selected="selected"';} ?>>IM Fell English SC</option>
		<option value="IM+Fell+French+Canon" <?php if($custom_price=='IM+Fell+French+Canon'){echo ' selected="selected"';} ?>>IM Fell French Canon</option>
		<option value="IM+Fell+French+Canon+SC" <?php if($custom_price=='IM+Fell+French+Canon+SC'){echo ' selected="selected"';} ?>>IM Fell French Canon SC</option>
		<option value="IM+Fell+Great+Primer" <?php if($custom_price=='IM+Fell+Great+Primer'){echo ' selected="selected"';} ?>>IM Fell Great Primer</option>
		<option value="IM+Fell+Great+Primer+SC" <?php if($custom_price=='IM+Fell+Great+Primer+SC'){echo ' selected="selected"';} ?>>IM Fell Great Primer SC</option>
		<option value="Irish+Grover" <?php if($custom_price=='Irish+Grover'){echo ' selected="selected"';} ?>>Irish Grover</option>
		<option value="Irish+Growler" <?php if($custom_price=='Irish+Growler'){echo ' selected="selected"';} ?>>Irish Growler</option>
		<option value="Istok+Web" <?php if($custom_price=='Istok+Web'){echo ' selected="selected"';} ?>>Istok Web</option>
		<option value="Josefin+Sans" <?php if($custom_price=='Josefin+Sans'){echo ' selected="selected"';} ?>>Josefin Sans Regular 400</option>
		<option value="Josefin+Slab" <?php if($custom_price=='Josefin+Slab'){echo ' selected="selected"';} ?>>Josefin Slab Regular 400</option>
		<option value="Judson" <?php if($custom_price=='Judson'){echo ' selected="selected"';} ?>>Judson</option>
		<option value="Jura" <?php if($custom_price=='Jura'){echo ' selected="selected"';} ?>> Jura Regular</option>
		<option value="Just+Another+Hand" <?php if($custom_price=='Just+Another+Hand'){echo ' selected="selected"';} ?>>Just Another Hand</option>
		<option value="Just+Me+Again+Down+Here" <?php if($custom_price=='Just+Me+Again+Down+Here'){echo ' selected="selected"';} ?>>Just Me Again Down Here</option>
		<option value="Kameron" <?php if($custom_price=='Kameron'){echo ' selected="selected"';} ?>>Kameron</option>
		<option value="Kenia" <?php if($custom_price=='Kenia'){echo ' selected="selected"';} ?>>Kenia</option>
		<option value="Kranky" <?php if($custom_price=='Kranky'){echo ' selected="selected"';} ?>>Kranky</option>
		<option value="Kreon" <?php if($custom_price=='Kreon'){echo ' selected="selected"';} ?>>Kreon</option>
		<option value="Kristi" <?php if($custom_price=='Kristi'){echo ' selected="selected"';} ?>>Kristi</option>
		<option value="La+Belle+Aurore" <?php if($custom_price=='La+Belle+Aurore'){echo ' selected="selected"';} ?>>La Belle Aurore</option>
		<option value="Lato" <?php if($custom_price=='Lato'){echo ' selected="selected"';} ?>>Lato</option>
		<option value="League+Script" <?php if($custom_price=='League+Script'){echo ' selected="selected"';} ?>>League Script</option>
		<option value="Lekton" <?php if($custom_price=='Lekton'){echo ' selected="selected"';} ?>> Lekton </option>
		<option value="Limelight" <?php if($custom_price=='Limelight'){echo ' selected="selected"';} ?>> Limelight </option>
		<option value="Lobster" <?php if($custom_price=='Lobster'){echo ' selected="selected"';} ?>>Lobster</option>
		<option value="Lobster Two" <?php if($custom_price=='Lobster Two'){echo ' selected="selected"';} ?>>Lobster Two</option>
		<option value="Lora" <?php if($custom_price=='Lora'){echo ' selected="selected"';} ?>>Lora</option>
		<option value="Love+Ya+Like+A+Sister" <?php if($custom_price=='Love+Ya+Like+A+Sister'){echo ' selected="selected"';} ?>>Love Ya Like A Sister</option>
		<option value="Loved+by+the+King" <?php if($custom_price=='Loved+by+the+King'){echo ' selected="selected"';} ?>>Loved by the King</option>
		<option value="Luckiest+Guy" <?php if($custom_price=='Luckiest+Guy'){echo ' selected="selected"';} ?>>Luckiest Guy</option>
		<option value="Maiden+Orange" <?php if($custom_price=='Maiden+Orange'){echo ' selected="selected"';} ?>>Maiden Orange</option>
		<option value="Mako" <?php if($custom_price=='Mako'){echo ' selected="selected"';} ?>>Mako</option>
		<option value="Maven+Pro" <?php if($custom_price=='Maven+Pro'){echo ' selected="selected"';} ?>> Maven Pro</option>
		<option value="Meddon" <?php if($custom_price=='Meddon'){echo ' selected="selected"';} ?>>Meddon</option>
		<option value="MedievalSharp" <?php if($custom_price=='MedievalSharp'){echo ' selected="selected"';} ?>>MedievalSharp</option>
		<option value="Megrim" <?php if($custom_price=='Megrim'){echo ' selected="selected"';} ?>>Megrim</option>
		<option value="Merriweather" <?php if($custom_price=='Merriweather'){echo ' selected="selected"';} ?>>Merriweather</option>
		<option value="Metrophobic" <?php if($custom_price=='Metrophobic'){echo ' selected="selected"';} ?>>Metrophobic</option>
		<option value="Michroma" <?php if($custom_price=='Michroma'){echo ' selected="selected"';} ?>>Michroma</option>
		<option value="Miltonian Tattoo" <?php if($custom_price=='Miltonian Tattoo'){echo ' selected="selected"';} ?>>Miltonian Tattoo</option>
		<option value="Miltonian" <?php if($custom_price=='Miltonian'){echo ' selected="selected"';} ?>>Miltonian</option>
		<option value="Modern Antiqua" <?php if($custom_price=='Modern Antiqua'){echo ' selected="selected"';} ?>>Modern Antiqua</option>
		<option value="Monofett" <?php if($custom_price=='Monofett'){echo ' selected="selected"';} ?>>Monofett</option>
		<option value="Molengo" <?php if($custom_price=='Molengo'){echo ' selected="selected"';} ?>>Molengo</option>
		<option value="Mountains of Christmas" <?php if($custom_price=='Mountains of Christmas'){echo ' selected="selected"';} ?>>Mountains of Christmas</option>
		<option value="Muli" <?php if($custom_price=='Muli'){echo ' selected="selected"';} ?>>Muli Regular</option>
		<option value="Neucha" <?php if($custom_price=='Neucha'){echo ' selected="selected"';} ?>>Neucha</option>
		<option value="Neuton" <?php if($custom_price=='Neuton'){echo ' selected="selected"';} ?>>Neuton</option>
		<option value="News+Cycle" <?php if($custom_price=='News+Cycle'){echo ' selected="selected"';} ?>>News Cycle</option>
		<option value="Nixie+One" <?php if($custom_price=='Nixie+One'){echo ' selected="selected"';} ?>>Nixie One</option>
		<option value="Nobile" <?php if($custom_price=='Nobile'){echo ' selected="selected"';} ?>>Nobile</option>
		<option value="Nova+Cut" <?php if($custom_price=='Nova+Cut'){echo ' selected="selected"';} ?>>Nova Cut</option>
		<option value="Nova+Flat" <?php if($custom_price=='Nova+Flat'){echo ' selected="selected"';} ?>>Nova Flat</option>
		<option value="Nova+Mono" <?php if($custom_price=='Nova+Mono'){echo ' selected="selected"';} ?>>Nova Mono</option>
		<option value="Nova+Oval" <?php if($custom_price=='Nova+Oval'){echo ' selected="selected"';} ?>>Nova Oval</option>
		<option value="Nova+Round" <?php if($custom_price=='Nova+Round'){echo ' selected="selected"';} ?>>Nova Round</option>
		<option value="Nova+Script" <?php if($custom_price=='Nova+Script'){echo ' selected="selected"';} ?>>Nova Script</option>
		<option value="Nova+Slim" <?php if($custom_price=='Nova+Slim'){echo ' selected="selected"';} ?>>Nova Slim</option>
		<option value="Nova+Square" <?php if($custom_price=='Nova+Square'){echo ' selected="selected"';} ?>>Nova Square</option>
		<option value="Nunito:light" <?php if($custom_price=='Nunito:light'){echo ' selected="selected"';} ?>> Nunito Light</option>
		<option value="Nunito" <?php if($custom_price=='Nunito'){echo ' selected="selected"';} ?>> Nunito Regular</option>
		<option value="OFL+Sorts+Mill+Goudy+TT" <?php if($custom_price=='OFL+Sorts+Mill+Goudy+TT'){echo ' selected="selected"';} ?>>OFL Sorts Mill Goudy TT</option>
		<option value="Old+Standard+TT" <?php if($custom_price=='Old+Standard+TT'){echo ' selected="selected"';} ?>>Old Standard TT</option>
		<option value="Open+Sans" <?php if($custom_price=='Open+Sans'){echo ' selected="selected"';} ?>>Open Sans regular</option>
		<option value="Open+Sans+Condensed" <?php if($custom_price=='Open+Sans+Condensed'){echo ' selected="selected"';} ?>>Open Sans Condensed</option>
		<option value="Orbitron" <?php if($custom_price=='Orbitron'){echo ' selected="selected"';} ?>>Orbitron Regular (400)</option>
		<option value="Oswald" <?php if($custom_price=='Oswald'){echo ' selected="selected"';} ?>>Oswald</option>
		<option value="Over+the+Rainbow" <?php if($custom_price=='Over+the+Rainbow'){echo ' selected="selected"';} ?>>Over the Rainbow</option>
		<option value="Reenie+Beanie" <?php if($custom_price=='Reenie+Beanie'){echo ' selected="selected"';} ?>>Reenie Beanie</option>
		<option value="Pacifico" <?php if($custom_price=='Pacifico'){echo ' selected="selected"';} ?>>Pacifico</option>
		<option value="Patrick+Hand" <?php if($custom_price=='Patrick+Hand'){echo ' selected="selected"';} ?>>Patrick Hand</option>
		<option value="Paytone+One" <?php if($custom_price=='Paytone+One'){echo ' selected="selected"';} ?>>Paytone One</option>
		<option value="Permanent+Marker" <?php if($custom_price=='Permanent+Marker'){echo ' selected="selected"';} ?>>Permanent Marker</option>
		<option value="Philosopher" <?php if($custom_price=='Philosopher'){echo ' selected="selected"';} ?>>Philosopher</option>
		<option value="Play" <?php if($custom_price=='Play'){echo ' selected="selected"';} ?>>Play</option>
		<option value="Playfair+Display" <?php if($custom_price=='Playfair+Display'){echo ' selected="selected"';} ?>> Playfair Display </option>
		<option value="Podkova" <?php if($custom_price=='Podkova'){echo ' selected="selected"';} ?>> Podkova </option>
		<option value="PT+Sans" <?php if($custom_price=='PT+Sans'){echo ' selected="selected"';} ?>>PT Sans</option>
		<option value="PT+Sans+Narrow" <?php if($custom_price=='PT+Sans+Narrow'){echo ' selected="selected"';} ?>>PT Sans Narrow</option>
		<option value="PT+Sans+Narrow:regular,bold" <?php if($custom_price=='PT+Sans+Narrow:regular,bold'){echo ' selected="selected"';} ?>>PT Sans Narrow (plus bold)</option>
		<option value="PT+Serif" <?php if($custom_price=='PT+Serif'){echo ' selected="selected"';} ?>>PT Serif</option>
		<option value="PT+Serif Caption" <?php if($custom_price=='PT+Serif Caption'){echo ' selected="selected"';} ?>>PT Serif Caption</option>
		<option value="Puritan" <?php if($custom_price=='PT+Serif Caption'){echo ' selected="selected"';} ?>>Puritan</option>
		<option value="Quattrocento" <?php if($custom_price=='Quattrocento'){echo ' selected="selected"';} ?>>Quattrocento</option>
		<option value="Quattrocento+Sans" <?php if($custom_price=='Quattrocento+Sans'){echo ' selected="selected"';} ?>>Quattrocento Sans</option>
		<option value="Radley" <?php if($custom_price=='Radley'){echo ' selected="selected"';} ?>>Radley</option>
		<option value="Raleway" <?php if($custom_price=='Raleway'){echo ' selected="selected"';} ?>>Raleway</option>
		<option value="Redressed" <?php if($custom_price=='Redressed'){echo ' selected="selected"';} ?>>Redressed</option>
		<option value="Rock+Salt" <?php if($custom_price=='Rock+Salt'){echo ' selected="selected"';} ?>>Rock Salt</option>
		<option value="Rokkitt" <?php if($custom_price=='Rokkitt'){echo ' selected="selected"';} ?>>Rokkitt</option>
		<option value="Ruslan+Display" <?php if($custom_price=='Ruslan+Display'){echo ' selected="selected"';} ?>>Ruslan Display</option>
		<option value="Schoolbell" <?php if($custom_price=='Schoolbell'){echo ' selected="selected"';} ?>>Schoolbell</option>
		<option value="Shadows+Into+Light" <?php if($custom_price=='Shadows+Into+Light'){echo ' selected="selected"';} ?>>Shadows Into Light</option>
		<option value="Shanti" <?php if($custom_price=='Shanti'){echo ' selected="selected"';} ?>>Shanti</option>
		<option value="Sigmar+One" <?php if($custom_price=='Sigmar+One'){echo ' selected="selected"';} ?>>Sigmar One</option>
		<option value="Six+Caps" <?php if($custom_price=='Six+Caps'){echo ' selected="selected"';} ?>>Six Caps</option>
		<option value="Slackey" <?php if($custom_price=='Slackey'){echo ' selected="selected"';} ?>>Slackey</option>
		<option value="Smythe" <?php if($custom_price=='Smythe'){echo ' selected="selected"';} ?>>Smythe</option>
		<option value="Sniglet" <?php if($custom_price=='Sniglet'){echo ' selected="selected"';} ?>>Sniglet</option>
		<option value="Special+Elite" <?php if($custom_price=='Special+Elite'){echo ' selected="selected"';} ?>>Special Elite</option>
		<option value="Stardos+Stencil" <?php if($custom_price=='Stardos+Stencil'){echo ' selected="selected"';} ?>>Stardos Stencil</option>
		<option value="Sue+Ellen+Francisco" <?php if($custom_price=='Sue+Ellen+Francisco'){echo ' selected="selected"';} ?>>Sue Ellen Francisco</option>
		<option value="Sunshiney" <?php if($custom_price=='Sunshiney'){echo ' selected="selected"';} ?>>Sunshiney</option>
		<option value="Swanky+and+Moo+Moo" <?php if($custom_price=='Swanky+and+Moo+Moo'){echo ' selected="selected"';} ?>>Swanky and Moo Moo</option>
		<option value="Syncopate" <?php if($custom_price=='Syncopate'){echo ' selected="selected"';} ?>>Syncopate</option>
		<option value="Tangerine" <?php if($custom_price=='Tangerine'){echo ' selected="selected"';} ?>>Tangerine</option>
		<option value="Tenor+Sans" <?php if($custom_price=='Tenor+Sans'){echo ' selected="selected"';} ?>> Tenor Sans </option>
		<option value="Terminal+Dosis+Light" <?php if($custom_price=='Terminal+Dosis+Light'){echo ' selected="selected"';} ?>>Terminal Dosis Light</option>
		<option value="The+Girl+Next+Door" <?php if($custom_price=='The+Girl+Next+Door'){echo ' selected="selected"';} ?>>The Girl Next Door</option>
		<option value="Tinos" <?php if($custom_price=='Tinos'){echo ' selected="selected"';} ?>>Tinos</option>
		<option value="Ubuntu" <?php if($custom_price=='Ubuntu'){echo ' selected="selected"';} ?>>Ubuntu</option>
		<option value="Ultra" <?php if($custom_price=='Ultra'){echo ' selected="selected"';} ?>>Ultra</option>
		<option value="Unkempt" <?php if($custom_price=='Unkempt'){echo ' selected="selected"';} ?>>Unkempt</option>
		<option value="UnifrakturCook:bold" <?php if($custom_price=='UnifrakturCook:bold'){echo ' selected="selected"';} ?>>UnifrakturCook</option>
		<option value="UnifrakturMaguntia" <?php if($custom_price=='UnifrakturMaguntia'){echo ' selected="selected"';} ?>>UnifrakturMaguntia</option>
		<option value="Varela" <?php if($custom_price=='Varela'){echo ' selected="selected"';} ?>>Varela</option>
		<option value="Varela Round" <?php if($custom_price=='Varela Round'){echo ' selected="selected"';} ?>>Varela Round</option>
		<option value="Vibur" <?php if($custom_price=='Vibur'){echo ' selected="selected"';} ?>>Vibur</option>
		<option value="Vollkorn" <?php if($custom_price=='Vollkorn'){echo ' selected="selected"';} ?>>Vollkorn</option>
		<option value="VT323" <?php if($custom_price=='VT323'){echo ' selected="selected"';} ?>>VT323</option>
		<option value="Waiting+for+the+Sunrise" <?php if($custom_price=='Waiting+for+the+Sunrise'){echo ' selected="selected"';} ?>>Waiting for the Sunrise</option>
		<option value="Wallpoet" <?php if($custom_price=='Wallpoet'){echo ' selected="selected"';} ?>>Wallpoet</option>
		<option value="Walter+Turncoat" <?php if($custom_price=='Walter+Turncoat'){echo ' selected="selected"';} ?>>Walter Turncoat</option>
		<option value="Wire+One" <?php if($custom_price=='Wire+One'){echo ' selected="selected"';} ?>>Wire One</option>
		<option value="Yanone+Kaffeesatz" <?php if($custom_price=='Yanone+Kaffeesatz'){echo ' selected="selected"';} ?>>Yanone Kaffeesatz</option>
		<option value="Yeseva+One" <?php if($custom_price=='Yeseva+One'){echo ' selected="selected"';} ?>>Yeseva One</option>
		<option value="Zeyada" <?php if($custom_price=='Zeyada'){echo ' selected="selected"';} ?>>Zeyada</option>  
								
								</select>
								<select name="custom_price_px" style="width:80px">
								
									<?php for( $x = 9; $x <= 50; $x++ ) { ?>
					              <option value="<?php echo $x; ?>" <?php if($custom_price_px==$x || ($x == 14 && $custom_price_px < 6)){echo ' selected="selected"';} ?>><?php echo $x; ?> px</option>
									<?php } ?>
								
								</select>
								<p style="width:135px">On product page</p>
								<select name="custom_price_on_product_page" style="width:80px;margin-right:0px">
								
									<?php for( $x = 9; $x <= 50; $x++ ) { ?>
					              <option value="<?php echo $x; ?>" <?php if($custom_price_on_product_page==$x || ($x == 25 && $custom_price_on_product_page < 6)){echo ' selected="selected"';} ?>><?php echo $x; ?> px</option>
									<?php } ?>
								
								</select>
							
							
								<div class="clear"></div>
							
							</div>
							
							<!-- End Input -->
						
						</div>
					
						<!-- End Font -->
						
						<!-- Colors -->
						
						<div id="tab_colors" class="tab-content">
						
							<h4>Colors settings</h4>
							
							<!-- Select color settings -->
							
							<ul class="select-color-settings">
							
								<li><a href="javascript:;" rel="0"<?php if($megastore_color < 1) { echo ' class="active"'; } ?>><img src="view/image/megastore/version_1.png" alt=""></a></li>
								<li><a href="javascript:;" rel="1"<?php if($megastore_color == 1) { echo ' class="active"'; } ?>><img src="view/image/megastore/version_2.png" alt=""></a></li>
								<li><a href="javascript:;" rel="2"<?php if($megastore_color == 2) { echo ' class="active"'; } ?>><img src="view/image/megastore/version_3.png" alt=""></a></li>
								<li><a href="javascript:;" rel="3"<?php if($megastore_color == 3) { echo ' class="active"'; } ?>><img src="view/image/megastore/version_4.png" alt=""></a></li>
								<li><a href="javascript:;" rel="4"<?php if($megastore_color == 4) { echo ' class="active"'; } ?>><img src="view/image/megastore/version_5.png" alt=""></a></li>
							
							</ul>
							<input name="megastore_color" value="<?php echo $megastore_color; ?>" id="megastore_color" type="hidden" />
							
							<!-- Status -->
							
							<?php if($colors_status == 1) { echo '<div class="status status-on" title="1" rel="colors_status"></div>'; } else { echo '<div class="status status-off" title="0" rel="colors_status"></div>'; } ?>
							
							<input name="colors_status" value="<?php echo $colors_status; ?>" id="colors_status" type="hidden" />
							
							<!-- Colors Left -->
							
							<div class="colors_left">
								
								<h5>Top Bar / Breadcrumb</h5>
								
								<!-- Input -->
								
								<div class="color_input">
								
									<p>Background</p>
									<div><input type="text" value="<?php echo $top_bar_breadcrumb_background; ?>" id="top_bar_breadcrumb_background" name="top_bar_breadcrumb_background" /></div>
								
								</div>
								
								<!-- Input -->
								<!-- Input -->
								
								<div class="color_input">
								
									<p>Body</p>
									<div><input type="text" value="<?php echo $top_bar_breadcrumb_body; ?>" id="top_bar_breadcrumb_body" name="top_bar_breadcrumb_body" /></div>
								
								</div>
								
								<!-- Input -->
								<!-- Input -->
								
								<div class="color_input">
								
									<p>Link (login / register)</p>
									<div><input type="text" value="<?php echo $top_bar_breadcrumb_link; ?>" id="top_bar_breadcrumb_link" name="top_bar_breadcrumb_link" /></div>
								
								</div>
								
								<!-- Input -->
								<!-- Input -->
								
								<div class="color_input">
								
									<p>Headlines</p>
									<div><input type="text" value="<?php echo $top_bar_breadcrumb_headlines; ?>" id="top_bar_breadcrumb_headlines" name="top_bar_breadcrumb_headlines" /></div>
								
								</div>
								
								<!-- Input -->
								
							</div>
							
							<!-- End Colors Left -->
							
							<!-- Colors Center -->
							
							<div class="colors_center">
								
								<h5>Content</h5>
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Background</p>
									<div><input type="text" value="<?php echo $content_background; ?>" id="content_background" name="content_background" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Body and old price</p>
									<div><input type="text" value="<?php echo $content_body_and_old_price; ?>" id="content_body_and_old_price" name="content_body_and_old_price" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Product name</p>
									<div><input type="text" value="<?php echo $content_product_name; ?>" id="content_product_name" name="content_product_name" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Price</p>
									<div><input type="text" value="<?php echo $content_price; ?>" id="content_price" name="content_price" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Headlines</p>
									<div><input type="text" value="<?php echo $content_headlines; ?>" id="content_headlines" name="content_headlines" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Links</p>
									<div><input type="text" value="<?php echo $content_links; ?>" id="content_links" name="content_links" /></div>
								
								</div>
								
								<!-- Input -->
								
							</div>
							
							<!-- End Colors Center -->
							
							<!-- Colors Right -->
							
							<div class="colors_right">
								
								<h5>Footer</h5>
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Background</p>
									<div><input type="text" value="<?php echo $footer_backgrounds; ?>" id="footer_backgrounds" name="footer_backgrounds" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Body</p>
									<div><input type="text" value="<?php echo $footer_body; ?>" id="footer_body" name="footer_body" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Headlines</p>
									<div><input type="text" value="<?php echo $footer_headliness; ?>" id="footer_headliness" name="footer_headliness" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Links</p>
									<div><input type="text" value="<?php echo $footer_links; ?>" id="footer_links" name="footer_links" /></div>
								
								</div>
								
								<!-- Input -->
									
							</div>
							
							<!-- End Colors Right -->
							
							<p style="font-size:1px;line-height:1px;height:1px;clear:both;margin:0px;padding:0px;"></p>
							
							<!-- Colors Left -->
							
							<div class="colors_left">
								
								<h5>Category bar</h5>
								
								<!-- Input -->
								
								<div class="color_input">
								
									<p>Top gardient</p>
									<div><input type="text" value="<?php echo $category_bar_top_gradient; ?>" id="category_bar_top_gradient" name="category_bar_top_gradient" /></div>
								
								</div>
								
								<!-- Input -->
								<!-- Input -->
								
								<div class="color_input">
								
									<p>Bottom gardient</p>
									<div><input type="text" value="<?php echo $category_bar_bottom_gradient; ?>" id="category_bar_bottom_gradient" name="category_bar_bottom_gradient" /></div>
								
								</div>
								
								<!-- Input -->
								<!-- Input -->
								
								<div class="color_input">
								
									<p>Font color</p>
									<div><input type="text" value="<?php echo $category_bar_font_color; ?>" id="category_bar_font_color" name="category_bar_font_color" /></div>
								
								</div>
								
								<!-- Input -->
								
							</div>
							
							<!-- End Colors Left -->
							
							<!-- Colors Center -->
							
							<div class="colors_center">
								
								<h5>Add to Cart button</h5>
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Top Gradient</p>
									<div><input type="text" value="<?php echo $add_to_cart_button_top_gradient; ?>" id="add_to_cart_button_top_gradient" name="add_to_cart_button_top_gradient" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Bottom Gradient</p>
									<div><input type="text" value="<?php echo $add_to_cart_button_bottom_gradient; ?>" id="add_to_cart_button_bottom_gradient" name="add_to_cart_button_bottom_gradient" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Font color</p>
									<div><input type="text" value="<?php echo $add_to_cart_button_font_color; ?>" id="add_to_cart_button_font_color" name="add_to_cart_button_font_color" /></div>
								
								</div>
								
								<!-- Input -->
																								
							</div>
							
							<!-- End Colors Center -->
							
							<div class="colors_right">
							
								<h5>Standard button</h5>
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Top Gradient</p>
									<div><input type="text" value="<?php echo $standard_button_top_gradient; ?>" id="standard_button_top_gradient" name="standard_button_top_gradient" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Bottom Gradient</p>
									<div><input type="text" value="<?php echo $standard_button_bottom_gradient; ?>" id="standard_button_bottom_gradient" name="standard_button_bottom_gradient" /></div>
								
								</div>
								
								<!-- Input -->
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Font color</p>
									<div><input type="text" value="<?php echo $standard_button_font_color; ?>" id="standard_button_font_color" name="standard_button_font_color" /></div>
								
								</div>
								
								<!-- Input -->
														
							</div>
							
							<p style="font-size:1px;line-height:1px;height:1px;clear:both;margin:0px;padding:0px;"></p>
							
							<!-- Colors Left -->
							
							<div class="colors_left">
							
								<h5>Body</h5>
 								<!-- Input -->	
								
								<div class="color_input">
								
									<p>Background</p>
									<div><input type="text" value="<?php echo $body_backgrounds; ?>" id="body_backgrounds" name="body_backgrounds" /></div>
								
								</div>
								
								<!-- Input -->
							
							</div>
							
							<!-- End Colors Left -->
							
							<p style="font-size:1px;line-height:1px;height:1px;clear:both;margin:0px;padding:0px;"></p>

						
						</div>
						
						<!-- End Colors -->
						
						<!-- Background -->
						
						<div id="tab_background" class="tab-content">
						
							<!-- Status -->
							
							<?php if($background_status == 1) { echo '<div class="status status-on" title="1" rel="background_status"></div>'; } else { echo '<div class="status status-off" title="0" rel="background_status"></div>'; } ?>
							
							<input name="background_status" value="<?php echo $background_status; ?>" id="background_status" type="hidden" />
							
							<!-- Title -->
							
							<h4>Background settings</h4>
							
							<div class="background-left">
								
								<h5>General background</h5>
								
								<div class="own_image" onclick="image_upload('general_background', 'preview1');">
								
									<input type="hidden" name="general_background" value="<?php echo $general_background; ?>" id="general_background" />
									<img src="../image/<?php echo $general_background; ?>" alt="" id="preview1" />
								
								</div>
								
								<!-- Input -->
								
								<div class="input">
								
									<p>Background</p>
									<select name="general_background_background"><option value="0"<?php if($general_background_background < 1) { echo ' selected="selected"'; } ?>>Standard</option><option value="1"<?php if($general_background_background == 1) { echo ' selected="selected"'; } ?>>None</option><option value="2"<?php if($general_background_background == 2) { echo ' selected="selected"'; } ?>>Own</option></select>
								
									<div class="clear"></div>
								
								</div>
								
								<!-- End Input -->
								
 								<!-- Input -->	
								
								<div class="input">
								
									<p>Position</p>
									<select name="general_background_position"><option value="top left"<?php if($general_background_position == 'top left') { echo ' selected="selected"'; } ?>>Top left</option><option value="top center"<?php if($general_background_position == 'top center') { echo ' selected="selected"'; } ?>>Top center</option><option value="top right"<?php if($general_background_position == 'top right') { echo ' selected="selected"'; } ?>>Top right</option><option value="bottom left"<?php if($general_background_position == 'bottom left') { echo ' selected="selected"'; } ?>>Bottom left</option><option value="bottom center"<?php if($general_background_position == 'bottom center') { echo ' selected="selected"'; } ?>>Bottom center</option><option value="bottom right"<?php if($general_background_position == 'bottom right') { echo ' selected="selected"'; } ?>>Bottom right</option></select>
								
									<div class="clear"></div>
								
								</div>
								
								<!-- End Input -->
								
 								<!-- Input -->	
								
								<div class="input">
								
									<p>Repeat</p>
									<select name="general_background_repeat"><option value="no-repeat"<?php if($general_background_repeat == 'no-repeat') { echo ' selected="selected"'; } ?>>no-repeat</option><option value="repeat-x"<?php if($general_background_repeat == 'repeat-x') { echo ' selected="selected"'; } ?>>repeat-x</option><option value="repeat-y"<?php if($general_background_repeat == 'repeat-y') { echo ' selected="selected"'; } ?>>repeat-y</option><option value="repeat"<?php if($general_background_repeat == 'repeat') { echo ' selected="selected"'; } ?>>repeat</option></select>
								
									<div class="clear"></div>
								
								</div>
								
								<!-- End Input -->
								
 								<!-- Input -->	
								
								<div class="input">
								
									<p>Attachment</p>
									<select name="general_background_attachment"><option value="scroll"<?php if($general_background_attachment == 'scroll') { echo ' selected="selected"'; } ?>>scroll</option><option value="fixed"<?php if($general_background_attachment == 'fixed') { echo ' selected="selected"'; } ?>>fixed</option></select>
								
									<div class="clear"></div>
								
								</div>
								
								<!-- End Input -->
							
							</div>
							
							<div class="background-right">
								
								<h5>Footer background</h5>
								
								<div class="own_image" onclick="image_upload('footer_background', 'preview2');">
								
									<input type="hidden" name="footer_background" value="<?php echo $footer_background; ?>" id="footer_background" />
									<img src="../image/<?php echo $footer_background; ?>" alt="" id="preview2" />
								
								</div>
								
								<!-- Input -->
								
								<div class="input">
								
									<p>Background</p>
									<select name="footer_background_background"><option value="0"<?php if($footer_background_background < 1) { echo ' selected="selected"'; } ?>>Standard</option><option value="1"<?php if($footer_background_background == 1) { echo ' selected="selected"'; } ?>>None</option><option value="2"<?php if($footer_background_background == 2) { echo ' selected="selected"'; } ?>>Own</option></select>
								
									<div class="clear"></div>
								
								</div>
								
								<!-- End Input -->
								
 								<!-- Input -->	
								
								<div class="input">
								
									<p>Position</p>
									<select name="footer_background_position"><option value="top left"<?php if($footer_background_position == 'top left') { echo ' selected="selected"'; } ?>>Top left</option><option value="top center"<?php if($footer_background_position == 'top center') { echo ' selected="selected"'; } ?>>Top center</option><option value="top right"<?php if($footer_background_position == 'top right') { echo ' selected="selected"'; } ?>>Top right</option><option value="bottom left"<?php if($footer_background_position == 'bottom left') { echo ' selected="selected"'; } ?>>Bottom left</option><option value="bottom center"<?php if($footer_background_position == 'bottom center') { echo ' selected="selected"'; } ?>>Bottom center</option><option value="bottom right"<?php if($footer_background_position == 'bottom right') { echo ' selected="selected"'; } ?>>Bottom right</option></select>
								
									<div class="clear"></div>
								
								</div>
								
								<!-- End Input -->
								
 								<!-- Input -->	
								
								<div class="input">
								
									<p>Repeat</p>
									<select name="footer_background_repeat"><option value="no-repeat"<?php if($footer_background_repeat == 'no-repeat') { echo ' selected="selected"'; } ?>>no-repeat</option><option value="repeat-x"<?php if($footer_background_repeat == 'repeat-x') { echo ' selected="selected"'; } ?>>repeat-x</option><option value="repeat-y"<?php if($footer_background_repeat == 'repeat-y') { echo ' selected="selected"'; } ?>>repeat-y</option><option value="repeat"<?php if($footer_background_repeat == 'repeat') { echo ' selected="selected"'; } ?>>repeat</option></select>
								
									<div class="clear"></div>
								
								</div>
								
								<!-- End Input -->
								
								
							</div>
							
							<p style="font-size:1px;line-height:1px;height:1px;clear:both;margin:0px;padding:0px;"></p>
						
						</div>
						
						<!-- End Background -->
					
					</div>
					
					<!-- End Design -->
					
					<!-- Footer -->
					
					<div id="tab_footer" class="tab-content3">
					
						<div class="footer_left">
						
							<!-- Contact, About us, Facebook TABS -->
							
							<div id="tabs_footer" class="htabs main-tabs">
								
								<?php foreach ($languages as $language): ?>
								<a href="#tab_customfooter_<?php echo $language['language_id']; ?>"><img src="../image/flags/<?php echo $language['image'] ?>" alt="<?php echo $language['name']; ?>" width="16px" height="11px" /><span><?php echo $language['name']; ?></span></a>
								<?php endforeach; ?>
							
							</div>
							
							<!-- End Contact, About us, Facebook Tabs -->
						
						</div>
						
						<div class="footer_right">
							
							<?php foreach ($languages as $language) { ?>
							<?php $language_id = $language['language_id']; ?>
							
							<div id="tab_customfooter_<?php echo $language_id; ?>">
							
								<!-- Contact, About us, Facebook TABS -->
								
								<div id="tabs_<?php echo $language_id; ?>" class="htabs tabs-design">
								
									<a href="#tab_contact_<?php echo $language_id; ?>" class="tcontact"><span>Contact</span></a>
									<a href="#tab_aboutus_<?php echo $language_id; ?>" class="taboutus"><span>About us</span></a>
									<a href="#tab_facebook_<?php echo $language_id; ?>" class="tfacebook"><span>Facebook</span></a>
									<a href="#tab_twitter_<?php echo $language_id; ?>" class="ttwitter"><span>Twitter</span></a>
								
								</div>
								
								<!-- Contact, About us, Facebook -->
								
								<!-- TAB CONTACT -->
								
								<div id="tab_contact_<?php echo $language_id; ?>" class="tab-content4">
								
									<!-- Status -->
									
									<?php if(isset($customfooter[$language_id]['contact_status'])) { ?>
									<?php if($customfooter[$language_id]['contact_status'] == 1) { echo '<div class="status status-on" title="1" rel="customfooter_'.$language_id.'_contact_status"></div>'; } else { echo '<div class="status status-off" title="0" rel="customfooter_'.$language_id.'_contact_status"></div>'; } ?>
									
									<input name="customfooter[<?php echo $language_id; ?>][contact_status]" value="<?php echo $customfooter[$language_id]['contact_status']; ?>" id="customfooter_<?php echo $language_id; ?>_contact_status" type="hidden" />
									<?php } else { ?>
									<?php echo '<div class="status status-off" title="0" rel="customfooter_'.$language_id.'_contact_status"></div>'; ?>
									<input name="customfooter[<?php echo $language_id; ?>][contact_status]" value="0" id="customfooter_<?php echo $language_id; ?>_contact_status" type="hidden" />
									<?php } ?>
									
									<h4>Contact</h4>
									
									<!-- Input -->
									
									<div class="input">
									
										<p>Phone</p>
										<?php if(isset($customfooter[$language_id]['contact_phone'])) { ?>
										<input name="customfooter[<?php echo $language_id; ?>][contact_phone]" value="<?php echo $customfooter[$language_id]['contact_phone']; ?>" />
										<?php } else { ?>
										<input name="customfooter[<?php echo $language_id; ?>][contact_phone]" value="" />
										<?php } ?>
									
										<div class="clear"></div>
									
									</div>
									
									<!-- End Input -->
									<!-- Input -->
									
									<div class="input">
									
										<p>Skype</p>
										<?php if(isset($customfooter[$language_id]['contact_skype'])) { ?>
										<input name="customfooter[<?php echo $language_id; ?>][contact_skype]" value="<?php echo $customfooter[$language_id]['contact_skype']; ?>" />
										<?php } else { ?>
										<input name="customfooter[<?php echo $language_id; ?>][contact_skype]" value="" />
										<?php } ?>
									
										<div class="clear"></div>
									
									</div>
									
									<!-- End Input -->
									<!-- Input -->
									
									<div class="input">
									
										<p>E-mail</p>
										<?php if(isset($customfooter[$language_id]['contact_email'])) { ?>
										<input name="customfooter[<?php echo $language_id; ?>][contact_email]" value="<?php echo $customfooter[$language_id]['contact_email']; ?>" />
										<?php } else { ?>
										<input name="customfooter[<?php echo $language_id; ?>][contact_email]" value="" />
										<?php } ?>
									
										<div class="clear"></div>
									
									</div>
									
									<!-- End Input -->
																	
								</div>
								
								<!-- End TAB CONTACT -->
								
								<!-- TAB About us -->
								
								<div id="tab_aboutus_<?php echo $language_id; ?>" class="tab-content4">
								
									<!-- Status -->
									
									<?php if(isset($customfooter[$language_id]['aboutus_status'])) { ?>
									<?php if($customfooter[$language_id]['aboutus_status'] == 1) { echo '<div class="status status-on" title="1" rel="customfooter_'.$language_id.'_aboutus_status"></div>'; } else { echo '<div class="status status-off" title="0" rel="customfooter_'.$language_id.'_aboutus_status"></div>'; } ?>
									
									<input name="customfooter[<?php echo $language_id; ?>][aboutus_status]" value="<?php echo $customfooter[$language_id]['aboutus_status']; ?>" id="customfooter_<?php echo $language_id; ?>_aboutus_status" type="hidden" />
									<?php } else { ?>
									<?php echo '<div class="status status-off" title="0" rel="customfooter_'.$language_id.'_aboutus_status"></div>'; ?>
									<input name="customfooter[<?php echo $language_id; ?>][aboutus_status]" value="0" id="customfooter_<?php echo $language_id; ?>_aboutus_status" type="hidden" />
									<?php } ?>
									
									<h4>About us</h4>
									
									<!-- Input -->
									
									<div class="input">
									
										<p>Title</p>
										<?php if(isset($customfooter[$language_id]['aboutus_title'])) { ?>
										<input name="customfooter[<?php echo $language_id; ?>][aboutus_title]" value="<?php echo $customfooter[$language_id]['aboutus_title']; ?>" />
										<?php } else { ?>
										<input name="customfooter[<?php echo $language_id; ?>][aboutus_title]" value="" />
										<?php } ?>
									
										<div class="clear"></div>
									
									</div>
									
									<!-- End Input -->
									
									<!-- Input -->
									
									<div class="input">										
									
										<p>Text</p>
										<?php if(isset($customfooter[$language_id]['aboutus_text'])) { ?>
										<textarea rows="0" cols="0" name="customfooter[<?php echo $language_id; ?>][aboutus_text]"><?php echo $customfooter[$language_id]['aboutus_text']; ?></textarea>
										<?php } else { ?>
										<textarea rows="0" cols="0" name="customfooter[<?php echo $language_id; ?>][aboutus_text]"></textarea>
										<?php } ?>
									
										<div class="clear"></div>
									
									</div>
									
									<!-- End Input -->
																	
								</div>
								
								<!-- End TAB About US -->
								
								<!-- TAB Facebook -->
								
								<div id="tab_facebook_<?php echo $language_id; ?>" class="tab-content4">
								
									<!-- Status -->
									
									<?php if(isset($customfooter[$language_id]['facebook_status'])) { ?>
									<?php if($customfooter[$language_id]['facebook_status'] == 1) { echo '<div class="status status-on" title="1" rel="customfooter_'.$language_id.'_facebook_status"></div>'; } else { echo '<div class="status status-off" title="0" rel="customfooter_'.$language_id.'_facebook_status"></div>'; } ?>
									
									<input name="customfooter[<?php echo $language_id; ?>][facebook_status]" value="<?php echo $customfooter[$language_id]['facebook_status']; ?>" id="customfooter_<?php echo $language_id; ?>_facebook_status" type="hidden" />
									<?php } else { ?>
									<?php echo '<div class="status status-off" title="0" rel="customfooter_'.$language_id.'_facebook_status"></div>'; ?>
									<input name="customfooter[<?php echo $language_id; ?>][facebook_status]" value="0" id="customfooter_<?php echo $language_id; ?>_facebook_status" type="hidden" />
									<?php } ?>
									
									<h4>Facebook</h4>
									
									<!-- Input -->
									
									<div class="input">
									
										<p>Facebook ID</p>
										<?php if(isset($customfooter[$language_id]['facebook_id'])) { ?>
										<input name="customfooter[<?php echo $language_id; ?>][facebook_id]" value="<?php echo $customfooter[$language_id]['facebook_id']; ?>" />
										<?php } else { ?>
										<input name="customfooter[<?php echo $language_id; ?>][facebook_id]" value="" />
										<?php } ?>
									
										<div class="clear"></div>
									
									</div>
									
									<!-- End Input -->
																	
								</div>
								
								<!-- End TAB Facebook -->
								
								<!-- TAB Twitter -->
								
								<div id="tab_twitter_<?php echo $language_id; ?>" class="tab-content4">
								
									<!-- Status -->
									
									<?php if(isset($customfooter[$language_id]['twitter_status'])) { ?>
									<?php if($customfooter[$language_id]['twitter_status'] == 1) { echo '<div class="status status-on" title="1" rel="customfooter_'.$language_id.'_twitter_status"></div>'; } else { echo '<div class="status status-off" title="0" rel="customfooter_'.$language_id.'_twitter_status"></div>'; } ?>
									
									<input name="customfooter[<?php echo $language_id; ?>][twitter_status]" value="<?php echo $customfooter[$language_id]['twitter_status']; ?>" id="customfooter_<?php echo $language_id; ?>_twitter_status" type="hidden" />
									<?php } else { ?>
									<?php echo '<div class="status status-off" title="0" rel="customfooter_'.$language_id.'_twitter_status"></div>'; ?>
									<input name="customfooter[<?php echo $language_id; ?>][twitter_status]" value="0" id="customfooter_<?php echo $language_id; ?>_twitter_status" type="hidden" />
									<?php } ?>
									
									<h4>Twitter</h4>
									
									<!-- Input -->
									
									<div class="input">
									
										<p>Twitter profile</p>
										<?php if(isset($customfooter[$language_id]['twitter_profile'])) { ?>
										<input name="customfooter[<?php echo $language_id; ?>][twitter_profile]" value="<?php echo $customfooter[$language_id]['twitter_profile']; ?>" />
										<?php } else { ?>
										<input name="customfooter[<?php echo $language_id; ?>][twitter_profile]" value="" />
										<?php } ?>
									
										<div class="clear"></div>
									
									</div>
									
									<!-- End Input -->
																	
								</div>
								
								<!-- End TAB Twitter -->
								
								<script type="text/javascript"><!--
								$('#tabs_<?php echo $language_id; ?> a').tabs();
								//--></script> 
							
							</div>
							
							<?php } ?>
						
						</div>
						
						<p style="font-size:1px;line-height:1px;height:1px;clear:both;margin:0px;padding:0px;"></p>
					
					</div>
					
					<!-- End Footer -->
					
					<!-- Payment -->
					
					<div id="tab_payment" class="tab-content">
					
						<!-- Status -->
							
						<?php if($payment_status == 0 && $payment_status != '') { echo '<div class="status status-off" title="0" rel="payment_status"></div>'; } else { echo '<div class="status status-on" title="1" rel="payment_status"></div>'; } ?>
						
						<input name="payment_status" value="<?php echo $payment_status; ?>" id="payment_status" type="hidden" />
						
						<!-- Title -->
						
						<h4>Payment</h4>
						
						<p class="text">Show payment images in footer</p>
						
						<!-- Input Payment -->
						
						<div class="input-payment">
						
							<img src="view/image/megastore/mastercard.png" alt="Mastercard" />
							
							<div class="input-payment-div">
							
								<p>Url</p>
								<input type="text" value="<?php echo $payment_mastercard; ?>" name="payment_mastercard" />
							
							</div>
							
							<?php if($payment_mastercard_status == 0 && $payment_mastercard_status != '') { echo '<div class="status status-off" title="0" rel="payment_mastercard_status"></div>'; } else { echo '<div class="status status-on" title="1" rel="payment_mastercard_status"></div>'; } ?>
							
							<input name="payment_mastercard_status" value="<?php echo $payment_mastercard_status; ?>" id="payment_mastercard_status" type="hidden" />
						
						</div>
						
						<!-- End Input Payment -->
						
						<!-- Input Payment -->
						
						<div class="input-payment">
						
							<img src="view/image/megastore/visa.png" alt="Visa" />
							
							<div class="input-payment-div">
							
								<p>Url</p>
								<input type="text" value="<?php echo $payment_visa; ?>" name="payment_visa" />
							
							</div>
							
							<?php if($payment_visa_status == 0 && $payment_visa_status != '') { echo '<div class="status status-off" title="0" rel="payment_visa_status"></div>'; } else { echo '<div class="status status-on" title="1" rel="payment_visa_status"></div>'; } ?>
							
							<input name="payment_visa_status" value="<?php echo $payment_visa_status; ?>" id="payment_visa_status" type="hidden" />
						
						</div>
						
						<!-- End Input Payment -->
						
						<!-- Input Payment -->
						
						<div class="input-payment">
						
							<img src="view/image/megastore/moneybookers.png" alt="Moneybookers" />
							
							<div class="input-payment-div">
							
								<p>Url</p>
								<input type="text" value="<?php echo $payment_moneybookers; ?>" name="payment_moneybookers" />
							
							</div>
							
							<?php if($payment_moneybookers_status == 0 && $payment_moneybookers_status != '') { echo '<div class="status status-off" title="0" rel="payment_moneybookers_status"></div>'; } else { echo '<div class="status status-on" title="1" rel="payment_moneybookers_status"></div>'; } ?>
							
							<input name="payment_moneybookers_status" value="<?php echo $payment_moneybookers_status; ?>" id="payment_moneybookers_status" type="hidden" />
						
						</div>
						
						<!-- End Input Payment -->
						
						<!-- Input Payment -->
						
						<div class="input-payment">
						
							<img src="view/image/megastore/paypal.png" alt="PayPal" />
							
							<div class="input-payment-div">
							
								<p>Url</p>
								<input type="text" value="<?php echo $payment_paypal; ?>" name="payment_paypal" />
							
							</div>
							
							<?php if($payment_paypal_status == 0 && $payment_paypal_status != '') { echo '<div class="status status-off" title="0" rel="payment_paypal_status"></div>'; } else { echo '<div class="status status-on" title="1" rel="payment_paypal_status"></div>'; } ?>
							
							<input name="payment_paypal_status" value="<?php echo $payment_paypal_status; ?>" id="payment_paypal_status" type="hidden" />
						
						</div>
						
						<!-- End Input Payment -->
						
					</div>
					
					<!-- End Payment -->
					
					<p style="font-size:1px;line-height:1px;height:1px;clear:both;margin:0px;padding:0px;"></p>
				
				</div>
				
				<!-- End Tabs -->
				
				<!-- Buttons -->
				
				<div class="buttons"><a onclick="$('#form').submit();" class="button-save"><span><?php echo $button_save; ?></span></a></div>
				
				<!-- End Buttons -->
			
			</form>
		
		</div>
	
	</div>
	
	<!-- End Content -->

</div>

<!-- End Theme Options -->

</div>

<!-- END #CONTENT -->

<script type="text/javascript"><!--
function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script> 


<script type="text/javascript">
jQuery(document).ready(function($) {

	$(".select-color-settings li a").click(function () {
		
		var styl = $(this).attr("rel");
		var element_index = $('.select-color-settings li a').index(this);
		$(".select-color-settings li a").removeClass("active");
		$(".select-color-settings li a").eq(element_index).addClass("active");
		$("#megastore_color").val(styl);
		
	});

});	
</script>

<script type="text/javascript">

$(document).ready(function() {

	$('#top_bar_breadcrumb_background, #top_bar_breadcrumb_body, #top_bar_breadcrumb_link, #top_bar_breadcrumb_headlines, #content_background, #content_body_and_old_price, #content_product_name, #content_price, #content_headlines, #content_links, #footer_backgrounds, #footer_body, #footer_headliness, #footer_links, #category_bar_top_gradient, #category_bar_bottom_gradient, #category_bar_font_color, #add_to_cart_button_top_gradient, #add_to_cart_button_bottom_gradient, #add_to_cart_button_font_color, #body_backgrounds, #standard_button_top_gradient, #standard_button_bottom_gradient, #standard_button_font_color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
	 });
</script>

<script type="text/javascript"><!--
$('#tabs a').tabs();
$('#tabs_design a').tabs();
$('#tabs_footer a').tabs();
//--></script> 

<script type="text/javascript">
jQuery(document).ready(function($) {

	$(".status").click(function () {
		
		var styl = $(this).attr("rel");
		var co = $(this).attr("title");
		
		if(co == 1) {
		
			$(this).removeClass('status-on');
			$(this).addClass('status-off');
			$(this).attr("title", "0");

			$("#"+styl+"").val(0);
		
		}
		
		if(co == 0) {
		
			$(this).addClass('status-on');
			$(this).removeClass('status-off');
			$(this).attr("title", "1");

			$("#"+styl+"").val(1);
		
		}
		
	});


});	
</script>

<?php echo $footer; ?>
