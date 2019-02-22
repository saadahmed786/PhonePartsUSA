<?php
require_once("auth.php");
require_once("inc/functions.php");

		
?>

<?php


$json = array();

$url = urldecode($_POST['scrape_url']);
$type=$_POST['type'];
$sku = $_POST['sku'];
if($_POST['action']=='fetch')
{

$to_be_replaced = array('$',' ','or','US');
$scraped_page = curl($url);  

	if($type=='gadgetfix')
	{
	$scraped_data = scrape_between($scraped_page, ' <div class="fl divPrice">', '<span class="bold">');  
	
	$scraped_data = scrape_between($scraped_data,'<span class="silver  none-bold">','</span>');
	}
	elseif($type=='ebay' or $type=='ebay_2' or $type=='ebay_3' or $type=='ebay_4')
	{
	$scraped_data = scrape_between($scraped_page, '<span class="notranslate" id="prcIsum" itemprop="price"  style="">', '</span>');	
		if($scraped_data=='')
		{
			$scraped_data = scrape_between($scraped_page, '<span class="notranslate mm-strkthru" id="mm-saleOrgPrc">', '</span>');	
			if($scraped_data=='')
			{
				$scraped_data = scrape_between($scraped_page, '<span class="notranslate" id="prcIsum_bidPrice" itemprop="price">', '</span>');	
				
			}
			
		}
		
	}
	elseif($type=='mengtor')
	{
		$scraped_data = scrape_between($scraped_page, '<div class="price">', '</div>'); 
	
	$scraped_data = scrape_between($scraped_page, '<span>', '</span>');
		
	}
	elseif($type=='mobile_defenders')
	{
		
	$scraped_data = scrape_between($scraped_page, '<span itemprop="price">', '<div class="clear"></div>');
	
	$scraped_data = scrape_between($scraped_data, '<span class="price">', '</span>'); 
	}
	else
	{
	$scraped_data = '';	
		
	}
	if($scraped_data)
	{
		$check = $db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='$sku' AND scrape_site='".$type."'");
	
	$array = array();
	$array['sku'] = $sku;
	$array['scrape_site'] = $type;
	$array['url'] = $url;
	$array['price'] = (float)str_replace($to_be_replaced,'',$scraped_data);
	$array['date_updated'] = date('Y-m-d H:i:s');
	$array['site_updated'] = 1;
	if($check)
	{
		$db->func_array2update("inv_product_scrape_prices", $array,"sku='$sku' AND scrape_site='$type'");	
	}
	else
	{
	$db->func_array2insert("inv_product_scrape_prices", $array);	
	}
		
		
	$json['success'] = (float)str_replace($to_be_replaced,'',$scraped_data);
	}
	else
	{
	$json['error'] = 'Unable to fetch the record, please try again later.';	
		
	}
}
if($_POST['action']=='update')
{
	$sku = $_POST['sku'];
	$price = (float)$_POST['scrape_price'];
	
	//$db->db_exec("UPDATE oc_product SET price='".$price."' WHERE sku='".$sku."'");	
	$check = $db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='$sku' AND scrape_site='".$type."'");
	
	$array = array();
	$array['sku'] = $sku;
	$array['scrape_site'] = $type;
	$array['url'] = $url;
	$array['price'] = $price;
	$array['date_updated'] = date('Y-m-d H:i:s');
	$array['site_updated'] = 1;
	if($check)
	{
		$db->func_array2update("inv_product_scrape_prices", $array,"sku='$sku' AND scrape_site='$type'");	
	}
	else
	{
	$db->func_array2insert("inv_product_scrape_prices", $array);	
	}
	
	$json['success'] = 'Price Updated';
	
}
echo json_encode($json); 
?>



