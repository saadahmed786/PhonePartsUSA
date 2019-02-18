<?php
class ModelModuleSimilarProducts extends Model {
    protected $productCount = 0;

    public function getPlainProduct($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");

        if ($query->num_rows) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getProductTags($product_id) {
        $tags = array();

        if (defined('VERSION') && version_compare(VERSION, '1.5.4', '>=')) {
            $query = $this->db->query("SELECT tag FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

            if ($query->num_rows) {
                $tags = array_map("trim", explode(',', $query->row['tag']));
            }
        } else if (defined('VERSION') && version_compare(VERSION, '1.5.1', '>=')) {
            $query = $this->db->query("SELECT tag FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

            if ($query->num_rows) {
                $tags = $query->rows;
            }
        }

        return $tags;
    }

    public function similarProductsExist($product_id, $data = array()) {
        $sql = "SELECT similar_id FROM " . DB_PREFIX . "product_similar ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.similar_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE ps.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if ((int)$data['stock_only']) {
            $sql .= " AND p.quantity > '0'";
        }

        $sql .= " LIMIT 1";

        $query = $this->db->query($sql);

        if ($query->num_rows) {
            return 1;
        }

        if ((int)$data['auto_select']) {
            $sql = "";

            switch ($data['auto_select']) {
                case '1': // By category
                    if ($data['categories']) {
                        $sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2c.category_id IN (" . $data['categories'] . ") AND p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                    }
                    break;
                case '2': // Name fragment
                    if ((int)$data['substr_length']) {
                        $sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND pd.name LIKE CONCAT('%', (SELECT SUBSTR(pd.name, " . ((int)$data['substr_start'] + 1) . ", " . (int)$data['substr_length'] . ") FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') WHERE p.product_id = '" . (int)$product_id . "'), '%')";
                    }
                    break;
                case '3': // Model fragment
                    if ((int)$data['substr_length']) {
                        $sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.model LIKE CONCAT('%', (SELECT SUBSTR(model, " . ((int)$data['substr_start'] + 1) . ", " . (int)$data['substr_length'] . ") FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'), '%')";
                    }
                    break;
                case '4': // Name custom string
                    if ($data['custom_string']) {
                        $sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND pd.name LIKE '%" . $this->db->escape($data['custom_string']) . "%'";
                    }
                    break;
                case '5': // Model custom string
                    if ($data['custom_string']) {
                        $sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.model LIKE '%" . $this->db->escape($data['custom_string']) . "%'";
                    }
                    break;
                case '6': // Product tags
                    if (defined('VERSION') && version_compare(VERSION, '1.5.4', '>=')) {
                        $sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

                        $tags = $this->getProductTags($product_id);
                        $implode = array();

                        foreach ($tags as $tag) {
                            $implode[] = "pd.tag LIKE '%" . $this->db->escape($tag) . "%'";
                        }

                        if ($implode) {
                            $sql .= " AND (" . implode(' OR ', $implode) . ")";
                        }
                    } else if (defined('VERSION') && version_compare(VERSION, '1.5.1', '>=')) {
                        $sql = "SELECT pt1.product_id FROM " . DB_PREFIX . "product_tag pt1 JOIN " . DB_PREFIX . "product_tag pt2 ON (LOWER(pt1.tag) LIKE LOWER(pt2.tag)) LEFT JOIN " . DB_PREFIX . "product p ON (pt1.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (pt1.product_id = p2s.product_id) WHERE pt1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pt2.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pt1.product_id <> '" . (int)$product_id . "' AND pt2.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                    }
                    break;
                default:
                    break;
            }

            if ($sql && (int)$data['stock_only']) {
                $sql .= " AND p.quantity > '0'";
            }

            if ($sql) {
                $sql .= " LIMIT 1";

                $query = $this->db->query($sql);

                if ($query->num_rows) {
                    return 1;
                }
            }
        }

        return 0;
    }

    public function getSimilarProducts($product_id, $data = array()) {
        $product_data = array();

        $sort_data = array(
            'p.date_modified'   => 'date_modified',
            'p.date_added'      => 'date_added',
            'p.viewed'          => 'viewed',
            'p.quantity'        => 'quantity',
            'pd.name'           => 'name',
            'p.model'           => 'model',
            'p.sort_order'      => 'sort_order',
        );

        if (!in_array($data['sort'], array_keys($sort_data)) && $data['sort'] != 'random') {
            $global_sort_column = "sort_order";
            $inner_sort_column = "p.sort_order";
        } else if ($data['sort'] != 'random') {
            $global_sort_column = $sort_data[$data['sort']];
            $inner_sort_column = $data['sort'];
        } else {
            $global_sort_column = "random";
            $inner_sort_column = "";
        }

        //$start_time = microtime(true);
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM (";

        $sql .= "(SELECT similar_id AS product_id" . ($inner_sort_column ? ", " . $inner_sort_column : "") . " FROM " . DB_PREFIX . "product_similar ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.similar_id = p.product_id)";

        if ($data['sort'] == 'pd.name') {
            $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (ps.similar_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')";
        }

        $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE ps.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if ((int)$data['stock_only']) {
            $sql .= " AND p.quantity > '0'";
        }

        $sql .= ")";

        if ((int)$data['auto_select']) {
            $union_sql = "";
            switch ($data['auto_select']) {
                case '1': // By category
                    if ($data['categories']) {
                        $union_sql = "(SELECT DISTINCT p.product_id" . ($inner_sort_column ? ", " . $inner_sort_column : "") . " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

                        if ($data['sort'] == 'pd.name') {
                            $union_sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')";
                        }

                        $union_sql .= " WHERE p2c.category_id IN (" . $data['categories'] . ") AND p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                    }
                    break;
                case '2': // Name fragment
                    if ((int)$data['substr_length']) {
                        $union_sql = "(SELECT DISTINCT p.product_id" . ($inner_sort_column ? ", " . $inner_sort_column : "") . " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND pd.name LIKE CONCAT('%', (SELECT SUBSTR(pd.name, " . ((int)$data['substr_start'] + 1) . ", " . (int)$data['substr_length'] . ") FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') WHERE p.product_id = '" . (int)$product_id . "'), '%')";
                    }
                    break;
                case '3': // Model fragment
                    if ((int)$data['substr_length']) {
                        $union_sql = "(SELECT DISTINCT p.product_id" . ($inner_sort_column ? ", " . $inner_sort_column : "") . " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

                        if ($data['sort'] == 'pd.name') {
                            $union_sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')";
                        }

                        $union_sql .= " WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.model LIKE CONCAT('%', (SELECT SUBSTR(model, " . ((int)$data['substr_start'] + 1) . ", " . (int)$data['substr_length'] . ") FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'), '%')";
                    }
                    break;
                case '4': // Name custom string
                    if ($data['custom_string']) {
                        $union_sql = "(SELECT p.product_id" . ($inner_sort_column ? ", " . $inner_sort_column : "") . " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND pd.name LIKE '%" . $this->db->escape($data['custom_string']) . "%'";
                    }
                    break;
                case '5': // Model custom string
                    if ($data['custom_string']) {
                        $union_sql = "(SELECT p.product_id" . ($inner_sort_column ? ", " . $inner_sort_column : "") . " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

                        if ($data['sort'] == 'pd.name') {
                            $union_sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')";
                        }

                        $union_sql .= " WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.model LIKE '%" . $this->db->escape($data['custom_string']) . "%'";
                    }
                    break;
                case '6': // Product tags
                    if (defined('VERSION') && version_compare(VERSION, '1.5.4', '>=')) {
                        $union_sql = "(SELECT DISTINCT p.product_id" . ($inner_sort_column ? ", " . $inner_sort_column : "") . " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id <> '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

                        $tags = $this->getProductTags($product_id);
                        $implode = array();

                        foreach ($tags as $tag) {
                            $implode[] = "pd.tag LIKE '%" . $this->db->escape($tag) . "%'";
                        }

                        if ($implode) {
                            $union_sql .= " AND (" . implode(' OR ', $implode) . ")";
                        }
                    } else if (defined('VERSION') && version_compare(VERSION, '1.5.1', '>=')) {
                        $union_sql = "(SELECT DISTINCT pt1.product_id" . ($inner_sort_column ? ", " . $inner_sort_column : "") . " FROM " . DB_PREFIX . "product_tag pt1 JOIN " . DB_PREFIX . "product_tag pt2 ON (LOWER(pt1.tag) LIKE LOWER(pt2.tag)) LEFT JOIN " . DB_PREFIX . "product p ON (pt1.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (pt1.product_id = p2s.product_id)";

                        if ($data['sort'] == 'pd.name') {
                            $union_sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (pt1.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')";
                        }

                        $union_sql .= " WHERE pt1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pt2.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pt1.product_id <> '" . (int)$product_id . "' AND pt2.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= '" . date ("Y-m-d") . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                    }
                    break;
                default:
                    break;
            }

            if ($union_sql) {
                $sql .= " UNION " . $union_sql;

                if ((int)$data['stock_only']) {
                    $sql .= " AND p.quantity > '0'";
                }

                $sql .= ")";
            }
        }

        $sql .= ") AS tbl";

        if ($global_sort_column && $global_sort_column != "random") {
            $sql .= " ORDER BY " . $global_sort_column;

            if ($data['order'] == 'DESC') {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }
        }

        if ((int)$data['limit'] < 0) {
            $limit = 0;
        } else {
            $limit = (int)$data['limit'];
        }

        $start = (int)$data['start'];
        if ($limit > 0 && (int)$data['start'] + (int)$data['per_page'] > $limit || !(int)$data['per_page'] && $limit > 0) {
            $count = $limit - $start;
        } else {
            $count = (int)$data['per_page'];
        }

        if ($global_sort_column != 'random' && $count > 0) {
            $sql .= " LIMIT " . $start . "," . $count;
        }

        $query = $this->db->query($sql);

        $products = $query->rows;

        $product_count = $this->db->query("SELECT FOUND_ROWS() AS count");
        $this->productCount = ($product_count->num_rows) ? ((int)$product_count->row['count'] > $limit ? $limit : (int)$product_count->row['count']) : 0;

        if ($global_sort_column == 'random') {
            srand($data['seed']);
            shuffle($products);
            srand();
            if ($count) {
                $products = array_slice($products, $start, $count);
            }
        }

        foreach ($products as $product) {
            $product_data[$product['product_id']] = $this->model_catalog_product->getProduct($product['product_id']);
        }
        //$end_time = microtime(true);
        //$total_time = $end_time - $start_time;

        return $product_data;
    }

    public function getProductCount() {
        return $this->productCount;
    }
}
?>
