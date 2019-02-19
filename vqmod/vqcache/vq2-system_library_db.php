<?php
class DB {
public $cacheDir;
	private $driver;

	public function __construct($driver, $hostname, $username, $password, $database) {
$this->cacheDir = 'general';
		$file = DIR_DATABASE . $driver . '.php';

		if (file_exists($file)) {
			require_once(\VQMod::modCheck($file));

			$class = 'DB' . $driver;

			$this->driver = new $class($hostname, $username, $password, $database);

		    $gm_localtime = strtotime(gmdate('Y-m-d H:i:s'));    // GET LOCAL TIME IN GMT/UTC
		    $localtime    = strtotime(date('Y-m-d H:i:s'));      // GET LOCAL TIME SETTING
			
		    //ADJUST MYSQL SERVER TO ADJUSTED TIME SETTINGS
			$hour_adjust   = intval(($localtime - $gm_localtime) / 3600);  // GET TIME DIFFERENCE IN HOURS
			$minute_adjust = intval(sprintf("%02s", abs(($hour_adjust * 60) % 60)));
			
			
			if (strlen($hour_adjust) == 1) {
				$hour_adjust = '0' . $hour_adjust;
			}
			if (strlen($minute_adjust) == 1) {
				$minute_adjust = '0' . $minute_adjust;
			}
			
			/*
			$start_date    = new DateTime(gmdate('Y-m-d H:i:s'));                        // GET LOCAL TIME IN GMT/UTC
			$since_start   = $start_date->diff(new DateTime(date('Y-m-d H:i:s')));      // GET LOCAL TIME SETTING
			$hour_adjust   = $since_start->h;
			$minute_adjust = $since_start->i;

			if (strlen($hour_adjust) == 1) {
				$hour_adjust = '0' . $hour_adjust;
			}
			if (strlen($minute_adjust) == 1) {
				$minute_adjust = '0' . $minute_adjust;
			}
			*/
			
		    if ($hour_adjust > 0) {
		       $adjust = "SET time_zone = '+" . $hour_adjust . ":" . $minute_adjust . "'";
		    } elseif ($hour_adjust < 0) {
		       $adjust = "SET time_zone = '" . $hour_adjust . ":" . $minute_adjust . "'";
		    } else {
		       $adjust = "SET time_zone = '+0:00'";
		    }
		    $this->query($adjust);
			
		} else {
			exit('Error: Could not load database driver type ' . $driver . '!');
		}
	}


	public function cachedQuery($sql) {
		
		$folder = DIR_CACHE . 'sql/' . $this->cacheDir . '/';
		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}
			
		$filename = $folder . md5($sql);

		if (!file_exists($filename)) {
			$query =  $this->query($sql);
			$handle = fopen($filename, 'w');
			fwrite($handle, serialize($query));
			fclose($handle);
			return $query;	
		} else {
			$cache = unserialize(file_get_contents($filename));
			return $cache;
		}
	}
	
	public function query($sql) {
		return $this->driver->query($sql);
	}

	public function escape($value) {
		return $this->driver->escape($value);
	}

	public function countAffected() {
		return $this->driver->countAffected();
	}

	public function getLastId() {
		return $this->driver->getLastId();
	}
}
?>