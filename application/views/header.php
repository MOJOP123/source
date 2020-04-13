<?php
$session_en = superpower_assigned();
$current_mench = current_mench();
$first_segment = $this->uri->segment(1);
$second_segment = $this->uri->segment(2);
$en_all_11035 = $this->config->item('en_all_11035'); //MENCH  NAVIGATION
$en_all_2738 = $this->config->item('en_all_2738');

//Arrange based on current mench:
$en_all_2738_mench = array();
$did_find = false;
$found_at = 1; //1 or 2 or 3
foreach($en_all_2738 /* Source Status */ as $en_id => $m){
    if(!$did_find){
        if($current_mench['x_id']==$en_id){
            $did_find = true;
        } else {
            $found_at++;
        }
    }

    //Did we find?
    if($did_find){
        $en_all_2738_mench[$en_id] = $m;
    }
}
if($found_at > 1){

    $append_end = 1;

    foreach($en_all_2738 /* Source Status */ as $en_id => $m){
        //Append this:
        $en_all_2738_mench[$en_id] = $m;
        $append_end++;

        //We did it all?
        if($append_end==$found_at){
            break;
        }
    }
}

?><!doctype html>
<html lang="en" >
<head>

    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="/img/<?= $current_mench['x_class'] ?>.png">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= ( isset($title) ? $title . ' | ' : '' ) ?>MENCH</title>

    <?php
    echo '<script type="text/javascript">';

    echo ' var js_session_superpowers_assigned = ' . json_encode( ($session_en && count($this->session->userdata('session_superpowers_assigned'))) ? $this->session->userdata('session_superpowers_assigned') : array() ) . '; ';
    echo ' var js_pl_id = ' . ( $session_en ? $session_en['en_id'] : 0 ) . '; ';
    echo ' var js_pl_name = \'' . ( $session_en ? $session_en['en_name'] : 'Unknown' ) . '\'; ';

    //LOAD JS CACHE:
    foreach($this->config->item('en_all_11054') as $en_id => $m){
        if(count($this->config->item('en_all_'.$en_id))){
            echo ' var js_en_all_'.$en_id.' = ' . json_encode($this->config->item('en_all_'.$en_id)) . '; ';
        }
    }

    //Random Messages:
    echo ' var random_loading_message = ' . json_encode(echo_random_message('loading_notify', true)) . '; ';
    echo ' var random_saving_message = ' . json_encode(echo_random_message('saving_notify', true)) . '; ';

    echo '</script>';
    ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/fbf7f3ae67.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/typeit@6.1.1/dist/typeit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/autosize@4.0.2/dist/autosize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.textcomplete/1.8.5/jquery.textcomplete.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autocomplete.js/0.37.0/autocomplete.jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/algoliasearch/3.35.1/algoliasearch.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.10.1/Sortable.min.js" type="text/javascript"></script>
    <script src="/application/views/mench.js?v=v<?= config_var(11060) ?>" type="text/javascript"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css" integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:800&display=swap" rel="stylesheet">


    <link href="/application/views/mench.css?v=v<?= config_var(11060) ?>" rel="stylesheet"/>
    <script type="text/javascript">
        if(js_pl_id>0){
            //https://help.fullstory.com/hc/en-us/articles/360020623294-FS-setUserVars-Recording-custom-user-data
            FS.identify(js_pl_id, {
                displayName: js_pl_name,
                uid: js_pl_id,
                profileURL: 'https://mench.com/source/'+js_pl_id
            });
        }
    </script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-92774608-1"></script>
    <script type="text/javascript">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-92774608-1');
    </script>
</head>

<body class="<?= 'to'.$current_mench['x_class'] ?>">

<?php
//Any message we need to show here?
if (!isset($flash_message)) {
    $flash_message = $this->session->flashdata('flash_message');
}


if(strlen($flash_message) > 0) {

    //Remove from Flash:
    $this->session->unmark_flash('flash_message');

    echo '<div class="container '.( isset($hide_header) ? ' center-info ' : '' ).'" id="custom_message" style="padding-bottom: 0;">'.$flash_message.'</div>';
}


if(isset($custom_header)){
    echo $custom_header;
}


