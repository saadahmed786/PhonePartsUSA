<?php
require_once("auth.php");
require_once("inc/functions.php");
page_permission("product_pricing");
$table = "inv_product_pricing";
$page = 'product_pricing.php';
$title = "Product Pricing Settings";
$mode = $_GET['mode'];
$selected = '';
if ($mode == 'edit') {
    $id = (int) $_GET['id'];
    $result = $db->func_query_first("select * from $table where id = '$id'");
 //   $selected = ($result['kitsku_name']) ? 'checked' : '';
}
if ($mode == 'delete') {
    $id = (int) $_GET['id'];
    $db->db_exec("DELETE FROM $table WHERE id='" . $id . "'");
    $_SESSION['message'] = "Record Deleted";
    header("Location: $page");
    exit;
}

if ($_POST['add']) {
    unset($_POST['add']);

    $array = array();
	//$_POST['kitsku_name'] = 'SRN-SAM-900K';
    $array = $_POST;
    

    if ($id) {
        $array['date_modified'] = date('Y-m-d H:i:s');
        $db->func_array2update($table, $array, "id = '$id'");
    } else {
        $array['date_added'] = date('Y-m-d H:i:s');
        $id = $db->func_array2insert($table, $array);
    }

    $log = 'Price Group Modified From "'. $_POST['range_from'] .'" To "'. $_POST['range_to'] .'"';
    actionLog($log);

    header("Location: $page");
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title><?= $title; ?></title>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script>
        function checkKitSKu (all, disableClass, check) {
            if ($('#' + check).is(":checked")) {
                $(all).attr('disabled', 'disabled');
                $('.' + disableClass).removeAttr('disabled');
            } else {
                $(all).removeAttr('disabled');
                $('.' + disableClass).attr('disabled', 'disabled');
            }
        }
        $(document).ready(function () {
            checkKitSKu('input[type=text]', 'kitSku', 'updateSKU');
            $('#updateSKU').click(function () {
                checkKitSKu('input[type=text]', 'kitSku', 'updateSKU');
            });
        });
    </script>
</head>
<body>
    <div align="center">
        <div align="center" >
            <?php include_once 'inc/header.php'; ?>
        </div>

        <?php if ($_SESSION['message']): ?>
            <div align="center"><br />
                <font color="red"><?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?><br /></font>
                </div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <h2> <?= $title; ?></h2>
                <table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
                    <tr>
                        <td>Range:</td>
                        <td>From <input type="text" name="range_from" value="<?php echo @$result['range_from']; ?>" required style="width:70px" /> to <input type="text" name="range_to" value="<?php echo @$result['range_to']; ?>" required style="width:70px" /> </td>
                    </tr>

                    <tr>
                        <td>Markup General:</td>
                        <td> <input type="text" name="markup_general" value="<?php echo @$result['markup_general']; ?>" required style="width:70px" /> </td>
                    </tr>
                    <tr>
                        <td>Markup Special:</td>
                        <td> <input type="text" name="markup_special" value="<?php echo @$result['markup_special']; ?>" required style="width:70px" /> </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td colspan="2">
                            <table width="80%" cellspacing="0" cellpadding="5" border="1">
                                <tbody><tr>
                                    <th>Customer Group</th>
                                    <th colspan="2">QTY 1</th>
                                    <th colspan="2">QTY 3</th>
                                    <th colspan="2">QTY 10</th>
                                </tr>

                                <tr>
                                    <td>Default</td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_d1']; ?>" name="markup_d1" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_d3']; ?>" name="markup_d3" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_d10']; ?>" name="markup_d10" required></td>
                                </tr>
                                <tr>
                                    <td>Local</td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_l1']; ?>" name="markup_l1" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_l3']; ?>" name="markup_l3" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_l10']; ?>" name="markup_l10" required></td>
                                </tr>
                                <tr>
                                    <td>Wholesale Small</td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_w1']; ?>" name="markup_w1" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_w3']; ?>" name="markup_w3" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_w10']; ?>" name="markup_w10" required></td>
                                </tr>
                                
                                <tr>
                                    <td>Silver</td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_silver1']; ?>" name="markup_silver1" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_silver3']; ?>" name="markup_silver3" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_silver10']; ?>" name="markup_silver10" required></td>
                                </tr>
                                
                                <tr>
                                    <td>Gold</td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_gold1']; ?>" name="markup_gold1" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_gold3']; ?>" name="markup_gold3" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_gold10']; ?>" name="markup_gold10" required></td>
                                </tr>
                                
                                <tr>
                                    <td>Platinum</td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_platinum1']; ?>" name="markup_platinum1" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_platinum3']; ?>" name="markup_platinum3" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_platinum10']; ?>" name="markup_platinum10" required></td>
                                </tr>
                                
                                <tr>
                                    <td>Diamond</td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_diamond1']; ?>" name="markup_diamond1" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_diamond3']; ?>" name="markup_diamond3" required></td>
                                    <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['markup_diamond10']; ?>" name="markup_diamond10" required></td>
                                </tr>
                                
                                
                                
                            </tbody>
                        </table>
                        <br></br>
                        <table width="80%" cellspacing="0" cellpadding="5" border="1">
                            <tbody><tr>
                                <th>Grades</th>
                                <th colspan="2">Price</th>
                            </tr>

                            <tr>
                                <td>Grade A--</td>
                                <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['grade_a']; ?>" name="grade_a" required></td>
                            </tr>
                            <tr>
                                <td>Grade B--</td>
                                <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['grade_b']; ?>" name="grade_b" required></td>
                            </tr>
                            <tr>
                                <td>Grade C--</td>
                                <td align="center" colspan="2"><input type="text" size="10" value="<?= $result['grade_c']; ?>" name="grade_c" required></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            
            <tr>
                <td> Kit Price:</td>
                <td> <input type="text" name="kit_price" value="<?php echo @$result['kit_price']; ?>" required style="width:70px" /> </td>
            </tr>
            
            
            <tr style="display:none">
                <td>Please Check to Update Kit SKU</td>
                <td><input type="checkbox" id="updateSKU" value="1" <?= $selected; ?> /></td>
            </tr>
            <tr style="display:none">
                <td>Kit SKU Name:</td>
                <td><input type="text" name="kitsku_name" value="<?php echo @$result['kitsku_name']; ?>" style="width:200px" class="kitSku" /></td>
            </tr>

            

            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="add" value="Submit" />
                </td>
            </tr>
        </table>
    </form>
