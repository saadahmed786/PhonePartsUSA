<?php
include_once 'auth.php';
include_once 'inc/functions.php';
page_permission('purchasing_metrics');
if(!isset($_GET['hide_header']))
{
  unset($_SESSION['hide_header']);
}
function getFilterHtml($data_filter,$data_start,$data_end)
{
	return '<select id="filter_date" class="filter_date">
  
  <option value="this week" '.($data_filter=='this week'?'selected':'').'>This Week</option>
  <option value="this month" '.($data_filter=='this month'?'selected':'').'>This Month</option>
  <option value="previous week" '.($data_filter=='previous week'?'selected':'').'>Last Week</option>
  <option value="previous month" '.($data_filter=='previous month'?'selected':'').'>Last Month</option>
  
  <option value="this quarter" '.($data_filter=='this quarter'?'selected':'').'>This Quarter</option>
  <option value="previous quarter" '.($data_filter=='previous quarter'?'selected':'').'>Last Quarter</option>
  <option value="-7 days" '.($data_filter=='-7 days'?'selected':'').' >Last 7 Days</option>
  <option value="-30 days" '.($data_filter=='-30 days'?'selected':'').'>Last 30 Days</option>
  <option value="custom" '.($data_filter=='custom'?'selected':'').'>Custom</option>
  </select>
  <div id="custom_date_div" '.($data_filter!='custom'?'style="display:none"':'').'>
  <br>
  <strong>Date From:</strong><input type="date" class="custom_date_start" value="'.$data_start.'"> to <input type="date" class="custom_date_end" value="'.$data_end.'"> </strong>

  </div>';
}

function getDateRange($filter_date,$custom_date_start,$custom_date_end)
{

if($filter_date=='this week')
{
  $_date1 = date('Y-m-d',strtotime('previous sunday'));
  $_date2 = date('Y-m-d');
  
}

if($filter_date=='previous week')
{
  $_date1 =  date('Y-m-d',strtotime('-2 weeks sunday'));;
  $_date2 =  date('Y-m-d',strtotime('previous saturday'));;
  
  
}

if($filter_date=='this month')
{
  $_date1 =  date('Y-m-01');
  $_date2 =  date('Y-m-d');
  
}

if($filter_date=='previous month')
{
  $_date1 =  date('Y-m-01',strtotime('previous month'));
  $_date2 =  date('Y-m-t',strtotime('previous month'));
  
}

if($filter_date=='this quarter')
{

  $curMonth = date("m", time());
  $curQuarter = ceil($curMonth/3);
  $quarter_start_month = (($curQuarter*3)+1)-3;
  $quarter_end_month = date('m');

  $_date1=date('Y-'.$quarter_start_month.'-01');
  $_date2=date('Y-'.$quarter_end_month.'-d');

}

if($filter_date=='previous quarter')
{

  $curMonth = date("m", time());
  $curQuarter = ceil($curMonth/3);
  $curQuarter--;
  if($curQuarter==0)
  {
    $curQuarter = 4;
    $curYear = date('Y',strtotime('previous year'));
  }
  else
  {
    $curYear = date('Y');
  }
  $quarter_start_month = ($curQuarter*4)-(4+1);
  $quarter_end_month = ($curQuarter*3);

  $_date1=date($curYear.'-'.$quarter_start_month.'-01');
  $_date2=date($curYear.'-'.$quarter_end_month.'-t');

}

if($filter_date=='-7 days')
{
  $_date1 = date('Y-m-d',strtotime('-7 day'));
  $_date2 = date('Y-m-d');
}

if($filter_date=='-30 days')
{
  $_date1 = date('Y-m-d',strtotime('-30 day'));
  $_date2 = date('Y-m-d');
}


if($filter_date=='custom')
{
  $_date1 = date('Y-m-d',strtotime($custom_date_start));
  $_date2 = date('Y-m-d',strtotime($custom_date_end));
}
return array('date1'=>$_date1,'date2'=>$_date2);
}




