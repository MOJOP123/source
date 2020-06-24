<?php

//IDEA MARKS LIST ALL

echo '<p>Below are all the Conditional Step Links:</p>';
echo '<table class="table table-sm table-striped maxout" style="text-align: left;">';

$sources__6103 = $this->config->item('sources__6103'); //Link Metadata
$sources__6186 = $this->config->item('sources__6186'); //Interaction Status

echo '<tr style="font-weight: bold;">';
echo '<td colspan="4" style="text-align: left;">'.$sources__6103[6402]['m_icon'].' '.$sources__6103[6402]['m_name'].'</td>';
echo '</tr>';
$counter = 0;
$total_count = 0;
foreach($this->DISCOVER_model->fetch(array(
    'x__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
    'i__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
    'x__type IN (' . join(',', $this->config->item('sources_id_12842')) . ')' => null, //IDEA LINKS ONE-WAY
    'LENGTH(x__metadata) > 0' => null,
), array('x__right'), 0, 0) as $i_discover) {
    //Echo HTML format of this message:
    $metadata = unserialize($i_discover['x__metadata']);
    $mark = view_i_marks($i_discover);
    if($mark){

        //Fetch parent Idea:
        $previous_ideas = $this->MAP_model->fetch(array(
            'i__id' => $i_discover['x__left'],
        ));

        $counter++;
        echo '<tr>';
        echo '<td style="width: 50px;">'.$counter.'</td>';
        echo '<td style="font-weight: bold; font-size: 1.3em; width: 100px;">'.view_i_marks($i_discover).'</td>';
        echo '<td>'.$sources__6186[$i_discover['x__status']]['m_icon'].'</td>';
        echo '<td style="text-align: left;">';

        echo '<div>';
        echo '<span style="width:25px; display:inline-block; text-align:center;">'.$sources__4737[$previous_ideas[0]['i__status']]['m_icon'].'</span>';
        echo '<a href="/map/i_go/'.$previous_ideas[0]['i__id'].'">'.$previous_ideas[0]['i__title'].'</a>';
        echo '</div>';

        echo '<div>';
        echo '<span style="width:25px; display:inline-block; text-align:center;">'.$sources__4737[$i_discover['i__status']]['m_icon'].'</span>';
        echo '<a href="/map/i_go/'.$i_discover['i__id'].'">'.$i_discover['i__title'].' [child]</a>';
        echo '</div>';

        if(count($this->DISCOVER_model->fetch(array(
                'x__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
                'i__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
                'i__type NOT IN (6907,6914)' => null, //NOT AND/OR Lock
                'x__type IN (' . join(',', $this->config->item('sources_id_4486')) . ')' => null, //IDEA LINKS
                'x__right' => $i_discover['i__id'],
            ), array('x__left'))) > 1 || $i_discover['i__type'] != 6677){

            echo '<div>';
            echo 'NOT COOL';
            echo '</div>';

        } else {

            //Update user progression link type:
            $user_discoveries = $this->DISCOVER_model->fetch(array(
                'x__type IN (' . join(',', $this->config->item('sources_id_6255')) . ')' => null, //DISCOVER COIN
                'x__left' => $i_discover['i__id'],
                'x__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
            ), array(), 0);

            $updated = 0;

            echo '<div>Total Steps: '.count($user_discoveries).'</div>';
            $total_count += count($user_discoveries);

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
    foreach($this->DISCOVER_model->fetch(array(
        'x__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
        'i__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
        'x__type IN (' . join(',', $this->config->item('sources_id_12840')) . ')' => null, //IDEA LINKS TWO-WAY
        'LENGTH(x__metadata) > 0' => null,
    ), array('x__right'), 0, 0) as $i_discover) {
        //Echo HTML format of this message:
        $metadata = unserialize($i_discover['x__metadata']);
        $tr__assessment_points = ( isset($metadata['tr__assessment_points']) ? $metadata['tr__assessment_points'] : 0 );
        if($tr__assessment_points!=0){

            //Fetch parent Idea:
            $previous_ideas = $this->MAP_model->fetch(array(
                'i__id' => $i_discover['x__left'],
            ));

            $counter++;
            echo '<tr>';
            echo '<td style="width: 50px;">'.$counter.'</td>';
            echo '<td style="font-weight: bold; font-size: 1.3em; width: 100px;">'.view_i_marks($i_discover).'</td>';
            echo '<td>'.$sources__6186[$i_discover['x__status']]['m_icon'].'</td>';
            echo '<td style="text-align: left;">';
            echo '<div>';
            echo '<span style="width:25px; display:inline-block; text-align:center;">'.$sources__4737[$previous_ideas[0]['i__status']]['m_icon'].'</span>';
            echo '<a href="/map/i_go/'.$previous_ideas[0]['i__id'].'">'.$previous_ideas[0]['i__title'].'</a>';
            echo '</div>';

            echo '<div>';
            echo '<span style="width:25px; display:inline-block; text-align:center;">'.$sources__4737[$i_discover['i__status']]['m_icon'].'</span>';
            echo '<a href="/map/i_go/'.$i_discover['i__id'].'">'.$i_discover['i__title'].'</a>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';

        }
    }

    echo '</table>';
}