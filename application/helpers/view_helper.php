<?php

function view_e_load_more($page, $limit, $list_e_count)
{
    /*
     * Gives an option to "Load More" sources when we have too many to show in one go
     * */

    $ui = '<div class="load-more montserrat list-group-item itemsource no-left-padding"><a href="javascript:void(0);" onclick="e_load_page(' . $page . ', 0)">';

    //Regular section:
    $max_e = (($page + 1) * $limit);
    $max_e = ($max_e > $list_e_count ? $list_e_count : $max_e);
    $ui .= '<span class="icon-block"><i class="far fa-plus-circle source"></i></span><b class="montserrat source">SEE MORE</b>';
    $ui .= '</a></div>';

    return $ui;
}

function view_i_tree_stats($i_stats, $hide_idea){

    //IDEA STATUS BAR
    $CI =& get_instance();
    $e___13544 = $CI->config->item('e___13544'); //IDEA TREE COUNT
    $is_interactive = ( $i_stats['i___6169']!=$i_stats['i___6170'] );
    $has_idea = ( $i_stats['i___6169'] && $i_stats['i___6170'] );

    //Variable time range:
    $ui = '<span class="montserrat doupper" style="padding-right:8px;">';


    //TIME STATS
    if($i_stats['i___6161']<30 && $i_stats['i___6162']<30){
        //SECONDS
        $ui .= '<span class="icon-block">'.$e___13544[13292]['m_icon'].'</span><span class="inline-block" style="min-width:96px;">'.$i_stats['i___6161'].( $i_stats['i___6161']!=$i_stats['i___6162'] ? '<span class="mid-range">-</span>'.$i_stats['i___6162'] : '' ).'&nbsp;SEC</span>';
    } else {
        //MINUTES
        $ui .= '<span class="icon-block">'.$e___13544[13292]['m_icon'].'</span><span class="inline-block" style="min-width:96px;">'.round_minutes($i_stats['i___6161']).( round_minutes($i_stats['i___6161']) != round_minutes($i_stats['i___6162']) ? '<span class="mid-range">-</span>'.round_minutes($i_stats['i___6162']) : '' ).'&nbsp;MIN</span>';
    }



    //IDEA STATS
    if(!$hide_idea){
        $ui .= '<span class="i_x_stats hidden inline-block '.extract_icon_color($e___13544[13629]['m_icon']).'">&nbsp;&nbsp;'.( $has_idea ? $e___13544[13629]['m_icon'] : '' ).' '.( $has_idea ? view_number($i_stats['i___6169']).( $is_interactive ? '<span class="mid-range">-</span>'.view_number($i_stats['i___6170']) : '' ) : '&nbsp;' ).'</span>';
    }


    $ui .= '</span>';


    return $ui;
}

function view_db_field($field_name){

    //Takes a database field name and returns a human-friendly version
    return ucwords(str_replace('i__', '', str_replace('e__', '', str_replace('x__', '', $field_name))));

}


function view_x__message($x__message, $x__type, $full_message = null)
{

    /*
     *
     * Displays Source Transactions @4592
     *
     * $full_message Would be the entire message
     * in an idea message that would be passed down
     * to the source profile $x__message value.
     *
     * */

    $CI =& get_instance();

    if ($x__type == 4256 /* Generic URL */) {

        return '<div class="block"><a href="' . $x__message . '" target="_blank"><span class="url_truncate">' . view_url_clean($x__message) . '</span></a></div>';

    } elseif ($x__type == 4257 /* Embed Widget URL? */) {

        return view_url_embed($x__message, $full_message);

    } elseif ($x__type == 4260 /* Image URL */) {

        return '<img data-src="' . $x__message . '" src="/img/mench.png" alt="IMAGE" class="content-image lazyimage" />';

    } elseif ($x__type == 4259 /* Audio URL */) {

        return  '<audio controls><source src="' . $x__message . '" type="audio/mpeg"></audio>' ;

    } elseif ($x__type == 4258 /* Video URL */) {

        return  '<video width="100%" onclick="this.play()" controls poster="https://s3foundation.s3-us-west-2.amazonaws.com/9988e7bc95f25002b40c2a376cc94806.png"><source src="' . $x__message . '" type="video/mp4"></video>' ;

    } elseif ($x__type == 4261 /* File URL */) {

        $e___11035 = $CI->config->item('e___11035'); //MENCH NAVIGATION
        return '<a href="' . $x__message . '" class="btn btn-idea" target="_blank">'.$e___11035[13573]['m_icon'].' '.$e___11035[13573]['m_title'].'</a>';

    } elseif(strlen($x__message) > 0) {

        return htmlentities($x__message);

    } else {

        //UNKNOWN
        return false;

    }
}




function view_url_embed($url, $full_message = null, $return_array = false)
{


    /*
     *
     * Detects and displays URLs from supported website with an embed widget
     *
     * Alert: Changes to this function requires us to re-calculate all current
     *       values for x__type as this could change the equation for those
     *       transaction types. Change with care...
     *
     * */



    $clean_url = null;
    $embed_html_code = null;
    $prefix__message = null;
    $CI =& get_instance();
    $e___11035 = $CI->config->item('e___11035');

    if(is_https_url($url)){

        //See if $url has a valid embed video in it, and transform it if it does:
        $is_embed = (substr_count($url, 'youtube.com/embed/') == 1);

        if ((substr_count($url, 'youtube.com/watch') == 1) || substr_count($url, 'youtu.be/') == 1 || $is_embed) {

            $start_time = 0;
            $end_time = 0;
            $video_id = extract_youtube_id($url);

            if ($video_id) {

                $string_references = extract_e_references($full_message);

                if($string_references['ref_time_found']){

                    $start_time = $string_references['ref_time_start'];
                    $end_time = $string_references['ref_time_end'];

                } elseif($is_embed){

                    if(is_numeric(one_two_explode('start=','&',$url))){
                        $start_time = intval(one_two_explode('start=','&',$url));
                    }
                    if(is_numeric(one_two_explode('end=','&',$url))){
                        $end_time = intval(one_two_explode('end=','&',$url));
                    }
                }

                //Set the Clean URL:
                $clean_url = 'https://www.youtube.com/watch?v=' . $video_id;

                //Header For Time
                if($end_time){
                    $embed_html_code .= '<div class="headline" style="padding-bottom: 0; font-size:0.84em;"><span class="icon-block-xs">'.$e___11035[13292]['m_icon'].'</span>'.round_minutes($end_time-$start_time).' MIN CLIP ('.view_time_hours($start_time, true).' TO '.view_time_hours($end_time, true).')</div>';
                }

                $embed_html_code .= '<div class="media-content"><div class="yt-container video-sorting" style="margin-top:5px;"><iframe src="//www.youtube.com/embed/' . $video_id . '?wmode=opaque&theme=light&color=white&keyboard=1&autohide=2&modestbranding=1&showinfo=0&rel=0&iv_load_policy=3&start=' . $start_time . ($end_time ? '&end=' . $end_time : '') . '" frameborder="0" allowfullscreen class="yt-video"></iframe></div></div>';

            }

        } elseif (substr_count($url, 'vimeo.com/') == 1 && is_numeric(one_two_explode('vimeo.com/','?',$url))) {

            //Seems to be Vimeo:
            $video_id = trim(one_two_explode('vimeo.com/', '?', $url));

            //This should be an integer!
            if (intval($video_id) == $video_id) {
                $clean_url = 'https://vimeo.com/' . $video_id;
                $embed_html_code = '<div class="media-content"><div class="yt-container video-sorting" style="margin-top:5px;"><iframe src="https://user.vimeo.com/video/' . $video_id . '?title=0&byline=0" class="yt-video" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div></div>';
            }

        } elseif (substr_count($url, 'wistia.com/medias/') == 1) {

            //Seems to be Wistia:
            $video_id = trim(one_two_explode('wistia.com/medias/', '?', $url));
            $clean_url = trim(one_two_explode('', '?', $url));
            $embed_html_code = '<script src="https://fast.wistia.com/embed/medias/' . $video_id . '.jsonp" async></script><script src="https://fast.wistia.com/assets/external/E-v1.js" async></script><div class="wistia_responsive_padding video-sorting" style="padding:56.25% 0 0 0;position:relative;"><div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;"><div class="wistia_embed wistia_async_' . $video_id . ' seo=false videoFoam=true" style="height:100%;width:100%">&nbsp;</div></div></div>';

        }
    }


    if ($return_array) {

        //Return all aspects of this parsed URL:
        return array(
            'status' => ( $embed_html_code ? 1 : 0 ),
            'embed_code' => $embed_html_code,
            'clean_url' => $clean_url,
        );

    } else {

        //Just return the embed code:
        return $embed_html_code;

    }
}

function view_i_title($i, $common_prefix = null){
    if(strlen($common_prefix) > 0){
        $i['i__title'] = trim(substr($i['i__title'], strlen($common_prefix)));
    }
    return '<span class="text__4736_'.$i['i__id'].'">'.htmlentities(trim($i['i__title'])).'</span>';
}


