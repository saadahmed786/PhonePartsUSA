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
    $curl = 'y';
    
    $curl = curl_init();
    
   	curl_setopt($curl, CURLOPT_URL, $home . 'licensed.php?domain=' . $domain . '&extension=2500');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    $licensed = curl_exec($curl);
    
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
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/feed.png" alt="" /> <?php echo $heading_title; ?></h1>
      <?php if(md5($licensed)=='e9dc924f238fa6cc29465942875fe8f0'){ ?><div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div><?php } ?>
    </div>
    <div class="content">
      <?php if(md5($licensed)=='e9dc924f238fa6cc29465942875fe8f0'){ ?><form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
            <tr>
              <td colspan="2"><h2><?php echo $heading_general_settings; ?></h2></td>
            </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="uksb_sitemaps_status">
                <?php if ($uksb_sitemaps_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_image_sitemap; ?></td>
            <td><select name="uksb_image_sitemap">
                <?php if ($uksb_image_sitemap) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_split; ?><br /><span class="help"><?php echo $help_split; ?></span></td>
            <td valign="top"><select name="uksb_sitemaps_split">
                <?php if ($uksb_sitemaps_split=='500') { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="500" selected="selected">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
                <option value="5000">5000</option>
                <option value="10000">10000</option>
                <option value="20000">20000</option>
                <option value="30000">30000</option>
                <option value="50000">50000</option>
                <?php } elseif ($uksb_sitemaps_split=='1000') { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="500">500</option>
                <option value="1000" selected="selected">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
                <option value="5000">5000</option>
                <option value="10000">10000</option>
                <option value="20000">20000</option>
                <option value="30000">30000</option>
                <option value="50000">50000</option>
                <?php } elseif ($uksb_sitemaps_split=='1500') { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500" selected="selected">1500</option>
                <option value="2000">2000</option>
                <option value="5000">5000</option>
                <option value="10000">10000</option>
                <option value="20000">20000</option>
                <option value="30000">30000</option>
                <option value="50000">50000</option>
                <?php } elseif ($uksb_sitemaps_split=='2000') { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000" selected="selected">2000</option>
                <option value="5000">5000</option>
                <option value="10000">10000</option>
                <option value="20000">20000</option>
                <option value="30000">30000</option>
                <option value="50000">50000</option>
                <?php } elseif ($uksb_sitemaps_split=='5000') { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
                <option value="5000" selected="selected">5000</option>
                <option value="10000">10000</option>
                <option value="20000">20000</option>
                <option value="30000">30000</option>
                <option value="50000">50000</option>
                <?php } elseif ($uksb_sitemaps_split=='10000') { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
                <option value="5000">5000</option>
                <option value="10000" selected="selected">10000</option>
                <option value="20000">20000</option>
                <option value="30000">30000</option>
                <option value="50000">50000</option>
                <?php } elseif ($uksb_sitemaps_split=='20000') { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
                <option value="5000">5000</option>
                <option value="10000">10000</option>
                <option value="20000" selected="selected">20000</option>
                <option value="30000">30000</option>
                <option value="50000">50000</option>
                <?php } elseif ($uksb_sitemaps_split=='30000') { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
                <option value="5000">5000</option>
                <option value="10000">10000</option>
                <option value="20000">20000</option>
                <option value="30000" selected="selected">30000</option>
                <option value="50000">50000</option>
                <?php } elseif ($uksb_sitemaps_split=='50000') { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
                <option value="5000">5000</option>
                <option value="10000">10000</option>
                <option value="20000">20000</option>
                <option value="30000">30000</option>
                <option value="50000" selected="selected">50000</option>
                <?php } else { ?>

                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <option value="500">500</option>
                <option value="1000">1000</option>
                <option value="1500">1500</option>
                <option value="2000">2000</option>
                <option value="5000">5000</option>
                <option value="10000">10000</option>
                <option value="20000">20000</option>
                <option value="30000">30000</option>
                <option value="50000">50000</option>
                <?php } ?>
              </select></td>
          </tr>
        </table>
        <table class="form">
            <tr>
              <td colspan="4"><h2><?php echo $entry_sitemap_content; ?></h2><span class="help"><?php echo $help_content; ?></span></td>
            </tr>
          <tr>
            <td><?php echo $entry_products; ?></td>
            <td><?php echo $entry_in_sitemap; ?> <select name="uksb_sitemap_products_on">
                <?php if ($uksb_sitemap_products_on=='0') { ?>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <option value="1"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <?php } ?>
              </select></td>
            <td><?php echo $entry_frequency; ?> <select name="uksb_sitemap_products_fr">
                <?php if ($uksb_sitemap_products_fr == 'always') { ?>
                <option value="always" selected="selected"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_products_fr == 'hourly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly" selected="selected"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_products_fr == 'daily') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily" selected="selected"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_products_fr == 'monthly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly" selected="selected"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_products_fr == 'yearly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly" selected="selected"><?php echo $text_yearly; ?></option>
                <?php } else  { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly" selected="selected"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } ?>
              </select></td>
            <td><?php echo $entry_priority; ?> <select name="uksb_sitemap_products_pr">
                <?php if ($uksb_sitemap_products_pr == '0.9') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9" selected="selected">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_products_pr == '0.8') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8" selected="selected">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_products_pr == '0.7') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7" selected="selected">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_products_pr == '0.6') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6" selected="selected">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_products_pr == '0.5') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5" selected="selected">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_products_pr == '0.4') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4" selected="selected">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_products_pr == '0.3') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3" selected="selected">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_products_pr == '0.2') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2" selected="selected">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_products_pr == '0.1') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1" selected="selected">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_products_pr == '0.0') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0" selected="selected">0.0</option>
                <?php } else { ?>
                <option value="1.0" selected="selected">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_categories; ?></td>
            <td style="width:230px;"><?php echo $entry_in_sitemap; ?> <select name="uksb_sitemap_categories_on">
                <?php if ($uksb_sitemap_categories_on=='0') { ?>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <option value="1"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <?php } ?>
              </select></td>
            <td style="width:230px;"><?php echo $entry_frequency; ?> <select name="uksb_sitemap_categories_fr">
                <?php if ($uksb_sitemap_categories_fr == 'always') { ?>
                <option value="always" selected="selected"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_categories_fr == 'hourly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly" selected="selected"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_categories_fr == 'daily') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily" selected="selected"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_categories_fr == 'monthly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly" selected="selected"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_categories_fr == 'yearly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly" selected="selected"><?php echo $text_yearly; ?></option>
                <?php } else  { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly" selected="selected"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } ?>
              </select></td>
            <td><?php echo $entry_priority; ?> <select name="uksb_sitemap_categories_pr">
                <?php if ($uksb_sitemap_categories_pr == '1.0') { ?>
                <option value="1.0" selected="selected">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_categories_pr == '0.8') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8" selected="selected">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_categories_pr == '0.7') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7" selected="selected">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_categories_pr == '0.6') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6" selected="selected">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_categories_pr == '0.5') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5" selected="selected">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_categories_pr == '0.4') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4" selected="selected">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_categories_pr == '0.3') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3" selected="selected">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_categories_pr == '0.2') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2" selected="selected">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_categories_pr == '0.1') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1" selected="selected">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_categories_pr == '0.0') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0" selected="selected">0.0</option>
                <?php } else { ?>
                <option value="1.0">1.0</option>
                <option value="0.9" selected="selected">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_manufacturers; ?></td>
            <td><?php echo $entry_in_sitemap; ?> <select name="uksb_sitemap_manufacturers_on">
                <?php if ($uksb_sitemap_manufacturers_on=='0') { ?>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <option value="1"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <?php } ?>
              </select></td>
            <td><?php echo $entry_frequency; ?> <select name="uksb_sitemap_manufacturers_fr">
                <?php if ($uksb_sitemap_manufacturers_fr == 'always') { ?>
                <option value="always" selected="selected"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_manufacturers_fr == 'hourly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly" selected="selected"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_manufacturers_fr == 'daily') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily" selected="selected"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_manufacturers_fr == 'monthly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly" selected="selected"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_manufacturers_fr == 'yearly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly" selected="selected"><?php echo $text_yearly; ?></option>
                <?php } else  { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly" selected="selected"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } ?>
              </select></td>
            <td><?php echo $entry_priority; ?> <select name="uksb_sitemap_manufacturers_pr">
                <?php if ($uksb_sitemap_manufacturers_pr == '1.0') { ?>
                <option value="1.0" selected="selected">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_manufacturers_pr == '0.9') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9" selected="selected">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_manufacturers_pr == '0.7') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7" selected="selected">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_manufacturers_pr == '0.6') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6" selected="selected">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_manufacturers_pr == '0.5') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5" selected="selected">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_manufacturers_pr == '0.4') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4" selected="selected">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_manufacturers_pr == '0.3') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3" selected="selected">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_manufacturers_pr == '0.2') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2" selected="selected">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_manufacturers_pr == '0.1') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1" selected="selected">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_manufacturers_pr == '0.0') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0" selected="selected">0.0</option>
                <?php } else { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8" selected="selected">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_pages; ?></td>
            <td><?php echo $entry_in_sitemap; ?> <select name="uksb_sitemap_pages_on">
                <?php if ($uksb_sitemap_pages_on=='0') { ?>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <option value="1"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <?php } ?>
              </select></td>
            <td><?php echo $entry_frequency; ?> <select name="uksb_sitemap_pages_fr">
                <?php if ($uksb_sitemap_pages_fr == 'always') { ?>
                <option value="always" selected="selected"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_pages_fr == 'hourly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly" selected="selected"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_pages_fr == 'daily') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily" selected="selected"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_pages_fr == 'monthly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly" selected="selected"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } elseif ($uksb_sitemap_pages_fr == 'yearly') { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly" selected="selected"><?php echo $text_yearly; ?></option>
                <?php } else  { ?>
                <option value="always"><?php echo $text_always; ?></option>
                <option value="hourly"><?php echo $text_hourly; ?></option>
                <option value="daily"><?php echo $text_daily; ?></option>
                <option value="weekly" selected="selected"><?php echo $text_weekly; ?></option>
                <option value="monthly"><?php echo $text_monthly; ?></option>
                <option value="yearly"><?php echo $text_yearly; ?></option>
                <?php } ?>
              </select></td>
            <td><?php echo $entry_priority; ?> <select name="uksb_sitemap_pages_pr">
                <?php if ($uksb_sitemap_pages_pr == '1.0') { ?>
                <option value="1.0" selected="selected">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_pages_pr == '0.9') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9" selected="selected">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_pages_pr == '0.8') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8" selected="selected">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_pages_pr == '0.6') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6" selected="selected">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_pages_pr == '0.5') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5" selected="selected">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_pages_pr == '0.4') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4" selected="selected">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_pages_pr == '0.3') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3" selected="selected">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_pages_pr == '0.2') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2" selected="selected">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_pages_pr == '0.1') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1" selected="selected">0.1</option>
                <option value="0.0">0.0</option>
                <?php } elseif ($uksb_sitemap_pages_pr == '0.0') { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0" selected="selected">0.0</option>
                <?php } else { ?>
                <option value="1.0">1.0</option>
                <option value="0.9">0.9</option>
                <option value="0.8">0.8</option>
                <option value="0.7" selected="selected">0.7</option>
                <option value="0.6">0.6</option>
                <option value="0.5">0.5</option>
                <option value="0.4">0.4</option>
                <option value="0.3">0.3</option>
                <option value="0.2">0.2</option>
                <option value="0.1">0.1</option>
                <option value="0.0">0.0</option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_pages_omit; ?></td>
            <td colspan="2">
             <input name="uksb_pages_omit_a" type="checkbox" value="1"<?php if($uksb_pages_omit_a!=''){ ?> checked="checked"<?php } ?> /> <?php echo $text_pg_home; ?>
             <br /><input name="uksb_pages_omit_b" type="checkbox" value="1"<?php if($uksb_pages_omit_b!=''){ ?> checked="checked"<?php } ?> /> <?php echo $text_pg_specials; ?>
             <?php foreach($informations as $information){  ?>
             <br /><input name="uksb_pages_omit_<?php echo $information['information_id']; ?>" type="checkbox" value="1"<?php if(${'uksb_pages_omit_'.$information['information_id']}!=''){ ?> checked="checked"<?php } ?> /> <?php echo $information['title']; ?>
             <?php } ?>
              </td>
          </tr>
        </table>
        <table class="form">
            <tr>
              <td colspan="2"><h2><?php echo $heading_sitemap_urls; ?></h2></h2><span class="help"><?php echo $help_urls; ?></span></td>
            </tr>
          <?php
          $feeds = explode("^", $data_feed);
          $feed_urls = explode("^", $data_feed_url);
          foreach (array_keys($feeds) as $key) {
          ?><tr>
            <td><?php echo $entry_data_feed1; ?></td>
            <td><strong><?php echo $feed_urls[$key]; ?></strong><br /><textarea cols="40" rows="5" readonly="readonly" onClick="$(this).select();"><?php echo $feeds[$key]; ?></textarea></td>
          </tr>
          <?php
          } 
          $feeds2 = explode("^", $data_feed2);
          foreach (array_keys($feeds2) as $key) {
          ?><tr>
            <td><?php echo $entry_data_feed2; ?></td>
            <td><strong><?php echo $feed_urls[$key]; ?></strong><br /><textarea cols="40" rows="5" readonly="readonly" onClick="$(this).select();"><?php echo $feeds2[$key]; ?></textarea></td>
          </tr>
          <?php
          } ?>
        </table>
        <table class="form">
          <tr>
            <td><?php echo $entry_info; ?></td>
            <td><?php echo $help_info; ?></td>
          </tr>
        </table>
      </form>
    <?php } //end of full content ?>
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
<?php echo $footer; ?>