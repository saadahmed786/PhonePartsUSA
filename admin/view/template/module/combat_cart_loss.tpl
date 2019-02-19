<?php echo $header; ?>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
var recipient_type;
var new_recipients=[];
$('document').ready(function(){
    $('#tabs > a').tabs();
    $('#vtabs > a').tabs();
});
</script>
<style type="text/css">
.vtabs a, .vtabs span{padding-left:0px;padding-right:2%;width:97%;}
.vtabs a.selected {padding-right:3%;}
</style>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
        <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($success){?>
        <div class="success"><?php echo $success?></div>
    <?php }?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
        </div>
        <div id="request_result"></div>
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="#unconfirmed_orders" id="unconfirmed_orders_tab"><?php echo $title_unconfirmed_orders?></a>
                <a href="#confirmed_orders" id="confirmed_orders_tab"><?php echo $title_confirmed_orders?></a>
                <a href="#templates_list" id="templates_list_tab"><?php echo $title_templates_list?></a>
                <a href="#settings" id="settings_tab"><?php echo $title_settings?></a>
                <a href="#autoemail" id="autoemail_tab"><?php echo $title_autoemail_settings ?></a>
            </div>
            <div id="unconfirmed_orders">
                <form action="<?php echo $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL');?>" method="post" enctype="multipart/form-data" id="orders_form" target="_self">
                    <a onclick="if (confirm('<?php echo $question_delete_orders?>')){$('#orders_form').submit();}" class="button"><span><?php echo $title_button_delete; ?></span></a>
                    <a class="button" onclick="send_mass_messages('orders');return false;"><span><?php echo $title_send_mass_message?></span></a><br/><br/>
                    <table width="100%" class="list">
                        <thead>
                            <tr>
                                <td width="10"><input type="checkbox" onclick="$(this).parents('table').find('input[name*=\'delete_order\']').attr('checked', this.checked);"></td>
                                <td class="center" width="80"><?php echo $title_order_id?></td>
                                <td class="left"><?php echo $title_order_customer?></td>
                                <td class="left"><?php echo "Store Name"; ?></td>
                                <td class="center"><?php echo $title_order_total?></td>
                                <td class="left"><?php echo $title_order_added?></td>
                                <td class="left"><?php echo $title_order_modified?></td>
                                <td class="left"><?php echo $title_order_contacted?></td>
                                <td></td>
                            </tr>
                        </thead>
                        <?php if (count($orders->rows)==0){?>
                        <tr>
                            <td colspan="7">
                                <br/><div class="success"><?php echo $message_no_orders; ?></div>
                            </td>
                        </tr>
                        <?php }else{
                        $count_orders = 0;
                        foreach($orders->rows as $row){
                            $count_orders++;
                            if($count_orders==21){ ?>
                                </table>
                                    <a onclick="get_more_unconfirmed_orders(this);return false;" class="button"><span>Get More Orders</span></a>
                            <?php
                                break;
                            }
                        ?>
                        <tr>
                            <td><input type="checkbox" name="delete_order[]" value="<?php echo $row['order_id']?>"></td>
                            <td class="center"><?php echo $row['order_id']?></td>
                            <td class="left"><?php echo  $row['customer']?></td>
                            <td class="left"><?php echo  $row['store_name']?></td>
                            <td class="center"><?php echo $row['total']?></td>
                            <td class="left"><?php echo date('d M Y H:i:s',strtotime($row['date_added']))?></td>
                            <td class="left"><?php echo date('d M Y H:i:s',strtotime($row['date_modified']))?></td>
                            <td class="left"><?php echo ((int)$row['total_emails']>0?$title_yes:$title_no);?></td>
                            <td class="center"><a href="<?php echo $this->url->link('module/combat_cart_loss/order_details', 'order_id=' . $row['order_id'].'&token=' . $this->session->data['token'], 'SSL')?>"><?php echo $title_detail; ?></a></td>
                        </tr>
                        <?php }
                        }?>
                    </table>
                </form>
            </div>

            <div id="confirmed_orders">
                <form action="<?php echo $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL');?>" method="post" enctype="multipart/form-data" id="orders_form" target="_self">
                    <a class="button" onclick="send_mass_messages('orders');return false;"><span><?php echo $title_send_mass_message?></span></a><br/><br/>

                    <div id="vtabs" class="vtabs" style="width:14%; padding-top:0px;">
                        <?php if($order_status) { ?>
                            <?php foreach($order_status as $s => $status) { ?>
                                <a href="#confirmed_orders_tab<?php echo $s;?>"><?php echo $status;?></a>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?php if($order_status) { ?>
                            <?php foreach($order_status as $s => $status) { ?>
                            <div  style="width:84%;min-height:416px;;float:left" id="confirmed_orders_tab<?php echo $s;?>">
                                <table class="list">
                                <thead>
                                    <tr>
                                        <td width="10"><input type="checkbox" onclick="$(this).parents('table').find('input[name*=\'delete_order\']').attr('checked', this.checked);"></td>
                                        <td class="center" width="80"><?php echo $title_order_id?></td>
                                        <td class="left"><?php echo $title_order_customer?></td>
                                        <td class="left"><?php echo "Store Name"; ?></td>
                                        <td class="center"><?php echo $title_order_total?></td>
                                        <td class="left"><?php echo $title_order_added?></td>
                                        <td class="left"><?php echo $title_order_modified?></td>
                                        <td class="left"><?php echo $title_order_contacted?></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <?php
                                $count_orders = 0;
                                foreach($confirmed_orders->rows as $row){
                                    if($row['status'] != $status){
                                        continue;
                                    }
                                    $count_orders++;
                                    if($count_orders==21){ ?>
                                        </table>
                                            <a onclick="get_more_orders('<?php echo $s;?>',this);return false;" class="button"><span>Get More Orders</span></a>
                                    <?php
                                        break;
                                    }
                                ?>
                                    <tr>
                                        <td><input type="checkbox" name="delete_order[]" value="<?php echo $row['order_id']?>"></td>
                                        <td class="center"><?php echo $row['order_id']?></td>
                                        <td class="left"><?php echo  $row['customer']?></td>
                                        <td class="left"><?php echo  $row['store_name']?></td>
                                        <td class="center"><?php echo $row['total']?></td>
                                        <td class="left"><?php echo date('d M Y H:i:s',strtotime($row['date_added']))?></td>
                                        <td class="left"><?php echo date('d M Y H:i:s',strtotime($row['date_modified']))?></td>
                                        <td class="left"><?php echo ((int)$row['total_emails']>0?$title_yes:$title_no);?></td>
                                        <td class="center"><a href="<?php echo $this->url->link('module/combat_cart_loss/order_details', 'order_id=' . $row['order_id'].'&token=' . $this->session->data['token'], 'SSL');?>">Details</a></td>
                                    </tr>
                                <?php } // end for each loop
                                if ($count_orders==0){ ?>
                                    <tr>
                                        <td colspan="7">
                                            <br/><div class="success"><?php echo $message_no_orders?></div>
                                        </td>
                                    </tr>

                               <?php }?>
                            </table>
                            </div>
                            <?php } ?>
                        <?php } ?>
                </form>
            </div>

            <div id="templates_list">
                <form action="<?php echo $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL');?>" method="post" id="templates_form" target="_self">
                    <a class="button" href="<?php echo $this->url->link('module/combat_cart_loss/new_template', 'token=' . $this->session->data['token'], 'SSL');?>"><span><?php echo $title_new_template?></span></a>
                    <a onclick="if(confirm('<?php echo $question_delete_templates?>')){$('#templates_form').submit()}" class="button"><span><?php echo $title_button_delete?></span></a><br/><br/>
                    <table width="100%" class="list">
                        <thead>
                            <tr>
                                 <td width="10"><input type="checkbox" onclick="$('input[name*=\'delete_template\']').attr('checked', this.checked);"></td>
                                 <td class="left"><?php echo $title_template_subject?></td>
                                 <td class="left"></td>
                            </tr>
                        </thead>
                        <?php if (count($templates->rows)==0){?>
                        <tr>
                            <td  colspan="4">
                                <br/><div class="success"><?php echo $message_no_templates?></div>
                            </td>
                        </tr>
                        <?php }else{?>
                        <?php foreach($templates->rows as $row){?>
                        <tr>
                            <td><input type="checkbox" name="delete_template[]" value="<?php echo $row['template_id']?>"></td>
                            <td class="left"><?php echo $row['template_subject']?></td>
                            <td>
                                <a href="<?php echo $this->url->link('module/combat_cart_loss/template_edit', 'template_id=' . $row['template_id'].'&token=' . $this->session->data['token'], 'SSL');?>"><?php echo $title_edit_template?></a>
                            </td>
                        </tr>
                        <?php }?>
                        <?php }?>
                    </table>
                </form>
            </div>

            <div id="settings">
                <form action="<?php echo $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL');?>" method="post" id="settings_form" target="_self">
                    <a class="button" onclick="$('#settings_form').submit();"><span><?php echo $button_save;?></span></a>
                    <table width="100%" class="form">

                            <tr>
                                <td><?php echo $entry_admin_email; ?></td>
                                <td><?php if ($ccl_enable_admin_emails) { ?>
                                    <input type="radio" name="ccl_enable_admin_emails" value="1" checked="checked" />
                                    <?php echo $text_yes; ?>
                                    <input type="radio" name="ccl_enable_admin_emails" value="0" />
                                    <?php echo $text_no; ?>
                                    <?php } else { ?>
                                    <input type="radio" name="ccl_enable_admin_emails" value="1" />
                                    <?php echo $text_yes; ?>
                                    <input type="radio" name="ccl_enable_admin_emails" value="0" checked="checked" />
                                    <?php echo $text_no; ?>
                                    <?php } ?></td>
                            </tr>

                            <tr>
                                <td><?php echo $entry_ccl_email_subject; ?></td>
                                <td>
                                    <input type="text" size="100" id="ccl_admin_email_subject" name="ccl_admin_email_subject" value="<?php echo $ccl_admin_email_subject;?>" maxlength="300"/>
                                </td>
                            </tr>


                            <tr>
                                <td><?php echo $entry_ccl_email_message; ?></td>
                                <td>
                                    <textarea rows="6" cols="80" name="ccl_admin_email_message" id="ccl_admin_email_message"><?php echo $ccl_admin_email_message;?></textarea>
                                </td>
                            </tr>

                    </table>
                </form>
            </div>
            
            <div id="autoemail">
                <form action="<?php echo $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL');?>" method="post" id="autoemail_form" target="_self">
                    <a class="button" onclick="$('#autoemail_form').submit();"><span><?php echo $button_save;?></span></a>
                    <table width="100%" class="form">
                            
                            <tr>
                                <td><?php echo $entry_auto_email; ?></td>
                                <td><?php if ($ccl_enable_auto_emails) { ?>
                                    <input type="radio" name="ccl_enable_auto_emails" value="1" checked="checked" />
                                    <?php echo $text_yes; ?>
                                    <input type="radio" name="ccl_enable_auto_emails" value="0" />
                                    <?php echo $text_no; ?>
                                    <?php } else { ?>
                                    <input type="radio" name="ccl_enable_auto_emails" value="1" />
                                    <?php echo $text_yes; ?>
                                    <input type="radio" name="ccl_enable_auto_emails" value="0" checked="checked" />
                                    <?php echo $text_no; ?>
                                    <?php } ?></td>
                            </tr>
                           
                            <tr>
                                <td>Days on which to send automated reminder:</td>
                                <td>
                                    <?php if ($ccl_auto_mon) { ?>
                                        <input type="checkbox" name="ccl_auto_mon" value="1" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="ccl_auto_mon" value="0" />
                                    <?php } ?>
                                    &nbsp;Monday&nbsp; 
                                    
                                    <?php if ($ccl_auto_tue) { ?>
                                        <input type="checkbox" name="ccl_auto_tue" value="1" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="ccl_auto_tue" value="0" />
                                    <?php } ?>
                                    &nbsp;Tuesday&nbsp;
                                    
                                    <?php if ($ccl_auto_wed) { ?>
                                    <input type="checkbox" name="ccl_auto_wed" value="1" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="ccl_auto_wed" value="0" />
                                    <?php } ?>
                                    &nbsp;Wednesday&nbsp;
                                    
                                    <?php if ($ccl_auto_thu) { ?>
                                        <input type="checkbox" name="ccl_auto_thu" value="1" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="ccl_auto_thu" value="0" />
                                    <?php } ?>
                                    &nbsp;Thursday&nbsp;
                                    
                                    <?php if($ccl_auto_fri) { ?>
                                        <input type="checkbox" name="ccl_auto_fri" value="1" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="ccl_auto_fri" value="0" />
                                    <?php } ?>
                                    &nbsp;Friday&nbsp;
                                    
                                    <?php if($ccl_auto_sat) { ?>
                                        <input type="checkbox" name="ccl_auto_sat" value="1" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="ccl_auto_sat" value="0" />
                                    <?php } ?>
                                    &nbsp;Saturday&nbsp;
                                    
                                    <?php if($ccl_auto_sun) { ?>
                                        <input type="checkbox" name="ccl_auto_sun" value="1" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="ccl_auto_sun" value="0" />
                                    <?php } ?>
                                    &nbsp;Sun&nbsp;
                                </td>
                                
                            </tr>
                             <tr>
                                <td><?php echo $entry_auto_coupon_value ?> (%)</td>
                                <td><input type="text" id="ccl_auto_coupon_value" name="ccl_auto_coupon_value" value="<?php echo number_format($ccl_auto_coupon_value,4);?>" /></td>
                            </tr>
                            <tr>
                                <td><?php echo $entry_auto_coupon_total ?></td>
                                <td><input type="text" id="ccl_auto_coupon_total" name="ccl_auto_coupon_total" value="<?php echo number_format($ccl_auto_coupon_total,4);?>" /></td>
                            </tr>
                            <tr>
                                <td><?php echo $entry_auto_coupon_duration ?></td>
                                <td><input type="text" id="ccl_auto_coupon_duration" name="ccl_auto_coupon_duration" value="<?php echo $ccl_auto_coupon_duration;?>" /></td>
                            </tr>
                            <tr>
                                <td><?php echo "From Email Address"; ?></td>
                                <td><input type="text" id="ccl_auto_email_from" name="ccl_auto_email_from" value="<?php echo $ccl_auto_email_from;?>" /></td>
                            </tr>

                            <tr>
                                <td><?php echo $entry_ccl_autoemail_subject; ?></td>
                                <td>
                                    <input type="text" size="100" id="ccl_auto_email_subject" name="ccl_auto_email_subject" value="<?php echo $ccl_auto_email_subject;?>" maxlength="300"/>
                                </td>
                            </tr>


                            <tr>
                                <td><?php echo $entry_ccl_autoemail_message; ?></td>
                                <td>
                                    <textarea rows="6" cols="80" name="ccl_auto_email_message" id="ccl_auto_email_message"><?php echo $ccl_auto_email_message;?></textarea>
                                </td>
                            </tr>

                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function send_mass_messages(source_type)
{
    recipients=(source_type=='carts')?$('input[name*="delete_cart"]:checked'):$('input[name*="delete_order"]:checked');
    if (recipients.length==0)
    {
        alert((source_type=='carts')?'<?php echo $error_no_carts_selected?>':'<?php echo $error_no_orders_selected?>');
        return false;
    }

    recipient_type=source_type;

    new_recipients=[];

    recipients.each(function(index,elem){
        new_recipients.push(elem.value);// += '&recipients%5B%5D=' + elem.value;
        //new_recipients += '&recipients[]=' + elem.value;
    })
    $("#request_result").html('Waiting...');
    <?php  //need clean url,without &amp; ?>
    $.post('<?php echo str_replace('&amp;','&',$this->url->link('module/combat_cart_loss/mass_message', 'token=' . $this->session->data['token'], 'SSL'));?>',{recipients:new_recipients},function(html){
        $("#request_result").html(html);
    });
}
function get_more_orders(status,btn){
        var tbody = (jQuery(btn).siblings('.list').length>0)?jQuery(jQuery(btn).siblings('.list')[0]).find('tbody'):false;
        if(!tbody){
            return;
        }
        jQuery.ajax({
                url:'index.php?route=module/combat_cart_loss/get_more_orders&token=<?php echo $token; ?>&order_status_id='+status,
                dataType: 'json',
                success: function(data){
                    if(data){
                        tbody.find('tr').remove();
                        for(var r in data){
                                r = data[r];
                                tbody.append('<tr><td><input type="checkbox" name="delete_order[]" value="'+r.order_id+'"></td><td class="center">'+r.order_id+'</td><td class="left">'+r.customer+'</td><td class="center">'+r.total+'</td><td class="left">'+r.date_added+'</td><td class="left">'+r.date_modified+'</td><td class="center"><a href="index.php?route=module/combat_cart_loss/order_details&order_id=2&token=<?php echo $token?>&order_id='+r.order_id+'">Details</a></td></tr>');
                        }
                        jQuery(btn).remove();
                    }
                }
            });
    }
 function get_more_unconfirmed_orders(btn){
        var tbody = (jQuery(btn).siblings('.list').length>0)?jQuery(jQuery(btn).siblings('.list')[0]).find('tbody'):false;
        if(!tbody){
            return;
        }
        jQuery.ajax({
                url:'index.php?route=module/combat_cart_loss/get_more_orders&token=<?php echo $token; ?>&unconfirmed=1',
                dataType: 'json',
                success: function(data){
                    if(data){
                        tbody.find('tr').remove();
                        for(var r in data){
                                r = data[r];
                                tbody.append('<tr><td><input type="checkbox" name="delete_order[]" value="'+r.order_id+'"></td><td class="center">'+r.order_id+'</td><td class="left">'+r.customer+'</td><td class="center">'+r.total+'</td><td class="left">'+r.date_added+'</td><td class="left">'+r.date_modified+'</td><td class="center"><a href="index.php?route=module/combat_cart_loss/order_details&order_id=2&token=<?php echo $token?>&order_id='+r.order_id+'">Details</a></td></tr>');
                        }
                        jQuery(btn).remove();
                    }
                }
            });
    }
</script>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--
var email_message = CKEDITOR.replace('ccl_admin_email_message');
var auto_email_message = CKEDITOR.replace('ccl_auto_email_message');
//--></script>
<?php echo $footer; ?>