function view_13574($x, $note_e = false)
{

    /*
     *
     * A wrapper function that helps manage messages
     * by giving the message additional platform functions
     * such as editing and changing message type.
     *
     * */


    $CI =& get_instance();
    $user_e = superpower_assigned();
    $e___4485 = $CI->config->item('e___4485'); //IDEA NOTES
    $e___6186 = $CI->config->item('e___6186'); //Transaction Status
    $e___11035 = $CI->config->item('e___11035');
    $note_e = ( $note_e || superpower_active(10984, true) );


    //Build the HTML UI:
    $ui = '';
    $ui .= '<div class="list-group-item itemidea is-msg note_sortable msg_e_type_' . $x['x__type'] . '" id="ul-nav-' . $x['x__id'] . '" x__id="' . $x['x__id'] . '" title="'.$x['e__title'].' Posted On '.substr($x['x__time'], 0, 19).'" data-toggle="tooltip" data-placement="top">';
    $ui .= '<div style="overflow:visible !important;">';

    //Type & Delivery Method:
    $ui .= '<div class="text_message edit-off" id="msgbody_' . $x['x__id'] . '">';
    $ui .= $CI->X_model->message_send($x['x__message'], $user_e, $x['x__right']);
    $ui .= '</div>';

    //Editing menu:
    if($note_e){

        $ui .= '<div class="note-editor edit-off"><span class="show-on-hover">';

            //SORT NOTE
            if(in_array($x['x__type'], $CI->config->item('n___4603'))){
                $ui .= '<span title="SORT"><i class="fas fa-sort i_note_sorting"></i></span>';
            }

            //MODIFY NOTE
            $ui .= '<span title="MODIFY"><a href="javascript:load_13574(' . $x['x__id'] . ');" title="'.$e___11035[13574]['m_title'].'">'.$e___11035[13574]['m_icon'].'</a></span>';

            //REMOVE NOTE
            $ui .= '<span><a href="javascript:void(0);" onclick="remove_13579(' . $x['x__id'] . ', '.$x['x__type'].')" title="'.$e___11035[13579]['m_title'].'">'.$e___11035[13579]['m_icon'].'</a></span>';

        $ui .= '</span></div>';


        //Text editing:
        $ui .= '<textarea onkeyup="count_13574(' . $x['x__id'] . ')" name="x__message" id="message_body_' . $x['x__id'] . '" class="edit-on hidden msg note-textarea algolia_search" placeholder="'.stripslashes($x['x__message']).'">' . $x['x__message'] . '</textarea>';


        //Editing menu:
        $ui .= '<ul class="msg-nav space-left">';

            //SAVE
            $ui .= '<li class="edit-on hidden"><a class="btn btn-idea white-third" href="javascript:save_13574(' . $x['x__id'] . ',' . $x['x__type'] . ');"><i class="fas fa-check"></i> Save</a></li>';

            //CANCEL
            $ui .= '<li class="edit-on hidden"><a class="btn btn-idea white-third" href="javascript:cancel_13574(' . $x['x__id'] . ');"><i class="fas fa-times"></i></a></li>';

            //TEXT COUNTER
            $ui .= '<li class="edit-on hidden"><span id="ideaNoteCount' . $x['x__id'] . '"><span id="charEditingNum' . $x['x__id'] . '">0</span>/' . config_var(4485) . '</span></li>';

            //Update result:
            $ui .= '<li class="edit-updates hideIfEmpty"></li>'; //Show potential errors

        $ui .= '</ul>';
    }

    $ui .= '</div>';
    $ui .= '</div>';

    return $ui;
}


function view_e__icon($e__icon = null)
{
    //A simple function to display the User Icon OR the default icon if not available:
    if (strlen($e__icon) > 0) {

        return $e__icon;

    } else {
        //Return default icon for sources:
        $CI =& get_instance();
        $e___12467 = $CI->config->item('e___12467'); //MENCH
        return $e___12467[12274]['m_icon'];
    }
}


function view_number($number)
{

    //Round & format numbers

    if ($number < 950) {
        return intval($number);
    }

    if ($number >= 950000000) {
        $formatting = array(
            'multiplier' => (1 / 1000000000),
            'decimals' => 1,
            'suffix' => 'B',
        );
    } elseif ($number >= 9500000) {
        $formatting = array(
            'multiplier' => (1 / 1000000),
            'decimals' => 0,
            'suffix' => 'M',
        );
    } elseif ($number >= 950000) {
        $formatting = array(
            'multiplier' => (1 / 1000000),
            'decimals' => 1,
            'suffix' => 'M',
        );
    } elseif ($number >= 9500) {
        $formatting = array(
            'multiplier' => (1 / 1000),
            'decimals' => 0,
            'suffix' => 'K',
        );
    } else {
        $formatting = array(
            'multiplier' => (1 / 1000),
            'decimals' => 1,
            'suffix' => 'K',
        );
    }

    return round(($number * $formatting['multiplier']), $formatting['decimals']) . $formatting['suffix'];

}


function view_x($x, $is_parent_tr = false)
{

    $CI =& get_instance();
    $e___4593 = $CI->config->item('e___4593'); //Transaction Type
    $e___4341 = $CI->config->item('e___4341'); //Transaction Table
    $e___6186 = $CI->config->item('e___6186'); //Transaction Status
    $user_e = superpower_assigned();
    $hide_sensitive = (in_array($x['x__type'] , $CI->config->item('n___4755')) && (!$user_e || $x['x__source']!=$user_e['e__id']) && !superpower_active(12701, true));



    if(!isset($e___4593[$x['x__type']])){
        //We've probably have not yet updated php cache, set error:
        $e___4593[$x['x__type']] = array(
            'm_icon' => '<i class="fas fa-exclamation-circle"></i>',
            'm_title' => 'Transaction Type Not Synced in PHP Cache',
            'm_message' => '',
            'm_profile' => array(),
        );
    }







    //Display the item
    $ui = '<div class="x-list">';


    //ID
    $ui .= '<div class="simple-line"><a href="/ledger?x__id='.$x['x__id'].'" data-toggle="tooltip" data-placement="top" title="'.$e___4341[4367]['m_title'].'" class="mono-space"><span class="icon-block">'.$e___4341[4367]['m_icon']. '</span>'.$x['x__id'].'</a></div>';

    //TIME
    $ui .= '<div class="simple-line"><span data-toggle="tooltip" data-placement="top" title="' . $e___4341[4362]['m_title'].': '.$x['x__time'] . ' PST"><span class="icon-block">'.$e___4341[4362]['m_icon']. '</span>' . view_time_difference(strtotime($x['x__time'])) . ' Ago</span></div>';


    //Order
    if($x['x__sort'] > 0){
        $ui .= '<div class="simple-line"><span data-toggle="tooltip" data-placement="top" title="'.$e___4341[4370]['m_title']. '"><span class="icon-block">'.$e___4341[4370]['m_icon']. '</span>'.view_ordinal($x['x__sort']).'</span></div>';
    }


    if(!$hide_sensitive){
        //Metadata
        if(strlen($x['x__metadata']) > 0){
            $ui .= '<div class="simple-line"><a href="/e/plugin/12722?x__id=' . $x['x__id'] . '"><span class="icon-block">'.$e___4341[6103]['m_icon']. '</span><u>'.$e___4341[6103]['m_title']. '</u></a></div>';
        }

        //Message
        if(strlen($x['x__message']) > 0 && $x['x__message']!='@'.$x['x__up']){
            $ui .= '<div class="simple-line" data-toggle="tooltip" data-placement="top" title="'.$e___4341[4372]['m_title'].'"><span class="icon-block">'.$e___4341[4372]['m_icon'].'</span><div class="title-block x-msg">'.htmlentities($x['x__message']).'</div></div>';
        }
    }


    //STATUS
    $ui .= '<div class="simple-line"><a href="/@'.$x['x__status'].'" data-toggle="tooltip" data-placement="top" title="'.$e___4341[6186]['m_title'].( strlen($e___6186[$x['x__status']]['m_message']) ? ': '.$e___6186[$x['x__status']]['m_message'] : '' ).'" class="montserrat"><span class="icon-block">'.$e___4341[6186]['m_icon']. '</span>'.$e___6186[$x['x__status']]['m_icon'].'&nbsp;<span class="'.extract_icon_color($e___6186[$x['x__status']]['m_icon']).'">'.$e___6186[$x['x__status']]['m_title'].'</span></a></div>';



    //TYPE
    $ui .= '<div class="simple-line"><a href="/@'.$x['x__type'].'" data-toggle="tooltip" data-placement="top" title="'.$e___4341[4593]['m_title'].( strlen($e___4593[$x['x__type']]['m_message']) ? ': '.$e___4593[$x['x__type']]['m_message'] : '' ).'" class="montserrat"><span class="icon-block">'.$e___4341[4593]['m_icon']. '</span>'. $e___4593[$x['x__type']]['m_icon'] . '&nbsp;<span class="'.extract_icon_color($e___4593[$x['x__type']]['m_icon']).'">' . $e___4593[$x['x__type']]['m_title'] . '</span></a></div>';


    //Hide Sensitive Details?
    if($hide_sensitive){

        //Hide Information:
        $ui .= '<div class="simple-line"><span data-toggle="tooltip" class="montserrat" data-placement="top" title="Details are kept private"><span class="icon-block"><i class="fal fa-eye-slash"></i></span>PRIVATE INFORMATION</span></div>';

    } else {

        //Creator (Do not repeat)
        if($x['x__source'] > 0 && $x['x__source']!=$x['x__up'] && $x['x__source']!=$x['x__down']){

            $add_e = $CI->E_model->fetch(array(
                'e__id' => $x['x__source'],
            ));

            $ui .= '<div class="simple-line"><a href="/@'.$add_e[0]['e__id'].'" data-toggle="tooltip" data-placement="top" title="'.$e___4341[4364]['m_title'].'" class="montserrat"><span class="icon-block">'.$e___4341[4364]['m_icon']. '</span><span class="'.extract_icon_color($add_e[0]['e__icon']).'"><span class="img-block">'.view_e__icon($add_e[0]['e__icon']) . '</span> ' . $add_e[0]['e__title'] . '</span></a></div>';

        }

    }

    //5x Relations:
    if(!$is_parent_tr){

        $var_index = var_index();
        foreach($CI->config->item('e___10692') as $e__id => $m) {

            //Do we have this set?
            if(!array_key_exists($e__id, $var_index) || !intval($x[$var_index[$e__id]])){
                continue;
            }

            if(in_array(6160 , $m['m_profile'])){

                //SOURCE
                $es = $CI->E_model->fetch(array('e__id' => $x[$var_index[$e__id]]));

                $ui .= '<div class="simple-line"><a href="/@'.$es[0]['e__id'].'" data-toggle="tooltip" data-placement="top" title="'.$e___4341[$e__id]['m_title'].'" class="montserrat"><span class="icon-block">'.$e___4341[$e__id]['m_icon']. '</span>'.( $x[$var_index[$e__id]]==$x['x__source'] ? $e___4341[4364]['m_icon']. '&nbsp;' : '' ).'<span class="'.extract_icon_color($es[0]['e__icon']).' img-block">'.view_e__icon($es[0]['e__icon']). '&nbsp;'.$es[0]['e__title'].'</span></a></div>';

            } elseif(in_array(6202 , $m['m_profile'])){

                //IDEA
                $is = $CI->I_model->fetch(array('i__id' => $x[$var_index[$e__id]]));

                $ui .= '<div class="simple-line"><a href="/i/i_go/'.$is[0]['i__id'].'" data-toggle="tooltip" data-placement="top" title="'.$e___4341[$e__id]['m_title'].'" class="montserrat"><span class="icon-block">'.$e___4341[$e__id]['m_icon']. '</span>'.view_cache(4737 /* Idea Status */, $is[0]['i__status'], true, 'right', $is[0]['i__id']).view_i_title($is[0]).'</a></div>';

            } elseif(in_array(4367 , $m['m_profile'])){

                //PARENT DISCOVER
                $x = $CI->X_model->fetch(array('x__id' => $x[$var_index[$e__id]]));

                if(count($x)){
                    $ui .= '<div class="simple-line"><span class="icon-block" data-toggle="tooltip" data-placement="top" title="'.$e___4341[$e__id]['m_title'].'">'.$e___4341[$e__id]['m_icon']. '</span><div class="x-ref">'.view_x($x[0], true).'</div></div>';
                }

            }
        }
    }


    $ui .= '</div>';

    return $ui;
}


