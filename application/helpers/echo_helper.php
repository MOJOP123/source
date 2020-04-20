<?php

function echo_en_load_more($page, $limit, $en__child_count)
{
    /*
     * Gives an option to "Load More" sources when we have too many to show in one go
     * */

    $ui = '<div class="load-more montserrat list-group-item itemsource no-left-padding" style="padding-bottom:20px;"><a href="javascript:void(0);" onclick="en_load_next_page(' . $page . ', 0)">';

    //Regular section:
    $max_sources = (($page + 1) * $limit);
    $max_sources = ($max_sources > $en__child_count ? $en__child_count : $max_sources);
    $ui .= '<span class="icon-block"><i class="far fa-search-plus"></i></span>LOAD MORE';
    $ui .= '</a></div>';

    return $ui;
}

function echo_db_field($field_name){

    //Takes a database field name and returns a human-friendly version

    $prefix = substr($field_name, 0, 3);

    if($prefix == 'ln_'){
        $name = 'Transaction';
    } elseif($prefix == 'in_'){
        $name = 'Idea';
    } elseif($prefix == 'en_'){
        $name = 'Source';
    } else {
        return false;
    }

    return ucwords(
        str_replace('_', ' ',
            str_replace('_id', '',
                str_replace('_source_id', '',
                    str_replace('_idea_id', '',
                        //Start here:
                        str_replace($prefix, $name.' ', $field_name)
                    )
                )
            )
        )
    );

}



function echo_time_minutes($sec_int)
{
    //Turns seconds into a nice format with minutes, like "1m 23s"
    $sec_int = intval($sec_int);
    $min = 0;
    $sec = fmod($sec_int, 60);
    if ($sec_int >= 60) {
        $min = floor($sec_int / 60);
    }

    return ( $min ? $min . ' Min.' : ( $sec ? $sec . ' Sec.' : false ) );
}

function echo_url_types($url, $en_type_link_id)
{

    /*
     *
     * Displays Player Links that are a URL based on their
     * $en_type_link_id as listed under Player URL Links:
     * https://mench.com/source/4537
     *
     * */


    if ($en_type_link_id == 4256 /* Generic URL */) {

        return '<a href="' . $url . '"><span class="url_truncate">' . echo_url_clean($url) . '</span></a>';

    } elseif ($en_type_link_id == 4257 /* Embed Widget URL? */) {

        return echo_url_embed($url);

    } elseif ($en_type_link_id == 4260 /* Image URL */) {

        $current_mench = current_mench();
        if($current_mench['x_name']=='source'){
            return '<a href="' . $url . '"><img src="' . $url . '" class="content-image" /></a>';
        } else {
            return '<img src="' . $url . '" class="content-image" />';
        }

    } elseif ($en_type_link_id == 4259 /* Audio URL */) {

        return  '<audio controls><source src="' . $url . '" type="audio/mpeg"></audio>' ;

    } elseif ($en_type_link_id == 4258 /* Video URL */) {

        return  '<video width="100%" onclick="this.play()" controls poster="https://s3foundation.s3-us-west-2.amazonaws.com/9988e7bc95f25002b40c2a376cc94806.png"><source src="' . $url . '" type="video/mp4"></video>' ;

    } elseif ($en_type_link_id == 4261 /* File URL */) {

        return '<a href="' . $url . '" class="btn btn-idea" target="_blank"><i class="fas fa-cloud-download"></i> Download File</a>';

    } else {

        //Unknown, return null:
        return false;

    }
}




