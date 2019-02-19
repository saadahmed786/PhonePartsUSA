<?php
class ModelModuleautoseo extends Model {
    public function generateCategories () {
        $categories = $this->getCategories();
        $slugs = array();
        foreach ($categories as $category) {

            $slug = $uniqueSlug = $this->makeSlugs($category['name']);
            $index = 1;
            while (in_array($uniqueSlug, $slugs)) {
                $uniqueSlug = $slug . '-' . $index ++;
            }
            $slugs[] = $uniqueSlug;
            $query_url = $this->get_categories_query($category['category_id'],$category['parent_id']);
//            $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = '".$query_url."'");
            $this->delete($query_url);
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '".$query_url."', keyword = '" . $this->db->escape($uniqueSlug) . "'");
        }
    }
    public function generateProducts () {
        $products = $this->getProducts();
        $slugs = array();
        foreach ($products as $product) {
            $slug = $uniqueSlug = $this->makeSlugs($product['name']);
            $index = 1;
           // while (in_array($uniqueSlug, $slugs)) {
                $uniqueSlug = $slug . '-' . $product['product_id'];
           // }
            $slugs[] = $uniqueSlug;
            $query_url = $this->get_product_query($product['product_id']);
//            $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = '".$query_url."'");
            $this->delete($query_url);
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '".$query_url."', keyword = '" . $this->db->escape($uniqueSlug) . "'");
        }
    }
    
    
    private function getCategories () {
        $query = $this->db->query("SELECT c.category_id, cd.name,c.parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
        return $query->rows;
    }
    private function getProducts () {
        $query = $this->db->query("SELECT p.product_id, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY p.product_id ASC");
        return $query->rows;
    }
    // Taken from http://code.google.com/p/php-slugs/
    private function my_str_split ($string) {
        $slen = strlen($string);
        for ($i = 0; $i < $slen; $i ++) {
            $sArray[$i] = $string{$i};
        }
        return $sArray;
    }
    private function noDiacritics ($string) {

        $i =  strpos($string, '(');
        if($i){
        	
        	$string = substr($string,0,$i);
        }
        
        $cyrylicFrom = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $cyrylicTo = array('A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia');
        $from = array("Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž");
        $to = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");
		$f  = array("&",'(',')','.');
		$t  = array(" ",' ',' ',' ');
        $from = array_merge($from, $cyrylicFrom,$f);
        $to = array_merge($to, $cyrylicTo,$t);
        $newstring = str_replace($from, $to, $string);
        return $newstring;
    }
    private function makeSlugs ($string, $maxlen = 0) {
        $newStringTab = array();
        $string = strtolower($this->noDiacritics($string));
        if (function_exists('str_split')) {
            $stringTab = str_split($string);
        } else {
            $stringTab = $this->my_str_split($string);
        }
        $numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-");
        foreach ($stringTab as $letter) {
            if (in_array($letter, range("a", "z")) || in_array($letter, $numbers)) {
                $newStringTab[] = $letter;
            } elseif ($letter == " ") {
                $newStringTab[] = "-";
            }
        }
        if (count($newStringTab)) {
            $newString = implode($newStringTab);
            if ($maxlen > 0) {
                $newString = substr($newString, 0, $maxlen);
            }
            $newString = $this->removeDuplicates('--', '-', $newString);
        } else {
            $newString = '';
        }
        return $newString;
    }
    private function removeDuplicates ($sSearch, $sReplace, $sSubject) {
        $i = 0;
        do {
            $sSubject = str_replace($sSearch, $sReplace, $sSubject);
            $pos = strpos($sSubject, $sSearch);
            $i ++;
            if ($i > 100) {
                die('removeDuplicates() loop error');
            }
        } while ($pos !== false);
        return $sSubject;
    }
    
    public function get_categories_query($category_id,$parent_id='',$q = array()){
    	
    	$q[]=$category_id;
    	if(!$parent_id){
    		
    		$query = $this->db->query('SELECT `parent_id` FROM ' . DB_PREFIX . 'category WHERE `category_id` ='.$category_id.'');
    		if($query->num_rows > 0){
    			$result = $query->row;
    			$parent_id = $result['parent_id'];
    		}
    	}
    	
    	if($parent_id>0){
	    	$query = $this->db->query('SELECT `category_id`,`parent_id` FROM ' . DB_PREFIX . 'category WHERE `category_id` ='.$parent_id.'');
	    	$result = $query->row;
	    	
    	}else{
    		$result = array();
    	}
    	if(count($result)>0){
    		
    		return  $this->get_categories_query($result['category_id'],$result['parent_id'],$q);
    		
    	}
    	asort($q);
    	return 'category_id='.join('_',$q);
    }
    
    public function get_product_query($product_id){
    	
    	$query = $this->db->query('SELECT `c`.`category_id`,`c`.`parent_id` FROM ' . DB_PREFIX . 'product_to_category AS `pc` join '.DB_PREFIX.'category AS
    	                           `c` ON  `pc`.`category_id` = `c`.`category_id` WHERE `product_id` = '.$product_id.'');
    	
    	$result = $query->row;
    	if($result){
	    	$c_url = $this->get_categories_query($result['category_id'],$result['parent_id']);
	    	
	    	return 'product_id='.$product_id.'&'.$c_url;
    	}else{
    		return 'product_id='.$product_id.'&';
    	}
    }
    
    // add news categories seo url
    public function add_new_category($data){
    	
    	
    	
    	foreach ($data['category_description'] as $language_id => $value) {
    		
    		$name        =  $value['name'];
	    	$category_id = $this->getLastId_category();
	    	$category_id = $category_id['category_id'];
	    	$parent_id   = (int)$data['parent_id'];
	    	
	    	 $uniqueSlug = $this->makeSlugs($name);
	    	 $slugs = $this->select_keywords();
	    	 $index = 1;
	    	 if(in_array($uniqueSlug, $slugs)) {
	
	    	 	$uniqueSlug = $uniqueSlug . '-' . $index;
	         
	    	 }   	
	    	$query_url = $this->get_categories_query($category_id,$parent_id);
	//    	$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = '".$query_url."'");	
			$this->delete($query_url);    	 
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '".$query_url."', keyword = '" . $this->db->escape($uniqueSlug) . "'");  
		}
		return true; 
    	 
    }
    
    
    // add news product seo url
    public function add_new_product($data){
    	
   	    $product_id = $this->getLastId_product();
	    $product_id = $product_id['product_id'];
    	foreach ($data['product_description'] as $language_id => $value) {
    		
	    	$name = $value['name'];
	    	$uniqueSlug = $this->makeSlugs($name);
	    	$slugs = $this->select_keywords();
	    	
			
	    	$index = 1;
	     	if(in_array($uniqueSlug, $slugs)) {
	
	    	 	$uniqueSlug = $uniqueSlug . '-' . $index;
	         
	    	}   
	    	$query_url = $this->get_product_query($product_id);
	    	if($query_url){

	    		$this->delete($query_url);
	       	 	$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '".$query_url."', keyword = '" . $this->db->escape($uniqueSlug) . "'");	    		
	    	}
	    	//$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = '".$query_url."'");	
	        
        }
  		return true;
    }
    
    // get last category id
    // 
    public function getLastId_category(){
    	
    	$query = $this->db->query('SELECT category_id from '.DB_PREFIX.'category order by category_id DESC limit 1');
    	$result = $query->row;
    	return $result;
    }
    
    // get last product id
    // 
    public function getLastId_product(){
    	
    	$query = $this->db->query('SELECT product_id from '.DB_PREFIX.'product order by product_id DESC limit 1');
    	$result = $query->row;
    	return $result;
    }

    
    // if flag eq 1 select category,eq 2 select product_id
    public function select_keywords(){
    	
   
    		
    	$query = $this->db->query('select keyword from '.DB_PREFIX.'url_alias');
    
    	
    	$result = $query->rows;
    	$keyword = array();
    	foreach ($result as $v){

    		$keyword[] = $v['keyword'];
    	}
    	
      	return $keyword;
    }
    
    public function delete($query_url = ''){
    	
    	$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = '".$query_url."'");	
    	return true;
    }
    
    
}