function view_url_clean($url)
{
    //Returns the watered-down version of the URL for a cleaner UI:
    return rtrim(str_replace('http://', '', str_replace('https://', '', str_replace('www.', '', $url))), '/');
}


function view_time_difference($t, $second_time = null)
{
    if (!$second_time) {
        $second_time = time(); //Now
    } else {
        $second_time = strtotime(substr($second_time, 0, 19));
    }

    $time = $second_time - (is_int($t) ? $t : strtotime(substr($t, 0, 19))); // to get the time since that moment
    $is_future = ($time < 0);
    $time = abs($time);
    $time_units = array(
        31536000 => 'Year',
        2592000 => 'Month',
        604800 => 'Week',
        86400 => 'Day',
        3600 => 'Hour',
        60 => 'Minute',
        1 => 'Second'
    );

    foreach($time_units as $unit => $period) {
        if ($time < $unit && $unit > 1) continue;
        if ($unit >= 2592000 && fmod(($time / $unit), 1) >= 0.33 && fmod(($time / $unit), 1) <= .67) {
            $numberOfUnits = number_format(($time / $unit), 1);
        } else {
            $numberOfUnits = number_format(($time / $unit), 0);
        }

        if ($numberOfUnits < 1 && $unit == 1) {
            $numberOfUnits = 1; //Change "0 seconds" to "1 second"
        }

        return $numberOfUnits . ' ' . $period . (($numberOfUnits > 1) ? 's' : '');
    }
}


function view_cache($config_var_id, $e__id, $micro_status = true, $data_placement = 'top', $i__id = 0)
{

    /*
     *
     * UI for Platform Cache sources
     *
     * */

    $CI =& get_instance();
    $config_array = $CI->config->item('e___'.$config_var_id);
    $cache = $config_array[$e__id];
    if (!$cache) {
        //Could not find matching item
        return false;
    }


    //We have two skins for displaying Status:
    if (is_null($data_placement)) {
        if($micro_status){
            return $cache['m_icon'];
        } else {
            return $cache['m_icon'].' '.$cache['m_title'];
        }
    } else {
        //data-toggle="tooltip" data-placement="' . $data_placement . '"
        return '<span class="'.( $micro_status ? 'cache_micro_'.$config_var_id.'_'.$i__id : '' ).'" ' . ( $micro_status && !is_null($data_placement) ? ' title="' . ($micro_status ? $cache['m_title'] : '') . (strlen($cache['m_message']) > 0 ? ($micro_status ? ': ' : '') . $cache['m_message'] : '') . '"' : 'style="cursor:pointer;"') . '>' . $cache['m_icon'] . ' ' . ($micro_status ? '' : $cache['m_title']) . '</span>';
    }
}










function view_coins_e($x__type, $e__id, $page_num = 0, $append_coin_icon = true){

    /*
     *
     * Loads Source Mench Coins
     *
     * */

    $CI =& get_instance();

    if($x__type==12274){

        //SOURCES
        $order_columns = array('x__sort' => 'ASC', 'e__title' => 'ASC');
        $join_objects = array('x__down');
        $query_filters = array(
            'x__up' => $e__id,
            'x__type IN (' . join(',', $CI->config->item('n___4592')) . ')' => null, //SOURCE LINKS
            'x__status IN (' . join(',', $CI->config->item('n___7359')) . ')' => null, //PUBLIC
            'e__status IN (' . join(',', $CI->config->item('n___7358')) . ')' => null, //ACTIVE
        );

    } elseif($x__type==12273){

        //IDEAS
        $join_objects = array('x__right');

        if($page_num > 0){
            $order_columns = array('x__sort' => 'ASC');
            $query_filters = array(
                'i__status IN (' . join(',', $CI->config->item('n___7356')) . ')' => null, //ACTIVE
                'x__status IN (' . join(',', $CI->config->item('n___7359')) . ')' => null, //PUBLIC
                'x__type' => 10573, //MY IDEAS
                'x__up' => $e__id, //For this user
            );
        } else {
            $order_columns = array('i__weight' => 'DESC'); //BEST IDEAS
            $query_filters = array(
                'i__status IN (' . join(',', $CI->config->item('n___7356')) . ')' => null, //ACTIVE
                'x__status IN (' . join(',', $CI->config->item('n___7359')) . ')' => null, //PUBLIC
                'x__type IN (' . join(',', $CI->config->item('n___13550')) . ')' => null, //SOURCE IDEAS
                '(x__up = '.$e__id.' OR x__down = '.$e__id.')' => null,
            );
        }

    } elseif($x__type==6255){

        //DISCOVERIES
        $join_objects = array('x__left');

        if($page_num > 0){
            $order_columns = array('x__sort' => 'ASC');
            $query_filters = array(
                'x__source' => $e__id,
                'x__type IN (' . join(',', $CI->config->item('n___12969')) . ')' => null, //MY DISCOVERIES
                'x__status IN (' . join(',', $CI->config->item('n___7359')) . ')' => null, //PUBLIC
                'i__status IN (' . join(',', $CI->config->item('n___7355')) . ')' => null, //PUBLIC
            );
        } else {
            $order_columns = array('x__id' => 'DESC'); //LATEST DISCOVERIES
            $query_filters = array(
                'x__source' => $e__id,
                'x__type IN (' . join(',', $CI->config->item('n___6255')) . ')' => null, //DISCOVER COIN
                'x__status IN (' . join(',', $CI->config->item('n___7359')) . ')' => null, //PUBLIC
                'i__status IN (' . join(',', $CI->config->item('n___7355')) . ')' => null, //PUBLIC
            );
        }

    }

    //Return Results:
    if($page_num > 0){

        return $CI->X_model->fetch($query_filters, $join_objects, config_var(11064), ($page_num-1)*config_var(11064), $order_columns);

    } else {
        $count_query = $CI->X_model->fetch($query_filters, $join_objects, 1, 0, array(), 'COUNT(x__id) as totals');
        if($append_coin_icon){
            $e___12467 = $CI->config->item('e___12467'); //MENCH COINS
            return ( $count_query[0]['totals'] > 0 ? '<span class="montserrat '.extract_icon_color($e___12467[$x__type]['m_icon']).'" title="'.$e___12467[$x__type]['m_title'].'">'.$e___12467[$x__type]['m_icon'].'&nbsp;'.view_number($count_query[0]['totals']).'</span>' : null);
        } else {
            return intval($count_query[0]['totals']);
        }
    }

}



function view_coins_i($x__type, $i, $append_coin_icon = true, $append_name = false, $data_placement = 'top'){

    /*
     *
     * Loads Idea Mench Coins
     *
     * */

    $CI =& get_instance();

    if($x__type==12274){

        //SOURCES
        $i_stats = i_stats($i['i__metadata']);
        $count_query = $i_stats['e_count'];

    } elseif($x__type==12273){

        //IDEAS
        $i_stats = i_stats($i['i__metadata']);
        $count_query = $i_stats['i___6170'];

    } elseif($x__type==6255){

        $query_filters = array(
            'x__status IN (' . join(',', $CI->config->item('n___7359')) . ')' => null, //PUBLIC
            'x__type IN (' . join(',', $CI->config->item('n___6255')) . ')' => null, //DISCOVER COIN
            'x__left' => $i['i__id'],
        );

        if(isset($_GET['focus__e'])){
            $query_filters['x__source'] = intval($_GET['focus__e']);
        }


        $x_coins = $CI->X_model->fetch($query_filters, array(), 1, 0, array(), 'COUNT(x__id) as totals');
        $count_query = $x_coins[0]['totals'];

    }

    //Return Results:
    if($append_coin_icon){
        $e___12467 = $CI->config->item('e___12467'); //MENCH COINS
        return ( $count_query > 0 ? '<span title="'.$e___12467[$x__type]['m_title'].'" '.( $data_placement ? 'data-toggle="tooltip" data-placement="'.$data_placement.'"' : ''  ).' class="montserrat '.extract_icon_color($e___12467[$x__type]['m_icon']).'">'.( $append_name ? '' : $e___12467[$x__type]['m_icon'].'&nbsp;'  ).view_number($count_query).( $append_name ? '&nbsp'.$e___12467[$x__type]['m_title'] : '' ).'</span>' : null);
    } else {
        return intval($count_query);
    }

}



