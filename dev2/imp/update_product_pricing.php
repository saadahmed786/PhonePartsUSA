<?php
require_once("auth.php");
require_once("inc/functions.php");
page_permission("product_pricing");
$table = "inv_price_change_history";
$page = 'update_product_pricing.php';
$title = "Update Pricing";

if ($_POST['sku']) {
    foreach ($_POST['sku'] as $sku) {
        $product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='" . $db->func_escape_string($sku) . "'");
        if ($product_id) {
            $db->db_exec("UPDATE oc_product SET price='" . (float) $_POST['product_price'][$sku] . "' WHERE product_id='" . (int) $product_id . "'");
            $db->db_exec("UPDATE $table SET is_updated='1' WHERE sku='" . $sku . "'");
            $db->db_exec("DELETE FROM oc_product_discount WHERE product_id='" . (int) $product_id . "'");

            foreach ($_POST['discount'][$sku] as $customer_group_id => $data) {

                foreach ($data as $qty => $price) {
                    $db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product_id . "' , customer_group_id = '" . (int) $customer_group_id . "' , quantity = '" . (int) $qty . "' , price = '" . (float) $price . "'");
                }
            }

            foreach ($_POST['grade'][$sku] as $grade => $price) {

                $db->db_exec('UPDATE oc_product SET  `price` = "' . $price . '" WHERE `main_sku` = "' . $sku . '" AND `item_grade` = "' . $grade . '"');
            }

            $kitP = $_POST['kitsku_price'][$sku];
            $kitS = $_POST['kitSku'][$sku];
            if ($kitP && $kitS) {
                $db->db_exec('UPDATE oc_product SET  `price` = "' . $kitP . '" WHERE `sku` = "' . $kitS . '"');
            }

        }
    }
    $log = 'Price Updated From Price Change Report';
    actionLog($log);
    $_SESSION['message'] = "Pricing Updated!";
    header("Location: $page");
}
$inv_query = "SELECT a.* FROM $table a, oc_product op WHERE a.sku = op.sku AND op.ignore_up <> 1 AND is_updated=0";

if (isset($_GET['start_date']) && isset($_GET['end(array)d_date'])) {

    $inv_query.= " AND a.date_added BETWEEN '" . date('Y-m-d', strtotime($_GET['start_date'])) . "' AND  '" . date('Y-m-d', strtotime($_GET['end_date'])) . "'";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title><?= $title; ?></title>
    <link href="include/style.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="js/jquery.min.js"></script>




    <style>
        input[type="text"]{
            border:none;

            text-align:center;
            font-size:9pt;	
        }

    </style>
</head>
<body>

    <div style="display: none;">
        <?php include_once 'inc/header.php';?>
    </div>


    <div style="margin-top:20px">
        <?php
        $lists = $db->func_query($inv_query);
        ?>	

        <?php if (@$_SESSION['message']): ?>
            <div align="center"><br />
                <font color="red"><?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?><br /><br /></font>
                </div>
            <?php endif; ?>

            <form name="search_frm" method="get">
                <table class="data" border="1" style="border-collapse:collapse;margin-bottom:20px" width="65%" cellspacing="0" align="center" cellpadding="5">
                    <tr>
                        <td>
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="datepicker" value="<?php echo @$_REQUEST['start_date'];?>" name="start_date"  value="<?= ($_GET['start_date'] ? date('Y-m-d', strtotime($_GET['start_date'])) : ''); ?>">

                        </td>

                        <td>
                            <label for="end_date" style="margin-left: 30px;" valign="top">End Date:</label> 
                            <input type="date" class="datepicker" value="<?php echo @$_REQUEST['end_date'];?>" name="end_date"  value="<?= ($_GET['end_date'] ? date('Y-m-d', strtotime($_GET['end_date'])) : ''); ?>">
                        </td>
                        <td colspan="3">
                            <input type="submit" class="button" value="Search" />

                        </td>
                    </tr>
                </table>    
            </form>



            <form name="frm" method="post" enctype="multipart/form-data" onSubmit="if (confirm('Are you sure to proceed?')) {
            if (!verifyPositions()) {
            alert('Please Select One Product!');
            return false;
        } else {
        return true;
    }
} else
return false;">
<input type="button" class="button" value="Ignore" onclick="hideIgnored();" />

