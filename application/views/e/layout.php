<?php
$e___6177 = $this->config->item('e___6177'); //Source Status
$e___11035 = $this->config->item('e___11035'); //NAVIGATION
$e___13571 = $this->config->item('e___13571'); //SOURCE EDITOR
$e___6198 = $this->config->item('e___6198'); //SOURCE ICON
$source_of_e = source_of_e($e['e__id']);
$source_is_e = $e['e__id']==$user_e['e__id'];
$superpower_10939 = superpower_active(10939, true); //SUPERPOWER OF IDEATION
$superpower_13422 = superpower_active(13422, true); //SUPERPOWER OF SOURCING
$superpower_12701 = superpower_active(12701, true); //SUPERPOWER OF DISCOVERY GLASSES
$superpower_any = ( $user_e ? count($this->session->userdata('session_superpowers_assigned')) : 0 );

?>

<script>
    //Set global variables:
    var e_focus_filter = -1; //No filter, show all
    var e_focus_id = <?= $e['e__id'] ?>;
</script>

<script src="/application/views/e/layout.js?v=<?= config_var(11060) ?>" type="text/javascript"></script>

    <?php

    if(!$source_is_e || $superpower_13422 ){

        //PROFILE
        echo '<div class="container coin-frame">';
        echo '<div class="headline top-margin"><span class="icon-block">'.$e___11035[11030]['m_icon'].'</span>'.$e___11035[11030]['m_title'].'</div>';
        echo '<div id="list_11030" class="list-group grey-list">';
        $profiles = $this->X_model->fetch(array(
            'x__type IN (' . join(',', $this->config->item('n___4592')) . ')' => null, //SOURCE LINKS
            'x__status IN (' . join(',', $this->config->item('n___7360')) . ')' => null, //ACTIVE
            'e__status IN (' . join(',', $this->config->item('n___7358')) . ')' => null, //ACTIVE
            'x__down' => $e['e__id'],
        ), array('x__up'), 0, 0, array('e__weight' => 'DESC'));
        $show_max = config_var(13803);
        $hide = false;
        foreach($profiles as $count => $e_profile) {
            if(!$hide && $count==$show_max){
                $hide = true;
                echo '<div class="load-more montserrat list-group-item itemsource no-left-padding see_all_11030"><a href="javascript:void(0);" onclick="$(\'.see_all_11030\').toggleClass(\'hidden\')"><span class="icon-block">'.$e___11035[13915]['m_icon'].'</span><b class="montserrat source">'.$e___11035[13915]['m_title'].'<span class="'.superpower_active(13422).'"> ['.count($profiles).']</span></b></a></div>';
                echo '<div class="list-group-item see_all_11030 no-padding"></div>';
            }
            echo view_e($e_profile,true, ( $hide ? ' see_all_11030 hidden ' : null ), true, ($source_of_e || ($user_e && ($user_e['e__id']==$e_profile['x__source']))));

        }

        //ADD NEW
        echo '<div id="new_11030" class="list-group-item list-adder no-side-padding '.superpower_active(13422).'">
                    <div class="input-group border">
                        <a class="input-group-addon addon-lean icon-adder" href="javascript:void(0);" onclick="$(\'#New11030input\').focus();"><span class="icon-block">'.$e___11035[13914]['m_icon'].'</span></a>
                        <input type="text"
                               class="form-control form-control-thick algolia_search dotransparent add-input"
                               id="New11030input"
                               maxlength="' . config_var(6197) . '"
                               placeholder="'.$e___11035[13914]['m_title'].'">
                    </div><div class="algolia_pad_search hidden pad_expand">'.config_var(13914).'</div></div>';
        echo '</div>';
        echo '</div>';


    }


    echo '<div class="container wrap-card card-source">';


    //SOURCE DRAFTING?
    if(!in_array($e['e__status'], $this->config->item('n___7357'))){
        echo '<div class="montserrat '.extract_icon_color($e___6177[$e['e__status']]['m_icon']).' top-margin"><span class="icon-block">' . $e___6177[$e['e__status']]['m_icon'] . '</span>'.$e___6177[$e['e__status']]['m_title'].'</div>';
    }


    //SOURCE NAME
    echo '<div style="padding: 8px 0; margin-top:13px;">'.view_input_text(6197, $e['e__title'], $e['e__id'], ($source_of_e && in_array($e['e__status'], $this->config->item('n___7358'))), 0, true, '<span class="e_ui_icon_'.$e['e__id'].'">'.view_e__icon($e['e__icon']).'</span>', extract_icon_color($e['e__icon'])).'</div>';
    echo '<div class="doclear">&nbsp;</div>';


    //SOURCE MODIFY BUTTON
    echo '<div class="pull-right inline-block" style="margin:0 0 -40px 0;">';

        if($superpower_13422) {
            echo '<a href="javascript:void(0);" onclick="load_13571(' . $e['e__id'] . ',0)" class="icon-block" style="padding-top:10px;" data-toggle="tooltip" data-placement="top" title="'.$e___11035[13571]['m_title'].'">'.$e___11035[13571]['m_icon'].'</a>';
        }

    echo '</div>';
    echo '<div class="doclear">&nbsp;</div>';



    if($source_is_e && isset($_GET['reset'])){
        //DISCOVER DELETE ALL (ACCESSIBLE VIA MAIN MENU)
        echo '<div class="margin-top-down left-margin">';
        echo '<p>'.$e___11035[6415]['m_message'].'</p>';
        echo '<p style="padding-top:13px;"><a href="javascript:void(0);" onclick="reset_6415()" class="btn btn-discover">'.$e___11035[6415]['m_icon'].' '.$e___11035[6415]['m_title'].'</a> or <a href="/" style="text-decoration: underline;">Cancel</a></p>';
        echo '</div>';

        echo '<div class="doclear">&nbsp;</div>';
    }


    //Determine Focus Tab:
    $counter__e = view_coins_e(12274, $e['e__id'], 0, false);
    $counter__i = view_coins_e(12273, $e['e__id'], 0, false);
    $counter__x = view_coins_e( 6255, $e['e__id'], 0, false);
    $active_x__type = 0;

    if($counter__e > 0 && !$source_is_e && $counter__e > $counter__i){
        //SOURCES
        $active_x__type = 12274;
    } elseif($counter__i > 0){
        //IDEAS
        $active_x__type = 12273;
    } elseif($source_is_e || ($superpower_12701 && $counter__x > 0)){
        //DISCOVERIES
        $active_x__type = 6255;
    }

    $tab_nav = '';
    $tab_content = '';
    foreach($this->config->item('e___11089') as $x__type => $m) {

        $superpower_actives = array_intersect($this->config->item('n___10957'), $m['m_profile']);
        if(count($superpower_actives)){
            if(!superpower_active(end($superpower_actives), true) && !$source_is_e){
                //Missing Superpower:
                continue;
            }
        }


        if(in_array($x__type, $this->config->item('n___13425')) && !$source_is_e){
            continue;
        }

        $counter = null;
        $focus_tab = null;

        if($source_is_e && strlen($m['m_message']) > 0){
            $focus_tab .= '<div style="padding-bottom: 13px;"><span class="icon-block"><i class="fas fa-info-circle black"></i></span>'.$m['m_message'].'</div>';
        }

        //Is this a caret menu?
        if(in_array(11040 , $m['m_profile'])){

            $tab_nav .= view_caret($x__type, $m, $e['e__id']);
            continue;

        } elseif(in_array($x__type, $this->config->item('n___6194'))){

            //SOURCE REFERENCE:
            $e_count_6194 = e_count_6194($e['e__id'], $x__type);
            $counter = ( isset($e_count_6194[$x__type]) ? $e_count_6194[$x__type] : 0 );
            if(!$counter){
                continue;
            }
            $focus_tab .= '<div><span class="icon-block">&nbsp;</span>Source referenced as '.$m['m_icon'].' '.$m['m_title'].' '.number_format($counter, 0).' times.</div>';

        } elseif($x__type==12274){

            //SOURCES
            $counter = $counter__e;
            $list_e = view_coins_e(12274, $e['e__id'], 1);

            if(!$counter && !$superpower_10939 && !$source_is_e){
                continue;
            }


            //SOURCE MASS EDITOR
            if($superpower_13422){

                //Mass Editor:
                $dropdown_options = '';
                $input_options = '';
                $editor_counter = 0;

                foreach($this->config->item('e___4997') as $action_e__id => $e_list_action) {


                    $editor_counter++;
                    $dropdown_options .= '<option value="' . $action_e__id . '">' .$e_list_action['m_title'] . '</option>';
                    $is_upper = ( in_array($action_e__id, $this->config->item('n___12577') /* SOURCE UPDATER UPPERCASE */) ? ' montserrat doupper ' : false );


                    //Start with the input wrapper:
                    $input_options .= '<span id="mass_id_'.$action_e__id.'" title="'.$e_list_action['m_message'].'" class="inline-block '. ( $editor_counter > 1 ? ' hidden ' : '' ) .' mass_action_item">';




                    if(in_array($action_e__id, array(5000, 5001, 10625))){

                        //String Find and Replace:

                        //Find:
                        $input_options .= '<input type="text" name="mass_value1_'.$action_e__id.'" placeholder="Search" class="form-control border '.$is_upper.'">';

                        //Replace:
                        $input_options .= '<input type="text" name="mass_value2_'.$action_e__id.'" placeholder="Replace" class="form-control border '.$is_upper.'">';


                    } elseif(in_array($action_e__id, array(5981, 12928, 12930, 5982, 13441))){

                        //User search box:

                        //String command:
                        $input_options .= '<input type="text" name="mass_value1_'.$action_e__id.'"  placeholder="Search sources..." class="form-control algolia_search e_text_search border '.$is_upper.'">';

                        //We don't need the second value field here:
                        $input_options .= '<input type="hidden" name="mass_value2_'.$action_e__id.'" value="" placeholder="Search Source" />';


                    } elseif($action_e__id == 11956){

                        //IF HAS THIS
                        $input_options .= '<input type="text" name="mass_value1_'.$action_e__id.'"  placeholder="IF THIS SOURCE..." class="form-control algolia_search e_text_search border '.$is_upper.'">';

                        //ADD THIS
                        $input_options .= '<input type="text" name="mass_value2_'.$action_e__id.'"  placeholder="ADD THIS SOURCE..." class="form-control algolia_search e_text_search border '.$is_upper.'">';


                    } elseif($action_e__id == 5003){

                        //User Status update:

                        //Find:
                        $input_options .= '<select name="mass_value1_'.$action_e__id.'" class="form-control border">';
                        $input_options .= '<option value="*">Update All Statuses</option>';
                        foreach($this->config->item('e___6177') /* Source Status */ as $x__type3 => $m3){
                            $input_options .= '<option value="'.$x__type3.'">Update All '.$m3['m_title'].'</option>';
                        }
                        $input_options .= '</select>';

                        //Replace:
                        $input_options .= '<select name="mass_value2_'.$action_e__id.'" class="form-control border">';
                        $input_options .= '<option value="">Set New Status...</option>';
                        foreach($this->config->item('e___6177') /* Source Status */ as $x__type3 => $m3){
                            $input_options .= '<option value="'.$x__type3.'">Set to '.$m3['m_title'].'</option>';
                        }
                        $input_options .= '</select>';


                    } elseif($action_e__id == 5865){

                        //Transaction Status update:

                        //Find:
                        $input_options .= '<select name="mass_value1_'.$action_e__id.'" class="form-control border">';
                        $input_options .= '<option value="*">Update All Statuses</option>';
                        foreach($this->config->item('e___6186') /* Transaction Status */ as $x__type3 => $m3){
                            $input_options .= '<option value="'.$x__type3.'">Update All '.$m3['m_title'].'</option>';
                        }
                        $input_options .= '</select>';

                        //Replace:
                        $input_options .= '<select name="mass_value2_'.$action_e__id.'" class="form-control border">';
                        $input_options .= '<option value="">Set New Status...</option>';
                        foreach($this->config->item('e___6186') /* Transaction Status */ as $x__type3 => $m3){
                            $input_options .= '<option value="'.$x__type3.'">Set to '.$m3['m_title'].'</option>';
                        }
                        $input_options .= '</select>';


                    } else {

                        //String command:
                        $input_options .= '<input type="text" name="mass_value1_'.$action_e__id.'"  placeholder="String..." class="form-control border '.$is_upper.'">';

                        //We don't need the second value field here:
                        $input_options .= '<input type="hidden" name="mass_value2_'.$action_e__id.'" value="" />';

                    }

                    $input_options .= '</span>';

                }


                $focus_tab .= '<div class="pull-right" style="margin:-42px 33px 0 0;">'.( superpower_active(13422, true) && sources_currently_sorted($e['e__id']) ? '<span class="sort_reset hidden icon-block" title="'.$e___11035[13007]['m_title'].'" data-toggle="tooltip" data-placement="top"><a href="javascript:void(0);" onclick="e_sort_reset()">'.$e___11035[13007]['m_icon'].'</a></span>' : '').'<a href="javascript:void(0);" onclick="$(\'.e_editor\').toggleClass(\'hidden\');" title="'.$e___11035[4997]['m_title'].'" data-toggle="tooltip" data-placement="top">'.$e___11035[4997]['m_icon'].'</a></div>';



                $focus_tab .= '<div class="doclear">&nbsp;</div>';
                $focus_tab .= '<div class="e_editor hidden">';
                $focus_tab .= '<form class="mass_modify" method="POST" action="" style="width: 100% !important; margin-left: 33px;">';

                //Drop Down
                $focus_tab .= '<select class="form-control border" name="mass_action_e__id" id="set_mass_action">';
                $focus_tab .= $dropdown_options;
                $focus_tab .= '</select>';

                $focus_tab .= $input_options;

                $focus_tab .= '<div><input type="submit" value="APPLY" class="btn btn-source inline-block"></div>';

                $focus_tab .= '</form>';

                //Also add invisible child IDs for quick copy/pasting:
                $focus_tab .= '<div style="color:transparent;" class="hideIfEmpty">';
                foreach($list_e as $e_portfolio) {
                    $focus_tab .= $e_portfolio['e__id'].',';
                }
                $focus_tab .= '</div>';

                $focus_tab .= '</div>';







                //Source Status Filters:
                if(superpower_active(12701, true)){

                    $e_count = $this->E_model->child_count($e['e__id'], $this->config->item('n___7358') /* ACTIVE */);
                    $child_e_filters = $this->X_model->fetch(array(
                        'x__up' => $e['e__id'],
                        'x__type IN (' . join(',', $this->config->item('n___4592')) . ')' => null, //SOURCE LINKS
                        'x__status IN (' . join(',', $this->config->item('n___7360')) . ')' => null, //ACTIVE
                        'e__status IN (' . join(',', $this->config->item('n___7358')) . ')' => null, //ACTIVE
                    ), array('x__down'), 0, 0, array('e__status' => 'ASC'), 'COUNT(e__id) as totals, e__status', 'e__status');

                    //Only show filtering UI if we find child sources with different Status (Otherwise no need to filter):
                    if (count($child_e_filters) > 0 && $child_e_filters[0]['totals'] < $e_count) {

                        //Add 2nd Navigation to UI
                        $focus_tab .= '<div class="nav nav-pills nav-sm">';

                        //Show fixed All button:
                        $focus_tab .= '<li class="nav-item"><a href="#" onclick="e_filter_status(-1)" class="nav-x e_filter_status active en_status_-1" data-toggle="tooltip" data-placement="top" title="View all sources"><i class="fas fa-asterisk source"></i><span class="source">&nbsp;' . $e_count . '</span><span class="show-max source">&nbsp;TOTAL</span></a></li>';

                        //Show each specific filter based on DB counts:
                        foreach($child_e_filters as $c_c) {
                            $st = $e___6177[$c_c['e__status']];
                            $extract_icon_color = extract_icon_color($st['m_icon']);
                            $focus_tab .= '<li class="nav-item"><a href="javascript:void(0)" onclick="e_filter_status(' . $c_c['e__status'] . ')" class="nav-x nav-link e_filter_status en_status_' . $c_c['e__status'] . '" data-toggle="tooltip" data-placement="top" title="' . $st['m_message'] . '">' . $st['m_icon'] . '<span class="' . $extract_icon_color . '">&nbsp;' . $c_c['totals'] . '</span><span class="show-max '.$extract_icon_color.'">&nbsp;' . $st['m_title'] . '</span></a></li>';
                        }

                        $focus_tab .= '</div>';

                    }
                }
            }

            $focus_tab .= '<div class="headline top-margin"><span class="icon-block">'.$e___11035[11029]['m_icon'].'</span>'.$e___11035[11029]['m_title'].'</div>';
            $focus_tab .= '<div id="list_e" class="list-group">';

            $common_prefix = i_calc_common_prefix($list_e, 'e__title');

            foreach($list_e as $e_portfolio) {
                $focus_tab .= view_e($e_portfolio,false, null, true, ($source_of_e || ($user_e && ($user_e['e__id']==$e_portfolio['x__source']))), $common_prefix);
            }
            if ($counter > count($list_e)) {
                $focus_tab .= view_e_load_more(1, config_var(11064), $counter);
            }

            //Input to add new child:
            if($superpower_13422){

                $focus_tab .= '<div id="new_11029" current-count="'.$counter.'" class="list-group-item list-adder no-side-padding '.superpower_active(10939).'">
                        <div class="input-group border">
                            <a class="input-group-addon addon-lean icon-adder" href="javascript:void(0);" onclick="$(\'#New11029input\').focus();"><span class="icon-block">'.$e___11035[13914]['m_icon'].'</span></a>
                            <input type="text"
                                   class="form-control form-control-thick algolia_search dotransparent add-input"
                                   id="New11029input"
                                   maxlength="' . config_var(6197) . '"
                                   placeholder="'.$e___11035[13914]['m_title'].'">
                        </div><div class="algolia_pad_search hidden pad_expand">'.config_var(13914).'</div></div>';

            } else {

                $focus_tab .= '<div id="new_11029" class="hideIfEmpty"></div>';

            }

            $focus_tab .= '</div>';

        } elseif($x__type==12273){

            //IDEAS
            $counter = $counter__i;
            $exclude_ids = array();

            if($superpower_10939){

                //MY IDEAS?
                if($source_is_e){

                    $focus_tab .= '<div class="headline top-margin"><span class="icon-block">' . $e___11035[10573]['m_icon'] . '</span>' . $e___11035[10573]['m_title'] . '</div>';

                    //Give Option to Add New Idea:
                    $focus_tab .= '<div class="list-group add_e_idea"><div class="list-group-item list-adder">
                    <div class="input-group border">
                        <a class="input-group-addon addon-lean icon-adder" href="javascript:void(0);" onclick="$(\'#newIdeaTitle\').focus();"><span class="icon-block">'.$e___11035[13912]['m_icon'].'</span></a>
                        <input type="text"
                               class="form-control form-control-thick algolia_search dotransparent add-input"
                               maxlength="' . config_var(4736) . '"
                               id="newIdeaTitle"
                               placeholder="'.$e___11035[13912]['m_title'].'">
                    </div><div class="algolia_pad_search hidden">'.config_var(13914).'</div></div></div>';


                    $i_bookmarks = $this->X_model->fetch(array(
                        'i__status IN (' . join(',', $this->config->item('n___7356')) . ')' => null, //ACTIVE
                        'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
                        'x__type' => 10573, //BOOKMARKED IDEAS
                        'x__up' => $e['e__id'],
                    ), array('x__right'), config_var(11064), 0, array('x__sort' => 'ASC'));

                    if(count($i_bookmarks) > 0){

                        //Need 2 or more to sort...
                        if(count($i_bookmarks) > 1){
                            //SORTING ENABLED
                            $focus_tab .= '<script> $(document).ready(function () {x_sort_load(13412)}); </script>';
                        } else {
                            //SORTING DISABLED
                            $focus_tab .= '<style> #list_13412 .x_sort {display:none !important;} </style>';
                        }

                        $focus_tab .= '<div class="row top-margin" id="list_13412">';
                        foreach($i_bookmarks as $item){
                            array_push($exclude_ids, $item['i__id']);
                            $focus_tab .= view_i_cover(12273, $item, $source_is_e, null, $e);
                        }
                        $focus_tab .= '</div>';

                    } else {

                        $focus_tab .= '<div class="msg alert alert-warning" role="alert" style="text-decoration: none;"><span class="icon-block"><i class="fas fa-exclamation-circle idea"></i></span>No Ideas Bookmarked Yet</div>';

                    }
                }

            } else {

                //Give Option to Get Started:
                $focus_tab .= '<div class="msg alert alert-warning" role="alert" style="text-decoration: none;"><span class="icon-block">'.$e___11035[10939]['m_icon'].'</span><a href="'.config_var(10939).'">'.$e___11035[10939]['m_title'].'<span class="icon-block"><i class="fas fa-arrow-right"></i></span></a><div class="padded">'.$e___11035[10939]['m_message'].'</div></div>';

            }



            //List References
            $list_i = view_coins_e(12273, $e['e__id'], 1, true, $exclude_ids);

            if(count($list_i)){
                $focus_tab .= '<div class="headline top-margin"><span class="icon-block">'.$e___11035[13550]['m_icon'].'</span>'.$e___11035[13550]['m_title'].'</div>';
            }

            if($superpower_10939 && !$source_is_e){
                //Give Option to Add New Idea:
                $focus_tab .= '<div class="list-group add_e_idea"><div class="list-group-item list-adder">
                    <div class="input-group border">
                        <a class="input-group-addon addon-lean icon-adder" href="javascript:void(0);" onclick="$(\'#newIdeaTitle\').focus();"><span class="icon-block">'.$e___11035[13912]['m_icon'].'</span></a>
                        <input type="text"
                               class="form-control form-control-thick algolia_search dotransparent add-input"
                               maxlength="' . config_var(4736) . '"
                               id="newIdeaTitle"
                               placeholder="'.$e___11035[13912]['m_title'].'">
                    </div><div class="algolia_pad_search hidden">'.config_var(13914).'</div></div></div>';
            }


            if(count($list_i)){
                $focus_tab .= '<div class="row top-margin" id="list_13550">';
                foreach($list_i as $count => $item){
                    $show_message = strlen($item['x__message']) && trim($item['x__message'])!=$this->uri->segment(1); //Basic references only
                    $focus_tab .= view_i_cover(12273, $item, false, ( $show_message ? $this->X_model->message_send($item['x__message']) : null), $e);
                }
                $focus_tab .= '</div>';

                //Are there more?
                if($counter > count($list_i)){
                    $focus_tab .= '<div style="padding: 13px 0;" class="'.superpower_active(12700).'"><div class="msg alert alert-warning" role="alert"><a href="/ledger?x__source='.$user_e['e__id'].'&x__type=4983&x__status='.join(',', $this->config->item('n___7359')).'"><span class="icon-block">'.$e___11035[13913]['m_icon'].'</span>'.$e___11035[13913]['m_title'].' ['.$counter.']</a></div></div>';
                }
            }

        } elseif($x__type==6255){

            //DISCOVERIES
            $counter = $counter__x;
            $my_x_ids = array();

            if($source_is_e){

                //Give Option to Add New Idea:
                $focus_tab .= '<div class="headline top-margin"><span class="icon-block">'.$e___11035[12969]['m_icon'].'</span>'.$e___11035[12969]['m_title'].'</div>';

                $focus_tab .= '<div class="list-group" id="discoveryNew"><div class="list-group-item list-adder">
                    <div class="input-group border">
                        <a class="input-group-addon addon-lean icon-adder" href="javascript:void(0);" onclick="$(\'#searchDiscoveries\').focus();"><span class="icon-block">'.$e___11035[13956]['m_icon'].'</span></a>
                        <input type="text"
                               class="form-control form-control-thick algolia_search dotransparent add-input"
                               id="searchDiscoveries"
                               placeholder="'.$e___11035[13956]['m_title'].'">
                    </div><div class="algolia_pad_search hidden">'.config_var(13956).'</div></div></div>';
            }

            //Show My Discoveries
            if($counter){

                if($source_is_e || superpower_active(12701, true)){

                    $list_x  = view_coins_e(6255, $e['e__id'], 1);
                    if(count($list_x)){

                        $focus_tab .= '<div class="row" style="margin-top:34px;" id="list_6132">';
                        foreach($list_x as $item){
                            $focus_tab .= view_i_cover(6255, $item, $source_is_e, null, $e);
                            array_push($my_x_ids, $item['i__id']);
                        }
                        $focus_tab .= '</div>';

                        $focus_tab .= ( count($list_x) > 1 ? '<script> $(document).ready(function () {x_sort_load(6132)}); </script>' : '<style> #list_6132 .x_sort {display:none !important;} </style>' ); //Need 2 or more to sort

                    }

                } else {

                    $focus_tab .= '<div class="i_content padded top-margin"><div class="msg">'.$e['e__title'].' has discovered '.number_format($counter, 0).' idea'.view__s($counter).'.</div></div>';


                }

            }



            if($source_is_e){

                $featured_filter = array(
                    'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
                    'i__status IN (' . join(',', $this->config->item('n___7355')) . ')' => null, //PUBLIC
                    'x__type IN (' . join(',', $this->config->item('n___12840')) . ')' => null, //IDEA LINKS TWO-WAY
                    'x__left' => config_var(12137),
                );
                if(count($my_x_ids)){
                    //Exclude Featured Ideas already added to Discoveries:
                    $featured_filter['i__id NOT IN (' . join(',', $my_x_ids) . ')'] = null;
                }

                //FETCH FEATURED IDEAS
                $featured_i = $this->X_model->fetch($featured_filter, array('x__right'), 0, 0, array('x__sort' => 'ASC'));

                if(count($featured_i)){
                    $focus_tab .= '<div class="headline top-margin"><span class="icon-block">'.$e___11035[13813]['m_icon'].'</span>'.$e___11035[13813]['m_title'].'</div>';
                    $focus_tab .= '<div class="row top-margin">';
                    foreach($featured_i as $key => $x){
                        $focus_tab .= view_i_cover(6255, $x, false, null, $e);
                    }
                    $focus_tab .= '</div>';
                }
            }

        } elseif(in_array($x__type, $this->config->item('n___4485'))){

            //IDEA NOTES
            $i_notes_filters = array(
                'x__status IN (' . join(',', $this->config->item('n___7360')) . ')' => null, //ACTIVE
                'i__status IN (' . join(',', $this->config->item('n___7356')) . ')' => null, //ACTIVE
                'x__type' => $x__type,
                'x__up' => $e['e__id'],
            );

            //COUNT ONLY
            $item_counters = $this->X_model->fetch($i_notes_filters, array('x__right'), 0, 0, array(), 'COUNT(i__id) as totals');
            $counter = $item_counters[0]['totals'];

            $focus_tab .= '<div class="list-group">';
            if($counter>0){

                $i_notes_query = $this->X_model->fetch($i_notes_filters, array('x__right'), config_var(11064), 0, array('i__weight' => 'DESC'));
                foreach($i_notes_query as $count => $i_notes) {

                    if($x__type==12896){

                        //Saved IDEA
                        $focus_tab .= view_i_x($i_notes, -2 /* Unlocked */, true, null, true);


                    } else {
                        $focus_tab .= view_i_x($i_notes, -2 /* Unlocked */, true); //Inputs when it used to use view_i() : 0, false, false, $i_notes['x__message'], null, false
                    }

                }

            }


            $focus_tab .= '</div>';


            //SHOW LASTEST 100
            if(!$counter){

                $focus_tab .= '<div class="msg alert alert-warning" role="alert"><span class="icon-block"><i class="fas fa-exclamation-circle"></i></span> No '.$m['m_title'].' yet</div>';

            }

        } elseif($x__type==6225){

            //ACCOUNT SETTING
            $focus_tab = '<div class="accordion" id="MyAccountAccordion" style="margin-bottom:33px;">';

            //Display account fields ordered with their SOURCE LINKS:
            foreach($this->config->item('e___6225') as $acc_e__id => $acc_detail) {

                //Print header:
                $focus_tab .= '<div class="card">
<div class="card-header" id="heading' . $acc_e__id . '">
<button class="btn btn-block" type="button" data-toggle="collapse" data-target="#openEn' . $acc_e__id . '" aria-expanded="false" aria-controls="openEn' . $acc_e__id . '">
  <span class="icon-block">' . $acc_detail['m_icon'] . '</span><b class="montserrat doupper ' . extract_icon_color($acc_detail['m_icon']) . '">' . $acc_detail['m_title'] . '</b><span class="pull-right icon-block"><i class="fas fa-chevron-down"></i></span>
</button>
</div>

<div class="doclear">&nbsp;</div>

<div id="openEn' . $acc_e__id . '" class="collapse" aria-labelledby="heading' . $acc_e__id . '" data-parent="#MyAccountAccordion">
<div class="card-body">';


                //Show description if any:
                $focus_tab .= (strlen($acc_detail['m_message']) > 0 ? '<div class="i_content padded"><div class="msg">' . $acc_detail['m_message'] . '</div></div>' : '');


                //Print account fields that are either Single Selectable or Multi Selectable:
                $is_multi_selectable = in_array(6122, $acc_detail['m_profile']);
                $is_single_selectable = in_array(6204, $acc_detail['m_profile']);

                if ($acc_e__id == 12289) {

                    $e__icon_parts = explode(' ',one_two_explode('class="', '"', $user_e['e__icon']));


                    $focus_tab .= '<div class="'.superpower_active(10939).'"><div class="doclear">&nbsp;</div><div class="btn-group avatar-type-group pull-right" role="group" style="margin:0 0 10px 0;">';
                    foreach($this->config->item('e___13533') as $m3) {
                        $focus_tab .= '<a href="javascript:void(0)" onclick="account_update_avatar_type(\''.$m3['m_message'].'\')" class="btn btn-'.$m3['m_message'].' '.( $e__icon_parts[0]==$m3['m_message'] ? ' active ' : '' ).'" title="'.$m3['m_title'].'">'.$m3['m_icon'].'</a>';
                    }
                    $focus_tab .= '</div>';
                    $focus_tab .= '<div class="doclear">&nbsp;</div>';
                    $focus_tab .= '</div>';


                    //List avatars:
                    foreach($this->config->item('e___12279') as $x__type3 => $m3) {

                        $avatar_icon_parts = explode(' ',one_two_explode('class="', '"', $m3['m_icon']));
                        $avatar_type_match = ($e__icon_parts[0] == $avatar_icon_parts[0]);
                        $superpower_actives3 = array_intersect($this->config->item('n___10957'), $m3['m_profile']);

                        $focus_tab .= '<span class="'.( count($superpower_actives3) ? superpower_active(end($superpower_actives3)) : '' ).'">';
                        $focus_tab .= '<a href="javascript:void(0);" onclick="e_avatar(\'' . $avatar_icon_parts[0] . '\', \'' . $avatar_icon_parts[1] . '\')" icon-css="' . $avatar_icon_parts[1] . '" class="list-group-item avatar-item item-square avatar-type-'.$avatar_icon_parts[0].' avatar-name-'.$avatar_icon_parts[1].' ' .( $avatar_type_match ? '' : ' hidden ' ). ( $avatar_type_match && $e__icon_parts[1] == $avatar_icon_parts[1] ? ' active ' : '') . '"><div class="avatar-icon">' . $m3['m_icon'] . '</div></a>';
                        $focus_tab .= '</span>';

                    }

                } elseif ($acc_e__id == 10957 /* Superpowers */) {

                    if($superpower_any >= 2){
                        //Mass Toggle Option:
                        $focus_tab .= '<div class="btn-group pull-right" role="group" style="margin:0 0 10px 0;">
                  <a href="javascript:void(0)" onclick="account_toggle_all(1)" class="btn btn-far"><i class="fas fa-toggle-on"></i></a>
                  <a href="javascript:void(0)" onclick="account_toggle_all(0)" class="btn btn-fad"><i class="fas fa-toggle-off"></i></a>
                </div><div class="doclear">&nbsp;</div>';
                    }


                    //List avatars:
                    $superpowers_unlocked = 0;
                    $focus_tab .= '<div class="list-group">';
                    foreach($this->config->item('e___10957') as $superpower_e__id => $m3){

                        //What is the superpower requirement?
                        if(!superpower_assigned($superpower_e__id)){
                            continue;
                        }

                        $superpowers_unlocked++;
                        $extract_icon_color = extract_icon_color($m3['m_icon']);
                        $focus_tab .= '<a class="list-group-item itemsetting btn-superpower superpower-frame-'.$superpower_e__id.' '.( in_array($superpower_e__id, $this->session->userdata('session_superpowers_activated')) ? ' active ' : '' ).'" en-id="'.$superpower_e__id.'" href="javascript:void();" onclick="e_toggle_superpower('.$superpower_e__id.')"><span class="icon-block '.$extract_icon_color.'" title="Source @'.$superpower_e__id.'">'.$m3['m_icon'].'</span><b class="montserrat '.$extract_icon_color.'">'.$m3['m_title'].'</b> '.$m3['m_message'].'</a>';

                    }

                    if(!$superpowers_unlocked){
                        //Let them know they do not have any superpowers yet...
                        $focus_tab .= '<div class="msg alert alert-info no-margin"><span class="icon-block"><i class="fas fa-exclamation-circle source"></i></span>No superpowers unlocked yet.</div>';
                    }

                    $focus_tab .= '</div>';

                } elseif ($acc_e__id == 3288 /* Email */) {

                    $u_emails = $this->X_model->fetch(array(
                        'x__status IN (' . join(',', $this->config->item('n___7359')) . ')' => null, //PUBLIC
                        'x__down' => $user_e['e__id'],
                        'x__type IN (' . join(',', $this->config->item('n___4592')) . ')' => null, //SOURCE LINKS
                        'x__up' => 3288, //Mench Email
                    ));

                    $focus_tab .= '<span><input type="email" id="e_email" class="form-control border dotransparent" value="' . (count($u_emails) > 0 ? $u_emails[0]['x__message'] : '') . '" placeholder="you@gmail.com" /></span>
                <a href="javascript:void(0)" onclick="e_email()" class="btn btn-source">Save</a>
                <span class="saving-account save_email"></span>';

                } elseif ($acc_e__id == 3286 /* Password */) {

                    $focus_tab .= '<span><input type="password" id="input_password" class="form-control border dotransparent" data-lpignore="true" autocomplete="new-password" placeholder="New Password..." /></span>
                <a href="javascript:void(0)" onclick="e_password()" class="btn btn-source">Save</a>
                <span class="saving-account save_password"></span>';

                } elseif ($is_multi_selectable || $is_single_selectable) {

                    $focus_tab .= view_radio_e($acc_e__id, $user_e['e__id'], ($is_multi_selectable ? 1 : 0));

                }

                //Print footer:
                $focus_tab .= '<div class="doclear">&nbsp;</div>';
                $focus_tab .= '</div></div></div>';

            }

            $focus_tab .= '</div>'; //End of accordion

        }

        if(!$counter && !in_array($x__type, $this->config->item('n___12574')) && !($x__type==12274 && $superpower_13422) && !($x__type==6255 && $source_is_e)){
            //Hide since Zero without exception @12574:
            continue;
        }


        $default_active = ( (!isset($_GET['came_from']) && $x__type==$active_x__type) || ( isset($_GET['came_from']) && $_GET['came_from']==$x__type));

        $tab_nav .= '<li class="nav-item"><a class="nav-x tab-nav-11089 tab-head-'.$x__type.' '.( $default_active ? ' active ' : '' ).extract_icon_color($m['m_icon']).'" href="javascript:void(0);" onclick="loadtab(11089, '.$x__type.')">'.$m['m_icon'].' <span class="en-type-counter-'.$x__type.'">'.view_number($counter).'</span><span class="show-max-active">&nbsp;'.$m['m_title'].'</span></a></li>';


        $tab_content .= '<div class="tab-content tab-group-11089 tab-data-'.$x__type.( $default_active ? '' : ' hidden ' ).'">';
        $tab_content .= $focus_tab;
        $tab_content .= '</div>';

    }


    if($tab_nav){

        echo '<ul class="nav nav-tabs nav-sm nav-source">';
        echo $tab_nav;
        echo '</ul>';

        //Show All Tab Content:
        echo $tab_content;

    }


    echo '</div>';
    ?>

