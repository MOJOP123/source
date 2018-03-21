<?php
//Fetch the sprint units from config:
$core_objects = $this->config->item('core_objects');
$message_max = $this->config->item('message_max');
$intent_statuses = status_bible('c');
$udata = $this->session->userdata('user');
?>
<style> .breadcrumb li { display:block; } </style>
<script>

//This functions updates the input placeholders to refect the next item to be added:
function update_tree_input(){
    //First update the number of Tasks in main input field:
    $('#addnode').attr("placeholder", "Task #"+($("#list-outbound").children().length)+" Primary Outcome");

    //Now go through each Step list and see whatsupp:
    if($('.step-group').length){
        $( ".step-group" ).each(function() {
            var node_id = $( this ).attr('node-id');
            $('#addnode'+node_id).attr("placeholder", "Step #"+($("#list-outbound-"+node_id).children().length-1)+" Primary Outcome");
        });
    }
}

function format_hours(dbl_hour){
    dbl_hour = parseFloat(dbl_hour);
    if(dbl_hour<=0){
        return '0';
    } else if(dbl_hour<1){
        //Show this in minutes:
        return Math.round((dbl_hour*60)) + "m";
    } else {
        //Show in rounded hours:
        return ( (dbl_hour % 1 == 0) ? "" : "~" ) + Math.round((dbl_hour)) + "h";
    }
}


$(document).ready(function() {

    //Deletion warning to Steps & Task drop down:
    $('#c_status_2').change(function() {
        if(parseInt($(this).val())<0){
            //Delete has been selected!
            $('#delete_warning').html('<span style="color:#FF0000;"><i class="fa fa-trash" aria-hidden="true"></i> You are about to permanently delete this Task, its Steps and all related messages.</span>');
        } else {
            $('#delete_warning').html('');
        }
    });
    $('#c_status_3').change(function() {
        if(parseInt($(this).val())<0){
            //Delete has been selected!
            $('#delete_warning').html('<span style="color:#FF0000;"><i class="fa fa-trash" aria-hidden="true"></i> You are about to permanently delete this Step and all its messages.</span>');
        } else {
            $('#delete_warning').html('');
        }
    });


    //Enforce Alphanumeric for URL Key:
    $('#b_url_key').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
    });


    //Make iPhone X Sticky for scrolling longer lists
    $(".main-panel").scroll(function() {
        var top_position = $(this).scrollTop();
        clearTimeout($.data(this, 'scrollTimer'));
        $.data(this, 'scrollTimer', setTimeout(function() {
            $("#iphonex").css('top',(top_position-70)); //PX also set in style.css for initial load
            $("#modifybox").css('top',(top_position-0)); //PX also set in style.css for initial load
        }, 34));
    });


    //addnode
    if(window.location.hash) {
        var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        var hash_parts = hash.split("-");
        if(hash_parts.length>=2){
            var level_id = parseInt($('.maplevel'+hash_parts[1]).attr('level-id'));
            if(level_id>0){
                //Fetch level if available:
                if(hash_parts[0]=='messages'){
                    load_iphone(hash_parts[1],level_id);
                } else if(hash_parts[0]=='modify'){
                    load_modify(hash_parts[1],level_id);
                }
            }
        } else {
            //Perhaps a menu change?
            focus_hash(window.location.hash);
        }
    }

    //Update Bootcamp hours:
    $('.hours_level_1').text(format_hours($('.hours_level_1').attr('current-hours')));

    //Loadup Task numbering based on duratioan extenstions:
    intents_sort(0,2);

    //Activate sorting for Steps:
    if($('.step-group').length){

        $( ".step-group" ).each(function() {

            var node_id = $( this ).attr('node-id');

            //Load sorting:
            load_intent_sort(node_id,"3");

            //Load time:
            $('#t_estimate_'+node_id).text(format_hours($('#t_estimate_'+node_id).attr('current-hours')));

        });

        if($('.is_step_sortable').length){
            //Goo through all Steps:
            $( ".is_step_sortable" ).each(function() {
                var node_id = $(this).attr('node-id');
                if(node_id){
                    //Load time:
                    $('#t_estimate_'+node_id).text(format_hours($('#t_estimate_'+node_id).attr('current-hours')));
                }
            });
        }

    }

    //Update counters on load:
    update_tree_input();
    //Also update every time DOM changes
    //TODO This is probably a heavy process, but I hacked it for now until we can improve later...
    $('#list-outbound').bind("DOMSubtreeModified",function(){
        update_tree_input();
    });


	//Load Sortable:
	load_intent_sort($("#pid").val(),"2");



	//Add new Task:
    $('#dir_handle').click(function (e) {
        new_intent($('#pid').val(),2);
    });
    $( "#addnode" ).keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if ((code == 13) || (e.ctrlKey && code == 13)) {
            return new_intent($('#pid').val(),2);
        }
    });


	//Load Algolia:
	/*
	$( "#addnode" ).on('autocomplete:selected', function(event, suggestion, dataset) {

		link_lintent(suggestion.c_id);

	}).autocomplete({ hint: false, keyboardShortcuts: ['a'] }, [{
	    source: function(q, cb) {
		      algolia_index.search(q, { hitsPerPage: 7 }, function(error, content) {
		        if (error) {
		          cb([]);
		          return;
		        }

		        cb(content.hits, content);
		      });
		    },
		    displayKey: function(suggestion) { return "" },
		    templates: {
		      suggestion: function(suggestion) {
		         return '<span class="suggest-prefix"><i class="fa fa-eye" aria-hidden="true"></i> Link to</span> '+ suggestion._highlightResult.c_objective.value;
		      },
		      header: function(data) {
		    	  if(!data.isEmpty){
		    		  return '<a href="javascript:new_intent(\''+data.query+'\')" class="add_node"><span class="suggest-prefix"><i class="fa fa-plus" aria-hidden="true"></i> Create</span> "'+data.query+'"'+'</a>';
		    	  }
		      },
		      empty: function(data) {
	    		  	  return '<a href="javascript:new_intent(\''+data.query+'\')" class="add_node"><span class="suggest-prefix"><i class="fa fa-plus" aria-hidden="true"></i> Create</span> "'+data.query+'"'+'</a>';
		      },
		    }
	}]).keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if ((code == 13) || (e.ctrlKey && code == 13)) {
        	new_intent($( "#addnode" ).val());
            return true;
        }
    });
    */
});







