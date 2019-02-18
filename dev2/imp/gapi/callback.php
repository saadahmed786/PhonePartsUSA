<?php
require '../config.php';
$path = $host_path . 'gapi/index.php?code='.$_GET['code'];
?>
<script type="text/javascript">
	window.opener.location.href = "<?php echo $path; ?>"; 
	self.close();
</script>