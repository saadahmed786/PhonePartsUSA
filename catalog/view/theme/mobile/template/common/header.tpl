<?php if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>">
<head>
<title><?php echo $title; ?></title>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<base href="<?php echo $base; ?>" />
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo str_replace('&', '&amp;', $link['href']); ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/<?php echo $template; ?>/stylesheet/stylesheet.css" />
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie6.css" />
<script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
<script>
DD_belatedPNG.fix('img, #header .div3 a, #content .left, #content .right, .box .top');
</script>
<![endif]-->
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/thickbox/thickbox-compressed.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/thickbox/thickbox.css" />
<script type="text/javascript" src="catalog/view/javascript/jquery/tab.js"></script>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<script type="text/javascript"><!--
function bookmark(url, title) {
	if (window.sidebar) { // firefox
    window.sidebar.addPanel(title, url, "");
	} else if(window.opera && window.print) { // opera
		var elem = document.createElement('a');
		elem.setAttribute('href',url);
		elem.setAttribute('title',title);
		elem.setAttribute('rel','sidebar');
		elem.click();
	} else if(document.all) {// ie
   		window.external.AddFavorite(url, title);
	}
}
//--></script>
</head>
<body>
<div id="container">
<div id="header">
  <?php if ($logo) { ?>
    <a href="<?php echo str_replace('&', '&amp;', $home); ?>"><img src="<?php echo $logo; ?>" title="<?php echo $store; ?>" alt="<?php echo $store; ?>" /></a>
  <?php } ?>
</div>
<div id="menu">
  <img src="catalog/view/theme/mobile/image/special.png" /><a href="<?php echo str_replace('&', '&amp;', $special); ?>"><?php echo $text_special; ?></a>
  <?php if (!$logged) { ?>
    <img src="catalog/view/theme/mobile/image/icon_login.png" /><a href="<?php echo str_replace('&', '&amp;', $login); ?>"><?php echo $text_login; ?></a>
  <?php } else { ?>
    <img src="catalog/view/theme/mobile/image/icon_logout.png" /><a href="<?php echo str_replace('&', '&amp;', $logout); ?>"><?php echo $text_logout; ?></a>
  <?php } ?>
    <img src="catalog/view/theme/mobile/image/icon_account.png" /><a href="<?php echo str_replace('&', '&amp;', $account); ?>"><?php echo $text_account; ?></a>
    <img src="catalog/view/theme/mobile/image/icon_basket.png" /><a href="<?php echo str_replace('&', '&amp;', $cart); ?>"><?php echo $text_cart; ?></a>
</div>
 <div class="box">
  <div class="top" style="background: #FFFFFF;">
    <table style="width: 100%">
      <tr>
        <td style="width: 30%">
        <div style="text-align: left; color: #999; margin-bottom: 4px;">
          <?php foreach ($languages as $language) { ?>
          <?php if ($languages) { ?>
          <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
            <div style="display: inline;">
              <input type="image" src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" style="position: relative; top: 4px;" />
              <input type="hidden" name="language_code" value="<?php echo $language['code']; ?>" />
              <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
            </div>
          </form>
          <?php } ?>
          <?php } ?>
        </div>
        </td>
        <td>
        <form method="get" action="index.php">
        <input type="hidden" name="route" value="product/search" />
        <?php if ($keyword) { ?>
        <input type="text" name="keyword" value="<?php echo $keyword; ?>" id="filter_keyword" />
        <?php } else { ?>
        <input type="text" name="keyword" value="<?php echo $text_keyword; ?>" id="filter_keyword" onclick="this.value = '';" onkeydown="this.style.color = '#000000'" style="color: #999;" />
        <?php } ?>
        <input type="hidden" name="category_id" value="0" />
         <input type="submit" value="Search" />
         </form>
        </td>
      </tr>
    </table>
  </div>
  <div class="bottom">&nbsp;</div>
 </div>
 <div class="box">
  <div class="top"><img src="catalog/view/theme/mobile/image/icon_currency.png" alt="">Currency</div>
  <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="currency_form">
    <div class="middle" style="text-align: left;">
      <select name="currency_code">
      <?php foreach ($currencies as $currency) { ?>
        <option <?php if ($currency['code'] == $currency_code) { echo 'selected="selected"';} ?> value="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?></option>
      <?php } ?>
      </select>
      <input name="redirect" value="<?php echo $redirect; ?>" type="hidden">
      <input name="Change" value="Change" type="submit">
    </div>
  </form>
  <div class="bottom">&nbsp;</div>
</div>