function add_to_list(sort_list_id,sort_handler,html_content){
    //See if we already have a list in place?
    if($( "#"+sort_list_id+" "+sort_handler).length>0){
        //yes we do! add this:
        $( "#"+sort_list_id+" "+sort_handler+":last").after(html_content);
    } else {
        //Empty list, add before input filed:
        $( "#"+sort_list_id).prepend(html_content);
    }
}

function new_intent(pid,next_level){

    //Set variables mostly based on level:
    if(next_level==2){
        var input_field = $('#addnode');
        var sort_list_id = "list-outbound";
        var sort_handler = ".is_sortable";
    } else if(next_level==3){
        var input_field = $('#addnode'+pid);
        var sort_list_id = "list-outbound-"+pid;
        var sort_handler = ".is_step_sortable";
    }

    var b_id = $('#b_id').val();
    var intent_name = input_field.val();

 	if(intent_name.length<1){
 		alert('Error: Missing Outcome. Try Again...');
        input_field.focus();
 		return false;
 	}

 	//Set processing status:
    add_to_list(sort_list_id,sort_handler,'<div id="temp'+next_level+'" class="list-group-item"><img src="/img/round_load.gif" class="loader" /> Adding... </div>');

     //Empty Input:
    input_field.val("").focus();

 	//Update backend:
 	$.post("/api_v1/intent_create", {b_id:b_id, pid:pid, c_objective:intent_name, next_level:next_level}, function(data) {

 	    //Update UI to confirm with user:
 		$( "#temp"+next_level ).remove();

 		//Add new
        add_to_list(sort_list_id,sort_handler,data.html);

 		//Re-adjust sorting:
 		load_intent_sort(pid,next_level);

 		if(next_level==2){

            //Adjust the Task count:
            intents_sort(0,2);

            //Re-adjust sorting for inner Steps:
            load_intent_sort(data.c_id,3);

        } else {
            //Adjust Step sorting:
            intents_sort(pid,next_level);
        }

 		//Tooltips:
 		$('[data-toggle="tooltip"]').tooltip();

 		//Add the new time:
        var step_deficit = 0.05; //3 minutes is the default for new Tasks/Steps
        var current_b_hours = parseFloat($('.hours_level_1').attr('current-hours'));
        var current_task_hours = parseFloat($('#t_estimate_'+pid).attr('current-hours'));
        var current_task_status = parseInt($('.c_objective_'+pid).attr('current-status'));

        //Update Miletsone:
        $('#t_estimate_'+pid).attr('current-hours',(current_task_hours + step_deficit)).text(format_hours((current_task_hours + step_deficit)));

        //Only update Bootcamp if Task is active:
        if(current_task_status>0){
            $('.hours_level_1').attr('current-hours',(current_b_hours + step_deficit)).text(format_hours((current_b_hours + step_deficit)));
        }

 	});

 	//Prevent form submission:
    event.preventDefault();
 	return false;
}



/*
function link_lintent(target_id){
    //TODO Update based on new_intent() changes when implementing search
 	//Fetch needed vars:
 	var pid = $('#pid').val();
 	var b_id = $('#b_id').val();
 	var next_level = $( "#next_level" ).val();

 	//Set processing status:
     $( "#list-outbound>div" ).before('<a href="#" id="temp" class="list-group-item"><img src="/img/round_load.gif" class="loader" /> Adding... </a>');

     //Empty Input:
 	$( "#addnode" ).val("").focus();

 	//Update backend:
 	$.post("/api_v1/intent_link", {b_id:b_id, pid:pid, target_id:target_id, next_level:next_level}, function(data) {
 		//Update UI to confirm with user:
 		$( "#temp" ).remove();

 		//Add new
 		$('#list-outbound>div').before(data);

        //TODO Resort

 		//Tooltips:
 		$('[data-toggle="tooltip"]').tooltip();
 	});
}
*/



