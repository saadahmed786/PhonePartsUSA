<?php
require_once("auth.php");
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
if ($_GET['date_added']) {
    $date = $db->func_escape_string($_GET['date_added']);
    $date = date('Y-m-d', strtotime($date));
    $parameters = "date_added=$date";
} else {
    $date = date('Y-m-d');
    $_GET['date_added'] = $date;
    $parameters = "date_added=$date";
}

$page = (int) $_GET['page'];
if (!$page) {
    $page = 1;
}


$_query = "SELECT distinct(sku) FROM inv_product_price_scrap where DATE(date_updated) = '".$date."' and url<>'' ";


$splitPage = new splitPageResults($db, $_query, 25, "competitor_module_new.php", $page);
$rows = $db->func_query($splitPage->sql_query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Competitor Price Search</title>
        

            <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
            <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

            <script type="text/javascript">
                $(document).ready(function () {
                    $('.fancybox').fancybox({width: '450px', autoCenter: true, autoSize: true});
                    $('.fancybox2').fancybox({width: '680px', autoCenter: true, autoSize: true});

                    $(".datepicker").datepicker();
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
            <?php include_once 'inc/header.php'; ?>
        </div>

        <?php if ($_SESSION['message']): ?>
            <div align="center"><br />
                <font color="red"><?php echo $_SESSION['message'];
        unset($_SESSION['message']);
            ?><br /></font>
            </div>
        <?php else: ?>
            <br /><br /> 
<?php endif; ?>
<div align="center"><h2>Competitor Pricing Search</h2></div>
        <div align="center">
            <form action="" method="get">
                <table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                        <td>
                            Date: <?php echo createField("date_added", "date_added", "text", $_GET['date_added'], null, " class='datepicker' "); ?>                     
                        </td>
                        
                    </tr>	
                </table>

                <br />
                <input type="submit" name="search" value="Search" class="button" />
            </form>
        </div>			
        <br />
        <table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
            <thead>
                <tr>
                    <th rowspan="2"></th>
                    <th rowspan="2">SKU</th>
                    <th rowspan="2">Title</th>
                    <th rowspan="2">Price</th>
                    <th rowspan="2">Cost</th>
                    <th colspan="5">Mobile Sentrix</th>
                    <th colspan="5">Fixez</th>
                    <th colspan="5">Mengtor</th>
                    <th colspan="5">Mobile Defenders</th>
                    <th colspan="5">E-Trade Supply</th>
                    <th colspan="5">Maya Cellular</th>
                    <th colspan="5">LCD Loop</th>
                    <th colspan="5">Parts 4 Cells</th>
                    <th colspan="5">Cell Parts Hub</th>
                </tr>
                <tr>
                    <th>Previous Price</th>
                    <th>Current Price</th>
                    <th>% Change</th>
                    <th>In Stock</th>
                    <th>Date Updated</th>
                    
                    <th>Previous Price</th>
                    <th>Current Price</th>
                    <th>% Change</th>
                    <th>In Stock</th>
                    <th>Date Updated</th>
                    
                    <th>Previous Price</th>
                    <th>Current Price</th>
                    <th>% Change</th>
                    <th>In Stock</th>
                    <th>Date Updated</th>

                    <th>Previous Price</th>
                    <th>Current Price</th>
                    <th>% Change</th>
                    <th>In Stock</th>
                    <th>Date Updated</th>

                    <th>Previous Price</th>
                    <th>Current Price</th>
                    <th>% Change</th>
                    <th>In Stock</th>
                    <th>Date Updated</th>

                    <th>Previous Price</th>
                    <th>Current Price</th>
                    <th>% Change</th>
                    <th>In Stock</th>
                    <th>Date Updated</th>

                    <th>Previous Price</th>
                    <th>Current Price</th>
                    <th>% Change</th>
                    <th>In Stock</th>
                    <th>Date Updated</th>

                    <th>Previous Price</th>
                    <th>Current Price</th>
                    <th>% Change</th>
                    <th>In Stock</th>
                    <th>Date Updated</th>

                    <th>Previous Price</th>
                    <th>Current Price</th>
                    <th>% Change</th>
                    <th>In Stock</th>
                    <th>Date Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php $scrapping_sites = array('mobile_sentrix', 'fixez', 'mengtor', 'mobile_defenders','etrade_supply','maya_cellular','lcd_loop','parts_4_cells','cell_parts_hub'); ?>
                <?php foreach ($rows as $i => $product) { ?>
                <?php $name = $db->func_query_first_cell('SELECT opd.name FROM oc_product op inner join oc_product_description opd on op.product_id = opd.product_id WHERE sku = "'. $product['sku'] .'"'); ?>
                <?php $price = $db->func_query_first_cell('SELECT opd.price FROM oc_product op inner join oc_product_discount opd on op.product_id = opd.product_id WHERE customer_group_id = "1633" AND opd.quantity = "1" AND sku = "'. $product['sku'] .'"'); ?>
                <tr>
                    <td><input class="selection checkboxes" type="checkbox" name="delete_sku[<?php echo $product['sku'];?>]" value="<?php echo $product['sku'];?>" /></td>
                    <td><?php echo linkToProduct($product['sku'],$host_path); ?></td>
                    <td><?php echo $name; ?></td>
                    <td><?php echo '$'.number_format($price,2); ?></td>
                    <td><?php echo '$'.number_format(getTrueCost($product['sku']),2); ?></td>
                    <?php 
                    $iterator = 1;
                    foreach ($scrapping_sites as $site) { ?>
                    <?php $price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $product['sku'] . "' AND type = '$site' order by added DESC limit 1"); ?>
                    <?php $change = number_format($price['price'] / $price['old_price'] * 100, 2); ?>
                    <?php if ($change < 100.00 && $change > 0.00) {
                        $change = '-' . (100 - $change).'%';
                    } else if ($change == 0.00) {
                        $change = (100 - $change).'%';
                    } else {
                        $change = '+' . ($change - 100).'%';
                    }
                    if($price['old_price']==0.00)
                    {
                        $change = 'N/A';
                    }
                    $scrap1=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="mobile_sentrix" AND sku = "'. $product['sku'] .'"');
                    $scrap2=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="fixez" AND sku = "'. $product['sku'] .'"');
                    $scrap3=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="mengtor" AND sku = "'. $product['sku'] .'"');
                    $scrap4=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="mobile_defenders" AND sku = "'. $product['sku'] .'"');
                    $scrap5=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="etrade_supply" AND sku = "'. $product['sku'] .'"');
                    $scrap6=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="maya_cellular" AND sku = "'. $product['sku'] .'"');
                    $scrap7=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="lcd_loop" AND sku = "'. $product['sku'] .'"');
                    $scrap8=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="parts_4_cells" AND sku = "'. $product['sku'] .'"');
                    $scrap9=$db->func_query_first_cell('SELECT url from inv_product_price_scrap where url<>"" and type="cell_parts_hub" AND sku = "'. $product['sku'] .'"');

                    $date1=$db->func_query_first_cell('SELECT date_updated from inv_product_price_scrap where type="mobile_sentrix" AND sku = "'. $product['sku'] .'"');
                    $date2=$db->func_query_first_cell('SELECT date_updated from inv_product_price_scrap where type="fixez" AND sku = "'. $product['sku'] .'"');
                    $date3=$db->func_query_first_cell('SELECT date_updated from inv_product_price_scrap where type="mengtor" AND sku = "'. $product['sku'] .'"');
                    $date4=$db->func_query_first_cell('SELECT date_updated from inv_product_price_scrap where type="mobile_defenders" AND sku = "'. $product['sku'] .'"');
                    $date5=$db->func_query_first_cell('SELECT date_updated from inv_product_price_scrap where type="etrade_supply" AND sku = "'. $product['sku'] .'"');
                    $date6=$db->func_query_first_cell('SELECT date_updated from inv_product_price_scrap where type="maya_cellular" AND sku = "'. $product['sku'] .'"');
                    $date7=$db->func_query_first_cell('SELECT date_updated from inv_product_price_scrap where type="lcd_loop" AND sku = "'. $product['sku'] .'"');
                    $date8=$db->func_query_first_cell('SELECT date_updated from inv_product_price_scrap where type="parts_4_cells" AND sku = "'. $product['sku'] .'"');
                    $date9=$db->func_query_first_cell('SELECT date_updated from inv_product_price_scrap where type="cell_parts_hub" AND sku = "'. $product['sku'] .'"');
                    ?>
                    <td>$<?php echo number_format($price['old_price'], 2); ?></td>
                    <td>$<?php echo number_format($price['price'], 2); ?></td>
                    <td><?php echo $change; ?></td>
                    <td>
                    <?php
                    if(${"scrap".$iterator}!='')
                    {


                    ?>
                    <a href="<?php echo ${"scrap".$iterator};?>" target="blank">
                    
                    <?php echo ($price['out_of_stock'])? 'No': 'Yes'; ?>

                </a>
                    <?php
                }
                else
                {
                    echo '-';
                }
                ?>
                    </td>
                    <td>
                        <?php echo americanDate(${"date".$iterator});?>
                    </td>
                    <?php 
                    $iterator++;
                    } ?>
                </tr>
                <?php } ?>
                
            </tbody>

        </table>

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
    </body>
</html>            			   