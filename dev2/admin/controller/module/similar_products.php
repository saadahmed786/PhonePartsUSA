<?php
define('EXTENSION_NAME',            'Similar Products');
define('EXTENSION_VERSION',         '4.0.1');
define('EXTENSION_ID',              '3449');
define('EXTENSION_COMPATIBILITY',   'OpenCart 1.5.1.3, 1.5.2.x, 1.5.3.x, 1.5.4.x, 1.5.5.x and 1.5.6.x');
define('EXTENSION_STORE_URL',       'http://www.opencart.com/index.php?route=extension/extension/info&extension_id=' . EXTENSION_ID);
define('EXTENSION_SUPPORT_EMAIL',   'support@opencart.ee');
define('EXTENSION_SUPPORT_FORUM',   'http://forum.opencart.com/viewtopic.php?f=123&t=42624');
define('OTHER_EXTENSIONS',          'http://www.opencart.com/index.php?route=extension/extension&filter_username=bull5-i');

class ControllerModuleSimilarProducts extends Controller {
    private $error = array();
    protected $alert = array(
        'error'     => array(),
        'warning'   => array(),
        'success'   => array(),
        'info'      => array()
    );

    private $defaults = array(
        'sp_installed'              => 1,
        'sp_installed_version'      => EXTENSION_VERSION,
        'sp_status'                 => 0,
        'sp_auto_select'            => 0,
        'sp_product_sort_order'     => 0,
        'sp_leaves_only'            => 1,
        'sp_substr_start'           => 0,
        'sp_substr_length'          => 5,
        'sp_custom_string'          => "",
        'sp_remove_sql_changes'     => 0,
        'sp_apply_to'               => array(),
        'similar_products_module'   => array(),
    );

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->helper('sp');

