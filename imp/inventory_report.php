<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
page_permission('inventory_report');
$xpage = 'inventory_report.php';
if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 200;
$start = ($page - 1)*$num_rows;

$keyword = @trim($_GET['keyword']);
$date = @trim($_GET['date']);
$show_all = (int)@$_GET['show_all'];

if($keyword){
	$keyword = $db->func_escape_string($keyword);
	$where = " Where Lower(pd.name) like Lower('%$keyword%') OR Lower(p.sku) like Lower('%$keyword%') ";
	$where2 = "Lower(sku) like Lower('%$keyword%')";
	$parameters[] = "keyword=$keyword";
}
else{
	$where = " Where p.sku != '' and p.sku<>'SIGN'";
	$where2 = "sku<>'' and sku<>'SIGN'";
	$parameters[] = "";
}

if($show_all){
	$show_all = $db->func_escape_string($show_all);
	$where .= " and 1=1 ";
	$where2 .= 'and 1=1';
	$parameters[] = "show_all=$show_all";
}
else{
	$where .= " and p.quantity>0";
	$where2 .= ' and qty>0';
	$parameters[] = "";
}


$dir = @$_GET['dir'];
if(!$dir || !in_array($dir,array("asc","desc"))){
	$dir = 'asc';
}

$parameters[] = "dir=$dir";

$sort = @$_GET['sort'];
if(!$sort || !in_array($sort,array("name","sku","quantity"))){
	$sort = 'sku';
}

$parameters[] = "sort=$sort";

if(in_array($sort,array("sku","quantity"))){
	$dsort = "p.$sort";
}
elseif(in_array($sort,array("name"))){
	$dsort = "pd.$sort";
}
else{
	$dsort = "p.$sort";
}
$where.=' AND p.sku NOT IN (SELECT kit_sku FROM inv_kit_skus) ';
$_query = "Select p.sku, p.weight, p.quantity, p.price ,  pd.name
		   from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
		   
           $where group by p.sku order by $dsort $dir";

if($date and $date!=date('Y-m-d'))
{
	$_query = "SELECT sku,0.00 as weight,qty as quantity, '-' as name from inv_product_stock_record where $where2 and `date`='".date('Y-m-d',strtotime($date))."' order by sku asc";
}

// echo $_query;exit;
//$splitPage = new splitPageResults($db , $_query , 100 , $xpage,$page);
$results = $db->func_query($_query);

if($dir == 'asc'){
	$ddir = 'desc';
}
else{
	$ddir = 'asc';
}