function view_icon_i_x($completion_percentage, $i, $not_started_type){

    $user_e = superpower_assigned();
    if(!$not_started_type || !$user_e){
        //IDAE Icon
        return view_i_icon($i);
    } elseif($completion_percentage <= 0){
        //Assign default not started type:
        $x_legend = $not_started_type;
    } elseif($completion_percentage<100){
        //DISCOVER IN PROGRESS
        $x_legend = 12447;
    } else {
        //DISCOVER COMPLETED
        $x_legend = 13338;
    }

    $CI =& get_instance();
    $e___12446 = $CI->config->item('e___12446'); //DISCOVER ICON LEGEND
    return '<span title="'.$e___12446[$x_legend]['m_title'].' '.$completion_percentage.'% DONE">'.$e___12446[$x_legend]['m_icon'].'</span>';

}

function view_i_x($i, $index_id, $can_click, $common_prefix = null, $show_editor = false, $completion_rate = null)
{
    //See if user is logged-in:
    $CI =& get_instance();
    $user_session = superpower_assigned();
    $user_e__id = ( (isset($_GET['focus__e']) ? $_GET['focus__e'] : ( $user_session ? $user_session['e__id'] : 0 ) ));
    $is_saved = ( isset($i['x__type']) && $i['x__type']==12896 );

    if(!$completion_rate){
        if($user_e__id){
            $completion_rate = $CI->X_model->completion_progress($user_e__id, $i);
        } else {
            $completion_rate['completion_percentage'] = 0;
        }
    }

    $i_stats = i_stats($i['i__metadata']);
    $first_segment = $CI->uri->segment(1);
    $e___12467 = $CI->config->item('e___12467'); //MENCH COINS
    $e___13369 = $CI->config->item('e___13369'); //IDEA COVER UI
    $has_completion = $completion_rate['completion_percentage']>0;

    //Build View:
    $ui  = '<div id="x_save_'.$i['i__id'].'" '.( isset($i['x__id']) ? ' x__id="'.$i['x__id'].'" ' : '' ).' class="list-group-item no-side-padding '.( $show_editor ? ' home_sort ' : '' ).( $can_click ? ' itemdiscover ' : '' ).'" style="padding-right:17px;">';

    $ui .= ( $can_click ? '<a href="/' . $i['i__id'] .'" class="itemdiscover">' : '' );


    if($has_completion && $index_id==-1){
        $ui .= view_progress($completion_rate, $i);
    }



    $ui .= '<div class="row">';
        $ui .= '<div class="col-sm col-md">';

            $ui .= '<span class="icon-block">'.view_icon_i_x($completion_rate['completion_percentage'], $i, ( $index_id!=0 ? 13752 /* NOT STARTED */ : 13822 /* Next Idea */ ) ).'</span>';
            $ui .= '<b class="'.( $can_click ? 'montserrat' : '' ).' i-url title-block">'.view_i_title($i, $common_prefix).'</b>';

        $ui .= '</div>';
        $ui .= '<div class="col-sm-5 col-md-4 col2nd i_x_stats hidden">';

            //IDA STATS
            $ui .= view_i_tree_stats($i_stats, false);

        $ui .= '</div>';
    $ui .= '</div>';



    $ui .= ( $can_click ? '</a>' : '' );


    //Give option to remove saved ideas:
    if($show_editor && $is_saved){
        $ui .= '<div class="note-editor edit-off">';
        $ui .= '<span class="show-on-hover">';
        $ui .= '<span><a href="javascript:void(0);" title="Unsave" data-toggle="tooltip" data-placement="left" onclick="x_save('.$i['i__id'].');$(\'#x_save_'.$i['i__id'].'\').remove();"><i class="fas fa-times"></i></a></span>';
        $ui .= '</span>';
        $ui .= '</div>';
    }


    $ui .= '</div>';

    return $ui;
}


function view_i_tree_e($i){
    $CI =& get_instance();
    $i_stats = i_stats($i['i__metadata']);
    $ui = '';

    foreach($CI->config->item('e___4251') as $e__id => $m2){
        if($i_stats['count_'.$e__id]>0){
            $ui .= '<div class="headline"><span class="icon-block">'.$m2['m_icon'].'</span>'.$i_stats['count_'.$e__id].' '.$m2['m_title'].':</div>';
            $ui .= '<div class="list-group" style="margin-bottom:33px;">';
            foreach ($i_stats['array_'.$e__id] as $e) {
                $ui .= view_e_basic($e);
            }
            $ui .= '</div>';
        }
    }
    return $ui;
}

function view_i_scores_answer($i__id, $depth_levels, $original_depth_levels, $previous_i__type){

    if($depth_levels<=0){
        //End recursion:
        return false;
    }

    //We're going 1 level deep:
    $depth_levels--;

    //Go down recursively:
    $CI =& get_instance();
    $e___6186 = $CI->config->item('e___6186'); //Transaction Status
    $e___4486 = $CI->config->item('e___4486');
    $e___4737 = $CI->config->item('e___4737'); // Idea Status
    $e___7585 = $CI->config->item('e___7585'); // Idea Subtypes


    $ui = null;
    foreach($CI->X_model->fetch(array(
        'x__left' => $i__id,
        'x__type IN (' . join(',', $CI->config->item('n___4486')) . ')' => null, //IDEA LINKS
        'x__status IN (' . join(',', $CI->config->item('n___7360')) . ')' => null, //ACTIVE
        'i__status IN (' . join(',', $CI->config->item('n___7356')) . ')' => null, //ACTIVE
    ), array('x__right'), 0, 0, array('x__sort' => 'ASC')) as $i_x){

        //Prep Metadata:
        $metadata = unserialize($i_x['x__metadata']);
        $tr__assessment_points = ( isset($metadata['tr__assessment_points']) ? $metadata['tr__assessment_points'] : 0 );
        $messages = $CI->X_model->fetch(array(
            'x__status IN (' . join(',', $CI->config->item('n___7360')) . ')' => null, //ACTIVE
            'x__type' => 4231, //IDEA NOTES Messages
            'x__right' => $i_x['i__id'],
        ), array(), 0, 0, array('x__sort' => 'ASC'));

        //Display block:
        $ui .= '<div class="'.( $tr__assessment_points==0 ? 'no-assessment ' : 'has-assessment' ).'">';
        $ui .= '<span class="icon-block" data-toggle="tooltip" data-placement="top" title="Idea Transaction Type: '.$e___4486[$i_x['x__type']]['m_title'].'">'. $e___4486[$i_x['x__type']]['m_icon'] . '</span>';
        $ui .= '<span class="icon-block" data-toggle="tooltip" data-placement="top" title="Idea Transaction Status: '.$e___6186[$i_x['x__status']]['m_title'].'">'. $e___6186[$i_x['x__status']]['m_icon'] . '</span>';

        $ui .= '<span class="icon-block" data-toggle="tooltip" data-placement="top" title="Idea Type: '.$e___7585[$i_x['i__type']]['m_title'].'">'. $e___7585[$i_x['i__type']]['m_icon'] . '</span>';
        $ui .= '<span class="icon-block" data-toggle="tooltip" data-placement="top" title="Idea Status: '.$e___4737[$i_x['i__status']]['m_title'].'">'. $e___4737[$i_x['i__status']]['m_icon']. '</span>';
        $ui .= '<a href="?i__id='.$i_x['i__id'].'&depth_levels='.$original_depth_levels.'" data-toggle="tooltip" data-placement="top" title="Navigate report to this idea"><u>' .   view_i_title($i_x) . '</u></a>';

        $ui .= ' [<span data-toggle="tooltip" data-placement="top" title="Completion Marks">'.( ($i_x['x__type'] == 4228 && in_array($previous_i__type , $CI->config->item('n___6193') /* OR Ideas */ )) || ($i_x['x__type'] == 4229) ? view_i_marks($i_x) : '' ).'</span>]';

        if(count($messages) > 0){
            $ui .= ' <a href="javascript:void(0);" onclick="$(\'.messages-'.$i_x['i__id'].'\').toggleClass(\'hidden\');"><i class="fas fa-comment"></i><b>' .  count($messages) . '</b></a>';
        }
        $ui .= '</div>';

        //Display Messages:
        $ui .= '<div class="messages-'.$i_x['i__id'].' hidden">';
        foreach($messages as $msg) {
            $ui .= '<div class="tip_bubble">';
            $ui .= $CI->X_model->message_send($msg['x__message']);
            $ui .= '</div>';
        }
        $ui .= '</div>';

        //Go Recursively down:
        $ui .=  view_i_scores_answer($i_x['i__id'], $depth_levels, $original_depth_levels, $i_x['i__type']);

    }

    //Return the wrapped UI if existed:
    return ($ui ? $ui : false);
}