<!-- Source Editor Modal -->
<div class="modal fade" id="modal13571" tabindex="-1" role="dialog" aria-labelledby="modal13571Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title montserrat <?= extract_icon_color($e___11035[13571]['m_icon']) ?>" id="modal13571Label"><?= $e___11035[13571]['m_icon'].' '.$e___11035[13571]['m_title'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <input type="hidden" class="modal_e__id" value="0" />
                <input type="hidden" class="modal_x__id" value="0" />
                <div class="save_results margin-top-down-half hideIfEmpty"></div>

                <!-- Source Status -->
                <div class="headline no-left-padding"><?= '<span class="icon-block">'.$e___13571[6177]['m_icon'].'</span>'.$e___13571[6177]['m_title'] ?></div>
                <select class="form-control border" id="e__status">
                    <?php
                    foreach($this->config->item('e___6177') /* Source Status */ as $x__type => $m){
                        echo '<option value="' . $x__type . '" title="' . $m['m_message'] . '">' . $m['m_title'] . '</option>';
                    }
                    ?>
                </select>
                <div class="notify_e_delete hidden">

                    <input type="hidden" id="e_x_count" value="0" />
                    <div class="msg alert alert-danger"><span class="icon-block"><i class="fas fa-exclamation-circle discover"></i></span>Saving will delete source & <span class="e_delete_stats" style="display:inline-block; padding: 0;"></span> links</div>

                </div>



                <!-- Source Title -->
                <div class="headline no-left-padding"><?= '<span class="icon-block">'.$e___13571[6197]['m_icon'].'</span>'.$e___13571[6197]['m_title'] ?> [<span style="margin:0 0 10px 0;"><span id="charEnNum">0</span>/<?= config_var(6197) ?></span>]</div>
                <textarea class="form-control text-edit border montserrat doupper" id="e__title" onkeyup="e__title_word_count()" data-lpignore="true"></textarea>



                <!-- Source Icon -->
                <div class="headline no-left-padding"><?= '<span class="icon-block">'.$e___13571[6198]['m_icon'].'</span>'.$e___13571[6198]['m_title'] ?>

                    <a href="javascript:void(0);" style="margin-left: 5px;" onclick="$('#e__icon').val( '<img src=&quot;https://mench.com/img/mench.png&quot; />' );update_demo_icon();" title="<?= $e___6198[4260]['m_title'].': '.$e___6198[4260]['m_message'] ?>"><?= $e___6198[4260]['m_icon'] ?></a>

                    <a href="javascript:void(0);" style="margin-left: 5px;" onclick="$('#e__icon').val( '<i class=&quot;fas fa-laugh&quot;></i>' );update_demo_icon();" title="<?= $e___6198[13577]['m_title'].': '.$e___6198[13577]['m_message'] ?>"><?= $e___6198[13577]['m_icon'] ?></a>

                    <a href="https://fontawesome.com/icons" style="margin-left: 5px;" target="_blank" title="<?= $e___6198[13578]['m_title'].': '.$e___6198[13578]['m_message'] ?>"><?= $e___6198[13578]['m_icon'] ?></a>

                </div>
                <div class="form-group" style="margin:0 0 13px; border-radius: 10px;">
                    <div class="input-group border">
                        <input type="text" id="e__icon" value="" data-lpignore="true" placeholder="" class="form-control" style="margin-bottom: 0;">
                        <span class="input-group-addon addon-lean addon-grey icon-demo icon-block" style="padding-top:8px;"></span>
                    </div>
                </div>


                <div class="e_has_link">

                    <div class="headline no-left-padding"><?= '<span class="icon-block">'.$e___13571[6186]['m_icon'].'</span>'.$e___13571[6186]['m_title'] ?></div>
                    <select class="form-control border" id="x__status">
                        <?php
                        foreach($this->config->item('e___6186') /* Transaction Status */ as $x__type => $m){
                            echo '<option value="' . $x__type . '" title="' . $m['m_message'] . '">' . $m['m_title'] . '</option>';
                        }
                        ?>
                    </select>

                    <div class="notify_unx_e hidden">
                        <div class="msg alert alert-warning"><span class="icon-block"><i class="fas fa-exclamation-circle discover"></i></span>Saving will remove source</div>
                    </div>



                    <!-- Transaction Message -->
                    <div class="headline no-left-padding" style="margin-top: 20px;"><?= '<span class="icon-block">'.$e___13571[4372]['m_icon'].'</span>'.$e___13571[4372]['m_title'] ?></div>
                    <form class="drag-box" method="post" enctype="multipart/form-data">

                        <textarea class="form-control text-edit border" id="x__message" data-lpignore="true" placeholder="<?= $e___13571[4372]['m_message'] ?>"></textarea>

                        <div class="pull-left">
                            <div id="x__type_preview" class="hideIfEmpty"></div>
                            <div id="x__message_preview" class="hideIfEmpty" style="width: 377px;"></div>
                        </div>

                        <div class="pull-right">
                            <input class="inputfile" type="file" name="file" id="enFile" /><label class="" for="enFile" title="<?= $e___11035[13572]['m_message'] ?>"><?= $e___11035[13572]['m_icon'] ?></label>
                        </div>

                        <div class="doclear">&nbsp;</div>

                    </form>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" onclick="save_13571()" id="save_btn" class="btn btn-source">SAVE</button>
            </div>
        </div>
    </div>
</div>
