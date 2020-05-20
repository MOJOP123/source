<?php

//IDEA MARKS LIST ALL

echo '<p>Below are all the Conditional Step Links:</p>';
echo '<table class="table table-sm table-striped maxout" style="text-align: left;">';

$sources__6103 = $this->config->item('sources__6103'); //Link Metadata
$sources__6186 = $this->config->item('sources__6186'); //Read Status

echo '<tr style="font-weight: bold;">';
echo '<td colspan="4" style="text-align: left;">'.$sources__6103[6402]['m_icon'].' '.$sources__6103[6402]['m_name'].'</td>';
echo '</tr>';
$counter = 0;
$total_count = 0;
foreach($this->READ_model->fetch(array(
    'read__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
    'idea__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
    'read__type IN (' . join(',', $this->config->item('sources_id_12842')) . ')' => null, //IDEA LINKS ONE-WAY
    'LENGTH(read__metadata) > 0' => null,
), array('idea_next'), 0, 0) as $in_ln) {
    //Echo HTML format of this message:
    $metadata = unserialize($in_ln['read__metadata']);
    $mark = view_in_marks($in_ln);
    if($mark){

        //Fetch parent Idea:
        $parent_ins = $this->IDEA_model->fetch(array(
            'idea__id' => $in_ln['read__left'],
        ));

        $counter++;
        echo '<tr>';
        echo '<td style="width: 50px;">'.$counter.'</td>';
        echo '<td style="font-weight: bold; font-size: 1.3em; width: 100px;">'.view_in_marks($in_ln).'</td>';
        echo '<td>'.$sources__6186[$in_ln['read__status']]['m_icon'].'</td>';
        echo '<td style="text-align: left;">';

        echo '<div>';
        echo '<span style="width:25px; display:inline-block; text-align:center;">'.$sources__4737[$parent_ins[0]['idea__status']]['m_icon'].'</span>';
        echo '<a href="/idea/go/'.$parent_ins[0]['idea__id'].'">'.$parent_ins[0]['idea__title'].'</a>';
        echo '</div>';

        echo '<div>';
        echo '<span style="width:25px; display:inline-block; text-align:center;">'.$sources__4737[$in_ln['idea__status']]['m_icon'].'</span>';
        echo '<a href="/idea/go/'.$in_ln['idea__id'].'">'.$in_ln['idea__title'].' [child]</a>';
        echo '</div>';

        if(count($this->READ_model->fetch(array(
                'read__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
                'idea__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
                'idea__type NOT IN (6907,6914)' => null, //NOT AND/OR Lock
                'read__type IN (' . join(',', $this->config->item('sources_id_4486')) . ')' => null, //IDEA LINKS
                'read__right' => $in_ln['idea__id'],
            ), array('idea_previous'))) > 1 || $in_ln['idea__type'] != 6677){

            echo '<div>';
            echo 'NOT COOL';
            echo '</div>';

        } else {

            //Update user progression link type:
            $user_steps = $this->READ_model->fetch(array(
                'read__type IN (' . join(',', $this->config->item('sources_id_6255')) . ')' => null, //READ COIN
                'read__left' => $in_ln['idea__id'],
                'read__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
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
    foreach($this->READ_model->fetch(array(
        'read__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
        'idea__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
        'read__type IN (' . join(',', $this->config->item('sources_id_12840')) . ')' => null, //IDEA LINKS TWO-WAY
        'LENGTH(read__metadata) > 0' => null,
    ), array('idea_next'), 0, 0) as $in_ln) {
        //Echo HTML format of this message:
        $metadata = unserialize($in_ln['read__metadata']);
        $tr__assessment_points = ( isset($metadata['tr__assessment_points']) ? $metadata['tr__assessment_points'] : 0 );
        if($tr__assessment_points!=0){

            //Fetch parent Idea:
            $parent_ins = $this->IDEA_model->fetch(array(
                'idea__id' => $in_ln['read__left'],
            ));

            $counter++;
            echo '<tr>';
            echo '<td style="width: 50px;">'.$counter.'</td>';
            echo '<td style="font-weight: bold; font-size: 1.3em; width: 100px;">'.view_in_marks($in_ln).'</td>';
            echo '<td>'.$sources__6186[$in_ln['read__status']]['m_icon'].'</td>';
            echo '<td style="text-align: left;">';
            echo '<div>';
            echo '<span style="width:25px; display:inline-block; text-align:center;">'.$sources__4737[$parent_ins[0]['idea__status']]['m_icon'].'</span>';
            echo '<a href="/idea/go/'.$parent_ins[0]['idea__id'].'">'.$parent_ins[0]['idea__title'].'</a>';
            echo '</div>';

            echo '<div>';
            echo '<span style="width:25px; display:inline-block; text-align:center;">'.$sources__4737[$in_ln['idea__status']]['m_icon'].'</span>';
            echo '<a href="/idea/go/'.$in_ln['idea__id'].'">'.$in_ln['idea__title'].'</a>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';

        }
    }

    echo '</table>';
}