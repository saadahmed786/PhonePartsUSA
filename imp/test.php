 <?php
 include_once 'config.php';

include_once 'inc/functions.php';
$rows = $db->func_query("SELECT * FROM `inv_users_log` WHERE `log` like '%Status Changed to: Disabled%' and month(date_added) in ('10','11') and year(date_added)='2018' ");
// print_r($rows);exit;
$i=1;
$my_skus = array();
foreach($rows as $row)
{
    // echo $row['log'];exit;
    $data = scrape_between($row['log'],'Product updated for <a href="','"');
    $sku_data = explode("/", $data);
    $sku = $sku_data[1];
    $my_skus[] = array('sku'=>$sku,'date_added'=>$row['date_added'],'user_id'=>$row['user_id']);
}

// $skus = array_unique($my_skus);

foreach($my_skus as $sku)
{
    $data = getProduct($sku['sku'],array('model','quantity'));

    echo '"'.$data['model'].'","'.getItemName($sku['sku']).'","'.$data['quantity'].'","'.getTrueCost($sku['sku']).'","'.get_username($sku['user_id']).'","'.americanDate($sku['date_added']).'"<br>';    
}



?>