function intents_sort(c_id,level){

    if(level==2){
        var s_element = "list-outbound";
        var s_draggable = ".is_sortable";
    } else if(level==3){
        var s_element = "list-outbound-"+c_id;
        var s_draggable = ".is_step_sortable";
    } else {
        //Should not happen!
        return false;
    }

    //Fetch new sort:
    var new_sort = [];
 	var sort_rank = 0;
 	var is_properly_sorted = true; //Assume good unless proven otherwise
    var drafting_ids = [];

 	$( "#"+s_element+" "+s_draggable ).each(function() {
        //Make sure this is NOT the dummy drag in box
 	    if(!$(this).hasClass('dropin-box')){

            //Fetch variables for this intent:
            var pid = parseInt($(this).attr('node-id'));
            var cr_id = parseInt($( this ).attr('data-link-id'));
            var status = parseInt($('.c_objective_'+pid).attr('current-status'));
            var prefix = ( level==2 ? '' : 'Step' ); //The default for all nodes

            if(status>=1){

                //Remove potential line throughs:
                $('#t_estimate_'+pid).removeClass('crossout');

                sort_rank++;

                //Store in DB:
                new_sort[sort_rank] = cr_id;

                //Is the Outbound rank correct? Check DB value:
                var db_rank = parseInt($('.c_objective_'+pid).attr('outbound-rank'));

                if(level==2 && !(db_rank==sort_rank) && !c_id){
                    is_properly_sorted = false;
                    console.log('Node #'+pid+' detected out of sync.');
                }

                //Update sort handler:
                $( "#cr_"+cr_id+" .inline-level-"+level ).html( prefix + ' #' + sort_rank );


            } else {

                //Add line through:
                $('#t_estimate_'+pid).addClass('crossout');

                //Save temporarily so we can later give proper index based on all active ones:
                drafting_ids[(drafting_ids.length)] = cr_id;

                $( "#cr_"+cr_id+" .inline-level-"+level ).html('<b><i class="fa fa-pencil-square"></i></b>');

            }
        }
 	});

 	//Append drafting messages as they always go last:
 	if(drafting_ids.length>0){
        for (var i = 0, len = drafting_ids.length; i < len; i++) {
            sort_rank++;
            //Store in DB:
            new_sort[sort_rank] = drafting_ids[i];
        }
    }


 	if(level==2 && !is_properly_sorted && !c_id){
 	    //Sorting issue detected on Task load:
        c_id = parseInt($('#pid').val());
    }

    //It might be zero for lists that have jsut been emptied
 	if(sort_rank>0 && c_id){
        //Update backend:
        $.post("/api_v1/intents_sort", { pid:c_id, b_id:$('#b_id').val(), new_sort:new_sort }, function(data) {
            //Update UI to confirm with user:
            if(!data.status){
                //There was some sort of an error returned!
                alert('ERROR: '+data.message);
            }
        });
    }
}


function load_intent_sort(pid,level){

    if(level==2){
        var s_element = "list-outbound";
        var s_draggable = ".is_sortable";
    } else if(level==3){
        var s_element = "list-outbound-"+pid;
        var s_draggable = ".is_step_sortable";
    } else {
        //Should not happen!
        return false;
    }


	var theobject = document.getElementById(s_element);
	var settings = {
        animation: 150, // ms, animation speed moving items when sorting, `0` � without animation
        draggable: s_draggable, // Specifies which items inside the element should be sortable
        handle: ".fa-bars", // Restricts sort start click/touch to the specified element
        onUpdate: function (evt/**Event*/){
            intents_sort(pid,level);
        }
    };

	//Enable between list moves:
	if(level=="3"){

        settings['group'] = "steplists";
        settings['ghostClass'] = "drop-step-here";
        settings['onAdd'] = function (evt) {
            //Define variables:
            var inputs = {
                cr_id:evt.item.attributes[1].nodeValue,
                c_id:evt.item.attributes[2].nodeValue,
                b_id:$('#b_id').val(),
                from_c_id:evt.from.attributes[2].value,
                to_c_id:evt.to.attributes[2].value,
            };
            //Update:
            $.post("/api_v1/migrate_step", inputs, function(data) {
                //Update sorts in both lists:
                if(!data.status){

                    //There was some sort of an error returned!
                    alert('ERROR: '+data.message);

                } else {

                    //All good as expected!
                    //Moved the parent pointer:
                    $('.maplevel'+inputs.c_id).attr('parent-node-id',inputs.to_c_id);

                    //Determine core variables for hour move calculations:
                    var step_hours = parseFloat($('#t_estimate_'+inputs.c_id).attr('current-hours'));
                    var step_status = parseInt($('.c_objective_'+inputs.c_id).attr('current-status'));
                    var from_task_status = parseInt($('.c_objective_'+inputs.from_c_id).attr('current-status'));
                    var to_task_status = parseInt($('.c_objective_'+inputs.to_c_id).attr('current-status'));

                    if(!(step_hours==0) && step_status>0){

                        //Remove from old one:
                        var from_hours_new = parseFloat($('#t_estimate_'+inputs.from_c_id).attr('current-hours'))-step_hours;
                        $('#t_estimate_'+inputs.from_c_id).attr('current-hours',from_hours_new).text(format_hours(from_hours_new));

                        //Add to new:
                        var to_hours_new = parseFloat($('#t_estimate_'+inputs.to_c_id).attr('current-hours'))+step_hours;
                        $('#t_estimate_'+inputs.to_c_id).attr('current-hours',to_hours_new).text(format_hours(to_hours_new));

                        //Adjust Bootcamp hours if necessary:
                        if(!(from_task_status==to_task_status)){
                            //Yes we need to adjust as the statuses of these Tasks are different:
                            var current_b_hours = parseFloat($('.hours_level_1').attr('current-hours'));
                            //Determine what to do:
                            var new_project_hours = current_b_hours + ( ( from_task_status>to_task_status ? -1 : 1 ) * step_hours );
                            $('.hours_level_1').attr('current-hours',new_project_hours).text(format_hours(new_project_hours));
                        }
                    }

                    //Update sorting for both lists:
                    intents_sort(inputs.from_c_id,"3");
                    intents_sort(inputs.to_c_id,"3");

                }
            });
        };
    }
 	var sort = Sortable.create( theobject , settings );
}