if(isset($_POST['type']) && $_POST['type']=='items_below_rop')
{
  // echo getGenericQuery(20000,30000);exit;

$result2 = $cache->get('purchasing_metrics.items_below_rop');
if(!$result2)
{
   $result2 = $db->func_query("SELECT p.tier,p.product_id,p.model,(select name from oc_product_description d where p.product_id=d.product_id) as product_name,p.quantity,p.mps,(p.mps*(select lead_time+safety_stock from inv_reorder_settings )) as rop,((p.mps * (select lead_time from inv_reorder_settings )) + ((p.mps*(select lead_time+safety_stock from inv_reorder_settings ))-p.quantity)) as qty_to_be_shipped  FROM oc_product p where Lower(p.model) not like Lower('BKB-MOD-%') and Lower(model) not like Lower('lbb-%') AND p.is_main_sku=1  and p.status = 1 and p.discontinue = 0 and p.is_blowout=0 and p.is_kit = 0 group by p.sku HAVING p.quantity<rop order by p.tier ");
  $cache->set('purchasing_metrics.items_below_rop',$result2);

}
// testObject($result2);

  $html = '
  <strong>Items below ROP <a href="'.$host_path.'reorder_settings.php" class="fancybox2 fancybox.iframe button">Re-Order Point</a></strong>
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Tier</th>
                          <th>SKU</th>
                          <th>Name</th>
                          <th>Stock</th>
                          <th>ROP</th>
                          <th>NTO</th>

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  foreach($result2 as $result)
                  {
                    $html.='<tr>
                    <td>'.$result['tier'].'</td>
                    <td>'.linkToProduct($result['model'],$host_path).'</td>
                    <td>'.$result['product_name'].'</td>
                    <td>'.$result['quantity'].'</td>
                    <td>'.ceil($result['rop']).'</td>
                    <td>'.ceil($result['qty_to_be_shipped']).'</td>
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}


if(isset($_POST['type']) && $_POST['type']=='lost_sales_oos')
{
  // echo getGenericQuery(20000,30000);exit;
if(!isset($_POST['filter_date']))
{
  $_POST['filter_date'] ='-7 days';
}
$_date = getDateRange($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']);


$build_query = md5(http_build_query($_POST));
$result2 = $cache->get('purchasing_metrics.lost_sales_oos.'.$build_query);
if(!$result2)
{
   $result2 = $db->func_query("SELECT p.price,p.sale_price, p.tier,p.product_id,p.model,(select name from oc_product_description d where p.product_id=d.product_id) as product_name,p.quantity,p.mps,b.outstock_date,b.instock_date from oc_product p,inv_product_inout_stocks b where lower(p.model)=lower(b.product_sku) and Lower(p.model) not like Lower('BKB-MOD-%') and Lower(model) not like Lower('lbb-%') AND p.is_main_sku=1 and p.status = 1 and p.discontinue = 0  and p.is_blowout=0 and p.is_kit = 0 and date(b.outstock_date) between '".$_date['date1']."' and '".$_date['date2']."' group by p.sku order by p.mps desc");
  $cache->set('purchasing_metrics.lost_sales_oos.'.$build_query,$result2);

}


// testObject($result2);

  $html = '
  <strong>Sale Lost from Inventory Outages</strong>'.getFilterHtml($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']).'
  
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Tier</th>
                          <th>SKU</th>
                          <th>Name</th>
                          <th>Qty Lost</th>
                          <th>Lost</th>
                          

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  foreach($result2 as $result)
                  {
                    $outstock_date = $result['outstock_date'];
                    $instock_date = $result['instock_date'];
                    if($instock_date=='0000-00-00 00:00:00')
                    {
                      $instock_date = time();
                    }
                    else
                    {
                      $instock_date = strtotime($instock_date);
                    }
                    // $now = time(); // or your date as well
$your_date = strtotime(date('Y-m-d',strtotime($outstock_date)));
$datediff = $instock_date - $your_date;

$day_outofstock =  round($datediff / (60 * 60 * 24));
$missing_qty = $day_outofstock * $result['mps'];


$loss = $missing_qty * $result['price'];


                    $html.='<tr>
                    <td>'.$result['tier'].'</td>
                    <td>'.linkToProduct($result['model'],$host_path).'</td>
                    <td>'.utf8_encode($result['product_name']).'</td>
                    <td>'.ceil($missing_qty).'</td>
                    
                    <td>$'.number_format($loss,2).'</td>
                    
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}
if(isset($_POST['type']) && $_POST['type']=='parts_with_return_ratio')
{
  // echo getGenericQuery(20000,30000);exit;
	if(!isset($_POST['filter_date']))
{
	$_POST['filter_date'] ='-7 days';
}
$_date = getDateRange($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']);


$build_query = md5(http_build_query($_POST));
$result2='';
$result2 = $cache->get('purchasing_metrics.parts_with_return_ratio.'.$build_query);
if(!$result2)
{
 
   $result2 = $db->func_query("select b.product_sku as sku, sum(b.product_qty) as sold_qty,sum(b.product_price) as sold_price,sum(b.product_true_cost*b.product_qty) as sold_cost,(select count(d.sku) from inv_return_items d,inv_returns c where c.id=d.return_id and b.product_sku=d.sku and d.item_condition in ('Item Issue','Item Issue - RTV','Not Tested','Over 60 Days') and date(c.date_qc) BETWEEN '".$_date['date1']."' AND '".$_date['date2']."') as total_return from inv_orders a,inv_orders_items b where a.order_id=b.order_id and lower(a.order_status) in ('shipped','processed','completed') and date(a.order_date) BETWEEN '".$_date['date1']."' AND '".$_date['date2']."' group by b.product_sku order by sum(b.product_price) desc limit 100");
  $cache->set('purchasing_metrics.parts_with_return_ratio.'.$build_query,$result2);

}
// testObject($result2);


  $html = '
  <strong>Top 100 Selling Items</strong>'.getFilterHtml($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']).'
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>SKU</th>
                          <th>Name</th>
                          <th>Return Rate</th>
                          <th>QTY Sold</th>
                          <th>Sold Price</th>
                          <th>Cost</th>
                          <th>Profit</th>
                          <th>Profit%</th>

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  $total_return = 0;
                  $total_sold = 0;
                  $total_sold_price = 0;
                  $total_sold_cost = 0;
                  $total_profit = 0;

                  foreach($result2 as $result)
                  {
                  	$total_return+=$result['total_return'];
                  	$total_sold+=$result['sold_qty'];
                  	$total_sold_price+=$result['sold_price'];
                  	$total_sold_cost+=$result['sold_cost'];
                  	$total_profit+=($result['sold_price']-$result['sold_cost']);
                    $html.='<tr>
                    
                    <td>'.linkToProduct($result['sku'],$host_path).'</td>
                    <td>'.getItemName($result['sku']).'</td>
                    <td>'.number_format(($result['total_return']/$result['sold_qty'])*100,2).'%</td>
                    <td>'.(int)$result['sold_qty'].'</td>
                    <td>$'.number_format($result['sold_price'],2).'</td>
                    <td>$'.number_format($result['sold_cost'],2).'</td>
                    <td>$'.number_format($result['sold_price']-$result['sold_cost'],2).'</td>
                    <td>'.number_format((($result['sold_price']-$result['sold_cost'])/$result['sold_cost'])*100,2).'%</td>
                    </tr>
                    ';
                  }
                  $html.='<tr style="font-weight:bold">

                  <td colspan="3">Total:</td>
                  <td>'.$total_sold.'</td>
                  <td>$'.number_format($total_sold_price,2).'</td>
                  <td>$'.number_format($total_sold_cost,2).'</td>
                  <td>$'.number_format($total_profit,2).'</td>
                  <td>'.number_format((($total_sold_price-$total_sold_cost)/$total_sold_cost)*100,2).'%</td>
                  
                  </tr>';
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}

