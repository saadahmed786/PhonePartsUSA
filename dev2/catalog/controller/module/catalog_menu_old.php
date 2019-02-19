<?php
class ControllerModuleCatalogMenu extends Controller {
	public function index() {
		$this->language->load('module/catalog_menu');
		// Text
		$route = $this->request->get['route'];
		// echo $route;exit;
		$this->load->model('catalog/catalog');
		$this->data['home'] = $this->url->link('common/home');

		//$mainClasses = $this->model_catalog_catalog->loadMainClassification();
		$manFilter = array (
			'limit' => 6,
			'order_by' => 'name',
			'order_type' => 'ASC',
			'where' => array (
				array (
					'column' => 'status',
					'operator' => '=',
					'value' => 1
					)
				)
			);

		//$data = $this->model_catalog_catalog->loadManufacturers($manFilter);
		$_temp = array(2,10,7,4,6,11);
		$data = array();
		foreach($_temp as $temp)
		{
			$name_query = $this->db->query("SELECT name FROM inv_manufacturer where manufacturer_id='".$temp."'");
			$name = $name_query->row;
			$data[] = array('name'=>$name['name'],'id'=>$temp);
		}
		$manufacturers = array ();

		foreach ($data as $val) {

			$modFilter = array (
				'limit' => 50,
				'where' => array (
					array (
						'column' => 'manufacturer_id',
						'operator' => '=',
						'value' => $val['id']
						)
					)
				);

			$subRows = $this->model_catalog_catalog->loadModels($modFilter);
			$subMenu = array ();
			$subMenu[] = array (
				'href' => $this->url->link('catalog/repair_parts','path=' . $val['id'])  ,
				'name' => $this->language->get('text_all_models'),

				);
			foreach ($subRows as $row) {
				$subMenu[] = array (
					'href' => $this->url->link('catalog/repair_parts', 'path=' . $val['id'] . '_' . $row['id']) ,
					'name' => $row['name']
					);
			}

			

			$manufacturers[] = array (
				'href' => $this->url->link('catalog/repair_parts','path=' . $val['id']) ,
				'name' => $val['name'],
				'subMenu' => $subMenu,
				);
		}

		$manufacturers[] = array (
			'href' => $this->url->link('catalog/repair_parts'),
			'name' => 'Other',
			);

		$classFilter = array (
			'limit' => 50,
			'order_by' => 'main_class',
			'order_type' => 'DESC',
			'where' => array (
				array (
					'column' => 'inv_classification.status',
					'operator' => '=',
					'value' => '1'
					),
				array (
					'column' => 'main_class_id',
					'operator' => '=',
					'value' => 5
					)
				)
			);

		$data = $this->model_catalog_catalog->loadClassification($classFilter);

		$classification = array ();

		foreach ($data as $val) {
			$classification[] = array (
				'href' => $this->url->link('catalog/repair_tools','path=' . $val['id']) ,
				'name' => $val['name'],
				);
		}

		$classification[] = array (
			'href' => $this->url->link('catalog/repair_tools'),
			'name' => $this->language->get('text_all_tools'),
			);

		$menu = array (
			array (
				'name' => $this->language->get('text_repair_parts'),
				'href' => $this->url->link('catalog/repair_parts'),
				'subMenu' => $manufacturers,
				'active'=> ($route=='catalog/repair_parts'?'true':'false')
				),
			array (
				'name' => $this->language->get('text_repair_tools'),
				'href' => $this->url->link('catalog/repair_tools'),
				'active'=> ($route=='catalog/repair_tools'?'true':'false')
				//'subMenu' => $classification
				),
			array (
				'name' => $this->language->get('text_tempered_glass'),
				'href' => $this->url->link('catalog/temperedglass'),
				// 'href' => HTTPS_SERVER.'index.php?route=catalog/temperedglass&path=3',
				'active'=> ($route=='catalog/temperedglass'?'true':'false')
				),
			// array (
			// 	'name' => $this->language->get('text_refurebishing'),
			// 	'href' => $this->url->link('catalog/refurebishing')
			// 	),
			array (
				'name' => $this->language->get('text_accessories'),
				'href' => $this->url->link('catalog/accessories'),
				'active'=> ($route=='catalog/accessories'?'true':'false')
				)
			);
	// print_r($menu);exit;

		$this->data['menu'] = $menu;

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/catalog_menu.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/catalog_menu.tpl';
		} else {
			$this->template = 'default/template/module/catalog_menu.tpl';
		}

		$this->response->setOutput($this->render());
	}
}
?>