function echo_url_embed($url, $full_message = null, $return_array = false)
{


    /*
     *
     * Detects and displays URLs from supported website with an embed widget
     *
     * Alert: Changes to this function requires us to re-calculate all current
     *       values for ln_type_source_id as this could change the equation for those
     *       link types. Change with care...
     *
     * */



    $clean_url = null;
    $embed_html_code = null;
    $prefix_message = null;
    $CI =& get_instance();

    if (!$full_message) {
        $full_message = $url;
    }

    if(is_https_url($url)){
        //See if $url has a valid embed video in it, and transform it if it does:
        $is_embed = (substr_count($url, 'youtube.com/embed/') == 1);

        if ((substr_count($url, 'youtube.com/watch') == 1) || substr_count($url, 'youtu.be/') == 1 || $is_embed) {

            $start_sec = 0;
            $end_sec = 0;
            $video_id = extract_youtube_id($url);

            if ($video_id) {

                if($is_embed){
                    if(is_numeric(one_two_explode('start=','&',$url))){
                        $start_sec = intval(one_two_explode('start=','&',$url));
                    }
                    if(is_numeric(one_two_explode('end=','&',$url))){
                        $end_sec = intval(one_two_explode('end=','&',$url));
                    }
                }

                //Set the Clean URL:
                $clean_url = 'https://www.youtube.com/watch?v=' . $video_id;

                //Inform User that this is a sliced video
                if ($start_sec || $end_sec) {
                    $embed_html_code .= '<div class="discover-topic">' . ( $end_sec ? '<b title="FROM SECOND '.$start_sec.' to '.$end_sec.'">WATCH ' . echo_time_minutes(($end_sec - $start_sec)) . ' CLIP</b>' : '<b>WATCH FROM ' . ($start_sec ? echo_time_minutes($start_sec) : 'START') . '</b> TO <b>' . ($end_sec ? echo_time_minutes($end_sec) : 'END') . '</b>') . ':</div>';
                }

                $embed_html_code .= '<div class="yt-container video-sorting" style="margin-top:5px;"><iframe src="//www.youtube.com/embed/' . $video_id . '?theme=light&color=white&keyboard=1&autohide=2&modestbranding=1&showinfo=0&rel=0&iv_load_policy=3&start=' . $start_sec . ($end_sec ? '&end=' . $end_sec : '') . '" frameborder="0" allowfullscreen class="yt-video"></iframe></div>';

            }

        } elseif (substr_count($url, 'vimeo.com/') == 1 && is_numeric(one_two_explode('vimeo.com/','?',$url))) {

            //Seems to be Vimeo:
            $video_id = trim(one_two_explode('vimeo.com/', '?', $url));

            //This should be an integer!
            if (intval($video_id) == $video_id) {
                $clean_url = 'https://vimeo.com/' . $video_id;
                $embed_html_code = '<div class="yt-container video-sorting" style="margin-top:5px;"><iframe src="https://player.vimeo.com/video/' . $video_id . '?title=0&byline=0" class="yt-video" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
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
            'status' => ($embed_html_code ? 1 : 0),
            'embed_code' => $embed_html_code,
            'clean_url' => $clean_url,
        );

    } else {
        //Just return the embed code:
        if ($embed_html_code) {
            return trim(str_replace($url, $embed_html_code, $full_message));
        } else {
            //Not matched with an embed rule:
            return false;
        }
    }
}

function echo_in_title($in, $push_message = false, $common_prefix = null){

    if(strlen($common_prefix) > 0){
        $in['in_title'] = trim(substr($in['in_title'], strlen($common_prefix)));
    }

    if($push_message){

        return $in['in_title'];

    } else {

        return '<span class="text__4736_'.$in['in_id'].'">'.htmlentities(trim($in['in_title'])).'</span>';

    }

}


function echo_in_notes($ln)
{

    /*
     *
     * A wrapper function that helps manage messages
     * by giving the message additional platform functions
     * such as editing and changing message type.
     *
     * */

    $CI =& get_instance();
    $session_en = superpower_assigned();
    $en_all_4485 = $CI->config->item('en_all_4485'); //Idea Notes


    //Transaction Status
    $en_all_6186 = $CI->config->item('en_all_6186');


    //Build the HTML UI:
    $ui = '';
    $ui .= '<div class="list-group-item itemidea is-msg pads_sortable msg_en_type_' . $ln['ln_type_source_id'] . '" id="ul-nav-' . $ln['ln_id'] . '" tr-id="' . $ln['ln_id'] . '">';
    $ui .= '<div style="overflow:visible !important;">';

    //Type & Delivery Method:
    $ui .= '<div class="text_message edit-off" id="msgbody_' . $ln['ln_id'] . '">';
    $ui .= $CI->DISCOVER_model->dispatch_message($ln['ln_content'], $session_en, false, array(), $ln['ln_next_idea_id']);
    $ui .= '</div>';

    //Editing menu:
    $ui .= '<div class="pads-editor edit-off '.superpower_active(10939).'"><span class="show-on-hover">';

        //Modify:
        $ui .= '<span title="MODIFY" data-toggle="tooltip" data-placement="left"><a href="javascript:in_notes_modify_start(' . $ln['ln_id'] . ');"><i class="fas fa-pen-square"></i></a></span>';

        //Sort:
        if(in_array(4603, $en_all_4485[$ln['ln_type_source_id']]['m_parents'])){
            $ui .= '<span title="SORT" data-toggle="tooltip" data-placement="left"><i class="fas fa-bars '.( in_array(4603, $en_all_4485[$ln['ln_type_source_id']]['m_parents']) ? 'in_notes_sorting' : '' ).'"></i></span>';
        }

    $ui .= '</span></div>';


    //Text editing:
    $ui .= '<textarea onkeyup="in_edit_notes_count(' . $ln['ln_id'] . ')" name="ln_content" id="message_body_' . $ln['ln_id'] . '" class="edit-on hidden msg pads-textarea algolia_search" placeholder="'.stripslashes($ln['ln_content']).'">' . $ln['ln_content'] . '</textarea>';


    //Editing menu:
    $ui .= '<ul class="msg-nav '.superpower_active(10939).'">';

    //Counter:
    $ui .= '<li class="edit-on hidden"><span id="ideaPadsCount' . $ln['ln_id'] . '"><span id="charEditingNum' . $ln['ln_id'] . '">0</span>/' . config_var(11073) . '</span></li>';

    //Save Edit:
    $ui .= '<li class="pull-right edit-on hidden"><a class="btn btn-idea white-third" href="javascript:in_notes_modify_save(' . $ln['ln_id'] . ',' . $ln['ln_type_source_id'] . ');" title="Save changes" data-toggle="tooltip" data-placement="top"><i class="fas fa-check"></i> Save</a></li>';

    //Cancel Edit:
    $ui .= '<li class="pull-right edit-on hidden"><a class="btn btn-idea white-third" href="javascript:in_notes_modify_cancel(' . $ln['ln_id'] . ');" title="Cancel editing" data-toggle="tooltip" data-placement="top"><i class="fas fa-times"></i></a></li>';

    //Show drop down for message link status:
    $ui .= '<li class="pull-right edit-on hidden"><span class="white-wrapper" style="margin:-5px 0 0 0; display: block;">';
    $ui .= '<select id="message_status_' . $ln['ln_id'] . '"  class="form-control border" style="margin-bottom:0;" title="Change message status" data-toggle="tooltip" data-placement="top">';
    foreach($CI->config->item('en_all_12012') as $en_id => $m){
        $ui .= '<option value="' . $en_id . '" '.( $en_id==$ln['ln_status_source_id'] ? 'selected="selected"' : '' ).'>' . $m['m_name'] . '</option>';
    }
    $ui .= '</select>';
    $ui .= '</span></li>';

    //Update result:
    $ui .= '<li class="pull-right edit-updates"></li>'; //Show potential errors



    $ui .= '</ul>';

    $ui .= '</div>';
    $ui .= '</div>';

    return $ui;
}


function echo_en_icon($en_icon = null)
{
    //A simple function to display the Player Icon OR the default icon if not available:
    if (strlen($en_icon) > 0) {

        return $en_icon;

    } else {
        //Return default icon for sources:
        $CI =& get_instance();
        $en_all_2738 = $CI->config->item('en_all_2738'); //MENCH
        return $en_all_2738[4536]['m_icon'];
    }
}

function echo_url($text)
{
    //Find and makes links within $text clickable
    return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1"><u>$1</u></a>', $text);
}


function echo_number($number, $micro = true, $push_message = false)
{

    //Displays number with a nice format

    //Let's see if we need to apply special formatting:
    $formatting = null;

    if ($number > 0 && $number < 1) {

        $original_format = $number; //Keep as is

        //Format Decimal number:
        if ($number < 0.000001) {
            $formatting = array(
                'multiplier' => 1000000000,
                'decimals' => 0,
                'micro_1' => 'n',
                'micro_0' => ' Nano',
            );
        } elseif ($number < 0.001) {
            $formatting = array(
                'multiplier' => 1000000,
                'decimals' => 0,
                'micro_1' => 'µ',
                'micro_0' => ' Micro',
            );
        } elseif ($number < 0.01) {
            $formatting = array(
                'multiplier' => 100000,
                'decimals' => 0,
                'micro_1' => 'm',
                'micro_0' => ' Milli',
            );
        } else {
            //Must be cents
            $formatting = array(
                'multiplier' => 100,
                'decimals' => 0,
                'micro_1' => 'c',
                'micro_0' => ' Cent',
            );
        }

    } elseif ($number >= 950) {

        $original_format = number_format($number); //Add commas

        if ($number >= 950000000) {
            $formatting = array(
                'multiplier' => (1 / 1000000000),
                'decimals' => 1,
                'micro_1' => 'B',
                'micro_0' => ' Billion',
            );
        } elseif ($number >= 9500000) {
            $formatting = array(
                'multiplier' => (1 / 1000000),
                'decimals' => 0,
                'micro_1' => 'M',
                'micro_0' => ' Million',
            );
        } elseif ($number >= 950000) {
            $formatting = array(
                'multiplier' => (1 / 1000000),
                'decimals' => 1,
                'micro_1' => 'M',
                'micro_0' => ' Million',
            );
        } elseif ($number >= 9500) {
            $formatting = array(
                'multiplier' => (1 / 1000),
                'decimals' => 0,
                'micro_1' => 'K',
                'micro_0' => ' Thousand',
            );
        } elseif ($number >= 950) {
            $formatting = array(
                'multiplier' => (1 / 1000),
                'decimals' => 1,
                'micro_1' => 'K',
                'micro_0' => ' Thousand',
            );
        }

    }


    if ($formatting) {

        //See what to show:
        $rounded = round(($number * $formatting['multiplier']), $formatting['decimals']);
        $append = $formatting['micro_' . (int)$micro] . (!$micro ? echo__s($rounded) : '');

        if ($push_message) {
            //Messaging format, show using plain text:
            return $rounded . $append . ' (' . $original_format . ')';
        } else {
            //HTML, so we can show Tooltip:
            return $rounded . $append;
        }

    } else {

        return intval($number);

    }
}


function echo_ln_urls($ln_content, $ln_type_source_id){

    $CI =& get_instance();
    if (in_array($ln_type_source_id, $CI->config->item('en_ids_4537'))) {

        //Player URL Links
        return echo_url_types($ln_content, $ln_type_source_id);

    } elseif($ln_type_source_id==10669) {

        return '<i class="'.$ln_content.'"></i>';

    } elseif(strlen($ln_content) > 0) {

        return echo_url(htmlentities($ln_content));

    } else {

        return null;

    }
}


function echo_ln($ln, $is_parent_tr = false)
{

    $CI =& get_instance();
    $en_all_4593 = $CI->config->item('en_all_4593'); //Link Type
    $en_all_4341 = $CI->config->item('en_all_4341'); //Link Table
    $en_all_2738 = $CI->config->item('en_all_2738');
    $en_all_6186 = $CI->config->item('en_all_6186'); //Transaction Status
    $session_en = superpower_assigned();



    if(!isset($en_all_4593[$ln['ln_type_source_id']])){
        //We've probably have not yet updated php cache, set error:
        $en_all_4593[$ln['ln_type_source_id']] = array(
            'm_icon' => '<i class="fad fa-exclamation-triangle"></i>',
            'm_name' => 'Link Type Not Synced in PHP Cache',
            'm_desc' => '',
            'm_parents' => array(),
        );
    }





    //Display the item
    $ui = '<div class="ledger-list">';


    //Transaction ID
    $ui .= '<div class="simple-line"><a href="/ledger?ln_id='.$ln['ln_id'].'" data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[4367]['m_name'].'" class="montserrat"><span class="icon-block">'.$en_all_4341[4367]['m_icon']. '</span>'.$ln['ln_id'].'</a></div>';


    //Status
    $ui .= '<div class="simple-line"><span data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[6186]['m_name'].( strlen($en_all_6186[$ln['ln_status_source_id']]['m_desc']) ? ': '.$en_all_6186[$ln['ln_status_source_id']]['m_desc'] : '' ).'"><span class="icon-block">'.$en_all_6186[$ln['ln_status_source_id']]['m_icon'].'</span>'.$en_all_6186[$ln['ln_status_source_id']]['m_name'].'</span></div>';

    //Time
    $ui .= '<div class="simple-line"><span data-toggle="tooltip" data-placement="top" title="' . $en_all_4341[4362]['m_name'].': '.$ln['ln_timestamp'] . ' PST"><span class="icon-block">'.$en_all_4341[4362]['m_icon']. '</span>' . echo_time_difference(strtotime($ln['ln_timestamp'])) . ' ago</span></div>';


    //Transaction Type
    $ui .= '<div class="simple-line"><a href="/source/'.$ln['ln_type_source_id'].'" data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[4593]['m_name'].( strlen($en_all_4593[$ln['ln_type_source_id']]['m_desc']) ? ': '.$en_all_4593[$ln['ln_type_source_id']]['m_desc'] : '' ).'" class="montserrat"><span class="icon-block">'.$en_all_4341[4593]['m_icon']. '</span><span class="'.extract_icon_color($en_all_4593[$ln['ln_type_source_id']]['m_icon']).'">'. $en_all_4593[$ln['ln_type_source_id']]['m_icon']. ' '. $en_all_4593[$ln['ln_type_source_id']]['m_name'] . '</span></a></div>';


    //COINS AWARDED?
    if(in_array($ln['ln_type_source_id'], $CI->config->item('en_ids_6255'))){
        $coin_type = 'discover';
    } elseif(in_array($ln['ln_type_source_id'], $CI->config->item('en_ids_12274'))){
        $coin_type = 'source';
    } elseif(in_array($ln['ln_type_source_id'], $CI->config->item('en_ids_12273'))){
        $coin_type = 'idea';
    } else {
        $coin_type = null;
    }
    if($coin_type){
        $ui .= '<div class="simple-line"><span class="icon-block"><i class="fad fa-award"></i></span><span class="montserrat doupper '.$coin_type.'"><i class="fas fa-circle '.$coin_type.'"></i> '.$coin_type.' coin awarded</span></div>';
    }


    //Hide Sensitive Details?
    if(in_array($ln['ln_type_source_id'] , $CI->config->item('en_ids_4755')) && (!$session_en || $ln['ln_creator_source_id']!=$session_en['en_id']) && !superpower_active(12701, true)){

        //Hide Information:
        $ui .= '<div class="simple-line"><span data-toggle="tooltip" class="montserrat" data-placement="top" title="Details are kept private"><span class="icon-block"><i class="fal fa-eye-slash"></i></span>PRIVATE INFORMATION</span></div>';

    } else {

        //Metadata
        if(strlen($ln['ln_metadata']) > 0){
            $ui .= '<div class="simple-line"><a href="/plugin/12722?ln_id=' . $ln['ln_id'] . '" class="montserrat"><span class="icon-block">'.$en_all_4341[6103]['m_icon']. '</span>'.$en_all_4341[6103]['m_name']. '</a></div>';
        }

        //External ID
        if($ln['ln_external_id'] > 0){
            $ui .= '<div class="simple-line"><span data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[7694]['m_name'].'"><span class="icon-block">'.$en_all_4341[7694]['m_icon']. '</span>'.$ln['ln_external_id'].'</span></div>';
        }

        //Order
        if($ln['ln_order'] > 0){
            $ui .= '<div class="simple-line"><span data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[4370]['m_name']. '"><span class="icon-block">'.$en_all_4341[4370]['m_icon']. '</span>'.echo_ordinal_number($ln['ln_order']).'</span></div>';
        }


        //Message
        if(strlen($ln['ln_content']) > 0 && $ln['ln_content']!='@'.$ln['ln_parent_source_id']){
            //$CI->DISCOVER_model->dispatch_message($ln['ln_content'])
            $ui .= '<div class="simple-line" data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[4372]['m_name'].'"><span class="icon-block">'.$en_all_4341[4372]['m_icon'].'</span><div class="title-block ledger-msg">'.htmlentities($ln['ln_content']).'</div></div>';
        }


        //Creator (Do not repeat)
        if($ln['ln_creator_source_id'] > 0 && $ln['ln_creator_source_id']!=$ln['ln_parent_source_id'] && $ln['ln_creator_source_id']!=$ln['ln_child_source_id']){

            $player_ens = $CI->SOURCE_model->en_fetch(array(
                'en_id' => $ln['ln_creator_source_id'],
            ));

            $ui .= '<div class="simple-line"><a href="/source/'.$player_ens[0]['en_id'].'" data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[4364]['m_name'].'" class="montserrat"><span class="icon-block">'.$en_all_4341[4364]['m_icon']. '</span><span class="'.extract_icon_color($player_ens[0]['en_icon']).'"><span class="img-block">'.echo_en_icon($player_ens[0]['en_icon']) . '</span> ' . $player_ens[0]['en_name'] . '</span></a></div>';

        }

    }


    //5x Relations:
    if(!$is_parent_tr){

        $en_all_6232 = $CI->config->item('en_all_6232'); //PLATFORM VARIABLES
        foreach ($CI->config->item('en_all_10692') as $en_id => $m) {

            //Do we have this set?
            if(!intval($ln[$en_all_6232[$en_id]['m_desc']])){
                continue;
            }

            if(in_array(6160 , $m['m_parents'])){

                //SOURCE
                $ens = $CI->SOURCE_model->en_fetch(array('en_id' => $ln[$en_all_6232[$en_id]['m_desc']]));

                $ui .= '<div class="simple-line"><a href="/source/'.$ens[0]['en_id'].'" data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[$en_id]['m_name'].'" class="montserrat"><span class="icon-block">'.$en_all_4341[$en_id]['m_icon']. '</span>'.( $ln[$en_all_6232[$en_id]['m_desc']]==$ln['ln_creator_source_id'] ? $en_all_4341[4364]['m_icon']. '&nbsp;' : '' ).'<span class="'.extract_icon_color($ens[0]['en_icon']).' img-block">'.echo_en_icon($ens[0]['en_icon']). '&nbsp;'.$ens[0]['en_name'].'</span></a></div>';

            } elseif(in_array(6202 , $m['m_parents'])){

                //IDEA
                $ins = $CI->IDEA_model->in_fetch(array('in_id' => $ln[$en_all_6232[$en_id]['m_desc']]));

                $ui .= '<div class="simple-line"><a href="/idea/'.$ins[0]['in_id'].'" data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[$en_id]['m_name'].'" class="montserrat"><span class="icon-block">'.$en_all_4341[$en_id]['m_icon']. '</span>'.$en_all_2738[4535]['m_icon']. '&nbsp;'.echo_in_title($ins[0]).'</a></div>';

            } elseif(in_array(4367 , $m['m_parents'])){

                //PARENT TRANSACTION
                $lns = $CI->DISCOVER_model->ln_fetch(array('ln_id' => $ln[$en_all_6232[$en_id]['m_desc']]));

                $ui .= '<div class="simple-line"><span class="icon-block" data-toggle="tooltip" data-placement="top" title="'.$en_all_4341[$en_id]['m_name'].'">'.$en_all_4341[$en_id]['m_icon']. '</span><div class="transaction-ref">'.echo_ln($lns[0], true).'</div></div>';

            }
        }
    }


    $ui .= '</div>';

    return $ui;
}


function echo_url_clean($url)
{
    //Returns the watered-down version of the URL for a cleaner UI:
    return rtrim(str_replace('http://', '', str_replace('https://', '', str_replace('www.', '', $url))), '/');
}


function echo_time_hours($seconds, $micro = false)
{

    /*
     * A function that will return a fancy string representing hours & minutes
     *
     * */

    if ($seconds < 1) {
        //Under 30 seconds would not round up to even 1 minute, so don't show:
        return 0;
    } elseif ($seconds < 60) {
        return 1 . ($micro ? ' Min.' : ' Minutes');
    } elseif ($seconds < 3600) {
        return round($seconds / 60) . ($micro ? ' Min.' : ' Minutes');
    } else {
        //Roundup the hours:
        $hours = round($seconds / 3600);
        return $hours . ' Hour' . echo__s($hours);
    }
}







function echo_in_tree_sources($in, $push_message = false, $autoexpand = false)
{

    /*
     *
     * a IDEA function to display experts sources for
     * the entire Idea stored in the metadata field.
     *
     * */

    //Do we have anything to return?
    $metadata = unserialize($in['in_metadata']);
    if ((!isset($metadata['in__metadata_experts']) || count($metadata['in__metadata_experts']) < 1) && (!isset($metadata['in__metadata_sources']) || count($metadata['in__metadata_sources']) < 1)) {
        return false;
    }


    //Let's count to see how many content pieces we have references for this Idea:
    $source_info = '';
    $source_count = 0;

    if(isset($metadata['in__metadata_sources'])){
        foreach ($metadata['in__metadata_sources'] as $type_en_id => $referenced_ens) {
            $source_count += count($referenced_ens);
        }
    }
    if ($source_count > 0) {

        //Set some variables and settings to get started:
        $type_all_count = count($metadata['in__metadata_sources']);
        $CI =& get_instance();
        $en_all_3000 = $CI->config->item('en_all_3000');
        $visible_ppl = 3; //How many people to show before clicking on "see more"
        $type_count = 0;
        foreach ($metadata['in__metadata_sources'] as $type_id => $referenced_ens) {

            if ($type_count > 0) {
                if (($type_count + 1) >= $type_all_count) {
                    $source_info .= ' &';
                } else {
                    $source_info .= ',';
                }
            }

            //Show category:
            $cat_contribution = count($referenced_ens) . ' ' . $en_all_3000[$type_id]['m_name'];
            if ($push_message) {

                $source_info .= ' ' . $cat_contribution;

            } else {

                $source_info .= ' <span class="show_type_' . $type_id . '"><a href="javascript:void(0);" source-type-en-id="'.$type_id.'" onclick="$(\'.show_type_' . $type_id . '\').toggle()" style="text-decoration:underline; display:inline-block;">' . $cat_contribution . '</a></span><span class="show_type_' . $type_id . '" style="display:none;">';

                //We only show details on our website's HTML landing pages:
                $count = 0;
                foreach ($referenced_ens as $en) {

                    if ($count > 0) {
                        if (($count + 1) >= count($referenced_ens)) {
                            $source_info .= ' &';
                        } else {
                            $source_info .= ',';
                        }
                    }

                    $source_info .= ' ';

                    //Show link to platform:
                    //$source_info .= '<a href="/source/' . $en['en_id'] . '">';
                    $source_info .= '<span>';
                    $source_info .= $en['en_name'];
                    $source_info .= '</span>';
                    //$source_info .= '</a>';

                    $count++;
                }
                $source_info .= '</span>';

            }
            $type_count++;
        }
    }


    //Define some variables to get stared:
    $expert_count = ( isset($metadata['in__metadata_experts']) ? count($metadata['in__metadata_experts']) : 0 );
    $visible_html = 4; //Landing page, beyond this is hidden and visible with a click
    $visible_bot = 10; //Plain text style, but beyond this is cut out!
    $expert_info = '';

    if(isset($metadata['in__metadata_experts'])){
        foreach ($metadata['in__metadata_experts'] as $count => $en) {

            $is_last_fb_item = ($push_message && $count >= $visible_bot);

            if ($count > 0) {
                if (($count + 1) >= $expert_count || $is_last_fb_item) {
                    $expert_info .= ' &';
                    if ($is_last_fb_item) {
                        $expert_info .= ' ' . ($expert_count - $visible_bot) . ' more!';
                        break;
                    }
                } else {
                    $expert_info .= ',';
                }
            }

            $expert_info .= ' ';

            if ($push_message) {

                //Just the name:
                $expert_info .= $en['en_name'];

            } else {

                //HTML Format:
                //$expert_info .= '<a href="/source/' . $en['en_id'] . '">';
                $expert_info .= '<span>';
                $expert_info .= $en['en_name'];
                $expert_info .= '</span>';
                //$expert_info .= '</a>';

                if (($count + 1) == $visible_html && ($expert_count - $visible_html) > 0) {
                    $expert_info .= '<span class="show_more_' . $in['in_id'] . '"> & <a href="javascript:void(0);" onclick="$(\'.show_more_' . $in['in_id'] . '\').toggle()" style="text-decoration:underline;">' . ($expert_count - $visible_html) . ' more</a>.</span><span class="show_more_' . $in['in_id'] . '" style="display:none;">';
                }
            }
        }

        if (!$push_message && ($count + 1) >= $visible_html) {
            //Close the span:
            $expert_info .= '.</span>';
        } elseif ($push_message && !$is_last_fb_item) {
            //Close the span:
            $expert_info .= '.';
        }
    }



    $pitch_title = '<span class="icon-block"><i class="fas fa-shield-check"></i></span>&nbsp;';
    $pitch_body = 'References ';
    if($source_count > 0){
        $pitch_title .= $source_count . ' source' . echo__s($source_count);
        $pitch_body .= trim($source_info);
    }
    if($expert_count > 0){
        if($source_count > 0){
            $pitch_title .= ' by ';
            $pitch_body .= ' by ';
        }
        $pitch_title .= $expert_count . ' expert'. echo__s($expert_count);
        $pitch_body .= $expert_count . ' industry expert'. echo__s($expert_count) . ($expert_count == 1 ? ':' : ' including') . $expert_info;
    }

    if ($push_message) {
        return '⭐ ' . $pitch_body. "\n\n";
    } else {
        //HTML format
        return $pitch_title.$pitch_body;
    }
}


function echo_time_range($in, $micro = false, $hide_zero = false)
{

    //Make sure we have metadata passed on via $in as sometimes it might miss it (Like when passed on via Algolia results...)
    if (!isset($in['in_metadata'])) {
        //We don't have it, so fetch it:
        $CI =& get_instance();
        $ins = $CI->IDEA_model->in_fetch(array(
            'in_id' => $in['in_id'], //We should always have Idea ID
        ));
        if (count($ins) > 0) {
            $in = $ins[0];
        } else {
            return false;
        }
    }

    //By now we have the metadata, extract it:
    $metadata = unserialize($in['in_metadata']);

    if (!isset($metadata['in__metadata_max_seconds']) || !isset($metadata['in__metadata_min_seconds'])) {
        return false;
    } elseif($hide_zero && $metadata['in__metadata_max_seconds'] < 1){
        return false;
    }

    //Construct the UI:
    if ($metadata['in__metadata_max_seconds'] == $metadata['in__metadata_min_seconds']) {

        //Exactly the same, show a single value:
        return echo_time_hours($metadata['in__metadata_max_seconds'], $micro);

    } elseif ($metadata['in__metadata_min_seconds'] < 3600) {

        if ($metadata['in__metadata_min_seconds'] < 7200 && $metadata['in__metadata_max_seconds'] < 10800) {
            $is_minutes = true;
            $hours_decimal = 0;
        } elseif ($metadata['in__metadata_min_seconds'] < 36000) {
            $is_minutes = false;
            $hours_decimal = 1;
        } else {
            //Number too large to matter, just treat as one:
            return echo_time_hours($metadata['in__metadata_max_seconds'], $micro);
        }

    } else {
        $is_minutes = false;
        $hours_decimal = 0;
    }

    $min_minutes = round($metadata['in__metadata_min_seconds'] / 60);
    $min_hours = round(($metadata['in__metadata_min_seconds'] / 3600), $hours_decimal);
    $max_minutes = round($metadata['in__metadata_max_seconds'] / 60);
    $max_hours = round(($metadata['in__metadata_max_seconds'] / 3600), $hours_decimal);

    //Generate hours range:
    $the_min = ($is_minutes ? $min_minutes : $min_hours );
    $the_max = ($is_minutes ? $max_minutes : $max_hours );
    $ui_time = $the_min;
    if($the_min != $the_max){
        $ui_time .= ( $micro ? '-' : ' - ' );
        $ui_time .= $the_max;
    }
    $ui_time .= strtoupper($is_minutes ? ($micro ? ' Min.' : ' Minute'.echo__s($max_minutes)) : ' Hour'.echo__s($max_hours));

    //Generate UI to return:
    return $ui_time;
}


function echo_time_difference($t, $second_time = null)
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

    foreach ($time_units as $unit => $period) {
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


function echo_en_cache($config_var_name, $en_id, $micro_status = true, $data_placement = 'top')
{

    /*
     *
     * UI for Platform Cache sources
     *
     * */

    $CI =& get_instance();
    $config_array = $CI->config->item($config_var_name);
    $cache_en = $config_array[$en_id];
    if (!$cache_en) {
        //Could not find matching item
        return false;
    }


    //We have two skins for displaying Status:
    if (is_null($data_placement)) {
        if($micro_status){
            return $cache_en['m_icon'].' ';
        } else {
            return $cache_en['m_icon'].' '.$cache_en['m_name'].' ';
        }
    } else {
        return '<span class="status-label" ' . ( $micro_status && !is_null($data_placement) ? 'data-toggle="tooltip" data-placement="' . $data_placement . '" title="' . ($micro_status ? $cache_en['m_name'] : '') . (strlen($cache_en['m_desc']) > 0 ? ($micro_status ? ': ' : '') . $cache_en['m_desc'] : '') . '"' : 'style="cursor:pointer;"') . '>' . $cache_en['m_icon'] . ' ' . ($micro_status ? '' : $cache_en['m_name']) . '</span>';
    }
}



function echo_coins_count_discover($in_id = 0, $en_id = 0){

    $CI =& get_instance();
    $discover_coins = $CI->DISCOVER_model->ln_fetch(array(
        'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
        'ln_type_source_id IN (' . join(',', $CI->config->item('en_ids_6255')) . ')' => null,
        ( $in_id > 0 ? 'ln_previous_idea_id' : 'ln_creator_source_id' ) => ( $in_id > 0 ? $in_id : $en_id ),
    ), array(), 1, 0, array(), 'COUNT(ln_id) as totals');

    if($discover_coins[0]['totals'] > 0){
        return '<span class="montserrat discover"><span class="icon-block"><i class="fas fa-circle"></i></span>'.echo_number($discover_coins[0]['totals']).'</span>';
    } else {
        return false;
    }

}

function echo_coins_count_source($in_id = 0, $en_id = 0){

    $CI =& get_instance();

    if($in_id){
        $mench = 'source';
        $coin_filter = array(
            'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
            'ln_type_source_id IN (' . join(',', $CI->config->item('en_ids_12273')) . ')' => null, //IDEA COIN
            'ln_next_idea_id' => $in_id,
        );
    } elseif($en_id){
        $mench = 'idea';
        $coin_filter = array(
            'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
            'ln_type_source_id IN (' . join(',', $CI->config->item('en_ids_12273')) . ')' => null, //IDEA COIN
            'ln_parent_source_id' => $en_id,
        );
    }

    $source_coins = $CI->DISCOVER_model->ln_fetch($coin_filter, array(), 0, 0, array(), 'COUNT(ln_id) as totals');
    if($source_coins[0]['totals'] > 0){
        return '<span class="montserrat '.$mench.'"><span class="icon-block"><i class="fas fa-circle"></i></span>'.echo_number($source_coins[0]['totals']).'</span>';
    }

    return null;
}



function echo_in_discover($in, $parent_is_or = false, $common_prefix = null, $extra_class = null, $show_editor = false, $completion_rate = null, $recipient_en = false)
{

    //See if user is logged-in:
    $CI =& get_instance();
    if(!$recipient_en){
        $recipient_en = superpower_assigned();
    }

    $metadata = unserialize($in['in_metadata']);
    $has_time_estimate = ( isset($metadata['in__metadata_max_seconds']) && $metadata['in__metadata_max_seconds']>0 );


    if(!$completion_rate){
        if($recipient_en){
            $completion_rate = $CI->DISCOVER_model->discover__completion_progress($recipient_en['en_id'], $in);
        } else {
            $completion_rate['completion_percentage'] = 0;
        }
    }

    $can_click = ( ( $parent_is_or && in_array($in['in_status_source_id'], $CI->config->item('en_ids_12138')) ) || $completion_rate['completion_percentage']>0 || $show_editor );


    $ui  = '<div id="ap_in_'.$in['in_id'].'" '.( isset($in['ln_id']) ? ' sort-link-id="'.$in['ln_id'].'" ' : '' ).' class="list-group-item no-side-padding '.( $show_editor ? 'actionplan_sort' : '' ).' itemdiscover '.$extra_class.'">';
    $ui .= ( $can_click ? '<a href="/'.$in['in_id'] . '" class="itemdiscover">' : '' );



    if($can_click && $completion_rate['completion_percentage']>0){
        $ui .= '<div class="progress-bg" title="You are '.$completion_rate['completion_percentage'].'% done as you have discover '.$completion_rate['steps_completed'].' of '.$completion_rate['steps_total'].' ideas'.( $has_time_estimate ? ' (Total Estimate '.echo_time_range($in, true).')' : '' ).'"><div class="progress-done" style="width:'.$completion_rate['completion_percentage'].'%"></div></div>';
    }


    //DISCOVER ICON
    $ui .= '<span class="icon-block">'.( $can_click ? '<i class="fas fa-circle discover"></i>' : '<i class="far fa-lock discover"></i>' ).'</span>';
    $ui .= '<b class="montserrat idea-url title-block" style="padding-right:23px;">'.echo_in_title($in, false, $common_prefix).'</b>';


    //Search for Idea Image:
    if($show_editor){

        $ui .= '<div class="pads-editor edit-off">';

        $ui .= '<span class="show-on-hover">';

        $ui .= '<span title="REMOVE" data-toggle="tooltip" data-placement="left"><span class="actionplan_delete" in-id="'.$in['in_id'].'"><i class="fas fa-times"></i></span></span>';

        $ui .= '<span class="discover-sorter" title="SORT" data-toggle="tooltip" data-placement="left"><i class="fas fa-bars"></i></span>';

        $ui .= '</span>';
        $ui .= '</div>';

    }

    $ui .= ( $can_click ? '</a>' : '' );
    $ui .= '</div>';

    return $ui;
}


function echo_in_scores_answer($starting_in, $depth_levels, $original_depth_levels, $parent_in_type_source_id){

    if($depth_levels<=0){
        //End recursion:
        return false;
    }

    //We're going 1 level deep:
    $depth_levels--;

    //Go down recursively:
    $CI =& get_instance();
    $en_all_6186 = $CI->config->item('en_all_6186'); //Transaction Status
    $en_all_4486 = $CI->config->item('en_all_4486');
    $en_all_4737 = $CI->config->item('en_all_4737'); // Idea Status
    $en_all_7585 = $CI->config->item('en_all_7585'); // Idea Subtypes


    $ui = null;
    foreach($CI->DISCOVER_model->ln_fetch(array(
        'ln_previous_idea_id' => $starting_in,
        'ln_type_source_id IN (' . join(',', $CI->config->item('en_ids_4486')) . ')' => null, //Idea-to-Idea Links
        'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7360')) . ')' => null, //Transaction Status Active
        'in_status_source_id IN (' . join(',', $CI->config->item('en_ids_7356')) . ')' => null, //Idea Status Active
    ), array('in_child'), 0, 0, array('ln_order' => 'ASC')) as $in_ln){

        //Prep Metadata:
        $metadata = unserialize($in_ln['ln_metadata']);
        $tr__assessment_points = ( isset($metadata['tr__assessment_points']) ? $metadata['tr__assessment_points'] : 0 );
        $messages = $CI->DISCOVER_model->ln_fetch(array(
            'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7360')) . ')' => null, //Transaction Status Active
            'ln_type_source_id' => 4231, //Idea Notes Messages
            'ln_next_idea_id' => $in_ln['in_id'],
        ), array(), 0, 0, array('ln_order' => 'ASC'));

        //Display block:
        $ui .= '<div class="'.( $tr__assessment_points==0 ? 'no-assessment ' : 'has-assessment' ).'">';
        $ui .= '<span class="icon-block" data-toggle="tooltip" data-placement="top" title="Idea Link Type: '.$en_all_4486[$in_ln['ln_type_source_id']]['m_name'].'">'. $en_all_4486[$in_ln['ln_type_source_id']]['m_icon'] . '</span>';
        $ui .= '<span class="icon-block" data-toggle="tooltip" data-placement="top" title="Idea Transaction Status: '.$en_all_6186[$in_ln['ln_status_source_id']]['m_name'].'">'. $en_all_6186[$in_ln['ln_status_source_id']]['m_icon'] . '</span>';

        $ui .= '<span class="icon-block" data-toggle="tooltip" data-placement="top" title="Idea Type: '.$en_all_7585[$in_ln['in_type_source_id']]['m_name'].'">'. $en_all_7585[$in_ln['in_type_source_id']]['m_icon'] . '</span>';
        $ui .= '<span class="icon-block" data-toggle="tooltip" data-placement="top" title="Idea Status: '.$en_all_4737[$in_ln['in_status_source_id']]['m_name'].'">'. $en_all_4737[$in_ln['in_status_source_id']]['m_icon']. '</span>';
        $ui .= '<a href="?starting_in='.$in_ln['in_id'].'&depth_levels='.$original_depth_levels.'" data-toggle="tooltip" data-placement="top" title="Navigate report to this idea"><u>' .   echo_in_title($in_ln, false) . '</u></a>';

        $ui .= ' [<span data-toggle="tooltip" data-placement="top" title="Completion Marks">'.( ($in_ln['ln_type_source_id'] == 4228 && in_array($parent_in_type_source_id , $CI->config->item('en_ids_6193') /* OR Ideas */ )) || ($in_ln['ln_type_source_id'] == 4229) ? echo_in_marks($in_ln) : '' ).'</span>]';

        if(count($messages) > 0){
            $ui .= ' <a href="javascript:void(0);" onclick="$(\'.messages-'.$in_ln['in_id'].'\').toggleClass(\'hidden\');"><i class="fas fa-comment"></i><b>' .  count($messages) . '</b></a>';
        }
        $ui .= '</div>';

        //Display Messages:
        $ui .= '<div class="messages-'.$in_ln['in_id'].' hidden">';
        foreach ($messages as $msg) {
            $ui .= '<div class="tip_bubble">';
            $ui .= $CI->DISCOVER_model->dispatch_message($msg['ln_content']);
            $ui .= '</div>';
        }
        $ui .= '</div>';

        //Go Recursively down:
        $ui .=  echo_in_scores_answer($in_ln['in_id'], $depth_levels, $original_depth_levels, $in_ln['in_type_source_id']);

    }

    //Return the wrapped UI if existed:
    return ($ui ? '<div class="inline-box">' . $ui . '</div>' : false);
}

