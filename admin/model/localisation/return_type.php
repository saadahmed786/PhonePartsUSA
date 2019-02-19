<?php

class ModelLocalisationReturnType extends Model {
  public function addReturnType($data) {
    foreach ($data['return_type'] as $language_id => $value) {
      if (isset($return_type_id)) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "return_type` SET return_type_id = '" . (int)$return_type_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
      } else {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "return_type` SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

        $return_type_id = $this->db->getLastId();
      }
    }

    $this->cache->delete('return_type');
  }

  public function editReturnType($return_type_id, $data) {
    $this->db->query("DELETE FROM `" . DB_PREFIX . "return_type` WHERE return_type_id = '" . (int)$return_type_id . "'");

    foreach ($data['return_type'] as $language_id => $value) {
      $this->db->query("INSERT INTO `" . DB_PREFIX . "return_type` SET return_type_id = '" . (int)$return_type_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
    }

    $this->cache->delete('return_type');
  }

  public function deleteReturnType($return_type_id) {
    $this->db->query("DELETE FROM `" . DB_PREFIX . "return_type` WHERE return_type_id = '" . (int)$return_type_id . "'");

    $this->cache->delete('return_type');
  }

  public function getReturnType($return_type_id) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "return_type` WHERE return_type_id = '" . (int)$return_type_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

    return $query->row;
  }

  public function getReturnTypes($data = array()) {
    if ($data) {
      $sql = "SELECT * FROM `" . DB_PREFIX . "return_type` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

      $sql .= " ORDER BY name";

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

        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
      }

      $query = $this->db->query($sql);

      return $query->rows;
    } else {
      $return_type_data = $this->cache->get('return_type.' . (int)$this->config->get('config_language_id'));

      if (!$return_type_data) {
        $query = $this->db->query("SELECT return_type_id, name FROM `" . DB_PREFIX . "return_type` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

        $return_type_data = $query->rows;

        $this->cache->set('return_type.' . (int)$this->config->get('config_language_id'), $return_type_data);
      }

      return $return_type_data;
    }
  }

  public function getReturnTypeDescriptions($return_type_id) {
    $return_type_data = array();

    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "return_type` WHERE return_type_id = '" . (int)$return_type_id . "'");

    foreach ($query->rows as $result) {
      $return_type_data[$result['language_id']] = array('name' => $result['name']);
    }

    return $return_type_data;
  }

  public function getTotalReturnTypes() {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return_type` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

    return $query->row['total'];
  }
}

?>
