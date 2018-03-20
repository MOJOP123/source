<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My extends CI_Controller {
    
    //This controller is usually accessed via the /my/ URL prefix via the Messenger Bot
    
	function __construct() {
		parent::__construct();
		
		//Load our buddies:
		$this->output->enable_profiler(FALSE);
	}

	function index(){
	    //Nothing here:
	    header( 'Location: /');
	}

    function ping(){
        echo_json(array('status'=>'success'));
    }

	function webview_video($i_id){

        if($i_id>0){
            $messages = $this->Db_model->i_fetch(array(
                'i_id' => $i_id,
                'i_status >' => 0, //Not deleted
            ));
        }

        if(isset($messages[0]) && strlen($messages[0]['i_url'])>0 && in_array($messages[0]['i_media_type'],array('text','video'))){

            if($messages[0]['i_media_type']=='video'){
                //Show video
                echo '<div>'.format_e_message('/attach '.$messages[0]['i_media_type'].':'.$messages[0]['i_url']).'</div>';
            } else {
                //Show embed video:
                echo detect_embed_video($messages[0]['i_url'],$messages[0]['i_message']);
            }

        } else {

            $this->load->view('front/shared/p_header' , array(
                'title' => 'Watch Online Video',
            ));
            $this->load->view('front/error_message' , array(
                'error' => 'Invalid Message ID, likely because message has been deleted.',
            ));
            $this->load->view('front/shared/p_footer');
        }
    }

	function load_url($i_id){

	    //Loads the URL:
	    if($i_id>0){
	        $messages = $this->Db_model->i_fetch(array(
	            'i_id' => $i_id,
	            'i_status >' => 0, //Not deleted
	        ));
	    }

        if(isset($messages[0]) && $messages[0]['i_media_type']=='text' && strlen($messages[0]['i_url'])>0){

            //Is this an embed video?
            $embed_html = detect_embed_video($messages[0]['i_url'],$messages[0]['i_message']);

            if(!$embed_html){
                //Now redirect:
                header('Location: '.$messages[0]['i_url']);
            } else {
                $this->load->view('front/shared/p_header' , array(
                    'title' => 'Watch Online Video',
                ));
                $this->load->view('front/embed_video' , array(
                    'embed_html' => $embed_html,
                ));
                $this->load->view('front/shared/p_footer');
            }

        } else {

            $this->load->view('front/shared/p_header' , array(
                'title' => 'Watch Online Video',
            ));
            $this->load->view('front/error_message' , array(
                'error' => 'Invalid Message ID, likely because message has been deleted.',
            ));
            $this->load->view('front/shared/p_footer');
        }
	}
	
	function display_account(){
	    //echo '<p class="p_footer"><img src="'.$admissions[0]['u_image_url'].'" class="mini-image" /> '.$admissions[0]['u_fname'].' '.$admissions[0]['u_lname'].'</p>';
	}

	function account(){
	    //Load apply page:
	    $data = array(
	        'title' => '⚙My Account',
	    );
	    $this->load->view('front/shared/p_header' , $data);
	    $this->load->view('front/student/my_account' , $data);
	    $this->load->view('front/shared/p_footer');
	}

    function actionplan($b_id=null,$c_id=null){
        //Load apply page:
        $data = array(
            'title' => '🚩 Action Plan',
            'b_id' => $b_id,
            'c_id' => $c_id,
        );
        $this->load->view('front/shared/p_header' , $data);
        $this->load->view('front/student/actionplan_frame' , $data);
        $this->load->view('front/shared/p_footer');
    }

    function reset_pass(){
        $data = array(
            'title' => 'Password Reset',
        );
        $this->load->view('front/shared/p_header' , $data);
        $this->load->view('front/student/password_reset');
        $this->load->view('front/shared/p_footer');
    }

    function classmates(){
        //Load apply page:
        $data = array(
            'title' => '👥 Classmates',
        );
        $this->load->view('front/shared/p_header' , $data);
        $this->load->view('front/student/classmates_frame' , $data);
        $this->load->view('front/shared/p_footer');
    }

    function display_actionplan($ru_fp_psid,$b_id=0,$c_id=0){

        $uadmission = array();
	    if(!$ru_fp_psid){
            $uadmission = $this->session->userdata('uadmission');
        }

        //Fetch Bootcamps for this user:
        if(!$ru_fp_psid && count($uadmission)<1){
	        //There is an issue here!
	        die('<div class="alert alert-danger" role="alert">Invalid Credentials</div>');
	    } elseif(count($uadmission)<1 && !is_dev() && isset($_GET['sr']) && !parse_signed_request($_GET['sr'])){
	        die('<div class="alert alert-danger" role="alert">Unable to authenticate your origin.</div>');
	    }

        //Set admission filters:
        $admission_filters = array(
            'ru.ru_status >=' => 4, //Admitted
            'r.r_status >=' => 1, //Open for Admission or Higher
        );

	    //Define user identifier:
        if(count($uadmission)>0 && $uadmission['u_id']>0){
            $admission_filters['u.u_id'] = $uadmission['u_id'];
        } else {
            $admission_filters['ru.ru_fp_psid'] = $ru_fp_psid;
        }

        //Fetch all their admissions:
	    if($b_id>0){
	        //Enhance our search and make it specific to this $b_id:
            $admission_filters['r.r_b_id'] = $b_id;
        }


        $admissions = $this->Db_model->remix_admissions($admission_filters);
        $active_admission = filter_active_admission($admissions); //We'd need to see which admission to load now

        if(!$active_admission){

            /*
            $this->Db_model->e_create(array(
                'e_json' => array(
                    'ru_fp_psid' => $ru_fp_psid,
                    'b_id' => $b_id,
                    'c_id' => $c_id,
                    'admission_filters' => $admission_filters,
                    'admissions' => $admissions,
                    'active_admission' => $active_admission,
                ),
                'e_type_id' => 9, //Support Needed
                'e_message' => 'Student failed to access the Action Plan',
                'e_b_id' => $b_id,
                'e_c_id' => $c_id,
            ));
            */

            //Show Error:
            die('<div class="alert alert-danger" role="alert">You have not joined any Bootcamps yet</div>');
        }

	    
	    if(!$b_id || !$c_id){

            //Log Engagement for opening the Action Plan, which happens without $b_id & $c_id
            $this->Db_model->e_create(array(
                'e_initiator_u_id' => $active_admission['u_id'],
                'e_json' => $admissions,
                'e_type_id' => 32, //actionplan Opened
                'e_b_id' => $active_admission['b_id'],
                'e_r_id' => $active_admission['r_id'],
                'e_c_id' => $active_admission['c_id'],
            ));

            //Reload with specific directions:
            $this->display_actionplan($ru_fp_psid,$active_admission['b_id'],$active_admission['c_id']);

            //Reload this function, this time with specific instructions on what to load:
            return true;
	    }


        //Fetch full Bootcamp/Class data for this:
        $bs = fetch_action_plan_copy($b_id,$active_admission['r_id']);
        $class = $bs[0]['this_class'];

        //Fetch intent relative to the Bootcamp by doing an array search:
        $view_data = extract_level( $bs[0] , $c_id );

        if($view_data){

            //Append more data:
            $view_data['class'] = $class;
            $view_data['admission'] = $active_admission;
            $view_data['us_data'] = $this->Db_model->us_fetch(array(
                'us_r_id' => $active_admission['r_id'],
                'us_student_id' => $active_admission['u_id'],
            ));

        } else {

            //This should not happen either:
            $this->Db_model->e_create(array(
                'e_initiator_u_id' => $active_admission['u_id'],
                'e_message' => 'extract_level() Failed to load Bootcamp data in the Student Action Plan',
                'e_json' => $admissions,
                'e_type_id' => 8, //Platform Error
                'e_b_id' => $b_id,
                'e_r_id' => $active_admission['r_id'],
                'e_c_id' => $c_id,
            ));

            //Ooops, they dont have anything!
            die('<div class="alert alert-danger" role="alert">Invalid ID</div>');

        }

        //All good, Load UI:
        $this->load->view('front/student/actionplan_ui.php' , $view_data);

	}
	
	function applications(){
	    
	    //List student applications
	    $application_status_salt = $this->config->item('application_status_salt');
	    if(!isset($_GET['u_key']) || !isset($_GET['u_id']) || intval($_GET['u_id'])<1 || !(md5($_GET['u_id'].$application_status_salt)==$_GET['u_key'])){
	        //Log this error:
	        redirect_message('/','<div class="alert alert-danger" role="alert">Invalid Application Key. Choose your Bootcamp and re-apply to receive an email with your application status url.</div>');
	        exit;
	    }
	    
	    //Is this a paypal success?
        $purchase_value = 0;
	    if(isset($_GET['status']) && intval($_GET['status'])==1){
	        //Give the PayPal webhook enough time to update the DB status:
	        sleep(2);

	        //Capture Facebook Conversion:
            //TODO This would capture again upon refresh, fix later...
            $purchase_value = doubleval($_GET['purchase_value']);
	    }
	    
	    //Search for class using form ID:
	    $users = $this->Db_model->u_fetch(array(
	        'u_id' => intval($_GET['u_id']),
	    ));

        if(count($users)==1){
            $udata = $users[0];
        } else {
            redirect_message('/','<div class="alert alert-danger" role="alert">Invalid Application Key. Choose your Bootcamp and re-apply to receive an email with your application status url.</div>');
        }
	    
	    //Fetch all their addmissions:
        $admissions = $this->Db_model->ru_fetch(array(
            'ru_u_id'	=> $udata['u_id'],
        ));

        $bs = $this->Db_model->b_fetch(array(
            'b_id'	=> $admissions[0]['r_b_id'],
        ));
	    
	    //Validate Class ID that it's still the latest:
	    $data = array(
	        'title' => 'My Application(s) Status',
	        'udata' => $udata,
	        'u_id' => $_GET['u_id'],
            'u_key' => $_GET['u_key'],
            'b_thankyou_url' => $bs[0]['b_thankyou_url'],
            'purchase_value' => $purchase_value, //Capture via Facebook Pixel
	        'admissions' => $admissions,
	        'hm' => ( isset($_GET['status']) && isset($_GET['message']) ? '<div class="alert alert-'.( intval($_GET['status']) ? 'success' : 'danger').'" role="alert">'.( intval($_GET['status']) ? 'Success' : 'Error').': '.$_GET['message'].'</div>' : '' ),
	    );

	    //Load apply page:
	    $this->load->view('front/shared/p_header' , $data);
	    $this->load->view('front/student/my_applications' , $data);
	    $this->load->view('front/shared/p_footer');

	}



	function class_application($ru_id){
	    
	    //List student applications
	    $application_status_salt = $this->config->item('application_status_salt');
	    if(intval($ru_id)<1 || !isset($_GET['u_key']) || !isset($_GET['u_id']) || intval($_GET['u_id'])<1 || !(md5($_GET['u_id'].$application_status_salt)==$_GET['u_key'])){
	        //Log this error:
	        redirect_message('/','<div class="alert alert-danger" role="alert">Invalid Application Key. Choose your Bootcamp and re-apply to receive an email with your application status url.</div>');
	        exit;
	    }
	    
	    //Fetch all their addmissions:
	    $admissions = $this->Db_model->remix_admissions(array(
	        'ru.ru_id'	   => $ru_id, //Loading a very specific Admission ID
	        'ru.ru_u_id'   => intval($_GET['u_id']),
	    ));
	    //Did we find at-least one?
	    if(count($admissions)<=0){
	        //Log this error:
	        redirect_message('/my/applications?u_key='.$_GET['u_key'].'&u_id='.$_GET['u_id'],'<div class="alert alert-danger" role="alert">No Active Applications.</div>');
	        exit;
	    }
	    
	    //Assemble the data:
	    $data = array(
	        'title' => 'Join '.$admissions[0]['c_objective'].' - Starting '.time_format($admissions[0]['r_start_date'],4),
	        'ru_id' => $ru_id,
	        'u_id' => $_GET['u_id'],
	        'u_key' => $_GET['u_key'],
	        'admission' => $admissions[0],
	        'b_fb_pixel_id' => $admissions[0]['b_fb_pixel_id'], //Will insert pixel code in header
	    );
	    
	    //Load apply page:
	    $this->load->view('front/shared/p_header' , $data);
	    $this->load->view('front/student/class_apply' , $data);
	    $this->load->view('front/shared/p_footer');
	    
	}


	function review($ru_id,$ru_key){
	    //Loadup the review system for the student's Class
        if(!($ru_key==substr(md5($ru_id.'r3vi3wS@lt'),0,6))){
            //There is an issue with the key, show error to user:
            redirect_message('/','<div class="alert alert-danger" role="alert">Invalid Review URL.</div>');
            exit;
        }

        //Student is validated, loadup their Reivew portal:
        $admissions = $this->Db_model->remix_admissions(array(
            'ru.ru_id'     => $ru_id,
        ));

        //Should never happen:
        if(count($admissions)<1){

            $this->Db_model->e_create(array(
                'e_initiator_u_id' => 0, //System
                'e_message' => 'Validated review URL failed to fetch admission data',
                'e_type_id' => 8, //System Error
            ));

            //There is an issue with the key, show error to user:
            redirect_message('/','<div class="alert alert-danger" role="alert">Admission not found for placing a review.</div>');
            exit;
        }


        $lead_instructor = $admissions[0]['b__admins'][0]['u_fname'].' '.$admissions[0]['b__admins'][0]['u_lname'];

        //Assemble the data:
        $data = array(
            'title' => 'Review '.$lead_instructor.' - '.$admissions[0]['c_objective'],
            'lead_instructor' => $lead_instructor,
            'admission' => $admissions[0],
            'ru_key' => $ru_key,
            'ru_id' => $ru_id,
        );

        if(isset($_GET['raw'])){
            echo_json($admissions[0]);
            exit;
        }

        //Load apply page:
        $this->load->view('front/shared/p_header' , $data);
        $this->load->view('front/student/review_class' , $data);
        $this->load->view('front/shared/p_footer');
    }
	
}