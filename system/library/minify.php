<?php
class OC_Minify {
		

	public function minify($output) {
		global $vqmod;
		global $config;
		$css_pattern = '~<link rel="stylesheet" type="text\/css" href="(.*?\.css)".*?\/?>~i';
		$js_pattern  = '/<script type="text\/javascript" src="(.*?\.js)">\s*?<\/script>/i';
		$all_js_scripts = array();
		$all_css_scripts = array();
		$dir_include = '';

		$parsed_url = parse_url(HTTP_SERVER);
		
		if(strlen($parsed_url['path']) >1) {
			$dir_include = substr($parsed_url['path'], 1, strlen($parsed_url['path']) -1);
		}
		
		if (preg_match('~.*<\/head>~im', $output) && !defined('DIR_CATALOG')) { // IF OUTPUT HAS A HTML HEAD SECTION

			$header_data_split = preg_split('~<\/head>~i', $output);
			$header_data = $header_data_split[0];
			$after_header_data = $header_data_split[1];
			
			$match_data = preg_replace('/<!--\[if.*?\]>\s*?.*?<!\[endif\]-->/is', '', $header_data);//REMOVE CONDITIONAL DATA
			$match_data = preg_replace('/<!--.*?-->/is', '', $match_data); //REMOVE COMMENTED DATA
			$match_data = preg_replace('~<script type="text\/javascript" src="http(.*?\.js)">\s*?<\/script>~i', '', $match_data); //REMOVE EXTERNAL JS
			$match_data = preg_replace('~<script type="text\/javascript" src="\/\/(.*?\.js)">\s*?<\/script>~i', '', $match_data); //REMOVE EXTERNAL JS
			$match_data = preg_replace('~<link rel="stylesheet" type="text\/css" href="http(.*?\.css)".*?\/?>~i', '', $match_data); //REMOVE EXTERNAL CSS
			$match_data = preg_replace('~<link rel="stylesheet" type="text\/css" href="\/\/(.*?\.css)".*?\/?>~i', '', $match_data); //REMOVE EXTERNAL JAVASCRIPT
			$match_data = preg_replace('~\?v\=1\.0\.11~i', '', $match_data); //SHOPPICA
			
			//JAVASCRIPT EXCLUDES
			$js_excludes = array();
			$js_exclude = $config->get('config_ipsjs_excludes');
			if (!empty($js_exclude)) {
				$js_excludes = explode($js_exclude, ",");
				foreach ($js_excludes as $jse) {
					$match_data = preg_replace('~<script type="text\/javascript" src=".*?' . trim($jse) . '.*?">\s*?<\/script>~i', '', $match_data); //REMOVE CUSTOM JS
				}
			}
			
			//CSS EXCLUDES
			$css_exclude = $config->get('config_ipscss_excludes');
			$css_excludes = array();
			if (!empty($css_exclude)) {
				$css_excludes = explode($css_exclude, ",");
				foreach ($css_excludes as $csse) {
					$match_data = preg_replace('~<link rel="stylesheet" type="text\/css" href=".*?' . trim($csse) . '.*?".*?\/?>~i', '', $match_data); //REMOVE CUSTOM CSS
				}
			}
				
			$match_data = preg_replace('~<link rel="stylesheet" type="text\/css" href="\/\/(.*?\.css)".*?\/?>~i', '', $match_data); //REMOVE EXTERNAL JAVASCRIPT
			
			$css_files = array();
			$js_files = array();

			preg_match_all($css_pattern, $match_data, $matches, PREG_OFFSET_CAPTURE);
			foreach($matches[1] as $match) { 
				$css_files[] = $match[0]; 
			}
			
			$css_base = $this->getCommonPath($css_files);
			//echo $css_base;
			foreach($css_files as $key => $cssfile) { 
				if (!empty($css_base)) {
					$css_base = (substr($css_base, -1) == '/') ? substr($css_base, 0, -1) : $css_base; //REMOVE TRAILING SLASH IF EXISTING
					$css_files[$key] = str_replace($css_base . '/', '',$cssfile); }
				$header_data = preg_replace('~<link rel="stylesheet" type="text\/css" href="' . str_replace('/','\/', $cssfile) . '".*?/?>~i', '', $header_data);
			}
			
			preg_match_all($js_pattern, $match_data, $matches, PREG_OFFSET_CAPTURE);
			foreach($matches[1] as $match) {
				$js_files[] = $match[0];
			}
			
			$js_base = $this->getCommonPath($js_files);
			foreach($js_files as $key => $jsfile) {
				if (!empty($js_base)) { $js_files[$key] = str_replace($js_base . '/', '',$jsfile); }
				$header_data = preg_replace('/<script type="text\/javascript" src="' . str_replace('/','\/', $jsfile) . '">\s*?<\/script>/', '', $header_data);
			}


			$js_files = array_unique($js_files);  //REMOVE DUPLICATES
			$css_files = array_unique($css_files); // REMOVE DUPLICATES
			
			$js_dir_include = $dir_include;
			if (empty($js_base) & !empty($dir_include)) {
				$js_dir_include = substr($dir_include, 0, strlen($dir_include) -1);				
			}
			
			if ($js_files) { //IF WE HAVE JAVASCRIPT FILES TO COMBINE
				$js_combined =  ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) ? $config->get('config_minify_cdn_https') : $config->get('config_minify_cdn_http'))
						. '/' . $dir_include . 'min/index.php?' . ((strlen($js_dir_include . $js_base)) ? 'b=' . $js_dir_include . $js_base .'&amp;' : '') . 'f=' . implode(',',$js_files);
				if (preg_match('~</title>~', $header_data)) { // IF PAGE HAD A TITLE INSERT AFTER
					$header_data = preg_replace('~</title>~', '</title><script type="text/javascript" src="' . $js_combined . '"></script>', $header_data);
				} else {
					$header_data = preg_replace('~<head>~', '<head><script type="text/javascript" src="' . $js_combined . '"></script>', $header_data);
				}
			}
			
