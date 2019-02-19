<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';

$page = (int) $_GET['page'];
if (!$page) {
    $page = 1;
}
$printers = array(
    array('id' => QC1_PRINTER, 'value' => 'QC1'),
    array('id' => QC2_PRINTER, 'value' => 'QC2'),
    array('id' => REC_PRINTER, 'value' => 'Receiving'),
    array('id' => STOREFRONT_PRINTER, 'value' => 'Storefront')
    );
if($_POST['print']){ 
    foreach ($_POST['reject_ids'] as $reject_id) {
        $box_id = $db->func_query_first_cell('SELECT `return_shipment_box_id` FROM `inv_return_shipment_box_items` WHERE return_item_id = "'. $reject_id .'"');
        $box_number = $db->func_query_first_cell('SELECT `box_number` FROM `inv_return_shipment_boxes` WHERE id = "'. $box_id .'"');
        printLabel($reject_id,$_POST['sku'][$reject_id],$box_number,$_POST['reason'][$reject_id],$_POST['order_id'][$reject_id],$_POST['printer_id'],'','','','');
        //printLabel($value, $returns_po_item_insert['product_sku'], $inv_return_shipment_box_number, $returns_po_item_insert['reason'], $returns_po_item_insert['order_id'], $returns_po_item['printer'], $source);
    }
}
if ($_POST['Transfer']) {
    if (count($_POST['reject_ids']) > 0) {
        foreach ($_POST['reject_ids'] as $reject_id) {
            $inv_return_shipment_box_items = array();
            $inv_return_shipment_box_items['return_shipment_box_id'] = $_POST['new_box_id'];
            $db->func_array2update("inv_return_shipment_box_items", $inv_return_shipment_box_items, "return_item_id = '$reject_id'");
            $box_id = $db->func_query_first_cell('SELECT `return_shipment_box_id` FROM `inv_return_shipment_box_items` WHERE return_item_id = "'. $reject_id .'"');
            addBoxMoveLog ($box_id, $_POST['new_box_id'], $reject_id);
            unset($box_id);
        }

        $_SESSION['message'] = "Return Items are moved to another box.";
        header("Location:$host_path/boxes/not_tested.php");
        exit;
    } else {
        $_SESSION['message'] = "Select at least one sku to move to delete.";
        header("Location:$host_path/boxes/not_tested.php");
        exit;
    }
} elseif ($_POST['MoveItemIssue']) {
    if (count($_POST['reject_ids']) > 0) {
        foreach ($_POST['reject_ids'] as $reject_id) {
            $box_id = $db->func_query_first_cell('SELECT `return_shipment_box_id` FROM `inv_return_shipment_box_items` WHERE return_item_id = "'. $reject_id .'"');
            $nID = moveItemToBox($reject_id, $_POST[$reject_id], 'ItemIssueBox');
            addBoxMoveLog ($box_id, $nID, $reject_id);
            unset($nID, $box_id);
        }

        $_SESSION['message'] = "Items moved to Item Issue.";
        header("Location:$host_path/boxes/not_tested.php");
        exit;
    } else {
        $_SESSION['message'] = "Select at least one sku to move to Item Issue.";
        header("Location:$host_path/boxes/not_tested.php");
        exit;
    }
} elseif ($_POST['MoveGFS']) {
    if (count($_POST['reject_ids']) > 0) {
        foreach ($_POST['reject_ids'] as $reject_id) {
            $box_id = $db->func_query_first_cell('SELECT `return_shipment_box_id` FROM `inv_return_shipment_box_items` WHERE return_item_id = "'. $reject_id .'"');
            $nID = moveItemToBox($reject_id, $_POST[$reject_id], 'GFSBox');
            addBoxMoveLog ($box_id, $nID, $reject_id);
            unset($nID, $box_id);
        }

        $_SESSION['message'] = "Items moved to GFS.";
        header("Location:$host_path/boxes/not_tested.php");
        exit;
    } else {
        $_SESSION['message'] = "Select at least one sku to move to GFS.";
        header("Location:$host_path/boxes/not_tested.php");
        exit;
    }
} elseif ($_POST['MoveNTR']) {
    if (count($_POST['reject_ids']) > 0) {
        foreach ($_POST['reject_ids'] as $reject_id) {
            $box_id = $db->func_query_first_cell('SELECT `return_shipment_box_id` FROM `inv_return_shipment_box_items` WHERE return_item_id = "'. $reject_id .'"');
            $nID = moveItemToBox($reject_id, $_POST[$reject_id], 'NTRBox');
            addBoxMoveLog ($box_id, $nID, $reject_id);
            unset($nID, $box_id);
        }
        $_SESSION['message'] = "Items moved to NTR.";
        header("Location:$host_path/boxes/need_to_repair.php");
        exit;
    } else {
        $_SESSION['message'] = "Select at least one sku to move to NTR.";
        header("Location:$host_path/boxes/not_tested.php");
        exit;
    }
} elseif ($_POST['save']) {
    //now update shipment item reject reason
    $reject_item_ids = $_POST['reject_item_ids'];
    foreach ($reject_item_ids as $id => $reject_id) {
        $text = $db->func_escape_string($_POST['reason'][$reject_id]);
        $reject_id = $db->func_escape_string($reject_id);

        $db->db_exec("update inv_return_shipment_box_items SET reason = '$text' , return_item_id = '$reject_id' where id = '$id'");
    }

    $_SESSION['message'] = "Items changes are saved.";
    header("Location:$host_path/boxes/not_tested.php");
    exit;
} elseif ($_POST['delete']) {
    if (count($_POST['reject_ids']) > 0) {
        foreach ($_POST['reject_ids'] as $reject_id) {
            $db->db_exec("delete from inv_return_shipment_box_items where return_item_id = '$reject_id'");
        }

        $_SESSION['message'] = "Items deleted successfully.";
        header("Location:$host_path/boxes/not_tested.php");
        exit;
    } else {
        $_SESSION['message'] = "Select at least one sku to move to delete.";
        header("Location:$host_path/boxes/not_tested.php");
        exit;
    }
}

