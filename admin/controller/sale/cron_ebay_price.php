<?php
require_once("auth.php");
require_once("inc/functions.php");

$rows = $db->func_query("SELECT * FROM inv_product_scrape_prices WHERE site_updated=0 AND scrape_site='ebay' LIMIT 25");
foreach($rows as $row)
{

$to_be_replaced = array('$',' ','or','US');
$scraped_page = curl($row['url']);  
$scraped_data = scrape_between($scraped_page, '<span class="notranslate" id="prcIsum" itemprop="price"  style="">', '</span>');   // Scraping downloaded dara in $scraped_page for content between <title> and </title> tags



$price = (float)str_replace($to_be_replaced,'',$scraped_data);
	if($price)
	{
		$db->db_exec("UPDATE inv_product_scrape_prices SET price='$price',date_updated='".date("Y-m-d H:i:s")."',site_updated=1 WHERE id='".$row['id']."'");	
		
	}
}
?>
