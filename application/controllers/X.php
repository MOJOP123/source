<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class X extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->output->enable_profiler(FALSE);

        date_default_timezone_set(config_var(11079));

    }

    function index(){

        //Load header:
        $sources__11035 = $this->config->item('sources__11035'); //MENCH NAVIGATION
        $this->load->view('header', array(
            'title' => $sources__11035[13210]['m_name'],
        ));
        $this->load->view('x/home');
        $this->load->view('footer');

    }


    function x_list(){

        /*
         *
         * List all Links on reverse chronological order
         * and Display Status for ideas, sources and
         * links.
         *
         * */

        //Load header:
        $sources__11035 = $this->config->item('sources__11035'); //MENCH NAVIGATION

        $this->load->view('header', array(
            'title' => $sources__11035[4341]['m_name'],
        ));
        $this->load->view('x/x');
        $this->load->view('footer');

    }

    function x_load(){

        /*
         * Loads the list of links based on the
         * filters passed on.
         *
         * */

        $filters = unserialize($_POST['link_filters']);
        $joined_by = unserialize($_POST['link_joined_by']);
        $page_num = ( isset($_POST['page_num']) && intval($_POST['page_num'])>=2 ? intval($_POST['page_num']) : 1 );
        $next_page = ($page_num+1);
        $query_offset = (($page_num-1)*config_var(11064));
        $session_source = superpower_assigned();

        $message = '';

        //Fetch links and total link counts:
        $discoveries = $this->X_model->fetch($filters, $joined_by, config_var(11064), $query_offset);
        $discoveries_count = $this->X_model->fetch($filters, $joined_by, 0, 0, array(), 'COUNT(x__id) as total_count');
        $total_items_loaded = ($query_offset+count($discoveries));
        $has_more_links = ($discoveries_count[0]['total_count'] > 0 && $total_items_loaded < $discoveries_count[0]['total_count']);


        //Display filter:
        if($total_items_loaded > 0){
            $message .= '<div class="montserrat discover-info"><span class="icon-block"><i class="fas fa-file-search"></i></span>'.( $has_more_links && $query_offset==0  ? 'FIRST ' : ($query_offset+1).' - ' ) . ( $total_items_loaded >= ($query_offset+1) ?  $total_items_loaded . ' OF ' : '' ) . number_format($discoveries_count[0]['total_count'] , 0) .' INTERACTIONS:</div>';
        }


        if(count($discoveries)>0){

            $message .= '<div class="list-group list-grey">';
            foreach($discoveries as $discovery) {

                $message .= view_interaction($discovery);

                if($session_source && strlen($discovery['x__message'])>0 && strlen($_POST['x__message_search'])>0 && strlen($_POST['x__message_replace'])>0 && substr_count($discovery['x__message'], $_POST['x__message_search'])>0){

                    $new_content = str_replace($_POST['x__message_search'],trim($_POST['x__message_replace']),$discovery['x__message']);

                    $this->X_model->update($discovery['x__id'], array(
                        'x__message' => $new_content,
                    ), $session_source['e__id'], 12360, update_description($discovery['x__message'], $new_content));

                    $message .= '<div class="alert alert-info" role="alert"><i class="fas fa-check-circle"></i> Replaced ['.$_POST['x__message_search'].'] with ['.trim($_POST['x__message_replace']).']</div>';

                }

            }
            $message .= '</div>';

            //Do we have more to show?
            if($has_more_links){
                $message .= '<div id="link_page_'.$next_page.'"><a href="javascript:void(0);" style="margin:10px 0 72px 0;" class="btn btn-discover" onclick="discover_load(link_filters, link_joined_by, '.$next_page.');"><span class="icon-block"><i class="fas fa-plus-circle"></i></span>Page '.$next_page.'</a></div>';
                $message .= '';
            } else {
                $message .= '<div style="margin:10px 0 72px 0;"><span class="icon-block"><i class="far fa-check-circle"></i></span>All '.$discoveries_count[0]['total_count'].' link'.view__s($discoveries_count[0]['total_count']).' have been loaded</div>';

            }

        } else {

            //Show no link warning:
            $message .= '<div class="alert alert-warning" role="alert"><span class="icon-block"><i class="fas fa-exclamation-circle"></i></span>No Links found with the selected filters. Modify filters and try again.</div>';

        }


        return view_json(array(
            'status' => 1,
            'message' => $message,
        ));


    }



    function x_type_preview()
    {

        if (!isset($_POST['x__message']) || !isset($_POST['x__id'])) {
            return view_json(array(
                'status' => 0,
                'message' => 'Missing inputs',
            ));
        }

        //Will Contain every possible Player Link Connector:
        $sources__4592 = $this->config->item('sources__4592');

        //See what this is:
        $detected_x_type = x_detect_type($_POST['x__message']);

        if(!$_POST['x__id'] && !in_array($detected_x_type['x__type'], $this->config->item('sources_id_4537'))){

            return view_json(array(
                'status' => 0,
                'message' => 'Invalid URL',
            ));

        } elseif (!$detected_x_type['status'] && isset($detected_x_type['url_previously_existed']) && $detected_x_type['url_previously_existed']) {

            //See if this is duplicate to either link:
            $e_discoveries = $this->X_model->fetch(array(
                'x__id' => $_POST['x__id'],
                'x__type IN (' . join(',', $this->config->item('sources_id_4537')) . ')' => null, //Player URL Links
            ));

            //Are they both different?
            if (count($e_discoveries) < 1 || ($e_discoveries[0]['x__up'] != $detected_x_type['e_url']['e__id'] && $e_discoveries[0]['x__down'] != $detected_x_type['e_url']['e__id'])) {
                //return error:
                return view_json($detected_x_type);
            }

        }



        return view_json(array(
            'status' => 1,
            'html_ui' => '<b class="montserrat doupper '.extract_icon_color($sources__4592[$detected_x_type['x__type']]['m_icon']).'">' . $sources__4592[$detected_x_type['x__type']]['m_icon'] . ' ' . $sources__4592[$detected_x_type['x__type']]['m_name'] . '</b>',
            'e_link_preview' => ( in_array($detected_x_type['x__type'], $this->config->item('sources_id_12524')) ? '<span class="paddingup inline-block">'.view_x__message($_POST['x__message'], $detected_x_type['x__type']).'</span>' : ''),
        ));

    }




    function x_set_text(){

        //Authenticate Player:
        $session_source = superpower_assigned();
        $sources__12112 = $this->config->item('sources__12112');

        if (!$session_source) {

            return view_json(array(
                'status' => 0,
                'message' => view_unauthorized_message(),
                'original_val' => '',
            ));

        } elseif(!isset($_POST['object__id']) || !isset($_POST['cache_e__id']) || !isset($_POST['field_value'])){

            return view_json(array(
                'status' => 0,
                'message' => 'Missing core variables',
                'original_val' => '',
            ));

        } elseif($_POST['cache_e__id']==4736 /* IDEA TITLE */){

            $ideas = $this->I_model->fetch(array(
                'i__id' => $_POST['object__id'],
                'i__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
            ));
            if(!count($ideas)){
                return view_json(array(
                    'status' => 0,
                    'message' => 'Invalid Idea ID.',
                    'original_val' => '',
                ));
            }

            //Validate Idea Outcome:
            $i__title_validation = i__title_validate($_POST['field_value']);
            if(!$i__title_validation['status']){
                //We had an error, return it:
                return view_json(array_merge($i__title_validation, array(
                    'original_val' => $ideas[0]['i__title'],
                )));
            }


            //All good, go ahead and update:
            $this->I_model->update($_POST['object__id'], array(
                'i__title' => trim($_POST['field_value']),
            ), true, $session_source['e__id']);

            return view_json(array(
                'status' => 1,
            ));

        } elseif($_POST['cache_e__id']==6197 /* SOURCE FULL NAME */){

            $sources = $this->E_model->fetch(array(
                'e__id' => $_POST['object__id'],
                'e__status IN (' . join(',', $this->config->item('sources_id_7358')) . ')' => null, //ACTIVE
            ));
            if(!count($sources)){
                return view_json(array(
                    'status' => 0,
                    'message' => 'Invalid Source ID.',
                    'original_val' => '',
                ));
            }


            $e__title_validate = e__title_validate($_POST['field_value']);
            if(!$e__title_validate['status']){
                return view_json(array_merge($e__title_validate, array(
                    'original_val' => $sources[0]['e__title'],
                )));
            }

            //All good, go ahead and update:
            $this->E_model->update($sources[0]['e__id'], array(
                'e__title' => $e__title_validate['e__title_clean'],
            ), true, $session_source['e__id']);

            //Reset user session data if this data belongs to the logged-in user:
            if ($sources[0]['e__id'] == $session_source['e__id']) {
                //Re-activate Session with new data:
                $sources[0]['e__title'] = $e__title_validate['e__title_clean'];
                $this->E_model->activate_session($sources[0], true);
            }

            return view_json(array(
                'status' => 1,
            ));

        } elseif($_POST['cache_e__id']==4356 /* DISCOVER TIME */){

            $ideas = $this->I_model->fetch(array(
                'i__id' => $_POST['object__id'],
                'i__status IN (' . join(',', $this->config->item('sources_id_7356')) . ')' => null, //ACTIVE
            ));

            if(!count($ideas)){

                return view_json(array(
                    'status' => 0,
                    'message' => 'Invalid Idea ID.',
                    'original_val' => '',
                ));

            } elseif(!is_numeric($_POST['field_value']) || $_POST['field_value'] < 0){

                return view_json(array(
                    'status' => 0,
                    'message' => $sources__12112[$_POST['cache_e__id']]['m_name'].' must be a number greater than zero.',
                    'original_val' => $ideas[0]['i__duration'],
                ));

            } elseif($_POST['field_value'] > config_var(4356)){

                $hours = rtrim(number_format((config_var(4356)/3600), 1), '.0');
                return view_json(array(
                    'status' => 0,
                    'message' => $sources__12112[$_POST['cache_e__id']]['m_name'].' should be less than '.$hours.' Hour'.view__s($hours).', or '.config_var(4356).' Seconds long. You can break down your idea into smaller ideas.',
                    'original_val' => $ideas[0]['i__duration'],
                ));

            } elseif($_POST['field_value'] < config_var(12427)){

                return view_json(array(
                    'status' => 0,
                    'message' => $sources__12112[$_POST['cache_e__id']]['m_name'].' should be at-least '.config_var(12427).' Seconds long. It takes time to discover ideas ;)',
                    'original_val' => $ideas[0]['i__duration'],
                ));

            } else {

                //All good, go ahead and update:
                $this->I_model->update($_POST['object__id'], array(
                    'i__duration' => $_POST['field_value'],
                ), true, $session_source['e__id']);

                return view_json(array(
                    'status' => 1,
                ));

            }

        } elseif($_POST['cache_e__id']==4358 /* DISCOVER MARKS */){

            //Fetch/Validate Link:
            $discoveries = $this->X_model->fetch(array(
                'x__id' => $_POST['object__id'],
                'x__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
                'x__type IN (' . join(',', $this->config->item('sources_id_4486')) . ')' => null, //IDEA LINKS
            ));
            $x__metadata = unserialize($discoveries[0]['x__metadata']);
            if(!$x__metadata){
                $x__metadata = array();
            }

            if(!count($discoveries)){

                return view_json(array(
                    'status' => 0,
                    'message' => 'Invalid Link ID.',
                    'original_val' => '',
                ));

            } elseif(!is_numeric($_POST['field_value']) || fmod($_POST['field_value'], 1)>0 || $_POST['field_value'] < config_var(11056) ||  $_POST['field_value'] > config_var(11057)){

                return view_json(array(
                    'status' => 0,
                    'message' => $sources__12112[$_POST['cache_e__id']]['m_name'].' must be an integer between '.config_var(11056).' and '.config_var(11057).'.',
                    'original_val' => ( isset($x__metadata['tr__assessment_points']) ? $x__metadata['tr__assessment_points'] : 0 ),
                ));

            } else {

                //All good, go ahead and update:
                $this->X_model->update($_POST['object__id'], array(
                    'x__metadata' => array_merge($x__metadata, array(
                        'tr__assessment_points' => intval($_POST['field_value']),
                    )),
                ), $session_source['e__id'], 10663 /* Idea Link updated Marks */, $sources__12112[$_POST['cache_e__id']]['m_name'].' updated'.( isset($x__metadata['tr__assessment_points']) ? ' from [' . $x__metadata['tr__assessment_points']. ']' : '' ).' to [' . $_POST['field_value']. ']');

                return view_json(array(
                    'status' => 1,
                ));

            }

        } elseif($_POST['cache_e__id']==4735 /* UNLOCK MIN SCORE */ || $_POST['cache_e__id']==4739 /* UNLOCK MAX SCORE */){

            //Fetch/Validate Link:
            $discoveries = $this->X_model->fetch(array(
                'x__id' => $_POST['object__id'],
                'x__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
                'x__type IN (' . join(',', $this->config->item('sources_id_4486')) . ')' => null, //IDEA LINKS
            ));
            $x__metadata = unserialize($discoveries[0]['x__metadata']);
            $field_name = ( $_POST['cache_e__id']==4735 ? 'tr__conditional_score_min' : 'tr__conditional_score_max' );

            if(!count($discoveries)){

                return view_json(array(
                    'status' => 0,
                    'message' => 'Invalid Link ID.',
                    'original_val' => '',
                ));

            } elseif(!is_numeric($_POST['field_value']) || fmod($_POST['field_value'], 1)>0 || $_POST['field_value'] < 0 || $_POST['field_value'] > 100){

                return view_json(array(
                    'status' => 0,
                    'message' => $sources__12112[$_POST['cache_e__id']]['m_name'].' must be an integer between 0 and 100.',
                    'original_val' => ( isset($x__metadata[$field_name]) ? $x__metadata[$field_name] : '' ),
                ));

            } else {

                //All good, go ahead and update:
                $this->X_model->update($_POST['object__id'], array(
                    'x__metadata' => array_merge($x__metadata, array(
                        $field_name => intval($_POST['field_value']),
                    )),
                ), $session_source['e__id'], 10664 /* Idea Link updated Score */, $sources__12112[$_POST['cache_e__id']]['m_name'].' updated'.( isset($x__metadata[$field_name]) ? ' from [' . $x__metadata[$field_name].']' : '' ).' to [' . $_POST['field_value'].']');

                return view_json(array(
                    'status' => 1,
                ));

            }

        } else {

            return view_json(array(
                'status' => 0,
                'message' => 'Unknown Update Type ['.$_POST['cache_e__id'].']',
                'original_val' => '',
            ));

        }
    }




    function x_start($i__id){

        //Adds Idea to the Players Discovery

        $session_source = superpower_assigned();
        $sources__11035 = $this->config->item('sources__11035'); //MENCH NAVIGATION

        //Check to see if added to Discovery for logged-in users:
        if(!$session_source){
            return redirect_message('/e/signin/'.$i__id);
        }

        //Add this Idea to their Discovery If not there:
        $i__id_added = $i__id;
        $success_message = null;
        $in_my_discoveries = $this->X_model->i_home($i__id, $session_source);

        if(!$in_my_discoveries){
            $i__id_added = $this->X_model->start($session_source['e__id'], $i__id);
            if($i__id_added){
                $success_message = '<div class="alert alert-info" role="alert"><span class="icon-block">'.$sources__11035[12969]['m_icon'].'</span>Successfully added to '.$sources__11035[12969]['m_name'].'. Continue below:</div>';
            } else {
                //Failed to add to Discovery:
                return redirect_message('/', '<div class="alert alert-danger" role="alert"><span class="icon-block">'.$sources__11035[12969]['m_icon'].'</span>FAILED to add to '.$sources__11035[12969]['m_name'].'.</div>');
            }
        }

        //Go to this newly added idea:
        return redirect_message('/'.$i__id_added, $success_message);

    }

    function x_next($i__id = 0){

        $session_source = superpower_assigned();
        if(!$session_source){
            return redirect_message('/e/signin/');
        }

        if($i__id > 0){

            //Fetch Idea:
            $ideas = $this->I_model->fetch(array(
                'i__id' => $i__id,
            ));

            //Should we check for auto next redirect if empty? Only if this is a selection:
            if($ideas[0]['i__type']==6677){

                //Mark as discover If not previously:
                $discovery_completes = $this->X_model->fetch(array(
                    'x__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
                    'x__type IN (' . join(',', $this->config->item('sources_id_12229')) . ')' => null, //DISCOVER COMPLETE
                    'x__player' => $session_source['e__id'],
                    'x__left' => $ideas[0]['i__id'],
                ));

                if(!count($discovery_completes)){
                    $this->X_model->mark_complete($ideas[0], array(
                        'x__type' => 4559, //DISCOVER MESSAGES
                        'x__player' => $session_source['e__id'],
                        'x__left' => $ideas[0]['i__id'],
                    ));
                }
            }
        }

        //Go to Next Idea:
        $next_i__id = $this->X_model->find_next($session_source['e__id'], $ideas[0]);
        if($next_i__id > 0){
            return redirect_message('/'.$next_i__id.'?previous_discover='.( isset($_GET['previous_discover']) && $_GET['previous_discover']>0 ? $_GET['previous_discover'] : $i__id ));
        } else {
            $sources__11035 = $this->config->item('sources__11035'); //MENCH NAVIGATION
            return redirect_message('/', '<div class="alert alert-info" role="alert"><div><span class="icon-block"><i class="fas fa-check-circle"></i></span>Successfully completed everything in '.$sources__11035[12969]['m_name'].'.</div></div>');
        }

    }




    function x_previous($previous_level_id, $i__id){

        $current_i__id = $previous_level_id;

        //Make sure not a select idea:
        if(!count($this->I_model->fetch(array(
            'i__id' => $current_i__id,
            'i__type IN (' . join(',', $this->config->item('sources_id_7712')) . ')' => null, //SELECT IDEA
        )))){
            //FIND NEXT IDEAS
            foreach($this->X_model->fetch(array(
                'i__status IN (' . join(',', $this->config->item('sources_id_7355')) . ')' => null, //PUBLIC
                'x__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
                'x__type IN (' . join(',', $this->config->item('sources_id_4486')) . ')' => null, //IDEA LINKS
                'x__left' => $previous_level_id,
            ), array('x__right'), 0, 0, array('x__sort' => 'ASC')) as $next_idea){
                if($next_idea['i__id']==$i__id){
                    break;
                } else {
                    $current_i__id = $next_idea['i__id'];
                }
            }
        }

        return redirect_message('/'.$current_i__id);

    }



    function x_coin($i__id)
    {

        /*
         *
         * Enables a Player to DISCOVER a IDEA
         * on the public web
         *
         * */

        if($i__id==config_var(13405)){
            return redirect_message('/');
        }

        //Fetch data:
        $ideas = $this->I_model->fetch(array(
            'i__id' => $i__id,
        ));

        //Make sure we found it:
        if ( count($ideas) < 1) {

            return redirect_message('/', '<div class="alert alert-danger" role="alert"><span class="icon-block"><i class="fas fa-exclamation-circle discover"></i></span>Idea ID ' . $i__id . ' not found</div>');

        } elseif(!in_array($ideas[0]['i__status'], $this->config->item('sources_id_7355') /* PUBLIC */)){

            if(superpower_assigned(10939)){

                //Give them idea access:
                return redirect_message('/~' . $i__id);

            } else {

                //Inform them not published:
                return redirect_message('/', '<div class="alert alert-warning" role="alert"><span class="icon-block"><i class="fas fa-exclamation-circle"></i></span>This idea is not published yet.</div>');

            }

        }

        $this->load->view('header', array(
            'title' => $ideas[0]['i__title'],
        ));

        //Load specific view based on Idea Level:
        $this->load->view('x/layout', array(
            'i_focus' => $ideas[0],
        ));

        $this->load->view('footer');

    }



    function x_upload()
    {

        //TODO: MERGE WITH FUNCTION i_note_file()

        //Authenticate Player:
        $session_source = superpower_assigned();
        if (!$session_source) {

            return view_json(array(
                'status' => 0,
                'message' => view_unauthorized_message(),
            ));

        } elseif (!isset($_POST['i__id'])) {

            return view_json(array(
                'status' => 0,
                'message' => 'Missing IDEA',
            ));

        } elseif (!isset($_POST['upload_type']) || !in_array($_POST['upload_type'], array('file', 'drop'))) {

            return view_json(array(
                'status' => 0,
                'message' => 'Unknown upload type.',
            ));

        } elseif (!isset($_FILES[$_POST['upload_type']]['tmp_name']) || strlen($_FILES[$_POST['upload_type']]['tmp_name']) == 0 || intval($_FILES[$_POST['upload_type']]['size']) == 0) {

            return view_json(array(
                'status' => 0,
                'message' => 'Unknown error 2 while trying to save file.',
            ));

        } elseif ($_FILES[$_POST['upload_type']]['size'] > (config_var(11063) * 1024 * 1024)) {

            return view_json(array(
                'status' => 0,
                'message' => 'File is larger than ' . config_var(11063) . ' MB.',
            ));

        }

        //Validate Idea:
        $ideas = $this->I_model->fetch(array(
            'i__id' => $_POST['i__id'],
            'i__status IN (' . join(',', $this->config->item('sources_id_7355')) . ')' => null, //PUBLIC
        ));
        if(count($ideas)<1){
            return view_json(array(
                'status' => 0,
                'message' => 'Invalid Idea ID',
            ));
        }


        //Attempt to save file locally:
        $file_parts = explode('.', $_FILES[$_POST['upload_type']]["name"]);
        $temp_local = "application/cache/" . md5($file_parts[0] . $_FILES[$_POST['upload_type']]["type"] . $_FILES[$_POST['upload_type']]["size"]) . '.' . $file_parts[(count($file_parts) - 1)];
        move_uploaded_file($_FILES[$_POST['upload_type']]['tmp_name'], $temp_local);


        //Attempt to store in Mench Cloud on Amazon S3:
        if (isset($_FILES[$_POST['upload_type']]['type']) && strlen($_FILES[$_POST['upload_type']]['type']) > 0) {
            $mime = $_FILES[$_POST['upload_type']]['type'];
        } else {
            $mime = mime_content_type($temp_local);
        }

        $cdn_status = upload_to_cdn($temp_local, $session_source['e__id'], $_FILES[$_POST['upload_type']], true, $ideas[0]['i__title']);
        if (!$cdn_status['status']) {
            //Oops something went wrong:
            return view_json($cdn_status);
        }


        //Delete previous answer(s):
        foreach($this->X_model->fetch(array(
            'x__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
            'x__type IN (' . join(',', $this->config->item('sources_id_6255')) . ')' => null, //DISCOVER COIN
            'x__left' => $ideas[0]['i__id'],
            'x__player' => $session_source['e__id'],
        )) as $discovery_progress){
            $this->X_model->update($discovery_progress['x__id'], array(
                'x__status' => 6173, //Interaction Removed
            ), $session_source['e__id'], 12129 /* DISCOVER ANSWER DELETED */);
        }

        //Save new answer:
        $new_message = '@'.$cdn_status['cdn_source']['e__id'];
        $this->X_model->mark_complete($ideas[0], array(
            'x__type' => 12117,
            'x__left' => $ideas[0]['i__id'],
            'x__player' => $session_source['e__id'],
            'x__message' => $new_message,
            'x__up' => $cdn_status['cdn_source']['e__id'],
        ));

        //All good:
        return view_json(array(
            'status' => 1,
            'message' => '<div class="discover-topic"><span class="icon-block">&nbsp;</span>YOUR UPLOAD:</div><div class="previous_answer">'.$this->X_model->message_send($new_message).'</div>',
        ));

    }




    function x_respond(){

        $session_source = superpower_assigned();
        if (!$session_source) {
            return view_json(array(
                'status' => 0,
                'message' => view_unauthorized_message(),
            ));
        } elseif (!isset($_POST['i__id']) || !intval($_POST['i__id'])) {
            return view_json(array(
                'status' => 0,
                'message' => 'Missing idea ID.',
            ));
        } elseif (!isset($_POST['x_respond']) || !strlen($_POST['x_respond'])) {
            return view_json(array(
                'status' => 0,
                'message' => 'Missing text answer.',
            ));
        }

        //Validate/Fetch idea:
        $ideas = $this->I_model->fetch(array(
            'i__id' => $_POST['i__id'],
            'i__status IN (' . join(',', $this->config->item('sources_id_7355')) . ')' => null, //PUBLIC
        ));
        if(count($ideas) < 1){
            return view_json(array(
                'status' => 0,
                'message' => 'Idea not published.',
            ));
        }

        //Delete previous answer(s):
        foreach($this->X_model->fetch(array(
            'x__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
            'x__type IN (' . join(',', $this->config->item('sources_id_6255')) . ')' => null, //DISCOVER COIN
            'x__left' => $ideas[0]['i__id'],
            'x__player' => $session_source['e__id'],
        )) as $discovery_progress){
            $this->X_model->update($discovery_progress['x__id'], array(
                'x__status' => 6173, //Interaction Removed
            ), $session_source['e__id'], 12129 /* DISCOVER ANSWER DELETED */);
        }

        //Save new answer:
        $this->X_model->mark_complete($ideas[0], array(
            'x__type' => 6144,
            'x__left' => $ideas[0]['i__id'],
            'x__player' => $session_source['e__id'],
            'x__message' => $_POST['x_respond'],
        ));

        //All good:
        return view_json(array(
            'status' => 1,
            'message' => 'Answer Saved',
        ));

    }


    function x_answer(){

        $session_source = superpower_assigned();
        if (!$session_source) {
            return view_json(array(
                'status' => 0,
                'message' => view_unauthorized_message(),
            ));
        } elseif (!isset($_POST['i_loaded_id'])) {
            return view_json(array(
                'status' => 0,
                'message' => 'Missing idea id.',
            ));
        } elseif (!isset($_POST['answered_ideas']) || !is_array($_POST['answered_ideas']) || !count($_POST['answered_ideas'])) {
            return view_json(array(
                'status' => 0,
                'message' => 'Select an answer',
            ));
        }

        //Save answer:
        return view_json($this->X_model->answer($session_source['e__id'], $_POST['i_loaded_id'], $_POST['answered_ideas']));

    }




    function x_clear_coins(){

        $session_source = superpower_assigned(null, true);

        //Fetch their current progress links:
        $progress_links = $this->X_model->fetch(array(
            'x__status IN (' . join(',', $this->config->item('sources_id_7360')) . ')' => null, //ACTIVE
            'x__type IN (' . join(',', $this->config->item('sources_id_12227')) . ')' => null,
            'x__player' => $session_source['e__id'],
        ), array(), 0);

        if(count($progress_links) > 0){

            //Yes they did have some:
            $message = 'Removed '.count($progress_links).' idea'.view__s(count($progress_links));

            //Log link:
            $clear_all_link = $this->X_model->create(array(
                'x__message' => $message,
                'x__type' => 6415,
                'x__player' => $session_source['e__id'],
            ));

            //Delete all progressions:
            foreach($progress_links as $progress_link){
                $this->X_model->update($progress_link['x__id'], array(
                    'x__status' => 6173, //Interaction Removed
                    'x__reference' => $clear_all_link['x__id'], //To indicate when it was deleted
                ), $session_source['e__id'], 6415 /* Reset All Discoveries */);
            }

        } else {

            //Nothing to do:
            $message = 'Nothing found to be removed';

        }

        //Show basic UI for now:
        return redirect_message('/', '<div class="alert alert-info" role="alert"><span class="icon-block"><i class="fas fa-trash-alt"></i></span>'.$message.'</div>');

    }


    function i_save(){

        //See if we need to add or remove a highlight:
        //Authenticate Player:
        $session_source = superpower_assigned();
        if (!$session_source) {

            return view_json(array(
                'status' => 0,
                'message' => view_unauthorized_message(),
            ));

        } elseif (!isset($_POST['i__id'])) {

            return view_json(array(
                'status' => 0,
                'message' => 'Missing Idea ID',
            ));

        }

        $ideas = $this->I_model->fetch(array(
            'i__id' => $_POST['i__id'],
            'i__status IN (' . join(',', $this->config->item('sources_id_7355')) . ')' => null, //PUBLIC
        ));
        if (!count($ideas)) {
            return view_json(array(
                'status' => 0,
                'message' => 'Invalid Idea ID',
            ));
        }

        //First try to remove:
        $removed = 0;
        foreach($this->X_model->fetch(array(
            'x__up' => $session_source['e__id'],
            'x__right' => $_POST['i__id'],
            'x__type' => 12896, //SAVED
            'x__status IN (' . join(',', $this->config->item('sources_id_7359')) . ')' => null, //PUBLIC
        )) as $remove_saved){
            $removed++;
            $this->X_model->update($remove_saved['x__id'], array(
                'x__status' => 6173, //Interaction Removed
            ), $session_source['e__id'], 12906 /* UNSAVED */);
        }

        //Need to add?
        if(!$removed){
            //Then we must add:
            $this->X_model->create(array(
                'x__player' => $session_source['e__id'],
                'x__up' => $session_source['e__id'],
                'x__message' => '@'.$session_source['e__id'],
                'x__right' => $_POST['i__id'],
                'x__type' => 12896, //SAVED
            ));
        }

        //All Good:
        return view_json(array(
            'status' => 1,
        ));

    }



    function x_remove(){

        /*
         *
         * When users indicate they want to stop
         * a IDEA this function saves the changes
         * necessary and delete the idea from their
         * Discoveries.
         *
         * */

        $session_source = superpower_assigned();

        if (!$session_source) {
            return view_json(array(
                'status' => 0,
                'message' => view_unauthorized_message(),
            ));
        } elseif (!isset($_POST['i__id']) || intval($_POST['i__id']) < 1) {
            return view_json(array(
                'status' => 0,
                'message' => 'Missing idea ID',
            ));
        } elseif (!isset($_POST['x__type']) || !in_array($_POST['x__type'], $this->config->item('sources_id_13414'))) {
            return view_json(array(
                'status' => 0,
                'message' => 'Invalid Interaction Type',
            ));
        }


        //Call function to delete form Discoveries:
        if($_POST['x__type']==6155){
            //Delete Discovery
            $delete_result = $this->X_model->delete($session_source['e__id'], $_POST['i__id'], $_POST['x__type']);
        } elseif($_POST['x__type']==13415){
            //Delete IDEAS
            $delete_result = $this->I_model->delete($session_source['e__id'], $_POST['i__id'], $_POST['x__type']);
        }

        if(!$delete_result['status']){
            return view_json($delete_result);
        } else {
            return view_json(array(
                'status' => 1,
            ));
        }
    }





    function x_sort()
    {
        /*
         *
         * Saves the order of discover ideas based on
         * user preferences.
         *
         * */

        $session_source = superpower_assigned();

        if (!$session_source) {
            return view_json(array(
                'status' => 0,
                'message' => view_unauthorized_message(),
            ));
        } elseif (!isset($_POST['new_x_order']) || !is_array($_POST['new_x_order']) || count($_POST['new_x_order']) < 1) {
            return view_json(array(
                'status' => 0,
                'message' => 'Missing sorting ideas',
            ));
        } elseif (!isset($_POST['x__type']) || !in_array($_POST['x__type'], $this->config->item('sources_id_13413'))) {
            return view_json(array(
                'status' => 0,
                'message' => 'Invalid Interaction Type',
            ));
        }

        //Update the order of their Discoveries:
        $results = array();
        foreach($_POST['new_x_order'] as $x__sort => $x__id){
            if(intval($x__id) > 0 && intval($x__sort) > 0){
                //Update order of this link:
                $results[$x__sort] = $this->X_model->update(intval($x__id), array(
                    'x__sort' => $x__sort,
                ), $session_source['e__id'], intval($_POST['x__type']));
            }
        }

        //All good:
        return view_json(array(
            'status' => 1,
            'message' => count($_POST['new_x_order']).' Ideas Sorted',
        ));
    }


}