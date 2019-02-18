<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

if ((int) $_GET['id'] and $_GET['action'] == 'ignore') {
    $product_id = (int) $_GET['id'];
    $db->db_exec("update oc_product SET ignored = '0' where product_id = '$product_id'");

    $_SESSION['message'] = "Product status is ignored";
    header("Location:sku_creation.php");
    exit;
}

if ((int)$_GET['product_id'] && $_GET['action'] == 'delete') {

    $sk = $db->func_query_first('SELECT * FROM oc_product where product_id = "'. (int)($_POST['product_id']) .'"');
    $log = 'SKU Deleted ' . $sk['sku'];
    actionLog($log);

    $tables = array('oc_product_to_store', 'oc_product_to_layout', 'oc_product_to_field', 'oc_product_to_download', 'oc_product_to_category', 'oc_product_tag', 'oc_product_special', 'oc_product_sn', 'oc_product_similar', 'oc_product_reward', 'oc_product_related', 'oc_product_option_value', 'oc_product_option', 'oc_product_image', 'oc_product_discount', 'oc_product');


    foreach ($tables as $key => $table) {
        $db->db_exec('DELETE FROM `' . $table . '` WHERE product_id = "'. (int)$_GET['product_id'] .'"');
    }

    $_SESSION['message'] = "Product Deleted";
    header("Location:sku_creation.php");
    exit;
}