        $this->language->load('module/similar_products');
        $this->load->model('module/similar_products');
    }

    public function index() {
        $this->document->addStyle('view/stylesheet/sp/css/custom.min.css');

        $this->document->addScript('view/javascript/sp/custom.min.js');
        $this->document->addScript('view/javascript/jquery/superfish/js/superfish.js');

        $this->document->setTitle($this->language->get('extension_name'));

        $this->load->model('setting/setting');

        $ajax_request = isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && !$ajax_request && $this->validateForm($this->request->post)) {
            $original_settings = $this->model_setting_setting->getSetting('similar_products');

            if (isset($this->request->post['sp_apply_to']['products']) && $this->request->post['sp_apply_to']['products'] != "") {
                $this->model_module_similar_products->applyMassChange($this->request->post);
            }

            $settings = array_merge($original_settings, $this->request->post);
            $settings['sp_installed_version'] = $original_settings['sp_installed_version'];
            $settings['sp_apply_to'] = array();

            $this->model_setting_setting->editSetting('similar_products', $settings);

            $this->session->data['success'] = $this->language->get('text_success_update');

            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        } else if ($this->request->server['REQUEST_METHOD'] == 'POST' && $ajax_request) {
            $response = array();

            if ($this->validateForm($this->request->post)) {
                $original_settings = $this->model_setting_setting->getSetting('similar_products');

                if (isset($this->request->post['sp_apply_to']['products']) && $this->request->post['sp_apply_to']['products'] != "") {
                    $this->model_module_similar_products->applyMassChange($this->request->post);
                }

                $settings = array_merge($original_settings, $this->request->post);
                $settings['sp_installed_version'] = $original_settings['sp_installed_version'];
                $settings['sp_apply_to'] = array();

                $this->model_setting_setting->editSetting('similar_products', $settings);

                $response['success'] = true;
                $response['msg'] = $this->language->get("text_success_update");
            } else {
                $response = array_merge(array("error" => true), array("errors" => $this->error), array("alerts" => $this->alert));
            }

            $this->response->setOutput(json_enc($response, JSON_UNESCAPED_SLASHES));
            return;
        }

        $db_structure_ok = $this->checkVersion() && $this->model_module_similar_products->checkDatabaseStructure($this->alert);

        $this->checkPrerequisites();

        $this->checkVersion();

        $this->data['heading_title'] = $this->language->get('extension_name');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_toggle_navigation'] = $this->language->get('text_toggle_navigation');
        $this->data['text_legal_notice'] = $this->language->get('text_legal_notice');
        $this->data['text_license'] = $this->language->get('text_license');
        $this->data['text_extension_information'] = $this->language->get('text_extension_information');
        $this->data['text_terms'] = $this->language->get('text_terms');
        $this->data['text_license_text'] = $this->language->get('text_license_text');
        $this->data['text_other_extensions'] = sprintf($this->language->get('text_other_extensions'), OTHER_EXTENSIONS);
        $this->data['text_support_subject'] = $this->language->get('text_support_subject');
        $this->data['text_faq'] = $this->language->get('text_faq');
        $this->data['text_content_tab'] = $this->language->get('text_content_tab');
        $this->data['text_content_top'] = $this->language->get('text_content_top');
        $this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
        $this->data['text_column_left'] = $this->language->get('text_column_left');
        $this->data['text_column_right'] = $this->language->get('text_column_right');
        $this->data['text_random'] = $this->language->get('text_random');
        $this->data['text_most_viewed'] = $this->language->get('text_most_viewed');
        $this->data['text_date_added'] = $this->language->get('text_date_added');
        $this->data['text_date_modified'] = $this->language->get('text_date_modified');
        $this->data['text_name'] = $this->language->get('text_name');
        $this->data['text_sort_order'] = $this->language->get('text_sort_order');
        $this->data['text_model'] = $this->language->get('text_model');
        $this->data['text_quantity'] = $this->language->get('text_quantity');
        $this->data['text_no_modules'] = $this->language->get('text_no_modules');
        $this->data['text_off'] = $this->language->get('text_off');
        $this->data['text_tags'] = $this->language->get('text_tags');
        $this->data['text_category'] = $this->language->get('text_category');
        $this->data['text_name_fragment'] = $this->language->get('text_name_fragment');
        $this->data['text_model_fragment'] = $this->language->get('text_model_fragment');
        $this->data['text_name_custom_string'] = $this->language->get('text_name_custom_string');
        $this->data['text_model_custom_string'] = $this->language->get('text_model_custom_string');
        $this->data['text_change_product_settings'] = $this->language->get('text_change_product_settings');
        $this->data['text_no_products'] = $this->language->get('text_no_products');
        $this->data['text_all_products'] = $this->language->get('text_all_products');
        $this->data['text_all_empty_products'] = $this->language->get('text_all_empty_products');
        $this->data['text_all_category_products'] = $this->language->get('text_all_category_products');
        $this->data['text_selected_products'] = $this->language->get('text_selected_products');
        $this->data['text_remove'] = $this->language->get('text_remove');
        $this->data['text_autocomplete'] = $this->language->get('text_autocomplete');

        $this->data['tab_settings'] = $this->language->get('tab_settings');
        $this->data['tab_modules'] = $this->language->get('tab_modules');
        $this->data['tab_support'] = $this->language->get('tab_support');
        $this->data['tab_about'] = $this->language->get('tab_about');
        $this->data['tab_general'] = $this->language->get('tab_general');
        $this->data['tab_faq'] = $this->language->get('tab_faq');
        $this->data['tab_services'] = $this->language->get('tab_services');
        $this->data['tab_changelog'] = $this->language->get('tab_changelog');
        $this->data['tab_extension'] = $this->language->get('tab_extension');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_apply'] = $this->language->get('button_apply');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_close'] = $this->language->get('button_close');
        $this->data['button_upgrade'] = $this->language->get('button_upgrade');
        $this->data['button_add_module'] = $this->language->get('button_add_module');
        $this->data['button_remove'] = $this->language->get('button_remove');

        $this->data['help_remove_sql_changes'] = $this->language->get('help_remove_sql_changes');
        $this->data['help_auto_select'] = $this->language->get('help_auto_select');
        $this->data['help_name_fragment'] = $this->language->get('help_name_fragment');
        $this->data['help_custom_string'] = $this->language->get('help_custom_string');
        $this->data['help_leaves_only'] = $this->language->get('help_leaves_only');
        $this->data['help_stock_only'] = $this->language->get('help_stock_only');
        $this->data['help_lazy_load'] = $this->language->get('help_lazy_load');
        $this->data['help_change_product_settings'] = $this->language->get('help_change_product_settings');

        $this->data['entry_installed_version'] = $this->language->get('entry_installed_version');
        $this->data['entry_extension_status'] = $this->language->get('entry_extension_status');
        $this->data['entry_name'] = $this->language->get('entry_name');
        $this->data['entry_layout'] = $this->language->get('entry_layout');
        $this->data['entry_limit'] = $this->language->get('entry_limit');
        $this->data['entry_image_width'] = $this->language->get('entry_image_width');
        $this->data['entry_image_height'] = $this->language->get('entry_image_height');
        $this->data['entry_position'] = $this->language->get('entry_position');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_module_sort_order'] = $this->language->get('entry_module_sort_order');
        $this->data['entry_product_sort_order'] = $this->language->get('entry_product_sort_order');
        $this->data['entry_products_per_page'] = $this->language->get('entry_products_per_page');
        $this->data['entry_stock_only'] = $this->language->get('entry_stock_only');
        $this->data['entry_lazy_load'] = $this->language->get('entry_lazy_load');
        $this->data['entry_auto_select'] = $this->language->get('entry_auto_select');
        $this->data['entry_leaves_only'] = $this->language->get('entry_leaves_only');
        $this->data['entry_substr_start'] = $this->language->get('entry_substr_start');
        $this->data['entry_substr_length'] = $this->language->get('entry_substr_length');
        $this->data['entry_custom_string'] = $this->language->get('entry_custom_string');
        $this->data['entry_remove_sql_changes'] = $this->language->get('entry_remove_sql_changes');
        $this->data['entry_products'] = $this->language->get('entry_products');
        $this->data['entry_extension_name'] = $this->language->get('entry_extension_name');
        $this->data['entry_extension_compatibility'] = $this->language->get('entry_extension_compatibility');
        $this->data['entry_extension_store_url'] = $this->language->get('entry_extension_store_url');
        $this->data['entry_copyright_notice'] = $this->language->get('entry_copyright_notice');

        $this->data['error_module_name'] = $this->language->get('error_module_name');
        $this->data['error_positive_integer'] = $this->language->get('error_positive_integer');
        $this->data['error_ajax_request'] = $this->language->get('error_ajax_request');

        $this->data['ext_name'] = EXTENSION_NAME;
        $this->data['ext_version'] = EXTENSION_VERSION;
        $this->data['ext_id'] = EXTENSION_ID;
        $this->data['ext_compatibility'] = EXTENSION_COMPATIBILITY;
        $this->data['ext_store_url'] = EXTENSION_STORE_URL;
        $this->data['ext_support_email'] = EXTENSION_SUPPORT_EMAIL;
        $this->data['ext_support_forum'] = EXTENSION_SUPPORT_FORUM;
        $this->data['other_extensions_url'] = OTHER_EXTENSIONS;

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'active'    => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'active'    => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('extension_name'),
            'href'      => $this->url->link('module/similar_products', 'token=' . $this->session->data['token'], 'SSL'),
            'active'    => true
        );

        $this->data['save'] = $this->url->link('module/similar_products', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['upgrade'] = $this->url->link('module/similar_products/upgrade', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['autocomplete'] = html_entity_decode($this->url->link('module/similar_products/autocomplete', 'type=%TYPE%&query=%QUERY&token=' . $this->session->data['token'], 'SSL'));

        $this->data['update_pending'] = !$this->checkVersion();

        $this->data['ssl'] = (int)$this->config->get('config_use_ssl') ? 's' : '';

        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        $this->data['languages'] = arrayRemapKeysToIds('language_id', $languages);

        $layouts = $this->model_module_similar_products->getProductLayouts();
        $this->data['layouts'] = arrayRemapKeysToIds('layout_id', $layouts);

        $this->data['default_image_width'] = $this->config->get('config_image_related_width');
        $this->data['default_image_height'] = $this->config->get('config_image_related_height');

        if (!count($layouts)) {
            $this->alert['warning']['layout'] = $this->language->get('error_layout');
        }

        $this->load->model('catalog/category');
        $categories = $this->model_catalog_category->getCategories(0);
        $this->data['categories'] = arrayRemapKeysToIds('category_id', $categories);

        $this->data['installed_version'] = $this->installedVersion();

        # Loop through all settings for the post/config values
        foreach (array_keys($this->defaults) as $setting) {
            if (isset($this->request->post[$setting])) {
                $this->data[$setting] = $this->request->post[$setting];
            } else {
                $this->data[$setting] = $this->config->get($setting);
                if ($this->data[$setting] === null) {
                    if (!isset($this->alert['warning']['unsaved']) && $this->checkVersion())  {
                        $this->alert['warning']['unsaved'] = $this->language->get('error_unsaved_settings');
                    }
                    if (isset($this->defaults[$setting])) {
                        $this->data[$setting] = $this->defaults[$setting];
                    }
                }
            }
        }

        if (isset($this->session->data['error'])) {
            $this->error = $this->session->data['error'];

            unset($this->session->data['error']);
        }

        if (isset($this->error['warning'])) {
            $this->alert['warning']['warning'] = $this->error['warning'];
        }

        if (isset($this->error['error'])) {
            $this->alert['error']['error'] = $this->error['error'];
        }

        if (isset($this->session->data['success'])) {
            $this->alert['success']['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        }

        $this->data['errors'] = $this->error;

        $this->data['token'] = $this->session->data['token'];

        $this->data['alerts'] = $this->alert;

        $this->template = 'module/similar_products.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function install() {
        $this->model_module_similar_products->applyDatabaseChanges();

        $this->load->model('setting/setting');

        $this->model_setting_setting->editSetting('similar_products', $this->defaults);
    }

    public function uninstall() {
        if ($this->config->get("sp_remove_sql_changes")) {
            $this->model_module_similar_products->revertDatabaseChanges();
        }

        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('similar_products');
    }

    public function upgrade() {
        $ajax_request = isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        $response = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateUpgrade()) {
            $this->load->model('setting/setting');

            if ($this->model_module_similar_products->upgradeDatabaseStructure($this->installedVersion(), $this->alert)) {
                $settings = array();

                // Go over all settings, add new values and remove old ones
                foreach ($this->defaults as $setting => $default) {
                    $value = $this->config->get($setting);
                    if ($value === null) {
                        $settings[$setting] = $default;
                    } else {
                        $settings[$setting] = $value;
                    }
                }

                $settings['sp_installed_version'] = EXTENSION_VERSION;

                $this->model_setting_setting->editSetting('similar_products', $settings);

                $response['success'] = true;
                $response['msg'] = sprintf($this->language->get('text_success_upgrade'), EXTENSION_VERSION);
            } else {
                $this->alert['error']['database_upgrade'] = $this->language->get('error_upgrade_database');
                $response = array_merge(array("error" => true), array("errors" => $this->error), array("alerts" => $this->alert));
            }
        } else {
            $response = array_merge(array("error" => true), array("errors" => $this->error), array("alerts" => $this->alert));
        }

        if (!$ajax_request) {
            $this->redirect($this->url->link('module/similar_products', 'token=' . $this->session->data['token'], 'SSL'));
        } else {
            $this->response->setOutput(json_enc($response, JSON_UNESCAPED_SLASHES));
            return;
        }
    }

    public function autocomplete() {
        if ($this->request->server['REQUEST_METHOD'] == 'GET' && isset($this->request->get['type'])) {
            $resp = array();
            switch ($this->request->get['type']) {
                case 'product':
                    $this->load->model('catalog/product');

                    $results = array();

                    if (isset($this->request->get['query'])) {
                        $data = array(
                            'filter_name'   => $this->request->get['query'],
                            'sort'          => 'pd.name',
                            'start'         => 0,
                            'limit'         => 20,
                        );

                        $results = $this->model_catalog_product->getProducts($data);
                    }

                    foreach ($results as $result) {
                        $result['name'] = html_entity_decode($result['name']);
                        $resp[] = array(
                            'value'     => $result['name'],
                            'tokens'    => explode(' ', $result['name']),
                            'id'        => $result['product_id'],
                            'model'     => $result['model']
                        );
                    }
                    break;
                case 'category':
                    $this->load->model('catalog/category');

                    $results = array();

                    if (isset($this->request->get['query'])) {
                        $data = array(
                            'filter_name'   => $this->request->get['query'],
                            'sort'          => 'name',
                            'start'         => 0,
                            'limit'         => 20,
                        );

                        $results = $this->model_catalog_category->getCategories($data);

                        if (stripos($this->language->get('text_none'), $this->request->get['query']) !== false) {
                            $resp[] = array(
                                    'value'     => $this->language->get('text_none'),
                                    'tokens'    => explode(' ', trim(str_replace('---', '', $this->language->get('text_none')))),
                                    'id'        => '*',
                                    'path'      => '',
                                    'full_name' => $this->language->get('text_none')
                                );
                        }
                    }

                    foreach ($results as $result) {
                        $result['name'] = html_entity_decode($result['name']);
                        $parts = explode(' > ', $result['name']);
                        $last_part = array_pop($parts);

                        $resp[] = array(
                            'value'     => $last_part,
                            'tokens'    => explode(' ', str_replace(' > ', ' ', $result['name'])),
                            'id'        => $result['category_id'],
                            'path'      => $parts ? implode(' > ', $parts) . ' > ' : '',
                            'full_name' => $result['name']
                        );
                    }
                    break;
                default:
                    break;
            }
        }

        $this->response->setOutput(json_enc($resp, JSON_UNESCAPED_SLASHES));
    }

    private function checkPrerequisites() {
        $errors = false;

        if (!class_exists('VQMod')) {
            $errors = true;
            $this->alert['error']['vqmod'] = $this->language->get('error_vqmod');
        }

        return !$errors;
    }

    private function checkVersion() {
        $errors = false;

        $installed_version = $this->installedVersion();

        if ($installed_version != EXTENSION_VERSION) {
            $errors = true;
            $this->alert['info']['version'] = sprintf($this->language->get('error_version'), EXTENSION_VERSION);
        }

        return !$errors;
    }

    private function validate() {
        $errors = false;

        if (!$this->user->hasPermission('modify', 'module/similar_products')) {
            $errors = true;
            $this->alert['error']['permission'] = $this->language->get('error_permission');
        }

        if (!$errors) {
            return $this->checkVersion() && $this->model_module_similar_products->checkDatabaseStructure($this->alert) && $this->checkPrerequisites();
        } else {
            return false;
        }
    }

    private function validateForm($data) {
        $errors = false;

        if (isset($data['similar_products_module'])) {
            foreach ((array)$data['similar_products_module'] as $idx => $module) {
                if (isset($module['names'])) {
                    foreach ((array)$module['names'] as $language_id => $value) {
                        if (!utf8_strlen($value)) {
                            $errors = true;
                            $this->error['modules'][$idx]['names'][$language_id] = $this->language->get('error_module_name');
                        }
                    }
                } else {
                    $errors = true;
                }

                if (!(int)$module['image_width']) {
                    $errors = true;
                    $this->error['modules'][$idx]['image_width'] = $this->language->get('error_positive_integer');
                }

                if (!(int)$module['image_height']) {
                    $errors = true;
                    $this->error['modules'][$idx]['image_height'] = $this->language->get('error_positive_integer');
                }
            }
        }

        if ($errors) {
            $this->alert['warning']['warning'] = $this->language->get('error_warning');
        }

        if (!$errors) {
            return $this->validate();
        } else {
            return false;
        }
    }

    private function validateUpgrade() {
        $errors = false;

        if (!$this->user->hasPermission('modify', 'module/similar_products')) {
            $errors = true;
            $this->alert['error']['permission'] = $this->language->get('error_permission');
        }

        return !$errors;
    }

    private function installedVersion() {
        $installed_version = $this->config->get('sp_installed_version');
        return $installed_version ? $installed_version : '3.2.2';
    }
}
?>
