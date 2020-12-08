<?php

$e___11035 = $this->config->item('e___11035'); //MENCH NAVIGATION

//List Apps:
echo '<div class="container">';
echo '<div class="list-group">';
foreach($this->config->item('e___6287') as $e__id => $m) {

    echo '<a href="/app/'.$e__id.'" class="list-group-item no-side-padding">';

    //SOURCE
    echo '<span class="icon-block">' . view_e__icon($m['m__icon']) . '</span>';
    echo '<b class="montserrat '.extract_icon_color($m['m__icon']).'">'.$m['m__title'].'</b>';
    echo ( strlen($m['m__message']) ? '&nbsp;'.$m['m__message'] : '' );


    //PROFILE
    echo '<div class="pull-right inline-block">';
    foreach($this->X_model->fetch(array(
        'x__type IN (' . join(',', $this->config->item('n___4592')) . ')' => null, //SOURCE LINKS
        'x__down' => $e__id,
        'x__up !=' => 6287,
        'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        'e__type IN (' . join(',', $this->config->item('n___7358')) . ')' => null, //ACTIVE
    ), array('x__up')) as $e_profile){
        echo '<span class="icon-block-img e_child_icon_' . $e_profile['e__id'] . '" data-toggle="tooltip" title="' . $e_profile['e__title'] . (strlen($e_profile['x__message']) > 0 ? ' = ' . $e_profile['x__message'] : '') . '" data-placement="top">' . view_e__icon($e_profile['e__icon']) . '</span>&nbsp;';
    }
    echo '</div>';

    echo '</a>';
}
echo '</div>';
echo '</div>';