if(isset($_POST['action']) and $_POST['action']=='ajax')
{
	
    $class_id = $_POST['class_id'];




    $attrib_groups = 	$db->func_query_first("SELECT attribute_group_id FROM inv_classification WHERE id='".$class_id."'");
    if($attrib_groups['attribute_group_id']=='')
    {
      echo 'No Attribute Defined for this SKU Type';	
  }
  else
  {


     $attrib_groups =  rtrim($attrib_groups['attribute_group_id'],",");
     $attrib_groups = explode(",",$attrib_groups);

     echo '<input type="checkbox" name="attrib[]" value="0"> No Attribute <input type="text" name="text_value[]" value="" style="display:none"> <br />';
     foreach($attrib_groups as $attrib_group)
     {
        $group_info = $db->func_query_first("SELECT name FROM inv_attribute_group WHERE id='".$attrib_group."'");
        $rows = 	$db->func_query("SELECT * FROM inv_attr WHERE attribute_group_id='".(int)$attrib_group."'");	

        echo '<strong>'.$group_info['name'].'</strong><br />';
        $c = 1;
        $c2=0;
        foreach($rows as $row)
        {





          $checked = '';	


          echo '<input type="checkbox" name="attrib[]" value="'.$row['id'].'" '.$checked.'> '.$row['name'].($row['is_text']==1?'<input type="text" name="text_value[]" value="">':'<input type="text" name="text_value[]" value="" style="display:none">').'<br />';	

      }


  }

}	
exit;
}
if (isset($_POST['add'])) {
    $last_id = getProductSkuLastID($_POST['sku_type']);
    $product_sku = getSKUFromLastId($_POST['sku_type'], $last_id);
    $status = ($_POST['status']) ? 1 : 0;
    if ($_POST['custom_sku'] != '') {
        $product_sku = $_POST['custom_sku'];
    }
    $image = '';
    if ($_FILES['image']['tmp_name'] AND $_FILES['image']['error'] == 0) {
        $name = preg_replace("/[^a-zA-Z0-9 ]/is", "", $_POST['name']);
        $file_name = substr(str_ireplace(" ", "-", strtolower($name)), 0, 80) . ".jpg";

        $image = "impskus/$file_name";
        if (file_exists("../image/$image")) {
            $image = md5(microtime()) . '.jpg';
        }
        move_uploaded_file($_FILES['image']['tmp_name'], "../image/$image");
    }

    if ($_SESSION['edit_cost']) {
        createSKU($product_sku, $_POST['name'], '', $_POST['price'], '', 1, '', $image, $status);
    } else {
        createSKU($product_sku, $_POST['name'], '', NULL, '', 1, '', $image, $status);
    }
    if ($_POST['custom_sku'] == '') {
        if ($_POST['grade_a']) {
            createGradeSku($product_sku, 'A');
        }

        if ($_POST['grade_b']) {
            createGradeSku($product_sku, 'B');
        }

        if ($_POST['grade_c']) {
            createGradeSku($product_sku, 'C');
        }

        if ($_POST['grade_d']) {
            createGradeSku($product_sku, 'D');
        }
    }
    $db->db_exec("UPDATE oc_product SET weight='".(float)$_POST['weight_lb']."',weight_class_id=5 WHERE sku='".$product_sku."'");
    initCompetitorLinks($product_sku);

    $sku = $product_sku;
    $name = $_POST['name'];
    $classes = $_POST['item_class'];
    $manufacturers = $_POST['manufacturer'];
    $devices = $_POST['device'];
    $models = $_POST['model'];
    $attribs = $_POST['attrib'];
    $error = array();
    if (!$classes) {
        $error[] = 'Please select class';
    }

    if (!$manufacturers) {
        $error[] = 'Please select manufacturer';
    }
    if (!$devices) {
        $error[] = 'Please select device';
    }
    if (!$models) {
        $error[] = 'Please select models';
    }
    if (!$attribs) {
        $error[] = 'Please select attributes';        
    }
    //getting skus of grades and kits.
    $grade_skus = $db->func_query("SELECT DISTINCT sku FROM oc_product WHERE main_sku='" . $sku . "'");
    $kit_sku = $db->func_query_first_cell("SELECT DISTINCT sku  FROM oc_product WHERE sku LIKE '". $sku ."K'");
    $skus = array();
    $skus[] = $sku;
    foreach ($grade_skus as $gsku) {
        $skus[] = $gsku['sku'];
    }

    
    if ($kit_sku) {
        $skus[]  =  $kit_sku;
    }

    if (!$error) {
    //Going on Loop to put it in db
        foreach ($skus as $sku) {
            $name = $db->func_query_first_cell("SELECT b.name FROM oc_product_description b,oc_product a WHERE a.product_id=b.product_id AND a.sku='" . $sku . "'");
            $oldproductid = $db->func_query_first_cell('SELECT `device_product_id` FROM `inv_device_product` where sku = "' . $sku . '"');
            $db->db_exec("delete from inv_device_product where sku = '" . $sku . "'");

            $array = array();

            $array['sku'] = $sku;
            $array['name'] = $name;
            $array['date_added'] = date('Y-m-d h:i:s');
            $array['added_by'] = $_SESSION['login_as'];

            $prod_id = $db->func_array2insert("inv_device_product", $array);

            $array = array();
            $array['device_product_id'] = $prod_id;
            $array['class_id'] = $classes;

            $db->func_query("UPDATE oc_product SET classification_id = $classes WHERE model = '$sku'");

            $db->db_exec("delete from inv_device_class where device_product_id = '" . $oldproductid . "'");
            $class_id = $db->func_array2insert("inv_device_class", $array);

            foreach ($manufacturers as $manufacturer) {
                $array = array();

                $array['device_product_id'] = $prod_id;
                $array['manufacturer_id'] = $manufacturer;
                $array['class_id'] = $class_id;


                $manuf_id = $db->func_array2insert("inv_device_manufacturer", $array);

                foreach ($devices as $device) {
                    $xdevice = explode("-", $device);

                    if ($xdevice[1] == $manufacturer) {
                        $array = array();

                        $array['device_manufacturer_id'] = $manuf_id;
                        $array['device_id'] = $device;


                        $device_id = $db->func_array2insert("inv_device_device", $array);

                        foreach ($models as $model) {

                            $xmodel = explode("-", $model);

                            if ($xmodel[1] == $xdevice[0]) {

                                $array = array();

                                $array['device_device_id'] = $device_id;
                                $array['model_id'] = $xmodel[0];


                                $model_idx = $db->func_array2insert("inv_device_model", $array);

                                $db->db_exec("DELETE FROM inv_device_attrib WHERE device_model_id='" . $model_idx . "'");

                                foreach ($attribs as $akey => $attrib) {





                                    $array = array();

                                    $array['device_model_id'] = $model_idx;
                                    $array['attrib_id'] = $attrib;
                                    $array['text_value'] = $_POST['text_value'][$akey];


                                    $model_id = $db->func_array2insert("inv_device_attrib", $array);
                                }
                            }
                        }
                    }
                }
                
            }
        }
    }

    $_SESSION['message'] = "New SKU created";
    if ($error) {
        $_SESSION['message'] .= '<br>' . implode('<br>', $error);
    }
    $log = 'New SKU created '. linkToProduct($product_sku);
    actionLog($log);
    header("Location:sku_creation.php");
    exit;
}

