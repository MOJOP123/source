<?php

$stats = array(
    'ideas' => 0,
    'source_missing' => 0,
    'note_deleted' => 0,
    'is_deleted' => 0,
    'creator_missing' => 0,
    'creator_extra' => 0,
    'creator_fixed' => 0,
    'source_duplicate' => 0,
);

//FInd and delete duplicate sources:
foreach($this->MAP_model->fetch() as $in) {

    $stats['ideas']++;

    $is_deleted = !in_array($in['i__status'], $this->config->item('sources_id_7356'));

    //Scan sources:
    $idea_sources = $this->DISCOVER_model->fetch(array(
        'x__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
        'x__type IN (' . join(',', $this->config->item('sources_id_12273')) . ')' => null, //IDEA COIN
        'x__right' => $in['i__id'],
        '(x__up > 0 OR x__down > 0)' => null, //MESSAGES MUST HAVE A SOURCE REFERENCE TO ISSUE IDEA COINS
    ));

    $idea_creators = $this->DISCOVER_model->fetch(array(
        'x__type' => 4250, //New Idea Created
        'x__right' => $in['i__id'],
    ), array(), 0, 0, array('x__id' => 'ASC')); //Order in case we have extra & need to remove

    $idea_notes = $this->DISCOVER_model->fetch(array( //Idea Links
        'x__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
        'x__type IN (' . join(',', $this->config->item('sources_id_4485')) . ')' => null, //IDEA NOTES
        'x__right' => $in['i__id'],
    ), array(), 0);

    if(!count($idea_creators)) {
        $stats['creator_missing']++;
        $this->DISCOVER_model->create(array(
            'x__player' => $session_source['e__id'],
            'x__right' => $in['i__id'],
            'x__message' => $in['i__title'],
            'x__type' => 4250, //New Idea Created
        ));
    } elseif(count($idea_creators) >= 2) {
        //Remove extra:
        foreach($idea_creators as $count => $idea_source_tr){
            if($count == 0){
                continue; //Keep first one
            } else {
                $stats['creator_extra']++;
                $this->db->query("DELETE FROM mench__x WHERE x__id=".$idea_source_tr['x__id']);
            }
        }
    }


    if(!$is_deleted && !count($idea_sources)){

        //Missing SOURCE
        $stats['source_missing']++;
        $creator_id = ( count($idea_sources) ? $idea_sources[0]['x__player'] : $session_source['x__up'] );
        $this->DISCOVER_model->create(array(
            'x__type' => 4983, //IDEA COIN
            'x__player' => $creator_id,
            'x__up' => $creator_id,
            'x__message' => '@'.$creator_id,
            'x__right' => $in['i__id'],
        ));

    } elseif($is_deleted && count($idea_notes)){

        //Extra SOURCES
        foreach($idea_notes as $idea_note){
            //Delete this link:
            $stats['note_deleted'] += $this->DISCOVER_model->update($idea_note['x__id'], array(
                'x__status' => 6173, //Link Deleted
            ), $session_source['e__id'], 10686 /* Idea Link Unpublished */);
        }

    }
}

echo nl2br(print_r($stats, true));