<?php

if(isset($_GET['update_u_icons'])){

    $base_filters = array(
        'x__up' => 4430, //Members
        'x__type IN (' . join(',', $this->config->item('n___4592')) . ')' => null, //SOURCE LINKS
        'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        'e__type IN (' . join(',', $this->config->item('n___7357')) . ')' => null, //PUBLIC
    );

    if(!isset($_GET['force'])) {
        $base_filters['(LENGTH(e__icon) < 1 OR e__icon IS NULL)'] = null;
    }

    $updated = 0;
    foreach($this->X_model->fetch($base_filters, array('x__down'), 0) as $mench_u){
        $updated += $this->E_model->update($mench_u['e__id'], array(
            'e__icon' => random_avatar(),
        ));
    }
    echo '<span class="icon-block"><i class="fas fa-check-circle"></i></span>'.$updated.' Member profiles updated with new random animal icons';
}

for($i=0;$i<750;$i++){
    echo '<span class="icon-block">'.random_avatar().'</span>';
}