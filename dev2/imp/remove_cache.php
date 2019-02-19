<?php
include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

	$files = glob(DIR_CACHE . 'cache.*');
		
		if ($files) {
			foreach ($files as $file) {
				
					if (file_exists($file)) {
						unlink($file);
					}
      			
    		}
		}
		if($_SESSION['login_as']=='admin')
		{
		 $db->db_exec("UPDATE inv_cron SET `status`=0 WHERE store_type='Store'");
		}
		

?>

<script>
alert('System Cache Removed');
 window.history.back();

</script>