if(isset($_POST['type']) && $_POST['type']=='parts_with_return_ratio2')
{
  // echo getGenericQuery(20000,30000);exit;
  if(!isset($_POST['filter_date']))
{
  $_POST['filter_date'] ='-7 days';
}
$_date = getDateRange($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']);


$build_query = md5(http_build_query($_POST));
$result2='';
$result2 = $cache->get('purchasing_metrics.parts_with_return_ratio2.'.$build_query);
if(!$result2)
{

   $result2 = $db->func_query("select b.sku,count(b.sku) as total_return,a.date_qc,
    (select sum(ii.product_qty) from inv_orders_items ii,inv_orders o where ii.order_id=o.order_id and ii.product_sku=b.sku and lower(o.order_status) in ('shipped','processed','completed') and date(o.order_date) BETWEEN '".$_date['date1']."' AND '".$_date['date2']."') as sold_qty,
    (select sum(ii.product_price) from inv_orders_items ii,inv_orders o where ii.order_id=o.order_id and ii.product_sku=b.sku and lower(o.order_status) in ('shipped','processed','completed') and date(o.order_date) BETWEEN '".$_date['date1']."' AND '".$_date['date2']."') as sold_price,

    (select sum(ii.product_true_cost*ii.product_qty) from inv_orders_items ii,inv_orders o where ii.order_id=o.order_id and ii.product_sku=b.sku and lower(o.order_status) in ('shipped','processed','completed') and date(o.order_date) BETWEEN '".$_date['date1']."' AND '".$_date['date2']."') as sold_cost


    from inv_return_items b,inv_returns a where b.sku<>'' and a.id=b.return_id and b.item_condition in ('Item Issue','Item Issue - RTV','Not Tested','Over 60 Days') and date(a.date_qc) BETWEEN '".$_date['date1']."' AND '".$_date['date2']."' GROUP BY b.sku having ((count(b.sku)/(select sum(ii.product_qty) from inv_orders_items ii,inv_orders o where ii.order_id=o.order_id and ii.product_sku=b.sku and lower(o.order_status) in ('shipped','processed','completed') and date(o.order_date) BETWEEN '".$_date['date1']."' AND '".$_date['date2']."'))*100)>5 order by 2,4 desc limit 100");
  $cache->set('purchasing_metrics.parts_with_return_ratio2.'.$build_query,$result2);

}
// testObject($result2);


  $html = '
  <strong>Parts >5% Return Ratio</strong>'.getFilterHtml($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']).'
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>SKU</th>
                          <th>Name</th>
                          <th>Return Rate</th>
                          <th>QTY Sold</th>
                          <th>Sold Price</th>
                          <th>Cost</th>
                          <th>Profit</th>
                          <th>Profit%</th>

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  $total_return = 0;
                  $total_sold = 0;
                  $total_sold_price = 0;
                  $total_sold_cost = 0;
                  $total_profit = 0;

                  foreach($result2 as $result)
                  {
                    $total_return+=$result['total_return'];
                    $total_sold+=$result['sold_qty'];
                    $total_sold_price+=$result['sold_price'];
                    $total_sold_cost+=$result['sold_cost'];
                    $total_profit+=($result['sold_price']-$result['sold_cost']);
                    $html.='<tr>
                    
                    <td>'.linkToProduct($result['sku'],$host_path).'</td>
                    <td>'.getItemName($result['sku']).'</td>
                    <td>'.number_format(($result['total_return']/$result['sold_qty'])*100,2).'%</td>
                    <td>'.(int)$result['sold_qty'].'</td>
                    <td>$'.number_format($result['sold_price'],2).'</td>
                    <td>$'.number_format($result['sold_cost'],2).'</td>
                    <td>$'.number_format($result['sold_price']-$result['sold_cost'],2).'</td>
                    <td>'.number_format((($result['sold_price']-$result['sold_cost'])/$result['sold_cost'])*100,2).'%</td>
                    </tr>
                    ';
                  }
                  $html.='<tr style="font-weight:bold">

                  <td colspan="3">Total:</td>
                  <td>'.$total_sold.'</td>
                  <td>$'.number_format($total_sold_price,2).'</td>
                  <td>$'.number_format($total_sold_cost,2).'</td>
                  <td>$'.number_format($total_profit,2).'</td>
                  <td>'.number_format((($total_sold_price-$total_sold_cost)/$total_sold_cost)*100,2).'%</td>
                  
                  </tr>';
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}


