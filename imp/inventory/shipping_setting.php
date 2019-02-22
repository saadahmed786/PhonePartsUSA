<?php
include_once '../auth.php';
include_once '../inc/functions.php';
include_once 'class.php';

$carriers = $inventory->listCarriers();
$carriers = json_decode($carriers,true);



$shippings = oc_config('multiflatrate_data');
$shippings = unserialize($shippings);
$my_shippings = array();
$i=0;
foreach($shippings as $shipping)
{
  if($i==0)
  {
    $i++;
    continue;
  }

    
    
  $my_shippings[] = $shipping['title']['en'];
$i++;
}
$my_shippings = array_unique($my_shippings);

if(isset($_POST['action']))
{
  // echo "<Pre>";
  // print_r($_POST);exit;
  $db->db_exec("DELETE FROM inv_shipping_mapping");


  

  foreach($_POST['shipping'] as $i=> $shipping)
  {
    $array = array();
    
    foreach($_POST['service_'.$i] as $j => $service)
    {
      // print_r( $_POST['min_total_'.$i][$j])."<br>";
      // echo $_POST['max_total_'.$i[0]];
      // $array[] = array('service'=>$service,'min_total'=>(int)$_POST['min_total_'.$i][$j],'max_total'=>(int)$_POST['max_total_'.$i][$j]);
      $array[] = $service;
    }
    // exit;

    $db->db_exec("INSERT INTO inv_shipping_mapping SET shipping_method='".$db->func_escape_string($shipping)."',mapping='".json_encode(array('service'=>$array))."',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."'");
  }
  header("Location: shipping_setting.php");
}

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <script src="../js/jquery.min.js"></script>
  
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
  
  <title>Reason Classified Vouchers</title>

</head>
<style>
* { font-family: Verdana, Geneva, sans-serif; font-size:12px; }
</style>
<body>

    <h3 align="center">Shipping Mapping Preset</h3>

    <form method="post">
<?php
$i=0;
  foreach($my_shippings as $shipping)
  {

    $details = json_decode($db->func_query_first_cell("SELECT mapping FROM inv_shipping_mapping WHERE shipping_method='".$shipping."'"),true);
    if(!$details)
    {
      $details = array(1=>1);
    }
    // print_r($details);;
    $j=0;
    foreach($details['service'] as $detail)
    {
?>
<div class="row" id="row_<?php echo $i;?>" style="margin-bottom:5px">
        
        <div class="col-md-4" style="font-size:15px;font-weight: bold">
                
          <?php

          if($j==0)
          {
            ?>
            <input type="hidden" name="shipping[]" value="<?php echo $shipping;?>">
            <?php
            echo $shipping;
          }
          else
          {
            echo '-';
          }
          ?>  
            </div>
            <div class="col-md-5">
              
            
              <select class="form-control service_<?php echo $i;?>" name="service_<?php echo $i;?>[]"  >
                                <option value="">Please Select</option>
                                <?php
                                foreach($carriers['carriers'] as $carrier)
                                {
                                  ?>
                                  <optgroup label="<?php echo $carrier['friendly_name'];?>">
                                  <?php
                                  foreach($carrier['services'] as $service)
                                  {
                                  ?>
                                  <option <?php echo (($service['service_code']==$detail)?'selected':'');?> value="<?php echo $service['service_code'];?>"><?php echo utf8_decode($service['name']);?></option>

                                  <?php
                                }
                                ?>
                                </optgroup>
                                <?php
                                }
                                ?>
                              </select>
                             
                              

            </div>
             <!-- <div class="col-md-2">
                              <input type="text"  class="form-control" placeholder="Min Total" name="min_total_<?php echo $i;?>[]" value="<?php echo $detail['min_total'];?>" >
                              </div>

                              <div class="col-md-2">
                              <input type="text"  class="form-control" placeholder="Max Total" name="max_total_<?php echo $i;?>[]" value="<?php echo $detail['max_total'];?>"">
                              </div> -->
            <div class="col-md-3">
            <?php 
            if($j==0)
            {

            ?>
            <a href="#" onclick="addRow(<?php echo $i;?>)">Add</a>
            <?php
          }
          else
          {
            ?>
            <a href="#" onclick="removeThis(this)">Remove</a>
            <?php
          }
          ?>
            </div>
          </div>

          

          <?php
          $j++;
        }
        $i++;
        }
        ?>
      

  </body>
  <div id="newRow" class="hidden"></div>

  <input type="submit" class="btn btn-primary" value="Save" name="action">
  </form>
  
  <script>

  function addRow(row_no)
  {
    var count_elements = $('.service_'+row_no).length;
    var clone = $('#row_'+row_no).clone();
    $('#newRow').html(clone);
    $('#newRow div:first').removeAttr('id');
    $('#newRow div:first div:first').html('-');
    $('#newRow div:first div:eq(2)').html('<a href="#" onclick="removeThis(this)">Remove</a>');
    $('#newRow div:first .form-control').val('');
    // $('#newRow div:first div:eq(1) select').removeAttr('name').attr('name','service_'+row_no+'_'+count_elements);
    
    // $('#newRow div:first div:eq(2) input').removeAttr('name').attr('name','min_total_'+row_no+'_'+count_elements);
    // $('#newRow div:first div:eq(3) input').removeAttr('name').attr('name','max_total_'+row_no+'_'+count_elements);


    $('#row_'+row_no).after($('#newRow').html());
    $('#newRow').html('');
  }

  function removeThis(obj)
  {
    $(obj).parent().parent().remove();
  }
  </script>
   </html>