function load_iphone(c_id, level){

    var messages_focus_pid = ( $('#iphonex').hasClass('hidden') ? 0 : parseInt($('#iphonex').attr('node-id')) );

    //Check to see if its open or close:
    if(messages_focus_pid==c_id){

        //close and return
        //$('#iphonex').addClass('hidden');
        $('#iphonex').hide().fadeIn();
        return false;

    } else {

        //Make the frame visible:
        $("#iphonex").removeClass('hidden').hide().fadeIn();
        $('#modifybox').addClass('hidden');
        var handler = $( "#iphone-screen" );

        //Define the top menu that would not change:
        $('#iphonex').attr('node-id',c_id);

        //Define standard phone header:
        var top_menu = '<div class="ix-top">\n' +
            '<span class="ix-top-left" data-toggle="tooltip" title="PST Time" data-placement="bottom"><?= date("H:i") ?></span>\n' +
            '<span class="ix-top-right">\n' +
            '<i class="fa fa-wifi" aria-hidden="true"></i>\n' +
            '<i class="fa fa-battery-full" aria-hidden="true"></i>\n' +
            '</span>\n' +
            '</div>';

        //Show tem loader:
        handler.html('<div style="text-align:center; padding-top:89px; padding-bottom:89px;"><img src="/img/round_load.gif" class="loader" /></div>');

        //Load the frame:
        $.post("/api_v1/load_iphone", {

            b_id:$('#b_id').val(),
            c_id:c_id,
            level:level,

        }, function(data) {

            //Empty Inputs Fields if success:
            handler.html(top_menu+data);

            //SHow inner tooltips:
            $('[data-toggle="tooltip"]').tooltip();

        });
    }
}



function load_modify(c_id, level){

    //$('.levelz, #modifybox').removeClass('hidden');
    var modify_focus_pid = ( $('#modifybox').hasClass('hidden') ? 0 : parseInt($('#modifybox').attr('node-id')) );
    var modify_focus_level = ( $('#modifybox').hasClass('hidden') ? 0 : parseInt($('#modifybox').attr('level')) );

    //Do we already have this loaded? Then we should close it:
    if(modify_focus_pid==c_id){

        //$('#modifybox').addClass('hidden');
        $('#modifybox').hide().fadeIn();
        return false;

    } else {

        //Loadup variables for Tasks & Steps:
        if(level>=2){

            $('#c_objective'+level+' .c_objective_input').val($(".c_objective_"+c_id).html());

            //Fetch current status
            $('#modifybox #c_status_'+level).val($('.c_objective_'+c_id).attr('current-status'));

            //Update Timer:
            $('.timer_'+level).val($('#t_estimate_'+c_id).attr('current-hours'));

            //Completion settings:
            document.getElementById("c_complete_url_required").checked = parseInt($('.c_objective_'+c_id).attr('c_complete_url_required'));
            document.getElementById("c_complete_notes_required").checked = parseInt($('.c_objective_'+c_id).attr('c_complete_notes_required'));

        }

        //Make the frame visible:
        $("#modifybox").removeClass('hidden').hide().fadeIn();
        $('#iphonex').addClass('hidden');

        //Reset potential delete message:
        $('#delete_warning').html('');

        //Show the right elements based on level:
        $('.levelz').addClass('hidden');
        $('.level'+level).removeClass('hidden');

        //Update variables:
        $('#modifybox').attr('node-id',c_id);
        $('#modifybox').attr('level',level);

    }
}









