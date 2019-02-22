<?php
include_once '../config.php';
include_once '../inc/functions.php';
include_once 'trellocard.php';
$trello = new trello();
if ($_POST['url']) {
$members = array();
	$_POST['url'] = base64_decode($_POST['url']);
	
	$template_T = $trello->makeTemplate($_POST);
	$label = 'purple';
	if ($_POST['type'] == 'canceled the') {
		$label = 'red';
	}
	if($_POST['type']=='put Hold On')
	{
		$label = 'blue';
		$list_id = '560ec36c5ad34c2901978e9c';
		$members = array('56130490dd27732b40cd4bb2','561408467b71350854bedad4','560eb3d74896cec0d07daa7d');
	}
	$trello->addCard($template_T['order_id'], $template_T['name'], $template_T['desc'], $label,array(),$list_id);
	exit;
}


?>