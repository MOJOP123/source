<?php

//Construct filters based on GET variables:
$filters = array();
$join_by = array();

//We have a special OR filter when combined with any_en_id & any_in_id
$any_in_en_set = ( ( isset($_GET['any_en_id']) && $_GET['any_en_id'] > 0 ) || ( isset($_GET['any_in_id']) && $_GET['any_in_id'] > 0 ) );
$parent_tr_filter = ( isset($_GET['ln_parent_link_id']) && $_GET['ln_parent_link_id'] > 0 ? ' OR ln_parent_link_id = '.$_GET['ln_parent_link_id'].' ' : false );



//Apply filters:
if(isset($_GET['in_status_entity_id']) && strlen($_GET['in_status_entity_id']) > 0){
    if(isset($_GET['ln_type_entity_id']) && $_GET['ln_type_entity_id']==4250){ //Intent created
        //Filter intent status based on
        $join_by = array('in_child');

        if (substr_count($_GET['in_status_entity_id'], ',') > 0) {
            //This is multiple IDs:
            $filters['( in_status_entity_id IN (' . $_GET['in_status_entity_id'] . '))'] = null;
        } else {
            $filters['in_status_entity_id'] = intval($_GET['in_status_entity_id']);
        }
    } else {
        unset($_GET['in_status_entity_id']);
    }
}



if(isset($_GET['in_scope_entity_id']) && strlen($_GET['in_scope_entity_id']) > 0){
    if(isset($_GET['ln_type_entity_id']) && $_GET['ln_type_entity_id']==4250){ //Intent created
        //Filter intent status based on
        $join_by = array('in_child');

        if (substr_count($_GET['in_scope_entity_id'], ',') > 0) {
            //This is multiple IDs:
            $filters['( in_scope_entity_id IN (' . $_GET['in_scope_entity_id'] . '))'] = null;
        } else {
            $filters['in_scope_entity_id'] = intval($_GET['in_scope_entity_id']);
        }
    } else {
        unset($_GET['in_scope_entity_id']);
    }
}




if(isset($_GET['in_verb_entity_id']) && strlen($_GET['in_verb_entity_id']) > 0){
    if(isset($_GET['ln_type_entity_id']) && $_GET['ln_type_entity_id']==4250){ //Intent created
        //Filter intent status based on
        $join_by = array('in_child');
        if (substr_count($_GET['in_verb_entity_id'], ',') > 0) {
            //This is multiple IDs:
            $filters['( in_verb_entity_id IN (' . $_GET['in_verb_entity_id'] . '))'] = null;
        } else {
            $filters['in_verb_entity_id'] = intval($_GET['in_verb_entity_id']);
        }
    } else {
        unset($_GET['in_verb_entity_id']);
    }
}

if(isset($_GET['in_subtype_entity_id']) && strlen($_GET['in_subtype_entity_id']) > 0){
    if(isset($_GET['ln_type_entity_id']) && $_GET['ln_type_entity_id']==4250){ //Intent created
        //Filter intent status based on
        $join_by = array('in_child');
        if (substr_count($_GET['in_subtype_entity_id'], ',') > 0) {
            //This is multiple IDs:
            $filters['( in_subtype_entity_id IN (' . $_GET['in_subtype_entity_id'] . '))'] = null;
        } else {
            $filters['in_subtype_entity_id'] = intval($_GET['in_subtype_entity_id']);
        }
    } else {
        unset($_GET['in_subtype_entity_id']);
    }
}

if(isset($_GET['en_status_entity_id']) && strlen($_GET['en_status_entity_id']) > 0){
    if(isset($_GET['ln_type_entity_id']) && $_GET['ln_type_entity_id']==4251){ //Entity Created

        //Filter intent status based on
        $join_by = array('en_child');

        if (substr_count($_GET['en_status_entity_id'], ',') > 0) {
            //This is multiple IDs:
            $filters['( en_status_entity_id IN (' . $_GET['en_status_entity_id'] . '))'] = null;
        } else {
            $filters['en_status_entity_id'] = intval($_GET['en_status_entity_id']);
        }
    } else {
        unset($_GET['en_status_entity_id']);
    }
}

