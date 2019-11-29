<?php

//Generate completion stats:
$course_details = '';
$total_enrolled = 0;
$total_completed = 0;


//FEATUREF BLOGD
foreach($this->READ_model->ln_fetch(array(
    'in_completion_method_entity_id IN (' . join(',', $this->config->item('en_ids_7582')) . ')' => null, //READ LOGIN REQUIRED
    'in_status_entity_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //Intent Statuses Public
    'ln_status_entity_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
    'ln_type_entity_id' => 4601, //BLOG KEYWORDS
    'ln_parent_entity_id IN (' . join(',', featured_topic_ids()) . ')' => null,
), array('in_child'), 0) as $in_published_tree){

    //Count Enrolled Users:
    $enrolled_users = $this->READ_model->ln_fetch(array(
        'ln_parent_intent_id' => $in_published_tree['in_id'],
        'ln_type_entity_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //Action Plan Steps Progressed
        'ln_status_entity_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
    ), array(), 0, 0, array(), 'COUNT(ln_id) as totals');

    if($enrolled_users[0]['totals'] < 1){
        continue;
    }

    //Determine the final common step to determine completion rate:
    $in_metadata = unserialize($in_published_tree['in_metadata']);
    $common_steps = array_flatten($in_metadata['in__metadata_common_steps']);
    $completed_users = $this->READ_model->ln_fetch(array(
        'ln_parent_intent_id' => end($common_steps),
        'ln_type_entity_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //Action Plan Steps Progressed
        'ln_status_entity_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Link Statuses Public
    ), array(), 0, 0, array(), 'COUNT(ln_id) as totals');

    $total_enrolled += $enrolled_users[0]['totals'];
    $total_completed += $completed_users[0]['totals'];
    $completion_rate = $completed_users[0]['totals']/$enrolled_users[0]['totals']*100;

    $course_details .= '<tr class="panel-title down-border">';
    $course_details .= '<td style="text-align: left;"><a href="/read/'.$in_published_tree['in_id'].'">'.echo_in_outcome($in_published_tree['in_outcome']).'</a></td>';
    $course_details .= '<td style="text-align: left;">'.number_format($enrolled_users[0]['totals'], 0).'</td>';
    $course_details .= '<td style="text-align: left;">'.number_format($completion_rate, ( $completion_rate<100 && $completion_rate>0 ? 1 : 0 )).'%</td>';
    $course_details .= '</tr>';

}

echo '<div class="container">';

echo '<h1 style="margin-bottom:30px;">READ RATES</h1>';

echo '<table class="table table-sm table-striped stats-table mini-stats-table" style="font-size: 0.8em;">';

echo '<tr class="panel-title down-border copy-btn-done">';
echo '<td style="text-align: left;">ALL BLOGS</td>';
echo '<td style="text-align: left;">'.number_format($total_enrolled, 0).'</td>';
echo '<td style="text-align: left;">'.( $total_enrolled>0 ? number_format(($total_completed/$total_enrolled*100), 1) : 0 ).'%</td>';
echo '</tr>';


echo '<tr class="panel-title down-border">';
echo '<td style="text-align: left;">BLOGS</td>';
echo '<td style="text-align: left;">READERS</td>';
echo '<td style="text-align: left;">COMPLETED</td>';
echo '</tr>';



echo $course_details;

echo '</table>';
echo '</div>';

?>