function save_modify(){

    //Define shared data for all 3 levels:
    var modify_data = {
        b_id:$('#b_id').val(),
        pid:( $('#modifybox').hasClass('hidden') ? 0 : parseInt($('#modifybox').attr('node-id')) ),
        level:( $('#modifybox').hasClass('hidden') ? 0 : parseInt($('#modifybox').attr('level')) ),
    };

    if(!modify_data['pid'] || !modify_data['level']){

        //Oops, this should not happen!
        return false;

    } else {

        //Now append more based on levels and take action:
        if(modify_data['level']==1){

            modify_data['c_objective'] = $('#c_objective1 .c_objective_input').val();

        } else if(modify_data['level']>=2){

            if(modify_data['level']==2){
                //TODO TO be implemented
                modify_data['c_extension_rule'] = parseInt($('.c_objective_'+modify_data['pid']).attr('extension-rule'));
            }

            modify_data['c_objective'] = $('#c_objective'+modify_data['level']+' .c_objective_input').val();
            modify_data['c_status'] = $('#c_status_'+modify_data['level']).val();
            modify_data['c_time_estimate'] = $('#c_time_estimate').val();
            modify_data['c_complete_url_required'] = (document.getElementById('c_complete_url_required').checked ? 1 : 0);
            modify_data['c_complete_notes_required'] = (document.getElementById('c_complete_notes_required').checked ? 1 : 0);

        }

        //Show spinner:
        $('.save_setting_results').html('<span><img src="/img/round_load.gif" class="loader" /></span>').hide().fadeIn();


        //Save the rest of the content:
        $.post("/api_v1/save_modify", modify_data , function(data) {

            if(data.status){

                //Always update title:
                $(".c_objective_"+modify_data['pid']).html(modify_data['c_objective']);

                //Update page variables:
                if(modify_data['level']==1){

                    //Also adjust top left title:
                    $("#top-left-title").html(modify_data['c_objective']);

                } else if(modify_data['level']==2){


                    //Update status?
                    var current_status = parseInt($('.c_objective_'+modify_data['pid']).attr('current-status'));

                    if(!(current_status==modify_data['c_status'])) {

                        //Needs to update:
                        $('.c_objective_' + modify_data['pid']).attr('current-status', modify_data['c_status']);

                        var current_task_hours = parseFloat($('#t_estimate_' + modify_data['pid']).attr('current-hours'));
                        var current_b_hours = parseFloat($('.hours_level_1').attr('current-hours'));

                        //Does this need to be removed from the totals?
                        if (current_status == 1 && modify_data['c_status'] <= 0 && current_task_hours > 0) {
                            //We need to remove initial hours from the totals:
                            $('.hours_level_1').attr('current-hours', (current_b_hours - current_task_hours)).text(format_hours(current_b_hours - current_task_hours));
                        } else if (current_status <= 0 && modify_data['c_status'] > 0 && current_task_hours > 0) {
                            //We need to add the hours to the total:
                            $('.hours_level_1').attr('current-hours', (current_b_hours + current_task_hours)).text(format_hours(current_b_hours + current_task_hours));
                        }

                        //Has this been deleted?
                        if (modify_data['c_status'] < 0) {

                            //Yes! Remove from UI:
                            $('.node_line_' + modify_data['pid']).html('<span style="color:#222;"><i class="fa fa-trash" aria-hidden="true"></i> Deleted</span>');

                            //Disapper in a while:
                            setTimeout(function () {
                                //Hide the editor & saving results:
                                $('.node_line_' + modify_data['pid']).fadeOut();
                                setTimeout(function () {
                                    //Hide the editor & saving results:
                                    $('.node_line_' + modify_data['pid']).remove();

                                    //Hide editing box:
                                    $('#modifybox').addClass('hidden');

                                    //Resort all Tasks to illustrate changes on UI:
                                    intents_sort(0, 2);
                                }, 377);
                            }, 1597);

                        }

                        //Resort all Tasks to illustrate changes on UI:
                        intents_sort(0, 2);
                    }


                    //What to do with the hours:
                    var current_task_hours = parseFloat($('#t_estimate_'+modify_data['pid']).attr('current-hours'));
                    var task_deficit = modify_data['c_time_estimate'] - current_task_hours;

                    if(!(task_deficit==0)){

                        //Adjust 3 levels of hours:
                        var current_task_status = parseInt($('.c_objective_'+modify_data['pid']).attr('current-status'));
                        var current_task_hours = parseFloat($('#t_estimate_'+modify_data['pid']).attr('current-hours'));
                        var current_b_hours = parseFloat($('.hours_level_1').attr('current-hours'));

                        //Only update Bootcamp if Task+Step are Active:
                        if(current_task_status>0){
                            var new_b_hours = current_b_hours + task_deficit;
                            $('.hours_level_1').attr('current-hours',new_b_hours).text(format_hours(new_b_hours));
                        }

                        //Update Task:
                        $('#t_estimate_'+modify_data['pid']).attr('current-hours',modify_data['c_time_estimate']).text(format_hours(modify_data['c_time_estimate']));

                    }


                } else if(modify_data['level']>=3){

                    //Update time?
                    var current_hours_step = parseFloat($('#t_estimate_'+modify_data['pid']).attr('current-hours'));
                    var step_deficit = modify_data['c_time_estimate'] - current_hours_step;
                    var parent_c_id = parseInt($('.maplevel'+modify_data['pid']).attr('parent-node-id'));

                    //Update Completion Settings (All the time):
                    $('.c_objective_'+modify_data['pid']).attr('c_complete_url_required'    , modify_data['c_complete_url_required']);
                    $('.c_objective_'+modify_data['pid']).attr('c_complete_notes_required'  , modify_data['c_complete_notes_required']);

                    //Update status?
                    var current_status = parseInt($('.c_objective_'+modify_data['pid']).attr('current-status'));
                    if(!(current_status==modify_data['c_status'])){
                        //Needs to update:
                        $('.c_objective_'+modify_data['pid']).attr('current-status',modify_data['c_status']);

                        if(current_status==1 && modify_data['c_status']<=0){
                            //We need to remove initial hours from the totals:
                            step_deficit = -(current_hours_step);
                        } else if(current_status<=0 && modify_data['c_status']==1){
                            //We need to remove initial hours from the totals:
                            step_deficit = +(modify_data['c_time_estimate']);
                        }

                        //Has this been deleted?
                        if(modify_data['c_status']<0){
                            //Yes! Remove from UI:
                            $('.node_line_'+modify_data['pid']).html('<span style="color:#222;"><i class="fa fa-trash" aria-hidden="true"></i> Deleted</span>');
                            //Disapper in a while:
                            setTimeout(function() {
                                //Hide the editor & saving results:
                                $('.node_line_'+modify_data['pid']).fadeOut();
                                setTimeout(function() {
                                    //Hide the editor & saving results:
                                    $('.node_line_'+modify_data['pid']).remove();

                                    //Hide editing box:
                                    $('#modifybox').addClass('hidden');

                                    //Resort all Tasks to illustrate changes on UI:
                                    intents_sort(parent_c_id,3);
                                }, 377);
                            }, 1597);
                        } else {
                            //Resort all Tasks to illustrate changes on UI:
                            intents_sort(parent_c_id,3);
                        }
                    }

                    if(!(step_deficit==0)){

                        //Adjust 3 levels of hours:
                        var current_b_hours = parseFloat($('.hours_level_1').attr('current-hours'));
                        var current_task_hours = parseFloat($('#t_estimate_'+parent_c_id).attr('current-hours'));
                        var current_task_status = parseInt($('.c_objective_'+parent_c_id).attr('current-status'));


                        //Update Task if Step is active:
                        if(( modify_data['c_status']>0 || !(current_status==modify_data['c_status']) )){

                            //Update Miletsone:
                            var new_task_hours = current_task_hours + step_deficit;
                            $('#t_estimate_'+parent_c_id).attr('current-hours',new_task_hours).text(format_hours(new_task_hours));

                            //Only update Bootcamp if Task+Step are Active:
                            if(current_task_status>0){
                                var new_b_hours = current_b_hours + step_deficit;
                                $('.hours_level_1').attr('current-hours',new_b_hours).text(format_hours(new_b_hours));
                            }

                        }

                        //Always update the Step:
                        $('#t_estimate_'+modify_data['pid']).attr('current-hours',modify_data['c_time_estimate']).text(format_hours(modify_data['c_time_estimate']));
                    }
                }

                //Update UI to confirm with user:
                $('.save_setting_results').html(data.message).hide().fadeIn();

                //Disapper in a while:
                setTimeout(function() {
                    //Hide the editor & saving results:
                    $('.save_setting_results').hide();
                }, 1000);

            } else {
                //Ooops there was an error!
                $('.save_setting_results').html('<span style="color:#FF0000;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '+data.message+'</span>').hide().fadeIn();
            }
        });
    }
}