if(isset($_GET['ln_status_entity_id']) && strlen($_GET['ln_status_entity_id']) > 0){
    if (substr_count($_GET['ln_status_entity_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_status_entity_id IN (' . $_GET['ln_status_entity_id'] . '))'] = null;
    } else {
        $filters['ln_status_entity_id'] = intval($_GET['ln_status_entity_id']);
    }
}

if(isset($_GET['ln_creator_entity_id']) && strlen($_GET['ln_creator_entity_id']) > 0){
    if (substr_count($_GET['ln_creator_entity_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_creator_entity_id IN (' . $_GET['ln_creator_entity_id'] . '))'] = null;
    } elseif (intval($_GET['ln_creator_entity_id']) > 0) {
        $filters['ln_creator_entity_id'] = $_GET['ln_creator_entity_id'];
    }
}


if(isset($_GET['ln_parent_entity_id']) && strlen($_GET['ln_parent_entity_id']) > 0){
    if (substr_count($_GET['ln_parent_entity_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_parent_entity_id IN (' . $_GET['ln_parent_entity_id'] . '))'] = null;
    } elseif (intval($_GET['ln_parent_entity_id']) > 0) {
        $filters['ln_parent_entity_id'] = $_GET['ln_parent_entity_id'];
    }
}

if(isset($_GET['ln_child_entity_id']) && strlen($_GET['ln_child_entity_id']) > 0){
    if (substr_count($_GET['ln_child_entity_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_child_entity_id IN (' . $_GET['ln_child_entity_id'] . '))'] = null;
    } elseif (intval($_GET['ln_child_entity_id']) > 0) {
        $filters['ln_child_entity_id'] = $_GET['ln_child_entity_id'];
    }
}

if(isset($_GET['ln_parent_intent_id']) && strlen($_GET['ln_parent_intent_id']) > 0){
    if (substr_count($_GET['ln_parent_intent_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_parent_intent_id IN (' . $_GET['ln_parent_intent_id'] . '))'] = null;
    } elseif (intval($_GET['ln_parent_intent_id']) > 0) {
        $filters['ln_parent_intent_id'] = $_GET['ln_parent_intent_id'];
    }
}

if(isset($_GET['ln_child_intent_id']) && strlen($_GET['ln_child_intent_id']) > 0){
    if (substr_count($_GET['ln_child_intent_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_child_intent_id IN (' . $_GET['ln_child_intent_id'] . '))'] = null;
    } elseif (intval($_GET['ln_child_intent_id']) > 0) {
        $filters['ln_child_intent_id'] = $_GET['ln_child_intent_id'];
    }
}

if(isset($_GET['ln_parent_link_id']) && strlen($_GET['ln_parent_link_id']) > 0 && !$any_in_en_set){
    if (substr_count($_GET['ln_parent_link_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_parent_link_id IN (' . $_GET['ln_parent_link_id'] . '))'] = null;
    } elseif (intval($_GET['ln_parent_link_id']) > 0) {
        $filters['ln_parent_link_id'] = $_GET['ln_parent_link_id'];
    }
}

if(isset($_GET['ln_id']) && strlen($_GET['ln_id']) > 0){
    if (substr_count($_GET['ln_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_id IN (' . $_GET['ln_id'] . '))'] = null;
    } elseif (intval($_GET['ln_id']) > 0) {
        $filters['ln_id'] = $_GET['ln_id'];
    }
}

if(isset($_GET['any_en_id']) && strlen($_GET['any_en_id']) > 0){
    //We need to look for both parent/child
    if (substr_count($_GET['any_en_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_child_entity_id IN (' . $_GET['any_en_id'] . ') OR ln_parent_entity_id IN (' . $_GET['any_en_id'] . ') OR ln_creator_entity_id IN (' . $_GET['any_en_id'] . ') ' . $parent_tr_filter . ' )'] = null;
    } elseif (intval($_GET['any_en_id']) > 0) {
        $filters['( ln_child_entity_id = ' . $_GET['any_en_id'] . ' OR ln_parent_entity_id = ' . $_GET['any_en_id'] . ' OR ln_creator_entity_id = ' . $_GET['any_en_id'] . $parent_tr_filter . ' )'] = null;
    }
}

if(isset($_GET['any_in_id']) && strlen($_GET['any_in_id']) > 0){
    //We need to look for both parent/child
    if (substr_count($_GET['any_in_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_child_intent_id IN (' . $_GET['any_in_id'] . ') OR ln_parent_intent_id IN (' . $_GET['any_in_id'] . ') ' . $parent_tr_filter . ' )'] = null;
    } elseif (intval($_GET['any_in_id']) > 0) {
        $filters['( ln_child_intent_id = ' . $_GET['any_in_id'] . ' OR ln_parent_intent_id = ' . $_GET['any_in_id'] . $parent_tr_filter . ')'] = null;
    }
}

if(isset($_GET['any_ln_id']) && strlen($_GET['any_ln_id']) > 0){
    //We need to look for both parent/child
    if (substr_count($_GET['any_ln_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['( ln_id IN (' . $_GET['any_ln_id'] . ') OR ln_parent_link_id IN (' . $_GET['any_ln_id'] . '))'] = null;
    } elseif (intval($_GET['any_ln_id']) > 0) {
        $filters['( ln_id = ' . $_GET['any_ln_id'] . ' OR ln_parent_link_id = ' . $_GET['any_ln_id'] . ')'] = null;
    }
}

if(isset($_GET['ln_content_search']) && strlen($_GET['ln_content_search']) > 0){
    $filters['LOWER(ln_content) LIKE'] = '%'.$_GET['ln_content_search'].'%';
}


if(isset($_GET['start_range']) && is_valid_date($_GET['start_range'])){
    $filters['ln_timestamp >='] = $_GET['start_range'].( strlen($_GET['start_range']) <= 10 ? ' 00:00:00' : '' );
}
if(isset($_GET['end_range']) && is_valid_date($_GET['end_range'])){
    $filters['ln_timestamp <='] = $_GET['end_range'].( strlen($_GET['end_range']) <= 10 ? ' 23:59:59' : '' );
}








//Fetch unique link types recorded so far:
$ini_filter = array();
foreach($filters as $key => $value){
    if(!includes_any($key, array('in_status_entity_id', 'in_verb_entity_id', 'in_subtype_entity_id', 'en_status_entity_id', 'in_scope_entity_id'))){
        $ini_filter[$key] = $value;
    }
}



//Make sure its a valid type considering other filters:
if(isset($_GET['ln_type_entity_id'])){

    if (substr_count($_GET['ln_type_entity_id'], ',') > 0) {
        //This is multiple IDs:
        $filters['ln_type_entity_id IN (' . $_GET['ln_type_entity_id'] . ')'] = null;
    } elseif (intval($_GET['ln_type_entity_id']) > 0) {
        $filters['ln_type_entity_id'] = intval($_GET['ln_type_entity_id']);
    }

}


$has_filters = ( count($_GET) > 0 );


$en_all_7368 = $this->config->item('en_all_7368'); //Trainer App

?>

<script>
    var link_filters = '<?= serialize(count($filters) > 0 ? $filters : array()) ?>';
    var link_join_by = '<?= serialize(count($join_by) > 0 ? $join_by : array()) ?>';
</script>
<script src="/js/custom/links-js.js?v=v<?= $this->config->item('app_version') ?>"
        type="text/javascript"></script>

<?php

echo '<div class="row">';
    echo '<div class="'.$this->config->item('css_column_1').'">';

        echo '<h1>'.$en_all_7368[6205]['m_icon'].' Links</h1>';

        echo '<div><a href="javascript:void();" onclick="$(\'.show-filter\').toggleClass(\'hidden\');"><i class="far fa-filter"></i> Toggle Filters</a></div>';


        echo '<div class="inline-box show-filter '.( $has_filters && 0 ? '' : 'hidden' ).'">';
        echo '<form action="" method="GET">';







        echo '<table class="table table-condensed maxout"><tr>';

        //ANY Intent
        echo '<td><div style="padding-right:5px;">';
        echo '<span class="mini-header">Any Intent IDs:</span>';
        echo '<input type="text" name="any_in_id" value="' . ((isset($_GET['any_in_id'])) ? $_GET['any_in_id'] : '') . '" class="form-control border">';
        echo '</div></td>';

        echo '<td><span class="mini-header">Intent Parent IDs:</span><input type="text" name="ln_parent_intent_id" value="' . ((isset($_GET['ln_parent_intent_id'])) ? $_GET['ln_parent_intent_id'] : '') . '" class="form-control border"></td>';

        echo '<td><span class="mini-header">Intent Child IDs:</span><input type="text" name="ln_child_intent_id" value="' . ((isset($_GET['ln_child_intent_id'])) ? $_GET['ln_child_intent_id'] : '') . '" class="form-control border"></td>';

        echo '</tr></table>';







        echo '<table class="table table-condensed maxout"><tr>';

        //ANY Entity
        echo '<td><div style="padding-right:5px;">';
        echo '<span class="mini-header">Any Entity IDs:</span>';
        echo '<input type="text" name="any_en_id" value="' . ((isset($_GET['any_en_id'])) ? $_GET['any_en_id'] : '') . '" class="form-control border">';
        echo '</div></td>';

        echo '<td><span class="mini-header">Creator Entity IDs:</span><input type="text" name="ln_creator_entity_id" value="' . ((isset($_GET['ln_creator_entity_id'])) ? $_GET['ln_creator_entity_id'] : '') . '" class="form-control border"></td>';

        echo '<td><span class="mini-header">Entity Parent IDs:</span><input type="text" name="ln_parent_entity_id" value="' . ((isset($_GET['ln_parent_entity_id'])) ? $_GET['ln_parent_entity_id'] : '') . '" class="form-control border"></td>';

        echo '<td><span class="mini-header">Entity Child IDs:</span><input type="text" name="ln_child_entity_id" value="' . ((isset($_GET['ln_child_entity_id'])) ? $_GET['ln_child_entity_id'] : '') . '" class="form-control border"></td>';

        echo '</tr></table>';





        echo '<table class="table table-condensed maxout"><tr>';

        //ANY Link
        echo '<td><div style="padding-right:5px;">';
        echo '<span class="mini-header">Any Link IDs:</span>';
        echo '<input type="text" name="any_ln_id" value="' . ((isset($_GET['any_ln_id'])) ? $_GET['any_ln_id'] : '') . '" class="form-control border">';
        echo '</div></td>';

        echo '<td><span class="mini-header">Link IDs:</span><input type="text" name="ln_id" value="' . ((isset($_GET['ln_id'])) ? $_GET['ln_id'] : '') . '" class="form-control border"></td>';

        echo '<td><span class="mini-header">Parent Link IDs:</span><input type="text" name="ln_parent_link_id" value="' . ((isset($_GET['ln_parent_link_id'])) ? $_GET['ln_parent_link_id'] : '') . '" class="form-control border"></td>';

        echo '<td><span class="mini-header">Link Status:</span><input type="text" name="ln_status_entity_id" value="' . ((isset($_GET['ln_status_entity_id'])) ? $_GET['ln_status_entity_id'] : '') . '" class="form-control border"></td>';

        echo '</tr></table>';






        echo '<table class="table table-condensed maxout"><tr>';

        //Search
        echo '<td><div style="padding-right:5px;">';
        echo '<span class="mini-header">Link Content Search:</span>';
        echo '<input type="text" name="ln_content_search" value="' . ((isset($_GET['ln_content_search'])) ? $_GET['ln_content_search'] : '') . '" class="form-control border">';
        echo '</div></td>';


        //Link Type Filter Groups
        echo '<td></td>';




//Filters UI:
echo '<table class="table table-condensed maxout"><tr>';

echo '<td valign="top" style="vertical-align: top;"><div style="padding-right:5px;">';
echo '<span class="mini-header">Start Date:</span>';
echo '<input type="date" class="form-control border" name="start_range" value="'.( isset($_GET['start_range']) ? $_GET['start_range'] : '' ).'">';
echo '</div></td>';

echo '<td valign="top" style="vertical-align: top;"><div style="padding-right:5px;">';
echo '<span class="mini-header">End Date:</span>';
echo '<input type="date" class="form-control border" name="end_range" value="'.( isset($_GET['end_range']) ? $_GET['end_range'] : '' ).'">';
echo '</div></td>';



        echo '<td>';
        echo '<div>';
        echo '<span class="mini-header">Link Type:</span>';

        if(isset($_GET['ln_type_entity_id']) && substr_count($_GET['ln_type_entity_id'], ',')>0){

            //We have multiple predefined link types, so we must use a text input:
            echo '<input type="text" name="ln_type_entity_id" value="' . $_GET['ln_type_entity_id'] . '" class="form-control border">';

        } else {

            echo '<select class="form-control border" name="ln_type_entity_id" id="ln_type_entity_id" class="border" style="width: 100% !important;">';

            if(isset($_GET['ln_creator_entity_id'])) {

                //Fetch details for this user:
                $all_link_count = 0;
                $select_ui = '';
                foreach ($this->Links_model->ln_fetch($ini_filter, array('ln_type'), 0, 0, array('en_name' => 'ASC'), 'COUNT(ln_type_entity_id) as total_count, SUM(ABS(ln_words)) as total_words, en_name, ln_type_entity_id', 'ln_type_entity_id, en_name') as $ln) {
                    //Echo drop down:
                    $select_ui .= '<option value="' . $ln['ln_type_entity_id'] . '" ' . ((isset($_GET['ln_type_entity_id']) && $_GET['ln_type_entity_id'] == $ln['ln_type_entity_id']) ? 'selected="selected"' : '') . '>' . $ln['en_name'] . ' ('  . number_format($ln['total_count'], 0) . ')</option>';
                    $all_link_count += $ln['total_count'];
                }

                //Now that we know the total show:
                echo '<option value="0">All ('  . number_format($all_link_count, 0) . ')</option>';
                echo $select_ui;

            } else {

                //Load all fast:
                echo '<option value="0">All Link Types</option>';
                foreach($this->config->item('en_all_4593') /* Link Types */ as $en_id => $m){
                    //Echo drop down:
                    echo '<option value="' . $en_id . '" ' . ((isset($_GET['ln_type_entity_id']) && $_GET['ln_type_entity_id'] == $en_id) ? 'selected="selected"' : '') . '>' . $m['m_name'] . '</option>';
                }

            }

            echo '</select>';


        }

        echo '</div>';

        //Optional Intent/Entity status filter ONLY IF Link Type = Create New Intent/Entity

    echo '<div class="filter-statuses filter-in-status hidden"><span class="mini-header">Intent Status(es)</span><input type="text" name="in_status_entity_id" value="' . ((isset($_GET['in_status_entity_id'])) ? $_GET['in_status_entity_id'] : '') . '" class="form-control border"></div>';

    echo '<div class="filter-statuses filter-in-status hidden"><span class="mini-header">Intent Scope(s)</span><input type="text" name="in_scope_entity_id" value="' . ((isset($_GET['in_scope_entity_id'])) ? $_GET['in_scope_entity_id'] : '') . '" class="form-control border"></div>';

        echo '<div class="filter-statuses filter-in-status hidden"><span class="mini-header">Intent Type(s)</span><input type="text" name="in_subtype_entity_id" value="' . ((isset($_GET['in_subtype_entity_id'])) ? $_GET['in_subtype_entity_id'] : '') . '" class="form-control border"></div>';

        echo '<div class="filter-statuses filter-in-status hidden"><span class="mini-header">Intent Verb(s)</span><input type="text" name="in_verb_entity_id" value="' . ((isset($_GET['in_verb_entity_id'])) ? $_GET['in_verb_entity_id'] : '') . '" class="form-control border"></div>';

        echo '<div class="filter-statuses filter-en-status hidden"><span class="mini-header">Entity Status(es)</span><input type="text" name="en_status_entity_id" value="' . ((isset($_GET['en_status_entity_id'])) ? $_GET['en_status_entity_id'] : '') . '" class="form-control border"></div>';

        echo '</td>';

        echo '</tr></table>';




        echo '</tr></table>';




        echo '<input type="submit" class="btn btn-sm btn-third" value="Apply" />';

        if($has_filters){
            echo ' &nbsp;<a href="/links" style="font-size: 0.8em;">Remove Filters</a>';
        }

        echo '</form>';
        echo '</div>';


        //AJAX Would load content here:
        echo '<div id="link_page_1"></div>';
    echo '</div>';



    //Show left column for intent management
    //TODO support entity management later...
    echo '<div class="'.$this->config->item('css_column_2').'">';
        $this->load->view('view_trainer_app/in_right_column');
    echo '</div>';


echo '</div>';


?>