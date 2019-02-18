<?php
class ModelWxSmartcache extends Model {
	public function clear($route = '', $id = '', $redirect = false) {

		if ($route != '' & $id != '') {		
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "smart_cache WHERE route = '" . $route . "' AND page_id = '" . $id . "'");
			if ($query->num_rows) {
				foreach ($query->rows as $row) {
					$this->delfile($row['cache_file']);
					$this->db->query("DELETE FROM " . DB_PREFIX . "smart_cache WHERE cache_id = '" . $row['cache_id'] . "'");
				}
			}
		} else if ($route != '') {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "smart_cache WHERE route = '" . $route . "'");
			if ($query->num_rows) {
				foreach ($query->rows as $row) {
					$this->delfile($row['cache_file']);
					$this->db->query("DELETE FROM " . DB_PREFIX . "smart_cache WHERE cache_id = '" . $row['cache_id'] . "'");
				}
			}
		} else {
			$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "smart_cache");
			$homedir = '/home';
			$cachedir = realpath(DIR_SYSTEM . '/../');
			if (substr($cachedir, 0, strlen($homedir)) == $homedir) {
				$cachedir = '/cache' . substr($cachedir, strlen($homedir));
			} 
			$pagefiles = glob($cachedir . '/*.*');
			if ($pagefiles) {
				foreach($pagefiles as $pagefile){
					$this->delfile($pagefile);
				}
			}
		}
		
		if ($redirect) {
			$this->load->language('wx/clearcache');
			$this->session->data['success'] = $this->language->get('text_success_page');
			$this->redirect($this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
	
	private function delfile($filename) {
		if (is_file($filename)) {
			@unlink($filename);
		}
	}
	
	private function deldir($dirname) {
		if(file_exists($dirname)) {
			if(is_dir($dirname)) {
				$dir=opendir($dirname);
				while($filename=readdir($dir)) {
					if($filename!="." && $filename!="..") {
						$file=$dirname."/".$filename;
						$this->deldir($file); 
					}
				}
				closedir($dir);  
				rmdir($dirname);
			} else {
				@unlink($dirname);
			}			
		}
	}

}
?>