function echo_radio_sources($parent_en_id, $child_en_id, $enable_mulitiselect, $show_max = 25){

    /*
     * Print UI for
     * */

    $CI =& get_instance();
    $count = 0;

    $ui = '<div class="list-group radio-'.$parent_en_id.'">';

    if(!count($CI->config->item('en_ids_'.$parent_en_id))){
        return false;
    }

    foreach($CI->config->item('en_all_'.$parent_en_id) as $en_id => $m) {
        $ui .= '<a href="javascript:void(0);" onclick="account_update_radio('.$parent_en_id.','.$en_id.','.$enable_mulitiselect.')" class="item'.extract_icon_color($m['m_icon']).' list-group-item montserrat itemsetting item-'.$en_id.' '.( $count>=$show_max ? 'extra-items-'.$parent_en_id.' hidden ' : '' ).( count($CI->DISCOVER_model->ln_fetch(array(
                'ln_parent_source_id' => $en_id,
                'ln_child_source_id' => $child_en_id,
                'ln_type_source_id IN (' . join(',', $CI->config->item('en_ids_4592')) . ')' => null, //Source Links
                'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
            )))>0 ? ' active ' : '' ). '"><span class="icon-block">'.$m['m_icon'].'</span>'.$m['m_name'].'<span class="change-results"></span></a>';
        $count++;
    }


    //Did we have too many items?
    if($count>=$show_max){
        //Show "Show more" button
        $ui .= '<a href="javascript:void(0);" class="list-group-item itemsource itemsetting montserrat extra-items-'.$parent_en_id.'" onclick="$(\'.extra-items-'.$parent_en_id.'\').toggleClass(\'hidden\')"><span class="icon-block"><i class="fas fa-plus-circle"></i></span>Show '.($count-$show_max).' more</a>';
    }

    $ui .= '</div>';

    return $ui;
}


