<?php

class ControllerFeedUksbGoogleMerchant extends Controller {

	public function index() {

		$cronlimit = 500;

		if ($this->config->get('uksb_google_merchant_status')) { 

			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {

				$server = $this->config->get('config_ssl');

			} else {

				$server = $this->config->get('config_url');

			}



			$this->load->model('feed/uksb_google');

			

			if(isset($this->request->get['send']) || isset($this->request->get['mode'])){

				if(!isset($this->request->get['send']) && $this->request->get['mode']=='cron'){

					$this->request->get['send'] = '1-'.$cronlimit;

				}

				

				$split = explode("-", $this->request->get['send']);

				$data = array('start' => ($split[0]-1), 'limit' => ($split[1]-$split[0]+1));

				$products = $this->model_feed_uksb_google->getProducts($data);

			}else{

				$products = $this->model_feed_uksb_google->getProducts();

			}

			

			$lang = ($this->request->get['language']?$this->request->get['language']:$this->config->get('config_language'));

			$curr = ($this->request->get['currency']?$this->request->get['currency']:$this->config->get('config_currency'));

			$tax = 1;

			$gpc = '';

			$col = ($curr == 'GBP' || $curr == 'INR' ? 'colour' : 'color');

			$id_suffix = '';	

			$gpc_suffix = '';	

			$supported_currencies = array('GBP', 'USD', 'EUR', 'AUD', 'BRL', 'CZK', 'JPY', 'CHF', 'CAD', 'DKK', 'INR', 'MXN', 'NOK', 'PLN', 'RUB', 'SEK', 'TRY');

			

			if (in_array($curr, $supported_currencies)) {

				$currency = $curr;

			} else {

				$currency = 'GBP';

			}

			if($curr == 'GBP'){

				$gpc_suffix = $id_suffix = 'gb';

				$gpc = html_entity_decode($this->config->get('uksb_google_merchant_google_category_gb'),ENT_QUOTES, 'UTF-8');

			}elseif($curr == 'USD'){

				$gpc_suffix = $id_suffix = 'us';

				$gpc = html_entity_decode($this->config->get('uksb_google_merchant_google_category_us'),ENT_QUOTES, 'UTF-8');

				$tax = 0;

			}elseif($curr == 'AUD'){

				$file = 'google_en-AU.xml';

				$gpc_suffix = $id_suffix = 'au';

				$gpc = html_entity_decode($this->config->get('uksb_google_merchant_google_category_au'),ENT_QUOTES, 'UTF-8');	

			}elseif($lang == 'en' && $curr == 'CAD'){

				$id_suffix = 'ca';

				$tax = 0;

			}elseif($lang == 'fr' && $curr == 'CAD'){

				$id_suffix = 'ca_fr';

				$tax = 0;

			}elseif($lang == 'en' && $curr == 'CHF'){

				$id_suffix = 'ch';

			}elseif($lang == 'fr' && $curr == 'CHF'){

				$id_suffix = 'ch_fr';

			}elseif($lang == 'de' && $curr == 'CHF'){

				$id_suffix = 'ch_de';

			}elseif($lang == 'it' && $curr == 'CHF'){

				$id_suffix = 'ch_it';

			}elseif($curr == 'MXN'){

				$id_suffix = 'mx';

			}elseif($curr == 'INR'){

				$id_suffix = 'in';

				$tax = 0;

			}else{

				if($lang == 'fr'){

					$gpc_suffix = $id_suffix = 'fr';

				}elseif($lang == 'de'){

					$gpc_suffix = $id_suffix = 'de';

				}elseif($lang == 'it'){

					$gpc_suffix = $id_suffix = 'it';

				}elseif($lang == 'nl'){

					$gpc_suffix = $id_suffix = 'nl';

				}elseif($lang == 'es'){

					$gpc_suffix = $id_suffix = 'es';

				}elseif($lang == 'pt'){

					$gpc_suffix = $id_suffix = 'pt';

				}elseif($lang == 'cz'){

					$gpc_suffix = $id_suffix = 'cz';

				}elseif($lang == 'ja'){

					$gpc_suffix = $id_suffix = 'jp';

				}elseif($lang == 'dk'){

					$gpc_suffix = $id_suffix = 'dk';

				}elseif($lang == 'no'){

					$gpc_suffix = $id_suffix = 'no';

				}elseif($lang == 'pl'){

					$gpc_suffix = $id_suffix = 'pl';

				}elseif($lang == 'ru'){

					$gpc_suffix = $id_suffix = 'ru';

				}elseif($lang == 'se'){

					$gpc_suffix = $id_suffix = 'sv';

				}elseif($lang == 'tr'){

					$gpc_suffix = $id_suffix = 'tr';

				}else{

					$gpc_suffix = $id_suffix = 'gb';

				}

				

				$gpc = html_entity_decode($this->config->get('uksb_google_merchant_google_category_'.$gpc_suffix),ENT_QUOTES, 'UTF-8');

			}

			

			if(!isset($this->request->get['mode']) || ($this->request->get['mode']=='cron' && $split[0] == 1)){

				$output  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";



				$output .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'."\n";

				

				$output .= '<channel>'."\n";

				

				$output .= '<title><![CDATA[' . $this->config->get('config_name') . ']]></title>'."\n";

				

				if(!is_array($this->config->get('config_meta_description')) && $this->config->get('config_meta_description')!==''){

					$output .= '<description><![CDATA[' . $this->config->get('config_meta_description') . ']]></description>'."\n";

				}

				

				$output .= '<link>' . $server . '</link>'."\n\n\n";

			}else{

				$output = '';	

			}

			

			foreach ($products as $product) {

				$sizes = explode(",", $product['g_size']); $num_sizes = count($sizes);

				$colours = explode(",", $product['g_colour']); $num_colours = count($colours);

				$materials = explode(",", $product['g_material']); $num_materials = count($materials);

				$patterns = explode(",", $product['g_pattern']); $num_patterns = count($patterns);

				$mpns = explode(",", $product['v_mpn']); $num_mpns = count($mpns);

				$gtins = explode(",", $product['v_gtin']); $num_gtins = count($gtins);

				$prices = explode(",", $product['v_prices']); $num_prices = count($prices);

				$images = explode(",", $product['v_images']); $num_images = count($images);

				$variant = 0;

				$variant = (count($colours)>1||count($sizes)>1||count($materials)>1||count($patterns)>1?1:0);

				$max_variants = max($num_sizes, $num_colours, $num_materials, $num_patterns, $num_mpns, $num_gtins, $num_prices);

				

				$group_id = ($variant==1?$product['product_id'] . '_' . $id_suffix:'');

				

				if($variant==1){

					for($i = 1; $i <= $max_variants; $i++){

						

						$j = $i-1;

						

						$output .= '<item>'."\n";

						

						$output .= '<title><![CDATA[' . $product['name'] . (isset($colours[$j])&&trim($colours[$j])!=''?' - '.trim($colours[$j]):'') . (isset($sizes[$j])&&trim($sizes[$j])!=''?' - '.trim($sizes[$j]):'') . ']]></title>'."\n";

						

						$output .= '<link><![CDATA[' . $this->url->link('product/product', 'product_id=' . $product['product_id'] . '&language=' . $lang . '&currency='. $curr) . ']]></link>'."\n";

						

						$output .= '<description><![CDATA[' . $this->plainText((($product['description']) ? $product['description'] : $product['name'])) . ']]></description>'."\n";

						

						$output .= '<g:brand><![CDATA[' . ((strtolower(substr($product['sku'], 0, 2)) == 'BT') ? 'BinTEK' : 'PhonePartsUSA' ) . ']]></g:brand>'."\n";

						

						$output .= '<g:condition>' . ($product['g_condition']?$product['g_condition']:$this->config->get('uksb_google_merchant_condition')) . '</g:condition>'."\n";

						

						$output .= '<g:item_group_id><![CDATA[' . $group_id . ']]></g:item_group_id>'."\n";

						

						$output .= '<g:id><![CDATA[' . $product['product_id'] . '_' . $i . '_' . $id_suffix . ']]></g:id>'."\n";

						

						if(isset($images[$j]) && $images[$j] != 'no_image.jpg' ){

							$output .= '<g:image_link><![CDATA[' . $server . 'image/' . str_replace(" ", "%20", $images[$j]) . ']]></g:image_link>'."\n";

						}elseif ($product['image'] != '' && $product['image'] != 'no_image.jpg') {

							$output .= '<g:image_link><![CDATA[' . $server . 'image/' . str_replace(" ", "%20", $product['image']) . ']]></g:image_link>'."\n";

						}

						

						$addimages = $this->model_feed_uksb_google->getProductImages($product['product_id']);

						

						$addimnum = 0;

						foreach($addimages as $addimage){

							if($addimnum<10){

								$output .= '<g:additional_image_link><![CDATA[' . $server . 'image/' . str_replace(" ", "%20", $addimage['image']) . ']]></g:additional_image_link>'."\n";

							}

							$addimnum++;

						}

						

						if ($product['quantity']>0) {

							$output .= '<g:availability>in stock</g:availability>'."\n";

						} else {

							$output .= '<g:availability>' . ($this->config->get('config_stock_checkout')==0 ? 'out of stock' : 'in stock') . '</g:availability>'."\n";

						}

						

						if($product['g_multipack']!='0'){

							$output .= '<g:multipack><![CDATA[' . $product['g_multipack'] . ']]></g:multipack>'."\n";

						}

						

						if($product['g_is_bundle']){

							$output .= '<g:is_bundle>TRUE</g:is_bundle>'."\n";

						}

						

						if($product['g_expiry_date']){

							$output .= '<g:expiration_date>' . $product['g_expiry_date'] . '</g:expiration_date>'."\n";

						}





						if(isset($mpns[$j])&&trim($mpns[$j])!=''&&$product['g_identifier_exists']>0){

							$output .= '<g:mpn><![CDATA[' . trim($mpns[$j]) . ']]></g:mpn>'."\n";

						}

						

						if(isset($prices[$j])&&trim($prices[$j])!=''){

							$quantifier = strval($prices[$j]);

							$pricevalue = floatval($prices[$j]);

							if($quantifier == ''){

								$quantifier = '+';

							}

							if($pricevalue==''){

								$pricevalue=0;

							}

						}else{

							$quantifier = '+';

							$pricevalue = '0';

						}

						

						if ((float)$product['special']) {

							$sprice = ($quantifier=='-'?$product['special'] - $pricevalue:$product['special'] + $pricevalue);

							

							if($tax > 0){

								$output .= '<g:sale_price>' .  $this->currency->format($this->tax->calculate($sprice, $product['tax_class_id']), $currency, FALSE, FALSE) . ' ' . $currency . '</g:sale_price>'."\n";

							}else{

								$output .= '<g:sale_price>' .  $this->currency->format($sprice, $currency, FALSE, FALSE) . ' ' . $currency . '</g:sale_price>'."\n";

							}

							$output .= '<g:sale_price_effective_date>' . $this->model_feed_uksb_google->getFeedSpecialStartDate($product['product_id']).'T00:00:00'.date("P").'/'.$this->model_feed_uksb_google->getFeedSpecialEndDate($product['product_id']).'T23:59:59'.date("P").'</g:sale_price_effective_date>'."\n";

						}

						

						$price = ($quantifier=='-'?$product['price'] - $pricevalue:$product['price'] + $pricevalue);

						if($tax > 0){

							$output .= '<g:price>' . $this->currency->format($this->tax->calculate($price, $product['tax_class_id']), $currency, FALSE, FALSE) . ' ' . $currency . '</g:price>'."\n";

						}else{

							$output .= '<g:price>' . $this->currency->format($price, $currency, FALSE, FALSE) . ' ' . $currency . '</g:price>'."\n";

						}

						

						if($product['g_unit_pricing_measure']!='' && !$product['g_energy_efficiency_class']){

							$output .= '<g:unit_pricing_measure>' . $product['g_unit_pricing_measure'] . '</g:unit_pricing_measure>'."\n";

						}

						

						if($product['g_unit_pricing_measure']!='' && $product['g_unit_pricing_base_measure']!='' && !$product['g_energy_efficiency_class']){

							$output .= '<g:unit_pricing_base_measure>' . $product['g_unit_pricing_base_measure'] . '</g:unit_pricing_base_measure>'."\n";

						}



						if($product['reviews']>0){

							$output .= '<g:product_review_count>' . $product['reviews'] . '</g:product_review_count>'."\n";

							$output .= '<g:product_review_average>' . $product['rating'] . '</g:product_review_average>'."\n";

						}

						

						$categories = $this->model_feed_uksb_google->getCategories($product['product_id']);

						

						$catno = 1;

						$gpcc = '';

						foreach ($categories as $category) {

							if($catno<11){

								$path = $this->getPath(array('parent_id' => $category['category_id']));

								$gpcc = ($gpcc == '' && $gpc_suffix != '' ? $this->model_feed_uksb_google->getCategoryGoogleCategories($category['category_id'], $gpc_suffix) : $gpcc);

								

								if ($path) {

									$string = '';

									

									foreach (explode('_', $path) as $path_id) {

										$category_info = $this->model_feed_uksb_google->getCategory($path_id);

										

										if ($category_info) {

											if (!$string) {

												$string = $category_info['name'];

											} else {

												$string .= ' &gt; ' . $category_info['name'];

											}

										}

									}

									

									// $output .= '<g:product_type><![CDATA[' . $string . ']]></g:product_type>'."\n";

									$catno++;

								}

							}

						}

						$this->load->model('catalog/catalog');

						foreach($this->model_catalog_catalog->getProductInfoManu($product['product_id']) as $productx ){



							$output .= '<g:product_type><![CDATA[' . $productx . ']]></g:product_type>'."\n";



						}

						

						$this->load->model('catalog/product');

						$mainClass = $this->model_catalog_product->getProductMainClass($product['product_id']);

						$product_quality=$this->model_catalog_product->getProductQuality($product['model']);



						if(isset($gtins[$j])&&trim($gtins[$j])!=''&&$product['g_identifier_exists']>0){

							// $output .= '<g:gtin><![CDATA[' . $gtins[$j] . ']]></g:gtin>'."\n";

						}

						

						$output .= '<g:identifier_exists><![CDATA[' . ($product['g_identifier_exists']>0 ? 'TRUE' : 'FALSE' ) . ']]></g:identifier_exists>'."\n";

						

						if((float)$product['weight']){

							$output .= '<g:shipping_weight>' . $this->weight->format($product['weight'], $product['weight_class_id']) . '</g:shipping_weight>'."\n";

						}

						

						if($product['g_size_type']){

							$output .= '<g:size_type>' . $product['g_size_type'] . '</g:size_type>'."\n";

						}



						if($product['g_size_system']){

							$output .= '<g:size_system>' . $product['g_size_system'] . '</g:size_system>'."\n";

						}





						if($gpc_suffix!=''){

							if($product['google_category_'.$gpc_suffix]!=''){

								$output .= '<g:google_product_category><![CDATA[' . html_entity_decode($product['google_category_'.$gpc_suffix], ENT_QUOTES, 'UTF-8') . ']]></g:google_product_category>'."\n";

							}elseif($gpcc!=''){

								$output .= '<g:google_product_category><![CDATA[' . html_entity_decode($gpcc, ENT_QUOTES, 'UTF-8') . ']]></g:google_product_category>'."\n";

							}elseif($gpc!=''){

								$output .= '<g:google_product_category><![CDATA[' . $gpc . ']]></g:google_product_category>'."\n";

							}

						}

						

						if($product['g_gender']){

							$output .= '<g:gender>' . ($product['g_gender']?$product['g_gender']:$this->config->get('uksb_google_merchant_gender')) . '</g:gender>'."\n";

						}

						

						if($product['g_age_group']){

							$output .= '<g:age_group>' . ($product['g_age_group']?$product['g_age_group']:$this->config->get('uksb_google_merchant_age_group')) . '</g:age_group>'."\n";

						}

						

						if($product['g_adult']){

							$output .= '<g:adult>TRUE</g:adult>'."\n";

						}

						

						if($product['g_energy_efficiency_class']){

							$output .= '<g:energy_efficiency_class>' . $product['g_energy_efficiency_class'] . '</g:energy_efficiency_class>'."\n";

						}



						if(isset($colours[$j])&&trim($colours[$j])!=''){

							$output .= '<g:'.$col.'><![CDATA[' . trim($colours[$j]) . ']]></g:'.$col.'>'."\n";

						}

						

						if(isset($sizes[$j])&&trim($sizes[$j])!=''){

							$output .= '<g:size><![CDATA[' . trim($sizes[$j]) . ']]></g:size>'."\n";

						}

						

						if(isset($materials[$j])&&trim($materials[$j])!=''){

							$output .= '<g:material><![CDATA[' . trim($materials[$j]) . ']]></g:material>'."\n";

						}

						

						if(isset($patterns[$j])&&trim($patterns[$j])!=''){

							$output .= '<g:pattern><![CDATA[' . trim($patterns[$j]) . ']]></g:pattern>'."\n";

						}

						

						if($mainClass){

							$output .= '<g:custom_label_0><![CDATA[' . $mainClass . ']]></g:custom_label_0>'."\n";

						}



						if($product['g_custom_label_1']!=''){

							$output .= '<g:custom_label_1><![CDATA[' . $product['g_custom_label_1'] . ']]></g:custom_label_1>'."\n";

						}



						if($product_quality){

							$output .= '<g:custom_label_2><![CDATA[' . $product_quality . ']]></g:custom_label_2>'."\n";

						}



						if($product['g_custom_label_3']!=''){

							$output .= '<g:custom_label_3><![CDATA[' . $product['g_custom_label_3'] . ']]></g:custom_label_3>'."\n";

						}



						if($product['g_custom_label_4']!=''){

							$output .= '<g:custom_label_4><![CDATA[' . $product['g_custom_label_4'] . ']]></g:custom_label_4>'."\n";

						}



						if($product['g_adwords_redirect']!=''){

							$output .= '<g:adwords_redirect><![CDATA[' . $product['g_adwords_redirect'] . ']]></g:adwords_redirect>'."\n";

						}

						

						$output .= '</item>'."\n\n\n";

					}

				}else{



					$output .= '<item>'."\n";

					

					$output .= '<title><![CDATA[' . $product['name'] . ']]></title>'."\n";

					

					$output .= '<link><![CDATA[' . $this->url->link('product/product', 'product_id=' . $product['product_id'] . '&language=' . $lang . '&currency='. $curr) . ']]></link>'."\n";

					

					$output .= '<description><![CDATA[' . $this->plainText((($product['description']) ? $product['description'] : $product['name'])) . ']]></description>'."\n";

					

					$output .= '<g:brand><![CDATA[' . ((strtolower(substr($product['sku'], 0, 2)) == 'BT') ? 'BinTEK' : 'PhonePartsUSA' ) . ']]></g:brand>'."\n";

					

					$output .= '<g:condition>' . ($product['g_condition']?$product['g_condition']:$this->config->get('uksb_google_merchant_condition')) . '</g:condition>'."\n";

					

					$output .= '<g:id><![CDATA[' . $product['product_id'] . '_' . $id_suffix . ']]></g:id>'."\n";

					

					if ($product['image']) {

						$output .= '<g:image_link><![CDATA[' . $server . 'image/' . str_replace(" ", "%20", $product['image']) . ']]></g:image_link>'."\n";

					}

					

					$addimages = $this->model_feed_uksb_google->getProductImages($product['product_id']);

					

					$addimnum = 0;

					foreach($addimages as $addimage){

						if($addimnum<10){

							$output .= '<g:additional_image_link><![CDATA[' . $server . 'image/' . str_replace(" ", "%20", $addimage['image']) . ']]></g:additional_image_link>'."\n";

						}

						$addimnum++;

					}

					

					if ($product['quantity']>0) {

						$output .= '<g:availability>in stock</g:availability>'."\n";

					} else {

						$output .= '<g:availability>' . ($this->config->get('config_stock_checkout')==0 ? 'out of stock' : 'in stock') . '</g:availability>'."\n";

					}

					

					if($product['g_multipack']!='0'){

						$output .= '<g:multipack><![CDATA[' . $product['g_multipack'] . ']]></g:multipack>'."\n";

					}

					

					if($product['g_is_bundle']){

						$output .= '<g:is_bundle>TRUE</g:is_bundle>'."\n";

					}

					

					if($product['g_expiry_date']){

						$output .= '<g:expiration_date>' . $product['g_expiry_date'] . '</g:expiration_date>'."\n";

					}



					if($product['g_identifier_exists']>0){

						if($this->config->get('uksb_google_merchant_mpn')=='mpn'){

							if($product['g_mpn']){

								$output .= '<g:mpn><![CDATA[' . $product['g_mpn'] . ']]></g:mpn>'."\n";

							}

						}elseif($this->config->get('uksb_google_merchant_mpn')=='location'){

							if($product['location']){

								$output .= '<g:mpn><![CDATA[' . $product['location'] . ']]></g:mpn>'."\n";

							}

						}elseif($this->config->get('uksb_google_merchant_mpn')=='sku'){

							if($product['sku']){

								$output .= '<g:mpn><![CDATA[' . $product['sku'] . ']]></g:mpn>'."\n";

							}

						}else{

							if($product['model']){

								$output .= '<g:mpn><![CDATA[' . $product['model'] . ']]></g:mpn>'."\n";

							}

						}

					}

					

					

					if ((float)$product['special']) {

						if($tax > 0){

							$output .= '<g:sale_price>' .  $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id']), $currency, FALSE, FALSE) . ' ' . $currency . '</g:sale_price>'."\n";

						}else{

							$output .= '<g:sale_price>' .  $this->currency->format($product['special'], $currency, FALSE, FALSE) . ' ' . $currency . '</g:sale_price>'."\n";

						}

						$output .= '<g:sale_price_effective_date>' . $this->model_feed_uksb_google->getFeedSpecialStartDate($product['product_id']).'T00:00:00'.date("P").'/'.$this->model_feed_uksb_google->getFeedSpecialEndDate($product['product_id']).'T23:59:59'.date("P").'</g:sale_price_effective_date>'."\n";

					}

					

					if($tax > 0){

						$output .= '<g:price>' . $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id']), $currency, FALSE, FALSE) . ' ' . $currency . '</g:price>'."\n";

					}else{

						$output .= '<g:price>' . $this->currency->format($product['price'], $currency, FALSE, FALSE) . ' ' . $currency . '</g:price>'."\n";

					}

					

					if($product['g_unit_pricing_measure']!='' && !$product['g_energy_efficiency_class']){

						$output .= '<g:unit_pricing_measure>' . $product['g_unit_pricing_measure'] . '</g:unit_pricing_measure>'."\n";

					}

					

					if($product['g_unit_pricing_measure']!='' && $product['g_unit_pricing_base_measure']!='' && !$product['g_energy_efficiency_class']){

						$output .= '<g:unit_pricing_base_measure>' . $product['g_unit_pricing_base_measure'] . '</g:unit_pricing_base_measure>'."\n";

					}



					if($product['reviews']>0){

						$output .= '<g:product_review_count>' . $product['reviews'] . '</g:product_review_count>'."\n";

						$output .= '<g:product_review_average>' . $product['rating'] . '</g:product_review_average>'."\n";

					}

					

					$categories = $this->model_feed_uksb_google->getCategories($product['product_id']);

					

					$catno = 1;

					$gpcc = '';

					

					foreach ($categories as $category) {

						if($catno<11){

							$path = $this->getPath(array('parent_id' => $category['category_id']));

							

							$gpcc = ($gpcc=='' && $gpc_suffix != '' ? $this->model_feed_uksb_google->getCategoryGoogleCategories($category['category_id'], $gpc_suffix) : $gpcc);

							

							if ($path) {

								$string = '';

								

								foreach (explode('_', $path) as $path_id) {

									$category_info = $this->model_feed_uksb_google->getCategory($path_id);

									

									if ($category_info) {

										if (!$string) {

											$string = $category_info['name'];

										} else {

											$string .= ' &gt; ' . $category_info['name'];

										}

									}

								}

								

								// $output .= '<g:product_type><![CDATA[' . $string . ']]></g:product_type>'."\n";

								$catno++;

							}

						}

					}



					$this->load->model('catalog/catalog');

					foreach($this->model_catalog_catalog->getProductInfoManu($product['product_id']) as $productx ){



						$output .= '<g:product_type><![CDATA[' . $productx . ']]></g:product_type>'."\n";



					}



					$this->load->model('catalog/product');

					$mainClass = $this->model_catalog_product->getProductMainClass($product['product_id']);

					$product_quality=$this->model_catalog_product->getProductQuality($product['model']);



					if($product['g_identifier_exists']>0){

						if($this->config->get('uksb_google_merchant_g_gtin')=='gtin'){

							if($product['g_gtin']){

								// $output .= '<g:gtin><![CDATA[' . $product['g_gtin'] . ']]></g:gtin>'."\n";

							}

						}elseif($this->config->get('uksb_google_merchant_g_gtin')=='location'){

							if($product['location']){

								// $output .= '<g:gtin><![CDATA[' . $product['location'] . ']]></g:gtin>'."\n";

							}

						}elseif($this->config->get('uksb_google_merchant_g_gtin')=='sku'){

							if($product['sku']){

								// $output .= '<g:gtin><![CDATA[' . $product['sku'] . ']]></g:gtin>'."\n";

							}

						}elseif($this->config->get('uksb_google_merchant_g_gtin')=='upc'){

							if($product['upc']!=''){

								// $output .= '<g:gtin><![CDATA[' . $product['upc'] . ']]></g:gtin>'."\n";

							}

						}

					}

					

					$output .= '<g:identifier_exists><![CDATA[' . ($product['g_identifier_exists']>0 ? 'TRUE' : 'FALSE' ) . ']]></g:identifier_exists>'."\n";

					

					if((float)$product['weight']){

						$output .= '<g:shipping_weight>' . $this->weight->format($product['weight'], $product['weight_class_id']) . '</g:shipping_weight>'."\n";

					}

					

					if($product['g_size_type']){

						$output .= '<g:size_type>' . $product['g_size_type'] . '</g:size_type>'."\n";

					}



					if($product['g_size_system']){

						$output .= '<g:size_system>' . $product['g_size_system'] . '</g:size_system>'."\n";

					}



					if($gpc_suffix != ''){

						if($product['google_category_'.$gpc_suffix]!=''){

							$output .= '<g:google_product_category><![CDATA[' . html_entity_decode($product['google_category_'.$gpc_suffix], ENT_QUOTES, 'UTF-8') . ']]></g:google_product_category>'."\n";

						}elseif($gpcc!=''){

							$output .= '<g:google_product_category><![CDATA[' . html_entity_decode($gpcc, ENT_QUOTES, 'UTF-8') . ']]></g:google_product_category>'."\n";

						}elseif($gpc!=''){

							$output .= '<g:google_product_category><![CDATA[' . $gpc . ']]></g:google_product_category>'."\n";

						}

					}

					

					if($product['g_gender']){

						$output .= '<g:gender>' . ($product['g_gender']?$product['g_gender']:$this->config->get('uksb_google_merchant_gender')) . '</g:gender>'."\n";

					}

					

					if($product['g_age_group']){

						$output .= '<g:age_group>' . ($product['g_age_group']?$product['g_age_group']:$this->config->get('uksb_google_merchant_age_group')) . '</g:age_group>'."\n";

					}

					

					if($product['g_adult']){

						$output .= '<g:adult>TRUE</g:adult>'."\n";

					}

					

					if($product['g_energy_efficiency_class']){

						$output .= '<g:energy_efficiency_class>' . $product['g_energy_efficiency_class'] . '</g:energy_efficiency_class>'."\n";

					}



					if($product['g_colour']){

						$output .= '<g:'.$col.'><![CDATA[' . $product['g_colour'] . ']]></g:'.$col.'>'."\n";

					}

					

					if($product['g_size']){

						$output .= '<g:size><![CDATA[' . $product['g_size'] . ']]></g:size>'."\n";

					}

					

					if($product['g_material']){

						$output .= '<g:material><![CDATA[' . $product['g_material'] . ']]></g:material>'."\n";

					}

					

					if($product['g_pattern']){

						$output .= '<g:pattern><![CDATA[' . $product['g_pattern'] . ']]></g:pattern>'."\n";

					}

					

					if($mainClass){

						$output .= '<g:custom_label_0><![CDATA[' . $mainClass . ']]></g:custom_label_0>'."\n";

					}



					if($product['g_custom_label_1']!=''){

						$output .= '<g:custom_label_1><![CDATA[' . $product['g_custom_label_1'] . ']]></g:custom_label_1>'."\n";

					}



					if($product_quality){

						$output .= '<g:custom_label_2><![CDATA[' . $product_quality . ']]></g:custom_label_2>'."\n";

					}



					if($product['g_custom_label_3']!=''){

						$output .= '<g:custom_label_3><![CDATA[' . $product['g_custom_label_3'] . ']]></g:custom_label_3>'."\n";

					}



					if($product['g_custom_label_4']!=''){

						$output .= '<g:custom_label_4><![CDATA[' . $product['g_custom_label_4'] . ']]></g:custom_label_4>'."\n";

					}



					if($product['g_adwords_redirect']!=''){

						$output .= '<g:adwords_redirect><![CDATA[' . $product['g_adwords_redirect'] . ']]></g:adwords_redirect>'."\n";

					}

					

					$output .= '</item>'."\n\n\n";

				}

			}

			

			if(!isset($this->request->get['mode'])){

				$output .= '</channel>'."\n"; 

				$output .= '</rss>';	



				if(!isset($this->request->get['mode'])){

					$this->response->addHeader('Content-Type: text/xml; charset=utf-8');

					$this->response->setCompression(0);

					$this->response->setOutput($output);

				}

			}

			

			if(isset($this->request->get['mode'])){

				if($this->model_feed_uksb_google->getTotalProducts() <= $split[1]){

					$output .= '</channel>'."\n"; 

					$output .= '</rss>';	

				}



				$fspath = str_replace("/system/", "/uksb_feeds/google_".$lang."-".$curr.".xml", DIR_SYSTEM);

				$writemode = ($this->request->get['mode']=='cron' && $split[0] == 1 ? 'w' : 'a');

				

				$fp = fopen($fspath, $writemode);

				fwrite($fp, $output);

				fclose($fp);

				

				if($this->model_feed_uksb_google->getTotalProducts() > $split[1]){

					$this->redirect('index.php?route=feed/uksb_google_merchant&mode=cron&language='.$lang.'&currency='.$curr.'&send='.($split[0]+$cronlimit).'-'.($split[1]+$cronlimit));

				}

			}

		}

	}



