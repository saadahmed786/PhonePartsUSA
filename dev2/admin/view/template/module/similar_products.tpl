<?php echo $header; ?>
<div class="modal fade" id="legal_text" tabindex="-1" role="dialog" aria-labelledby="legal_text_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="legal_text_label"><?php echo $text_terms; ?></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default cancel" data-dismiss="modal"><i class="fa fa-times"></i> <?php echo $button_close; ?></button>
      </div>
    </div>
  </div>
</div>

<div id="content" class="main-content">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li<?php echo ($breadcrumb['active']) ? ' class="active"' : ''; ?>><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>

  <div class="alerts">
    <div class="container" id="alerts">
      <?php foreach ($alerts as $type => $_alerts) { ?>
        <?php foreach ((array)$_alerts as $alert) { ?>
          <?php if ($alert) { ?>
      <div class="alert alert-<?php echo ($type == "error") ? "danger" : $type; ?> fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo $alert; ?>
      </div>
          <?php } ?>
        <?php } ?>
      <?php } ?>
    </div>
  </div>

  <div class="navbar-placeholder">
    <nav class="navbar navbar-default" role="navigation" id="bull5i-navbar">
      <div class="nav-container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
            <span class="sr-only"><?php echo $text_toggle_navigation; ?></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <span class="navbar-brand"><i class="fa fa-files-o fa-fw ext-icon"></i> <?php echo $heading_title; ?></span>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#settings" data-toggle="tab"><?php echo $tab_settings; ?></a></li>
            <li><a href="#modules" data-toggle="tab"><?php echo $tab_modules; ?></a></li>
            <li><a href="#support" data-toggle="tab"><?php echo $tab_support; ?></a></li>
            <li><a href="#about" data-toggle="tab"><?php echo $tab_about; ?></a></li>
          </ul>
          <div class="nav navbar-nav btn-group navbar-btn navbar-right">
            <?php if ($update_pending) { ?><button type="button" class="btn btn-primary" id="upgrade" action="<?php echo $upgrade; ?>"><i class="fa fa-arrow-circle-up"></i> <?php echo $button_upgrade; ?></button><?php } ?>
            <button type="button" class="btn btn-default" id="apply" action="<?php echo $save; ?>"<?php echo $update_pending ? ' disabled': ''; ?>><?php echo $button_apply; ?></button>
            <button type="button" class="btn btn-default" id="save" action="<?php echo $save; ?>"<?php echo $update_pending ? ' disabled': ''; ?>><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
            <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-ban"></i> <?php echo $button_cancel; ?></a>
          </div>
        </div>
      </div>
    </nav>
  </div>

  <div class="bull5i-content bull5i-container">
    <div id="page-overlay" class="bull5i-overlay fade">
      <div class="page-overlay-progress"><i class="fa fa-refresh fa-spin fa-5x text-muted"></i></div>
    </div>

    <form action="<?php echo $save; ?>" method="post" enctype="multipart/form-data" id="sForm" class="form-horizontal" role="form">
      <div class="tab-content">
        <div class="tab-pane active" id="settings">
          <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-cog fa-fw"></i> <?php echo $tab_settings; ?></h3></div>
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-12">
                  <fieldset>
                    <div class="form-group">
                      <label class="col-sm-3 col-md-2 control-label" for="sp_status"><?php echo $entry_extension_status; ?></label>
                      <div class="col-sm-2 fc-auto-width">
                        <select name="sp_status" id="sp_status" class="form-control">
                          <option value="1"<?php echo ((int)$sp_status) ? ' selected' : ''; ?>><?php echo $text_enabled; ?></option>
                          <option value="0"<?php echo (!(int)$sp_status) ? ' selected' : ''; ?>><?php echo $text_disabled; ?></option>
                        </select>
                        <input type="hidden" name="sp_installed" value="1" />
                        <input type="hidden" name="sp_installed_version" value="<?php echo $installed_version; ?>" />
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 col-md-2 control-label" for="sp_remove_sql_changes0"><?php echo $entry_remove_sql_changes; ?></label>
                      <div class="col-sm-9 col-md-10">
                        <label class="radio-inline">
                          <input type="radio" name="sp_remove_sql_changes" id="sp_remove_sql_changes1" value="1"<?php echo ((int)$sp_remove_sql_changes) ? ' checked' : ''; ?>> <?php echo $text_yes; ?>
                        </label>
                        <label class="radio-inline">
                          <input type="radio" name="sp_remove_sql_changes" id="sp_remove_sql_changes0" value="0"<?php echo (!(int)$sp_remove_sql_changes) ? ' checked' : ''; ?>> <?php echo $text_no; ?>
                        </label>
                      </div>
                      <div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 help-container">
                        <span class="help-block help-text"><?php echo $help_remove_sql_changes; ?></span>
                      </div>
                    </div>
                  </fieldset>
                  <fieldset id="sp-mass-change">
                    <legend><?php echo $text_change_product_settings; ?></legend>
                    <div class="row">
                      <div class="col-sm-12 help-container">
                        <span class="help-block help-text"><?php echo $help_change_product_settings; ?></span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 col-md-2 control-label" for="sp_auto_select"><?php echo $entry_auto_select; ?></label>
                      <div class="col-sm-2 fc-auto-width">
                        <select name="sp_auto_select" id="sp_auto_select" data-bind="value: auto_select" class="form-control">
                          <option value="0"><?php echo $text_off; ?></option>
                          <option value="1"><?php echo $text_category; ?></option>
                          <option value="2"><?php echo $text_name_fragment; ?></option>
                          <option value="3"><?php echo $text_model_fragment; ?></option>
                          <option value="4"><?php echo $text_name_custom_string; ?></option>
                          <option value="5"><?php echo $text_model_custom_string; ?></option>
                          <option value="6"><?php echo $text_tags; ?></option>
                        </select>
                      </div>
                      <div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 help-container">
                        <span class="help-block help-text"><?php echo $help_auto_select; ?></span>
                      </div>
                    </div>
                    <!-- ko if: auto_select() == '1' -->
                    <div class="form-group">
                      <label class="col-sm-3 col-md-2 control-label" for="sp_leaves_only"><?php echo $entry_leaves_only; ?></label>
                      <div class="col-sm-2 fc-auto-width">
                        <select name="sp_leaves_only" id="sp_leaves_only" data-bind="value: leaves_only" class="form-control">
                          <option value="1"><?php echo $text_yes; ?></option>
                          <option value="0"><?php echo $text_no; ?></option>
                        </select>
                      </div>
                      <div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 help-container">
                        <span class="help-block help-text"><?php echo $help_leaves_only; ?></span>
                      </div>
                    </div>
                    <!-- /ko -->
                    <!-- ko if: auto_select() != '1' -->
                    <input type="hidden" name="sp_leaves_only" data-bind="value: leaves_only">
                    <!-- /ko -->
                    <!-- ko if: auto_select() == '2' || auto_select() == '3' -->
                    <div class="form-group">
                      <label class="col-sm-3 col-md-2 control-label" for="sp_substr_start"><?php echo $entry_substr_start; ?></label>
                      <div class="col-sm-2 col-md-1">
                        <input name="sp_substr_start" id="sp_substr_start" data-bind="value: substr_start" class="form-control text-right">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 col-md-2 control-label" for="sp_substr_length"><?php echo $entry_substr_length; ?></label>
                      <div class="col-sm-2 col-md-1">
                        <input name="sp_substr_length" id="sp_substr_length" data-bind="value: substr_length" class="form-control text-right">
                      </div>
                      <div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 help-container">
                        <span class="help-block help-text"><?php echo $help_name_fragment; ?></span>
                      </div>
                    </div>
                    <!-- /ko -->
                    <!-- ko if: auto_select() != '2' && auto_select() != '3' -->
                    <input type="hidden" name="sp_substr_start" data-bind="value: substr_start">
                    <input type="hidden" name="sp_substr_length" data-bind="value: substr_length">
                    <!-- /ko -->
                    <!-- ko if: auto_select() == '4' || auto_select() == '5' -->
                    <div class="form-group">
                      <label class="col-sm-3 col-md-2 control-label" for="sp_custom_string"><?php echo $entry_custom_string; ?></label>
                      <div class="col-sm-4 col-md-3">
                        <input name="sp_custom_string" id="sp_custom_string" data-bind="value: custom_string" class="form-control">
                      </div>
                      <div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 help-container">
                        <span class="help-block help-text"><?php echo $help_custom_string; ?></span>
                      </div>
                    </div>
                    <!-- /ko -->
                    <!-- ko if: auto_select() != '2' && auto_select() != '3' -->
                    <input type="hidden" name="sp_custom_string" data-bind="value: custom_string">
                    <!-- /ko -->
                    <div class="form-group">
                      <label class="col-sm-3 col-md-2 control-label" for="sp_product_sort_order"><?php echo $entry_product_sort_order; ?></label>
                      <div class="col-sm-2 fc-auto-width">
                        <select name="sp_product_sort_order" id="sp_product_sort_order" data-bind="value: product_sort_order" class="form-control">
                          <option value="0"><?php echo $text_sort_order; ?></option>
                          <option value="1"><?php echo $text_model; ?></option>
                          <option value="2"><?php echo $text_name; ?></option>
                          <option value="3"><?php echo $text_quantity; ?></option>
                          <option value="4"><?php echo $text_most_viewed; ?></option>
                          <option value="5"><?php echo $text_date_added; ?></option>
                          <option value="6"><?php echo $text_date_modified; ?></option>
                          <option value="7"><?php echo $text_random; ?></option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 col-md-2 control-label" for="apply_to_null"><?php echo $entry_products; ?></label>
                      <div class="col-sm-9 col-md-10">
                        <label class="radio">
                          <input type="radio" value="" name="sp_apply_to[products]" id="apply_to_null" data-bind="checked: products"> <?php echo $text_no_products; ?>
                        </label>
                        <label class="radio">
                          <input type="radio" value="0" name="sp_apply_to[products]" id="apply_to_all" data-bind="checked: products"> <?php echo $text_all_products; ?>
                        </label>
                        <label class="radio">
                          <input type="radio" value="1" name="sp_apply_to[products]" id="apply_to_category" data-bind="checked: products"> <?php echo $text_all_category_products; ?>
                        </label>
                        <div class="row">
                          <div class="col-sm-12">
                            <select name="sp_apply_to[category]" data-bind="disable: products() != '1', value: category" class="form-control fc-auto-width">
                              <?php foreach ($categories as $category) { ?>
                              <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <label class="radio">
                          <input type="radio" value="2" name="sp_apply_to[products]" id="sp_apply_to_selected" data-bind="checked: products"> <?php echo $text_selected_products; ?>
                        </label>
                        <div class="row">
                          <div class="col-sm-6 col-md-5 col-lg-4">
                            <input class="form-control typeahead product" placeholder="<?php echo $text_autocomplete; ?>" autocomplete="off" data-method="addSelected">
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-sm-12 col-md-8 col-lg-6 multi-row">
                            <div class="scroll-box" data-bind="css: {disabled: products() != '2'}">
                            <!-- ko foreach: selected -->
                              <div><!-- ko text: name --><!-- /ko -->
                                <button type="button" data-bind="click: $parent.removeSelected" class="btn btn-link btn-xs pull-right" rel="tooltip" data-original-title="<?php echo $text_remove; ?>"><i class="fa fa-ban text-danger"></i></button>
                                <input type="hidden" data-bind="attr: {name: 'sp_apply_to[selected][' + $index() + '][product_id]'}, value: id" />
                                <input type="hidden" data-bind="attr: {name: 'sp_apply_to[selected][' + $index() + '][name]'}, value: name" />
                                <input type="hidden" data-bind="attr: {name: 'sp_apply_to[selected][' + $index() + '][model]'}, value: model" />
                              </div>
                            <!-- /ko -->
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane" id="modules">
          <div class="panel panel-default" id="sp-modules">
            <div class="panel-heading">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#modules-navbar-collapse">
                  <span class="sr-only"><?php echo $text_toggle_navigation; ?></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <h3 class="panel-title"><i class="fa fa-puzzle-piece fa-fw"></i> <?php echo $tab_modules; ?></h3>
              </div>
              <div class="collapse navbar-collapse" id="modules-navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                  <li><button type="button" class="btn btn-primary add-module"><i class="fa fa-plus"></i> <?php echo $button_add_module; ?></button></li>
                </ul>
              </div>
            </div>
            <!-- ko if: modules().length == 0 -->
            <div class="panel-body">
              <p><?php echo $text_no_modules; ?></p>
            </div>
            <!-- /ko -->
            <ul class="list-group">
              <!-- ko foreach: modules -->
              <li class="list-group-item" data-bind="css: {'list-group-item-disabled': !parseInt(status()) }">
                <fieldset>
                  <div class="row">
                    <div class="col-sm-6 col-md-4 col-lg-3">
                      <div class="form-group no-margin">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $parent.index + '_name<?php echo $this->config->get('config_language_id'); ?>'}, css: {'has-error': names.hasError}"><?php echo $entry_name; ?></label>
                        <!-- ko foreach: names -->
                        <div data-bind="css: {'multi-row': $index() != 0, 'has-error': name.hasError}">
                          <div class="input-group">
                            <input data-bind="attr: {name: 'similar_products_module[' + $parentContext.$index() + '][names][' + language_id() + ']', id: 'sp_' + $parentContext.$index() + '_name' + language_id()}, value: name" class="form-control">
                            <span class="input-group-addon" data-bind="attr: {title: $root.languages[language_id()].name}"><img data-bind="attr: {src: $root.languages[language_id()].flag, title: $root.languages[language_id()].name}" /></span>
                          </div>
                        </div>
                        <div class="has-error" data-bind="visible: name.hasError">
                          <span class="help-block" data-bind="text: name.errorMsg"></span>
                        </div>
                        <!-- /ko -->
                      </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-2">
                      <div class="form-group no-margin">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_layout'}"><?php echo $entry_layout; ?></label>
                        <select data-bind="attr: {name: 'similar_products_module[' + $index() + '][layout_id]', id: 'sp_' + $index() + '_layout'}, value: layout_id" class="form-control">
                          <?php foreach ($layouts as $layout_id => $layout) { ?>
                          <option value="<?php echo $layout_id; ?>"><?php echo $layout['name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-2">
                      <div class="form-group no-margin">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_position'}"><?php echo $entry_position; ?></label>
                        <select data-bind="attr: {name: 'similar_products_module[' + $index() + '][position]', id: 'sp_' + $index() + '_position'}, value: position" class="form-control">
                          <!-- ko if: !parent.tabPositionUsed() || position() == 'content_tab' -->
                          <option value="content_tab"><?php echo $text_content_tab; ?></option>
                          <!-- /ko -->
                          <option value="content_top"><?php echo $text_content_top; ?></option>
                          <option value="content_bottom"><?php echo $text_content_bottom; ?></option>
                          <option value="column_left"><?php echo $text_column_left; ?></option>
                          <option value="column_right"><?php echo $text_column_right; ?></option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-2">
                      <div class="form-group no-margin">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_per_page'}"><?php echo $entry_products_per_page; ?></label>
                        <input data-bind="attr: {name: 'similar_products_module[' + $index() + '][products_per_page]', id: 'sp_' + $index() + '_per_page'}, value: products_per_page" class="form-control text-right">
                      </div>
                    </div>
                    <div class="col-sm-2 col-md-1 col-lg-1">
                      <div class="form-group no-margin">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_limit'}"><?php echo $entry_limit; ?></label>
                        <input data-bind="attr: {name: 'similar_products_module[' + $index() + '][limit]', id: 'sp_' + $index() + '_limit'}, value: limit" class="form-control text-right">
                      </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-2">
                      <div class="form-group no-margin">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_sort_order'}"><?php echo $entry_module_sort_order; ?></label>
                        <input data-bind="attr: {name: 'similar_products_module[' + $index() + '][sort_order]', id: 'sp_' + $index() + '_sort_order'}, value: sort_order, disable: position() == 'content_tab'" class="form-control text-right">
                        <!-- ko if: position() == 'content_tab' -->
                        <input type="hidden" data-bind="attr: {name: 'similar_products_module[' + $index() + '][sort_order]'}, value: sort_order">
                        <!-- /ko -->
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-3 col-md-2 col-lg-2">
                      <div class="form-group no-margin" data-bind="css: {'has-error': image_width.hasError}">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_image_width'}"><?php echo $entry_image_width; ?></label>
                        <input data-bind="attr: {name: 'similar_products_module[' + $index() + '][image_width]', id: 'sp_' + $index() + '_image_width'}, value: image_width" class="form-control text-right">
                        <div class="has-error" data-bind="visible: image_width.hasError">
                          <span class="help-block" data-bind="text: image_width.errorMsg"></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-3 col-md-2 col-lg-2">
                      <div class="form-group no-margin" data-bind="css: {'has-error': image_height.hasError}">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_image_height'}"><?php echo $entry_image_height; ?></label>
                        <input data-bind="attr: {name: 'similar_products_module[' + $index() + '][image_height]', id: 'sp_' + $index() + '_image_height'}, value: image_height" class="form-control text-right">
                        <div class="has-error" data-bind="visible: image_height.hasError">
                          <span class="help-block" data-bind="text: image_height.errorMsg"></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <div class="form-group no-margin">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_stock_only'}"><?php echo $entry_stock_only; ?> <i class="fa fa-question-circle text-info" rel="tooltip" data-original-title="<?php echo $help_stock_only; ?>"></i></label>
                        <select data-bind="attr: {name: 'similar_products_module[' + $index() + '][stock_only]', id: 'sp_' + $index() + '_stock_only'}, value: stock_only" class="form-control">
                          <option value="1"><?php echo $text_yes; ?></option>
                          <option value="0"><?php echo $text_no; ?></option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <div class="form-group no-margin">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_lazy_load'}"><?php echo $entry_lazy_load; ?> <i class="fa fa-question-circle text-info" rel="tooltip" data-original-title="<?php echo $help_lazy_load; ?>"></i></label>
                        <select data-bind="attr: {name: 'similar_products_module[' + $index() + '][lazy_load]', id: 'sp_' + $index() + '_lazy_load'}, value: lazy_load" class="form-control">
                          <option value="1"><?php echo $text_yes; ?></option>
                          <option value="0"><?php echo $text_no; ?></option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3 col-md-2 col-lg-2">
                      <div class="form-group no-margin">
                        <label class="control-label" data-bind="attr: {for: 'sp_' + $index() + '_status'}"><?php echo $entry_status; ?></label>
                        <select data-bind="attr: {name: 'similar_products_module[' + $index() + '][status]', id: 'sp_' + $index() + '_status'}, value: status" class="form-control">
                          <option value="1"><?php echo $text_enabled; ?></option>
                          <option value="0"><?php echo $text_disabled; ?></option>
                        </select>
                        <input type="hidden" data-bind="attr: {name: 'similar_products_module[' + $index() + '][index]'}, value: $index()">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <button type="button" class="btn btn-warning remove-module pull-right"><i class="fa fa-minus"></i> <?php echo $button_remove; ?></button>
                    </div>
                  </div>
                </fieldset>
              </li>
              <!-- /ko -->
            </ul>
          </div>
        </div>
        <div class="tab-pane" id="support">
          <div class="panel panel-default">
            <div class="panel-heading">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#support-navbar-collapse">
                  <span class="sr-only"><?php echo $text_toggle_navigation; ?></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <h3 class="panel-title"><i class="fa fa-phone fa-fw"></i> <?php echo $tab_support; ?></h3>
              </div>
              <div class="collapse navbar-collapse" id="support-navbar-collapse">
                <ul class="nav navbar-nav">
                  <li class="active"><a href="#general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                  <li><a href="#faq" data-toggle="tab" title="<?php echo $text_faq; ?>"><?php echo $tab_faq; ?></a></li>
                  <li><a href="#services" data-toggle="tab"><?php echo $tab_services; ?></a></li>
                </ul>
              </div>
            </div>
            <div class="panel-body">
              <div class="tab-content">
                <div class="tab-pane active" id="general">
                  <div class="row">
                    <div class="col-sm-12">
                      <h3>Getting support</h3>
                      <p>I consider support a priority of mine, so if you need any help with your purchase you can contact me in one of the following ways:</p>
                      <ul>
                        <li>Send an email to <a href="mailto:<?php echo $ext_support_email; ?>?subject='<?php echo $text_support_subject; ?>'"><?php echo $ext_support_email; ?></a></li>
                        <li>Post in the <a href="<?php echo $ext_support_forum; ?>" target="_blank">extension forum thread</a> or send me a <a href="http://forum.opencart.com/ucp.php?i=pm&mode=compose&u=17771">private message</a></li>
                        <li><a href="<?php echo $ext_store_url; ?>" target="_blank">Leave a comment</a> in the extension store comments section</li>
                      </ul>
                      <p>I usually reply within a few hours, but can take up to 24 hours.</p>
                      <p>Please note that all support is free if it is an issue with the product. Only issues due conflicts with other third party extensions/modules or custom front end theme are the exception to free support. Resolving such conflicts, customizing the extension or doing additional bespoke work will be provided with the hourly rate of <span id="hourly_rate">USD 50 / EUR 40</span>.</p>

                      <h4>Things to note when asking for help</h4>
                      <p>Please describe your problem in as much detail as possible. When contacting, please provide the following information:</p>
                      <ul>
                        <li>The OpenCart version you are using. <small>This can be found at the bottom of any admin page.</small></li>
                        <li>The extension name and version. <small>You can find this information under the About tab.</small></li>
                        <li>If you got any error messages, please include them in the message.</li>
                        <li>In case the error message is generated by a vQmod cached file, please also attach that file.</li>
                      </ul>
                      <p>Any additional information that you can provide about the issue is greatly appreciated and will make problem solving much faster.</p>

                      <h3 class="page-header">Happy with <?php echo $ext_name; ?>?</h3>
                      <p>I would appreciate it very much if you could <a href="<?php echo $ext_store_url; ?>" target="_blank">rate the extension</a> once you've had a chance to try it out. Why not tell everybody how great this extension is by <a href="<?php echo $ext_store_url; ?>" target="_blank">leaving a comment</a> as well.</p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="alert alert-info">
                        <p><?php echo $text_other_extensions; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="faq">
                  <h3><?php echo $text_faq; ?></h3>
                  <ul class="media-list" id="faqs">
                    <li class="media">
                      <div class="pull-left">
                        <i class="fa fa-question-circle fa-4x media-object"></i>
                      </div>
                      <div class="media-body">
                        <h4 class="media-heading">How to translate the extension to another language?</h4>

                        <p class="short-answer">Copy the extension language files <em>admin/language/english/module/similar_products.php</em> and <em>catalog/language/english/module/similar_products.php</em> to your language folder and translate the string inside the copied files. Additionally translate the language strings found in the <em>vqmod/xml/similar_products.xml</em> vQmod script file.</p>

                        <button type="button" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#translation" data-parent="#faqs">Show the full answer</button>
                        <div class="collapse full-answer" id="translation">
                          <h5 class="text-muted no-top-margin">Translating <strong>admin</strong> panel module files</h5>
                          <ol>
                            <li>
                              <p><strong>Copy</strong> the following language file <strong>to YOUR_LANGUAGE folder</strong> under the appropriate location as shown below:</p>
                              <div class="btm-mgn">
                                <em class="text-muted"><small>FROM:</small></em>
                                <ul class="list-unstyled">
                                  <li>admin/language/english/module/similar_products.php</li>
                                </ul>
                                <em class="text-muted"><small>TO:</small></em>
                                <ul class="list-unstyled">
                                  <li>admin/language/YOUR_LANGUAGE/module/similar_products.php</li>
                                </ul>
                              </div>
                            </li>

                            <li>
                              <p><strong>Open</strong> the copied <strong>language file</strong> with a text editor such as <a href="http://www.sublimetext.com/">Sublime Text</a> or <a href="http://notepad-plus-plus.org/">Notepad++</a> and <strong>make the required translations</strong>. You can also leave the files in English.</p>
                              <p><span class="label label-info">Note</span> You only need to translate the parts that are to the right of the equal sign.</p>
                            </li>

                            <li>
                              <p>Some of the translatable strings are located inside the vQmod script file <em>vqmod/xml/similar_products.xml</em>, so <strong>open the XML file</strong> with a text editor (<strong>not</strong> with a word processor application such as MS Word) and <strong>search</strong> for a <em>file</em> block that edits the <em>admin/language/english/catalog/product.php</em> language file. It should look similar to the following:</p>
                              <pre class="prettyprint linenums"><code class="language-xml">    &lt;file name="admin/language/english/catalog/product.php"&gt;
        &lt;operation&gt;
            &lt;search position="before"&gt;&lt;![CDATA[
            ?&gt;
            ]]&gt;&lt;/search&gt;
            &lt;add&gt;&lt;![CDATA[
$_['text_random']                       = 'Random';
$_['text_most_viewed']                  = 'Most Viewed';
$_['text_date_added']                   = 'Date Added';
$_['text_date_modified']                = 'Date Modified';
$_['text_name']                         = 'Product Name';
$_['text_sort_order']                   = 'Sort Order';
$_['text_model']                        = 'Model';
$_['text_quantity']                     = 'Quantity';
$_['text_off']                          = 'Off';
$_['text_category']                     = 'Category';
$_['text_name_fragment']                = 'Product Name Fragment';
$_['text_model_fragment']               = 'Product Model Fragment';
$_['text_name_custom_string']           = 'Custom String in Product Name';
$_['text_model_custom_string']          = 'Custom String in Product Model';

$_['entry_sp_auto_select']              = 'Auto select similar:';
$_['entry_sp_product_sort_order']       = 'Product sort order:';
$_['entry_sp_leaves_only']              = 'Leaves only:';
$_['entry_sp_substr_start']             = 'Substring start:';
$_['entry_sp_substr_length']            = 'Substring length:';
$_['entry_sp_custom_string']            = 'Custom string:';
$_['entry_sp_similar_products']         = 'Similar Products:&lt;br /&gt;&lt;span class="help"&gt;(Autocomplete)&lt;/span&gt;';
            ]]&gt;&lt;/add&gt;
        &lt;/operation&gt;
    &lt;/file&gt;</code></pre>
                            </li>

                            <li>
                              <p>Make a <strong>copy</strong> of the whole <em>file</em> block, <strong>replace</strong> <em>english</em> with <em>YOUR_LANGUAGE</em> in the file path and <strong>translate the string(s)</strong>. You can also leave the strings in English.</p>

                              <p><span class="label label-info">Note</span> If you want to quickly familiarize yourself with the simple <a href="http://code.google.com/p/vqmod/" target="_blank">vQmod</a> script syntax, please check out the <a href="http://code.google.com/p/vqmod/wiki/Scripting" target="_blank">official Wiki page</a></p>

                              <p>The end result would look similar to the following example:</p>

                              <pre class="prettyprint linenums"><code class="language-xml">    &lt;file name="admin/language/english/catalog/product.php"&gt;
        &lt;operation&gt;
            &lt;search position="before"&gt;&lt;![CDATA[
            ?&gt;
            ]]&gt;&lt;/search&gt;
            &lt;add&gt;&lt;![CDATA[
$_['text_random']                       = 'Random';
$_['text_most_viewed']                  = 'Most Viewed';
$_['text_date_added']                   = 'Date Added';
$_['text_date_modified']                = 'Date Modified';
$_['text_name']                         = 'Product Name';
$_['text_sort_order']                   = 'Sort Order';
$_['text_model']                        = 'Model';
$_['text_quantity']                     = 'Quantity';
$_['text_off']                          = 'Off';
$_['text_category']                     = 'Category';
$_['text_name_fragment']                = 'Product Name Fragment';
$_['text_model_fragment']               = 'Product Model Fragment';
$_['text_name_custom_string']           = 'Custom String in Product Name';
$_['text_model_custom_string']          = 'Custom String in Product Model';

$_['entry_sp_auto_select']              = 'Auto select similar:';
$_['entry_sp_product_sort_order']       = 'Product sort order:';
$_['entry_sp_leaves_only']              = 'Leaves only:';
$_['entry_sp_substr_start']             = 'Substring start:';
$_['entry_sp_substr_length']            = 'Substring length:';
$_['entry_sp_custom_string']            = 'Custom string:';
$_['entry_sp_similar_products']         = 'Similar Products:&lt;br /&gt;&lt;span class="help"&gt;(Autocomplete)&lt;/span&gt;';
            ]]&gt;&lt;/add&gt;
        &lt;/operation&gt;
    &lt;/file&gt;

    &lt;file name="admin/language/YOUR_LANGUAGE/catalog/product.php"&gt;
        &lt;operation&gt;
            &lt;search position="before"&gt;&lt;![CDATA[
            ?&gt;
            ]]&gt;&lt;/search&gt;
            &lt;add&gt;&lt;![CDATA[
$_['text_random']                       = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_most_viewed']                  = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_date_added']                   = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_date_modified']                = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_name']                         = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_sort_order']                   = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_model']                        = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_quantity']                     = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_off']                          = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_category']                     = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_name_fragment']                = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_model_fragment']               = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_name_custom_string']           = 'YOUR_LANGUAGE_TRANSLATION';
$_['text_model_custom_string']          = 'YOUR_LANGUAGE_TRANSLATION';