function view_radio_e($parent_e__id, $child_e__id, $enable_mulitiselect, $show_max = 25){

    /*
     * Print UI for
     * */

    $CI =& get_instance();
    $count = 0;

    $ui = '<div class="list-group list-radio-select radio-'.$parent_e__id.'">';

    if(!count($CI->config->item('n___'.$parent_e__id))){
        return false;
    }

    $already_selected = array();
    foreach($CI->X_model->fetch(array(
        'x__up IN (' . join(',', $CI->config->item('n___'.$parent_e__id)) . ')' => null,
        'x__down' => $child_e__id,
        'x__type IN (' . join(',', $CI->config->item('n___4592')) . ')' => null, //SOURCE LINKS
        'x__status IN (' . join(',', $CI->config->item('n___7359')) . ')' => null, //PUBLIC
    )) as $sel){
        array_push($already_selected, $sel['x__up']);
    }

    if(!count($already_selected) && $parent_e__id==13491){
        //Medium Font as Default:
        $already_selected = array(13493);
    }

    foreach($CI->config->item('e___'.$parent_e__id) as $e__id => $m) {
        $ui .= '<a href="javascript:void(0);" onclick="e_radio('.$parent_e__id.','.$e__id.','.$enable_mulitiselect.')" class="item'.extract_icon_color($m['m_icon']).' list-group-item montserrat itemsetting item-'.$e__id.' '.( $count>=$show_max ? 'extra-items-'.$parent_e__id.' hidden ' : '' ).( in_array($e__id, $already_selected) ? ' active ' : '' ). '"><span class="icon-block">'.$m['m_icon'].'</span>'.$m['m_title'].'<span class="change-results"></span></a>';
        $count++;
    }


    //Did we have too many items?
    if($count>=$show_max){
        //Show "Show more" button
        $ui .= '<a href="javascript:void(0);" class="list-group-item itemsource itemsetting montserrat extra-items-'.$parent_e__id.'" onclick="$(\'.extra-items-'.$parent_e__id.'\').toggleClass(\'hidden\')"><span class="icon-block"><i class="fas fa-search-plus"></i></span>Show '.($count-$show_max).' more</a>';
    }

    $ui .= '</div>';

    return $ui;
}


function view_i_marks($i_x){

    //Validate core inputs:
    if(!isset($i_x['x__metadata']) || !isset($i_x['x__type'])){
        return false;
    }

    //prep metadata:
    $x__metadata = unserialize($i_x['x__metadata']);

    //Return mark:
    return ( $i_x['x__type'] == 4228 ? ( !isset($x__metadata['tr__assessment_points']) || $x__metadata['tr__assessment_points'] == 0 ? '' : '<span class="score-range">[<span style="'.( $x__metadata['tr__assessment_points']>0 ? 'font-weight:bold;' : ( $x__metadata['tr__assessment_points'] < 0 ? 'font-weight:bold;' : '' )).'">' . ( $x__metadata['tr__assessment_points'] > 0 ? '+' : '' ) . $x__metadata['tr__assessment_points'].'</span>]</span>' ) : '<span class="score-range">['.$x__metadata['tr__conditional_score_min'] . ( $x__metadata['tr__conditional_score_min']==$x__metadata['tr__conditional_score_max'] ? '' : '-'.$x__metadata['tr__conditional_score_max'] ).'%]</span>' );

}


function view_i_icon($i){

    $CI =& get_instance();

    if(!in_array($i['i__status'], $CI->config->item('n___7355'))){
        //Not Public, Show Drafting Icon:
        $config_var_id = 4737; //Idea Status
        $e__id = $i['i__status'];
    } else {
        $config_var_id = 7585; //Idea Type
        $e__id = $i['i__type'];
    }

    return '<span class="this_i__icon_'.$i['i__id'].'">'.view_cache($config_var_id, $e__id, true, 'right', $i['i__id']).'</span>';

}


function view_i($i, $i_x_id = 0, $is_parent = false, $e_of_i = false, $message_input = null, $extra_class = null, $control_enabled = true)
{

    $CI =& get_instance();
    $user_e = superpower_assigned();
    $e___6186 = $CI->config->item('e___6186');
    $e___4737 = $CI->config->item('e___4737'); //IDEA STATUS
    $e___7585 = $CI->config->item('e___7585');
    $e___4486 = $CI->config->item('e___4486');
    $e___12467 = $CI->config->item('e___12467');
    $e___13408 = $CI->config->item('e___13408');

    //DISCOVER
    $x__id = ( isset($i['x__id']) ? $i['x__id'] : 0 );
    $is_i_link = ($x__id && in_array($i['x__type'], $CI->config->item('n___4486')));

    //IDEA
    $i_stats = i_stats($i['i__metadata']);
    $is_public = in_array($i['i__status'], $CI->config->item('n___7355'));
    $e_of_i = ( !$is_i_link ? false : $e_of_i ); //Disable Edits on Idea List Page
    $show_toolbar = ($control_enabled && superpower_active(12673, true));

    //IDEA INFO BAR
    $box_items_list = '';

    //DISCOVER STATUS
    if($x__id && !in_array($i['x__status'], $CI->config->item('n___7359'))){
        $box_items_list .= '<span class="inline-block"><span data-toggle="tooltip" data-placement="right" title="'.$e___6186[$i['x__status']]['m_title'].' @'.$i['x__status'].'">' . $e___6186[$i['x__status']]['m_icon'] . '</span>&nbsp;</span>';
    }


    $ui = '<div x__id="' . $x__id . '" i-id="' . $i['i__id'] . '" class="list-group-item no-side-padding itemidea itemidealist i_sortable paddingup level2_in object_saved saved_i_'.$i['i__id'] . ' i_line_' . $i['i__id'] . ' i__tr_'.$x__id.' '.$extra_class.'" style="padding-left:0;">';





    //EDITING TOOLBAR
    if($is_i_link && $control_enabled && $e_of_i){

        //RIGHT EDITING:
        $ui .= '<div class="note-editor edit-off '.superpower_active(10939).'">';
        $ui .= '<span class="show-on-hover">';

        if(!$is_parent){
            $ui .= '<span title="SORT"><i class="fas fa-sort black"></i></span>';
        }

        //Remove:
        $ui .= '<span title="REMOVE"><a href="javascript:void(0);" onclick="i_remove('.$i['i__id'].', '.$i['x__id'].', '.( $is_parent ? 1 : 0 ).')"><i class="fas fa-times black"></i></a></span>';

        $ui .= '</span>';
        $ui .= '</div>';

    }






    $ui .= '<div class="row">';
    $ui .= '<div class="col-sm col-md">';

        //IDEA Transaction:
        $href = '/~'.$i['i__id'].( isset($_GET['focus__e']) ? '?focus__e='.intval($_GET['focus__e']) : '' );

        //IDEA STATUS:
        $ui .= '<a href="'.$href.'" title="Idea Weight: '.number_format($i['i__weight'], 0).'" class="icon-block">'.view_i_icon($i).'</a>';

        //IDEA TITLE
        if($is_i_link && superpower_active(13354, true)){

            $ui .= view_input_text(4736, $i['i__title'], $i['i__id'], $e_of_i, (($i['x__sort']*100)+1));

        } else {

            $ui .= '<a href="'.$href.'" class="title-block montserrat">';
            $ui .= $box_items_list;
            $ui .= view_i_title($i); //IDEA TITLE
            $ui .= '</a>';

        }
    $ui .= '</div>';
    $ui .= '<div class="col-sm-6 col-md-4 col2nd">';
        //MENCH COINS
        $ui .= '<div class="row">';
            $ui .= '<div class="col-4">'.view_coins_i(12274, $i).'</div>';
            $ui .= '<div class="col-4">'.view_coins_i(12273, $i).'</div>';
            $ui .= '<div class="col-4">'.view_coins_i(6255,  $i).'</div>';
        $ui .= '</div>';

    $ui .= '</div>';
    $ui .= '</div>';



    if($message_input && trim($message_input)!=$CI->uri->segment(1)){
        $ui .= '<div class="i-footer hideIfEmpty">' . $CI->X_model->message_send($message_input, $user_e) . '</div>';
    }


    if($show_toolbar){

        //Idea Toolbar
        $ui .= '<div class="space-content ' . superpower_active(12673) . '" style="padding-left:25px; padding-top:13px;">';

        $ui .= $box_items_list;

        //IDEA TYPE
        $ui .= '<div class="inline-block">'.view_input_dropdown(7585, $i['i__type'], null, $e_of_i, false, $i['i__id']).'</div>';

        //IDEA STATUS
        $ui .= '<div class="inline-block">' . view_input_dropdown(4737, $i['i__status'], null, $e_of_i, false, $i['i__id']) . ' </div>';




        if($x__id){

            $x__metadata = unserialize($i['x__metadata']);

            //IDEA LINK BAR
            $ui .= '<span class="' . superpower_active(12700) . '">';

            //LINK TYPE
            $ui .= view_input_dropdown(4486, $i['x__type'], null, $e_of_i, false, $i['i__id'], $i['x__id']);

            //LINK MARKS
            $ui .= '<span class="x_marks settings_4228 '.( $i['x__type']==4228 ? : 'hidden' ).'">';
            $ui .= view_input_text(4358, ( isset($x__metadata['tr__assessment_points']) ? $x__metadata['tr__assessment_points'] : '' ), $i['x__id'], $e_of_i, ($i['x__sort']*10)+2 );
            $ui .='</span>';


            //LINK CONDITIONAL RANGE
            $ui .= '<span class="x_marks settings_4229 '.( $i['x__type']==4229 ? : 'hidden' ).'">';
            //MIN
            $ui .= view_input_text(4735, ( isset($x__metadata['tr__conditional_score_min']) ? $x__metadata['tr__conditional_score_min'] : '' ), $i['x__id'], $e_of_i, ($i['x__sort']*10)+3);
            //MAX
            $ui .= view_input_text(4739, ( isset($x__metadata['tr__conditional_score_max']) ? $x__metadata['tr__conditional_score_max'] : '' ), $i['x__id'], $e_of_i, ($i['x__sort']*10)+4);
            $ui .= '</span>';
            $ui .= '</span>';

        }

        $ui .= '</div>';

    }

    $ui .= '</div>';



    return $ui;

}




