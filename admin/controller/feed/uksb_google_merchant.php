<?php 
class ControllerFeedUksbGoogleMerchant extends Controller {
	private $error = array(); 
	
	public function index() {
		$this->language->load('uksb_licensing/uksb_licensing');
		
		$this->data['regerror_email'] = $this->language->get('regerror_email');
		$this->data['regerror_orderid'] = $this->language->get('regerror_orderid');
		$this->data['regerror_noreferer'] = $this->language->get('regerror_noreferer');
		$this->data['regerror_localhost'] = $this->language->get('regerror_localhost');
		$this->data['regerror_licensedupe'] = $this->language->get('regerror_licensedupe');
		$this->data['regerror_quote_msg'] = $this->language->get('regerror_quote_msg');
		$this->data['license_purchase_thanks'] = $this->language->get('license_purchase_thanks');
		$this->data['license_registration'] = $this->language->get('license_registration');
		$this->data['license_opencart_email'] = $this->language->get('license_opencart_email');
		$this->data['license_opencart_orderid'] = $this->language->get('license_opencart_orderid');
		$this->data['license_update'] = $this->language->get('license_update');
		$this->data['license_updated'] = $this->language->get('license_updated');
		$this->data['license_update_info'] = $this->language->get('license_update_info');
		$this->data['license_update_localhost'] = $this->language->get('license_update_localhost');
		$this->data['license_update_error'] = $this->language->get('license_update_error');
		$this->data['check_email'] = $this->language->get('check_email');
		$this->data['check_orderid'] = $this->language->get('check_orderid');
		$this->data['server_error_curl'] = $this->language->get('server_error_curl');
		
		$this->data['uksb_install_link'] = $this->url->link('feed/uksb_google_merchant/uksb_install', 'token=' . $this->session->data['token'], 'SSL');
		

		$this->language->load('feed/uksb_google_merchant');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('uksb_google_merchant', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_condition_new'] = $this->language->get('text_condition_new');
		$this->data['text_condition_used'] = $this->language->get('text_condition_used');
		$this->data['text_condition_ref'] = $this->language->get('text_condition_ref');
		$this->data['text_male'] = $this->language->get('text_male');
		$this->data['text_female'] = $this->language->get('text_female');
		$this->data['text_unisex'] = $this->language->get('text_unisex');
		$this->data['text_newborn'] = $this->language->get('text_newborn');
		$this->data['text_toddler'] = $this->language->get('text_toddler');
		$this->data['text_infant'] = $this->language->get('text_infant');
		$this->data['text_kids'] = $this->language->get('text_kids');
		$this->data['text_adult'] = $this->language->get('text_adult');
		$this->data['text_model'] = $this->language->get('text_model');
		$this->data['text_location'] = $this->language->get('text_location');
		$this->data['text_gtin'] = $this->language->get('text_gtin');
		$this->data['text_mpn'] = $this->language->get('text_mpn');
		$this->data['text_sku'] = $this->language->get('text_sku');
		$this->data['text_upc'] = $this->language->get('text_upc');
		$this->data['text_initialise_data'] = $this->language->get('text_initialise_data');
		$this->data['text_initialise_data_text'] = $this->language->get('text_initialise_data_text');
		
		$this->data['tab_general_settings'] = $this->language->get('tab_general_settings');
		$this->data['tab_google_settings'] = $this->language->get('tab_google_settings');
		$this->data['tab_google_feeds'] = $this->language->get('tab_google_feeds');
		$this->data['tab_bing_feeds'] = $this->language->get('tab_bing_feeds');
		$this->data['tab_utilities'] = $this->language->get('tab_utilities');
		$this->data['tab_videos'] = $this->language->get('tab_videos');

		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_condition'] = $this->language->get('entry_condition');
		$this->data['entry_mpn'] = $this->language->get('entry_mpn');
		$this->data['entry_gtin'] = $this->language->get('entry_gtin');
		$this->data['entry_gender'] = $this->language->get('entry_gender');
		$this->data['entry_age_group'] = $this->language->get('entry_age_group');
		$this->data['entry_characters'] = $this->language->get('entry_characters');
		$this->data['entry_split'] = $this->language->get('entry_split');
		$this->data['entry_cron'] = $this->language->get('entry_cron');
		$this->data['entry_site'] = $this->language->get('entry_site');
		$this->data['entry_google_category'] = $this->language->get('entry_google_category');
		$this->data['entry_choose_google_category'] = $this->language->get('entry_choose_google_category');
		$this->data['entry_choose_google_category_xml'] = $this->language->get('entry_choose_google_category_xml');
		$this->data['entry_info'] = $this->language->get('entry_info');
		$this->data['entry_data_feed'] = $this->language->get('entry_data_feed');
		$this->data['entry_cron_code'] = $this->language->get('entry_cron_code');
		
		$this->data['help_brand'] = $this->language->get('help_brand');
		$this->data['help_condition'] = $this->language->get('help_condition');
		$this->data['help_mpn'] = $this->language->get('help_mpn');
		$this->data['help_gtin'] = $this->language->get('help_gtin');
		$this->data['help_gender'] = $this->language->get('help_gender');
		$this->data['help_age_group'] = $this->language->get('help_age_group');
		$this->data['help_characters'] = $this->language->get('help_characters');
		$this->data['help_split'] = $this->language->get('help_split');
		$this->data['help_split_help'] = $this->language->get('help_split_help');
		$this->data['help_cron'] = $this->language->get('help_cron');
		$this->data['help_cron_code'] = $this->language->get('help_cron_code');
		$this->data['help_site'] = $this->language->get('help_site');
		$this->data['help_google_category'] = $this->language->get('help_google_category');
		$this->data['help_info'] = $this->language->get('help_info');
		
		$this->data['utilities1'] = $this->language->get('utilities1');
		$this->data['utilities2'] = $this->language->get('utilities2');
		$this->data['utilities3'] = $this->language->get('utilities3');
		$this->data['utilities4'] = $this->language->get('utilities4');
		$this->data['utilities5'] = $this->language->get('utilities5');
		$this->data['utilities6'] = $this->language->get('utilities6');
		$this->data['utilities7'] = $this->language->get('utilities7');
		$this->data['utilities8'] = $this->language->get('utilities8');
		$this->data['utilities9'] = $this->language->get('utilities9');
		$this->data['utilities_confirm'] = $this->language->get('utilities_confirm');
		
		$this->data['button_run'] = $this->language->get('button_run');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['duplicate'])) {
			$this->data['error_duplicate'] = $this->error['duplicate'];
		} else {
			$this->data['error_duplicate'] = '';
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_feed'),
			'href'      => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('feed/uksb_google_merchant', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('feed/uksb_google_merchant', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['uksb_google_merchant_status'])) {
			$this->data['uksb_google_merchant_status'] = $this->request->post['uksb_google_merchant_status'];
		} else {
			$this->data['uksb_google_merchant_status'] = $this->config->get('uksb_google_merchant_status');
		}
		
		if (isset($this->request->post['uksb_google_merchant_mpn'])) {
			$this->data['uksb_google_merchant_mpn'] = $this->request->post['uksb_google_merchant_mpn'];
		} else {
			$this->data['uksb_google_merchant_mpn'] = $this->config->get('uksb_google_merchant_mpn');
		}
		
		if (isset($this->request->post['uksb_google_merchant_condition'])) {
			$this->data['uksb_google_merchant_condition'] = $this->request->post['uksb_google_merchant_condition'];
		} else {
			$this->data['uksb_google_merchant_condition'] = $this->config->get('uksb_google_merchant_condition');
		}
		
		if (isset($this->request->post['uksb_google_merchant_g_gtin'])) {
			$this->data['uksb_google_merchant_g_gtin'] = $this->request->post['uksb_google_merchant_g_gtin'];
		} else {
			$this->data['uksb_google_merchant_g_gtin'] = $this->config->get('uksb_google_merchant_g_gtin');
		}
		
		if (isset($this->request->post['uksb_google_merchant_gender'])) {
			$this->data['uksb_google_merchant_gender'] = $this->request->post['uksb_google_merchant_gender'];
		} else {
			$this->data['uksb_google_merchant_gender'] = $this->config->get('uksb_google_merchant_gender');
		}
		
		if (isset($this->request->post['uksb_google_merchant_age_group'])) {
			$this->data['uksb_google_merchant_age_group'] = $this->request->post['uksb_google_merchant_age_group'];
		} else {
			$this->data['uksb_google_merchant_age_group'] = $this->config->get('uksb_google_merchant_age_group');
		}
				
		if (isset($this->request->post['uksb_google_merchant_characters'])) {
			$this->data['uksb_google_merchant_characters'] = $this->request->post['uksb_google_merchant_characters'];
		} else {
			$this->data['uksb_google_merchant_characters'] = $this->config->get('uksb_google_merchant_characters');
		}
		
		if (isset($this->request->post['uksb_google_merchant_split'])) {
			$this->data['uksb_google_merchant_split'] = $this->request->post['uksb_google_merchant_split'];
		} else {
			$this->data['uksb_google_merchant_split'] = $this->config->get('uksb_google_merchant_split');
		}
		
		if (isset($this->request->post['uksb_google_merchant_cron'])) {
			$this->data['uksb_google_merchant_cron'] = $this->request->post['uksb_google_merchant_cron'];
		} else {
			$this->data['uksb_google_merchant_cron'] = $this->config->get('uksb_google_merchant_cron');
		}
		
		if (isset($this->request->post['uksb_google_merchant_google_category_gb'])) {
			$this->data['uksb_google_merchant_google_category_gb'] = $this->request->post['uksb_google_merchant_google_category_gb'];
			$this->data['uksb_google_merchant_google_category_us'] = $this->request->post['uksb_google_merchant_google_category_us'];
			$this->data['uksb_google_merchant_google_category_au'] = $this->request->post['uksb_google_merchant_google_category_au'];
			$this->data['uksb_google_merchant_google_category_fr'] = $this->request->post['uksb_google_merchant_google_category_fr'];
			$this->data['uksb_google_merchant_google_category_de'] = $this->request->post['uksb_google_merchant_google_category_de'];
			$this->data['uksb_google_merchant_google_category_it'] = $this->request->post['uksb_google_merchant_google_category_it'];
			$this->data['uksb_google_merchant_google_category_nl'] = $this->request->post['uksb_google_merchant_google_category_nl'];
			$this->data['uksb_google_merchant_google_category_es'] = $this->request->post['uksb_google_merchant_google_category_es'];
			$this->data['uksb_google_merchant_google_category_pt'] = $this->request->post['uksb_google_merchant_google_category_pt'];
			$this->data['uksb_google_merchant_google_category_cz'] = $this->request->post['uksb_google_merchant_google_category_cz'];
			$this->data['uksb_google_merchant_google_category_jp'] = $this->request->post['uksb_google_merchant_google_category_jp'];
			$this->data['uksb_google_merchant_google_category_dk'] = $this->request->post['uksb_google_merchant_google_category_dk'];
			$this->data['uksb_google_merchant_google_category_no'] = $this->request->post['uksb_google_merchant_google_category_no'];
			$this->data['uksb_google_merchant_google_category_pl'] = $this->request->post['uksb_google_merchant_google_category_pl'];
			$this->data['uksb_google_merchant_google_category_ru'] = $this->request->post['uksb_google_merchant_google_category_ru'];
			$this->data['uksb_google_merchant_google_category_sv'] = $this->request->post['uksb_google_merchant_google_category_sv'];
			$this->data['uksb_google_merchant_google_category_tr'] = $this->request->post['uksb_google_merchant_google_category_tr'];
		} else {
			$this->data['uksb_google_merchant_google_category_gb'] = $this->config->get('uksb_google_merchant_google_category_gb');
			$this->data['uksb_google_merchant_google_category_us'] = $this->config->get('uksb_google_merchant_google_category_us');
			$this->data['uksb_google_merchant_google_category_au'] = $this->config->get('uksb_google_merchant_google_category_au');
			$this->data['uksb_google_merchant_google_category_fr'] = $this->config->get('uksb_google_merchant_google_category_fr');
			$this->data['uksb_google_merchant_google_category_de'] = $this->config->get('uksb_google_merchant_google_category_de');
			$this->data['uksb_google_merchant_google_category_it'] = $this->config->get('uksb_google_merchant_google_category_it');
			$this->data['uksb_google_merchant_google_category_nl'] = $this->config->get('uksb_google_merchant_google_category_nl');
			$this->data['uksb_google_merchant_google_category_es'] = $this->config->get('uksb_google_merchant_google_category_es');
			$this->data['uksb_google_merchant_google_category_pt'] = $this->config->get('uksb_google_merchant_google_category_pt');
			$this->data['uksb_google_merchant_google_category_cz'] = $this->config->get('uksb_google_merchant_google_category_cz');
			$this->data['uksb_google_merchant_google_category_jp'] = $this->config->get('uksb_google_merchant_google_category_jp');
			$this->data['uksb_google_merchant_google_category_dk'] = $this->config->get('uksb_google_merchant_google_category_dk');
			$this->data['uksb_google_merchant_google_category_no'] = $this->config->get('uksb_google_merchant_google_category_no');
			$this->data['uksb_google_merchant_google_category_pl'] = $this->config->get('uksb_google_merchant_google_category_pl');
			$this->data['uksb_google_merchant_google_category_ru'] = $this->config->get('uksb_google_merchant_google_category_ru');
			$this->data['uksb_google_merchant_google_category_sv'] = $this->config->get('uksb_google_merchant_google_category_sv');
			$this->data['uksb_google_merchant_google_category_tr'] = $this->config->get('uksb_google_merchant_google_category_tr');
		}

		$this->load->model('feed/uksb_google_merchant');
		
		
		$this->data['data_feed'] = '';
		$this->data['data_bingfeed'] = '';
		if(!$this->config->get('uksb_google_merchant_cron')){
			if($this->config->get('uksb_google_merchant_split')>0){
				$split = $this->config->get('uksb_google_merchant_split');
				$totalproducts = $this->model_feed_uksb_google_merchant->getTotalProductsByStore(0);
				if($totalproducts>$split){
					$j = floor($totalproducts/$split);
					$rem = $totalproducts-($j*$split);
					for($i=1; $i<=$j; $i++){
						$from = (($i-1)*$split)+1;
						$to = $i*$split;
						$this->data['data_feed'] .= ($i>1?'^':'').HTTP_CATALOG.'index.php?route=feed/uksb_google_merchant&send='.$from.'-'.$to;
						$this->data['data_bingfeed'] .= ($i>1?'^':'').HTTP_CATALOG.'index.php?route=feed/uksb_bing_shopping&send='.$from.'-'.$to;
					}
					if($rem>0){
						$this->data['data_feed'] .= '^'.HTTP_CATALOG.'index.php?route=feed/uksb_google_merchant&send='.($to+1).'-'.($to+$split);
						$this->data['data_bingfeed'] .= '^'.HTTP_CATALOG.'index.php?route=feed/uksb_bing_shopping&send='.($to+1).'-'.($to+$split);
					}
				}else{
					$this->data['data_feed'] = HTTP_CATALOG.'index.php?route=feed/uksb_google_merchant';
					$this->data['data_bingfeed'] = HTTP_CATALOG.'index.php?route=feed/uksb_bing_shopping';
				}
			}else{
				$this->data['data_feed'] = HTTP_CATALOG.'index.php?route=feed/uksb_google_merchant';
				$this->data['data_bingfeed'] = HTTP_CATALOG.'index.php?route=feed/uksb_bing_shopping';
			}
		}else{
			$this->data['data_feed'] = HTTP_CATALOG.'uksb_feeds/';
			$this->data['data_cron_path'] = HTTP_CATALOG.'index.php?route=feed/uksb_google_merchant&mode=cron';
			
			$this->data['data_bingfeed'] = HTTP_CATALOG.'index.php?route=feed/uksb_bing_shopping';
		}
		
		$this->load->model('setting/store');

		if($this->model_setting_store->getTotalStores()>0){
			$stores = $this->model_setting_store->getStores();
			$stores = array_reverse($stores);
			
			foreach($stores as $store){
				if(!$this->config->get('uksb_google_merchant_cron')){
					if($this->config->get('uksb_google_merchant_split')>0){
						$split = $this->config->get('uksb_google_merchant_split');
						$totalproducts = $this->model_feed_uksb_google_merchant->getTotalProductsByStore($store['store_id']);
						if($totalproducts>$split){
							$j = floor($totalproducts/$split);
							$rem = $totalproducts-($j*$split);
							for($i=1; $i<=$j; $i++){
								$from = (($i-1)*$split)+1;
								$to = $i*$split;
								$this->data['data_feed'] .= '^'.$store['url'].'index.php?route=feed/uksb_google_merchant&send='.$from.'-'.$to;
								$this->data['data_bingfeed'] .= '^'.$store['url'].'index.php?route=feed/uksb_bing_shopping&send='.$from.'-'.$to;
							}
							if($rem>0){
								$this->data['data_feed'] .= '^'.$store['url'].'index.php?route=feed/uksb_google_merchant&send='.($to+1).'-'.($to+$split);
								$this->data['data_bingfeed'] .= '^'.$store['url'].'index.php?route=feed/uksb_bing_shopping&send='.($to+1).'-'.($to+$split);
							}
						}else{
							$this->data['data_feed'] .= '^'.$store['url'].'index.php?route=feed/uksb_google_merchant';
							$this->data['data_bingfeed'] .= '^'.$store['url'].'index.php?route=feed/uksb_bing_shopping';
						}
					}else{
						$this->data['data_feed'] .= '^'.$store['url'].'index.php?route=feed/uksb_google_merchant';
						$this->data['data_bingfeed'] .= '^'.$store['url'].'index.php?route=feed/uksb_bing_shopping';
					}
				}else{
					$this->data['data_feed'] .= '^'.$store['url'].'uksb_feeds/';
					$this->data['data_cron_path'] .= '^'.$store['url'].'index.php?route=feed/uksb_google_merchant&mode=cron';
			
					$this->data['data_bingfeed'] .= '^'.$store['url'].'index.php?route=feed/uksb_bing_shopping';
				}
			}
		}
		
		$this->data['state'] = $this->model_feed_uksb_google_merchant->checkInstallState();
		$this->template = 'feed/uksb_google_merchant.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	} 
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'feed/uksb_google_merchant')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['uksb_google_merchant_status'])&&$this->request->post['uksb_google_merchant_mpn']==$this->request->post['uksb_google_merchant_g_gtin']) {
			$this->error['duplicate'] = $this->language->get('error_duplicate');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}	

	public function uksb_install() {
		$this->load->model('feed/uksb_google_merchant');
		$this->model_feed_uksb_google_merchant->uksbInstall();
		$this->redirect($this->url->link('feed/uksb_google_merchant', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	public function install() {
		
		$this->load->model('feed/uksb_google_merchant');
		
		$this->model_feed_uksb_google_merchant->install();
	}	
}
?>