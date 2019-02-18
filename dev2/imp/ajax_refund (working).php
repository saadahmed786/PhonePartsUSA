<?php

include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");

require_once("auth.php");
require_once("inc/functions.php");

$paypal_check = $db->func_query_first("SELECT payment_code FROM oc_order WHERE order_id='" . $_POST['order_id'] . "' AND payment_code IN('pp_standard','paypal_express','paypal_express_new','pp_standard_new')");

$auth_check = $db->func_query_first("SELECT payment_code FROM oc_order WHERE order_id='" . $_POST['order_id'] . "' AND payment_code IN('authorizenet_aim','authorizenet_cim')");

$payflow_check = $db->func_query_first("SELECT payment_code FROM oc_order WHERE order_id='" . $_POST['order_id'] . "' AND payment_code IN('pp_payflow_pro')");

if ($paypal_check) {
    paypal_refund();
}
if ($auth_check) {
    authnet_refund();
}

if($payflow_check)
{
	payflow_refund();	
	
}

function payflow_refund(){
    global $db;

    $json = array();
    $json['error'] = false;


    $user = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_username'");
    $password = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_password'");
    $vendor = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_vendor'");
    $server = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_server'");
// Reseller who registered you for Payflow or 'PayPal' if you registered
// directly with PayPal
    $partner = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_partner'");
    if($server=='L')
    {
        $sandbox = false;
    }
    else
    {
     $sandbox = true;
 }

 $order_info = $db->func_query_first("SELECT * FROM oc_payflow_admin_tools at LEFT JOIN `oc_order` o ON (at.order_id = o.order_id) WHERE at.order_id = '" . (int) $_POST['order_id'] . "'");

 if (!$order_info) {
    $json['error'] = 'Error: Order data not found';
} else {
    $transactionID = $order_info['transaction_id'];
    $currency = 'USD';
    $amount = $_POST['amount'];
}


$url = 'https://payflowpro.paypal.com';

$params = array(
  'USER' => $user,
  'VENDOR' => $vendor,
  'PARTNER' => $partner,
  'PWD' => $password,
  'TENDER' => 'C', // C = credit card, P = PayPal
  'TRXTYPE' => 'C', //  S=Sale, A= Auth, C=Credit, D=Delayed Capture, V=Void                        
  'ORIGID' => $transactionID,
  'AMT' => $amount,
  'CURRENCY' => $currency
  );

$data = '';
$i = 0;
foreach ($params as $n=>$v) {
    $data .= ($i++ > 0 ? '&' : '') . "$n=" . urlencode($v);
}

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'Content-Length: ' . strlen($data);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

curl_close($ch);

// Parse results
$response = array();
$result = strstr($result, 'RESULT');    
$valArray = explode('&', $result);
foreach ($valArray as $val) {
  $valArray2 = explode('=', $val);
  $response[$valArray2[0]] = $valArray2[1];
}



if (isset($response['RESULT']) && $response['RESULT'] == 0) {
   sendEmail($_POST['order_id'], $_POST['return_id'], $_POST['items']);
   $json['success'] = ('Action Completed Successfully');
   $json['response'] = $response;


   $i = 0;
   foreach (explode(",", $_POST['items']) as $item) {

    $return_info = $db->func_query_first("SELECT * FROM inv_return_items WHERE id='" . $item . "'");

    $data = array();
    $data['return_id'] = $_POST['return_id'];
    $data['order_id'] = $_POST['order_id'];
    $data['sku'] = $return_info['sku'];
    $data['price'] = $return_info['price'];
    $data['action'] = 'Issue Refund';
    $data['date_added'] = date('Y-m-d h:i:s');

    $db->func_array2insert("inv_return_decision", $data);


    $data = array();

    $data['decision'] = 'Issue Refund';


    $db->func_array2update("inv_return_items", $data, 'id="' . $item . '"');

    $i++;
}
} else {
  $json['error'] = 'Unable to refund, please try again';
  
}	
echo json_encode($json);
}