<table class="data" border="1" style="border-collapse:collapse;" width="95%" cellspacing="0" align="center" cellpadding="5">
    <tr style="background:#e5e5e5;">
        <th style="width:30px;" align="center"> <input type="checkbox" id="toggleCheck" onclick="if ($(this).is(':checked'))
            {
            $('.order_checks').prop('checked', true);

        }
        else
        {
        $('.order_checks').prop('checked', false);

    }" /></th>



    <th align="center">Date Completed</th>
    <th align="center">SKU</th>
    <th align="center">Item Name</th>
    <th align="center">Raw Cost</th>

    <th align="center">True Cost</th>
    <th align="center">General Price</th>

    <th align="center">D1</th>
    <th align="center">D3</th>
    <th align="center">D10</th>

    <th align="center">L1</th>
    <th align="center">L3</th>
    <th align="center">L10</th>

    <th align="center">W1</th>
    <th align="center">W3</th>
    <th align="center">W10</th>

    <th align="center">S1</th>
    <th align="center">S3</th>
    <th align="center">S10</th>

    <th align="center">G1</th>
    <th align="center">G3</th>
    <th align="center">G10</th>

    <th align="center">P1</th>
    <th align="center">P3</th>
    <th align="center">P10</th>

    <th align="center">D1</th>
    <th align="center">D3</th>
    <th align="center">D10</th>

    <th align="center">G-A</th>
    <th align="center">G-B</th>
    <th align="center">G-C</th>

    <th align="center">Kit</th>

</tr>
<?php
$i = 1;
foreach ($lists as $list):

    $previous_cost = $db->func_query_first("SELECT DISTINCT  DATE(cost_date) as cost_date,raw_cost FROM inv_product_costs WHERE sku='" . $list['sku'] . "' ORDER BY id DESC limit 1,1");
$cost_difference = $previous_cost['raw_cost'] - (float) $list['raw_cost'];

                        // Check if product have a KIT

