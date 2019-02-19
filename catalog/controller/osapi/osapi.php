<?php
/*
    osapi.php

    OneSaas Connect API 2.0.6.35 for OpenCart v1.5.4.1
    http://www.onesaas.com

    Copyright (c) 2012 oneSaas

    1.0.6.2 - Added XML encoding for all text fields
            - Version attribute in Xml response is loaded dinamically from db

    1.0.6.3 - Updated support for pushing Shipping Tracking and Product Stock (both in batch mode and single mode) via xml requests
            - Added parsing LastUpdatedTime as UCT

    1.0.6.4 - Checking if HTTP_ADMIN constant is defined before using it to create Url links...
                    (Mijosoft - Opencart - an extension of Joomla does not define it)

    1.0.6.5 - Added shipping tax to shipping amount

    1.0.6.6 - Modified products response to return as <Code> the first not null and not empty from the following in the table product: 
                product.code, product.model, product.sku, product.product_id;  
            
    1.0.6.7 - Fixed Shipping tax calculation bug in comparing string representing decimal numbers
                - Added quick exit for empty records

    1.0.6.8 - Added OrderFilters in Settings.xml
            - Added Categories in Product.Response.xml
            - Added Categories in Settings.xml
            - Added Plugin Capabilities in Settings.xml
            - Added IPAddress in Settings.xml
            - Added get item by id
            - Added additional addresses (shipping, billing) for orders
            - Added support for OrderCreatedTime
            - Added some checks in Settings.xml to prevent access to unset variables
            - Added additional dimension variables
            - Added basic support for pushing products
    1.0.6.9 - Added support for Product-Variants if OpenStock module is installed
            - Hardcoded version
    1.0.6.10    - Fixed bug in Shipping Tracking (Notice undefined varables)
    1.0.6.11    - Fixed bug in OriginalStatus field so that it is set to "NotSet" if null or empty
    1.0.6.12    - Added support for coupon discounts
    1.0.6.13    - Changed updateLastMofified query to avoid duplicated contacts from select
    1.0.6.14    - Changed Order Discounts to include the case the discount is ex Tax.
    1.0.6.15    - Fixed lastModified error
    1.0.6.16    - Removed strip html tags from product description specified charset Specify charset UTF-8 in htmlentities as it was wrongly encoding Chinese characters (http://stackoverflow.com/questions/6452720/htmlentities-makes-chinese-characters-unusable)
    1.0.6.17    - Changed Tax Information reported by Orders and Settings Actions to be compatible with "Tax code mapping upgrade for shopping carts" (https://trello.com/c/w4KjuzAY/1480-tax-code-mapping-upgrade-for-shopping-carts-as-per-description-and-photo-of-whiteboard-session)
    1.0.6.18    - Fix bug: Division by zero in calculating $ItemTaxRate
    1.0.6.19    - Fix bug: 
                - Remove the testing for tax difference, let onesaas server side to validate the order
                - Support debug mode for better debugging experience
    1.0.6.20    - Fix bug: Undefined variable: items_tax_rate
    1.0.6.21    - Try to calculate shipping tax based on item's tax rate if shipping tax code is not available
    1.0.6.23    - Fix error: <b>Notice</b>: Undefined variable: product_code in <b>/home/webuser/public_html/shop/catalog/controller/osapi/osapi.php</b> on line <b>538</b>
    1.0.6.24    - SB-962  Added payment amount to paymentMethod if order status is processed
    2.0.6.28    - OS-286 Include contact details into orders
*/

class ControllerOsapiOsapi extends Controller {
    private $error = array();
    private $DebugMode = false;
    private $not_payment_satuses = array("Canceled", "Denied", "Canceled Reversal", "Failed", "Refunded", "Reversed", "Chargeback", "Voided", "Expired");
  // Functions
    private function validateProductRequest(SimpleXmlElement $productRequest) {
        if (isset($productRequest->Id) && ($productRequest->Id != "")) {
            // Check Id Exists else return "Specified Id = " . $productRequest['Id'] . " does not exists in remote system"
            $product_id  = $this->db->query("select p.product_id from " . DB_PREFIX . "product p where product_id='" . (int) $productRequest->Id . "'");
            if ($product_id->num_rows == 0) {
                return "Specified Id = " . $productRequest['Id'] . " does not exists.";
            }
        }
        if (!isset($productRequest->Code) || is_null($productRequest->Code) || $productRequest->Code=="") {
            return "Product Code Missing";
        }
        
        // TODO Other Checks ...
        
        // All good - return Error Message = null
        return null;
    }
    
    private function parseSingleStockUpdateRequest (SimpleXmlElement $aRequest) {
        $stockUpdateRequest = array();
        if (!is_null($aRequest) && $aRequest->getName()==='ProductStockUpdate') {
            foreach ($aRequest->attributes() as $attr) {
                if ($attr->getName() === 'Id') {
                    $stockUpdateRequest['ProductCode'] = $attr;
                }
            }
            foreach ($aRequest->children() as $child) {
                switch ($child->getName()) {
                    case 'StockAtHand':
                        $stockUpdateRequest['StockAtHand'] = $child;
                        break;
                    case 'StockAllocated':
                        $stockUpdateRequest['StockAllocated'] = $child;
                        break;
                    case 'StockAvailable':
                        $stockUpdateRequest['StockAvailable'] = (int) $child;
                        break;
                    default:
                        // Not interested
                        break;
                }
            }
            $stockUpdateRequest;
        }
        return $stockUpdateRequest;
    }

    private function updateLastModified($objectType, $forceFull=false) {
        switch ($objectType) {
            case 'customer':
                //$contacts = $this->db->query("select c.*, s.value as store_name from " . DB_PREFIX . "customer c left join " . DB_PREFIX . "setting s on c.store_id=s.store_id where s.key='config_name' and s.group='config'");
                $contacts = $this->db->query("select c.* from " . DB_PREFIX . "customer c");
                foreach ($contacts->rows as $contact) {
                    $string_to_be_hashed = $contact['customer_id'] . $contact['firstname'] . $contact['lastname'] . $contact['email'] . $contact['telephone'] . $contact['address_id'];
                    $addresses = $this->db->query("select a.address_id, a.address_1, a.address_2, a.city, a.postcode, a.company, a.company_id, z.name, co.iso_code_2 from " . DB_PREFIX. "address a left join " . DB_PREFIX . "country co on a.country_id=co.country_id left join " . DB_PREFIX . "zone z on a.zone_id=z.zone_id where a.customer_id = '" . $contact['customer_id'] ."'");
                    foreach($addresses->rows as $address) {
                        $string_to_be_hashed .= $address['address_1'] . $address['address_2'] . $address['city'] . $address['postcode'] . $address['name'] . $address['iso_code_2'] . $address['company'] . $address['company_id'];
                    }
                    $hash = md5($string_to_be_hashed);
                    $hash_query = $this->db->query("select lm.hash from " . DB_PREFIX . "osapi_last_modified lm where lm.object_type='customer' and lm.id='" . (int) $contact['customer_id'] . "'");
                    if ($hash_query->num_rows > 0) {
                        // Already existing - check if needs to be updated
                        if ($forceFull || ($hash_query->row['hash'] !== $hash)) {
                            // Update hash and last_modified_before field
                            $this->db->query("update " . DB_PREFIX  ."osapi_last_modified set hash = '" . $hash . "', last_modified_before ='" . gmdate('Y-m-d H:i:s') . "' where object_type='customer' and id='" . $contact['customer_id'] . "'");
                        }
                    } else {
                        // New customer
                        // Insert new record for this contact
                        $this->db->query("insert ignore into " . DB_PREFIX . "osapi_last_modified values ('customer', " . $contact['customer_id'] .", '" . $hash . "', '" . gmdate('Y-m-d H:i:s') . "')");
                    }
                    /*
                    if ($hash_query->num_rows == 1) {
                        $hash_stored = $hash_query->row['hash'];
                    } else {
                        $hash_stored = '';
                    }
                    if ($hash != $hash_stored) {
                        // The contact has changed since last time we checked.
                        $now = Date('Y-m-d H:i:s');
                        if ($hash_stored==='') {
                            // Insert new record for this contact
                            $this->db->query("insert into " . DB_PREFIX . "osapi_last_modified values ('customer', " . $contact['customer_id'] .", '" . $hash . "', '" . $now . "')");
                        } else {
                            // Update hash and last_modified_before field
                            $this->db->query("update " . DB_PREFIX  ."osapi_last_modified set hash = '" . $hash . "', last_modified_before ='" . $now . "' where object_type='customer' and id='" . $contact['customer_id'] . "'");
                        }
                    }
                    */
                }
            break;
            default:
            break;
        }
    }

    private function getCurrentShippingTaxRate($shipping_code) {
    	$rate = 0;
    	$name = "";
        $shipCode = explode(".",$shipping_code);
        if (count($shipCode)>0) {
            $shipping_tax_query = $this->db->query("SELECT trr.name, trr.rate FROM  `" . DB_PREFIX . "setting` s LEFT JOIN `" . DB_PREFIX . "tax_rule` tr ON CAST( s.value AS CHAR( 10000 ) CHARACTER SET utf8 ) = tr.tax_class_id LEFT JOIN `" . DB_PREFIX . "tax_rate`  trr ON tr.tax_rate_id = trr.tax_rate_id WHERE s.key =  '" . $shipCode[0] . "_tax_class_id' AND tr.based =  'shipping' order by tr.priority");
            if ($shipping_tax_query->num_rows) {
               $rate = $shipping_tax_query->row['rate']/100;
               $name = $shipping_tax_query->row['name'];
            }
        }
		$shippingRate = array(
			"taxRate" => $rate,
			"taxName" => $name,
		);

        return $shippingRate;
    }

