<?php if (count($currencies) > 1) { ?>
<a href="javascript:void(0);"><?php echo $text_currency; ?></a>
<ul id="currency">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">  
    <?php foreach ($currencies as $currency) { ?>
    <?php if ($currency['code'] == $currency_code) { ?>
    <?php if ($currency['symbol_left']) { ?>
    	<li><a title="<?php echo $currency['title']; ?>" class="active"><?php echo $currency['symbol_left'] . " - " . $currency['title']; ?></a></li>
    <?php } else { ?>
   		<li><a title="<?php echo $currency['title']; ?>" class="active"><?php echo $currency['symbol_right'] . " - " . $currency['title']; ?></a></li>
    <?php } ?>
    <?php } else { ?>
    <?php if ($currency['symbol_left']) { ?>
    	<li><a href="javascript:void(0);" title="<?php echo $currency['title']; ?>" onclick="$('input[name=\'currency_code\']').attr('value', '<?php echo $currency['code']; ?>'); $(this).parent().parent().submit();"> <?php echo $currency['symbol_left'] . " - " . $currency['title']; ?></a></li>
    <?php } else { ?>
    	<li><a href="javascript:void(0);" title="<?php echo $currency['title']; ?>" onclick="$('input[name=\'currency_code\']').attr('value', '<?php echo $currency['code']; ?>'); $(this).parent().parent().submit();"><?php echo $currency['symbol_right'] . " - " . $currency['title']; ?></a></li>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <input type="hidden" name="currency_code" value="" />
    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
  
</form>
</ul>
<?php } ?>