function echo_in_marks($in_ln){

    //Validate core inputs:
    if(!isset($in_ln['ln_metadata']) || !isset($in_ln['ln_type_source_id'])){
        return false;
    }

    //prep metadata:
    $ln_metadata = unserialize($in_ln['ln_metadata']);

    //Return mark:
    return ( $in_ln['ln_type_source_id'] == 4228 ? ( !isset($ln_metadata['tr__assessment_points']) || $ln_metadata['tr__assessment_points'] == 0 ? '' : '<span class="score-range">[<span style="'.( $ln_metadata['tr__assessment_points']>0 ? 'font-weight:bold;' : ( $ln_metadata['tr__assessment_points'] < 0 ? 'font-weight:bold;' : '' )).'">' . ( $ln_metadata['tr__assessment_points'] > 0 ? '+' : '' ) . $ln_metadata['tr__assessment_points'].'</span>]</span>' ) : '<span class="score-range">['.$ln_metadata['tr__conditional_score_min'] . ( $ln_metadata['tr__conditional_score_min']==$ln_metadata['tr__conditional_score_max'] ? '' : '-'.$ln_metadata['tr__conditional_score_max'] ).'%]</span>' );

}


function echo_in($in, $in_linked_id, $is_parent, $is_source, $infobar_details = null)
{

    $CI =& get_instance();

    $en_all_6186 = $CI->config->item('en_all_6186');
    $en_all_4737 = $CI->config->item('en_all_4737'); //IDEA STATUS
    $en_all_7585 = $CI->config->item('en_all_7585');
    $en_all_4486 = $CI->config->item('en_all_4486');
    $en_all_2738 = $CI->config->item('en_all_2738');
    $en_all_12413 = $CI->config->item('en_all_12413');

    //Prep link metadata to be analyzed later:
    $ln_id = $in['ln_id'];
    $ln_metadata = unserialize($in['ln_metadata']);
    $in_metadata = unserialize($in['in_metadata']);

    $session_en = superpower_assigned();
    $is_public = in_array($in['in_status_source_id'], $CI->config->item('en_ids_7355'));
    $is_link_published = in_array($in['ln_status_source_id'], $CI->config->item('en_ids_7359'));
    $is_in_link = in_array($in['ln_type_source_id'], $CI->config->item('en_ids_4486'));
    $is_source = ( !$is_in_link ? false : $is_source ); //Disable Edits on Idea List Page

    $ui = '<div in-link-id="' . $ln_id . '" in-tr-type="' . $in['ln_type_source_id'] . '" idea-id="' . $in['in_id'] . '" parent-idea-id="' . $in_linked_id . '" class="list-group-item no-side-padding itemidea ideas_sortable level2_in object_highlight highlight_in_'.$in['in_id'] . ' in_line_' . $in['in_id'] . ( $is_parent ? ' parent-idea ' : '' ) . ' in__tr_'.$ln_id.'" style="padding-left:0;">';


    $ui .= '<table class="table table-sm" style="background-color: transparent !important; margin-bottom: 0;"><tr>';

    $ui .= '<td class="MENCHcolumn1">';


        $ui .= '<div class="block">';
            //IDEA ICON:
            $ui .= '<span class="icon-block"><a href="/idea/'.$in['in_id'].'" title="Weight: '.number_format($in['in_weight'], 0).'">' . $en_all_2738[4535]['m_icon'] . '</a></span>';

            //IDEA TITLE
            if($is_in_link && superpower_active(12673, true)){
                $ui .= echo_in_text(4736, $in['in_title'], $in['in_id'], $is_source, (($in['ln_order']*100)+1));
            } else {
                $ui .= '<a href="/idea/'.$in['in_id'].'" class="title-block montserrat">' . echo_in_title($in) . '</a>';
            }
        $ui .= '</div>';




        //SECOND STATS ROW
        $ui .= '<div class="doclear">&nbsp;</div>';

        //Idea Toolbar
        $ui .= '<div class="space-content ' . superpower_active(12673) . '">';

            //IDEA STATUS
            $ui .= '<div class="inline-block">' . echo_in_dropdown(4737, $in['in_status_source_id'], null, $is_source, false, $in['in_id']) . ' </div>';

            //IDEA TYPE
            $ui .= echo_in_dropdown(7585, $in['in_type_source_id'], null, $is_source, false, $in['in_id']);

            //IDEA DISCOVER TIME
            $ui .= echo_in_text(4356, $in['in_time_seconds'], $in['in_id'], $is_source, ($in['ln_order']*10)+1);


            //PREVIOUS & NEXT IDEAS
            $previous_ins = $CI->DISCOVER_model->ln_fetch(array(
                'ln_next_idea_id' => $in['in_id'],
                'ln_type_source_id IN (' . join(',', $CI->config->item('en_ids_4486')) . ')' => null, //Idea-to-Idea Links
                'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7360')) . ')' => null, //Transaction Status Active
            ), array(), 0, 0, array(), 'COUNT(ln_id) as total_ins');
            $next_ins = $CI->DISCOVER_model->ln_fetch(array(
                'ln_previous_idea_id' => $in['in_id'],
                'ln_type_source_id IN (' . join(',', $CI->config->item('en_ids_4486')) . ')' => null, //Idea-to-Idea Links
                'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7360')) . ')' => null, //Transaction Status Active
            ), array(), 0, 0, array(), 'COUNT(ln_id) as total_ins');


            //Previous idea:
            $ui .= '<span class="montserrat idea idea-previous">' . ( $previous_ins[0]['total_ins'] >= 2 ? $previous_ins[0]['total_ins'] . $en_all_12413[11019]['m_icon'] : '&nbsp;') . '</span>';

            //Next Ideas:
            $ui .= '<span class="montserrat idea idea-next">' . ( $next_ins[0]['total_ins'] > 0 ? $en_all_12413[11020]['m_icon'] . $next_ins[0]['total_ins']: '&nbsp;' ) . '</span>';



            //Idea Wand
            $ui .= '<div class="inline-block ' . superpower_active(12700) . '">';
                //LINK TYPE
                $ui .= echo_in_dropdown(4486, $in['ln_type_source_id'], null, $is_source, false, $in['in_id'], $in['ln_id']);

                //LINK MARKS
                $ui .= '<span class="link_marks settings_4228 '.( $in['ln_type_source_id']==4228 ? : 'hidden' ).'">';
                $ui .= echo_in_text(4358, ( isset($ln_metadata['tr__assessment_points']) ? $ln_metadata['tr__assessment_points'] : '' ), $in['ln_id'], $is_source, ($in['ln_order']*10)+2 );
                $ui .='</span>';


                //LINK CONDIITONAL RANGE
                $ui .= '<span class="link_marks settings_4229 '.( $in['ln_type_source_id']==4229 ? : 'hidden' ).'">';
                //MIN
                $ui .= echo_in_text(4735, ( isset($ln_metadata['tr__conditional_score_min']) ? $ln_metadata['tr__conditional_score_min'] : '' ), $in['ln_id'], $is_source, ($in['ln_order']*10)+3);
                //MAX
                $ui .= echo_in_text(4739, ( isset($ln_metadata['tr__conditional_score_max']) ? $ln_metadata['tr__conditional_score_max'] : '' ), $in['ln_id'], $is_source, ($in['ln_order']*10)+4);
                $ui .= '</span>';
            $ui .= '</div>';



        $ui .= '</div>';

    $ui .= '</td>';




    //DISCOVER
    $ui .= '<td class="MENCHcolumn2 discover">';
    $ui .= echo_coins_count_discover($in['in_id']);
    $ui .= '</td>';




    //SOURCE
    $ui .= '<td class="MENCHcolumn3 source">';

    //RIGHT EDITING:
    $ui .= '<div class="pull-right inline-block '.superpower_active(10939).'">';
    $ui .= '<div class="pads-editor edit-off">';
    $ui .= '<span class="show-on-hover">';

    if($is_in_link){
        if($is_source || !$is_parent){

            //Unlink:
            $ui .= '<span title="UNLINK" data-toggle="tooltip" data-placement="left"><a href="javascript:void(0);" onclick="in_unlink('.$in['in_id'].', '.$in['ln_id'].')"><i class="fas fa-times black"></i></a></span>';

            if($is_source && !$is_parent){
                $ui .= '<span title="SORT" data-toggle="tooltip" data-placement="left"><i class="fas fa-bars black idea-sort-handle"></i></span>';
            }

        } elseif(!$is_source) {

            //Indicate if NOT a Source:
            $ui .= '<span data-toggle="tooltip" title="You are not yet a source of this idea" data-placement="bottom"><i class="fas fa-user-minus discover"></i></span>';

        }
    }

    $ui .= '</span>';
    $ui .= '</div>';
    $ui .= '</div>';


    //SOURCE STATS
    $ui .= echo_coins_count_source($in['in_id'], 0);

    $ui .= '</td>';



    $ui .= '</tr></table>';

    if($infobar_details){
        $ui .= '<div class="idea-footer">' . $infobar_details . '</div>';
    }

    $ui .= '</div>';



    return $ui;

}