    private function getOrder($order_id) {
        $order_query = $this->db->query("SELECT o.*, os.name as 'order_status_name' FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_status` os ON o.order_status_id = os.order_status_id WHERE order_id = '" . (int)$order_id . "'");

        if ($order_query->num_rows) {
            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

            if ($country_query->num_rows) {
                $payment_iso_code_2 = $country_query->row['iso_code_2'];
                $payment_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $payment_iso_code_2 = '';
                $payment_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $payment_zone_code = $zone_query->row['code'];
            } else {
                $payment_zone_code = '';
            }

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

            if ($country_query->num_rows) {
                $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                $shipping_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $shipping_iso_code_2 = '';
                $shipping_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $shipping_zone_code = $zone_query->row['code'];
            } else {
                $shipping_zone_code = '';
            }

            return array(
                'order_id'                => $order_query->row['order_id'],
                'invoice_no'              => $order_query->row['invoice_no'],
                'invoice_prefix'          => $order_query->row['invoice_prefix'],
                'store_id'                => $order_query->row['store_id'],
                'store_name'              => $order_query->row['store_name'],
                'store_url'               => $order_query->row['store_url'],
                'customer_id'             => $order_query->row['customer_id'],
                'firstname'               => $order_query->row['firstname'],
                'lastname'                => $order_query->row['lastname'],
                'telephone'               => $order_query->row['telephone'],
                'fax'                     => $order_query->row['fax'],
                'email'                   => $order_query->row['email'],
                'payment_firstname'       => $order_query->row['payment_firstname'],
                'payment_lastname'        => $order_query->row['payment_lastname'],
                'payment_company'         => $order_query->row['payment_company'],
                'payment_address_1'       => $order_query->row['payment_address_1'],
                'payment_address_2'       => $order_query->row['payment_address_2'],
                'payment_postcode'        => $order_query->row['payment_postcode'],
                'payment_city'            => $order_query->row['payment_city'],
                'payment_zone_id'         => $order_query->row['payment_zone_id'],
                'payment_zone'            => $order_query->row['payment_zone'],
                'payment_zone_code'       => $payment_zone_code,
                'payment_country_id'      => $order_query->row['payment_country_id'],
                'payment_country'         => $order_query->row['payment_country'],
                'payment_iso_code_2'      => $payment_iso_code_2,
                'payment_iso_code_3'      => $payment_iso_code_3,
                'payment_address_format'  => $order_query->row['payment_address_format'],
                'payment_method'          => $order_query->row['payment_method'],
                'shipping_firstname'      => $order_query->row['shipping_firstname'],
                'shipping_lastname'       => $order_query->row['shipping_lastname'],
                'shipping_company'        => $order_query->row['shipping_company'],
                'shipping_address_1'      => $order_query->row['shipping_address_1'],
                'shipping_address_2'      => $order_query->row['shipping_address_2'],
                'shipping_postcode'       => $order_query->row['shipping_postcode'],
                'shipping_city'           => $order_query->row['shipping_city'],
                'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
                'shipping_zone'           => $order_query->row['shipping_zone'],
                'shipping_zone_code'      => $shipping_zone_code,
                'shipping_country_id'     => $order_query->row['shipping_country_id'],
                'shipping_country'        => $order_query->row['shipping_country'],
                'shipping_iso_code_2'     => $shipping_iso_code_2,
                'shipping_iso_code_3'     => $shipping_iso_code_3,
                'shipping_address_format' => $order_query->row['shipping_address_format'],
                'shipping_method'         => $order_query->row['shipping_method'],
                'shipping_code'           => $order_query->row['shipping_code'],
                'comment'                 => $order_query->row['comment'],
                'total'                   => $order_query->row['total'],
                'order_status_id'         => $order_query->row['order_status_id'],
                'order_status_name'       => $order_query->row['order_status_name'],
                'language_id'             => $order_query->row['language_id'],
                'currency_id'             => $order_query->row['currency_id'],
                'currency_code'           => $order_query->row['currency_code'],
                'currency_value'          => $order_query->row['currency_value'],
                'date_modified'           => $order_query->row['date_modified'],
                'date_added'              => $order_query->row['date_added'],
                'ip'                      => $order_query->row['ip']
            );
        } else {
            return false;
        }
    }

