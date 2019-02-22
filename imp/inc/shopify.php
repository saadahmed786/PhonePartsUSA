<?php

function getShopifyOrders() {

  $username = '6ba96f364c76a58f02f69f9a4eef8227';
  $password = '2e978b362c0be1d467c36a431e802826';

  $url = 'https://ppusa1.myshopify.com/admin/orders.json';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch), true);
  if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
  }
  curl_close($ch);
  return $result;
}


function addShopifyProduct($product_data){
  $username = '6ba96f364c76a58f02f69f9a4eef8227';
  $password = '2e978b362c0be1d467c36a431e802826';

  $url = 'https://ppusa1.myshopify.com/admin/products.json';
  $ch = curl_init();
  $product = array (            
   "product" => array("title" => $product_data['name'],
     "body_html" => $product_data['description'],
     "vendor" => 'PPUSA1',
     "product_type" => 'Type',
     "published" => true
     )               
   ); 
  $product = json_encode($product, JSON_NUMERIC_CHECK);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_POSTFIELDS,$product);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch), true);
  if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
  }
  curl_close($ch);
  return $result;
}

function getShopifyProducts() {

  $username = '6ba96f364c76a58f02f69f9a4eef8227';
  $password = '2e978b362c0be1d467c36a431e802826';

  $url = 'https://ppusa1.myshopify.com/admin/products.json';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch), true);
  if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
  }
  curl_close($ch);
  //print_r($result);exit;
  return $result;
}

function getShopifyProduct($id) {
  $idd = (int)$id;
  $username = '6ba96f364c76a58f02f69f9a4eef8227';
  $password = '2e978b362c0be1d467c36a431e802826';

  $url = 'https://ppusa1.myshopify.com/admin/products/'.$idd.'.json';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch), true);
  if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
  }
  curl_close($ch);
  return $result;
}

function updateShopifyProductPrice($productId,$price,$compare_price) {
  $res = getShopifyProduct($productId);
  $variantId = $res['product']['variants'][0]['id'];
  $username = '6ba96f364c76a58f02f69f9a4eef8227';
  $password = '2e978b362c0be1d467c36a431e802826';
  $url = 'https://ppusa1.myshopify.com/admin/';

  $ch = curl_init();
  $payload = array (            
   "variant" => array("id" => $variantId,
     "price" => (float)$price,
     "compare_at_price" => (float)$compare_price
     )               
   );
  $putUrl = $url ."variants/".$variantId.".json";  
  $payload = json_encode($payload, JSON_NUMERIC_CHECK);
  //print_r($payload);
  curl_setopt($ch, CURLOPT_URL, $putUrl);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
  curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch), true);
  if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
  }
  curl_close($ch);
  return $result;
}

function addShopifyProductImg($productId,$source) {
  //print_r($source);exit;
  $username = '6ba96f364c76a58f02f69f9a4eef8227';
  $password = '2e978b362c0be1d467c36a431e802826';
  $url = 'https://ppusa1.myshopify.com/admin/';

  $ch = curl_init();
  $payload = array (            
   "image" => array("src" => $source
     )               
   );
  $putUrl = $url ."products/".$productId."/images.json";  
  $payload = json_encode($payload, JSON_NUMERIC_CHECK);
  //print_r($payload);
  curl_setopt($ch, CURLOPT_URL, $putUrl);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch), true);
  if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
  }
  curl_close($ch);
  return $result;
}

function updateShopifyProductSku($productId,$sku) {
  $res = getShopifyProduct($productId);
  $variantId = $res['product']['variants'][0]['id'];
  $username = '6ba96f364c76a58f02f69f9a4eef8227';
  $password = '2e978b362c0be1d467c36a431e802826';
  $url = 'https://ppusa1.myshopify.com/admin/';

  $ch = curl_init();
  $payload = array (            
   "variant" => array("id" => $variantId,
     "sku" => $sku
     )               
   );
  $putUrl = $url ."variants/".$variantId.".json";  
  $payload = json_encode($payload, JSON_NUMERIC_CHECK);
  //print_r($payload);
  curl_setopt($ch, CURLOPT_URL, $putUrl);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
  curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch), true);
  if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
  }
  curl_close($ch);
  return $result;
}

function updateShopifyProductInventoryStatus($productId) {
  $res = getShopifyProduct($productId);
  $variantId = $res['product']['variants'][0]['id'];
  $username = '6ba96f364c76a58f02f69f9a4eef8227';
  $password = '2e978b362c0be1d467c36a431e802826';
  $url = 'https://ppusa1.myshopify.com/admin/';

  $ch = curl_init();
  $payload = array (            
   "variant" => array("id" => $variantId,
     "inventory_management" => 'shopify'
     )               
   );
  $putUrl = $url ."variants/".$variantId.".json";  
  $payload = json_encode($payload, JSON_NUMERIC_CHECK);
  //print_r($payload);
  curl_setopt($ch, CURLOPT_URL, $putUrl);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
  curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch), true);
  if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
  }
  curl_close($ch);
  return $result;
}

function updateShopifyProductQty($productId,$qty) {
  $res = getShopifyProduct((int)$productId);
  $variantId = $res['product']['variants'][0]['id'];
  $oldQty = $res['product']['variants'][0]['inventory_quantity'];
  $username = '6ba96f364c76a58f02f69f9a4eef8227';
  $password = '2e978b362c0be1d467c36a431e802826';
  $url = 'https://ppusa1.myshopify.com/admin/';

  $ch = curl_init();
  $payload = array (            
   "variant" => array("id" => (int)$variantId,
     "inventory_quantity" => (int)$qty,
     "old_inventory_quantity" => (int)$oldQty
     )               
   );
  $putUrl = $url ."variants/".$variantId.".json"; 
  $payload = json_encode($payload, JSON_NUMERIC_CHECK);
  curl_setopt($ch, CURLOPT_URL, $putUrl);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
  curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch), true);
  if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
  }
  curl_close($ch);
  return $result;
}
?>
