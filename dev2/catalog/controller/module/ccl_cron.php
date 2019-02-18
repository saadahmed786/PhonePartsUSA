<?php

class ControllerModuleCclCron extends Controller {
	public function index() {
            
            $this->load->model('tool/combat_cart_loss');
            $this->load->model('setting/setting');
            
            $ccldays = $this->model_setting_setting->getSetting('ccldays');
            $finalday = array();
            $today = date("D");
            $flag = TRUE;
            
            if($ccldays['ccl_auto_mon']) {
                $finalday[] = 'Mon';
            }
            if($ccldays['ccl_auto_tue']) {
                $finalday[] = 'Tue';
            }
            if($ccldays['ccl_auto_wed']) {
                $finalday[] = 'Wed';
            }
            if($ccldays['ccl_auto_thu']) {
                $finalday[] = 'Thu';
            }
            if($ccldays['ccl_auto_fri']) {
                $finalday[] = 'Fri';
            }
            if($ccldays['ccl_auto_sat']) {
                $finalday[] = 'Sat';
            }
            if($ccldays['ccl_auto_sun']) {
                $finalday[] = 'Sun';
            }
            
            $unorders = $this->model_tool_combat_cart_loss->get_orders();
            foreach ($unorders->rows as $row) {
                if($row['total_emails'] != '1' && in_array($today, $finalday)) {
                   $this->model_tool_combat_cart_loss->autoSendUnconfirmedOrder($row['order_id']);
                    echo "Email sent to OrderId: ".$row['order_id']."<br>";
                   $flag = FALSE;
                } 
            }
            
            if($flag) {
                echo "No Emails are sending through this cron...";
            }
            
	}
        public function admin_reminder() {
            $this->load->model('tool/combat_cart_loss');
            $this->model_tool_combat_cart_loss->send_admin_notification();
        }
}
?>