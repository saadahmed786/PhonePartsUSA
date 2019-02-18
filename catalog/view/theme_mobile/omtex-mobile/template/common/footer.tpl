<?php if ($informations) { ?>
<ul data-role="listview" id="more_info">
	<li data-role="list-divider"><?php echo $text_information; ?></li>
	<li>
		<a href="<?php echo HTTPS_SERVER;?>index.php?route=buyback/buyback">LCD Buy Back</a>
	</li>
	<?php foreach ($informations as $information) { ?>
	<li><a href="<?php echo $information['href']; ?>" rel="external"><?php echo $information['title']; ?></a></li>
	<?php } ?>
</ul>
<?php } ?>
<ul data-role="listview" id="more_customer_service">
	<li data-role="list-divider"><?php echo $text_nav_support; ?></li>
	<li><a href="<?php echo $contact; ?>" rel="external" ><?php echo $text_contact; ?></a></li>
	<li><a href="<?php echo $return; ?>" rel="external" ><?php echo $text_return; ?></a></li>
	<li><a href="<?php echo $sitemap; ?>" rel="external" ><?php echo $text_sitemap; ?></a></li>
</ul>  
<ul data-role="listview" id="more_extras">
	<li data-role="list-divider"><?php echo $text_extra; ?></li>
	<li><a href="<?php echo $manufacturer; ?>" rel="external" ><?php echo $text_manufacturer; ?></a></li>
	<li><a href="<?php echo $voucher; ?>" rel="external" ><?php echo $text_voucher; ?></a></li>
	<li><a href="<?php echo $affiliate; ?>" rel="external" ><?php echo $text_affiliate; ?></a></li>
	<li><a href="<?php echo $special; ?>" rel="external" ><?php echo $text_special; ?></a></li>
	<?php echo $language; ?>
	<?php echo $currency; ?>
</ul>  
<ul data-role="listview" id="more_account">
	<li data-role="list-divider"><?php echo $text_account; ?></li>
	<li><a href="<?php echo $account; ?>" rel="external" ><?php echo $text_account; ?></a></li>
	<?php if (isset($logged)) { ?>
	<li><a href="index.php?route=account/logout" rel="external" ><?php echo $text_logout; ?></a></li>
	<?php } ?>
	<li><a href="<?php echo $order; ?>" rel="external" ><?php echo $text_order; ?></a></li>
	<li><a href="<?php echo $wishlist; ?>" rel="external" ><?php echo $text_wishlist; ?></a></li>
	<li><a href="<?php echo $newsletter; ?>" rel="external" ><?php echo $text_newsletter; ?></a></li>
</ul>          
<div data-role="footer" data-theme="a">

	<div data-role="navbar">
		<ul>
			<li><a id="click_info" data-icon="info"><?php echo $text_nav_info; ?></a></li>
			<li><a id="click_customer_service" data-icon="gear"><?php echo $text_nav_support; ?></a></li>
			<li><a id="click_extras" data-icon="plus"><?php echo $text_extra; ?></a></li>
			<li><a id="click_account" data-icon="grid"><?php echo $text_account; ?></a></li>
			<!--<li><a href="<?php echo $contact; ?>" data-icon="contact" rel="external">Contact</a></li>-->
		</ul>
	</div>
	<div id="powered"><?php 
		$powered=explode('<br>',$powered); 
		if($powered[0]!="") { ?>
		<div id="device_mode" data-role="navbar">
			<ul>
				<li><?php echo $powered[0];?></li>
				<li><a href="#" data-role="button" data-corners="false" data-inline="true" data-theme="b"><?php echo $text_mobile; ?></a></li>
			</ul>
		</div>
		<?php }	?>
		<?php echo $powered[1].'<br>'; ?>
	</div>
</div>
<div id="back-top">
	<div class="top_icon_right" data-role="controlgroup">
		<a href="#top" data-role="button" data-icon="arrow-u" data-iconpos="notext" data-theme="a"></a>
	</div>
</div>
</div>

<div data-role="page" id="agree_page"> 
	<div data-role="content"> 
		<div id="agree_page_text"></div>
		<p><a href="#page" data-role="button">back</a></p>
	</div>
</div>
<div data-role="page" id="privacy_page"> 
	<div data-role="content"> 
		<div id="privacy_page_text"></div>
		<p><a href="#page" data-role="button">back</a></p>
	</div>
</div>
<script type="text/javascript"><!--
	$(function(){$("#quantity").parent().children().css("vertical-align","middle")});function btnminus(a){document.getElementById("quantity").value>a?document.getElementById("quantity").value--:document.getElementById("quantity").value=a}function btnplus(){document.getElementById("quantity").value++};

	$(function() {
		$("#quantity_new").parent().children().css("vertical-align", "middle")
	});


	$(document).ready(function() {
		$(document).on('click', 'a.close', function() {
			$('div.success').hide();
		});

		$('a.plus_cart').click(function() {
			var qty = $(this).prev().val();
			var count = 1;
			qty = parseInt(qty)+parseInt(count);
			$(this).prev().val(qty);
			$('#cartForm').submit();
		});

		$('a.mainus_cart').click(function() {
			var qty = $(this).next().val();
			var count = 1;
			if(qty > 1) {
				qty = parseInt(qty)-parseInt(count);
				$(this).next().val(qty);
				$('#cartForm').submit();
			} else {
				var link = $('a#remove_pro').attr('href');
				window.location.href = link;

			}

		});

		setInterval(function(){
			jQuery.ajax({
				type: "GET",
				url: "index.php?route=common/header/updateCartNew",
				dataType:"HTML",
				success:function(response){
					$('span.count a').html(response);
				}
			});
		}, 3000);

		$('#back-top').remove();
	});
	//-->
</script>
<script type="text/javascript">
	var gr_goal_params = {
		param_0 : '',
		param_1 : '',
		param_2 : '',
		param_3 : '',
		param_4 : '',
		param_5 : ''
	};
</script>
<script type="text/javascript" src="https://app.getresponse.com/goals_log.js?p=668602&u=jgEp"></script>
</body></html>