function view_caret($e__id, $m, $object__id){
    //Display drop down menu:
    $CI =& get_instance();

    $superpower_actives = array_intersect($CI->config->item('n___10957'), $m['m_profile']);

    $ui = '<li class="nav-item dropdown '.( count($superpower_actives) ? superpower_active(end($superpower_actives)) : '' ).'" title="'.$m['m_title'].'">';
    $ui .= '<a class="nav-x dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"></a>';
    $ui .= '<div class="dropdown-menu">';
    foreach($CI->config->item('e___'.$e__id) as $e__id => $m2){
        $ui .= '<a class="dropdown-item montserrat '.extract_icon_color($m2['m_icon']).'" href="' . $m2['m_message'] . $object__id . '"><span class="icon-block">'.view_e__icon($m2['m_icon']).'</span> '.$m2['m_title'].'</a>';
    }
    $ui .= '</div>';
    $ui .= '</li>';

    return $ui;
}


function view_i_list($in_my_x, $i, $is_next, $user_e, $prefix_statement = null){

    //If no list just return the next step:
    if(!count($is_next)){
        return false;
    }

    //List children so they know what's ahead:
    $CI =& get_instance();
    $e___11035 = $CI->config->item('e___11035'); //MENCH NAVIGATION
    $e___12467 = $CI->config->item('e___12467'); //MENCH COINS
    $common_prefix = i_calc_common_prefix($is_next, 'i__title');
    $ui = '';


    if(strlen($prefix_statement)){
        $ui .= '<div class="pull-left headline"><span class="icon-block">'.$e___12467[12273]['m_icon'].'</span>'.$prefix_statement.'</div>';
    }

    $ui .= '<div class="doclear">&nbsp;</div>';
    $ui .= '<div class="pull-right grey right-adj"><a href="javascript:void(0);" onclick="$(\'.i_x_stats\').toggleClass(\'hidden\')" title="'.$e___11035[13689]['m_title'].'">'.$e___11035[13689]['m_icon'].'</a></div>';

    $ui .= '<div class="doclear">&nbsp;</div>';
    $ui .= '<div class="list-group">';
    $is_last_continious_complete = true;
    $index_id = 0;
    foreach($is_next as $key => $next_i){
        $completion_rate = $CI->X_model->completion_progress($user_e['e__id'], $next_i);
        $ui .= view_i_x($next_i, ( $in_my_x ? $index_id : 99 /* Locked */ ), $completion_rate['completion_percentage'] > 0, $common_prefix, false, $completion_rate);

        //Search for the first unlocked idea right after the first stack of continuously completed ideas
        $is_last_continious_complete = ( $is_last_continious_complete && $completion_rate['completion_percentage']>=100 ? true : false );
        $index_id = ( $is_last_continious_complete ? 0 : $index_id+1 );
    }
    $ui .= '</div>';
    $ui .= '<div class="doclear">&nbsp;</div>';

    return $ui;

}


function view_i_note_mix($x__type, $i_notes, $e_of_i){

    $CI =& get_instance();
    $e___4485 = $CI->config->item('e___4485'); //IDEA NOTES
    $e___11035 = $CI->config->item('e___11035');
    $handles_uploads = (in_array($x__type, $CI->config->item('n___12359')));
    $handles_url = (in_array($x__type, $CI->config->item('n___7551')) || in_array($x__type, $CI->config->item('n___4986')));
    $user_e = superpower_assigned();
    $ui = '';


    /*
    if(!count($i_notes) && $e_of_i){
        $ui .= '<div class="no_notes_' . $x__type .'" style="margin-bottom:13px;">';
        $ui .= '<div class="msg alert alert-warning" role="alert"><span class="icon-block">&nbsp;</span>No '.ucwords(strtolower($e___4485[$x__type]['m_title'])).'. Be the first to post one</div>';
        $ui .= '</div>';
    }
    */

    //Show no-Message notifications for each message type:
    $ui .= '<div id="i_notes_list_'.$x__type.'" class="list-group">';

    //List current notes:
    foreach($i_notes as $i_notes) {
        $ui .= view_13574($i_notes, ($i_notes['x__source']==$user_e['e__id']));
    }

    //ADD NEW:
    if(!in_array($x__type, $CI->config->item('n___12677')) && $e_of_i){
        $ui .= '<div class="list-group-item itemidea add_notes_' . $x__type .'">';
        $ui .= '<div class="add_notes_form">';
        $ui .= '<form class="box box' . $x__type . '" method="post" enctype="multipart/form-data" class="'.superpower_active(10939).'">';



        $ui .= '<textarea onkeyup="i_note_count_new('.$x__type.')" class="form-control msg note-textarea algolia_search new-note input_note_'.$x__type.'" note_type_id="' . $x__type . '" id="x__message' . $x__type . '" placeholder="WRITE'.( $handles_url ? ', PASTE URL' : '' ).( $handles_uploads ? ', DROP FILE' : '' ).'" style="margin-top:6px;"></textarea>';


        //Response result:
        $ui .= '<div class="note_error_'.$x__type.' hideIfEmpty discover msg alert alert-danger" style="margin:8px 0;"></div>';


        $ui .= '<table class="table table-condensed" style="margin-bottom: 16px;"><tr>';

        //Save button:
        $ui .= '<td style="width:85px; padding: 10px 0 0 0;"><a href="javascript:i_note_text('.$x__type.');" class="btn btn-'.trim(extract_icon_color($e___4485[$x__type]['m_icon'])).' save_notes_'.$x__type.'">'.$e___4485[$x__type]['m_icon'].'</a></td>';


        //File counter:
        $ui .= '<td style="padding: 10px 0 0 0; font-size: 0.85em;"><span id="ideaNoteNewCount' . $x__type . '" class="hidden"><span id="charNum' . $x__type . '">0</span>/' . config_var(4485).'</span></td>';


        //Upload File:
        if($handles_uploads){
            $ui .= '<td style="width:42px; padding:42px 23px 0 0;">';
            $ui .= '<input class="inputfile hidden" type="file" name="file" id="fileIdeaType'.$x__type.'" />';
            $ui .= '<label class="file_label_'.$x__type.'" for="fileIdeaType'.$x__type.'" data-toggle="tooltip" title="'.$e___11035[13572]['m_message'].'" data-placement="top"><span class="icon-block">'.$e___11035[13572]['m_icon'].'</span></label>';
            $ui .= '</td>';
        }


        $ui .= '</tr></table>';



        $ui .= '</form>';
        $ui .= '</div>';
        $ui .= '</div>';
    }


    $ui .= '</div>';

    return $ui;

}

function view_12687($e__id){
    $CI =& get_instance();
    $e___12687 = $CI->config->item('e___12687');
    if(!substr_count($e___12687[$e__id]['m_message'], " | ")){
        //Single message:
        return $e___12687[$e__id]['m_message'];
    } else {
        //Random message:
        $line_messages = explode(" | ", $e___12687[$e__id]['m_message']);
        return $line_messages[rand(0, (count($line_messages) - 1))];
    }
}

function view_unauthorized_message($superpower_e__id = 0){

    $user_e = superpower_assigned($superpower_e__id);

    if(!$user_e){
        if(!$superpower_e__id){

            //Missing Session
            return 'You must login to continue.';

        } else {

            //Missing Superpower:
            $CI =& get_instance();
            $e___10957 = $CI->config->item('e___10957');
            return 'You are missing the required superpower of '.$e___10957[$superpower_e__id]['m_title'];

        }
    }


    return null;

}

function view_time_hours($total_seconds, $hide_hour = false){

    $total_seconds = intval($total_seconds);
    //Turns seconds into HH:MM:SS
    $hours = floor($total_seconds/3600);
    $minutes = floor(fmod($total_seconds, 3600)/60);
    $seconds = fmod($total_seconds, 60);

    return ( $hide_hour && !$hours ? '' : str_pad($hours, 2, "0", STR_PAD_LEFT).':' ).str_pad($minutes, 2, "0", STR_PAD_LEFT).':'.str_pad($seconds, 2, "0", STR_PAD_LEFT);
}

function view__focus__e($e){
    $CI =& get_instance();
    $e___11035 = $CI->config->item('e___11035');
    return '<div class="msg alert alert-info no-margin" style="margin-bottom: 10px !important;" title="'.$e___11035[13670]['m_title'].'"><span class="icon-block">'.$e___11035[13670]['m_icon'].'</span>' . view_e__icon($e['e__icon']) . '&nbsp;<a href="/@'.$e['e__id'].'" class="'.extract_icon_color($e['e__icon']).'">' . $e['e__title'].'</a>&nbsp;&nbsp;&nbsp;<a href="/'.$CI->uri->segment(1).'" title="'.$e___11035[13671]['m_title'].'">'.$e___11035[13671]['m_icon'].'</a></div>';
}