if(isset($_POST['type']) && $_POST['type']=='models_with_returns')
{
  

  if(!isset($_POST['filter_date']))
{
  $_POST['filter_date'] ='-7 days';
}

$_date = getDateRange($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']);


$build_query = md5(http_build_query($_POST));



$result2 = $cache->get('purchasing_metrics.models_with_returns.'.$build_query);
if(!$result2)
{
   $datas = $db->func_query("select count(b.sku) as return_qty,a.date_completed, (select concat(d.manufacturer_id,'-',e.device_id) from inv_device_product c,inv_device_manufacturer d,inv_device_device e where d.device_product_id=c.device_product_id and e.device_manufacturer_id=d.device_manufacturer_id and c.sku=b.sku limit 1 ) as device_id, (select sum(ii.product_qty) from inv_orders_items ii,inv_orders o where ii.order_id=o.order_id and ii.product_sku=b.sku and lower(o.order_status) in ('shipped','processed','completed') and date(o.order_date) between '".$_date['date1']."' and '".$_date['date2']."') as sold_qty from inv_return_items b,inv_returns a where b.sku<>'' and b.item_condition in ('Item Issue - RTV','Not Tested','Over 60 days') and a.id=b.return_id and date(a.date_completed) between '".$_date['date1']."' and '".$_date['date2']."' GROUP BY b.sku order by 1 desc");
   $result2 = array();
   foreach($datas as $data)
   {
    if(!$data['device_id']) continue;
    $result2[$data['device_id']] = array(
      'return_qty'=>$result2[$data['device_id']]['return_qty']+$data['return_qty'],
      'sold_qty'=>$result2[$data['device_id']]['sold_qty']+$data['sold_qty'],

      );
   }
  $cache->set('purchasing_metrics.models_with_returns.'.$build_query,$result2);

}
// testObject($result2);

  $html = '
  <strong>Models with Highest Return Rates</strong>'.getFilterHtml($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']).'
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>Manufacturer</th>
                          <th>Model</th>
                          <th>Parts Sold</th>
                          <th>Parts Returned</th>
                          <th>Return Rate%</th>
                          

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  foreach($result2 as $device_id=> $result)
                  {
                    $device_detail=explode("-", $device_id);
                    $manufacturer_id = $device_detail[0];
                    $device_id = $device_detail[1];
                    $manufacturer = $db->func_query_first_cell("select name from inv_manufacturer where manufacturer_id='".(int)$manufacturer_id."'");
                    $model = $db->func_query_first_cell("select device from inv_model_mt where model_id='".(int)$device_id."'");
                    $html.='<tr>
                    
                    <td>'.$manufacturer.'</td>
                    <td>'.$model.'</td>
                    <td>'.(int)$result['sold_qty'].'</td>
                    <td>'.(int)$result['return_qty'].'</td>
                    
                    
                    <td>'.number_format((($result['return_qty'])/$result['sold_qty'])*100,2).'%</td>
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}



if(isset($_POST['type']) && $_POST['type']=='profit_by_sku_type')
{

  if(!isset($_POST['filter_date']))
{
  $_POST['filter_date'] ='-7 days';
}

$_date = getDateRange($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']);


$build_query = md5(http_build_query($_POST));



  // echo getGenericQuery(20000,30000);exit;
  $rows = $db->func_query("select sku from inv_product_skus order by sku");
  
$result2 = array();
$result2 = $cache->get('purchasing_metrics.profit_by_sku_type.'.$build_query);
if(!$result2)
{
  $i=0;
  foreach($rows as $row)
  {

    $result2[$i] = $db->func_query_first("select '".$row['sku']."' as sku, sum(ii.product_qty) as sold_qty,sum(ii.product_price) as sold_price,sum(ii.product_true_cost*ii.product_qty) as sold_cost from inv_orders_items ii,inv_orders o where ii.order_id=o.order_id and LEFT(ii.product_sku,".strlen($row['sku']).")='".$row['sku']."' and lower(o.order_status) in ('shipped','processed','completed') and date(o.order_date) between '".$_date['date1']."' and '".$_date['date2']."' group by LEFT(ii.product_sku,".strlen($row['sku']).")");

    if(empty($result2[$i]))
    {
      unset($result2[$i]);
    }
    else
    {
    $i++;
      
    }
  }

  $cache->set('purchasing_metrics.profit_by_sku_type.'.$build_query,$result2);

}
// testObject($result2);

  $html = '
  <strong>Profit Margin by SKU Type</strong>'.getFilterHtml($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']).'
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>Type</th>
                          
                          <th>QTY Sold</th>
                          <th>Sold Price</th>
                          <th>Cost</th>
                          <th>Profit</th>
                          <th>Profit%</th>

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  foreach($result2 as $result)
                  {

                    $html.='<tr>
                    
                    <td>'.($result['sku']).'</td>
                    
                    <td><a href="#">'.(int)$result['sold_qty'].'</a></td>
                    <td>$'.number_format($result['sold_price'],2).'</td>
                    <td>$'.number_format($result['sold_cost'],2).'</td>
                    <td>$'.number_format($result['sold_price']-$result['sold_cost'],2).'</td>
                    <td>'.number_format((($result['sold_price']-$result['sold_cost'])/$result['sold_cost'])*100,2).'%</td>
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}

