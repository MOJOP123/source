<?php

if(!isset($_GET['e__id']) || !intval($_GET['e__id'])){

    echo 'Missing source ID (Append ?e__id=SOURCE_ID in URL)';

} else {

    //Fetch parent URLs:
    $profiles = $this->X_model->fetch(array(
        'x__type IN (' . join(',', $this->config->item('n___4537')) . ')' => null, //SOURCE LINK URLS
        'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        'e__type IN (' . join(',', $this->config->item('n___7357')) . ')' => null, //PUBLIC
        'x__down' => $_GET['e__id'],
    ), array('x__up'), 0, 0, array('e__spectrum' => 'DESC'));


    echo '<div class="center-info">';
    echo '<div class="text-center"><img src="/img/mench.png" class="mench-spin mench-large" /></div>';
    echo '<p style="margin-top:13px; text-align: center;">'.view_shuffle_message(12694).'</p>';
    echo '</div>';


    if(!count($profiles)){

        $this->X_model->create(array(
            'x__down' => $_GET['e__id'],
            'x__message' => 'Failed to find URL',
            'x__type' => 4246, //Platform Bug Reports
            'x__source' => ( $member_e ? $member_e['e__id'] : 0 ),
            'x__up' => 7146,
            'x__down' => $_GET['e__id'],
        ));

        js_redirect('/', 1597);

    } else {

        js_redirect($profiles[0]['x__message'], 1597);

    }

}
