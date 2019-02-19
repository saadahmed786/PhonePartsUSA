<script>
    var email_message;
    $('document').ready(function(){
        $('#mass_message_window').dialog({modal:true,autoOpen:false,width:790,position:'top',
                buttons:{
                    "close":function(){
                        $('#mass_message_window').dialog('destroy').remove();
                        delete CKEDITOR.instances['email_message'];
                    },
                    "send":function(){
                        $("#request_result_modal").html('Waiting...');
                        <?php  //need clean url,without &amp; ?>

                        $.post('<?php echo str_replace('&amp;','&',$this->url->link('module/combat_cart_loss/send_mass_message', 'token=' . $this->session->data['token'], 'SSL'))?>',{'recipients[]':new_recipients,recipient_type:recipient_type,message:email_message.getData(),subject:$("#email_subject").val(), from:$("#email_from").val()},function(data){
                            if (data.error)
                            {
                                $("#request_result_modal").html('<div class="warning">'+data.error+'</div>');
                            }
                            else
                            {
                                $("#request_result_modal").html('<div class="success">'+data.success+'</div>');
                            }
                        },'json');
                    }
                },
                close:function(){
                    $('#mass_message_window').dialog('destroy').remove();
                    delete CKEDITOR.instances['email_message'];
                }})
            .dialog('open');
        email_message = CKEDITOR.replace('email_message');
    });
    function send_message()
    {
        $('#message_form').submit();
        return true;
    }

    function load_template(template_id)
    {
        <?php  //need clean url,without &amp; ?>
        $.ajax({
            url:'<?php echo str_replace('&amp;','&',$this->url->link('module/combat_cart_loss/get_template', 'token=' . $this->session->data['token'].'&template_id=', 'SSL'))?>'+template_id,
            dataType:'json',
            success:function(data){
                email_message.setData(data.message);
                $("#email_subject").val(data.subject);
                $("#email_from").val(data.from);
            }
        })
    }
</script>
<div id="mass_message_window" title="<?php echo $title_mass_message_window?>">
<div id="request_result_modal"></div>
<table>
    <tr>
        <td valign="top" colspan="2">
            <label for="message_template"><?php echo $title_message_template?></label>
            <?php if ($templates->num_rows==0){?>
            <div class="warning"><?php echo $message_no_templates;?></div>
            <?php }else{?>
            <br/><select id="message_template" onchange="load_template($(this).val())">
                <option value="0"><?php echo $message_default_template?></option>
                <?php foreach($templates->rows as $template){?>
                <option value="<?php echo $template['template_id']?>"><?php echo $template['template_subject']?></option>
                <?php }?>
            </select>
            <?php }?>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <label for="email_from"><?php echo $title_message_from?></label><br/>
            <input type="text" name="email_from" id="email_from" maxlength="300"><br/>
        </td>
        <td valign="top">
            <label for="email_subject"><?php echo $title_message_subject?></label><br/>
            <input type="text" size="100" name="email_subject" id="email_subject" maxlength="300"><br/>
        </td>
    </tr>
</table>
<label for="email_message"><?php echo $title_message_to_customer?></label>
<textarea cols="" rows="" id="email_message" name="email_message"></textarea>
</div>