<?php

if(isset($_GET['update_user_icons'])){

    $base_filters = array(
        'ln_profile_source_id' => 4430, //Players
        'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_4592')) . ')' => null, //SOURCE LINKS
        'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //PUBLIC
        'en_status_source_id IN (' . join(',', $this->config->item('en_ids_7357')) . ')' => null, //PUBLIC
    );

    if(!isset($_GET['force'])) {
        $base_filters['(LENGTH(en_icon) < 1 OR en_icon IS NULL)'] = null;
    }

    $updated = 0;
    foreach($this->LEDGER_model->ln_fetch($base_filters, array('en_portfolio'), 0) as $mench_user){
        $updated += $this->SOURCE_model->en_update($mench_user['en_id'], array(
            'en_icon' => random_player_avatar(),
        ));
    }
    echo '<span class="icon-block"><i class="fas fa-check-circle"></i></span>'.$updated.' User profiles updated with new random animal icons';
}

for($i=0;$i<750;$i++){
    echo '<span class="icon-block">'.random_player_avatar().'</span>';
}