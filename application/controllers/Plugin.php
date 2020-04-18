<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plugin extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->output->enable_profiler(FALSE);

        date_default_timezone_set(config_var(11079));
    }

    function index($plugin_en_id = 0){

        $en_all_6287 = $this->config->item('en_all_6287'); //MENCH PLUGINS

        if(!$plugin_en_id){

            die('Enter plugin ID');

        } else {

            $this->load->view('header', array(
                'title' => ( strlen($en_all_6287[$plugin_en_id]['m_icon']) && !string_contains_html($en_all_6287[$plugin_en_id]['m_icon']) ? $en_all_6287[$plugin_en_id]['m_icon'].' ' : '' ).$en_all_6287[$plugin_en_id]['m_name'],
            ));
            $this->load->view('plugin/plugin_header');
            $this->load->view('plugin/'.$plugin_en_id.'/index' , array(
                'session_en' => superpower_assigned(),
            ));

            $this->load->view('footer');

        }

    }

    function older($action = null, $command1 = null, $command2 = null)
    {

        boost_power();

        //Validate player:
        $session_en = superpower_assigned(12699, true);
        $en_all_11035 = $this->config->item('en_all_11035'); //MENCH NAVIGATION

        $this->load->view('header', array(
            'title' => $en_all_11035[4341]['m_name'],
        ));

        $this->load->view('plugin/plugin_home' , array(
            'action' => $action,
            'command1' => $command1,
            'command2' => $command2,
            'session_en' => $session_en,
        ));

        $this->load->view('footer');

    }

}
