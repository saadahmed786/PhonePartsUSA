<h2>Map Order</h2>
<div style="clear: both;"></div>
<div class="grid">
    <div class="row">
        <!-- order list -->
        <div class="order_list" id="map_order_list">
            <form action="" method="post" enctype="multipart/form-data" id="form">
                <table class="table striped">
                    <thead>
                        <tr>
                            
                            <th>Name</th>
                            <th>Email</th>
                            <th>Transaction ID</th>
                            <th>Total</th>
                            <th>Net</th>
                            
                            <th>Date added</th>
                            
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="map_filter">
                            <td ><input type="text" id="filter_name" value="<?php echo $filter_name; ?>" placeholder="Customer Name Filter"  /></td>
                            <td ><input type="text" id="filter_email" value="<?php echo $filter_email; ?>" placeholder="Email Filter"  /></td>
                            <td><input type="text" id="filter_transaction_id" value="<?php echo $filter_transaction_id; ?>" placeholder="Transaction ID Filter" /></td>
                            <td>
                               
                            </td>
                            <td><input type="text" id="filter_date_from" value="<?php echo $filter_date_from; ?>" size="12" class="date" placeholder="YYYY-MM-DD HH:MM:SS" /></td>

                            

                           

                            <td><input type="text" id="filter_date_to" value="<?php echo $filter_date_to; ?>" size="12" class="date" placeholder="YYYY-MM-DD HH:MM:SS" /></td>
                            <td align="center"><button type="button" onclick="map_filter();" class="btn_filter">Filter</button></td>
                        </tr>
                        <?php
                        if($rows)
                        {
                        foreach($rows as $row)
                        {
                        ?>
                        <tr class="map_tr">
                        <td><?=$row['firstname'].' '.$row['lastname'];?></td>
                        <td><?=$row['email'];?></td>
                        <td><?=$row['transaction_id'];?></td>
                       <td align="right">$<?=number_format($row['amount'],2);?></td> 
                       <td align="right">$<?=number_format($row['net_amount'],2);?></td>
                       <td align="center"><?=date('m/d/Y h:iA',strtotime($row['order_date']));?></td>
                       <td><a href="javascript:void(0);" onClick="map_transaction_id('<?=$row['transaction_id'];?>','<?=$row['amount'];?>')">Map</a></td>
                        </tr>
                        <?php
                    }
                }
                else
                {
                    ?>
                    <tr class="not_found">

                    <td colspan="7">No Data Found, Please try again</td>
                    </tr>
                    <?php
                }
                    ?>
                    </tbody>
                </table>  
            </form>
        </div>
        <!-- END .order_list -->
        <div class="pagination2">
          <?php echo $pagination; ?>
      </div>
        <!-- END .pagination -->

    </div>
</div>  
 
<!-- END .grid -->
