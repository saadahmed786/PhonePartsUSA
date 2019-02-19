<?php
require_once("auth.php");
require_once("inc/functions.php");
$type = $_POST['type'];
		
?>

<?php
$continue = true;
$site = "http://www.mobiledefenders.com/";
$url = $_POST['url'];
    
	
	$i=0;
	 while ($continue == true) :
	 $scraped_page = curl($url);  
	$scraped_data = scrape_between($scraped_page, '<section class="category-products">', '</section>');   // Scraping downloaded dara in $scraped_page for content between <title> and </title> tags
   
  // echo $scraped_data;exit;
   
 $results = explode("<li class=\"grid_3 item\" itemscope itemtype=\"http://schema.org/Product\">",$scraped_data);
 //echo "<pre>";
 //print_r($results);exit;
 $retruns = array();
 
 foreach($results as $result)
 {
	 $title = scrape_between($result,"title=\"","\" class");
	 $price = scrape_between($result,"<span class=\"price\">","</span>");
	 if($price=='')
	 {
		 $price = scrape_between($result,"<p class=\"special-price\">","</p>");
	 }
		 if($title!='' and $title!='Grid')
		 {
	 $returns[$i]['site_url'] = $site;
	 $returns[$i]['detail_url'] = $url;
	 
	 $returns[$i]['title'] = addslashes($title);
	 $returns[$i]['sku'] = 'N/A';
	 $returns[$i]['stock_status'] = 'N/A';
	$returns[$i]['price'] = $price;
	
	 $i++;
		 }
 }
 
 $next_nav = scrape_between($results[0],'<div class="pages">','</div>');
 $next_nav = scrape_between($next_nav,'href="','">');
 if($next_nav!='')
 {
	 $continue = true;
	 $url = $next_nav;
	// $url = $site.$url;
 }
 else
 {
	$continue = false; 
 }
 
 
 sleep(rand(3,5));   // Sleep for 3 to 5 seconds. Useful if not using proxies. We don't want to get into trouble.
	 endwhile;
	 


  
 
?>


<div align="center">
			 	  <table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	 <td>#</td>
			 	  	 	 <td>SKU</td>
			 	  	 	 <td>Title</td>
                         <td>Price</td>
                         <td>Stock Status</td>
                     
                         
			 	  	 </tr>
                   <?php
				   $i=1;
				   foreach($returns as $return)
				   {
					   if($type=='import')
					   {
							$CheckQuery = $db->func_query_first("SELECT id FROM inv_scrapper WHERE product_title='".$return['title']."'");   
							$Array = array();
							$Array['product_title'] = $return['title'];
							$Array['sku'] = $return['sku'];
							$Array['price'] = $return['price'];
							$Array['stock_status'] = $return['stock_status'];
							$Array['site_url'] = $return['site_url'];
							$Array['detail_url'] = $return['detail_url'];
							$Array['date_added'] = date('Y-m-d h:i:s');
							
							if($CheckQuery)
							{
								$db->func_array2update("inv_scrapper",$Array,"product_title = '".$return['title']."'");
							}
							else
							{
								$db->func_array2insert("inv_scrapper",$Array);
							}
						   
					   }
					   
					   ?>
                       <tr>
                       <td><?php echo $i;?></td>
                       <td><?php echo $return['sku'];?></td>
                       <td><?php echo $return['title'];?></td>
                       <td><?php echo $return['price'];?></td>
                       <td><?php echo $return['stock_status'];?></td>
                       
                       </tr>
                       <?php
					   
					 $i++;  
				   }
				   ?>
			 	  	 
			 	  	 
			 	  </table>
		     </div>
