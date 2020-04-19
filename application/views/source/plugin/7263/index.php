<?php

//IDEA MARKS LIST ALL

echo '<p>Below are all the Conditional Step Links:</p>';
echo '<table class="table table-sm table-striped maxout" style="text-align: left;">';

$en_all_6103 = $this->config->item('en_all_6103'); //Link Metadata
$en_all_6186 = $this->config->item('en_all_6186'); //Transaction Status

echo '<tr style="font-weight: bold;">';
echo '<td colspan="4" style="text-align: left;">'.$en_all_6103[6402]['m_icon'].' '.$en_all_6103[6402]['m_name'].'</td>';
echo '</tr>';
$counter = 0;
$total_count = 0;
foreach ($this->DISCOVER_model->ln_fetch(array(
    'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Transaction Status Active
    'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7356')) . ')' => null, //Idea Status Active
    'ln_type_source_id' => 4229, //Idea Link Locked Discovery
    'LENGTH(ln_metadata) > 0' => null,
), array('in_child'), 0, 0) as $in_ln) {
    //Echo HTML format of this message:
    $metadata = unserialize($in_ln['ln_metadata']);
    $mark = echo_in_marks($in_ln);
    if($mark){

        //Fetch parent Idea:
        $parent_ins = $this->IDEA_model->in_fetch(array(
            'in_id' => $in_ln['ln_previous_idea_id'],
        ));

        $counter++;
        echo '<tr>';
        echo '<td style="width: 50px;">'.$counter.'</td>';
        echo '<td style="font-weight: bold; font-size: 1.3em; width: 100px;">'.echo_in_marks($in_ln).'</td>';
        echo '<td>'.$en_all_6186[$in_ln['ln_status_source_id']]['m_icon'].'</td>';
        echo '<td style="text-align: left;">';

        echo '<div>';
        echo '<span style="width:25px; display:inline-block; text-align:center;">'.$en_all_4737[$parent_ins[0]['in_status_source_id']]['m_icon'].'</span>';
        echo '<a href="/idea/'.$parent_ins[0]['in_id'].'">'.$parent_ins[0]['in_title'].'</a>';
        echo '</div>';

        echo '<div>';
        echo '<span style="width:25px; display:inline-block; text-align:center;">'.$en_all_4737[$in_ln['in_status_source_id']]['m_icon'].'</span>';
        echo '<a href="/idea/'.$in_ln['in_id'].'">'.$in_ln['in_title'].' [child]</a>';
        echo '</div>';

        if(count($this->DISCOVER_model->ln_fetch(array(
                'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Transaction Status Active
                'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7356')) . ')' => null, //Idea Status Active
                'in_type_source_id NOT IN (6907,6914)' => null, //NOT AND/OR Lock
                'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_4486')) . ')' => null, //Idea-to-Idea Links
                'ln_next_idea_id' => $in_ln['in_id'],
            ), array('in_parent'))) > 1 || $in_ln['in_type_source_id'] != 6677){

            echo '<div>';
            echo 'NOT COOL';
            echo '</div>';

        } else {

            //Update user progression link type:
            $user_steps = $this->DISCOVER_model->ln_fetch(array(
                'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //DISCOVER COIN
                'ln_previous_idea_id' => $in_ln['in_id'],
                'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
            ), array(), 0);

            $updated = 0;

            echo '<div>Total Steps: '.count($user_steps).'</div>';
            $total_count += count($user_steps);

        }

        echo '</td>';
        echo '</tr>';

    }
}

echo '</table>';

echo 'TOTALS: '.$total_count;

if(1){
    echo '<p>Below are all the fixed step links that award/subtract Completion Marks:</p>';
    echo '<table class="table table-sm table-striped maxout" style="text-align: left;">';

    echo '<tr style="font-weight: bold;">';
    echo '<td colspan="4" style="text-align: left;">Completion Marks</td>';
    echo '</tr>';

    $counter = 0;
    foreach ($this->DISCOVER_model->ln_fetch(array(
        'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Transaction Status Active
        'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7356')) . ')' => null, //Idea Status Active
        'ln_type_source_id' => 4228, //Idea Link Regular Discovery
        'LENGTH(ln_metadata) > 0' => null,
    ), array('in_child'), 0, 0) as $in_ln) {
        //Echo HTML format of this message:
        $metadata = unserialize($in_ln['ln_metadata']);
        $tr__assessment_points = ( isset($metadata['tr__assessment_points']) ? $metadata['tr__assessment_points'] : 0 );
        if($tr__assessment_points!=0){

            //Fetch parent Idea:
            $parent_ins = $this->IDEA_model->in_fetch(array(
                'in_id' => $in_ln['ln_previous_idea_id'],
            ));

            $counter++;
            echo '<tr>';
            echo '<td style="width: 50px;">'.$counter.'</td>';
            echo '<td style="font-weight: bold; font-size: 1.3em; width: 100px;">'.echo_in_marks($in_ln).'</td>';
            echo '<td>'.$en_all_6186[$in_ln['ln_status_source_id']]['m_icon'].'</td>';
            echo '<td style="text-align: left;">';
            echo '<div>';
            echo '<span style="width:25px; display:inline-block; text-align:center;">'.$en_all_4737[$parent_ins[0]['in_status_source_id']]['m_icon'].'</span>';
            echo '<a href="/idea/'.$parent_ins[0]['in_id'].'">'.$parent_ins[0]['in_title'].'</a>';
            echo '</div>';

            echo '<div>';
            echo '<span style="width:25px; display:inline-block; text-align:center;">'.$en_all_4737[$in_ln['in_status_source_id']]['m_icon'].'</span>';
            echo '<a href="/idea/'.$in_ln['in_id'].'">'.$in_ln['in_title'].'</a>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';

        }
    }

    echo '</table>';
}