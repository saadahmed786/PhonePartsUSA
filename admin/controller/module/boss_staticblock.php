<?php 
class ControllerModuleBossStaticblock extends Controller {
	private $error = array(); 
	 
	public function index() {   
		$this->load->language('module/boss_staticblock');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('boss_staticblock', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_header_top'] = $this->language->get('text_header_top');
		$this->data['text_header_bottom'] = $this->language->get('text_header_bottom');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
        $this->data['text_footer_top'] = $this->language->get('text_footer_top');
		$this->data['text_footer_bottom'] = $this->language->get('text_footer_bottom');
        $this->data['text_alllayout'] = $this->language->get('text_alllayout');
		$this->data['text_default'] = $this->language->get('text_default');
		
		$this->data['entry_content'] = $this->language->get('entry_content');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_new_block'] = $this->language->get('button_add_new_block');
		
		$this->data['tab_block'] = $this->language->get('tab_block');
		
		$this->data['token'] = $this->session->data['token'];

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/boss_staticblock', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/boss_staticblock', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['modules'] = array();
		
		if (isset($this->request->post['boss_staticblock_module'])) {
			$this->data['modules'] = $this->request->post['boss_staticblock_module'];
		} elseif ($this->config->get('boss_staticblock_module')) { 
			$this->data['modules'] = $this->config->get('boss_staticblock_module');
		}	
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->load->model('setting/store');
		
		$this->data['stores'] = $this->model_setting_store->getStores();
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->template = 'module/boss_staticblock.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/boss_staticblock')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	private function getIdLayout($layout_name) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "layout WHERE LOWER(name) = LOWER('".$layout_name."')");
		return (int)$query->row['layout_id'];
	}
	
	public function install() 
	{
		$this->load->model('setting/setting');
		
		$this->load->model('localisation/language');
			
		$languages = $this->model_localisation_language->getLanguages();
		
		$array_description0 = array();
		$array_description1 = array();
		$array_description2 = array();
		$array_description3 = array();
		$array_description4 = array();
		$array_description5 = array();
		$array_description6 = array();
		$array_description7 = array();
		$array_description8 = array();
		$array_description9 = array();
		$array_description10 = array();
		$array_description11 = array();
						
		foreach($languages as $language){
			$array_description0{$language['language_id']} = '&lt;div class=&quot;static-header-top&quot;&gt;
&lt;div class=&quot;static-header static-1&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Mastercard&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/header_1.png&quot; title=&quot;Mastercard&quot; /&gt;&lt;/a&gt;
&lt;div class=&quot;static-content&quot;&gt;
&lt;h4&gt;Save&lt;span&gt;10%&lt;/span&gt;&lt;/h4&gt;

&lt;p&gt;For all products on monday&lt;/p&gt;
&lt;/div&gt;
&lt;/div&gt;

&lt;div class=&quot;static-header static-2&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Return&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/header_2.png&quot; title=&quot;Return&quot; /&gt;&lt;/a&gt;

&lt;div class=&quot;static-content&quot;&gt;
&lt;h4&gt;Free Return&lt;/h4&gt;

&lt;p&gt;Vestibulum sceleris nunc&lt;/p&gt;
&lt;/div&gt;
&lt;/div&gt;

&lt;div class=&quot;static-header static-3&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Shipping&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/header_3.png&quot; title=&quot;Shipping&quot; /&gt;&lt;/a&gt;

&lt;div class=&quot;static-content&quot;&gt;
&lt;h4&gt;Free Shipping&lt;/h4&gt;

&lt;p&gt;Aenean vitae neque&lt;/p&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;';
			$array_description1{$language['language_id']} = '&lt;div class=&quot;banner-home-2&quot;&gt;&lt;a class=&quot;banner-1&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;Mastercard&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner-header_01.jpg&quot; title=&quot;Mastercard&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-3&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;Mastercard&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner-header_03.jpg&quot; title=&quot;Mastercard&quot; /&gt;&lt;/a&gt;&lt;/div&gt;

&lt;div class=&quot;banner-home-1&quot;&gt;&lt;a class=&quot;banner-2&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;Mastercard&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner-header_02.jpg&quot; title=&quot;Mastercard&quot; /&gt;&lt;/a&gt;&lt;/div&gt;';
			$array_description2{$language['language_id']} = '&lt;div class=&quot;static-footer-1 column&quot;&gt;
&lt;h3&gt;Delivery &amp;amp; Return&lt;/h3&gt;

&lt;ul&gt;
	&lt;li&gt;&lt;a href=&quot;index.php?route=account/address&quot;&gt;Address&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Faucibus blandit&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Venenatis placerat&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Pellentesque non &lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Praesent egestas&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Faucibus blandit&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Malesuada tincidunt&lt;/a&gt;&lt;/li&gt;
&lt;/ul&gt;
&lt;/div&gt;';
			$array_description3{$language['language_id']} = '&lt;div class=&quot;static-footer-2 column&quot;&gt;
&lt;h3&gt;Conect with us&lt;/h3&gt;

&lt;ul&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Facebook&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/connect_01.png&quot; title=&quot;Facebook&quot; /&gt;Find Us on Facebook&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Vimeo&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/connect_03.png&quot; title=&quot;Vimeo&quot; /&gt;Watch Us on Vimeo&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Twitter&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/connect_02.png&quot; title=&quot;Twitter&quot; /&gt;Follow Us on Twitter&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Dribble&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/connect_04.png&quot; title=&quot;Dribble&quot; /&gt;Find Us on Dribble&lt;/a&gt;&lt;/li&gt;
&lt;/ul&gt;
&lt;/div&gt;';
			$array_description4{$language['language_id']} = '&lt;div class=&quot;static-footer-3 column&quot;&gt;
&lt;h3&gt;Reward Program&lt;/h3&gt;
&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Reward&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/reward_01.jpg&quot; title=&quot;Reward&quot; /&gt;&lt;/a&gt;

&lt;p&gt;About Rewards Program&lt;/p&gt;

&lt;p&gt;Rewards / Points Dashboard&lt;/p&gt;
&lt;/div&gt;';
			$array_description5{$language['language_id']} = '&lt;div class=&quot;partner&quot;&gt;
&lt;h3&gt;Our Preferred Partners:&lt;/h3&gt;

&lt;ul&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Ups&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_01.png&quot; title=&quot;Ups&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Visa&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_02.png&quot; title=&quot;Visa&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;MasterCard&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_03.png&quot; title=&quot;MasterCard&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;AmericanExpress&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_04.png&quot; title=&quot;AmericanExpress&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Paypal&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_05.png&quot; title=&quot;Paypal&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Skrill&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_06.png&quot; title=&quot;Skrill&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;Western Union&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_07.png&quot; title=&quot;Western Union&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;EMS&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_08.png&quot; title=&quot;EMS&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;DHL&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_09.png&quot; title=&quot;DHL&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;&lt;img alt=&quot;FedEx&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/partner_10.png&quot; title=&quot;FedEx&quot; /&gt;&lt;/a&gt;&lt;/li&gt;
&lt;/ul&gt;
&lt;/div&gt;';
			$array_description6{$language['language_id']} = '&lt;div class=&quot;footer-Category&quot;&gt;
&lt;h3&gt;featured Categories&lt;/h3&gt;

&lt;ul&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;MarkPro Group: Proin&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Mattis&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Dignissim&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Turpis&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Venenatis&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Sed&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Adipiscing&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Massa&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Neque&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Nibh&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Tristique&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Egestas&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Auctor&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Sapien&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Nam&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Dignissim&lt;/a&gt;&lt;/li&gt;
&lt;/ul&gt;

&lt;ul&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Turpis&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Nulla&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Venenatis&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Sed&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Adipiscing&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Massa&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Neque&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Nibh&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Tristique&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Egestas&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Auctor&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Sapien&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Nam&lt;/a&gt;&lt;/li&gt;
	&lt;li&gt;&lt;a href=&quot;#&quot;&gt;Metus&lt;/a&gt;&lt;/li&gt;
	&lt;li class=&quot;last&quot;&gt;&lt;a href=&quot;#&quot;&gt;Nulla&lt;/a&gt;&lt;/li&gt;
&lt;/ul&gt;
&lt;/div&gt;

&lt;div id=&quot;powered&quot;&gt;
&lt;p&gt;&lt;a href=&quot;http://bossthemes.com/opencart-themes/optronics.html&quot;&gt;Optronics Theme&lt;/a&gt; by &lt;a href=&quot;http://bossthemes.com/&quot;&gt;BossThemes&lt;/a&gt;. &copy; 2013 Powered By &lt;a href=&quot;www.opencart.com&quot;&gt;OpenCart&lt;/a&gt;.&lt;/p&gt;
&lt;/div&gt;';
			$array_description7{$language['language_id']} = '&lt;div class=&quot;banner-home-column&quot;&gt;&lt;a class=&quot;banner-column-1&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_right_01.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-column-2&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_right_02.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-column-3&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_right_03.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-column-4&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_right_04.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-column-5&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_right_05.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-column-6&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_right_06.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt;&lt;/div&gt;';
			$array_description8{$language['language_id']} = '&lt;div class=&quot;banner-home-3 banner-block01&quot;&gt;&lt;a class=&quot;banner-1&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_block_01.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-2&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_block_02.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt;&lt;/div&gt;';
			$array_description9{$language['language_id']} = '&lt;div class=&quot;banner-home-3 banner-block02&quot;&gt;&lt;a class=&quot;banner-1&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_block_03.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-2&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_block_04.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt;&lt;/div&gt;';
			$array_description10{$language['language_id']} = '&lt;div class=&quot;banner-home-3 banner-block03&quot;&gt;&lt;a class=&quot;banner-1&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_block_05.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-2&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_block_06.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt;&lt;/div&gt;';
			$array_description11{$language['language_id']} = '&lt;div class=&quot;banner-home-3 banner-block04&quot;&gt;&lt;a class=&quot;banner-1&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_block_07.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt; &lt;a class=&quot;banner-2&quot; href=&quot;#&quot;&gt;&lt;img alt=&quot;banner&quot; src=&quot;http://demo.bossthemes.com/optronics/image/data/bt_optronics/banner_block_08.jpg&quot; title=&quot;banner&quot; /&gt;&lt;/a&gt;&lt;/div&gt;';
		}
		
		$boss_block = array('boss_staticblock_module' => array ( 
			0 => array ( 'description' => $array_description0, 'layout_id' => 0, 'store_id' => array(0=>0), 'position' => 'header_top', 'status' => 1, 'sort_order' => 2),
			1 => array ( 'description' => $array_description1, 'layout_id' => $this->getIdLayout("home"), 'store_id' => array(0=>0), 'position' => 'header_bottom', 'status' => 1, 'sort_order' => 2),
			2 => array ( 'description' => $array_description2, 'layout_id' => 0, 'store_id' => array(0=>0), 'position' => 'footer_bottom', 'status' => 1, 'sort_order' => 1),
			3 => array ( 'description' => $array_description3, 'layout_id' => 0, 'store_id' => array(0=>0), 'position' => 'footer_bottom', 'status' => 1, 'sort_order' => 2),	
			4 => array ( 'description' => $array_description4, 'layout_id' => 0, 'store_id' => array(0=>0), 'position' => 'footer_bottom', 'status' => 1, 'sort_order' => 4),	
			5 => array ( 'description' => $array_description5, 'layout_id' => 0, 'store_id' => array(0=>0), 'position' => 'footer_bottom', 'status' => 1, 'sort_order' => 5),
			6 => array ( 'description' => $array_description6, 'layout_id' => 0, 'store_id' => array(0=>0), 'position' => 'footer_bottom', 'status' => 1, 'sort_order' => 6),	
			7 => array ( 'description' => $array_description7, 'layout_id' => $this->getIdLayout("home"), 'store_id' => array(0=>0), 'position' => 'column_right', 'status' => 1, 'sort_order' => 1),		
			8 => array ( 'description' => $array_description8, 'layout_id' => $this->getIdLayout("home"), 'store_id' => array(0=>0), 'position' => 'content_bottom', 'status' => 1, 'sort_order' => 2),		
			9 => array ( 'description' => $array_description9, 'layout_id' => $this->getIdLayout("home"), 'store_id' => array(0=>0), 'position' => 'content_bottom', 'status' => 1, 'sort_order' => 4),		
			10 => array ( 'description' => $array_description10, 'layout_id' => $this->getIdLayout("home"), 'store_id' => array(0=>0), 'position' => 'content_bottom', 'status' => 1, 'sort_order' => 6),		
			11 => array ( 'description' => $array_description11, 'layout_id' => $this->getIdLayout("home"), 'store_id' => array(0=>0), 'position' => 'content_bottom', 'status' => 1, 'sort_order' => 8),		
		));

		$this->model_setting_setting->editSetting('boss_staticblock', $boss_block);		
	}
}
?>