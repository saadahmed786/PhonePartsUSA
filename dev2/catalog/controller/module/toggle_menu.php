<?php
class ControllerModuleToggleMenu extends Controller {
	public function index() {
		$this->language->load('module/catalog_menu');
		// Text

		$this->load->model('catalog/catalog');
		$this->data['home'] = $this->url->link('common/home');

		$this->data['page_class'] = str_replace('/', '_', $this->request->get['route']);

		$manufacturers = array(
					array('href'=>$this->url->link('catalog/repair_parts','path=2'),'name'=>'iPhone','subMenu'=>
							array(
								array('name' => 'iPhone X','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_606')),
								array('name' => 'iPhone 8','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_603')),
								array('name' => 'iPhone 8 Plus','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_604')),
										
										array('name' => 'iPhone 7','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_510')),
										array('name' => 'iPhone 7 Plus','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_511')),
										
										array('name' => 'iPhone 6S','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_291')),
										array('name' => 'iPhone 6S Plus','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_292')),

										array('name' => 'iPhone 6','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_13')),
										array('name' => 'iPhone 6 Plus','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_14')),


										array('name' => 'iPhone SE','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_464')),
										array('name' => 'iPhone 5S','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_11')),
										array('name' => 'iPhone 5C','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_10')),
										array('name' => 'iPhone 5','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_9')),
										array('name' => 'iPhone 4S','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_4')),
										array('name' => 'iPhone 4','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_3')),
										
						
									),
						),

					array('href'=>$this->url->link('catalog/repair_parts','path=2'),'name'=>'iPad/iPod/iWatch','subMenu'=>
							array(
										
										array('name' => 'iPad Pro 9.7','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_472')),
										array('name' => 'iPad Pro 12.9','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_462')),
										array('name' => 'iPad Air 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_204')),
										array('name' => 'iPad Air','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_201')),
										array('name' => 'iPad Mini 4','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_295')),
										array('name' => 'iPad Mini 3','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_202')),
										array('name' => 'iPad Mini 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_199')),
										array('name' => 'iPad Mini 1','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_198')),
										array('name' => 'iPad 4','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_197')),
										array('name' => 'iPad 3','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_196')),
										array('name' => 'iPad 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_5')),
										
										array('name' => 'iPod','type'=>'span','href'=>$this->url->link('catalog/repair_parts','path=2')),
										// array('name' => 'All Models','type'=>'red_link','href'=>$this->url->link('catalog/repair_parts','path=2')),
										array('name' => 'iPod Touch 6','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_500')),
										array('name' => 'iPod Touch 5','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_209')),
										array('name' => 'iPod Touch 4','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_211')),
										array('name' => 'iPod Nano 7','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_217')),
										array('name' => 'iPod Nano 6','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_219')),

										array('name' => 'Apple Watch','type'=>'span','href'=>$this->url->link('catalog/repair_parts','path=2')),
										// array('name' => 'All Models','type'=>'red_link','href'=>$this->url->link('catalog/repair_parts','path=2')),
										array('name' => 'iWatch 42mm','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_294')),
										array('name' => 'iWatch 38mm','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_293')),
										array('name' => 'iWatch 2 42mm','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_608')),
										array('name' => 'iWatch 2 38mm','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_607')),
										array('name' => 'iWatch 3 42mm','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_610')),
										array('name' => 'iWatch 3 38mm','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=2_609')),
										
									),
						),

					array('href'=>$this->url->link('catalog/repair_parts','path=10'),'name'=>'Samsung Phones','subMenu'=>
							array(
										array('name' => 'Galaxy Note 8','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_605')),
										array('name' => 'Galaxy S8','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_586')),
										array('name' => 'Galaxy S8 Plus','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_587')),
										array('name' => 'Galaxy S7','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_458')),
										array('name' => 'Galaxy S7 Edge','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_460')),
										array('name' => 'Galaxy S7 Active','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_547')),
										array('name' => 'Galaxy S6','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_7')),
										array('name' => 'Galaxy S6 Edge','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_8')),
										array('name' => 'Galaxy S6 Edge Plus','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_324')),
										array('name' => 'Galaxy S6 Active','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_388')),
										array('name' => 'Galaxy S5','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_74')),
										array('name' => 'Galaxy S5 Active','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_80')),
										array('name' => 'Galaxy S4','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_106')),
										array('name' => 'Galaxy S4 Active','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_105')),
										array('name' => 'Galaxy S3','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_73')),
										array('name' => 'Galaxy Note 5','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_323')),
										array('name' => 'Galaxy Note 4','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_110')),
										array('name' => 'Galaxy Note 3','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_109')),
										array('name' => 'Galaxy Note 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_108')),
										array('name' => 'Galaxy Mega 6.3','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_112')),
										array('name' => 'Galaxy J3 (2016)','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_488')),
										array('name' => 'Galaxy J7 (2016)','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=10_555')),
										
										
										
										
									),
						),

					array('href'=>$this->url->link('catalog/repair_parts','path=6'),'name'=>'LG','subMenu'=>
							array(
										
										array('name' => 'G6','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_582')),
										array('name' => 'G5','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_461')),
										array('name' => 'G4','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_82')),
										array('name' => 'G3','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_81')),
										array('name' => 'G2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_22')),
										array('name' => 'V20','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_524')),
										array('name' => 'V10','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_465')),
										array('name' => 'Nexus 5X','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_457')),
										array('name' => 'Nexus 5','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_83')),
										array('name' => 'K3','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_536')),
										array('name' => 'K7','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_486')),
										array('name' => 'K8','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_533')),
										array('name' => 'K10','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_501')),
										array('name' => 'G Vista','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_408')),
										array('name' => 'G Vista 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_504')),
										array('name' => 'G Stylo','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_473')),
										array('name' => 'G Stylo 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_503')),
										array('name' => 'G Flex','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_77')),
										array('name' => 'G Flex 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=6_403')),
										
										
										
									),
						),

					array('href'=>$this->url->link('catalog/repair_parts','path=7'),'name'=>'Motorola','subMenu'=>
							array(
										
									
										array('name' => 'Moto X Pure','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_451')),
										array('name' => 'Moto X Play','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_450')),
										array('name' => 'Moto X Force','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_549')),
										array('name' => 'Nexus 6','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_135')),
										array('name' => 'Droid Maxx','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_122')),
										array('name' => 'Droid Maxx 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_485')),
										array('name' => 'Droid Turbo','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_126')),
										array('name' => 'Droid Turbo 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_484')),
										array('name' => 'Droid Razr','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_138')),
										array('name' => 'Droid Razr HD','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_124')),
										array('name' => 'Moto Z Play Droid','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_529')),
										array('name' => 'Moto Z Force Droid','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_513')),
										array('name' => 'Moto Z Droid','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_512')),
										array('name' => 'Moto G4 Plus','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_542')),
										array('name' => 'Moto G4 Play','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_541')),
										array('name' => 'Moto G4','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_502')),
										
										
										
									),
						),

					array('href'=>$this->url->link('catalog/repair_parts','path=7'),'name'=>'HTC','subMenu'=>
							array(
										
									
										array('name' => 'Moto X Pure','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_451')),
										array('name' => 'Moto X Play','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_450')),
										array('name' => 'Moto X Force','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_549')),
										array('name' => 'Nexus 6','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_135')),
										array('name' => 'Droid Maxx','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_122')),
										array('name' => 'Droid Maxx 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_485')),
										array('name' => 'Droid Turbo','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_126')),
										array('name' => 'Droid Turbo 2','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_484')),
										array('name' => 'Droid Razr','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_138')),
										array('name' => 'Droid Razr HD','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_124')),
										array('name' => 'Moto Z Play Droid','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_529')),
										array('name' => 'Moto Z Force Droid','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_513')),
										array('name' => 'Moto Z Droid','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_512')),
										array('name' => 'Moto G4 Plus','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_542')),
										array('name' => 'Moto G4 Play','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_541')),
										array('name' => 'Moto G4','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=7_502')),
										
										
										
									),
						),

					array('href'=>'#','name'=>'Other','subMenu'=>
							array(
										array('name' => 'All Makes','type'=>'red_link','href'=>$this->url->link('catalog/repair_parts')),
										array('name' => 'Acer','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=18')),
										array('name' => 'Alcatel','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=31')),
										array('name' => 'Amazon','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=26')),
										array('name' => 'ASUS','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=17')),
										array('name' => 'BlackBerry','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=3')),
										array('name' => 'ClickN Kids','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=32')),
										// array('name' => 'Desire 630','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=4_465')),
										array('name' => 'Dell','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=27')),
										// array('name' => 'Desire 830','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=4_514')),
										array('name' => 'Google','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=35')),
										array('name' => 'Hisense','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=16')),
										array('name' => 'HP','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=20')),
										array('name' => 'HTC','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=4')),
										array('name' => 'Huawei','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=5')),
										array('name' => 'iView','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=19')),
										array('name' => 'Kurio','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=24')),
										array('name' => 'Lenovo','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=29')),
										array('name' => 'Meizu','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=30')),
										array('name' => 'Microsoft','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=25')),
										array('name' => 'Nextbook','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=15')),
										array('name' => 'Nokia','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=8')),
										array('name' => 'OnePlus','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=14')),
										array('name' => 'Pantech','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=9')),
										array('name' => 'RCA','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=21')),
										array('name' => 'Sony','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=11')),
										array('name' => 'STK','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=34')),
										array('name' => 'Visual Land','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=23')),
										array('name' => 'Xiaomi','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=33')),
										array('name' => 'ZTE','type'=>'link','href'=>$this->url->link('catalog/repair_parts','path=13')),
										
										
										
									),
						),
			);


		$menu = array (
			array (
				'name' => $this->language->get('text_repair_parts'),
				'href' => $this->url->link('catalog/repair_parts'),
				'subMenu' => $manufacturers
				),
			array (
				'name' => $this->language->get('text_repair_tools'),
				'href' => $this->url->link('catalog/repair_tools'),
				//'subMenu' => $classification
				),
				array (
			 	'name' => $this->language->get('text_refurbishing'),
			 	'href' => $this->url->link('catalog/refurbishing')
			 	),
			array (
				'name' => $this->language->get('text_tempered_glass'),
				'href' => $this->url->link('catalog/temperedglass'),
				),
			// array (
			// 	'name' => $this->language->get('text_refurebishing'),
			// 	'href' => $this->url->link('catalog/refurebishing')
			// 	),
			array (
				'name' => $this->language->get('text_accessories'),
				'href' => $this->url->link('catalog/accessories')
				),
			array (
				'name' => 'Blowout',
				'href' => $this->url->link('catalog/blowout')
				)
			);


		$this->data['menu'] = $menu;

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/toggle_menu.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/toggle_menu.tpl';
		} else {
			$this->template = 'default/template/module/toggle_menu.tpl';
		}

		$this->response->setOutput($this->render());
	}
}
?>