function paypal_refund() {
    global $db;

    $json = array();
    $json['error'] = false;


    //$this->load->language('module/' . $classname);



    $ppat_api_user = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_admin_tools_ppat_api_user'");
    $ppat_api_user = $ppat_api_user['value'];

    $ppat_api_pass = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_admin_tools_ppat_api_pass'");
    $ppat_api_pass = $ppat_api_pass['value'];

    $ppat_api_sig = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_admin_tools_ppat_api_sig'");
    $ppat_api_sig = $ppat_api_sig['value'];


    $ppat_api_env = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_admin_tools_ppat_env'");
    $ppat_api_env = $ppat_api_env['value'];

    if (!$json['error']) {
        // Save API details to settings db
        /* $savefields = array('ppat_api_user', 'ppat_api_pass', 'ppat_api_sig', 'ppat_env');
          $savearr = array();
          foreach ($this->request->post as $key => $value) {
          if (in_array($key, $savefields)) {
          $savearr['paypal_admin_tools_' . $key] = $value;
          }
          }
          $this->load->model('setting/setting');
          $this->model_setting_setting->editSetting($classname, $savearr); */
        //

          $query = $db->func_query_first("SELECT * FROM oc_paypal_admin_tools at LEFT JOIN `oc_order` o ON (at.order_id = o.order_id) WHERE at.order_id = '" . (int) $_POST['order_id'] . "'");

          if($query['payment_code']=='paypal_express_new' or $query['payment_code']=='pp_standard_new')
          {

              $ppat_api_user = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_express_new_apiuser'");
              $ppat_api_user = $ppat_api_user['value'];

              $ppat_api_pass = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_express_new_apipass'");
              $ppat_api_pass = $ppat_api_pass['value'];

              $ppat_api_sig = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_express_new_apisig'");
              $ppat_api_sig = $ppat_api_sig['value'];


              $ppat_api_env = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_express_new_test'");
              $ppat_api_env = $ppat_api_env['value'];	
              $ppat_api_env = ($ppat_api_env == 0 ? 'live' : 'sandbox');
          }


          if (!$query) {
            $json['error'] = 'Error: Order data not found';
        } else {
            $transactionID = urlencode($query['transaction_id']);
            $currencyID = urlencode($query['currency_code']);
        }

        if (!$json['error']) {

            // Set request-specific fields.
            $api_user = ($ppat_api_user);
            $api_pass = ($ppat_api_pass);
            $api_sig = ($ppat_api_sig);
            $env = ($ppat_api_env);
            $type = urlencode('Partial');   // 'Full' or 'Partial'
            $amount = $_POST['amount'] ? $_POST['amount'] : ''; // required if Partial.
            $memo = $type . ' ' . $amount;

            if ($type == 'Partial' || $type == 'Full') { //Refund types
                $method = 'RefundTransaction';
                // Add request-specific fields to the request string.
                $nvpStr = "&TRANSACTIONID=$transactionID&REFUNDTYPE=$type&CURRENCYCODE=$currencyID&NOTE=$memo";
            }

            if (strcasecmp($type, 'Partial') == 0) {
                if (!isset($amount)) {
                    $json['error'] = ('Error: You must specify amount!');
                } else {
                    $nvpStr = $nvpStr . "&AMT=$amount";
                }
            } elseif ($type == 'NotComplete') {
                $method = 'DoCapture';
                $amount = urlencode(number_format($query['amount'], 2, '.', ''));
                $currencyID = urlencode($query['currency']);
                $authorizationID = urlencode($query['authorization_id']);
                $memo = empty($memo) ? 'Capture' : $memo;
                $nvpStr = "&AUTHORIZATIONID=$authorizationID&AMT=$amount&COMPLETETYPE=$type&CURRENCYCODE=$currencyID&NOTE=$memo";
            } elseif ($type == 'Void') {
                $method = 'DoVoid';
                $authorizationID = urlencode($query['authorization_id']);
                $memo = empty($memo) ? 'Void' : $memo;
                $nvpStr = "&AUTHORIZATIONID=$authorizationID&NOTE=$memo";
            }

            if (!$json['error']) {
                // Execute the API operation; see the PPHttpPost function above.
                $httpParsedResponseAr = PPHttpPost($method, $nvpStr, $api_user, $api_pass, $api_sig, $env);

                if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
                    sendEmail($_POST['order_id'], $_POST['return_id'], $_POST['items']);
                    $json['success'] = ('Action Completed Successfully');



                    $i = 0;
                    foreach (explode(",", $_POST['items']) as $item) {

                        $return_info = $db->func_query_first("SELECT * FROM inv_return_items WHERE id='" . $item . "'");

                        $data = array();
                        $data['return_id'] = $_POST['return_id'];
                        $data['order_id'] = $_POST['order_id'];
                        $data['sku'] = $return_info['sku'];
                        $data['price'] = $return_info['price'];
                        $data['action'] = 'Issue Refund';
                        $data['date_added'] = date('Y-m-d h:i:s');

                        $db->func_array2insert("inv_return_decision", $data);


                        $data = array();

                        $data['decision'] = 'Issue Refund';


                        $db->func_array2update("inv_return_items", $data, 'id="' . $item . '"');

                        $i++;
                    }
                } else {
                    $json['error'] = ($httpParsedResponseAr['ACK'] . ': ' . urldecode($httpParsedResponseAr['L_LONGMESSAGE0']));
                }

                $json['sent'] = print_r($nvpStr, 1);
                $json['rcvd'] = urldecode(print_r($httpParsedResponseAr, 1));
            }
        }
    }

    echo json_encode($json);
}

