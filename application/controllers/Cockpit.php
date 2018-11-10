<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cockpit extends CI_Controller {

    //To carry the user object after validation
    var $udata;

	function __construct() {
		parent::__construct();
		
		//Load our buddies:
		$this->output->enable_profiler(FALSE);

        //Authenticate level 3 or higher, redirect if not:
        $this->udata = auth(array(1281),1);

	}


    function engagements(){
        $this->load->view('console/console_header', array(
            'title' => 'Platform Engagements',
        ));
        $this->load->view('cockpit/engagements_browse');
        $this->load->view('console/console_footer');
    }




    function subscriptions(){
        $this->load->view('console/console_header', array(
            'title' => 'Subscriptions Browser',
        ));
        $this->load->view('cockpit/subscriptions_browse');
        $this->load->view('console/console_footer');
    }



    function ej_list($e_id){
        $udata = auth(array(1281),1);
        //Fetch blob of engagement and display it on screen:
        $blobs = $this->Db_model->e_fetch(array(
            'ej_e_id' => $e_id,
        ),1,array('ej'));
        if(count($blobs)==1){
            echo_json(array(
                'blob' => unserialize($blobs[0]['ej_e_blob']),
                'e' => $blobs[0]
            ));
        } else {
            echo_json(array('error'=>'Not Found'));
        }
    }
	
	function udemy(){

	    if(isset($_GET['cat'])){
	        
	        //Load coach list:
	        $this->load->view('console/console_header', array(
	            'title' => urldecode($_GET['cat']).' Udemy Community',
	        ));
	        $this->load->view('cockpit/udemy_category' , array(
	            'il_category' => $this->Db_model->il_fetch(array(
	                'il_udemy_user_id >' => 0,
	                'il_student_count >' => 0,
	                'il_udemy_category' => urldecode($_GET['cat']),
	            )),
	        ));
	        $this->load->view('console/console_footer');
	        
	    } else {
	        
	        //Load category list:
	        $this->load->view('console/console_header', array(
	            'title' => 'Udemy Community',
	        ));
	        $this->load->view('cockpit/udemy_all' , array(
	            'il_overview' => $this->Db_model->il_overview(),
	        ));
	        $this->load->view('console/console_footer');
	        
	    }
	}


    function statuslegend(){
        //Load views
        $this->load->view('console/console_header' , array(
            'title' => 'Status Legend',
        ));
        $this->load->view('cockpit/statuslegend');
        $this->load->view('console/console_footer');
    }


}