			if ($css_files) { // IF WE HAVE CSS FILES TO COMBINE
				if (!empty($css_base) || !empty($dir_include)) {
					$css_combined = ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) ? $config->get('config_minify_cdn_https') : $config->get('config_minify_cdn_http'))
							. '/' . $dir_include . 'min/index.php?b=' . $dir_include . $css_base . '&amp;f=' . implode($css_files,',');
				} else {
					$css_combined = ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) ? $config->get('config_minify_cdn_https') : $config->get('config_minify_cdn_http'))
							. '/' . $dir_include . 'min/index.php?f=' . implode($css_files,',');			
				}
				if (preg_match('~</title>~', $header_data)) { // IF PAGE HAD A TITLE INSERT AFTER
					$header_data = preg_replace('~</title>~', '</title><link type="text/css" rel="stylesheet" href="' . $css_combined . '" media="all" />', $header_data);
				} else {
					$header_data = preg_replace('~<head>~', '<head><link type="text/css" rel="stylesheet" href="' . $css_combined . '" media="all" />', $header_data);				
				}
			}
			
			return $header_data . '</head>' . $after_header_data;
		} else {
			return $output;
		}
		
	}
	
	private function getCommonPath(array $paths) {
		$count = count($paths);
		if (empty($paths)) { return ''; }
		if ($count == 1) { return dirname($paths[0]) . '/'; }
		$_paths = array();
		for ($i = 0; $i < $count; $i++) { $_paths[$i] = explode('/', $paths[$i]); if (empty($_paths[$i][0])) { $_paths[$i][0] = '/'; } }
		$common = ''; $done = FALSE; $j = 0; $count--;
		while (!$done) { for ($i = 0; $i < $count; $i++) { if ($_paths[$i][$j] != $_paths[$i+1][$j]) { $done = TRUE; break; } } if (!$done) { $common .= $_paths[0][$j].'/'; } $j++; }
		return substr($common, 0, strlen($common) -1);
	}
	
	private function strposa($haystack, $needles=array(), $offset=0) {
		$chr = array();
		foreach($needles as $needle) {
			$res = strpos($haystack, $needle, $offset);
			if ($res !== false) $chr[$needle] = $res;
		}
		if(empty($chr)) return false;
		return min($chr);
	}
}
?>