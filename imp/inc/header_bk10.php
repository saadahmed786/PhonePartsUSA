<style>
    <?php if ($_SERVER['SCRIPT_NAME'] != '/imp/product_catalog/man_catalog.php') { ;?>
        * { font-family: Verdana, Geneva, sans-serif; font-size:11px; }
        body {
            margin: 0px;
            padding: 0px;
            background: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
        }
        <?php } ?>
        .nav {margin: 0px; padding: 0px; background: #fff; font-family: Verdana, Geneva, sans-serif; font-size:11px; line-height: normal;}

        .blackPage {position: fixed; background: rgba(0,0,0,.5); height: 100%; width: 100%; top: 0; left: 0; }
        .whitePage {background: rgb(255, 255, 255) none repeat scroll 0% 0%; padding: 50px; position: fixed; left: 50%; transform: translate(-50%, -50%); width: 500px; text-align: center; top: 50%; border-radius: 20px; box-shadow: 0px 0px 5px 0px #000; }
        .form {padding: 10px; }
        .form input {margin: 10px; }

        .makeTabs {

            display: none;

        }
        .bootstrap-datetimepicker-widget.dropdown-menu {
            position: absolute;
            background: #fff;
        }
        .ajax-dropdown {
            width: 100%;
            position: relative;
        }
        .ajax-dropdown table {
            width: 100%;
            position: absolute;
            top: 5px;
            -webkit-transform: translate(-50%, 0%);
            -moz-transform: translate(-50%, 0%);
            -ms-transform: translate(-50%, 0%);
            -o-transform: translate(-50%, 0%);
            transform: translate(-50%, 0%);
            left: 50%;
        }
        .ajax-dropdown tbody tr {
            background-color: #fff;
            border-bottom: 1px solid #000;
            padding:5px;
        }
        .ajax-dropdown tbody td {
            padding:5px;
        }
        
        .ajax-dropdown tbody tr:hover {
            background-color: #999;
            color: #fff;
        }

        .tabMenu {

            width: 100%;

            position: relative;

            z-index: 2;

        }

        .tabMenu .toogleTab {

            border: 1px solid #ccc;

            background-color: #E5E5E5;

            padding: 3px 6px;

            display: inline;

            color: #000;

            font-weight: 600;

            cursor: pointer;

            border-radius: 10px 10px 0px 0px;

            margin-bottom: -1px;

        }

        .toogleTab.current {

            border-color: #999;

            background-color:#000;

            color:#fff;

        }

        .tabHolder {

            border-radius:20px;

            border: #999 1px solid;

            position: relative;

            z-index: 3;

        }

    </style>

    <script type="text/javascript" src="<?php echo $host_path; ?>js/jquery.min.js"></script>

    <script type="text/javascript" src="<?php echo $host_path; ?>js/sticky.js"></script>

    <script type="text/javascript" src="<?php echo $host_path; ?>fancybox/jquery.fancybox.js?v=2.1.5"></script>

    <!--<script src="<?php echo $host_path; ?>js/moment.js"></script>-->
    <script src="<?php echo $host_path; ?>js/moment.min.js"></script>

    <script src="<?php echo $host_path; ?>js/pikaday.js"></script>

    <script src="<?php echo $host_path; ?>js/pikaday.jquery.js"></script>
    <script src='<?php echo $host_path; ?>include/fullcalender/fullcalendar.js'></script>
    <script src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
    <script src='<?php echo $host_path; ?>include/date-picker/js/bootstrap-datetimepicker.min.js'></script>

    <link href='<?php echo $host_path; ?>include/fullcalender/fullcalendar.css' rel='stylesheet' />
    <link href='<?php echo $host_path; ?>include/date-picker/css/bootstrap-datetimepicker.min.css' rel='stylesheet' />
    <link href='<?php echo $host_path; ?>include/fullcalender/fullcalendar.print.css' rel='stylesheet' media='print' />

    <link rel="stylesheet" href="<?php echo $host_path; ?>include/pikaday.css">

    <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

    <link href="<?php echo $host_path ?>include/style.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/sticky.css" media="screen" />


    <script>

        $(document).ready(function () {

            $('input[data-type=datetime]').datetimepicker({
                format: 'Y-MM-DD H:m',
            });

            $('input[data-type=date]').datetimepicker({
                format: 'Y-MM-DD'
            });

            $('input[data-type=time]').datetimepicker({
                format: 'H:m'
            });

            var $datepickerx = $('.datepicker').pikaday({

                firstDay: 1,

                minDate: new Date(1930, 0, 1),

                maxDate: new Date(2020, 12, 31),

                yearRange: [1930,2020]

            });

            $datepickerx.toString();

       // openTab('input[data-tab=tabImages]');

       openTab('input.toogleTab:eq(0)'); // open first tab by default

       $('.toogleTab').on('click', function() {

        openTab(this);

    });

   });

        function selectAllCheck (t) {
            var selector = $(t).attr('data-class');
            if ($(t).is(':checked')) {
                $('.' + selector).each(function() {
                    var chex = $(this).find('input[type=checkbox]')
                    if (!chex.attr('disabled')) {
                        chex.prop('checked', true);
                    }
                });
            } else {
                $('.' + selector).each(function() {
                    $(this).find('input[type=checkbox]').prop('checked', false);
                });
            }
        }

        function openTab(t) {

            if ($(t).hasClass('current')) {

                return false;

            }

            $('#loading').hide('slow');

            var activate = $(t).attr('data-tab');

            $('.makeTabs').hide('fast');

            $('#' + activate).addClass('tabActive');

            $('.tabActive').show('400');

            $('.tabActive').removeClass('tabActive');

            $('.toogleTab').removeClass('current');

            $(t).addClass('current');

        }

    </script>

    <script>

        $(document).ready(function (e) {

            $('.fancybox').fancybox({ width: '450px' , autoCenter : true , autoSize : true });

            $('.fancybox3').fancybox({ width: '1200px', height : '800px' , autoCenter : true , autoSize : false });

            $('.fancyboxX3').fancybox({width: '90%', autoCenter: true, autoSize: true});

            $('.fancyboxX4').fancybox({width: '800px', height : '600px', autoCenter: true, autoSize: true});

            setInterval(cron_dashboard, 1000 * 20);

        });

        function cron_dashboard() {

            var defaults = {

            position: 'bottom-right', // top-left, top-right, bottom-left, or bottom-right

            speed: 'fast', // animations: fast, slow, or integer

            allowdupes: false, // true or false

            autoclose: 0, // delay in milliseconds. Set to 0 to remain open.

            classList: 'info' // arbitrary list of classes. Suggestions: success, warning, important, or info. Defaults to ''.

        };



        $.ajax({

            url: '<?php echo $host_path; ?>crons/dashboard_check.php',

            type: 'post',

            //data: {},

            dataType: 'json',

            beforeSend: function () {



            },

            complete: function () {



            },

            success: function (json) {

                if (json['error']) {

                    //alert(json['error']);

                }



                if (json['success']) {

                    //$("#decision-anchor").attr("href","issues_complaint_view.php?id="+json['success']+"&user_id=<?php echo $_SESSION['user_id']; ?>&popup=1");

                    //$("#decision-anchor").click();

                    $.sticky("<a class='fancyboxX3 complain_view fancybox.iframe' onClick='closeDialog(this)' style='color:white' data-issue-id='" + json['success']['issue_assigned_id'] + "' href='issues_complaint_view.php?id=" + json['success']['id'] + "&user_id=<?php echo $_SESSION['user_id']; ?>&popup=1'>New Task - " + json['success']['description'] + "</a>", $.extend({}, defaults));

                }

            }

        });

    }



    function closeDialog(obj)

    {

        $(obj).parent().parent().find('.sticky-close').click();

    }



    function markSeen(issue_assigned_id)

    {

        $.ajax({

            url: 'crons/mark_seen_notification.php',

            type: 'post',

            data: {issue_assigned_id: issue_assigned_id},

            dataType: 'json',

            beforeSend: function () {



            },

            complete: function () {



            },

            success: function (json) {

                if (json['success']) {

                    //$("#decision-anchor").attr("href","issues_complaint_view.php?id="+json['success']+"&user_id=<?php echo $_SESSION['user_id']; ?>&popup=1");

                    //$("#decision-anchor").click();

                    //$("#"+obj).remove();

                }

            }

        });

    }

</script>

<div style="margin: 0px auto;">

    <table style="margin:0%;" cellpadding="10" align="center">

        <?php if (@$_SESSION['error']): ?>

            <tr>

                <td colspan="2" align="center">

                    <font color="red"><?php echo $_SESSION['error'];

                        unset($_SESSION['error']); ?> <br /></font>

                    </td>

                </tr>

            <?php endif; ?>



            <tr>

                <td width="50">

                    Welcome <?php echo $_SESSION['user_name']; ?>

                </td>

                <td>

                    <div> 

                        <ul class="nav">

                            <li>

                                <a href="<?php echo $host_path ?>home.php">Home</a>

                                <ul class="drop">

                                    <?php if ($_SESSION['login_as'] == 'admin'): ?>

                                        <li><a href="<?php echo $host_path ?>account.php">Dashboard</a></li>
                                        <li><a href="<?php echo $host_path ?>dashboard.php">Calendar</a></li>



                                        <li><a href="<?php echo $host_path ?>configuration.php">Configurations</a></li>



                                        <li><a href="<?php echo $host_path ?>CA/formulas.php">CA Formula</a></li>



                                        <li><a href="<?php echo $host_path ?>price_settings.php">Prices Settings</a></li>

                                    <?php else: ?>



                                        <li><a href="<?php echo $host_path ?>dashboard.php">Dashboard</a></li>

                                    <?php endif; ?>			

                                </ul>

                            </li>



                            <?php if ($_SESSION['login_as'] == 'admin'): ?>

                                <li>

                                    <a href="<?php echo $host_path ?>users.php">Users</a>

                                    <ul class="drop">

                                        <li><a href="<?php echo $host_path ?>users.php">Users</a></li>
                                        <li><a href="<?php echo $host_path ?>admin_users.php">Admin Users</a></li>



                                        <li><a href="<?php echo $host_path ?>groups.php">Groups</a></li>


                                        <li><a href="<?php echo $host_path ?>oc_users.php">OC Groups</a></li>



                                        <li><a href="<?php echo $host_path ?>user_log.php">Users Log</a></li>

                                    </ul>

                                </li>


                                <?php
                                endif;
                                ?>


                                <li>

                                    <a href="#">Finance</a>

                                    <ul class="drop">


                                        <?php
                                        if($_SESSION['login_as']=='admin' || $_SESSION['group']=='Vendor'):
                                            ?>  
                                        <li><a href="<?php echo $host_path ?>finance.php">Finance</a></li>
                                        <?php
                                        endif;
                                        ?>

                                         <?php
                                        if($_SESSION['login_as']=='admin'):
                                            ?>  
                                        <li><a href="<?php echo $host_path ?>monthly_finance.php">Monthly</a></li>

                                        <li><a href="<?php echo $host_path ?>finance_report.php">Sales / Finance</a></li>

                                        <?php 
                                        endif;
                                        if ($_SESSION['local_orders']): ?>

                                        <li><a href="<?php echo $host_path ?>local_orders.php">Store Front Orders</a></li>

                                    <?php endif; ?>







                                </ul>

                            </li>







                            <li>

                                <a href="<?php echo $host_path ?>order.php">Orders</a>

                                <ul class="drop">

                                 <?php if ($_SESSION['create_order']): ?>

                                    <li><a href="<?php echo $host_path ?>order_create.php">Create Order</a></li>

                                <?php endif; ?> 
                                <?php if ($_SESSION['order_history']): ?>
                                    <li><a href="<?php echo $host_path ?>order.php">Order History</a></li>
                                <?php endif;?>
                              
                                <?php if ($_SESSION['order_verification']): ?>

                                    <li><a href="<?php echo $host_path ?>verify_orders.php">Verification</a></li>


                                <?php endif; ?>

                                <?php
                                if($_SESSION['purchase_orders_view']):
                                    ?>
                                <li><a href="<?php echo $host_path ?>po_orders.php">Purchase Orders</a></li>
                                <?php
                                endif;?>


                                <?php if ($_SESSION['vouchers']): ?>

                                    <li><a href="<?php echo $host_path ?>vouchers_manage.php">Vouchers</a></li>

                                <?php endif; ?>

                                <?php if ($_SESSION['charge_back']): ?>

                                    <li><a href="<?php echo $host_path ?>chargeback_manage.php">Blacklisted</a></li>

                                <?php endif; ?>





                                <?php if ($_SESSION['ignore_order']): ?>

                                    <li><a href="<?php echo $host_path ?>ignore.php">Ignore Orders</a></li>     

                                <?php endif; ?> 
                                <?php
                                if($_SESSION['login_as']=='admin'):
                                    ?>
                                <li><a href="<?php echo $host_path ?>amazon/reports.php">Amazon Reports</a></li>		
                                <?php
                                endif;?>

                                <?php if ($_SESSION['users_local_orders']): ?>

                                    <li><a href="<?php echo $host_path ?>users_local_orders.php">Local Orders (Users)</a></li>

                                <?php endif; ?>

                                <?php if ($_SESSION['compare_local_orders']): ?>

                                    <li><a href="<?php echo $host_path ?>local_orders_manage.php">Compare Orders</a></li>

                                <?php endif; ?>
                                <?php if($_SESSION['login_as']=='admin'):
                                ?>

                                <li><a href="<?php echo $host_path ?>error_logs.php">Error Logs</a></li>
                            <?php endif;?>






























                        </ul>	

                    </li>

                    

                    <?php if ($_SESSION['po_business']): ?>

                        <li>

                            <a href="<?php echo $host_path ?>po_businesses.php">Accounts</a>

                            <ul class="drop">

                                <li><a href="<?php echo $host_path ?>po_businesses.php">Manage</a></li>



                                <li><a href="<?php echo $host_path ?>po_business_create.php">Create</a></li>



                                <?php if ($_SESSION['wholesale']): ?>

                                    <li><a href="<?php echo $host_path ?>wholesale.php">Wholesale</a></li>

                                <?php endif; ?>

                                <?php if ($_SESSION['customers']): ?>

                                    <li><a href="<?php echo $host_path ?>customers.php">Customers</a></li>

                                    <li><a href="<?php echo $host_path ?>vendor_accounts.php">Vendors</a></li>

                                    <li><a href="<?php echo $host_path ?>customer_groups.php">Customer Groups</a></li>
                                <?php endif; ?>

                            </ul>

                        </li>

                    <?php endif ?>

                    <li>

                        <a href="#">Complaints</a>

                        <ul class="drop">

                            <?php if ($_SESSION['complaints']): ?>

                                <li><a href="<?php echo $host_path ?>issues_complaints.php">Issues / Complaints</a></li>

                                <li><a href="<?php echo $host_path ?>issues_complaint_add.php">Add Issue</a></li>

                            <?php endif; ?>      



                            <?php if ($_SESSION['complaint_category']): ?>

                                <li><a href="<?php echo $host_path ?>issues_complaint_category.php">Issues Category</a></li>

                            <?php endif; ?>

                        </ul>

                    </li>



                    <li>

                        <a href="<?php echo $host_path ?>products.php">Inventory</a>

                        <ul class="drop">

                            <li><a href="<?php echo $host_path ?>products.php">Products</a></li>



                            <?php if ($_SESSION['classify_product']): ?>

                                <li><a href="<?php echo $host_path ?>devices_new.php">Classify Product</a></li>

                            <?php endif; ?>



                            <?php if ($_SESSION['product_classification']): ?>

                                <li><a href="<?php echo $host_path ?>product_classification.php">Product Classification</a></li>
                                <li><a href="<?php echo $host_path;?>product_catalog/man_catalog.php">Product Catalog</a></li>
                                <!--                                    <li><a href="<?php echo $host_path ?>product_sub_classification.php">Product Sub-Classification</a></li>-->

                            <?php endif; ?>



                            <li><a href="<?php echo $host_path ?>product_skus.php">Product SKUs</a></li>



                            <?php if ($_SESSION['sku_creation']): ?>

                                <li><a href="<?php echo $host_path ?>sku_creation.php">SKU Creation</a></li>

                            <?php endif; ?>



                            <li><a href="<?php echo $host_path ?>kit_skus.php">Kit Skus</a></li>



                            <?php if ($_SESSION['create_order']): ?>

                                <li><a href="<?php echo $host_path ?>price_change_report.php">Price Change Report</a></li>

                                <li><a href="<?php echo $host_path ?>inventory_report.php">Inventory Report</a></li>

                            <?php endif; ?>



                            <?php if ($_SESSION['create_order']): ?>

                                <li><a href="<?php echo $host_path ?>product_pricing.php">Product Pricing</a></li>

                            <?php endif; ?>

                        </ul>

                    </li>



                    <?php if ($_SESSION['manage_returns']): ?>

                        <li>

                            <a href="<?php echo $host_path ?>manage_returns.php">Returns</a>

                            <ul class="drop">

                                <li><a href="<?php echo $host_path ?>manage_returns.php">Manage</a></li>



                                <li><a href="<?php echo $host_path ?>returns_history.php">History</a></li>



                                <li><a href="<?php echo $host_path ?>returns.php">Input</a></li>



                                <?php if ($_SESSION['issue_types']): ?>

                                    <li><a href="<?php echo $host_path ?>settings/reject_reasons.php">Reason types</a></li>



                                    <li><a href="<?php echo $host_path ?>settings/item_issues.php">Item reasons</a></li>

                                <?php endif; ?> 	



                                <?php if ($_SESSION['returns_po']): ?>

                                    <!--                                        <li><a href="<?php echo $host_path ?>manage_returns_po.php">Orders</a></li>-->

                                <?php endif; ?> 	



                                <?php if ($_SESSION['returns_boxes']): ?>

                                    <!--<li><a href="<?php echo $host_path ?>manage_returns_boxes.php">Shipment Boxes</a></li> -->	



                                    <!-- <li><a href="<?php echo $host_path ?>boxes/customer_damage.php">Customer Damage Boxes</a></li> -->



                                    <li><a href="<?php echo $host_path ?>boxes/not_tested.php">Not Tested Boxes</a></li>



                                    <li><a href="<?php echo $host_path ?>boxes/item_issue.php">Item Issues Boxes</a></li>



                                    <li><a href="<?php echo $host_path ?>boxes/shipping_damage.php">Shipping Damage Items</a></li>


                                    <li><a href="<?php echo $host_path ?>boxes/customer_issue_box.php">Customer Issue Box</a></li>



                                    <!-- <li><a href="<?php echo $host_path ?>boxes/over_days.php">Over 60 Days Boxes</a></li> -->



                                    <li><a href="<?php echo $host_path ?>boxes/need_to_repair.php">NTR Items</a></li>



                                    <li><a href="<?php echo $host_path ?>boxes/return_to_stock.php">RTS Boxes</a></li>


                                    <?php if ($_SESSION['boxes_log']): ?>

                                        <li><a href="<?php echo $host_path ?>boxes/boxes_log.php">Boxes Log</a></li>

                                    <?php endif; ?>

                                    <li><a href="<?php echo $host_path ?>customer_damage_shipments.php">Customer Damage Shipments</a></li>  

                                <?php endif; ?>     



                                <?php if ($_SESSION['email_return_report']): ?>

                                    <li><a href="<?php echo $host_path ?>report_return_emails.php">Return Emails Report</a></li>

                                <?php endif; ?>

                                <li><a href="<?php echo $host_path ?>return_rj_search.php">Returned Item Search</a></li>




                            </ul>

                        </li>

                    <?php endif; ?> 







                    <?php if ($_SESSION['reorder_page']): ?>

                        <li><a href="<?php echo $host_path ?>sales.php">Purchasing</a></li>

                    <?php endif; ?>	



                    <li>

                        <a href="<?php echo $host_path ?>shipments.php">Shipments</a>

                        <ul class="drop">

                            <li><a href="<?php echo $host_path ?>shipments.php">Manage Shipments</a></li>



                            <?php if ($_SESSION['rejected_shipment']): ?>

                                <li><a href="<?php echo $host_path ?>rejected_shipments.php">Rejected Item PO</a></li>

                            <?php endif; ?>



                            <?php if ($_SESSION['price_modification']): ?>

                                <li><a href="<?php echo $host_path ?>price_modification.php">Missing Cost</a></li>

                            <?php endif; ?>


                            <li><a href="<?php echo $host_path ?>vendor_po.php">Vendor PO</a></li>


                        </ul>	

                    </li>





                               <!--  <li>

                                    <a href="#">Category Link</a>

                                    <ul class="drop" >

                                        <li><a href="<?php echo $host_path ?>device_list.php">Device Page</a></li>



                                    </ul>

                                    <ul class="drop" style="display:none" >

                                        <li><a href="<?php echo $host_path ?>devices_new.php">Device Page</a></li>



                                        <li><a href="<?php echo $host_path ?>carrier_list.php">Carrier</a></li>

                                        <li><a href="<?php echo $host_path ?>manufacturer_list.php">Manufacturer</a></li>



                                        <li><a href="<?php echo $host_path ?>model_list.php">Model</a></li>

                                        <li><a href="<?php echo $host_path ?>model_type_list.php">Model Type</a></li>

                                        <li><a href="<?php echo $host_path ?>usb_conn_list.php">Model Connection</a></li>

                                        <li><a href="<?php echo $host_path ?>attribute_group_list.php">Attributes</a></li>



                                        <li><a href="<?php echo $host_path ?>attribute_list.php">Assign Attributes to SKU</a></li>

                                    </ul>

                                </li> -->



                             <!--<li>

                                <a href="#">Scrapping</a>

                                <ul class="drop">

                                    <li><a href="<?php echo $host_path ?>scrap_mengtor.php">Scrape Mengtor</a></li>

                                    <li><a href="<?php echo $host_path ?>scrap_mobiledefender.php">Scrape Mobile Defender</a></li>

                                </ul>

                            </li>-->

                            <?php

                            if($_SESSION['login_as']=='admin' or $_SESSION['buyback']):





                               ?>

                           <li>

                            <a href="#">BuyBack</a>

                            <ul class="drop">

                                <?php

                                if($_SESSION['login_as']=='admin' or $_SESSION['buyback_inputs'])

                                {

                                 ?><li><a href="<?php echo $host_path ?>buyback/inputs.php">Inputs</a></li>

                                 <?php

                             }

                             ?>

                             <li><a href="<?php echo $host_path ?>buyback/shipments.php">Shipments</a></li>
                             <?php
                             if($_SESSION['login_as']=='admin' or $_SESSION['buyback_inputs'])

                             {
                                 ?>
                                 <li><a href="<?php echo $host_path;?>buyback/lbb_sku_mapping.php">SKU Mapping</a></li>
                                 <?php
                             }
                             ?>
                             <li><a href="<?php echo $host_path ?>buyback/box_shipments.php">Boxes</a></li>



                         </ul>

                     </li>

                     <?php

                     endif;

                     ?>



                     <li>

                        <a href="#">Reports</a>

                        <ul class="drop">


                            <?php if ($_SESSION['paypal_transactions']) : ?>
                                <li><a href="<?php echo $host_path ?>paypal_orders.php">PayPal Transactions</a></li>
                            <?php endif; ?>
                            <?php if ($_SESSION['shipstation_trackings']) : ?>
                                <li><a href="<?php echo $host_path ?>shipstation_transactions.php">Shipstation Trackings</a></li>
                            <?php endif; ?>

                            <?php if ($_SESSION['aging_report']) : ?>
                                <li><a href="<?php echo $host_path ?>aging_report.php">Aging Report</a></li>
                            <?php endif; ?>

                            <?php if ($_SESSION['tracking_report']) : ?>
                                <li><a href="<?php echo $host_path ?>trackings.php">Order Trackings</a></li>
                            <?php endif ?>

                            <?php if ($_SESSION['item_wise_return_report']): ?>
                                <li><a href="<?php echo $host_path ?>report_return_item_wise.php">Product Returns Report</a></li>
                            <?php endif; ?>

                            <?php if ($_SESSION['replacement_wise_return_report']): ?>
                                <li><a href="<?php echo $host_path ?>report_replacement_wise.php">Replacement Returns Report</a></li>
                            <?php endif; ?>

                            <?php if ($_SESSION['customer_replacement_report']): ?>
                                <li><a href="<?php echo $host_path ?>customer_replacement_wise.php">Customer Replacement Report</a></li>
                            <?php endif; ?>

                            <?php if ($_SESSION['stock_inout_report']): ?>
                                <li><a href="<?php echo $host_path ?>stock_inout_report.php">Stock In / Out Report</a></li>
                            <?php endif; ?>
                            
                            <?php if($_SESSION['sales_dashboard']) : ?>
                                <li><a href="<?php echo $host_path ?>sales_dashboard.php">Sales Dashboard</a></li>
                            <?php endif; ?>

                        </ul>

                    </li>



                    <?php if ($_SESSION['login_as'] == 'admin'): ?> 

                        <li>

                            <a href="#">Admin Tools</a>

                            <ul class="drop">

                                <li><a href="<?php echo $host_path ?>generate_product_newsletter.php">Product Newsletter</a></li>

                                <li><a href="<?php echo $host_path;?>login_details.php">IP Logs</a></li>




                            </ul>

                        </li>

                    <?php endif; ?>

                    <li>

                        <a href="#">Setting</a>

                        <ul class="drop">

                            <?php if ($_SESSION['canned_messages']) { ?>

                            <li><a href="<?php echo $host_path; ?>canned_messages_manage.php">Canned Messages</a></li>

                            <?php } ?>
                            <?php if ($_SESSION['catalog_setting']) { ?>

                            <li><a href="<?php echo $host_path; ?>catalog_setting.php">Catalog Settings</a></li>

                            <?php } ?>

                            <?php if ($_SESSION['login_as'] == 'admin') { ?>

                            <li><a href="<?php echo $host_path; ?>disclaimer.php">Disclaimer</a></li>

                            <?php } ?>

                            <li><a href="<?php echo $host_path ?>signature.php">Signature</a></li>

                            <?php

                            if($_SESSION['login_as']=='admin'):

                                ?>

                            <li><a href="<?php echo $host_path ?>whitelist_ip.php">Admin Security</a></li>

                            <?php

                            endif;

                            ?>

                            <?php

                            if($_SESSION['login_as'] != 'admin'):

                                ?>

                            <li><a href="<?php echo $host_path ?>user.php?id=<?php echo $_SESSION['user_id']; ?>&mode=edit">Profile</a></li>

                            <?php

                            endif;

                            ?>

                        </ul>

                    </li>

                    <li><a href="<?php echo $host_path ?>logout.php">Logout</a></li>

                </ul>   

            </div>

        </td>

    </tr>

</table>
<?php include_once($path . 'gapi/eventWig.php'); ?>
</div>
