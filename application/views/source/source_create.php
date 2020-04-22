<?php
$en_all_11035 = $this->config->item('en_all_11035'); //MENCH NAVIGATION
$en_all_12762 = $this->config->item('en_all_12762'); //IDEA SOURCE CREATOR
$en_all_2738 = $this->config->item('en_all_2738'); //MENCH
?>

<script>
    var in_loaded_id = <?= $in['in_id'] ?>;
</script>
<script src="/application/views/source/source_create.js?v=<?= config_var(11060) ?>" type="text/javascript"></script>

<div class="container">

    <?php

    //SOURCE CREATOR TITLE
    echo '<h1 class="idea" style="padding-top:5px;"><a href="/idea/'.$in['in_id'].'"><span class="icon-block">'.$en_all_2738[4535]['m_icon'].'</span>'.echo_in_title($in).'</a></h1>';
    //echo '<p class="space-left">You are about to create a new source that references this idea.</p>';


    //Content Title
    echo '<div><span class="icon-block">'.$en_all_12762[12772]['m_icon'].'</span><div class="form-group is-empty block-one inline-block"><input type="text" id="content_title" '.( isset($_GET['content_title']) ? ' value="'.$_GET['content_title'].'" ' : '' ).' class="form-control border montserrat doupper source" placeholder="'.$en_all_12762[12772]['m_name'].'"></div></div>';


    //Content URL
    echo '<div><span class="icon-block">'.$en_all_12762[12763]['m_icon'].'</span><div class="form-group is-empty block-one inline-block"><input type="url" id="ln_content" '.( isset($_GET['ln_content']) ? ' value="'.$_GET['ln_content'].'" ' : '' ).' class="form-control border" placeholder="'.$en_all_12762[12763]['m_name'].'"></div></div>';
    //echo '<span id="en_type_link_id" class="space-left"></span>';
    //echo '<p id="en_link_preview" class="hideIfEmpty space-left"></p>';


    //Content Type
    echo '<div class="space-left" style="padding-bottom: 10px;">'.echo_in_dropdown(3000, ( isset($_GET['content_type']) ? $_GET['content_type'] : 3005 /* Books */ ), 'btn-source').'</div>';


    //Industry Experts
    echo '<div class="new_experts hideIfEmpty"></div>';
    echo '<div id="addContent" class="list-group-item list-adder itemsource no-side-padding">
                <div class="input-group border">
                    <span class="input-group-addon addon-lean icon-adder"><span class="icon-block">'.$en_all_12762[12764]['m_icon'].'</span></span>
                    <input type="text"
                           class="form-control source form-control-thick montserrat doupper algolia_search dotransparent add-input"
                           maxlength="' . config_var(11072) . '"
                           id="authorName"
                           placeholder="'.$en_all_12762[12764]['m_name'].'">
                </div><div class="algolia_pad_search hidden pad_expand"></div></div>';


    //CREATE BUTTON:
    echo '<div style="margin-top:34px;"><a href="javascript:void();" onclick="create_process()" class="btn btn-source">'.$en_all_12762[12771]['m_name'].'</a></div>';


    ?>
</div>