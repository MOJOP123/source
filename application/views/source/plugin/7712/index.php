<?php

$all_reads = 0;
$all_children = 0;
$updated = 0;

foreach($this->IDEA_model->fetch(array(
    'idea__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
    'idea__type IN (' . join(',', $this->config->item('sources_id_7712')) . ')' => null,
), 0, 0, array('idea__id' => 'DESC')) as $count => $in) {

    echo '<div>'.($count+1).') '.view_cache('sources__4737' /* Idea Status */, $in['idea__status']).' '.view_cache('sources__6193' /* OR Ideas */, $in['idea__type']).' <b><a href="/g'.$in['idea__id'].'">'.view_idea__title($in).'</a></b></div>';

    echo '<ul>';
    //Fetch all children for this OR:
    foreach($this->READ_model->fetch(array(
        'read__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
        'idea__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
        'read__type IN (' . join(',', $this->config->item('sources_id_12840')) . ')' => null, //IDEA LINKS TWO-WAY
        'read__left' => $in['idea__id'],
    ), array('read__right'), 0, 0, array('read__sort' => 'ASC')) as $child_or){

        $user_reads = $this->READ_model->fetch(array(
            'read__type IN (' . join(',', $this->config->item('sources_id_6255')) . ')' => null, //READ COIN
            'read__left' => $child_or['idea__id'],
            'read__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
        ), array(), 0);
        $all_reads += count($user_reads);

        $all_children++;
        echo '<li>'.view_cache('sources__6186' /* Read Status */, $child_or['read__status']).' '.view_cache('sources__4737' /* Idea Status */, $child_or['idea__status']).' '.view_cache('sources__7585', $child_or['idea__type']).' <a href="/g'.$child_or['idea__id'].'">'.view_idea__title($child_or).'</a>'.( count($user_reads) > 0 ? ' / Steps: '.count($user_reads) : '' ).'</li>';
    }
    echo '</ul>';
    echo '<hr />';
}

echo 'READ: '.$all_reads.( $updated > 0 ? ' ('.$updated.' updated)' : '' ).' across '.$all_children.' answers';