if(isset($_POST['type']) && $_POST['type']=='profit_by_part_type')
{

  if(!isset($_POST['filter_date']))
{
  $_POST['filter_date'] ='-7 days';
}

$_date = getDateRange($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']);


$build_query = md5(http_build_query($_POST));

$result2 = $cache->get('purchasing_metrics.profit_by_party_type.'.$build_query);
if(!$result2)
{
$result2 = $db->func_query("SELECT c.main_class,sum(i.product_qty) as sold_qty,sum(i.product_price) as sold_price,sum(i.product_true_cost*i.product_qty) as sold_cost from inv_classification c,oc_product p,inv_orders_items i,inv_orders o where c.id=p.classification_id and p.model=i.product_sku and i.order_id=o.order_id and lower(o.order_status) in ('processed','shipped','completed') and date(o.order_date) between '".$_date['date1']."' and '".$_date['date2']."' group by c.main_class");

  $cache->set('purchasing_metrics.profit_by_part_type.'.$build_query,$result2);

}
// testObject($result2);

  $html = '
  <strong>Profit Margin by Part Type</strong>'.getFilterHtml($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']).'
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>Type</th>
                          
                          <th>QTY Sold</th>
                          <th>Sold Price</th>
                          <th>Cost</th>
                          <th>Profit</th>
                          <th>Profit%</th>

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  foreach($result2 as $result)
                  {

                    $html.='<tr>
                    
                    <td>'.($result['main_class']).'</td>
                    
                    <td>'.(int)$result['sold_qty'].'</td>
                    <td>$'.number_format($result['sold_price'],2).'</td>
                    <td>$'.number_format($result['sold_cost'],2).'</td>
                    <td>$'.number_format($result['sold_price']-$result['sold_cost'],2).'</td>
                    <td>'.number_format((($result['sold_price']-$result['sold_cost'])/$result['sold_cost'])*100,2).'%</td>
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}


if(isset($_POST['type']) && $_POST['type']=='profit_by_vendor')
{

  if(!isset($_POST['filter_date']))
{
  $_POST['filter_date'] ='-7 days';
}

$_date = getDateRange($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']);

$build_query = md5(http_build_query($_POST));



$result2 = $cache->get('purchasing_metrics.profit_by_vendor.'.$build_query);
if(!$result2)
{
$result2 = $db->func_query("SELECT p.vendor,sum(i.product_qty) as sold_qty,sum(i.product_price) as sold_price,sum(i.product_true_cost*i.product_qty) as sold_cost from oc_product p,inv_orders_items i,inv_orders o where  p.model=i.product_sku and i.order_id=o.order_id and lower(o.order_status) in ('processed','shipped','completed') and date(o.order_date) between '".$_date['date1']."' and '".$_date['date2']."' and p.vendor<>'' group by p.vendor");

  $cache->set('purchasing_metrics.profit_by_vendor.'.$build_query,$result2);

}
// testObject($result2);

  $html = '
  <strong>Profit Margin by Vendor</strong> '.getFilterHtml($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']).'
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>Vendor</th>
                          
                          <th>QTY Sold</th>
                          <th>Sold Price</th>
                          <th>Cost</th>
                          <th>Profit</th>
                          <th>Profit%</th>

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  foreach($result2 as $result)
                  {

                    $html.='<tr>
                    
                    <td>'.$result['vendor'].'</td>
                    
                    <td>'.(int)$result['sold_qty'].'</td>
                    <td>$'.number_format($result['sold_price'],2).'</td>
                    <td>$'.number_format($result['sold_cost'],2).'</td>
                    <td>$'.number_format($result['sold_price']-$result['sold_cost'],2).'</td>
                    <td>'.number_format((($result['sold_price']-$result['sold_cost'])/$result['sold_cost'])*100,2).'%</td>
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}

if(isset($_POST['type']) && $_POST['type']=='issued_rtv')
{

  $vendors = $db->func_query("select distinct b.id,b.name,count(*) as total_shipments from inv_rejected_shipments a,inv_users b where a.vendor=b.id and a.status='Issued' and a.is_hidden=0 and a.vendor>0 group by a.vendor");
  $html = '
  <strong>RTV Issued Shipments</strong> 
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>Vendor</th>
                          
                          <th>Total Shipments</th>
                          <th># Items</th>
                          <th>Estimated Credit</th>
                         

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  $total_items = 0;
                  $total_estimated = 0;
                  $total_shipments = 0;
                  foreach($vendors as $vendor)
                  {
                    $_data = $db->func_query_first("select sum(b.cost) as estimated,count(*) as no_of_items from inv_rejected_shipment_items b,inv_rejected_shipments a where a.id=b.rejected_shipment_id and a.vendor='".$vendor['id']."' and deleted=0 and a.status='Issued' and a.is_hidden=0");
                    $total_items+=$_data['no_of_items'];
                    $total_shipments+=$vendor['total_shipments'];
                    $total_estimated+=$_data['estimated'];
                    $html.='<tr>
                    <td>'.$vendor['name'].'</td>
                    <td>'.$vendor['total_shipments'].'</td>
                    <td>'.$_data['no_of_items'].'</td>
                    <td>$'.number_format($_data['estimated'],2).'</td>
                    </tr>';
                  }
                  $html.='<tr>
                  <td><strong>Total:</strong></td>
                  <td><strong>'.$total_shipments.'</strong></td>
                  <td><strong>'.$total_items.'</strong></td>
                  <td><strong>$'.number_format($total_estimated,2).'</strong></td>
                

                  </tr>';
                  $html.='</tbody></table>';

                  echo $html;exit;


}

