<?php

$e___6287 = $this->config->item('e___6287'); //MENCH PLUGIN
$e___11035 = $this->config->item('e___11035'); //MENCH NAVIGATION

echo '<div class="container">';

    //Plugins, Header & Source:
    echo '<h1 style="padding-top:5px;"><a href="/app/"><span class="icon-block">'.view_e__icon($e___11035[6287]['m_icon']).'</span></a><a href="/@'.$app_e__id.'"><span class="icon-block">'.view_e__icon($e___6287[$app_e__id]['m_icon']).'</span>'.$e___6287[$app_e__id]['m_title'].'</a></h1>';

    //Optional Description:
    if(strlen($e___6287[$app_e__id]['m_message']) > 0){
        echo '<p>'.$e___6287[$app_e__id]['m_message'].'</p>';
    }

    //Load Plugin:
    $this->load->view('e/app/'.$app_e__id.'/index', array(
        'app_e__id' => $app_e__id,
        'user_e' => $user_e,
        'is_u_request' => $is_u_request,
    ));

echo '</div>';
