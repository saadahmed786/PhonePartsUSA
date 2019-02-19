<?php
class ControllerWxCron extends Controller {
	public function index() {
		set_time_limit(0);
		if (DEBUG) {
			error_reporting(E_ALL);
			ini_set('display_errors', '1');
			ini_set('log_errors', '1');
			set_error_handler('cron_error_handler');
			ob_end_flush();
			echo "Starting Runtime\n";
		}
		$time_and_window = time() + 3600;
		$scripts         = array();
		
		$result = $this->db->query("SELECT * FROM " . DB_PREFIX . "wx_cron WHERE run_time <= " . $time_and_window . " AND paused='0'");

		if($result->num_rows) {
			foreach ($result->rows AS $row) {
				foreach ($row AS $key => $value) $$key = $value;    
				$new_time = $run_time + $time_interval;
				$scripts[] = array("script" => $scriptpath, "id" => $wx_cron_id);
				if (DEBUG) echo "Found script to run: $scriptpath - id=$wx_cron_id\n";
				$this->db->query("UPDATE " . DB_PREFIX . "wx_cron SET run_time='" . $new_time . "', last_run='" . $run_time . "' WHERE wx_cron_id = '" . $wx_cron_id . "'");
				if($run_once) {
					//$this->db->query("DELETE FROM " . DB_PREFIX . "wx_cron WHERE wx_cron_id='" . $wx_cron_id . "'");
				}
			}
		}

		foreach ($scripts as $script)
			$this->execute($script['script'],$script['id']);
			
		exit();

	}

	private function execute($script,$id,$buffer_output=1) {
	
		if(($buffer_output) AND (!DEBUG)) ob_start();//buffer output 
		$cron = new wxCronJob($script);
	
		//if ($cron->running($id)) {
			if (DEBUG) echo "Now running ID {$id}: {$script}\n";
			$start_time = microtime(true);
			
			if(version_compare(VERSION, '2.0.0', '<')) {
				if (DEBUG) echo "Using getChild() Method\n";
				$this->getChild($script);
			} else {
				if (DEBUG) echo "Using load->controller() Method\n";
				$output = $this->load->controller($script);
			}
			
			$cron->execution_time=number_format( (microtime(true) - $start_time), 5 )." seconds.";
			$cron->stop($id);
		//}
	}

	function fire_remote_script($url) {
		$url_parsed = parse_url($url);
		$scheme     = $url_parsed["scheme"];
		$host       = $url_parsed["host"];
		$port       = isset($url_parsed["port"]) ? $url_parsed["port"] : 80;
		$path       = isset($url_parsed["path"]) ? $url_parsed["path"] : "/";
		$query      = isset($url_parsed["query"]) ? $url_parsed["query"] : "";
		$user       = isset($url_parsed["user"]) ? $url_parsed["user"] : "";
		$pass       = isset($url_parsed["pass"]) ? $url_parsed["pass"] : "";
		$referer    = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$buffer     = "";
		if (function_exists('curl_exec')) {
			$ch = curl_init($scheme."://".$host.$path);
			curl_setopt($ch, CURLOPT_PORT, $port);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch, CURLOPT_FAILONERROR,1); // true to fail silently
			curl_setopt($ch, CURLOPT_AUTOREFERER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$query);
			curl_setopt($ch, CURLOPT_REFERER,$referer);
			curl_setopt($ch, CURLOPT_USERPWD,$user.":".$pass);
			$buffer = curl_exec($ch);
			curl_close($ch);
		} else if ($fp = @fsockopen($host, $port, $errno, $errstr, 30)) {
			$header = "POST $path HTTP/1.0\r\nHost: $host\r\nReferer: $referer\r\n"
					 ."Content-Type: application/x-www-form-urlencoded\r\n"
					 ."Content-Length: ". strlen($query)."\r\n";
			if($user!= "") $header.= "Authorization: Basic ".base64_encode("$user:$pass")."\r\n";
			$header.= "Connection: close\r\n\r\n";
			fputs($fp, $header);
			fputs($fp, $query);
			if ($fp) while (!feof($fp)) $buffer.= fgets($fp, 8192);
				@fclose($fp);
		}
		echo $buffer;
	}	
}


function cron_error_handler($errno, $errstr, $errfile, $errline) {
	global $log, $config;
	
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}
		
	echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';

	return true;
}
	
class wxCronJob extends Controller {
	public $script;
	public $output;
	public $execution_time;

	public function __construct($script = '') {
		if (!empty($script)) {
			$this->script = $script;
		}
	}
	
	public function running($id) {
		$this->db->query("UPDATE " . DB_PREFIX . "wx_cron SET currently_running = '1' where wx_cron_id = '" . $id . "'");
		return true; //$this->db->countAffected();
	}

	public function stop($id) {
		$this->db->query("UPDATE " . DB_PREFIX . " SET currently_running='0' where wx_cron_id='" . $id . "'");
		/*
		if (ERROR_LOG) { //save log to db
			$now = time();
			
			$query="INSERT INTO " . DB_PREFIX . "wx_cron_logs (`id`, `date_added`,`script`, `output`, `execution_time`)
					VALUES (NULL,'$now', '$this->script','$this->output','$this->execution_time') ";
			$result = $dbc->prepare($query);
			$result = $dbc->execute($result);
			if (DEBUG) echo "<br>QUERY to insert data to ".LOGS_TABLE." table:<br>$query (debug ref. 3.9c)<br>";
		} 
		*/
	}

	public function clear($id) {
		$this->db->query("UPDATE ".PJS_TABLE." SET currently_running = '0' where id='$id' ");
	}	
}