$where = array();
if ($_GET['rma_number']) {
    $rma_number = $db->func_escape_string(trim($_GET['rma_number']));
    $where[] = " LCASE(rma_number) = LCASE('$rma_number') ";
    $parameters[] = "rma_number=$rma_number";
}

if ($_GET['order_id']) {
    $order_id = $db->func_escape_string(trim($_GET['order_id']));
    $where[] = " order_id = '$order_id' ";
    $parameters[] = "order_id=$order_id";
}

if ($where) {
    $where = implode(" AND ", $where);
} else {
    $where = ' 1 = 1';
}

$_query = "select si.* from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s on (s.id = si.return_shipment_box_id)
where $where and box_type = 'NotTestedBox' order by date_added desc";

$splitPage = new splitPageResults($db, $_query, 500, "not_tested.php", $page, $count_query);
$nts_items = $db->func_query($splitPage->sql_query);

foreach ($nts_items as $index => $nts_item) {
    if ($nts_item['shipment_id']) {
        $nts_items[$index]['shipment_number'] = $db->func_query_first_cell("select package_number from inv_rejected_shipments where id = '" . $nts_item['shipment_id'] . "'");
    }

    $_query = "select ((pc.raw_cost + pc.shipping_fee) / pc.ex_rate) from inv_product_costs pc where pc.sku = '" . $nts_item['product_sku'] . "' order by pc.id DESC";
    $nts_items[$index]['item_cost'] = round($db->func_query_first_cell($_query), 2);
}

if ($parameters) {
    $parameters = implode("&", $parameters);
} else {
    $parameters = '';
}

$boxes = $db->func_query("select id , box_number from inv_return_shipment_boxes where box_type = 'ItemIssueBox' order by box_type");

