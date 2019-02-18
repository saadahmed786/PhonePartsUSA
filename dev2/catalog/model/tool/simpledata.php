<?php
/*
@author	Dmitriy Kubarev
@link	http://www.simpleopencart.com
@link	http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/  

class ModelToolSimpleData extends Model {
    
    // The method for saving data from the registration form
    public function saveRegistrationData($customer_id, $data) {
        // $data - all data from post data, to see it you can use print_r($data)
        /*
        $this->db->query("INSERT INTO 
                            " . DB_PREFIX . "your_custom_or_standart_table 
                            SET 
                                store_id = '" . (int)$this->config->get('config_store_id') . "', 
                                firstname = '" . $this->db->escape($data['main_firstname']) . "', 
                                lastname = '" . $this->db->escape($data['main_lastname']) . "', 
                                email = '" . $this->db->escape($data['main_email']) . "', 
                                telephone = '" . $this->db->escape($data['main_telephone']) . "'
                                ...etc...
                                ");
      	
        */
	}
    
    // The method for saving data from the checkout form
    public function saveCheckoutData($customer_id, $order_id, $data) {
        // $data - all data from post data, to see it you can use print_r($data)
        /*
        $this->db->query("INSERT INTO 
                            " . DB_PREFIX . "your_custom_or_standart_table 
                            SET 
                                store_id = '" . (int)$this->config->get('config_store_id') . "', 
                                firstname = '" . $this->db->escape($data['main_firstname']) . "', 
                                lastname = '" . $this->db->escape($data['main_lastname']) . "', 
                                email = '" . $this->db->escape($data['main_email']) . "', 
                                telephone = '" . $this->db->escape($data['main_telephone']) . "'
                            ...etc...
                            ");
      	
        */
	}
    
    // The method for loading custom fields of customer info
    public function loadCustomerData($customer_id) {
        /*
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "your_custom_or_standart_table WHERE customer_id = '" . (int)$customer_id . "'");
		// return value must be array with fields ids as keys (for example main_email,main_name,custom_id1,company_id2, etc)
        // return array('main_email' => $query->row['email'], ...etc...);
		return $query->row;
        */
	}
    
    // The method for loading custom fields of address info
    public function loadAddressData($address_id) {
        /*
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "your_custom_or_standart_table WHERE customer_id = '" . (int)$customer_id . "'");
		// return value must be array with field ids as keys (for example main_email,main_name,custom_id1,company_id2, etc)
        // return array('main_email' => $query->row['email'], ...etc...);
		return $query->row;
        */
	}
    
    // This is example of validation method for field company_name. This method must return text of the validation error.
    public function validate_company_name($value) {
        return empty($value) ? 'error' : '';
    }
}
?>