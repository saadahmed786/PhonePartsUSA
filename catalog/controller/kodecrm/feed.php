<?php

class ControllerKodecrmFeed extends Controller {

    public function index() {

        if ($this->config->get('kodecrm_feed_status')) {

            $arr = array();

            $arr['title'] = $this->config->get('config_title');
            $arr['link'] = $this->url->link('common/home');
            $arr['item'] = array();

            $this->load->model('catalog/category');

            $this->load->model('catalog/product');

            $this->load->model('tool/image');

            $products = $this->model_catalog_product->getProducts();

            foreach($products as $product){

                if ($product['image']) {
                    $image = $this->model_tool_image->resize($product['image'], 500, 500);
                } else {
                    $image = $this->model_tool_image->resize('no_image.jpg', 500, 500);
                }

                if ((float)$product['special']) {
                    $price = ($this->tax->calculate($product['special'], $product['tax_class_id']));
                } else {
                    $price = ($this->tax->calculate($product['price'], $product['tax_class_id']));
                }

                $categories = $this->model_catalog_product->getCategories($product['product_id']);
                $product_categories = array();

                foreach ($categories as $category) {
                    $path = $this->getPath($category['category_id']);

                    if ($path) {
                        foreach (explode('_', $path) as $path_id) {
                            $category_info = $this->model_catalog_category->getCategory($path_id);

                            if ($category_info) {
                                $product_categories[] = $category_info['name'];
                            }
                        }
                    }
                }

                $arr['item'][] = array(
                    'title' => html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8'),
                    'brand' => html_entity_decode($product['manufacturer'], ENT_QUOTES, 'UTF-8'),
                    'description' => html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'),
                    'pid' => $product['product_id'],
                    'link' =>  html_entity_decode($this->url->link('product/product', 'product_id=' . $product['product_id']), ENT_QUOTES, 'UTF-8'),
                    'image_link' => $image,
                    'price' => $price,
                    'currency' => $this->config->get('config_currency'),
                    'availability' => ($product['quantity'] ? '1' : '0'),
                    'category' => $product_categories
                );
            }

            require_once (DIR_SYSTEM . 'library/kodecrm/feed.php');

            $feed = kodecrm_feed_create($arr);

            header('Content-Type: application/rss+xml; charset=utf-8');

            echo $feed;

        }

    }

    protected function getPath($parent_id, $current_path = '') {
        $category_info = $this->model_catalog_category->getCategory($parent_id);

        if ($category_info) {
            if (!$current_path) {
                $new_path = $category_info['category_id'];
            } else {
                $new_path = $category_info['category_id'] . '_' . $current_path;
            }

            $path = $this->getPath($category_info['parent_id'], $new_path);

            if ($path) {
                return $path;
            } else {
                return $new_path;
            }
        }
    }
}
