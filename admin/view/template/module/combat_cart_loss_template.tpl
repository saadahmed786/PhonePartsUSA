<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if (!$template) { ?>
        <div class="warning"><?php echo $message_no_template; ?></div>
        </div>
        <?php echo $footer;
        exit;
        ?>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/module.png" alt="" /> <?php echo $title_template_edit; ?></h1>
        </div>
        <div class="content">
            <?php if ($success){?>
            <div class="success"><?php echo $success?></div>
            <?php }?>
            <?php if ($error_warning) { ?>
            <div class="warning"><?php echo $error_warning; ?></div>
            <?php } ?>
            <form action="<?php echo $this->url->link('module/combat_cart_loss/template_edit', 'token=' . $this->session->data['token'].(isset($template->row['template_id'])?('&template_id='.$template->row['template_id']):''), 'SSL');?>" method="post" target="_self" enctype="multipart/form-data" id="template_form">
                <input type="hidden" name="template_id" value="<?php echo (isset($template->row['template_id']))?$template->row['template_id']:0?>">
                 <label for="template_from"><?php echo $title_template_from?></label><br/>
                <input type="text" name="template_from" id="template_from" value="<?php echo (isset($template->row['template_from']))?$template->row['template_from']:''?>"><br /><br/>
                
                <label for="template_subject"><?php echo $title_template_subject?></label><br/>
                <input type="text" size="100" name="template_subject" id="template_subject" maxlength="300" value="<?php echo (isset($template->row['template_subject']))?$template->row['template_subject']:''?>"><br/>
                <br/><label for="template_message"><?php echo $title_template_message?></label>
                <textarea cols="" rows="" name="template_message" id="template_message"><?php echo (isset($template->row['template_message']))?$template->row['template_message']:''?></textarea>
                <br/>
                <a href="<?php echo $this->url->link('module/combat_cart_loss', 'token=' . $this->session->data['token'], 'SSL');?>"><?php echo $title_back_to_module?></a>&nbsp;&nbsp;
                <a class="button" onclick="$('#template_form').submit()"><span><?php echo $title_save_template?></span></a>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--
var email_message = CKEDITOR.replace('template_message');
//--></script>
<?php echo $footer; ?>