    private function hasOpenStockModuleInstalled() {
        //check product table for has_option
        $res = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product` LIKE 'has_option'");
        if($res->num_rows == 0) {
            return false;
        }
        //check for product_option_relation table
        $res = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."product_option_relation'");
        if($res->num_rows == 0) {
            return false;
        }
        //check for product_option_relation_group_price table
        $res = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."product_option_relation_group_price'");
        if($res->num_rows == 0) {
            return false;
        }
        //check for product_option_relation_discount_price table
        $res = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."product_option_relation_discount_price'");
        if($res->num_rows == 0) {
            return false;
        }
        return true;
    }
    private function getProductCodeFromId($product_id) {
        if (strpos($product_id,'#') > 0) {
            if ($this->hasOpenStockModuleInstalled()) {             
                $product_ids = explode("#", $product_id);
                if (count($product_ids) == 2) {
                    $master_id = $product_ids[0];
                    $variant_id = $product_ids[1];
                    $option = $this->db->query("select * from " . DB_PREFIX . "product_option_relation por where product_id='" . (int)$master_id . "' AND id = '" . (int) $variant_id . "'")->row;
                    if (($option != null) && isset($option["sku"]) && $option["sku"] != "") {
                        return $option["sku"];
                    }
                }
            }                   
        }
        $product = $this->db->query("select * from " . DB_PREFIX . "product p where product_id='" . (int)$product_id . "'")->row;
        if ($product != null) {
            if (isset($product['code']) && !is_null($product['code']) && $product['code']!="") {
                return $product['code'];
            }
            if (isset($product['model']) && !is_null($product['model']) && $product['model']!="") {
                return $product['model'];
            }
            if (isset($product['sku']) && !is_null($product['sku']) && $product['sku']!="") {
                return $product['sku'];
            }
        }
        return $product_id;
    }
    
    public function index() {
        // Initialise objects
        $this->language->load('osapi/osapi');
        $this->load->model('setting/setting');
        $pageSize = 10;

        // Parse input
        $this->DebugMode = isset($_GET['Debug']) && $_GET['Debug'] == 'true';
        $AccessKey = (isset($_GET['AccessKey']) ? $_GET['AccessKey'] : '');
        $Page = ((isset($_GET['Page']) && (is_numeric($_GET['Page']))) ? (int) $_GET['Page'] : 0);
        $LastUpdatedTime = ((isset($_GET['LastUpdatedTime']) && (strtotime($_GET['LastUpdatedTime'].'UCT')>0)) ? strtotime($_GET['LastUpdatedTime'].'UCT') : strtotime('1970-01-19T00:00:00+00:00UCT'));
        $LastUpdatedTimeString = gmdate('Y-m-d H:i:s', $LastUpdatedTime);
        $action = (isset($_GET['Action']) ? $_GET['Action'] : '');
        $requestType = $_SERVER['REQUEST_METHOD'];
        if ($requestType == "POST") {
            switch ($action) {
                case "Products":
                    // Rename $action to UpdateProducts
                    $action = "UpdateProducts";
                    break;
                default:
                    // We are not supporting push contacts and orders
                    break;
            }
        }
        $itemId = (isset($_GET['ItemId']) ? $_GET['ItemId'] : '');
        $OrderCreatedTime = ((isset($_GET['OrderCreatedTime']) && (strtotime($_GET['OrderCreatedTime'].'UCT')>0)) ? strtotime($_GET['OrderCreatedTime'].'UCT') : strtotime('1970-01-19T00:00:00+00:00UCT'));
        if (isset($_GET['ForceFull']) && ($_GET['ForceFull']=='true')) {
            $this->updateLastModified('customer', true);
        }

        // Initialise XML Response
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><OneSaas></OneSaas>');
        /* Read Version
        $os_version_query = $this->db->query("select s.value from " . DB_PREFIX . "setting s where s.key = 'OSAPI_VERSION' and s.group='OSAPI'");
        if ($os_version_query->num_rows == 1) {
            $os_version = $os_version_query->row['value'];
        }
        */
        $xml->addAttribute('Version','2.0.6.35');
        $xml->addAttribute('DebugMode',var_export($this->DebugMode, true));

        // Authenticate access
        $ak_query = $this->db->query("select s.value from " . DB_PREFIX . "setting s where s.key='OSAPI_ACCESS_KEY' and s.group='OSAPI'");
        if ($ak_query->num_rows == 0) {
            $xml->addChild('Error','Access Key not initialized');
            return 0;
        }
        if ($ak_query->row['value']!==$AccessKey) {
            // Prepare Error Response Message
            $xml->addChild('Error','Invalid Key');
        } else {
            // Fulfill request
            //$xml->LastUpdatedTime = $_GET['LastUpdatedTime'];
            //$xml->LastUpdatedTimeParsed = $LastUpdatedTimeString;
            //date_default_timezone_set('UTC');
            //$xml->Timezone = date_default_timezone_get();
            //$xml->LastUpdatedTimeLocal = Date('Y-m-d H:i:s', $LastUpdatedTime);
            //$xml->CurrentTime = Date('Y-m-d H:i:s');
            switch ($action) {
                case "Contacts":
                    if ($itemId == '') {
                        if ($Page == 0) {
                            $this->updateLastModified('customer');
                        }
                    }
                    if ($itemId == '') {
                        $contacts = $this->db->query("select c.*, lm.last_modified_before, s.value as store_name from " . DB_PREFIX . "customer c left join " . DB_PREFIX . "osapi_last_modified lm on c.customer_id = lm.id left join " . DB_PREFIX . "setting s on c.store_id=s.store_id where s.key='config_name' and s.group='config' and lm.object_type='customer' and lm.last_modified_before >'" .  $LastUpdatedTimeString . "' limit " . $Page*$pageSize . ", " . $pageSize);
                    } else {
                        $contacts = $this->db->query("select c.*, lm.last_modified_before, s.value as store_name from " . DB_PREFIX . "customer c left join " . DB_PREFIX . "osapi_last_modified lm on c.customer_id = lm.id left join " . DB_PREFIX . "setting s on c.store_id=s.store_id where s.key='config_name' and s.group='config' and lm.object_type='customer' and c.customer_id='" . (int) $itemId . "'");
                    }
                    foreach ($contacts->rows as $contact ) {
                        $xml_contact = $xml->addChild('Contact');
                        $xml_contact->addAttribute('Id', $contact['customer_id']);
                        $xml_contact->addAttribute('LastUpdated', $contact['last_modified_before']);
                        $xml_contact->FirstName = htmlspecialchars($contact['firstname']);
                        $xml_contact->LastName = htmlspecialchars($contact['lastname']);
                        $xml_contact->WorkPhone = htmlspecialchars($contact['telephone']);
                        $xml_contact->Email = htmlspecialchars($contact['email']);
                        $xml_contact->addChild('OrganizationName');
                        $xml_contact->addChild('OrganizationBusinessNumber');
                        $xml_contact->Tags = 'StoreName:' . htmlspecialchars($contact['store_name']);
                        $xml_addresses = $xml_contact->addChild('Addresses');
                        $addresses = $this->db->query("select a.address_id, a.address_1, a.address_2, a.city, a.postcode, a.company, a.company_id, z.name, co.iso_code_2 from " . DB_PREFIX. "address a left join " . DB_PREFIX . "country co on a.country_id=co.country_id left join " . DB_PREFIX . "zone z on a.zone_id=z.zone_id where a.customer_id = '" . $contact['customer_id'] ."'");
                        foreach($addresses->rows as $address) {
                            $xml_address = $xml_addresses->addChild('Address');
                            if ($address['address_id'] == $contact['address_id']) {
                                //default address - set for shipping and billing
                                $xml_address->addAttribute('Type', 'Shipping');
                                $xml_addressB = $xml_addresses->addChild('Address');
                                $xml_addressB->addAttribute('Type', 'Billing');
                                $xml_addressB->Line1 = htmlspecialchars($address['address_1']);
                                $xml_addressB->Line2 = htmlspecialchars($address['address_2']);
                                $xml_addressB->City = htmlspecialchars($address['city']);
                                $xml_addressB->PostCode = htmlspecialchars($address['postcode']);
                                $xml_addressB->State = htmlspecialchars($address['name']);
                                $xml_addressB->CountryCode = $address['iso_code_2'];
                                $xml_contact->OrganizationName = htmlspecialchars($address['company']);
                                $xml_contact->OrganizationBusinessNumber = htmlspecialchars($address['company_id']);
                            } else {
                                $xml_address->addAttribute('Type', '');
                            }
                            $xml_address->Line1 = htmlspecialchars($address['address_1']);
                            $xml_address->Line2 = htmlspecialchars($address['address_2']);
                            $xml_address->City = htmlspecialchars($address['city']);
                            $xml_address->PostCode = htmlspecialchars($address['postcode']);
                            $xml_address->State = htmlspecialchars($address['name']);
                            $xml_address->CountryCode = $address['iso_code_2'];
                        }
                        if (defined("HTTP_ADMIN")) {
                            $xml_contact->Url = HTTP_ADMIN . 'index.php?route=sale/customer/update&customer_id=' . $contact['customer_id'];
                        }
                    }
                    break;
                case "Products":
                    if ($itemId == '') {
                        //$product_ids = $this->db->query("select p.product_id from " . DB_PREFIX . "product p where (p.date_added>FROM_UNIXTIME(" .  $LastUpdatedTime . ") or p.date_modified>FROM_UNIXTIME(" .  $LastUpdatedTime . ") ) limit " . $Page*$pageSize . ", " . $pageSize);
                        $product_ids = $this->db->query("select p.product_id from " . DB_PREFIX . "product p where (p.date_added>= DATE_SUB('".Date('Y-m-d H:i:s', $LastUpdatedTime) ."', INTERVAL 1 DAY) or p.date_modified>=DATE_SUB('" . Date('Y-m-d H:i:s', $LastUpdatedTime) . "', INTERVAL 1 DAY) ) limit " . $Page*$pageSize . ", " . $pageSize);
                    } else {
                        //$product_ids = array(array('product_id'=>$itemId));
                        $product_ids = $this->db->query("select p.product_id from " . DB_PREFIX . "product p where product_id='" . $itemId . "'");
                    }
                    foreach ($product_ids->rows as $product_id) {
                        $product = $this->db->query("SELECT DISTINCT *, pd.name AS name,(SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount,(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special,(SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status,p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id = '" . (int)$product_id['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'")->row;
                        if (!is_null($product) && isset($product['product_id'])) {
                            
                            // Always add master product
                            $xml_product = $xml->addChild('Product');
                            $xml_product->addAttribute('Id', $product['product_id']);
                            $lastModifiedTS = strtotime(max($product['date_added'],$product['date_modified']));
                            if ($lastModifiedTS>0) {
                                // The date is stored in Mysql using locale zone which we don't know what it is.  To avoid OS rejecting the order as it is before its LastUpdate, we "refresh" it by 1 day
                                $lastModifiedTS += 60*60*24;
                            } else {
                                // The parsing above failed, using tomorrow
                                $lastModifiedTS = time() + 60*60*24;
                            }
                            $xml_product->addAttribute('LastUpdated', Date('Y-m-d H:i:s',$lastModifiedTS));
                            $xml_product->Code = $this->getProductCodeFromId($product['product_id']);
                            $xml_product->Name = ($product['name']);
                            //$xml_product->Description = htmlentities(strip_tags(html_entity_decode($product['description'])));
                            $xml_product->Description = htmlentities($product['description'], ENT_COMPAT, 'UTF-8');
                            $xml_product->IsActive = ($product['status']==='1')?'True':'False';
                            if (defined("HTTP_ADMIN")) {
                                $xml_product->Url = HTTP_ADMIN . 'index.php?route=catalog/product/update&product_id=' . $product['product_id'];
                            }
                            $xml_product->PublicUrl = $this->url->link('product/product', '', 'SSL') . '&product_id=' . $product['product_id'];
                            $xml_product->SalePrice = ($product['special']?$product['special']:($product['discount'] ? $product['discount'] : $product['price']));
                            $xml_product->StockAtHand = $product['quantity'];
                            $xml_product->IsInventoried = 'true';
                            // Categories
                            $categories_query = $this->db->query("SELECT c.category_id, c.parent_id, cd.name FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "category c ON p2c.category_id = c.category_id LEFT JOIN " . DB_PREFIX . "category_description cd ON c.category_id=cd.category_id WHERE p2c.product_id='" . (int) $product['product_id'] . "' AND cd.language_id='" . (int)$this->config->get('config_language_id') . "'")->rows;
                            if (sizeof($categories_query)>0) {
                                $categories = $xml_product->addChild('Categories');
                                foreach($categories_query as $category_query) {
                                    $category = $categories->addChild('Category');
                                    $category->Id = htmlspecialchars($category_query['category_id']);
                                    if ($category_query['parent_id'] != '0') {
                                        $category->ParentId = htmlspecialchars($category_query['parent_id']);
                                    }
                                    $category->Name = htmlspecialchars($category_query['name']);
                                }
                            }
                            // Images
                            if (isset($product['image']) && (defined("HTTP_IMAGE")) ) {
                                $images_xml = $xml_product->addChild('Images');
                                $images_xml->Image = HTTP_IMAGE . $product['image'];
                            }
                            // Additional dimensions fields
                            if (isset($product['weight']) && isset($product['weight_class_id'])) {
                                $weight_unit = $this->db->query("select wcd.unit from " . DB_PREFIX . "weight_class_description wcd where wcd.weight_class_id = '" . (int) $product['weight_class_id'] . "' AND wcd.language_id='" . (int)$this->config->get('config_language_id') . "'")->row;
                                if (isset($weight_unit['unit'])) {
                                    $weight_xml = $xml_product->addChild('Weight', $product['weight']);
                                    $weight_xml->addAttribute('Unit', $weight_unit['unit']);    
                                }
                            }
                            if (isset($product['length_class_id'])) {
                                $length_unit = $this->db->query("select lcd.unit from " . DB_PREFIX . "length_class_description lcd where lcd.length_class_id = '" . (int) $product['length_class_id'] . "' AND lcd.language_id='" . (int)$this->config->get('config_language_id') . "'")->row;
                                if (isset($length_unit['unit'])) {
                                    if (isset($product['length'])) {
                                        $length_xml = $xml_product->addChild('Length', $product['length']);
                                        $length_xml->addAttribute('Unit', $length_unit['unit']);
                                    }
                                    if (isset($product['width'])) {
                                        $width_xml = $xml_product->addChild('Width', $product['width']);
                                        $width_xml->addAttribute('Unit', $length_unit['unit']);
                                    }
                                    if (isset($product['height'])) {
                                        $height_xml = $xml_product->addChild('Height', $product['height']);
                                        $height_xml->addAttribute('Unit', $length_unit['unit']);
                                    }
                                }
                            }
                            $xml_product->Type = 'Product';
                            // Get Stores for this product
                            $stores = $this->db->query("select CONVERT(s.value USING latin1) as value from " . DB_PREFIX . "product_to_store p2s left join " . DB_PREFIX . "setting s on p2s.store_id = s.store_id where p2s.product_id = '" . (int) $product['product_id'] . "' and s.key='config_name' and s.group='config'");
                            foreach ($stores->rows as $store) {
                                $xml_product->Tags .= htmlentities('StoreName:' . $store['value'] .',');
                            }
                            
                            // Check if OpenStock module is installed and if there are options
                            if ($this->hasOpenStockModuleInstalled() && $product['has_option']) {
                                // Calculate total stock for master product
                                $master_stock = 0;
                                // Query the options
                                $options = $this->db->query("select * from " . DB_PREFIX . "product_option_relation por where product_id='" . (int)$product_id['product_id'] . "'");
                                foreach ($options->rows as $option) {
                                    // Copy the master product
                                    $xml_option = $xml->AddChild("Product");
                                    $xml_option->addAttribute("Id",  $product['product_id'] . "#" . $option["id"]);
                                    $xml_option->addAttribute('LastUpdated', max($product['date_added'],$product['date_modified']));
                                    // Change Code (SKU if present)
                                    if (isset($option['sku']) && ($option['sku'] != null) && ($option['sku'] != '')) {
                                        $xml_option->Code = $option['sku'];
                                    } else {
                                        $xml_option->Code = $product_id['product_id'];
                                    }
                                    // Add MasterCode
                                    $xml_option->MasterCode = $xml_product->Code;
                                    $xml_option->Name = ($product['name']);
                                    //$xml_option->Description = htmlentities(strip_tags(html_entity_decode($product['description'])));
                                    $xml_option->Description = htmlentities($product['description'], ENT_COMPAT, 'UTF-8');
                                    // Change Active
                                    if (isset($option['active']) && ($option['active'] != null) && ($option['active'] != '')) {
                                        $xml_option->IsActive = ($option['active']==='1')?'True':'False';
                                    } else {
                                        $xml_option->IsActive = ($product['status']==='1')?'True':'False';
                                    }
                                    if (defined("HTTP_ADMIN")) {
                                        $xml_option->Url = HTTP_ADMIN . 'index.php?route=catalog/product/update&product_id=' . $product['product_id'];
                                    }
                                    $xml_option->PublicUrl = $this->url->link('product/product', '', 'SSL') . '&product_id=' . $product['product_id'];
                                    // Change Price
                                    if (isset($option['price']) && ($option['price']) != null && ($option['price'] != '') && ((float) $option['price'] != 0.0)) {
                                        $xml_option->SalePrice = $option['price'];
                                    } else {
                                        $xml_option->SalePrice = ($product['special']?$product['special']:($product['discount'] ? $product['discount'] : $product['price']));
                                    }
                                    // Change Stock
                                    if (isset($option['stock']) && ($option['stock'] != null) && ($option['stock'] != '')) {
                                        $xml_option->StockAtHand = $option['stock'];
                                        $master_stock += $option['stock'];
                                    } else {
                                        $xml_option->StockAtHand = $product['quantity'];
                                    }
                                    $xml_option->IsInventoried = 'true';
                            
                                    // Categories
                                    if (sizeof($categories_query)>0) {
                                        $categories = $xml_option->addChild('Categories');
                                        foreach($categories_query as $category_query) {
                                            $category = $categories->addChild('Category');
                                            $category->Id = htmlspecialchars($category_query['category_id']);
                                            if ($category_query['parent_id'] != '0') {
                                                $category->ParentId = htmlspecialchars($category_query['parent_id']);
                                            }
                                            $category->Name = htmlspecialchars($category_query['name']);
                                        }
                                    }
                                    // Images
                                    if (isset($product['image']) && (defined("HTTP_IMAGE")) ) {
                                        $images_xml = $xml_option->addChild('Images');
                                        $images_xml->Image = HTTP_IMAGE . $product['image'];
                                    }
                                    // Additional dimensions fields
                                    if (isset($product['weight']) && isset($product['weight_class_id'])) {
                                        $weight_unit = $this->db->query("select wcd.unit from " . DB_PREFIX . "weight_class_description wcd where wcd.weight_class_id = '" . (int) $product['weight_class_id'] . "' AND wcd.language_id='" . (int)$this->config->get('config_language_id') . "'")->row;
                                        if (isset($weight_unit['unit'])) {
                                            $weight_xml = $xml_option->addChild('Weight', $product['weight']);
                                            $weight_xml->addAttribute('Unit', $weight_unit['unit']);    
                                        }
                                    }
                                    if (isset($product['length_class_id'])) {
                                        $length_unit = $this->db->query("select lcd.unit from " . DB_PREFIX . "length_class_description lcd where lcd.length_class_id = '" . (int) $product['length_class_id'] . "' AND lcd.language_id='" . (int)$this->config->get('config_language_id') . "'")->row;
                                        if (isset($length_unit['unit'])) {
                                            if (isset($product['length'])) {
                                                $length_xml = $xml_option->addChild('Length', $product['length']);
                                                $length_xml->addAttribute('Unit', $length_unit['unit']);
                                            }
                                            if (isset($product['width'])) {
                                                $width_xml = $xml_option->addChild('Width', $product['width']);
                                                $width_xml->addAttribute('Unit', $length_unit['unit']);
                                            }
                                            if (isset($product['height'])) {
                                                $height_xml = $xml_option->addChild('Height', $product['height']);
                                                $height_xml->addAttribute('Unit', $length_unit['unit']);
                                            }
                                        }
                                    }
                                    $xml_option->Type = 'Product';
                                    // Get Stores for this product
                                    foreach ($stores->rows as $store) {
                                        $xml_option->Tags .= htmlentities('StoreName:' . $store['value'] .',');
                                    }
                                    // Add Option values
                                    if (isset($option['var']) && ($option['var'] != null) && ($option['var'] != '')) {
                                        $options_xml = $xml_option->AddChild("Options");
                                        $option_value_ids = explode(":", $option["var"]);
                                        foreach ($option_value_ids as $option_value_id) {   
                                            $option_details = $this->db->query("select od.name as option_name, ovd.name as option_value from " . DB_PREFIX . "product_option_value pov left join " . DB_PREFIX . "option_description od on pov.option_id = od.option_id LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON pov.option_value_id=ovd.option_value_id where product_option_value_id='" . (int)$option_value_id . "' AND od.language_id='" . (int)$this->config->get('config_language_id') . "' AND ovd.language_id='" . (int)$this->config->get('config_language_id') . "'" )->row;
                                            if ($option_details != null && isset($option_details['option_name']) && isset($option_details['option_value'])) {
                                                $option_xml = $options_xml->AddChild("Option");
                                                $option_xml->Name = $option_details['option_name'];
                                                $option_xml->Value = $option_details['option_value'];
                                            }
                                        }
                                    }
                                }
                                if ($master_stock >0) {
                                    $xml_product->StockAtHand = $master_stock;
                                }
                            }
                        }
                    }
                    break;
                case "Orders":
                    if ($itemId == '') {
                        $orders_id = $this->db->query("select o.order_id from `" . DB_PREFIX . "order` o where (o.date_added>= DATE_SUB('".Date('Y-m-d H:i:s', $LastUpdatedTime) ."', INTERVAL 1 DAY) or o.date_modified>=DATE_SUB('" . Date('Y-m-d H:i:s', $LastUpdatedTime) . "', INTERVAL 1 DAY) ) AND o.date_added>=DATE_SUB('" . Date('Y-m-d H:i:s', $OrderCreatedTime) . "'   , INTERVAL 1 DAY) limit " . $Page*$pageSize . ", " . $pageSize)->rows;
                    } else {
                        $orders_id = $this->db->query("select o.order_id from `" . DB_PREFIX . "order` o where order_id='" . $itemId . "'")->rows;
                    }
                    foreach ($orders_id as $order_id) {
                        // Quick Exit
                        $order = $this->getOrder($order_id['order_id']);
                        if ($order['order_status_id'] == 0) { continue; }
                        if ($order['order_id'] == "") { continue; }
                        $xml_order = $xml->addChild('Order');
                        $xml_order->addAttribute('Id', $order['order_id']);
                        $lastModifiedTS = strtotime(max($order['date_added'],$order['date_modified']));
                        if ($lastModifiedTS>0) {
                            // The date is stored in Mysql using locale zone which we don't know what it is.  To avoid OS rejecting the order as it is before its LastUpdate, we "refresh" it by 1 day
                            $lastModifiedTS += 60*60*24;
                        } else {
                            // The parsing above failed, using tomorrow
                            $lastModifiedTS = time() + 60*60*24;
                        }
                        $xml_order->addAttribute('LastUpdated', Date('Y-m-d H:i:s', $lastModifiedTS));
                        $xml_order->OrderNumber = $order['order_id'];
                        $xml_order->Date = $order['date_added'];
                        $xml_order->Type = 'Order';
                        
                        switch($order['order_status_id']) {
                            // Selection based on standard order status codes.  Modified installations might have different status meaning
                            //Available from osapi New|FullyPaid|Shipped|Cancelled|Refunded
                            case 1: // Pending
                            case 2: // Processing
                            case 15: // Processed
                                $xml_order->Status = 'New';
                                break;
                            case 3: // Shipped
                                $xml_order->Status = 'Shipped';
                                break;
                            case 5: // Complete
                                $xml_order->Status = 'FullyPaid';
                                break;
                            case 7: // Canceled
                            case 8: // Denied
                            case 9: // Canceled Reversal
                            case 10: // Failed
                            case 12: // Reversed
                            case 13: // Chargeback
                            case 14: // Expired
                            case 16: // Voided
                                $xml_order->Status = 'Cancelled';
                                break;

                            case 11: // Refunded
                                $xml_order->Status = 'Refunded';
                                break;
                            default:
                                $xml_order->Status = 'New';
                                break;
                        }
                        if (isset($order['order_status_name']) && ($order['order_status_name'] !== "")) {
                            $xml_order->OriginalStatus = $order['order_status_name'];
                        } else {
                            $xml_order->OriginalStatus = "NotSet";
                        }
						
                        $xml_order->Notes = htmlspecialchars($order['comment']);
						
                        $comments = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order['order_id'] . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added")->rows;
                        foreach ($comments as $comment) {
                            $xml_order->Notes .= ($comment['date_added'])?htmlentities('Date added: ' . $comment['date_added'] . '</br>'):'';
                            $xml_order->Notes .= ($comment['status'])?htmlentities('Status: ' . $comment['status'] . '</br>'):'';
                            $xml_order->Notes .= ($comment['comment'])?htmlentities($comment['comment'] . '</br></br>'):'';
                        }

                        $xml_order->Total = $order['total'];

                        // add contact details
                        $xml_contact = $xml_order->addChild('Contact');

                        if ($order['customer_id']!=='0') {
                            $xml_contact->addAttribute('Id', $order['customer_id']);
                        }
						if($order['comment'] == 'POS')
						{					
							$xml_contact->FirstName = 'POS';
							$xml_contact->LastName = 'Customer';
							$xml_order->Tags = htmlentities('StoreName:' . $order['store_name'].',OrderSource:POS');							
						}
						else
						{	
							if ($order['customer_id']!=='0')
							{ 
								$xml_contact->FirstName = htmlspecialchars($order['payment_firstname']);
								$xml_contact->LastName = htmlspecialchars($order['payment_lastname']);
								$xml_order->Tags = htmlentities('StoreName:' . $order['store_name']);
							}
							else
							{
								$xml_contact->addAttribute('Id', 'POS:'.$order['order_id']);
								if($order['firstname'] != '')
								{
									$xml_contact->FirstName = htmlspecialchars($order['firstname']);
									$xml_contact->LastName = htmlspecialchars($order['lastname']);							
								}
								else
								{
									$xml_contact->FirstName = 'POS';
									$xml_contact->LastName = 'Customer';
									$xml_order->Tags = htmlentities('StoreName:' . $order['store_name'].',OrderSource:POS');									
								}									
							}
						}						
                        $xml_contact->WorkPhone = htmlspecialchars($order['telephone']);
                        $xml_contact->Email = htmlspecialchars($order['email']);
                        $xml_contact->OrganizationName = htmlspecialchars($order['payment_company']);
                        $xml_contact->OrganizationBusinessNumber = '';
                        $xml_contact->Tags = 'StoreName:' . htmlspecialchars($order['store_name']);
                        $xml_addresses = $xml_contact->addChild('Addresses');
                        $xml_addressB = $xml_addresses->addChild('Address');
                        $xml_addressB->addAttribute('Type', 'Billing');
						$xml_addressB->FirstName = htmlspecialchars($order['payment_firstname']);
						$xml_addressB->LastName = htmlspecialchars($order['payment_lastname']);
						$xml_addressB->OrganizationName = htmlspecialchars($order['payment_company']);							
                        $xml_addressB->Line1 = htmlspecialchars($order['payment_address_1']);
                        $xml_addressB->Line2 = htmlspecialchars($order['payment_address_2']);
                        $xml_addressB->City = htmlspecialchars($order['payment_city']);
                        $xml_addressB->PostCode = htmlspecialchars($order['payment_postcode']);
                        $xml_addressB->State = htmlspecialchars($order['payment_zone']);
                        $xml_addressB->CountryCode = htmlspecialchars($order['payment_iso_code_2']);
                        $xml_addressS = $xml_addresses->addChild('Address');
                        $xml_addressS->addAttribute('Type', 'Shipping');
						$xml_addressS->FirstName = htmlspecialchars($order['shipping_firstname']);
						$xml_addressS->LastName = htmlspecialchars($order['shipping_lastname']);
						$xml_addressS->OrganizationName = htmlspecialchars($order['shipping_company']);							
                        $xml_addressS->Line1 = htmlspecialchars($order['shipping_address_1']);
                        $xml_addressS->Line2 = htmlspecialchars($order['shipping_address_2']);
                        $xml_addressS->City = htmlspecialchars($order['shipping_city']);
                        $xml_addressS->PostCode = htmlspecialchars($order['shipping_postcode']);
                        $xml_addressS->State = htmlspecialchars($order['shipping_zone']);
                        $xml_addressS->CountryCode = htmlspecialchars($order['shipping_iso_code_2']);
                        
                        // Additional Shipping and Billing addresses for transaction
                        $xml_addresses = $xml_order->addChild('Addresses');
                        $xml_addressB = $xml_addresses->addChild('Address');
                        $xml_addressB->addAttribute('Type', 'Billing');
						$xml_addressB->FirstName = htmlspecialchars($order['payment_firstname']);
						$xml_addressB->LastName = htmlspecialchars($order['payment_lastname']);
						$xml_addressB->OrganizationName = htmlspecialchars($order['payment_company']);                        
                        $xml_addressB->Line1 = htmlspecialchars($order['payment_address_1']);
                        $xml_addressB->Line2 = htmlspecialchars($order['payment_address_2']);
                        $xml_addressB->City = htmlspecialchars($order['payment_city']);
                        $xml_addressB->PostCode = htmlspecialchars($order['payment_postcode']);
                        $xml_addressB->State = htmlspecialchars($order['payment_zone']);
                        $xml_addressB->CountryCode = htmlspecialchars($order['payment_iso_code_2']);
                        $xml_addressS = $xml_addresses->addChild('Address');
                        $xml_addressS->addAttribute('Type', 'Shipping');
						$xml_addressS->FirstName = htmlspecialchars($order['shipping_firstname']);
						$xml_addressS->LastName = htmlspecialchars($order['shipping_lastname']);
						$xml_addressS->OrganizationName = htmlspecialchars($order['shipping_company']);	                        
                        $xml_addressS->Line1 = htmlspecialchars($order['shipping_address_1']);
                        $xml_addressS->Line2 = htmlspecialchars($order['shipping_address_2']);
                        $xml_addressS->City = htmlspecialchars($order['shipping_city']);
                        $xml_addressS->PostCode = htmlspecialchars($order['shipping_postcode']);
                        $xml_addressS->State = htmlspecialchars($order['shipping_zone']);
                        $xml_addressS->CountryCode = htmlspecialchars($order['shipping_iso_code_2']);

                        // - Items
                        $order_items_tax = 0;
                        $items = $xml_order->addChild('Items');
                        $this->load->model('account/order');
                        $subTotal = 0.0;
                        $item_results = $this->model_account_order->getOrderProducts($order['order_id']);
                        $this->debugDump($xml_order, $item_results);
                        foreach($item_results as $item_result) {
                            $item = $items->addChild('Item');
                            $item->ProductId = $item_result['product_id'];
                            // Check if OpenStock module is installed
                            if ($this->hasOpenStockModuleInstalled()) {
                                // Check if options are specified for this order
                                $item_options = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option where order_id ='" . (int)$item_result["order_id"] . "' AND order_product_id='" . (int) $item_result["order_product_id"] ."' ORDER BY order_option_id");
                                //$item->Query1 = "SELECT * FROM " . DB_PREFIX . "order_option where order_id ='" . (int)$item_result["order_id"] . "' AND order_product_id='" . (int) $item_result["order_product_id"] ."' ORDER BY order_option_id";
                                if ($item_options->num_rows >0) {
                                    // Construct $option_string to query OpenStock module 
                                    $option_string = "";
                                    $counter = 0;
                                    foreach($item_options->rows as $item_option) {
                                        $counter++;
                                        if ($counter == 1) {
                                            $option_string = $item_option["product_option_value_id"];
                                        } else {
                                            $option_string .= ":" . $item_option["product_option_value_id"];
                                        }
                                    }
                                    // Check if product_option exists for this combination in OpenStock module
                                    $openStock_query = $this->db->query("SELECT id FROM " . DB_PREFIX . "product_option_relation WHERE var='" . $option_string . "'")->row;
                                    //$item->Query2 = "SELECT id FROM " . DB_PREFIX . "product_option_relation WHERE var='" . $option_string . "'";
                                    if (($openStock_query != null) && isset($openStock_query['id'])) {
                                        $item->ProductId = $item_result['product_id'] . "#" . $openStock_query['id'];
                                    }
                                }
                            }
                            $item->ProductCode = $this->getProductCodeFromId($item->ProductId);
                            $item->ProductName = htmlspecialchars($item_result['name']);
                            $item->Quantity = $item_result['quantity'];
                            $item->Price = $item_result['price']+$item_result['tax'];
                            $item->UnitPriceExTax = $item_result['price'];
                            $order_items_tax += $item_result['tax']*$item_result['quantity'];
                            if (isset($item_result['tax']) && ($item_result['tax'] > 0 )) {
                                $itemTaxes = $item->addChild('Taxes');
                                $tax_rates = $this->db->query("select trr.name, trr.rate, trr.type from " . DB_PREFIX . "product p left join " . DB_PREFIX . "tax_class tc on p.tax_class_id=tc.tax_class_id left join " . DB_PREFIX . "tax_rule tr on tc.tax_class_id=tr.tax_class_id left join " . DB_PREFIX . "tax_rate trr on tr.tax_rate_id=trr.tax_rate_id where p.product_id='" . (int) $item_result['product_id'] . "'")->rows;
                                $this->debugDump($item, $tax_rates);

                                //$itemTaxes->Query = "select trr.name, trr.rate, trr.type from " . DB_PREFIX . "product p left join " . DB_PREFIX . "tax_class tc on p.tax_class_id=tc.tax_class_id left join " . DB_PREFIX . "tax_rule tr on tc.tax_class_id=tr.tax_class_id left join " . DB_PREFIX . "tax_rate trr on tr.tax_rate_id=trr.tax_rate_id where p.product_id='" . (int) $item_result['product_id'] . "'";
                                //$itemTaxes->Obj = print_r($tax_rates, 1);
                                if (sizeof($tax_rates)>0) {
                                    foreach($tax_rates as $tax_rate) {
                                        $item_tax_amount = 0.0 + $item_result['tax'];
                                        if ($tax_rate['type']==="P") {
                                            $tax_rate_tax_amount = 0.0 + $item_result['price']*$tax_rate['rate']/100;
                                        } else {
                                            $tax_rate_tax_amount = 0.0 + $tax_rate['rate'];
                                        }
                                        if (abs($item_tax_amount-$tax_rate_tax_amount) <= 0.01) {
                                            $itemTax = $itemTaxes->addChild('Tax');
                                            if (isset($tax_rate['name'])) {
                                                $itemTax->TaxName = htmlspecialchars($tax_rate['name']);
                                            }
                                            if (isset($tax_rate['rate'])) {
                                                $itemTax->TaxRate = 0.0 + $tax_rate['rate']/100;
                                            }
                                            if (isset($item_result['quantity'])) {
                                                $itemTax->TaxAmount = $item_result['tax']*$item_result['quantity'];
                                            }
                                            break;
                                        }
                                    }
                                }
                            }
                            $item->LineTotalIncTax = ($item_result['price']+$item_result['tax'])*$item_result['quantity'];
                            $subTotal += ($item_result['price']+$item_result['tax'])*$item_result['quantity'];
                        }

						$voucher_results = $this->model_account_order->getOrderVouchers($order['order_id']);
                        $this->debugDump($xml_order, $voucher_results);
                        foreach($voucher_results as $voucher_result) {
                            $item = $items->addChild('Item');
                            $item->ProductId = 'Voucher_'.$voucher_result['voucher_id'];
                            $item->ProductCode = $voucher_result['code'];
                            $item->ProductName = htmlspecialchars($voucher_result['description']);
							$item->Quantity = '1';
                            $item->Price = $voucher_result['amount'];
                        }						
						
                        // Init Other charges
                        $otherCharges = $xml_order->addChild('OtherCharges');

                        // - Shipping & Discounts
                        $shipping = $xml_order->addChild('Shipping');
                        $shipping->addAttribute('Name', htmlspecialchars($order['shipping_method']));
                        $order_totals = $this->model_account_order->getOrderTotals($order['order_id']);
                        $this->debugDump($xml_order, $order_totals);

                        $order_total_tax = 0;
                        foreach ($order_totals as $order_total) {
                            if ($order_total['code']=='tax') {
                                $order_total_tax += $order_total['value'];
                            }
                        }
                        $discounts = 0;
						$hasDiscount = false;
						foreach ($order_totals as $order_total) {
							if (($order_total['code']=='coupon') || ($order_total['code']=='voucher')) {
								$hasDiscount = true;
								$discounts += abs($order_total['value']);
							}
						}
						
                        foreach ($order_totals as $order_total) {
                            if ($order_total['code']=='shipping') {
                                if (is_numeric($order_items_tax) && is_numeric($order_total_tax)) {
                                    $order_items_tax = $order_items_tax + 0.0;  // Transform to float
                                    $order_total_tax = $order_total_tax + 0.0;  // Transform to float
                                } // else let it fails later
								
                                $epsilon = 0.01;
								if($hasDiscount){
									$items_tax_rate = $order_items_tax/($subTotal-$order_items_tax);
									$discount_tax_amount = $discounts*$items_tax_rate;
									$shipping_tax_discrepancy = abs(abs($order_items_tax - $order_total_tax) - $discount_tax_amount);
								}
								else{
									$shipping_tax_discrepancy = abs($order_items_tax - $order_total_tax);
								}
                                
                                if ($shipping_tax_discrepancy > $epsilon) {
                                    // Shipping might have tax
                                    $shipping_tax_rate_row = $this->getCurrentShippingTaxRate($order['shipping_code']);
                                    $shipping_tax_rate = $shipping_tax_rate_row['taxRate'];
                                    $shipping_tax_amount = $order_total['value'] * $shipping_tax_rate;
                                    
                                    $this->debugDump($shipping, array(
                                        'shipping_cost' => $order_total['value'],
                                        'shipping_code' => $order['shipping_code'], 
                                        'shipping_tax_rate' => $shipping_tax_rate,
                                        'shipping_tax_amount' => $shipping_tax_amount)
                                    );

                                    if (is_numeric($shipping_tax_amount)) {
                                        if (abs($shipping_tax_discrepancy - $shipping_tax_amount) > $epsilon) { // the shipping tax code seems wrong, try using product tax instead
                                            $shipping_tax_names = array();
                                            $shipping_tax_rates = array();
                                            foreach ($items as $line_item) {
                                                foreach ($line_item->Taxes as $line_item_taxes) {                                                   
                                                    foreach ($line_item_taxes as $line_item_tax) {
                                                        $shipping_tax_names[] = (string)$line_item_tax->TaxName;
                                                        $shipping_tax_rates[] = (float)$line_item_tax->TaxRate;
                                                    }
                                                }
                                                break;  // one line item is enough
                                            }
                                            
                                            $shipping_tax_rate_from_line_item = array_sum($shipping_tax_rates); // sum all tax rates
                                            
                                            if (abs($shipping_tax_discrepancy - $order_total['value'] * $shipping_tax_rate_from_line_item) <= $epsilon) {
                                                $shipping_tax_amount = $shipping_tax_discrepancy;
                                                $shipping_taxes = $shipping->addChild('Taxes');
                                                foreach ($shipping_tax_names as $key => $tax_name) {
                                                    $shipping_tax = $shipping_taxes->addChild('Tax');
                                                    $shipping_tax->TaxName = $tax_name;
                                                    $shipping_tax->TaxRate = $shipping_tax_rates[$key];
                                                    $shipping_tax->TaxAmount = $order_total['value'] * $shipping_tax_rates[$key];
                                                }
                                            }
                                        }
										else{
                                        	$shipping_taxes = $shipping->addChild('Taxes');
											$shipping_tax = $shipping_taxes->addChild('Tax');
											$shipping_tax->TaxName = $shipping_tax_rate_row['taxName'];
											$shipping_tax->TaxRate = $shipping_tax_rate;
											$shipping_tax->TaxAmount = $order_total['value'] * $shipping_tax_rate;
                                        }
                                        $shipping_tax_amount = $shipping_tax_amount + 0.0;  // Transform to float
                                    } // else let it fails later

                                    $shipping->Amount = $order_total['value'] + $shipping_tax_amount;
                                    $shipping->ShippingTax = $shipping_tax_amount;
                                    $shipping->TotalTax = $order_total_tax;
                                    $shipping->ItemsTax = $order_items_tax;
                                } else {
                                    $shipping->Amount = $order_total['value'] + 0.0;
                                }
                            } else {
								if (array_key_exists('code', $order_total) && $order_total['code']!='total' && $order_total['code']!= 'sub_total' && $order_total['code']!='tax'&& $order_total['code']!='coupon'&& $order_total['code']!='voucher' && $order_total['code']!='POS') {
									$other_charge = (float)preg_replace('/[^\d-.]+/', '',$order_total['text']);
									if($other_charge < 0)
									{	
										//If charge is negative we add to discount
										$discounts += abs($order_total['value']);
									}
									else
									{
										//In case is positive we add extra charge to add to order total
										$charge = $otherCharges->addChild('Charge');
										$charge->addAttribute('Name', $order_total['code']);
										$charge->addAttribute('Type', 'Other');
										$charge->Amount = $order_total['value'] + 0.0;
									}
								}
                            }
                        }
                        
                        if (isset($discounts)) {
                            // Difference could be due to discount being before tax.
                            $xml_order->SubTotal = $subTotal;
                            if (($subTotal-$order_items_tax)>0.01) {
                                $items_tax_rate = $order_items_tax/($subTotal-$order_items_tax);
                                // Calculate discount tax
                                $discount_tax_amount = $discounts*$items_tax_rate;
                                $xml_order->ItemTaxRate = $items_tax_rate;
                                $xml_order->DiscountTaxAmount = $discount_tax_amount;
                                if (!isset($shipping_tax_amount)) {
                                    $shipping_tax_amount = 0;
                                }
                                if (abs($order_items_tax + $shipping_tax_amount -$discount_tax_amount - $order_total_tax) < 0.01) {
                                    // All Good. Discount is exTax
                                    $discounts+=$discount_tax_amount;
                                } else {
                                    // Error in total will be raised in OneSaas.
                                }
                            } else {
                                // Subtotal = $order_item_tax - Only tax?
                                // Error in total will be raised in OneSaas.
                            }
                        }
                        
                        $xml_order->Discounts = $discounts;
                        
                        // - Payment
                        $payments = $xml_order->addChild('Payments');
                        $paymentMethod = $payments->addChild('PaymentMethod');
                        $paymentMethod->addAttribute('Name', htmlspecialchars($order['payment_method']));
                        if (!in_array($xml_order->OriginalStatus, $this->not_payment_satuses)) {
                            // Assuming full amount paid
                            $paymentMethod->Amount = $xml_order->Total;
                        } 
                    }
                    break;
                case "Settings":
                    $language = $this->db->query("select l.name from " . DB_PREFIX . "language l where language_id='" . (int)$this->config->get('config_language_id') . "'")->row;

                    $payments_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'payment'")->rows;
                    if (sizeof($payments_query)>0) {
                        $payments = $xml->addChild('PaymentGateways');
                        foreach($payments_query as $payment_query) {
                            $_['text_description'] = null;
                            if (file_exists(DIR_LANGUAGE. '/' . strtolower($language['name']) . '/payment/' . $payment_query['code'] . '.php')) {
                                include(DIR_LANGUAGE. '/' . strtolower($language['name']) . '/payment/' . $payment_query['code'] . '.php');                         
                                $payment = $payments->addChild('PaymentGateway');
                                if (isset($_['text_title'])) { 
									$pg_title = $_['text_title'];
									$pg_title_sanitized = strip_tags($pg_title);
									$payment->Name = htmlspecialchars($pg_title_sanitized); 
									}
                                if (!is_null($_['text_description'])) {
                                    $payment->Description = htmlspecialchars($_['text_description']);
                                    $_['text_description'] = null;
                                } else {
                                    if (isset($_['text_title'])) {
									$pg_title = $_['text_title'];
									$pg_title_sanitized = strip_tags($pg_title);									
									$payment->Description = htmlspecialchars($_['text_title']);
									}
                                }
                            }
                        }
                    }

                    // TaxCodes
                    //$taxes_query = $this->db->query("select tc.title, tc.description, tr.name, tr.rate, tr.type, c.iso_code_2, z.name as state from " . DB_PREFIX . "tax_class tc left join " . DB_PREFIX . "tax_rule tru on tc.tax_class_id=tru.tax_class_id left join " . DB_PREFIX . "tax_rate tr on tru.tax_rate_id=tr.tax_rate_id left join " . DB_PREFIX . "zone_to_geo_zone z2gz on tr.geo_zone_id=z2gz.geo_zone_id left join " . DB_PREFIX . "country c on z2gz.country_id=c.country_id left join " . DB_PREFIX . "zone z on z2gz.zone_id=z.zone_id where (tr.date_added>FROM_UNIXTIME(" .  $LastUpdatedTime . ") or tr.date_modified>FROM_UNIXTIME(" .  $LastUpdatedTime . "))")->rows;
                    $taxes_query = $this->db->query("select tr.name, tr.rate, tr.type, c.iso_code_2, z.name as state from " . DB_PREFIX . "tax_rate tr left join " . DB_PREFIX . "zone_to_geo_zone z2gz on tr.geo_zone_id=z2gz.geo_zone_id left join " . DB_PREFIX . "country c on z2gz.country_id=c.country_id left join " . DB_PREFIX . "zone z on z2gz.zone_id=z.zone_id where (tr.date_added>FROM_UNIXTIME(" .  $LastUpdatedTime . ") or tr.date_modified>FROM_UNIXTIME(" .  $LastUpdatedTime . "))")->rows;
                    if (sizeof($taxes_query)>0) {$tax_codes = $xml->addChild('TaxCodes');}
                    foreach ($taxes_query as $tax_query) {
                        $tax_code = $tax_codes->addChild('TaxCode');
                        if (isset($tax_query['name']) ) {$tax_code->Name = htmlspecialchars($tax_query['name']);}
                        if (isset($tax_query['description']) ) {$tax_code->Description = htmlspecialchars($tax_query['description']);}
                        if (isset($tax_query['rate']) ) {$tax_code->Rate = 0.0 + $tax_query['rate']/100;}
                        if (isset($tax_query['iso_code_2']) ) {$tax_code->CountryCode = $tax_query['iso_code_2'];} 
                        if (isset($tax_query['state']) && !is_null($tax_query['state'])) { $tax_code->StateCode = htmlspecialchars($tax_query['state']);}
                    }
                    
                    // Shipping 
                    $shippings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'shipping'")->rows;
                    if (sizeof($shippings_query)>0) {
                        $shippings = $xml->addChild('ShippingMethods');
                        foreach($shippings_query as $shipping_query) {
                            $_['text_description'] = null;
                            if (file_exists(DIR_LANGUAGE. '/' . strtolower($language['name']) . '/shipping/' . $shipping_query['code'] . '.php')) {
                                include(DIR_LANGUAGE. '/' . strtolower($language['name']) . '/shipping/' . $shipping_query['code'] . '.php');                               
                                $shipping = $shippings->addChild('ShippingMethod');
                                if (isset($_['text_title'])) { $shipping->Name = htmlspecialchars($_['text_title']); }
                                if (!is_null($_['text_description'])) {
                                    $shipping->Description = htmlspecialchars($_['text_description']);
                                    $_['text_description'] = null;
                                } else {
                                    $shipping->Description = htmlspecialchars($_['text_title']);
                                }
                            }
                        }
                    }
                    
                    // Order Statuses
                    $order_statuses_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id='" . (int)$this->config->get('config_language_id') . "'")->rows;
                    if (sizeof($order_statuses_query)>0) {
                        $order_statuses = $xml->addChild('OrderStatuses');
                        foreach($order_statuses_query as $order_status_query) {
                            $order_status = $order_statuses->addChild('OrderStatus');
                            $order_status->Name = htmlspecialchars($order_status_query['name']);
                        }
                    }
                    
                    // Categories
                    // $categories_query = $this->db->query("SELECT c.category_id, c.parent_id, cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON c.category_id=cd.category_id WHERE c.status='1' AND cd.language_id='" . (int)$this->config->get('config_language_id') . "'")->rows;
                    // if (sizeof($categories_query)>0) {
                    //     $categories = $xml->addChild('Categories');
                    //     foreach($categories_query as $category_query) {
                    //         $category = $categories->addChild('Category');
                    //         $category->Id = htmlspecialchars($category_query['category_id']);
                    //         if ($category_query['parent_id'] != '0') {
                    //             $category->ParentId = htmlspecialchars($category_query['parent_id']);
                    //         }
                    //         $category->Name = htmlspecialchars($category_query['name']);
                    //     }
                    // }
                    
					// Plugin Features
					$features = array('BatchStockUpdates'=>'true');
					$features_xml = $xml->addChild('PluginFeatures');
					foreach($features as $feature => $status) {
						$feature_xml = $features_xml->addChild('PluginFeature');
						$feature_xml->Name = $feature;
						$feature_xml->Value = $status;
					}
					
                    // Server IP Address
                    if (array_key_exists('SERVER_ADDR',$_SERVER) && isset($_SERVER['SERVER_ADDR'])) {
                        $xml->IPAddress = $_SERVER['SERVER_ADDR'];
                    } else if (array_key_exists('LOCAL_ADDR',$_SERVER) && isset($_SERVER['LOCAL_ADDR'])) {
                        $xml->IPAddress = $_SERVER['LOCAL_ADDR'];
                    }
                    break;

                case "UpdateStock":
                    // Parse posted parameters StockUpdateId, ProductCode, StockAtHand, StockAllocated, StockAvailable
                    $xmlRequest = new SimpleXmlElement(file_get_contents("php://input"));
                    $stockUpdateRequests = array();
                    $batchMode='false';
                    if ($xmlRequest->getName()==='ProductStockUpdate') {
                        // Single product stock update
                        $stockUpdateRequests[] = $this->parseSingleStockUpdateRequest($xmlRequest);
                    } elseif ($xmlRequest->getName()==='ProductStockUpdates') {
                        // Multiple product stock update
                        $batchMode='true';
                        foreach ($xmlRequest->children() as $aXmlRequest) {
                            $stockUpdateRequests[] = $this->parseSingleStockUpdateRequest($aXmlRequest);
                        }
                    } else {
                        // Wrong format
                    }

                    foreach ($stockUpdateRequests as $stockUpdateRequest) {
                        $psu = $xml->addChild('Response');
                        $psu->addAttribute('Id', $stockUpdateRequest['ProductCode']);

                        if ($stockUpdateRequest['ProductCode'] != ''){
                            // Check for product variants and OpenStock module installed
                            if (strpos($stockUpdateRequest['ProductCode'],'#') > 0) {
                                if ($this->hasOpenStockModuleInstalled()) {
                                    $product_ids = explode("#", $stockUpdateRequest['ProductCode']);
                                    if (count($product_ids) == 2) {
                                        $master_id = $product_ids[0];
                                        $variant_id = $product_ids[1];
                                        $updateQuery = "update " . DB_PREFIX . "product_option_relation set stock ='" . (int) $stockUpdateRequest['StockAvailable'] . "' where product_id='" . (int) $master_id . "' AND id ='" . (int) $variant_id . "'";
                                    } else {
                                        $psu->addChild('Error','Wrong product id ' . $stockUpdateRequest['ProductCode']);
                                    }
                                } else {
                                    $psu->addChild('Error','The product id ' . $stockUpdateRequest['ProductCode'] . ' is for a variant but the OpenStock module is not installed.');
                                }
                            } else {
                                $updateQuery = "update " . DB_PREFIX . "product set quantity ='" . (int) $stockUpdateRequest['StockAvailable'] . "', date_modified= now() where product_id='" . (int) $stockUpdateRequest['ProductCode'] . "'";
                            }
                            if($this->db->query($updateQuery)) {
                                $psu->addChild('Success', 'Operation Succeeded. Batch mode=' . $batchMode);
                                //$psu->addChild('SQL', $updateQuery);
                            } else {
                                $psu->addChild('Error','Stock Update failed. Query: ' . $updateQuery);
                            }
                        } else {
                            $psu->addChild('Success', 'Operation Succeeded');	// no need for error
                        }
                    }
                    break;

                case "ShippingTracking":
                    // Parse posted parameters ShippingTrackingId, OrderNumber, Date, TrackingCode, CarrierCode, CarrierName, Notes
                    $xmlRequest = new SimpleXmlElement(file_get_contents("php://input"));
                    if ($xmlRequest->getName()==='OrderShippingTracking') {
                        foreach ($xmlRequest->attributes() as $attr) {
                            if ($attr->getName() === 'Id') {
                                $OrderNumber = "" . $attr;
                            }
                        }
                        // Init variables to avoid Notices
                        $Date = '';
                        $TrackingCode = '';
                        $CarrierCode = '';
                        $CarrierName = '';
                        $Notes = '';
                        foreach ($xmlRequest->children() as $child) {
                            switch ($child->getName()) {
                                case 'OrderNumber':
                                    $OrderIncrementId = $child;
                                    break;
                                case 'Date':
                                    $Date = $child;
                                    break;
                                case 'TrackingCode':
                                    $TrackingCode = $child;
                                    break;
                                case 'CarrierCode':
                                    $CarrierCode = $child;
                                    break;
                                case 'CarrierName':
                                    $CarrierName = $child;
                                    break;
                                case 'Notes':
                                    $Notes = $child;
                                    break;
                                default:
                                    // Not interested
                                    break;
                            }
                        }

                        if ($OrderNumber != '') {
                            $update_status = "update `" . DB_PREFIX . "order` set order_status_id = '3', date_modified = now() where order_id = '" . $OrderNumber . "'";
                            $note_text =    "Updated from OneSaas system\n" .
                                            "Date Added: " . Date("d/m/Y - H:i:s") . "\n" .
                                            "Date: " . $Date . "\n" .
                                            "Tracking Code: " . $TrackingCode . "\n" .
                                            "Carrier Code: " . $CarrierCode . "\n" .
                                            "Carrier Name: " . $CarrierName . "\n" .
                                            "Notes: " . $Notes;
                            $insert_notes = "insert into " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values (" . $OrderNumber . ", 3, 0, '" . $note_text ."', now())";
                            if ($this->db->query($update_status)) {
                                if ($this->db->query($insert_notes)) {
                                    $xml->addChild('Success', 'Operation Succeeded');
                                } else {
                                    $xml->addChild('Error', 'Shipping Tracking.  Insert Comment failed: ' . $insert_notes);
                                }
                            } else {
                                $xml->addChild('Error','Shipping Tracking Update failed.  Query: ' . $update_query);
                            }
                        } else {
                            $psu->addChild('Success', 'Operation Succeeded');	// no need for error
                        }
                    } else {
                        $xml->addChild('Error','Wrong xml request format');
                    }
                    break;
                
                case "UpdateProducts":
                    // Parse resquest
                    $xmlRequest = null;
                    try {
                        $xmlRequest = new SimpleXmlElement(file_get_contents("php://input"));
                    } catch (Exception $e) {
                        $xml->Error = $e->getMessage();
                    }
                    if (!is_null($xmlRequest) && $xmlRequest->getName()==='OneSaas') {
                        foreach ($xmlRequest->children() as $productRequest) {
                            $xml_product = $xml->addChild("ProductResponse");
                            if (isset($productRequest->Id)) { $xml_product->Id = $productRequest->Id; }
                            if (isset($productRequest->Code)) { $xml_product->Code = $productRequest->Code; }
                            $errorMsg = $this->validateProductRequest($productRequest);
                            if (is_null($errorMsg)) {
                                try {
                                    $update = isset($productRequest->Id) && !is_null($productRequest->Id) && ($productRequest->Id != "");
                                    if ($update) {
                                        // Update product fields in db
                                        $update_product = "update `" . DB_PREFIX . "product` set "
                                        . " model='" . $productRequest->Code . "', '"
                                        . " sku='" . $productRequest->Code . "', " 
                                        //. "quantity=" . (int) $productRequest->StockAtHand . ", " 
                                        . "price=" . $productRequest->SalePrice . " " // Price
                                        . "date_modified=now() )" // Date Added
                                        . " where product_id = '" . (int) $productRequest->Id . "'";
                                        if ($this->db->query($update_product) ) {
                                            // Update product name & description fields in db
                                            $update_product_description = "update `" . DB_PREFIX . "product_description` set "
                                            . " name='" . $productRequest->Name . "', '" 
                                            . " description='" . $productRequest->Description . "'" 
                                            . "  where product_id = '" . $productRequest->Id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'";
                                            if ($this->db->query($update_product_description) ) {
                                                $xml_product->Success = "Operation Succeeded";
                                            } else {
                                                $xml_product->Error = 'Product Description Update failed.  Query: ' . $update_product_description;
                                            } 
                                        } else {
                                            $xml_product->Error = 'Product Update failed.  Query: ' . $update_product;
                                        }
                                    } else {
                                        // Insert product fields in db
                                        $insert_product = "INSERT INTO `" . DB_PREFIX . "product` (model, sku,  price, date_added, date_modified) VALUES ('" 
                                        . $productRequest->Code . "', '" // Model
                                        . $productRequest->Code . "', " // SKU
                                        //. $productRequest->StockAtHand . ", " // Quantity
                                        . $productRequest->SalePrice . ", " // Price
                                        . "now(), " // Date Added
                                        . "now() )"; // Date Modified
                                        if ($this->db->query($insert_product)) { 
                                            // Insert product description fields in db
                                            $item_id = $this->db->getLastId();
                                            $insert_product_description = "INSERT INTO `" . DB_PREFIX . "product_description` (product_id, language_id, name, description) VALUES ("
                                            . $item_id . ", " // Id
                                            . (int) $this->config->get('config_language_id') . ", '" // Language Id
                                            . $productRequest->Name . "', '" // Name
                                            . $productRequest->Description . "')"; // Description
                                            
                                            if ($this->db->query($insert_product_description) ) {
                                                $xml_product->Id = $item_id;
                                                $xml_product->Success = "Operation Succeeded";
                                            } else {
                                                $xml_product->Error = 'Product Insert Description failed.  Query: ' . $insert_product_description;
                                            }
                                        } else {
                                            $xml_product->Error = 'Product Insert failed.  Query: ' . $insert_product;
                                        }
                                    }
                                } catch (Exception $e) {
                                    $xml_product->Error = $e->getMessage();
                                }
                            } else {
                                $xml_product->Error = $errorMsg;
                            }
                        }
                    } else {
                        $xml->addChild('Error','Wrong xml request format');
                    }
                    break;
                    
                default:
                    $xml->addChild('Error','Invalid Action Request');
                    break;
            }
        }

        // Set view
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/osapi/osapi.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/osapi/osapi.tpl';
        } else {
            $this->template = 'default/template/osapi/osapi.tpl';
        }
        // Pass data to view
        $this->data['xml'] = $xml;

        //Output response
        $this->response->addHeader('Content-type: application/xml; charset=utf-8');
        $this->response->setOutput($this->render());
    }

    function debugDump($node, $data){
        if ($this->DebugMode) {
            $node->DebugDump[] = str_replace('=>', ':', var_export($data, true));
        }       
    }
}
?>
