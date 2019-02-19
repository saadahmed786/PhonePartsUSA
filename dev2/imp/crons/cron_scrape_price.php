<?php
require_once("../config.php");
require_once("../inc/functions.php");

$rows = $db->func_query("SELECT * FROM inv_product_scrape_prices WHERE site_updated=0 LIMIT 25");
foreach($rows as $row)
{
$type = $row['scrape_site'];
$to_be_replaced = array('$',' ','or','US');

 
$scraped_page = curl($row['url']);  
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
	else{
	$scraped_data = '';	
	}

$price = (float)str_replace($to_be_replaced,'',$scraped_data);
echo $row['sku']."<br>";
echo $price."<br>==========================================<br><br>";
	
		$db->db_exec("UPDATE inv_product_scrape_prices SET price='$price',date_updated='".date("Y-m-d H:i:s")."',site_updated=1 WHERE id='".$row['id']."'");	
		
	
}
?>
