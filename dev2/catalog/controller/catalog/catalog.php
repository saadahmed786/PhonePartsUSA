<?php

class ControllerCatalogCatalog extends Controller {

    public function index() {
        $this->document->setTitle('Product Catalog');
        $this->load->model('catalog/catalog');

        $this->data['instructions'] = $this->model_catalog_catalog->getSettings('instructions');
        $this->data['youtube_link'] = $this->model_catalog_catalog->getSettings('youtube_link');

        if ($this->customer->isLogged()) {
            $this->data['isLogged'] = true;
            $this->data['userEmail'] = $this->customer->getEmail();
            $this->data['userFirstName'] = $this->customer->getFirstName();
            $this->data['userLastName'] = $this->customer->getLastName();
        } else {
            $this->data['isLogged'] = false;
        }

        header("Location: https://phonepartsusa.com/old/index.php?route=catalog/catalog");

        $filter = unserialize(base64_decode($this->model_catalog_catalog->getShare($this->request->get['share'])));
        // print_r($filter);
        // echo json_encode($filter);
        // die();
        $this->data['filter'] = $filter;
        $this->data['group_id'] = $this->customer->getCustomerGroupId();


        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => 'Home',
            'href' => $this->url->link('common/home'),
            'separator' => false
        );



        $this->data['breadcrumbs'][] = array(
            'text' => 'Product Catalog',
            'href' => $this->url->link('catalog/catalog', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = 'Product Catalog';



        $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/catalog.tpl';


        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
}
?>
