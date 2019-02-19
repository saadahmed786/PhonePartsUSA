<?php

class ControllerModuleSimilarProducts extends Controller {

    private $products = 0;



    protected function index($settings) {

        $this->language->load('module/similar_products');



        $this->data['heading_title'] = isset($settings['names'][$this->config->get('config_language_id')]) ? $settings['names'][$this->config->get('config_language_id')] : $this->language->get('heading_title');

        $this->data['error_ajax_request'] = $this->language->get('error_ajax_request');



        $this->data['products'] = '';

        $this->data['show_similar'] = 0;

        $this->data['product_id'] = 0;

        $this->data['position'] = $settings['position'];



        $results = array();



        if (isset($this->request->get['product_id'])) {

            $this->document->addScript('catalog/view/javascript/sp/custom.min.js');



            $this->load->model('module/similar_products');



            $this->data['product_id'] = $this->request->get['product_id'];

            $this->data['mid'] = $settings['index'];

            $this->data['path'] = isset($this->request->get['path']) ? "&_path=" . $this->request->get['path'] : '';



            $product_info = $this->model_module_similar_products->getPlainProduct($this->data['product_id']);



            $categories = isset($this->request->get['path']) ? explode('_', $this->request->get['path']) : array();



            if ((int)$product_info['sp_auto_select'] == 1 && !$categories) {

                $this->load->model('catalog/product');

                $p_categories = $this->model_catalog_product->getCategories($this->data['product_id']);



                if ((int)$product_info['sp_leaves_only']) {

                    $this->load->model('catalog/category');

                    $cc = array();

                    foreach($p_categories as $c) {

                        $cc[$c['category_id']] = $this->model_catalog_category->getCategoriesByParentId($c['category_id']);

                    }

                    foreach($cc as $k => $c) {

                        if (!count(array_intersect(array_keys($cc), $c))) {

                            $categories[] = $k;

                        }

                    }

                } else {

                    foreach($p_categories as $c) {

                        $categories[] = $c['category_id'];

                    }

                }

                $categories = implode(",", $categories);

            } else {

                $categories = end($categories);

            }



            $data = array(

                "auto_select"     => $product_info['sp_auto_select'],

                "sort_order"      => $product_info['sp_product_sort_order'],

                "substr_start"    => $product_info['sp_substr_start'],

                "substr_length"   => $product_info['sp_substr_length'],

                "custom_string"   => $product_info['sp_custom_string'],

                "categories"      => $categories,

                "stock_only"      => $settings['stock_only'],

            );



            list($usec, $sec) = explode(' ', microtime());

            $this->session->data['sp_seed'] = (float) $sec + ((float) $usec * 100000);



            $this->data['lazy_load'] = $settings['lazy_load'];



            if (!$settings['lazy_load']) {

                $this->data['products'] = $this->get($this->data['product_id'], $product_info, $settings, $categories);

                $this->data['show_similar'] = $this->products;

            } else {

                $this->data['show_similar'] = $this->model_module_similar_products->similarProductsExist($this->data['product_id'], $data);

            }

        }



        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/similar_products.tpl')) {

            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/similar_products.tpl';

        } else {

            $this->template = 'default/template/module/similar_products.tpl';

        }



