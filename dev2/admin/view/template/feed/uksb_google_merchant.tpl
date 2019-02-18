<?php
//licensing check
if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
	$ssl = 1;
    $home = 'https://www.secureserverssl.co.uk/opencart-extensions/google-merchant/';
}else{
	$ssl = 0;
    $home = 'http://www.opencart-extensions.co.uk/google-merchant/';
}

if ($ssl) {
	$domain = str_replace("https://", "", HTTPS_SERVER);
}else{
	$domain = str_replace("http://", "", HTTP_SERVER);
}

if (extension_loaded('curl')) {
    $curl = curl_init();
    
   	curl_setopt($curl, CURLOPT_URL, $home . 'licensed.php?domain=' . $domain . '&extension=2500');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    $licensed = curl_exec($curl);
    
    curl_close($curl);

    $curl = curl_init();
    
   	curl_setopt($curl, CURLOPT_URL, $home . 'upgradecheck.php?domain=' . $domain . '&extension=2500');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    $upgradecheck = curl_exec($curl);
    
    curl_close($curl);
}else{
	$curl = 'n';
    $licensed = 'curl';
}

if(isset($this->request->get['regerror'])&&$this->request->get['regerror']=='emailmal'){
	$error_warning = $regerror_email;
}

if(isset($this->request->get['regerror'])&&$this->request->get['regerror']=='orderidmal'){
	$error_warning = $regerror_orderid;
}

if(isset($this->request->get['regerror'])&&$this->request->get['regerror']=='noreferer'){
	$error_warning = $regerror_noreferer;
}

if(isset($this->request->get['regerror'])&&$this->request->get['regerror']=='localhost'){
	$error_warning = $regerror_localhost;
}

