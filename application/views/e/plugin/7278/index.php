<?php

/*
 *
 * Populates the nodes and edges table for
 * Gephi https://gephi.org network visualizer
 *
 * */


//Empty both tables:
$this->db->query("TRUNCATE TABLE public.gephi_edges CONTINUE IDENTITY RESTRICT;");
$this->db->query("TRUNCATE TABLE public.gephi_nodes CONTINUE IDENTITY RESTRICT;");

//Load IDEA LINKS:
$e___4593 = $this->config->item('e___4593');

//To make sure Idea/source IDs are unique:
$id_prefix = array(
    4535 => 100,
    4536 => 200,
);

//Size of nodes:
$node_size = array(
    4535 => 3,
    4536 => 2,
    'msg' => 1,
);

//Add Ideas:
$ideas = $this->I_model->fetch(array(
    'i__status IN (' . join(',', $this->config->item('e___n_7356')) . ')' => null, //ACTIVE
));
foreach($ideas as $in){

    //Prep metadata:
    $i__metadata = ( strlen($in['i__metadata']) > 0 ? unserialize($in['i__metadata']) : array());

    //Add Idea node:
    $this->db->insert('gephi_nodes', array(
        'id' => $id_prefix[4535].$in['i__id'],
        'label' => $in['i__title'],
        //'size' => ( isset($i__metadata['i___max_seconds']) ? round(($i__metadata['i___max_seconds']/3600),0) : 0 ), //Max time
        'size' => $node_size[4535],
        'node_type' => 1, //Idea
        'node_status' => $in['i__status'],
    ));

    //Fetch children:
    foreach($this->X_model->fetch(array(
        'x__status IN (' . join(',', $this->config->item('e___n_7360')) . ')' => null, //ACTIVE
        'i__status IN (' . join(',', $this->config->item('e___n_7356')) . ')' => null, //ACTIVE
        'x__type IN (' . join(',', $this->config->item('e___n_4486')) . ')' => null, //IDEA LINKS
        'x__left' => $in['i__id'],
    ), array('x__right'), 0, 0) as $next_idea){

        $this->db->insert('gephi_edges', array(
            'source' => $id_prefix[4535].$next_idea['x__left'],
            'target' => $id_prefix[4535].$next_idea['x__right'],
            'label' => $e___4593[$next_idea['x__type']]['m_name'], //TODO maybe give visibility to condition here?
            'weight' => 1,
            'edge_type' => $next_idea['x__type'],
            'edge_status' => $next_idea['x__status'],
        ));

    }
}


//Add sources:
$sources = $this->E_model->fetch(array(
    'e__status IN (' . join(',', $this->config->item('e___n_7358')) . ')' => null, //ACTIVE
));
foreach($sources as $en){

    //Add source node:
    $this->db->insert('gephi_nodes', array(
        'id' => $id_prefix[4536].$en['e__id'],
        'label' => $en['e__title'],
        'size' => $node_size[4536] ,
        'node_type' => 2, //Player
        'node_status' => $en['e__status'],
    ));

    //Fetch children:
    foreach($this->X_model->fetch(array(
        'x__status IN (' . join(',', $this->config->item('e___n_7360')) . ')' => null, //ACTIVE
        'e__status IN (' . join(',', $this->config->item('e___n_7358')) . ')' => null, //ACTIVE
        'x__type IN (' . join(',', $this->config->item('e___n_4592')) . ')' => null, //SOURCE LINKS
        'x__up' => $en['e__id'],
    ), array('x__down'), 0, 0) as $e_child){

        $this->db->insert('gephi_edges', array(
            'source' => $id_prefix[4536].$e_child['x__up'],
            'target' => $id_prefix[4536].$e_child['x__down'],
            'label' => $e___4593[$e_child['x__type']]['m_name'].': '.$e_child['x__message'],
            'weight' => 1,
            'edge_type' => $e_child['x__type'],
            'edge_status' => $e_child['x__status'],
        ));

    }
}

//Add messages:
$messages = $this->X_model->fetch(array(
    'x__status IN (' . join(',', $this->config->item('e___n_7360')) . ')' => null, //ACTIVE
    'i__status IN (' . join(',', $this->config->item('e___n_7356')) . ')' => null, //ACTIVE
    'x__type IN (' . join(',', $this->config->item('e___n_4485')) . ')' => null, //IDEA NOTES
), array('x__right'), 0, 0);
foreach($messages as $message) {

    //Add message node:
    $this->db->insert('gephi_nodes', array(
        'id' => $message['x__id'],
        'label' => $e___4593[$message['x__type']]['m_name'] . ': ' . $message['x__message'],
        'size' => $node_size['msg'],
        'node_type' => $message['x__type'], //Message type
        'node_status' => $message['x__status'],
    ));

    //Add child idea link:
    $this->db->insert('gephi_edges', array(
        'source' => $message['x__id'],
        'target' => $id_prefix[4535].$message['x__right'],
        'label' => 'Child Idea',
        'weight' => 1,
    ));

    //Add parent idea link?
    if ($message['x__left'] > 0) {
        $this->db->insert('gephi_edges', array(
            'source' => $id_prefix[4535].$message['x__left'],
            'target' => $message['x__id'],
            'label' => 'Parent Idea',
            'weight' => 1,
        ));
    }

    //Add parent source link?
    if ($message['x__up'] > 0) {
        $this->db->insert('gephi_edges', array(
            'source' => $id_prefix[4536].$message['x__up'],
            'target' => $message['x__id'],
            'label' => 'Parent Source',
            'weight' => 1,
        ));
    }

}

echo count($ideas).' ideas & '.count($sources).' sources & '.count($messages).' messages synced.';