<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/user.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_username; ?></td>
            <td><input type="text" name="username" value="<?php echo $username; ?>" />
              <?php if ($error_username) { ?>
              <span class="error"><?php echo $error_username; ?></span>
              <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
              <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" />
                <?php if ($error_firstname) { ?>
                <span class="error"><?php echo $error_firstname; ?></span>
                <?php } ?></td>
              </tr>
              <tr>
                <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
                <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
                  <?php if ($error_lastname) { ?>
                  <span class="error"><?php echo $error_lastname; ?></span>
                  <?php } ?></td>
                </tr>
                <tr>
                  <td><?php echo $entry_email; ?></td>
                  <td><input type="text" name="email" value="<?php echo $email; ?>" /></td>
                </tr>
                <tr>
                  <td><?php echo $entry_user_group; ?></td>
                  <td><select name="user_group_id">
                    <?php foreach ($user_groups as $user_group) { ?>
                    <?php if ($user_group['user_group_id'] == $user_group_id) { ?>
                    <option value="<?php echo $user_group['user_group_id']; ?>" selected="selected"><?php echo $user_group['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $user_group['user_group_id']; ?>"><?php echo $user_group['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select></td>
                </tr>
                <tr>
                  <td><?php echo $entry_password; ?></td>
                  <td><input type="password" name="password" value="<?php echo $password; ?>"  />
                    <?php if ($error_password) { ?>
                    <span class="error"><?php echo $error_password; ?></span>
                    <?php  } ?></td>
                  </tr>
                  <tr>
                    <td><?php echo $entry_confirm; ?></td>
                    <td><input type="password" name="confirm" value="<?php echo $confirm; ?>" />
                      <?php if ($error_confirm) { ?>
                      <span class="error"><?php echo $error_confirm; ?></span>
                      <?php  } ?></td>
                    </tr>

                    <tr>
                      <td>View Returned Orders</td>
                      <td>
                        <?php if($view_returned_items)
                        {
                          ?>
                          <input type="checkbox" name="view_returned_items" value="1" checked />
                          <?php
                        }
                        else
                        {
                          ?>
                          <input type="checkbox" name="view_returned_items" value="1" />
                          <?php
                        }
                        ?>
                      </td>
                    </tr>

                    <tr>
                    <td>Update Bulk Orders</td>
                      <td>
                        <?php if($update_b_order)
                        {
                          ?>
                          <input type="checkbox" name="update_b_order" value="1" checked />
                          <?php
                        }
                        else
                        {
                          ?>
                          <input type="checkbox" name="update_b_order" value="1" />
                          <?php
                        }
                        ?>
                      </td>
                    </tr>

                    <tr>
                      <td>Edit Order?</td>
                      <td>
                        <?php if($can_edit_order)
                        {
                          ?>
                          <input type="checkbox" name="can_edit_order" value="1" checked />
                          <?php
                        }
                        else
                        {
                          ?>
                          <input type="checkbox" name="can_edit_order" value="1" />
                          <?php
                        }
                        ?>
                      </td>
                    </tr>

                    <tr>
                      <td>POS User</td>
                      <td>
                        <?php if($pos_user)
                        {
                          ?>
                          <input type="checkbox" name="pos_user" value="1" checked />
                          <?php
                        }
                        else
                        {
                          ?>
                          <input type="checkbox" name="pos_user" value="1" />
                          <?php
                        }
                        ?>
                      </td>
                    </tr>

                     <tr>
                      <td>Create Order</td>
                      <td>
                        <?php if($can_create_order)
                        {
                          ?>
                          <input type="checkbox" name="can_create_order" value="1" checked />
                          <?php
                        }
                        else
                        {
                          ?>
                          <input type="checkbox" name="can_create_order" value="1" />
                          <?php
                        }
                        ?>
                      </td>
                    </tr>
                    
                    <tr>
                      <td>Process RMA</td>
                      <td>
                        <?php if($can_process_rma)
                        {
                          ?>
                          <input type="checkbox" name="can_process_rma" value="1" checked />
                          <?php
                        }
                        else
                        {
                          ?>
                          <input type="checkbox" name="can_process_rma" value="1" />
                          <?php
                        }
                        ?>
                      </td>
                    </tr>

                    <tr>
                      <td>Issue Store Credit</td>
                      <td>
                        <?php if($can_issue_store_credit)
                        {
                          ?>
                          <input type="checkbox" name="can_issue_store_credit" value="1" checked />
                          <?php
                        }
                        else
                        {
                          ?>
                          <input type="checkbox" name="can_issue_store_credit" value="1" />
                          <?php
                        }
                        ?>
                      </td>
                    </tr>
                    
                    

                    <tr>
                      <td><?php echo $entry_status; ?></td>
                      <td><select name="status">
                        <?php if ($status) { ?>
                        <option value="0"><?php echo $text_disabled; ?></option>
                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                        <?php } else { ?>
                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <option value="1"><?php echo $text_enabled; ?></option>
                        <?php } ?>
                      </select></td>
                    </tr>
                  </table>
                </form>
              </div>
            </div>
          </div>
          <?php echo $footer; ?> 