if (isset($_POST['update'])) {
    $main_sku = $db->func_escape_string($_POST['main_sku']);
    $product_detail = getProduct($main_sku, array("is_main_sku"));

    if (!$product_detail['is_main_sku']) {
        $_SESSION['message'] = "$main_sku is not main sku.";
        header("Location:sku_creation.php");
        exit;
    }

    $grade_skus = $db->func_query("select model,item_grade from oc_product where main_sku = '" . $main_sku . "'", "item_grade");
}

if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
}
if ($page < 1) {
    $page = 1;
}

if($_SESSION['product_pricing']){

    $default_price=$_SESSION['product_pricing'];
}

$max_page_links = 10;
$num_rows = 200;
$start = ($page - 1) * $num_rows;

$keyword = @$_GET['keyword'];
if ($keyword) {
    $keyword = $db->func_escape_string($keyword);
    $where = " Where Lower(pd.name) like Lower('%$keyword%') OR Lower(p.sku) like Lower('%$keyword%') ";
    $parameters[] = "keyword=$keyword";
} else {
    $where = " Where is_imp_sku = 1";
    $parameters[] = "";
}

$_query = "select p.product_id , p.sku , p.fb_added, p.ignored, p.date_added, pd.name, p.price , p.status , p.image , p.is_main_sku , u.name as user_name from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
left join inv_users u on (u.id = p.user_id) $where order by date_added desc";

$splitPage = new splitPageResults($db, $_query, 100, "sku_creation.php", $page);
$products = $db->func_query($splitPage->sql_query);

$product_skus = $db->func_query("select sku from inv_product_skus order by sku asc");
$item_classification =$db->func_query("SELECT * FROM inv_classification WHERE status=1");
$manufacturers = $db->func_query("select * from inv_manufacturer WHERE status=1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>SKU Creation</title>

    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.lazyload.min.js"></script>
    <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

    <script type="text/javascript">
        $(document).ready(function () {
            $('.fancybox').fancybox({width: '600px', height: '300', autoCenter: true, autoSize: false});
            $('.fancybox2').fancybox({width: '500px', height: '500px', autoCenter: true, autoSize: false});

            $("img.lazy").lazyload({
                effect: "fadeIn"
            });
        });
    </script>	
    <style type="text/css">
        #table1 tr select.multiple {
            min-height: 500px;
        }
    </style>
