    <?php
$e___2738 = $this->config->item('e___2738');
$e___11035 = $this->config->item('e___11035'); //MENCH NAVIGATION

$e_of_i = e_of_i($i_focus['i__id']);
$is_active = in_array($i_focus['i__type'], $this->config->item('n___7356'));
$is_public = in_array($i_focus['i__type'], $this->config->item('n___7355'));
$superpower_13422 = superpower_active(13422, true); //Advance Sourcing
$superpower_14005 = superpower_active(14005, true);

?>

<style>
    .i_child_icon_<?= $i_focus['i__id'] ?> { display:none; }
    <?= ( !$e_of_i ? '.note-editor {display:none;}' : '' ) ?>
</style>

<input type="hidden" id="focus_i__id" value="<?= $i_focus['i__id'] ?>" />
<script src="/application/views/i_layout.js?v=<?= view_memory(6404,11060) ?>" type="text/javascript"></script>

<?php

$e_focus_found = false; //Used to determine the first tab to be opened
$is_north_star = $i_focus['i__id']==view_memory(6404,14002);
$show_previous = $e_of_i && $is_active && !$is_north_star;
$is_in_my_ideas = count($this->X_model->fetch(array(
    'x__up' => $member_e['e__id'],
    'x__right' => $i_focus['i__id'],
    'x__type' => 10573, //MY IDEAS
    'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
)));




echo '<div class="container">';



if(!$e_of_i){

    //DO they already have a request?
    $request_history = $this->X_model->fetch(array(
        'x__source' => $member_e['e__id'],
        'x__status IN (' . join(',', $this->config->item('n___7360')) . ')' => null, //ACTIVE
        'x__type' => 14577,
        'x__right' => $i_focus['i__id'],
    ), array(), 1, 0, array('x__id' => 'DESC'));

    if(count($request_history)){

        echo '<div class="msg alert alert-warning no-margin"><span class="icon-block"><i class="fas fa-exclamation-circle source"></i></span>You submitted your request to join ' . view_time_difference(strtotime($request_history[0]['x__time'])) . ' ago. You will be notified soon.</span></div>';

    } else {

        echo '<div class="msg alert alert-warning no-margin"><span class="icon-block"><i class="fas fa-exclamation-circle source"></i></span>You are not a source for this idea, yet. <span class="inline-block '.superpower_active(10939).'"><a href="/i/i_e_add/'.$i_focus['i__id'].'" class="inline-block css__title">REQUEST TO JOIN</a></span></div>';

    }
}



if($is_north_star) {
    echo '<div class="headline"><span class="icon-block">'.$e___11035[14002]['m__icon'].'</span>'.$e___11035[14002]['m__title'].'</div>';
}


if($show_previous){
    echo '<div class="new-list-11019 list-adder '.superpower_active(10939).'">
                    <div class="input-group border">
                        <a class="input-group-addon addon-lean icon-adder" href="javascript:void(0);" onclick="$(\'#new-list-11019 .add-input\').focus();"><span class="icon-block">'.$e___11035[14014]['m__icon'].'</span></a>
                        <input type="text"
                               class="form-control form-control-thick add-input algolia_search dotransparent"
                               maxlength="' . view_memory(6404,4736) . '"
                               placeholder="'.$e___11035[14014]['m__title'].'">
                    </div></div>';
}
echo '<div id="list-in-11019" class="row top-margin grey-list hideIfEmpty">';
foreach($this->X_model->fetch(array(
    'x__status IN (' . join(',', $this->config->item('n___7360')) . ')' => null, //ACTIVE
    'i__type IN (' . join(',', $this->config->item('n___7356')) . ')' => null, //ACTIVE
    'x__type IN (' . join(',', $this->config->item('n___4486')) . ')' => null, //IDEA LINKS
    'x__right' => $i_focus['i__id'],
), array('x__left'), 0, 0, array('i__spectrum' => 'DESC')) as $previous_i) {
    echo view_i(11019, 0, null, $previous_i, $e_of_i);
}
echo '</div>';




if(isset($_GET['load__e']) && $superpower_14005){
    //Filtered Specific Source:
    $e_filters = $this->E_model->fetch(array(
        'e__id' => intval($_GET['load__e']),
        'e__type IN (' . join(',', $this->config->item('n___7358')) . ')' => null, //ACTIVE
    ));
    if(count($e_filters)){
        echo view__load__e($e_filters[0]);
    }
}








