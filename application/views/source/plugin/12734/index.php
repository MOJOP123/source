<?php

$active_ins = $this->IDEA_model->in_fetch(array(
    'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7356')) . ')' => null, //ACTIVE
), ( isset($_GET['limit']) ? $_GET['limit'] : 0 ));
$found = 0;
foreach($active_ins as $count=>$in){

    $recursive_children = $this->IDEA_model->in_recursive_child_ids($in['in_id'], false);
    if(count($recursive_children) > 0){
        $recursive_parents = $this->IDEA_model->in_recursive_parents($in['in_id']);
        foreach ($recursive_parents as $grand_parent_ids) {
            $crossovers = array_intersect($recursive_children, $grand_parent_ids);
            if(count($crossovers) > 0){
                //Ooooopsi, this should not happen:
                echo $in['in_titile'].' Has Parent/Child crossover for #'.join(' & #', $crossovers).'<hr />';
                $found++;
                break; //Otherwise too show...
            }
        }
    }
}

echo 'Found '.$found.' Crossovers.';