	public function isCyrillic($string) {

		return preg_match('/[А-Яа-яЁё]/u', $string);

	}



	public function plainText($string) {

		setlocale(LC_ALL, "en_US.utf8");



		$table = array(

			'“'=>'&#39;', '”'=>'&#39;', '‘'=>"&#34;", '’'=>"&#34;", '•'=>'*', '—'=>'-', '–'=>'-', '¿'=>'?', '¡'=>'!', '°'=>' deg. ',

			'÷'=>' / ', '×'=>'X', '±'=>'+/-',

			'&nbsp;'=> ' ', '"'=> '&#34;', "'"=> '&#39;', '<'=> '&lt;', '>'=> '&gt;', "\n"=> ' ', "\r"=> ' '

			);

		

		$string = strip_tags(html_entity_decode($string));

		$string = strtr($string, $table);

		$string = preg_replace('/&#?[a-z0-9]+;/i',' ',$string);	

		$string = preg_replace('/\s{2,}/i', ' ', $string );	

		if($this->config->get('uksb_google_merchant_characters')){

			if($this->isCyrillic($string)){

				$string = iconv(mb_detect_encoding($string), "UTF-8//IGNORE//TRANSLIT", $string);

			}else{

				$table2 = array(

					'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 

					'Þ'=>'B', 'þ'=>'b', 'ß'=>'Ss',

					'ç'=>'c',

					'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 

					'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',

					'ñ'=>'n',

					'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'œ'=>'o', 'ð'=>'o',

					'š'=>'s',

					'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u',

					'ý'=>'y', 'ÿ'=>'y', 

					'ž'=>'z', 'ž'=>'z',

					'©'=>'(c)', '®'=>'(R)'

					);

				

				$string = strtr($string, $table2);

				$string = preg_replace('/[^(\x20-\x7F)]*/','', $string ); 

			}

		}

		return substr($string, 0, 5000 );	

	}



	protected function getPath($params = array())

	{

		$defaults = array( // the defaults will be overidden if set in $params

			'parent_id' => 0,

			'current_path' => '',

			);



		$params = array_merge($defaults, $params);



		$category_info = $this->model_feed_uksb_google->getCategory($params['parent_id']);



		if ($category_info) {

			if (!$params['current_path']) {

				$new_path = $category_info['category_id'];

			} else {

				$new_path = $category_info['category_id'] . '_' . $params['current_path'];

			}	



			$path = $this->getPath(array('parent_id' => $category_info['parent_id'], 'current_path' => $new_path));



			if ($path) {

				return $path;

			} else {

				return $new_path;

			}

		}

	}		

}

?>