function PPHttpPost($methodName_, $nvpStr_, $API_UserName, $API_Password, $API_Signature, $environment) {

    // Set up your API credentials, PayPal end point, and API version.
    $API_UserName = urlencode($API_UserName);
    $API_Password = urlencode($API_Password);
    $API_Signature = urlencode($API_Signature);
    $API_Endpoint = "https://api-3t.paypal.com/nvp";
    if ("sandbox" === $environment || "beta-sandbox" === $environment) {
        $API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
    }
    $version = urlencode('51.0');

    // Set the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);

    // Turn off the server and peer verification (TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    // Set the API operation, version, and API signature in the request.
    $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

    // Set the request as a POST FIELD for curl.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

    // Get response from the server.
    $httpResponse = curl_exec($ch);

    if (!$httpResponse) {
        exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
    }

    // Extract the response details.
    $httpResponseAr = explode("&", $httpResponse);

    $httpParsedResponseAr = array();
    foreach ($httpResponseAr as $i => $value) {
        $tmpAr = explode("=", $value);
        if (sizeof($tmpAr) > 1) {
            $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
        }
    }

    if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
        exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
    }

    return $httpParsedResponseAr;
}

function authnet_refund() {
    global $db;
    $json = array();
    $json['error'] = false;





    // Save API details to settings db
    //


    $query = $db->func_query_first("SELECT * FROM `oc_authnetaim_admin` ana LEFT JOIN `oc_order` o ON (ana.order_id = o.order_id) WHERE ana.order_id = '" . (int) $_POST['order_id'] . "'");


    if (!$query) {
        $json['error'] = 'Necessary transaction details missing. This order will need to be adjusted manually from your Authorize.net Account!';
    }

    if (!$json['error']) {


        $login = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='authorizenet_aim_login'");
        $login = $login['value'];

        $key = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='authorizenet_aim_key'");
        $key = $key['value'];

        $server = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='authorizenet_aim_server'");
        $server = $server['value'];

        // Common Data setup:
        $data['x_login'] = $login;
        $data['x_tran_key'] = $key;
        $data['x_version'] = '3.1';
        $data['x_delim_data'] = 'true';
        $data['x_delim_char'] = ',';
        $data['x_encap_char'] = '"';
        $data['x_relay_response'] = 'false';
        $data['x_invoice_num'] = $_POST['order_id'];
        $data['x_type'] = 'CREDIT';

        // Specific Data setup:
        Switch ($data['x_type']) {
            Case "CREDIT":
            if (empty($_POST['amount'])) {
                $json['error'] = 'Amount Required!';
            }
            if (empty($query['last_four'])) {
                $json['error'] = 'Last 4 digits not found on original order!';
            }
            if (!$json['error']) {
                $data['x_amount'] = $_POST['amount'];
                $data['x_card_num'] = $query['last_four'];
                $data['x_trans_id'] = $query['trans_id'];
                    //$data['x_ref_trans_id'] = $query->row['trans_id'];
            }
            break;
            Case "PRIOR_AUTH_CAPTURE":
            if (!isset($query['auth_code'])) {
                $json['error'] = 'Auth Code not found on original order!';
            }
            if (!$json['error']) {
                if (!empty($_POST['amount'])) {
                    $data['x_amount'] = $_POST['amount'];
                }
                $data['x_auth_code'] = $query['auth_code'];
                $data['x_trans_id'] = $query['trans_id'];
            }
            break;
            Case "VOID":
            if (!isset($query['auth_code'])) {
                $json['error'] = 'Auth Code not found on original order!';
            }
            if (!$json['error']) {
                $data['x_trans_id'] = $query['trans_id'];
            }
            break;
        }

        if (!$json['error']) {
            if ($server == 'live') {
                $url = 'https://secure.authorize.net/gateway/transact.dll'; // PROD
            } else {
                $url = 'https://test.authorize.net/gateway/transact.dll'; // DEV
            }

            $response = curl_post($url, $data);

            $results = explode(',', $response['data']);

            foreach ($results as $i => $result) {
                if (trim($result, '"') != "") {
                    $response_info[$i + 1] = trim($result, '"');
                }
            }

            $json['sent'] = print_r($data, 1);
            $json['rcvd'] = print_r($response_info, 1);

            if ($response_info[1] == 1) {
                if ($data['x_type'] == "PRIOR_AUTH_CAPTURE") {
                    $auth_code = (isset($response_info['5'])) ? $response_info['5'] : 0;
                    $db->db_exec("UPDATE oc_authnetaim_admin SET auth_code = '" . ($auth_code) . "' WHERE `order_id` = '" . (int) $_POST['order_id'] . "'");
                }
                $xComment = ("Action: " . $data['x_type'] . "\r\nResult: " . $response_info[4] . "\r\nRAW: " . print_r($response_info, 1));
                $order_status_info = $db->func_query_first("SELECT order_status_id FROM `oc_order` WHERE order_id = '" . (int) $_POST['order_id'] . "'");
                $db->db_exec("INSERT INTO oc_order_history SET order_id = '" . (int) $_POST['order_id'] . "', order_status_id = '" . (int) $order_status_info['order_status_id'] . "', notify = '0', comment = '" . ($xComment) . "', date_added = NOW()");



                $i = 0;
                foreach (explode(",", $_POST['items']) as $item) {

                    $return_info = $db->func_query_first("SELECT * FROM inv_return_items WHERE id='" . $item . "'");

                    $data = array();
                    $data['return_id'] = $_POST['return_id'];
                    $data['order_id'] = $_POST['order_id'];
                    $data['sku'] = $return_info['sku'];
                    $data['price'] = $return_info['price'];
                    $data['action'] = 'Issue Refund';
                    $data['date_added'] = date('Y-m-d h:i:s');

                    $db->func_array2insert("inv_return_decision", $data);


                    $data = array();

                    $data['decision'] = 'Issue Refund';


                    $db->func_array2update("inv_return_items", $data, 'id="' . $item . '"');

                    $i++;
                }

                sendEmail($_POST['order_id'], $_POST['return_id'], $_POST['items']);


                $json['success'] = $response_info[4];
            } else {
                $json['error'] = $response_info[4];
            }
        }
    }

    echo (json_encode($json));
}

