<?php
class Response {
protected $registry;
	private $headers = array(); 
	private $level = 1;
	private $output;
	

	public function __construct($reg = '') {
		$this->registry = $reg;
		if (version_compare(VERSION, '2.2.0', '<')) {
			global $registry;
			$this->registry = $registry;
		}
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}		
		
	public function addHeader($header) {
		$this->headers[] = $header;
	}

	public function redirect($url) {
		header('Location: ' . $url);
		exit;
	}
	
	public function setCompression($level) {
		$this->level = $level;
	}
		
	public function setOutput($output) {
		$this->output = $output;
	}

	private function compress($data, $level = 0) {
		if (!defined('DIR_CATALOG')) $data = $this->getOptimizedOutput($data);
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
			$encoding = 'gzip';
		}

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
			$encoding = 'x-gzip';
		}

		if (!isset($encoding) || ($level < -1 || $level > 9)) {
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (headers_sent()) {
			return $data;
		}

		if (connection_status()) {
			return $data;
		}

		$this->addHeader('Content-Encoding: ' . $encoding);

		return gzencode($data, (int)$level);
	}


		function getOptimizedOutput($output) {

			$min = new OC_Minify;

			if (defined('OPTIMIZER_MINIFY_JAVASCRIPT') && OPTIMIZER_MINIFY_JAVASCRIPT == true) $output = $min->minifyJavascript($output);
			if (defined('OPTIMIZER_MINIFY_CSS') && OPTIMIZER_MINIFY_CSS == true) $output = $min->minifyCSS($output);
		
			if (defined('WX_CDN_STATUS') && WX_CDN_STATUS == true) {
				$cdn_domain = (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) ? WX_CDN_HTTPS : WX_CDN_HTTP;
				$http_image = (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) ? HTTPS_SERVER . 'image/' : HTTP_SERVER . 'image/';
				$cdn_path = parse_url(HTTP_SERVER, PHP_URL_PATH);
				if (strlen($cdn_path) > 1) {
					$cdn_domain .= $cdn_path;
					$cdn_domain = trim($cdn_domain, '/');
				}
				
				if (defined('WX_CDN_IMAGES') && WX_CDN_IMAGES == true) {
					$output = str_replace($http_image, $cdn_domain . '/image/', $output);
					$output = str_replace('src="image/', 'src="' . $cdn_domain . '/image/', $output);
					if ($this->config->get('config_store_id')) {
						if ($this->config->get('config_ssl')) {
							$output = str_replace($this->config->get('config_ssl') . 'image/', $cdn_domain . '/image/', $output);
							$output = str_replace('src="image/', 'src="' . $cdn_domain . '/image/', $output);
						}
						if ($this->config->get('config_url')) {
							$output = str_replace($this->config->get('config_url') . 'image/', $cdn_domain . '/image/', $output);
							$output = str_replace('src="image/', 'src="' . $cdn_domain . '/image/', $output);
						}
					}
					$output = str_replace('src="/image/data', 'src="' . $cdn_domain . '/image/data', $output);
					$output = str_replace('src="catalog/view/theme/' . $this->config->get("config_template") . '/image/', 'src="' . $cdn_domain . '/catalog/view/theme/' . $this->config->get("config_template") . '/image/', $output);
					$output = str_replace('src="catalog/view/theme/default/image/', 'src="' . $cdn_domain . '/catalog/view/theme/default/image/', $output);
				}
				if (WX_CDN_JS) {
					$output = str_replace('src="catalog/view/javascript/', 'src="' . $cdn_domain . '/catalog/view/javascript/', $output);
				}
				if (WX_CDN_CSS) {
					$output = str_replace('href="catalog/view/theme/' . $this->config->get("config_template") . '/stylesheet/', 'href="' . $cdn_domain . '/catalog/view/theme/' . $this->config->get("config_template") . '/stylesheet/', $output);
					$output = str_replace('href="catalog/view/theme/default/stylesheet/', 'href="' . $cdn_domain . '/catalog/view/theme/default/stylesheet/', $output);
				}
			}

			if (defined('OPTIMIZER_MINIFY_HTML') && OPTIMIZER_MINIFY_HTML == true && json_decode($output) == null) {
				preg_match_all('!(<script.*?>.*?</script>)!is',$output,$pre);
				$output = preg_replace('!(<script.*?>.*?</script>)!is', '#pre#', $output);
				$output = preg_replace('/[\r\n\t]+/', ' ', $output);
				$output = preg_replace('/>\s+</', '><', $output);
				$output = preg_replace('/\s+/', ' ', $output);
				if (!empty($pre[0])) {
					foreach ($pre[0] as $original) {
						$output = preg_replace('!#pre#!', $original, $output,1);
					}
				}
			}

			$current_route = isset($this->request->get['route']) ? $this->request->get['route'] : (isset($this->request->get['_route_']) ? $this->request->get['_route_'] : '');
			if (OptimizerPageCache::cachable($current_route)) {
				OptimizerPageCache::write($output);
				

			}
			
			if (defined('OPTIMIZER_JAVASCRIPT_DEFER') && OPTIMIZER_JAVASCRIPT_DEFER) {
				$output = preg_replace('~type="text\/javascript"~', 'type="text/psajs" pagespeed_orig_type="text/javascript"', $output );
				$output = str_replace('<script>', '<script type="text/psajs">', $output);
				$output = str_replace('text/psa-donotdefer', 'text/javascript', $output);
				$output = str_replace('</body>', '<script type="text/javascript" src="catalog/view/javascript/page_speed/js_defer.js" async defer></script></body>',$output);
			}
			
			return $output;
		}
		
	public function output() {
		
		if ($this->output) {
			
			if ($this->level) {
				$output = $this->compress(minify($this->output,0), $this->level);
			} else {
				$output = $this->output;
				if (!defined('DIR_CATALOG')) $output = $this->getOptimizedOutput($output);
			}

			if (!headers_sent()) {
				foreach ($this->headers as $header) {
					header($header, true);
				}
			}

			echo $output;
		}
	}

}
define('SAFE', 1);
define('EXTREME', 2);
define('EXTREME_SAVE_COMMENTS', 4);
define('EXTREME_SAVE_PRE', 3);

