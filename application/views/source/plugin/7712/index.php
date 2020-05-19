<?php

$all_steps = 0;
$all_children = 0;
$updated = 0;

foreach($this->IDEA_model->fetch(array(
    'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7356')) . ')' => null, //ACTIVE
    'in_type_source_id IN (' . join(',', $this->config->item('en_ids_7712')) . ')' => null,
), 0, 0, array('in_id' => 'DESC')) as $count => $in) {

    echo '<div>'.($count+1).') '.view_en_cache('en_all_4737' /* Idea Status */, $in['in_status_source_id']).' '.view_en_cache('en_all_6193' /* OR Ideas */, $in['in_type_source_id']).' <b><a href="/idea/go/'.$in['in_id'].'">'.view_in_title($in).'</a></b></div>';

    echo '<ul>';
    //Fetch all children for this OR:
    foreach($this->READ_model->fetch(array(
        'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //ACTIVE
        'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7356')) . ')' => null, //ACTIVE
        'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_12840')) . ')' => null, //IDEA LINKS TWO-WAY
        'ln_previous_idea_id' => $in['in_id'],
    ), array('in_next'), 0, 0, array('ln_order' => 'ASC')) as $child_or){

        $user_steps = $this->READ_model->fetch(array(
            'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //READ COIN
            'ln_previous_idea_id' => $child_or['in_id'],
            'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //PUBLIC
        ), array(), 0);
        $all_steps += count($user_steps);

        $all_children++;
        echo '<li>'.view_en_cache('en_all_6186' /* Transaction Status */, $child_or['ln_status_source_id']).' '.view_en_cache('en_all_4737' /* Idea Status */, $child_or['in_status_source_id']).' '.view_en_cache('en_all_7585', $child_or['in_type_source_id']).' <a href="/idea/go/'.$child_or['in_id'].'">'.view_in_title($child_or).'</a>'.( count($user_steps) > 0 ? ' / Steps: '.count($user_steps) : '' ).'</li>';
    }
    echo '</ul>';
    echo '<hr />';
}

echo 'READ: '.$all_steps.( $updated > 0 ? ' ('.$updated.' updated)' : '' ).' across '.$all_children.' answers';

