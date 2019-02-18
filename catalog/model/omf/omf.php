<?php
class ModelOmfOmf extends Model {
   public function isBlogManagerInstalled() {
      $query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."blog_setting'");
      
      if ($query->num_rows) {
		return true;
      } else {
      	return false;
      }
   }
}
?>