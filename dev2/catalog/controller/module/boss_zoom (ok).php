<?php
class ControllerModuleBossZoom extends Controller {
	protected function index($setting) {
		if($setting['status']){
			
			if (file_exists('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/cloud-zoom.1.0.3.css')) {
				$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/cloud-zoom.1.0.3.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/cloud-zoom.1.0.3.css');
			}
			
			$this->data['status'] = $setting['status']; 
			$this->data['thumb_image_width'] = $setting['thumb_image_width']; 
			$this->data['thumb_image_heigth'] = $setting['thumb_image_heigth']; 
			$this->data['zoom_image_width'] = $setting['zoom_image_width']; 
			$this->data['zoom_image_heigth'] = $setting['zoom_image_heigth']; 
			$this->data['zoom_area_width'] = $setting['zoom_area_width']; 
			$this->data['zoom_area_heigth'] = $setting['zoom_area_heigth']; 
			$this->data['addition_image_width'] = $setting['addition_image_width']; 
			$this->data['addition_image_heigth'] = $setting['addition_image_heigth']; 
			$this->data['position_zoom_area'] = $setting['position_zoom_area']; 
			$this->data['adjustX'] = $setting['adjustX']; 
			$this->data['adjustY'] = $setting['adjustY']; 
			$this->data['title_image'] = $setting['title_image']; 
			$this->data['title_opacity'] = $setting['title_opacity']; 
			$this->data['tint'] = $setting['tint']; 
			$this->data['tintOpacity'] = $setting['tintOpacity']; 
			$this->data['softfocus'] = $setting['softfocus']; 
			$this->data['lensOpacity'] = $setting['lensOpacity']; 
			$this->data['smoothMove'] = $setting['smoothMove']; 
			
			if (isset($this->request->get['product_id'])) {
				$product_id = (int)$this->request->get['product_id'];
			} else {
				$product_id = 0;
			}
			
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($product_id);
			$this->data['heading_title'] = $product_info['name'];
			
			$this->load->model('tool/image');
			$this->data['images'] = array();
			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
			//Thumb Image
			if ($product_info['image']) {
				$this->data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->data['zoom_image_width'] ,$this->data['zoom_image_heigth'] );
				$this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->data['thumb_image_width'], $this->data['thumb_image_heigth']);
			} else {
				if($results){
					$this->data['popup'] = $this->model_tool_image->resize($results[0]['image'], $this->data['zoom_image_width'] ,$this->data['zoom_image_heigth'] );
					$this->data['thumb'] = $this->model_tool_image->resize($results[0]['image'], $this->data['thumb_image_width'], $this->data['thumb_image_heigth']);
					unset($results[0]);
				}else{
					$this->data['popup'] = '';
					$this->data['thumb'] = '';
				}
			}
			//addition image
			if ($product_info['image']) {
				$this->data['images'][] =  array(
					'popup' =>$this->model_tool_image->resize($product_info['image'], $this->data['zoom_image_width'] ,$this->data['zoom_image_heigth'] ),
					'addition' => $this->model_tool_image->resize($product_info['image'], $this->data['addition_image_width'], $this->data['addition_image_heigth']),
					'thumb'   => $this->model_tool_image->resize($product_info['image'], $this->data['thumb_image_width'], $this->data['thumb_image_heigth']),
				);
			}
			foreach ($results as $result) {
				$this->data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->data['zoom_image_width'], $this->data['zoom_image_heigth']),
					'addition' => $this->model_tool_image->resize($result['image'], $this->data['addition_image_width'], $this->data['addition_image_heigth']),
					'thumb'   => $this->model_tool_image->resize($result['image'], $this->data['thumb_image_width'], $this->data['thumb_image_heigth']),
				);
			}
		}
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_zoom.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_zoom.tpl';
		} else {
			$this->template = 'default/template/module/boss_zoom.tpl';
		}

		$this->render();
	}
}
?>