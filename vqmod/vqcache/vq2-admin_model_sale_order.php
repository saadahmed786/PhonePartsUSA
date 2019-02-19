<?php
class ModelSaleOrder extends Model {
    public function addOrder($data) {
        $this->load->model('setting/store');
        $store_info = $this->model_setting_store->getStore($data['store_id']);
        if ($store_info) {
            $store_name = $store_info['name'];
            $store_url = $store_info['url'];
        } else {
            $store_name = $this->config->get('config_name');
            $store_url = HTTP_CATALOG;
        }
        $this->load->model('setting/setting');
        $setting_info = $this->model_setting_setting->getSetting('setting', $data['store_id']);
        if (isset($setting_info['invoice_prefix'])) {
            $invoice_prefix = $setting_info['invoice_prefix'];
        } else {
            $invoice_prefix = $this->config->get('config_invoice_prefix');
        }
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $country_info = $this->model_localisation_country->getCountry($data['shipping_country_id']);
        if ($country_info) {
            $shipping_country = $country_info['name'];
            $shipping_address_format = $country_info['address_format'];
        } else {
            $shipping_country = '';
            $shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
        }
        $zone_info = $this->model_localisation_zone->getZone($data['shipping_zone_id']);
        if ($zone_info) {
            $shipping_zone = $zone_info['name'];
        } else {
            $shipping_zone = '';
        }
        $country_info = $this->model_localisation_country->getCountry($data['payment_country_id']);
        if ($country_info) {
            $payment_country = $country_info['name'];
            $payment_address_format = $country_info['address_format'];
        } else {
            $payment_country = '';
            $payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
        }
        $zone_info = $this->model_localisation_zone->getZone($data['payment_zone_id']);
        if ($zone_info) {
            $payment_zone = $zone_info['name'];
        } else {
            $payment_zone = '';
        }
        $this->load->model('localisation/currency');
        $currency_info = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));
        if ($currency_info) {
            $currency_id = $currency_info['currency_id'];
            $currency_code = $currency_info['code'];
            $currency_value = $currency_info['value'];
        } else {
            $currency_id = 0;
            $currency_code = $this->config->get('config_currency');
            $currency_value = 1.00000;
        }
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($invoice_prefix) . "', store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($store_name) . "',store_url = '" . $this->db->escape($store_url) . "', customer_id = '" . (int) $data['customer_id'] . "', customer_group_id = '" . (int) $data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', company = '" . $this->db->escape($data['company']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int) $data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int) $data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int) $data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int) $data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', order_status_id = '" . (int) $data['order_status_id'] . "', affiliate_id  = '" . (int) $data['affiliate_id'] . "', language_id = '" . (int) $this->config->get('config_language_id') . "', currency_id = '" . (int) $currency_id . "', currency_code = '" . $this->db->escape($currency_code) . "', currency_value = '" . (float) $currency_value . "', admin_view_only='" . $data['admin_view_only'] . "', date_added = NOW(), date_modified = NOW()");
        $order_id = $this->db->getLastId();
        if (isset($data['order_product'])) {
            foreach ($data['order_product'] as $order_product) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int) $order_id . "', product_id = '" . (int) $order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (int) $order_product['quantity'] . "', price = '" . (float) $order_product['price'] . "', total = '" . (float) $order_product['total'] . "', tax = '" . (float) $order_product['tax'] . "', reward = '" . (int) $order_product['reward'] . "'");
                $order_product_id = $this->db->getLastId();
                if (isset($order_product['order_option'])) {
                    foreach ($order_product['order_option'] as $order_option) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $order_option['product_option_id'] . "', product_option_value_id = '" . (int) $order_option['product_option_value_id'] . "', name = '" . $this->db->escape($order_option['name']) . "', `value` = '" . $this->db->escape($order_option['value']) . "', `type` = '" . $this->db->escape($order_option['type']) . "'");
                    }
                }
                if (isset($order_product['order_download'])) {
                    foreach ($order_product['order_download'] as $order_download) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', name = '" . $this->db->escape($order_download['name']) . "', filename = '" . $this->db->escape($order_download['filename']) . "', mask = '" . $this->db->escape($order_download['mask']) . "', remaining = '" . (int) $order_download['remaining'] . "'");
                    }
                }
            }
        }
        if (isset($data['order_voucher'])) {
            foreach ($data['order_voucher'] as $order_voucher) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int) $order_id . "', voucher_id = '" . (int) $order_voucher['voucher_id'] . "', description = '" . $this->db->escape($order_voucher['description']) . "', code = '" . $this->db->escape($order_voucher['code']) . "', from_name = '" . $this->db->escape($order_voucher['from_name']) . "', from_email = '" . $this->db->escape($order_voucher['from_email']) . "', to_name = '" . $this->db->escape($order_voucher['to_name']) . "', to_email = '" . $this->db->escape($order_voucher['to_email']) . "', voucher_theme_id = '" . (int) $order_voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($order_voucher['message']) . "', amount = '" . (float) $order_voucher['amount'] . "'");
                $this->db->query("UPDATE " . DB_PREFIX . "voucher SET order_id = '" . (int) $order_id . "' WHERE voucher_id = '" . (int) $order_voucher['voucher_id'] . "'");
            }
        }
        // Get the total
        $total = 0;
        if (isset($data['order_total'])) {
            foreach ($data['order_total'] as $order_total) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($order_total['code']) . "', title = '" . $this->db->escape($order_total['title']) . "', text = '" . $this->db->escape($order_total['text']) . "', `value` = '" . (float) $order_total['value'] . "', sort_order = '" . (int) $order_total['sort_order'] . "'");
            }
            $total += $order_total['value'];
        }
        if (isset($this->session->data['voucher'])) {
            $this->load->model('sale/voucher');
            $voucher_info = $this->model_sale_voucher->getVoucherByCode($this->session->data['voucher']);
            if ($voucher_info) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "voucher_history` SET voucher_id = '" . (int) $voucher_info['voucher_id'] . "', order_id = '" . (int) $order_id . "', amount = '" . (float) $voucher_info['amount'] * (-1) . "', date_added = NOW()");
            }
        }
        // Affiliate
        $affiliate_id = 0;
        $commission = 0;
        if (!empty($this->request->post['affiliate_id'])) {
            $this->load->model('sale/affiliate');
            $affiliate_info = $this->model_sale_affiliate->getAffiliate($this->request->post['affiliate_id']);
            if ($affiliate_info) {
                $affiliate_id = $affiliate_info['affiliate_id'];
                $commission = ($total / 100) * $affiliate_info['commission'];
            }
        }
        // Update order total			 
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float) $total . "', affiliate_id = '" . (int) $affiliate_id . "', commission = '" . (float) $commission . "' WHERE order_id = '" . (int) $order_id . "'");
        if ($data['user_id']) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET user_id = '" . $data['user_id'] . "' WHERE order_id = '" . (int) $order_id . "'");
        }
        if ($data['pos_total']) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET pos_total = '" . (float) $data['pos_total'] . "' WHERE order_id = '" . (int) $order_id . "'");
        }
        if ($data['ref_order_id']) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET ref_order_id = '" . $this->db->escape($data['ref_order_id']) . "' WHERE order_id = '" . (int) $order_id . "'");
        }
    }
    public function editOrder($order_id, $data) {
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $country_info = $this->model_localisation_country->getCountry($data['shipping_country_id']);
        if ($country_info) {
            $shipping_country = $country_info['name'];
            $shipping_address_format = $country_info['address_format'];
        } else {
            $shipping_country = '';
            $shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
        }
        $zone_info = $this->model_localisation_zone->getZone($data['shipping_zone_id']);
        if ($zone_info) {
            $shipping_zone = $zone_info['name'];
        } else {
            $shipping_zone = '';
        }
        $country_info = $this->model_localisation_country->getCountry($data['payment_country_id']);
        if ($country_info) {
            $payment_country = $country_info['name'];
            $payment_address_format = $country_info['address_format'];
        } else {
            $payment_country = '';
            $payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
        }
        $zone_info = $this->model_localisation_zone->getZone($data['payment_zone_id']);
        if ($zone_info) {
            $payment_zone = $zone_info['name'];
        } else {
            $payment_zone = '';
        }
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int) $data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int) $data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',  shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int) $data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int) $data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', order_status_id = '" . (int) $data['order_status_id'] . "', affiliate_id  = '" . (int) $data['affiliate_id'] . "', date_modified = NOW() WHERE order_id = '" . (int) $order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int) $order_id . "'");
        if (isset($data['order_product'])) {
            foreach ($data['order_product'] as $order_product) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_product_id = '" . (int) $order_product['order_product_id'] . "', order_id = '" . (int) $order_id . "', product_id = '" . (int) $order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (int) $order_product['quantity'] . "', price = '" . (float) $order_product['price'] . "', total = '" . (float) $order_product['total'] . "', tax = '" . (float) $order_product['tax'] . "', reward = '" . (int) $order_product['reward'] . "'");
                $order_product_id = $this->db->getLastId();
                if (isset($order_product['order_option'])) {
                    foreach ($order_product['order_option'] as $order_option) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_option_id = '" . (int) $order_option['order_option_id'] . "', order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $order_option['product_option_id'] . "', product_option_value_id = '" . (int) $order_option['product_option_value_id'] . "', name = '" . $this->db->escape($order_option['name']) . "', `value` = '" . $this->db->escape($order_option['value']) . "', `type` = '" . $this->db->escape($order_option['type']) . "'");
                    }
                }
                if (isset($order_product['order_download'])) {
                    foreach ($order_product['order_download'] as $order_download) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_download_id = '" . (int) $order_download['order_download_id'] . "', order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', name = '" . $this->db->escape($order_download['name']) . "', filename = '" . $this->db->escape($order_download['filename']) . "', mask = '" . $this->db->escape($order_download['mask']) . "', remaining = '" . (int) $order_download['remaining'] . "'");
                    }
                }
            }
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int) $order_id . "'");
        if (isset($data['order_voucher'])) {
            foreach ($data['order_voucher'] as $order_voucher) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_voucher_id = '" . (int) $order_voucher['order_voucher_id'] . "', order_id = '" . (int) $order_id . "', voucher_id = '" . (int) $order_voucher['voucher_id'] . "', description = '" . $this->db->escape($order_voucher['description']) . "', code = '" . $this->db->escape($order_voucher['code']) . "', from_name = '" . $this->db->escape($order_voucher['from_name']) . "', from_email = '" . $this->db->escape($order_voucher['from_email']) . "', to_name = '" . $this->db->escape($order_voucher['to_name']) . "', to_email = '" . $this->db->escape($order_voucher['to_email']) . "', voucher_theme_id = '" . (int) $order_voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($order_voucher['message']) . "', amount = '" . (float) $order_voucher['amount'] . "'");
                $this->db->query("UPDATE " . DB_PREFIX . "voucher SET order_id = '" . (int) $order_id . "' WHERE voucher_id = '" . (int) $order_voucher['voucher_id'] . "'");
            }
        }
        // Get the total
        $total = 0;
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "'");
        if (isset($data['order_total'])) {
            foreach ($data['order_total'] as $order_total) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_total_id = '" . (int) $order_total['order_total_id'] . "', order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($order_total['code']) . "', title = '" . $this->db->escape($order_total['title']) . "', text = '" . $this->db->escape($order_total['text']) . "', `value` = '" . (float) $order_total['value'] . "', sort_order = '" . (int) $order_total['sort_order'] . "'");
            }
            $total += $order_total['value'];
        }
        // Affiliate
        $affiliate_id = 0;
        $commission = 0;
        if (!empty($this->request->post['affiliate_id'])) {
            $this->load->model('sale/affiliate');
            $affiliate_info = $this->model_sale_affiliate->getAffiliate($this->request->post['affiliate_id']);
            if ($affiliate_info) {
                $affiliate_id = $affiliate_info['affiliate_id'];
                $commission = ($total / 100) * $affiliate_info['commission'];
            }
        }
        $voucher_check = $this->db->query("SELECT SUM(value) AS amount,title FROM " . DB_PREFIX . "order_total WHERE order_id='" . (int) $order_id . "' AND code='voucher'");
        $voucher_check = $voucher_check->row;
        if ($voucher_check) {
            $this->load->model('sale/voucher');
            $voucher_code = '';
            $code_start = strpos($voucher_check['title'], '(') + 1;
                $code_end = strrpos($voucher_check['title'], ')');
                if ($code_start && $code_end) {
                    $voucher_code = substr($voucher_check['title'], $code_start, $code_end - $code_start);
                }
                $voucher_detail = $this->model_sale_voucher->getVoucherByCode($voucher_code);
                if ($voucher_detail) {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "voucher_history` SET voucher_id = '" . (int) $voucher_detail['voucher_id'] . "', order_id = '" . (int) $order_id . "', amount = '" . (float) $voucher_check['amount'] . "', date_added = NOW()");
                }
            }
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float) $total . "', affiliate_id = '" . (int) $affiliate_id . "', commission = '" . (float) $commission . "' WHERE order_id = '" . (int) $order_id . "'");
        }
        public function deleteOrder($order_id) {
            $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND order_id = '" . (int) $order_id . "'");
            if ($order_query->num_rows) {
                $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
                foreach ($product_query->rows as $product) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int) $product['quantity'] . ") WHERE product_id = '" . (int) $product['product_id'] . "' AND subtract = '1'");
                    $option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $product['order_product_id'] . "'");
                    foreach ($option_query->rows as $option) {
                        $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int) $product['quantity'] . ") WHERE product_option_value_id = '" . (int) $option['product_option_value_id'] . "' AND subtract = '1'");
                    }
                }
            }
            $this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int) $order_id . "'");
        }
        public function getOrder($order_id) {
            $order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "'");
            if ($order_query->num_rows) {
                $reward = 0;
                $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
                foreach ($order_product_query->rows as $product) {
                    $reward += $product['reward'];
                }
                $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int) $order_query->row['payment_country_id'] . "'");
                if ($country_query->num_rows) {
                    $payment_iso_code_2 = $country_query->row['iso_code_2'];
                    $payment_iso_code_3 = $country_query->row['iso_code_3'];
                } else {
                    $payment_iso_code_2 = '';
                    $payment_iso_code_3 = '';
                }
                $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int) $order_query->row['payment_zone_id'] . "'");
                if ($zone_query->num_rows) {
                    $payment_zone_code = $zone_query->row['code'];
                } else {
                    $payment_zone_code = '';
                }
                $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int) $order_query->row['shipping_country_id'] . "'");
                if ($country_query->num_rows) {
                    $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                    $shipping_iso_code_3 = $country_query->row['iso_code_3'];
                } else {
                    $shipping_iso_code_2 = '';
                    $shipping_iso_code_3 = '';
                }
                $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int) $order_query->row['shipping_zone_id'] . "'");
                if ($zone_query->num_rows) {
                    $shipping_zone_code = $zone_query->row['code'];
                } else {
                    $shipping_zone_code = '';
                }
                if ($order_query->row['affiliate_id']) {
                    $affiliate_id = $order_query->row['affiliate_id'];
                } else {
                    $affiliate_id = 0;
                }
                $this->load->model('sale/affiliate');
                $affiliate_info = $this->model_sale_affiliate->getAffiliate($affiliate_id);
                if ($affiliate_info) {
                    $affiliate_firstname = $affiliate_info['firstname'];
                    $affiliate_lastname = $affiliate_info['lastname'];
                } else {
                    $affiliate_firstname = '';
                    $affiliate_lastname = '';
                }
                $this->load->model('localisation/language');
                $language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);
                if ($language_info) {
                    $language_code = $language_info['code'];
                    $language_filename = $language_info['filename'];
                    $language_directory = $language_info['directory'];
                } else {
                    $language_code = '';
                    $language_filename = '';
                    $language_directory = '';
                }
                if ($order_query->row['old_total'] == '0.0000') {
                    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET old_total=total WHERE order_id='" . (int) $order_id . "'");
                }

                if($this->config->get('amazon_status') == 1){
                    $amazonOrderId = $this->db->query("
                        SELECT `amazon_order_id`
                        FROM `" . DB_PREFIX . "amazon_order`
                        WHERE `order_id` = " . (int) $order_query->row['order_id'] . "
                        LIMIT 1")->row;

                    if (isset($amazonOrderId['amazon_order_id']) && !empty($amazonOrderId['amazon_order_id'])) {
                        $amazonOrderId = $amazonOrderId['amazon_order_id'];
                    }else{
                        $amazonOrderId = '';
                    }
                }else{
                    $amazonOrderId = '';
                }
            

                if($this->config->get('amazonus_status') == 1){
                    $amazonusOrderId = $this->db->query("
                        SELECT `amazonus_order_id`
                        FROM `" . DB_PREFIX . "amazonus_order`
                        WHERE `order_id` = " . (int) $order_query->row['order_id'] . "
                        LIMIT 1")->row;

                    if (isset($amazonusOrderId['amazonus_order_id']) && !empty($amazonusOrderId['amazonus_order_id'])) {
                        $amazonusOrderId = $amazonusOrderId['amazonus_order_id'];
                    }else{
                        $amazonusOrderId = '';
                    }
                }else{
                    $amazonusOrderId = '';
                }
            
                return array(

              'amazonus_order_id' => $amazonusOrderId,
            

              'amazon_order_id' => $amazonOrderId,
            
                    'order_id' => $order_query->row['order_id'],
                    'ref_order_id' => $order_query->row['ref_order_id'],
                    'invoice_no' => $order_query->row['invoice_no'],
                    'invoice_prefix' => $order_query->row['invoice_prefix'],
                    'store_id' => $order_query->row['store_id'],
                    'store_name' => $order_query->row['store_name'],
                    'store_url' => $order_query->row['store_url'],
                    'customer_id' => $order_query->row['customer_id'],
                    'customer' => $order_query->row['customer'],
                    'customer_group_id' => $order_query->row['customer_group_id'],
                    'firstname' => $order_query->row['firstname'],
                    'lastname' => $order_query->row['lastname'],
                    'telephone' => $order_query->row['telephone'],
                    'fax' => $order_query->row['fax'],
                    'email' => $order_query->row['email'],
                    'company'=>$order_query->row['company'],
                    'admin_view_only' => $order_query->row['admin_view_only'],
                    'payment_firstname' => $order_query->row['payment_firstname'],
                    'payment_lastname' => $order_query->row['payment_lastname'],
                    'payment_company' => $order_query->row['payment_company'],
                    'payment_company_id' => $order_query->row['payment_company_id'],
                    'payment_tax_id' => $order_query->row['payment_tax_id'],
                    'payment_address_1' => $order_query->row['payment_address_1'],
                    'payment_address_2' => $order_query->row['payment_address_2'],
                    'payment_postcode' => $order_query->row['payment_postcode'],
                    'payment_city' => $order_query->row['payment_city'],
                    'payment_zone_id' => $order_query->row['payment_zone_id'],
                    'payment_zone' => $order_query->row['payment_zone'],
                    'payment_zone_code' => $payment_zone_code,
                    'payment_country_id' => $order_query->row['payment_country_id'],
                    'payment_country' => $order_query->row['payment_country'],
                    'payment_iso_code_2' => $payment_iso_code_2,
                    'payment_iso_code_3' => $payment_iso_code_3,
                    'payment_address_format' => $order_query->row['payment_address_format'],
                    'payment_method' => $order_query->row['payment_method'],
                    'payment_code' => $order_query->row['payment_code'],
                    'shipping_firstname' => $order_query->row['shipping_firstname'],
                    'shipping_lastname' => $order_query->row['shipping_lastname'],
                    'shipping_company' => $order_query->row['shipping_company'],
                    'shipping_address_1' => $order_query->row['shipping_address_1'],
                    'shipping_address_2' => $order_query->row['shipping_address_2'],
                    'shipping_postcode' => $order_query->row['shipping_postcode'],
                    'shipping_city' => $order_query->row['shipping_city'],
                    'shipping_zone_id' => $order_query->row['shipping_zone_id'],
                    'shipping_zone' => $order_query->row['shipping_zone'],
                    'shipping_zone_code' => $shipping_zone_code,
                    'shipping_country_id' => $order_query->row['shipping_country_id'],
                    'shipping_country' => $order_query->row['shipping_country'],
                    'shipping_iso_code_2' => $shipping_iso_code_2,
                    'shipping_iso_code_3' => $shipping_iso_code_3,
                    'shipping_address_format' => $order_query->row['shipping_address_format'],
                    'shipping_method' => $order_query->row['shipping_method'],
                    'shipping_code' => $order_query->row['shipping_code'],
                    'comment' => $order_query->row['comment'],
                    'total' => $order_query->row['total'],
                    'card_paid' => $order_query->row['card_paid'],
                    'cash_paid' => $order_query->row['cash_paid'],
                    'paypal_paid' => $order_query->row['paypal_paid'],
                    'change_due' => $order_query->row['change_due'],
                    'old_total' => $order_query->row['old_total'],
                    'reward' => $reward,
                    'order_status_id' => $order_query->row['order_status_id'],
                    'affiliate_id' => $order_query->row['affiliate_id'],
                    'affiliate_firstname' => $affiliate_firstname,
                    'affiliate_lastname' => $affiliate_lastname,
                    'commission' => $order_query->row['commission'],
                    'language_id' => $order_query->row['language_id'],
                    'language_code' => $language_code,
                    'language_filename' => $language_filename,
                    'language_directory' => $language_directory,
                    'currency_id' => $order_query->row['currency_id'],
                    'currency_code' => $order_query->row['currency_code'],
                    'currency_value' => $order_query->row['currency_value'],
                    'ip' => $order_query->row['ip'],
                    'forwarded_ip' => $order_query->row['forwarded_ip'],
                    'user_agent' => $order_query->row['user_agent'],
                    'user_id' => $order_query->row['user_id'],
                    'accept_language' => $order_query->row['accept_language'],
                    'date_added' => $order_query->row['date_added'],
                    'date_modified' => $order_query->row['date_modified']
                    );
} else {
    return false;
}
}
public function getOrders($data = array()) {

        // if (!empty($data['filter_sku']))
        // {
        //     $sku = "SELECT model FROM " . DB_PREFIX . "order_product WHERE model LIKE '%" . $this->db->escape($data['filter_sku']) . "%' ) AS sku";
        //     // print_r($sku);
        //     // die();
        // }
    $sql = "SELECT o.ref_order_id,o.order_status_id, o.order_id, o.email, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int) $this->config->get('config_language_id') . "') AS status,  o.payment_method, o.total,o.pos_total, o.currency_code, o.currency_value, o.date_added, o.date_modified, o.shipping_code FROM `" . DB_PREFIX . "order` o";
    if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
        $sql .= " WHERE o.order_status_id = '" . (int) $data['filter_order_status_id'] . "'";
    } else {
        $sql .= " WHERE o.order_status_id > '0'";
    }
    if (!empty($data['filter_order_id'])) {
        $sql .= " AND (o.order_id = '" . (int) $data['filter_order_id'] . "' or o.ref_order_id='" . $data['filter_order_id'] . "') ";
    }
    if (!empty($data['filter_customer'])) {
        $sql .= " AND LCASE(CONCAT(o.firstname, ' ', o.lastname)) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_customer'])) . "%'  OR o.email LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
    }
    if (!empty($data['filter_date_added'])) {
        $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
    }
    if (!empty($data['filter_date_modified'])) {
        $sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
    }
    if (!empty($data['filter_total'])) {
        $sql .= " AND o.total = '" . (float) $data['filter_total'] . "'";
    }
        // $_sku_filter = false;
        // if (!empty($data['filter_sku'])) {
        //     // $sku = "SELECT op.model FROM `" . DB_PREFIX . "order_product` op WHERE op.model LIKE %'" . $this->db->escape($data['filter_sku']) . "%'";
        //   //  $sql .= " AND sku LIKE '%" . $this->db->escape($data['filter_sku']) . "%'";
        //     $_sku_filter = true;
        // }
    if (!empty($data['shipping_code'])) {
        $sql .= " AND o.shipping_code = '" . $data['shipping_code'] . "'";
    }
    if (isset($data['picked_up_orders']) and $data['picked_up_orders'] == true) {
        $sql .= " AND o.order_status_id = '3'";
    }
    if (isset($data['picked_up_orders']) and $data['picked_up_orders'] == false) {
        $sql .= " AND o.order_status_id in ('24','15')";
    }
    if (isset($data['customer_id'])) {
        $sql .= " AND o.customer_id='" . (int) $data['customer_id'] . "'";
    }
    if (isset($data['customer_email'])) {
        $sql .= " AND o.email='" . $this->db->escape($data['customer_email']) . "'";
    }
    if (isset($data['payment_method'])) {
        $sql .= " AND o.payment_method='" . $this->db->escape($data['payment_method']) . "'";
    }
    if (isset($data['payment_method_not'])) {
        $sql .= " AND ref_order_id IS NULL";
    }
    $sort_data = array(
        'o.order_id',
        'customer',
        'status',
        'o.date_added',
        'o.date_modified',
        'sku',
        'o.total'

        );
    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
        $sql .= " ORDER BY " . $data['sort'];
    } else {
        $sql .= " ORDER BY o.order_id";
    }
    if (isset($data['order']) && ($data['order'] == 'DESC')) {
        $sql .= " DESC";
    } else {
        $sql .= " ASC";
    }
    if (isset($data['start']) || isset($data['limit'])) {
        if ($data['start'] < 0) {
            $data['start'] = 0;
        }
        if ($data['limit'] < 1) {
            $data['limit'] = 20;
        }
        $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
    }
        // echo $sql;exit;
    $query = $this->db->query($sql);
        // print_r($query);
        // die();
    return $query->rows;
}
public function getOrdersPOS($data = array()) {
    $sql = "SELECT distinct o.ref_order_id,o.order_status_id, o.order_id, o.email, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int) $this->config->get('config_language_id') . "') AS status,  o.payment_method, o.total,o.pos_total, o.currency_code, o.currency_value, o.date_added, o.date_modified, o.shipping_code FROM `" . DB_PREFIX . "order` o INNER JOIN ".DB_PREFIX."order_product op ON(o.order_id=op.order_id )" ;
    if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
        $sql .= " WHERE o.order_status_id = '" . (int) $data['filter_order_status_id'] . "'";
    } else {
        $sql .= " WHERE o.order_status_id > '0'";
    }
    if (!empty($data['filter_order_id'])) {
        $sql .= " AND (o.order_id = '" . (int) $data['filter_order_id'] . "' or o.ref_order_id='" . $data['filter_order_id'] . "') ";
    }
    if (!empty($data['filter_customer'])) {
        $sql .= " AND (LCASE(CONCAT(o.firstname, ' ', o.lastname)) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_customer'])) . "%'  OR LCASE(o.email) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_customer'])) . "%') ";
    }
    if (!empty($data['filter_date_added'])) {
        $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
    }
    if (!empty($data['filter_date_modified'])) {
        $sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
    }
    if (!empty($data['filter_total'])) {
        $sql .= " AND o.total = '" . (float) $data['filter_total'] . "'";
    }

    if (!empty($data['filter_sku'])) {
            // $sku = "SELECT op.model FROM `" . DB_PREFIX . "order_product` op WHERE op.model LIKE %'" . $this->db->escape($data['filter_sku']) . "%'";
          //  $sql .= " AND sku LIKE '%" . $this->db->escape($data['filter_sku']) . "%'";
      $sql .= " AND LOWER(op.model) LIKE '%" . $this->db->escape( strtolower($data['filter_sku']) ) . "%'";
  }
  if (!empty($data['shipping_code'])) {
    $sql .= " AND o.shipping_code = '" . $data['shipping_code'] . "'";
}
if (isset($data['picked_up_orders']) and $data['picked_up_orders'] == true) {
    $sql .= " AND o.order_status_id = '3'";
}
if (isset($data['picked_up_orders']) and $data['picked_up_orders'] == false) {
    $sql .= " AND o.order_status_id in ('24','15')";
}
if (isset($data['customer_id'])) {
    $sql .= " AND o.customer_id='" . (int) $data['customer_id'] . "'";
}
if (isset($data['customer_email'])) {
            // $sql .= " AND o.email='" . $this->db->escape($data['customer_email']) . "'";
}
if (isset($data['payment_method'])) {
    $sql .= " AND o.payment_method='" . $this->db->escape($data['payment_method']) . "'";
}
if (isset($data['payment_method_not'])) {
    $sql .= " AND ref_order_id IS NULL";
}
$sort_data = array(
    'o.order_id',
    'customer',
    'status',
    'o.date_added',
    'o.date_modified',
    'op.sku',
    'o.total'

    );
