<?php

//List all Feature Ideas
echo '<div class="list-group row">';
foreach($this->I_model->fetch(array(
    'i__type IN (' . join(',', $this->config->item('n___12138')) . ')' => null, //FEATURED
), 0, 0, array('i__weight' => 'DESC')) as $i){
    echo view_i_cover($i);
}
echo '</div>';