</div>

<div style="margin-top:20px">
    <?php
    $lists = $db->func_query("SELECT * FROM $table ORDER BY range_from");
    ?>	
    <table class="data" border="1" style="border-collapse:collapse;" width="95%" cellspacing="0" align="center" cellpadding="5">
        <tr style="background:#e5e5e5;">
            <th style="width:50px;">#</th>


            <th align="center">Range</th>
            <th align="center">General</th>
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

            <th align="center">G A</th>
            <th align="center">G B</th>
            <th align="center">G C</th>
            <th align="center">Kit</th>
            
            

            <th align="center">Date Added</th>
            <th align="center">Date Modified</th>

            <th align="center">Action</th>


        </tr>
        <?php
        $i = 1;
        foreach ($lists as $list):
            ?>
        <tr>
            <td align="center"><?= $i; ?></td>

            <td align="center"><?=  '$' . number_format($list['range_from'], 2) . ' - ' . '$' . number_format($list['range_to'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_general'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_d1'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_d3'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_d10'], 2); ?></td>

            <td align="center"><?= number_format($list['markup_l1'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_l3'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_l10'], 2); ?></td>

            <td align="center"><?= number_format($list['markup_w1'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_w3'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_w10'], 2); ?></td>
            
            <!-- -->
            <td align="center"><?= number_format($list['markup_silver1'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_silver3'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_silver10'], 2); ?></td>
            
            <td align="center"><?= number_format($list['markup_gold1'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_gold3'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_gold10'], 2); ?></td>
            
            <td align="center"><?= number_format($list['markup_platinum1'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_platinum3'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_platinum10'], 2); ?></td>
            
            <td align="center"><?= number_format($list['markup_diamond1'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_diamond3'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_diamond10'], 2); ?></td>
            
            
            <!-- -->

            <td align="center"><?= number_format($list['grade_a'], 2); ?></td>
            <td align="center"><?= number_format($list['grade_b'], 2); ?></td>
            <td align="center"><?= number_format($list['grade_c'], 2); ?></td>
            <td align="center"><?= number_format($list['markup_d1'],2).'+'.number_format($list['kit_price'],2); ?></td>
            
            

            <td align="center"><?= americanDate($list['date_added']); ?></td>
            <td align="center"><?php if ($list['date_modified']) echo americanDate($list['date_modified']); ?></td>


            <td align="center"><a href="<?= $page; ?>?mode=edit&id=<?= $list['id']; ?>">Edit</a> | <a href="javascript:void(0);" onclick="if (confirm('Are you sure to delete this entry?')) {
                window.location = '<?= $page; ?>?mode=delete&id=<?= $list['id']; ?>'
            }">Delete</a></td>
        </tr>
        <?php
        $i++;
        endforeach;
        ?>
    </table>


</div>

</body>
</html>