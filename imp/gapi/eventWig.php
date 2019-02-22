<?php
include_once ('../config.php');
$idWig = $_SESSION['id'];
$tableWig = ($_SESSION['user_id'])? 'inv_users': 'admin';
$authG = $db->func_query_first("SELECT * FROM $tableWig WHERE id = '$idWig'");
$linkWig = array();
if (isset($_GET['shipment'])) {
	$eid = $_GET['shipment'];
} else if (isset($_GET['rma_number'])) {
	$eid = $_GET['rma_number'];
} else if (isset($_GET['order'])) {
	$eid = $_GET['order'];
}
if ($eid) {
	$eid = '?id=' . $eid;
}

if ($authG['g_access_token'] && $authG['gmail']) {
	$link['link'] = $host_path . 'gapi/addEvent.php' . $eid;
	$link['title'] = 'Add Event';
	$link['class'] = 'button addEvent fancyboxX4 fancybox.iframe';
} else if (!$authG['g_access_token'] && $authG['gmail']) {
	$link['title'] = 'Auth Google';
	$link['class'] = 'button addEvent';
} else if (!$authG['gmail']) {
	$link['link'] = 'javascript:void(0);';
	$link['title'] = 'Ask Admin to Add Calendar Account';
	$link['class'] = '';
}
?>
<style type="text/css">
	.event {
		display: block;
		position: relative;
		width: 80%;
		text-align: right;
	}
	.addEvent {
		margin-right: 20px;
	}
</style>
<div class="event">
	<a class="<?php echo $link['class']; ?>" <?php echo ($link['title'] == 'Auth Google')? 'onclick="authGoogle();"': $link['link']; ?> href="<?php echo ($link['title'] == 'Auth Google')? 'javascript:void(0);': $link['link']; ?>"><?php echo $link['title']; ?></a>
</div>
<script type="text/javascript">
	function authGoogle() {
		window.open("<?php echo $host_path . 'gapi/index.php?auth=' . date('c'); ?>", "Google Account", "width=600, height=800");
	}
</script>
