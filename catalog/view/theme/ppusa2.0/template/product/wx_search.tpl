<?php echo $header; ?>
<?php $endBC = end($breadcrumbs); ?>

<main class="main">
  <div class="container category-page">
    <div class="row">
      <!-- <div class="col-md-12">
        <ul class="breadcrum clearfix">
          <?php foreach ($breadcrumbs as $key => $breadcrumb) : ?>
            <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
            <?php if (count($breadcrumbs) != ($key + 1)) { ?>
            <li class="seprator">></li>
            <?php } ?>
          <?php endforeach; ?>
        </ul>
        
      </div> -->
      <div class="col-md-12">
        <div class="text-right">
          <div class="filter-counter">
            Refine Products &nbsp;(<span class="filter-qty">0</span>)
          </div>
        </div>
      </div>
      <div class="col-md-3 filter-product" style="display: none;">
        <div class="filter-inner">
          <div class="filter-buttons text-right">
            <!-- <a href="javascript:void(0);" class="btn btn-primary yellow-btn clear-filter">Clear All</a> -->
            <a href="javascript:void(0);" class="btn btn-primary apply-filter">APPLY</a>
            <a href="javascript:void(0);" class="btn btn-primary yellow-btn" id="close_filter_box">CLOSE</a>
          </div>
           <h2>Filter Products</h2>
          <div class="filter-group">
            <?php if ($devices) { ?>
            <div class="panel">
              <div class="panel-heading">
                <input type="checkbox" checked="checked" class="css-checkbox" id="checkAll">
                  <label for="checkAll" data-parent="#accordion" class="css-label3">
                    <a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#collapse1">
                  Phone model <i class="fa fa-angle-down"></i>
                </a>
                  </label>


              </div>
              <div id="collapse1" class="panel-collapse collapse">
                <ul class="filter-check checkAll-parent" data-filter-name="Model">
                  <?php foreach ($devices as $_submodels) { ?>
                  <?php foreach ($_submodels as $submodel) { ?>
                  <li>
                    <a href="javascript:void(0);">
                      <input type="checkbox" class="css-checkbox submodel" value="<?php echo $submodel['id']; ?>" id="submodel<?php echo $submodel['id']; ?>" checked>
                      <label for="submodel<?php echo $submodel['id']; ?>" class="css-label3"><?php echo $submodel['name']; ?></label>
                    </a>
                  </li>
                  <?php } ?>
                  <?php } ?>
                  
                </ul>
              </div>
            </div>
            <?php } ?>
            <?php if ($classes) { ?>
            <?php $main_name; ?>
            <?php foreach ($classes as $x => $class) : ?>
              
              <?php if ($class['main_name'] != $main_name) { ?>
              <div class="panel update-filters">
                <div class="panel-heading">
                  
                  <input type="checkbox" class="css-checkbox" checked id="selectAll<?php echo strtolower(str_replace(' ', '_', $class['main_name'])); ?>">
                <label for="selectAll<?php echo strtolower(str_replace(' ', '_', $class['main_name'])); ?>" class="css-label3">
                   <a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#<?php echo strtolower(str_replace(' ', '_', $class['main_name'])); ?>">
                    <?php echo $class['main_name']; ?><i class="fa fa-angle-down"></i>
                  </a>
                </label>

                 
                  
                </div>
                <div id="<?php echo strtolower(str_replace(' ', '_', $class['main_name'])); ?>" class="panel-collapse collapse">
                  <ul class="filter-check">
                    <?php $main_name = $class['main_name']; ?>
                    <?php } ?>
                    <li>
                    <?php if ($class['id'] == $class_id) {
                     $enable = strtolower(str_replace(' ', '_', $class['name'])) . '-' . $class['id']; 
                     } ?>
                      <a href="javascript:void(0)">
                        <input type="checkbox" class="css-checkbox selectClass <?php echo strtolower(str_replace(' ', '_', $class['main_name']));?>" checked="checked" value="<?php echo $class['id']; ?>" id="<?php echo strtolower(str_replace(' ', '_', $class['name'])) . '-' . $class['id']; ?>">

                        <label for="<?php echo strtolower(str_replace(' ', '_', $class['name'])) . '-' . $class['id']; ?>" class="css-label3"><?php echo $class['name']; ?></label>
                      </a>
                      <ul class="subfilter" data-filter-name="<?php echo $class['name']; ?>">
                        <input type="hidden" readonly id="checker<?php echo $class['id']; ?>" value="<?php echo $class['main_name']; ?>">
                      </ul>
                    </li>
                    <?php if ($classes[($x + 1)]['main_name'] != $class['main_name']) { ?>
                  </ul>
                </div>
              </div>
              <?php } ?>
            <?php endforeach; ?>
            
            <?php } ?>
          </div>       
        </div>  
      </div>
      <div class="col-md-12 right-content">
        <div class="filter-buttons row hidden">
          <div class="col-md-1 col-sm-2 filter-buttons-left">
            <label>Filter:</label>
          </div>
          <div class="col-md-11 col-sm-10 filter-buttons-right">
            <ul class="list-inline">
            </ul>
            <a href="javascript:void(0);" class="btn btn-primary yellow-btn clear-filter" style="display: none;">Clear All</a>
          </div>
        </div>
        <ul class="nav nav-tabs">
        <?php if ($module == 'repair_parts') { ?>
          <li class="active"><a class="loadProducts" data-contid="repairpartsid" data-name="Replacement Parts" href="<?php echo $endBC['href']; ?>#repairpartsid">Repair Parts</a></li>
        <?php } ?>
        <?php if ($module == 'repair_parts' || $module == 'repair_tools') { ?>
          <li <?php echo ($module == 'repair_tools')? 'class="active"': ''; ?>><a class="loadProducts" data-name="Repair Tools" data-contid="repairtoolsid" href="<?php echo $endBC['href']; ?>#repairtoolsid">Repair Tools</a></li>
        <?php } ?>
        <?php if ($module == 'repair_parts' || $module == 'accessories') { ?>
          <li <?php echo ($module == 'accessories')? 'class="active"': ''; ?>><a class="loadProducts" data-name="Accessories" data-contid="accessoriesid" href="<?php echo $endBC['href']; ?>#accessoriesid">Accessories</a></li>
        <?php } ?>
        <?php if ($module == 'temperedglass') { ?>
          <li <?php echo ($module == 'temperedglass')? 'class="active"': ''; ?>><a class="loadProducts" data-name="Tempered Glass" data-contid="temperedglassid" href="<?php echo $endBC['href']; ?>#temperedglass">Accessories</a></li>
        <?php } ?>
        </ul>
        <div class="tab-content">
          <div id="<?php echo str_replace('_', '', $module); ?>id" class="fade in active">
            <div class="row listing-row">
				<style type="text/css">
				.filter-inner a,.filter-inner a:active,.filter-inner a:visited { color: #4986fe; }
				.filter-inner a.active { font-weight: bold; }
				
				</style>
				<div class="filter-inner">
				<h3>Your search returned <?php echo $product_total; ?> products</h3>
				<p>You can further refine your results by selecting</p>
				<font style="font-weight: bold">Device Manufacturer :&nbsp;&nbsp;&nbsp;</font>
				<?php foreach ($manufacturers as $mfg) { ?>
					<a href="<?php echo $this->url->link('wx/search', $url . "&filter_manufacturer=" . $mfg['manufacturer_id']); ?>"><?php echo $mfg['name']; ?></a>
				<?php } ?>
				<br /><font style="font-weight: bold">Device Make :&nbsp;&nbsp;&nbsp;</font>
				<?php 
				$first = true;
				foreach ($models as $mod) { 
					if (!$first) echo ", "; ?><a class="link" href="<?php echo $this->url->link('wx/search', $url . "&filter_device_id=" . $mod['model_id']); ?>"><?php echo $mod['device']; ?></a>
				<?php 
					$first = false;
				} 
				?>
				</div>
			</div>
		  </div>
		</div>
        <div class="tab-content" style="margin-top:20px;">
          <div id="<?php echo str_replace('_', '', $module); ?>id" class="tab-pane fade in active">
            <div class="row listing-row">
              <?php foreach ($products as $product) : ?>
                <div class="col-md-2 listing-items product_<?php echo $product['product_id']; ?>">
                  <article class="related-product">
                    <div class="image">
                      <img class="lazy" data-original="<?php echo $product['thumb']; ?>" src="catalog/view/theme/ppusa2.0/images/spinner2.gif" height="150" width="150" alt="<?php echo $product['name']; ?>">
                    </div>
                    <h4 style="height: 80px"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
                    
                    <?php
          if((int)$product['quantity']>0)
            {
              $in_cart= (isset($this->session->data['cart'][$product['product_id']])?true:false);
              ?>
                    <div class="qtyt-box">
                      <div class="input-group spinner">
                        <span class="txt">QTY</span>
                        <input type="text" class="form-control qty" value="1" style="color:#303030">
                        <div class="input-group-btn-vertical">
                          <button class="btn" type="button"><i class="fa fa-plus"></i></button>
                          <button class="btn" type="button"><i class="fa fa-minus"></i></button>
                        </div>
                      </div>
                    </div>
                    
                   <?php if($product['sale_price']){ ?>
                    
                    <p class="price"><span style="font-size: 13px;margin-right:5px;text-decoration:line-through;"><?php echo $product['price']; ?></span><span style="color: red;"><?php echo $product['sale_price']; ?></span></p>
                    <?php } else {?>
                    <p class="price"><span><?php echo $product['price']; ?></span></p>
                    <?php } ?>
                    <button onclick="addToCartpp2(<?php echo $product['product_id']; ?>, $(this).parent().find('.qty').val())" class="btn <?php echo ($in_cart?'btn-success2':'btn-info');?>"><?php echo ($in_cart?'In Cart ('.$this->session->data['cart'][$product['product_id']].')':'Add to cart');?></button>
                    <?php
                 }else
                  {
                    ?>
                    <div class="qtyt-box">
                      <div class="input-group spinner">
                        <span class="txt">QTY</span>
                        <input type="text" class="form-control qty" disabled="" value="1" style="color:#303030">
                        <div class="input-group-btn-vertical">
                          <button class="btn" type="button" disabled><i class="fa fa-plus"></i></button>
                          <button class="btn" type="button" disabled><i class="fa fa-minus"></i></button>
                        </div>
                      </div>
                    </div>
                    <!--   <div >
      <span class="oos_qty_error_<?php echo $product['product_id'];?>" style="font-size:11px;color:red"></span>
      <input type="text" class="form-control customer_email_<?php echo $product['product_id'] ?>" style="margin-bottom:48px" placeholder="Enter your Email" value="<?php echo $this->customer->getEmail();?>">
  </div> -->
  <?php if($product['sale_price']){ ?>
                    
                    <p class="price"><span style="font-size: 13px;margin-right:5px;text-decoration:line-through;"><?php echo $product['price']; ?></span><span style="color: red;"><?php echo $product['sale_price']; ?></span></p>
                    <?php } else {?>
                    <p class="price"><span><?php echo $product['price']; ?></span></p>
                    <?php } ?>
  <button class="btn btn-danger" id="notify_btn_<?php echo $product['product_id'];?>" >Out of Stock</button>
                    <?php
                  }
                  ?>
                  </article>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php if ($module == 'repair_parts') { ?>
          <div id="repairtoolsid" class="tab-pane fade">
            <div align="center">
            <img src="./imp/images/loading.gif" style="width: 50px;">
            </div>
          </div>
          <div id="accessoriesid" class="tab-pane fade">
          <div align="center">
            <img src="./imp/images/loading.gif" style="width: 50px;">
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</main>
<input type="hidden" id="manufacturer_id" value="<?php echo $manufacturer_id; ?>">
<input type="hidden" id="device_id" value="<?php echo $device_id; ?>">
<input type="hidden" id="main_class_id" value="<?php echo $main_class_id; ?>">
<?php echo $footer; ?>
<script type="text/javascript">
$('#checkAll').change(function(event) {
   if($('#checkAll').prop("checked")){
    $('.submodel').prop("checked",true);
    loadProducts();
   } else {
    $('.submodel').prop("checked",false);
    loadProducts();
   }
});

$('#selectAllreplacement_parts').change(function(event) {
   if($('#selectAllreplacement_parts').prop("checked")){
    $('.replacement_parts').prop("checked",true);
    loadProducts();
   } else {
    $('.replacement_parts').prop("checked",false);
    loadProducts();
   }
});
$('#selectAllrepair_tools').change(function(event) {
   if($('#selectAllrepair_tools').prop("checked")){
    $('.repair_tools').prop("checked",true);
    loadProducts();
   } else {
    $('.repair_tools').prop("checked",false);
    loadProducts();
   }
});
$('#selectAllscreen_protectors').change(function(event) {
   if($('#selectAllscreen_protectors').prop("checked")){
    $('.screen_protectors').prop("checked",true);
    loadProducts();
   } else {
    $('.screen_protectors').prop("checked",false);
    loadProducts();
   }
});
$('#selectAllaccessories').change(function(event) {
   if($('#selectAllaccessories').prop("checked")){
    $('.accessories').prop("checked",true);
    loadProducts();
   } else {
    $('.accessories').prop("checked",false);
    loadProducts();
   }
});

  $('.loadProducts').click(function(event) {
    $('.clear-filter').click();
    var class_name = $(this).data('name');
    var cont = $(this).data('contid');
    var filter = getFilter();
    $.ajax({
      url: '?route=product/search/loadFilterProducts',
      type: 'POST',
      dataType: 'json',
       data: {filter: filter, class_name: class_name,filter_name:encodeURIComponent('<?php echo $filter_name;?>')},
       beforeSend: function() {
                $('#' + cont + ' .listing-row').html('<img src="catalog/view/theme/ppusa2.0/images/spinner.gif">');
            }
    })
    .always(function(json) {
      $('#' + cont+' .listing-row').html(json['products']);
      var classes = '';
      if (json['classes']) {
        var main_name = '';
        var loop = json['classes'].length;
        for (var i = 0; i < loop; i++) {
          if (json['classes'][i].main_name != main_name) {
            classes += '<div class="panel update-filters">';
            classes += '<div class="panel-heading">';
            classes += '<a data-toggle="collapse" data-parent="#accordion" href="#'+ json['classes'][i].main_name.split(' ').join('_') +'">';
            classes += json['classes'][i].main_name +'<i class="fa fa-angle-down"></i>';
            classes += '</a>';
            classes += '</div>';
            classes += '<div id="' + json['classes'][i].main_name.split(' ').join('_') + '" class="panel-collapse collapse">';
            classes += '<ul class="filter-check">';

            main_name = json['classes'][i].main_name;
          }
          
          classes += '<li>';
          classes += '<a href="javascript:void(0);">';
          classes += '<input type="checkbox" class="css-checkbox selectClass" value="'+ json['classes'][i].id +'" id="'+ json['classes'][i].name.split(' ').join('_') + '-' + json['classes'][i].id +'">';
          classes += '<label for="'+ json['classes'][i].name.split(' ').join('_') + '-' + json['classes'][i].id +'" class="css-label">'+ json['classes'][i].name +'</label>';
          classes += '</a>';
          classes += '<ul class="subfilter" data-filter-name="'+ json['classes'][i].name +'">';
          classes += '</ul>';
          classes += '</li>';
          var x = i + 1;
          if (typeof json['classes'][x] == 'undefined' || json['classes'][x].main_name != json['classes'][i].main_name) {           
            classes += '</ul>';
            classes += '</div>';
            classes += '</div>';
          }
        }
      }
      $('.update-filters').remove();
      $('.filter-group').append(classes);
      $('#main_class_id').val(json['main_class_id']);
    });

  });
  function getFilter () {
    class_id = [];
    $('.selectClass').each(function() {
      if ($(this).is(':checked')) {
        class_id.push($(this).val());
      }
    });

    var attrib = {};
    $('.selectClass').each(function(index, el) {
      if ($(el).is(':checked')) {
        var cID = $(el).val();
        attrib['c'+cID] = [];
        $(el).parent().next('.subfilter').find('.attrid').each(function(index, atEl) {
          if ($(atEl).is(':checked')) {
            attrib['c'+cID].push($(atEl).val());
          }
        });

      }
    });

    var model_id = [];
    $('.submodel').each(function() {
      if ($(this).is(':checked')) {
        model_id.push($(this).val());
      }
    });

    var filter = {};
    filter['class_id'] = class_id;
    filter['manufacturer_id'] = $('#manufacturer_id').val();
    filter['device_id'] = $('#device_id').val();
    filter['model_id'] = model_id.toString();
    // filter['main_class_id'] = $('#main_class_id').val();
    filter['attrib_id'] = attrib;
    // filter['group_id'] = $('#priceGroup').val();
    return filter;
  }
  $(document).ready(function() {
    $('.filter-group').on('change', '.selectClass', function() {
      var subfilter = $(this).parent().next('.subfilter');
      var class_id = $(this).val();
      if (subfilter.text() == '') {
        var filter = getFilter();
        $.ajax({
          url: '?route=catalog/<?php echo $module; ?>/loadAttributes',
          type: 'POST',
          dataType: 'json',
          data: {filter: filter, class: class_id}
        }).always(function(json) {
          var attributes = '';
          var main_name = '';

          if (json['attributes']) {
            var loop = json['attributes'].length;
            for (var i = 0; i < loop; i++) {
              if (json['attributes'][i].main_name != main_name) {
                attributes += '<li>';
                attributes += '<div class="panel-heading">';
                attributes += '<br><a>';
                attributes += json['attributes'][i].main_name;
                attributes += '</a>';
                attributes += '</div>';
                main_name = json['attributes'][i].main_name;
              }
              attributes += '<li>';
              attributes += '<a href="javascript:void(0)">';
              attributes += '<input type="checkbox" checked="checked" value="'+ json['attributes'][i].id +'" class="css-checkbox attrid" id="'+ class_id + '-' + json['attributes'][i].id +'">';
              attributes += '<label for="'+ class_id + '-' + json['attributes'][i].id +'" class="css-label">'+ json['attributes'][i].name +'</label>';
              attributes += '</a>';
              attributes += '</li>';
            }
          }
          subfilter.html(attributes);
          $('.filter-check input[type=checkbox].attrid').trigger('change');
          loadProducts();
        });
      } else {
        console.log('here');
        subfilter.find('.attrid').trigger('click');
        // $('.filter-check input[type=checkbox].attrid').trigger('change');
        loadProducts();
      }
    });
    $('.filter-group').on('click', '.submodel', function() {
      loadProducts();
    });
    $('.filter-group').on('click', '.attrid', function() {
      loadProducts();
    });
  });
  function loadProducts () {
    var count = window.location.hash.substring(1);
    if (!count) {
      count = '<?php echo str_replace('_', '', $module); ?>id';
    }
    console.log(count);
    var class_name = $('.nav-tabs').find('.active').find('a').data('name');
    var cont = $(this).data('contid');
    var filter = getFilter();
    $.ajax({
      url: '?route=product/search/loadFilterProducts',
      type: 'POST',
      dataType: 'json',
      data: {filter: filter, class_name: class_name,filter_name:encodeURIComponent('<?php echo $filter_name;?>')},
      beforeSend: function() {
                $('#' + count+' .listing-row').html('<img src="catalog/view/theme/ppusa2.0/images/spinner.gif">');
            },
    }).always(function(json) {
      $('#' + count + ' .listing-row').html(json['products']);
    });
  }
  <?php if ($enable && isset($enable)) { ?>
    $(document).ready(function($) {
      $('#<?php echo $enable; ?>').trigger('click');
    });
  <?php } ?>

  <?php if (isset($this->request->get['class_name'])) { ?>
    $(document).ready(function($) {
      $('[data-name="<?php echo $this->request->get['class_name']; ?>"]').trigger('click');
      $('#submodel<?php echo $this->request->get['submodel']; ?>').trigger('click');
    });
  <?php } ?>
</script>
<!-- @End of main -->