</head>
<body>
    <div align="center"> 
        <?php include_once 'inc/header.php'; ?>
    </div>

    <?php if ($_SESSION['message']): ?>
        <div align="center"><br />
            <font color="red"><?php echo $_SESSION['message'];
                unset($_SESSION['message']); ?><br /></font>
            </div>
        <?php endif; ?>

        <div align="center">
            <form method="post" enctype="multipart/form-data">
                <table border="1" width="30%" cellpadding="5" cellspacing="0" style="float:left;margin-left:15%;">
                    <caption>Add New SKU</caption>
                    <tr>
                        <td width="100" colspan="2">Sku Type</td>				
                        <td align="left" colspan="2">
                            <select name="sku_type" onchange="showCustomSKUBox(this.value)" required>
                                <option value="">Select SKU Type</option>
                                <?php foreach ($product_skus as $product_sku): ?>
                                    <option value="<?php echo $product_sku['sku']; ?>"><?php echo $product_sku['sku']; ?></option>
                                <?php endforeach; ?>
                                <option value="-1">Custom</option>
                            </select>
                            <input type="text" name="custom_sku" id="custom_sku" placeholder="Custom SKU" style="display:none" />
                            <script>
                                function loadAttr (t) {
                                    var class_id = $(t).val();                                    
                                    $.ajax({
                                        url: "sku_creation.php",
                                        type:"POST",
                                        data: {class_id:class_id,action:'ajax'},
                                        success: function(data) {


                                           $('#div_device').html(data);


                                       }
                                   });
                                }
                                function showCustomSKUBox(val)
                                {
                                    if (val == '-1')
                                    {
                                        $('#custom_sku').val('');
                                        $('#custom_sku').show();
                                    }
                                    else
                                    {
                                        $('#custom_sku').val('');
                                        $('#custom_sku').hide();

                                    }

                                }
                            </script>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">Name</td>				
                        <td align="left" colspan="2">
                            <input type="text" name="name" value="" size="30" required />
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">Image</td>				
                        <td colspan="2">
                            <input type="file" name="image" value="" />
                        </td>
                    </tr>

                    <?php if ($_SESSION['product_pricing']): ?>
                        <tr>
                            <td colspan="2">Price</td>				
                            <td colspan="2">
                                <input type="number" name="price" step="any" value="" required />
                            </td>
                        </tr>
                    <?php endif; ?>	

                    <tr>
                        <td colspan="2">Status</td>				
                        <td colspan="2">
                            <input type="checkbox" name="status" value="1" />
                        </td>
                    </tr>

                    <tr>
                        <td>Grade A</td>				
                        <td>
                            <input type="checkbox" name="grade_a" value="1" />
                        </td>
                        <td>Grade B</td>				
                        <td>
                            <input type="checkbox" name="grade_b" value="1" />
                        </td>
                    </tr>

                    <tr>
                        <td>Grade C</td>				
                        <td>
                            <input type="checkbox" name="grade_c" value="1" />
                        </td>
                        <td>Grade D</td>				
                        <td>
                            <input type="checkbox" name="grade_d" value="1" />
                        </td>
                    </tr>
                    <tr>
                        <td>Weight</td>
                        <td colspan="3"><input type="text" name="weight_lb" id="weight_lb" style="width:80px" onchange="calculateWeight('lb');" /> lb <input type="text" name="weight_oz" id="weight_oz" style="width:80px" onchange="calculateWeight('oz');" /> oz</td>

                    </tr>
                   <!--  <tr>
                        <td colspan="2">Item Class</td>
                        <td colspan="2">
                            <select name="item_class" onchange="loadAttr(this);" required>
                                <option value="">Select Class</option>
                                <?php
                                foreach($item_classification as $class)
                                {
                                 ?>
                                 <option value="<?php echo $class['id'];?>"><?php echo $class['name'];?></option>
                                 <?php	

                             }
                             ?>
                         </select>
                     </td>
                 </tr>
                 <tr>
                    <td colspan="4" id="div_device"></td>
                </tr> -->

                <tr>
                    <td colspan="4" align="center">
                        <input type="submit" name="add" value="Submit" />
                    </td>
                </tr>
            </table>
            <table border="1" width="98%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;" id="table1">
                <tr style="background-color:#e7e7e7;font-weight:bold">
                    <td>Class</td>
                    <td>Manufacturer</td>
                    <td>Device</td>
                    <td>Model / Sub Model</td>

                    <td style="display:none">SKU Type</td>
                    <td>Attributes</td>
                </tr>
                <tr id="tr_0" class="list_items">
                    <td>
                        <input type="hidden" name="item_class" value="<?php $product['classification_id']; ?>" />
                        <select name="classification[]" id="classification0" onchange="populateDevice(0); populateModel(0); $('input[name=item_class]').val($(this).val());">
                            <option>Select Class</option>
                            <?php
                            $man_query1 = $db->func_query_first("SELECT * FROM inv_device_product WHERE sku='" . $product['sku'] . "'");
                            foreach ($item_classification as $class) {
                                $classSelect = $db->func_query_first("SELECT * FROM inv_device_class WHERE device_product_id='" . $man_query1['device_product_id'] . "' AND class_id='" . $class['id'] . "'");
                                ?>
                                <option value="<?php echo $class['id']; ?>" <?= ($class['id'] == $product['classification_id']) ? 'selected="selected"' : ''; ?>>
                                    <?= $class['name']; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td>

                        <select name="manufacturer[]" id="manufacturer0" multiple="multiple" class="multiple" onchange="populateDevice(0)">
                            <?php
                            $xmanu_id = '';
                            $device_did = '';
                            $device_did2 = '';
                            $device_model = '';
                            $device_model2 = '';
                            foreach ($manufacturers as $manufacturer): ?>
                            <?php $man_query2 = $db->func_query_first("SELECT * FROM inv_device_manufacturer WHERE device_product_id='" . $man_query1['device_product_id'] . "' AND manufacturer_id='" . $manufacturer['manufacturer_id'] . "'");
                            ?>
                            <option value="<?php echo $manufacturer['manufacturer_id']; ?>" <?php
                                if ($man_query2) {
                                    echo 'selected';
                                    $xmanu_id.=$man_query2['device_manufacturer_id'] . ',';
                                }
                                ?>><?php echo $manufacturer['name']; ?></option>
                            <?php endforeach; ?>
                            <?php $xmanu_id = rtrim($xmanu_id, ","); ?>

                        </select>
                        <a href="javascript:void(0);" style="float:left" onclick="clearIt('0', 'manufacturer')">Clear </a>
                        <input type="hidden" id="product_id0" value="<?php echo $man_query1['device_product_id']; ?>" />
                        <input type="hidden" id="manufacturer_ids0" value="<?php echo $xmanu_id; ?>" />
                    </td>
                    <td>
                        <div id="div_device0">
                            <?php
                            if ($xmanu_id) {
                                $man_query3 = $db->func_query("SELECT * FROM inv_device_device WHERE device_manufacturer_id IN ($xmanu_id)");


                                foreach ($man_query3 as $query) {
                                    $device_did.=$query['device_device_id'] . ',';
                                    $device_did2.=$query['device_id'] . ',';
                                    echo getResult("SELECT device FROM inv_model_mt WHERE model_id='" . $query['device_id'] . "'") . "<br>";
                                }
                                $device_did = rtrim($device_did, ",");
                                ?>

                                <?php
                            }
                            ?>

                        </div>
                        <a href="javascript:void(0);" style="float:left" onclick="clearIt('0', 'device')">Clear </a>
                        <a href="javascript:void(0);" style="float:right" onclick="editDevice('0', '<?php echo $device_did2; ?>')">Edit</a>
                    </td>
                    <td>
                        <div id="div_model0"><?php
                            if ($device_did) {
                                $man_query4 = $db->func_query("SELECT * FROM inv_device_model WHERE device_device_id IN ($device_did)");


                                foreach ($man_query4 as $query) {
                                    $device_model.=$query['device_model_id'] . ',';
                                    $device_model2.=$query['model_id'] . ',';

                                    $resultx = getResult("SELECT sub_model_id FROM inv_model_carrier WHERE id='" . $query['model_id'] . "'");
                                            //echo $resultx;
                                    echo getResult("SELECT sub_model FROM inv_model_dt WHERE sub_model_id='" . $resultx . "'") . "<br>";
                                }
                                $device_model = rtrim($device_model, ",");
                                $device_model2 = rtrim($device_model2, ",");
                            }
                            ?>
                        </div>
                        <a href="javascript:void(0);" style="float:left" onclick="clearIt('0', 'model')">Clear </a>
                        <a href="javascript:void(0);" style="float:right" onclick="editModel('0', '<?php echo $device_model2; ?>')">Edit</a>
                    </td>
                    <td style="display:none">
                        <div id="div_sku_type0" style="display:none">

                            <select name="sku_type" id="sku_type0" onchange="populateAttributes(0)">
                                <?php foreach ($sku_types as $sku_type) { ?>
                                <option value="<?php echo $sku_type['id']; ?>" <?php
                                    if ($my_sku == $sku_type['sku']) {
                                        $sku_type_id = $sku_type['id'];
                                        echo 'selected';
                                    }
                                    ?>><?php echo $sku_type['sku']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div id="div_attribs0"><?php
                            if ($device_model) {
                                $man_query4 = $db->func_query("SELECT DISTINCT attrib_id,text_value FROM inv_device_attrib WHERE device_model_id IN ($device_model)");

                                $_attrib = array();
                                $_attrib_parent = array();

                                foreach ($man_query4 as $attribs) {
                                    $attribute_row = $db->func_query_first("SELECT a.name,b.name as group_name,a.attribute_group_id,a.is_text
                                        FROM
                                        `inv_attr` a
                                        INNER JOIN `inv_attribute_group` b
                                        ON (a.`attribute_group_id` = b.`id`) where a.id='" . $attribs['attrib_id'] . "'");
                                    if ($attribs == 0) {
                                        echo 'No Attrib' . "<br>";
                                    }

                                    if (!in_array($attribute_row['attribute_group_id'], $_attrib_parent)) {
                                        $_attrib_parent[] = $attribute_row['attribute_group_id'];
                                        echo "<strong>" . $attribute_row['group_name'] . "</strong><br>";
                                    }
                                    $_attrib[] = $attribs['attrib_id'];
                                    $__attrib[] = $attribs['text_value'];
                                    if ($attribute_row['is_text'] == 0) {
                                        echo $attribute_row['name'] . "<br>";
                                    } else {
                                        echo $attribute_row['name'] . ": " . $attribs['text_value'] . " <br>";
                                    }
                                }
                                $device_model = rtrim($device_model, ",");
                            }
                            ?>
                        </div>
                        <input type="hidden" id="attrib_ids0" value="<?php echo implode(",", $_attrib); ?>" />
                        <input type="hidden" id="attrib_fields0" value="<?php echo implode(",", $__attrib); ?>" />
                        <input type="hidden" id="temp_did0" value="<?php echo $device_did; ?>"/>
                        <input type="hidden" id="temp_model0" value="<?php echo $device_model2; ?>" />
                        <script>
                            <?php
                            if ($xmanu_id and $man_query1['verified'] == 0) {
                                ?>
                                populateDevice(0);
                                <?php
                            }
                            ?>
                        </script>
                    </td>
                </tr>
            </table>
        </form>

        <form method="post" enctype="multipart/form-data">	
            <table border="1" width="30%" cellpadding="5" cellspacing="0" style="float:left;margin-left:2%;">
                <caption>Create Lower Grade SKU</caption>
                <tr>
                    <td>Main SKU</td>				
                    <td>
                        <input type="text" name="main_sku" value="<?php echo $_POST['main_sku'] ?>" />
                    </td>
                    <td>
                        <input type="submit" value="Update" name="update" />
                    </td>
                </tr>

                <?php if (isset($grade_skus)): ?>
                    <tr>
                        <td>Grade A</td>				
                        <td colspan="2">
                            <?php if ($grade_skus['Grade A']['model']): ?>
                                <input type="text" readonly="readonly" size="15" name="product[grade_a]" value="<?php echo $grade_skus['Grade A']['model'] ?>" /> 
                            <?php else: ?>
                                <a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $main_sku ?>&grade=A">Create SKU</a>
                            <?php endif; ?>	
                            <br />
                        </td>
                    </tr>

                    <tr>
                        <td>Grade B</td>				
                        <td colspan="2">
                            <?php if ($grade_skus['Grade B']['model']): ?>
                                <input type="text" readonly="readonly" size="15" name="product[grade_b]" value="<?php echo $grade_skus['Grade B']['model'] ?>" /> 
                            <?php else: ?>
                                <a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $main_sku ?>&grade=B">Create SKU</a>
                            <?php endif; ?>	
                            <br />
                        </td>
                    </tr>

                    <tr>
                        <td>Grade C</td>				
                        <td colspan="2">
                            <?php if ($grade_skus['Grade C']['model']): ?>
                                <input type="text" readonly="readonly" size="15" name="product[grade_c]" value="<?php echo $grade_skus['Grade C']['model'] ?>" /> 
                            <?php else: ?>
                                <a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $main_sku ?>&grade=C">Create SKU</a>
                            <?php endif; ?>	
                            <br />
                        </td>
                    </tr>

                    <tr>
                        <td>Grade D</td>				
                        <td colspan="2">
                            <?php if ($grade_skus['Grade D']['model']): ?>
                                <input type="text" readonly="readonly" size="15" name="product[grade_d]" value="<?php echo $grade_skus['Grade D']['model'] ?>" /> 
                            <?php else: ?>
                                <a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $main_sku ?>&grade=D">Create SKU</a>
                            <?php endif; ?>	
                            <br />
                        </td>
                    </tr>
                <?php endif; ?>		

            </table>
        </form>
        <br clear="all" /><br clear="all" /><br clear="all" />
    </div>
    <br />
    <div class="search" align="center">
        <form>
            Keyword: 
            <input type="text" name="keyword" value="<?php echo $keyword; ?>" required />
            <input type="submit" name="Go" class="button" value="Search" />
        </form>
    </div>
    <br />
    <table border="1" style="border-collapse:collapse;" width="90%" align="center" cellpadding="5">
        <tr style="background:#e5e5e5;">
            <th>#</th>
            <th>Image</th>
            <th>SKU</th>
            <th width="250px">Name</th>
            <th>Username</th>
            <?php if ($_SESSION['edit_cost']): ?>
                <th>Price</th>
            <?php endif; ?>	 
            <th>Status</th>
            <th>Main SKU</th>
            <th>FB Added</th>
            <th>Ignored</th>
            <th>Date Added</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $i => $product): ?>
            <tr>
                <td><?php echo $i + 1; ?></td>
                <td align="center">
                    <?php if ($product['image'] && file_exists(str_replace('imp/', '', $path) . 'image/' . $product['image'])) { ?>
                    <a href="<?= str_replace('imp/', '', $host_path); ?>image/<?php echo $product['image']; ?>" class="fancybox2 fancybox.iframe">
                        <img class="lazy" src="<?= str_replace('imp/', '', $host_path); ?>image/<?php echo $product['image']; ?>" data-original="<?= str_replace('imp/', '', $host_path); ?>image/<?php echo $product['image']; ?>" height="50" width="50" alt="" />
                    </a>
                    <?php } else { ?>
                    <a href="javascript:void(0)" class="fancybox2 fancybox.iframe">
                        <img class="lazy" src="<?= str_replace('imp/', '', $host_path); ?>image/no_image.jpg" data-original="<?= str_replace('imp/', '', $host_path); ?>image/no_image.jpg" height="50" width="50" alt="" />
                    </a>
                    <?php } ?>
                </td>
                <td align="center">
                    <?php if ($_SESSION['login_as'] == 'admin'): ?>
                        <a href="product/<?php echo $product['sku']; ?>"><?php echo $product['sku']; ?></a>
                    <?php else: ?>
                        <?php echo $product['sku']; ?>
                    <?php endif; ?>			
                </td>

                <td><?php echo $product['name']; ?></td>
                <td><?php echo ($product['user_name']) ? $product['user_name'] : ' Admin '; ?></td>
                <?php if ($_SESSION['edit_cost']): ?>
                    <td><?php echo $product['price']; ?></td>
                <?php endif; ?>	
                <td><?php echo $product['status']; ?></td>
                <td><?php echo ($product['is_main_sku'] == 1) ? 'Yes' : 'No'; ?></td>
                <td><?php echo ($product['fb_added'] == 1) ? 'Yes' : 'No'; ?></td>
                <td align="center">
                    <?php if ($product['ignored'] == 1): ?>
                        <a href="sku_creation.php?action=ignore&id=<?php echo $product['product_id'] ?>" onclick="if (!confirm('Are you sure?')) {
                          return false;
                      }">Upload Again</a>
                  <?php endif; ?>	
              </td>
              <td><?php echo americanDate($product['date_added']); ?></td>
              <td>
                <a class="fancybox fancybox.iframe" href="edit_sku.php?product_sku=<?php echo $product['sku']; ?>">Edit</a>
                <?php if ($_SESSION['delete_product']) { ?>
                <a href="sku_creation.php?product_id=<?php echo $product['product_id']; ?>&action=delete" onclick="return confirm('Do Want to Delete This Product');">Delete</a>
                <?php } ?> 
                
            </td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="2" align="left">
            <?php echo $splitPage->display_count("Displaying %s to %s of (%s)"); ?>
        </td>

        <td colspan="3" align="right">
            <?php echo $splitPage->display_links(10, $parameters); ?>
        </td>
    </tr>
</table>
<br /> 
<!-- Adding Product Clasification -->
<script>
    $(document).ready(function (e) {
        $('.fancybox3').fancybox({width: '980px', 'height': 800, autoCenter: true, autoSize: false});
    });

    function populateDevice($i)
    {
        $('#div_sku_type' + $i).hide();
        $('#div_model' + $i).html('');

        var manufacturers = $('#manufacturer' + $i).val();
        var product_id = $('#product_id' + $i).val();

        $.ajax({
            url: "devices_new.php",
            type: "POST",
            data: {manufacturers: manufacturers, action: 'ajax', type: 'device', i: $i, product_id: product_id},
            success: function (data) {


                $('#div_device' + $i).html(data);
                populateModel($i);

            }
        });



    }


    function editDevice($i, $device_ids)
    {

        var manufacturers = $('#manufacturer' + $i).val();
        $.ajax({
            url: "devices_new.php",
            type: "POST",
            data: {manufacturers: manufacturers, action: 'ajax', type: 'device', i: $i, device_ids: $device_ids},
            success: function (data) {


                $('#div_device' + $i).html(data);
            }
        });

    }


    function populateModel($i)
    {
        $('#div_sku_type' + $i).hide();

        var manufacturers = $('#manufacturer' + $i).val();
        var product_id = $('#product_id' + $i).val();
        var models = $('#device' + $i).val();
        var device_ids = $('#xdevice_id' + $i).val();
        $.ajax({
            url: "devices_new.php",
            type: "POST",
            data: {models: models, action: 'ajax', type: 'model', i: $i, device_ids: device_ids, manufacturer_ids: manufacturers, product_id: product_id},
            success: function (data) {


                $('#div_model' + $i).html(data);
                populateAttributes($i);
                $('#div_sku_type' + $i).show();
            }
        });



    }




    function editModel($i, $model_ids)
    {



        var models = $('#device' + $i).val();
        $.ajax({
            url: "devices_new.php",
            type: "POST",
            data: {models: models, action: 'ajax', type: 'model', i: $i, model_ids: $model_ids},
            success: function (data) {


                $('#div_model' + $i).html(data);
                populateAttributes2($i);

            }
        });

    }



    function populateAttributes($i)
    {

        var classification_type = $('#classification' + $i).val();
        var attribs = $('#attrib_ids' + $i).val();
        var text_fields = $('#attrib_fields' + $i).val();

        $.ajax({
            url: "devices_new.php",
            type: "POST",
            data: {classification_type: classification_type, action: 'ajax', type: 'attribs', i: $i, attribs: attribs, text_fields: text_fields},
            success: function (data) {


                $('#div_attribs' + $i).html(data);

            }
        });



    }


    function populateAttributes2($i)
    {

        var sku_type = $('#sku_type' + $i).val();
        var attribs = $('#attrib_ids' + $i).val();
        var text_fields = $('#attrib_fields' + $i).val();
            //var model_ids = $('#model'+$i).val();
            $.ajax({
                url: "devices_new.php",
                type: "POST",
                data: {sku_type: sku_type, action: 'ajax', type: 'attribs', i: $i, model_id: $('#model' + $i).val(), attribs: attribs, text_fields: text_fields, model_ids: model_ids},
                success: function (data) {


                    $('#div_attribs' + $i).html(data);

                }
            });



        }
        function submitThis(i, device_ids, model_ids)

        {
            opt = (typeof opt === 'undefined') ? '' : opt;

            var checked1 = []
            var checked2 = [];

            var index = 0;
            $('#tr_' + i + ' input[name=attrib\\[\\]]').each(function (index)
            {
                if ($(this).prop('checked') == true)
                {

                    checked1.push(parseInt($(this).val()));
                    checked2.push($('input[name=text_value\\[\\]]:eq(' + index + ')').val());

                }
                index++;
            });



            var classification = $('#classification' + i).val();
            var manufacturer = $('#manufacturer' + i).val();
            var device = $('#device' + i).val();
            var model = $('#model' + i).val()

            if (!device)
            {


                device = device_ids.split(",");
            }
            if (!model)
            {


                model = model_ids.split(",");
            }
            if (!manufacturer)
            {

                alert('Please select manufacturer');
                return false;
            }

            if (!device)
            {

                alert('Please select device');
                return false;
            }

            if (!model)
            {

                alert('Please select model');
                return false;
            }

            $.ajax({
                url: "devices_new.php",
                type: "POST",
                data: {sku: encodeURIComponent($('#sku' + i).val()), name: encodeURIComponent($('#name' + i).val()), classification: classification, manufacturer: manufacturer, device: device, model: model, attrib: checked1, text_value: checked2, add: 'save'},
                success: function (data) {
                    if (opt == '')
                    {
                        alert(data);
                    }
                        //location.reload(true);

                    }
                });

        }

        function verifyThis(device_product_id, opt)
        {
            opt = (typeof opt === 'undefined') ? '' : opt;

            $.ajax({
                url: "devices_new.php",
                type: "POST",
                data: {device_product_id: device_product_id, action: 'ajax', type: 'verify'},
                success: function (data) {
                    if (opt == '')
                    {
                        alert(data);
                        location.reload();
                    }


                }
            });

        }
        function toggleCheck(obj)
        {
            $('.checkboxes').prop('checked', obj.checked);
            traverseCheckboxes();
        }
        function traverseCheckboxes()
        {
            var Val = '';
            $('.checkboxes').each(function (index, element) {
                $(element).parent().parent().removeClass('highlight');
                if ($(element).is(":checked"))
                {
                    Val += $(element).val() + ',';
                    $(element).parent().parent().addClass('highlight');

                }
            });
            $('#selected_items').val(Val);
        }
        function mapSelected()
        {
            var $items = $('#selected_items').val();
            if ($items == '')
            {
                alert('Please select atleast 1 device before mapping');
                return false;

            }
            $('a#map-selected-anchor').attr('href', 'map_device.php?items=' + $items);
            $('a#map-selected-anchor').click();

        }

    </script>
    <!-- Adding Product Clasification -->      
</body>

</html>	
<script>
    function calculateWeight(type)
    {

       $lb = $('#weight_lb').val();
       $oz = $('#weight_oz').val();
       var rlb = 0.00;
       var roz = 0.00;

       if($lb=='') $lb = 0.00;
       if($oz=='') $oz = 0.00;


       if(type=='lb')
       {
           roz = parseFloat($lb * 16);
           $('#weight_oz').val(roz.toFixed(4));
       }
       else
       {
          rlb = parseFloat($oz / 16);
          $('#weight_lb').val(rlb.toFixed(4));

      }






  }

</script>