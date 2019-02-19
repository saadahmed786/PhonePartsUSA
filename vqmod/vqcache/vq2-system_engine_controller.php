<?php
abstract class Controller {
	protected $registry;	
	protected $id;
	protected $layout;
	protected $template;
	protected $children = array();
	protected $data = array();
	protected $output;
	
	public function __construct($registry) {
		$this->registry = $registry;

                $this->load->library('play');
                $play = new Play($registry);
                $registry->set('play', $play);
            
 $this->load->library('openbay'); $registry->set('openbay', new Openbay($registry));
                if (!defined('HTTPS_CATALOG') && defined('HTTP_CATALOG')) {
                    define('HTTPS_CATALOG', HTTP_CATALOG);
                } 
                

                $this->load->library('ebay');
                $ebay = new Ebay($registry);
                $registry->set('ebay', $ebay);
            

                $this->load->library('amazonus');
                $amazonus = new Amazonus($registry);
                $registry->set('amazonus', $amazonus);
            

                $this->load->library('amazon');
                $amazon = new Amazon($registry);
                $registry->set('amazon', $amazon);
            
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
			
	protected function forward($route, $args = array()) {
		return new Action($route, $args);
	}

	protected function redirect($url, $status = 302) {
		header('Status: ' . $status);
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
		exit();				
	}
	
	protected function getChild($child, $args = array()) {
		$action = new Action($child, $args);
		$file = $action->getFile();
		$class = $action->getClass();
		$method = $action->getMethod();
	
		if (file_exists($file)) {

	global $cache;
	$styles['pre']   = $this->document->getStyles();
	$scripts['pre']  = $this->document->getScripts();
	
	$cached_children = $this->config->get('optimizer_page_cached_routes');
	if (!is_array($cached_children)) {
		$cached_children = array();
	}
	$cached_output = false;
	if (!defined("DIR_CATALOG")) {
		$https = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 1 : 0;
		$cached_name = 'wxblc.' . str_replace('/', '-', $child)  . '.' . $https . '.' . $this->config->get('config_template') . '.' . (int)$this->currency->getCode() . '.' . $this->customer->getCustomerGroupId() . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . (!empty($args) ? '.' . md5(serialize($args)) : '');
		$cached_output = $this->cache->get($cached_name);
	}
	
	if (!defined("DIR_CATALOG") && in_array($child, $cached_children) && $cached_output) {
	
		$style_cache = $cache->get($cached_name . ".styles");
		if (isset($style_cache['scripts'])) {
			foreach($style_cache['scripts'] as $script) {
				$this->document->addScript($script);
			}
		}
		if (isset($style_cache['styles'])) {
			foreach($style_cache['styles'] as $style) {
				$this->document->addStyle($style['href'], $style['rel'], $style['media']);
			}
		}

		return $cached_output;
	} else {
	
			require_once(\VQMod::modCheck($file));

			$controller = new $class($this->registry);
			
			$controller->$method($args);
			
			
		if (!defined("DIR_CATALOG") && in_array($child, $cached_children)) {
			$cache->set($cached_name, $controller->output);

			$style_cache      = array();
			$styles['post']   = $this->document->getStyles();
			$scripts['post']  = $this->document->getScripts();
			
			if (count($styles['post']) !==  count($styles['pre'])) {
				foreach ($styles['pre'] as $key => $style) {
					unset($styles['post'][$key]);
				}
				if (!empty($styles['post'])) {
					$style_cache['styles'] = $styles['post'];
				}
			}
			
			if  (count($scripts['post']) !==  count($scripts['pre'])) {
				$style_cache['scripts'] = array_diff($scripts['post'],$scripts['pre']);
			}
			
			if (!empty($style_cache)) {
				$cache->set($cached_name . ".styles", $style_cache);			
			}					
		}
		return $controller->output;
	}
	
		} else {
			trigger_error('Error: Could not load controller ' . $child . '!');
			exit();					
		}		
	}
	
	protected function render() {

		foreach ($this->children as $child) {
			$this->data[basename($child)] = $this->getChild($child);
		}
		// print_r($this->data);
		
		if (file_exists(DIR_TEMPLATE . $this->template)) {
			extract($this->data);
			
      		ob_start();
      
	  		require(\VQMod::modCheck(DIR_TEMPLATE . $this->template));
      
	  		$this->output = ob_get_contents();

      		ob_end_clean();
      		
			return $this->output;
    	} else {
			// echo 'error ' . DIR_TEMPLATE . $this->template ;
			trigger_error('Error: Could not load template ' . DIR_TEMPLATE . $this->template . '!');
			exit();				
    	}
	}
}
?>