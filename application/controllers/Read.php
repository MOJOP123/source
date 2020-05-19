<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Read extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->output->enable_profiler(FALSE);

        date_default_timezone_set(config_var(11079));

    }


    function index(){

        //My Reads
        $session_en = superpower_assigned(null, true);
        $en_all_11035 = $this->config->item('en_all_11035'); //MENCH NAVIGATION

        //Log home View:
        $this->TRANSACTION_model->create(array(
            'ln_type_source_id' => 4283, //Opened Reads
            'ln_creator_source_id' => $session_en['en_id'],
        ));

        $this->load->view('header', array(
            'title' => $en_all_11035[12969]['m_name'],
        ));
        $this->load->view('read/read_home', array(
            'session_en' => $session_en,
        ));
        $this->load->view('footer');

    }



    function saved(){

        $session_en = superpower_assigned(null, true);
        $en_all_11035 = $this->config->item('en_all_11035'); //MENCH NAVIGATION
        $this->load->view('header', array(
            'title' => $en_all_11035[12896]['m_name'],
        ));
        $this->load->view('read/read_saved', array(
            'session_en' => $session_en,
        ));
        $this->load->view('footer');

    }


    function start($in_id){

        //Adds Idea to the Players Reads

        $session_en = superpower_assigned();

        //Check to see if added to Reads for logged-in users:
        if(!$session_en){
            return redirect_message('/source/sign/'.$in_id);
        }

        //Add this Idea to their Reads If not already there:
        $success_message = null;
        $read_in_home = $this->READ_model->in_home($in_id, $session_en);
        if(!$read_in_home){
            if($this->READ_model->start($session_en['en_id'], $in_id)){
                $success_message = '<div class="alert alert-info" role="alert"><span class="icon-block"><i class="fas fa-check-circle"></i></span>Successfully added to your Reads</div>';
            } else {
                //Failed to add to Reads:
                return redirect_message('/read', '<div class="alert alert-danger" role="alert"><span class="icon-block"><i class="fas fa-exclamation-circle read"></i></span>Failed to add idea to your Reads.</div>');
            }
        }

        //Go to this newly added idea:
        return redirect_message('/'.$in_id, $success_message);

    }

    function next($in_id = 0){

        $append_url = '?previous_read='.( isset($_GET['previous_read']) && $_GET['previous_read']>0 ? $_GET['previous_read'] : $in_id );
        $session_en = superpower_assigned();
        if(!$session_en){
            return redirect_message('/source/sign');
        }

        if($in_id > 0){

            //Fetch Idea:
            $ins = $this->IDEA_model->fetch(array(
                'in_id' => $in_id,
            ));


            //Should we check for auto next redirect if empty? Only if this is a selection:
            if($ins[0]['in_type_source_id']==6677){

                //Mark as read If not previously:
                $read_completes = $this->TRANSACTION_model->fetch(array(
                    'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //PUBLIC
                    'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_12229')) . ')' => null, //READ COMPLETE
                    'ln_creator_source_id' => $session_en['en_id'],
                    'ln_previous_idea_id' => $ins[0]['in_id'],
                ));

                if(!count($read_completes)){
                    $this->READ_model->is_complete($ins[0], array(
                        'ln_type_source_id' => 4559, //READ MESSAGES
                        'ln_creator_source_id' => $session_en['en_id'],
                        'ln_previous_idea_id' => $ins[0]['in_id'],
                    ));
                }

            }


            //Find next Idea based on source's Reads:
            $next_in_id = $this->READ_model->find_next($session_en['en_id'], $ins[0]);
            if($next_in_id > 0){
                return redirect_message('/'.$next_in_id.$append_url);
            } else {
                $next_in_id = $this->READ_model->find_next_go($session_en['en_id']);
                if($next_in_id > 0){
                    return redirect_message('/'.$next_in_id.$append_url);
                } else {
                    return redirect_message('/', '<div class="alert alert-info" role="alert"><div><span class="icon-block"><i class="fas fa-check-circle"></i></span>Successfully readed your entire list.</div></div>');
                }
            }

        } else {

            //Find the next idea in the Reads:
            $next_in_id = $this->READ_model->find_next_go($session_en['en_id']);
            if($next_in_id > 0){
                return redirect_message('/'.$next_in_id.$append_url);
            } else {
                return redirect_message('/', '<div class="alert alert-info" role="alert"><div><span class="icon-block"><i class="fas fa-check-circle"></i></span>Successfully readed your entire list.</div></div>');
            }

        }
    }

    function previous($previous_level_id, $in_id){

        $current_in_id = $previous_level_id;

        //Make sure not a select idea:
        if(!count($this->IDEA_model->fetch(array(
            'in_id' => $current_in_id,
            'in_type_source_id IN (' . join(',', $this->config->item('en_ids_7712')) . ')' => null, //SELECT IDEA
        )))){
            //FIND NEXT IDEAS
            foreach($this->TRANSACTION_model->fetch(array(
                'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //PUBLIC
                'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //PUBLIC
                'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_4486')) . ')' => null, //IDEA LINKS
                'ln_previous_idea_id' => $previous_level_id,
            ), array('in_next'), 0, 0, array('ln_order' => 'ASC')) as $in_next){
                if($in_next['in_id']==$in_id){
                    break;
                } else {
                    $current_in_id = $in_next['in_id'];
                }
            }
        }

        return redirect_message('/'.$current_in_id);

    }



    function read_coin($in_id = 0)
    {

        /*
         *
         * Enables a Player to READ a IDEA
         * on the public web
         *
         * */

        //Fetch user session:
        $session_en = superpower_assigned();
        $primary_in_id = config_var(12156);

        if($in_id > 0 && $in_id==$primary_in_id){
            return redirect_message('/');
        } elseif(!$in_id){
            //Load the Starting Idea:
            $in_id = $primary_in_id;
        }

        //Fetch data:
        $ins = $this->IDEA_model->fetch(array(
            'in_id' => $in_id,
        ));

        //Make sure we found it:
        if ( count($ins) < 1) {
            return redirect_message('/', '<div class="alert alert-danger" role="alert"><span class="icon-block"><i class="fas fa-exclamation-circle read"></i></span>Idea #' . $in_id . ' not found</div>');
        } elseif(!in_array($ins[0]['in_status_source_id'], $this->config->item('en_ids_7355') /* PUBLIC */)){

            if(superpower_assigned(10939)){
                //Give them idea access:
                return redirect_message('/idea/' . $in_id);
            } else {
                //Inform them not published:
                return redirect_message('/', '<div class="alert alert-warning" role="alert"><span class="icon-block"><i class="fas fa-exclamation-circle"></i></span>Cannot read this idea because it\'s not published yet.</div>');
            }

        }

        $this->load->view('header', array(
            'title' => $ins[0]['in_title'],
            'in' => $ins[0],
        ));

        //Load specific view based on Idea Level:
        $this->load->view('read/read_coin', array(
            'in' => $ins[0],
            'session_en' => $session_en,
        ));

        $this->load->view('footer');

    }



    function read_file_upload()
    {

        //TODO: MERGE WITH FUNCTION in_notes_create_upload()

        //Authenticate Player:
        $session_en = superpower_assigned();
        if (!$session_en) {

            return echo_json(array(
                'status' => 0,
                'message' => echo_unauthorized_message(),
            ));

        } elseif (!isset($_POST['in_id'])) {

            return echo_json(array(
                'status' => 0,
                'message' => 'Missing IDEA',
            ));

        } elseif (!isset($_POST['upload_type']) || !in_array($_POST['upload_type'], array('file', 'drop'))) {

            return echo_json(array(
                'status' => 0,
                'message' => 'Unknown upload type.',
            ));

        } elseif (!isset($_FILES[$_POST['upload_type']]['tmp_name']) || strlen($_FILES[$_POST['upload_type']]['tmp_name']) == 0 || intval($_FILES[$_POST['upload_type']]['size']) == 0) {

            return echo_json(array(
                'status' => 0,
                'message' => 'Unknown error 2 while trying to save file.',
            ));

        } elseif ($_FILES[$_POST['upload_type']]['size'] > (config_var(11063) * 1024 * 1024)) {

            return echo_json(array(
                'status' => 0,
                'message' => 'File is larger than ' . config_var(11063) . ' MB.',
            ));

        }

        //Validate Idea:
        $ins = $this->IDEA_model->fetch(array(
            'in_id' => $_POST['in_id'],
            'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //PUBLIC
        ));
        if(count($ins)<1){
            return echo_json(array(
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

        $cdn_status = upload_to_cdn($temp_local, $session_en['en_id'], $_FILES[$_POST['upload_type']], true, $ins[0]['in_title']);
        if (!$cdn_status['status']) {
            //Oops something went wrong:
            return echo_json($cdn_status);
        }


        //Delete previous answer(s):
        foreach($this->TRANSACTION_model->fetch(array(
            'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //PUBLIC
            'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //READ COIN
            'ln_previous_idea_id' => $ins[0]['in_id'],
            'ln_creator_source_id' => $session_en['en_id'],
        )) as $read_progress){
            $this->TRANSACTION_model->update($read_progress['ln_id'], array(
                'ln_status_source_id' => 6173, //Transaction Deleted
            ), $session_en['en_id'], 12129 /* READ ANSWER DELETED */);
        }

        //Save new answer:
        $new_message = '@'.$cdn_status['cdn_en']['en_id'];
        $this->READ_model->is_complete($ins[0], array(
            'ln_type_source_id' => 12117,
            'ln_previous_idea_id' => $ins[0]['in_id'],
            'ln_creator_source_id' => $session_en['en_id'],
            'ln_content' => $new_message,
            'ln_profile_source_id' => $cdn_status['cdn_en']['en_id'],
        ));

        //All good:
        return echo_json(array(
            'status' => 1,
            'message' => '<div class="read-topic"><span class="icon-block">&nbsp;</span>YOUR UPLOAD:</div><div class="previous_answer">'.$this->READ_model->send_message($new_message).'</div>',
        ));

    }




    function read_text_answer(){

        $session_en = superpower_assigned();
        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => echo_unauthorized_message(),
            ));
        } elseif (!isset($_POST['in_id']) || !intval($_POST['in_id'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing idea ID.',
            ));
        } elseif (!isset($_POST['read_text_answer']) || !strlen($_POST['read_text_answer'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing text answer.',
            ));
        }

        //Validate/Fetch idea:
        $ins = $this->IDEA_model->fetch(array(
            'in_id' => $_POST['in_id'],
            'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //PUBLIC
        ));
        if(count($ins) < 1){
            return echo_json(array(
                'status' => 0,
                'message' => 'Idea not published.',
            ));
        }

        //Delete previous answer(s):
        foreach($this->TRANSACTION_model->fetch(array(
            'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //PUBLIC
            'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_6255')) . ')' => null, //READ COIN
            'ln_previous_idea_id' => $ins[0]['in_id'],
            'ln_creator_source_id' => $session_en['en_id'],
        )) as $read_progress){
            $this->TRANSACTION_model->update($read_progress['ln_id'], array(
                'ln_status_source_id' => 6173, //Transaction Deleted
            ), $session_en['en_id'], 12129 /* READ ANSWER DELETED */);
        }

        //Save new answer:
        $this->READ_model->is_complete($ins[0], array(
            'ln_type_source_id' => 6144,
            'ln_previous_idea_id' => $ins[0]['in_id'],
            'ln_creator_source_id' => $session_en['en_id'],
            'ln_content' => $_POST['read_text_answer'],
        ));

        //All good:
        return echo_json(array(
            'status' => 1,
            'message' => 'Answer Saved',
        ));

    }


    function read_answer(){

        $session_en = superpower_assigned();
        if (!$session_en) {
            return echo_json(array(
                'status' => 0,
                'message' => echo_unauthorized_message(),
            ));
        } elseif (!isset($_POST['in_loaded_id'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing idea id.',
            ));
        } elseif (!isset($_POST['answered_ins']) || !is_array($_POST['answered_ins']) || !count($_POST['answered_ins'])) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Select an answer',
            ));
        }

        //Save answer:
        return echo_json($this->READ_model->answer($session_en['en_id'], $_POST['in_loaded_id'], $_POST['answered_ins']));

    }




    function read_coins_remove_all($en_id, $timestamp, $secret_key){

        if($secret_key != md5($en_id . $this->config->item('cred_password_salt') . $timestamp)){
            die('Invalid Secret Key');
        }

        //Fetch their current progress links:
        $progress_links = $this->TRANSACTION_model->fetch(array(
            'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7360')) . ')' => null, //ACTIVE
            'ln_type_source_id IN (' . join(',', $this->config->item('en_ids_12227')) . ')' => null,
            'ln_creator_source_id' => $en_id,
        ), array(), 0);

        if(count($progress_links) > 0){

            //Yes they did have some:
            $message = 'Removed '.count($progress_links).' idea'.echo__s(count($progress_links)).' from your Reads.';

            //Log link:
            $clear_all_link = $this->TRANSACTION_model->create(array(
                'ln_content' => $message,
                'ln_type_source_id' => 6415, //Reads Reset Reads
                'ln_creator_source_id' => $en_id,
            ));

            //Delete all progressions:
            foreach($progress_links as $progress_link){
                $this->TRANSACTION_model->update($progress_link['ln_id'], array(
                    'ln_status_source_id' => 6173, //Transaction Deleted
                    'ln_parent_transaction_id' => $clear_all_link['ln_id'], //To indicate when it was deleted
                ), $en_id, 6415 /* User Cleared Reads */);
            }

        } else {

            //Nothing to do:
            $message = 'Your Reads was empty as there was nothing to delete';

        }

        //Show basic UI for now:
        return redirect_message('/read', '<div class="alert alert-info" role="alert"><span class="icon-block"><i class="fas fa-trash-alt"></i></span>'.$message.'</div>');

    }


    function read_toggle_saved(){

        //See if we need to add or remove a highlight:
        //Authenticate Player:
        $session_en = superpower_assigned();
        if (!$session_en) {

            return echo_json(array(
                'status' => 0,
                'message' => echo_unauthorized_message(),
            ));

        } elseif (!isset($_POST['in_id'])) {

            return echo_json(array(
                'status' => 0,
                'message' => 'Missing Idea ID',
            ));

        }

        $ins = $this->IDEA_model->fetch(array(
            'in_id' => $_POST['in_id'],
            'in_status_source_id IN (' . join(',', $this->config->item('en_ids_7355')) . ')' => null, //PUBLIC
        ));
        if (!count($ins)) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid Idea ID',
            ));
        }

        //First try to remove:
        $removed = 0;
        foreach($this->TRANSACTION_model->fetch(array(
            'ln_profile_source_id' => $session_en['en_id'],
            'ln_next_idea_id' => $_POST['in_id'],
            'ln_type_source_id' => 12896, //SAVED
            'ln_status_source_id IN (' . join(',', $this->config->item('en_ids_7359')) . ')' => null, //PUBLIC
        )) as $remove_saved){
            $removed++;
            $this->TRANSACTION_model->update($remove_saved['ln_id'], array(
                'ln_status_source_id' => 6173, //Transaction Deleted
            ), $session_en['en_id'], 12906 /* UNSAVED */);
        }

        //Need to add?
        if(!$removed){
            //Then we must add:
            $this->TRANSACTION_model->create(array(
                'ln_creator_source_id' => $session_en['en_id'],
                'ln_profile_source_id' => $session_en['en_id'],
                'ln_content' => '@'.$session_en['en_id'],
                'ln_next_idea_id' => $_POST['in_id'],
                'ln_type_source_id' => 12896, //SAVED
            ));
        }

        //All Good:
        return echo_json(array(
            'status' => 1,
        ));

    }



    function read_remove_item(){

        /*
         *
         * When users indicate they want to stop
         * a IDEA this function saves the changes
         * necessary and delete the idea from their
         * Reads.
         *
         * */


        if (!isset($_POST['js_pl_id']) || intval($_POST['js_pl_id']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid player ID',
            ));
        } elseif (!isset($_POST['in_id']) || intval($_POST['in_id']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing idea ID',
            ));
        }

        //Call function to delete form Reads:
        $delete_result = $this->READ_model->delete($_POST['js_pl_id'], $_POST['in_id'], 6155); //REMOVED BOOKMARK

        if(!$delete_result['status']){
            return echo_json($delete_result);
        } else {
            return echo_json(array(
                'status' => 1,
            ));
        }
    }





    function read_sort_save()
    {
        /*
         *
         * Saves the order of Reads ideas based on
         * user preferences.
         *
         * */

        if (!isset($_POST['js_pl_id']) || intval($_POST['js_pl_id']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Invalid player ID',
            ));
        } elseif (!isset($_POST['new_read_order']) || !is_array($_POST['new_read_order']) || count($_POST['new_read_order']) < 1) {
            return echo_json(array(
                'status' => 0,
                'message' => 'Missing sorting ideas',
            ));
        }

        //Update the order of their Reads:
        $results = array();
        foreach($_POST['new_read_order'] as $ln_order => $ln_id){
            if(intval($ln_id) > 0 && intval($ln_order) > 0){
                //Update order of this link:
                $results[$ln_order] = $this->TRANSACTION_model->update(intval($ln_id), array(
                    'ln_order' => $ln_order,
                ), $_POST['js_pl_id'], 6132 /* Ideas Ordered by User */);
            }
        }

        //All good:
        return echo_json(array(
            'status' => 1,
            'message' => count($_POST['new_read_order']).' Ideas Sorted',
        ));
    }


}