function view_i_cover($x__type, $i, $show_editor, $extra_class = null, $message_input = null, $user_e = false){

    //Search to see if an idea has a thumbnail:
    $CI =& get_instance();
    $e___13369 = $CI->config->item('e___13369'); //IDEA COVER UI
    $user_input = $user_e;
    $user_session = superpower_assigned();

    if(!$user_e){
        $user_e = $user_session;
    }

    $i_stats = i_stats($i['i__metadata']);
    $href = ( $x__type == 6255 ? '/'.$i['i__id'] . ( $user_input && $user_e['e__id']!=$user_session['e__id'] ? '?focus__e='.$user_e['e__id'] : '' ) : '/i/i_go/'.$i['i__id'] . ( isset($_GET['focus__e']) ? '?focus__e='.intval($_GET['focus__e']) : '' ));
    $completion_rate['completion_percentage'] = 0; //Assume no progress

    $ui  = '<div '.( isset($i['x__id']) ? ' x__id="'.$i['x__id'].'" ' : '' ).' class="i_class_'.$x__type.'_'.$i['i__id'].' no-padding big-cover '.( $show_editor ? ' home_sort ' : '' ).' '.$extra_class.'">';

        //EDITING TOOLBAR
        if($show_editor){

            //RIGHT EDITING:
            $ui .= '<div class="note-editor cover-editor edit-off '.( $x__type==6255 ? '' : superpower_active(10939) ).'">';
            $ui .= '<span class="show-on-hover">';

            //SORT
            $ui .= '<span title="'.$e___13369[13413]['m_title'].'" class="x_sort">'.$e___13369[13413]['m_icon'].'</span>';

            //Remove:
            $ui .= '<span title="'.$e___13369[13414]['m_title'].'" class="x_remove" i__id="'.$i['i__id'].'" x__type="'.$x__type.'">'.$e___13369[13414]['m_icon'].' </span>';

            $ui .= '</span>';
            $ui .= '</div>';

        }


        $ui .= '<div class="row">';
            $ui .= '<div class="col-9 feature-content">';

                //Title
                $ui .= '<div>';
                $ui .= '<span class="icon-block icon-title"><a href="'.$href.'">'.view_icon_i_x($completion_rate['completion_percentage'], $i, ( $x__type == 6255 ? 13822 /* Next Idea */ : 0 /* Idea Icon */ )).'</a></span>';
                $ui .= '<h2 class="inline-block cover-title"><a href="'.$href.'">'.view_i_title($i).'</a></h2>';
                $ui .= '</div>';


                if(strlen(strip_tags($message_input))){
                    //Description, if any
                    $ui .= '<div class="inline-block space-left">'.$message_input.'</div>';
                } else {
                    //Description, if any
                    $ui .= '<div class="inline-block space-left hideIfEmpty">'.i_fetch_description($i['i__id']).'</div>';
                }


                $ui .= '<div class="space-left hideIfEmpty">'.view_i_tree_stats($i_stats, false).'</div>';


                if($user_e && $x__type==6255){
                    $completion_rate = $CI->X_model->completion_progress($user_e['e__id'], $i);
                    if($completion_rate['completion_percentage']>0){
                        $ui .= '<div class="space-all">'.view_progress($completion_rate, $i).'</div>';
                    }
                }


            $ui .= '</div>';
            $ui .= '<div class="col-3"><a href="'.$href.'">'.i_fetch_cover($i['i__id'], true).'</a></div>';
        $ui .= '</div>';
    $ui .= '</div>';

    return $ui;

}

function view_progress($completion_rate, $i, $extra_class = null){
    return '<div class="progress-bg-list '.$extra_class.'"><div class="progress-done" style="width:'.$completion_rate['completion_percentage'].'%"></div><div class="progress-rate">&nbsp;'.$completion_rate['completion_percentage'].'% COMPLETED<span class="show-max"> '.$completion_rate['steps_completed'].'/'.$completion_rate['steps_total'].' IDEAS</span></div></div>';
}

function view_e_basic($e)
{
    $ui = '<div class="list-group-item no-side-padding">';
    $ui .= '<span class="icon-block">' . view_e__icon($e['e__icon']) . '</span>';
    if(superpower_active(10939, true)){
        $ui .= '<a class="title-block title-no-right montserrat '.extract_icon_color($e['e__icon']).'" href="/@'.$e['e__id'].'">'.$e['e__title'].'</a>';
    } else {
        $ui .= '<b class="title-block title-no-right">'.$e['e__title'].'</b>';
    }
    if(isset($e['x__message']) && strlen($e['x__message']) > 0){
        $ui .= '<div class="space-content" style="padding-top:5px;">'.$e['x__message'].'</div>';
    }
    $ui .= '</div>';
    return $ui;
}




function view_e($e, $is_parent = false, $extra_class = null, $control_enabled = false, $source_of_e = false, $common_prefix = null)
{

    $CI =& get_instance();
    $user_e = superpower_assigned();
    $e___6177 = $CI->config->item('e___6177'); //Source Status
    $e___4592 = $CI->config->item('e___4592');
    $e___6186 = $CI->config->item('e___6186'); //Transaction Status
    $e___11035 = $CI->config->item('e___11035'); //MENCH NAVIGATION

    $focus_e__id = ( substr($CI->uri->segment(1), 0, 1)=='@' ? intval(substr($CI->uri->segment(1), 1)) : 0 );
    $x__id = (isset($e['x__id']) ? $e['x__id'] : 0);
    $is_e_link = ( $x__id > 0 && in_array($e['x__type'], $CI->config->item('n___4592')));
    $is_x_progress = ( $x__id > 0 && in_array($e['x__type'], $CI->config->item('n___12227')));
    $inline_editing = $control_enabled && superpower_active(13402, true);
    $superpower_10939 = superpower_active(10939, true);
    $superpower_12706 = superpower_active(12706, true);
    $superpower_13422 = superpower_active(13422, true);

    $source_of_e = ( $superpower_13422 ? true : $source_of_e );

    $e__profiles = $CI->X_model->fetch(array(
        'x__type IN (' . join(',', $CI->config->item('n___4592')) . ')' => null, //SOURCE LINKS
        'x__up !=' => $focus_e__id, //Do Not Fetch Current Source
        'x__down' => $e['e__id'], //This child source
        'x__status IN (' . join(',', $CI->config->item('n___7360')) . ')' => null, //ACTIVE
        'e__status IN (' . join(',', $CI->config->item('n___7358')) . ')' => null, //ACTIVE
    ), array('x__up'), 0, 0, array('e__weight' => 'DESC'));

    $is_public = in_array($e['e__status'], $CI->config->item('n___7357'));
    $is_x_published = ( !$x__id || in_array($e['x__status'], $CI->config->item('n___7359')));
    //Allow source to see all their own transactions:
    $is_hidden = (!$user_e || $user_e['e__id']!=$focus_e__id) && (filter_array($e__profiles, 'e__id', '4755') || in_array($e['e__id'], $CI->config->item('n___4755')));
    $e_url = '/@'.$e['e__id'];


    if(!$user_e && (!$is_public || !$is_x_published)){
        //Not logged in, so should only see published:
        return false;
    } elseif($is_hidden && !superpower_assigned(12701)){
        //Cannot see this private discover:
        return false;
    } elseif($is_hidden && !superpower_active(12701, true)){
        //They don't have the needed superpower:
        return false;
    }


    //SOURCE INFO BAR
    $box_items_list = '';

    //SOURCE STATUS
    if(!$is_public){
        $box_items_list .= '<span class="inline-block e__status_' . $e['e__id'].'"><span data-toggle="tooltip" data-placement="right" title="'.$e___6177[$e['e__status']]['m_title'].' @'.$e['e__status'].'">' . $e___6177[$e['e__status']]['m_icon'] . '</span>&nbsp;</span>';
    }

    //DISCOVER STATUS
    if($x__id){
        if(!$is_x_published){
            $box_items_list .= '<span class="inline-block x__status_' . $x__id .'"><span data-toggle="tooltip" data-placement="right" title="'.$e___6186[$e['x__status']]['m_title'].' @'.$e['x__status'].'">' . $e___6186[$e['x__status']]['m_icon'] . '</span>&nbsp;</span>';
        }
    }


    //ROW
    $ui = '<div class="list-group-item no-side-padding itemsource en-item object_saved saved_e_'.$e['e__id'].' e__id_' . $e['e__id'] . ( $x__id > 0 ? ' tr_' . $e['x__id'].' ' : '' ) . ' '. $extra_class  . '" e__id="' . $e['e__id'] . '" x__id="'.$x__id.'">';



    if($control_enabled && $source_of_e && $is_e_link){

        //RIGHT EDITING:
        $ui .= '<div class="note-editor edit-off">';
        $ui .= '<span class="show-on-hover">';

        if($superpower_13422){

            //Sort
            if(!$is_parent && $superpower_10939){
                $ui .= '<span title="SORT"><i class="fas fa-sort source hidden"></i></span>';
            }

            //Edit Raw Source
            $ui .= '<span><a href="javascript:void(0);" onclick="load_13571(' . $e['e__id'] . ',' . $x__id . ')" title="'.$e___11035[13571]['m_title'].'">'.$e___11035[13571]['m_icon'].'</a></span>';

        } elseif($superpower_10939 && !$is_parent && $source_of_e){

            //UNLINK SOURCE
            $ui .= '<span><a href="javascript:void(0);" onclick="remove_10673(' . $x__id . ', '.$e['x__type'].')" title="'.$e___11035[10673]['m_title'].'">'.$e___11035[10673]['m_icon'].'</a></span>';

        }

        $ui .= '</span>';
        $ui .= '</div>';

    }




    $ui .= '<div class="row">';


        $ui .= '<div class="col-sm col-md">';

            //SOURCE ICON
            $ui .= '<a href="'.$e_url.'" '.( $is_e_link ? ' title="TRANSACTION ID '.$e['x__id'].' TYPE @'.$e['x__type'].' SORT '.$e['x__sort'].' WEIGHT '.$e['e__weight'].'" ' : '' ).'><span class="icon-block e_ui_icon_' . $e['e__id'] . ' e__icon_'.$e['e__id'].'">' . view_e__icon($e['e__icon']) . '</span></a>';


            //SOURCE TITLE TEXT EDITOR
            if($inline_editing){

                $ui .= view_input_text(6197, $e['e__title'], $e['e__id'], $source_of_e, 0, false, null, extract_icon_color($e['e__icon']));

                if($superpower_12706){
                    $ui .= '<div class="space-content">'.$box_items_list.'</div>';
                }

            } else {

                //SOURCE NAME
                $ui .= '<a href="'.$e_url.'" class="title-block title-no-right montserrat '.extract_icon_color($e['e__icon']).'">';
                $ui .= $box_items_list;
                $ui .= '<span class="text__6197_' . $e['e__id'] . '">'.( $common_prefix ? str_replace($common_prefix, '', $e['e__title']) : $e['e__title'] ).'</span>';
                $ui .= '</a>';

            }
        $ui .= '</div>';

        $ui .= '<div class="col-sm-6 col-md-4 col2nd">';

            //MENCH COINS
            $ui .= '<div class="row">';
                $ui .= '<div class="col-4">'.view_coins_e(12274, $e['e__id']).'</div>';
                $ui .= '<div class="col-4">'.view_coins_e(12273, $e['e__id']).'</div>';
                $ui .= '<div class="col-4">'.view_coins_e(6255, $e['e__id']).'</div>';
            $ui .= '</div>';

        $ui .= '</div>';



    $ui .= '</div>';






    if($superpower_12706){
        //PROFILE
        $ui .= '<div class="space-content hideIfEmpty">';
        //PROFILE SOURCES:
        $ui .= '<span class="paddingup inline-block hideIfEmpty">';
        foreach($e__profiles as $e_profile) {
            $ui .= '<span class="icon-block-img e_child_icon_' . $e_profile['e__id'] . '"><a href="/@' . $e_profile['e__id'] . '" data-toggle="tooltip" title="' . $e_profile['e__title'] . (strlen($e_profile['x__message']) > 0 ? ' = ' . $e_profile['x__message'] : '') . '" data-placement="bottom">' . view_e__icon($e_profile['e__icon']) . '</a></span> ';
        }
        $ui .= '</span>';
        $ui .= '</div>';
    }



    //DISCOVERY TOOLBAR
    if($is_x_progress && superpower_active(13758, true)){
        $ui .= '<div class="message_content paddingup x__message block">';

        //Show Filter?
        if(superpower_active(12701, true) && (!isset($_GET['focus__e']) || $_GET['focus__e']!=$e['e__id'])){
            $ui .= '<a href="/'.$CI->uri->segment(1).'?focus__e='.$e['e__id'].'" class="icon-block-xs" title="'.$e___11035[13670]['m_title'].'">'.$e___11035[13670]['m_icon'].'</a>';
        }

        //Total Progress
        if(isset($_GET['progress'])){
            $is = $CI->I_model->fetch(array(
                'i__id' => $e['x__left'],
            ));
            $completion_rate = $CI->X_model->completion_progress($e['x__source'], $is[0]);
            $ui .= '<span style="min-width:34px;" class="inline-block" title="'.$e['x__source'].'/'.$e['x__left'].'">' . $completion_rate['completion_percentage'] . '%</span>';
        }

        //Method & Time:
        $ui .= '<span style="min-width:147px;" title="'.$e['x__time'].'" class="inline-block"><span class="icon-block-xs">'.view_cache(12227, $e['x__type']).'</span>' . view_time_difference(strtotime($e['x__time'])) . ' Ago</span>';

        $ui .= '</div>';
    }


    //MESSAGE
    if ($x__id > 0) {
        if($is_e_link){

            $ui .= '<span class="message_content paddingup x__message hideIfEmpty x__message_' . $x__id . '">' . view_x__message($e['x__message'] , $e['x__type']) . '</span>';

        } elseif($is_x_progress && strlen($e['x__message'])){

            //DISCOVER PROGRESS
            $ui .= '<div class="message_content paddingup" style="margin-left: 0;">';
            $ui .= $CI->X_model->message_send($e['x__message']);
            $ui .= '</div>';

        }
    }





    $ui .= '</div>';

    return $ui;

}


