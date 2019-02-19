<script type="text/javascript" src="catalog/view/javascript/bossthemes/bossthemes.js"></script>
<script>
  $(function() {
    $( "#sortable" ).sortable({
      revert: true
    });
    $( "#draggable" ).draggable({
      connectToSortable: "#sortable",
      helper: "clone",
      revert: "invalid"
    });
    $( "ul, li" ).disableSelection();
  });

</script>
<div id="create-list-pop" class="popup">
  <div class="popup-head">
    <h2 class="blue-title uppercase">Create a new list</h2>
  </div>
  <div class="popup-body">
    <form class="form-horizontal">
      <div class="form-group" id="new_list_form">
        <label for="listName" class="col-sm-3 control-label">List Name</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" placeholder="New List Name..." id="list_name">
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label"></label>
          <div class="col-sm-9">
            <input type="button" value="Create List" class="btn btn-primary" onclick="saveNewList()">
            <input type="button" value="Cancel" class="btn btn-primary  red-btn">
          </div>
      </div>
    </form>
  </div>  
</div>
<div id="move-list-pop" class="popup">
  <div class="popup-head">
    <h2 class="blue-title uppercase">Move Item to list</h2>
  </div>
  <div class="popup-body">
    <form class="form-horizontal">
      <div class="form-group">
        <label for="listName" class="col-sm-3 control-label">List Name</label>
          <div class="col-sm-9">
            <select class="selectpicker">
                <option value="">Select...</option>
                <?php foreach($lists as $key => $value) { ?>
                    <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
                <?php } ?>
            </select>
          </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label"></label>
          <div class="col-sm-9">
            <input type="button" value="Move Item" class="btn btn-primary" id='move_item'>
            <input type="submit" value="Cancel" class="btn btn-primary  red-btn">
          </div>
      </div>
    </form>
  </div>  
</div>
<div id="delete-list-pop" class="popup">
  <div class="popup-head">
    <h2 class="blue-title uppercase">Are you sure you want to Delete this List</h2>
  </div>
  <div class="popup-body">
    <h5 class="text-center">
      Favorites List #1
    </h5>
    <div class="text-center popup-btns">
      <a href="#" class="btn btn-primary" type="submit">Yes Delete The List Forever</a> &nbsp;
      <a href="#" class="btn btn-primary red-btn" type="submit">No, I changed my mind</a>
    </div>
  </div>  
</div>