function echo_caret($en_id, $m, $url_append){
    //Display drop down menu:
    $CI =& get_instance();

    $superpower_actives = array_intersect($CI->config->item('en_ids_10957'), $m['m_parents']);

    $ui = '<li class="nav-item dropdown '.( count($superpower_actives) ? superpower_active(end($superpower_actives)) : '' ).'" title="'.$m['m_name'].'" data-toggle="tooltip" data-placement="top">';
    $ui .= '<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"></a>';
    $ui .= '<div class="dropdown-menu">';
    foreach ($CI->config->item('en_all_'.$en_id) as $en_id2 => $m2){
        $ui .= '<a class="dropdown-item" href="' . $m2['m_desc'] . $url_append . '"><span class="icon-block">'.$m2['m_icon'].'</span> '.$m2['m_name'].'</a>';
    }
    $ui .= '</div>';
    $ui .= '</li>';

    return $ui;
}


function echo_in_list($in, $in__children, $recipient_en, $push_message, $prefix_statement = null, $in_discoveries = true, $show_next = true){

    //If no list just return the next step:
    if(!count($in__children)){
        return ( $show_next ? echo_in_next($in['in_id'], $recipient_en, $push_message) : false );
    }

    $CI =& get_instance();

    if(count($in__children)){

        if(!$push_message){
            echo '<div class="previous_discoveries">';
        }

        //List children so they know what's ahead:
        $max_and_list = ( $push_message ? 5 : 0 );
        $common_prefix = common_prefix($in__children, 'in_title', $in, $max_and_list);
        $has_content = ($prefix_statement || strlen($common_prefix));

        if($push_message){

            $message_content = ( $has_content ? trim($prefix_statement)."\n\n" : '' );
            $msg_quick_reply = array();

        } else {

            //HTML:
            if($has_content){
                echo '<div class="discover-topic">'.trim($prefix_statement).'</div>';
            } else {
                echo '<div class="discover-topic"><span class="icon-block">&nbsp;</span>NEXT:</div>';
            }
            echo '<div class="list-group">';

        }




        foreach($in__children as $key => $child_in){

            if($push_message){

                $message_content .= ($key+1).'. '.echo_in_title($child_in, $push_message, $common_prefix)."\n";


                //We know that the $next_step_message length cannot surpass the limit defined by facebook
                if (($key >= $max_and_list || strlen($message_content) > (config_var(11074) - 150))) {
                    //We cannot add any more, indicate truncating:
                    $remainder = count($in__children) - $max_and_list;
                    $message_content .= "\n\n".'... plus ' . $remainder . ' more discover' . echo__s($remainder) . '.';
                    break;
                }

            } else {

                echo echo_in_discover($child_in, false, $common_prefix);

            }
        }

        if($push_message){

            $CI->DISCOVER_model->dispatch_message(
                $message_content,
                $recipient_en,
                true,
                $msg_quick_reply
            );

        } else {

            echo '</div>';

        }
    }

    if(!$push_message){
        echo '</div>';
    }

    if($show_next){
        echo_in_next($in['in_id'], $recipient_en, $push_message);
    }

}

