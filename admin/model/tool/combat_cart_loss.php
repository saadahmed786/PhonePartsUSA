<?php
    class ModelToolCombatCartLoss extends Model
    {
        private $current_recipient = 0;
        private $ccl_version = '1.1';

        function install()
        {
            $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."lost_carts_templates`  (
                                    `template_id` SMALLINT(5) UNSIGNED NULL AUTO_INCREMENT,
                                    `template_from` VARCHAR(512) NOT NULL COLLATE 'utf8_unicode_ci',
                                    `template_subject` VARCHAR(512) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
                                    `template_message` TEXT NULL COLLATE 'utf8_unicode_ci',
                                    PRIMARY KEY (`template_id`)
                                )
                                COLLATE='utf8_unicode_ci'
                                ENGINE=InnoDB
                                ROW_FORMAT=DEFAULT");

            $this->db->query("CREATE TABLE IF NOT EXISTS  `".DB_PREFIX."order_emails` (
                                    `order_email_id` int(11) NOT NULL AUTO_INCREMENT,
                                    `order_id` int(11) NOT NULL,
                                    `email_subject` varchar(512) NOT NULL,
                                    `email_message` text NOT NULL ,
                                    `admin_notify` varchar(2) NOT NULL DEFAULT '0',
                                    `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                                    PRIMARY KEY (`order_email_id`)
                                    )
                                COLLATE='utf8_unicode_ci'
                                ENGINE=InnoDB
                                ROW_FORMAT=DEFAULT");

        }
        /*function add_update_field() {
            $this->db->query("ALTER TABLE `".DB_PREFIX."lost_carts_templates` ADD `template_from` VARCHAR(512) NOT NULL AFTER `template_id`");
        }*/
        function getVersion(){
            return $this->ccl_version;
        }

        function uninstall()
        {
            $this->db->query('DROP TABLE IF EXISTS `'.DB_PREFIX.'lost_carts_templates`;');
            $this->db->query('DROP TABLE IF EXISTS `'.DB_PREFIX.'order_emails`;');
        }

        function get_orders($data = array())
        {

            return $this->db->query('SELECT o.order_id, CONCAT(o.firstname, " ", o.lastname) AS customer, o.store_name, o.total,o.date_added, o.date_modified, (select count(*) from `'.DB_PREFIX.'order_emails` oe where o.order_id=oe.order_id AND oe.admin_notify = 0) as total_emails
                                    FROM `'.DB_PREFIX.'order` o
                                    WHERE o.order_status_id <=0 AND o.date_added > DATE_SUB(CURDATE(), INTERVAL 15 DAY)
                                    ORDER BY o.date_modified desc LIMIT 300');
        }



        function delete_orders($orders)
        {
            $new_orders=array();
            foreach($orders as $order)
            {
                $new_orders[]=(int)$order;
            }

            if (count($new_orders)==0)
            {
                return FALSE;
            }

            $this->db->query('DELETE FROM `'.DB_PREFIX.'order` WHERE order_id IN ('.implode(',',$new_orders).')');
            $this->db->query('DELETE FROM `'.DB_PREFIX.'order_product` WHERE order_id IN('.implode(',',$new_orders).')');
            $this->db->query('DELETE FROM `'.DB_PREFIX.'order_total` WHERE order_id IN('.implode(',',$new_orders).')');
            return TRUE;
        }

        function get_unconfirmed_order($order_id)
        {
            $order['order']=$this->db->query('SELECT order_id,IFNULL('.DB_PREFIX.'customer.lastname,`'.DB_PREFIX.'order`.lastname) as lastname,IFNULL('.DB_PREFIX.'customer.firstname,`'.DB_PREFIX.'order`.firstname) as firstname,IFNULL('.DB_PREFIX.'customer.email,`'.DB_PREFIX.'order`.email) as email,`total`,IFNULL('.DB_PREFIX.'customer.telephone,`'.DB_PREFIX.'order`.telephone) as telephone
                                              FROM `'.DB_PREFIX.'order`
                                              LEFT JOIN '.DB_PREFIX.'customer ON '.DB_PREFIX.'customer.customer_id = `'.DB_PREFIX.'order`.customer_id
                                              WHERE order_id='.(int)$order_id);
                                              //WHERE order_id='.(int)$order_id.' AND order_status_id<=0');
            if ($order['order']->num_rows==0)
            {
                return FALSE;
            }

            $order['products']=$this->db->query('SELECT '.DB_PREFIX.'order_product.product_id,name,'.DB_PREFIX.'order_product.model,'.DB_PREFIX.'order_product.quantity,'.DB_PREFIX.'order_product.price,total,image
                                                FROM `'.DB_PREFIX.'order_product`
                                                LEFT JOIN `'.DB_PREFIX.'product` ON '.DB_PREFIX.'product.product_id = '.DB_PREFIX.'order_product.product_id
                                                WHERE order_id='.(int)$order_id);

            $order['emails'] = $this->db->query('select * from `'.DB_PREFIX.'order_emails` oe
                                    WHERE order_id='.(int)$order_id .' ORDER BY oe.order_email_id');
            return $order;
        }

        function getTotalUnconfirmedOrders(){
            $total = $this->db->query("SELECT count(*) as total FROM `".DB_PREFIX."order` o where order_status_id<=0");
            if($total->num_rows){
                return $total->row['total'];
            }else
                return 0;
        }

        function send_message($message,$email_subject, $email_from ='', $customer_email,$order_id)
        {

            $recipients=$this->db->query('SELECT IFNULL(c.email,o.email) as email,
                            concat(ifnull(c.firstname,o.firstname), \' \', ifnull(c.lastname,o.lastname)) as customer_name ,
                            ifnull(c.firstname,o.firstname) firstname,
                            ifnull(c.lastname,o.lastname) lastname, o.shipping_firstname,o.shipping_lastname,o.shipping_address_1,o.shipping_address_2,o.shipping_city,o.shipping_postcode,o.shipping_country,o.shipping_zone, order_id,o.store_id, o.store_name, o.store_url, o.currency_code, o.currency_value
                                          FROM `'.DB_PREFIX.'order` o
                                          LEFT JOIN '.DB_PREFIX.'customer c ON c.customer_id = o.customer_id
                                          WHERE order_id = '.(int)$order_id);

            if(!$recipients->num_rows){
                return FALSE;
            }
            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($customer_email);

            $settings = $this->getSetting('config', $recipients->row['store_id']);
            if($email_from!='') {
                $from = $email_from;
            }else {
                $from = $settings['config_email'];
            }
            
            $sender = $settings['config_name'];

            $mail->setFrom(!empty($from)?$from:$this->config->get('config_email'));
            $mail->setSender(!empty($sender)?$sender:$this->config->get('config_name'));

            $recipients->row['store'] = !empty($sender)?$sender:$this->config->get('config_name');

            $this->current_recipient = $recipients->row;


            $shortcodes_subject = new Shortcodes();
            $shortcodes_subject->add_shortcode('customername',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('order',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('customerfirstname',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('customerlastname',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('cost',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('store',array(&$this,'template_variables_subject'));

            $email_subject = $shortcodes_subject->do_shortcode($email_subject);


            $message = htmlspecialchars_decode($message);
            /*content short codes here*/
            $shortcodes = new Shortcodes();
            $shortcodes->add_shortcode('customername',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('customerfirstname',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('customerlastname',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('deliveryaddress',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('cost',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('products',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('order',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('store',array(&$this,'template_variables'));
            $message = $shortcodes->do_shortcode($message);

            $mail->setSubject($email_subject);
            $mail->setHtml($message);
            $this->current_recipient = false;


            $this->save_order_email($order_id, $email_subject, $message);
            $mail->send();

            /*if ($this->config->get('ccl_enable_admin_emails')) {
                $mail->setTo($this->config->get('config_email'));
                $mail->send();
            }*/


            return TRUE;
        }

        function save_order_email($order_id,$email_subject,$email_message){
            $this->db->query("insert into `".DB_PREFIX."order_emails` set order_id='".(int)$order_id."', date_added=now(),email_subject='".$this->db->escape($email_subject)."', email_message='".$this->db->escape($email_message)."'");
        }

        function get_templates()
        {
            return $this->db->query('SELECT template_id,template_subject FROM `'.DB_PREFIX.'lost_carts_templates` ORDER BY template_subject');
        }

        function get_template($template_id)
        {
            $template=$this->db->query('SELECT template_message,template_subject, template_from FROM `'.DB_PREFIX.'lost_carts_templates` WHERE template_id='.(int)$template_id);
            if ($template->num_rows==0)
            {
                return json_encode(array('message'=>'','subject'=>'', 'from'=>''));
            }
            return  json_encode(array('message'=>htmlspecialchars_decode($template->row['template_message']),'subject'=>$template->row['template_subject'], 'from'=>$template->row['template_from']));
        }

        function delete_templates($templates)
        {
            $new_templates=array();
            foreach($templates as $template)
            {
                $new_templates[]=(int)$template;
            }

            if (count($new_templates)==0)
            {
                return FALSE;
            }

            $this->db->query('DELETE FROM  `'.DB_PREFIX.'lost_carts_templates` WHERE template_id IN ('.implode(',',$new_templates).')');

            return TRUE;
        }

        function get_template_details($template_id)
        {
            $template=$this->db->query('SELECT template_id, template_from, template_subject,template_message FROM `'.DB_PREFIX.'lost_carts_templates` WHERE template_id='.(int)$template_id);
            if ($template->num_rows==0)
            {
                return FALSE;
            }

            return $template;
        }

        function update_template($template_id, $template_from, $template_subject,$template_message)
        {
            if ($template_id==0)
            {
                $this->db->query('INSERT INTO `'.DB_PREFIX.'lost_carts_templates` (template_id, template_from, template_subject,template_message) VALUES(null,"'.$this->db->escape($template_from).'", "'.$this->db->escape($template_subject).'","'.$this->db->escape($template_message).'")');
                return $this->db->getLastId();
            }

            $this->db->query('UPDATE `'.DB_PREFIX.'lost_carts_templates` SET template_from="'.$this->db->escape($template_from).'", template_subject="'.$this->db->escape($template_subject).'", template_message="'.$this->db->escape($template_message).'" WHERE template_id='.(int)$template_id);

            return TRUE;
        }

        function send_mass_messages($recipients,$message,$subject,$efrom='')
        {
            //print_r($_POST);
            //print_r($recipients);

            foreach($recipients as $recipient_id)
            {
                $new_recipients[]=(int)$recipient_id;
            }
            //changed the query to include order id and customer name. Will order ID to include other template variables like cart contents etc. but for now adding only name
            $recipients=$this->db->query('SELECT IFNULL(c.email,o.email) as email, concat(ifnull(c.firstname,o.firstname), \' \', ifnull(c.lastname,o.lastname)) as customer_name ,
                            ifnull(c.firstname,o.firstname) firstname,
                            ifnull(c.lastname,o.lastname) lastname, o.shipping_firstname,o.shipping_lastname,o.shipping_address_1,o.shipping_address_2,o.shipping_city,o.shipping_postcode,o.shipping_country,o.shipping_zone, order_id,o.store_id, o.store_name, o.store_url, o.currency_code, o.currency_value
                                          FROM `'.DB_PREFIX.'order` o
                                          LEFT JOIN '.DB_PREFIX.'customer c ON c.customer_id = o.customer_id
                                          WHERE order_id IN ('.implode(',',$new_recipients).') ');



            $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->username = $this->config->get('config_smtp_username');
                $mail->password = $this->config->get('config_smtp_password');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');


            //$mail->setFrom($this->config->get('config_email'));
            //$mail->setSender($this->config->get('config_name'));

            /*content short codes here*/
            $shortcodes = new Shortcodes();
            $shortcodes->add_shortcode('customername',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('customerfirstname',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('customerlastname',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('deliveryaddress',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('cost',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('products',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('order',array(&$this,'template_variables'));
            $shortcodes->add_shortcode('store',array(&$this,'template_variables'));

            $shortcodes_subject = new Shortcodes();
            $shortcodes_subject->add_shortcode('customername',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('order',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('customerfirstname',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('customerlastname',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('cost',array(&$this,'template_variables_subject'));
            $shortcodes_subject->add_shortcode('store',array(&$this,'template_variables_subject'));

            //$subject = $subject;
            $message = htmlspecialchars_decode($message);



            foreach($recipients->rows as $recipient)
            {

                $settings = $this->getSetting('config', $recipient['store_id']);
                
                if($efrom!='') {
                    $from = $efrom;
                } else {
                    $from = $settings['config_email'];
                }
                $sender = $settings['config_name'];

                $mail->setFrom(!empty($from)?$from:$this->config->get('config_email'));
                $mail->setSender(!empty($sender)?$sender:$this->config->get('config_name'));

                $recipient['store'] = !empty($sender)?$sender:$this->config->get('config_name');

                $mail->setTo($recipient['email']);
                $this->current_recipient = $recipient;

                $email_subject = $subject;
                $email_subject = $shortcodes_subject->do_shortcode($email_subject);

                $email_message = $message;
                $email_message = $shortcodes->do_shortcode($email_message);

                $mail->setSubject($email_subject);
                $mail->setHtml($email_message);
                $this->current_recipient = false;

                $this->save_order_email($recipient['order_id'], $email_subject, $email_message);
                $mail->send();


                /*if ($this->config->get('ccl_enable_admin_emails')) {
                    $mail->setTo($this->config->get('config_email'));
                    $mail->send();
                }*/
            }


            return TRUE;
        }

        /*ADD New functions */

        function template_variables($atts,$content,$variable){

            extract($this->shortcode_atts(array(
                    'class' => '',
                    'style' => '',
                    'href' => ''
            ), $atts));


            switch($variable){
                case 'customername':
                    if($this->current_recipient){
                        return sprintf('<span class="%s" style="%s">%s</span>',$class,$style,$this->current_recipient['customer_name']);
                    }
                    break;
                case 'customerfirstname':
                    if($this->current_recipient){
                        return sprintf('<span class="%s" style="%s">%s</span>',$class,$style,$this->current_recipient['firstname']);
                    }
                    break;
                case 'customerlastname':
                    if($this->current_recipient){
                        return sprintf('<span class="%s" style="%s">%s</span>',$class,$style,$this->current_recipient['lastname']);
                    }
                    break;
                case 'deliveryaddress':
                    if($this->current_recipient){
                        return sprintf('<div class="%s" style="%s">%s %s<br/>%s<br/>%s<br/>%s %s %s<br/>%s</div>',$class,$style,
                                        $this->current_recipient['shipping_firstname'],$this->current_recipient['shipping_lastname'],
                                        $this->current_recipient['shipping_address_1'],
                                        $this->current_recipient['shipping_address_2'],
                                        $this->current_recipient['shipping_city'],$this->current_recipient['shipping_zone'],$this->current_recipient['shipping_postcode'],
                                        $this->current_recipient['shipping_country']);
                    }
                    break;
                case 'cost':
                    $cost = $this->db->query("SELECT text from `".DB_PREFIX."order_total` ot where (title='Total:' or title='Total') and order_id='".(int)$this->current_recipient['order_id']."'");
                    if($cost->num_rows){
                        return sprintf('<span class="%s" style="%s">%s</span>',$class,$style,$cost->row['text']);
                    }
                    break;
                case 'products':
                    $products = $this->db->query("SELECT name,model,quantity,price,total,tax from `".DB_PREFIX."order_product` op where order_id='".(int)$this->current_recipient['order_id']."'");
                    $html = '';
                    if($products->num_rows){
                        $html = "<table class='cart_contents $class' style='$style'><thead><tr><th>".$this->language->get('Name').'</th><th>'.$this->language->get('Model').'</th><th>'.$this->language->get('Quantity').'</th><th>'.$this->language->get('Unit Price').'</th><th>'.$this->language->get('Total').'</th></tr></thead>';
                        foreach($products->rows as $product){
                            $html .= '<tr><td>'.$product['name'].'</td><td>'.$product['model'].'</td><td>'.$product['quantity'].'</td><td>'.$this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $this->current_recipient['currency_code'], $this->current_recipient['currency_value']).'</td><td>'.$this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $this->current_recipient['currency_code'], $this->current_recipient['currency_value']).'</td></tr>';
                        }

                        $totals = $this->db->query("SELECT title,text from `".DB_PREFIX."order_total` ot where order_id='".(int)$this->current_recipient['order_id']."' order by sort_order asc");
                        if($totals->num_rows){
                            $html .= '<tfoot>';
                            foreach($totals->rows as $total){
                                $html .= '<tr><th colspan="4">'.$total['title'].'</th><th>'.$total['text'].'</th></tr>';
                            }
                            $html .= '</tfoot>';
                        }

                        $html .= '</table>';
                    }
                    return $html;
                    break;
                case 'order':
                    if($this->current_recipient){
                        return $this->current_recipient['order_id'];
                        //return sprintf('<span class="%s" style="%s">%s</span>',$class,$style,$this->current_recipient['order_id']);
                    }
                    break;
                case 'store':
                    if($this->current_recipient){
                        return sprintf('<span class="%s" style="%s"><a href="%s">%s</a></span>',$class,$style,$this->current_recipient['store_url'],$this->current_recipient['store_name']);
                    }
                    break;
            }

        }

        function template_variables_subject($atts,$content,$variable){


            switch($variable){
                case 'customername':
                    if($this->current_recipient){
                        return $this->current_recipient['customer_name'];
                    }
                    break;
                case 'order':
                    if($this->current_recipient){
                        return $this->current_recipient['order_id'];
                    }
                    break;
                case 'customerfirstname':
                    if($this->current_recipient){
                        return $this->current_recipient['firstname'];
                    }
                    break;
                case 'customerlastname':
                    if($this->current_recipient){
                        return $this->current_recipient['lastname'];
                    }
                    break;

                case 'cost':
                    $cost = $this->db->query("SELECT text from `".DB_PREFIX."order_total` ot where (title='Total:' or title='Total') and order_id='".(int)$this->current_recipient['order_id']."'");
                    if($cost->num_rows){
                        return $cost->row['text'];
                    }
                    break;
                 case 'store':
                    if($this->current_recipient){
                        return $this->current_recipient['store_name'];
                    }
                    break;
            }

        }

        /**
        * Combine user attributes with known attributes and fill in defaults when needed.
        *
        * The pairs should be considered to be all of the attributes which are
        * supported by the caller and given as a list. The returned attributes will
        * only contain the attributes in the $pairs list.
        *
        * If the $atts list has unsupported attributes, then they will be ignored and
        * removed from the final returned list.
        *
        * @since 2.5
        *
        * @param array $pairs Entire list of supported attributes and their defaults.
        * @param array $atts User defined attributes in shortcode tag.
        * @return array Combined and filtered attribute list.
        */
        public function shortcode_atts($pairs, $atts) {
                $atts = (array)$atts;
                $out = array();
                foreach($pairs as $name => $default) {
                        if ( array_key_exists($name, $atts) )
                                $out[$name] = $atts[$name];
                        else
                                $out[$name] = $default;
                }
                return $out;
        }

        function get_order_status_list()
        {
            $status_list = $this->db->query('SELECT  *
                                    FROM `'.DB_PREFIX.'order_status` s
                                    where language_id=' .(int)$this->config->get('config_language_id') . '
                                    ORDER BY s.order_status_id');

            $status_data = array();
            foreach($status_list->rows as $row){
                $status_data[$row['order_status_id']] = $row['name'];
            }
            return $status_data;
        }

        function get_confirmed_orders($data=array())
        {
            if(isset($data['order_status_id'])){
                return $this->db->query('SELECT o.order_id, CONCAT(o.firstname, " ", o.lastname) AS customer, o.store_name, o.total, os.name AS `status`, o.date_added, o.date_modified, (select count(*) from `'.DB_PREFIX.'order_emails` oe where o.order_id=oe.order_id AND oe.admin_notify = 0) as total_emails
                                    FROM `'.DB_PREFIX.'order` o JOIN `'.DB_PREFIX.'order_status` os on o.order_status_id=os.order_status_id
                                    WHERE o.order_status_id ='.(int)$data['order_status_id']. ' and os.language_id=' .(int)$this->config->get('config_language_id') . '
                                    ORDER BY os.order_status_id ASC,o.order_id ASC');
            }else{
				
                return $this->db->query('SELECT o.order_id, CONCAT(o.firstname, " ", o.lastname) AS customer, o.store_name, o.total, os.name AS `status`, o.date_added, o.date_modified, (select count(*) from `'.DB_PREFIX.'order_emails` oe where o.order_id=oe.order_id AND oe.admin_notify = 0 AND o.date_added > DATE_SUB(CURDATE(), INTERVAL 15 DAY)) as total_emails
                                    FROM `'.DB_PREFIX.'order` o JOIN `'.DB_PREFIX.'order_status` os on o.order_status_id=os.order_status_id
                                    WHERE o.order_status_id > 0 and os.language_id=' .(int)$this->config->get('config_language_id') . ' AND o.date_added > DATE_SUB(CURDATE(), INTERVAL 15 DAY)
                                    ORDER BY os.order_status_id ASC,o.order_id ASC');
            }
        }

        public function getSetting($group) {
		$data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE  `group` = '" . $this->db->escape($group) . "'");

		foreach ($query->rows as $result) {
			//if (!$result['serialized']) {
				$data[$result['key']] = $result['value'];
			//} else {
			//	$data[$result['key']] = unserialize($result['value']);
			//}
		}

		return $data;
	}

    }

//added new class
class Shortcodes {
    /**
    * WordPress API for creating bbcode like tags or what WordPress calls
    * "shortcodes." The tag and attribute parsing or regular expression code is
    * based on the Textpattern tag parser.
    *
    * A few examples are below:
    *
    * [shortcode /]
    * [shortcode foo="bar" baz="bing" /]
    * [shortcode foo="bar"]content[/shortcode]
    *
    * Shortcode tags support attributes and enclosed content, but does not entirely
    * support inline shortcodes in other shortcodes. You will have to call the
    * shortcode parser in your function to account for that.
    *
    * {@internal
    * Please be aware that the above note was made during the beta of WordPress 2.6
    * and in the future may not be accurate. Please update the note when it is no
    * longer the case.}}
    *
    * To apply shortcode tags to content:
    *
    * <code>
    * $out = do_shortcode($content);
    * </code>
    *
    * @link http://codex.wordpress.org/Shortcode_API
    *
    * @package WordPress
    * @subpackage Shortcodes
    * @since 2.5
    */

    /**
    * Container for storing shortcode tags and their hook to call for the shortcode
    *
    * @since 2.5
    * @name $shortcode_tags
    * @var array
    * @global array $shortcode_tags
    */
    public $shortcode_tags = array();


    /**
    * Add hook for shortcode tag.
    *
    * There can only be one hook for each shortcode. Which means that if another
    * plugin has a similar shortcode, it will override yours or yours will override
    * theirs depending on which order the plugins are included and/or ran.
    *
    * Simplest example of a shortcode tag using the API:
    *
    * <code>
    * // [footag foo="bar"]
    * function footag_func($atts) {
    * 	return "foo = {$atts[foo]}";
    * }
    * add_shortcode('footag', 'footag_func');
    * </code>
    *
    * Example with nice attribute defaults:
    *
    * <code>
    * // [bartag foo="bar"]
    * function bartag_func($atts) {
    * 	extract(shortcode_atts(array(
    * 		'foo' => 'no foo',
    * 		'baz' => 'default baz',
    * 	), $atts));
    *
    * 	return "foo = {$foo}";
    * }
    * add_shortcode('bartag', 'bartag_func');
    * </code>
    *
    * Example with enclosed content:
    *
    * <code>
    * // [baztag]content[/baztag]
    * function baztag_func($atts, $content='') {
    * 	return "content = $content";
    * }
    * add_shortcode('baztag', 'baztag_func');
    * </code>
    *
    * @since 2.5
    * @uses $shortcode_tags
    *
    * @param string $tag Shortcode tag to be searched in post content.
    * @param callable $func Hook to run when shortcode is found.
    */
    public function add_shortcode($tag, $func) {

            if ( is_callable($func) )
                    $this->shortcode_tags[$tag] = $func;
    }

    /**
    * Removes hook for shortcode.
    *
    * @since 2.5
    * @uses $shortcode_tags
    *
    * @param string $tag shortcode tag to remove hook for.
    */
    public function remove_shortcode($tag) {

            unset($this->shortcode_tags[$tag]);
    }

    /**
    * Clear all shortcodes.
    *
    * This function is simple, it clears all of the shortcode tags by replacing the
    * shortcodes global by a empty array. This is actually a very efficient method
    * for removing all shortcodes.
    *
    * @since 2.5
    * @uses $shortcode_tags
    */
    public function remove_all_shortcodes() {


            $this->shortcode_tags = array();
    }

    /**
    * Search content for shortcodes and filter shortcodes through their hooks.
    *
    * If there are no shortcode tags defined, then the content will be returned
    * without any filtering. This might cause issues when plugins are disabled but
    * the shortcode will still show up in the post or content.
    *
    * @since 2.5
    * @uses $shortcode_tags
    * @uses get_shortcode_regex() Gets the search pattern for searching shortcodes.
    *
    * @param string $content Content to search for shortcodes
    * @return string Content with shortcodes filtered out.
    */
    public function do_shortcode($content) {

            if (empty($this->shortcode_tags) || !is_array($this->shortcode_tags))
                    return $content;

            $pattern = $this->get_shortcode_regex();
            return preg_replace_callback( "/$pattern/s", array( &$this, 'do_shortcode_tag'), $content );
    }

    /**
    * Retrieve the shortcode regular expression for searching.
    *
    * The regular expression combines the shortcode tags in the regular expression
    * in a regex class.
    *
    * The regular expression contains 6 different sub matches to help with parsing.
    *
    * 1 - An extra [ to allow for escaping shortcodes with double [[]]
    * 2 - The shortcode name
    * 3 - The shortcode argument list
    * 4 - The self closing /
    * 5 - The content of a shortcode when it wraps some content.
    * 6 - An extra ] to allow for escaping shortcodes with double [[]]
    *
    * @since 2.5
    * @uses $shortcode_tags
    *
    * @return string The shortcode search regular expression
    */
    public function get_shortcode_regex() {
            $tagnames = array_keys($this->shortcode_tags);
            $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

            // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
            return
                    '\\['                              // Opening bracket
                    . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
                    . "($tagregexp)"                     // 2: Shortcode name
                    . '\\b'                              // Word boundary
                    . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
                    .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
                    .     '(?:'
                    .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
                    .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
                    .     ')*?'
                    . ')'
                    . '(?:'
                    .     '(\\/)'                        // 4: Self closing tag ...
                    .     '\\]'                          // ... and closing bracket
                    . '|'
                    .     '\\]'                          // Closing bracket
                    .     '(?:'
                    .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                    .             '[^\\[]*+'             // Not an opening bracket
                    .             '(?:'
                    .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
                    .                 '[^\\[]*+'         // Not an opening bracket
                    .             ')*+'
                    .         ')'
                    .         '\\[\\/\\2\\]'             // Closing shortcode tag
                    .     ')?'
                    . ')'
                    . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }

    /**
    * Regular Expression callable for do_shortcode() for calling shortcode hook.
    * @see get_shortcode_regex for details of the match array contents.
    *
    * @since 2.5
    * @access private
    * @uses $shortcode_tags
    *
    * @param array $m Regular expression match array
    * @return mixed False on failure.
    */
    public function do_shortcode_tag( $m ) {

            // allow [[foo]] syntax for escaping a tag
            if ( $m[1] == '[' && $m[6] == ']' ) {
                    return substr($m[0], 1, -1);
            }

            $tag = $m[2];
            $attr = $this->shortcode_parse_atts( $m[3] );

            if ( isset( $m[5] ) ) {
                    // enclosing tag - extra parameter
                    return $m[1] . call_user_func( $this->shortcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
            } else {
                    // self-closing tag
                    return $m[1] . call_user_func( $this->shortcode_tags[$tag], $attr, NULL,  $tag ) . $m[6];
            }
    }

    /**
    * Retrieve all attributes from the shortcodes tag.
    *
    * The attributes list has the attribute name as the key and the value of the
    * attribute as the value in the key/value pair. This allows for easier
    * retrieval of the attributes, since all attributes have to be known.
    *
    * @since 2.5
    *
    * @param string $text
    * @return array List of attributes and their value.
    */
    public function shortcode_parse_atts($text) {
            $atts = array();
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
            $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
            if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
                    foreach ($match as $m) {
                            if (!empty($m[1]))
                                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                            elseif (!empty($m[3]))
                                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                            elseif (!empty($m[5]))
                                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                            elseif (isset($m[7]) and strlen($m[7]))
                                    $atts[] = stripcslashes($m[7]);
                            elseif (isset($m[8]))
                                    $atts[] = stripcslashes($m[8]);
                    }
            } else {
                    $atts = ltrim($text);
            }
            return $atts;
    }

    /**
    * Combine user attributes with known attributes and fill in defaults when needed.
    *
    * The pairs should be considered to be all of the attributes which are
    * supported by the caller and given as a list. The returned attributes will
    * only contain the attributes in the $pairs list.
    *
    * If the $atts list has unsupported attributes, then they will be ignored and
    * removed from the final returned list.
    *
    * @since 2.5
    *
    * @param array $pairs Entire list of supported attributes and their defaults.
    * @param array $atts User defined attributes in shortcode tag.
    * @return array Combined and filtered attribute list.
    */
    public function shortcode_atts($pairs, $atts) {
            $atts = (array)$atts;
            $out = array();
            foreach($pairs as $name => $default) {
                    if ( array_key_exists($name, $atts) )
                            $out[$name] = $atts[$name];
                    else
                            $out[$name] = $default;
            }
            return $out;
    }

    /**
    * Remove all shortcode tags from the given content.
    *
    * @since 2.5
    * @uses $shortcode_tags
    *
    * @param string $content Content to remove shortcode tags.
    * @return string Content without shortcode tags.
    */
    public function strip_shortcodes( $content ) {


            if (empty($this->shortcode_tags) || !is_array($this->shortcode_tags))
                    return $content;

            $pattern = $this->get_shortcode_regex();

            return preg_replace_callback( "/$pattern/s", array( &$this, 'strip_shortcode_tag'), $content );
    }

    public function strip_shortcode_tag( $m ) {
            // allow [[foo]] syntax for escaping a tag
            if ( $m[1] == '[' && $m[6] == ']' ) {
                    return substr($m[0], 1, -1);
            }

            return $m[1] . $m[6];
    }


}

?>