//check if there is any box open
$inv_return_shipment_box_id = $db->func_query_first_cell("select id from inv_return_shipment_boxes where box_number LIKE '%NotTestedBox%' and status = 'Issued'");
if (!$inv_return_shipment_box_id) {
    $return_shipment_boxes_insert = array();
    $return_shipment_boxes_insert ['box_number'] = getReturnBoxNumber(0, 'NotTestedBox');
    $return_shipment_boxes_insert ['box_type'] = 'NotTestedBox';
    $return_shipment_boxes_insert ['date_added'] = date('Y-m-d H:i:s');
    $inv_return_shipment_box_id = $db->func_array2insert("inv_return_shipment_boxes", $return_shipment_boxes_insert);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Not Tested Items</title>
    <script type="text/javascript" src="<?php echo $host_path; ?>js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $host_path; ?>fancybox/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

    <script type="text/javascript">
        $(document).ready(function () {
            $('.fancybox').fancybox({width: '400px', height: '200px', autoCenter: true, autoSize: true});
            $('.fancybox2').fancybox({width: '500px', height: '500px', autoCenter: true, autoSize: true});
        });
    </script>	
    <style type="text/css">
        .data td,.data th{
            border: 1px solid #e8e8e8;
            text-align:center;
            width: 150px;
        }
        .div-fixed{
            position:fixed;
            top:0px;
            left:8px;
            background:#fff;
            width:98.8%; 
        }
        .red td{ box-shadow:1px 2px 5px #990000;}
    </style>
</head>
<body>
    <div align="center"> 
        <?php include_once '../inc/header.php'; ?>
    </div>
    <h2 align="center">Not Tested Boxes</h2>
    <?php if ($_SESSION['message']): ?>
        <div align="center"><br />
            <font color="red"><?php echo $_SESSION['message'];
                unset($_SESSION['message']); ?><br /></font>
            </div>
        <?php else: ?>
            <br /><br /> 
        <?php endif; ?>

        <div align="center">
            <a class="fancybox fancybox.iframe" href="<?php echo $host_path; ?>/popupfiles/returns_box_skuadd.php?return_shipment_box_id=<?php echo $inv_return_shipment_box_id ?>">Add Item</a>
            <br /><br /> 
        </div>	

        <div align="center">
            <form action="" method="get">
                <table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                        <td>
                            RMA Number: <?php echo createField("rma_number", "rma_number", "text", $_GET['rma_number']); ?>				        
                        </td>

                        <td>
                            Order ID: <?php echo createField("order_id", "order_id", "text", $_GET['order_id']); ?>				        
                        </td>

                        <td>
                            <input type="submit" name="search" value="Search" class="button" />
                        </td>
                    </tr>	
                </table>
                <br />
            </form>
        </div>			

        <div>	
            <form action="" method="post">
                <div align="center">
                    <input type="button" value="Print" onclick="$('.printer').show();" />
                    <input type="submit" name="MoveGFS" value="Move to GFS" />
                    <input type="submit" name="MoveNTR" value="Move to NTR" />
                    <input type="submit" name="MoveItemIssue" value="Move to Item Issue" />

                    <?php if ($_SESSION['login_as'] == 'admin'): ?>
                        <input type="submit" name="delete" value="Delete" onclick="if (!confirm('Are you sure?')) {
                            return false;
                        }" />
                    <?php endif; ?>

                    <!-- Item Issue Box:
                    <select name="new_box_id" id="new_box_id" style="width:150px;">
                        <option value="">Select One</option>
                    <?php foreach ($boxes as $box): ?>
                            <option value="<?php echo $box['id']; ?>"><?php echo $box['box_number']; ?></option>
                    <?php endforeach; ?>
                    </select>

                    <input type="submit" name="Transfer" value="Transfer" onclick="if (!$('#new_box_id').val()) {
                                alert('Please select one BOX.');
                                return false;
                            }" /> -->

                            <br /><br />
                        </div>	

                        <table id="table1" class="data" border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="3">
                            <thead>
                            <tr style="background:#e5e5e5;">
                                <th style="width:50px;">#</th>
                                <th>Inserted</th>
                                <!--                        <th>Shipment ID</th>-->
                                <th>Return ID</th>
                                <th>SKU</th>
                                <th>Title</th>
                                <th>Order ID</th>
                                <th>RMA</th>
                                <?php if ($_SESSION['boxes_cost']): ?>
                                    <th>Cost</th>
                                <?php endif; ?>	
                                <th>Comment</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($nts_items as $k => $nts_item): ?>
                                <tr class="list_items">
                                    <input type="hidden" value="<?php echo $nts_item['product_sku']; ?>" name="sku[<?php echo $nts_item['return_item_id']; ?>]" />
                                    <input type="hidden" value="<?php echo $nts_item['order_id']; ?>" name="order_id[<?php echo $nts_item['return_item_id']; ?>]" />
                                    <input type="hidden" name="reason[<?php echo $nts_item['return_item_id'] ?>]" value="<?php echo $nts_item['reason'] ?>" />
                                    <td style="width:50px;">
                                        <input type="checkbox" name="reject_ids[]" class="selection" value="<?php echo $nts_item['return_item_id']; ?>" />

                                        <?php echo $k + 1; ?>
                                    </td>
                                    <td><?= americanDate($nts_item['date_added']); ?></td>
                                    <!--                            <td><?php echo $nts_item['shipment_number']; ?></td>-->
                                    <td align="center">
                                        <input name="reject_item_ids[<?php echo $nts_item['id'] ?>]" value="<?php echo $nts_item['return_item_id']; ?>" required />
                                    </td>
                                    <td><a href="<?= $host_path . 'product/' . $nts_item['product_sku']; ?>"><?php echo $nts_item['product_sku']; ?></a></td>
                                    <td><?= getItemName($nts_item['product_sku']); ?></td>
                                    <td><a href="<?= $host_path . 'viewOrderDetail.php?order=' . $nts_item['order_id']; ?>"><?= $nts_item['order_id']; ?></a></td>
                                    <td><?= linkToRma($nts_item['rma_number'], $host_path); ?></td>

                                    <?php if ($_SESSION['boxes_cost']): ?>
                                        <td>
                                            $<?php echo number_format($nts_item['cost'], 2); ?>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <input type="hidden" value="<?php echo $nts_item['product_sku']; ?>" name="sku[<?php echo $nts_item['return_item_id']; ?>]" />
                                        <input type="hidden" value="<?php echo $nts_item['order_id']; ?>" name="order_id[<?php echo $nts_item['return_item_id']; ?>]" />
                                        <input type="text" name="reason[<?php echo $nts_item['return_item_id'] ?>]" value="<?php echo $nts_item['reason'] ?>" />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <br /><br />

                        <div align="center">
                            <input type="button" value="Print" onclick="$('.printer').show();" />
                            <input type="submit" name="MoveGFS" value="Move to GFS" />
                            <input type="submit" name="MoveNTR" value="Move to NTR" />
                            <input type="submit" name="save" value="Save" />

                            <?php if ($_SESSION['login_as'] == 'admin'): ?>
                                <input type="submit" name="delete" value="Delete" onclick="if (!confirm('Are you sure?')) {
                                    return false;
                                }" />
                            <?php endif; ?>
                        </div>
                        <div class="printer" style="display: none;">
                            <div class="whitePage">
                                <div class="form">
                                    <select name="printer_id" id="printer_id">
                                     <option value="">Select</option>
                                        <?php foreach ($printers as $printer): ?>
                                        <option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_item['printer']): ?> selected="selected" <?php endif; ?>>
                                            <?php echo $printer['value'] ?>
                                        </option>
                                <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form">
                                    <input type="submit" name="print" value="Submit" onclick="if(!confirm('Are you sure?')){ return false; }" />
                                    <input class="button" type="button" value="Cancel" onclick="$('.printer').hide();" />
                                    <!-- <input type="hidden" name="selected_items1" id="selected_items1" value=""> -->
                                </div>
                            </div>
                        </div>	
                    </form>
                    `	   
                    <br /><br />
                    <table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
                        <tr>
                            <td colspan="7" align="left">
                                <?php echo $splitPage->display_count("Displaying %s to %s of (%s)"); ?>
                            </td>

                            <td colspan="6" align="right">
                                <?php echo $splitPage->display_links(10, $parameters); ?>
                            </td>
                        </tr>
                    </table>
                    <br />
                </div>
                <script type="text/javascript" src="../js/newmultiselect.js"></script>
                <script type="text/javascript">
                    $(function () {
                        $('#table1').multiSelect({
                            actcls: 'highlightx',
                            selector: 'tbody .list_items',
                            except: ['form'],
                            callback: function (items) {
                                traverseCheckboxes('#table1', '.selection');
                            }
                        });
                    })
                </script>
            </body>
            </html>            			   