$_['entry_sp_auto_select']              = 'YOUR_LANGUAGE_TRANSLATION';
$_['entry_sp_product_sort_order']       = 'YOUR_LANGUAGE_TRANSLATION';
$_['entry_sp_leaves_only']              = 'YOUR_LANGUAGE_TRANSLATION';
$_['entry_sp_substr_start']             = 'YOUR_LANGUAGE_TRANSLATION';
$_['entry_sp_substr_length']            = 'YOUR_LANGUAGE_TRANSLATION';
$_['entry_sp_custom_string']            = 'YOUR_LANGUAGE_TRANSLATION';
$_['entry_sp_similar_products']         = 'YOUR_LANGUAGE_TRANSLATION';
            ]]&gt;&lt;/add&gt;
        &lt;/operation&gt;
    &lt;/file&gt;</code></pre>
                            </li>
                          </ol>

                          <h5 class="text-muted">Translating <strong>store front end</strong> module files</h5>
                          <ol>
                            <li>
                              <p><strong>Copy</strong> the following language file <strong>to YOUR_LANGUAGE folder</strong> under the appropriate location as shown below:</p>
                              <div class="btm-mgn">
                                <em class="text-muted"><small>FROM:</small></em>
                                <ul class="list-unstyled">
                                  <li>catalog/language/english/module/similar_products.php</li>
                                </ul>
                                <em class="text-muted"><small>TO:</small></em>
                                <ul class="list-unstyled">
                                  <li>catalog/language/YOUR_LANGUAGE/module/similar_products.php</li>
                                </ul>
                              </div>
                            </li>

                            <li>
                              <p><strong>Open</strong> the copied <strong>language file</strong> with a text editor such as <a href="http://www.sublimetext.com/">Sublime Text</a> or <a href="http://notepad-plus-plus.org/">Notepad++</a> and <strong>make the required translations</strong>. You can also leave the files in English.</p>
                              <p><span class="label label-info">Note</span> You only need to translate the parts that are to the right of the equal sign.</p>
                            </li>

                            <li>
                              <p>Some of the translatable strings are again located inside the vQmod script file <em>vqmod/xml/custom_image_titles.xml</em>, so <strong>open the XML file</strong> with a text editor (<strong>not</strong> with a word processor application such as MS Word) and <strong>search</strong> for a <em>file</em> block that edits the <em>catalog/language/english/product/product</em> language file. It should look similar to the following:</p>
                              <pre class="prettyprint linenums"><code class="language-xml">    &lt;file name="catalog/language/english/product/product.php"&gt;
        &lt;operation&gt;
            &lt;search position="before"&gt;&lt;![CDATA[
            ?&gt;
            ]]&gt;&lt;/search&gt;
            &lt;add&gt;&lt;![CDATA[
$_['tab_similar']       = 'Similar Products';
$_['error_ajax_request']= 'Sorry but there was an AJAX error: ';
            ]]&gt;&lt;/add&gt;
        &lt;/operation&gt;
    &lt;/file&gt;</code></pre>
                            </li>

                            <li>
                              <p>Make a <strong>copy</strong> of the whole <em>file</em> block, <strong>replace</strong> <em>english</em> with <em>YOUR_LANGUAGE</em> in the file path and <strong>translate the string(s)</strong>. You can also leave the strings in English.</p>

                              <p><span class="label label-info">Note</span> If you want to quickly familiarize yourself with the simple <a href="http://code.google.com/p/vqmod/" target="_blank">vQmod</a> script syntax, please check out the <a href="http://code.google.com/p/vqmod/wiki/Scripting" target="_blank">official Wiki page</a></p>

                              <p>The end result would look similar to the following example:</p>

                              <pre class="prettyprint linenums"><code class="language-xml">    &lt;file name="catalog/language/english/product/product.php"&gt;
        &lt;operation&gt;
            &lt;search position="before"&gt;&lt;![CDATA[
            ?&gt;
            ]]&gt;&lt;/search&gt;
            &lt;add&gt;&lt;![CDATA[
$_['tab_similar']       = 'Similar Products';
$_['error_ajax_request']= 'Sorry but there was an AJAX error: ';
            ]]&gt;&lt;/add&gt;
        &lt;/operation&gt;
    &lt;/file&gt;

    &lt;file name="catalog/language/YOUR_LANGUAGE/product/product.php"&gt;
        &lt;operation&gt;
            &lt;search position="before"&gt;&lt;![CDATA[
            ?&gt;
            ]]&gt;&lt;/search&gt;
            &lt;add&gt;&lt;![CDATA[
$_['tab_similar']       = 'YOUR_LANGUAGE_TRANSLATION';
$_['error_ajax_request']= 'YOUR_LANGUAGE_TRANSLATION';
            ]]&gt;&lt;/add&gt;
        &lt;/operation&gt;
    &lt;/file&gt;</code></pre>
                            </li>
                          </ol>
                        </div>
                      </div>
                    </li>
                    <li class="media">
                      <div class="pull-left">
                        <i class="fa fa-question-circle fa-4x media-object"></i>
                      </div>
                      <div class="media-body">
                        <h4 class="media-heading">How to integrate the extension with a custom theme?</h4>

                        <p class="short-answer">If the look and feel of the products displayed by this module does not fit your theme then you will have to customize the module specific template files <em>catalog/view/theme/default/template/module/similar_products.tpl</em> and <em>catalog/view/theme/default/template/module/similar_products_products.tpl</em>. If you are trying to display the module in the tab position and you are using a custom theme then the first thing to do is to change the theme name in the <em>vqmod/xml/similar_products.xml</em> vQmod script file to point to your custom theme.</p>

                        <button type="button" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#theme_integration" data-parent="#faqs">Show the full answer</button>
                        <div class="collapse full-answer" id="theme_integration">
                          <p>In order to be able to display the module in the tab position on a custom theme you need to modify the <em>vqmod/xml/similar_products.xml</em> file. Find the block that edits the default theme <em>product.tpl</em> template file (near the end of the file) and change the theme name from 'default' to your custom theme (folder name).</p>
                          <p>If the Similar Products tab does not appear after this change, then your custom theme template structure must differ in some way from the default theme. In this case you need to further tailor the vqmod search &amp; replace/insert patterns for the template files to deal with the structural peculiarities of your custom theme.</p>
                          <p>As due to the very nature of a custom theme there does not exist a universal solution. A custom theme may have a different way of displaying things. Take a look at the changes made to the default theme and work out adjustments to the search &amp; replace patterns to suit your theme.</p>
                          <p>If you do not know how the vqmod script works, I kindly suggest you read about it from the vqmod <a href="https://code.google.com/p/vqmod/w/list" target="_blank">wiki pages</a>. vQmod log files (<em>vqmod/logs/*.log</em>) are helpful for debugging. They will tell you where the script fails (meaning which vqmod search line it does not find in the referenced file), so you need to adjust that part of the script.</p>
                          <p>In case the products displayed by the <?php echo $ext_name; ?> extension do not have a natural look and feel for your theme you need to copy the <em>catalog/view/theme/default/template/module/similar_products.tpl</em> and <em>catalog/view/theme/default/template/module/similar_products_products.tpl</em> template files to the appropriate location under your custom theme folder. After the template files have been copied they need to be custom tailored to the peculiarities of your theme.</p>
                          <p>As <?php echo $ext_name; ?> displays a list of products it may be helpful to see how modules such as Bestsellers, Featured, Latest and/or Specials have been integrated with your theme and use the analogy from those modules.</p>
                          <p>Should you find yourself in trouble with the changes I can offer commercial custom theme integration service. Please refer to the <a href="#" class="external-tab-link" data-target="#services">Services</a> section.</p>
                        </div>
                      </div>
                    </li>
                    <li class="media">
                      <div class="pull-left">
                        <i class="fa fa-question-circle fa-4x media-object"></i>
                      </div>
                      <div class="media-body">
                        <h4 class="media-heading">How to upgrade the extension?</h4>
                        <p class="short-answer">Back up your system, disable the extension, overwrite the current extension files with new ones and click Upgrade on the extension settings page. After upgrade is complete enable the extension again.</p>

                        <button type="button" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#upgrade" data-parent="#faqs">Show the full answer</button>
                        <div class="collapse full-answer" id="upgrade">
                          <ol>
                            <li>
                              <p><strong>Back up your system</strong> before making any upgrades or changes.</p>
                              <p><span class="label label-info">Note</span> Although <?php echo $ext_name; ?> does not overwrite any OpenCart core files, it is always a good practice to create a system backup before making any changes to the system.</p>
                            </li>
                            <li><strong>Disable</strong> <?php echo $ext_name; ?> <strong>extension</strong> on the module settings page (<em>Extensions > Modules > <?php echo $ext_name; ?></em>) by changing <em>Extension status</em> setting to "Disabled".</li>

                            <li>
                              <p><strong>Copy</strong> the <strong>new files</strong> from the <em>FILES TO UPLOAD</em> directory <strong>to the root directory you have installed OpenCart in</strong> overwriting any files that already exist.</p>
                              <p><span class="label label-info">Note</span> Do not worry, no core files will be replaced! Only the previously installed <?php echo $ext_name; ?> files will be overwritten.</p>
                              <p><span class="label label-danger">Important</span> If you have done custom modifications to the extension (for example customized it for your theme) and you don't want to overwrite all of the extension files, please take a look at the changelog file. You can copy only these files which have been changed since your last update and merge the files you have made custom modifications to.</p>
                            </li>

                            <li>
                              <p><strong>Open</strong> the <?php echo $ext_name; ?> <strong>module settings page</strong> <small>(<em>Extensions > Modules > <?php echo $ext_name; ?></em>)</small> and <strong>refresh the page</strong> by pressing <em>Ctrl + F5</em> twice to force the browser to update the css changes.</p>
                            </li>

                            <li><p>You should see a notice stating that new version of extension files have been found. <strong>Upgrade the extension</strong> by clicking on the 'Upgrade' button.</p></li>

                            <li>After the extension has been successfully upgraded <strong>enable the extension</strong> by changing <em>Extension status</em> setting to "Enabled".</li>
                          </ol>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
                <div class="tab-pane" id="services">
                  <h3>Premium Services</h3>
                  <div id="service-container">
                    <p data-bind="visible: services().length == 0">There are currently no available services for this extension.</p>
                    <table class="table table-hover">
                      <tbody data-bind="foreach: services">
                        <tr class="srvc">
                          <td>
                            <h4 class="service" data-bind="html: name"></h4>
                            <span class="help-block">
                              <p class="description" data-bind="visible: description != '', html: description"></p>
                              <p data-bind="visible: turnaround != ''"><strong>Turnaround time</strong>: <span class="turnaround" data-bind="html: turnaround"></span></p>
                              <span class="hidden code" data-bind="html: code"></span>
                            </span>
                          </td>
                          <td class="nowrap text-right top-pad"><span class="currency" data-bind="html: currency"></span> <span class="price" data-bind="html: price"></span></td>
                          <td class="text-right"><button type="button" class="btn btn-sm btn-primary purchase"><i class="fa fa-shopping-cart"></i> Buy Now</button></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane" id="about">
          <div class="panel panel-default">
            <div class="panel-heading">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#about-navbar-collapse">
                  <span class="sr-only"><?php echo $text_toggle_navigation; ?></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <h3 class="panel-title"><i class="fa fa-info fa-fw"></i> <?php echo $tab_about; ?></h3>
              </div>
              <div class="collapse navbar-collapse" id="about-navbar-collapse">
                <ul class="nav navbar-nav">
                  <li class="active"><a href="#ext_info" data-toggle="tab"><?php echo $tab_extension; ?></a></li>
                  <li><a href="#changelog" data-toggle="tab"><?php echo $tab_changelog; ?></a></li>
                </ul>
              </div>
            </div>
            <div class="panel-body">
              <div class="tab-content">
                <div class="tab-pane active" id="ext_info">
                  <div class="row">
                    <div class="col-sm-12">
                      <h3><?php echo $text_extension_information; ?></h3>

                      <div class="form-group">
                        <label class="col-sm-3 col-md-2 control-label label-normal"><?php echo $entry_extension_name; ?></label>
                        <div class="col-sm-9 col-md-10">
                          <p class="form-control-static"><?php echo $ext_name; ?></p>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-3 col-md-2 control-label label-normal"><?php echo $entry_installed_version; ?></label>
                        <div class="col-sm-9 col-md-10">
                          <p class="form-control-static"><strong><?php echo $installed_version; ?></strong></p>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-3 col-md-2 control-label label-normal"><?php echo $entry_extension_compatibility; ?></label>
                        <div class="col-sm-9 col-md-10">
                          <p class="form-control-static"><?php echo $ext_compatibility; ?></p>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-3 col-md-2 control-label label-normal"><?php echo $entry_extension_store_url; ?></label>
                        <div class="col-sm-9 col-md-10">
                          <p class="form-control-static"><a href="<?php echo $ext_store_url; ?>" target="_blank"><?php echo htmlspecialchars($ext_store_url); ?></a></p>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-3 col-md-2 control-label label-normal"><?php echo $entry_copyright_notice; ?></label>
                        <div class="col-sm-9 col-md-10">
                          <p class="form-control-static">&copy; 2011 - <?php echo date("Y"); ?> Romi Agar</p>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9 col-md-offset-2 col-md-10">
                          <p class="form-control-static"><a href="view/static/bull5i_sp_extension_terms.htm" id="legal_notice" data-modal="#legal_text"><?php echo $text_terms; ?></a></p>
                        </div>
                      </div>

                      <h3 class="page-header"><?php echo $text_license; ?></h3>
                      <p><?php echo $text_license_text; ?></p>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="changelog">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="release">
                        <h3>Version 4.0.1 <small class="release-date text-muted">09 May 2014</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-success">Fixed:</em> Product Name sort order SQL error</li>
                            <li><em class="text-success">Fixed:</em> Auto select by Custom String in Product Model SQL error</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>admin/view/stylesheet/sp/css/custom.min.css</li>
                            <li>admin/view/template/module/similar_products.tpl</li>
                            <li>catalog/model/module/similar_products.php</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 4.0.0 <small class="release-date text-muted">05 Mar 2014</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-primary">New:</em> Revamped admin interface</li>
                            <li><em class="text-primary">New:</em> Date Added and Date Modified sort options</li>
                            <li><em class="text-primary">New:</em> Autoselect by product tags, product model fragment or custom string in name or model</li>
                            <li><em class="text-primary">New:</em> Multiple 'product/product' layouts supported</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>admin/language/english/module/similar_products.php</li>
                            <li>admin/view/static/bull5i_sp_extension_terms.htm</li>
                            <li>admin/view/template/module/similar_products.tpl</li>
                            <li>catalog/controller/module/similar_products.php</li>
                            <li>catalog/view/theme/default/template/module/similar_products.tpl</li>
                            <li>catalog/view/theme/default/template/module/similar_products_products.tpl</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>

                          <h4>Files added:</h4>

                          <ul>
                            <li>admin/model/module/similar_products.php</li>
                            <li>admin/view/javascript/sp/custom.min.js</li>
                            <li>admin/view/stylesheet/sp/*</li>
                            <li>catalog/model/module/similar_products.php</li>
                            <li>catalog/view/javascript/sp/custom.min.js</li>
                            <li>system/helper/sp.php</li>
                          </ul>

                          <h4>Files removed:</h4>

                          <ul>
                            <li>admin/view/image/spm/extension_logo.png</li>
                            <li>admin/view/static/bull5i_sp_extension_help.htm</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 3.2.2 <small class="release-date text-muted">06 Apr 2013</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-primary">New:</em> Automatic 'product/product' route layout detection</li>
                            <li><em class="text-primary">New:</em> Minor page load improvements</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>admin/language/english/module/similar_products.php</li>
                            <li>catalog/view/theme/default/template/module/similar_products.tpl</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 3.2.1 <small class="release-date text-muted">12 Jan 2013</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-success">Fixed:</em> Error displaying</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 3.2.0 <small class="release-date text-muted">27 Jan 2013</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-primary">New:</em> Improved module height resizing on pagination</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>admin/view/template/module/similar_products.tpl</li>
                            <li>catalog/view/theme/default/template/module/similar_products.tpl</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>

                          <h4>Files added:</h4>

                          <ul>
                            <li>admin/view/static/bull5i_sp_extension_help.htm</li>
                            <li>admin/view/static/bull5i_sp_extension_terms.htm</li>
                          </ul>

                          <h4>Files removed:</h4>

                          <ul>
                            <li>admin/view/static/rmg_extension_help.htm</li>
                            <li>admin/view/static/rmg_extension_terms.htm</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 3.1.2 <small class="release-date text-muted">30 Oct 2013</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-success">Fixed:</em> Compatibility with AQE &amp; AQE PRO extensions</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 3.1.1 <small class="release-date text-muted">27 Sep 2012</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-success">Fixed:</em> SQL error that appeared with negative limit value</li>
                            <li><em class="text-success">Fixed:</em> Incorrect Similar Product count in certain cases</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 3.1.0 <small class="release-date text-muted">18 May 2012</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-primary">New:</em> Option to automatically select similar products based on beginning fragment of product name</li>
                            <li><em class="text-success">Fixed:</em> A module display issue</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>admin/language/english/module/similar_products.php</li>
                            <li>admin/view/template/module/similar_products.tpl</li>
                            <li>catalog/controller/module/similar_products.php</li>
                            <li>catalog/view/theme/default/template/module/similar_products.tpl</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 3.0.0 <small class="release-date text-muted">26 Mar 2012</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-primary">New:</em> Option to sort by product quantity</li>
                            <li><em class="text-primary">New:</em> Lazy loading</li>
                            <li><em class="text-primary">New:</em> Pagination</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>admin/language/english/module/similar_products.php</li>
                            <li>admin/view/template/module/similar_products.tpl</li>
                            <li>catalog/controller/module/similar_products.php</li>
                            <li>catalog/language/english/module/similar_products.php</li>
                            <li>catalog/view/theme/default/template/module/similar_products.tpl</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>

                          <h4>Files added:</h4>

                          <ul>
                            <li>catalog/view/theme/default/image/loading_similar.gif</li>
                            <li>catalog/view/theme/default/template/module/similar_products_products.tpl</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 2.2.0 <small class="release-date text-muted">14 Feb 2012</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-primary">New:</em> Option to display only products currently in stock</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>admin/language/english/module/similar_products.php</li>
                            <li>admin/view/template/module/similar_products.tpl</li>
                            <li>catalog/controller/module/similar_products.php</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>

                          <h4>Files added:</h4>

                          <ul>
                            <li>admin/view/view/image/spm/extension_logo.png</li>
                            <li>admin/view/view/static/rmg_extension_help.htm</li>
                            <li>admin/view/view/static/rmg_extension_terms.htm</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 2.1.3 <small class="release-date text-muted">18 Jan 2012</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-success">Fixed:</em> Errors due to which the module would not show up</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>catalog/controller/module/similar_products.php</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 2.1.2 <small class="release-date text-muted">11 Jan 2012</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-success">Fixed:</em> Issue where leaf categories were not found and thus no similar products were shown</li>
                          </ul>

                          <h4>Files changed:</h4>

                          <ul>
                            <li>admin/controller/module/similar_products.php</li>
                            <li>catalog/controller/module/similar_products.php</li>
                            <li>vqmod/xml/similar_products.xml</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 2.1.1 <small class="release-date text-muted">29 Dec 2011</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-success">Fixed:</em> Issue with product copy</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 2.1.0 <small class="release-date text-muted">21 Dec 2011</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-primary">New:</em> Option to choose products only from leaf categories</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 2.0.0 <small class="release-date text-muted">16 Dec 2011</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-primary">New:</em> Option to automatically select similar products from the current category</li>
                            <li><em class="text-primary">New:</em> Option to sort similar products by name, model, sort order, most viewed or random order</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 1.1.1 <small class="release-date text-muted">31 Oct 2011</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-success">Fixed:</em> An undefined function call during extension installation</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 1.1.0 <small class="release-date text-muted">06 Oct 2011</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li><em class="text-primary">New:</em> Module positions - Content Top, Content Bottom, Column Left, Column Right</li>
                            <li><em class="text-primary">New:</em> Adjustable image size</li>
                          </ul>
                        </blockquote>
                      </div>

                      <div class="release">
                        <h3>Version 1.0.0 <small class="release-date text-muted">05 Oct 2011</small></h3>

                        <blockquote>
                          <ul class="list-unstyled">
                            <li>Initial release</li>
                          </ul>
                        </blockquote>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript"><!--
(function(bull5i,$,undefined){
bull5i.texts=$.extend({},bull5i.texts,{error_ajax_request:"<?php echo addslashes($error_ajax_request); ?>",error_module_name:"<?php echo addslashes($error_module_name); ?>",error_positive_integer:"<?php echo addslashes($error_positive_integer); ?>",default_name:"<?php echo addslashes($ext_name); ?>"});var Service=function(e,r,t,i,s,o){this.code=e,this.name=r,this.description=t,this.currency=i,this.price=s,this.turnaround=o},ServiceViewModel=function(){var e=this;e.services=ko.observableArray([]),e.addService=function(r,t,i,s,o,a){e.services.push(new Service(r,t,i,s,o,a))}},serviceVM=new ServiceViewModel;ko.applyBindings(serviceVM,$("#service-container")[0]);var positiveInteger=function(e){parseInt(e)?(this.target.hasError(!1),this.target.errorMsg("")):(this.target.hasError(!0),this.target.errorMsg(this.message))},Language=function(e,r,t){this.id=e,this.name=r,this.flag=t},ModuleName=function(e,r,t){var i=this;this.parent=t,this.language_id=ko.observable(e),this.name=ko.observable(r).extend({required:{message:bull5i.texts.error_module_name,context:i}}),this.hasError=ko.computed(function(){return this.name.hasError()},i),this.errorMsg=ko.computed(function(){return this.name.errorMsg()},i)},Module=function(e,r,t,i,s,o,a,n,h,c,d,u){var l=this,g={};$.each(u.languages,function(r,t){g[t.id]=e.hasOwnProperty(t.id)?e[t.id]:"<?php echo addslashes($ext_name); ?>"}),this.parent=u,this.names=ko.observableArray($.map(g,function(e,r){return new ModuleName(r,e,l)})).extend({hasError:{check:!0,context:l}}),this.layout_id=ko.observable(r),this.position=ko.observable(t),this.limit=ko.observable(i).extend({numeric:{precision:0,context:l}}),this.image_width=ko.observable(s).extend({numeric:{precision:0,context:l},required:{message:bull5i.texts.error_positive_integer,context:l,validate:positiveInteger}}),this.image_height=ko.observable(o).extend({numeric:{precision:0,context:l},required:{message:bull5i.texts.error_positive_integer,context:l,validate:positiveInteger}}),this.sort_order=ko.observable(a).extend({numeric:{precision:0,context:l}}),this.products_per_page=ko.observable(n).extend({numeric:{precision:0,context:l}}),this.status=ko.observable(h),this.stock_only=ko.observable(c),this.lazy_load=ko.observable(d),this.hasError=ko.computed(function(){return this.names.hasError()},l),this.applyErrors=function(e){e.hasOwnProperty("names")&&$.each(l.names(),function(r,t){e.names.hasOwnProperty(t.language_id())&&(t.name.hasError(!0),t.name.errorMsg(e.names[t.language_id()]))}),e.hasOwnProperty("image_width")&&(l.image_width.hasError(!0),l.image_width.errorMsg(e.image_width)),e.hasOwnProperty("image_height")&&(l.image_height.hasError(!0),l.image_height.errorMsg(e.image_height))}};
var ModuleViewModel=function(){var self=this,errors=<?php echo json_encode($errors); ?>;self.languages={};$.each(<?php echo json_encode($languages); ?>,function(k,v){self.languages[k]=new Language(v.language_id,v.name,'view/image/flags/'+v.image)});self.modules=ko.observableArray($.map(<?php echo json_encode($similar_products_module); ?>,function(m){return new Module(m.hasOwnProperty('names')?m.names:[],m.hasOwnProperty('layout')?m.layout:"",m.hasOwnProperty('position')?m.position:'content_bottom',m.hasOwnProperty('limit')?m.limit:25,m.hasOwnProperty('image_width')?m.image_width:<?php echo $default_image_width; ?>,m.hasOwnProperty('image_height')?m.image_height:<?php echo $default_image_height; ?>,m.hasOwnProperty('sort_order')?m.sort_order:0,m.hasOwnProperty('products_per_page')?m.products_per_page:5,m.hasOwnProperty('status')?m.status:0,m.hasOwnProperty('stock_only')?m.stock_only:0,m.hasOwnProperty('lazy_load')?m.lazy_load:1,self)})).extend({hasError:{check:true,context:self}});self.tabPositionUsed=ko.computed(function(){var o=!1;return ko.utils.arrayForEach(self.modules(),function(t){"content_tab"==t.position()&&(o=!0)}),o});self.addModule=function(){self.modules.push(new Module([],"","content_bottom",25,"<?php echo $default_image_width; ?>","<?php echo $default_image_height; ?>",0,5,0,0,1,self))},self.deleteModule=function(e){e&&self.modules.remove(e)},self.applyErrors=function(e){e.hasOwnProperty("modules")&&$.each(self.modules(),function(o,l){e.modules.hasOwnProperty(o)&&l.applyErrors(e.modules[o])})},self.applyErrors(errors)};var moduleVM=bull5i.view_model=new ModuleViewModel();ko.applyBindings(moduleVM,$("#sp-modules")[0]);
var products=new Bloodhound({datumTokenizer:function(t){return Bloodhound.tokenizers.whitespace(t.value)},queryTokenizer:Bloodhound.tokenizers.whitespace,limit:10,remote:'<?php echo str_replace("%TYPE%", "product", $autocomplete); ?>'});products.initialize();var ta_options={limit:10,source:products.ttAdapter(),templates:{suggestion:Handlebars.compile(['<p>{{value}} <small class="text-muted">{{model}}</small></p>'].join(""))}};attachTypeahead=function(t){$("."+t+".typeahead").not(".tt-input,.tt-hint").on("typeahead:selected",function(t,e){var a=ko.dataFor(this),o=$(this).data("method");o&&(a[o](e.id,e.value,e.model),$(this).typeahead("val",""))}),$("."+t+".typeahead").not(".tt-input,.tt-hint").typeahead({autoselect:!0,highlight:!0},$.extend(!0,ta_options,{name:t}))};
var Product=function(i,t,d){this.id=i,this.name=t,this.model=d};var ChangeViewModel=function(){var self=this;this.auto_select=ko.observable("<?php echo $sp_auto_select; ?>"),this.product_sort_order=ko.observable("<?php echo $sp_product_sort_order; ?>"),this.leaves_only=ko.observable("<?php echo $sp_leaves_only; ?>"),this.substr_start=ko.observable("<?php echo $sp_substr_start; ?>"),this.substr_length=ko.observable("<?php echo $sp_substr_length; ?>"),this.custom_string=ko.observable("<?php echo $sp_custom_string; ?>"),this.products=ko.observable('<?php echo isset($sp_apply_to["products"]) ? $sp_apply_to["products"] : ""; ?>'),this.category=ko.observable('<?php echo isset($sp_apply_to["category"]) ? $sp_apply_to["category"] : ""; ?>');this.selected=ko.observableArray(ko.utils.arrayMap(<?php echo json_encode(isset($sp_apply_to["selected"]) ? (array)$sp_apply_to["selected"] : array()); ?>,function(p){return new Product(p.product_id,p.name,p.model);}));this.removeSelected=function(e){self.selected.remove(e)},this.addSelected=function(e,t,d){if("2"==self.products()){var c=!1;$.each(self.selected(),function(t,d){return d.id==e?void(c=!0):void 0}),c||self.selected.push(new Product(e,t,d))}}};var changeVM=new ChangeViewModel();ko.applyBindings(changeVM,$("#sp-mass-change")[0]);
attachTypeahead("product"),$.when($.ajax({url:"http<?php echo $ssl; ?>://www.opencart.ee/services/",data:{eid:"<?php echo $ext_id; ?>",info:!0,general:!0},dataType:"jsonp"})).then(function(e){e.services&&$.each(e.services,function(e,o){var c=o.code,n=o.name,a=o.description||"",r=o.currency,t=o.price,i=o.turnaround;serviceVM.addService(c,n,a,r,t,i)}),e.rate&&$("#hourly_rate").html(e.rate)},function(e,o,c){window.console&&window.console.log&&window.console.log("Failed to load services list: "+c)});
}(window.bull5i=window.bull5i||{},jQuery));
//--></script>
<?php echo $footer; ?>