if(isset($_POST['type']) && $_POST['type']=='shipped_rtv')
{
   $vendors = $db->func_query("select distinct b.id,b.name,count(*) as total_shipments from inv_rejected_shipments a,inv_users b where a.vendor=b.id and a.status='Shipped' and a.is_hidden=0 and a.vendor>0 group by a.vendor");
  $html = '
  <strong>RTV Shipped Shipments</strong> 
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>Vendor</th>
                          
                          <th>Tracking #</th>
                          
                          <th># Items</th>
                          <th>Estimated Credit</th>
                         

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  $total_items = 0;
                  $total_estimated = 0;
                  $total_shipments = 0;
                  foreach($vendors as $vendor)
                  {
                    $_data = $db->func_query_first("select sum(b.cost) as estimated,count(*) as no_of_items from inv_rejected_shipment_items b,inv_rejected_shipments a where a.id=b.rejected_shipment_id and a.vendor='".$vendor['id']."' and deleted=0 and a.status='Issued' and a.is_hidden=0");
                    $trackings = $db->func_query("select distinct a.tracking_number,(select concat('(',c.datetime,') ',c.message) from inv_tracker_status c,inv_tracker b where b.tracker_id=c.tracker_id  and b.tracking_code=a.tracking_number order by c.id desc limit 1) as last_tracking from inv_rejected_shipments a where  a.vendor='".$vendor['id']."' and   a.status='Shipped' and tracking_number<>''  and a.is_hidden=0");

                    $total_items+=$_data['no_of_items'];
                    
                    $total_estimated+=$_data['estimated'];
                    $html.='<tr>
                    <td>'.$vendor['name'].'</td>
                    <td>';
                    foreach($trackings as $tracking)
                    {
                      if($tracking['last_tracking'])
                      {
                      $html.='<a href="#" data-tooltip="'.$tracking['last_tracking'].'">'.$tracking['tracking_number'].'</a><br>';
                        
                      }
                      else
                      {
                      $html.=$tracking['tracking_number'].'<br>';

                      }
                    }
                    $html.='</td>
                  
                    <td>'.$_data['no_of_items'].'</td>
                    <td>$'.number_format($_data['estimated'],2).'</td>
                    </tr>';
                  }
                  $html.='<tr>
                  <td colspan="2"><strong>Total:</strong></td>
                 
                  <td><strong>'.$total_items.'</strong></td>
                  <td><strong>$'.number_format($total_estimated,2).'</strong></td>
                

                  </tr>';
                  $html.='</tbody></table>';

                  echo $html;exit;


}


if(isset($_POST['type']) && $_POST['type']=='available_credits')
{

  $rows = $db->func_query("SELECT b.name,b.id as vendor_id,sum(a.amount) as total_amount,max(a.date_added) as date_added FROM inv_users b left join inv_vendor_credit_data a on (a.vendor_id=b.id) where b.group_id=1 and b.status=1 group by b.id having sum(a.amount)>0 order by b.name");
  $html = '
  <strong>Available Vendor Credit</strong> 
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>Vendor</th>
                          
                          <th>Credit Available</th>
                          <th>Last Update</th>
                          
                         

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  $total_credit = 0;
                  
                  foreach($rows as $row)
                  {
                   $total_credit+=$row['total_amount'];
                    $html.='<tr>
                    <td>'.$row['name'].'</td>
                    <td>$'.number_format($row['total_amount'],2).'</td>
                    <td>'.americanDate($row['date_added']).'</td>
                    
                    </tr>';
                  }
                  $html.='<tr>
                  <td><strong>Total:</strong></td>
                  <td><strong>$'.number_format($total_credit,2).'</strong></td>
                  <td></td>
                  
                

                  </tr>';
                  $html.='</tbody></table>';

                  echo $html;exit;


}


