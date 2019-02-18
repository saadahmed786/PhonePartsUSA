<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
  	<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1" /></script>
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js" /></script>
        
    <?php if (in_array('Facebook', $this->config->get('social_network'))) { ?>     
    <fb:like href="<?php echo $this->config->get('facebook_page'); ?>" layout="box_count" action="like" font="arial" show_faces="true" width="48" height="65"></fb:like> 
    <?php } ?>
  	<?php if (in_array('Twitter', $this->config->get('social_network'))) { ?>      
    <a href="http://twitter.com/share" 
           class="twitter-share-button"
           data-url="<?php echo HTTP_SERVER; ?>"
           data-text="<?php echo $twitter_message; ?>"
           data-count="vertical"
           data-via="<?php echo $this->config->get('twitter_page'); ?>"><?php echo $tweet; ?></a>
    <?php } ?>
 </div>
</div>
<script type="text/javascript">
try
{
    if(FB != undefined){
        FB.Event.subscribe('edge.create', function(href, widget) {
              applySocialDiscount('facebook');
        });
        
         FB.Event.subscribe('edge.remove', function(href, widget) {
            removeSocialDiscount('facebook');
        });
    }
}catch(err){}

try
{
    if(twttr != undefined)
    {
        twttr.events.bind('tweet', function(event) {
             applySocialDiscount('twitter');
        });
    }
}catch(err){}

function applySocialDiscount(social_network) {
$.ajax({
        url: 'index.php?route=module/social_discount/addSocialDiscount',
        type: 'post',
        data: 'social_network='+social_network,
        dataType: 'json',
        success: function(json) {
            	
				if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');				
				$('.success').fadeIn('slow');
				$('#cart').load('index.php?route=module/cart #cart > *');
				$('.cart-total').load('index.php?route=checkout/cart .cart-total > *');
				$('html, body').animate({ scrollTop: 0 }, 'slow');
				}	
		 }
       });    
}

function removeSocialDiscount(social_network) {
$.ajax({
        url: 'index.php?route=module/social_discount/removeSocialDiscount',
        type: 'post',
        data: 'social_network='+social_network,
        dataType: 'json',
        success: function(json) {
            	if (json['remove']) {
				$('#notification').html('<div class="warning" style="display: none;">' + json['remove'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');				
				$('.warning').fadeIn('slow');
				$('#cart').load('index.php?route=module/cart #cart > *');
				$('.cart-total').load('index.php?route=checkout/cart .cart-total > *');
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		 }
       });    
}

</script>
