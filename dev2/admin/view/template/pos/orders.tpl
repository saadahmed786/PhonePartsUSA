<?php

if($_GET['picked_up_orders']=='true')
{
    $picked_up = true;
}
else
{
    $picked_up = false;
}
if($picked_up)
{
    echo "<h2>Picked Up</h2>";
}
else
{
    echo "<h2>Awaiting Pickup</h2>";
}

if($_GET['combine']=='true')
{
    $combine = 1;
}
else
{
    $combine = 0;
}

?>

<a onclick="$('#form').attr('action', '<?php echo $this->url->link("sale/order/invoice&pos_view=1", "token=" . $this->session->data["token"], "SSL"); ?>'); $('#form').attr('target', '_blank'); $('#form').submit();" class="button" style="float: right;"><span>Print Invoice</span></a>
<?php if ($picked_up) { ?>
<a onclick="$('#form').attr('action', '<?php echo $this->url->link("pos/pos/printSlipAjax", "token=" . $this->session->data["token"], "SSL"); ?>'); $('#form').attr('target', '_blank'); $('#form').submit();" class="button" style="float: right; margin: 0px 15px;"><span>Print Receipt</span></a>
<?php } ?>
<div style="clear: both;"></div>

<div class="grid">
    <div class="row">
        <!-- order list -->
        <div class="order_list">
            <form action="" method="post" enctype="multipart/form-data" id="form">
                <table class="table striped">
                    <thead>
                        <tr>
                            <th style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <!-- <th>Status</th> -->
                            <th>Total</th>
                            <th>Payment</th>
                            <th>SKU * QTY</th>
                            <th>Placed</th>
                            <?php if ($picked_up) { ?>
                            <th><a href="javascript:void(0);" onclick="if ($('#order_by').val() == 'DESC'){$('#order_by').val('ASC');$(this).html('Picked Up &#x25B2;');}else{$('#order_by').val('DESC');$(this).html('Picked Up &#x25BC;');} filter();" style="color:#666" >Picked Up
                                <?php   
                                if($sort=='o.date_modified')
                                {
                                if($order=='DESC')
                                {	
                                echo '&#x25BC;';	
                            }
                            else
                            {
                            echo '&#x25B2;';	
                        }

                    }
                    ?>
                </a></th>
                <?php } ?>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr class="filter">
                <td align="right" colspan="2"><input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" size="4" style="text-align: right;" /></td>
                <td><input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" /></td>
                            <!-- <td>
                                <div class="css3-metro-dropdown">
                                    <select name="filter_order_status_id">
                                        <option value="*">All</option>
                                        <?php if ($filter_order_status_id == '0') { ?>
                                        <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                                        <?php } else { ?>
                                        <option value="0"><?php echo $text_missing; ?></option>
                                        <?php } ?>
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td> -->
                            <td align="right"><input type="text" name="filter_total" value="<?php echo $filter_total; ?>" size="4" style="text-align: right;" /></td>

                            <td>
                                <div class="css3-metro-dropdown">
                                    <select name="filter_payment_status">
                                        <option value="*">All</option>
                                        <option value="Cash or Credit at Store Pick-Up">Unpaid</option>
                                        <option value="Paypal">Paid</option>



                                    </select>
                                </div>
                            </td>

                            <td align="right"><input type="text" name="filter_sku" value="<?php echo $filter_sku; ?>" size="4" style="text-align: right;" />

                            </td>

                            <td><input class="date" type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" size="12"  /></td>
                            <?php if ($picked_up) { ?>
                            <td><input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" size="12" class="date" /><input type="hidden" id="sort_by" value="<?php echo $sort;?>" /><input type="hidden" id="order_by" value="<?php echo  $order;?>" /></td>
                            <?php } ?>
                            <td align="center"><a onclick="filter();" class="btn_filter">Filter</a><input type="hidden" id="picked_up_orders" value="<?php echo ($picked_up?'true':'false');?>" /></td>
                        </tr>
                        <?php 


                        if($rows){

                        foreach($rows as $row){

                        $paid_status = ($row['payment_method']=='Cash or Credit at Store Pick-Up'?'Unpaid':'Paid');
                        $pickup_status = ($row['order_status_id']==$this->config->get('config_complete_status_id')?'Picked Up':'Not Picked Up');
                        ?>
                        <tr class="data_row">
                            <?php
                            if($row['ref_order_id'])
                            {
                            ?>
                            <td align='center'><input type="checkbox" name="selected[]" value="<?= $row['ref_order_id'] ?>" /></td>
                            <td align='right'><?= $row['ref_order_id'] ?></td>
                            <?php
                        }
                        else
                        {
                        ?>
                        <td align='center'><input type="checkbox" name="selected[]" value="<?= $row['order_id'] ?>" /></td>
                        <td align='right'><?= $row['order_id'] ?></td>
                        <?php
                    }
                    ?>
                    <td style="line-height:10px;"><a target="_blank" href="<?php echo HTTPS_CATALOG; ?>imp/customer_profile.php?email=<?php echo base64_encode($row['email']); ?>"><?= $row['customer'] ?></a> <br> </br><?= $row['email'] ?></td>
                    <!-- <td><?= $row['status'] ?></td> -->
                    <td align='right' class='td_total'><?= $this->currency->format($row['total']) ?></td>
                    <td><?php echo $row['payment_method'].' <small>('. $paid_status.')</small>'; ?></td>
                    <td><?php 


                        foreach($row['products'] as $productx){

                        echo '<a target="_blank" href="' . HTTPS_CATALOG . 'index.php?route=product/product&product_id='. $productx['product_id'] .'" title="'. $productx['name'] .'">' . $productx['model'].' * '.$productx['quantity']."</a><br>";

                    }  ?></td>
                    <td><?= date('m-d-Y h:i A',strtotime($row['date_added'])); ?></td>
                    <?php if ($picked_up) { ?>
                    <td><?= date('m-d-Y h:i A',strtotime($row['date_modified'])); ?></td>
                    <?php } ?>
                    <td align="center">
                        [<a class="edit" data-order-id="<?= $row['order_id']; ?>" data-combine="<?php echo $combine;?>" href="#"><?php echo ($picked_up?'Return':($combine?'Combine':'Select'));?></a>]
                    </td>
                </tr>
                <?php }

            }
            else
            {
            ?>
            <tr class="data_row">

                <td colspan="8" align="center">No Order Available</td>
            </tr>

            <?php


        }

        ?>
    </tbody>
</table>  
</form>
</div>
<!-- END .order_list -->
<?php
if($row)
{

    ?>
    <div class="pagination">
        <?php echo $pagination; ?>
    </div>
    <?php

}
?>
<!-- END .pagination -->
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('keydown', 'input[type="text"]', function(e) {
            if (e.keyCode == 13) {
                filter();
            }
        });
    });
</script>
<script type="text/javascript"><!--
   $(document).ready(function() {
       $('.date').datepicker({dateFormat: 'yy-mm-dd'});
  
  // map_filter();
});
</script>
</div>
</div>   
<!-- END .grid -->
