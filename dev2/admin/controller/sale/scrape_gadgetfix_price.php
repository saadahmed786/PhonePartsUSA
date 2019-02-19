<?php
require_once("auth.php");
require_once("inc/functions.php");

		
?>

<?php


$json = array();

$url = urldecode($_POST['scrape_url']);
if($_POST['action']=='fetch')
{

$to_be_replaced = array('$',' ','or');
$scraped_page = curl($url);  
$scraped_data = scrape_between($scraped_page, ' <div class="fl divPrice">', '<span class="bold">');   // Scraping downloaded dara in $scraped_page for content between <title> and </title> tags

$scraped_data = scrape_between($scraped_data,'<span class="silver  none-bold">','</span>');

$json['success'] = (float)str_replace($to_be_replaced,'',$scraped_data);

}
if($_POST['action']=='update')
{
	$sku = $_POST['sku'];
	$price = (float)$_POST['scrape_price'];
	
	$db->db_exec("UPDATE oc_product SET price='".$price."' WHERE sku='".$sku."'");	
	$check = $db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='$sku' AND scrape_site='gadgetfix'");
	
	$array = array();
	$array['sku'] = $sku;
	$array['scrape_site'] = "gadgetfix";
	$array['url'] = $url;
	$array['price'] = $price;
	$array['date_updated'] = date('Y-m-d H:i:s');
	$array['site_updated'] = 1;
	if($check)
	{
		$db->func_array2update("inv_product_scrape_prices", $array,"sku='$sku' AND scrape_site='gadgetfix'");	
	}
	else
	{
	$db->func_array2insert("inv_product_scrape_prices", $array);	
	}
	
	$json['success'] = 'Price Updated';
	
}
echo json_encode($json); 
?>