if(!isset($hide_header)){
    ?>

    <!-- MENCH LINE -->
    <div class="container fixed-top" style="padding-bottom: 0 !important;">
        <div class="row">
            <table class="mench-navigation">
                <tr>

                    <?php
                    //MENCH LOGO
                    if (!isset($session_en['en_id'])) { echo '<td class="block-link block-logo"><a href="/"><img src="/img/mench.png" class="mench-logo mench-spin" /></a></td>'; }
                    ?>

                    <td>

                        <?php

                        echo '<div class="main_nav mench_nav">';
                        if(isset($session_en['en_id'])){

                            $en_all_10876 = $this->config->item('en_all_10876'); //MENCH WEBSITE

                            //Navigation Controller:
                            $nav_controller = array(
                                6205 => 12648, //READ
                                4536 => 12646, //SOURCE
                                4535 => 12647, //NOTE
                            );

                            //Show Mench Menu:
                            foreach ($en_all_2738_mench as $en_id => $m) {


                                $url_extension = null;
                                $is_current_mench = ($current_mench['x_id'] == $en_id);
                                $this_mench = current_mench(strtolower($m['m_name']));
                                $primary_url = 'href="/' . $this_mench['x_name'].'"';


                                if (!$is_current_mench && isset($in) && in_array($this_mench['x_name'], array('read', 'note'))) {
                                    if ($current_mench['x_name'] == 'read' && $this_mench['x_name'] == 'note' && $in['in_id']!=config_var(12156) ) {
                                        $primary_url = 'href="/note/' . $in['in_id'].'"';
                                    } elseif ($current_mench['x_name'] == 'note' && $this_mench['x_name'] == 'read') {
                                        $primary_url = 'href="javascript:void(0);" onclick="go_to_read('.$in['in_id'].')"';
                                    }
                                }


                                /*
                                echo '<a class="mench_coin ' . $this_mench['x_class'] . ' border-' . $this_mench['x_class'] . ($is_current_mench ? ' focustab ' : '') .'" ' . $primary_url . '>';
                                echo '<span class="icon-block">' . $m['m_icon'] . '</span>';
                                echo '<span class="montserrat ' . $this_mench['x_class'] . '_name show-max">' . $m['m_name'] . '&nbsp;</span>';
                                echo '<span class="montserrat" title="'.$player_stats[$this_mench['x_name'].'_count'].'">'.echo_number($player_stats[$this_mench['x_name'].'_count']).'</span>';
                                echo '</a>';
                                */


                                $nav_ui = '';
                                $primary_button = '';

                                foreach ($this->config->item('en_all_'.$nav_controller[$en_id]) as $en_id2 => $m2) {

                                    //Skip superpowers if not assigned
                                    if($en_id2==10957 && !count($this->session->userdata('session_superpowers_assigned'))){
                                        continue;
                                    } elseif($en_id2==7291 && intval($this->session->userdata('session_6196_sign'))){
                                        //Messenger sign in does not allow Signout:
                                        continue;
                                    }

                                    $count = null;
                                    $superpower_actives = array_intersect($this->config->item('en_ids_10957'), $m2['m_parents']);

                                    if(in_array($en_id2, $this->config->item('en_ids_12655'))){

                                        //We need to count this:
                                        if($en_id2==12274){

                                            $source_coins = $this->READ_model->ln_fetch(array(
                                                'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
                                                'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_12274')) . ')' => null, //SOURCE COIN
                                                'ln_creator_source_id' => $session_en['en_id'],
                                            ), array(), 0, 0, array(), 'COUNT(ln_id) as totals');
                                            $count = $source_coins[0]['totals'];

                                        } elseif($en_id2==12273){

                                            $note_coins = $this->READ_model->ln_fetch(array(
                                                'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //Note Status Public
                                                'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
                                                'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_12273')) . ')' => null, //NOTE COIN
                                                'ln_parent_source_id' => $session_en['en_id'],
                                            ), array('in_child'), 0, 0, array(), 'COUNT(ln_id) as totals');
                                            $count = $note_coins[0]['totals'];

                                        } elseif($en_id2==6255){

                                            $read_coins = $this->READ_model->ln_fetch(array(
                                                'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
                                                'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //READ COIN
                                                'ln_creator_source_id' => $session_en['en_id'],
                                            ), array(), 0, 0, array(), 'COUNT(ln_id) as totals');
                                            $count = $read_coins[0]['totals'];

                                        } elseif($en_id2==10573){

                                            $note_bookmarks = $this->READ_model->ln_fetch(array(
                                                'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7356')) . ')' => null, //Note Status Active
                                                'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
                                                'ln_type_source_id' => 10573, //Note Pads Bookmarks
                                                'ln_parent_source_id' => $session_en['en_id'], //For this trainer
                                            ), array('in_child'), 0, 0, array(), 'COUNT(ln_id) as totals');
                                            $count = $note_bookmarks[0]['totals'];

                                        } elseif($en_id2==7347){

                                            $read_bookmarks = $this->READ_model->ln_fetch(array(
                                                'ln_creator_source_id' => $session_en['en_id'],
                                                'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_7347')) . ')' => null, //🔴 READING LIST Note Set
                                                'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
                                                'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //Note Status Public
                                            ), array('in_parent'), 0, 0, array(), 'COUNT(ln_id) as totals');
                                            $count = $read_bookmarks[0]['totals'];

                                        } elseif($en_id2==6182){

                                            $note_archived = $this->READ_model->ln_fetch(array(
                                                'in_status_source_id' => 6182, //Note Archived
                                                'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //Transaction Status Active
                                                'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_12273')) . ')' => null, //NOTE COIN
                                                'ln_parent_source_id' => $session_en['en_id'],
                                            ), array('in_child'), 0, 0, array(), 'COUNT(ln_id) as totals');
                                            $count = $note_archived[0]['totals'];

                                        }

                                    }


                                    //Skip if they don't have it:
                                    if(in_array($en_id2, $this->config->item('en_ids_12656')) && !$count){
                                        continue;
                                    }


                                    //Fetch URL:
                                    if(in_array($en_id2, $this->config->item('en_ids_10876'))){

                                        $nav_url = $en_all_10876[$en_id2]['m_desc'];
                                        $nav_url_parts = explode('/', one_two_explode('mench.com/','',$nav_url));

                                        $is_active = ( $first_segment==$nav_url_parts[0] && (
                                                (!$second_segment && !isset($nav_url_parts[1])) ||
                                                (isset($nav_url_parts[1]) && $second_segment==$nav_url_parts[1]) ||
                                                (is_numeric($second_segment) && !isset($nav_url_parts[1]))
                                            ));

                                    } elseif($en_id2==12581) {

                                        //Home page has no URL (As it's not allowed based on current URL policy)
                                        $nav_url = '/';
                                        $is_active = ( !$first_segment );

                                    } else {

                                        //Don't know URL structure:
                                        continue;

                                    }

                                    $full_name = ( !is_null($count) ? '<span title="'.number_format($count, 0).'">'.echo_number($count).'</span> ' : '' ).$m2['m_name'];

                                    //Determine Primary:
                                    if($is_current_mench && $is_active){

                                        $primary_button = '<button class="btn '.$this_mench['x_class'].'"><span class="icon-block">'.$m2['m_icon'].'</span><span class="show-max">'.$full_name.'</span></button>';

                                    } elseif(!$is_current_mench && in_array($en_id2, $this->config->item('en_ids_12654'))){

                                        $primary_button = '<a href="'.$nav_url.'" class="btn '.$this_mench['x_class'].'"><span class="icon-block">'.$m['m_icon'].'</span><span class="show-max">'.$m['m_name'].'</span></a>';

                                    }

                                    $nav_ui .= '<a href="'.$nav_url.'" class="dropdown-item montserrat doupper '.extract_icon_color($m2['m_icon']).( $is_active ? ' active ' : '' ).( count($superpower_actives) ? superpower_active(end($superpower_actives)) : '' ).'"><span class="icon-block">'.$m2['m_icon'].'</span>'.$full_name.'</a>';

                                }



                                //Show Split Menu:
                                echo '<div class="btn-group">';
                                echo $primary_button;

                                //Show expanded Menu:
                                if($is_current_mench){
                                    echo '<button type="button" class="btn dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="sr-only">Toggle Dropdown</span></button>';
                                    echo '<div class="dropdown-menu">';
                                    echo $nav_ui;
                                    echo '</div>';
                                }

                                echo '</div>';

                            }
                        }
                        echo '</div>';
                        ?>


                        <div class="main_nav search_nav hidden"><form id="searchFrontForm" style="margin-top:5px;"><input class="form-control algolia_search" type="search" id="mench_search" data-lpignore="true" placeholder="<?= $en_all_11035[7256]['m_name'] ?>"></form></div>

                        <div class="main_nav superpower_nav hidden" style="margin-top:10px;">
                            <?php
                            if(count($this->session->userdata('session_superpowers_assigned'))){

                                //Option to Close:
                                echo '<a class="btn btn-sm btn-superpower icon-block" style="cursor:zoom-out;" href="javascript:void();" onclick="toggle_nav(\'superpower_nav\')" title="Close '.$en_all_11035[10957]['m_name'].'"><i class="fas fa-times"></i></a>';

                                //List Superpowers:
                                foreach($this->config->item('en_all_10957') as $superpower_en_id => $m){
                                    if(superpower_assigned($superpower_en_id)){
                                        //Superpower already unlocked:
                                        echo '<a class="btn btn-sm btn-superpower icon-block-sm superpower-frame-'.$superpower_en_id.' '.( in_array($superpower_en_id, $this->session->userdata('session_superpowers_activated')) ? 'active' : '' ).'" href="javascript:void();" onclick="toggle_superpower('.$superpower_en_id.')" title="'.$m['m_name'].' '.$m['m_desc'].' @'.$superpower_en_id.'">'.$m['m_icon'].'</a>';

                                    }
                                }
                            }
                            ?>
                        </div>

                    </td>

                    <td class="block-link <?= ( isset($basic_header) ? ' hidden ' : '' ) ?>"><a href="javascript:void(0);" onclick="toggle_search()"><span class="search_icon"><?= $en_all_11035[7256]['m_icon'] ?></span><span class="search_icon hidden"><i class="far fa-times"></i></span></a></td>

                    <?php
                    if (!isset($session_en['en_id'])) {
                        //Sign In/Up
                        echo '<td class="block-link '.( isset($basic_header) ? ' hidden ' : '' ).'"><a href="/source/sign" title="'.$en_all_11035[4269]['m_name'].'">'.$en_all_11035[4269]['m_icon'].'</a></td>';
                    }
                    ?>

                </tr>
            </table>
        </div>
    </div>

<?php } ?>