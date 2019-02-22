<?php
include_once '../config.php';
include_once '../inc/functions.php';
include_once 'load_catalog2.php';
$product_id = (int)$_POST['product_id'];

if ($_POST['action'] == 'remove') {
	unset($_SESSION['cart'][$product_id]);
} else if ($_POST['action'] != 'preload'){
	$qty = (int)$_POST['qty'];
	$price = number_format($_POST['price'], 2);
	$product_info = $db->func_query_first("SELECT a.*, b.`name` FROM `oc_product` a, `oc_product_description` b  WHERE a.`product_id` = b.`product_id` AND a.`product_id`='".$product_id."'");

	if ($_POST['update']) {
		$_SESSION['cart'][$product_id]['qty'] = 0;
	}
	
	if(isset($_SESSION['cart'][$product_id]))
	{
		$qty = (int)$_SESSION['cart'][$product_id]['qty']+$qty;
	}
	if($qty<=0) $qty = 1;
	if(($qty>$product_info['quantity'] && $qty>$catalog->productQtyOnOrder($product_info['model'])) && $_POST['force_add'] == '0')
	{
		echo json_encode(array('error'=>'You are putting more quantity than available in stock, please check'));
		exit;
	}
	$_SESSION['cart'][$product_id] = array('product_id'=>$product_id,'sku'=>$product_info['model'],'qty'=>$qty,'price'=>$price,'name'=>$product_info['name']);
}

$data = '<div id="cart" style="display: none;">';
if ($_SESSION['cart']) {
	$data .= '<div class="row">';
	$data .= '<div class="col-md-6 text-left"><h4>SKU</h4></div>';
	$data .= '<div class="col-md-3 text-left"><h4>QTY</h4></div>';
	$data .= '<div class="col-md-3 text-center"><h4>Action</h4></div>';
		$data .= '</div>';
	foreach ($_SESSION['cart'] as $id => $product) {
		$data .= '<div class="row cart-item cart-p-'. $id .'">';
		$data .= '<div class="col-md-3 text-left">'. $product['sku'] .'<br><small>$'. $product['price'] .' x '. $product['qty'] .'</small></div>';
		$data .= '<div class="col-md-3 text-left">'. changeNameCatalog($product['name']) .'</div>';
		$data .= '<div class="col-md-3 text-center"><input type="number" class="form-control" data-id="'. $id .'" min="1" name="cartQty" value="'. $product['qty'] .'"/></div>';
		$data .= '<div class="col-md-3 text-center"><button onclick="removeFromCart(\'' . $id . '\')" class="btn btn-default btn-danger btn-xs" type="button"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div>';
		$data .= '</div>';
		$qtyT += $product['qty'];
		$priceT += $product['qty'] * $product['price'];
	}
	$data .= '<hr><div class="row">';
	$data .= '<div class="col-md-6">';
	$data .= 'Sub-Total';
	$data .= '</div>';
	$data .= '<div class="col-md-3">';
	$data .= '<strong>$'. $priceT .'</strong>';
	$data .= '</div>';
	$data .= '</div>';
	$data .= '<hr><div class="row"><div class="col-md-6"><button onclick="updateCart()" class="btn btn-default btn-success btn-md btn-block" type="button">Update</button></div><div class="col-md-6"><button type="button" class="btn btn-primary btn-block" onclick="checkout();" >Checkout</button></div></div>';
} else {
	$data .= '<div class="row"><div class="col-md-12"><span>No Items</span></div></div>';
}
	$data .= '</div>';
	$data = '<h3 class="text-right"><span style="cursor: pointer;" onclick="$(\'#cart\').toggle();">Cart('. (int) $qtyT .')</span></h3>' . $data;
echo json_encode(array('success'=> 1, 'data'=> $data));
?>