function view_input_text($cache_e__id, $current_value, $object__id, $e_of_i, $tabindex = 0, $extra_large = false, $e__icon = null, $append_css = null){

    $CI =& get_instance();
    $e___12112 = $CI->config->item('e___12112');
    $current_value = htmlentities($current_value);

    //Define element attributes:
    $attributes = ( $e_of_i ? '' : 'disabled' ).' spellcheck="false" tabindex="'.$tabindex.'" old-value="'.$current_value.'" id="input_'.$cache_e__id.'_'.$object__id.'" class="form-control dotransparent montserrat inline-block x_set_text text__'.$cache_e__id.'_'.$object__id.' texttype_'.($extra_large?'_lg':'_sm').' text_e_'.$cache_e__id.' '.$append_css.'" cache_e__id="'.$cache_e__id.'" object__id="'.$object__id.'" ';

    //Also Append Counter to the end?
    if($extra_large){

        $focus_element = '<textarea '.( !strlen($append_css) ? ' style="color:#000000 !important;" ' : '' ).' onkeyup="view_input_text_count('.$cache_e__id.','.$object__id.')" placeholder="'.$e___12112[$cache_e__id]['m_title'].'" '.$attributes.'>'.$current_value.'</textarea>';
        $character_counter = '<div class="title_counter title_counter_'.$cache_e__id.'_'.$object__id.' hidden grey montserrat doupper" style="text-align: right;"><span id="current_count_'.$cache_e__id.'_'.$object__id.'">0</span>/'.config_var($cache_e__id).' CHARACTERS</div>';
        $icon = '<span class="icon-block title-icon">'.$e__icon.'</span>';

    } else {

        $focus_element = '<input type="text" placeholder="__" value="'.$current_value.'" '.$attributes.' />';
        $character_counter = ''; //None
        $icon = $e__icon;

    }

    return '<span class="span__'.$cache_e__id.' '.( !$e_of_i ? 'edit-locked' : '' ).'">'.$icon.$focus_element.'</span>'.$character_counter;
}




function view_input_dropdown($cache_e__id, $selected_e__id, $btn_class, $e_of_i = true, $show_full_name = true, $i__id = 0, $x__id = 0){

    $CI =& get_instance();
    $e___this = $CI->config->item('e___'.$cache_e__id);

    if(!$selected_e__id || !isset($e___this[$selected_e__id])){
        return false;
    }

    $e___12079 = $CI->config->item('e___12079');
    $e___4527 = $CI->config->item('e___4527');

    //data-toggle="tooltip" data-placement="top" title="'.$e___4527[$cache_e__id]['m_title'].'"
    $ui = '<div title="'.$e___12079[$cache_e__id]['m_title'].'" data-toggle="tooltip" data-placement="top" class="inline-block">';
    $ui .= '<div class="dropdown inline-block dropd_'.$cache_e__id.'_'.$i__id.'_'.$x__id.' '.( !$show_full_name ? ' icon-block ' : '' ).'" selected-val="'.$selected_e__id.'">';

    $ui .= '<button type="button" '.( $e_of_i ? 'class="btn no-left-padding '.( $show_full_name ? 'dropdown-toggle' : 'no-right-padding dropdown-lock' ).' '.$btn_class.'" id="dropdownMenuButton'.$cache_e__id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' : 'class="btn '.( !$show_full_name ? 'no-padding' : '' ).' edit-locked  '.$btn_class.'"' ).' >';

    $ui .= '<span class="icon-block">' .$e___this[$selected_e__id]['m_icon'].'</span><span class="show-max">'.( $show_full_name ?  $e___this[$selected_e__id]['m_title'] : '' ).'</span>';

    $ui .= '</button>';

    $ui .= '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton'.$cache_e__id.'">';

    foreach($e___this as $e__id => $m) {

        $superpower_actives = array_intersect($CI->config->item('n___10957'), $m['m_profile']);
        $is_url_desc = ( substr($m['m_message'], 0, 1)=='/' );

        //What type of URL?
        if($is_url_desc){

            //Basic transaction:
            $anchor_url = ( $e__id==$selected_e__id ? 'href="javascript:void();"' : 'href="'.$m['m_message'].'"' );

        } else{

            //Idea Dropdown updater:
            $anchor_url = 'href="javascript:void();" new-en-id="'.$e__id.'" onclick="i_set_dropdown('.$cache_e__id.', '.$e__id.', '.$i__id.', '.$x__id.', '.intval($show_full_name).')"';

        }

        $ui .= '<a class="dropdown-item dropi_'.$cache_e__id.'_'.$i__id.'_'.$x__id.' montserrat optiond_'.$e__id.'_'.$i__id.'_'.$x__id.' doupper '.( $e__id==$selected_e__id ? ' active ' : ( count($superpower_actives) ? superpower_active(end($superpower_actives)) : '' ) ).'" '.$anchor_url.'><span class="icon-block">'.$m['m_icon'].'</span>'.$m['m_title'].'</a>'; //Used to show desc but caused JS click conflict sp retired for now: ( strlen($m['m_message']) && !$is_url_desc ? 'title="'.$m['m_message'].'" data-toggle="tooltip" data-placement="right"' : '' )

    }

    $ui .= '</div>';
    $ui .= '</div>';
    $ui .= '</div>';

    return $ui;
}

function view_json($array)
{
    header('Content-Type: application/json');
    echo json_encode($array);
    return true;
}


function view_ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if (($number % 100) >= 11 && ($number % 100) <= 13) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }
}

function view__s($count, $is_e = 0)
{
    //A cute little function to either display the plural "s" or not based on $count
    return ( intval($count) == 1 ? '' : ($is_e ? 'es' : 's'));
}

