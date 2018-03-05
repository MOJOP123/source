<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adjust extends CI_Controller {

    //This controller is for functions that do mass adjustments on the DB

    function __construct() {
        parent::__construct();

        $this->output->enable_profiler(FALSE);
    }


    function psid(){

        //Syncs the current communication methods for all students
        $facebook_activated = $this->Db_model->u_fetch(array(
            'u_fb_id >' => 0,
        ));

        $fp_id = 4; //MenchBot

        foreach($facebook_activated as $u){

            //Update their new field with the MenchBot:
            $this->Db_model->u_update( $u['u_id'], array(
                'u_cache__fp_id' => $fp_id,
                'u_cache__fp_psid' => $u['u_fb_id'], //Merged account
            ));

            //Go through all their admissions and set this:
            $admissions = $this->Db_model->ru_fetch(array(
                'ru_u_id' => $u['u_id'],
            ));
            foreach($admissions as $admission){
                $this->Db_model->ru_update( $admission['ru_id'], array(
                    'ru_fp_id' => $fp_id,
                    'ru_fp_psid' => $u['u_fb_id'], //Merged account
                ));
            }

        }

        echo count($facebook_activated).' Updated';

    }


    function sync_student_progress(){

        //Go through all admissions for running classes and updates the student positions in those classes:
        $classes = $this->Db_model->r_fetch(array(
            'r.r_status >=' => 2,
        ));

        $stats = array();
        foreach($classes as $class){


            //Fetch full Bootcamp/Class data for this:
            $bootcamps = fetch_action_plan_copy($class['r_b_id'],$class['r_id']);
            $class = $bootcamps[0]['this_class'];


            //Fetch all the students of these classes, and see where they are at:
            $class['students'] = $this->Db_model->ru_fetch(array(
                'ru.ru_status >='   => 4, //Initiated or higher as long as bootcamp is running!
                'ru.ru_r_id'	 => $class['r_id'],
            ));

            $stats[$class['r_id']] = 0;
            foreach($class['students'] as $admission){

                //Fetch all their submissions so far:
                $us_data = $this->Db_model->us_fetch(array(
                    'us_student_id' => $admission['u_id'],
                    'us_r_id' => $class['r_id'],
                    'us_status' => 1,
                ));


                //Go through and see where it breaks down:
                $found_incomplete_task = false;
                $total_hours_done = 0;
                $ru_cache__current_milestone = 1;
                $ru_cache__current_task = 1;
                $total_tasks = 0;
                $done_tasks = 0;

                //The goal is to find the task that is after the very last task done
                //Note that some Tasks could be done, but then rejected by the instructor...
                foreach($bootcamps[0]['c__child_intents'] as $milestone){
                    if($milestone['c_status']==1){
                        foreach($milestone['c__child_intents'] as $task){
                            if($task['c_status']==1){
                                $total_tasks++;
                                //Has the student done this?
                                if(!array_key_exists($task['c_id'],$us_data) || !($us_data[$task['c_id']]['us_status']==1)){

                                    if(!$found_incomplete_task){
                                        //The student is not done with this task, so here is were they're at:
                                        $ru_cache__current_milestone = $milestone['cr_outbound_rank'];
                                        $ru_cache__current_task = $task['cr_outbound_rank'];
                                        $found_incomplete_task = true;
                                    }

                                } else {

                                    //Addup the total hours based on the Action Plan
                                    $total_hours_done += $us_data[$task['c_id']]['us_time_estimate'];
                                    $found_incomplete_task = false; //Reset this
                                    $done_tasks++;

                                }
                            }
                        }
                    }
                }

                //Calculate the total progress:
                $ru_cache__completion_rate = number_format(($total_hours_done/$bootcamps[0]['c__estimated_hours']),3);

                if($done_tasks==$total_tasks){
                    //They have done all Tasks
                    $ru_cache__current_milestone = ($class['r__total_milestones']+1);
                    $ru_cache__current_task = 1;
                }

                //Do we need to update?
                if(!($admission['ru_cache__current_milestone']==$ru_cache__current_milestone) || !($admission['ru_cache__current_task']==$ru_cache__current_task) || !($admission['ru_cache__completion_rate']==$ru_cache__completion_rate)){

                    //Update DB:
                    $this->Db_model->ru_update( $admission['ru_id'] , array(
                        'ru_cache__completion_rate' => $ru_cache__completion_rate,
                        'ru_cache__current_task' => $ru_cache__current_task,
                        'ru_cache__current_milestone' => $ru_cache__current_milestone,
                    ));

                    //Increase counter:
                    $stats[$class['r_id']]++;
                }
            }
        }

        echo_json($stats);
    }

    function sync_class_completion_rates(){

        $running_classes = $this->Db_model->r_fetch(array(
            'r_status' => 3, //Only running classes
        ));

        foreach($running_classes as $class) {

            $qualified_students = $this->Db_model->ru_fetch(array(
                'ru.ru_r_id' => $class['r_id'],
                'ru.ru_status >' => 5,
            ));

            $completed_students = $this->Db_model->ru_fetch(array(
                'ru.ru_r_id' => $class['r_id'],
                'ru.ru_status' => 7,
            ));

            //Update Class:
            $this->Db_model->r_update( $class['r_id'], array(
                'r_cache__completion_rate' => ( count($qualified_students)>0 ? number_format((count($completed_students) / count($qualified_students)), 3) : 0 ),
            ));
        }

        echo count($running_classes).' adjusted';
    }

    function bootcamp_editing(){
        $bootcamps = $this->Db_model->remix_bootcamps(array(
            'b_status >' => 0,
        ));

        //Now lets see which ones have descriptions:
        foreach($bootcamps as $bootcamp){
            $found = 0;
            foreach($bootcamp['c__child_intents'] as $milestone) {
                if($milestone['c_status']>=0){
                    foreach($milestone['c__child_intents'] as $task) {
                        if($task['c_status']>=0){
                            //Do something here...
                        }
                    }
                }
            }
            if($found>0){
                echo '<hr />';
            }
        }
    }




}