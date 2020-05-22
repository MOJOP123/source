<?php

//Set activation time so if delete we then redirect here:
$this->session->set_userdata('session_time_7260', time());

$orphan_ideas = $this->IDEA_model->fetch(array(
    ' NOT EXISTS (SELECT 1 FROM mench_interactions WHERE idea__id=read__right AND read__type IN (' . join(',', $this->config->item('sources_id_4486')) . ') AND read__status IN ('.join(',', $this->config->item('sources_id_7360')) /* ACTIVE */.')) ' => null,
    'idea__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
    'idea__id !=' => config_var(12156), //Not the Starting Idea
));

if(count($orphan_ideas) > 0){

    //List orphans:
    echo '<div class="list-group">';
    foreach($orphan_ideas as $idea) {
        echo view_idea($idea, 0, false, true);
    }
    echo '</div>';

} else {
    echo '<span class="icon-block"><i class="fas fa-check-circle"></i></span>No orphans found!';
}