function tree_message(c_id,u_id){

    //Show loading:
    $('#simulate_'+c_id).attr('href','#').html('<span><img src="/img/round_load.gif" style="width:16px; height:16px; margin-top:-2px;" class="loader" /></span>');

    //Disapper in a while:
    setTimeout(function() {
        //Hide the editor & saving results:
        $.post("/api_v1/simulate_task", {
            c_id:c_id,
            depth:1,
            b_id:$('#b_id').val(),
            u_id:u_id,
        }, function(data) {
            //Show success:
            $('#simulate_'+c_id).html(data);
        });
    }, 334);

}





/* ******************************** */
/* ******************************** */
/* Simple List Management Functions */
/* ******************************** */
/* ******************************** */

function initiate_list(group_id,placeholder,prefix,current_items){

    //Is the ID on the page? Should be...
    if(!($('#'+group_id).length)){
        return false;
    }

    //Add the add line:
    $('#'+group_id).html('<div class="list-group-item list_input">'+
        '<div class="input-group">'+
        '<div class="form-group is-empty" style="margin: 0; padding: 0;"><input type="text" class="form-control listerin" placeholder="'+placeholder+'" maxlength="140"></div>'+
        '<span class="input-group-addon" style="padding-right:0;">'+
        '<span class="pull-right"><span class="badge badge-primary" style="cursor:pointer;"><i class="fa fa-plus" aria-hidden="true"></i></span></span>'+
        '</span>'+
        '</div>'+
        '</div>');

    //Initiate sort:
    var theobject = document.getElementById(group_id);
    var sort = Sortable.create( theobject , {
        animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
        handle: ".fa-bars", // Restricts sort start click/touch to the specified element
        draggable: ".is_sortable", // Specifies which items inside the element should be sortable
        onUpdate: function (evt/**Event*/){
            save_items(group_id);
        }
    });

    //Add initial items:
    if(current_items.length>0){
        $.each(current_items, function( index, value ) {
            add_item(group_id,prefix,value);
        });
    }

    //Also watch for the enter key:
    $('#'+group_id+' input[type=text]').keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            add_item(group_id,prefix,null);

            //Save the changes:
            save_items(group_id);
            return true;
        }
    });

    //And watch for the Add button click:
    $('#'+group_id+'>div .badge-primary').click(function (e) {
        //Add to UI:
        add_item(group_id,prefix,null);

        //Save the changes:
        save_items(group_id);
    });
}

function save_items(group_id){
    //Fetch new sort:
    var new_sort = [];
    var sort_rank = 0;

    $( '#'+group_id+'>li' ).each(function() {
        sort_rank++;
        //Update sort handler:
        var current_handler = $( this ).find( '.inline-level' ).html();
        var handler_parts = current_handler.split("#");
        $( this ).find( '.inline-level' ).html(handler_parts[0]+'#'+sort_rank);

        //Organize for saving:
        new_sort.push($( this ).find( '.theitem' ).text());
    });

    //Show Updating:
    //$('#'+group_id+'_status').html('<span><img src="/img/round_load.gif" class="loader" /></span>');

    //Update backend:
    $.post("/api_v1/save_b_list", {group_id:group_id, new_sort:new_sort, b_id:$('#b_id').val()}, function(data) {

        //Update UI to confirm with user? Keep it simple for now...
        if(!data.status){
            //Some error!
            $('#'+group_id+'_status').html('<span style="color:#FF0000;">Error: '+data.message+'</span>');
        } else {
            /*
            $('#'+group_id+'_status').html('<span>'+data.message+'</span>');
            //Disapper in a while:
            setTimeout(function() {
                //Hide the editor & saving results:
                $('#'+group_id+'_status').html('&nbsp;');
            }, 560);
            */
        }

    });
}

function confirm_remove(element){
    var group_id = element.parent().parent().parent().attr('id');
    var r = confirm("Remove this item?");
    if (r == true) {
        element.parent().parent().remove();
        save_items(group_id);
    }
}

function initiate_edit(element){
    var group_id = element.parent().parent().parent().attr('id');
    var new_item = prompt( "Modify:" , element.parent().parent().find( '.theitem' ).text() );
    if (new_item == null || new_item == "") {
        //Cancelled!
    } else if (new_item.length>140) {
        alert('ERROR: Cannot be more than 140 characters');
    } else {
        element.parent().parent().find( '.theitem' ).text(new_item);
        save_items(group_id);
    }
}

