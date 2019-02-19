<?php

require_once("../auth.php");

$return = $_GET['return'].".php";

if((int)$_GET['box_id'] and $_GET['action'] == 'delete' && $_SESSION['delete_shipment']){
    $box_id = (int)$_GET['box_id'];
    $db->db_exec("delete from inv_return_shipment_boxes where id = '$box_id'");

    $_SESSION['message'] = "Box is deleted successfully.";
    header("Location:$host_path/boxes/$return");
    exit;
}