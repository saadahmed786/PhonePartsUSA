<?php

include_once '../config.php';

if(!$_SESSION['email']){
    header("Location:index.php");
    exit;
}

$id = (int)$_REQUEST['id'];
if(!$id){
    header("Location:reports.php");
    exit;
}

$request_xml = $db->func_query_first_cell("select feed from amazon_requests where id = '$id'");

header("Content-type:text/xml;charset=utf-8");
echo $request_xml;