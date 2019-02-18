<?php
class ModelModuleSimilarProducts extends Model {
    private $tables = array(
        "product_similar" => array("product_id", "similar_id"),
        "product" => array("sp_auto_select", "sp_product_sort_order", "sp_substr_start", "sp_substr_length", "sp_custom_string", "sp_leaves_only"),
    );

    public function applyMassChange($data) {
        switch ($data['sp_apply_to']['products']) {
            case '0': // All products
                $this->db->query("UPDATE " . DB_PREFIX . "product SET sp_auto_select = '" . (int)$data['sp_auto_select'] . "', sp_product_sort_order = '" . (int)$data['sp_product_sort_order'] . "', sp_substr_start = '" . (int)$data['sp_substr_start'] . "', sp_substr_length = '" . (int)$data['sp_substr_length'] . "', sp_custom_string = '" . $this->db->escape($data['sp_custom_string']) . "', sp_leaves_only = '" . (int)$data['sp_leaves_only'] . "'");
                break;
            case '1': // Products in category
                $this->db->query("UPDATE " . DB_PREFIX . "product p JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id AND p2c.category_id = '" . (int)$data['sp_apply_to']['category'] . "') SET sp_auto_select = '" . (int)$data['sp_auto_select'] . "', sp_product_sort_order = '" . (int)$data['sp_product_sort_order'] . "', sp_substr_start = '" . (int)$data['sp_substr_start'] . "', sp_substr_length = '" . (int)$data['sp_substr_length'] . "', sp_custom_string = '" . $this->db->escape($data['sp_custom_string']) . "', sp_leaves_only = '" . (int)$data['sp_leaves_only'] . "'");
                break;
            case '2': // Selected products
                foreach ((array)$data['sp_apply_to']['selected'] as $product) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET sp_auto_select = '" . (int)$data['sp_auto_select'] . "', sp_product_sort_order = '" . (int)$data['sp_product_sort_order'] . "', sp_substr_start = '" . (int)$data['sp_substr_start'] . "', sp_substr_length = '" . (int)$data['sp_substr_length'] . "', sp_custom_string = '" . $this->db->escape($data['sp_custom_string']) . "', sp_leaves_only = '" . (int)$data['sp_leaves_only'] . "' WHERE product_id = '" . (int)$product['product_id'] . "'");
                }
                break;
            default:
                break;
        }
    }

    public function getProductLayouts() {
        $query = $this->db->query("SELECT DISTINCT l.* FROM " . DB_PREFIX . "layout l JOIN " . DB_PREFIX . "layout_route lr ON (l.layout_id = lr.layout_id) WHERE lr.route = 'product/product' ORDER BY l.name");

        return $query->rows;
    }

    public function upgradeDatabaseStructure($from_version, &$alert) {
        $errors = false;

        switch ($from_version) {
            case '3.2.2':
                $column_auto_select_exists = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "product LIKE 'auto_similar'");
                $column_sort_order_exists = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "product LIKE 'similar_order'");

                if ($column_auto_select_exists->num_rows && $column_sort_order_exists->num_rows) {
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . "product`
                        CHANGE `auto_similar` `sp_auto_select` TINYINT(1) NOT NULL DEFAULT '0',
                        CHANGE `similar_order` `sp_product_sort_order` TINYINT(1) NOT NULL DEFAULT '0',
                        ADD COLUMN `sp_substr_start` TINYINT(2) NOT NULL DEFAULT '0' AFTER `sp_auto_select`,
                        ADD COLUMN `sp_substr_length` TINYINT(2) NOT NULL DEFAULT '0' AFTER `sp_substr_start`,
                        ADD COLUMN `sp_custom_string` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' AFTER `sp_substr_length`,
                        ADD COLUMN `sp_leaves_only` TINYINT(1) NOT NULL DEFAULT '0' AFTER `sp_custom_string`"
                    );
                } else {
                    $errors = true;
                    $alert['error']['upgrade_database'] = $this->language->get('error_upgrade_database');
                }
            default:
                break;
        }

        return !$errors;
    }

    public function applyDatabaseChanges() {
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "product_similar (
            product_id INT(11) NOT NULL,
            similar_id INT(11) NOT NULL,
            PRIMARY KEY (product_id, similar_id),
            INDEX sp_similar (similar_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_auto_select'");
        if (!$column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD COLUMN sp_auto_select TINYINT(1) NOT NULL DEFAULT '0'");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_product_sort_order'");
        if (!$column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD COLUMN sp_product_sort_order TINYINT(1) NOT NULL DEFAULT '0' AFTER `sp_auto_select`");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_substr_start'");
        if (!$column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD COLUMN sp_substr_start TINYINT(2) NOT NULL DEFAULT '0' AFTER `sp_product_sort_order`");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_substr_length'");
        if (!$column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD COLUMN sp_substr_length TINYINT(2) NOT NULL DEFAULT '0' AFTER `sp_substr_start`");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_custom_string'");
        if (!$column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD COLUMN sp_custom_string VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' AFTER `sp_substr_length`");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_leaves_only'");
        if (!$column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD COLUMN sp_leaves_only TINYINT(1) NOT NULL DEFAULT '0' AFTER `sp_custom_string`");
        }
    }

    public function revertDatabaseChanges() {
        $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "product_similar");

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_auto_select'");
        if ($column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP COLUMN sp_auto_select");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_product_sort_order'");
        if ($column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP COLUMN sp_product_sort_order");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_substr_start'");
        if ($column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP COLUMN sp_substr_start");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_substr_length'");
        if ($column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP COLUMN sp_substr_length");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_custom_string'");
        if ($column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP COLUMN sp_custom_string");
        }

        $column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'sp_leaves_only'");
        if ($column_exists->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP COLUMN sp_leaves_only");
        }
    }

    public function checkDatabaseStructure(&$alert) {
        $errors = false;

        foreach ($this->tables as $tbl => $fields) {
            $table_exists = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "{$tbl}'");
            if (!$table_exists->num_rows) {
                $errors = true;
                $alert['error']["missing_table_{$tbl}"] = sprintf($this->language->get('error_missing_table'), DB_PREFIX . $tbl);
            } else { // Check for table fields
                foreach($fields as $field) {
                    $column_exists = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "{$tbl} LIKE '{$field}'");
                    if (!$column_exists->num_rows) {
                        $errors = true;
                        $alert['error']["missing_column_{$field}"] = sprintf($this->language->get('error_missing_column'), DB_PREFIX . $tbl, $field);
                    }
                }
            }
        }

        return !$errors;
    }
}
?>