//IDEA TYPE
echo '<div class="inline-block">'.view_input_dropdown(4737, $i_focus['i__type'], 'idea', $e_of_i, true, $i_focus['i__id'], 0).'</div>';

//IDEA TIME
echo '<div class="inline-block left-half-margin '.superpower_active(12700).'">'.view_input_text(4356, $i_focus['i__duration'], $i_focus['i__id'], $e_of_i && $is_active, 0).'</div>';



//IDEA TITLE
echo '<div class="top-margin">';
echo view_input_text(4736, $i_focus['i__title'], $i_focus['i__id'], ($e_of_i && $is_active), 0, true); //, view_i_icon($i_focus)
echo '</div>';


//IDEA MESSAGES:
echo view_i_note_list(4231, false, $i_focus, $this->X_model->fetch(array(
    'x__status IN (' . join(',', $this->config->item('n___7360')) . ')' => null, //ACTIVE
    'x__type' => 4231,
    'x__right' => $i_focus['i__id'],
), array('x__source'), 0, 0, array('x__spectrum' => 'ASC')), $e_of_i, false);














//IDEA LAYOUT
$i_stats = i_stats($i_focus['i__metadata']);
$counter_i = $i_stats['i___6170'];
$tab_group = 11018;
$tab_content = '';
echo '<ul class="nav nav-tabs nav-sm">';
foreach($this->config->item('e___'.$tab_group) as $x__type => $m){


    //Is this a caret menu?
    if(in_array(11040 , $m['m__profile'])){
        echo view_caret($x__type, $m, $i_focus['i__id']);
        continue;
    }

    //Have Needed Superpowers?
    $superpower_actives = array_intersect($this->config->item('n___10957'), $m['m__profile']);
    if(count($superpower_actives) && !superpower_unlocked(end($superpower_actives))){
        continue;
    }


    $counter = null; //Assume no counters
    $ui = '';
    $href = 'href="javascript:void(0);" onclick="loadtab('.$tab_group.','.$x__type.')"';



    if($x__type==13562) {

        if (!$is_north_star) {
            $href = 'href="/' . $i_focus['i__id'] . '"';
        } else {
            //Cannot preview North Star:
            continue;
        }

    } elseif(in_array($x__type, $this->config->item('n___7551'))){

        //Reference Sources Only:
        $i_notes = $this->X_model->fetch(array(
            'x__status IN (' . join(',', $this->config->item('n___7360')) . ')' => null, //ACTIVE
            'x__type' => $x__type,
            'x__right' => $i_focus['i__id'],
        ), array('x__up'), 0, 0, array('x__spectrum' => 'ASC'));
        $counter = count($i_notes);

        $ui .= '<div id="add-e-' .$x__type . '" class="list-group e-adder" style="margin-bottom:41px;">';
        foreach($i_notes as $i_note) {
            $ui .= view_e($x__type, $i_note,  null, $e_of_i && $is_active);
        }

        if($e_of_i && $is_active && !in_array($x__type, $this->config->item('n___12677'))) {
            $ui .= '<div class="list-group-item list-adder no-side-padding e-only-7551 e-i-' . $x__type . '" note_type_id="' . $x__type . '">
                <div class="input-group border">
                    <a class="input-group-addon addon-lean icon-adder" href="javascript:void(0);" onclick="$(\'#new_e_' . $x__type . '\').focus();"><span class="icon-block">'.$e___11035[14055]['m__icon'].'</span></a>
                    <input type="text"
                           class="form-control form-control-thick algolia_search input_note_'.$x__type.' dotransparent add-input"
                           id="new_e_' . $x__type . '"                          
                           maxlength="' . view_memory(6404,6197) . '"                          
                           placeholder="' . $e___11035[14055]['m__title'] . '">
                </div><div class="algolia_pad_search hidden pad_expand e-pad-' . $x__type . '">&nbsp;</div></div>';
        }

        $ui .= '</div>';

    } elseif($x__type==12273){

        //IDEAS
        $is_next = $this->X_model->fetch(array(
            'x__status IN (' . join(',', $this->config->item('n___7360')) . ')' => null, //ACTIVE
            'i__type IN (' . join(',', $this->config->item('n___7356')) . ')' => null, //ACTIVE
            'x__type IN (' . join(',', $this->config->item('n___4486')) . ')' => null, //IDEA LINKS
            'x__left' => $i_focus['i__id'],
        ), array('x__right'), 0, 0, array('x__spectrum' => 'ASC'));
        $counter = count($is_next);

        if(superpower_active(12700, true)){

            //IDEA LIST EDITOR
            $ui .= '<div class="action-left-btn grey toggle_12589"><a href="javascript:void(0);" onclick="$(\'.toggle_12589\').toggleClass(\'hidden\');" title="'.$e___11035[12589]['m__title'].'" data-toggle="tooltip" data-placement="top">'.$e___11035[12589]['m__icon'].'</a></div>';


            $ui .= '<div class="toggle_12589 hidden" style="margin-bottom:41px;">';
            $ui .= '<div class="headline"><span class="icon-block">'.$e___11035[12589]['m__icon'].'</span>'.$e___11035[12589]['m__title'].'</div>';
            $dropdown_options = '';
            $input_options = '';
            $this_counter = 0;

            //$ui .= '<div class="headline"><span class="icon-block">'.$m['m__icon'].'</span>'.$m['m__title'].'</div>';
            foreach($this->config->item('e___12589') as $action_e__id => $e_list_action) {

                $this_counter++;
                $dropdown_options .= '<option value="' . $action_e__id . '">' .$e_list_action['m__title'] . '</option>';


                //Start with the input wrapper:
                $input_options .= '<span id="mass_id_'.$action_e__id.'" title="'.$e_list_action['m__message'].'" class="inline-block '. ( $this_counter > 1 ? ' hidden ' : '' ) .' mass_action_item">';

                if(in_array($action_e__id, array(12591, 12592))){

                    //Source search box:

                    //String command:
                    $input_options .= '<input type="text" name="mass_value1_'.$action_e__id.'"  placeholder="Search Sources..." class="form-control algolia_search e_text_search border css__title">';

                    //We don't need the second value field here:
                    $input_options .= '<input type="hidden" name="mass_value2_'.$action_e__id.'" value="" />';

                } elseif(in_array($action_e__id, array(12611, 12612))){

                    //String command:
                    $input_options .= '<input type="text" name="mass_value1_'.$action_e__id.'"  placeholder="Search Ideas..." class="form-control algolia_search i_text_search border css__title">';

                    //We don't need the second value field here:
                    $input_options .= '<input type="hidden" name="mass_value2_'.$action_e__id.'" value="" />';

                }

                $input_options .= '</span>';

            }

            $ui .= '<form class="mass_modify" method="POST" action="" style="width: 100% !important; margin-left: 41px;">';

            //Drop Down
            $ui .= '<select class="form-control border" name="mass_action_e__id" id="set_mass_action">';
            $ui .= $dropdown_options;
            $ui .= '</select>';

            $ui .= $input_options;

            $ui .= '<div><input type="submit" value="APPLY" class="btn btn-idea inline-block"></div>';

            $ui .= '</form>';
            $ui .= '</div>';

        }


        //$ui .= '<div class="headline"><span class="icon-block">'.$e___11035[13542]['m__icon'].'</span>'.$e___11035[13542]['m__title'].'</div>';
        $ui .= '<div id="list-in-13542" class="row hideIfEmpty">';
        foreach($is_next as $next_i) {
            $ui .= view_i(13542, 0, $i_focus, $next_i, $e_of_i);
        }
        $ui .= '</div>';

        if($e_of_i && $is_active){
            $ui .= '<div class="new-list-13542 list-adder '.superpower_active(10939).'">
                <div class="input-group border">
                    <a class="input-group-addon addon-lean icon-adder" href="javascript:void(0);" onclick="$(\'#new-list-13542 .add-input\').focus();"><span class="icon-block">'.$e___11035[13912]['m__icon'].'</span></a>
                    <input type="text"
                           class="form-control form-control-thick add-input algolia_search dotransparent"
                           maxlength="' . view_memory(6404,4736) . '"
                           placeholder="'.$e___11035[13912]['m__title'].'">
                </div><div class="algolia_pad_search hidden">&nbsp;</div></div>';
        }

    } elseif($x__type==6255) {

        //READS
        $counter = view_coins_i(6255,  $i_focus, false);

        //$ui .= '<div class="headline"><span class="icon-block">'.$m['m__icon'].'</span>'.$m['m__title'].'</div>';
        if($counter){

            $query_filters = array(
                'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
                'x__type IN (' . join(',', $this->config->item('n___6255')) . ')' => null, //READ COIN
                'x__left' => $i_focus['i__id'],
            );
            if(isset($_GET['load__e']) && $superpower_14005){
                $query_filters['x__source'] = intval($_GET['load__e']);
            }

            //Fetch Results:
            $query = $this->X_model->fetch($query_filters, array('x__source'), view_memory(6404,11064), 0, array('x__id' => 'DESC'));

            //Return UI:
            $ui .= '<div class="list-group">';
            foreach($query as $item){
                $ui .= view_e(6255, $item);
            }
            $ui .= '</div>';

        } else {

            //No Results:
            $e___2738 = $this->config->item('e___2738'); //MENCH
            //$ui .= '<div class="msg alert alert-danger" role="alert"><span class="icon-block"><i class="fas fa-exclamation-circle"></i></span> No '.$e___2738[6255]['m__title'].' yet</div>';

        }

    } elseif($x__type==12274 || in_array($x__type, $this->config->item('n___4485'))){

        //IDEA NOTES
        $note_x__type = ($x__type==12274 ? 4983 : $x__type );
        $i_notes = $this->X_model->fetch(array(
            'x__status IN (' . join(',', $this->config->item('n___7360')) . ')' => null, //ACTIVE
            'x__type' => $note_x__type,
            'x__right' => $i_focus['i__id'],
        ), array('x__source'), 0, 0, array('x__spectrum' => 'ASC'));
        $counter = count($i_notes);
        $ui .= view_i_note_list($note_x__type, false, $i_focus, $i_notes, $e_of_i, false);

    } elseif($x__type==12969){

        //$ui .= '<div class="headline"><span class="icon-block">'.$m['m__icon'].'</span>'.$m['m__title'].'</div>';
        $u_x = $this->X_model->fetch(array(
            'x__left' => $i_focus['i__id'],
            'x__type IN (' . join(',', $this->config->item('n___12969')) . ')' => null, //MY READS
            'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
        ), array('x__source'), 0, 0, array(), 'COUNT(x__id) as totals');
        $counter = $u_x[0]['totals'];
        if($counter > 0){

            $ui .= '<div class="list-group">';
            foreach($this->X_model->fetch(array(
                'x__left' => $i_focus['i__id'],
                'x__type IN (' . join(',', $this->config->item('n___12969')) . ')' => null, //MY READS
                'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
            ), array('x__source')) as $u){
                $ui .= view_e(12969, $u);
            }
            $ui .= '</div>';

        }

    } else {

        //Not supported via here:
        continue;

    }


    if(!$counter && !in_array($x__type, $this->config->item('n___13530'))){
        //Hide since Zero:
        continue;
    }

    $default_active = in_array($x__type, $this->config->item('n___12675'));

    echo '<li class="nav-item'.( in_array($x__type, $this->config->item('n___14655')) ? ' pull-right ' : '' ).''.( count($superpower_actives) ? superpower_active(end($superpower_actives)) : '' ).'"><a '.$href.' class="nav-x tab-nav-'.$tab_group.' tab-head-'.$x__type.' '.( $default_active ? ' active ' : '' ).extract_icon_color($m['m__icon']).'" title="'.$m['m__title'].( strlen($m['m__message']) ? ' '.$m['m__message'] : '' ).'" data-toggle="tooltip" data-placement="top">&nbsp;'.$m['m__icon'].'&nbsp;<span class="en-type-counter-'.$x__type.'">'.view_number($counter).'</span>'.( intval($counter) ? '&nbsp;' : '' ).'</a></li>';


    $tab_content .= '<div class="tab-content tab-group-'.$tab_group.' tab-data-'.$x__type.( $default_active ? '' : ' hidden ' ).'">';
    $tab_content .= $ui;
    $tab_content .= '</div>';

}
echo '</ul>';


//Show All Tab Content:
echo $tab_content;

echo '</div>';
