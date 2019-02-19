<?php
class ControllerModuleEbaydisplay extends Controller {
    protected function index($setting) {
        $this->language->load('module/ebaydisplay');
        $this->load->model('tool/image');
        $this->load->model('ebay/product');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['products'] = array();

        $products = $this->cache->get('ebaydisplay.'.md5(serialize($setting)));

        if(!$products){
            $products = $this->model_ebay_product->getDisplayProducts();
            $this->cache->set('ebaydisplay.'.md5(serialize($setting)), $products);
        }

        foreach ($products['products'] as $product) {

            if(isset($product['pictures'][0])){
                $image = $this->model_ebay_product->resize($product['pictures'][0], $setting['image_width'], $setting['image_height']);
            }else{
                $image = '';
            }

            $this->data['products'][] = array(
                'thumb'   	 => $image,
                'name'    	 => base64_decode($product['Title']),
                'price'   	 => $this->currency->format($product['priceGross']),
                'href'    	 => (string)$product['link'],
            );
        }

        $this->data['tracking_pixel'] = $products['tracking_pixel'];

        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/ebaydisplay.tpl')) {
            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/ebaydisplay.tpl';
        } else {
            $this->template = 'default/template/module/ebaydisplay.tpl';
        }

        $this->render();
    }
}