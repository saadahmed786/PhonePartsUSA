<?php
    class ControllerModuleCombatCartLoss extends Controller
    {
        public function index()
        {
            if(method_exists($this->load,'language')){
                $this->load->language('module/combat_cart_loss');
            }else{
                $this->language->load('module/combat_cart_loss');
            }

            $this->load->model('tool/combat_cart_loss');

            $this->model_tool_combat_cart_loss->install();
            //$this->model_tool_combat_cart_loss->add_update_field();

            $this->document->setTitle($this->language->get('heading_title') . ' - ' .$this->model_tool_combat_cart_loss->getVersion());


            $this->data['error_warning']=$this->data['success']='';

            if (isset($_POST['delete_order']))
            {
                if ($this->model_tool_combat_cart_loss->delete_orders($_POST['delete_order']))
                {
                    $this->data['success']=$this->language->get('message_orders_deleted');
                }
                else
                {
                    $this->data['error_warning']= $this->language->get('error_orders_not_deleted');
                }
            }

            if (isset($_POST['delete_template']))
            {
                if ($this->model_tool_combat_cart_loss->delete_templates($_POST['delete_template']))
                {
                    $this->data['success']=$this->language->get('message_templates_deleted');
                }
                else
                {
                    $this->data['error_warning']= $this->language->get('error_templates_not_deleted');
                }
            }
            
            $this->data['orders']=$this->model_tool_combat_cart_loss->get_orders();
            //echo "<pre>"; print_r($this->data['orders']); die();
            $this->data['templates']=$this->model_tool_combat_cart_loss->get_templates();
			
            /*Added Order Status*/
                $this->data['order_status']=$this->model_tool_combat_cart_loss->get_order_status_list();
                $this->data['confirmed_orders']=$this->model_tool_combat_cart_loss->get_confirmed_orders();
				//echo "<pre>"; print_r($this->data['confirmed_orders']);die();
				//$this->data['confirmed_orders']=array();
            /*Added Order Status*/
            $this->data['token'] = $this->session->data['token'];

            $this->data['heading_title'] = $this->language->get('heading_title'). ' - ' .$this->model_tool_combat_cart_loss->getVersion();

            $this->data['title_unconfirmed_orders']=$this->language->get('title_unconfirmed_orders');
            $this->data['title_templates_list']=$this->language->get('title_templates_list');
            /*Added More Language translations*/
            $this->data['title_confirmed_orders']=$this->language->get('title_confirmed_orders');
            $this->data['title_settings']=$this->language->get('title_settings');
            $this->data['title_autoemail_settings']=$this->language->get('title_autoemail_settings');
            /*Added More Language translations*/

            $this->data['title_order_id']=$this->language->get('title_order_id');
            $this->data['title_order_customer']=$this->language->get('title_order_customer');
            $this->data['title_order_total']=$this->language->get('title_order_total');
            $this->data['title_order_added']=$this->language->get('title_order_added');
            $this->data['title_order_modified']=$this->language->get('title_order_modified');
            $this->data['title_order_contacted']=$this->language->get('title_order_contacted');


            $this->data['title_yes']=$this->language->get('title_yes');
            $this->data['title_no']=$this->language->get('title_no');
            $this->data['title_detail']=$this->language->get('title_detail');
            $this->data['title_order_emails']=$this->language->get('title_order_emails');

            $this->data['title_button_delete']=$this->language->get('title_button_delete');
            $this->data['button_save']=$this->language->get('button_save');

            $this->data['message_no_orders']=$this->language->get('message_no_orders');


            $this->data['question_delete_orders']=$this->language->get('question_delete_orders');
            $this->data['question_delete_templates']=$this->language->get('question_delete_templates');

            $this->data['message_no_templates']=$this->language->get('message_no_templates');

            $this->data['title_template_subject']=$this->language->get('title_template_subject');
            $this->data['title_edit_template']=$this->language->get('title_edit_template');
            $this->data['title_new_template']=$this->language->get('title_new_template');

            $this->data['title_send_mass_message']=$this->language->get('title_send_mass_message');

            $this->data['error_no_orders_selected']=$this->language->get('error_no_orders_selected');
            $this->data['error_no_carts_selected']=$this->language->get('error_no_carts_selected');

            $this->data['breadcrumbs'] = array();
            $this->data['breadcrumbs'][] = array(
               'text'      => $this->language->get('text_home'),
               'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
               'separator' => false
            );

            $this->data['breadcrumbs'][] = array(
               'text'      => $this->language->get('text_module'),
               'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
               'separator' => ' :: '
           );

           $this->data['breadcrumbs'][] = array(
               'text'      => $this->language->get('heading_title'),
               'href'      => $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL'),
               'separator' => ' :: '
           );

           /*Settings*/
           $this->load->model('setting/setting');

           $this->data['text_yes'] = $this->language->get('text_yes');
            $this->data['text_no'] = $this->language->get('text_no');
             $this->data['entry_admin_email'] = $this->language->get('entry_admin_email');
             $this->data['entry_ccl_email_subject'] = $this->language->get('entry_ccl_email_subject');
             $this->data['entry_ccl_email_message'] = $this->language->get('entry_ccl_email_message');
           
             $this->data['entry_auto_email'] = $this->language->get('entry_auto_email');
            $this->data['entry_ccl_autoemail_subject'] = $this->language->get('entry_ccl_autoemail_subject');
            $this->data['entry_ccl_autoemail_message'] = $this->language->get('entry_ccl_autoemail_message');
            
            $this->data['entry_auto_coupon_value'] = $this->language->get('entry_auto_coupon_value');
            $this->data['entry_auto_coupon_total'] = $this->language->get('entry_auto_coupon_total');
            $this->data['entry_auto_coupon_duration'] = $this->language->get('entry_auto_coupon_duration');
            
            $this->data['ccl_auto_mon'] = $this->config->get('ccl_auto_mon');
            $this->data['ccl_auto_tue'] = $this->config->get('ccl_auto_tue');
            $this->data['ccl_auto_wed'] = $this->config->get('ccl_auto_wed');
            $this->data['ccl_auto_thu'] = $this->config->get('ccl_auto_thu');
            $this->data['ccl_auto_fri'] = $this->config->get('ccl_auto_fri');
            $this->data['ccl_auto_sat'] = $this->config->get('ccl_auto_sat');
            $this->data['ccl_auto_sun'] = $this->config->get('ccl_auto_sun');
            
            if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['ccl_enable_admin_emails'])) {
                    
                    $settings = array();
                    $settings['ccl_enable_admin_emails'] = $this->request->post['ccl_enable_admin_emails'];
                    $settings['ccl_admin_email_subject'] = $this->request->post['ccl_admin_email_subject'];
                    $settings['ccl_admin_email_message'] = $this->request->post['ccl_admin_email_message'];
                    $this->model_setting_setting->editSetting('ccl', $settings);

            }
            if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['ccl_enable_auto_emails'])) {
               
                $autosettings = array();
                $autosettings['ccl_enable_auto_emails'] = $this->request->post['ccl_enable_auto_emails'];
                $autosettings['ccl_auto_email_from'] = $this->request->post['ccl_auto_email_from'];
                $autosettings['ccl_auto_email_subject'] = $this->request->post['ccl_auto_email_subject'];
                $autosettings['ccl_auto_email_message'] = $this->request->post['ccl_auto_email_message'];
                $autosettings['ccl_auto_coupon_value'] = $this->request->post['ccl_auto_coupon_value'];
                $autosettings['ccl_auto_coupon_total'] = $this->request->post['ccl_auto_coupon_total'];
                $autosettings['ccl_auto_coupon_duration'] = $this->request->post['ccl_auto_coupon_duration'];
                $this->model_setting_setting->editSetting('cclauto', $autosettings);
                
                //Save Automated email sending days
                
                $ccldays = array();
            
                //echo "<pre>"; print_r($this->request->post); die();
                if (isset($this->request->post['ccl_auto_mon'])) {
                        $this->data['ccl_auto_mon'] = $ccldays['ccl_auto_mon'] = '1';
                } else {
                        $this->data['ccl_auto_mon'] = $ccldays['ccl_auto_mon'] = '0';
                        
                }

                if (isset($this->request->post['ccl_auto_tue'])) {
                        $this->data['ccl_auto_tue'] = $ccldays['ccl_auto_tue'] = '1';
                } else {
                         $this->data['ccl_auto_tue'] = $ccldays['ccl_auto_tue'] = '0';
                        
                }

                if (isset($this->request->post['ccl_auto_wed'])) {
                        $this->data['ccl_auto_wed'] = $ccldays['ccl_auto_wed'] = '1';
                } else {
                        $this->data['ccl_auto_wed'] = $ccldays['ccl_auto_wed'] = '0';
                        
                }

                if (isset($this->request->post['ccl_auto_thu'])) {
                        $this->data['ccl_auto_thu'] = $ccldays['ccl_auto_thu'] = '1';
                } else {
                        $this->data['ccl_auto_thu'] = $ccldays['ccl_auto_thu'] = '0';
                        
                }

                if (isset($this->request->post['ccl_auto_fri'])) {
                        $this->data['ccl_auto_fri'] = $ccldays['ccl_auto_fri'] = '1';
                } else {
                        $this->data['ccl_auto_fri'] = $ccldays['ccl_auto_fri'] = '0';
                        
                }

                if (isset($this->request->post['ccl_auto_sat'])) {
                        $this->data['ccl_auto_sat'] = $ccldays['ccl_auto_sat'] = '1';
                } else {
                         $this->data['ccl_auto_sat'] = $ccldays['ccl_auto_sat'] = '0';
                        
                }

                if (isset($this->request->post['ccl_auto_sun'])) {
                    $this->data['ccl_auto_sun'] = $ccldays['ccl_auto_sun'] = '1';
                } else {
                    $this->data['ccl_auto_sun'] = $ccldays['ccl_auto_sun'] = '0';
                       
                }
                $this->model_setting_setting->editSetting('ccldays', $ccldays);
                
            }
            
            if (isset($this->request->post['ccl_auto_coupon_value'])) {
                    $this->data['ccl_auto_coupon_value'] = $this->request->post['ccl_auto_coupon_value'];
            } else {
                    $this->data['ccl_auto_coupon_value'] = $this->config->get('ccl_auto_coupon_value');
            }
            
            if (isset($this->request->post['ccl_auto_coupon_total'])) {
                    $this->data['ccl_auto_coupon_total'] = $this->request->post['ccl_auto_coupon_total'];
            } else {
                    $this->data['ccl_auto_coupon_total'] = $this->config->get('ccl_auto_coupon_total');
            }
            
            if (isset($this->request->post['ccl_auto_coupon_duration'])) {
                    $this->data['ccl_auto_coupon_duration'] = $this->request->post['ccl_auto_coupon_duration'];
            } else {
                    $this->data['ccl_auto_coupon_duration'] = $this->config->get('ccl_auto_coupon_duration');
            }
            
            if (isset($this->request->post['ccl_enable_admin_emails'])) {
                    $this->data['ccl_enable_admin_emails'] = $this->request->post['ccl_enable_admin_emails'];
            } else {
                    $this->data['ccl_enable_admin_emails'] = $this->config->get('ccl_enable_admin_emails');
            }

            if (isset($this->request->post['ccl_admin_email_subject'])) {
                    $this->data['ccl_admin_email_subject'] = $this->request->post['ccl_admin_email_subject'];
            } elseif($this->config->get('ccl_admin_email_subject')!=null){
                $this->data['ccl_admin_email_subject'] = $this->config->get('ccl_admin_email_subject');
            } else {
                $this->data['ccl_admin_email_subject'] = '';
            }

            if (isset($this->request->post['ccl_admin_email_message'])) {
                    $this->data['ccl_admin_email_message'] = $this->request->post['ccl_admin_email_message'];
            } elseif($this->config->get('ccl_admin_email_message')!=null){
                $this->data['ccl_admin_email_message'] = $this->config->get('ccl_admin_email_message');
            } else {
                    $this->data['ccl_admin_email_message'] = '';
            }
            
            /* Sending Automated email to customer */

            if (isset($this->request->post['ccl_enable_auto_emails'])) {
                    $this->data['ccl_enable_auto_emails'] = $this->request->post['ccl_enable_auto_emails'];
            } else {
                    $this->data['ccl_enable_auto_emails'] = $this->config->get('ccl_enable_auto_emails');
            }
            
            if (isset($this->request->post['ccl_auto_email_from'])) {
                    $this->data['ccl_auto_email_from'] = $this->request->post['ccl_auto_email_from'];
            } elseif($this->config->get('ccl_auto_email_from')!=null){
                $this->data['ccl_auto_email_from'] = $this->config->get('ccl_auto_email_from');
            } else {
                $this->data['ccl_auto_email_from'] = '';
            }
            
            if (isset($this->request->post['ccl_auto_email_subject'])) {
                    $this->data['ccl_auto_email_subject'] = $this->request->post['ccl_auto_email_subject'];
            } elseif($this->config->get('ccl_auto_email_subject')!=null){
                $this->data['ccl_auto_email_subject'] = $this->config->get('ccl_auto_email_subject');
            } else {
                $this->data['ccl_auto_email_subject'] = '';
            }

            if (isset($this->request->post['ccl_auto_email_message'])) {
                    $this->data['ccl_auto_email_message'] = $this->request->post['ccl_auto_email_message'];
            } elseif($this->config->get('ccl_auto_email_message')!=null){
                $this->data['ccl_auto_email_message'] = $this->config->get('ccl_auto_email_message');
            } else {
                    $this->data['ccl_auto_email_message'] = '';
            }
            /* End automated email to customer */

            
           //$this->load->model('design/layout');

           $this->template = 'module/combat_cart_loss.tpl';
           $this->children = array(
                'common/header',
                'common/footer'
            );

           $this->response->setOutput($this->render());
        }

        public function get_more_orders() {
		$json = array();

		if (isset($this->request->get['order_status_id'])) {
			$this->load->model('tool/combat_cart_loss');

			$data = array(
				'order_status_id' => $this->request->get['order_status_id']
			);

                        $results = $this->model_tool_combat_cart_loss->get_confirmed_orders($data);
                        foreach($results->rows as $row){
                            $json[] = array_merge($row,array(
                                        'date_added'=>date('d M Y H:i:s',strtotime($row['date_added'])),
                                        'date_modified'=>date('d M Y H:i:s',strtotime($row['date_modified']))
                                        ));
                        }
		}

                if (isset($this->request->get['unconfirmed'])) {
			$this->load->model('tool/combat_cart_loss');

                        $results = $this->model_tool_combat_cart_loss->get_orders();
                        foreach($results->rows as $row){
                            $json[] = array_merge($row,array(
                                        'date_added'=>date('d M Y H:i:s',strtotime($row['date_added'])),
                                        'date_modified'=>date('d M Y H:i:s',strtotime($row['date_modified']))
                                        ));
                        }
		}
		$this->response->setOutput(json_encode($json));
	}

        public function install()
        {
            $this->config->set('MIN_VERSION_CCL','1.5.3');
            $this->config->set('MAX_VERSION_CCL','1.5.7');
            if(version_compare(VERSION,$this->config->get('MIN_VERSION_CCL'))>=0 && version_compare(VERSION,$this->config->get('MAX_VERSION_CCL'))<1){
                $this->load->model('tool/combat_cart_loss');
                $this->model_tool_combat_cart_loss->install();
            }else{
                $this->load->model('setting/extension');
                $this->model_setting_extension->uninstall('module', 'combat_cart_loss');
                $this->session->data['error'] = $this->language->get('ccl_incompatible');
            }
        }

        function uninstall()
        {
            $this->load->model('tool/combat_cart_loss');
            $this->model_tool_combat_cart_loss->uninstall();
        }

        function order_details()
        {

            if(method_exists($this->load,'language')){
                $this->load->language('module/combat_cart_loss');
            }else{
                $this->language->load('module/combat_cart_loss');
            }


            $this->document->setTitle($this->language->get('order_details_title'));

            $this->load->model('tool/combat_cart_loss');

            $this->data['error_warning']=$this->data['success']='';
            if (isset($_POST['email_message']) AND ($_POST['email_message']) AND isset($_POST['email_subject']) AND $_POST['email_subject'])
            {

                if ($this->model_tool_combat_cart_loss->send_message($this->request->post['email_message'],$this->request->post['email_subject'], $this->request->post['email_from'],$this->request->post['customer_email'],$this->request->get['order_id']))
                {
                    $this->data['success']=$this->language->get('message_sent');
                }
                else
                {
                    $this->data['error_warning']=$this->language->get('error_message_not_sent');
                }
            }

            $this->data['order']=$this->model_tool_combat_cart_loss->get_unconfirmed_order((int)$this->request->get['order_id']);
            $this->data['templates']=$this->model_tool_combat_cart_loss->get_templates();

            $this->data['message_no_order']=$this->language->get('message_no_order');
            $this->data['order_title']=$this->language->get('order_title');

            $this->data['title_product_name']=$this->language->get('title_product_name');
            $this->data['title_product_model']=$this->language->get('title_product_model');
            $this->data['title_product_quantity']=$this->language->get('title_product_quantity');
            $this->data['title_product_total']=$this->language->get('title_product_total');

            $this->data['title_date']=$this->language->get('title_date');
            $this->data['title_email_subject']=$this->language->get('title_email_subject');
            $this->data['title_email_message']=$this->language->get('title_email_message');


            $this->data['title_customer_name']=$this->language->get('title_customer_name');
            $this->data['title_customer_email']=$this->language->get('title_customer_email');
            $this->data['title_customer_telephone']=$this->language->get('title_customer_telephone');
            $this->data['title_order_total']=$this->language->get('title_order_total');

            $this->data['title_back_to_module']=$this->language->get('title_back_to_module');

            $this->data['title_button_send_message']=$this->language->get('title_button_send_message');
            $this->data['title_message_to_customer']=$this->language->get('title_message_to_customer');
            $this->data['title_message_subject']=$this->language->get('title_message_subject');
            $this->data['title_message_from']=$this->language->get('title_message_from');
            $this->data['title_message_template']=$this->language->get('title_message_template');
            $this->data['message_no_templates']=$this->language->get('message_no_templates');
            $this->data['message_default_template']=$this->language->get('message_default_template');

            $this->data['message_no_products']=$this->language->get('message_no_products');
            $this->data['message_no_emails']=$this->language->get('message_no_emails');

            $this->data['breadcrumbs'] = array();
            $this->data['breadcrumbs'][] = array(
               'text'      => $this->language->get('text_home'),
               'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
               'separator' => false
            );

            $this->data['breadcrumbs'][] = array(
               'text'      => $this->language->get('text_module'),
               'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
               'separator' => ' :: '
           );

           $this->data['breadcrumbs'][] = array(
               'text'      => $this->language->get('heading_title'),
               'href'      => $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL'),
               'separator' => ' :: '
           );

           $this->load->model('design/layout');

            $this->template = 'module/combat_cart_loss_order.tpl';
            $this->children = array(
                    'common/header',
                    'common/footer',
            );

            $this->response->setOutput($this->render());

        }



        function get_template()
        {
            $this->load->model('tool/combat_cart_loss');
            echo $this->model_tool_combat_cart_loss->get_template((int)$this->request->get['template_id']);
        }

        function template_edit($is_existing=TRUE)
        {
            if(method_exists($this->load,'language')){
                $this->load->language('module/combat_cart_loss');
            }else{
                $this->language->load('module/combat_cart_loss');
            }

            $this->document->setTitle($this->language->get('order_details_title'));

            $this->load->model('tool/combat_cart_loss');

            $this->data['error_warning']=$this->data['success']='';

            $valid_fields=TRUE;
            if (isset($_POST['template_id']) AND ($_POST['template_id']>=0))
            {
                if ($this->validate_template())
                {
                    if ($template_id=$this->model_tool_combat_cart_loss->update_template($this->request->post['template_id'],$this->request->post['template_from'], $this->request->post['template_subject'],$this->request->post['template_message']))
                    {
                        if (is_numeric($template_id))
                        {
                            header('Location: '.str_replace('&amp;','&',$this->url->link('module/combat_cart_loss/template_edit','template_id='.$template_id.'&added=true&token=' . $this->session->data['token'], 'SSL')));
                        }
                        else
                        {
                            $this->data['success']=$this->language->get('message_template_updated');
                        }
                    }
                    else
                    {
                        $this->data['error_warning']=$this->language->get('error_template_not_updated');
                    }

                }
                else
                {
                    $valid_fields=FALSE;
                    $this->data['template']= new stdClass();
                    $this->data['template']->row=array('template_id'=>$this->request->post['template_id'],'template_from'=>$this->request->post['template_from'],'template_subject'=>$this->request->post['template_subject'],'template_message'=>$this->request->post['template_message']);
                    $this->data['error_warning']=$this->language->get('error_check_template_fields');
                }
            }

            if ($is_existing AND $valid_fields)
            {
                $this->data['template']=$this->model_tool_combat_cart_loss->get_template_details((int)$this->request->get['template_id']);

                if (isset($_GET['added']) AND ($_GET['added']==='true'))
                {
                    $this->data['success'] = $this->language->get('message_template_added');
                }
            }
            elseif($valid_fields)
            {
                $this->data['template']=TRUE;
            }

            $this->data['message_no_template']=$this->language->get('message_no_template');
            $this->data['title_template_edit']=$this->language->get('title_template_edit');
            $this->data['title_template_subject']=$this->language->get('title_template_subject');
            $this->data['title_template_from']=$this->language->get('title_template_from');
            $this->data['title_template_message']=$this->language->get('title_template_message');

            $this->data['title_save_template']=$this->language->get('title_save_template');

            $this->data['title_back_to_module']=$this->language->get('title_back_to_module');

            $this->data['breadcrumbs'] = array();
            $this->data['breadcrumbs'][] = array(
               'text'      => $this->language->get('text_home'),
               'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
               'separator' => false
            );

            $this->data['breadcrumbs'][] = array(
               'text'      => $this->language->get('text_module'),
               'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
               'separator' => ' :: '
           );

           $this->data['breadcrumbs'][] = array(
               'text'      => $this->language->get('heading_title'),
               'href'      => $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL'),
               'separator' => ' :: '
           );

           //$this->load->model('design/layout');

           $this->template = 'module/combat_cart_loss_template.tpl';
           $this->children = array(
                'common/header',
                'common/footer'
            );

           $this->response->setOutput($this->render());
        }

        function new_template()
        {
            $this->template_edit(FALSE);
        }

        private function validate_template()
        {
            if (strlen($_POST['template_subject'])==0 OR strlen($_POST['template_message'])==0)
            {
                return FALSE;
            }
            return TRUE;
        }

        function mass_message()
        {
            if (count($_POST['recipients'])==0)
            {
                return FALSE;
            }

            $this->load->model('tool/combat_cart_loss');
            if(method_exists($this->load,'language')){
                $this->load->language('module/combat_cart_loss');
            }else{
                $this->language->load('module/combat_cart_loss');
            }

            $this->data['templates']=$this->model_tool_combat_cart_loss->get_templates();

            $this->data['title_mass_message_window']=$this->language->get('title_mass_message_window');
            $this->data['title_message_subject']=$this->language->get('title_message_subject');
            $this->data['title_message_from']=$this->language->get('title_message_from');
            $this->data['title_message_template']=$this->language->get('title_message_template');
            $this->data['title_message_to_customer']=$this->language->get('title_message_to_customer');

            $this->data['message_no_templates']=$this->language->get('message_no_templates');
            $this->data['message_default_template']=$this->language->get('message_default_template');

            //$this->load->model('design/layout');

            $this->template = 'module/combat_cart_mass_message.tpl';

            $this->response->setOutput($this->render());
        }

        function send_mass_message()
        {
            if(method_exists($this->load,'language')){
                $this->load->language('module/combat_cart_loss');
            }else{
                $this->language->load('module/combat_cart_loss');
            }

            if (count($_POST['recipients'])==0)
            {
                echo json_encode(array('error'=>$this->language->get('error_no_recipients')));
                return FALSE;
            }

            if ((!isset($_POST['recipient_type']) OR !$_POST['recipient_type']) OR (!isset($_POST['message']) OR !$_POST['message']) OR (!isset($_POST['subject']) OR !$_POST['subject']))
            {
                echo json_encode(array('error'=>$this->language->get('error_check_subject_message')));
                return FALSE;
            }

            $this->load->model('tool/combat_cart_loss');

            if ($this->model_tool_combat_cart_loss->send_mass_messages($this->request->post['recipients'],$this->request->post['message'],$this->request->post['subject'], $this->request->post['from']))
            {
                echo json_encode(array('success'=>$this->language->get('mass_messages_sent')));
            }
            else
            {
                echo json_encode(array('error'=>$this->language->get('mass_message_not_sent')));
            }
        }
    }
?>