if(isset($this->request->get['regerror'])&&$this->request->get['regerror']=='licensedupe'){
	$error_warning = $regerror_licensedupe;
}
?>
<?php echo $header; ?>
<style type="text/css">
table.form > tbody > tr > td:first-child {
    width: 400px;
}
</style>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($error_duplicate) { ?>
  <div class="warning"><?php echo $error_duplicate; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/feed.png" alt="" /> <?php echo $heading_title; ?></h1>
      <?php if(md5($licensed)=='e9dc924f238fa6cc29465942875fe8f0'&&md5($upgradecheck)=='e9dc924f238fa6cc29465942875fe8f0'){ ?><div class="buttons"><?php if($state=='complete'){ ?><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><?php } ?><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div><?php } ?>
    </div>
    <div class="content">
      <?php if(md5($licensed)=='e9dc924f238fa6cc29465942875fe8f0'&&md5($upgradecheck)=='e9dc924f238fa6cc29465942875fe8f0'){ ?>
      <?php if($state!='complete'){ ?>
      <div id="create_data">
      <h2><?php echo $text_initialise_data; ?></h2>
      <?php echo $text_initialise_data_text; ?>
      <p><a onclick="$('#create_data').hide();$('#creating_data').show();location = '<?php echo $uksb_install_link; ?>';" class="button"><?php echo $button_run; ?></a></p>
      </div>
      <div id="creating_data" style="display:none;">
      <p><img src="view/image/create_data.gif"></p>
      </div>
      <?php } else { ?>
      <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $tab_general_settings; ?></a><a href="#tab-google-settings"><?php echo $tab_google_settings; ?></a><a href="#tab-google-feeds"><?php echo $tab_google_feeds; ?></a><a href="#tab-bing-feeds"><?php echo $tab_bing_feeds; ?></a><a href="#tab-utilities"><?php echo $tab_utilities; ?></a><a href="#tab-videos"><?php echo $tab_videos; ?></a></div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general">
        <table class="form">
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="uksb_google_merchant_status">
                <option value="1"<?php if ($uksb_google_merchant_status) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                <option value="0"<?php if (!$uksb_google_merchant_status) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_characters; ?><br /><span class="help"><?php echo $help_characters; ?></span></td>
            <td><select name="uksb_google_merchant_characters">
                <option value="1"<?php if ($uksb_google_merchant_characters) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                <option value="0"<?php if (!$uksb_google_merchant_characters) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_split; ?><br /><span class="help"><?php echo $help_split; ?></span></td>
            <td><select name="uksb_google_merchant_split" id="split">
                <option value="0"<?php if (!$uksb_google_merchant_split) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
                <option value="50"<?php if ($uksb_google_merchant_split=='50') { ?> selected="selected"<?php } ?>>50</option>
                <option value="100"<?php if ($uksb_google_merchant_split=='100') { ?> selected="selected"<?php } ?>>100</option>
                <option value="250"<?php if ($uksb_google_merchant_split=='250') { ?> selected="selected"<?php } ?>>250</option>
                <option value="500"<?php if ($uksb_google_merchant_split=='500') { ?> selected="selected"<?php } ?>>500</option>
                <option value="750"<?php if ($uksb_google_merchant_split=='750') { ?> selected="selected"<?php } ?>>750</option>
                <option value="1000"<?php if ($uksb_google_merchant_split=='1000') { ?> selected="selected"<?php } ?>>1000</option>
                <option value="1500"<?php if ($uksb_google_merchant_split=='1500') { ?> selected="selected"<?php } ?>>1500</option>
                <option value="2000"<?php if ($uksb_google_merchant_split=='2000') { ?> selected="selected"<?php } ?>>2000</option>
                <option value="2500"<?php if ($uksb_google_merchant_split=='2500') { ?> selected="selected"<?php } ?>>2500</option>
                <option value="3000"<?php if ($uksb_google_merchant_split=='3000') { ?> selected="selected"<?php } ?>>3000</option>
                <option value="3500"<?php if ($uksb_google_merchant_split=='3500') { ?> selected="selected"<?php } ?>>3500</option>
                <option value="4000"<?php if ($uksb_google_merchant_split=='4000') { ?> selected="selected"<?php } ?>>4000</option>
                <option value="4500"<?php if ($uksb_google_merchant_split=='4500') { ?> selected="selected"<?php } ?>>4500</option>
                <option value="5000"<?php if ($uksb_google_merchant_split=='5000') { ?> selected="selected"<?php } ?>>5000</option>
                <option value="6000"<?php if ($uksb_google_merchant_split=='6000') { ?> selected="selected"<?php } ?>>6000</option>
                <option value="7000"<?php if ($uksb_google_merchant_split=='7000') { ?> selected="selected"<?php } ?>>7000</option>
                <option value="8000"<?php if ($uksb_google_merchant_split=='8000') { ?> selected="selected"<?php } ?>>8000</option>
                <option value="9000"<?php if ($uksb_google_merchant_split=='9000') { ?> selected="selected"<?php } ?>>9000</option>
                <option value="10000"<?php if ($uksb_google_merchant_split=='10000') { ?> selected="selected"<?php } ?>>10000</option>
                <option value="12500"<?php if ($uksb_google_merchant_split=='12500') { ?> selected="selected"<?php } ?>>12500</option>
                <option value="15000"<?php if ($uksb_google_merchant_split=='15000') { ?> selected="selected"<?php } ?>>15000</option>
                <option value="20000"<?php if ($uksb_google_merchant_split=='20000') { ?> selected="selected"<?php } ?>>20000</option>
                <option value="25000"<?php if ($uksb_google_merchant_split=='25000') { ?> selected="selected"<?php } ?>>25000</option>
                <option value="30000"<?php if ($uksb_google_merchant_split=='30000') { ?> selected="selected"<?php } ?>>30000</option>
                <option value="40000"<?php if ($uksb_google_merchant_split=='40000') { ?> selected="selected"<?php } ?>>40000</option>
                <option value="50000"<?php if ($uksb_google_merchant_split=='50000') { ?> selected="selected"<?php } ?>>50000</option>
              </select><span id="split_help" style="display:none; color:red;"><br /><?php echo $help_split_help; ?></span></td>
          </tr>
          <tr>
            <td><?php echo $entry_cron; ?><br /><span class="help"><?php echo $help_cron; ?></span></td>
            <td><select name="uksb_google_merchant_cron" id="select_cron">
                <option value="1"<?php if ($uksb_google_merchant_cron) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                <option value="0"<?php if (!$uksb_google_merchant_cron) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
              </select><span id="cron_help" style="display:none; color:red;"><br /><?php echo $help_split_help; ?></span></td>
          </tr>
          <tr>
            <td><?php echo $entry_info; ?></td>
            <td><?php echo $help_info; ?></td>
          </tr>
        </table>
        </div>



        <div id="tab-google-settings">
        <table class="form">
          <tr>
            <td valign="top"><?php echo $entry_google_category; ?><br /><span class="help"><?php echo $help_google_category; ?><br /><br /><?php echo $entry_choose_google_category_xml; ?></span></td>
            <td><div style="overflow:auto;width:480px;height:180px;"><img src="view/image/flags/gb.png" /> <input type="text" name="uksb_google_merchant_google_category_gb" value="<?php echo $uksb_google_merchant_google_category_gb; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/us.png" /> <input type="text" name="uksb_google_merchant_google_category_us" value="<?php echo $uksb_google_merchant_google_category_us; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=en-US','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/au.png" /> <input type="text" name="uksb_google_merchant_google_category_au" value="<?php echo $uksb_google_merchant_google_category_au; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=en-AU','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/fr.png" /> <input type="text" name="uksb_google_merchant_google_category_fr" value="<?php echo $uksb_google_merchant_google_category_fr; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=fr-FR','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/de.png" /> <input type="text" name="uksb_google_merchant_google_category_de" value="<?php echo $uksb_google_merchant_google_category_de; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=de-DE','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/it.png" /> <input type="text" name="uksb_google_merchant_google_category_it" value="<?php echo $uksb_google_merchant_google_category_it; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=it-IT','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/nl.png" /> <input type="text" name="uksb_google_merchant_google_category_nl" value="<?php echo $uksb_google_merchant_google_category_nl; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=nl-NL','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/es.png" /> <input type="text" name="uksb_google_merchant_google_category_es" value="<?php echo $uksb_google_merchant_google_category_es; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=es-ES','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/br.png" /> <input type="text" name="uksb_google_merchant_google_category_pt" value="<?php echo $uksb_google_merchant_google_category_pt; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=pt-BR','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/cz.png" /> <input type="text" name="uksb_google_merchant_google_category_cz" value="<?php echo $uksb_google_merchant_google_category_cz; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=cs-CZ','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/jp.png" /> <input type="text" name="uksb_google_merchant_google_category_jp" value="<?php echo $uksb_google_merchant_google_category_jp; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=ja-JP','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/dk.png" /> <input type="text" name="uksb_google_merchant_google_category_dk" value="<?php echo $uksb_google_merchant_google_category_dk; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=da-DK','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/no.png" /> <input type="text" name="uksb_google_merchant_google_category_no" value="<?php echo $uksb_google_merchant_google_category_no; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=no-NO','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/pl.png" /> <input type="text" name="uksb_google_merchant_google_category_pl" value="<?php echo $uksb_google_merchant_google_category_pl; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=pl-PL','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/ru.png" /> <input type="text" name="uksb_google_merchant_google_category_ru" value="<?php echo $uksb_google_merchant_google_category_ru; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=ru-RU','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/se.png" /> <input type="text" name="uksb_google_merchant_google_category_sv" value="<?php echo $uksb_google_merchant_google_category_sv; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=sv-SE','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a><br /><br />
            <img src="view/image/flags/tr.png" /> <input type="text" name="uksb_google_merchant_google_category_tr" value="<?php echo $uksb_google_merchant_google_category_tr; ?>" style="width:400px; font-size:9px;" /> <a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=tr-TR','google');"><img src="view/image/add.png" border="0" alt="" title="<?php echo $entry_choose_google_category; ?>"></a></div></td>
          </tr>
          <tr>
            <td><?php echo $entry_condition; ?><br /><span class="help"><?php echo $help_condition; ?></span></td>
            <td><select name="uksb_google_merchant_condition">
                <option value="new"<?php if (!$uksb_google_merchant_condition||$uksb_google_merchant_condition=='new') { ?> selected="selected"<?php } ?>><?php echo $text_condition_new; ?></option>
                <option value="used"<?php if ($uksb_google_merchant_condition=='used') { ?> selected="selected"<?php } ?>><?php echo $text_condition_used; ?></option>
                <option value="refurbished"<?php if ($uksb_google_merchant_condition=='refurbished') { ?> selected="selected"<?php } ?>><?php echo $text_condition_ref; ?></option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_mpn; ?><br /><span class="help"><?php echo $help_mpn; ?></span></td>
            <td><select name="uksb_google_merchant_mpn">
                <option value="sku"<?php if ($uksb_google_merchant_mpn=='sku') { ?> selected="selected"<?php } ?>><?php echo $text_sku; ?></option>
                <option value="model"<?php if (!$uksb_google_merchant_mpn||$uksb_google_merchant_mpn=='model') { ?> selected="selected"<?php } ?>><?php echo $text_model; ?></option>
                <option value="mpn"<?php if ($uksb_google_merchant_mpn=='mpn') { ?> selected="selected"<?php } ?>><?php echo $text_mpn; ?></option>
                <option value="location"<?php if ($uksb_google_merchant_mpn=='location') { ?> selected="selected"<?php } ?>><?php echo $text_location; ?></option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_gtin; ?><br /><span class="help"><?php echo $help_gtin; ?></span></td>
            <td><select name="uksb_google_merchant_g_gtin">
                <option value="upc"<?php if (!$uksb_google_merchant_g_gtin||$uksb_google_merchant_g_gtin=='upc') { ?> selected="selected"<?php } ?>><?php echo $text_upc; ?></option>
                <option value="sku"<?php if ($uksb_google_merchant_g_gtin=='sku') { ?> selected="selected"<?php } ?>><?php echo $text_sku; ?></option>
                <option value="gtin"<?php if ($uksb_google_merchant_g_gtin=='gtin') { ?> selected="selected"<?php } ?>><?php echo $text_gtin; ?></option>
                <option value="location"<?php if ($uksb_google_merchant_g_gtin=='location') { ?> selected="selected"<?php } ?>><?php echo $text_location; ?></option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_gender; ?><br /><span class="help"><?php echo $help_gender; ?></span></td>
            <td><select name="uksb_google_merchant_gender">
                <option value="0"<?php if (!$uksb_google_merchant_gender||$uksb_google_merchant_gender=='0') { ?> selected="selected"<?php } ?>><?php echo $text_none; ?></option>
                <option value="male"<?php if ($uksb_google_merchant_gender=='male') { ?> selected="selected"<?php } ?>><?php echo $text_male; ?></option>
                <option value="female"<?php if ($uksb_google_merchant_gender=='female') { ?> selected="selected"<?php } ?>><?php echo $text_female; ?></option>
                <option value="unisex"<?php if ($uksb_google_merchant_gender=='unisex') { ?> selected="selected"<?php } ?>><?php echo $text_unisex; ?></option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_age_group; ?><br /><span class="help"><?php echo $help_age_group; ?></span></td>
            <td><select name="uksb_google_merchant_age_group">
                <option value="0"<?php if (!$uksb_google_merchant_age_group||$uksb_google_merchant_age_group=='0') { ?> selected="selected"<?php } ?>><?php echo $text_none; ?></option>
                <option value="newborn"<?php if ($uksb_google_merchant_age_group=='newborn') { ?> selected="selected"<?php } ?>><?php echo $text_newborn; ?></option>
                <option value="toddler"<?php if ($uksb_google_merchant_age_group=='toddler') { ?> selected="selected"<?php } ?>><?php echo $text_toddler; ?></option>
                <option value="infant"<?php if ($uksb_google_merchant_age_group=='infant') { ?> selected="selected"<?php } ?>><?php echo $text_infant; ?></option>
                <option value="kids"<?php if ($uksb_google_merchant_age_group=='kids') { ?> selected="selected"<?php } ?>><?php echo $text_kids; ?></option>
                <option value="adult"<?php if ($uksb_google_merchant_age_group=='adult') { ?> selected="selected"<?php } ?>><?php echo $text_adult; ?></option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_info; ?></td>
            <td><?php echo $help_info; ?></td>
          </tr>
        </table>
        </div>





        <div id="tab-google-feeds">
        <table class="form">
          <tr>
            <td><?php echo $entry_site; ?><br /><span class="help"><?php echo $help_site; ?></span></td>
            <td><select name="uksb_google_merchant_site" id="google_site">
                <option value="default" selected="selected">Store Default</option>
                <option value="gb">United Kingdom</option>
                <option value="us">United States of America</option>
                <option value="ca">Canada (English)</option>
                <option value="ca_fr">Canada (Français)</option>
                <option value="mx">México</option>
                <option value="au">Australia</option>
                <option value="fr">France</option>
                <option value="de">Deutschland</option>
                <option value="it">Italia</option>
                <option value="nl">Nederlands</option>
                <option value="es">España</option>
                <option value="be_nl">België (Nederlands)</option>
                <option value="be_fr">Belgique (Français)</option>
                <option value="at">Österreich</option>
                <option value="dk">Danmark</option>
                <option value="no">Norge</option>
                <option value="sv">Sverige</option>
                <option value="pl">Polska</option>
                <option value="cz">Československo</option>
                <option value="ch">Switzerland (English)</option>
                <option value="ch_fr">Suisse (Français)</option>
                <option value="ch_de">Schweiz (Deutsch)</option>
                <option value="ch_it">Svizzera (Italiano)</option>
                <option value="ru">Россия</option>
                <option value="tr">Türkiye</option>
                <option value="br">Brasil</option>
                <option value="in">India (English)</option>
                <option value="ja">日本</option>
              </select></td>
          </tr>
          <?php if($this->config->get('uksb_google_merchant_cron')){ ?><tr>
            <td colspan="2"><?php echo $help_cron_code; ?></td>
          </tr><?php } ?>
          <?php
          $feeds = explode("^", $data_feed);
          $crons = ($this->config->get('uksb_google_merchant_cron')?explode("^", $data_cron_path):'');
          $i=0;
          foreach (array_keys($feeds) as $key) {
          ?><tr>
            <td><?php echo $entry_data_feed; ?><br><br><textarea id="feed_url_<?php echo $i; ?>" cols="40" rows="3"><?php echo $feeds[$key]; ?></textarea></td>
            <td><?php if($this->config->get('uksb_google_merchant_cron')){ ?><?php echo $entry_cron_code; ?><br><br><textarea id="cron_code_<?php echo $i; ?>" cols="60" rows="3">curl -L "<?php echo $crons[$key]; ?> >/dev/null 2>&1</textarea><?php }else{ ?>&nbsp;<?php } ?></td>
          </tr>
          <?php
          $i++;
          } ?>
          <tr>
            <td><?php echo $entry_info; ?></td>
            <td><?php echo $help_info; ?></td>
          </tr>
        </table>
        </div>



        <div id="tab-bing-feeds">
        <table class="form">
          <?php
          $bingfeeds = explode("^", $data_bingfeed);
          $i=0;
          foreach($bingfeeds as $bingfeed){
          ?><tr>
            <td><?php echo $entry_data_feed; ?></td>
            <td><textarea id="bingfeed_url_<?php echo $i; ?>" cols="40" rows="5"><?php echo $bingfeed; ?></textarea></td>
          </tr>
          <?php
          $i++;
          } ?>
          <tr>
            <td><?php echo $entry_info; ?></td>
            <td><?php echo $help_info; ?></td>
          </tr>
        </table>
        </div>



        <div id="tab-utilities">
        <table class="form">
          <tr>
            <td><?php echo $utilities1; ?></td>
            <td><a onclick="if(confirm('<?php echo $utilities_confirm; ?>')){return window.open('model/feed/uksb_google_merchant_utilities.php?run=1','utilities','menubar=0, toolbar=0, resizable=1, scrollable=0, width=350, height=250');}" class="button"><?php echo $button_run; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $utilities2; ?></td>
            <td><a onclick="if(confirm('<?php echo $utilities_confirm; ?>')){return window.open('model/feed/uksb_google_merchant_utilities.php?run=2','utilities','menubar=0, toolbar=0, resizable=1, scrollable=0, width=350, height=250');}" class="button"><?php echo $button_run; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $utilities3; ?></td>
            <td><a onclick="if(confirm('<?php echo $utilities_confirm; ?>')){return window.open('model/feed/uksb_google_merchant_utilities.php?run=3','utilities','menubar=0, toolbar=0, resizable=1, scrollable=0, width=350, height=250');}" class="button"><?php echo $button_run; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $utilities4; ?></td>
            <td><a onclick="if(confirm('<?php echo $utilities_confirm; ?>')){return window.open('model/feed/uksb_google_merchant_utilities.php?run=4','utilities','menubar=0, toolbar=0, resizable=1, scrollable=0, width=350, height=250');}" class="button"><?php echo $button_run; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $utilities5; ?></td>
            <td><a onclick="if(confirm('<?php echo $utilities_confirm; ?>')){return window.open('model/feed/uksb_google_merchant_utilities.php?run=5','utilities','menubar=0, toolbar=0, resizable=1, scrollable=0, width=350, height=250');}" class="button"><?php echo $button_run; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $utilities6; ?></td>
            <td><a onclick="if(confirm('<?php echo $utilities_confirm; ?>')){return window.open('model/feed/uksb_google_merchant_utilities.php?run=6','utilities','menubar=0, toolbar=0, resizable=1, scrollable=0, width=350, height=250');}" class="button"><?php echo $button_run; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $utilities7; ?></td>
            <td><a onclick="if(confirm('<?php echo $utilities_confirm; ?>')){return window.open('model/feed/uksb_google_merchant_utilities.php?run=7','utilities','menubar=0, toolbar=0, resizable=1, scrollable=0, width=350, height=250');}" class="button"><?php echo $button_run; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $utilities8; ?></td>
            <td><a onclick="if(confirm('<?php echo $utilities_confirm; ?>')){return window.open('model/feed/uksb_google_merchant_utilities.php?run=8','utilities','menubar=0, toolbar=0, resizable=1, scrollable=0, width=350, height=250');}" class="button"><?php echo $button_run; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $utilities9; ?></td>
            <td><a onclick="if(confirm('<?php echo $utilities_confirm; ?>')){return window.open('model/feed/uksb_google_merchant_utilities.php?run=9','utilities','menubar=0, toolbar=0, resizable=1, scrollable=0, width=350, height=250');}" class="button"><?php echo $button_run; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $entry_info; ?></td>
            <td><?php echo $help_info; ?></td>
          </tr>
        </table>
        </div>



        <div id="tab-videos">
        <table class="form">
        <tr>
            <td colspan="2"><iframe width="853" height="480" src="//www.youtube.com/embed/videoseries?list=SPzQz7G36iOiZsePOZPhA8band-1rxZ9ae" frameborder="0" allowfullscreen></iframe></td>
          </tr>
          <tr>
            <td><?php echo $entry_info; ?></td>
            <td><?php echo $help_info; ?></td>
          </tr>
        </table>
        </div>

      </form>
    <?php } ?>
    <?php } ?>
    <?php if(md5($licensed)=='e9dc924f238fa6cc29465942875fe8f0'&&$upgradecheck=='upgrade'){ ?>
        <div>
        <form name="upgpp" method="post" action="<?php echo $home; ?>upgradepp.php" id="upgpp">
          <table class="form">
              <tr>
                <td><h2><?php echo $license_update; ?></h2></td>
              </tr>
              <?php if(isset($this->request->get['upgcancel'])){ ?>
              <tr>
                <td><?php echo $license_update_error; ?></td>
              </tr>
              <?php } ?>
              <?php if(!isset($this->request->get['updated'])){ ?>
              <tr>
                <td><?php echo $license_update_info; ?></td>
              </tr>
              <?php }else{ ?>
              <tr>
                <td><?php echo $license_updated; ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td><?php if(strstr($domain, 'localhost')||strstr($domain, '127.0.0.1')){
                echo $license_update_localhost;            
                }else{ ?><input type="hidden" name="id" value="2500">
                <input type="hidden" name="domain" value="<?php echo $domain; ?>">
                <input type="hidden" name="ssl" value="<?php echo $ssl; ?>">
                <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                <a onclick="$('#upgpp').submit();" class="button">Upgrade</a>
                <?php } ?></td>
              </tr>
          </table>
        </form>
        </div>
    <?php } ?>
    <?php if($licensed=='none'){ ?>
    <div>
    <?php echo $license_purchase_thanks; ?>
    <?php if(isset($this->request->get['regerror'])){ echo $regerror_quote_msg; } ?>
    <?php if(isset($this->request->get['regerror'])){ ?><p style="color:red;">error msg: <?php echo $this->request->get['regerror']; ?></p><?php } ?>
    <form name="reg" method="post" action="<?php echo $home; ?>register.php" id="reg">
      <table class="form">
    	  <tr>
            <td colspan="2"><h2><?php echo $license_registration; ?></h2></td>
          </tr>
    	  <tr>
            <td><?php echo $license_opencart_email; ?></td>
            <td><?php if(isset($this->request->get['emailmal'])&&$this->request->get['regerror']=='emailmal'){ ?><p style="color:red;"><?php echo $check_email; ?></p><?php } ?><input name="opencart_email" type="text" autofocus required id="opencart_email" form="reg" size="50"></td>
          </tr>
    	  <tr>
            <td><?php echo $license_opencart_orderid; ?></td>
            <td><?php if(isset($this->request->get['regerror'])&&$this->request->get['regerror']=='orderid'){ ?><p style="color:red;"><?php echo $check_orderid; ?></p><?php } ?><input name="order_id" type="text" required id="order_id" form="reg"></td>
          </tr>
    	  <tr>
            <td colspan="2"><input name="submit" type="submit" value="<?php echo $license_registration; ?>" class="button" form="reg">
          <input name="extension_id" type="hidden" id="extension_id" form="reg" value="2500"></td>
          </tr>
      </table>
    </form>
    </div>
    <?php } ?>
    <?php if($licensed=='curl'){ ?>
    <div>
    <?php echo $server_error_curl; ?>
    </div>
    <?php } ?>
    </div>
  </div>
</div>
<?php if(md5($licensed)=='e9dc924f238fa6cc29465942875fe8f0'){ ?>
<script type="text/javascript"><!--
$('#tabs a').tabs(); 
//--></script> 
<?php if($state=='complete'){ ?>
<script type="text/javascript"><!--
$(document).ready(function(){ 
<?php
$i = 0;
reset($feeds);
if($this->config->get('uksb_google_merchant_cron')){
	reset($crons);
}
foreach (array_keys($feeds) as $key) {
?>
		
	$("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $feeds[$key] . ($this->config->get('uksb_google_merchant_cron') ? 'google_' . $this->config->get('config_language') . '-' . $this->config->get('config_currency') . '.xml' : '&language=' . $this->config->get('config_language') . '&currency=' . $this->config->get('config_currency')); ?>');
		
		<?php if($this->config->get('uksb_google_merchant_cron')){ ?>$("textarea#cron_code_<?php echo $i; ?>").text('<?php echo 'curl -L "' . $crons[$key] . '&language='.$this->config->get('config_language').'&currency='.$this->config->get('config_currency').'" >/dev/null 2>&1'; ?>');
		<?php } ?>
<?php $i++; } ?>
			
	$("#split").change(function(){
		$("#split_help").css("display", "inline");
		if($( "#split" ).val() != '0'){
			$( "#select_cron" ).val('0');
		};
	});

	$("#select_cron").change(function(){
		$("#cron_help").css("display", "inline");
		if($( "#select_cron" ).val() == '1'){
			$( "#split" ).val('0');
		};
	});

	$("#google_site").change(function(){
		var site;
		site = $("#google_site").val();
		var cron_lang_curr;
		var feed_lang_curr;
		
		switch (site) {
			case 'gb':
				cron_lang_curr = 'en-GBP';
				feed_lang_curr = '&language=en&currency=GBP';
				break;
			case 'us':
				cron_lang_curr = 'en-USD';
				feed_lang_curr = '&language=en&currency=USD';
				break;
			case 'ca':
				cron_lang_curr = 'en-CAD';
				feed_lang_curr = '&language=en&currency=CAD';
				break;
			case 'ca_fr':
				cron_lang_curr = 'fr-CAD';
				feed_lang_curr = '&language=fr&currency=CAD';
				break;
			case 'au':
				cron_lang_curr = 'en-AUD';
				feed_lang_curr = '&language=en&currency=AUD';
				break;
			case 'fr':
			case 'be_fr':
				cron_lang_curr = 'fr-EUR';
				feed_lang_curr = '&language=fr&currency=EUR';
				break;
			case 'de':
			case 'at':
				cron_lang_curr = 'de-EUR';
				feed_lang_curr = '&language=de&currency=EUR';
				break;
			case 'it':
				cron_lang_curr = 'it-EUR';
				feed_lang_curr = '&language=it&currency=EUR';
				break;
			case 'nl':
			case 'be_nl':
				cron_lang_curr = 'nl-EUR';
				feed_lang_curr = '&language=nl&currency=EUR';
				break;
			case 'es':
				cron_lang_curr = 'es-EUR';
				feed_lang_curr = '&language=es&currency=EUR';
				break;
			case 'dk':
				cron_lang_curr = 'dk-DKK';
				feed_lang_curr = '&language=dk&currency=DKK';
				break;
			case 'no':
				cron_lang_curr = 'no-NOK';
				feed_lang_curr = '&language=no&currency=NOK';
				break;
			case 'sv':
				cron_lang_curr = 'se-SEK';
				feed_lang_curr = '&language=se&currency=SEK';
				break;
			case 'pl':
				cron_lang_curr = 'pl-PLN';
				feed_lang_curr = '&language=pl&currency=PLN';
				break;
			case 'cz':
				cron_lang_curr = 'cz-CZK';
				feed_lang_curr = '&language=cz&currency=CZK';
				break;
			case 'ru':
				cron_lang_curr = 'ru-RUB';
				feed_lang_curr = '&language=ru&currency=RUB';
				break;
			case 'tr':
				cron_lang_curr = 'tr-TRY';
				feed_lang_curr = '&language=tr&currency=TRY';
				break;
			case 'in':
				cron_lang_curr = 'en-INR';
				feed_lang_curr = '&language=en&currency=INR';
				break;
			case 'ja':
				cron_lang_curr = 'ja-JPY';
				feed_lang_curr = '&language=ja&currency=JPY';
				break;
			case 'br':
				cron_lang_curr = 'pt-BRL';
				feed_lang_curr = '&language=pt&currency=BRL';
				break;
			case 'mx':
				cron_lang_curr = 'es-MXN';
				feed_lang_curr = '&language=es&currency=MXN';
				break;
			case 'ch':
				cron_lang_curr = 'en-CHF';
				feed_lang_curr = '&language=en&currency=CHF';
				break;
			case 'ch-fr':
				cron_lang_curr = 'fr-CHF';
				feed_lang_curr = '&language=fr&currency=CHF';
				break;
			case 'ch-de':
				cron_lang_curr = 'de-CHF';
				feed_lang_curr = '&language=de&currency=CHF';
				break;
			case 'ch-it':
				cron_lang_curr = 'it-CHF';
				feed_lang_curr = '&language=it&currency=CHF';
				break;
			case 'default':
			default:
				cron_lang_curr = '<?php echo $this->config->get('config_language').'-'.$this->config->get('config_currency'); ?>';
				feed_lang_curr = '<?php echo '&language='.$this->config->get('config_language').'&currency='.$this->config->get('config_currency'); ?>';
		}
		
			<?php $i = 0; reset($feeds); if($this->config->get('uksb_google_merchant_cron')){reset($crons);}
    
			foreach (array_keys($feeds) as $key) {
			
				if($this->config->get('uksb_google_merchant_cron')){ ?>
				$("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $feeds[$key]; ?>' + 'google_' + cron_lang_curr + '.xml').effect( 'shake', { times:2 }, 50 );
				$("textarea#cron_code_<?php echo $i; ?>").text('curl -L "' + '<?php echo $crons[$key]; ?>' + feed_lang_curr + '" >/dev/null 2>&1').effect( 'shake', { times:2 }, 50 );
				<?php }else{ ?>
				$("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $feeds[$key]; ?>' + feed_lang_curr).effect( 'shake', { times:2 }, 50 );
				<?php } ?>
				
				
			<?php $i++; } ?>
		
	});
});
//--></script>
<?php } ?>
<?php echo $footer; ?>
<?php } ?>