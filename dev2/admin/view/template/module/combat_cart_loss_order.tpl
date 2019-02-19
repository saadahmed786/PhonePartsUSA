<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if (!$order) { ?>
        <div class="warning"><?php echo $message_no_order; ?></div>
        </div>
        <?php echo $footer;
        exit;
        ?>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/module.png" alt="" /> <?php echo $order_title.$order['order']->row['order_id']; ?></h1>
        </div>
        <div class="content">
            <?php if ($success){?>
            <div class="success"><?php echo $success?></div>
            <?php }?>
            <table width="100%">
                <tr>
                    <td valign="top">
                        <table class="list" width="100%">
                            <thead>
                                <tr>
                                    <td class="left"><?php echo $title_customer_name?></td>
                                    <td class="left"><?php echo $title_customer_email?></td>
                                    <td class="left"><?php echo $title_customer_telephone?></td>
                                    <td class="left"><?php echo $title_order_total?></td>
                                </tr>
                            </thead>
                            <tr>
                                <td class="left"><?php echo $order['order']->row['firstname'].' '.$order['order']->row['lastname']?></td>
                                <td class="left"><?php echo $order['order']->row['email']?></td>
                                <td class="left"><?php echo $order['order']->row['telephone']?></td>
                                <td class="left"><?php echo $order['order']->row['total']?></td>
                            </tr>
                        </table>
                    </td>
                    <td rowspan="2" width="50%" valign="top">
                    <?php if ($order['products']->num_rows==0){?>
                    <div class="warning"><?php echo $message_no_products;?></div>
                    <?php }else{?>
                    <table width="100%" class="list">
                        <thead>
                            <tr>
                                <td class="left"><?php echo $title_product_name?></td>
                                <td class="left"><?php echo $title_product_model?></td>
                                <td class="left"><?php echo $title_product_quantity?></td>
                                <td class="left"><?php echo $title_product_total?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($order['products']->rows as $product){?>
                            <tr>
                                <td class="left"><a href="<?php echo $this->url->link('catalog/product/update', 'product_id='.$product['product_id'].'&token=' . $this->session->data['token'], 'SSL');?>"><?php echo $product['name']?></a></td>
                                <td class="left"><?php echo $product['model']?></td>
                                <td class="left"><?php echo $product['quantity']?></td>
                                <td class="left"><?php echo $product['total']?></td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <?php }?>

                    <?php if ($order['emails']->num_rows==0){?>
                    <div class="warning"><?php echo $message_no_emails;?></div>
                    <?php }else{?>
                    <table width="100%" class="list">
                        <thead>
                            <tr>
                                <td class="left"><?php echo $title_date?></td>
                                <td class="left"><?php echo $title_email_subject?></td>
                                <td class="left"><?php echo $title_email_message?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($order['emails']->rows as $email){?>
                            <tr>
                                <td class="left"><?php echo date('d M Y',strtotime($email['date_added'])).'<br/>'.date('H:i:s',strtotime($email['date_added']))?></td>
                                <td class="left"><?php echo $email['email_subject']?></td>
                                <td class="left"><?php echo $email['email_message']?></td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <?php }?>

                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <form action="" method="post" id="message_form">
                            <input type="hidden" name="customer_email" value="<?php echo $order['order']->row['email']?>">
                            <table>
                                <tr>
                                    <td valign="top">
                                        <label for="email_from"><?php echo $title_message_from?></label><br/>
                                        <input type="text" name="email_from" id="email_from" maxlength="300"><br/>
                                    </td>
                                    <td valign="top">
                                        <label for="email_subject"><?php echo $title_message_subject?></label><br/>
                                        <input type="text" name="email_subject" id="email_subject" maxlength="300"><br/>
                                    </td>
                                    <td valign="top">
                                        <label for="message_template"><?php echo $title_message_template?></label>
                                        <?php if ($templates->num_rows==0){?>
                                        <div class="warning"><?php echo $message_no_templates;?></div>
                                        <?php }else{?>
                                        <br/><select id="message_template" onchange="load_template($(this).val())">
                                            <option value="0"><?php echo $message_default_template?></option>
                                            <?php foreach($templates->rows as $template){ ?>
                                            <option value="<?php echo $template['template_id']?>"><?php echo $template['template_subject']?></option>
                                            <?php }?>
                                        </select>
                                        <?php }?>
                                    </td>
                                </tr>
                            </table>
                            <label for="email_message"><?php echo $title_message_to_customer?></label>
                            <textarea cols="" rows="" id="email_message" name="email_message"></textarea>
                            <br/>
                                <a href="<?php echo $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL');?>"><?php echo $title_back_to_module?></a>&nbsp;&nbsp;
                                <a onclick="return send_message();" class="button"><span><?php echo $title_button_send_message; ?></span></a>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--
var email_message = CKEDITOR.replace('email_message');
function send_message()
{
    $('#message_form').submit();
    return true;
}

function load_template(template_id)
{
    <?php  //need clean url,without &amp; ?>
    $.ajax({
        url:'<?php echo str_replace('&amp;','&',$this->url->link('module/combat_cart_loss/get_template', 'token=' . $this->session->data['token'].'&template_id=', 'SSL'));?>'+template_id,
        dataType:'json',
        success:function(data){
            email_message.setData(data.message);
            $("#email_subject").val(data.subject);
            $("#email_from").val(data.from);
        }
    })
}
//--></script>
<?php echo $footer; ?>