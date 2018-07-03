<?php
//Fetch the sprint units from config:
$message_max = $this->config->item('message_max');
$website = $this->config->item('website');
$intent_statuses = echo_status('c');
$udata = $this->session->userdata('user');
?>
<style> .breadcrumb li { display:block; } </style>
<input type="hidden" id="b_id" value="<?= $b['b_id'] ?>" />
<input type="hidden" id="pid" value="<?= $intent['c_id'] ?>" />
<input type="hidden" id="obj_name" value="" />

<script>

    function echo_hours(dbl_hour){
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



        //Deletion warning for status change:
        $('#c_status_input').change(function() {
            if(parseInt($(this).val())<0){
                //Delete has been selected!
                $('#delete_warning').html('<span style="color:#FF0000;"><i class="fas fa-trash-alt"></i> You are about to permanently delete this item and its messages.</span>');
            } else {
                $('#delete_warning').html('');
            }
        });


        //addintent
        if(window.location.hash) {
            var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
            var hash_parts = hash.split("-");
            if(hash_parts.length>=2){
                //Fetch level if available:
                if(hash_parts[0]=='messages'){
                    i_load_frame(hash_parts[1],level_id);
                } else if(hash_parts[0]=='modify'){
                    load_modify(hash_parts[1],level_id);
                }
            } else {
                //Perhaps a menu change?
                focus_hash(window.location.hash);
            }
        }

        //Update Bootcamp hours:
        $('.hours_level_1').text(echo_hours($('.hours_level_1').attr('current-hours')));



        //Load Sortable:
        load_intent_sort($("#pid").val(),"2");




        //Activate sorting for Steps:
        if($('.step-group').length){

            $( ".step-group" ).each(function() {

                var intent_id = $( this ).attr('intent-id');

                //Load sorting:
                load_intent_sort(intent_id,"3");

                //Load time:
                $('#t_estimate_'+intent_id).text(echo_hours($('#t_estimate_'+intent_id).attr('current-hours')));

            });

            if($('.is_step_sortable').length){
                //Goo through all Steps:
                $( ".is_step_sortable" ).each(function() {
                    var intent_id = $(this).attr('intent-id');
                    if(intent_id){
                        //Load time:
                        $('#t_estimate_'+intent_id).text(echo_hours($('#t_estimate_'+intent_id).attr('current-hours')));
                    }
                });
            }

        }



        //Load Algolia:
        $( ".intentadder" ).on('autocomplete:selected', function(event, suggestion, dataset) {

            new_intent($(this).attr('intent-id'), 2, suggestion.c_id);

        }).autocomplete({ hint: false, keyboardShortcuts: ['a'] }, [{

            source: function(q, cb) {
                algolia_c_index.search(q, {
                    hitsPerPage: 7,
                    //filters: ( parseInt($('#u_inbound_u_id').val())==1281 ? null : '(c_inbound_u_id=' + $('#u_id').val() + ' OR c_is_public=1)' ),
                }, function(error, content) {
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
                    var minutes = parseFloat(suggestion.c_total_c_hours)*60;
                    var fancy_hours = ( minutes<60 ? minutes+'Min' : (Math.round( parseFloat(suggestion.c_total_c_hours) * 10 ) / 10) +'Hr'  );
                    return '<span class="suggest-prefix"><i class="fas fa-hashtag"></i></span> '+ suggestion._highlightResult.c_outcome.value + ' [' + suggestion.c_total_c_count + '/' + fancy_hours+']';
                },
                header: function(data) {
                    if(!data.isEmpty){
                        return '<a href="javascript:new_intent(\''+$(this).attr('intent-id')+'\',2)" class="suggestion"><span><i class="fas fa-plus-circle"></i></span> '+data.query+'</a>';
                    }
                },
                empty: function(data) {
                    return '<a href="javascript:new_intent(\''+$(this).attr('intent-id')+'\',2)" class="suggestion"><span><i class="fas fa-plus-circle"></i></span> '+data.query+'</a>';
                },
            }
        }]).keypress(function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if ((code == 13) || (e.ctrlKey && code == 13)) {
                return new_intent($(this).attr('intent-id'),2);
            }
        });

    });




    function c_sort(c_id,level){

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


        $( "#"+s_element+" "+s_draggable ).each(function() {
            //Make sure this is NOT the dummy drag in box
            if(!$(this).hasClass('dropin-box')){

                //Fetch variables for this intent:
                var pid = parseInt($(this).attr('intent-id'));
                var cr_id = parseInt($( this ).attr('data-link-id'));

                sort_rank++;

                //Store in DB:
                new_sort[sort_rank] = cr_id;

                //Is the Outbound rank correct? Check DB value:
                var db_rank = parseInt($('.c_outcome_'+pid).attr('outbound-rank'));

                if(level==2 && !(db_rank==sort_rank) && !c_id){
                    is_properly_sorted = false;
                    console.log('Intent #'+pid+' detected out of sync.');
                }

                //Update sort handler:
                $( "#cr_"+cr_id+" .inline-level-"+level ).html('#' + sort_rank);
            }
        });


        if(level==2 && !is_properly_sorted && !c_id){
            //Sorting issue detected on Task load:
            c_id = parseInt($('#pid').val());
        }

        //It might be zero for lists that have jsut been emptied
        if(sort_rank>0 && c_id){
            //Update backend:
            $.post("/api_v1/c_sort", { pid:c_id, b_id:$('#b_id').val(), new_sort:new_sort }, function(data) {
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
                c_sort(pid,level);
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
                $.post("/api_v1/c_move_c", inputs, function(data) {
                    //Update sorts in both lists:
                    if(!data.status){

                        //There was some sort of an error returned!
                        alert('ERROR: '+data.message);

                    } else {

                        //All good as expected!
                        //Moved the parent pointer:
                        $('.maplevel'+inputs.c_id).attr('parent-intent-id',inputs.to_c_id);

                        //Determine core variables for hour move calculations:
                        var step_hours = parseFloat($('#t_estimate_'+inputs.c_id).attr('current-hours'));
                        if(!(step_hours==0)){
                            //Remove from old one:
                            var from_hours_new = parseFloat($('#t_estimate_'+inputs.from_c_id).attr('current-hours'))-step_hours;
                            $('#t_estimate_'+inputs.from_c_id).attr('current-hours',from_hours_new).text(echo_hours(from_hours_new));

                            //Add to new:
                            var to_hours_new = parseFloat($('#t_estimate_'+inputs.to_c_id).attr('current-hours'))+step_hours;
                            $('#t_estimate_'+inputs.to_c_id).attr('current-hours',to_hours_new).text(echo_hours(to_hours_new));
                        }


                        //Update sorting for both lists:
                        c_sort(inputs.from_c_id,"3");
                        c_sort(inputs.to_c_id,"3");

                    }
                });
            };
        }
        var sort = Sortable.create( theobject , settings );
    }




    function i_load_frame(c_id, level){

        var messages_focus_pid = ( $('#iphonex').hasClass('hidden') ? 0 : parseInt($('#iphonex').attr('intent-id')) );

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
            $('#iphonex').attr('intent-id',c_id);

            //Define standard phone header:
            var top_menu = '<div class="ix-top">\n' +
                '<span class="ix-top-left" data-toggle="tooltip" title="PST Time" data-placement="bottom"><?= date("H:i") ?></span>\n' +
                '<span class="ix-top-right">\n' +
                '<i class="fas fa-wifi"></i>\n' +
                '<i class="fas fa-battery-full"></i>\n' +
                '</span>\n' +
                '</div>';

            //Show tem loader:
            handler.html('<div style="text-align:center; padding-top:89px; padding-bottom:89px;"><img src="/img/round_load.gif" class="loader" /></div>');

            //Load the frame:
            $.post("/api_v1/i_load_frame", {

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
        var modify_focus_pid = ( $('#modifybox').hasClass('hidden') ? 0 : parseInt($('#modifybox').attr('intent-id')) );
        var modify_focus_level = ( $('#modifybox').hasClass('hidden') ? 0 : parseInt($('#modifybox').attr('level')) );
        var cr_id = $('.intent_line_'+c_id).attr('data-link-id');
        var cr_outbound_b_id = $('.intent_line_'+c_id).attr('data-b-id');

        //Do we already have this loaded? Then we should close it:
        if(modify_focus_pid==c_id){

            //$('#modifybox').addClass('hidden');
            $('#modifybox').hide().fadeIn();
            return false;

        } else {

            //Always set title:
            $('.c_outcome_input').val($(".c_outcome_"+c_id+":first").text());

            if(level==0 && cr_outbound_b_id>0 && cr_id>0){
                //TODO Load the unlink button
            }

            //Loadup variables for Tasks & Steps:
            if(level>=2){

                //Status:
                $('#modifybox #c_status_input').val($('.c_outcome_'+c_id).attr('current-status'));


                //Update Timer:
                $('.timer_'+level).val($('#t_estimate_'+c_id).attr('current-hours'));

                //Completion settings:
                document.getElementById("c_complete_url_required").checked = parseInt($('.c_outcome_'+c_id).attr('c_complete_url_required'));
                document.getElementById("c_complete_notes_required").checked = parseInt($('.c_outcome_'+c_id).attr('c_complete_notes_required'));
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
            $('#modifybox').attr('intent-id',c_id);
            $('#modifybox').attr('level',level);

            //Load parent intents:
            $.post("/api_v1/load_inbound_c", {c_id:c_id, cr_id:cr_id} , function(data) {
                if(data.status){

                } else {
                    alert('ERROR: '+data.message);
                }
            });


        }
    }






    function save_modify(){

        //Validate that we have all we need:
        if($('#modifybox').hasClass('hidden') || !parseInt($('#modifybox').attr('intent-id'))) {
            //Oops, this should not happen!
            return false;
        }

        //Prepare data to be modified for this intent:
        var modify_data = {
            b_id:$('#b_id').val(),
            pid:parseInt($('#modifybox').attr('intent-id')),
            level:parseInt($('#modifybox').attr('level')),
            c_outcome:$('.c_outcome_input').val(),
            c_time_estimate:parseFloat($('#c_time_estimate').val()),
            c_complete_url_required:(document.getElementById('c_complete_url_required').checked ? 1 : 0),
            c_complete_notes_required:(document.getElementById('c_complete_notes_required').checked ? 1 : 0),
        };

        //Show spinner:
        $('.save_setting_results').html('<span><img src="/img/round_load.gif" class="loader" /></span>').hide().fadeIn();

        //Save the rest of the content:
        $.post("/api_v1/c_save_settings", modify_data , function(data) {

            if(data.status){

                //Always update title for all 3 levels:
                $(".c_outcome_"+modify_data['pid']).html(modify_data['c_outcome']);
                var parent_c_id = parseInt($('.maplevel'+modify_data['pid']).attr('parent-intent-id'));

                //Update page variables:
                if(modify_data['level']==2){

                    //What to do with the hours:
                    var current_c_hours = parseFloat($('#t_estimate_'+modify_data['pid']).attr('current-hours'));
                    var hours_deficit = modify_data['c_time_estimate'] - current_c_hours;

                    if(!(hours_deficit==0)){

                        //Adjust 3 levels of hours:
                        var current_c_hours = parseFloat($('#t_estimate_'+modify_data['pid']).attr('current-hours'));
                        var parent_c_hours = parseFloat($('#t_estimate_'+parent_c_id).attr('current-hours'));
                        var current_b_hours = parseFloat($('.hours_level_1').attr('current-hours'));

                        //Might need to update weekly Bootcamp in Multi-week Bootcamps:
                        if(modify_data['level']==2){
                            var new_parent_c_hours = parent_c_hours + hours_deficit;
                            $('#t_estimate_'+parent_c_id).attr('current-hours',new_parent_c_hours).text(echo_hours(new_parent_c_hours));
                        }

                        var new_b_hours = current_b_hours + hours_deficit;
                        $('.hours_level_1').attr('current-hours',new_b_hours).text(echo_hours(new_b_hours));

                        //Update Task:
                        $('#t_estimate_'+modify_data['pid']).attr('current-hours',modify_data['c_time_estimate']).text(echo_hours(modify_data['c_time_estimate']));

                    }


                } else if(modify_data['level']>=3){

                    //Update time?
                    var current_hours_step = parseFloat($('#t_estimate_'+modify_data['pid']).attr('current-hours'));
                    var step_deficit = modify_data['c_time_estimate'] - current_hours_step;

                    //Update Completion Settings (All the time):
                    $('.c_outcome_'+modify_data['pid']).attr('c_complete_url_required'    , modify_data['c_complete_url_required']);
                    $('.c_outcome_'+modify_data['pid']).attr('c_complete_notes_required'  , modify_data['c_complete_notes_required']);


                    if(!(step_deficit==0)){

                        //Adjust 3 levels of hours:
                        var current_b_hours = parseFloat($('.hours_level_1').attr('current-hours'));
                        var current_c_hours = parseFloat($('#t_estimate_'+parent_c_id).attr('current-hours'));

                        //Always update the Step:
                        $('#t_estimate_'+modify_data['pid']).attr('current-hours',modify_data['c_time_estimate']).text(echo_hours(modify_data['c_time_estimate']));
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
                $('.save_setting_results').html('<span style="color:#FF0000;"><i class="fas fa-exclamation-triangle"></i> '+data.message+'</span>').hide().fadeIn();
            }
        });

    }






    function tree_message(c_id,u_id){

        //Show loading:
        $('#simulate_'+c_id).attr('href','#').html('<span><img src="/img/round_load.gif" style="width:16px; height:16px; margin-top:-2px;" class="loader" /></span>');

        //Disapper in a while:
        setTimeout(function() {
            //Hide the editor & saving results:
            $.post("/api_v1/i_dispatch", {
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


    function new_intent(pid,next_level,link_c_id=0){

        //If link_c_id>0 this means we're only linking
        //Set variables mostly based on level:
        if(next_level==2){
            var input_field = $('#addintent');
            var sort_list_id = "list-outbound";
            var sort_handler = ".is_sortable";
        } else if(next_level==3){
            var input_field = $('#addintent'+pid);
            var sort_list_id = "list-outbound-"+pid;
            var sort_handler = ".is_step_sortable";
        } else {
            alert('Invalid next_level value');
            return false;
        }

        var b_id = $('#b_id').val();
        var intent_name = input_field.val();

        if(!link_c_id && intent_name.length<1){
            alert('Error: Missing Outcome. Try Again...');
            input_field.focus();
            return false;
        }

        //Set processing status:
        add_to_list(sort_list_id,sort_handler,'<div id="temp'+next_level+'" class="list-group-item"><img src="/img/round_load.gif" class="loader" /> Adding... </div>');

        //Empty Input:
        input_field.val("").focus();

        //Update backend:
        $.post("/api_v1/c_new", {b_id:b_id, pid:pid, c_outcome:intent_name, next_level:next_level, link_c_id:link_c_id}, function(data) {

            //Update UI to confirm with user:
            $( "#temp"+next_level ).remove();

            //Add new
            add_to_list(sort_list_id,sort_handler,data.html);

            //Re-adjust sorting:
            load_intent_sort(pid,next_level);

            if(next_level==2){

                //Adjust the Task count:
                c_sort(0,2);

                //Re-adjust sorting for inner Steps:
                load_intent_sort(data.c_id,3);

            } else {
                //Adjust Step sorting:
                c_sort(pid,next_level);
            }

            //Tooltips:
            $('[data-toggle="tooltip"]').tooltip();

            //Add the new time:
            var step_deficit = 0.05; //3 minutes is the default for new Tasks/Steps
            var current_b_hours = parseFloat($('.hours_level_1').attr('current-hours'));
            var current_task_hours = parseFloat($('#t_estimate_'+pid).attr('current-hours'));
            var current_task_status = parseInt($('.c_outcome_'+pid).attr('current-status'));

            //Update Miletsone:
            $('#t_estimate_'+pid).attr('current-hours',(current_task_hours + step_deficit)).text(echo_hours((current_task_hours + step_deficit)));

            //Only update Bootcamp if Task is active:
            if(current_task_status>0){
                $('.hours_level_1').attr('current-hours',(current_b_hours + step_deficit)).text(echo_hours((current_b_hours + step_deficit)));
            }

        });

        //Prevent form submission:
        return false;
    }





    /* ******************************** */
    /* Simple List Management Functions */
    /* ******************************** */

    function initiate_list(group_id,placeholder,prefix,current_items){

        //Is the ID on the page? Should be...
        if(!($('#'+group_id).length)){
            return false;
        }

        //Add the add line:
        $('#'+group_id).html('<div class="list-group-item list_input">'+
            '<div class="input-group">'+
            '<div class="form-group is-empty" style="margin: 0; padding: 0;"><input type="text" class="form-control listerin" placeholder="'+placeholder+'"></div>'+
            '<span class="input-group-addon" style="padding-right:0;">'+
            '<span class="pull-right"><span class="badge badge-primary" style="cursor:pointer;"><i class="fas fa-plus"></i></span></span>'+
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
        $.post("/api_v1/b_save_list", {group_id:group_id, new_sort:new_sort, b_id:$('#b_id').val()}, function(data) {

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
                '<a class="badge badge-primary" href="javascript:void(0);" onclick="confirm_remove($(this))"><i class="fas fa-trash-alt"></i></a> '+
                '<a class="badge badge-primary" href="javascript:void(0);" onclick="initiate_edit($(this))" style="margin-right: -3px;"><i class="fas fa-pen-square"></i></a>'+
                '</span>'+
                '<i class="fas fa-bars"></i> <span class="inline-level">'+prefix+' #'+next_item+'</span><span class="theitem">'+current_value+'</span>'+
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



<div class="row">
    <div class="col-xs-6">


        <div class="help_body below_h" id="content_2272"></div>
        <?php

        //Show relevant tips:
        /*
        if($level==1){
            echo_tip(599);
        } elseif($level==2){
            echo_tip(602);
        }
        */
        echo '<div id="bootcamp-objective" class="list-group">';
        echo echo_actionplan($b,$b,$level);
        echo '</div>';
        ?>



        <ul id="topnav" class="nav nav-pills nav-pills-primary">
            <li id="nav_prerequisites"><a href="#prerequisites"><i class="fas fa-shield-check"></i> Prerequisites</a></li>
            <li id="nav_intents" class="active"><a href="#intents"><i class="fas fa-hashtag"></i> Intents</a></li>
            <li id="nav_skills"><a href="#skills"><i class="fas fa-trophy"></i> Skills</a></li>
            <li id="nav_services"><a href="#services"><i class="fas fa-concierge-bell"></i> Services</a></li>
        </ul>


        <div class="tab-content tab-space">

            <div class="tab-pane active" id="tabintents" style="max-width: none !important;">

                <?php

                echo_tip(602);


                //Task Expand/Contract all if more than 2
                if(count($intent['c__child_intents'])>0){
                    echo '<div id="task_view">';
                    echo '<i class="fas fa-plus-square expand_all"></i> &nbsp;';
                    echo '<i class="fas fa-minus-square close_all"></i>';
                    echo '</div>';
                }

                //Task/Bootcamp List:
                echo '<div id="list-outbound" class="list-group">';

                    foreach($intent['c__child_intents'] as $key=>$sub_intent){
                        echo echo_actionplan($b,$sub_intent, ($level+1),$b['b_id']);
                    }


                    ?>
                    <div class="list-group-item list_input searchable">
                        <div class="input-group">
                            <div class="form-group is-empty" style="margin: 0; padding: 0;"><input type="text" class="form-control intentadder" maxlength="70" intent-id="<?= $intent['c_id'] ?>" id="addintent" placeholder="Add #Intent"></div>
                            <span class="input-group-addon" style="padding-right:8px;">
                                <span id="dir_handle" data-toggle="tooltip" title="or press ENTER ;)" data-placement="top" class="badge badge-primary pull-right" style="cursor:pointer; margin: 1px 3px 0 6px;">
                                    <div><i class="fas fa-plus"></i></div>
                                </span>
                            </span>
                        </div>
                    </div>
                    <?php

                echo '</div>';
                ?>
            </div>


            <div class="tab-pane" id="tabprerequisites">

                <?php echo_tip(610); ?>
                <script>
                    $(document).ready(function() {
                        initiate_list('b_prerequisites','New Prerequisite','<i class="fas fa-shield-check"></i>',<?= ( strlen($b['b_prerequisites'])>0 ? $b['b_prerequisites'] : '[]' ) ?>);
                    });
                </script>
                <div id="b_prerequisites" class="list-group grey-list"></div>

            </div>


            <div class="tab-pane" id="tabskills">

                <?php echo_tip(2271); ?>

                <script>
                    $(document).ready(function() {
                        initiate_list('b_transformations','New Skill','<i class="fas fa-trophy"></i>',<?= ( strlen($b['b_transformations'])>0 ? $b['b_transformations'] : '[]' ) ?>);
                    });
                </script>
                <div id="b_transformations" class="list-group grey-list"></div>
            </div>


            <div class="tab-pane" id="tabservices">
                <?php echo_tip(7100); ?>

                <?php if($b['b_offers_coaching']){ ?>
                    <script>
                        $(document).ready(function() {
                            initiate_list('b_coaching_services','New Coaching Service','<i class="fas fa-concierge-bell"></i>',<?= ( strlen($b['b_coaching_services'])>0 ? $b['b_coaching_services'] : '[]' ) ?>);
                        });
                    </script>
                    <div id="b_coaching_services" class="list-group grey-list"></div>
                <?php } else { ?>
                    <div class="alert alert-info maxout" role="alert"><i class="fas fa-exclamation-triangle"></i> You can set coaching services once you <a href="/console/<?= $b['b_id'] ?>/settings#enrollment">enable coaching</a> in settings.</div>
                <?php } ?>

            </div>






        </div>

    </div>


    <div class="col-xs-6" id="iphonecol">

        <div id="modifybox" class="hidden" intent-id="0" level="0">

            <div style="text-align:right; font-size: 22px; margin: -5px 0 -20px 0;"><a href="javascript:void(0)" onclick="$('#modifybox').addClass('hidden')"><i class="fas fa-times"></i></a></div>



            <div class="levelz level0 level1 level2 level3 hidden">
                <div class="title"><h4><i class="fas fa-dot-circle"></i> Outcome <span id="hb_598" class="help_button" intent-id="598"></span></h4></div>
                <div class="help_body maxout" id="content_598"></div>

                <div class="form-group label-floating is-empty">
                    <div class="input-group border">
                        <span class="input-group-addon addon-lean" style="color:#3C4858; font-weight: 300;">To</span>
                        <input style="padding-left:0;" type="text" id="c_outcome" maxlength="70" value="" class="form-control c_outcome_input">
                    </div>
                </div>
            </div>


            <div class="levelz level1 level2 level3 hidden" style="margin-top:15px;">
                <div class="title"><h4><i class="fas fa-sliders-h"></i> Status</h4></div>
                <div class="form-group label-floating is-empty">
                    <select class="form-control input-mini border" id="c_status_input">
                        <?php
                        foreach($intent_statuses as $status_id=>$status){
                            echo '<option value="'.$status_id.'">'.$status['s_name'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div id="delete_warning"></div>
            </div>


            <div class="levelz level0 hidden" id="remove_b_link" style="margin-top:15px;">

            </div>


            <div class="levelz level1 level2 level3 hidden" style="margin-top:15px;">
                <?php $times = $this->config->item('c_time_options'); ?>
                <div class="title"><h4><i class="fas fa-clock"></i> Time Estimate <span id="hb_609" class="help_button" intent-id="609"></span></h4></div>
                <div class="help_body maxout" id="content_609"></div>
                <select class="form-control input-mini border timer_2 timer_3" id="c_time_estimate">
                    <?php
                    foreach($times as $time){
                        echo '<option value="'.$time.'" '.( $intent['c_time_estimate']==$time ? 'selected="selected"' : '' ).'>'.echo_hours($time).'</option>';
                    }
                    ?>
                </select>
            </div>



            <div class="levelz level1 level2 level3 hidden" style="margin-top:15px;">
                <div class="title"><h4><i class="fal fa-check-circle"></i> Completion Settings <span id="hb_2284" class="help_button" intent-id="2284"></span></h4></div>
                <div class="help_body maxout" id="content_2284"></div>
                <div class="form-group label-floating is-empty">
                    <div class="checkbox">
                        <label><input type="checkbox" id="c_complete_notes_required" />Notes Required&nbsp;</label>
                        <label><input type="checkbox" id="c_complete_url_required" />URL Required&nbsp;</label>
                    </div>
                </div>
            </div>




            <table width="100%" style="margin-top:10px;"><tr><td class="save-td"><a href="javascript:save_modify();" class="btn btn-primary">Save</a></td><td><span class="save_setting_results"></span></td></tr></table>
        </div>


        <div class="marvel-device iphone-x hidden" id="iphonex" intent-id="">
            <div style="font-size: 22px; margin: -5px 0 -20px 0; top: 0; right: 0px; position: absolute; z-index:9999999;"><a href="javascript:void(0)" onclick="$('#iphonex').addClass('hidden')"><i class="fas fa-times"></i></a></div>
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