function add_item(group_id,prefix,current_value){
    if($('#'+group_id+' input[type=text]').val().length>0 || (current_value && current_value.length>0)){

        var next_item = $( '#'+group_id+'>li' ).length + 1;
        var do_focus = false;
        if(!current_value || current_value.length<1){
            current_value = $('#'+group_id+' input[type=text]').val();
            do_focus = true;
        }
        $('#'+group_id+'>.list_input').before( '<li class="list-group-item is_sortable">'+
            '<span class="pull-right">'+
                '<a class="badge badge-primary" href="javascript:void(0);" onclick="confirm_remove($(this))"><i class="fa fa-trash"></i></a> '+
                '<a class="badge badge-primary" href="javascript:void(0);" onclick="initiate_edit($(this))" style="margin-right: -3px;"><i class="fa fa-pencil-square-o"></i></a>'+
            '</span>'+
            '<i class="fa fa-bars"></i> <span class="inline-level">'+prefix+' #'+next_item+'</span><span class="theitem">'+current_value+'</span>'+
            '</li>');

        //Reset input field and re-focus only if manually added:
        if(do_focus){
            $('#'+group_id+' input[type=text]').val('').focus();
        }

    } else {
        alert('Error: field is empty!');
    }
}

</script>



<input type="hidden" id="b_id" value="<?= $b['b_id'] ?>" />
<input type="hidden" id="pid" value="<?= $intent['c_id'] ?>" />