function minify($html, $level=2)
{
	$level=1;
	switch((int)$level)
	{
		case 0:
			//Don't minify
		break;
		
		case 1: //Safe Minify
		
			// Replace all whitespace characters between tags with a single space
			$html = preg_replace("`>\s+<`", "> <", $html);
		break;
		
		case 2: //Extreme Minify
			
			// Placeholder to save conditional comment hack, <pre> and <code> tags
			$place_holders = array(
				'<!-->' => '_!--_',
			);
			
			//Set placeholders
			$html = strtr($html, $place_holders);
			
			// Remove all normal comments - save conditionals
			$html = preg_replace('/<!--[^(\[|(<!))](.*)-->/Uis', '', $html);
			
			// Replace all whitespace characters with a single space
			$html = preg_replace("`\s+`", " ", $html);
			
			// Remove the spaces between adjacent html tags
			$html = preg_replace("`> <`", "><", $html);
			
			// Replace space between adjacent a tags for readability
			$html = str_replace("</a><a", "</a> <a", $html);
			
			// Restore placeholders
			$html = strtr($html, array_flip($place_holders));
		break;
		
		case 3: //Extreme, save pre and code tags
			// Placeholder to save conditional comment hack, <pre> and <code> tags
			$place_holders = array(
				'<!-->' => '_!--_',
				'<pre>' => '_pre_',
				'</pre>' => '_/pre_',
				'<code>' => '_code_',
				'</code>' => '_/code_'
			);
			
			//Set placeholders
			$html = strtr($html, $place_holders);
			
			// Remove all normal comments - save conditionals
			$html = preg_replace('/<!--[^(\[|(<!))](.*)-->/Uis', '', $html);
			
			// Replace all whitespace characters with a single space
			$html = preg_replace(">`\s+`<", "> <", $html);
			
			// Remove the spaces between adjacent html tags
			$html = preg_replace("`> <`", "><", $html);
			
			// Replace space between adjacent a tags for readability
			$html = str_replace("</a><a", "</a> <a", $html);
			
			// Restore placeholders
			$html = strtr($html, array_flip($place_holders));
		
		break;
		
		case 4: //Extreme minify, save comments
			
			// Replace all whitespace characters with a single space
			$html = preg_replace("`\s+`", " ", $html);
			
			// Remove spaces between adjacent html tags
			$html = preg_replace("`> <`", "><", $html);
			
			// Restore space between ajacent a tags
			$html = str_replace("</a><a", "</a> <a", $html);
		break;
	}

	//Normalize ampersands
	//$html = str_replace("&amp;", "&", $html);
	//$html = str_replace("&", "&amp;", $html);
	
	//Replace common entities with more compatible versions
	$replace = array(
		'&nbsp;' => '&#160;',
		'&copy;' => '&#169;',
		'&acirc;' => '&#226;',
		'&cent;' => '&#162;',
		'&raquo;' => '&#187;',
		'&laquo;' => '&#171;'
	);
	
	//$html = strtr($html, $replace);
	
	//Return minified html
	return $html;
}
?>