if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
    $sql .= " ORDER BY " . $data['sort'];
} else {
    $sql .= " ORDER BY o.order_id";
}
if (isset($data['order']) && ($data['order'] == 'DESC')) {
    $sql .= " DESC";
} else {
    $sql .= " ASC";
}
if (isset($data['start']) || isset($data['limit'])) {
    if ($data['start'] < 0) {
        $data['start'] = 0;
    }
    if ($data['limit'] < 1) {
        $data['limit'] = 20;
    }
    $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
}
$query = $this->db->query($sql);
        // print_r($query);
        // die();
return $query->rows;
}
public function getOrderProducts($order_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
    return $query->rows;
}
public function getOrderProduct($order_id, $product_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "' AND product_id='" . (int) $product_id . "'");
    return $query->row;
}
public function getOrderOption($order_id, $order_option_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_option_id = '" . (int) $order_option_id . "'");
    return $query->row;
}
public function getOrderOptions($order_id, $order_product_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $order_product_id . "'");
    return $query->rows;
}
public function getOrderDownloads($order_id, $order_product_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $order_product_id . "'");
    return $query->rows;
}
public function getOrderVouchers($order_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int) $order_id . "'");
    return $query->rows;
}
public function getOrderVouchersAndProducts($order_id) {
    $query = $this->db->query("SELECT oov.*, ov.`product_ids`, ir.`title` AS `rtitle` FROM `" . DB_PREFIX . "order_voucher` as `oov` inner join `" . DB_PREFIX . "voucher` as ov on oov.`voucher_id` = ov.`voucher_id` INNER JOIN `inv_reasons` AS `ir` ON ov.`reason` = ir.`id` WHERE oov.`order_id` = '" . (int) $order_id . "'");
    return $query->rows;
}
public function getProductSku($product_id) {
    $query = $this->db->query("SELECT `model` FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . $product_id . "'");
    return $query->rows;
}
public function getUsedVoucherHistoryByCode($code) {
    $query = $this->db->query("SELECT ovh.*, ov.`amount` as `famount` FROM `" . DB_PREFIX . "voucher_history` as `ovh` left join `" . DB_PREFIX . "voucher` as `ov` on ovh.`voucher_id` = ov.`voucher_id` where ov.`code` = '" . $code . "'");
    return $query->rows;
}
public function getOrderVoucherByVoucherId($voucher_id) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE voucher_id = '" . (int) $voucher_id . "'");
    return $query->row;
}
public function getOrderTotals($order_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order");
    return $query->rows;
}
public function getTotalOrders($data = array()) {
    $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`";
    if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
        $sql .= " WHERE order_status_id = '" . (int) $data['filter_order_status_id'] . "'";
    } else {
        $sql .= " WHERE order_status_id > '0'";
    }
    if (!empty($data['filter_order_id'])) {
        $sql .= " AND (order_id = '" . (int) $data['filter_order_id'] . "' or ref_order_id='" . $data['filter_order_id'] . "')";
    }
    if (!empty($data['filter_customer'])) {
        $sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
    }
    if (!empty($data['filter_date_added'])) {
        $sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
    }
    if (!empty($data['filter_date_modified'])) {
        $sql .= " AND DATE(date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
    } else {
        if ($data['picked_up_orders'] == true) {
            $sql .= " AND order_status_id = '3'";
        } else {
            $sql .= " AND order_status_id IN ('24','15')";
        }
    }
    if (!empty($data['filter_total'])) {
        $sql .= " AND total = '" . (float) $data['filter_total'] . "'";
    }
    if (!empty($data['shipping_code'])) {
        $sql .= " AND shipping_code = '" . $data['shipping_code'] . "'";
    }
    if (isset($data['customer_id'])) {
        $sql .= " AND customer_id='" . (int) $data['customer_id'] . "'";
    }
    if (isset($data['payment_method'])) {
        $sql .= " AND payment_method='" . $this->db->escape($data['payment_method']) . "'";
    }
    if (isset($data['payment_method_not'])) {
        $sql .= " AND ref_order_id IS NULL";
    }
    $query = $this->db->query($sql);
    return $query->row['total'];
}
public function getTotalOrdersByStoreId($store_id) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE store_id = '" . (int) $store_id . "'");
    return $query->row['total'];
}
public function getTotalOrdersByOrderStatusId($order_status_id) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int) $order_status_id . "' AND order_status_id > '0'");
    return $query->row['total'];
}
public function getTotalOrdersByLanguageId($language_id) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE language_id = '" . (int) $language_id . "' AND order_status_id > '0'");
    return $query->row['total'];
}
public function getTotalOrdersByCurrencyId($currency_id) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE currency_id = '" . (int) $currency_id . "' AND order_status_id > '0'");
    return $query->row['total'];
}
public function getTotalSales() {
    $query = $this->db->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0'");
    return $query->row['total'];
}
public function getTotalSalesByYear($year) {
    $query = $this->db->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND YEAR(date_added) = '" . (int) $year . "'");
    return $query->row['total'];
}
public function createInvoiceNo($order_id) {
    $order_info = $this->getOrder($this->request->get['order_id']);
    if ($order_info && !$order_info['invoice_no']) {
        $query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");
        if ($query->row['invoice_no']) {
            $invoice_no = $query->row['invoice_no'] + 1;
        } else {
            $invoice_no = 1;
        }
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int) $invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int) $order_id . "'");
        return $order_info['invoice_prefix'] . $invoice_no;
    }
}
public function getModHistory($order_id) {
    $this->load->model('user/user');
    $this->load->model('localisation/order_status');
    $record = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_mod_logs` WHERE order_id='" . (int) $order_id . "' ORDER BY history_id DESC LIMIT 1");
    $record = $record->row;
    $user_detail = $this->model_user_user->getUser($record['user_id']);
    $user = $user_detail['firstname'] . ' ' . $user_detail['lastname'];
    $old_status = $this->model_localisation_order_status->getOrderStatus($record['old_status_id']);
    $new_status = $this->model_localisation_order_status->getOrderStatus($record['new_status_id']);

                if($this->config->get('amazon_status') == 1){
                    $amazonOrderId = $this->db->query("
                        SELECT `amazon_order_id`
                        FROM `" . DB_PREFIX . "amazon_order`
                        WHERE `order_id` = " . (int) $order_query->row['order_id'] . "
                        LIMIT 1")->row;

                    if (isset($amazonOrderId['amazon_order_id']) && !empty($amazonOrderId['amazon_order_id'])) {
                        $amazonOrderId = $amazonOrderId['amazon_order_id'];
                    }else{
                        $amazonOrderId = '';
                    }
                }else{
                    $amazonOrderId = '';
                }
            

                if($this->config->get('amazonus_status') == 1){
                    $amazonusOrderId = $this->db->query("
                        SELECT `amazonus_order_id`
                        FROM `" . DB_PREFIX . "amazonus_order`
                        WHERE `order_id` = " . (int) $order_query->row['order_id'] . "
                        LIMIT 1")->row;

                    if (isset($amazonusOrderId['amazonus_order_id']) && !empty($amazonusOrderId['amazonus_order_id'])) {
                        $amazonusOrderId = $amazonusOrderId['amazonus_order_id'];
                    }else{
                        $amazonusOrderId = '';
                    }
                }else{
                    $amazonusOrderId = '';
                }
            
    return array(

              'amazonus_order_id' => $amazonusOrderId,
            

              'amazon_order_id' => $amazonOrderId,
            
        'username' => $user,
        'old_status' => $old_status['name'],
        'new_status' => $new_status['name'],
        'date_modified' => $record['date_modified'],
        'product_id' => $record['product_id']
        );
}
public function addOrderHistory($order_id, $data) {
    $previous_status_query = $this->db->query("SELECT order_status_id FROM " . DB_PREFIX . "order WHERE order_id='" . (int) $order_id . "'");
    $previous_status = $previous_status_query->row;
    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $data['order_status_id'] . "' WHERE order_id = '" . (int) $order_id . "'");
    $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $data['order_status_id'] . "', notify = '" . (isset($data['notify']) ? (int) $data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");
    $HistoryID = $this->db->getLastId();
    if ($data['store_credit'] == 1) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order_history` SET store_credit = '" . (int) $data['store_credit'] . "', code = '" . $data['code'] . "',amount='" . $data['amount'] . "' WHERE order_history_id = '" . (int) $HistoryID . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_mod_logs` SET order_history_id='" . $HistoryID . "', order_id='" . (int) $order_id . "',user_id='" . $this->user->getId() . "',old_status_id='" . $previous_status['order_status_id'] . "',new_status_id='" . $data['order_status_id'] . "',date_modified=NOW()");
    }
    if ($previous_status['order_status_id'] != $data['order_status_id']) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_mod_logs` SET order_history_id='" . $HistoryID . "', order_id='" . (int) $order_id . "',user_id='" . $this->user->getId() . "',old_status_id='" . $previous_status['order_status_id'] . "',new_status_id='" . $data['order_status_id'] . "',date_modified=NOW()");
    }
    $order_info = $this->getOrder($order_id);

		if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
			if ($order_info['customer_id'] && $order_info['reward']) {
				$this->load->model('sale/customer');

				$reward_total = $this->model_sale_customer->getTotalCustomerRewardsByOrderId($order_id);
				
				if (!$reward_total) {
					$this->model_sale_customer->addReward($order_info['customer_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['reward'], $order_id);
				} 
			}
			
			if ($order_info && $order_info['affiliate_id']) {
				$this->load->model('sale/affiliate');
				
				$affiliate_total = $this->model_sale_affiliate->getTotalTransactionsByOrderId($order_id);
				
				if (!$affiliate_total) {
					$this->model_sale_affiliate->addTransaction($order_info['affiliate_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['commission'], $order_id);
					
				} 
			}
		}
			
        // Send out any gift voucher mails
    if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
        $this->load->model('sale/voucher');
        $results = $this->getOrderVouchers($order_id);
        foreach ($results as $result) {
            $this->model_sale_voucher->sendVoucher($result['voucher_id']);
        }
    }
    if ($data['notify']) {
        $language = new Language($order_info['language_directory']);
        $language->load($order_info['language_filename']);
        $language->load('mail/order');
        $subject = sprintf($language->get('text_subject'), $order_info['store_name'], $order_id);
        $message = $language->get('text_order') . ' ' . $order_id . "\n";
        $message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";
        $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int) $data['order_status_id'] . "' AND language_id = '" . (int) $order_info['language_id'] . "'");
        if ($order_status_query->num_rows) {
            $message .= $language->get('text_order_status') . "\n";
            $message .= $order_status_query->row['name'] . "\n\n";
        }
        if ($order_info['customer_id']) {
            $message .= $language->get('text_link') . "\n";
            $message .= html_entity_decode($order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
        }
        if ($data['comment']) {
            $message .= $language->get('text_comment') . "\n\n";
            $message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
        }
        $message .= $language->get('text_footer');
        $mail = new Mail();
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->hostname = $this->config->get('config_smtp_host');
        $mail->username = $this->config->get('config_smtp_username');
        $mail->password = $this->config->get('config_smtp_password');
        $mail->port = $this->config->get('config_smtp_port');
        $mail->timeout = $this->config->get('config_smtp_timeout');
        $mail->setTo($order_info['email']);
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender($order_info['store_name']);
        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
        $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
        $mail->send();
    }
}
public function getOrderHistories($order_id, $start = 0, $limit = 10) {
    $query = $this->db->query("SELECT oh.order_history_id,oh.date_added, os.name AS status, oh.comment, oh.notify,oh.store_credit,oh.code,oh.amount FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int) $order_id . "' AND os.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY oh.date_added ASC LIMIT " . (int) $start . "," . (int) $limit);
    return $query->rows;
}
public function getOrderHistory($order_id) {
    $query = $this->db->query("SELECT oh.order_history_id,oh.date_added, os.name AS status, oh.comment, oh.notify,oh.store_credit,oh.code,oh.amount FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int) $order_id . "' AND os.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY oh.date_added DESC  ");
    return $query->row;
}
public function getOrderHistoriesByStatusID($order_id, $order_status_id) {
    $query = $this->db->query("SELECT oh.order_history_id,oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int) $order_id . "' AND oh.order_status_id='" . (int) $order_status_id . "' AND os.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY oh.date_added ");
    return $query->rows;
}
public function getTotalOrderHistories($order_id) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int) $order_id . "'");
    return $query->row['total'];
}
public function getTotalOrderHistoriesByOrderStatusId($order_status_id) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int) $order_status_id . "'");
    return $query->row['total'];
}
public function getEmailsByProductsOrdered($products, $start, $end) {
    $implode = array();
    foreach ($products as $product_id) {
        $implode[] = "op.product_id = '" . $product_id . "'";
    }
    $query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");
    return $query->rows;
}
public function getTotalEmailsByProductsOrdered($products) {
    $implode = array();
    foreach ($products as $product_id) {
        $implode[] = "op.product_id = '" . $product_id . "'";
    }
    $query = $this->db->query("SELECT COUNT(DISTINCT email) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");
    return $query->row['total'];
}
public function updateOrderProductAndHistory_old($order_id, $data) {
    $this->load->model('catalog/product');
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id='" . $order_id . "' ");
    foreach ($query->rows as $row) {
        $this->db->query("INSERT INTO  `" . DB_PREFIX . "order_mod_logs` SET order_id='" . $order_id . "',user_id='" . $this->user->getId() . "',product_id='" . $row['product_id'] . "',quantity='" . $row['quantity'] . "',unit='" . $row['price'] . "',total='" . $row['total'] . "',date_modified=NOW() ");
    }
    $sub_total = 0;
    for ($i = 0; $i <= $data['TotalProducts'] - 1; $i++) {
        if (isset($data['equantity' . $i])) {
            $sub_total+=$data['etotal_' . $i];
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_product_id='" . $data['eorder_product_id_' . $i] . "' AND order_id='" . $order_id . "' ");
            $array = $query->row;
            if ($data['equantity' . $i] == 0) {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_product_id='" . $data['eorder_product_id_' . $i] . "' AND order_id='" . $order_id . "' ");
                $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int) $array['quantity'] . ") WHERE product_id='" . $data['eproduct_id_' . $i] . "' ");
            } else {
                if (count($array) > 0) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . ((int) $array['quantity'] - (int) $data['equantity' . $i]) . ") WHERE product_id='" . $data['eproduct_id_' . $i] . "' ");
                    $this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET quantity='" . $data['equantity' . $i] . "',total='" . $data['etotal_' . $i] . "' WHERE order_product_id='" . $data['eorder_product_id_' . $i] . "' AND order_id='" . $order_id . "'");
                } else {
                    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity - " . (int) $data['equantity' . $i] . ") WHERE product_id='" . $data['eproduct_id_' . $i] . "' ");
                    $result = $this->model_catalog_product->getProduct($data['eproduct_id_' . $i]);
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_product` SET order_id='" . $order_id . "',product_id='" . $result['product_id'] . "',name='" . $result['name'] . "',model='" . $result['model'] . "', quantity='" . $data['equantity' . $i] . "',total='" . $data['etotal_' . $i] . "',price='" . $data['eunit_' . $i] . "'");
                }
            }
        }
    }
    $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET value='" . $sub_total . "',text='$" . number_format($sub_total, 2) . "' WHERE order_id='" . $order_id . "' AND code='sub_total'");
    $total_diff = $this->db->query("SELECT SUM(value) as value FROM  `" . DB_PREFIX . "order_total` WHERE order_id='" . $order_id . "' AND code<>'total'");
    $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET value='" . $total_diff->row['value'] . "',text='$" . number_format($total_diff->row['value'], 2) . "' WHERE order_id='" . $order_id . "' AND code='total'");
    $UpdateProductTableTotal = $this->db->query("SELECT total FROM `" . DB_PREFIX . "order` WHERE order_id='" . (int) $order_id . "'");
    $this->db->query("UPDATE  `" . DB_PREFIX . "order` SET total='" . $total_diff->row['value'] . "' WHERE order_id='" . (int) $order_id . "' ");
}
public function updateOrderProductAndHistory($order_id) {
    $this->load->model('catalog/product');
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id='" . $order_id . "' ");
    foreach ($query->rows as $row) {
        $this->db->query("INSERT INTO  `" . DB_PREFIX . "order_mod_logs` SET order_id='" . $order_id . "',user_id='" . $this->user->getId() . "',product_id='" . $row['product_id'] . "',quantity='" . $row['quantity'] . "',unit='" . $row['price'] . "',total='" . $row['total'] . "',date_modified=NOW() ");
    }
    $sub_total = 0;
    for ($i = 0; $i <= count($_SESSION['order_changes']['products']) - 1; $i++) {
        if (isset($_SESSION['order_changes']['products'][$i]['quantity'])) {
            $sub_total+=$_SESSION['order_changes']['products'][$i]['total'];
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_product_id='" . $_SESSION['order_changes']['products'][$i]['order_product_id'] . "' AND order_id='" . $order_id . "' ");
            $array = $query->row;
            if ($_SESSION['order_changes']['products'][$i]['quantity'] == 0) {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_product_id='" . $_SESSION['order_changes']['products'][$i]['order_product_id'] . "' AND order_id='" . $order_id . "' ");
                $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int) $array['quantity'] . ") WHERE product_id='" . $_SESSION['order_changes']['products'][$i]['product_id'] . "' ");
            } else {
                if (count($array) > 0) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . ((int) $array['quantity'] - (int) $_SESSION['order_changes']['products'][$i]['quantity']) . ") WHERE product_id='" . $$_SESSION['order_changes']['products'][$i]['product_id'] . "' ");
                    $this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET quantity='" . $_SESSION['order_changes']['products'][$i]['quantity'] . "',total='" . $_SESSION['order_changes']['products'][$i]['total'] . "' WHERE order_product_id='" . $_SESSION['order_changes']['products'][$i]['order_product_id'] . "' AND order_id='" . $order_id . "'");
                } else {
                    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity - " . (int) $_SESSION['order_changes']['products'][$i]['quantity'] . ") WHERE product_id='" . $_SESSION['order_changes']['products'][$i]['product_id'] . "' ");
                    $result = $this->model_catalog_product->getProduct($_SESSION['order_changes']['products'][$i]['product_id']);
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_product` SET order_id='" . $order_id . "',product_id='" . $result['product_id'] . "',name='" . $result['name'] . "',model='" . $result['model'] . "', quantity='" . $_SESSION['order_changes']['products'][$i]['quantity'] . "',total='" . $_SESSION['order_changes']['products'][$i]['total'] . "',price='" . ($_SESSION['order_changes']['products'][$i]['total'] / $_SESSION['order_changes']['products'][$i]['quantity']) . "'");
                }
            }
        }
    }
    $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET value='" . $sub_total . "',text='$" . number_format($sub_total, 2) . "' WHERE order_id='" . $order_id . "' AND code='sub_total'");
    $total_diff = $this->db->query("SELECT SUM(value) as value FROM  `" . DB_PREFIX . "order_total` WHERE order_id='" . $order_id . "' AND code<>'total'");
    $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET value='" . $total_diff->row['value'] . "',text='$" . number_format($total_diff->row['value'], 2) . "' WHERE order_id='" . $order_id . "' AND code='total'");
    $UpdateProductTableTotal = $this->db->query("SELECT total FROM `" . DB_PREFIX . "order` WHERE order_id='" . (int) $order_id . "'");
    $this->db->query("UPDATE  `" . DB_PREFIX . "order` SET total='" . $total_diff->row['value'] . "' WHERE order_id='" . (int) $order_id . "' ");
    unset($_SESSION['order_changes']);
}
public function updateOrderProductAndHistory2($order_id, $data) {
    $this->load->model('catalog/product');
        /* $query = $this->db->query("SELECT * FROM `".DB_PREFIX."order_product` WHERE order_id='".$order_id."' ");
          foreach($query->rows as $row)
          {
          $this->db->query("INSERT INTO  `".DB_PREFIX."order_mod_logs` SET order_id='".$order_id."',user_id='".$this->user->getId()."',product_id='".$row['product_id']."',quantity='".$row['quantity']."',unit='".$row['price']."',total='".$row['total']."',date_modified=NOW() ");
      } */
      $sub_total = 0;
      $xArray = array();
      for ($i = 0; $i <= $data['TotalProducts'] - 1; $i++) {
        if (isset($data['equantity' . $i])) {
            $sub_total+=$data['etotal_' . $i];
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_product_id='" . $data['eorder_product_id_' . $i] . "' AND order_id='" . $order_id . "' ");
            $array = $query->row;
            if ($data['equantity' . $i] == 0) {
                $xArray[] = array('type' => 'delete', 'product_id' => $data['eproduct_id_' . $i], 'order_product_id' => $data['eorder_product_id_' . $i], 'quantity' => (int) $array['quantity'], 'total' => 0);
                    //			$this->db->query("DELETE FROM `".DB_PREFIX."order_product` WHERE order_product_id='".$data['eorder_product_id_'.$i]."' AND order_id='".$order_id."' ");
                    //		$this->db->query("UPDATE `".DB_PREFIX."product` SET quantity = (quantity + " . (int)$array['quantity'] . ") WHERE product_id='".$data['eproduct_id_'.$i]."' ");
            } else {
                    //		$this->db->query("UPDATE `".DB_PREFIX."product` SET quantity = (quantity + " . ((int)$array['quantity'] - (int)$data['equantity'.$i]) . ") WHERE product_id='".$data['eproduct_id_'.$i]."' ");
                    //$this->db->query("UPDATE `".DB_PREFIX."order_product` SET quantity='".$data['equantity'.$i]."',total='".$data['etotal_'.$i]."' WHERE order_product_id='".$data['eorder_product_id_'.$i]."' AND order_id='".$order_id."'");	
                $xArray[] = array('type' => 'update', 'product_id' => $data['eproduct_id_' . $i], 'order_product_id' => $data['eorder_product_id_' . $i], 'quantity' => (int) $data['equantity' . $i], 'total' => $data['etotal_' . $i]);
            }
        }
    }
        //$this->db->query("UPDATE `".DB_PREFIX."order_total` SET value='".$sub_total."',text='$".number_format($sub_total,2)."' WHERE order_id='".$order_id."' AND code='sub_total'");
    $total_diff = $this->db->query("SELECT SUM(value) as value FROM  `" . DB_PREFIX . "order_total` WHERE order_id='" . $order_id . "' AND code<>'total'");
    $new_total = $this->db->query("SELECT SUM(value) as value FROM  `" . DB_PREFIX . "order_total` WHERE order_id='" . $order_id . "' AND code<>'total' and code<>'sub_total'");
    $new_total = $new_total->row['value'];
        //$this->db->query("UPDATE `".DB_PREFIX."order_total` SET value='".$total_diff->row['value']."',text='$".number_format($total_diff->row['value'],2)."' WHERE order_id='".$order_id."' AND code='total'");
        //$UpdateProductTableTotal = $this->db->query("SELECT total FROM `".DB_PREFIX."order` WHERE order_id='".(int)$order_id."'");
        //$this->db->query("UPDATE  `".DB_PREFIX."order` SET total='".$total_diff->row['value']."' WHERE order_id='".(int)$order_id."' ");
    $_SESSION['order_changes'] = array(
        'order_id' => $order_id,
        'sub_total' => $sub_total,
        'old_total' => $total_diff->row['value'],
        'total' => ($sub_total + $new_total),
        'products' => $xArray);
}
public function getHistoryUser($history_id) {
    $query = $this->db->query("SELECT CONCAT(u.firstname,' ',u.lastname) AS user_name FROM oc_user u,oc_order_mod_logs m WHERE u.user_id=m.user_id AND m.order_history_id='" . $history_id . "'");
    return $query->row['user_name'];
}
public function getVoidProductsByOrderId($order_id) {
    $query = $this->db->query("SELECT
        a.*,b.`sku`,c.name
        FROM
        `oc_void_product` a
        INNER JOIN `oc_product` b
        ON (a.`product_id` = b.`product_id`)
        INNER JOIN `oc_product_description` c
        ON (b.`product_id` = c.`product_id`) 

        WHERE a.order_id='" . (int) $order_id . "'
        ");
    return $query->rows;
}
public function getOrderLatestProductPrice($order_id,$product_id)
{
  $this->load->model('catalog/product');
  $order_info = $this->getOrder($order_id);

		if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
			if ($order_info['customer_id'] && $order_info['reward']) {
				$this->load->model('sale/customer');

				$reward_total = $this->model_sale_customer->getTotalCustomerRewardsByOrderId($order_id);
				
				if (!$reward_total) {
					$this->model_sale_customer->addReward($order_info['customer_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['reward'], $order_id);
				} 
			}
			
			if ($order_info && $order_info['affiliate_id']) {
				$this->load->model('sale/affiliate');
				
				$affiliate_total = $this->model_sale_affiliate->getTotalTransactionsByOrderId($order_id);
				
				if (!$affiliate_total) {
					$this->model_sale_affiliate->addTransaction($order_info['affiliate_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['commission'], $order_id);
					
				} 
			}
		}
			
  $customer_group_id = $order_info['customer_group_id'];

  $product_info = $this->model_catalog_product->getProduct($product_id);
  $price = 0.00;
  $price = $product_info['price'];
  $order_products = $this->getOrderProducts($order_id);

  $discount_qty = 0;
  foreach($order_products as $order_product)
  {
     if ($order_product['product_id'] == $product_id) {
         $discount_quantity += $order_product['quantity'];
     }

 }

 $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");
 if ($product_discount_query->num_rows) {
  $price = $product_discount_query->row['price'];
}
return $price;
}
public function getReplacementOrder ($order_id) {
    $query = $this->db->query("SELECT order_id FROM " . DB_PREFIX . "order WHERE ref_order_id = '" . $order_id . "'");
    return $query->row;
}
public function getReplacementRef ($order_id)   {
    $query = $this->db->query("SELECT ref_order_id, order_id FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'");
    $order = $query->row;
    $order_id = ($order['ref_order_id'])? $order['ref_order_id']: $order_id;
    return $order_id;
}
public function backUpOrder ($order_id) {
    $order_id = (int) $order_id;
    $tables = array( 'order', 'order_commission', 'order_download', 'order_emails', 'order_fraud', 'order_history', 'order_misc', 'order_mod_logs', 'order_option', 'order_payment', 'order_product', 'order_survey', 'order_total', 'order_voucher');
    if ($order_id) {
        foreach ($tables as $table) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . $table ." WHERE order_id = '" . $order_id . "'");
            $query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "temp_" . $table ." WHERE order_id = '" . $order_id . "'");
            if ($query->row && !$query2->row) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "temp_" . $table ." SELECT * FROM " . DB_PREFIX . $table ." WHERE order_id = '" . $order_id . "'");
            }
        }
    }
}
public function removeItemFromOrderIMP ($data) {
    $this->db->query("INSERT INTO `inv_removed_order_items` SET order_id = '" . (int) $data['order_id'] . "', item_sku = '" . $data['item_sku'] . "', item_name = '" .  $data['item_name'] . "', date_removed = '" .  $data['date_removed'] . "', reason = '" .  $data['reason'] . "',
        removed_by = '" .  $data['removed_by'] . "'");
}
}
?>