<div class="row">
	<div class="col-xs-6">


        <div class="help_body below_h" id="content_2272"></div>
		<?php

        //Show relevant tips:
        /*
        if($level==1){
            itip(599);
        } elseif($level==2){
            itip(602);
        }
        */
        echo '<div id="project-objective" class="list-group maxout">';
            echo echo_cr($b,$b,$level);
        echo '</div>';

        ?>


        <ul id="topnav" class="nav nav-pills nav-pills-primary">
          <li id="nav_screening"><a href="#screening"><i class="fa fa-sign-in" aria-hidden="true"></i> Screening</a></li>
          <li id="nav_tasks" class="active"><a href="#tasks"><i class="fa fa-check-square-o" aria-hidden="true"></i> Tasks</a></li>
          <li id="nav_outcomes"><a href="#outcomes"><i class="fa fa-sign-out" aria-hidden="true"></i> Outcomes</a></li>
        </ul>

        <div class="tab-content tab-space">

            <div class="tab-pane" id="tabscreening">


                <div class="title" style="margin-top:25px;"><h4><i class="fa fa-address-book" aria-hidden="true"></i> Target Audience <span id="hb_426" class="help_button" intent-id="426"></span> <span id="b_target_audience_status" class="list_status">&nbsp;</span></h4></div>
                <div class="help_body maxout" id="content_426"></div>
                <script>
                    $(document).ready(function() {
                        initiate_list('b_target_audience','+ New Target Audience','<i class="fa fa-address-book" aria-hidden="true"></i>',<?= ( strlen($b['b_target_audience'])>0 ? $b['b_target_audience'] : '[]' ) ?>);
                    });
                </script>
                <div id="b_target_audience" class="list-group grey-list"></div>


                <div class="title" style="margin-top:30px;"><h4><i class="fa fa-check-square-o" aria-hidden="true"></i> Prerequisites <span id="hb_610" class="help_button" intent-id="610"></span> <span id="b_prerequisites_status" class="list_status">&nbsp;</span></h4></div>
                <div class="help_body maxout" id="content_610"></div>
                <script>
                    $(document).ready(function() {
                        initiate_list('b_prerequisites','+ New Prerequisite','<i class="fa fa-check-square-o" aria-hidden="true"></i>',<?= ( strlen($b['b_prerequisites'])>0 ? $b['b_prerequisites'] : '[]' ) ?>);
                    });
                </script>
                <div id="b_prerequisites" class="list-group grey-list"></div>


            </div>

            <div class="tab-pane active" id="tabtasks" style="padding-top:20px;">
                <?php
                //Task Expand/Contract all if more than 2
                if(count($intent['c__child_intents'])>0){
                    /*
                    echo '<div id="task_view">';
                    echo '<i class="fa fa-plus-square expand_all" aria-hidden="true"></i> &nbsp;';
                    echo '<i class="fa fa-minus-square close_all" aria-hidden="true"></i>';
                    echo '</div>';
                    */
                }
                //Tasks List:
                echo '<div id="list-outbound" class="list-group">';

                foreach($intent['c__child_intents'] as $key=>$sub_intent){
                    echo echo_cr($b,$sub_intent, ($level+1),$b['b_id']);
                }


                if(!$b['b_old_format'] || $udata['u_status']==3){
                    ?>
                    <div class="list-group-item list_input">
                        <div class="input-group">
                            <div class="form-group is-empty" style="margin: 0; padding: 0;"><input type="text" class="form-control autosearch" maxlength="70" id="addnode" placeholder=""></div>
                            <span class="input-group-addon" style="padding-right:8px;">
                            <span id="dir_handle" data-toggle="tooltip" title="or press ENTER ;)" data-placement="top" class="badge badge-primary pull-right" style="cursor:pointer; margin: 1px 3px 0 6px;">
                                <div><i class="fa fa-plus"></i></div>
                            </span>
                        </span>
                        </div>
                    </div>
                    <?php
                }

                echo '</div>';
                ?>
            </div>

            <div class="tab-pane" id="taboutcomes">

                <div class="title" style="margin-top:25px;"><h4><i class="fa fa-diamond" aria-hidden="true"></i> Skills You Will Gain <span id="hb_2271" class="help_button" intent-id="2271"></span> <span id="b_transformations_status" class="list_status">&nbsp;</span></h4></div>
                <div class="help_body maxout" id="content_2271"></div>
                <script>
                    $(document).ready(function() {
                        initiate_list('b_transformations','+ New Skill','<i class="fa fa-diamond"></i>',<?= ( strlen($b['b_transformations'])>0 ? $b['b_transformations'] : '[]' ) ?>);
                    });
                </script>
                <div id="b_transformations" class="list-group grey-list"></div>




                <div class="title" style="margin-top:30px;"><h4><i class="fa fa-trophy" aria-hidden="true"></i> Completion Awards <span id="hb_623" class="help_button" intent-id="623"></span> <span id="b_completion_prizes_status" class="list_status">&nbsp;</span></h4></div>
                <div class="help_body maxout" id="content_623"></div>
                <script>
                    $(document).ready(function() {
                        initiate_list('b_completion_prizes','+ New Prize','<i class="fa fa-trophy"></i>',<?= ( strlen($b['b_completion_prizes'])>0 ? $b['b_completion_prizes'] : '[]' ) ?>);
                    });
                </script>
                <div id="b_completion_prizes" class="list-group grey-list"></div>
            </div>

        </div>

	</div>


	<div class="col-xs-6" id="iphonecol">

        <div id="modifybox" class="hidden" node-id="0" level="0">

            <div style="text-align:right; font-size: 22px; margin: -5px 0 -20px 0;"><a href="javascript:void(0)" onclick="$('#modifybox').addClass('hidden')"><i class="fa fa-times" aria-hidden="true"></i></a></div>

            <div id="c_objective1" class="levelz level1 hidden">
                <?php $this->load->view('console/inputs/c_objective' , array(
                    'c_objective' => $b['c_objective'],
                    'level' => 1,
                )); ?>
            </div>
            <div id="c_objective2" class="levelz level2 hidden">
                <?php $this->load->view('console/inputs/c_objective' , array(
                    'c_objective' => null,
                    'level' => 2,
                )); ?>
            </div>
            <div id="c_objective3" class="levelz level3 hidden">
                <?php $this->load->view('console/inputs/c_objective' , array(
                    'c_objective' => null,
                    'level' => 3,
                )); ?>
            </div>



            <div class="levelz level3 level2 hidden" style="margin-top:15px;">
                <?php $times = $this->config->item('c_time_options'); ?>
                <div class="title"><h4><i class="fa fa-clock-o"></i> Time Estimate <span id="hb_609" class="help_button" intent-id="609"></span></h4></div>
                <div class="help_body maxout" id="content_609"></div>
                <select class="form-control input-mini border timer_2 timer_3" id="c_time_estimate">
                    <?php
                    foreach($times as $time){
                        echo '<option value="'.$time.'" '.( $intent['c_time_estimate']==$time ? 'selected="selected"' : '' ).'>'.echo_hours($time).'</option>';
                    }
                    ?>
                </select>
            </div>



            <div class="levelz level3 level2 hidden" style="margin-top:15px;">
                <div class="title"><h4><i class="fa fa-check-square"></i> Completion Settings <span id="hb_2284" class="help_button" intent-id="2284"></span></h4></div>
                <div class="help_body maxout" id="content_2284"></div>
                <div class="form-group label-floating is-empty">
                    <div class="checkbox">
                        <label><input type="checkbox" id="c_complete_notes_required" />Notes Required&nbsp;</label>
                        <label><input type="checkbox" id="c_complete_url_required" />URL Required&nbsp;</label>
                    </div>
                </div>
            </div>



            <div class="levelz level2 hidden" style="margin-top:15px;">
                <div class="title"><h4><i class="fa fa-circle" aria-hidden="true"></i> Task Status</h4></div>
                <div class="form-group label-floating is-empty">
                    <select class="form-control input-mini border" id="c_status_2">
                        <?php
                        foreach($intent_statuses as $status_id=>$status){
                            echo '<option value="'.$status_id.'">'.$status['s_name'].'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>


            <div class="levelz level3 hidden" style="margin-top:15px;">
                <div class="title"><h4><i class="fa fa-circle" aria-hidden="true"></i> Step Status</h4></div>
                <div class="form-group label-floating is-empty">
                    <select class="form-control input-mini border" id="c_status_3">
                        <?php
                        foreach($intent_statuses as $status_id=>$status){
                            echo '<option value="'.$status_id.'">'.$status['s_name'].'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div id="delete_warning"></div>







            <table width="100%" style="margin-top:10px;"><tr><td class="save-td"><a href="javascript:save_modify();" class="btn btn-primary">Save</a></td><td><span class="save_setting_results"></span></td></tr></table>
        </div>


        <div class="marvel-device iphone-x hidden" id="iphonex" node-id="">
            <div style="font-size: 22px; margin: -5px 0 -20px 0; top: 0; right: 0px; position: absolute; z-index:9999999;"><a href="javascript:void(0)" onclick="$('#iphonex').addClass('hidden')"><i class="fa fa-times" aria-hidden="true"></i></a></div>
            <div class="notch">
                <div class="camera"></div>
                <div class="speaker"></div>
            </div>
            <div class="top-bar"></div>
            <div class="sleep"></div>
            <div class="bottom-bar"></div>
            <div class="volume"></div>
            <div class="overflow">
                <div class="shadow shadow--tr"></div>
                <div class="shadow shadow--tl"></div>
                <div class="shadow shadow--br"></div>
                <div class="shadow shadow--bl"></div>
            </div>
            <div class="inner-shadow"></div>
            <div class="screen" id="iphone-screen">
            </div>

        </div>


    </div>
</div>