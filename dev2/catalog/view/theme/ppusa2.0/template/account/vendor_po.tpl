<?php echo $header; ?>
<main class="main">
  <div class="container history-detail-page">
    <div class="white-box overflow-hide">
      <div class="row">
        <div class="col-md-12">
          <div class="row inline-block">
          
            <div class="col-md-12 white-box-right inline-block pd30">
              <div class="text-center">
              <h2 class='uppercase mt40'>Vendor PO # <?php echo $detail['vendor_po_id'];?></h2>
              </div>
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <div class="row text-center">
                    <?php if ($products) { ?>
                    <form id="vendorForm" method="post">
                    <input type="hidden" name="vendor_po_id" value="<?php echo $detail['vendor_po_id'];?>">
                    <table class="table table-bordered lbbMain fontsize13">
                      <thead>
                        <tr>
                          <th style="width:15%">Image</th>
                          <th width="15%">SKU</th>
                          <th width="40%">Item Name</th>
                          <th width="10%">Req. Qty</th>
                          <th width="20%">Cost</th>
                        </tr>
                      </thead>
                    <?php foreach ($products  as $product) {

                    if($product['cost']==0.00)
                    {
                    $product['cost'] = '';
                    }
                     ?>
                      <tbody>
                        <tr>
                          <td><img style="width:75px" src="<?php echo $product['image'];?>" /></td>
                          <td><?php echo $product['model'];?></td>
                          <td><?php echo $product['name']; ?></td>
                          <td><?php echo $product['qty']; ?></td>
                          <td><input type="text" class="form-control" name="new_cost[<?php echo $product['model'];?>]" value="<?php echo $product['cost']; ?>"></td>
                        </tr>
                      </tbody>
                    <?php } ?>
                    </table>
                    </form>
                    <span style="color:red;font-weight:bold">*Before pressing Submit Button, make sure you have double-checked all the costing. Page will not be accessible after the data is submitted.</span>
                    <br>
                    <br>
                    <button class="btn btn-primary" id="btn_submit">Submit</button>
                    <br><br>
                    <?php } else { ?>
                    <p>No Items in this, please contact admin.</p>
                    <?php } ?>
                  </div>
                </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<script>
$(document).on('click','#btn_submit',function(e){

  if(!confirm('Are you sure want to complete the Purchase Order?'))
  {
    return false;
  }
var datastring = $("#vendorForm").serialize();
  $.ajax({
      
        url: 'index.php?route=account/vendor/update',
        dataType: 'json',
        type:'post',
      data:datastring,
      beforeSend: function() {
        $('#btn_submit').attr('disabled', true);
        $('#btn_submit').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
      },
      complete: function() {
       $('#btn_submit').attr('disabled', false); 
        $('.wait').remove();
      },
      success: function(json) {
      if(json['success'])
      {
        alert('Thank you for your time, Data has been submitted.');
        window.location='<?php echo HTTPS_SERVER;?>';
      }
      else
      {
        alert('There is some error submitting your request, please try again, or contact administrator');
        return false;
      }
       
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    }); 
  
});


</script>
<?php echo $footer; ?>
