<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
page_permission("classify_product");

if (isset($_POST['add'])) {
    //echo '<pre>'; print_r($_POST); exit;
    $sku = urldecode($_POST['sku']);
    $name = urldecode($_POST['name']);
    $classes = $_POST['classification'];
    $manufacturers = $_POST['manufacturer'];
    $devices = $_POST['device'];
    $models = $_POST['model'];
    $attribs = $_POST['attrib'];

    if (!$classes) {
        echo 'Please select class';
        exit;
    }

    if (!$manufacturers) {
        echo 'Please select manufacturer';
        exit;
    }
    if (!$devices) {
        echo 'Please select device';
        exit;
    }
    if (!$models) {
        echo 'Please select models';
        exit;
    }
    if (!$attribs) {
        echo 'Please select attributes';
        exit;
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

    $log = 'Products classification updated';
    actionLog($log);
    echo 'Record Saved';
    exit;
}
if (isset($_POST['action']) && $_POST['action'] == 'ajax') {
    if ($_POST['type'] == 'device') {
        $i = $_POST['i'];
        $manufacturers = implode(",", $_POST['manufacturers']);
        if (empty($manufacturers))
            exit;

        $device_ids = array();
        if (isset($_POST['device_ids'])) {
            $did = $_POST['device_ids'];
            $device_ids = explode(",", $did);
        } else {
            $prod_id = $_POST['product_id'];
            foreach ($_POST['manufacturers'] as $_manu) {

                $_recs = $db->func_query("SELECT DISTINCT b.device_id FROM inv_device_manufacturer a,inv_device_device b WHERE a.`device_manufacturer_id`=b.`device_manufacturer_id` AND a.`manufacturer_id`='" . $_manu . "' AND a.`device_product_id`='" . (int) $prod_id . "'");
                foreach ($_recs as $_rec) {

                    $device_ids[] = $_rec['device_id'];
                }
            }
        }


        $rows = $db->func_query("SELECT * FROM inv_model_mt WHERE manufacturer_id IN (" . $manufacturers . ") ORDER BY device");
        echo '<select name="device[]" id="device' . $i . '" multiple="multiple" onchange="populateModel(' . $i . ')" class="multiple">	';
        foreach ($rows as $row) {
            echo '<option value="' . $row['model_id'] . '-' . $row['manufacturer_id'] . '" ' . (in_array($row['model_id'], $device_ids) ? 'selected' : '') . '>' . $row['device'] . '</option>';
        }
        echo '</select>

        <input type="hidden" id="xdevice_id' . $i . '" value="' . implode(",", $device_ids) . '">
        ';
    }


    if ($_POST['type'] == 'model') {
        $i = $_POST['i'];
        $models = $_POST['models'];
        if (empty($models))
            exit;


        $model_ids = array();
        if (isset($_POST['model_ids'])) {
            $mid = $_POST['model_ids'];
            $model_ids = explode(",", $mid);
        } else {
            $xdevice_id = $_POST['device_ids'];
            $manufacturer_ids = $_POST['manufacturer_ids'];
            $product_id = $_POST['product_id'];
            foreach ($manufacturer_ids as $_x) {

                foreach (explode(",", $xdevice_id) as $_xx) {

                    $_recs = $db->func_query("SELECT
                        distinct mo.model_id
                        FROM
                        `inv_device_manufacturer` m
                        INNER JOIN `inv_device_device` d
                        ON (m.`device_manufacturer_id` = d.`device_manufacturer_id`)
                        INNER JOIN `inv_device_model` mo
                        ON (d.`device_device_id` = mo.`device_device_id`)

                        WHERE m.manufacturer_id='" . $_x . "' AND m.device_product_id='" . $product_id . "' AND d.device_id='" . $_xx . "'

                        ");
                    foreach ($_recs as $_rec) {

                        $model_ids[] = $_rec['model_id'];
                    }
                }
            }
        }

        echo '<select name="model[]" id="model' . $i . '" multiple="multiple" class="multiple" >	';
        foreach ($models as $model) {
            $xmodel = explode("-", $model);
            $rows = $db->func_query("SELECT
                mc.`id`,d.`sub_model`,d.`model_id`,d.`sub_model_id`,mc.`carrier_id`,c.`name`
                FROM
                `inv_model_dt` d
                LEFT JOIN `inv_model_carrier` mc
                ON (d.`sub_model_id` = mc.`sub_model_id`)
                LEFT JOIN `inv_carrier` c
                ON (mc.`carrier_id` = c.`id`)

                WHERE d.model_id =" . $xmodel[0] . "
                ");
            foreach ($rows as $row) {

                echo '<option value="' . $row['id'] . '-' . $row['model_id'] . '" ' . (in_array($row['id'], $model_ids) ? 'selected' : '') . '>' . $row['sub_model'] . ' (' . $row['name'] . ')' . '</option>';
            }
        }
        echo '</select>
        <input type="hidden" id="model_ids' . $i . '" value="' . implode(",", $model_ids) . '">
        ';
    }

    if ($_POST['type'] == 'attribs') {
        $classification_type = $_POST['classification_type'];

        $i = $_POST['i'];
        $text_fields = explode(",", $_POST['text_fields']);
        if (!$classification_type) {
            echo 'Please Select Class';
            exit;
        }
        $attrib_groups = $db->func_query_first("SELECT attribute_group_id FROM inv_classification WHERE id=" . (int) $classification_type);
        if ($attrib_groups['attribute_group_id'] == '') {
            echo 'No Attribute Defined for this Class';
        } else {
            $attribs = $_POST['attribs'];
            $attribs = explode(",", $attribs);

            $attrib_groups = rtrim($attrib_groups['attribute_group_id'], ",");
            $attrib_groups = explode(",", $attrib_groups);

            echo '<input type="checkbox" name="attrib[]" onclick="noAttr(this);" value="0" ' . (in_array(0, $attribs) ? 'checked' : '') . '> No Attribute <input type="text" name="text_value[]" value="" style="display:none"> <br />';
            foreach ($attrib_groups as $attrib_group) {
                $group_info = $db->func_query_first("SELECT name FROM inv_attribute_group WHERE id='" . $attrib_group . "'");
                $rows = $db->func_query("SELECT * FROM inv_attr WHERE attribute_group_id='" . (int) $attrib_group . "'");

                echo '<strong>' . $group_info['name'] . '</strong><br />';
                $c = 1;
                $c2 = 0;
                foreach ($rows as $row) {




                    if (in_array($row['id'], $attribs)) {
                        $checked = 'checked';
                        $c2++;
                    } else {
                        $checked = '';
                    }

                    echo '<input type="checkbox" name="attrib[]" value="' . $row['id'] . '" ' . $checked . '> ' . $row['name'] . ($row['is_text'] == 1 ? '<input type="text" name="text_value[]" value="' . $text_fields[$c2] . '">' : '<input type="text" name="text_value[]" value="' . $text_fields[$c2 - 1] . '" style="display:none">') . '<br />';
                    $c++;
                }
            }
        }
    }

    if ($_POST['type'] == 'verify') {
        $device_product_id = $_POST['device_product_id'];
        $db->db_exec("UPDATE inv_device_product SET verified='1',verified_by='" . $_SESSION['login_as'] . "' WHERE device_product_id='" . (int) $device_product_id . "'");
        $log = 'Products Verifeid from classification';
        actionLog($log);
        echo "Updated";
    }
    exit;
}

if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
}
if ($page < 1) {
    $page = 1;
}
$parameter = array();

$max_page_links = 10;
if ($_GET['no_rows']) {
    $parameter[] = "no_rows=" . $_REQUEST['no_rows'];
    $num_rows = (int) $_GET['no_rows'];
} else {
    $num_rows = 50;
}
$start = ($page - 1) * $num_rows;
$where = '';

if (isset($_REQUEST['search'])) {

    if (isset($_REQUEST['sku_group']) and $_REQUEST['sku_group'] != '') {

        $where = " AND LEFT(a.sku,7)='" . $db->func_escape_string($_REQUEST['sku_group']) . "' ";
        $parameter[] = "search=" . $_REQUEST['search'];
        $parameter[] = "sku_group=" . $_REQUEST['sku_group'];
    }

    if (isset($_REQUEST['query']) and $_REQUEST['query'] != '') {
        $string_part = explode(" ", strtolower($db->func_escape_string(trim($_REQUEST['query']))));

        $part_query = '';
        foreach ($string_part as $part) {
            $part_query.="   LOWER(b.name) LIKE '%" . $part . "%' AND ";
        }
        $part_query = rtrim($part_query, 'AND ');
        $where .=" and ( $part_query )";
        $parameter[] = "query=" . $_REQUEST['query'];


        $num_rows = 1000;
    }

    switch ($_REQUEST['show_devices']) {
        case 'all':
        $where.='  ';
        break;
        case 'incomplete':
        $where.='  AND a.sku NOT IN  (SELECT DISTINCT    sku   FROM    inv_device_product)  ';
        break;
        case 'nonverified':
        $where.='  AND c.verified=0';
        break;
        case 'verified':
        $where.='  AND c.verified=1';
        break;
        default:
        $where.='  ';
        break;
    }
}


$fproduct_id = (int)$_GET['frame'];
$fproduct = (int)$_GET['product_id'];
$frame = '';
if ($fproduct_id && $fproduct) {
    $frame = " AND a.`product_id` = '$fproduct' ";
}
$product_infos = "SELECT
a.`product_id`,a.`sku`,a.`image`, a.classification_id as class ,b.name as title
FROM
`oc_product` a
INNER JOIN `oc_product_description` b
ON (a.`product_id` = b.`product_id`)
LEFT JOIN inv_device_product c
on( a.sku = c.sku)

WHERE a.status=1 and a.is_main_sku=1 $frame and main_sku='' $where ORDER BY a.sku";

//echo $product_infos;exit;

$splitPage = new splitPageResults($db, $product_infos, $num_rows, "devices_new.php", $page);

$product_infos = $db->func_query($splitPage->sql_query);

$manufacturers = $db->func_query("select * from inv_manufacturer WHERE status=1");
$classification = $db->func_query("SELECT * FROM inv_classification WHERE status=1");
$sku_types = $db->func_query("SELECT * from inv_product_skus");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title><?php echo $title; ?></title>

    <script type="text/javascript" src="js/jquery.min.js"></script>
    <!-- Add Multi Select here to make it work on Server -->
    
    <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

    <style>
        .multiple{
            height:300px	
        }
        #xcontent2{width: 100%;
         height: 100%;
         top: 0px;
         left: 0px;
         position: fixed;
         display: block;
         opacity: 0.8;
         background-color: #000;
         z-index: 99;}

     </style>        
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
</head>
<body>
    <div id="xcontent2" style="display:none"><div style="color:#fff;
      top:40%;
      position:fixed;
      left:40%;
      font-weight:bold;font-size:25px"><img src="images/loader_white.gif" /><span style="margin-left: 11%;
      margin-top: 33%;
      position: absolute;

      width: 201px;">Please wait...</span></div></div>
      <div align="center">
        <div align="center" <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>
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

            <form id="myForm" action="" method="post">

                <table border="1" width="98%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;" id="table1">
                    <tr <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>
                        <td colspan="4">
                            Show: 
                            <select name="show_devices">
                                <option value="all" <?php echo ($_REQUEST['show_devices'] == 'all' ? 'selected' : ''); ?>>Show All</option>
                                <option value="incomplete" <?php echo ($_REQUEST['show_devices'] == 'incomplete' ? 'selected' : ''); ?>>Incomplete</option>
                                <option value="nonverified" <?php echo ($_REQUEST['show_devices'] == 'nonverified' ? 'selected' : ''); ?>>Non Verified</option>
                                <option value="verified" <?php echo ($_REQUEST['show_devices'] == 'verified' ? 'selected' : ''); ?>>Verified</option>
                            </select>
                        </td>
                        <td colspan="5" align="" style="border:none">
                            <form id="search_frm" action="" method="get" >
                                <div style="float:left;">Search: <input type="text" name="query" value="<?php echo $_REQUEST['query']; ?>" /></div>
                                <div style="float:right">
                                    SKU Group: 
                                    <select name="sku_group" >
                                        <option value="">Please Select</option>
                                        <?php
                                        $recs = $db->func_query("SELECT * FROM inv_product_skus ORDER BY sku");
                                        foreach ($recs as $rec) {
                                            ?>
                                            <option value="<?php echo $rec['sku']; ?>" <?php echo ($rec['sku'] == $_REQUEST['sku_group'] ? 'selected' : ''); ?>><?= $rec['sku']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <input type="submit" name="search" value="Submit" class="button" style="" />  <a href="devices_new_settings.php"  class="fancybox3 fancybox.iframe button" style="">Settings</a>
                                </div>
                            </form> 
                        </td>

                    </tr>
                    <tr <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>><td colspan="11" style="text-align:center"><a href="<?php echo $_SERVER['SCRIPT_NAME'];?>?no_rows=10">10</a> | <a href="<?php echo $_SERVER['SCRIPT_NAME'];?>?no_rows=20">20</a> | <a href="<?php echo $_SERVER['SCRIPT_NAME'];?>?no_rows=30">30</a> | <a href="<?php echo $_SERVER['SCRIPT_NAME'];?>?no_rows=40">40</a> | <a href="<?php echo $_SERVER['SCRIPT_NAME'];?>">50</a></td></tr>
                    <tr <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>
                        <td colspan="11" style="text-align:center"><input type="button" value="Update Selected" onclick="updateSelected();"  /> <input type="button" value="Verify Selected" onclick="verifySelected();"  /></td>
                    </tr>

                    <tr style="background-color:#e7e7e7;font-weight:bold">
                        <td width="1" style="text-align:center; <?php echo ($fproduct_id)? ' display: none;': ''; ?>"><input type="checkbox" onclick="toggleCheck(this)" /></td>
                        <td <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>Image</td>
                        <td <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>SKU</td>
                        <td <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>Product</td>
                        <td>Class</td>
                        <td>Manufacturer</td>
                        <td>Device</td>
                        <td>Model / Sub Model</td>

                        <td style="display:none">SKU Type</td>
                        <td>Attributes</td>
                        <td>Completed By</td>
                        <td  align="center">Action</td>
                    </tr>

                    <?php
                    $i = 0;
                    foreach ($product_infos as $product_info) {
                        $my_sku = substr($product_info['sku'], 0, 7);
                        ?>

                        <tr id="tr_<?php echo $i; ?>" class="list_items">
                            <td style="text-align:center; <?php echo ($fproduct_id)? ' display: none;': ''; ?>"><input type="checkbox" class="checkboxes" onclick="traverseCheckboxes()" value="<?php echo $product_info['sku']; ?>" /></td>
                            <td align="center" <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>
                                <a href="http://cdn.phonepartsusa.com/image/<?php echo $product_info['image']; ?>" class="fancybox3 fancybox.iframe">
                                    <img class="lazy" src="http://cdn.phonepartsusa.com/image/<?php echo $product_info['image']; ?>" data-original="http://cdn.phonepartsusa.com/image/<?php echo $product_info['image']; ?>" height="100" width="100" alt="" />
                                </a>	
                            </td>
                            <td <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>><?php echo linkToProduct($product_info['sku'], $host_path); ?></td>
                            <td <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>
                                <input type="text" id="name<?php echo $i; ?>" value="<?php echo $product_info['title']; ?>" style="width:300px" onkeyup="$('#char_count<?php echo $i; ?>').html('Chars: ' + $(this).val().length);" />
                                <div id="char_count<?php echo $i; ?>" style="text-align:right">
                                    Chars: <?php echo strlen($product_info['title']); ?> 
                                </div>
                            </td>
                            <td>

                                <select name="classification[]" id="classification<?php echo $i; ?>" onchange="populateDevice(<?php echo $i; ?>); populateModel(<?php echo $i; ?>);">
                                    <option>Select Class</option>
                                    <?php
                                    $man_query1 = $db->func_query_first("SELECT * FROM inv_device_product WHERE sku='" . $product_info['sku'] . "'");
                                    foreach ($classification as $class) {
                                        $classSelect = $db->func_query_first("SELECT * FROM inv_device_class WHERE device_product_id='" . $man_query1['device_product_id'] . "' AND class_id='" . $class['id'] . "'");
                                        ?>
                                        <option value="<?php echo $class['id']; ?>" <?= ($classSelect) ? 'selected="selected"' : ''; ?>>
                                            <?= $class['name']; ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>

                                <select name="manufacturer[]" id="manufacturer<?php echo $i; ?>" multiple="multiple" class="multiple" onchange="populateDevice(<?php echo $i; ?>)">
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
                                <a href="javascript:void(0);" style="float:left" onclick="clearIt('<?php echo $i; ?>', 'manufacturer')">Clear </a>
                                <input type="hidden" id="product_id<?php echo $i; ?>" value="<?php echo $man_query1['device_product_id']; ?>" />
                                <input type="hidden" id="manufacturer_ids<?php echo $i; ?>" value="<?php echo $xmanu_id; ?>" />
                            </td>
                            <td>
                                <div id="div_device<?php echo $i; ?>">
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
                                <a href="javascript:void(0);" style="float:left" onclick="clearIt('<?php echo $i; ?>', 'device')">Clear </a>
                                <a href="javascript:void(0);" style="float:right" onclick="editDevice('<?php echo $i; ?>', '<?php echo $device_did2; ?>')">Edit</a>
                            </td>
                            <td>
                                <div id="div_model<?php echo $i; ?>"><?php
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
                                <a href="javascript:void(0);" style="float:left" onclick="clearIt('<?php echo $i; ?>', 'model')">Clear </a>
                                <a href="javascript:void(0);" style="float:right" onclick="editModel('<?php echo $i; ?>', '<?php echo $device_model2; ?>')">Edit</a>
                            </td>
                            <td style="display:none">
                                <div id="div_sku_type<?php echo $i; ?>" style="display:none">

                                    <select name="sku_type" id="sku_type<?php echo $i; ?>" onchange="populateAttributes(<?php echo $i; ?>)">
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
                                <div id="div_attribs<?php echo $i; ?>"><?php
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
                                <input type="hidden" id="attrib_ids<?php echo $i; ?>" value="<?php echo implode(",", $_attrib); ?>" />
                                <input type="hidden" id="attrib_fields<?php echo $i; ?>" value="<?php echo implode(",", $__attrib); ?>" />
                                <input type="hidden" id="temp_did<?php echo $i; ?>" value="<?php echo $device_did; ?>"/>
                                <input type="hidden" id="temp_model<?php echo $i; ?>" value="<?php echo $device_model2; ?>" />
                            </td>

                            <td><?= ($man_query1['added_by'] ? $man_query1['added_by'] : 'Not Mapped'); ?> (<?= ($man_query1['verified'] == 1 ? $man_query1['verified_by'] : 'Unverified'); ?>)</td>
                            <td  align="center"><input type="button" class="button" name="add" value="Update" onclick="submitThis(<?php echo $i; ?>, '<?php echo $device_did2; ?>', '<?php echo $device_model2; ?>')" />  
                                <?php
                                if ($man_query1['added_by']) {
                                    if ($man_query1['verified'] == 0 or $man_query1['verified_by'] == $_SESSION['login_as']) {
                                        ?>
                                        <input type="button" class="button" value="Verify" onclick="verifyThis(<?php echo $man_query1['device_product_id']; ?>)" />
                                        <?php
                                    }
                                    ?>
                                    <?php
                                }
                                ?>
                                <input type="hidden" id="sku<?php echo $i; ?>" value="<?php echo $product_info['sku']; ?>" />

                                <script>
                                    <?php
                                    if ($xmanu_id and $man_query1['verified'] == 0) {
                                        ?>
                                        populateDevice(<?php echo $i; ?>);
                                        <?php
                                    }
                                    ?>
                                </script>
                            </td>
                        </tr>

                        <?php $i++; ?>
                        <?php } ?>
                        <tr <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>
                            <td colspan="9" align="center">Click the button to map the Checked Devices  <input type="button" onclick="mapSelected()" value="Map Selected" /> <input type="button" value="Update Selected" onclick="updateSelected();"  /> <input type="button" value="Verify Selected" onclick="verifySelected();"  /></td>

                        </tr>
                        <tr <?php echo ($fproduct_id)? 'style="display: none;"': ''; ?>>
                            <td  align="left" colspan="3">
                                <?php echo $splitPage->display_count("Displaying %s to %s of (%s)"); ?>
                            </td>

                            <td align="center" colspan="2"  >
                                <form method="get">
                                    Page: <input type="text" name="page" value="<?php echo $page; ?>" size="3" maxlength="3" />
                                    <input type="submit" name="Go" value="Go" />
                                </form>
                            </td>

                            <td align="right" colspan="4" >
                                <?php
                                $parameter = implode("&", $parameter);
                                echo $splitPage->display_links(10, $parameter);
                                ?>
                            </td>
                        </tr>
                    </table>
                    <a href="" id="map-selected-anchor" class="fancybox3 fancybox.iframe" style="display:none"></a>
                    <input type="hidden" id="selected_items" value="" />
                </form>


            </div>

        </body>
        </html>			 		 

        <script>
            $(document).ready(function (e) {
                $('.fancybox3').fancybox({width: '90%',
                    height: 600,
                    fitToView: false,
                    autoSize: false
                });
            });
            function clearIt($i, type)
            {
                $('#' + type + $i + ' option').removeAttr('selected');
                if (type == 'manufacturer')
                {
                    populateDevice($i);
                }
                else if (type == 'device')
                {
                    populateModel($i);

                }



            }

            $(function () {

                $('#table1').multiSelect({
                    actcls: 'highlightx',
                    selector: 'tbody .list_items',
                    except: ['tbody'],
                    callback: function (items) {
                        items.find('.checkboxes').prop('checked', true);
                        traverseCheckboxes();
                    }
                });
            })
            $('.list_items').click(function (e) {
                if (
                    $(this).find('.checkboxes').prop('checked') == true)
                {

                    $(this).find('.checkboxes').prop('checked', false);
                    traverseCheckboxes();
                }
            });
            function updateSelected()
            {
                if ($('#selected_items').val() == '')
                {
                    alert('Please select a device to process');
                    return false;
                }
                var $id;
                var $i;
                if (!confirm('Are you sure want to update selected?')) {
                    return false;

                }
                showLoader();
                $('.checkboxes:checked').each(function (index, element) {
                    $id = $(this).parent().parent().attr('id');
            //alert($id);
            $i = $id.split("_");
            $i = $i[1];

            submitThis($i, $('#temp_did' + $i).val(), $('#temp_model' + $i).val(), 'yes');
        });
                alert('Updated');
                hideLoader();
                location.reload();
            }
            function verifySelected()
            {
                if ($('#selected_items').val() == '')
                {
                    alert('Please select a device to process');
                    return false;
                }
                var $id;
                var $i;
                if (!confirm('Are you sure want to verify selected?')) {
                    return false;

                }
                showLoader();
                $('.checkboxes:checked').each(function (index, element) {
                    $id = $(this).parent().parent().attr('id');
                    alert($id);
                    $i = $id.split("_");
                    $i = $i[1];

                    verifyThis($('#product_id' + i).val(), 'yes');
                });
                alert('Verified');
                hideLoader();
                location.reload();
            }



            function showLoader()
            {
                $('#xcontent2').show();

            }
            function noAttr (t) {
                if ($(t).is(':checked')) {
                    $(t).parent().find('input[type="checkbox"]').each(function() {
                        if ($(this).val() != 0) {
                            $(this).attr('disabled', 'disabled').removeAttr('checked');
                        }
                    });
                } else {
                    $(t).parent().find('input[type="checkbox"]').removeAttr('disabled');
                }
            }
            function hideLoader()
            {
                $('#xcontent2').val();

            }

        </script>
        <!-- Add Multi Select here to make it work on Local -->
        <script type="text/javascript" src="js/multiselect.js"></script>