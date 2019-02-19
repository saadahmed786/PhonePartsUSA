<script type="text/javascript">
//catalog/view/theme/default/tweetexclamation/twitter/
jQuery(function($){
	$(".tweetexclamation<?php echo $tweetexclamation_id; ?>").tweet({
	modpath: "<?php echo $tweetexclamation_modpath; ?>",
	join_text: "auto",
	username: "<?php echo $tweetexclamation_username; ?>",
	avatar_size: <?php echo $tweetexclamation_avatar_size; ?>,
	count: <?php echo $tweetexclamation_count; ?>,
	auto_join_text_default: "<?php echo $text_join_text_default; ?>,", 
	auto_join_text_ed: "<?php echo $text_join_text_ed; ?>",
	auto_join_text_ing: "<?php echo $text_join_text_ing; ?>",
	auto_join_text_reply: "<?php echo $text_join_text_reply; ?>",
	auto_join_text_url: "<?php echo $text_join_text_url; ?>",
	loading_text: "<?php echo $text_loading; ?>",
	template: "<?php echo $tweetexclamation_template; ?>"
	});
});
</script>
<div class="tweets">
<? if ($tweetexclamation_title){ ?>
<h1><? echo $tweetexclamation_title; ?></h1>
<? } ?>
<div class="tweetexclamation tweetexclamation<?php echo $tweetexclamation_id; ?>"></div>
<? if ($tweetexclamation_readmore){ ?>
<div class="point"><a href="http://twitter.com/<?php echo $tweetexclamation_username; ?>" target="_blank"><? echo $tweetexclamation_readmore; ?></a></div>
<? } ?>
</div>