function curl_post($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_PORT, 443);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    $response = array();

    if (curl_error($ch)) {
        $response['error'] = curl_error($ch) . '(' . curl_errno($ch) . ')';
    } else {
        $response['data'] = curl_exec($ch);
    }
    curl_close($ch);

    return $response;
}

function sendEmail($order_id, $return_id, $items = array()) {

    global $db;
    $return_info = $db->func_query("SELECT
        a.*,
        b.sku,b.title,b.`quantity`,b.price,b.`return_code`,b.`reason`,b.`decision`,b.return_id
        FROM
        `inv_returns` a
        INNER JOIN `inv_return_items`  b
        ON (a.`id` = b.`return_id`) 

        WHERE a.id='" . $return_id . "' AND b.id IN($items)");
    $order_info = $db->func_query_first("SELECT a.*,b.* FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND a.order_id='" . (int) $order_id . "'");

    $emailInfo = $_SESSION['rma_info'];
    $adminInfo = array('name' => $_SESSION['login_as'], 'company_info' => $_SESSION['company_info'] );

    $productPrice = 0;
    $productNames = '<table><tbody>';
    $productDetails = '<table width="100%">';
    $productDetails .= '<thead><tr>';
    $productDetails .= '<th width="35%">Name</th>';
    $productDetails .= '<th width="10%">Return Reason</th>';
    $productDetails .= '<th width="10%">Condition</th>';
    $productDetails .= '<th width="10%">Decision</th>';
    $productDetails .= '<th width="10%">Amount</th>';
    $productDetails .= '<th width="35%">Images</th>';
    $productDetails .= '</tr></thead><tbody>';
    foreach ($return_info as $return_item) {
        $price = ($return_item['item_condition'] == 'Customer Damage' && $return_item['decision'] == 'Denied')? '0' : $return_item['price'];
        $productPrice += (float) $price;
        $productNames .= '<tr><td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td></tr>';
        $productDetails .= '<tr>';
        $productDetails .= '<td>'. $return_item['sku'] . ' - ' . $return_item['title'] .'</td>';
        $productDetails .= '<td>'. $return_item['return_code'] . '</td>';
        $productDetails .= '<td>'. $return_item['item_condition'] . ' - ' . $return_item['item_issue'] .'</td>';
        $productDetails .= '<td>Issue Refund</td>';
        $productDetails .= '<td>'. $price .'</td>';
        $images = $db->func_query("select * from inv_return_item_images where return_item_id = '" . $return_item['id'] . "'");
        $productDetails .= '<td>';
        if ($images) {
            $productDetails .= '<table> <tr>';
            foreach ($images as $image) {
                $productDetails .= '<td><a href="' . $host_path . str_ireplace("../", "", $image['image_path']) . '">';
                $productDetails .= '<img src="' . $host_path . str_ireplace("../", "", $image['thumb_path']) . '" width="25" height="25" />';
                $productDetails .= '</a></td>';
            }
            $productDetails .= '</tr></table>';
        }

        $productDetails .= '</td></tr>';

    }
    $productDetails .= '</tbody></table>';
    $productNames .= '</tbody></table>';

    $emailInfo['rma_products_names'] = $productNames;
    $emailInfo['rma_products_Details'] = $productDetails;
    $emailInfo['total_price'] = $productPrice;

    if ($_POST['canned_id']) {

        $email = array();

        $src = $path .'files/canned_' . $_POST['canned_id'] . ".png";

        if (file_exists($src)) {
            $email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
        }

        $email['title'] = $_POST['title'];
        $email['subject'] = $_POST['subject'];
        $email['message'] = shortCodeReplace($emailInfo, $_POST['comment']);

        sendEmailDetails($emailInfo, $email);

    } else {
        $_SESSION['message'] = 'Email not sent';
    }

}

?>