        $this->render();

    }



    public function get($product = null, $product_info = null, $settings = null, $categories = null) {

        $this->language->load('module/similar_products');



        $this->load->model('catalog/product');

        $this->load->model('module/similar_products');



        $this->load->model('tool/image');



        $this->data['button_cart'] = $this->language->get('button_cart');



        $this->data['products'] = array();



        $results = array();

        $products_total = 0;

        $product_id = 0;



        if (!is_null($product)) {

            $product_id = $product;

        } else if (isset($this->request->get['pid'])) {

            $product_id = $this->request->get['pid'];

        }



        if (is_null($settings) && isset($this->request->get['mid'])) {

            $module_id = $this->request->get['mid'];

            $modules = $this->config->get("similar_products_module");

            if (isset($modules[$module_id])) {

                $settings = $modules[$module_id];

            }

        }



        if ($product_id && $settings) {

            if (is_null($product_info)) {

                $product_info = $this->model_module_similar_products->getPlainProduct($product_id);

            }



            switch($product_info['sp_product_sort_order']) {

                case '7':

                    $s_sort = 'random';

                    $s_order = 'ASC';

                    break;

                case '6':

                    $s_sort = 'p.date_modified';

                    $s_order = 'DESC';

                    break;

                case '5':

                    $s_sort = 'p.date_added';

                    $s_order = 'DESC';

                    break;

                case '4':

                    $s_sort = 'p.viewed';

                    $s_order = 'DESC';

                    break;

                case '3':

                    $s_sort = 'p.quantity';

                    $s_order = 'DESC';

                    break;

                case '2':

                    $s_sort = 'pd.name';

                    $s_order = 'ASC';

                    break;

                case '1':

                    $s_sort = 'p.model';

                    $s_order = 'ASC';

                    break;

                case '0':

                default:

                    $s_sort = 'p.sort_order';

                    $s_order = 'ASC';

                    break;

            }



            if (is_null($categories)) {

                $categories = isset($this->request->get['path']) ? explode('_', $this->request->get['path']) : array();



                if ((int)$product_info['sp_auto_select'] == 1 && !$categories) {

                    $this->load->model('catalog/product');

                    $p_categories = $this->model_catalog_product->getCategories($product_id);



                    if ((int)$product_info['sp_leaves_only']) {

                        $this->load->model('catalog/category');

                        $cc = array();

                        foreach($p_categories as $c) {

                            $cc[$c['category_id']] = $this->model_catalog_category->getCategoriesByParentId($c['category_id']);

                        }

                        foreach($cc as $k => $c) {

                            if (!count(array_intersect(array_keys($cc), $c))) {

                                $categories[] = $k;

                            }

                        }

                    } else {

                        foreach($p_categories as $c) {

                            $categories[] = $c['category_id'];

                        }

                    }

                    $categories = implode(",", $categories);

                } else {

                    $categories = end($categories);

                }

            }



            if (isset($this->request->get['page'])) {

                $page = $this->request->get['page'];

            } else {

                $page = 1;

            }



            $data = array(

                "limit"         => $settings['limit'],

                "sort"          => $s_sort,

                "order"         => $s_order,

                "auto_select"   => $product_info['sp_auto_select'],

                "sort_order"    => $product_info['sp_product_sort_order'],

                "substr_start"  => $product_info['sp_substr_start'],

                "substr_length" => $product_info['sp_substr_length'],

                "custom_string" => $product_info['sp_custom_string'],

                "categories"    => $categories,

                "stock_only"    => $settings['stock_only'],

                "start"         => ($page - 1) * (int)$settings['products_per_page'],

                "per_page"      => (int)$settings['products_per_page'],

                "seed"          => isset($this->session->data['sp_seed']) ? $this->session->data['sp_seed'] : (int)date("Ymd")

            );



            $results = $this->model_module_similar_products->getSimilarProducts($product_id, $data);



            $products_total = $this->model_module_similar_products->getProductCount();

        }



        foreach ($results as $result) {

            if ($result['image']) {

                $image = $this->model_tool_image->resize($result['image'], $settings['image_width'], $settings['image_height']);

            } else {

                $image = false;

            }



            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {

                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));

            } else {

                $price = false;

            }



            if ((float)$result['special']) {

                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));

            } else {

                $special = false;

            }



            if ($this->config->get('config_review_status')) {

                $rating = $result['rating'];

            } else {

                $rating = false;

            }



            $this->data['products'][] = array(

                'product_id' => $result['product_id'],

                'thumb'      => $image,

                'name'       => $result['name'],

                'price'      => $price,

                'special'    => $special,

                'rating'     => $rating,

                'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),

                'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id']),

            );

        }



        $url = '&page={page}';



        if (isset($this->request->get['path'])) {

            $url .= "&_path=" . $this->request->get['path'];

        } else if (isset($this->request->get['_path'])) {

            $url .= "&_path=" . $this->request->get['_path'];

        }



        $pagination = new Pagination();

        $pagination->total = $products_total;

        $pagination->page = $page;

        $pagination->limit = ($settings['products_per_page']) ? $settings['products_per_page'] : $products_total;

        $pagination->text = $this->language->get('text_pagination');

        $pagination->url = $this->url->link('module/similar_products/get', 'pid=' . $product_id . '&mid=' . $settings['index'] . $url);



        $this->data['pagination'] = $pagination->render();

        $this->data['show_pagination'] = count($this->data['products']) != $products_total;



        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/similar_products_products.tpl')) {

            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/similar_products_products.tpl';

        } else {

            $this->template = 'default/template/module/similar_products_products.tpl';

        }



        if (!is_null($product)) {

            $this->products = $products_total;

            return $this->render();

        } else {

            $this->response->setOutput($this->render());

        }

    }

}

?>