function echo_in_next($in_id, $recipient_en, $push_message){

    //A function to display warning/success messages to users:
    $CI =& get_instance();

    if($push_message){

        $CI->DISCOVER_model->dispatch_message(
            'Say next to continue your discovery.',
            $recipient_en,
            true,
            array(
                array(
                    'content_type' => 'text',
                    'title' => 'Next',
                    'payload' => 'GONEXT_'.$in_id,
                )
            )
        );

    } else {

        //PREVIOUS:
        echo echo_in_previous_discover($in_id, $recipient_en);

        //NEXT:
        $en_all_11035 = $CI->config->item('en_all_11035'); //MENCH NAVIGATION
        echo '<div class="inline-block margin-top-down previous_discoveries pull-left"><a class="btn btn-discover" href="/discover/next/'.$in_id.'">'.$en_all_11035[12211]['m_name'].' '.$en_all_11035[12211]['m_icon'].'</a></div>';

    }

}

function echo_in_previous_discover($in_id, $recipient_en){

    if(!$recipient_en || $recipient_en['en_id'] < 1){
        return null;
    }

    //Now fetch the parent of the current
    $ui = null;
    $CI =& get_instance();
    $en_all_4737 = $CI->config->item('en_all_4737'); // Idea Status
    $en_all_2738 = $CI->config->item('en_all_2738');

    //DISCOVER LIST
    $player_discover_ids = $CI->DISCOVER_model->discover_ids($recipient_en['en_id']);
    $recursive_parents = $CI->IDEA_model->in_recursive_parents($in_id, true, true);
    $top_progress = null;
    foreach ($recursive_parents as $grand_parent_ids) {
        foreach(array_intersect($grand_parent_ids, $player_discover_ids) as $intersect) {

            //Show the breadcrumb since it's connected:
            $ui .= '<div id="previous_start_position">';
            $ui .= '<div class="previous_discoveries hidden">';
            //$ui .= '<div class="discover-topic"><span class="icon-block">&nbsp;</span>SELECT PREVIOUS:</div>';
            $ui .= '<div class="list-group bottom-discover-line">';

            $breadcrumb_items = array();

            foreach ($grand_parent_ids as $parent_in_id) {

                $ins_this = $CI->IDEA_model->in_fetch(array(
                    'in_id' => $parent_in_id,
                ));

                $completion_rate = $CI->DISCOVER_model->discover__completion_progress($recipient_en['en_id'], $ins_this[0]);

                array_push($breadcrumb_items, echo_in_discover($ins_this[0], false, null, null, false, $completion_rate, $recipient_en));

                if ($parent_in_id == $intersect) {
                    $top_progress = $completion_rate['completion_percentage'];
                    break;
                }
            }

            $ui .= join('', array_reverse($breadcrumb_items));
            $ui .= '</div>';
            $ui .= '</div>';
            $ui .= '</div>';

            break;

        }

        if($ui){
            break;
        }

    }


    //Did We Find It?
    if($ui){
        //Previous
        $ui .= '<div class="inline-block margin-top-down selected_before pull-left"><a class="btn btn-discover" href="javascript:void(0);" onclick="$(\'.previous_discoveries\').toggleClass(\'hidden\');"><span class="previous_discoveries"><i class="fad fa-step-backward"></i>&nbsp;<b class="montserrat">'.$top_progress.'%</b></span><span class="previous_discoveries hidden"><i class="fas fa-times"></i></span></a>&nbsp;</div>';
    }

    //Append Edit Option:
    if(superpower_active(10939, true)){
        //Allow Edit:
        $ui .= '<div class="inline-block margin-top-down previous_discoveries pull-right"><a class="btn btn-idea" href="/idea/'.$in_id.'"><i class="fas fa-pen-square"></i></a></div>';
    }


    return $ui;
}

function echo_in_note_source($in_id, $note_type_en_id, $in_notes, $is_source){

    $CI =& get_instance();
    $en_all_11018 = $CI->config->item('en_all_11018');

    $ui = '<div class="list-group">';
    foreach ($in_notes as $en) {
        $ui .= echo_en($en, false, null, true);
    }

    if( $is_source ){
        $ui .= '<div class="list-group-item itemsource '.superpower_active(10939).'" style="padding:5px 0;">
                <div class="input-group border">
                    <span class="input-group-addon addon-lean" style="margin-top: 6px;"><span class="icon-block">'.$en_all_11018[$note_type_en_id]['m_icon'].'</span></span>
                    <input type="text"
                           class="form-control IdeaAddPrevious form-control-thick montserrat algolia_search dotransparent"
                           maxlength="' . config_var(11072) . '"
                           idea-id="' . $in_id . '"
                           id="add-source-idea-' . $in_id . '"
                           style="margin-bottom: 0; padding: 5px 0;"
                           placeholder="'.$en_all_11018[$note_type_en_id]['m_name'].'">
                </div><div class="algolia_pad_search hidden in_pad_top"></div></div>';
    }
    $ui .= '</div>';

    return $ui;
}