if(isset($_POST['type']) && $_POST['type']=='estimated_credits')
{

    if(!isset($_POST['filter_date']))
{
  $_POST['filter_date'] ='-7 days';
}
$_date = getDateRange($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']);


  $vendors = $db->func_query("select distinct b.id,b.name,count(*) as total_shipments,sum(a.amount_credited) as credit_issued from inv_rejected_shipments a,inv_users b where a.vendor=b.id and a.amount_credited>0 and a.status='Completed' and (date(a.date_added) between '".$_date['date1']."' and '".$_date['date2']."' or date(a.date_issued) between '".$_date['date1']."' and '".$_date['date2']."' ) and a.is_hidden=0 and a.vendor>0 group by a.vendor");
  $html = '
  <strong>Estimated vs Actual Vendor Credit</strong>'.getFilterHtml($_POST['filter_date'],$_POST['custom_date_start'],$_POST['custom_date_end']).' 
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>Vendor</th>
                          
                          <th># RTV</th>
                          <th># Items</th>
                          <th>Estimated Credit</th>
                          <th>Credit Issued</th>
                          <th>Diff</th>
                         

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  $total_items = 0;
                  $total_estimated = 0;
                  $total_shipments = 0;
                  $total_issued = 0;
                  foreach($vendors as $vendor)
                  {
                    $_data = $db->func_query_first("select sum(b.cost) as estimated,count(*) as no_of_items from inv_rejected_shipment_items b,inv_rejected_shipments a where a.id=b.rejected_shipment_id and a.vendor='".$vendor['id']."' and deleted=0  and a.amount_credited>0 and a.status='Completed' and (date(a.date_added) between '".$_date['date1']."' and '".$_date['date2']."' or date(a.date_issued) between '".$_date['date1']."' and '".$_date['date2']."' ) and a.is_hidden=0");
                    $total_items+=$_data['no_of_items'];
                    $total_shipments+=$vendor['total_shipments'];
                    $total_estimated+=$_data['estimated'];
                    $total_issued+=$vendor['credit_issued'];
                    $html.='<tr>
                    <td>'.$vendor['name'].'</td>
                    <td>'.$vendor['total_shipments'].'</td>
                    <td>'.$_data['no_of_items'].'</td>
                    <td>$'.number_format($_data['estimated'],2).'</td>
                    <td>$'.number_format($vendor['credit_issued'],2).'</td>
                    <td>$'.number_format($_data['estimated'] - $vendor['credit_issued'],2).'<br><small>('.round((($_data['estimated'] - $vendor['credit_issued'])/$_data['estimated'])*100,2).'%)</small></td>
                    </tr>';
                  }
                  $html.='<tr>
                  <td><strong>Total:</strong></td>
                  <td><strong>'.$total_shipments.'</strong></td>
                  <td><strong>'.$total_items.'</strong></td>
                  <td><strong>$'.number_format($total_estimated,2).'</strong></td>
                  <td><strong>$'.number_format($total_issued,2).'</strong></td>
                  <td><strong>$'.number_format($total_estimated-$total_issued,2).'</strong></td>
                

                  </tr>';
                  $html.='</tbody></table>';

                  echo $html;exit;

}
if(isset($_POST['type']) && $_POST['type']=='latest_prices')
{

$new_links = $db->func_query("SELECT s.* , p.tier FROM inv_product_price_scrap s inner join oc_product p ON (s.sku = p.sku) WHERE s.url <> '' AND s.is_new = '1'   order by s.date_updated desc limit 20");
$json=array();
foreach ($new_links as $key => $data) {
  $price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $data['sku'] . "' AND type = '".$data['type']."' order by added DESC limit 1");
  $change = number_format($price['price'] / $price['old_price'] * 100, 2);
        if ($change < 100.00 && $change > 0.00) {
          $change = '-' . (100 - $change);
        } else if ($change == 0.00) {
          $change = 100 - $change;
        } else {
          $change = '+' . ($change - 100);
        }
        if((float)$price['old_price']==0.00)
        {
          $change = 0.00;
        }
  $ppusa_price = getOCItemPrice($db->func_query_first_cell("SELECT product_id FROM oc_product WHERE model='".$data['sku']."'"));
  $perc_diff = number_format(($ppusa_price - $price['price'])/ $price['price'] * 100, 2);
  if ($perc_diff>0) {
    $perc_diff = '+'.$perc_diff;
  }
    $json[$key]['last_fetch'] = americanDate($data['date_updated']);
      $json[$key]['tier'] = $data['tier'];
      $json[$key]['sku'] = linkToProduct($data['sku'],'','target="_blank"');
      $json[$key]['title'] = getItemName($data['sku']);
      $json[$key]['competitor'] = $data['type'];
    $json[$key]['our_price'] = number_format($ppusa_price,2);
    $json[$key]['old_price'] =  number_format($price['old_price'],2);
      $json[$key]['new_price'] =  number_format($price['price'],2);
      $json[$key]['perc_change'] =  number_format($change,2);
      $json[$key]['perc_diff'] =  number_format($perc_diff,2);    
}

// $result2 = $cache->get('purchasing_metrics.latest_prices');
// if(!$result2)
// {

  // $cache->set('purchasing_metrics.latest_prices',$result2);
// 

// testObject($result2);

  $html = '
  <strong>Latest Price Changes</strong>
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          
                          <th>Date Change</th>
                          
                          <th>SKU</th>
                          <th>Name</th>
                          <th>Old Price</th>
                          <th>New Price</th>
                          <th>New Margin</th>

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  foreach($json as $result)
                  {

                    $html.='<tr>
                    <td>'.$result['last_fetch'].'</td>
                    <td>'.$result['sku'].'</td>
                    
                    <td>'.$result['title'].'</td>
                    <td>$'.($result['old_price']).'</td>
                    <td>$'.($result['new_price']).'</td>
                    <td>'.($result['perc_diff']).'</td>
                    
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}




?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <script src="js/jquery.min.js"></script>
  
  <script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
  <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
  <link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
  <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">
  <title>Purchasing Metrics</title>

</head>
<body>
  <?php if (!$_SESSION['hide_header']) { ?>
  <div align="center"> 
    <?php } else { ?>
    <div style="display: none;" align="center">
      <?php } ?>
      <?php include_once 'inc/header.php';?>
    </div>
    <?php if(@$_SESSION['message']):?>
      <div align="center"><br />
        <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
      </div>
    <?php endif;?>
    
    <h2 align="center">Purchasing Metrics</h2>
    <div style="text-align: center;margin-right:40px;float:right;position:relative;border:1px solid #ddd;padding:10px">
    
    <form method="get" action="purchasing_inventory_csv.php" target="_blank">
    <strong>Purchase to Sale Report</strong><br><br>
    <input type="text" data-type="monthyear" name="start_date" value="<?php echo date('Y-m',strtotime('-3 months'));?>">
    <input type="text" data-type="monthyear" name="end_date" value="<?php echo date('Y-m');?>"><br><br>
    
    <select name="page">
    <?php
    for($i=1;$i<=5;$i++)
    {
      ?>
      <option value="<?php echo $i;?>">Chunk # <?php echo $i;?></option>
      <?php
    }
    ?>
    </select><br><br>
    <input type="submit" class="button" value="Generate Report">
    </form>
    </div>
    <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="4" width="50%" valign="top">
          <div id="items_below_rop" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>
           <td colspan="4" width="50%" valign="top">
          <div id="lost_sales_oos" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>
        </tr>
        <tr>
            <td colspan="8"><hr></td>
        </tr>

<tr>
          
          

          <td colspan="8" width="50%" valign="top">
          <div id="parts_with_return_ratio" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>
          </tr>

<tr>
            <td colspan="8"><hr></td>
        </tr>

<tr>
          
          

          <td colspan="8" width="50%" valign="top">
          <div id="parts_with_return_ratio2" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>
          </tr>



      <tr>

      <td colspan="8"></td>
      </tr>
      <tr>
          
          

          <td colspan="8" width="50%" valign="top">
          <div id="models_with_returns" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>
          </tr>

      <tr>

      <td colspan="8"></td>
      </tr>

      <tr>
          
          

          <td colspan="8" width="" valign="top">
          <div id="profit_by_sku_type" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>

        <tr>

        <tr>

      <td colspan="8"></td>
      </tr>
      <tr>
          
          

          <td colspan="8" width="" valign="top">
          <div id="profit_by_part_type" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>

        <tr>

           <tr>

      <td colspan="8"></td>
      </tr>
      <tr>
          
          

          <td colspan="8" width="" valign="top">
          <div id="profit_by_vendor" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>

        <tr>


            <tr>

      <td colspan="8"></td>
      </tr>

       <tr>
          
          

          <td colspan="4" width="" valign="top">
          <div id="issued_rtv" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>

          <td colspan="4">
             <div id="shipped_rtv" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>
          </td>

        </tr>

          <tr>

      <td colspan="8"></td>
      </tr>

       <tr>
          
          

          <td colspan="4" width="" valign="top">
          <div id="available_credits" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>

          <td colspan="4">
             <div id="estimated_credits" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>
          </td>

        </tr>
<tr>

      <td colspan="8"></td>
      </tr>

      <tr>
          
          

          <td colspan="4" width="" valign="top">
          <div id="latest_prices" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>

          <td colspan="4">

          </td>

        <tr>

        

      </tbody>
    </table>

  </body>
  </html>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  <script>
  $(document).ready(function(){
loadData('items_below_rop');

  });
  var first_load=true;
  function loadData(type)
  {
  	if(type=='latest_prices')
  	{
  		first_load = false;
  	}
  	var loading = first_load; 

    $.ajax({
        url: 'purchasing_metrics.php',
        type: 'post',
        data: {type:type,filter_date:$('#'+type+' .filter_date').val(),custom_date_start:$('#'+type+' .custom_date_start').val(),custom_date_end:$('#'+type+' .custom_date_end').val()},
        dataType: 'html',
        beforeSend: function() {
          $('#'+type).html('<img src="images/loading.gif" height"100" width="100" />');
        },  
        complete: function() {
        },      
        success: function(html) {
          $('#'+type).html(html);
          console.log(loading);
          if(loading==false)
          {
          	return false;
          }



          if(type=='items_below_rop')
          {

          loadData('lost_sales_oos');
          }

           if(type=='lost_sales_oos')
          {
          	
          		loadData('parts_with_return_ratio');
          		
          	

          }

          if(type=='parts_with_return_ratio')
          {

          loadData('parts_with_return_ratio2');
          
          }
          if(type=='parts_with_return_ratio2')
          {

          loadData('models_with_returns');
          
          }

          

           if(type=='models_with_returns')
          {

          loadData('profit_by_sku_type');
          }

          if(type=='profit_by_sku_type')
          {

          loadData('profit_by_part_type');
          }

          if(type=='profit_by_part_type')
          {

          loadData('profit_by_vendor');
          }

           if(type=='profit_by_vendor')
          {

          loadData('issued_rtv');
          
          }

           if(type=='issued_rtv')
          {

          loadData('shipped_rtv');
          
          }

          if(type=='shipped_rtv')
          {

          loadData('available_credits');
          
          }

          if(type=='available_credits')
          {

          loadData('estimated_credits');
          
          }





            if(type=='estimated_credits')
          {

          loadData('latest_prices');
          
          }
          

            if(type=='latest_prices')
          {
            $('.xtable').tablesorter({
        textExtraction: function(node){ 
            // for numbers formattted like €1.000,50 e.g. Italian
            // return $(node).text().replace(/[.$£€]/g,'').replace(/,/g,'.');

            // for numbers formattted like $1,000.50 e.g. English
            return $(node).text().replace(/[,$£€]/g,'');
         }
    });
          //loadData('sales_agent');
          }
        }
      });
  }

  jQuery(document).ready(function () {
        
        jQuery('.fancybox2').fancybox({width: '500px', height: '500px', autoCenter: true, autoSize: false});
      });
  $(document).on('change','.filter_date',function(e)
  {
  	
  	if($(this).val()=='custom')
  	{
  		$(this).next().show();
  	}
  	else
  	{
  		$(this).next().hide();	
  		loadData($(this).parent().attr('id'));
  	}
  })

  $(document).on('change','.custom_date_end',function(e)
  {
  
  		
  		loadData($(this).parent().parent().attr('id'));
  	
  })
  </script>