<!-- @End of header -->
                <div class="tab-inner">
                  <div class="account-list-head">
                    <h5>Current list</h5>
                    <div class="row">
                      <div class="col-md-7">
                        <div class="row">
                          <div class="col-sm-5 mb-sm-15">
                            <select name="" id="select_list" class="selectpicker">
                              <option value="">Favorites</option>
                              <?php foreach($lists as $key => $value) { ?>
                                <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                          <div class="col-sm-3 mb-sm-15">
                            <a href="#delete-list-pop" class="fancybox btn btn-primary red-btn">REMOVE</a>
                          </div>
                          <div class="col-sm-4 mb-sm-15">
                            <a href="#create-list-pop" class="btn btn-primary fancybox">Create new list</a>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-5">
                        <ul class="social-shares">
                        <li class="share-alt">
                          <i class="fa fa-share-alt"></i>
                          Share:
                        </li>
                        <li class="email">
                          <a href="#"><img src="catalog/view/theme/ppusa2.0/images/icons/envelope.png" alt=""></a>
                        </li>
                        <li class="print">
                          <a href="#"><img src="catalog/view/theme/ppusa2.0/images/icons/printer.png" alt=""></a>
                        </li>
                        <li class="twitter">
                          <a href="#"><i class="fa fa-twitter"></i></a>
                        </li>
                        <li class="facebook">
                          <a href="#"><i class="fa fa-facebook"></i></a>
                        </li>
                        <li class="google-plus">
                          <a href="#"><img src="catalog/view/theme/ppusa2.0/images/icons/googleplus.png" alt=""></a>
                        </li>
                        <li class="pinterest">
                          <a href="#"><i class="fa fa-pinterest-p"></i></a>
                        </li>
                      </ul>
                      </div>
                    </div>
                  </div>
                </div>  
                <div class="border"></div>
          <ul id="sortable">
          <?php foreach ($products as $key => $product) { ?>
            <div id="delete-item-pop" class="popup">
              <div class="popup-head">
                <h2 class="blue-title uppercase">Are you sure you want to Delete this Item?</h2>
              </div>
              <div class="popup-body">
                <h5 class="text-center">
                  <?php echo $product['name']; ?>
                </h5>
                <div class="text-center popup-btns">

                  <a href="<?php echo $product['remove']; ?>" class="btn btn-primary" type="submit">Yes Delete Item from List</a> &nbsp;
                  <button class="btn btn-primary red-btn" onclick='parent.$.fancybox.close();'>No, I changed my mind</button>
                </div>
              </div>  
            </div>
                <div class="tab-inner pb30">
                  
                    <li>
                      <div class="product-detail pr0">
                        <div class="account-product">
                          <div class="drager-icon">
                            <img src="catalog/view/theme/ppusa2.0/images/icons/drager.png" alt="">
                          </div>
                        <div class="product-detail-inner clearfix">
                          <div class="row">
                            <div class="col-md-2 product-detail-img">
                              <div class="image"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>"></div>
                            </div>
                            <div class="col-md-10 product-detail-text">
                              <h3><?php echo $product['name']; ?></h3>
                              <div class="row">
                                <div class="col-md-2">
                                  <div class="review-area">
                                    <ul class="review-stars clearfix">
                                      <li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
                                      <li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
                                      <li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
                                      <li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
                                      <li><a href="#"><i class="fa fa-star"></i></a></li>
                                    </ul>
                                    <p><a href="#" class="review-links underline">40 reviews</a></p>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="account-btns">
                                    <a href="#move-list-pop" class="fancybox btn btn-primary" onclick='addTolist("<?php echo $product['product_id'] ?>")'>move</a>
                                    <input type="hidden" name="theme" value="2">
                                    <a href="#delete-item-pop" class="fancybox btn btn-primary red-btn">Delete</a>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="text-center">
                                    <span class="favorite"><i class="fa fa-heart"></i><a href="#" class="underline">Favorite</a></span>
                                  </div>
                                  <div class="cart-quality">
                                    <table class="table">
                                      <thead>
                                        <tr>
                                          <th>Quantity</th>
                                          <th>Our Price</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr>
                                          <td>1</td>
                                          <td>$105.00</td>
                                        </tr>
                                        <tr>
                                          <td>3-9</td>
                                          <td>$100.00</td>
                                        </tr>
                                        <tr>
                                          <td>10+</td>
                                          <td>$95.00</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                                <div class="col-md-4 cart-total-wrp">
                                  <div class="cart-total text-right">
                                    <div class="qtyt-box">
                                      <div class="input-group spinner">
                                        <span class="txt">QTY</span>
                                          <input type="text" class="form-control qty" value="1">
                                          <div class="input-group-btn-vertical">
                                            <button class="btn " type="button"><i class="fa fa-plus" style="font-size:12px"></i></button>
                                            <button class="btn" type="button"><i class="fa fa-minus" style="font-size:12px;margin-top:8px"></i></button>
                                          </div>

                                       </div>
                                    </div>
                                    <h3><?php if ($product['price']) { ?>
                                      <div class="price">
                                        <?php if (!$product['special']) { ?>
                                          <?php echo $product['price']; ?>
                                          <?php } else { ?>
                                            <s><?php echo $product['price']; ?></s> <b><?php echo $product['special']; ?></b>
                                            <?php } ?>
                                          </div>
                                          <?php } ?></h3>
                                    
                               <button onclick="addToCartpp2(<?php echo $product['product_id']; ?>, $(this).parent().find('.qty').val())" class="btn btn-success addtocart">Add to cart</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="features-row">
                            <div class="row item-features">
                              <ul>
                                <li class="col-xs-6">
                                  Phone model
                                </li>
                                <li class="col-xs-6">
                                  <?php echo $product['model']; ?>
                                </li>
                              </ul>
                              <ul>
                                <li class="col-xs-6">
                                  Compatibility
                                </li>
                                <li class="col-xs-6">
                                  Type
                                </li>
                              </ul>
                              <ul>
                                <li class="col-xs-6">
                                  Information
                                </li>
                                <li class="col-xs-6">
                                  <?php echo $product['stock']; ?>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                
              </div>
             <?php } ?>
             </ul>

<script type="text/javascript">


  function saveNewList()
  {
    $(".fancybox-close").click();
    var name = $('#list_name').val();
    $('#list_name').val('');
    var d = new FormData();
    d.append('list_name',name);

    $.ajax({
      url : '?route=account/account/addNewList',
      type : 'POST',
      dataType: 'json',
      data: d,
      contentType: false,
      enctype: 'multipart/form-data',
      processData: false,
      success: function(response)
      {
        window.location.reload();
      }
    })
  }

  function moveItemToList(id)
  {
    var list_id = $('div#move-list-pop .selectpicker').val();
    var d = new FormData();
    d.append('product_id',id);
    d.append('list_id',list_id);
    $.ajax({
      url : '?route=account/account/addProductTolist',
      type : 'POST',
      dataType: 'json',
      data: d,
      contentType: false,
      enctype: 'multipart/form-data',
      processData: false,
      success: function(response)
      {
        window.location.reload();
      }
    })
  }

  function addTolist(id)
  {
    $('#move_item').attr('onclick','moveItemToList('+id+')');
  }

  $('#select_list').on('change', function (el) 
  {
    var list = el.target.value;
    
      var d = new FormData();
      d.append('list_id',list);
      $.ajax({
        url : '?route=account/wishlist/showListProducts',
        type : 'POST',
        dataType: 'json',
        data: d,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        success: function(response)
        {
          $('#sortable').html(response);
        }
      })
    
  })
</script>