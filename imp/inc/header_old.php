<style>
    * { font-family: Verdana, Geneva, sans-serif; font-size:11px; }
</style>
<script type="text/javascript" src="<?php echo $host_path; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $host_path; ?>/js/sticky.js"></script>
<script type="text/javascript" src="<?php echo $host_path; ?>/fancybox/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
<link href="<?php echo $host_path ?>/include/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>/include/sticky.css" media="screen" />
<script>
    $(document).ready(function (e) {
        $('.fancyboxX3').fancybox({width: '90%', autoCenter: true, autoSize: true});
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
<center>
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
                    Welcome <?php echo $_SESSION['login_as']; ?>
                </td>
                <td>
                    <div> 
                        <ul class="nav">
                            <li>
                                <a href="<?php echo $host_path ?>home.php">Home</a>
                                <ul class="drop">
                                    <?php if ($_SESSION['login_as'] == 'admin'): ?>
                                        <li><a href="<?php echo $host_path ?>account.php">Dashboard</a></li>

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

                                        <li><a href="<?php echo $host_path ?>groups.php">Groups</a></li>
                                    </ul>
                                </li>

                                <li>
                                    <a href="<?php echo $host_path ?>finance.php">Finance</a>
                                    <ul class="drop">
                                        <li><a href="<?php echo $host_path ?>finance.php">Finance</a></li>

                                        <li><a href="<?php echo $host_path ?>monthly_finance.php">Monthly</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>

                            <?php if ($_SESSION['order_history']): ?>
                                <li>
                                    <a href="<?php echo $host_path ?>order.php">Orders</a>
                                    <ul class="drop">
                                        <li><a href="<?php echo $host_path ?>order.php">Order History</a></li>

                                        <li><a href="<?php echo $host_path ?>amazon/reports.php">Amazon Reports</a></li>		

                                        <li><a href="<?php echo $host_path ?>error_logs.php">Error Logs</a></li>     

                                        <li><a href="<?php echo $host_path ?>po_orders.php">Purchase Orders</a></li>

                                        <?php if ($_SESSION['create_order']): ?>
                                            <li><a href="<?php echo $host_path ?>order_create.php">Create Order</a></li>
                                        <?php endif; ?>	     

                                        <?php if ($_SESSION['ignore_order']): ?>
                                            <li><a href="<?php echo $host_path ?>ignore.php">Ignore Orders</a></li>   	
                                        <?php endif; ?>			     	

                                        <?php if ($_SESSION['customers']): ?>
                                            <li><a href="<?php echo $host_path ?>customers.php">Customers</a></li>
                                        <?php endif; ?>		  
                                    </ul>	
                                </li>
                            <?php endif; ?>

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

                                            <li><a href="<?php echo $host_path ?>boxes/customer_damage.php">Customer Damage Boxes</a></li>

                                            <li><a href="<?php echo $host_path ?>boxes/not_tested.php">Not Tested Boxes</a></li>

                                            <li><a href="<?php echo $host_path ?>boxes/item_issue.php">Item Issues Boxes</a></li>

                                            <li><a href="<?php echo $host_path ?>boxes/need_to_repair.php">NTR Items</a></li>

                                            <li><a href="<?php echo $host_path ?>boxes/return_to_stock.php">RTS Boxes</a></li>

                                            <li><a href="<?php echo $host_path ?>customer_damage_shipments.php">Customer Damage Shipments</a></li>	
                                        <?php endif; ?> 	

                                        <?php if ($_SESSION['email_return_report']): ?>
                                            <li><a href="<?php echo $host_path ?>report_return_emails.php">Return Emails Report</a></li>
                                        <?php endif; ?>

                                        <?php if ($_SESSION['item_wise_return_report']): ?>
                                            <li><a href="<?php echo $host_path ?>report_return_item_wise.php">Item Wise Return Report</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endif; ?> 

                            <?php if ($_SESSION['po_business']): ?>
                                <li>
                                    <a href="<?php echo $host_path ?>po_businesses.php">PO Clients</a>
                                    <ul class="drop">
                                        <li><a href="<?php echo $host_path ?>po_businesses.php">Manage</a></li>

                                        <li><a href="<?php echo $host_path ?>po_business_create.php">Create</a></li>
                                    </ul>
                                </li>
                            <?php endif ?>

                            <?php if ($_SESSION['reorder_page']): ?>
                                <li><a href="<?php echo $host_path ?>sales.php">Re-Ordering</a></li>
                            <?php endif; ?>	

                            <li>
                                <a href="<?php echo $host_path ?>shipments.php">Shipments</a>
                                <ul class="drop">
                                    <li><a href="<?php echo $host_path ?>shipments.php">Manage Shipments</a></li>

                                    <?php if ($_SESSION['rejected_shipment']): ?>
                                        <li><a href="<?php echo $host_path ?>rejected_shipments.php">Rejected Item PO</a></li>
                                    <?php endif; ?>
                                </ul>	
                            </li>

                            <?php if ($_SESSION['login_as'] == 'admin'): ?>
                               <!-- <li>
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
                                </li>-->

                            <!--<li>
                                <a href="#">Scrapping</a>
                                <ul class="drop">
                                    <li><a href="<?php echo $host_path ?>scrap_mengtor.php">Scrape Mengtor</a></li>
                                    <li><a href="<?php echo $host_path ?>scrap_mobiledefender.php">Scrape Mobile Defender</a></li>
                                </ul>
                            </li>-->
                            
                             <li>
                                <a href="#">LCD Buy Back</a>
                                <ul class="drop">
                                    <li><a href="<?php echo $host_path ?>buyback/inputs.php">Inputs</a></li>
                                    <li><a href="<?php echo $host_path ?>buyback/shipments.php">Shipments</a></li>

                                </ul>
                            </li>
                            <li>
                                <a href="#">Marketing Tools</a>
                                <ul class="drop">
                                    <li><a href="<?php echo $host_path ?>generate_product_newsletter.php">Product Newsletter</a></li>

                                </ul>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="#">Setting</a>
                            <ul class="drop">
                                <?php if ($_SESSION['canned_messages']) { ?>
                                <li><a href="<?php echo $host_path; ?>manage_canned_messages.php">Canned Messages</a></li>
                                <?php } ?>
                                <?php if ($_SESSION['login_as'] == 'admin') { ?>
                                <li><a href="<?php echo $host_path; ?>disclaimer.php">Disclaimer</a></li>
                                <?php } ?>
                                <li><a href="<?php echo $host_path ?>signature.php">Signature</a></li>


                            </ul>
                        </li>
                        <li><a href="<?php echo $host_path ?>logout.php">Logout</a></li>
                    </ul>   
                </div>
            </td>
        </tr>
    </table>
</center>