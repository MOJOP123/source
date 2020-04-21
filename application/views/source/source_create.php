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
    echo '<p class="space-left">You are about to create a new source that references this idea.</p>';


    //Content Title
    echo '<h2 style="margin-top:34px;">'.$en_all_12762[12772]['m_name'].'</h2>';
    echo '<div><span class="icon-block">'.$en_all_12762[12772]['m_icon'].'</span><div class="form-group is-empty inline-block"><input type="text" id="content_title" '.( isset($_GET['content_title']) ? ' value="'.$_GET['content_title'].'" ' : '' ).' class="form-control border montserrat doupper" placeholder="'.$en_all_12762[12772]['m_desc'].'"></div></div>';


    //Content Type
    echo '<h2>'.$en_all_12762[3000]['m_name'].'</h2>';
    echo '<div>'.echo_in_dropdown(3000, ( isset($_GET['content_type']) ? $_GET['content_type'] : 3005 /* Books */ ), 'btn-source').'</div>';


    //Content URL
    echo '<h2 style="margin-top: 21px;">'.$en_all_12762[12763]['m_name'].'</h2>';
    echo '<div><span class="icon-block">'.$en_all_12762[12763]['m_icon'].'</span><div class="form-group is-empty inline-block"><input type="url" id="content_url" '.( isset($_GET['content_url']) ? ' value="'.$_GET['content_url'].'" ' : '' ).' class="form-control border" placeholder="'.str_replace(' ','',$en_all_12762[12763]['m_desc']).'"></div></div>';


    //Industry Experts
    echo '<h2 style="margin-top: 21px;"><span class="icon-block">&nbsp;</span>'.$en_all_11035[3084]['m_name'].'</h2>';
    echo '<div id="new-children" class="list-group-item list-adder itemsource no-side-padding">
                <div class="input-group border">
                    <span class="input-group-addon addon-lean icon-adder"><span class="icon-block">'.$en_all_11035[3084]['m_icon'].'</span></span>
                    <input type="text"
                           class="form-control source form-control-thick montserrat doupper algolia_search dotransparent add-input"
                           maxlength="' . config_var(11072) . '"
                           id="authorName"
                           placeholder="AUTHOR FULL NAME">
                </div><div class="algolia_pad_search hidden pad_expand"></div></div>';



    //CREATE BUTTON:
    echo '<div style="margin-top:34px;"><a href="javascript:void();" onclick="create_process()" class="btn btn-source">'.$en_all_12762[12771]['m_name'].'</a></div>';


    ?>
</div>