function echo_in_note_mix($note_type_en_id, $in_notes, $is_source){

    $CI =& get_instance();
    $en_all_4485 = $CI->config->item('en_all_4485'); //Idea Notes

    //Show no-Message notifications for each message type:
    $ui = '<div id="in_notes_list_'.$note_type_en_id.'" class="list-group">';

    foreach ($in_notes as $in_notes) {
        $ui .= echo_in_notes($in_notes);
    }

    //ADD NEW Alert:
    $ui .= '<div class="list-group-item itemidea space-left add_notes_' . $note_type_en_id . ( $is_source ? '' : ' hidden ' ).'">';
    $ui .= '<div class="add_notes_form">';
    $ui .= '<form class="box box' . $note_type_en_id . '" method="post" enctype="multipart/form-data" class="'.superpower_active(10939).'">'; //Used for dropping files


    $ui .= '<textarea onkeyup="in_new_notes_count('.$note_type_en_id.')" class="form-control msg pads-textarea algolia_search new-pads" pads-type-id="' . $note_type_en_id . '" id="ln_content' . $note_type_en_id . '" placeholder="WRITE'.( in_array($note_type_en_id, $CI->config->item('en_ids_7551')) || in_array($note_type_en_id, $CI->config->item('en_ids_4986')) ? ', PASTE URL' : '' ).( in_array($note_type_en_id, $CI->config->item('en_ids_12359')) ? ', DRAG FILE' : '' ).'" style="margin-top:6px;"></textarea>';



    $ui .= '<table class="table table-condensed hidden" id="pads_control_'.$note_type_en_id.'"><tr>';

    //Save button:
    $ui .= '<td style="width:85px; padding: 10px 0 0 0;"><a href="javascript:in_notes_add('.$note_type_en_id.');" class="btn btn-idea save_notes_'.$note_type_en_id.'">ADD</a></td>';

    //File counter:
    $ui .= '<td style="padding: 10px 0 0 0; font-size: 0.85em;"><span id="ideaPadsNewCount' . $note_type_en_id . '" class="hidden"><span id="charNum' . $note_type_en_id . '">0</span>/' . config_var(11073).'</span></td>';

    //YouTube Clip
    $ui .= '<td style="width:42px; padding: 10px 0 0 0;"><a href="javascript:in_notes_insert_string('.$note_type_en_id.', \'https://www.youtube.com/embed/VIDEO_ID_HERE?start=SECOND_HERE&end=SECOND_HERE\');" data-toggle="tooltip" title="Insert YouTube Clip URL" data-placement="top"><span class="icon-block"><i class="fab fa-youtube"></i></span></a></td>';

    //Reference Player
    $ui .= '<td style="width:42px; padding: 10px 0 0 0;"><a href="javascript:in_notes_insert_string('.$note_type_en_id.', \'@\');" data-toggle="tooltip" title="Reference Source @SOURCE_ID" data-placement="top"><span class="icon-block"><i class="far fa-at"></i></span></a></td>';

    //Upload File:
    if(in_array(12359, $en_all_4485[$note_type_en_id]['m_parents'])){
        $ui .= '<td style="width:36px; padding: 10px 0 0 0;">';
        $ui .= '<input class="inputfile hidden" type="file" name="file" id="fileIdeaType'.$note_type_en_id.'" />';
        $ui .= '<label class="file_label_'.$note_type_en_id.'" for="fileIdeaType'.$note_type_en_id.'" data-toggle="tooltip" title="Upload files up to ' . config_var(11063) . 'MB" data-placement="top"><span class="icon-block"><i class="far fa-paperclip"></i></span></label>';
        $ui .= '</td>';
    }


    $ui .= '</tr></table>';


    //Response result:
    $ui .= '<div class="pads_error_'.$note_type_en_id.'"></div>';


    $ui .= '</form>';
    $ui .= '</div>';
    $ui .= '</div>';
    $ui .= '</div>';

    return $ui;
}

function echo_platform_message($en_id){
    $CI =& get_instance();
    $en_all_12687 = $CI->config->item('en_all_12687');
    if(!substr_count($en_all_12687[$en_id]['m_desc'], " | ")){
        //Single message:
        return $en_all_12687[$en_id]['m_desc'];
    } else {
        //Random message:
        $line_messages = explode(" | ", $en_all_12687[$en_id]['m_desc']);
        return $line_messages[rand(0, (count($line_messages) - 1))];
    }
}

function echo_unauthorized_message($superpower_en_id = 0, $push_message = true){

    $CI =& get_instance();
    $en_all_10957 = $CI->config->item('en_all_10957');
    $session_en = superpower_assigned();

    if(!$session_en){

        //Missing Session
        return 'ERROR: Login to continue.';

    } elseif($superpower_en_id>0) {

        //Missing Superpower:
        return 'ERROR: You are missing the required supowerpower of '.( $push_message ? $en_all_10957[$superpower_en_id]['m_name'] : '<b class="montserrat '.extract_icon_color($en_all_10957[$superpower_en_id]['m_icon']).'">'.$en_all_10957[$superpower_en_id]['m_icon'].' '.$en_all_10957[$superpower_en_id]['m_name'].'</b>' ).'';

    }

    return null;

}

function echo_en($en, $is_parent = false, $extra_class = null, $note_controller = false)
{

    $CI =& get_instance();

    if(!isset($en['en_id'])){
        $CI->DISCOVER_model->ln_create(array(
            'ln_content' => 'echo_en() variable missing source',
            'ln_metadata' => $en,
            'ln_type_source_id' => 4246, //Platform Bug Reports
        ));
        return false;
    }

    $session_en = superpower_assigned();
    $en_all_6177 = $CI->config->item('en_all_6177'); //Source Status
    $en_all_2738 = $CI->config->item('en_all_2738');
    $en_all_11028 = $CI->config->item('en_all_11028'); //SOURCEERS LINKS DIRECTION

    $ln_id = (isset($en['ln_id']) ? $en['ln_id'] : 0);
    $is_link_source = ( $ln_id > 0 && in_array($en['ln_type_source_id'], $CI->config->item('en_ids_4592')));
    $is_discover_progress = ( $ln_id > 0 && in_array($en['ln_type_source_id'], $CI->config->item('en_ids_12227')));
    $ui = null;

    $en__parents = $CI->DISCOVER_model->ln_fetch(array(
        'ln_type_source_id IN (' . join(',', $CI->config->item('en_ids_4592')) . ')' => null, //Source Links
        'ln_child_source_id' => $en['en_id'], //This child source
        'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7360')) . ')' => null, //Transaction Status Active
        'en_status_source_id IN (' . join(',', $CI->config->item('en_ids_7358')) . ')' => null, //Source Status Active
    ), array('en_parent'), 0, 0, array('en_name' => 'ASC'));

    $child_links = $CI->DISCOVER_model->ln_fetch(array(
        'ln_parent_source_id' => $en['en_id'],
        'ln_type_source_id IN (' . join(',', $CI->config->item('en_ids_4592')) . ')' => null, //Source Links
        'ln_status_source_id IN (' . join(',', $CI->config->item('en_ids_7359')) . ')' => null, //Transaction Status Public
        'en_status_source_id IN (' . join(',', $CI->config->item('en_ids_7357')) . ')' => null, //Source Status Public
    ), array('en_child'), 0, 0, array(), 'COUNT(en_id) as totals');

    $is_public = in_array($en['en_status_source_id'], $CI->config->item('en_ids_7357'));
    $is_link_published = ( !$ln_id || in_array($en['ln_status_source_id'], $CI->config->item('en_ids_7359')));
    $is_hidden = filter_array($en__parents, 'en_id', '4755');

    if(!$session_en && ($is_hidden || !$is_public || !$is_link_published)){
        //Not logged in, so should only see published:
        return false;
    } elseif($is_hidden && !superpower_active(12701, true)){
        //They don't have the needed superpower:
        return false;
    }

    //ROW
    $ui .= '<div class="list-group-item no-side-padding itemsource en-item object_highlight highlight_en_'.$en['en_id'].' en___' . $en['en_id'] . ( $ln_id > 0 ? ' tr_' . $en['ln_id'].' ' : '' ) . ( $is_parent ? ' parent-source ' : '' ) . ' '. $extra_class  . '" source-id="' . $en['en_id'] . '" en-status="' . $en['en_status_source_id'] . '" tr-id="'.$ln_id.'" ln-status="'.( $ln_id ? $en['ln_status_source_id'] : 0 ).'" is-parent="' . ($is_parent ? 1 : 0) . '">';


    $ui .= '<table class="table table-sm" style="background-color: transparent !important; margin-bottom: 0;"><tr>';

    $ui .= '<td class="MENCHcolumn1">';



    $ui .= '<div class="inline-block">';


    //SOURCE ICON
    $ui .= '<a href="/source/'.$en['en_id'] . '"><span class="icon-block en_ui_icon_' . $en['en_id'] . ' en__icon_'.$en['en_id'].'" en-is-set="'.( strlen($en['en_icon']) > 0 ? 1 : 0 ).'">' . echo_en_icon($en['en_icon']) . '</span></a>';

    //SOURCE NAME
    $ui .= '<a href="/source/'.$en['en_id'] . '" class="title-block title-no-right montserrat '.extract_icon_color($en['en_icon']).'"><span class="en_name_full_' . $en['en_id'] . '">'.$en['en_name'].'</span>'.($child_links[0]['totals'] > 0 ? '<span class="'.superpower_active(12701).'" title="'.number_format($child_links[0]['totals'], 0).'">&nbsp;'.echo_number($child_links[0]['totals']).'</span>' : '').'</a>';

    $ui .= '</div>';




    //Does this source also include a link?
    if ($ln_id > 0) {
        if($is_link_source){

            //SOURCE LINKS:
            $ui .= '<div class="doclear">&nbsp;</div>';

            $ui .= '<span class="message_content ln_content hideIfEmpty ln_content_' . $ln_id . '">' . echo_ln_urls($en['ln_content'] , $en['ln_type_source_id']) . '</span>';

            //For JS editing only (HACK):
            $ui .= '<div class="ln_content_val_' . $ln_id . ' hidden overflowhide">' . $en['ln_content'] . '</div>';

        } elseif($is_discover_progress && strlen($en['ln_content'])){

            //DISCOVER PROGRESS
            $ui .= '<div class="message_content">';
            $ui .= $CI->DISCOVER_model->dispatch_message($en['ln_content']);
            $ui .= '</div>';

        }
    }



    //CHILDREN & PARENTS
    $ui .= '<div class="doclear">&nbsp;</div>';
    $ui .= '<div class="space-content">';


    //SOURCE STATUS
    $ui .= '<span class="en_status_source_id_' . $en['en_id'] . ( $is_public ? ' hidden ' : '' ).'"><span data-toggle="tooltip" data-placement="right" title="'.$en_all_6177[$en['en_status_source_id']]['m_name'].' @'.$en['en_status_source_id'].': '.$en_all_6177[$en['en_status_source_id']]['m_desc'].'">' . $en_all_6177[$en['en_status_source_id']]['m_icon'] . '</span>&nbsp;</span>';

    //LINK
    if ($ln_id > 0) {

        //Link Type Full List:
        $en_all_6186 = $CI->config->item('en_all_6186'); //Transaction Status

        //LINK STATUS
        $ui .= '<span class="ln_status_source_id_' . $ln_id . ( $is_link_published ? ' hidden ' : '' ) .'"><span data-toggle="tooltip" data-placement="right" title="'.$en_all_6186[$en['ln_status_source_id']]['m_name'].' @'.$en['ln_status_source_id'].': '.$en_all_6186[$en['ln_status_source_id']]['m_desc'].'">' . $en_all_6186[$en['ln_status_source_id']]['m_icon'] . '</span>&nbsp;</span>';

        //Show link index
        if($is_link_source && $en['ln_external_id'] > 0){

            //External ID
            if($en['ln_parent_source_id']==6196){
                //Give players the ability to ping Messenger profiles:
                $ui .= '<span class="'.superpower_active(12701).'" data-toggle="tooltip" data-placement="right" title="Link External ID = '.$en['ln_external_id'].' [Messenger Profile]"><a href="/messenger/fetch_profile/'.$en['ln_external_id'].'"><i class="fas fa-project-diagram"></i></a>&nbsp;</span>';
            } else {
                $ui .= '<span class="'.superpower_active(12701).'" data-toggle="tooltip" data-placement="right" title="Link External ID = '.$en['ln_external_id'].'"><i class="fas fa-project-diagram"></i>&nbsp;</span>';
            }

        }
    }



    $ui .= '<span class="'. superpower_active(12706) .'">';

    if($is_link_source){
        //Link Type
        $en_all_4592 = $CI->config->item('en_all_4592');
        $ui .= '<span class="icon-block-img ln_type_' . $ln_id .'" data-toggle="tooltip" data-placement="bottom" title="LINK ID '.$en['ln_id'].' '.$en_all_4592[$en['ln_type_source_id']]['m_name'].' @'.$en['ln_type_source_id'].'">' . $en_all_4592[$en['ln_type_source_id']]['m_icon'] . '</span> ';
    }

    foreach ($en__parents as $en_parent) {
        $ui .= '<span class="icon-block-img en_child_icon_' . $en_parent['en_id'] . '"><a href="/source/' . $en_parent['en_id'] . '" data-toggle="tooltip" title="' . $en_parent['en_name'] . (strlen($en_parent['ln_content']) > 0 ? ' = ' . $en_parent['ln_content'] : '') . '" data-placement="bottom">' . echo_en_icon($en_parent['en_icon']) . '</a></span> ';
    }
    $ui .= ' </span>';

    $ui .= '</div>';



    $ui .= '</td>';







    //IDEA
    $ui .= '<td class="MENCHcolumn3 source">';
    $ui .= echo_coins_count_source(0, $en['en_id']);
    $ui .= '</td>';




    //DISCOVER
    $ui .= '<td class="MENCHcolumn2 discover">';

    //RIGHT EDITING:
    $ui .= '<div class="pull-right inline-block">';
    $ui .= '<div class="pads-editor edit-off">';
    $ui .= '<span class="show-on-hover">';

    if($note_controller){
        //Option to Manage:
        $ui .= '<span class="'.superpower_active(10967).'"><a href="javascript:void(0);" onclick="en_modify_load(' . $en['en_id'] . ',' . $ln_id . ')"><i class="fas fa-pen-square black"></i></a></span>';
    }

    $ui .= '</span>';
    $ui .= '</div>';
    $ui .= '</div>';

    $ui .= echo_coins_count_discover(0, $en['en_id']);
    $ui .= '</td>';





    $ui .= '</tr></table>';
    $ui .= '</div>';

    return $ui;

}