if ($cost_difference != 0) {

    $true_cost = ($list['raw_cost'] + $list['shipping_fee']) / $list['ex_rate'];
    $true_cost = round($true_cost, 2);
    $markup = $db->func_query_first("SELECT * FROM  inv_product_pricing WHERE  $true_cost BETWEEN COALESCE(`range_from`,$true_cost) AND COALESCE(`range_to`,$true_cost)");

                            // Getting Kit Sku related to this product(sku)
    $sql = 'SELECT 
    iks.`kit_sku`, op.`price`
    FROM
    `inv_kit_skus` AS `iks`
    INNER JOIN
    `oc_product` AS `op` ON op.`sku` = iks.`kit_sku`
    WHERE
    iks.`kit_sku` = "' . $list['sku'] . 'K"
    ';

    $kitSku = $db->func_query_first($sql);
                            // Setting kit sku Price if it exist;
    $kitSkuPrice = 0;
    if ($kitSku) {
                              /*  $sql = 'SELECT 
                                        `kitsku_margin`
                                    FROM
                                        `inv_product_pricing`
                                    WHERE
                                        `kitsku_name` = "' . $kitSku['kit_sku'] . '"';
                                        $kitSkuMargin = $db->func_query_first_cell($sql);*/


                                        $kitSkuPrice = ($true_cost * $markup['markup_d1'])+$markup['kit_price'];

                                        $_temp_kit_sku = explode('.',(float)$kitSkuPrice);

                                        if((int)$_temp_kit_sku[1]==0)
                                        {
                                          $kitSkuPrice = $_temp_kit_sku[0].'.0000';	

                                      }
                                      else
                                      {

                                          $kitSkuPrice = $_temp_kit_sku[0].'.9500';	
                                      }
                                  }
                                  else
                                  {

                                     $kitSkuPrice = 0;


                                 }

                                 ?>
                                 <tr id="row">
                                    <td align="center"><input type="checkbox" name="sku[]" id="checkbox" class="order_checks"  value="<?= $list['sku']; ?>"></td>
                                    <td align="center"><?php echo date('m/d/Y h:ia', strtotime($list['date_added'])) ?></td>
                                    <td align="center"><?= $list['sku']; ?></td>
                                    <td align="center"><?php echo $db->func_query_first_cell("SELECT b.name FROM oc_product a,oc_product_description b WHERE a.product_id=b.product_id AND  a.sku='" . $list['sku'] . "'"); ?></td>
                                    <td align="center"><?= number_format($list['raw_cost'], 2); ?></td>
                                    <td align="center">$<?= number_format($true_cost, 2); ?></td>
                                    <td align="center"><input type="text" name="product_price[<?= $list['sku']; ?>]" value="<?= round($true_cost * $markup['markup_general'], 2); ?>" style="width:40px" /></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][8][1]" value="<?= round($true_cost * $markup['markup_d1'], 2); ?>" style="width:40px" /></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][8][3]" value="<?= round($true_cost * $markup['markup_d3'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][8][10]" value="<?= round($true_cost * $markup['markup_d10'], 2); ?>"  style="width:40px"/></td>

                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][10][1]" value="<?= round($true_cost * $markup['markup_l1'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][10][3]" value="<?= round($true_cost * $markup['markup_l3'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][10][10]" value="<?= round($true_cost * $markup['markup_l10'], 2); ?>"  style="width:40px"/></td>

                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][6][1]" value="<?= round($true_cost * $markup['markup_w1'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][6][3]" value="<?= round($true_cost * $markup['markup_w3'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][6][10]" value="<?= round($true_cost * $markup['markup_w10'], 2); ?>"  style="width:40px"/></td>

                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1631][1]" value="<?= round($true_cost * $markup['markup_silver1'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1631][3]" value="<?= round($true_cost * $markup['markup_silver3'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1631][10]" value="<?= round($true_cost * $markup['markup_silver10'], 2); ?>"  style="width:40px"/></td>

                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1632][1]" value="<?= round($true_cost * $markup['markup_gold1'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1632][3]" value="<?= round($true_cost * $markup['markup_gold3'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1632][10]" value="<?= round($true_cost * $markup['markup_gold10'], 2); ?>"  style="width:40px"/></td>

                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1633][1]" value="<?= round($true_cost * $markup['markup_platinum1'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1633][3]" value="<?= round($true_cost * $markup['markup_platinum3'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1633][10]" value="<?= round($true_cost * $markup['markup_platinum10'], 2); ?>"  style="width:40px"/></td>

                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1634][1]" value="<?= round($true_cost * $markup['markup_diamond1'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1634][3]" value="<?= round($true_cost * $markup['markup_diamond3'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="discount[<?= $list['sku']; ?>][1634][10]" value="<?= round($true_cost * $markup['markup_diamond10'], 2); ?>"  style="width:40px"/></td>

                                    <td align="center"><input type="text" name="grade[<?=$list['sku'];?>][Grade A]" value="<?= round($true_cost * $markup['grade_a'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="grade[<?=$list['sku'];?>][Grade B]" value="<?= round($true_cost * $markup['grade_b'], 2); ?>"  style="width:40px"/></td>
                                    <td align="center"><input type="text" name="grade[<?=$list['sku'];?>][Grade C]" value="<?= round($true_cost * $markup['grade_c'], 2); ?>"  style="width:40px"/></td>

                                    <td align="center"><input type="text" name="kitsku_price[<?=$list['sku'];?>]" value="<?= round($kitSkuPrice, 2); ?>"  style="width:40px"/><input type="hidden" name="kitSku[<?=$list['sku'];?>]" value="<?= $kitSku['kit_sku']; ?>" /></td>

                                </tr>
                                <?php
                            }
                            $i++;
                            endforeach;
                            ?>
                        </table>
                        <br />
                        <input type="submit" name="add" class="button" value="Update" style="margin-left:90%" />
                    </form>

                </div>

            </body>
            </html>			 	
            <script>
                function toggleCheck(obj)
                {
                    if ($(obj).is(':checked'))
                    {
                        $('.order_checks').prop('checked', true);

                    }
                    else
                    {
                        $('.order_checks').prop('checked', false);

                    }
        //selectedChecks();
    }
    function selectedChecks()
    {
        var arr = new Array();
        $('.order_checks').each(function (index, element) {
            if ($(this).is(':checked'))
            {
                arr.push($(this).val());
            }
        });
        $('#ids').val(arr.join());
    }

    function verifyPositions() {
        var flag = false;

        $(".order_checks").each(function () {
            if ($(this).is(":checked")) {
                flag = true;
            }
        });

        if (!flag) {
            return false;
        } else {
            return true;
        }
    }


    function hideIgnored(){

       var arr = new Array();
        $('.order_checks').each(function (index, element) {
            if ($(element).is(':checked'))
            {
               // arr.push($(this).val());
               $(element).hide();
               document.getElementById("checkbox").checked = false;

  //              arr.push($(this).val());
//              arr.hide();
               // document.write(arr);
  //            $("#arr").hide();
            }
        });
      //  $('#ids').val(arr.join());


    }


</script>