if($parameters){
	$parameters = implode("&",$parameters);
}
else{
	$parameters = '';
}
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="include/table_sorter.css" rel="stylesheet" type="text/css" />
        
        
		<script type="text/javascript" src="js/jquery.min.js"></script>
		
        
        <title>Total Inventory Report</title>
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div align="center">

	         <div class="search" align="center">
		 	<form>
		 		 Keyword: 
		 		 <input type="text" name="keyword" value="<?php echo $keyword;?>"  />
		 		 <input type="date" class="datepicker" placeholder="yyyy-mm-dd" name="date" value="<?php echo $date;?>"  />
                 <input type="checkbox" name="show_all" value="1" <?=($show_all?'checked':'');?>> Show All
                 <input type="submit" name="Go" class="button" value="Search" />

                 <a class="button" target="_blank" href="export_inventory_report_csv.php?date=<?=$_GET['date'];?>&keyword=<?=$_GET['keyword'];?>&show_all=<?=$_GET['show_all'];?>" class="button" >Export CSV</a>
		 	</form>
		</div>
		<br />

        <div align="center">
        <table cellpadding="10" cellspacing="0" width="20%" border="1">
       <tr style="font-weight:bold">
        <td>Total Qty:</td>
        <td id="total_qty"></td>
        </tr>
        <tr style="font-weight:bold">
        <td>Product Cost:</td>
        <td id="total_avg_cost"></td>
        </tr>
        <tr style="font-weight:bold">
        <td>Total Item Cost:</td>
        <td id="total_avg_total"></td>
        </tr>
         <tr style="font-weight:bold">
        <td>Avg. W1 Sale:</td>
        <td id="total_w1_cost"></td>
        </tr>
        <tr style="font-weight:bold">
        <td>Total W1 Cost Price:</td>
        <td id="total_w1_total"></td>
        </tr>
        
        </table>
        
        </div>
	        <br style="clear:both" />

	        <table border="1" cellpadding="10" cellspacing="0" width="90%" class="tablesorter">
            <thead>
        		<tr style="background:#e5e5e5;">
        			<!--<th><a href="<?php echo $xpage;?>?sort=sku&dir=<?php echo $ddir;?>&keyword=<?php echo $keyword;?>&page=1">SKU</a></th>
        			<th><a href="<?=$xpage;?>?sort=name&dir=<?php echo $ddir;?>&keyword=<?php echo $keyword;?>&page=1">Item Name</a></th>
                    <th><a href="<?=$xpage;?>?sort=quantity&dir=<?php echo $ddir;?>&keyword=<?php echo $keyword;?>&page=1">Qty</a></th>-->
                    <th width="100">SKU</th>
        			<th width="350">Item Name</th>
                    <th width="60">Qty</th>
                    <th width="130">Avg. Product Cost</th>
        			<th width="130">Total Item Cost</th>
        			<th width="130">W1 Avg. Sale</th>
                    <th width="130">Total W1 Price</th>
        	   </tr>
               </thead>
               <tbody>
		       <?php if($results):?>
		       		<?php 
					$k=0;
					$total_avg_cost = 0.00;
					$total_avg_total = 0.00;
					
					$total_w1_cost = 0.00;
					$total_w1_total = 0.00;
					
					$total_qty = 0;
					foreach($results as $i => $result):?>
                    <?php
					$avg_cost = getAvgProductCost($result['sku'], $date);
					$w1_avg = getWholeSaleAvgCost($result['sku'],1);
					
					if($result['quantity']<0) {
						$qty=0;	
					}
					else
					{
						$qty=$result['quantity'];	
					}
					
					$total_avg_cost+=$avg_cost;
					$total_w1_cost+=$w1_avg;
					
					$total_avg_total+=$qty * $avg_cost;
					$total_w1_total+=$qty * $w1_avg;
					
					if($result['name']=='-')
					{
						$result['name'] = getItemName($result['sku']);
					}
					
					
					$total_qty+=$result['quantity'];
					// $qty = ($date)? $db->func_query_first_cell("SELECT qty FROM inv_product_stock_record WHERE sku = '". $result['sku'] ."' AND date = '$date'") : $result['quantity'];
					$qty = $result['quantity'];
					?>
                    <tr>
                    <td align="center"><a href="<?=$host_path;?>product/<?=$result['sku'];?>"><?=$result['sku'];?></a></td>
                    <td align="center"><?=$result['name'];?></td>
                    <td align="center"><?= (int)$qty;?></td>
                    <td align="center">$<?=round($avg_cost,2);?></td>
                    <td align="center">$<?=round($qty * $avg_cost,2);?></td>
                    <td align="center">$<?=round($w1_avg,2);?></td>
                    <td align="center">$<?=round($qty * $w1_avg,2);?></td>
                    </tr>
                    
					<?php endforeach;?>       
		       <?php endif;?>
		       </tbody>
             
	        </table>
        </div>
    </body>
</html>
<script>
$(document).ready(function(e) {
  //  $('#total_avg_cost').html('<?='$'.number_format($total_avg_cost,2);?>');
   $('#total_avg_cost').html('<?='$'.number_format($total_avg_total/$total_qty,2);?>');
	$('#total_w1_cost').html('<?=number_format($total_w1_cost,2);?>');
	
	$('#total_qty').html('<?=number_format($total_qty,2);?>');
	
	 $('#total_avg_total').html('<?='$'.number_format($total_avg_total,2);?>');
	$('#total_w1_total').html('<?='$'.number_format($total_w1_total,2);?>');
	
	
});

</script>
 <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
         <script>
		 $(document).ready(function(e) {
             $(".tablesorter").tablesorter(); 
        });
		 </script>