function echo_basic_list_link($m, $url){

    $CI =& get_instance();
    $en_all_6287 = $CI->config->item('en_all_6287'); //MENCH PLUGIN
    $en_all_10957 = $CI->config->item('en_all_10957');

    $ui = '<a href="'.$url.'" class="list-group-item no-side-padding">';


    //Icon
    $ui .= '<span class="icon-block">' . echo_en_icon($m['m_icon']) . '</span>';
    $ui .= '<b class="montserrat '.extract_icon_color($m['m_icon']).'">'.$m['m_name'].'</b>';


    //Needs extra superpowers?
    $superpower_actives = array_intersect($CI->config->item('en_ids_10957'), $m['m_parents']);
    foreach($superpower_actives as $needed_superpower_en_id){
        $ui .= '<span title="Requires '.$en_all_10957[$needed_superpower_en_id]['m_name'].'" data-toggle="tooltip" data-placement="top">&nbsp;'.$en_all_10957[$needed_superpower_en_id]['m_icon'].'</span>';
    }


    //Description
    $ui .= ( strlen($m['m_desc']) ? '&nbsp;'.$m['m_desc'] : '' );


    $ui .= '</a>';

    return $ui;

}

function echo_in_text($cache_en_id, $current_value, $in_ln__id, $is_source, $tabindex = 0, $is_in_title_lg = false){

    $CI =& get_instance();
    $en_all_12112 = $CI->config->item('en_all_12112');
    $current_value = htmlentities($current_value);

    //Define element attributes:
    $attributes = ( $is_source ? '' : 'disabled' ).' tabindex="'.$tabindex.'" old-value="'.$current_value.'" class="form-control dotransparent montserrat inline-block in_update_text text__'.$cache_en_id.'_'.$in_ln__id.' texttype_'.$cache_en_id.($is_in_title_lg?'_lg':'_sm').'" cache_en_id="'.$cache_en_id.'" in_ln__id="'.$in_ln__id.'" ';


    $tooltip_span_start = '<span class="span__'.$cache_en_id.' '.( !$is_source ? 'edit-locked' : '' ).'" '.( !$is_in_title_lg || !$is_source ? 'data-toggle="tooltip" data-placement="top" title="'.$en_all_12112[$cache_en_id]['m_name'].'"' : '').'>';
    $tooltip_span_end = '</span>';


    //Determine ICON
    if($is_in_title_lg){
        //IDEA COIN:
        $icon = '<span class="icon-block title-icon">'.$en_all_12112[4535]['m_icon'].'</span>';
    } elseif(in_array($cache_en_id, $CI->config->item('en_ids_12420'))){
        $icon = '<span class="icon-block">'.$en_all_12112[$cache_en_id]['m_icon'].'</span>';
    } else {
        $icon = null;
    }

    if($is_in_title_lg){

        return $tooltip_span_start.$icon.'<textarea onkeyup="in_title_count()" placeholder="'.$en_all_12112[$cache_en_id]['m_name'].'" '.$attributes.'>'.$current_value.'</textarea>'.$tooltip_span_end;

    } else {

        return $tooltip_span_start.$icon.'<input type="text" placeholder="__" value="'.$current_value.'" '.$attributes.' />'.$tooltip_span_end;

    }
}


function echo_in_dropdown($cache_en_id, $selected_en_id, $btn_class, $is_source, $show_full_name, $in_id = 0, $ln_id = 0){

    $CI =& get_instance();
    $en_all_this = $CI->config->item('en_all_'.$cache_en_id);

    if(!$selected_en_id || !isset($en_all_this[$selected_en_id])){
        return false;
    }

    $en_all_12079 = $CI->config->item('en_all_12079');
    $en_all_4527 = $CI->config->item('en_all_4527');

    //data-toggle="tooltip" data-placement="top" title="'.$en_all_4527[$cache_en_id]['m_name'].'"
    $ui = '<div title="'.$en_all_12079[$cache_en_id]['m_name'].'" data-toggle="tooltip" data-placement="top" class="inline-block">';
    $ui .= '<div class="dropdown inline-block dropd_'.$cache_en_id.'_'.$in_id.'_'.$ln_id.' '.( !$show_full_name ? ' icon-block ' : '' ).'" selected-val="'.$selected_en_id.'">';

    $ui .= '<button type="button" '.( $is_source ? 'class="btn no-left-padding '.( $show_full_name ? 'dropdown-toggle' : 'no-right-padding dropdown-lock' ).' '.$btn_class.'" id="dropdownMenuButton'.$cache_en_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' : 'class="btn '.( !$show_full_name ? 'no-padding' : '' ).' edit-locked  '.$btn_class.'"' ).' >';
    $ui .= '<span class="icon-block">' .$en_all_this[$selected_en_id]['m_icon'].'</span><span class="show-max">'.( $show_full_name ?  $en_all_this[$selected_en_id]['m_name'] : '' ).'</span>';
    $ui .= '</button>';

    $ui .= '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton'.$cache_en_id.'">';

    foreach ($en_all_this as $en_id => $m) {

        $superpower_actives = array_intersect($CI->config->item('en_ids_10957'), $m['m_parents']);
        $is_url_desc = ( substr($m['m_desc'], 0, 1)=='/' );

        $ui .= '<a class="dropdown-item dropi_'.$cache_en_id.'_'.$in_id.'_'.$ln_id.' montserrat optiond_'.$en_id.'_'.$in_id.'_'.$ln_id.' doupper '.( $en_id==$selected_en_id ? ' active ' : ( count($superpower_actives) ? superpower_active(end($superpower_actives)) : '' ) ).'" '.( $is_url_desc ? ( $en_id==$selected_en_id ? 'href="javascript:void();"' : 'href="'.$m['m_desc'].'"' ) : 'href="javascript:void();" new-en-id="'.$en_id.'" onclick="in_update_dropdown('.$cache_en_id.', '.$en_id.', '.$in_id.', '.$ln_id.', '.intval($show_full_name).')"' ).'><span '.( strlen($m['m_desc']) && !$is_url_desc ? 'title="'.$m['m_desc'].'" data-toggle="tooltip" data-placement="right"' : '' ).'><span class="icon-block">'.$m['m_icon'].'</span>'.$m['m_name'].'</span></a>';

    }

    $ui .= '</div>';
    $ui .= '</div>';
    $ui .= '</div>';

    return $ui;
}

function echo_json($array)
{
    header('Content-Type: application/json');
    echo json_encode($array);
    return true;
}


function echo_ordinal_number($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if (($number % 100) >= 11 && ($number % 100) <= 13) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }
}

function echo__s($count, $is_es = 0)
{
    //A cute little function to either display the plural "s" or not based on $count
    return ( intval($count) == 1 ? '' : ($is_es ? 'es' : 's'));
}

